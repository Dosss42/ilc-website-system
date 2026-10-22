<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin / Super Admin "Change Section" in the enrollment details modal
 * (POST /admin/enrollments/{id}/change-section). The sections list it picks
 * from includes inactive and other-school-year sections, so the server must
 * refuse those — and it returns the new section's name for the inline editor.
 */
class AdminChangeSectionTest extends TestCase
{
    use RefreshDatabase;

    private function section(array $attrs = []): Section
    {
        return Section::create(array_merge([
            'name' => 'Section ' . uniqid(), 'grade_level' => 'grade1', 'max_students' => 30,
            'school_year' => '2026-2027', 'is_active' => true,
        ], $attrs));
    }

    public function test_admin_and_superadmin_can_move_a_student_to_another_section(): void
    {
        foreach (['admin', 'superadmin'] as $role) {
            $from = $this->section(['name' => "1-A $role"]);
            $to   = $this->section(['name' => "1-B $role"]);
            $enrollment = Enrollment::factory()->create(['section' => $from->name]);
            $from->students()->attach($enrollment->user_id);

            $this->actingAs(User::factory()->{$role}()->create(), 'web')
                ->postJson("/admin/enrollments/{$enrollment->id}/change-section", ['section_id' => $to->id])
                ->assertOk()
                ->assertJson(['success' => true, 'section_name' => "1-B $role"]);

            $this->assertSame("1-B $role", $enrollment->fresh()->section);
            $this->assertDatabaseHas('section_student', ['user_id' => $enrollment->user_id, 'section_id' => $to->id]);
            $this->assertDatabaseMissing('section_student', ['user_id' => $enrollment->user_id, 'section_id' => $from->id]);
        }
    }

    public function test_inactive_other_year_wrong_grade_and_full_sections_are_refused(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create(['section' => 'Original']);

        $cases = [
            'inactive'      => [$this->section(['is_active' => false]), 'is not active'],
            'other year'    => [$this->section(['school_year' => '2025-2026']), 'belongs to S.Y. 2025-2026'],
            'wrong grade'   => [$this->section(['grade_level' => 'grade2']), 'does not match'],
            'full'          => [$this->section(['max_students' => 0]), 'is already full'],
        ];
        foreach ($cases as $label => [$section, $expect]) {
            $res = $this->actingAs($admin, 'web')
                ->postJson("/admin/enrollments/{$enrollment->id}/change-section", ['section_id' => $section->id]);
            $this->assertFalse($res->json('success'), "$label should be refused");
            $this->assertStringContainsString($expect, $res->json('message'), "$label message");
        }

        $this->assertSame('Original', $enrollment->fresh()->section);
    }

    public function test_a_student_cannot_use_the_admin_endpoint(): void
    {
        $to = $this->section();
        $enrollment = Enrollment::factory()->create(['section' => 'Original']);

        $this->actingAs(User::find($enrollment->user_id), 'web')
            ->postJson("/admin/enrollments/{$enrollment->id}/change-section", ['section_id' => $to->id])
            ->assertStatus(403);

        $this->assertSame('Original', $enrollment->fresh()->section);
    }
}
