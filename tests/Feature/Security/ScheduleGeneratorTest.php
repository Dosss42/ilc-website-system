<?php

namespace Tests\Feature\Security;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Schedule Management › Subject Teachers + Generate Schedule (App\Services\ScheduleGenerator).
 */
class ScheduleGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private array $sec = [];
    private array $sub = [];
    private array $t = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        foreach (['Anya', 'Ben', 'Cora'] as $n) {
            $this->t[$n] = User::factory()->teacher()->create(['name' => $n]);
        }
        foreach (['A', 'B'] as $n) {
            $this->sec[$n] = Section::create(['name' => "Grade 1-{$n}", 'grade_level' => 'grade1', 'school_year' => '2027-2028', 'max_students' => 30, 'is_active' => true, 'room_number' => "R1{$n}"]);
        }
        foreach (['English', 'Math', 'Science'] as $n) {
            $this->sub[$n] = Subject::create(['name' => $n, 'code' => strtoupper(substr($n, 0, 3)) . '1', 'grade_level' => 'grade1', 'is_active' => true]);
        }
    }

    private function cells(array $map): array
    {
        $cells = [];
        foreach ($map as $sec => $subjects) {
            foreach ($subjects as $sub => $teacher) {
                $cells[] = ['section_id' => $this->sec[$sec]->id, 'subject_id' => $this->sub[$sub]->id, 'teacher_id' => $teacher ? $this->t[$teacher]->id : null];
            }
        }
        return $cells;
    }

    private function input(array $extra = []): array
    {
        return array_merge([
            'school_year' => '2027-2028', 'grade' => 'grade1', 'terms' => [1], 'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'start_time' => '07:00', 'period_minutes' => 60,
            'breaks' => [['label' => 'Recess', 'start' => '09:00', 'end' => '09:20']],
            'subjects' => collect($this->sub)->map(fn ($s) => ['id' => $s->id, 'per_week' => 5])->values()->all(),
            'limits' => ['week_hours' => 30, 'day_periods' => 6, 'consecutive' => 4], 'replace' => false,
        ], $extra);
    }

    public function test_subject_teachers_then_a_clash_free_timetable_that_saves_as_previewed(): void
    {
        // ① Subject Teachers: Ben teaches Math in BOTH sections
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/subject-teachers', ['school_year' => '2027-2028',
            'cells' => $this->cells(['A' => ['English' => 'Anya', 'Math' => 'Ben', 'Science' => 'Anya'], 'B' => ['English' => 'Cora', 'Math' => 'Ben', 'Science' => 'Cora']])])
            ->assertOk()->assertJson(['changed' => 6]);
        $this->assertSame(2, TeacherAssignment::where('teacher_id', $this->t['Ben']->id)->where('subject_id', $this->sub['Math']->id)->count());

        $grid = $this->actingAs($this->admin, 'web')->getJson('/admin/schedules/subject-teachers?school_year=2027-2028&grade=grade1')->assertOk();
        $this->assertSame($this->t['Ben']->id, $grid->json('cells.' . $this->sec['B']->id . '.' . $this->sub['Math']->id));

        // ② Preview: 3 periods a day, recess skipped, Ben never in two sections at once, no warnings
        $p = $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/preview', $this->input())->assertOk()->json();
        $this->assertSame(['07:00', '08:00', '09:20'], array_column($p['slots'], 'start'));
        $this->assertSame(0, $p['warnings']);
        foreach ($p['days'] as $day) {
            $benA = collect($p['sections'][0]['grid'][$day])->where('teacher_id', $this->t['Ben']->id)->pluck('start')->all();
            $benB = collect($p['sections'][1]['grid'][$day])->where('teacher_id', $this->t['Ben']->id)->pluck('start')->all();
            $this->assertEmpty(array_intersect($benA, $benB), "Ben double-booked on {$day}");
        }
        $this->assertSame(10.0, (float) collect($p['teacher_load'])->firstWhere('name', 'Anya')['total']); // English + Science in 1-A, 5 h each

        // ③ Save = exactly the preview
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/save', $this->input())->assertOk()->assertJson(['created' => 30, 'sections' => 2]);
        $saved = Schedule::where('section_id', $this->sec['A']->id)->where('day_of_week', 'Monday')->orderBy('start_time')->get();
        $this->assertSame(collect($p['sections'][0]['grid']['Monday'])->pluck('subject_id')->all(), $saved->pluck('subject_id')->all());
        $this->assertSame('R1A', $saved->first()->room);

        // ④ Again without "replace": both sections skipped, nothing touched
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/save', $this->input())->assertOk()->assertJson(['created' => 0, 'skipped' => 2]);
        $this->assertSame(30, Schedule::count());

        // ⑤ Changing a subject teacher moves that subject's slots to the new teacher
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/subject-teachers', ['school_year' => '2027-2028',
            'cells' => $this->cells(['B' => ['Math' => 'Cora']])])->assertOk()->assertJson(['changed' => 1, 'slots_moved' => 5]);
        $this->assertSame([$this->t['Cora']->id], Schedule::where('section_id', $this->sec['B']->id)->where('subject_id', $this->sub['Math']->id)->pluck('teacher_id')->unique()->values()->all());
        $this->assertFalse(TeacherAssignment::where('teacher_id', $this->t['Ben']->id)->where('section_id', $this->sec['B']->id)->exists());
    }

    public function test_overload_is_flagged_not_blocked_and_missing_teachers_are_listed(): void
    {
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/subject-teachers', ['school_year' => '2027-2028',
            'cells' => $this->cells(['A' => ['English' => 'Anya', 'Math' => 'Anya', 'Science' => 'Anya']])])->assertOk();

        $p = $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/preview',
            $this->input(['limits' => ['week_hours' => 10, 'day_periods' => 6, 'consecutive' => 4]]))->assertOk()->json();

        $anya = collect($p['teacher_load'])->firstWhere('name', 'Anya');
        $this->assertSame('over', $anya['status']); // 15 h a week > 10 h limit
        $this->assertCount(3, $p['missing_teachers']); // Grade 1-B has nobody yet
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/save',
            $this->input(['limits' => ['week_hours' => 10, 'day_periods' => 6, 'consecutive' => 4]]))->assertOk()->assertJson(['created' => 30]);
    }

    public function test_each_grade_gets_its_own_day_and_the_periods_are_shared_by_its_subjects(): void
    {
        // Nursery: half day 7:00–10:20 = 3 periods a day, 15 a week over 6 subjects → 3,3,3,2,2,2
        $nursery = Section::create(['name' => 'Nursery', 'grade_level' => 'nursery', 'school_year' => '2027-2028', 'max_students' => 20, 'is_active' => true]);
        foreach (['Aesthetic', 'Cognitive', 'Literacy', 'Physical', 'Socio-Emotional', 'Values'] as $n) {
            $s = Subject::create(['name' => $n, 'code' => 'N' . substr($n, 0, 3), 'grade_level' => 'nursery', 'is_active' => true]);
            $nursery->subjects()->attach($s->id);
        }
        $d = $this->actingAs($this->admin, 'web')->getJson('/admin/schedules/generator/defaults?grade=nursery&school_year=2027-2028')->assertOk();
        $this->assertSame('10:20', $d->json('end_time'));
        $this->assertSame(['Recess'], array_column($d->json('breaks'), 'label'));
        $this->assertSame([3, 3, 3, 2, 2, 2], array_column($d->json('subjects'), 'per_week'));

        // Grade 1: whole day 7:00–2:00 = 6 a day, 30 a week; an active subject not linked to the sections is left out
        $hcii = Subject::create(['name' => 'Hcii', 'code' => 'HCI1', 'grade_level' => 'grade1', 'is_active' => true]);
        foreach ($this->sec as $sec) { $sec->subjects()->attach(collect($this->sub)->pluck('id')); }
        $d = $this->actingAs($this->admin, 'web')->getJson('/admin/schedules/generator/defaults?grade=grade1&school_year=2027-2028')->assertOk();
        $this->assertSame('14:00', $d->json('end_time'));
        $this->assertSame([10, 10, 10], array_column($d->json('subjects'), 'per_week'));
        $this->assertSame(['Hcii'], $d->json('other_subjects'));
        $this->assertNotContains($hcii->id, array_column($d->json('subjects'), 'id'));

        // More periods than the day holds: the extra ones are flagged "After dismissal", not dropped
        $p = $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/preview', $this->input([
            'end_time' => '09:00', 'section_ids' => [$this->sec['A']->id]]))->assertOk()->json();
        $late = collect($p['sections'][0]['grid']['Monday'])->filter(fn ($r) => in_array('After dismissal (9:00 AM)', $r['notes']));
        $this->assertSame(['09:20'], $late->pluck('start')->values()->all());
    }

    public function test_only_the_chosen_sections_are_built_and_the_others_still_count_for_clashes(): void
    {
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/subject-teachers', ['school_year' => '2027-2028',
            'cells' => $this->cells(['A' => ['English' => 'Anya', 'Math' => 'Ben', 'Science' => 'Anya'], 'B' => ['English' => 'Cora', 'Math' => 'Ben', 'Science' => 'Cora']])])->assertOk();

        // Grade 1-A only
        $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/save', $this->input(['section_ids' => [$this->sec['A']->id]]))
            ->assertOk()->assertJson(['created' => 15, 'sections' => 1, 'skipped' => 0]);
        $this->assertSame(0, Schedule::where('section_id', $this->sec['B']->id)->count());

        // Later Grade 1-B only: Ben's Math in 1-A stays put and 1-B's Math avoids those times
        $p = $this->actingAs($this->admin, 'web')->postJson('/admin/schedules/generator/preview', $this->input(['section_ids' => [$this->sec['B']->id]]))->assertOk()->json();
        $this->assertSame(['Grade 1-B'], array_column($p['sections'], 'name'));
        $this->assertSame(0, $p['warnings']);
        $benA = Schedule::where('section_id', $this->sec['A']->id)->where('teacher_id', $this->t['Ben']->id)->get()
            ->map(fn ($s) => $s->day_of_week . substr($s->start_time, 0, 5))->all();
        foreach ($p['sections'][0]['grid'] as $day => $rows) {
            foreach (collect($rows)->where('teacher_id', $this->t['Ben']->id) as $r) {
                $this->assertNotContains($day . $r['start'], $benA, "Ben double-booked on {$day} {$r['start']}");
            }
        }
    }
}
