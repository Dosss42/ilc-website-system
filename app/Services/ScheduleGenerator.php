<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Subject Teachers grid + timetable generator for Schedule Management (school decision, October 29, 2026).
 *
 *  1. Subject Teachers: who teaches each subject in each section of a grade — saved as normal
 *     TeacherAssignment rows (the same links grade entry and Teacher Management use).
 *  2. Generator: places those teachers in time slots for every section of a grade — no teacher in two
 *     places at once (other grades' schedules included), each section's subject order shifted so a
 *     teacher shared across sections isn't needed by all of them in the same hour. Teacher load is
 *     shown against the school's limits; overload is FLAGGED, never blocked (school decision).
 *  Preview and save run the same build, so what is saved is exactly what was previewed.
 */
class ScheduleGenerator
{
    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    /** The school's usual day: 60-minute periods from 7:00, Grades 1–6 dismissed at 2:00 PM */
    const DEFAULTS = [
        'start_time'     => '07:00',
        'end_time'       => '14:00',
        'period_minutes' => 60,
        'breaks'         => [['label' => 'Recess', 'start' => '09:00', 'end' => '09:20'], ['label' => 'Lunch', 'start' => '11:20', 'end' => '12:00']],
        'days'           => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'limits'         => ['week_hours' => 30, 'day_periods' => 6, 'consecutive' => 4],
    ];

    /** Nursery and Kinder are half day: 7:00 – 10:20 AM (3 periods around recess) */
    const DISMISSAL = ['nursery' => '10:20', 'kindergarten' => '10:20'];

    /** A grade's usual day — DEFAULTS with its dismissal time, keeping only the breaks before it. */
    public static function gradeDay(string $grade): array
    {
        $end = self::DISMISSAL[$grade] ?? self::DEFAULTS['end_time'];
        return ['end_time' => $end, 'breaks' => array_values(array_filter(self::DEFAULTS['breaks'], fn ($b) => $b['start'] < $end))] + self::DEFAULTS;
    }

    /** How many periods fit in a day before dismissal. */
    public static function periodsBefore(string $start, int $minutes, array $breaks, string $end): int
    {
        $n = 0;
        foreach (self::slots($start, $minutes, $breaks, 24) as $s) {
            if ($s['end'] > substr($end, 0, 5) || $s['end'] < $s['start']) { break; } // past dismissal (or midnight)
            $n++;
        }
        return $n;
    }

    /** The week's periods shared evenly by the subjects, the first ones getting the remainder (15 over 6 → 3,3,3,2,2,2). */
    public static function spread(int $periods, int $subjects): array
    {
        return $subjects ? array_map(fn ($i) => intdiv($periods, $subjects) + ($i < $periods % $subjects ? 1 : 0), range(0, $subjects - 1)) : [];
    }

    // ── Subject Teachers ─────────────────────────────────────────

    /** Sections × subjects of a grade with the teacher of each cell, last year's teacher as a hint, and every teacher's load. */
    public static function teacherGrid(string $schoolYear, string $grade): array
    {
        $sections = Section::where('school_year', $schoolYear)->where('grade_level', $grade)->where('is_active', true)
            ->orderBy('name')->get(['id', 'name', 'teacher_id', 'room_number']);
        $subjects = self::gradeSubjects($grade);
        // Subjects the grade's sections actually have (Section Management › Subjects) — the rest are hidden at first
        $taught = DB::table('section_subject')->whereIn('section_id', $sections->pluck('id'))->pluck('subject_id')->unique();

        $rows = TeacherAssignment::where('school_year', $schoolYear)->whereIn('section_id', $sections->pluck('id'))->get();
        $advisers = $rows->where('is_advisory', true)->pluck('teacher_id', 'section_id');
        $cells = [];
        foreach ($rows->whereNotNull('subject_id') as $r) {
            $cells[$r->section_id][$r->subject_id] = $r->teacher_id;
        }

        // Last year's teacher of each subject in this grade — a suggestion for empty cells
        $prevYear = self::previousYear($schoolYear);
        $lastYear = TeacherAssignment::where('school_year', $prevYear)->whereNotNull('subject_id')
            ->whereIn('section_id', Section::where('school_year', $prevYear)->where('grade_level', $grade)->select('id'))
            ->get()->groupBy('subject_id')
            ->map(fn ($g) => $g->countBy('teacher_id')->sortDesc()->keys()->first());

        return [
            'school_year' => $schoolYear,
            'grade'       => $grade,
            'sections'    => $sections->map(fn ($s) => [
                'id' => $s->id, 'name' => $s->name, 'adviser_id' => $advisers[$s->id] ?? $s->teacher_id,
            ])->values(),
            'subjects'    => $subjects->map(fn ($s) => [
                'id' => $s->id, 'name' => $s->name,
                // taught by a section here, or already has a teacher — no linked subjects at all = show every one
                'taught' => $taught->isEmpty() || $taught->contains($s->id) || collect($cells)->contains(fn ($row) => isset($row[$s->id])),
            ])->sortByDesc('taught')->values(),
            'cells'       => $cells,
            'last_year'   => $lastYear,
            'teachers'    => self::teacherLoads($schoolYear),
            'limits'      => self::DEFAULTS['limits'],
        ];
    }

    /**
     * Save the grid: one teacher per subject per section. Changing a teacher also moves that subject's
     * existing timetable slots to the new teacher (schedules and grade-entry access stay in step);
     * clearing a cell leaves those slots without a teacher.
     */
    public static function saveTeacherGrid(string $schoolYear, array $cells): array
    {
        $changed = 0; $slotsMoved = 0;
        DB::transaction(function () use ($schoolYear, $cells, &$changed, &$slotsMoved) {
            foreach ($cells as $c) {
                $sectionId = (int) $c['section_id'];
                $subjectId = (int) $c['subject_id'];
                $teacherId = !empty($c['teacher_id']) ? (int) $c['teacher_id'] : null;

                $existing = TeacherAssignment::where('school_year', $schoolYear)->where('section_id', $sectionId)
                    ->where('subject_id', $subjectId)->get();
                if ($existing->pluck('teacher_id')->all() === ($teacherId ? [$teacherId] : [])) {
                    continue; // unchanged
                }

                TeacherAssignment::where('school_year', $schoolYear)->where('section_id', $sectionId)
                    ->where('subject_id', $subjectId)->when($teacherId, fn ($q) => $q->where('teacher_id', '!=', $teacherId))->delete();
                if ($teacherId) {
                    TeacherAssignment::firstOrCreate(
                        ['teacher_id' => $teacherId, 'subject_id' => $subjectId, 'section_id' => $sectionId, 'school_year' => $schoolYear],
                        ['is_advisory' => false]
                    );
                }
                $slotsMoved += Schedule::where('section_id', $sectionId)->where('subject_id', $subjectId)
                    ->where(fn ($q) => $teacherId ? $q->whereNull('teacher_id')->orWhere('teacher_id', '!=', $teacherId) : $q->whereNotNull('teacher_id'))
                    ->update(['teacher_id' => $teacherId]);
                $changed++;
            }
        });

        if ($changed) {
            ActivityLogger::log('update', "Subject Teachers saved for S.Y. {$schoolYear}: {$changed} subject(s) changed, {$slotsMoved} timetable slot(s) moved to the new teacher", 'TeacherAssignment');
        }
        return ['changed' => $changed, 'slots_moved' => $slotsMoved];
    }

    // ── Generator ────────────────────────────────────────────────

    /** Builds the timetables (nothing saved) — the preview, and the plan save() writes. */
    public static function build(array $in): array
    {
        $sy      = $in['school_year'];
        $grade   = $in['grade'];
        $terms   = array_values(array_unique(array_map('intval', $in['terms'] ?? [1])));
        $days    = array_values(array_intersect(self::DAYS, $in['days'] ?? self::DEFAULTS['days']));
        $limits  = array_merge(self::DEFAULTS['limits'], array_map('intval', $in['limits'] ?? []));
        $replace = (bool) ($in['replace'] ?? false);
        $breaks  = $in['breaks'] ?? self::DEFAULTS['breaks'];
        $dismiss = substr($in['end_time'] ?? self::gradeDay($grade)['end_time'], 0, 5);

        $subjects = collect($in['subjects'] ?? [])->map(fn ($s) => ['id' => (int) $s['id'], 'per_week' => max(0, min(15, (int) $s['per_week']))])
            ->filter(fn ($s) => $s['per_week'] > 0)->values();
        $subjectNames = Subject::whereIn('id', $subjects->pluck('id'))->pluck('name', 'id');

        // Each day's subjects: whole-week share every day, the remainder spread across the days
        $perDay = array_fill_keys($days, []);
        $ptr = 0;
        foreach ($subjects as $s) {
            for ($i = 0; $i < intdiv($s['per_week'], count($days)); $i++) {
                foreach ($days as $d) { $perDay[$d][] = $s['id']; }
            }
            for ($i = 0; $i < $s['per_week'] % count($days); $i++) {
                $perDay[$days[($ptr + $i) % count($days)]][] = $s['id'];
            }
            $ptr = ($ptr + $s['per_week'] % count($days)) % count($days);
        }
        $periodCount = max(array_map('count', $perDay) ?: [0]);
        $slots = self::slots($in['start_time'] ?? self::DEFAULTS['start_time'], (int) ($in['period_minutes'] ?? 60), $breaks, $periodCount);

        $sections = Section::where('school_year', $sy)->where('grade_level', $grade)->where('is_active', true)->orderBy('name')
            ->when(!empty($in['section_ids']), fn ($q) => $q->whereIn('id', array_map('intval', $in['section_ids'])))->get();
        $assign = TeacherAssignment::where('school_year', $sy)->whereIn('section_id', $sections->pluck('id'))->whereNotNull('subject_id')->get()
            ->groupBy('section_id')->map(fn ($g) => $g->pluck('teacher_id', 'subject_id'));

        // Which sections are skipped (already scheduled for a chosen term) and which are (re)built
        $hasSchedule = Schedule::whereIn('section_id', $sections->pluck('id'))->whereIn('term', $terms)->pluck('section_id')->unique();
        $build = $sections->filter(fn ($s) => $replace || !$hasSchedule->contains($s->id))->values();

        // Everyone else's timetable this school year (what we must not clash with), per term
        $busy = [];
        foreach ($terms as $t) {
            $busy[$t] = Schedule::where('term', $t)->where('is_active', true)->whereNotNull('teacher_id')
                ->whereIn('section_id', Section::where('school_year', $sy)->select('id'))
                ->whereNotIn('section_id', $build->pluck('id'))
                ->with('section:id,name')->get()
                ->map(fn ($s) => ['section' => $s->section->name ?? '?', 'teacher_id' => (int) $s->teacher_id, 'day_of_week' => $s->day_of_week,
                                  'start_time' => substr($s->start_time, 0, 5), 'end_time' => substr($s->end_time, 0, 5)]);
        }

        $out = []; $added = []; $warnings = 0; $missing = [];
        foreach ($sections as $k => $section) {
            if (!$build->contains('id', $section->id)) {
                $out[] = ['id' => $section->id, 'name' => $section->name, 'skipped' => true,
                          'reason' => 'Already has a schedule for term ' . implode('/', $terms) . ' — tick "Replace existing" to rebuild it'];
                continue;
            }
            $order = $subjects->pluck('id')->all();
            $shift = $k % max(1, count($order));
            $order = array_merge(array_slice($order, $shift), array_slice($order, 0, $shift)); // rotate per section

            $grid = [];
            foreach ($days as $d) {
                // Singles in this section's order first, repeats after
                $todo = []; $seen = [];
                foreach ($order as $sid) {
                    $n = count(array_keys($perDay[$d], $sid));
                    if ($n) { $todo[] = $sid; $seen[$sid] = $n - 1; }
                }
                foreach ($order as $sid) { for ($i = 0; $i < ($seen[$sid] ?? 0); $i++) { $todo[] = $sid; } }

                $row = [];
                $t0 = $terms[0];
                foreach ($slots as $j => $slot) {
                    if (!$todo) { break; }
                    // First subject whose teacher is free now (every chosen term), preferring one still within
                    // the day limits — counted over the teacher's whole day, all sections
                    $pick = null;
                    foreach ([true, false] as $strict) {
                        foreach ($todo as $idx => $sid) {
                            $tid = $assign[$section->id][$sid] ?? null;
                            if (!$tid) { $pick = $idx; break 2; }
                            $free = collect($terms)->every(fn ($t) => !self::clash($busy[$t], $tid, $d, $slot));
                            [$dayCount, $run] = self::dayStats($busy[$t0], $tid, $d, $slot);
                            $withinDay = $dayCount < $limits['day_periods'] && $run < $limits['consecutive'];
                            if ($free && (!$strict || $withinDay)) { $pick = $idx; break 2; }
                        }
                    }
                    $pick ??= 0; // nobody free: place it anyway and flag the clash
                    $sid = $todo[$pick];
                    array_splice($todo, $pick, 1);
                    $tid = $assign[$section->id][$sid] ?? null;

                    $notes = [];
                    if (!$tid) {
                        $notes[] = 'No teacher yet';
                        $missing[$section->name . '|' . $sid] = ['section' => $section->name, 'subject' => $subjectNames[$sid] ?? '?'];
                    } else {
                        foreach ($terms as $t) {
                            if ($c = self::clash($busy[$t], $tid, $d, $slot)) {
                                $notes[] = 'Teacher also teaches ' . $c['section'] . ' at this time' . (count($terms) > 1 ? " (term {$t})" : '');
                                break;
                            }
                        }
                        [$dayCount, $run] = self::dayStats($busy[$t0], $tid, $d, $slot);
                        if ($dayCount + 1 > $limits['day_periods']) { $notes[] = "Teacher's " . ($dayCount + 1) . "th period today (limit {$limits['day_periods']})"; }
                        if ($run + 1 > $limits['consecutive']) { $notes[] = 'Teacher has ' . ($run + 1) . " periods in a row (limit {$limits['consecutive']})"; }
                        $added[$tid] = ($added[$tid] ?? 0) + $slot['minutes'];
                        foreach ($terms as $t) {
                            $busy[$t]->push(['section' => $section->name, 'teacher_id' => $tid, 'day_of_week' => $d,
                                'start_time' => $slot['start'], 'end_time' => $slot['end']]);
                        }
                    }
                    if ($slot['end'] > $dismiss) {
                        $notes[] = 'After dismissal (' . Carbon::createFromFormat('H:i', $dismiss)->format('g:i A') . ')';
                    }
                    $warnings += count($notes);
                    $row[] = ['slot' => $j, 'start' => $slot['start'], 'end' => $slot['end'], 'subject_id' => $sid,
                              'subject' => $subjectNames[$sid] ?? '?', 'teacher_id' => $tid, 'notes' => $notes];
                }
                $grid[$d] = $row;
            }
            $out[] = ['id' => $section->id, 'name' => $section->name, 'room' => $section->room_number, 'skipped' => false,
                      'replacing' => $hasSchedule->contains($section->id), 'grid' => $grid];
        }

        // Teacher load (hours a week, one term — every term gets the same timetable)
        $loads = collect(self::teacherLoads($sy, $terms[0] ?? 1, $build->pluck('id')->all()))->keyBy('id');
        $teacherNames = User::whereIn('id', array_keys($added))->pluck('name', 'id');
        $load = collect($added)->map(function ($min, $tid) use ($loads, $teacherNames, $limits) {
            $now = $loads[$tid]['hours'] ?? 0;
            $total = round($now + $min / 60, 1);
            return ['id' => $tid, 'name' => $teacherNames[$tid] ?? '?', 'now' => $now, 'added' => round($min / 60, 1), 'total' => $total,
                    'status' => $total > $limits['week_hours'] ? 'over' : ($total >= $limits['week_hours'] * 0.9 ? 'near' : 'ok')];
        })->sortByDesc('total')->values();

        return [
            'school_year' => $sy, 'grade' => $grade, 'terms' => $terms, 'days' => $days, 'limits' => $limits, 'end_time' => $dismiss,
            'slots' => $slots, 'breaks' => $breaks, 'sections' => $out, 'teacher_load' => $load,
            'teacher_names' => User::whereIn('id', $assign->flatten()->filter()->unique())->pluck('name', 'id'),
            'missing_teachers' => array_values($missing), 'warnings' => $warnings,
            'will_build' => $build->count(), 'skipped' => $sections->count() - $build->count(),
        ];
    }

    /** Builds again from the same settings and saves it (replacing a section's term only when asked). */
    public static function save(array $in): array
    {
        $plan = self::build($in);
        $created = 0;
        DB::transaction(function () use ($plan, &$created) {
            foreach ($plan['sections'] as $s) {
                if ($s['skipped']) { continue; }
                foreach ($plan['terms'] as $term) {
                    Schedule::where('section_id', $s['id'])->where('term', $term)->delete(); // only reached when building it (replace or empty)
                    foreach ($s['grid'] as $day => $rows) {
                        foreach ($rows as $r) {
                            Schedule::create([
                                'section_id' => $s['id'], 'subject_id' => $r['subject_id'], 'teacher_id' => $r['teacher_id'],
                                'day_of_week' => $day, 'start_time' => $r['start'], 'end_time' => $r['end'],
                                'room' => $s['room'], 'is_active' => true, 'term' => $term,
                            ]);
                            $created++;
                        }
                    }
                }
            }
        });
        $over = collect($plan['teacher_load'])->where('status', 'over')->pluck('name')->implode(', ');
        ActivityLogger::log('create', "Generated schedules for {$plan['grade']} (S.Y. {$plan['school_year']}, term " . implode('/', $plan['terms']) . "): "
            . "{$plan['will_build']} section(s), {$created} slot(s), {$plan['warnings']} warning(s)" . ($over ? " — overload: {$over}" : ''), 'Schedule');
        return ['created' => $created, 'sections' => $plan['will_build'], 'skipped' => $plan['skipped'], 'warnings' => $plan['warnings']];
    }

    // ── Helpers ──────────────────────────────────────────────────

    /** Active subjects of a grade, by name (the generator window lets the Admin reorder them). */
    public static function gradeSubjects(string $grade): Collection
    {
        return Subject::where('grade_level', $grade)->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /** The day's periods: back to back from the start time, jumping over recess/lunch. */
    public static function slots(string $start, int $minutes, array $breaks, int $count): array
    {
        $minutes = max(20, min(120, $minutes));
        $t = Carbon::createFromFormat('H:i', substr($start, 0, 5));
        $slots = [];
        $guard = 0;
        while (count($slots) < $count && $guard++ < 40) {
            $end = $t->copy()->addMinutes($minutes);
            foreach ($breaks as $b) {
                $bs = Carbon::createFromFormat('H:i', substr($b['start'], 0, 5));
                $be = Carbon::createFromFormat('H:i', substr($b['end'], 0, 5));
                if ($t->lt($be) && $end->gt($bs)) { $t = $be->copy(); continue 2; }
            }
            $slots[] = ['start' => $t->format('H:i'), 'end' => $end->format('H:i'), 'minutes' => $minutes];
            $t = $end;
        }
        return $slots;
    }

    /** The slot a teacher is already busy in at this time, if any. */
    private static function clash(Collection $busy, int $teacherId, string $day, array $slot): ?array
    {
        return $busy->first(fn ($b) => $b['teacher_id'] === $teacherId && $b['day_of_week'] === $day
            && $b['start_time'] < $slot['end'] && $b['end_time'] > $slot['start']);
    }

    /** [periods the teacher already has that day, periods in a row ending right at this slot] — all sections. */
    private static function dayStats(Collection $busy, int $teacherId, string $day, array $slot): array
    {
        $mine = $busy->filter(fn ($b) => $b['teacher_id'] === $teacherId && $b['day_of_week'] === $day);
        $run = 0;
        $edge = $slot['start'];
        while ($prev = $mine->first(fn ($b) => $b['end_time'] === $edge)) {
            $run++;
            $edge = $prev['start_time'];
        }
        return [$mine->count(), $run];
    }

    /** Every active teacher's teaching hours a week this school year (one term), leaving out the given sections. */
    public static function teacherLoads(string $schoolYear, int $term = 1, array $exceptSections = []): array
    {
        $minutes = Schedule::where('term', $term)->where('is_active', true)->whereNotNull('teacher_id')
            ->whereIn('section_id', Section::where('school_year', $schoolYear)->select('id'))
            ->when($exceptSections, fn ($q) => $q->whereNotIn('section_id', $exceptSections))
            ->get(['teacher_id', 'start_time', 'end_time'])
            ->groupBy('teacher_id')
            ->map(fn ($g) => $g->sum(fn ($s) => max(0, (strtotime($s->end_time) - strtotime($s->start_time)) / 60)));

        return User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'hours' => round(($minutes[$t->id] ?? 0) / 60, 1)])
            ->all();
    }

    private static function previousYear(string $sy): string
    {
        return preg_match('/^(\d{4})-(\d{4})$/', $sy, $m) ? ($m[1] - 1) . '-' . ($m[2] - 1) : $sy;
    }
}
