<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService;

class CheckOverduePayments extends Command
{
    protected $signature = 'payments:check-overdue';
    protected $description = 'Check for overdue payments, apply late fees, send notifications, and block accounts if necessary';

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    public function handle(): int
    {
        $this->info('Checking for overdue payments...');
        
        try {
            $this->paymentService->checkAndProcessOverduePayments();
            $this->info('Overdue payment check completed successfully.');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error checking overdue payments: ' . $e->getMessage());
            \Log::error('CheckOverduePayments command failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
