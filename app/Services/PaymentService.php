<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\PaymentInstallment;
use App\Models\StudentDocument;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use App\Mail\LateFeeAppliedMail;
use App\Mail\PortalBlockedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentService
{
    const LATE_FEE_AMOUNT = 500;
    const WARNING_WEEKS = 1;  // First reminder after 1 week overdue
    const GRACE_PERIOD_WEEKS = 2; // 2 weeks = 14 days total (7 days after due date + 7 days grace)
    const LATE_FEE_WEEKS = 3; // Late fee applied after 3 weeks (2 weeks + 1 week grace)
    const MONTHS = ['July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];

    /**
     * Check and process all overdue payments
     * Sends notifications, applies late fees, and blocks accounts as needed
     */
    public function checkAndProcessOverduePayments(): void
    {
        $today = Carbon::now();

        // Get all pending/partial installments that are past due
        $overdueInstallments = PaymentInstallment::whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', '<', $today)
            ->get();

        foreach ($overdueInstallments as $installment) {
            $this->processOverdueInstallment($installment);
        }

        Log::info('Completed overdue payment check', ['count' => $overdueInstallments->count()]);
    }

    /**
     * Create payment installments when enrollment is approved
     */
    public static function createInstallments(Enrollment $enrollment): void
    {
        if ($enrollment->payment_type !== 'installment' && !in_array($enrollment->payment_option, ['B', 'C', 'D'])) {
            return;
        }

        // Auto-fix missing payment_type for legacy data
        if (!$enrollment->payment_type && in_array($enrollment->payment_option, ['B', 'C', 'D'])) {
            $enrollment->update(['payment_type' => 'installment']);
            $enrollment->refresh();
        }

        // Check if installments already exist
        if ($enrollment->paymentInstallments()->count() > 0) {
            return;
        }

        $monthlyAmount = floatval($enrollment->monthly_amount ?? 0);
        if ($monthlyAmount <= 0) {
            return;
        }

        $userId = $enrollment->user_id;
        $schoolYear = $enrollment->school_year;
        $startYear = intval(explode('-', $schoolYear)[0]) ?? now()->year;

        // Create 9 monthly installments (July - March)
        foreach (self::MONTHS as $index => $monthName) {
            // Determine year: July-Dec = startYear, Jan-Mar = startYear+1
            $year = $index < 6 ? $startYear : $startYear + 1;
            $monthNum = $index < 6 ? (7 + $index) : ($index - 5); // 7-12, 1-3
            
            // Due date is last day of the month
            $dueDate = Carbon::create($year, $monthNum, 1)->endOfMonth();

            PaymentInstallment::create([
                'enrollment_id' => $enrollment->id,
                'user_id' => $userId,
                'month_name' => $monthName,
                'due_date' => $dueDate,
                'amount' => $monthlyAmount,
                'amount_paid' => 0,
                'late_fee' => 0,
                'status' => 'pending',
                'weeks_overdue' => 0,
            ]);
        }
    }

    /**
     * Process a monthly payment for a specific installment
     */
    public static function processInstallmentPayment(
        Enrollment $enrollment,
        int $installmentId,
        float $amount,
        string $paymentMethod,
        ?string $referenceNumber = null,
        ?StudentDocument $document = null
    ): array {
        $installment = PaymentInstallment::find($installmentId);
        
        if (!$installment || $installment->enrollment_id !== $enrollment->id) {
            return ['success' => false, 'message' => 'Invalid installment'];
        }

        if ($installment->status === 'paid') {
            return ['success' => false, 'message' => 'This installment is already paid'];
        }

        if ($installment->status === 'pending_approval') {
            return ['success' => false, 'message' => 'This installment has a payment pending approval. Please wait for admin confirmation.'];
        }

        $totalDue = $installment->amount + $installment->late_fee;
        $amountWithLateFee = $amount;

        // Validate exact amount (must pay total due including late fee)
        if ($amount < $totalDue) {
            return [
                'success' => false, 
                'message' => 'Amount must be exactly ₱' . number_format($totalDue, 2) . ' (includes ₱' . number_format($installment->late_fee ?? 0, 2) . ' late fee)'
            ];
        }

        // Mark installment as pending_approval - NOT paid yet, waiting for finance approval
        $installment->update([
            'amount_paid' => $amountWithLateFee,
            'status' => 'pending_approval',
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'document_id' => $document?->id,
        ]);

        // Do NOT increment payment_amount yet - wait for finance approval
        // Finance approval will update installment to 'paid' and increment payment_amount

        return [
            'success' => true,
            'message' => 'Payment successful! ' . ($installment->late_fee > 0 ? 'Late fee of ₱' . number_format($installment->late_fee ?? 0, 2) . ' applied.' : ''),
            'installment' => $installment,
        ];
    }

    /**
     * Process advance payment for multiple months
     */
    public static function processAdvancePayment(
        Enrollment $enrollment,
        int $numberOfMonths,
        float $amount,
        string $paymentMethod,
        ?string $referenceNumber = null,
        ?StudentDocument $document = null
    ): array {
        // Get pending or overdue installments, oldest first
        $pendingInstallments = $enrollment->paymentInstallments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->take($numberOfMonths)
            ->get();

        if ($pendingInstallments->isEmpty()) {
            return ['success' => false, 'message' => 'No pending installments found'];
        }

        // Calculate total needed
        $totalNeeded = $pendingInstallments->sum(function ($inst) {
            return $inst->amount + $inst->late_fee;
        });

        if ($amount < $totalNeeded) {
            return [
                'success' => false,
                'message' => 'Amount insufficient. Need ₱' . number_format($totalNeeded, 2) . ' for ' . $numberOfMonths . ' month(s)'
            ];
        }

        // Process payment for each installment
        $processed = 0;
        $lateFeesApplied = 0;
        
        foreach ($pendingInstallments as $installment) {
            $result = self::processInstallmentPayment(
                $enrollment,
                $installment->id,
                $installment->amount + $installment->late_fee,
                $paymentMethod,
                $referenceNumber . ($processed > 0 ? '-' . ($processed + 1) : ''),
                $document
            );

            if ($result['success']) {
                $processed++;
                $lateFeesApplied += $installment->late_fee;
            }
        }

        return [
            'success' => true,
            'message' => "Paid {$processed} month(s) in advance!" . ($lateFeesApplied > 0 ? " Total late fees: ₱" . number_format($lateFeesApplied, 2) : ''),
            'months_paid' => $processed,
            'late_fees' => $lateFeesApplied,
        ];
    }

    /**
     * Process a single overdue installment
     * Mark as overdue and apply notifications
     */
    private function processOverdueInstallment($installment): void
    {
        $today = Carbon::now();
        $dueDate = Carbon::parse($installment->due_date);
        $weeksOverdue = floor($dueDate->diffInWeeks($today));

        // Mark as overdue if not already
        if ($installment->status !== 'overdue') {
            $installment->update(['status' => 'overdue']);
        }

        // Apply notification logic based on weeks overdue
        if ($weeksOverdue >= self::WARNING_WEEKS && !$installment->warning_sent) {
            self::sendWarningEmail($installment);
            $installment->update(['warning_sent' => true]);
        }

        if ($weeksOverdue >= self::GRACE_PERIOD_WEEKS && !$installment->reminder_sent) {
            self::sendReminderEmail($installment);
            $installment->update(['reminder_sent' => true]);
        }

        if ($weeksOverdue >= self::LATE_FEE_WEEKS && !$installment->late_fee_applied) {
            self::applyLateFee($installment);
            self::sendLateFeeEmail($installment);
            $installment->update(['late_fee_applied' => true]);
        }

        if ($weeksOverdue > self::LATE_FEE_WEEKS) {
            self::sendWeeklyReminderEmail($installment, $weeksOverdue);
        }

        $installment->save();
    }

    /**
     * Check and update overdue installments
     * Apply late fees and send notifications (NO portal blocking - email only)
     */
    public static function checkOverdueInstallments(): void
    {
        $overdueInstallments = PaymentInstallment::overdue()
            ->where('status', '!=', 'paid')
            ->with(['enrollment', 'user'])
            ->get();

        foreach ($overdueInstallments as $installment) {
            $weeksOverdue = $installment->due_date->diffInWeeks(now());
            
            if ($weeksOverdue !== $installment->weeks_overdue) {
                $installment->weeks_overdue = $weeksOverdue;

                // Week 1 (7 days overdue): Send first warning email
                if ($weeksOverdue >= self::WARNING_WEEKS && !$installment->warning_sent) {
                    $installment->warning_sent = true;
                    self::sendWarningEmail($installment);
                }

                // Week 2 (14 days overdue): Send reminder email (grace period - no late fee yet)
                if ($weeksOverdue >= self::GRACE_PERIOD_WEEKS && !$installment->reminder_sent) {
                    $installment->reminder_sent = true;
                    self::sendReminderEmail($installment);
                }

                // Week 3 (21 days overdue): Apply late fee
                if ($weeksOverdue >= self::LATE_FEE_WEEKS && !$installment->late_fee_applied) {
                    $installment->late_fee = self::LATE_FEE_AMOUNT;
                    $installment->late_fee_applied = true;
                    self::sendLateFeeEmail($installment);
                }

                // After week 3: Continue sending weekly reminders (NO portal blocking)
                if ($weeksOverdue > self::LATE_FEE_WEEKS) {
                    // Send weekly reminder every week after late fee applied
                    $reminderWeek = $weeksOverdue - self::LATE_FEE_WEEKS;
                    $reminderKey = 'weekly_reminder_' . $reminderWeek;
                    
                    if (!isset($installment->$reminderKey) || !$installment->$reminderKey) {
                        $installment->$reminderKey = true;
                        self::sendWeeklyReminderEmail($installment, $weeksOverdue);
                    }
                }

                $installment->save();
            }
        }
    }

    /**
     * Send warning email for overdue payment (Week 1)
     */
    private static function sendWarningEmail(PaymentInstallment $installment): void
    {
        try {
            Mail::to($installment->user->email)->send(new PaymentReminderMail($installment));
        } catch (\Exception $e) {
            \Log::error('Failed to send warning email', [
                'installment_id' => $installment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send reminder email during grace period (Week 2)
     */
    private static function sendReminderEmail(PaymentInstallment $installment): void
    {
        try {
            // Send a gentler reminder - no late fee yet
            Mail::to($installment->user->email)->send(new PaymentReminderMail($installment));
        } catch (\Exception $e) {
            \Log::error('Failed to send reminder email', [
                'installment_id' => $installment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send late fee applied email (Week 3+)
     */
    private static function sendLateFeeEmail(PaymentInstallment $installment): void
    {
        try {
            Mail::to($installment->user->email)->send(new LateFeeAppliedMail($installment));
        } catch (\Exception $e) {
            \Log::error('Failed to send late fee email', [
                'installment_id' => $installment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send weekly reminder email after late fee is applied (NO portal blocking)
     */
    private static function sendWeeklyReminderEmail(PaymentInstallment $installment, int $weeksOverdue): void
    {
        try {
            // Just send a reminder - no blocking, no extra penalties
            Mail::to($installment->user->email)->send(new PaymentReminderMail($installment));
        } catch (\Exception $e) {
            \Log::error('Failed to send weekly reminder email', [
                'installment_id' => $installment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment summary for enrollment
     */
    public static function getPaymentSummary(Enrollment $enrollment): array
    {
        $installments = $enrollment->paymentInstallments;
        
        $paid = $installments->where('status', 'paid')->count();
        $pending = $installments->where('status', 'pending')->count();
        $pendingApproval = $installments->where('status', 'pending_approval')->count();
        $overdue = $installments->where('status', 'overdue')->count();

        $totalLateFees = $installments->sum('late_fee');
        $totalAmountPaid = $installments->sum('amount_paid');
        $totalPending = $installments->where('status', '!=', 'paid')->sum('amount');

        // Include downpayment in total_paid since downpayments aren't attached to any installment
        $enrollmentTotalPaid = floatval($enrollment->payment_amount ?? 0);
        $totalAmountPaid = max($totalAmountPaid, $enrollmentTotalPaid);

        // Next unpaid installment — include overdue and pending_approval so the correct month always shows
        $nextPending = $installments
            ->whereIn('status', ['pending', 'overdue', 'pending_approval'])
            ->sortBy('due_date')
            ->first();
        
        return [
            'total_installments' => $installments->count(),
            'paid' => $paid,
            'pending' => $pending,
            'pending_approval' => $pendingApproval,
            'overdue' => $overdue,
            'total_late_fees' => $totalLateFees,
            'total_paid' => $totalAmountPaid,
            'total_pending' => $totalPending + $totalLateFees,
            'next_due' => $nextPending ? [
                'month' => $nextPending->month_name,
                'due_date' => $nextPending->due_date,
                'amount' => $nextPending->amount + $nextPending->late_fee,
                'late_fee' => $nextPending->late_fee,
            ] : null,
            'installments' => $installments,
        ];
    }

    /**
     * Reconcile installment statuses against enrollment.payment_amount.
     * Marks oldest unpaid installments as 'paid' if payment_amount indicates they were paid
     * but the status wasn't updated (e.g. due to the old overdue-lookup bug).
     * Safe to call repeatedly — only marks installments that math says should be paid.
     */
    public static function reconcileInstallmentStatuses(Enrollment $enrollment): void
    {
        $monthly     = (float) ($enrollment->monthly_amount ?? 0);
        $downpayment = (float) ($enrollment->downpayment_amount ?? 0);
        $totalPaid   = (float) ($enrollment->payment_amount ?? 0);

        if ($monthly <= 0 || $totalPaid <= 0) {
            return;
        }

        // How many monthly installments should be paid based on payment_amount
        $amountForMonthly    = max(0, $totalPaid - $downpayment);
        $expectedPaidMonths  = (int) floor($amountForMonthly / $monthly + 0.01); // +0.01 for float rounding

        // How many are currently marked paid
        $installments      = $enrollment->paymentInstallments;
        $actualPaidMonths  = $installments->where('status', 'paid')->count();
        $toMark            = $expectedPaidMonths - $actualPaidMonths;

        if ($toMark <= 0) {
            return;
        }

        // Mark the oldest unpaid installments as paid (skip pending_approval — those await finance action)
        $unpaid = $installments
            ->whereIn('status', ['pending', 'overdue'])
            ->sortBy('due_date')
            ->take($toMark);

        foreach ($unpaid as $inst) {
            $inst->update([
                'status'     => 'paid',
                'paid_at'    => $inst->paid_at ?? now(),
                'amount_paid'=> $inst->amount + $inst->late_fee,
            ]);
        }

        // Refresh relationship so callers see updated statuses
        $enrollment->load('paymentInstallments');
    }

    /**
     * Apply late fee to an installment
     */
    public static function applyLateFee(PaymentInstallment $installment): void
    {
        $installment->update([
            'late_fee' => self::LATE_FEE_AMOUNT,
        ]);
    }

    /**
     * Waive late fee (admin function)
     */
    public static function waiveLateFee(PaymentInstallment $installment): bool
    {
        if ($installment->late_fee <= 0) {
            return false;
        }

        $installment->update([
            'late_fee' => 0,
            'status' => 'pending', // Reset status if it was overdue
        ]);

        return true;
    }

    /**
     * Check and update account status after payment (NO blocking - just updates status)
     */
    public static function updateAccountAfterPayment(Enrollment $enrollment): void
    {
        // Check if all overdue installments are now paid
        $hasOverdue = $enrollment->paymentInstallments()
                ->where('status', 'overdue')
                ->where('status', '!=', 'paid')
                ->exists();

        if (!$hasOverdue) {
            $enrollment->update(['account_blocked' => false]);
        }
    }
}
