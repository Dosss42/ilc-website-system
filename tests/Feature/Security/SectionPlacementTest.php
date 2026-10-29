<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A student who moved up and paid before any section of their new grade/year existed: Student
 * Management counts them as "no section yet", creating a section offers to place them, and
 * Auto-Assign places them — it used to skip anyone still on LAST year's class list.
 */
class SectionPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_returning_student_without_a_section_is_flagged_and_placed(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $admin = User::factory()->admin()->create();

        $old = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'grade1', 'school_year' => '2026-2027', 'status' => 'completed', 'section' => 'Grade 1-A']);
        Section::create(['name' => 'Grade 1-A', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true])
            ->students()->attach($old->user_id); // still on last year's class list
        Enrollment::factory()->fullyPaid()->create(['user_id' => $old->user_id, 'grade_level' => 'grade2', 'school_year' => '2027-2028', 'status' => 'enrolled', 'section' => null]);

        // Current year: the Completed row says where the student went
        $row = collect($this->actingAs($admin, 'web')->getJson('/admin/section/students-data')->assertOk()->json('students'))->firstWhere('id', $old->user_id);
        $this->assertSame(['grade_label' => 'Grade 2', 'school_year' => '2027-2028', 'status' => 'Enrolled'], $row['next']);

        // Next year: flagged as having no section
        $this->assertSame(1, $this->actingAs($admin, 'web')->getJson('/admin/section/students-data?student_schoolyear=2027-2028')->json('stats.no_section'));
        $this->actingAs($admin, 'web')->get('/admin/section/students?student_schoolyear=2027-2028')->assertOk()->assertSee('have no section yet');

        // Creating a Grade 2 section for 2027-2028 reports the waiting student; placing puts them in it
        $this->actingAs($admin, 'web')->postJson('/admin/sections', ['name' => 'Grade 2-A', 'grade_level' => 'grade2', 'school_year' => '2027-2028', 'max_students' => 30, 'is_active' => true])
            ->assertCreated()->assertJson(['waiting_count' => 1]);
        // Auto-Assign previews first (nothing saved): who is waiting, free seats, how many will be placed
        $this->actingAs($admin, 'web')->postJson('/admin/sections/auto-assign', ['preview' => true, 'school_year' => '2027-2028'])
            ->assertOk()->assertJson(['waiting' => 1, 'will_place' => 1, 'grades' => [['grade_level' => 'grade2', 'waiting' => 1, 'free_seats' => 30, 'sections' => ['Grade 2-A']]]]);
        $this->assertNull(Enrollment::where('user_id', $old->user_id)->where('school_year', '2027-2028')->value('section'));

        $this->actingAs($admin, 'web')->postJson('/admin/sections/auto-assign', ['grade_level' => 'grade2', 'school_year' => '2027-2028'])
            ->assertOk()->assertJson(['assigned' => 1]);

        $this->assertSame('Grade 2-A', Enrollment::where('user_id', $old->user_id)->where('school_year', '2027-2028')->value('section'));
        $this->assertSame(0, $this->actingAs($admin, 'web')->getJson('/admin/section/students-data?student_schoolyear=2027-2028')->json('stats.no_section'));
    }
}
