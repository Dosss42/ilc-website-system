<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\PaymentInstallment;
use App\Models\Setting;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment per Term: the 9 monthly installments grouped by term (Total Terms
 * setting) — 1st Term Jul–Sep, 2nd Oct–Dec, 3rd Jan–Mar.
 */
class TermBreakdownTest extends TestCase
{
    use RefreshDatabase;

    private function plan(array $statusByMonth = []): Enrollment
    {
        $e = Enrollment::factory()->withInstallment()->create(['payment_option' => 'C', 'downpayment_amount' => 6000, 'monthly_amount' => 1278.32]);
        PaymentService::createInstallments($e);
        foreach ($statusByMonth as $month => [$status, $paid]) {
            $e->paymentInstallments()->where('month_name', $month)->update(['status' => $status, 'amount_paid' => $paid]);
        }
        return $e->fresh();
    }

    public function test_months_split_by_the_total_terms_setting(): void
    {
        $this->assertSame([
            1 => ['July', 'August', 'September'],
            2 => ['October', 'November', 'December'],
            3 => ['January', 'February', 'March'],
        ], PaymentService::termMonths(3));
        $this->assertSame([3, 2, 2, 2], array_map('count', array_values(PaymentService::termMonths(4))));

        Setting::set('total_terms', '3', 'integer');
        $this->assertCount(3, PaymentService::termMonths());
    }

    public function test_term_amounts_add_up_and_statuses_follow_the_installments(): void
    {
        Carbon::setTestNow('2026-11-15'); // Term 1 months are past due, Term 2 in progress
        $e = $this->plan([
            'July'      => ['paid', 1278.32],
            'August'    => ['paid', 1278.32],
            'September' => ['paid', 1278.32],
            'October'   => ['paid', 1278.32],
            'November'  => ['pending', 500],
        ]);

        $rows = PaymentService::termBreakdown($e->paymentInstallments);
        $this->assertSame(['1st Term', '2nd Term', '3rd Term'], array_column($rows, 'label'));
        $this->assertSame([3834.96, 3834.96, 3834.96], array_column($rows, 'amount'));
        $this->assertEqualsWithDelta(6000 + 3 * 3834.96, 6000 + array_sum(array_column($rows, 'amount')), 0.01); // = plan total

        $this->assertSame(['paid', 'partial', 'unpaid'], array_column($rows, 'status'));
        $this->assertSame([3834.96, 1778.32, 0.0], array_column($rows, 'paid'));
        $this->assertSame([false, true, false], array_column($rows, 'is_next'));
        $this->assertSame('2026-12-31', $rows[1]['deadline']->toDateString());

        // A missed month makes its term Overdue (and that term is the one to pay next)
        Carbon::setTestNow('2027-01-10');
        $rows = PaymentService::termBreakdown($e->fresh()->paymentInstallments);
        $this->assertSame('overdue', $rows[1]['status']);
        $this->assertTrue($rows[1]['is_next']);

        // Awaiting Finance approval shows as such
        $e->paymentInstallments()->where('month_name', 'January')->update(['status' => 'pending_approval']);
        $this->assertSame('pending', PaymentService::termBreakdown($e->fresh()->paymentInstallments)[2]['status']);
        Carbon::setTestNow();
    }

    public function test_student_portal_shows_payment_per_term(): void
    {
        Carbon::setTestNow('2026-10-05');
        Setting::set('current_school_year', '2026-2027');
        $e = $this->plan(['July' => ['paid', 1278.32], 'August' => ['paid', 1278.32], 'September' => ['paid', 1278.32]]);
        $e->update(['payment_amount' => 6000 + 3 * 1278.32, 'payment_status' => 'partial']);

        $this->actingAs($e->user, 'web')->get('/student/portal')->assertOk()
            ->assertSee('Payment per Term')
            ->assertSeeInOrder(['1st Term', 'Jul · Aug · Sep', '₱3,834.96', '2nd Term', 'Oct · Nov · Dec', '3rd Term', 'Jan · Feb · Mar'])
            ->assertSee('Pay this next')
            ->assertSee('Pay by Dec 31, 2026');
        Carbon::setTestNow();
    }
}
