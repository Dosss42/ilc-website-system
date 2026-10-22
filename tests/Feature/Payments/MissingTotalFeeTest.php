<?php

namespace Tests\Feature\Payments;

use App\Models\Enrollment;
use App\Models\User;
use App\Services\FeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * An unset total_fee is the string "0.00" (column default 0, cast decimal:2), which PHP treats
 * as true — so `!total_fee` / `empty(total_fee)` thought a fee was already there and never
 * saved one. A promoted Kindergarten student who paid ₱14,505.99 online ended up with a ₱0 fee.
 */
class MissingTotalFeeTest extends TestCase
{
    use RefreshDatabase;

    private float $kinderA;

    protected function setUp(): void
    {
        parent::setUp();
        FeeCalculator::syncComponents([
            'tuition' => 10000, 'misc' => 2000, 'insurance' => 500, 'electric' => 1000,
            'books_nursery' => 1500, 'books_grade1' => 2500, 'option_a_discount' => 494.01,
            'optb_dp_kinder' => 3000, 'optb_monthly_tuition' => 1000, 'optb_monthly_electric' => 150,
        ]);
        FeeCalculator::forgetCache();
        $this->kinderA = FeeCalculator::calculate('kindergarten', 'A')['total_due'];
        $this->assertGreaterThan(0, $this->kinderA);
    }

    /** Like a promotion-created enrollment: no plan chosen yet, fee left at the default 0. */
    private function unpricedEnrollment(array $attrs = []): Enrollment
    {
        return Enrollment::factory()->create(array_merge([
            'grade_level' => 'kindergarten', 'school_year' => '2027-2028',
            'payment_option' => null,
            'total_fee' => 0, 'remaining_balance' => 0, 'payment_amount' => 0, 'payment_status' => 'pending',
        ], $attrs))->fresh();
    }

    public function test_online_payment_link_saves_the_fee_when_it_is_still_zero(): void
    {
        config(['services.xendit.secret_key' => 'xnd_test']);
        Http::fake(['api.xendit.co/*' => Http::response([
            'id' => 'inv-fee', 'invoice_url' => 'https://checkout.xendit.co/web/inv-fee', 'expiry_date' => now()->addDay()->toIso8601String(),
        ], 200)]);
        $e = $this->unpricedEnrollment();
        $this->assertSame('0.00', $e->total_fee); // the value that used to slip through

        $this->actingAs($e->user, 'web')->postJson('/student/payment/xendit-link', [
            'enrollment_id' => $e->id, 'amount' => 1, 'payment_method' => 'gcash',
            'payment_type' => 'full', 'payment_option' => 'A',
        ])->assertOk()->assertJson(['success' => true]);

        $e->refresh();
        $this->assertEquals($this->kinderA, (float) $e->total_fee);
        $this->assertEquals($this->kinderA, (float) $e->remaining_balance);
        $this->assertSame('A', $e->payment_option);
    }

    public function test_approval_saves_the_fee_from_the_form_when_it_is_still_zero(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $e = $this->unpricedEnrollment([
            'status' => 'pending',
            'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => 'kindergarten', 'payment_option' => 'A'],
        ]);

        $this->actingAs($admin, 'web')->postJson("/admin/enrollments/{$e->id}/approve");

        $this->assertEquals($this->kinderA, (float) $e->fresh()->total_fee);
    }

    public function test_repair_migration_fills_the_fee_and_leaves_good_rows_alone(): void
    {
        $broken = $this->unpricedEnrollment([
            'payment_option' => 'A', 'payment_type' => 'full', 'payment_amount' => $this->kinderA, 'payment_status' => 'paid',
        ]);
        $underpaid = $this->unpricedEnrollment([
            'payment_option' => 'B', 'payment_type' => 'installment', 'payment_amount' => 3000, 'payment_status' => 'paid',
        ]);
        $noPlan = $this->unpricedEnrollment();
        $good = Enrollment::factory()->create(['total_fee' => 99999, 'remaining_balance' => 99999]);

        (require database_path('migrations/2026_10_22_120000_fill_missing_total_fee_on_enrollments.php'))->up();

        $broken->refresh();
        $this->assertEquals($this->kinderA, (float) $broken->total_fee);
        $this->assertEquals(0, (float) $broken->remaining_balance);
        $this->assertSame('paid', $broken->payment_status);

        // A downpayment only: with a ₱0 fee it looked fully paid
        $underpaid->refresh();
        $kinderB = FeeCalculator::calculate('kindergarten', 'B');
        $this->assertEquals($kinderB['total_due'], (float) $underpaid->total_fee);
        $this->assertEquals($kinderB['total_due'] - 3000, (float) $underpaid->remaining_balance);
        $this->assertSame('partial', $underpaid->payment_status);

        $this->assertEquals(0, (float) $noPlan->fresh()->total_fee); // no plan chosen yet: left for the portal
        $this->assertEquals(99999, (float) $good->fresh()->total_fee);
    }
}
