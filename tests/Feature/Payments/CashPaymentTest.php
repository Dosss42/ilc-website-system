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
        // Installment plan: the ₱5,000 down payment is the amount due now
        $enrollment = Enrollment::factory()->create([
            'payment_type' => 'installment',
            'payment_option' => 'B',
            'downpayment_amount' => 5000,
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

    public function test_cash_payment_is_logged_to_the_audit_trail(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 14504, // pay-in-full plan: the whole balance is due
                'payment_type' => 'Full Payment',
            ])
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $cashier->id,
            'event_type' => 'cash_payment',
        ]);

        // Cashiers no longer view an audit trail; the entry shows up on the Admin's
        $this->actingAs($cashier, 'cashier')->getJson('/cashier/audit-trail')->assertNotFound();
        $this->actingAs(User::factory()->admin()->create(), 'web')
            ->get('/admin/section/audit?audit_type=cash_payment')->assertOk()
            ->assertSee('Cash Payment')->assertSee($cashier->name);
    }

    public function test_cash_payment_is_refused_when_no_fee_plan_is_set(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create([
            'status' => 'approved',
            'payment_option' => null,
            'total_fee' => 0,
            'downpayment_amount' => 0,
            'payment_amount' => 0,
            'remaining_balance' => 0,
        ]);

        $response = $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 100,
                'payment_type' => 'Downpayment',
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        $enrollment->refresh();
        $this->assertNotEquals('enrolled', $enrollment->status);
        $this->assertDatabaseMissing('payment_transactions', [
            'enrollment_id' => $enrollment->id,
        ]);
    }

    public function test_cash_payment_is_refused_when_an_option_is_set_but_total_fee_is_zero(): void
    {
        // Guards against the no-fee-plan check regressing back to only
        // looking at payment_option — an option chosen with no real total
        // (total_fee still 0) must be refused too, not just a fully blank plan.
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create([
            'status' => 'approved',
            'payment_option' => 'A',
            'total_fee' => 0,
            'downpayment_amount' => 0,
            'payment_amount' => 0,
            'remaining_balance' => 0,
        ]);

        $response = $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/cash', [
                'enrollment_id' => $enrollment->id,
                'amount' => 100,
                'payment_type' => 'Downpayment',
            ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('payment_transactions', [
            'enrollment_id' => $enrollment->id,
        ]);
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
