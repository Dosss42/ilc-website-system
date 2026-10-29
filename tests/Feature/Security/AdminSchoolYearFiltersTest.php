<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * School Year filters on Assessment & Promotion (?assess_sy), Student Management
 * (?student_schoolyear) and Enrollment Management (?enrollment_sy).
 */
class AdminSchoolYearFiltersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        Setting::set('enrollment_target_year', '2027-2028');
        Setting::set('enrollment_open', '1', 'boolean');
        $this->admin = User::factory()->admin()->create();
    }

    private function enrolled(string $grade, string $sy = '2026-2027', array $extra = []): Enrollment
    {
        return Enrollment::factory()->fullyPaid()->withApprovedDocuments()->create(array_merge([
            'grade_level' => $grade, 'school_year' => $sy,
            'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => $grade, 'student_type' => 'returning'],
        ], $extra));
    }

    private function promote(Enrollment $e): Promotion
    {
        $this->actingAs($this->admin, 'web')->postJson("/admin/assessment/{$e->user_id}/promote", [
            'action' => 'promote', 'from_school_year' => $e->school_year, 'to_school_year' => '2027-2028',
        ])->assertOk()->assertJson(['success' => true]);
        // The assessment records the result; the student's Re-enroll creates the 2027-2028 enrollment
        $this->actingAs(User::find($e->user_id), 'web')->post('/student/reenroll')->assertSessionHas('success');
        return Promotion::where('student_id', $e->user_id)->latest('id')->firstOrFail();
    }

    private function assessment(string $query = ''): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->admin, 'web')->get('/admin/dashboard?section=assessment' . $query)->assertOk();
    }

    private function assessRows($res): array
    {
        return collect($res->viewData('assessStudents')->items())
            ->mapWithKeys(fn ($s) => [$s->id => $s->promotions->isNotEmpty() ? 'done' : 'pending'])->all();
    }

    public function test_assessment_lists_the_selected_year_and_keeps_promoted_students_as_assessed(): void
    {
        $a = $this->enrolled('grade2');
        $b = $this->enrolled('grade4');
        $old = $this->enrolled('grade1', '2025-2026'); // only enrolled last year

        $res = $this->assessment();
        $this->assertSame('2026-2027', $res->viewData('assessSchoolYear'));
        $this->assertSame('2027-2028', $res->viewData('assessNextSchoolYear'));
        $this->assertEqualsCanonicalizing([$a->user_id => 'pending', $b->user_id => 'pending'], $this->assessRows($res));
        $this->assertSame(2, $res->viewData('sidebarAssessCount'));

        // Promoted: stays in 2026-2027 as Assessed (used to vanish), badge drops
        $promo = $this->promote($a);
        $res = $this->assessment();
        $rows = $this->assessRows($res);
        $this->assertSame(['done', 'pending'], [$rows[$a->user_id] ?? 'missing', $rows[$b->user_id] ?? 'missing']);
        $this->assertSame(1, $res->viewData('assessDone'));
        $this->assertSame(1, $res->viewData('sidebarAssessCount'));
        $res->assertSee('Already assessed — to change it, use Undo', false);

        // Undo puts them back to pending
        $this->actingAs($this->admin, 'web')->postJson("/admin/promotions/{$promo->id}/undo")->assertOk();
        $this->assertSame('pending', $this->assessRows($this->assessment())[$a->user_id]);

        // Past year: only last year's student; promote target is the year after it;
        // the sidebar badge still counts the CURRENT year
        $res = $this->assessment('&assess_sy=2025-2026');
        $this->assertSame([$old->user_id => 'pending'], $this->assessRows($res));
        $this->assertSame('2026-2027', $res->viewData('assessNextSchoolYear'));
        $this->assertSame(2, $res->viewData('sidebarAssessCount'));

        // Junk falls back to the current year
        $this->assertSame('2026-2027', $this->assessment('&assess_sy=nope')->viewData('assessSchoolYear'));
    }

    public function test_bulk_promote_ignores_undone_promotions(): void
    {
        $e = $this->enrolled('grade3');
        $promo = $this->promote($e);
        $this->actingAs($this->admin, 'web')->postJson("/admin/promotions/{$promo->id}/undo")->assertOk();

        $res = $this->actingAs($this->admin, 'web')->getJson('/admin/assessment/bulk-preview?grade=grade3&from_school_year=2026-2027')
            ->assertOk()->json();
        $reasons = collect($res['excluded'])->firstWhere('id', $e->user_id)['reasons'] ?? [];
        $this->assertNotContains('Already assessed for this school year', $reasons, 'an undone promotion must not block bulk promotion');
        $ids = collect($res['eligible'])->pluck('id')->merge(collect($res['excluded'])->pluck('id'));
        $this->assertTrue($ids->contains($e->user_id), 'undone student is a bulk candidate again');
    }

    public function test_student_management_shows_the_selected_years_enrollment(): void
    {
        $e = $this->enrolled('grade2');
        $this->promote($e); // 2026-2027 → completed, new 2027-2028 grade3 pending

        $get = fn ($sy) => $this->actingAs($this->admin, 'web')->get('/admin/section/students?student_schoolyear=' . $sy)->assertOk();

        $res = $get('2026-2027');
        $row = collect($res->viewData('students')->items())->firstWhere('id', $e->user_id);
        $this->assertSame(['2026-2027', 'completed', 'grade2'], [$row->latestEnrollment->school_year, $row->latestEnrollment->status, $row->latestEnrollment->grade_level]);
        $this->assertSame(0, $res->viewData('smPending'));

        $res = $get('2027-2028');
        $row = collect($res->viewData('students')->items())->firstWhere('id', $e->user_id);
        $this->assertSame(['2027-2028', 'pending', 'grade3'], [$row->latestEnrollment->school_year, $row->latestEnrollment->status, $row->latestEnrollment->grade_level]);
        $this->assertSame(1, $res->viewData('smPending'));

        // A year they were never in: not listed
        $this->assertSame(0, $get('2024-2025')->viewData('smTotal'));
        // No year picked = the Current School Year (2026-2027)
        $res = $this->actingAs($this->admin, 'web')->get('/admin/section/students')->assertOk();
        $this->assertSame('2026-2027', $res->viewData('studentSchoolYearFilter'));
        $this->assertSame('2026-2027', collect($res->viewData('students')->items())->firstWhere('id', $e->user_id)->latestEnrollment->school_year);
        // All years: latest enrollment, as before
        $row = collect($get('all')->viewData('students')->items())->firstWhere('id', $e->user_id);
        $this->assertSame('2027-2028', $row->latestEnrollment->school_year);
    }

    public function test_enrollment_management_year_filter_status_options_and_cancelled_rows(): void
    {
        $cur = $this->enrolled('grade2');
        $declined = Enrollment::factory()->create(['status' => 'declined', 'school_year' => '2026-2027']);
        $promo = $this->promote($cur);
        $this->actingAs($this->admin, 'web')->postJson("/admin/promotions/{$promo->id}/undo")->assertOk(); // leaves a cancelled 2027-2028 row
        $next = Enrollment::factory()->pendingApproval()->create(['school_year' => '2027-2028']);

        $ids = fn ($q) => collect($this->actingAs($this->admin, 'web')->get('/admin/section/enrollment?' . $q)->assertOk()
            ->viewData('enrollments')->items())->pluck('id')->sort()->values()->all();

        // Opens on the Current School Year (2026-2027)
        $this->assertSame(collect([$cur->id, $declined->id])->sort()->values()->all(), $ids(''));
        // All years, all status: cancelled leftover hidden
        $this->assertSame(collect([$cur->id, $declined->id, $next->id])->sort()->values()->all(), $ids('enrollment_sy=all'));
        $this->assertSame([$next->id], $ids('enrollment_sy=2027-2028'));
        $this->assertSame([$declined->id], $ids('status=declined'));                            // "Rejected" used to match nothing
        $this->assertSame([$promo->enrollment_id], $ids('enrollment_sy=all&status=cancelled')); // still reachable

        // Next year's new application isn't missed: the 2026-2027 view flags it
        $this->actingAs($this->admin, 'web')->get('/admin/section/enrollment')->assertOk()
            ->assertViewHas('pendingOtherYears', fn ($p) => $p->all() === ['2027-2028' => 1])
            ->assertSee('S.Y. 2027-2028 · <strong>1</strong> pending', false);

        $res = $this->actingAs($this->admin, 'web')->get('/admin/section/enrollment?enrollment_sy=2027-2028')->assertOk();
        $this->assertSame('2027-2028', $res->viewData('enrolledStatYear'));
        $this->assertSame(0, $res->viewData('enrolledThisYear'));
        $res->assertSee('id="enrollmentSyFilter"', false)
            ->assertSee('<option value="2027-2028" selected>S.Y. 2027-2028</option>', false)
            ->assertSee('Enrolled · S.Y. 2027-2028');

        // Pending Payments no longer counts the cancelled leftover as anyone's latest enrollment
        $all = $this->actingAs($this->admin, 'web')->get('/admin/section/enrollment?enrollment_sy=all')->viewData('pendingPaymentsCount');
        $this->assertSame(Enrollment::where('status', '!=', 'cancelled')
            ->whereIn('id', Enrollment::selectRaw('MAX(id)')->where('status', '!=', 'cancelled')->groupBy('user_id'))
            ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', 'pending'))->count(), $all);
    }
}
