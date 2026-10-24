<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin › Grade Oversight: one review card per teacher's subject, term and SECTION, and Approve /
 * Return act on exactly that card's grades (the same subject in the teacher's other section is untouched).
 */
class GradeOversightTest extends TestCase
{
    use RefreshDatabase;

    public function test_batches_are_per_section_and_approve_only_touches_that_card(): void
    {
        $admin   = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create(['name' => 'Ana Reyes']);
        $math    = Subject::create(['name' => 'Math', 'code' => 'M4', 'grade_level' => 'grade4', 'is_active' => true]);

        $grade = function (string $section, int $value) use ($teacher, $math) {
            // no enrollment_id on the grade, like grades saved from the teacher's grade table
            $e = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade4', 'section' => $section]);
            return Grade::create(['student_id' => $e->user_id, 'teacher_id' => $teacher->id, 'subject_id' => $math->id,
                'term' => 1, 'grade' => $value, 'school_year' => '2026-2027', 'status' => 'submitted']);
        };
        $rizalA = $grade('Rizal', 90);
        $rizalB = $grade('Rizal', 70);
        $mabini = $grade('Mabini', 85);

        $batches = collect($this->actingAs($admin, 'web')->getJson('/admin/grades/pending')->assertOk()->json('data'))->keyBy('section');
        $this->assertSame(['Mabini', 'Rizal'], $batches->keys()->sort()->values()->all(), 'one card per section');
        $this->assertSame(['Grade 4', 2, 80, 1, 1], [$batches['Rizal']['grade_label'], $batches['Rizal']['count'],
            (int) $batches['Rizal']['average'], $batches['Rizal']['passed'], $batches['Rizal']['failed']]);

        // Approve the Rizal card → only its 2 grades; Mabini's same subject/term stays submitted
        $rizal = $batches['Rizal'];
        $this->postJson('/admin/grades/approve', ['grade_ids' => $rizal['ids'], 'teacher_id' => $rizal['teacher_id'],
            'subject_id' => $rizal['subject_id'], 'term' => $rizal['term'], 'school_year' => $rizal['school_year']])
            ->assertOk()->assertJson(['success' => true, 'message' => '2 grade(s) approved.']);
        $this->assertSame(['approved', 'approved', 'submitted'], [$rizalA->fresh()->status, $rizalB->fresh()->status, $mabini->fresh()->status]);

        // Return the Mabini card
        $m = collect($this->getJson('/admin/grades/pending')->json('data'))->firstWhere('section', 'Mabini');
        $this->postJson('/admin/grades/reject', ['grade_ids' => $m['ids'], 'teacher_id' => $m['teacher_id'], 'subject_id' => $m['subject_id'],
            'term' => $m['term'], 'school_year' => $m['school_year'], 'reason' => 'Check the computation'])->assertOk();
        $this->assertSame('rejected', $mabini->fresh()->status);
        $this->assertSame([], $this->getJson('/admin/grades/pending')->json('data'));
    }

    public function test_registrar_correction_is_logged_and_shown_to_the_teacher(): void
    {
        $admin   = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create(['name' => 'Ana Reyes']);
        $math    = Subject::create(['name' => 'Math', 'code' => 'M4', 'grade_level' => 'grade4', 'is_active' => true]);
        $section = \App\Models\Section::create(['name' => 'Rizal', 'grade_level' => 'grade4', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        \App\Models\TeacherAssignment::create(['teacher_id' => $teacher->id, 'section_id' => $section->id, 'is_advisory' => true, 'school_year' => '2026-2027']);
        $e = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade4', 'section' => 'Rizal']);
        $section->students()->attach($e->user_id);
        $grade = Grade::create(['student_id' => $e->user_id, 'teacher_id' => $teacher->id, 'subject_id' => $math->id, 'term' => 1,
            'grade' => 70, 'remarks' => 'Passed with Remedial', 'school_year' => '2026-2027', 'status' => 'submitted']);

        // Registrar corrects 70 → 78: still waiting for approval, teacher's value kept, remarks follow
        $this->actingAs($admin, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 78])
            ->assertOk()->assertJson(['success' => true, 'message' => 'Grade changed from 70 to 78.']);
        $grade->refresh();
        $this->assertSame(['submitted', '78.00', 'Passed', '70', $admin->id], [$grade->status, $grade->grade, $grade->remarks, $grade->original_grade, $grade->edited_by]);
        $this->assertDatabaseHas('activity_logs', ['subject_type' => 'Grade', 'subject_id' => $grade->id, 'user_id' => $admin->id]);
        $this->assertStringContainsString('from 70 to 78', \App\Models\ActivityLog::where('subject_type', 'Grade')->where('subject_id', $grade->id)->latest('id')->value('description'));
        $this->assertSame('70', $this->getJson('/admin/grades/pending')->json('data.0.grades.0.original_grade'));

        // A second correction still remembers the TEACHER's value; going back to it clears the note
        $this->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 80])->assertOk();
        $this->assertSame('70', $grade->fresh()->original_grade);

