<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Section names are saved with a capital first letter on each word (same rule as teacher names), and renaming a
 * section moves its own students' enrollments (which keep the section as plain text) to the new name.
 */
class SectionNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_section_name_is_capitalized(): void
    {
        $this->actingAs(User::factory()->admin()->create(), 'web')->postJson('/admin/sections', [
            'name' => 'rizal b', 'grade_level' => 'grade3', 'school_year' => '2026-2027', 'is_active' => true,
        ])->assertSuccessful();
        $this->assertTrue(Section::where('name', 'Rizal B')->exists());
    }

    public function test_renaming_moves_only_this_sections_students(): void
    {
        $section = Section::create(['name' => 'grade 3B', 'grade_level' => 'grade3', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $mine = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade3', 'section' => 'grade 3B', 'status' => 'enrolled']);
        $section->students()->attach($mine->user_id);
        // same name, but another section's student (different grade) and another school year — must not change
        $other = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade4', 'section' => 'grade 3B', 'status' => 'enrolled']);
        $lastYear = Enrollment::factory()->create(['school_year' => '2025-2026', 'grade_level' => 'grade3', 'section' => 'grade 3B', 'status' => 'completed']);

        $this->actingAs(User::factory()->admin()->create(), 'web')->putJson("/admin/sections/{$section->id}", [
            'name' => 'grade 3B', 'grade_level' => 'grade3', 'school_year' => '2026-2027', 'is_active' => true, 'max_students' => 30,
        ])->assertSuccessful();

        $this->assertSame('Grade 3B', $section->fresh()->name, 'capitalized on edit');
        $this->assertSame('Grade 3B', $mine->fresh()->section, "this section's student follows the new name");
        $this->assertSame('grade 3B', $other->fresh()->section, 'another section with the same name is untouched');
        $this->assertSame('grade 3B', $lastYear->fresh()->section, 'another school year is untouched');
    }
}
