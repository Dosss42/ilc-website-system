<?php

use Illuminate\Support\Facades\Schedule;

// Check payment due dates and send reminders, apply penalties, or block accounts daily at 8:00 AM
Schedule::command('app:check-payment-due-dates')->dailyAt('08:00');

// Automatic database backup — no one has to remember to click the button.
// Runs at 2 AM (low-traffic hour), then copies to the secondary drive
// and/or Google Drive if configured, and prunes old backups. Requires
// Windows Task Scheduler to run `php artisan schedule:run` every minute —
// see BACKUP_SETUP.md.
Schedule::command('backup:run')->dailyAt('02:00');
