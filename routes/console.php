<?php

use Illuminate\Support\Facades\Schedule;

// Check payment due dates and send reminders, apply penalties, or block accounts daily at 8:00 AM
Schedule::command('app:check-payment-due-dates')->dailyAt('08:00');
