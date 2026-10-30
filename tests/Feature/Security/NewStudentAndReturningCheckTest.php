<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * School decisions, October 30, 2026:
 *  - a NEW student (first time in school) only brings the original PSA birth certificate; transferees all four;
 *  - a pending application from someone ILC may already know (same name + birthdate on another account,
 *    or a balance left from an earlier school year) shows a warning to the admin — never a block.
 */
class NewStudentAndReturningCheckTest extends TestCase
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

    private function student(string $grade, string $type): Enrollment
    {
        return Enrollment::factory()->fullyPaid()->create(['grade_level' => $grade,
            'student_data' => ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'grade_level' => $grade, 'student_type' => $type]]);
    }

    private function approve(Enrollment $e, string $type): void
    {
        StudentDocument::create(['user_id' => $e->user_id, 'document_type' => $type, 'file_path' => 'x', 'original_name' => 'x', 'status' => 'approved']);
    }

    public function test_a_new_student_only_needs_the_psa_birth_certificate_a_transferee_all_four(): void
    {
        // New Nursery pupil: PSA approved → 1/1, can move up to Kinder
        $new = $this->student('nursery', 'new');
        $this->assertSame(['birth_certificate'], StudentDocument::requiredTypesFor($new->user_id));
        $this->actingAs($new->user, 'web')->get('/student/portal')->assertOk()
            ->assertSee('New students only need the original PSA Birth Certificate', false)
            ->assertSee('0/1 Approved', false);
        $this->approve($new, 'birth_certificate');
        $this->actingAs($new->user, 'web')->post('/student/reenroll')->assertSessionHas('success');
        $this->assertSame('kindergarten', Enrollment::where('user_id', $new->user_id)->where('school_year', '2027-2028')->value('grade_level'));

        // Transferee: the PSA alone is not enough
        $tr = $this->student('grade3', 'transferee');
        $this->approve($tr, 'birth_certificate');
        $this->assertSame([1, 4], StudentDocument::requiredProgress($tr->user_id));
        $this->actingAs($this->admin, 'web')->postJson("/admin/assessment/{$tr->user_id}/promote", [
            'action' => 'promote', 'from_school_year' => '2026-2027', 'to_school_year' => '2027-2028',
        ])->assertStatus(422)->assertJson(['message' => "Required documents incomplete (1/4 approved). Approve the student's documents before moving them to the next school year."]);
    }

    public function test_an_application_matching_a_former_pupil_or_with_an_old_balance_warns_the_admin(): void
    {
        // Juan was in Kinder (2023-2024) and still owes for it
        $old = Enrollment::factory()->withInstallment()->create(['grade_level' => 'kindergarten', 'school_year' => '2023-2024',
            'payment_amount' => 3000, 'remaining_balance' => 11504, 'payment_status' => 'partial',
            'student_data' => ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'grade_level' => 'kindergarten', 'student_type' => 'new']]);
        StudentProfile::create(['user_id' => $old->user_id, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'birthdate' => '2018-05-12', 'gender' => 'male', 'contact' => '09123456789']);

        // Back for Grade 3 with a different Gmail → a new account
        $app = Enrollment::factory()->create(['grade_level' => 'grade3', 'status' => 'pending',
            'student_data' => ['first_name' => ' juan ', 'last_name' => 'DELA CRUZ', 'birthdate' => '2018-05-12', 'grade_level' => 'grade3', 'student_type' => 'transferee']]);

        $rc = $this->actingAs($this->admin, 'web')->getJson("/admin/enrollments/{$app->id}")->assertOk()->json('returning_check');
        $this->assertSame([$old->user_id], array_column($rc['matches'], 'id'));
        $this->assertStringContainsString('Kindergarten, S.Y. 2023-2024', $rc['matches'][0]['last']);
        $this->assertEquals([['name' => $old->user->name, 'school_year' => '2023-2024', 'grade' => 'Kindergarten', 'amount' => 11504]], $rc['balances']);

        // A different birthday is someone else: no warning
        $other = Enrollment::factory()->create(['grade_level' => 'grade3', 'status' => 'pending',
            'student_data' => ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'birthdate' => '2017-01-01', 'grade_level' => 'grade3', 'student_type' => 'new']]);
        $rc = $this->actingAs($this->admin, 'web')->getJson("/admin/enrollments/{$other->id}")->assertOk()->json('returning_check');
        $this->assertSame([], $rc['matches']);
        $this->assertSame([], $rc['balances']);
    }
}
