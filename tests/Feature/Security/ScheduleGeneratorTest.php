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
}
