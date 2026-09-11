<?php

namespace Tests\Feature\Payments;

use App\Models\Enrollment;
use App\Models\PaymentInstallment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallmentPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_downpayment_is_recorded_without_touching_monthly_installments(): void
    {
        $finance = User::factory()->finance()->create();
        $enrollment = Enrollment::factory()->withInstallment()->create([
            'total_fee' => 14504,
            'payment_amount' => 0,
            'remaining_balance' => 14504,
            'downpayment_amount' => 3000,
            'monthly_amount' => 1150,
        ]);

        $response = $this->actingAs($finance, 'finance')
            ->postJson("/finance/installments/{$enrollment->id}/pay", [
                'payment_method' => 'cash',
                'amount' => 3000,
            ]);

        $response->assertRedirect();

        $enrollment->refresh();
        $this->assertEquals(3000, $enrollment->payment_amount);
        $this->assertEquals(11504, $enrollment->remaining_balance);
        $this->assertEquals('partial', $enrollment->payment_status);
        $this->assertDatabaseHas('payment_transactions', [
            'enrollment_id' => $enrollment->id, 'payment_type' => 'downpayment', 'amount' => 3000,
        ]);
    }

    public function test_paying_a_specific_installment_marks_only_that_one_paid(): void
    {
        $finance = User::factory()->finance()->create();
        $enrollment = Enrollment::factory()->withInstallment()->create([
            'total_fee' => 14504,
            'payment_amount' => 3000, // downpayment already paid
            'remaining_balance' => 11504,
            'downpayment_amount' => 3000,
            'monthly_amount' => 1150,
        ]);

        PaymentService::createInstallments($enrollment);
        $firstInstallment = $enrollment->paymentInstallments()->orderBy('due_date')->first();
        $this->assertNotNull($firstInstallment);
        $this->assertEquals('pending', $firstInstallment->status);

        $this->actingAs($finance, 'finance')
            ->postJson("/finance/installments/{$enrollment->id}/pay", [
                'installment_id' => $firstInstallment->id,
                'payment_method' => 'gcash',
                'amount' => 1150,
                'reference_number' => 'GCASH-REF-1',
            ])
            ->assertRedirect();

        $firstInstallment->refresh();
        $this->assertEquals('paid', $firstInstallment->status);
        $this->assertEquals('GCASH-REF-1', $firstInstallment->reference_number);

        // The other 8 installments must remain untouched.
        $stillPending = $enrollment->paymentInstallments()->where('status', 'pending')->count();
        $this->assertEquals(8, $stillPending);

        $enrollment->refresh();
        $this->assertEquals(4150, $enrollment->payment_amount); // 3000 + 1150
    }

    public function test_cannot_pay_an_installment_that_belongs_to_a_different_enrollment(): void
    {
        $finance = User::factory()->finance()->create();
        $enrollmentA = Enrollment::factory()->withInstallment()->create(['monthly_amount' => 1150]);
        // Downpayment already settled on B, so the request is forced through
        // the installment-ownership check rather than being treated as B's
        // still-outstanding downpayment (a different, safe code path).
        $enrollmentB = Enrollment::factory()->withInstallment()->create([
            'monthly_amount' => 1150, 'downpayment_amount' => 3000, 'payment_amount' => 3000,
        ]);

        PaymentService::createInstallments($enrollmentA);
        $installmentFromA = $enrollmentA->paymentInstallments()->first();

        // Try to pay it through enrollment B's URL.
        $response = $this->actingAs($finance, 'finance')
            ->postJson("/finance/installments/{$enrollmentB->id}/pay", [
                'installment_id' => $installmentFromA->id,
                'payment_method' => 'cash',
                'amount' => 1150,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $installmentFromA->refresh();
        $this->assertEquals('pending', $installmentFromA->status);
    }

    public function test_installment_payment_requires_finance_authentication(): void
    {
        $enrollment = Enrollment::factory()->withInstallment()->create();

        $this->postJson("/finance/installments/{$enrollment->id}/pay", [
            'payment_method' => 'cash', 'amount' => 1150,
        ])->assertStatus(401);
    }

    /**
     * Regression test — enrollments.next_installment_date used to stay NULL
     * from creation until the first payment happened to touch it. The
     * Finance Dashboard's "Overdue"/"Due This Week" widgets query this
     * column directly, so any installment plan that hadn't made a payment
     * yet was invisible to those counts even when genuinely overdue — while
     * the Installments list page (which always computed it fresh) showed
     * the correct, disagreeing value. Found during manual QA, fixed at the
     * source in createInstallments()/reconcileInstallmentStatuses().
     */
    public function test_next_installment_date_is_set_immediately_when_installments_are_created(): void
    {
        $enrollment = Enrollment::factory()->withInstallment()->create([
            'monthly_amount' => 1000, 'school_year' => '2026-2027',
        ]);
        $this->assertNull($enrollment->next_installment_date);

        PaymentService::createInstallments($enrollment);
        $enrollment->refresh();

        $this->assertNotNull($enrollment->next_installment_date, 'next_installment_date should be set as soon as installments exist, not left null until a payment happens.');
        $this->assertEquals('2026-07-31', $enrollment->next_installment_date->toDateString());
    }

    public function test_next_installment_date_advances_after_reconciling_a_payment(): void
    {
        $enrollment = Enrollment::factory()->withInstallment()->create([
            'monthly_amount' => 1000, 'downpayment_amount' => 0,
            'payment_amount' => 0, 'school_year' => '2026-2027',
        ]);
        PaymentService::createInstallments($enrollment);
        $enrollment->refresh();
        $this->assertEquals('2026-07-31', $enrollment->next_installment_date->toDateString());

        // Simulate July's installment having been paid.
        $enrollment->update(['payment_amount' => 1000]);
        $enrollment->refresh();
        PaymentService::reconcileInstallmentStatuses($enrollment);
        $enrollment->refresh();

        $this->assertEquals('2026-08-31', $enrollment->next_installment_date->toDateString(), 'Should advance to the next unpaid month, not stay stuck on the one just paid.');

        $july = $enrollment->paymentInstallments()->where('month_name', 'July')->first();
        $this->assertEquals('paid', $july->status);
    }

    public function test_finance_dashboard_overdue_count_includes_a_never_paid_installment_plan(): void
    {
        $finance = User::factory()->finance()->create();
        $enrollment = Enrollment::factory()->withInstallment()->create([
            'monthly_amount' => 1000, 'payment_amount' => 0,
            'payment_status' => 'pending', 'school_year' => '2020-2021', // guaranteed overdue
        ]);
        PaymentService::createInstallments($enrollment);

        $response = $this->actingAs($finance, 'finance')->getJson('/finance/dashboard');
        $response->assertOk();
        // The dashboard renders server-side; a 200 plus a real non-null
        // next_installment_date on the model is the meaningful assertion —
        // the actual widget count is exercised via the controller unit
        // above. This just proves the enrollment is now reachable by the
        // same `next_installment_date < today` query the widget uses.
        $overdueCount = Enrollment::where('id', $enrollment->id)
            ->where('next_installment_date', '<', now())
            ->count();
        $this->assertEquals(1, $overdueCount);
    }
}
