<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['section', 'subject', 'teacher']);

        // Filter by section_id
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by grade_level (via section relationship)
        if ($request->filled('grade_level')) {
            $query->whereHas('section', function($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
            });
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->get();
        $this->flagConflicts($schedules);
        return response()->json(['schedules' => $schedules]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'required|exists:subjects,id',
            'teacher_id'  => 'nullable|exists:users,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'room'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
            'term'        => 'required|integer|min:1|max:3',
        ]);

        // Conflicts no longer block saving — the schedule is created either way and
        // flagged so the UI can render it with a red warning instead of refusing it.
        $conflicts = $this->detectConflicts($validated);

        $schedule = Schedule::create($validated);
        $schedule->load(['section', 'subject', 'teacher']);
        $schedule->has_conflict     = !empty($conflicts);
        $schedule->conflict_reasons = $conflicts;

        $this->ensureTeacherAssignment($schedule);

        $desc = "Created schedule: {$schedule->subject->name} for {$schedule->section->name} on {$schedule->day_of_week} "
              . substr($schedule->start_time, 0, 5) . '–' . substr($schedule->end_time, 0, 5)
              . (!empty($conflicts) ? ' (⚠ conflict)' : '');
        ActivityLogger::log('create', $desc, 'Schedule', $schedule->id);

        return response()->json($schedule, 201);
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['section', 'subject', 'teacher']);
        return response()->json($schedule);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'required|exists:subjects,id',
            'teacher_id'  => 'nullable|exists:users,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'room'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
            'term'        => 'required|integer|min:1|max:3',
        ]);

        // Conflicts no longer block saving — the schedule is updated either way and
        // flagged so the UI can render it with a red warning instead of refusing it.
        $conflicts = $this->detectConflicts($validated, $schedule->id);

        $schedule->update($validated);
        $schedule->load(['section', 'subject', 'teacher']);
        $schedule->has_conflict     = !empty($conflicts);
        $schedule->conflict_reasons = $conflicts;

        $this->ensureTeacherAssignment($schedule);

        $desc = "Updated schedule: {$schedule->subject->name} for {$schedule->section->name} on {$schedule->day_of_week} "
              . substr($schedule->start_time, 0, 5) . '–' . substr($schedule->end_time, 0, 5)
              . (!empty($conflicts) ? ' (⚠ conflict)' : '');
        ActivityLogger::log('update', $desc, 'Schedule', $schedule->id);

        return response()->json($schedule);
    }

    /**
     * Keep teacher_assignments in sync with schedules — a schedule row is
     * the source of truth for "this teacher teaches this subject in this
     * section", but grade-entry authorization (Teacher\DashboardController::
     * teacherOwnsSubjectInSection) checks teacher_assignments, not schedules.
     * Without this, a schedule created here with no matching assignment
     * silently locks that teacher out of entering grades for it — exactly
     * the drift `php artisan db:verify-normalization` (schedules vs
     * teacher_assignments check) exists to catch. Runs after every
     * create/update so new schedules can never reintroduce that gap.
     */
    private function ensureTeacherAssignment(Schedule $schedule): void
    {
        if (!$schedule->teacher_id || !$schedule->subject_id) {
            return;
        }

        $schoolYear = $schedule->section->school_year ?? null;
        if (!$schoolYear) {
            return;
        }

        TeacherAssignment::firstOrCreate([
            'teacher_id'  => $schedule->teacher_id,
            'subject_id'  => $schedule->subject_id,
            'section_id'  => $schedule->section_id,
            'school_year' => $schoolYear,
            'is_advisory' => false,
        ]);
    }

    /**
     * Detect scheduling conflicts for a given set of schedule data.
     * Checks teacher double-booking, room double-booking, and section overlap.
     * Pass $excludeId when updating to skip the schedule being edited.
     */
    private function detectConflicts(array $data, ?int $excludeId = null): array
    {
        $conflicts = [];

        // Base query: same day, same term, active schedules, overlapping time
        // Two slots overlap when: start_A < end_B AND end_A > start_B
        $base = Schedule::where('day_of_week', $data['day_of_week'])
            ->where('term', $data['term'])
            ->where('is_active', true)
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId));

        // 1. Teacher conflict — only flag when teacher is on a DIFFERENT section at the same time.
        //    Same section is fine (e.g. Nursery/Kinder advisory teacher teaching multiple subjects).
        if (!empty($data['teacher_id'])) {
            $teacherConflict = (clone $base)
                ->where('teacher_id', $data['teacher_id'])
                ->where('section_id', '!=', $data['section_id'])
                ->with(['section:id,name,grade_level', 'subject:id,name'])
                ->first();

            if ($teacherConflict) {
                $conflicts[] = "Teacher is already assigned to another section ({$teacherConflict->section->name} - {$teacherConflict->subject->name}, {$teacherConflict->start_time}–{$teacherConflict->end_time}) on {$data['day_of_week']}.";
            }
        }

        // 2. Room conflict
        if (!empty($data['room'])) {
            $roomConflict = (clone $base)
                ->where('room', $data['room'])
                ->with(['section:id,name', 'subject:id,name'])
                ->first();

            if ($roomConflict) {
                $conflicts[] = "Room \"{$data['room']}\" is already occupied by {$roomConflict->section->name} - {$roomConflict->subject->name} ({$roomConflict->start_time}–{$roomConflict->end_time}) on {$data['day_of_week']}.";
            }
        }

        // 3. Section conflict
        $sectionConflict = (clone $base)
            ->where('section_id', $data['section_id'])
            ->with(['subject:id,name'])
            ->first();

        if ($sectionConflict) {
            $conflicts[] = "This section already has {$sectionConflict->subject->name} scheduled at {$sectionConflict->start_time}–{$sectionConflict->end_time} on {$data['day_of_week']}.";
        }

        return $conflicts;
    }

    /**
     * Mark every schedule in the given collection with `has_conflict` (bool) and
     * `conflict_reasons` (string[]) by comparing it against every other schedule
     * already loaded — same pairwise rules as detectConflicts(). A teacher or
     * room conflict can involve a schedule from a completely different
     * section/grade than the one currently filtered on screen, so each
     * schedule is compared against every OTHER active schedule on the same
     * day + term (a small extra query per distinct day/term combo actually
     * present), not just the rows already in $schedules — otherwise a Grade 1
     * teacher clash never shows up while viewing Kindergarten's grid.
     */
    private function flagConflicts($schedules): void
    {
        // Accumulate in a plain array keyed by schedule id first — appending
        // directly to an Eloquent model's dynamic property with `$model->prop[] =`
        // doesn't work (Eloquent's __get() returns attributes by value, so PHP
        // treats it as "indirect modification of overloaded property" and throws
        // in strict/dev error reporting). Assign the finished arrays back to each
        // model in one shot at the end instead.
        $reasonsById = [];
        foreach ($schedules as $s) {
            $reasonsById[$s->id] = [];
        }

        $combos = $schedules->map(fn($s) => $s->day_of_week . '|' . (int) $s->term)->unique();

        foreach ($combos as $combo) {
            [$day, $term] = explode('|', $combo, 2);

            $everyoneThatDayTerm = Schedule::where('day_of_week', $day)
                ->where('term', (int) $term)
                ->where('is_active', true)
                ->with(['section:id,name', 'subject:id,name'])
                ->get();

            foreach ($schedules->where('day_of_week', $day)->where('term', (int) $term) as $a) {
                foreach ($everyoneThatDayTerm as $b) {
                    if ($a->id === $b->id) continue;
                    if (!$a->is_active || !$b->is_active) continue;
                    if (!($a->start_time < $b->end_time && $a->end_time > $b->start_time)) continue;

                    $bName = optional($b->subject)->name ?? 'a subject';
                    $bSec  = optional($b->section)->name ?? 'another section';

                    if ($a->teacher_id && $b->teacher_id && $a->teacher_id === $b->teacher_id && $a->section_id !== $b->section_id) {
                        $reasonsById[$a->id][] = "Teacher also teaches {$bName} ({$bSec}) at this time.";
                    } elseif ($a->room && $b->room && $a->room === $b->room && $a->section_id !== $b->section_id) {
                        $reasonsById[$a->id][] = "Room \"{$a->room}\" is also used by {$bSec} - {$bName} at this time.";
                    } elseif ($a->section_id === $b->section_id) {
                        $reasonsById[$a->id][] = "This section also has {$bName} scheduled at this time.";
                    }
                }
            }
        }

        foreach ($schedules as $s) {
            $s->conflict_reasons = $reasonsById[$s->id] ?? [];
            $s->has_conflict     = !empty($s->conflict_reasons);
        }
    }

    /**
     * Duplicate an entire term's schedule into another term (same school year,
     * same sections). Runs the same conflict checks as a normal create — any
     * block that would conflict in the target term is skipped and reported
     * rather than failing the whole batch.
     */
    public function copyTerm(Request $request)
    {
        $validated = $request->validate([
            'source_term'     => 'required|integer|min:1|max:3',
            'target_term'     => 'required|integer|min:1|max:3|different:source_term',
            'replace_target'  => 'boolean',
        ]);

        $sourceSchedules = Schedule::where('term', $validated['source_term'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($sourceSchedules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No schedule found for the source term — nothing to copy.',
            ], 422);
        }

        if ($request->boolean('replace_target')) {
            Schedule::where('term', $validated['target_term'])->delete();
        }

        $copied  = 0;
        $skipped = [];

        foreach ($sourceSchedules as $src) {
            $data = [
                'section_id'  => $src->section_id,
                'subject_id'  => $src->subject_id,
                'teacher_id'  => $src->teacher_id,
                'day_of_week' => $src->day_of_week,
                'start_time'  => $src->start_time,
                'end_time'    => $src->end_time,
                'room'        => $src->room,
                'is_active'   => $src->is_active,
                'term'        => $validated['target_term'],
            ];

            $conflicts = $this->detectConflicts($data);
            if (!empty($conflicts)) {
                $skipped[] = [
                    'section' => $src->section->name ?? ('#' . $src->section_id),
                    'subject' => $src->subject->name ?? ('#' . $src->subject_id),
                    'day'     => $src->day_of_week,
                    'time'    => $src->start_time . '–' . $src->end_time,
                    'reasons' => $conflicts,
                ];
                continue;
            }

            $newSchedule = Schedule::create($data);
            $newSchedule->load('section');
            $this->ensureTeacherAssignment($newSchedule);
            $copied++;
        }

        return response()->json([
            'success' => true,
            'copied'  => $copied,
            'skipped' => $skipped,
            'total'   => $sourceSchedules->count(),
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->load(['section', 'subject']);
        $desc = "Deleted schedule: " . ($schedule->subject->name ?? '—') . ' for ' . ($schedule->section->name ?? '—')
              . " on {$schedule->day_of_week} " . substr($schedule->start_time, 0, 5) . '–' . substr($schedule->end_time, 0, 5);
        $scheduleId = $schedule->id;
        $schedule->delete();
        ActivityLogger::log('delete', $desc, 'Schedule', $scheduleId);
        return response()->json(['success' => true]);
    }

    public function getSectionSchedules(Section $section)
    {
        $schedules = $section->schedules()->with(['subject', 'teacher'])->get();
        return response()->json($schedules);
    }

    public function getTeacherSchedules(User $teacher)
    {
        $schedules = $teacher->schedules()->with(['section', 'subject'])->get();
        return response()->json($schedules);
    }
}
