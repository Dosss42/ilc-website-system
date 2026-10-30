<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\PaymentInstallment;
use App\Models\StudentDocument;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use App\Mail\LateFeeAppliedMail;
use App\Mail\PortalBlockedMail;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentService
{
    const LATE_FEE_AMOUNT = 500;
    // Late fee the morning after the due date — the day the due date ends unpaid (school decision,
    // October 28, 2026; it used to be 3 weeks after, with a 1-week warning and a 2-week grace period).
    // Reminders: 3 days before and on the due date (app:check-payment-due-dates), then weekly while unpaid.
    const LATE_FEE_DAYS = 1;
    const MONTHS = ['July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];

    // Exam Permit Hold: how many consecutive unpaid months trigger it.
    // Confirmed with the school: 3 months behind, no earlier than that —
    // the existing weekly email/late-fee escalation above already covers
    // months 1-2, this is the next step up in consequence.
    const EXAM_PERMIT_HOLD_MONTHS = 3;

    /** Setting (Super Admin › System Settings) that lets the daily scheduler send payment
     * reminder/warning emails and apply late fees. OFF by default: on test data it would
     * email made-up addresses and charge late fees that aren't real. */
    const AUTOMATION_SETTING = 'payment_automation_enabled';

    /** Whole months that may be paid ahead of what's due (1–3), else the full balance — Cashier, Finance, portal */
    const MAX_ADVANCE_MONTHS = 3;

    public static function automationEnabled(): bool
    {
        return (bool) \App\Models\Setting::get(self::AUTOMATION_SETTING, false);
    }

    /**
     * Check and process all overdue payments (installments whose due date has ended unpaid):
     * the ₱500 late fee the morning after the due date, then a weekly reminder email.
     */
    public function checkAndProcessOverduePayments(?int $enrollmentId = null): void
    {
        $today = Carbon::now()->startOfDay();

        // whereNotIn(...'paid','pending_approval') rather than
        // whereIn(...'pending','partial') — the old query excluded 'overdue'
        // itself, so once a row got marked overdue on day one it dropped out
        // of every future day's query and the warning/reminder/late-fee
        // escalation could never progress past whatever weeks_overdue was on
        // that first day (almost always 0, since day one of being overdue is
        // under a week). Confirmed live: real overdue installments existed
        // with weeks_overdue/warning_sent/late_fee_applied all still at their
        // defaults.
        $overdueInstallments = PaymentInstallment::whereNotIn('status', ['paid', 'pending_approval'])
            ->whereDate('due_date', '<', $today)
            ->when($enrollmentId, fn ($q) => $q->where('enrollment_id', $enrollmentId)) // one student (demo/testing)
            ->get();

        foreach ($overdueInstallments as $installment) {
            $this->processOverdueInstallment($installment, $today);
        }

        Log::info('Completed overdue payment check', ['count' => $overdueInstallments->count()]);
    }

    /**
     * Which months belong to which term: the 9 monthly installments (July–March)
     * split in order across the school's terms (Admin › Settings › Academic ›
     * Total Terms). 3 terms → Jul–Sep / Oct–Dec / Jan–Mar; 4 → 3/2/2/2.
     *
     * @return array<int, string[]>  term number => month names
     */
    public static function termMonths(?int $terms = null): array
    {
        $terms = max(1, min(4, $terms ?? (int) \App\Models\Setting::get('total_terms', 3) ?: 3));
        $months = self::MONTHS;
        $out = [];
        $offset = 0;
        for ($t = 1; $t <= $terms; $t++) {
            $size = (int) ceil((count($months) - $offset) / ($terms - $t + 1));
            $out[$t] = array_slice($months, $offset, $size);
            $offset += $size;
        }
        return $out;
    }

    /**
     * Per-term view of an installment plan — what has to be paid in each term
     * (1st Term = Jul–Sep, …), how much of it is paid, and by when. Purely a
     * grouping of the existing monthly installments: amounts, due dates, late
     * fees and the Exam Permit rule (3 months behind) are unchanged.
     *
     * @param  \Illuminate\Support\Collection<int, PaymentInstallment>  $installments
     * @return array<int, array{term:int,label:string,months:string[],amount:float,paid:float,remaining:float,deadline:?Carbon,status:string,is_next:bool}>
     */
    public static function termBreakdown($installments, ?int $terms = null): array
    {
        $ordinal = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'];
        $today = Carbon::today();
        $rows = [];
        $nextFound = false;

        foreach (self::termMonths($terms) as $t => $monthNames) {
            $items = collect($installments)->filter(fn ($i) => in_array($i->month_name, $monthNames, true));
            $amount    = round((float) $items->sum(fn ($i) => (float) $i->total_due), 2);
            $paid      = round((float) $items->sum(fn ($i) => min((float) $i->amount_paid, (float) $i->total_due)), 2);
            $remaining = round(max(0, $amount - $paid), 2);
            $deadline  = $items->max('due_date');

            $status = match (true) {
                $items->isEmpty()                                              => 'none',
                $remaining <= 0                                                => 'paid',
                $items->contains(fn ($i) => $i->status === 'pending_approval') => 'pending',
                $items->contains(fn ($i) => $i->status !== 'paid' && $i->due_date && $i->due_date->lt($today)) => 'overdue',
                $paid > 0                                                      => 'partial',
                default                                                        => 'unpaid',
            };

            $isNext = !$nextFound && in_array($status, ['overdue', 'partial', 'unpaid', 'pending'], true);
            $nextFound = $nextFound || $isNext;

            $rows[] = [
                'term'      => $t,
                'label'     => ($ordinal[$t] ?? $t . 'th') . ' Term',
                'months'    => $monthNames,
                'amount'    => $amount,
                'paid'      => $paid,
                'remaining' => $remaining,
                'deadline'  => $deadline,
                'status'    => $status,
                'is_next'   => $isNext,
            ];
        }

        return $rows;
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
        $firstDueDate = null;
        foreach (self::MONTHS as $index => $monthName) {
            // Determine year: July-Dec = startYear, Jan-Mar = startYear+1
            $year = $index < 6 ? $startYear : $startYear + 1;
            $monthNum = $index < 6 ? (7 + $index) : ($index - 5); // 7-12, 1-3

            // Due date is last day of the month
            $dueDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
            $firstDueDate ??= $dueDate;

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

        // Without this, enrollments.next_installment_date stays NULL until
        // the first payment happens to touch it — and the Finance Dashboard's
        // "Overdue"/"Due This Week" widgets query this column directly, so
        // any enrollment that hasn't made a payment yet was silently
        // invisible to those counts even when genuinely overdue. The
        // Installments list page itself was unaffected (it always computed
        // this fresh from the installment rows), which is what made the two
        // disagree.
        if ($firstDueDate) {
            $enrollment->update(['next_installment_date' => $firstDueDate]);
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
     * Process a single overdue installment (its due date has ended unpaid): mark it overdue, add the
     * ₱500 late fee once — the morning after the due date — with the "Late Fee Applied" email, then a
     * reminder email every week it stays unpaid (1, 2, 3 … weeks after the due date).
     *
     * The weekly reminder fires exactly once per week: it compares the previously-saved weeks_overdue
     * with today's, so it stays correct even if this job runs more than once in a day (there is no
     * per-week "reminder sent" column — only weeks_overdue, warning_sent and late_fee_applied exist).
     */
    private function processOverdueInstallment($installment, ?Carbon $today = null): void
    {
        $today ??= Carbon::now()->startOfDay();
        $dueDate = Carbon::parse($installment->due_date)->startOfDay();
        $previousWeeksOverdue = (int) ($installment->weeks_overdue ?? 0);
        $daysOverdue  = (int) round($dueDate->diffInDays($today));
        $weeksOverdue = intdiv($daysOverdue, 7);

        $installment->status = 'overdue';
        $installment->weeks_overdue = $weeksOverdue;

        // The due date has ended unpaid → late fee now (once), owed on top of tuition
        if ($daysOverdue >= self::LATE_FEE_DAYS && !$installment->late_fee_applied) {
            $lateFeeBefore = (float) ($installment->late_fee ?? 0);
            $installment->late_fee = self::LATE_FEE_AMOUNT;
            $installment->late_fee_applied = true;
            self::shiftTotalForLateFee($installment, self::LATE_FEE_AMOUNT - $lateFeeBefore);
            self::sendLateFeeEmail($installment);
        }

        // Then a reminder every week while unpaid
        if ($weeksOverdue >= 1 && $weeksOverdue !== $previousWeeksOverdue) {
            self::sendWeeklyReminderEmail($installment, $weeksOverdue);
        }

        $installment->save();
    }

    /**
     * Send late fee applied email (the morning after the due date)
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
    public static function reconcileInstallmentStatuses(Enrollment $enrollment, ?PaymentTransaction $transaction = null): void
    {
        $monthly     = (float) ($enrollment->monthly_amount ?? 0);
        $downpayment = (float) ($enrollment->downpayment_amount ?? 0);
        $totalPaid   = (float) ($enrollment->payment_amount ?? 0);

        if ($monthly <= 0 || $totalPaid <= 0) {
            self::syncNextInstallmentDate($enrollment);
            return;
        }

        $installments = $enrollment->paymentInstallments;

        // How many monthly installments should be paid based on payment_amount.
        // payment_amount includes late fees already paid (incremented by
        // amount + late_fee on approval, see Finance\DashboardController),
        // so late fees on installments already marked 'paid' have to be
        // subtracted here — otherwise every late fee paid inflates this count
        // by a fraction of a month, and enough of them wrongly mark a
        // never-paid installment as paid.
        $alreadyCountedLateFees = $installments->where('status', 'paid')->sum('late_fee');
        $amountForMonthly    = max(0, $totalPaid - $downpayment - $alreadyCountedLateFees);
        $expectedPaidMonths  = (int) floor($amountForMonthly / $monthly + 0.01); // +0.01 for float rounding

        // How many are currently marked paid
        $actualPaidMonths  = $installments->where('status', 'paid')->count();
        $toMark            = $expectedPaidMonths - $actualPaidMonths;

        if ($toMark > 0) {
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

            if ($transaction) {
                self::linkPaidMonths($transaction, $unpaid);
            }

            // Refresh relationship so callers see updated statuses
            $enrollment->load('paymentInstallments');
        }

        self::syncNextInstallmentDate($enrollment);
        self::fulfilPromissoryNoteIfCaughtUp($enrollment);
    }

    /**
     * Ties the months a payment paid to that payment, and writes them — with any late fee — into its
     * description, e.g. "Overdue — October (incl. ₱500.00 late fee)". Every portal lists payments by their
     * description (Student Portal, Admin Student Management, Cashier, Finance, the Official Receipt), so the
     * family and the staff see what the money covered, late fee included. Before this, months paid through
     * the Cashier or online kept no link to the payment (no method or reference on the month) and the
     * payment just read "Installment".
     */
    public static function linkPaidMonths(PaymentTransaction $transaction, $months): void
    {
        $months = collect($months)->filter()->sortBy('due_date')->values();
        if ($months->isEmpty()) {
            return;
        }

        foreach ($months as $inst) {
            $inst->update(array_filter([
                'payment_transaction_id' => $inst->payment_transaction_id ?: $transaction->id,
                'payment_method'         => $inst->payment_method ?: $transaction->payment_method,
                'reference_number'       => $inst->reference_number ?: $transaction->reference_number,
            ]));
        }

        $names = $months->pluck('month_name')->implode(', ');
        $fees  = (float) $months->sum('late_fee');
        $desc  = trim((string) $transaction->description) ?: 'Payment';
        if (stripos($desc, 'late fee') === false) {
            if (!$months->every(fn ($m) => stripos($desc, $m->month_name) !== false)) {
                $desc .= ' — ' . $names;
            }
            if ($fees > 0) {
                $desc .= ' (incl. ₱' . number_format($fees, 2) . ' late fee' . ($months->where('late_fee', '>', 0)->count() > 1 ? 's' : '') . ')';
            }
        }

        $transaction->update(array_filter([
            'description'       => $desc,
            'installment_month' => $transaction->installment_month ?: $names,
            'installment_id'    => $transaction->installment_id ?: ($months->count() === 1 ? $months->first()->id : null),
        ]));
    }

    /**
     * A family that has caught up — no month past due and still unpaid — has kept its Promissory Note, so
     * the note is marked fulfilled. Only Finance could do this by hand before: a note left "pending" after
     * the family paid everything turned "broken" on its promised date and withheld the Exam Permit of a
     * student who owed nothing. A partial payment never fulfils a note (the school's policy stays: being
     * behind after a broken promise still needs settling or a new note). Runs after every payment —
     * Cashier, Finance, online, Student Portal — through reconcileInstallmentStatuses().
     */
    public static function fulfilPromissoryNoteIfCaughtUp(Enrollment $enrollment): void
    {
        $note = $enrollment->promissoryNotes()->first(); // newest
        if (!$note || !in_array($note->status, ['pending', 'extended', 'broken'], true)) {
            return;
        }
        $stillBehind = $enrollment->paymentInstallments()
            ->whereNotIn('status', ['paid'])
            ->whereDate('due_date', '<', Carbon::today())
            ->exists();
        if ($stillBehind) {
            return;
        }
        $note->update(['status' => 'fulfilled', 'fulfilled_at' => now()]);
        \App\Services\ActivityLogger::log('update', 'Promissory Note ' . $note->reference_number
            . ' marked fulfilled automatically — the family caught up on all past-due months', 'PromissoryNote', $note->id);
    }

    /**
     * Keep enrollments.next_installment_date in agreement with the actual
     * earliest not-yet-paid installment row. This column is what the Finance
     * Dashboard's "Overdue"/"Due This Week" widgets query directly (a plain
     * WHERE on the enrollments table, not a live subquery), so letting it
     * drift out of sync with the real installment rows silently breaks those
     * counts — exactly what happened before createInstallments() and this
     * method both started calling this.
     */
    private static function syncNextInstallmentDate(Enrollment $enrollment): void
    {
        $nextPending = $enrollment->paymentInstallments
            ->whereIn('status', ['pending', 'overdue', 'pending_approval'])
            ->sortBy('due_date')
            ->first();

        $correctDate = $nextPending?->due_date;
        $currentDate = $enrollment->next_installment_date;

        $datesMatch = $correctDate && $currentDate
            ? $correctDate->isSameDay($currentDate)
            : ($correctDate === null && $currentDate === null);

        if (!$datesMatch) {
            $enrollment->update(['next_installment_date' => $correctDate]);
        }
    }

    /**
     * A late fee is money owed ON TOP of tuition, so it has to change the enrollment's total and balance
     * too. It used to be stored on the installment only: paying "month + ₱500" then took the ₱500 off the
     * TUITION balance, leaving the balance ₱500 too low — the account could show "fully paid" with a
     * month still unpaid. Every late-fee change goes through here: +₱500 when added, −₱500 when waived.
     */
    public static function shiftTotalForLateFee(PaymentInstallment $installment, float $change): void
    {
        $change = round($change, 2);
        $enrollment = $installment->enrollment;
        if (!$enrollment || abs($change) < 0.005) {
            return;
        }
        $enrollment->update([
            'total_fee'         => max(0, round((float) $enrollment->total_fee + $change, 2)),
            'remaining_balance' => max(0, round((float) $enrollment->remaining_balance + $change, 2)),
        ]);
    }

    /**
     * Apply late fee to an installment
     */
    public static function applyLateFee(PaymentInstallment $installment): void
    {
        $before = (float) ($installment->late_fee ?? 0);
        $installment->update([
            'late_fee' => self::LATE_FEE_AMOUNT,
        ]);
        self::shiftTotalForLateFee($installment, self::LATE_FEE_AMOUNT - $before);
    }

    /**
     * Waive late fee (admin function)
     */
    public static function waiveLateFee(PaymentInstallment $installment): bool
    {
        if ($installment->late_fee <= 0) {
            return false;
        }

        $waived = (float) $installment->late_fee;
        $installment->update([
            'late_fee' => 0,
            'status' => 'pending', // Reset status if it was overdue
        ]);
        self::shiftTotalForLateFee($installment, -$waived);

        return true;
    }

    /**
     * Exam Permit Hold status for an enrollment.
     *
     * The existing weekly email/late-fee escalation above (checkOverdueInstallments)
     * already covers the first ~3 weeks of a single missed month. This is the
     * next, heavier consequence once a family has fallen EXAM_PERMIT_HOLD_MONTHS
     * consecutive months behind — the point at which parents can no longer
     * pick up an Exam Permit until either the balance is settled or a
     * Promissory Note is signed with Finance/Admin.
     *
     * "Consecutive months behind" is simply the count of installments whose
     * due_date has passed and are still not 'paid' — advance/partial payments
     * always settle the OLDEST unpaid installment first (see
     * processAdvancePayment/reconcileInstallmentStatuses), so those overdue
     * rows always form a contiguous run ending at "today". That's what lets
     * "paid month 1, then missed months 2-4" and "never paid months 1-3"
     * resolve to the same, correct answer here without tracking a separate
     * streak counter that could drift out of sync with the installment rows.
     *
     * A broken promise (a Promissory Note whose promised date passed unpaid)
     * is treated as worse than a plain overdue balance: it holds regardless
     * of the raw month count, and — per school policy — needs a *new*
     * Promissory Note (created by Finance or Admin) to lift, not just a
     * partial payment.
     */
    public static function getExamPermitStatus(Enrollment $enrollment): array
    {
        // Use the loaded relation collections (->paymentInstallments /
        // ->promissoryNotes as properties, not ->paymentInstallments() calls)
        // so callers that eager-load them for a list of enrollments — e.g.
        // Finance's Installments page — don't turn this into an N+1 query
        // per row. Falls back to a normal lazy-load automatically if the
        // caller didn't eager-load, so this stays correct everywhere either way.
        $today = Carbon::today();
        $overdueMonths = $enrollment->paymentInstallments
            ->filter(fn($i) => $i->due_date && $i->due_date->lt($today) && $i->status !== 'paid')
            ->count();

        // Enrollment::promissoryNotes() is ordered newest-first, and only the
        // LATEST note represents the family's current standing — an old
        // broken note must not keep holding the account forever once a new
        // note has been issued to replace it (that's the whole point of the
        // "needs a *new* Promissory Note to lift" policy above). Scanning the
        // full history for any broken note, instead of just the latest one,
        // was the original bug here: a new note never actually lifted the hold.
        $notes = $enrollment->promissoryNotes;
        $latestNote = $notes->first();

        if ($latestNote && ($latestNote->status === 'broken' || ($latestNote->status === 'pending' && $latestNote->is_overdue))) {
            return [
                'held'           => true,
                'reason'         => 'broken_promise',
                'overdue_months' => $overdueMonths,
                'note'           => $latestNote,
            ];
        }

        $activeNote = null;
        if ($latestNote) {
            if ($latestNote->status === 'pending' && $latestNote->promise_date && Carbon::today()->lte($latestNote->promise_date)) {
                $activeNote = $latestNote;
            } elseif ($latestNote->status === 'extended' && $latestNote->extended_date && Carbon::today()->lte($latestNote->extended_date)) {
                $activeNote = $latestNote;
            }
        }

        if ($overdueMonths >= self::EXAM_PERMIT_HOLD_MONTHS && !$activeNote) {
            return [
                'held'           => true,
                'reason'         => 'overdue',
                'overdue_months' => $overdueMonths,
                'note'           => null,
            ];
        }

        return [
            'held'           => false,
            'reason'         => null,
            'overdue_months' => $overdueMonths,
            'note'           => $activeNote,
        ];
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

    /**
     * Advance an enrollment's payment_status / status / section assignment
     * after ANY payment lands on it, however it arrived. Moved here from
     * CashierController (which still has its own private wrapper used by
     * the cash-payment path) so the Xendit webhook and the live-status
     * fallback below share the exact same logic instead of two copies
     * quietly drifting apart over time.
     */
    public static function advanceEnrollmentAfterPayment(Enrollment $enrollment): void
    {
        $totalPaid = (float) ($enrollment->payment_amount ?? 0);
        $totalFee  = (float) ($enrollment->total_fee ?? 0);

        if ($totalFee > 0) {
            if ($totalPaid >= $totalFee) {
                $enrollment->update(['payment_status' => 'paid', 'remaining_balance' => 0]);
            } elseif ($totalPaid > 0) {
                $enrollment->update([
                    'payment_status'    => 'partial',
                    'remaining_balance' => max(0, $totalFee - $totalPaid),
                ]);
            }
        } elseif ($totalPaid > 0) {
            $remaining = (float) ($enrollment->remaining_balance ?? 0);
            $enrollment->update([
                'payment_status'    => $remaining <= 0 ? 'paid' : 'partial',
                'remaining_balance' => max(0, $remaining),
            ]);
        }

        $threshold = self::requiredEnrollmentThreshold($enrollment);
        $meetsThreshold = $threshold > 0 ? $totalPaid >= $threshold : $totalPaid > 0;
        if (in_array($enrollment->status, ['approved', 'pending']) && $meetsThreshold) {
            $enrollment->update(['status' => 'enrolled', 'enrolled_at' => now()]);
            self::assignSectionForEnrollment($enrollment);
        }
    }

    /**
     * The minimum payment required before an enrollment may officially
     * become 'enrolled' — previously any nonzero payment (even ₱1) was
     * treated as sufficient here and in Finance\DashboardController::
     * updateEnrollmentPaymentStatus(), letting a token payment reserve a
     * seat ahead of families paying the real required amount. Returns 0
     * when the fee plan isn't known yet, so callers can fall back to their
     * prior "any payment counts" behavior for that edge case rather than
     * blocking enrollment entirely on an enrollment with no fee data.
     */
    public static function requiredEnrollmentThreshold(Enrollment $enrollment): float
    {
        $downpayment = (float) ($enrollment->downpayment_amount ?? 0);
        if ($downpayment > 0) {
            return $downpayment;
        }
        return (float) ($enrollment->total_fee ?? 0);
    }

    /**
     * What is still owed on an enrollment's school year, or null when no fee was ever set
     * (no payment plan chosen, so it can't be called paid). Computed from total_fee and
     * payment_amount (completed payments only), not the stored remaining_balance.
     */
    public static function balanceDue(Enrollment $enrollment): ?float
    {
        $totalFee = (float) $enrollment->total_fee;
        if ($totalFee <= 0) {
            return ($enrollment->payment_status === 'paid' && (float) $enrollment->payment_amount > 0) ? 0.0 : null;
        }
        return max(0, round($totalFee - (float) $enrollment->payment_amount, 2));
    }

    /**
     * The exact amounts the cashier may take now (cash or a payment link). Only whole parts of the
     * plan are accepted — no odd amounts (₱2,000 on a ₱1,400 monthly) that leave a "credit" and
     * shift every later month:
     *   pay-in-full plan (A) → the whole remaining balance
     *   installment plan     → what is due now (the rest of the down payment, or the next unpaid
     *                          month + its late fee, less any credit already paid toward it), or that
     *                          plus 1, 2, 3… whole months in advance, or the full balance
     * Returns ['min' => float|null, 'max' => float|null, 'allowed' => [['label' =>, 'amount' =>], …]];
     * nulls / [] = no rule (no fee plan yet), max 0 = fully paid.
     */
    public static function paymentLimits(Enrollment $enrollment): array
    {
        $max = self::balanceDue($enrollment);
        if ($max === null) {
            $stored = (float) ($enrollment->remaining_balance ?? 0);
            $max = $stored > 0 ? round($stored, 2) : null;
        }
        if ($max === null) {
            return ['min' => null, 'max' => null, 'allowed' => []];
        }
        if ($max <= 0) {
            return ['min' => 0.0, 'max' => 0.0, 'allowed' => []];
        }

        $paid        = (float) ($enrollment->payment_amount ?? 0);
        $downpayment = (float) ($enrollment->downpayment_amount ?? 0);
        $monthly     = (float) ($enrollment->monthly_amount ?? 0);
        $installment = $enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']);

        // The parts still to pay, in order: [label, amount, overdue?]
        $parts = [];
        if ($installment) {
            $rows   = $enrollment->paymentInstallments()->orderBy('due_date')->get();
            $unpaid = $rows->whereIn('status', ['pending', 'overdue'])->values();
            $owed   = fn ($i) => (float) $i->amount + (float) ($i->late_fee ?? 0);
            $isOverdue = fn ($i) => $i->due_date && $i->due_date->lt(Carbon::today());

            if ($downpayment > 0 && $paid < $downpayment - 0.005) {
                $parts[] = ['Downpayment', $downpayment - $paid];
                // the months after it — from the schedule if it exists yet, else the plan's monthly amount
                if ($unpaid->isNotEmpty()) {
                    foreach ($unpaid as $i) $parts[] = [$i->month_name ?: 'Month', $owed($i), $isOverdue($i)];
                } elseif ($monthly > 0) {
                    for ($left = $max - ($downpayment - $paid); $left > 0.005; $left -= $monthly) $parts[] = ['Month', min($monthly, $left)];
                }
            } elseif ($unpaid->isNotEmpty()) {
                // credit = paid beyond the down payment and the months already marked paid (e.g. an old odd
                // payment) — it lowers the first month still due
                $credit = max(0, $paid - $downpayment - $rows->where('status', 'paid')->sum($owed));
                foreach ($unpaid as $k => $i) {
                    $parts[] = [$i->month_name ?: 'Month', $k === 0 ? max(0, $owed($i) - $credit) : $owed($i), $isOverdue($i)];
                }
            }
        }

        // Cumulative options: due now, + 1 month ahead, + 2 …, then the full balance. Months already past
        // their due date are "overdue", not "in advance": with July–September unpaid the options read
        // "Due now (July)", "Overdue July – August", "Overdue July – September", "… + 1 month in advance".
        $allowed = [];
        $sum = 0.0;
        $overdueCount = count(array_filter($parts, fn ($p) => !empty($p[2])));
        foreach ($parts as $k => $part) {
            [$label, $amount] = $part;
            $sum = round($sum + $amount, 2);
            // below Xendit's ₱1 minimum, or within ₱1 of the full balance (centavo rounding across the months
            // would leave a few centavos nobody can pay online) — then only "Full balance" is offered
            if ($sum < 1 || $sum > $max - 1) continue;
            // At most MAX_ADVANCE_MONTHS months ahead of what's due (school decision, October 31, 2026) — every
            // overdue month stays payable; past that, the full balance. Same list on every screen.
            // (after a down payment, months already past their due date — a late enrollee's — are overdue, not ahead)
            $dp = $parts[0][0] === 'Downpayment';
            $advanceMonths = $dp ? max(0, $k - $overdueCount) : $k - max(0, $overdueCount - 1);
            if ($advanceMonths > self::MAX_ADVANCE_MONTHS) continue;
            if ($parts[0][0] === 'Downpayment') {
                // "Downpayment + July – August", with "(1 month in advance)" only for months not yet due
                $ahead = max(0, $k - $overdueCount);
                $text = $k === 0 ? 'Downpayment'
                    : self::monthsCovered($parts, $k) . ($ahead ? ' (' . $ahead . ' month' . ($ahead > 1 ? 's' : '') . ' in advance)' : '');
            } elseif ($k === 0) {
                $text = 'Due now (' . $label . ')';
            } elseif ($k < $overdueCount) {
                $text = 'Overdue ' . $parts[0][0] . ' – ' . $label;
            } else {
                $base    = $overdueCount > 1 ? 'All overdue (' . $parts[0][0] . ' – ' . $parts[$overdueCount - 1][0] . ')' : 'Due now';
                $advance = $k + 1 - max(1, $overdueCount);
                $text    = $base . ' + ' . $advance . ' month' . ($advance > 1 ? 's' : '') . ' in advance';
            }
            // For the Cashier's amount cards: what kind of payment, and which months it covers ("August – October")
            $kind = $dp ? ($k === 0 ? 'downpayment' : ($k <= $overdueCount ? 'overdue' : 'advance'))
                : ($k === 0 ? 'due' : ($k < $overdueCount ? 'overdue' : 'advance'));
            $allowed[] = ['label' => $text, 'amount' => $sum, 'kind' => $kind, 'months' => self::monthsCovered($parts, $k),
                          'late' => $dp ? ($k > 0 && $k <= $overdueCount) : $k < $overdueCount]; // only months already past due
        }
        $allowed[] = ['label' => 'Full balance', 'amount' => round($max, 2), 'kind' => 'full',
                      'months' => $parts ? self::monthsCovered($parts, count($parts) - 1) : ''];

        return ['min' => $allowed[0]['amount'], 'max' => round($max, 2), 'allowed' => $allowed];
    }

    /** The parts an option pays, as a range: "August", "August – October", "Downpayment + July – August". */
    private static function monthsCovered(array $parts, int $k): string
    {
        $range = fn (int $from) => $from === $k ? $parts[$k][0] : $parts[$from][0] . ' – ' . $parts[$k][0];
        if ($parts[0][0] === 'Downpayment') {
            return $k === 0 ? 'Downpayment' : 'Downpayment + ' . $range(1);
        }
        return $range(0);
    }

    /**
     * Why the cashier can't take $amount now, or null when it is exactly one of the accepted amounts
     * (paymentLimits()['allowed'], compared to the centavo).
     */
    public static function paymentLimitError(Enrollment $enrollment, float $amount): ?string
    {
        $limits = self::paymentLimits($enrollment);
        if (empty($limits['allowed'])) {
            return null; // no fee plan rule to check against (handled by the callers' own checks)
        }
        $amount = round($amount, 2);
        foreach ($limits['allowed'] as $option) {
            if (abs($option['amount'] - $amount) < 0.005) {
                return null;
            }
        }
        $options = collect($limits['allowed']);
        if ($options->count() > 4) { // due now, 2 months ahead … full balance
            $options = $options->take(3)->push(['label' => '…', 'amount' => null])->push($options->last());
        }
        $list = $options->map(fn ($o) => $o['amount'] === null ? '…' : '₱' . number_format($o['amount'], 2) . ' (' . $o['label'] . ')')->implode(', ');
        return 'Only exact amounts are accepted, so each payment covers whole months. For this student: ' . $list . '.';
    }

    /**
     * Why this enrollment's school year blocks moving the student to the next one, or null
     * when it is fully paid. Every way a student moves up (assess, bulk promote, Nursery/Kinder
     * auto-advance, portal re-enrollment) requires the old school year to be settled.
     */
    public static function promotionBlockReason(?Enrollment $enrollment): ?string
    {
        if (!$enrollment) {
            return null;
        }
        $due = self::balanceDue($enrollment);
        if ($due === null) {
            return 'No tuition fee recorded for S.Y. ' . $enrollment->school_year . ' (no payment plan chosen)';
        }
        if ($due > 0) {
            return 'Unpaid balance of ₱' . number_format($due, 2) . ' for S.Y. ' . $enrollment->school_year;
        }
        return null;
    }

    /**
     * Single source of truth for "find an active section for this grade
     * level/school year that still has room, and assign the student to it."
     *
     * Previously implemented independently three times: here, in
     * Finance\DashboardController::updateEnrollmentPaymentStatus(), and in
     * EnrollmentController::assignSection() — only the EnrollmentController
     * copy actually respected each section's max_students capacity. The
     * other two took the first matching section regardless of how full it
     * already was, letting a section be silently over-filled through the
     * most commonly-used payment paths (admin/finance recording, cashier
     * cash, Xendit). All three now delegate here.
     */
    /**
     * Re-fetch an Enrollment with a row lock, for the payment-mutation
     * methods that read payment_amount/remaining_balance, compute a new
     * absolute total in PHP, and write it back — not an atomic increment().
     * Two staff members recording a payment for the same enrollment at
     * nearly the same time could otherwise both compute from the same stale
     * starting amount, and the second write would silently lose the first's
     * payment. Must be called inside a DB transaction (same pattern already
     * used correctly for PaymentTransaction rows in completeXenditPayment()/
     * reconcileXenditInvoice()) — the lock is only held until that
     * transaction commits or rolls back.
     */
    public static function lockEnrollment(int $enrollmentId): Enrollment
    {
        return Enrollment::where('id', $enrollmentId)->lockForUpdate()->firstOrFail();
    }

    public static function assignSectionForEnrollment(Enrollment $enrollment): void
    {
        try {
            $gradeLevel = $enrollment->grade_level ?? ($enrollment->student_data['grade_level'] ?? null);
            $schoolYear = $enrollment->school_year ?? (now()->year . '-' . (now()->year + 1));

            if (!$gradeLevel || !$enrollment->user_id) {
                return;
            }

            // Find the least-filled active section with available capacity.
            // Uses the live section_student count (via withCount), not the
            // current_enrollment column, which can drift.
            $section = \App\Models\Section::where('grade_level', $gradeLevel)
                ->where('school_year', $schoolYear)
                ->where('is_active', true)
                ->withCount('students')
                ->get()
                ->filter(fn ($s) => $s->students_count < ($s->max_students ?? 30))
                ->sortBy('students_count')
                ->first();

            if (!$section) {
                Log::warning("No active section with capacity for grade level: {$gradeLevel}, school year: {$schoolYear}");
                return;
            }

            $enrollment->update(['section' => $section->name]);

            $exists = DB::table('section_student')
                ->where('section_id', $section->id)
                ->where('user_id', $enrollment->user_id)
                ->exists();

            if (!$exists) {
                DB::table('section_student')->insert([
                    'section_id' => $section->id,
                    'user_id'    => $enrollment->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info("Student {$enrollment->user_id} assigned to section {$section->name} (Grade: {$gradeLevel})");
        } catch (\Exception $e) {
            Log::error("Section assignment error: " . $e->getMessage());
        }
    }

    /**
     * Everything that needs to happen once a Xendit transaction is confirmed
     * paid — shared by the real webhook (CashierController::xenditWebhook)
     * and reconcileXenditInvoice() below, so there is exactly one place this
     * logic lives rather than two copies that could disagree.
     */
    /**
     * Creates a Xendit invoice for the EXACT amount, centavos included (₱2,083.33), and returns
     * [Http response, the amount Xendit accepted]. Xendit's docs don't say clearly whether invoices
     * take centavos, so if a centavo amount is refused it retries once with the amount rounded up
     * to the whole peso — and the caller records (and later credits) whatever amount was accepted,
     * so what is charged and what is credited are always the same number.
     */
    public static function createXenditInvoice(string $apiKey, array $payload, float $amount): array
    {
        $exact = round($amount, 2);
        $send  = fn ($value) => Http::withBasicAuth($apiKey, '')
            ->post('https://api.xendit.co/v2/invoices', array_merge($payload, ['amount' => $value]));

        $response = $send($exact);
        if (!$response->successful() && $exact != floor($exact)) {
            $whole = (int) ceil($exact);
            Log::info('Xendit refused a centavo amount; retrying with whole pesos', [
                'amount' => $exact, 'retry' => $whole, 'error' => $response->json('message'),
            ]);
            $retry = $send($whole);
            if ($retry->successful()) {
                return [$retry, (float) $whole];
            }
        }
        return [$response, $exact];
    }

    /**
     * One open payment link per student at a time, used by both the Cashier and the Student Portal.
     * Returns [the open link to reuse (same amount) or null, an error message or null].
     * An open link for a DIFFERENT amount (e.g. due now → full balance) is cancelled at Xendit first
     * (POST /invoices/{id}/expire!) so the two can never both be paid; if it can't be cancelled (e.g.
     * it was just paid), the new link is refused and the old one stands.
     */
    public static function openXenditLink(int $enrollmentId, float $amount, string $apiKey): array
    {
        $open = PaymentTransaction::where('enrollment_id', $enrollmentId)
            ->where('payment_type', 'online')
            ->where('status', 'pending')
            ->whereNotNull('xendit_invoice_url')
            ->where('created_at', '>=', now()->subSeconds(86400))
            ->latest()
            ->get();

        $amount = round($amount, 2);
        $same = $open->first(fn ($t) => abs((float) $t->amount - $amount) < 0.005 || abs((float) $t->amount - ceil($amount)) < 0.005);
        if ($same) {
            return [$same, null];
        }

        foreach ($open as $old) {
            try {
                $res = Http::withBasicAuth($apiKey, '')->post('https://api.xendit.co/invoices/' . $old->xendit_invoice_id . '/expire!');
            } catch (\Throwable $e) {
                $res = null;
            }
            if (!$res || !$res->successful()) {
                return [null, 'A payment link for ₱' . number_format((float) $old->amount, 2) . ' is still open and could not be cancelled'
                    . ' (it may have just been paid). Please wait a moment and check the payment status first.'];
            }
            $old->update(['status' => 'expired', 'processed_at' => now()]);
            \App\Services\ActivityLogger::log('update', 'Cancelled the open ₱' . number_format((float) $old->amount, 2)
                . ' payment link (Invoice: ' . $old->xendit_invoice_id . ') — replaced by a link for ₱' . number_format($amount, 2), 'PaymentTransaction', $old->id);
        }
        return [null, null];
    }

    /**
     * Why a "paid" Xendit invoice must NOT be credited to this transaction, or null when it matches.
     * Checked before every credit (webhook and live status check): the invoice must carry our
     * reference, the same amount to the centavo, and pesos. The webhook token proves the message
     * came from Xendit; this proves it is about this exact payment, for this exact amount.
     */
    public static function xenditInvoiceMismatch(PaymentTransaction $transaction, array $invoice): ?string
    {
        $externalId = $invoice['external_id'] ?? null;
        if ($externalId !== null && $transaction->reference_number && $externalId !== $transaction->reference_number) {
            return 'reference ' . $externalId . ' is not this payment\'s ' . $transaction->reference_number;
        }
        $paid = $invoice['paid_amount'] ?? $invoice['amount'] ?? null;
        if ($paid === null) {
            return 'no amount in the invoice';
        }
        if (abs(round((float) $paid, 2) - round((float) $transaction->amount, 2)) >= 0.01) {
            return 'paid ₱' . number_format((float) $paid, 2) . ' but the payment is for ₱' . number_format((float) $transaction->amount, 2);
        }
        $currency = $invoice['currency'] ?? 'PHP';
        if (strtoupper((string) $currency) !== 'PHP') {
            return 'currency ' . $currency . ', not PHP';
        }
        return null;
    }

    /** Records a refused credit (see xenditInvoiceMismatch) for Admin's Audit Trail and the server log. */
    public static function logXenditMismatch(PaymentTransaction $transaction, string $why): void
    {
        // The status check is polled every few seconds — record each mismatched payment once an hour
        if (!\Illuminate\Support\Facades\Cache::add('xendit-mismatch-' . $transaction->id, true, 3600)) {
            return;
        }
        Log::warning('Xendit payment NOT credited — invoice does not match', ['transaction' => $transaction->id, 'invoice' => $transaction->xendit_invoice_id, 'why' => $why]);
        \App\Services\ActivityLogger::log(
            'error',
            'Xendit payment NOT credited (Invoice: ' . $transaction->xendit_invoice_id . '): ' . $why . ' — please check it in Xendit',
            'PaymentTransaction',
            $transaction->id
        );
    }

    public static function completeXenditPayment(PaymentTransaction $transaction): void
    {
        // Idempotency guard — Xendit can and does redeliver the same "paid"
        // webhook more than once, and the live-status fallback could also
        // race with a webhook arriving at the same time.
        if ($transaction->status === 'completed') {
            return;
        }

        $transaction->update(['status' => 'completed', 'processed_at' => now()]);

        \App\Services\ActivityLogger::log(
            'xendit_payment_completed',
            'Xendit payment of ₱' . number_format($transaction->amount, 2) . ' confirmed (Invoice: ' . $transaction->xendit_invoice_id . ')',
            'PaymentTransaction',
            $transaction->id
        );

        $enrollment = $transaction->enrollment;
        if (!$enrollment) {
            return;
        }

        $enrollment->decrement('remaining_balance', $transaction->amount);
        $enrollment->increment('payment_amount', $transaction->amount);
        $fresh = $enrollment->fresh();

        if (in_array($fresh->payment_option, ['B', 'C', 'D']) || $fresh->payment_type === 'installment') {
            self::createInstallments($fresh);
            self::reconcileInstallmentStatuses($fresh, $transaction);
        }

        self::advanceEnrollmentAfterPayment($fresh->fresh());
    }

    /**
     * Actively asks Xendit for an invoice's real status instead of only
     * trusting our own database — which the webhook alone updates, and the
     * webhook can only ever arrive if Xendit's servers can reach this one
     * over the public internet. On local development (APP_URL pointing at
     * 127.0.0.1, no tunnel) that delivery is simply impossible no matter how
     * correct the webhook code is, so a transaction can sit as "pending"
     * forever even though Xendit's own dashboard already shows it paid.
     * Called from the student portal and cashier "check status" endpoints
     * that the browser polls after opening a payment link, so the page can
     * self-heal regardless of whether the webhook ever gets delivered.
     *
     * Safe to call on an already-resolved transaction (returns immediately)
     * and safe to call repeatedly (locks the row and re-checks before
     * writing, so a webhook that arrives in between wins the race cleanly).
     */
    public static function reconcileXenditInvoice(PaymentTransaction $transaction): string
    {
        if ($transaction->status !== 'pending' || !$transaction->xendit_invoice_id) {
            return $transaction->status;
        }

        $apiKey = config('services.xendit.secret_key');
        if (empty($apiKey)) {
            return $transaction->status;
        }

        try {
            $response = Http::withBasicAuth($apiKey, '')
                ->get('https://api.xendit.co/v2/invoices/' . $transaction->xendit_invoice_id);
        } catch (\Throwable $e) {
            Log::warning('Xendit live status check failed', [
                'invoice_id' => $transaction->xendit_invoice_id,
                'error'      => $e->getMessage(),
            ]);
            return $transaction->status;
        }

        if (!$response->successful()) {
            return $transaction->status;
        }

        $invoice    = (array) $response->json();
        $liveStatus = $invoice['status'] ?? null;

        return DB::transaction(function () use ($transaction, $liveStatus, $invoice) {
            $locked = PaymentTransaction::where('id', $transaction->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'pending') {
                // Already resolved (e.g. a webhook that did get through, or
                // another status check) between our read above and this lock.
                return $locked->status ?? $transaction->status;
            }

            if (in_array($liveStatus, ['PAID', 'SETTLED'])) {
                // same check as the webhook: never credit an invoice that doesn't match this payment
                if ($why = self::xenditInvoiceMismatch($locked, $invoice)) {
                    self::logXenditMismatch($locked, $why);
                    return 'pending';
                }
                self::completeXenditPayment($locked);
                return 'completed';
            }

            if (in_array($liveStatus, ['EXPIRED', 'FAILED'])) {
                $locked->update(['status' => 'expired', 'processed_at' => now()]);
                return 'expired';
            }

            return 'pending';
        });
    }

    /**
     * Sum of remaining_balance across a student's OTHER enrollments — the
     * "prior year(s)" debt that goes invisible once a student is promoted,
     * since Cashier/Finance otherwise only ever show whichever enrollment
     * is current. See docs/SCHOOL-YEAR-VISIBILITY-PHASED-PLAN.md Phase 4.
     * 'declined'/'cancelled' are excluded — neither represents a real debt
     * (a cancelled row is an undone promotion artifact, see
     * EnrollmentController::undoPromotion()).
     *
     * @return array{amount: float, school_year: ?string} school_year is a
     *   comma-joined list when more than one prior year still has a balance.
     */
    public static function priorOutstandingBalance(int $studentId, ?int $excludeEnrollmentId = null): array
    {
        $rows = Enrollment::where('user_id', $studentId)
            ->when($excludeEnrollmentId, fn ($q) => $q->where('id', '!=', $excludeEnrollmentId))
            ->whereNotIn('status', ['declined', 'cancelled'])
            ->where('remaining_balance', '>', 0)
            ->orderByDesc('school_year')
            ->get(['school_year', 'remaining_balance']);

        if ($rows->isEmpty()) {
            return ['amount' => 0.0, 'school_year' => null];
        }

        return [
            'amount'      => (float) $rows->sum('remaining_balance'),
            'school_year' => $rows->pluck('school_year')->unique()->implode(', '),
        ];
    }
}
