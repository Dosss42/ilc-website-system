<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A student changes their own section once per school year, before classes start (June 1).
 * It used to be any number of times, all year, while "Enrollment Open" was on — and each change
 * removed the student from every section they had ever been in, past years included.
 */
class SectionChangeRuleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function section(string $name, string $sy): Section
    {
        return Section::create(['name' => $name, 'grade_level' => 'grade2', 'max_students' => 30, 'school_year' => $sy, 'is_active' => true]);
    }

    private function change(Enrollment $e, Section $to)
    {
        return $this->actingAs(User::find($e->user_id), 'web')->postJson('/student/change-section', ['section_id' => $to->id]);
    }

    public function test_once_per_school_year_before_classes_start(): void
    {
        Carbon::setTestNow('2027-04-15 09:00'); // S.Y. 2027-2028 classes start June 1, 2027
        $e = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'grade2', 'school_year' => '2027-2028', 'section' => 'Grade2-A']);
        $a = $this->section('Grade2-A', '2027-2028');
        $b = $this->section('Grade2-B', '2027-2028');
        $c = $this->section('Grade2-C', '2027-2028');
        $past = $this->section('Grade1-A', '2026-2027'); // last year's class list
        $a->students()->attach($e->user_id);
        $past->students()->attach($e->user_id);

        $this->change($e, $b)->assertOk()->assertJson(['success' => true, 'section_name' => 'Grade2-B']);
        $this->assertSame('Grade2-B', $e->fresh()->section);
        $this->assertNotNull($e->fresh()->section_changed_at);
        $this->assertDatabaseMissing('section_student', ['user_id' => $e->user_id, 'section_id' => $a->id]);
        $this->assertDatabaseHas('section_student', ['user_id' => $e->user_id, 'section_id' => $past->id]); // past years stay on the class list

        // The one change is used up
        $this->change($e, $c)->assertStatus(403)->assertJson(['message' => 'You already changed your section once this school year (April 15, 2027). For another change, please ask the school office.']);
        $this->assertSame('Grade2-B', $e->fresh()->section);
    }

    public function test_closed_once_classes_have_started(): void
    {
        Carbon::setTestNow('2026-10-28 09:00'); // mid-year, while next year's enrollment is open
        $e = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'grade2', 'school_year' => '2026-2027', 'section' => 'Grade2-A']);
        $this->section('Grade2-A', '2026-2027')->students()->attach($e->user_id);
        $b = $this->section('Grade2-B', '2026-2027');

        $this->change($e, $b)->assertStatus(403)
            ->assertJson(['message' => 'Section changes closed when classes started on June 1, 2026. For a change, please ask the school office.']);
        $this->assertSame('Grade2-A', $e->fresh()->section);
    }
}
