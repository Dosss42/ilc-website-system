<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\PaymentService;

class CheckOverduePayments extends Command
{
    protected $signature = 'payments:check-overdue
        {--enrollment= : Only this enrollment ID (demo/testing) — runs even while the automation switch is OFF}
        {--to= : Send every email of this run to this address instead of the students (testing)}';
    protected $description = 'Check for overdue payments, apply late fees, send notifications, and block accounts if necessary';

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    public function handle(): int
    {
        $enrollmentId = $this->option('enrollment') ? (int) $this->option('enrollment') : null;

        // The switch (Super Admin › System Settings) guards every family; only a one-student test run may pass it
        if (!$enrollmentId && !PaymentService::automationEnabled()) {
            $this->warn('Payment automation is OFF (Super Admin › System Settings) — nothing was done. '
                . 'To test with one student: --enrollment=<id> [--to=you@gmail.com]');
            return self::SUCCESS;
        }
        if ($to = $this->option('to')) {
            Mail::alwaysTo($to);
            $this->line("All emails of this run go to {$to}.");
        }

        $this->info('Checking for overdue payments' . ($enrollmentId ? " (enrollment #{$enrollmentId} only)" : '') . '...');

        try {
            $this->paymentService->checkAndProcessOverduePayments($enrollmentId);
            $this->info('Overdue payment check completed successfully.');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error checking overdue payments: ' . $e->getMessage());
            \Log::error('CheckOverduePayments command failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
