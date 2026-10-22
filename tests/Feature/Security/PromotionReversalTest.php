<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Promotion;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Undoing a promotion, or declining the enrollment a promotion created, must
 * leave the student able to be re-assessed / re-enrolled. Both used to leave
 * them stuck: the cancelled/declined 2027-2028 row still blocked re-enrolment,
 * or (decline) the old enrollment stayed 'completed' so the student dropped out
 * of Assessment & Promotion entirely.
 */
class PromotionReversalTest extends TestCase
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

    private function enrolled(string $grade): Enrollment
    {
        $e = Enrollment::factory()->fullyPaid()->create([
            'grade_level'  => $grade,
            'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => $grade, 'student_type' => 'returning'],
        ]);
        Section::create(['name' => ucfirst($grade) . ' A', 'grade_level' => $grade, 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true])
            ->students()->attach($e->user_id);
        $e->update(['section' => ucfirst($grade) . ' A']);
        return $e;
    }

    private function promote(Enrollment $e): Promotion
    {
        $this->actingAs($this->admin, 'web')->postJson("/admin/assessment/{$e->user_id}/promote", [
            'action' => 'promote', 'from_school_year' => '2026-2027', 'to_school_year' => '2027-2028',
        ])->assertOk()->assertJson(['success' => true]);
        return Promotion::where('student_id', $e->user_id)->latest('id')->firstOrFail();
    }

    private function assessRow(int $userId): ?array
    {
        $view = $this->actingAs($this->admin, 'web')->get('/admin/dashboard?section=assessment')->assertOk();
        $row = collect($view->viewData('assessStudents')->items())->firstWhere('id', $userId);
        return $row ? ['done' => $row->promotions->isNotEmpty()] : null;
    }

    public function test_after_undo_the_student_can_re_enroll_from_the_portal(): void
    {
        // Kindergarten auto-advances — no assessment needed to re-enroll
        $old = $this->enrolled('kindergarten');
        $promo = $this->promote($old);
        $this->actingAs($this->admin, 'web')->postJson("/admin/promotions/{$promo->id}/undo")->assertOk()->assertJson(['success' => true]);

        $this->assertSame('enrolled', $old->fresh()->status);
        $this->assertSame(['done' => false], $this->assessRow($old->user_id), 'an undone promotion is not "Assessed"');

        $student = User::find($old->user_id);
        $portal = $this->actingAs($student, 'web')->get('/student/portal')->assertOk();
        $this->assertTrue($portal->viewData('needsReenrollment'), 'the re-enroll card must come back after an undo');

        $this->actingAs($student, 'web')->post('/student/reenroll')->assertSessionHas('success');
        $this->assertDatabaseHas('enrollments', ['user_id' => $old->user_id, 'school_year' => '2027-2028', 'status' => 'pending', 'grade_level' => 'grade1']);
    }

    public function test_after_undo_the_admin_can_promote_again(): void
    {
        $old = $this->enrolled('grade2');
        $promo = $this->promote($old);
        $this->actingAs($this->admin, 'web')->postJson("/admin/promotions/{$promo->id}/undo")->assertOk();

        $this->assertSame(['done' => false], $this->assessRow($old->user_id));
        $again = $this->promote($old);
        $this->assertSame('completed', $again->status);
        $this->assertSame('pending', Enrollment::find($again->enrollment_id)->status);
    }

    public function test_declining_a_promoted_enrollment_puts_the_student_back_for_re_assessment(): void
    {
        $old = $this->enrolled('grade2');
        $promo = $this->promote($old);
        $new = Enrollment::find($promo->enrollment_id);

        $this->actingAs($this->admin, 'web')->postJson("/admin/enrollments/{$new->id}/decline", ['reason' => 'Wrong grade'])
            ->assertOk()->assertJson(['success' => true]);

        // Old enrollment and section restored; the promotion no longer counts
        $this->assertSame('enrolled', $old->fresh()->status);
        $this->assertSame('reversed', $promo->fresh()->status);
        $this->assertDatabaseHas('section_student', ['user_id' => $old->user_id, 'section_id' => Section::where('name', 'Grade2 A')->value('id')]);
        $this->assertSame('Wrong grade', $new->fresh()->decline_reason);
        $this->assertSame(['done' => false], $this->assessRow($old->user_id), 'student is back in Assessment & Promotion');

        // Portal shows their real 2026-2027 enrollment, not the declined artifact
        $student = User::find($old->user_id);
        $this->assertSame($old->id, $this->actingAs($student, 'web')->get('/student/portal')->assertOk()->viewData('enrollment')->id);

        // And they can be promoted again
        $this->assertSame('completed', $this->promote($old)->status);
    }

    public function test_declining_through_the_status_dropdown_also_reverses_the_promotion(): void
    {
        $old = $this->enrolled('grade3');
        $promo = $this->promote($old);

        $this->actingAs($this->admin, 'web')->postJson("/admin/enrollment/{$promo->enrollment_id}/change-status", ['status' => 'declined'])
            ->assertOk()->assertJson(['success' => true]);

        $this->assertSame('enrolled', $old->fresh()->status);
        $this->assertSame('reversed', $promo->fresh()->status);
        $this->assertSame(['done' => false], $this->assessRow($old->user_id));
    }

    public function test_declining_a_normal_new_application_is_unchanged(): void
    {
        $app = Enrollment::factory()->pendingApproval()->create();
        $this->actingAs($this->admin, 'web')->postJson("/admin/enrollments/{$app->id}/decline", ['reason' => 'Incomplete'])
            ->assertOk()->assertJson(['success' => true]);
        $this->assertSame('declined', $app->fresh()->status);
    }
}
