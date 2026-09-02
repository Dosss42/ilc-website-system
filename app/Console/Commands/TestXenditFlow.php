<?php

namespace App\Console\Commands;

use App\Http\Controllers\CashierController;
use App\Http\Controllers\StudentPortalController;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Throwaway dev helper: drives the real student-portal Xendit flow
 * (invoice creation, and optionally the paid webhook) without clicking
 * through the UI. Safe to delete once Xendit testing is done.
 *
 *   php artisan xendit:test
 *   php artisan xendit:test --method=gcash --amount=500 --complete
 *   php artisan xendit:test --user=42
 */
class TestXenditFlow extends Command
{
    protected $signature = 'xendit:test
        {--method=gcash : gcash|maya|grabpay|bank|otc}
        {--amount=500 : Test amount in PHP}
        {--complete : Also simulate the "PAID" webhook locally to close the loop}
        {--user= : Use an existing user ID instead of the throwaway test student}';

    protected $description = 'Create a real Xendit sandbox invoice via the student-portal flow, and optionally simulate the paid webhook.';

    public function handle(): int
    {
        $secretKey = config('services.xendit.secret_key');

        if (empty($secretKey)) {
            $this->error('XENDIT_SECRET_KEY is not set in .env — nothing to test.');
            return self::FAILURE;
        }

        if (!str_starts_with($secretKey, 'xnd_development_')) {
            if (!$this->confirm('XENDIT_SECRET_KEY does not look like a sandbox key (expected "xnd_development_..."). Continue anyway?', false)) {
                return self::FAILURE;
            }
        }

        [$user, $enrollment] = $this->resolveTestSubject();

        $this->info("Using user #{$user->id} ({$user->email}), enrollment #{$enrollment->id} — balance ₱" . number_format($enrollment->remaining_balance, 2));

        $request = Request::create('/student/payment/xendit-link', 'POST', [
            'enrollment_id'  => $enrollment->id,
            'amount'         => (float) $this->option('amount'),
            'payment_method' => $this->option('method'),
            'payment_type'   => 'test',
        ]);

        Auth::login($user);
        $request->setUserResolver(fn () => $user);

        $this->line('Calling Xendit invoice API...');
        $response = app(StudentPortalController::class)->generateXenditLink($request);
        $data = json_decode($response->getContent(), true);

        if (!($data['success'] ?? false)) {
            $this->error('Invoice creation failed: ' . ($data['message'] ?? 'unknown error'));
            $this->line(json_encode($data));
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Invoice created.');
        $this->line('  invoice_id:  ' . $data['invoice_id']);
        $this->line('  invoice_url: ' . $data['invoice_url']);
        $this->line('  -> open invoice_url in a browser to pay it via the sandbox checkout.');

        if ($this->option('complete')) {
            $this->newLine();
            $this->info('Simulating the "PAID" webhook locally...');

            $webhookRequest = Request::create('/cashier/webhook/xendit', 'POST', [
                'id'     => $data['invoice_id'],
                'status' => 'PAID',
            ]);
            $webhookRequest->headers->set('x-callback-token', config('services.xendit.webhook_token'));

            $webhookResponse = app(CashierController::class)->xenditWebhook($webhookRequest);
            $this->line('  webhook response: ' . $webhookResponse->getContent());

            $enrollment->refresh();
            $this->line('  payment_amount now:    ₱' . number_format($enrollment->payment_amount, 2));
            $this->line('  remaining_balance now: ₱' . number_format($enrollment->remaining_balance, 2));
            $this->line('  payment_status now:    ' . $enrollment->payment_status);
        } else {
            $this->newLine();
            $this->comment('Re-run with --complete to also simulate the paid webhook and see balances update.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array{0: User, 1: Enrollment}
     */
    private function resolveTestSubject(): array
    {
        if ($userId = $this->option('user')) {
            $user = User::findOrFail($userId);
            $enrollment = Enrollment::where('user_id', $user->id)->latest()->firstOrFail();
            return [$user, $enrollment];
        }

        $user = User::firstOrCreate(
            ['email' => 'xendit-test@example.test'],
            [
                'name'      => 'Xendit Test Student',
                'password'  => Hash::make('password'),
                'role'      => 'student',
                'is_active' => true,
            ]
        );

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'school_year' => '2025-2026'],
            [
                'reference_number'  => 'XENDITTEST-' . $user->id,
                'status'            => 'enrolled',
                'student_data'      => ['first_name' => 'Xendit', 'last_name' => 'Test'],
                'grade_level'       => 'grade1',
                'total_fee'         => 10000,
                'payment_amount'    => 0,
                'remaining_balance' => 10000,
                'payment_status'    => 'pending',
                'payment_type'      => 'installment',
                'payment_option'    => 'B',
            ]
        );

        return [$user, $enrollment];
    }
}
