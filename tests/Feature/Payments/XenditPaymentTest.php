<?php

namespace Tests\Feature\Payments;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_generate_a_xendit_payment_link(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create();

        Http::fake([
            'api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-123',
                'invoice_url' => 'https://checkout.xendit.co/web/inv-test-123',
                'expiry_date' => now()->addDay()->toIso8601String(),
            ], 200),
        ]);

        $response = $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/xendit-link', [
                'enrollment_id' => $enrollment->id,
                'amount' => 5000,
                'payment_type' => 'Downpayment',
                'payment_method' => 'gcash',
                'student_name' => 'Juan Cruz',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'invoice_id' => 'inv-test-123']);

        $this->assertDatabaseHas('payment_transactions', [
            'enrollment_id' => $enrollment->id,
            'xendit_invoice_id' => 'inv-test-123',
            'status' => 'pending',
        ]);
    }

    public function test_webhook_completes_a_pending_xendit_payment(): void
    {
        $enrollment = Enrollment::factory()->create([
            'total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504,
        ]);
        $transaction = PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF', 'xendit_invoice_id' => 'inv-webhook-test',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/cashier/webhook/xendit', [
            'id' => 'inv-webhook-test',
            'external_id' => 'ILC-' . $enrollment->id,
            'status' => 'PAID',
        ], [
            'x-callback-token' => config('services.xendit.webhook_token'),
        ]);

        $response->assertOk();

        $transaction->refresh();
        $this->assertEquals('completed', $transaction->status);

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->payment_amount);
        $this->assertEquals(9504, $enrollment->remaining_balance);
    }

    public function test_webhook_rejects_requests_with_wrong_signature(): void
    {
        $enrollment = Enrollment::factory()->create();
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-2', 'xendit_invoice_id' => 'inv-bad-sig',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/cashier/webhook/xendit', [
            'id' => 'inv-bad-sig', 'status' => 'PAID',
        ], ['x-callback-token' => 'wrong-token']);

        $response->assertStatus(401);
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-bad-sig', 'status' => 'pending']);
    }

    /**
     * This is the core resilience feature: on a local/non-public-URL dev
     * environment, Xendit's webhook can never be delivered — the polling
     * endpoint the browser calls must be able to self-heal by asking
     * Xendit's own API directly whenever our local copy still says pending.
     */
    public function test_status_check_reconciles_against_xendit_when_webhook_never_arrived(): void
    {
        $enrollment = Enrollment::factory()->create([
            'total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504,
        ]);
        $student = User::find($enrollment->user_id);
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-3', 'xendit_invoice_id' => 'inv-reconcile-test',
            'status' => 'pending',
        ]);

        // No webhook ever fires. Xendit's own API says PAID when we ask it directly.
        Http::fake([
            'api.xendit.co/*' => Http::response(['id' => 'inv-reconcile-test', 'status' => 'PAID'], 200),
        ]);

        $response = $this->actingAs($student, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-reconcile-test');

        $response->assertOk();
        $response->assertJson(['status' => 'completed']);

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->payment_amount);
        $this->assertEquals('partial', $enrollment->payment_status);
    }

    public function test_status_check_marks_expired_invoice_as_expired(): void
    {
        $enrollment = Enrollment::factory()->create();
        $student = User::find($enrollment->user_id);
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-4', 'xendit_invoice_id' => 'inv-expired-test',
            'status' => 'pending',
        ]);

        Http::fake([
            'api.xendit.co/*' => Http::response(['id' => 'inv-expired-test', 'status' => 'EXPIRED'], 200),
        ]);

        $this->actingAs($student, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-expired-test')
            ->assertOk()
            ->assertJson(['status' => 'expired']);

        $this->assertDatabaseHas('payment_transactions', [
            'xendit_invoice_id' => 'inv-expired-test', 'status' => 'expired',
        ]);
    }

    public function test_status_check_does_not_leak_another_students_transaction(): void
    {
        $enrollment = Enrollment::factory()->create();
        $owner = User::find($enrollment->user_id);
        $intruder = User::factory()->student()->create();

        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $owner->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-5', 'xendit_invoice_id' => 'inv-private-test',
            'status' => 'pending',
        ]);

        $this->actingAs($intruder, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-private-test')
            ->assertStatus(404);
    }
}
