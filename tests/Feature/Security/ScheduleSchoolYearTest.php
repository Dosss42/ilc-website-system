<?php

namespace Tests\Feature\Security;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Schedule Management › School Year: schedules belong to sections, which are created per
 * school year. The list, PDF and Copy Term work on the chosen year only, and schedules of
 * different years never count as conflicts (a teacher's 2026-2027 and 2027-2028 classes at
 * the same time aren't a double booking).
 */
class ScheduleSchoolYearTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->admin   = User::factory()->admin()->create();
        $this->teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Ana']);
        $this->subject = Subject::create(['name' => 'Math', 'code' => 'M1', 'grade_level' => 'grade1', 'is_active' => true]);
    }

    private function section(string $sy, string $name): Section
    {
        return Section::create(['name' => $name, 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => $sy, 'is_active' => true]);
    }

    private function schedule(Section $s, int $term = 1, string $start = '07:00:00', string $end = '08:00:00'): Schedule
    {
        return Schedule::create([
            'section_id' => $s->id, 'subject_id' => $this->subject->id, 'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday', 'start_time' => $start, 'end_time' => $end, 'term' => $term, 'is_active' => true,
        ]);
    }

    public function test_the_list_shows_only_the_chosen_school_year(): void
    {
        $this->schedule($this->section('2026-2027', 'Rizal'));
        $this->schedule($this->section('2027-2028', 'Rizal'));

        $ids = fn ($sy) => collect($this->actingAs($this->admin, 'web')
            ->getJson('/admin/schedules?grade_level=grade1&term=1&school_year=' . $sy)->assertOk()->json('schedules'))
            ->pluck('section.school_year')->unique()->values()->all();

        $this->assertSame(['2026-2027'], $ids('2026-2027'));
        $this->assertSame(['2027-2028'], $ids('2027-2028'));
        $this->assertCount(2, $this->getJson('/admin/schedules?grade_level=grade1&term=1')->json('schedules'), 'no year = all (other callers)');

        // The page has the filter, defaulting to the current year
        $this->get('/admin/dashboard')->assertOk()
            ->assertSee('id="scheduleYearFilter"', false)
            ->assertSee('<option value="2026-2027" selected>2026-2027 (current)</option>', false);
    }

    public function test_different_school_years_are_not_conflicts(): void
    {
        $this->schedule($this->section('2026-2027', 'Rizal'));
        $next = $this->section('2027-2028', 'Rizal');

        // Same teacher, same Monday 7–8 slot, but next school year → no conflict
        $res = $this->actingAs($this->admin, 'web')->postJson('/admin/schedules', [
            'section_id' => $next->id, 'subject_id' => $this->subject->id, 'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday', 'start_time' => '07:00', 'end_time' => '08:00', 'term' => 1, 'is_active' => true,
        ])->assertCreated();
        $this->assertFalse($res->json('has_conflict'));
        $listed = $this->getJson('/admin/schedules?grade_level=grade1&term=1&school_year=2027-2028')->json('schedules');
        $this->assertFalse($listed[0]['has_conflict']);

        // Same year, other section, same teacher and time → still a conflict
        $other = $this->section('2027-2028', 'Bonifacio');
        $res = $this->postJson('/admin/schedules', [
            'section_id' => $other->id, 'subject_id' => $this->subject->id, 'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday', 'start_time' => '07:00', 'end_time' => '08:00', 'term' => 1, 'is_active' => true,
        ])->assertCreated();
        $this->assertTrue($res->json('has_conflict'));
        $this->assertStringContainsString('Teacher is already assigned', $res->json('conflict_reasons.0'));
    }

    public function test_copy_term_only_touches_the_chosen_school_year(): void
    {
        $old = $this->section('2026-2027', 'Rizal');
        $new = $this->section('2027-2028', 'Rizal');
        $oldTerm2 = $this->schedule($old, 2);                       // last year's Term 2 — must survive
        $this->schedule($new, 1);                                   // next year's Term 1 (source)
        $newTerm2 = $this->schedule($new, 2, '09:20:00', '10:20:00'); // next year's Term 2 (replaced)

        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/copy-term', [
            'source_term' => 1, 'target_term' => 2, 'replace_target' => true, 'school_year' => '2027-2028',
        ])->assertOk()->assertJson(['success' => true, 'copied' => 1, 'total' => 1]);

        $this->assertNotNull(Schedule::find($oldTerm2->id), "the other year's Term 2 is not cleared");
        $this->assertNull(Schedule::find($newTerm2->id), "the chosen year's Term 2 is cleared");
        $this->assertSame(1, Schedule::where('term', 2)->where('section_id', $new->id)->count());
    }

    public function test_pdf_uses_the_chosen_school_year(): void
    {
        $this->schedule($this->section('2027-2028', 'Rizal'));
        $res = $this->actingAs($this->admin, 'web')
            ->get('/admin/schedules/pdf?grade_level=grade1&term=1&school_year=2027-2028')->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));
    }
}
