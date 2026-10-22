<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Student Management › Transaction History: GET /admin/students/{user}/transactions
 */
class StudentTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_one_students_transactions_across_years_with_filter_and_totals(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $admin   = User::factory()->admin()->create();
        $cashier = User::factory()->cashier()->create(['name' => 'Cashier Ana']);
        $old = Enrollment::factory()->fullyPaid()->create(['school_year' => '2025-2026', 'status' => 'completed', 'total_fee' => 15000, 'payment_amount' => 15000]);
        $cur = Enrollment::factory()->create(['user_id' => $old->user_id, 'school_year' => '2026-2027', 'total_fee' => 18000, 'payment_amount' => 5000, 'remaining_balance' => 13000, 'payment_option' => 'B']);
        $other = Enrollment::factory()->create(); // someone else's payment must not show

        $tx = fn ($e, array $a) => PaymentTransaction::create(array_merge([
            'enrollment_id' => $e->id, 'user_id' => $e->user_id, 'payment_type' => 'walkin', 'payment_method' => 'cash',
            'amount' => 1000, 'reference_number' => 'R-' . uniqid(), 'status' => 'completed', 'description' => 'Downpayment',
        ], $a));
        $tx($old, ['amount' => 15000, 'processed_by' => $cashier->id, 'description' => 'Full Payment']);
        $tx($cur, ['amount' => 5000, 'processed_by' => $cashier->id]);
        $tx($cur, ['amount' => 1278.32, 'payment_type' => 'online', 'payment_method' => 'gcash', 'status' => 'pending', 'description' => 'September Installment']);
        $tx($other, ['amount' => 999]);

        $all = $this->actingAs($admin, 'web')->getJson("/admin/students/{$old->user_id}/transactions")->assertOk()->json();
        $this->assertSame(3, $all['summary']['count']);
        $this->assertEquals(20000, $all['summary']['total_paid']);      // completed only
        $this->assertSame(1, $all['summary']['pending']);
        $this->assertSame(['2026-2027', '2025-2026'], array_column($all['school_years'], 'school_year'));
        $online = collect($all['transactions'])->firstWhere('status', 'pending');
        $this->assertSame(['GCash (Online)', 'Online payment (Xendit)', 'online'], [$online['method'], $online['processed_by'], $online['channel']]);
        $this->assertSame('Cashier Ana', collect($all['transactions'])->firstWhere('amount', 15000)['processed_by']);

        $cy = $this->actingAs($admin, 'web')->getJson("/admin/students/{$old->user_id}/transactions?school_year=2026-2027")->json();
        $this->assertSame(2, $cy['summary']['count']);
        $this->assertEquals(5000, $cy['summary']['total_paid']);
        $this->assertSame(['2026-2027', '2026-2027'], array_column($cy['transactions'], 'school_year'));
        $this->assertEquals(13000, collect($cy['school_years'])->firstWhere('school_year', '2026-2027')['balance']);

        // Unknown year falls back to all
        $this->assertSame('all', $this->actingAs($admin, 'web')->getJson("/admin/students/{$old->user_id}/transactions?school_year=1999-2000")->json('school_year'));
    }

    public function test_only_students_and_only_for_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $e = Enrollment::factory()->create();

        $this->actingAs($admin, 'web')->getJson("/admin/students/{$admin->id}/transactions")->assertNotFound();
        // A student can't read it — not even their own (admin-only route)
        $this->actingAs($e->user, 'web')->getJson("/admin/students/{$e->user_id}/transactions")->assertForbidden();
    }

    public function test_student_management_rows_have_the_button(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $admin = User::factory()->admin()->create();
        $e = Enrollment::factory()->create();
        $this->actingAs($admin, 'web')->get('/admin/section/students')->assertOk()
            ->assertSee('js-student-transactions', false)
            ->assertSee('data-id="' . $e->user_id . '" title="Transaction History"', false);
    }
}
