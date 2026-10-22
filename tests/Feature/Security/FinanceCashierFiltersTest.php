<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Finance (All Students, Installments, Payments, Dashboard overview) and Cashier
 * (Process Payment list) filters. A promoted student — paid 2026-2027 enrollment
 * plus a pending 2027-2028 one — must show under each year with THAT year's row.
 */
class FinanceCashierFiltersTest extends TestCase
{
    use RefreshDatabase;

    private User $finance;
    private User $cashier;
    private Enrollment $old;
    private Enrollment $next;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->finance = User::factory()->finance()->create();
        $this->cashier = User::factory()->cashier()->create();

        $this->old = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'grade2', 'status' => 'completed']);
        $this->next = Enrollment::factory()->create([
            'user_id' => $this->old->user_id, 'grade_level' => 'grade3', 'school_year' => '2027-2028',
            'status' => 'pending', 'payment_status' => 'pending', 'payment_amount' => 0,
        ]);
        // Leftover of an undone promotion: never shown as anyone's enrollment
        Enrollment::factory()->create(['user_id' => $this->old->user_id, 'school_year' => '2028-2029', 'status' => 'cancelled']);
    }

    private function financeRow(string $query)
    {
        $res = $this->actingAs($this->finance, 'finance')->get('/finance/students?' . $query)->assertOk();
        return collect($res->viewData('allStudents')->items())->first(fn ($s) => $s->user->id === $this->old->user_id);
    }

    public function test_finance_all_students_shows_the_picked_years_enrollment(): void
    {
        // Opens on the Current School Year; "All" = each student's newest enrollment
        $this->assertSame('2026-2027', $this->financeRow('')->school_year);
        $this->assertSame(['2027-2028', 'pending'], [$this->financeRow('school_year=all')->school_year, $this->financeRow('school_year=all')->payment_status]);
        // Used to vanish here: the newest enrollment (2027-2028) was compared to the filter
        $row = $this->financeRow('school_year=2026-2027');
        $this->assertSame(['2026-2027', 'grade2', 'paid'], [$row->school_year, $row->grade_level, $row->payment_status]);
        $this->assertNotNull($this->financeRow('school_year=2026-2027&status=paid'));
        $this->assertNull($this->financeRow('school_year=2028-2029'), 'cancelled leftover is not an enrollment');
    }

    public function test_finance_installments_all_years_really_means_all(): void
    {
        $current = Enrollment::factory()->withInstallment()->create();
        $older   = Enrollment::factory()->withInstallment()->create(['school_year' => '2025-2026']);

        $ids = fn ($q) => collect($this->actingAs($this->finance, 'finance')->get('/finance/installments?' . $q)->assertOk()
            ->viewData('installmentEnrollments')->items())->pluck('id')->sort()->values()->all();

        $this->assertSame([$current->id], $ids(''));                                   // opens on the current year
        $this->assertSame(collect([$current->id, $older->id])->sort()->values()->all(), $ids('school_year=all'));
        $this->assertSame([$older->id], $ids('school_year=2025-2026'));
    }

    public function test_finance_payments_filters_apply_to_both_tabs_and_the_stats(): void
    {
        $mk = fn ($e, $type, $method, $status = 'completed') => PaymentTransaction::create([
            'enrollment_id' => $e->id, 'user_id' => $e->user_id, 'payment_type' => $type, 'payment_method' => $method,
            'amount' => 100, 'reference_number' => 'T-' . uniqid(), 'status' => $status, 'description' => 'x',
        ]);
        $mk($this->old, 'walkin', 'cash');
        $mk($this->old, 'walkin', 'gcash');
        $mk($this->next, 'online', 'gcash');
        $mk($this->next, 'online', 'gcash', 'pending');

        $get = fn ($q) => $this->actingAs($this->finance, 'finance')->get('/finance/payments?' . $q)->assertOk();
        $tabs = fn ($res) => [$res->viewData('walkInTransactions')->total(), $res->viewData('xenditTransactions')->total(), $res->viewData('combinedPayStats')['total']];

        $this->assertSame([2, 0, 2], $tabs($get('')));                 // opens on the Current School Year
        $this->assertSame([2, 2, 4], $tabs($get('school_year=all')));
        $this->assertSame([2, 0, 2], $tabs($get('school_year=2026-2027')));
        $this->assertSame([0, 2, 2], $tabs($get('school_year=2027-2028')));
        $this->assertSame([1, 2, 3], $tabs($get('school_year=all&method=gcash')));
        $this->assertSame([0, 1, 1], $tabs($get('school_year=all&status=pending')));
        $get('method=gcash&tab=xendit')->assertSee('<option value="gcash" selected>GCash</option>', false);
    }

    public function test_finance_dashboard_overview_filters_the_whole_list(): void
    {
        Enrollment::factory()->count(20)->create(['payment_status' => 'partial']); // more than one page

        $get = fn ($q) => $this->actingAs($this->finance, 'finance')->get('/finance/dashboard?' . $q)->assertOk()->viewData('allStudentsPayment');
        $this->assertSame(1, $get('ov_sy=2027-2028')->total());
        $this->assertSame('2026-2027', collect($get('ov_sy=2026-2027&ov_pay=paid')->items())->first()->enrollments->first()->school_year);
        $this->assertSame(20, $get('ov_pay=partial')->total());
        $this->assertSame(1, $get('ov_sy=all&ov_pay=pending')->total()); // "Unpaid" — used to match a status that doesn't exist
        $this->assertSame(0, $get('ov_pay=pending')->total());           // ...that one is next year's; default = current year
        $this->assertSame(21, $get('')->total());                         // current year: the 20 + the promoted student
        $promoted = collect($get('ov_search=' . urlencode(User::find($this->old->user_id)->email))->items())->first();
        $this->assertSame('2026-2027', $promoted->enrollments->first()->school_year); // their current-year row, not next year's
    }

    public function test_cashier_process_payment_list_filters(): void
    {
        $other = Enrollment::factory()->create(['grade_level' => 'kindergarten', 'payment_status' => 'partial']);
        $list = fn ($q) => $this->actingAs($this->cashier, 'cashier')->getJson('/cashier/students?' . $q)->assertOk()->json();
        $row  = fn ($j) => collect($j['data'])->firstWhere('id', $this->old->user_id);

        $this->assertSame('2026-2027', $row($list(''))['school_year']);       // opens on the Current School Year
        $this->assertSame('2027-2028', $row($list('sy=all'))['school_year']); // All = newest enrollment
        $r = $row($list('sy=2026-2027'));
        $this->assertSame(['2026-2027', 'grade2', 'paid'], [$r['school_year'], $r['grade_level'], $r['payment_status']]);
        $this->assertSame([$other->user_id], collect($list('grade=kindergarten')['data'])->pluck('id')->all());
        $this->assertSame([$other->user_id], collect($list('sy=2026-2027&pay=partial')['data'])->pluck('id')->all());
        $this->assertSame(0, $list('sy=2025-2026')['total']);
        $this->assertSame(2, $list('sy=nonsense')['total']); // junk = All
    }
}
