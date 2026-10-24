<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * The whole grade flow, through the same routes the screens use:
 * teacher Save as Draft → Submit Grades → registrar corrects in Grade Oversight → Approve,
 * checking what Grade Oversight, the teacher, Assessment, the student portal and SF10 show at each step,
 * and that each status is locked for the right people.
 */
class GradeFlowEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_submit_correct_approve_flow(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $admin   = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create(['name' => 'Ana Reyes']);
        $section = Section::create(['name' => 'Rizal', 'grade_level' => 'grade4', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $math    = Subject::create(['name' => 'Math', 'code' => 'M4', 'grade_level' => 'grade4', 'is_active' => true]);
        $section->subjects()->attach($math->id);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'section_id' => $section->id, 'is_advisory' => true, 'school_year' => '2026-2027']);

        $enr = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade4', 'section' => 'Rizal', 'status' => 'enrolled',
            'student_data' => ['first_name' => 'Juan', 'last_name' => 'Cruz', 'grade_level' => 'grade4']]);
        $section->students()->attach($enr->user_id);
        $student = User::find($enr->user_id);
        StudentProfile::create(['user_id' => $student->id, 'first_name' => 'Juan', 'last_name' => 'Cruz', 'gender' => 'male']);

        $scope = ['section_id' => $section->id, 'subject_id' => $math->id, 'term' => 1, 'school_year' => '2026-2027'];
        $teacherSaves = fn ($value) => $this->actingAs($teacher, 'web')->postJson('/teacher/grades/save', ['section_id' => $section->id, 'draft' => true,
            'grades' => [['student_id' => $student->id, 'enrollment_id' => $enr->id, 'subject_id' => $math->id, 'term' => 1, 'grade' => $value, 'school_year' => '2026-2027']]]);
        $teacherRow   = fn () => $this->actingAs($teacher, 'web')->postJson('/teacher/grades/load-class', $scope)->assertOk()->json('data.0');
        $pending      = fn () => $this->actingAs($admin, 'web')->getJson('/admin/grades/pending')->assertOk()->json('data');
        $assess       = fn () => collect($this->actingAs($admin, 'web')->getJson("/admin/student/{$student->id}/grades-for-assess")->assertOk()->json('grades'))->firstWhere('subject', 'Math');
        $portal       = fn () => $this->actingAs($student, 'sanctum')->getJson('/api/student/grades?quarter=1')->assertOk()->json();
        $portalMath   = fn () => collect($portal()['data']['subjects'])->firstWhere('name', 'Math');

        // ── 1. Teacher: Save as Draft (70) ──
        $teacherSaves(70)->assertOk();
        $this->assertSame('draft', Grade::where('student_id', $student->id)->value('status'));
        $this->assertSame([], $pending(), 'a draft is not in Grade Oversight');
        $this->assertNull($assess()['term1'], 'a draft is not shown in Assessment');
        $this->assertNull($portalMath()['final_grade'], 'a draft is not shown to the student');
        $this->assertSame(0, $portal()['pending_count']);

        // ── 2. Teacher: Submit Grades ──
        $this->actingAs($teacher, 'web')->postJson('/teacher/grades/submit-draft', $scope)->assertOk();
        $grade = Grade::where('student_id', $student->id)->where('status', 'submitted')->firstOrFail();
        $this->assertSame('submitted', $teacherRow()['grade_status']);

        // Teacher can no longer change it (a new draft for a posted grade is skipped)
        $teacherSaves(99);
        $this->assertSame('70.00', $grade->fresh()->grade);
        $this->assertSame(0, Grade::where('student_id', $student->id)->where('status', 'draft')->count(), 'no draft created over a submitted grade');

        $batch = $pending()[0];
        $this->assertSame(['Math', 'Grade 4', 'Rizal', 1, 1, [$grade->id]], [$batch['subject'], $batch['grade_label'], $batch['section'], $batch['count'], $batch['failed'], $batch['ids']]);
        $this->assertSame(['70.00', 'submitted', null], [$assess()['term1'], $assess()['term1_status'], $assess()['average']], 'waiting approval, not counted');
        $this->assertNull($portalMath()['final_grade'], 'still hidden from the student');
        $this->assertSame(1, $portal()['pending_count'], 'the student sees "1 grade pending approval"');

        // ── 3. Registrar corrects 70 → 78 in Grade Oversight ──
        $this->actingAs($admin, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 78])->assertOk();
        $this->assertSame(['submitted', '78.00', '70'], [$grade->fresh()->status, $grade->fresh()->grade, $grade->fresh()->original_grade]);
        $row = $teacherRow();
        $this->assertSame(['78.00', '70', 'submitted'], [$row['grade'], $row['edited_from'], $row['grade_status']], 'teacher sees "Edited by registrar: 70 → 78"');
        $this->assertNull($portalMath()['final_grade'], 'a correction alone does not publish the grade');

        // ── 4. Registrar approves the card ──
        $batch = $pending()[0];
        $this->actingAs($admin, 'web')->postJson('/admin/grades/approve', ['grade_ids' => $batch['ids'], 'teacher_id' => $batch['teacher_id'],
            'subject_id' => $batch['subject_id'], 'term' => $batch['term'], 'school_year' => $batch['school_year']])->assertOk();
        $this->assertSame('approved', $grade->fresh()->status);
        $this->assertSame([], $pending(), 'the card is gone from Grade Oversight');

        // Now official everywhere
        $this->assertEquals(78, $portalMath()['final_grade'], 'the student sees the corrected grade');
        $this->assertSame(0, $portal()['pending_count']);
        $a = $assess();
        $this->assertSame(['approved', 'approved', 'Passed'], [$a['term1_status'], $a['status'], $a['remarks']]);
        $this->assertEquals(78, $a['average'], 'counted in the Assessment average');

        $sf10 = null;
        View::composer('pdf.sf10', function ($view) use (&$sf10) { $sf10 = $view->getData(); });
        $this->actingAs($admin, 'web')->get("/admin/students/{$student->id}/sf10")->assertOk();
        $this->assertEquals(78, collect($sf10['records'][0]['rows'])->firstWhere('name', 'Math')['terms'][0], 'on the SF10');

        $this->assertSame('approved', $teacherRow()['grade_status']);
        $this->assertSame('70', $teacherRow()['edited_from'], 'the teacher still sees the correction note');

        // ── 5. Approved is locked for everyone ──
        $this->actingAs($admin, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 95])->assertStatus(422);
        $teacherSaves(95);
        $this->actingAs($teacher, 'web')->postJson('/teacher/grades/submit-draft', $scope);
        $this->assertSame(['approved', '78.00'], [$grade->fresh()->status, $grade->fresh()->grade]);
        $this->assertSame(1, Grade::where('student_id', $student->id)->count(), 'no second grade record appeared');
    }
}
