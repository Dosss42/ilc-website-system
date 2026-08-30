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
        $penaltyCount = 0;
        $blockedCount = 0;

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

            // Apply penalty if payment is late (after due date)
            if ($daysUntilDue < 0 && $enrollment->late_payment_count < 3) {
                $penaltyAmount = 500; // ₱500 penalty per late payment
                $enrollment->update([
                    'penalty_amount' => ($enrollment->penalty_amount ?? 0) + $penaltyAmount,
                    'late_payment_count' => ($enrollment->late_payment_count ?? 0) + 1,
                ]);
                $penaltyCount++;
                $this->info("Penalty applied to {$user->name}. Total penalties: ₱" . number_format($enrollment->penalty_amount, 2));

                // Send penalty notification email
                $this->sendPenaltyEmail($user, $enrollment, $penaltyAmount);
            }

            // Block account after 3 late payments
            if ($enrollment->late_payment_count >= 3 && !$enrollment->account_blocked) {
                $enrollment->update(['account_blocked' => true]);
                $user->update(['blocked' => true]);
                $blockedCount++;
                $this->info("Account blocked for {$user->name} due to multiple late payments");

                // Send account blocking notification email
                $this->sendBlockAccountEmail($user, $enrollment);
            }
        }

        $this->info("Payment due date check completed.");
        $this->info("Reminders sent: {$reminderCount}");
        $this->info("Penalties applied: {$penaltyCount}");
        $this->info("Accounts blocked: {$blockedCount}");

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

    private function sendPenaltyEmail($user, $enrollment, $penaltyAmount)
    {
        try {
            Mail::raw(
                "Dear {$user->name},\n\n" .
                "Your payment was overdue. A penalty fee of ₱" . number_format($penaltyAmount, 2) . " has been applied to your account.\n\n" .
                "Total penalty amount: ₱" . number_format($enrollment->penalty_amount, 2) . "\n" .
                "Late payment count: {$enrollment->late_payment_count}/3\n\n" .
                "Please make your payment as soon as possible. After 3 late payments, your account will be blocked.\n\n" .
                "Thank you,\n" .
                "IEMELIF Learning Center",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Late Payment Penalty Applied - IEMELIF Learning Center');
                }
            );
            Log::info("Penalty email sent to user {$user->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send penalty email to user {$user->id}: " . $e->getMessage());
        }
    }

    private function sendBlockAccountEmail($user, $enrollment)
    {
        try {
            Mail::raw(
                "Dear {$user->name},\n\n" .
                "Your account has been blocked due to multiple late payments (3 or more).\n\n" .
                "To unblock your account, please contact the school administration and settle all outstanding payments including penalties.\n\n" .
                "Total penalty amount: ₱" . number_format($enrollment->penalty_amount, 2) . "\n\n" .
                "Thank you,\n" .
                "IEMELIF Learning Center",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Account Blocked - IEMELIF Learning Center');
                }
            );
            Log::info("Account block email sent to user {$user->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send account block email to user {$user->id}: " . $e->getMessage());
        }
    }
}
