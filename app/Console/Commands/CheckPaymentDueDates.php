<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('app:check-payment-due-dates')]
#[Description('Check payment due dates and send reminders, apply penalties, or block accounts')]
class CheckPaymentDueDates extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking payment due dates...');

        // Get enrollments with installment payments
        $installmentEnrollments = Enrollment::where('payment_type', 'installment')
            ->whereNotNull('next_installment_date')
            ->where('payment_status', '!=', 'paid')
            ->get();

        $reminderCount = 0;

        foreach ($installmentEnrollments as $enrollment) {
            $user = $enrollment->user;
            if (!$user) continue;

            $nextDueDate = $enrollment->next_installment_date;
            $today = now()->startOfDay();
            $daysUntilDue = $today->diffInDays($nextDueDate, false);

            // Send reminder 3 days before due date
            if ($daysUntilDue === 3 && !$enrollment->reminder_sent) {
                $this->sendReminderEmail($user, $enrollment, $nextDueDate);
                $enrollment->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => now(),
                ]);
                $reminderCount++;
                $this->info("Reminder sent to {$user->name} for due date {$nextDueDate->format('M d, Y')}");
            }

            // Penalty-after-3-days / block-after-3-"late-payments" logic that
            // used to live here has been removed — it incremented
            // late_payment_count and penalty_amount on EVERY run while an
            // installment stayed overdue (no once-per-occurrence guard), so
            // a single missed due date reached the "3 strikes" block after
            // just 3 CALENDAR DAYS, not 3 months. That directly contradicted
            // the actual, UI-visible Exam Permit Hold policy
            // (PaymentService::getExamPermitStatus — 3 CONSECUTIVE MONTHS,
            // with Promissory Note recourse), and once account_blocked was
            // set, StudentPortalController::processPayment() refused ALL
            // payments — locking an overdue family out of the one thing
            // that would let them fix it. penalty_amount/late_payment_count
            // were also never displayed anywhere, so neither staff nor the
            // family could ever see why. Severity-based consequences for
            // being overdue now live exclusively in the Exam Permit Hold
            // system, which already has the correct threshold and a
            // resolution path (Promissory Note).
        }

        $this->info("Payment due date check completed.");
        $this->info("Reminders sent: {$reminderCount}");

        return Command::SUCCESS;
    }

    private function sendReminderEmail($user, $enrollment, $dueDate)
    {
        try {
            Mail::raw(
                "Dear {$user->name},\n\n" .
                "This is a reminder that your next installment payment of ₱" . number_format($enrollment->total_fee / ($enrollment->installment_number ?: 1), 2) . " is due on {$dueDate->format('M d, Y')}.\n\n" .
                "Please ensure payment is made on time to avoid late fees.\n\n" .
                "Thank you,\n" .
                "IEMELIF Learning Center",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Payment Reminder - IEMELIF Learning Center');
                }
            );
            Log::info("Reminder email sent to user {$user->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send reminder email to user {$user->id}: " . $e->getMessage());
        }
    }

}
