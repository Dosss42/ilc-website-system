<?php

namespace App\Console\Commands;

use App\Mail\PaymentDueReminder;
use App\Models\Enrollment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('app:check-payment-due-dates
    {--enrollment= : Only this enrollment ID (demo/testing) — runs even while the automation switch is OFF}
    {--to= : Send every email of this run to this address instead of the students (testing)}')]
#[Description('Email a reminder 3 days before each installment due date, and on the due date itself')]
class CheckPaymentDueDates extends Command
{
    /** Remind this many days before the due date (still sent 2 or 1 days before if that run was missed). */
    const REMIND_DAYS_BEFORE = 3;

    /**
     * Execute the console command.
     *
     * Two reminders per due date: 3 days before (still sent 2 or 1 days before if that run was missed),
     * and on the due date itself ("due today — pay today to avoid the ₱500 late fee"). Three bugs fixed here:
     *  - `diffInDays()` returns a float on Carbon 3, so `=== 3` never matched — no reminder was ever sent;
     *  - the "already reminded" flag was set once and never cleared, so only the first month could get one;
     *  - the amount was total_fee ÷ installment_number, not the month actually due.
     * The morning after the due date, PaymentService::checkAndProcessOverduePayments() takes over
     * (₱500 late fee, then weekly reminders).
     */
    public function handle()
    {
        $enrollmentId = $this->option('enrollment') ? (int) $this->option('enrollment') : null;

        // The switch (Super Admin › System Settings) guards every family; only a one-student test run may pass it
        if (!$enrollmentId && !\App\Services\PaymentService::automationEnabled()) {
            $this->warn('Payment automation is OFF (Super Admin › System Settings) — nothing was done. '
                . 'To test with one student: --enrollment=<id> [--to=you@gmail.com]');
            return Command::SUCCESS;
        }
        if ($to = $this->option('to')) {
            Mail::alwaysTo($to);
            $this->line("All emails of this run go to {$to}.");
        }

        $this->info('Checking payment due dates' . ($enrollmentId ? " (enrollment #{$enrollmentId} only)" : '') . '...');

        $today = now()->startOfDay();
        $windowEnd = $today->copy()->addDays(self::REMIND_DAYS_BEFORE);

        // Students with an unpaid month due today or in the next 1–3 days. (Looks at the installment rows,
        // not next_installment_date: that stays on the oldest unpaid month, so while July was overdue the
        // family got no reminder before August.)
        $installmentEnrollments = Enrollment::where(fn ($q) => $q->where('payment_type', 'installment')->orWhereIn('payment_option', ['B', 'C', 'D']))
            ->where('payment_status', '!=', 'paid')
            ->whereNotIn('status', ['declined', 'cancelled'])
            ->when($enrollmentId, fn ($q) => $q->whereKey($enrollmentId))
            ->whereHas('paymentInstallments', fn ($q) => $q->where('status', 'pending')
                ->whereDate('due_date', '>=', $today)->whereDate('due_date', '<=', $windowEnd))
            ->get();

        $reminderCount = 0;

        foreach ($installmentEnrollments as $enrollment) {
            $user = $enrollment->user;
            if (!$user || !$user->email) continue;

            $upcoming = $enrollment->paymentInstallments()->where('status', 'pending')
                ->whereDate('due_date', '>=', $today)->whereDate('due_date', '<=', $windowEnd)->orderBy('due_date')->first();
            $nextDueDate  = $upcoming->due_date->copy()->startOfDay();
            $daysUntilDue = (int) round($today->diffInDays($nextDueDate, false));

            // Due today → the "due today" reminder, once today. 1–3 days before → the early reminder, once
            // for this due date (a reminder for an earlier month was sent before this window).
            $lastSent = $enrollment->reminder_sent_at?->copy()->startOfDay();
            $alreadyReminded = $daysUntilDue === 0
                ? ($lastSent && $lastSent->gte($today))
                : ($lastSent && $lastSent->gte($nextDueDate->copy()->subDays(self::REMIND_DAYS_BEFORE)));
            if ($alreadyReminded) {
                continue;
            }

            // Amount to have paid by that date: this month plus any months still overdue before it —
            // the matching "due now + N months" option the cashier and the portal accept
            $monthsUpTo = $enrollment->paymentInstallments()->whereIn('status', ['pending', 'overdue'])
                ->whereDate('due_date', '<=', $nextDueDate)->count();
            $options    = \App\Services\PaymentService::paymentLimits($enrollment)['allowed'];
            $amountDue  = $options[$monthsUpTo - 1]['amount'] ?? (end($options)['amount'] ?? null);

            try {
                Mail::to($user->email)->send(new PaymentDueReminder($enrollment, $daysUntilDue, $nextDueDate, $amountDue));
                $enrollment->update(['reminder_sent' => true, 'reminder_sent_at' => now()]);
                $reminderCount++;
                $this->info("Reminder sent to {$user->name} for due date {$nextDueDate->format('M d, Y')}");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder email to user {$user->id}: " . $e->getMessage());
            }

            // Penalty-after-3-days / block-after-3-"late-payments" logic that
            // used to live here has been removed — it incremented
            // late_payment_count and penalty_amount on EVERY run while an
            // installment stayed overdue (no once-per-occurrence guard), so
            // a single missed due date reached the "3 strikes" block after
            // just 3 CALENDAR DAYS, not 3 months. That directly contradicted
            // the actual, UI-visible Exam Permit Hold policy
            // (PaymentService::getExamPermitStatus — 3 CONSECUTIVE MONTHS,
            // with Promissory Note recourse). Severity-based consequences for
            // being overdue now live exclusively in the Exam Permit Hold
            // system, which already has the correct threshold and a
            // resolution path (Promissory Note).
        }

        $this->info("Payment due date check completed.");
        $this->info("Reminders sent: {$reminderCount}");

        return Command::SUCCESS;
    }
}
