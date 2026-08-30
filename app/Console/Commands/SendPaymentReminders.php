<?php

namespace App\Console\Commands;

use App\Mail\PaymentDueReminder;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send email reminders for payments due within 3 days';

    public function handle(): int
    {
        $today = Carbon::today();
        $threeDaysFromNow = $today->copy()->addDays(3);

        // Find enrollments with upcoming due dates (full payment or installment)
        $enrollments = Enrollment::with('user')
            ->where('payment_status', '!=', 'paid')
            ->where(function ($query) use ($today, $threeDaysFromNow) {
                // Full payments with payment_due_date in the next 3 days
                $query->where(function ($q) use ($today, $threeDaysFromNow) {
                    $q->where('payment_type', 'full')
                      ->whereNotNull('payment_due_date')
                      ->whereBetween('payment_due_date', [$today, $threeDaysFromNow]);
                })
                // Installments with next_installment_date in the next 3 days
                ->orWhere(function ($q) use ($today, $threeDaysFromNow) {
                    $q->where('payment_type', 'installment')
                      ->whereNotNull('next_installment_date')
                      ->whereBetween('next_installment_date', [$today, $threeDaysFromNow]);
                });
            })
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($enrollments as $enrollment) {
            // Skip if no user or no email
            if (!$enrollment->user || !$enrollment->user->email) {
                $skipped++;
                continue;
            }

            // Determine the relevant due date
            $dueDate = $enrollment->payment_type === 'installment'
                ? $enrollment->next_installment_date
                : $enrollment->payment_due_date;

            if (!$dueDate) {
                $skipped++;
                continue;
            }

            $daysUntilDue = (int) $today->diffInDays($dueDate, false);

            // Skip if already sent a reminder today
            if ($enrollment->reminder_sent && $enrollment->reminder_sent_at?->isToday()) {
                $skipped++;
                continue;
            }

            try {
                Mail::to($enrollment->user->email)
                    ->queue(new PaymentDueReminder($enrollment, $daysUntilDue));

                $enrollment->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => now(),
                ]);

                $sent++;
                $this->info("Reminder sent to {$enrollment->user->email} (due in {$daysUntilDue} days)");
            } catch (\Exception $e) {
                Log::error("Failed to send payment reminder for enrollment #{$enrollment->id}: " . $e->getMessage());
                $this->error("Failed: {$enrollment->user->email} - {$e->getMessage()}");
            }
        }

        $this->info("Payment reminders complete: {$sent} sent, {$skipped} skipped.");
        Log::info("Payment reminders: {$sent} sent, {$skipped} skipped.");

        return Command::SUCCESS;
    }
}
