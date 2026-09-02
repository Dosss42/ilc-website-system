<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\User;
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

        $conflicts = $this->detectConflicts($validated);
        if (!empty($conflicts)) {
            return response()->json(['success' => false, 'conflicts' => $conflicts], 422);
        }

        $schedule = Schedule::create($validated);
        return response()->json($schedule->load(['section', 'subject', 'teacher']), 201);
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

        $conflicts = $this->detectConflicts($validated, $schedule->id);
        if (!empty($conflicts)) {
            return response()->json(['success' => false, 'conflicts' => $conflicts], 422);
        }

        $schedule->update($validated);
        return response()->json($schedule->load(['section', 'subject', 'teacher']));
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

            Schedule::create($data);
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
        $schedule->delete();
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