        // The teacher's grade table shows "Edited by registrar: 70 → 80"
        $row = $this->actingAs($teacher, 'web')->postJson('/teacher/grades/load-class', [
            'section_id' => $section->id, 'subject_id' => $math->id, 'term' => 1, 'school_year' => '2026-2027',
        ])->assertOk()->json('data.0');
        $this->assertSame(['80.00', '70', 'submitted'], [$row['grade'], $row['edited_from'], $row['grade_status']]);

        $this->actingAs($admin, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 70])->assertOk();
        $this->assertSame([null, null], [$grade->fresh()->original_grade, $grade->fresh()->edited_by]);

        // Out of range, already approved, and non-admins are refused
        $this->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 120])->assertStatus(422);
        $grade->update(['status' => 'approved']);
        $this->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 90])->assertStatus(422);
        $grade->update(['status' => 'submitted']);
        $this->actingAs($teacher, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['grade' => 99]);
        $this->assertSame('70.00', $grade->fresh()->grade, 'a teacher cannot use the registrar correction');
    }

    public function test_assessment_window_hides_drafts_and_averages_approved_grades_only(): void
    {
        \App\Models\Setting::set('current_school_year', '2026-2027');
        $admin   = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $e = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade3', 'section' => 'Rizal']);
        $sub = fn ($name) => Subject::create(['name' => $name, 'code' => 'G3' . $name, 'grade_level' => 'grade3', 'is_active' => true]);
        [$gmrc, $math, $sci] = [$sub('GMRC'), $sub('Math'), $sub('Science')];
        $g = fn ($subject, $term, $value, $status) => Grade::create(['student_id' => $e->user_id, 'teacher_id' => $teacher->id, 'subject_id' => $subject->id,
            'term' => $term, 'grade' => $value, 'school_year' => '2026-2027', 'status' => $status]);
        $g($gmrc, 1, 90, 'approved');
        $g($gmrc, 2, 70, 'submitted');   // waiting → shown, not counted
        $g($gmrc, 3, 60, 'draft');       // teacher's draft → not shown
        $g($math, 1, 88, 'submitted');
        $g($sci, 1, 95, 'draft');

        $rows = collect($this->actingAs($admin, 'web')->getJson('/admin/student/' . $e->user_id . '/grades-for-assess')->assertOk()->json('grades'))->keyBy('subject');

        $this->assertSame(['90.00', '70.00', null], [$rows['GMRC']['term1'], $rows['GMRC']['term2'], $rows['GMRC']['term3']]);
        $this->assertSame(['approved', 'submitted', null], [$rows['GMRC']['term1_status'], $rows['GMRC']['term2_status'], $rows['GMRC']['term3_status']]);
        $this->assertEquals(90, $rows['GMRC']['average'], 'only the approved term counts');
        $this->assertSame(['Passed', 'submitted'], [$rows['GMRC']['remarks'], $rows['GMRC']['status']]);

        $this->assertSame(['88.00', null, 'submitted'], [$rows['Math']['term1'], $rows['Math']['average'], $rows['Math']['status']]);
        $this->assertSame([null, 'not_entered'], [$rows['Science']['term1'], $rows['Science']['status']], 'a draft is not shown at all');
    }

    public function test_kinder_ratings_are_corrected_with_a_rating(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $e = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'kindergarten', 'section' => 'Kinder']);
        $grade = Grade::create(['student_id' => $e->user_id, 'teacher_id' => $teacher->id, 'term' => 1, 'descriptive_grade' => 'S',
            'remarks' => 'Satisfactory', 'school_year' => '2026-2027', 'status' => 'submitted']);

        $this->actingAs($admin, 'web')->postJson("/admin/grades/{$grade->id}/edit", ['descriptive_grade' => 'XX'])->assertStatus(422);
        $this->postJson("/admin/grades/{$grade->id}/edit", ['descriptive_grade' => 'VS'])->assertOk();
        $this->assertSame(['VS', 'Very Satisfactory', 'S'], [$grade->fresh()->descriptive_grade, $grade->fresh()->remarks, $grade->fresh()->original_grade]);
    }
}
