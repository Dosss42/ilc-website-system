<?php

use App\Services\PaymentService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// The scheduler runs from docker/entrypoint.sh on Railway (`php artisan schedule:work`) and
// from Windows Task Scheduler on XAMPP (`php artisan schedule:run` every minute). The old
// trigger, cron-job.org calling /cron/run-scheduler, may still be active too — onOneServer()
// makes each daily job run once per scheduled time even when both fire in the same minute.
// Times are in the app timezone (Asia/Manila).

// Heartbeat: lets Super Admin › System Settings show whether the scheduler is really running.
Schedule::call(fn () => \App\Models\Setting::set('scheduler_last_run', now()->toDateTimeString()))
    ->everyMinute()->name('scheduler-heartbeat');

// ── Payment automation — only while a Super Admin has switched it on
// (System Settings › Automatic Payment Reminders & Late Fees). Off by default: on test data
// it would email made-up addresses and add late fees that aren't real.

// Send a reminder 3 days before an installment's due date.
Schedule::command('app:check-payment-due-dates')->dailyAt('08:00')->onOneServer()
    ->when(fn () => PaymentService::automationEnabled());

// Process genuinely overdue installments: marks them 'overdue', sends the
// warning (week 1) / reminder (week 2) emails, and applies the documented
// ₱500 late fee after 3 weeks. Runs after the reminder check above so a
// student who's 3 days from due and one who's genuinely overdue both get
// handled in the same daily batch window.
Schedule::command('payments:check-overdue')->dailyAt('08:10')->onOneServer()
    ->when(fn () => PaymentService::automationEnabled());

// Automatic database backup — no one has to remember to click the button.
// Runs at 2 AM (low-traffic hour), then copies to the secondary drive
// and/or Google Drive if configured, and prunes old backups.
Schedule::command('backup:run')->dailyAt('02:00')->withoutOverlapping()->onOneServer();

// Automatic clearing of old audit logs (period set by the Super Admin in System Logs).
// Also runs when an Admin/Super Admin opens the logs, in case the scheduler isn't running.
Artisan::command('audit:clear', function () {
    $n = \App\Services\AuditLogRetention::clear();
    $this->info("Keep: " . \App\Services\AuditLogRetention::label() . " — {$n} old audit log(s) cleared.");
})->purpose('Delete audit logs older than the retention period');
Schedule::command('audit:clear')->dailyAt('02:30')->onOneServer();
