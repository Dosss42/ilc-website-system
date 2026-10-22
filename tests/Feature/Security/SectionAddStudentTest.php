<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Section Management > Add Student. The modal used to read page 1 of
 * /admin/students (15 rows) and filter it in the browser, so unassigned
 * students on later pages never appeared ("No eligible students found").
 * It now uses GET /admin/sections/{id}/eligible-students.
 */
class SectionAddStudentTest extends TestCase
{
    use RefreshDatabase;

    private function section(array $a = []): Section
    {
        return Section::create(array_merge([
            'name' => 'S-' . uniqid(), 'grade_level' => 'grade1', 'max_students' => 30,
            'school_year' => '2026-2027', 'is_active' => true,
        ], $a));
    }

    public function test_lists_every_unassigned_student_of_the_sections_grade_and_year_not_just_page_one(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = $this->section(['name' => 'Grade 1 - B']);
        $other  = $this->section(['name' => 'Grade 1 - A']);

        // 20 students already placed in a Grade 1 section (more than one page of the old list)
        Enrollment::factory()->count(20)->create(['payment_status' => 'paid'])
            ->each(fn ($e) => $other->students()->attach($e->user_id));

        $waiting  = Enrollment::factory()->create(['payment_status' => 'partial']);                     // eligible
        $approved = Enrollment::factory()->create(['status' => 'approved', 'payment_status' => 'pending']); // eligible (approved)
        $unpaid   = Enrollment::factory()->create(['payment_status' => 'pending']);                       // eligible (enrolled, not paid yet)
        Enrollment::factory()->create(['grade_level' => 'grade2', 'payment_status' => 'paid']);           // other grade
        Enrollment::factory()->create(['school_year' => '2025-2026', 'payment_status' => 'paid']);        // other year
        Enrollment::factory()->create(['status' => 'pending', 'payment_status' => 'paid']);               // new applicant, not approved yet

        $res = $this->actingAs($admin, 'web')->getJson("/admin/sections/{$target->id}/eligible-students")->assertOk();

        $ids = collect($res->json('students'))->pluck('id')->sort()->values()->all();
        $this->assertSame(collect([$waiting->user_id, $approved->user_id, $unpaid->user_id])->sort()->values()->all(), $ids);
        $this->assertSame(3, $res->json('total'));
        $this->assertSame(['2025-2026' => 1], $res->json('waiting_elsewhere'));
    }

    public function test_a_promoted_student_is_offered_in_next_years_section_and_explained_in_this_years(): void
    {
        $admin = User::factory()->admin()->create();
        $thisYear = $this->section(['grade_level' => 'grade2', 'name' => 'Grade2B', 'school_year' => '2026-2027']);

        // What promoteStudent() leaves behind when no 2027-2028 Grade 2 section exists yet:
        // old enrollment completed, new pending one for next year, no section.
        $old = Enrollment::factory()->create(['grade_level' => 'grade1', 'status' => 'completed']);
        $new = Enrollment::factory()->create([
            'user_id' => $old->user_id, 'grade_level' => 'grade2', 'school_year' => '2027-2028',
            'status' => 'pending', 'payment_status' => 'pending', 'section' => null,
        ]);
        \App\Models\Promotion::create([
            'student_id' => $old->user_id, 'from_grade' => 'grade1', 'to_grade' => 'grade2',
            'from_school_year' => '2026-2027', 'to_school_year' => '2027-2028', 'from_status' => 'enrolled',
            'enrollment_id' => $new->id, 'previous_enrollment_id' => $old->id,
            'promoted_by' => $admin->id, 'promoted_at' => now(), 'status' => 'completed',
        ]);

        // This year's Grade2B: not offered (wrong school year), but the modal is told where they are
        $res = $this->actingAs($admin, 'web')->getJson("/admin/sections/{$thisYear->id}/eligible-students")->assertOk();
        $this->assertSame([], $res->json('students'));
        $this->assertSame(['2027-2028' => 1], $res->json('waiting_elsewhere'));

        // Once a 2027-2028 Grade 2 section exists, the promoted student is offered there and can be added
        $nextYear = $this->section(['grade_level' => 'grade2', 'name' => 'Grade2 2027', 'school_year' => '2027-2028']);
        $res = $this->actingAs($admin, 'web')->getJson("/admin/sections/{$nextYear->id}/eligible-students")->assertOk();
        $this->assertSame([$old->user_id], collect($res->json('students'))->pluck('id')->all());

        $this->actingAs($admin, 'web')->postJson("/admin/sections/{$nextYear->id}/add-student", ['student_id' => $old->user_id])
            ->assertOk()->assertJson(['success' => true]);
        $this->assertSame('Grade2 2027', $new->fresh()->section);
    }

    public function test_adding_from_the_list_works_and_the_student_then_disappears_from_it(): void
    {
        $admin   = User::factory()->admin()->create();
        $section = $this->section();
        $e       = Enrollment::factory()->create(['status' => 'approved', 'payment_status' => 'partial', 'section' => null]);

        $this->actingAs($admin, 'web')->postJson("/admin/sections/{$section->id}/add-student", ['student_id' => $e->user_id])
            ->assertOk()->assertJson(['success' => true]);

        $this->assertSame($section->name, $e->fresh()->section);
        $this->assertSame('enrolled', $e->fresh()->status);
        $this->assertSame([], $this->actingAs($admin, 'web')->getJson("/admin/sections/{$section->id}/eligible-students")->json('students'));
    }

    public function test_a_student_cannot_be_added_to_a_section_of_another_grade(): void
    {
        $admin   = User::factory()->admin()->create();
        $grade6  = $this->section(['grade_level' => 'grade6', 'name' => 'Grade 6 - A']);
        $e       = Enrollment::factory()->create(['payment_status' => 'paid']); // grade1 student

        $this->actingAs($admin, 'web')->postJson("/admin/sections/{$grade6->id}/add-student", ['student_id' => $e->user_id])
            ->assertStatus(422)->assertJsonPath('error', fn ($m) => str_contains($m, 'grade6 section'));

        $this->assertDatabaseMissing('section_student', ['user_id' => $e->user_id]);
    }
}
