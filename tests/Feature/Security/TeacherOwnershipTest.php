<?php

namespace Tests\Feature\Security;

use App\Models\Section;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A class listed in My Students always opens: a subject assigned in Teacher Assignments (no timetable yet)
 * lets that teacher open and grade it — like a schedule or advisory — while a teacher with no link is refused.
 */
class TeacherOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_assignment_without_schedule_opens_the_grade_sheet_and_others_are_refused(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $section = Section::create(['name' => 'Rizal B', 'grade_level' => 'grade3', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $eng = Subject::create(['name' => 'English', 'code' => 'ENG3', 'grade_level' => 'grade3', 'is_active' => true]);
        $math = Subject::create(['name' => 'Math', 'code' => 'MATH3', 'grade_level' => 'grade3', 'is_active' => true]);
        $section->subjects()->attach([$eng->id, $math->id]);

        $assigned = User::factory()->teacher()->create();
        TeacherAssignment::create(['teacher_id' => $assigned->id, 'section_id' => $section->id, 'subject_id' => $eng->id, 'is_advisory' => false, 'school_year' => '2026-2027']);
        $stranger = User::factory()->teacher()->create();

        $load = fn ($teacher, $subject) => $this->actingAs($teacher, 'web')->postJson('/teacher/grades/load-class',
            ['section_id' => $section->id, 'subject_id' => $subject->id, 'term' => 1, 'school_year' => '2026-2027']);

        // Listed in My Students …
        $this->actingAs($assigned, 'web')->get('/teacher/dashboard')->assertOk()->assertSee('data-subject-id="' . $eng->id . '"', false);
        // … and it opens
        $load($assigned, $eng)->assertOk()->assertJson(['success' => true]);
        // but not a subject of that section they were not assigned
        $load($assigned, $math)->assertStatus(403);
        // and a teacher with no link at all is refused
        $load($stranger, $eng)->assertStatus(403);
    }
}
