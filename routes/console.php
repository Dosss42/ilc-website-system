<?php

use Illuminate\Support\Facades\Schedule;

// Send a reminder 3 days before an installment's due date.
Schedule::command('app:check-payment-due-dates')->dailyAt('08:00');

// Process genuinely overdue installments: marks them 'overdue', sends the
// warning (week 1) / reminder (week 2) emails, and applies the documented
// ₱500 late fee after 3 weeks. This was written but never actually
// scheduled anywhere — confirmed live: real overdue installments existed
// with weeks_overdue/late_fee/warning_sent all still at their zero/default
// values, meaning no student had ever received an automatic reminder or
// had the late fee applied. Runs after the reminder check above so a
// student who's 3 days from due and one who's genuinely overdue both get
// handled in the same daily batch window.
Schedule::command('payments:check-overdue')->dailyAt('08:10');

// Automatic database backup — no one has to remember to click the button.
// Runs at 2 AM (low-traffic hour), then copies to the secondary drive
// and/or Google Drive if configured, and prunes old backups. Requires
// Windows Task Scheduler to run `php artisan schedule:run` every minute —
// see BACKUP_SETUP.md.
Schedule::command('backup:run')->dailyAt('02:00');
