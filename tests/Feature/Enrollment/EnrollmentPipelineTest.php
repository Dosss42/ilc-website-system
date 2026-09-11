<?php

namespace Tests\Feature\Enrollment;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EnrollmentPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approving_a_pending_application_creates_a_student_account(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $enrollment = Enrollment::factory()->create([
            'user_id' => null,
            'status' => 'pending',
            'total_fee' => 0,
            'student_data' => [
                'first_name' => 'Maria', 'last_name' => 'Santos',
                'student_email' => 'maria.santos.' . uniqid() . '@example.com',
                'grade_level' => 'grade1', 'student_type' => 'new',
            ],
        ]);

        $response = $this->actingAs($admin, 'web')
            ->postJson("/admin/enrollments/{$enrollment->id}/approve");

        $response->assertOk();

        $enrollment->refresh();
        $this->assertEquals('approved', $enrollment->status);
        $this->assertNotNull($enrollment->approved_at);
        $this->assertEquals($admin->id, $enrollment->approved_by);
        $this->assertNotNull($enrollment->user_id, 'A student account should have been created and linked.');

        $student = User::find($enrollment->user_id);
        $this->assertEquals('student', $student->role);
        $this->assertTrue($student->is_active);
    }

    public function test_only_admin_or_superadmin_can_approve_enrollments(): void
    {
        $teacher = User::factory()->teacher()->create();
        $enrollment = Enrollment::factory()->create(['status' => 'pending', 'total_fee' => 0]);

        $this->actingAs($teacher, 'web')
            ->postJson("/admin/enrollments/{$enrollment->id}/approve")
            ->assertStatus(403);

        $enrollment->refresh();
        $this->assertEquals('pending', $enrollment->status);
    }

    public function test_a_teachers_submitted_grade_is_hidden_from_the_student_until_admin_approves_it(): void
    {
        $teacher = User::factory()->teacher()->create();
        $enrollment = Enrollment::factory()->create(['grade_level' => 'grade1']);
        $student = User::find($enrollment->user_id);

        $subject = Subject::create([
            'name' => 'Mathematics', 'code' => 'MATH1', 'grade_level' => 'grade1', 'is_active' => true,
        ]);

        $grade = Grade::create([
            'student_id' => $student->id, 'teacher_id' => $teacher->id, 'subject_id' => $subject->id,
            'enrollment_id' => $enrollment->id, 'term' => 1, 'grade' => 88.5,
            'school_year' => $enrollment->school_year, 'status' => 'submitted',
        ]);

        // Student checks their grades while it's still awaiting admin approval.
        $pending = $this->actingAs($student, 'web')
            ->getJson('/api/student/grades?quarter=1&school_year=' . $enrollment->school_year);

        $pending->assertOk();
        $this->assertFalse($pending->json('data.has_grades'), 'Grade should not be visible while only submitted, not approved.');
        $this->assertGreaterThan(0, $pending->json('pending_count'), 'Should report at least one pending grade.');

        // Admin (registrar) approves it.
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web')
            ->postJson('/admin/grades/approve', [
                'teacher_id' => $teacher->id, 'subject_id' => $subject->id,
                'term' => 1, 'school_year' => $enrollment->school_year,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $grade->refresh();
        $this->assertEquals('approved', $grade->status);

        // Now it must actually show up for the student.
        $after = $this->actingAs($student, 'web')
            ->getJson('/api/student/grades?quarter=1&school_year=' . $enrollment->school_year);

        $after->assertOk();
        $this->assertTrue($after->json('data.has_grades'));
        $subjectsInResponse = collect($after->json('data.subjects'));
        $mathGrade = $subjectsInResponse->firstWhere('code', 'MATH1');
        $this->assertNotNull($mathGrade);
        $this->assertEquals(88.5, (float) $mathGrade['final_grade']);
    }

    public function test_admin_can_reject_submitted_grades_back_to_the_teacher(): void
    {
        $teacher = User::factory()->teacher()->create();
        $enrollment = Enrollment::factory()->create(['grade_level' => 'grade1']);
        $student = User::find($enrollment->user_id);
        $subject = Subject::create(['name' => 'Science', 'code' => 'SCI1', 'grade_level' => 'grade1', 'is_active' => true]);

        $grade = Grade::create([
            'student_id' => $student->id, 'teacher_id' => $teacher->id, 'subject_id' => $subject->id,
            'enrollment_id' => $enrollment->id, 'term' => 1, 'grade' => 70,
            'school_year' => $enrollment->school_year, 'status' => 'submitted',
        ]);

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web')
            ->postJson('/admin/grades/reject', [
                'teacher_id' => $teacher->id, 'subject_id' => $subject->id,
                'term' => 1, 'school_year' => $enrollment->school_year, 'reason' => 'Please double-check computation.',
            ])
            ->assertOk();

        $grade->refresh();
        $this->assertEquals('rejected', $grade->status);
    }
}
