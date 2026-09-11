<?php

namespace Tests\Feature\Payments;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_record_a_cash_payment(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create([
            'total_fee' => 14504,
            'payment_amount' => 0,
            'remaining_balance' => 14504,
        ]);

        $response = $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 5000,
                'payment_type' => 'Downpayment',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->payment_amount);
        $this->assertEquals(9504, $enrollment->remaining_balance);
        $this->assertEquals('partial', $enrollment->payment_status);

        $this->assertDatabaseHas('payment_transactions', [
            'enrollment_id' => $enrollment->id,
            'payment_method' => 'cash',
            'amount' => 5000,
            'status' => 'completed',
            'processed_by' => $cashier->id,
        ]);
    }

    public function test_full_cash_payment_marks_enrollment_paid_and_enrolled(): void
    {
        $cashier = User::factory()->cashier()->create();
        $student = User::factory()->student()->create();
        $enrollment = Enrollment::factory()->create([
            'user_id' => $student->id,
            'status' => 'approved',
            'total_fee' => 14504,
            'payment_amount' => 0,
            'remaining_balance' => 14504,
        ]);

        $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 14504,
                'payment_type' => 'Full Payment',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $enrollment->refresh();
        $this->assertEquals('paid', $enrollment->payment_status);
        $this->assertEquals(0, $enrollment->remaining_balance);
        $this->assertEquals('enrolled', $enrollment->status);
    }

    public function test_cash_payment_is_logged_to_the_cashiers_audit_trail(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 1000,
                'payment_type' => 'Partial',
            ])
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $cashier->id,
            'event_type' => 'cash_payment',
        ]);

        // Confirm it shows up through the actual Audit Trail endpoint too.
        $response = $this->actingAs($cashier, 'cashier')->getJson('/cashier/audit-trail');
        $response->assertOk();
        $this->assertTrue(collect($response->json())->contains(fn ($l) => $l['event_type'] === 'cash_payment'));
    }

    public function test_processing_cash_payment_requires_cashier_authentication(): void
    {
        $enrollment = Enrollment::factory()->create();

        // No one logged in at all.
        $this->postJson('/cashier/payment/cash', [
            'enrollment_id' => $enrollment->id,
            'amount' => 1000,
            'payment_type' => 'Partial',
        ])->assertStatus(401);

        // A student is logged in, but not on the cashier guard.
        $student = User::factory()->student()->create();
        $this->actingAs($student, 'web')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 1000,
                'payment_type' => 'Partial',
            ])->assertStatus(401);
    }
}
