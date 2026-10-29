<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Services\ScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Schedule Management › Subject Teachers and › Generate Schedule (Admin and Super Admin share these).
 * The rules live in App\Services\ScheduleGenerator.
 */
class ScheduleGeneratorController extends Controller
{
    private const GRADES = 'nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6';

    public function teacherGrid(Request $request)
    {
        $v = $request->validate([
            'school_year' => 'required|string|max:20',
            'grade'       => 'required|in:' . self::GRADES,
        ]);
        return response()->json(ScheduleGenerator::teacherGrid($v['school_year'], $v['grade']));
    }

    public function saveTeacherGrid(Request $request)
    {
        $v = $request->validate([
            'school_year'        => 'required|string|max:20',
            'cells'              => 'required|array',
            'cells.*.section_id' => 'required|integer|exists:sections,id',
            'cells.*.subject_id' => 'required|integer|exists:subjects,id',
            'cells.*.teacher_id' => 'nullable|integer|exists:users,id',
        ]);
        $result = ScheduleGenerator::saveTeacherGrid($v['school_year'], $v['cells']);
        return response()->json(['success' => true] + $result + [
            'message' => $result['changed']
                ? "{$result['changed']} subject teacher(s) saved" . ($result['slots_moved'] ? ", {$result['slots_moved']} timetable slot(s) moved to the new teacher." : '.')
                : 'No changes.',
        ]);
    }

    /**
     * Defaults for the generator window: the grade's usual day (Nursery/Kinder 7:00–10:20, Grades 1–6
     * 7:00–2:00) and the subjects its sections actually have (Section Management › Subjects), the day's
     * periods shared evenly among them. Other active subjects of the grade are only named, not scheduled.
     */
    public function defaults(Request $request)
    {
        $v = $request->validate(['grade' => 'required|in:' . self::GRADES, 'school_year' => 'required|string|max:20']);
        $sections = Section::where('school_year', $v['school_year'])->where('grade_level', $v['grade'])->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $taught = DB::table('section_subject')->whereIn('section_id', $sections->pluck('id'))->pluck('subject_id')->unique();
        // How many of the grade's sections have a teacher for each subject (Subject Teachers)
        $covered = \App\Models\TeacherAssignment::where('school_year', $v['school_year'])->whereIn('section_id', $sections->pluck('id'))
            ->whereNotNull('subject_id')->get()->groupBy('subject_id')->map(fn ($g) => $g->pluck('section_id')->unique()->count());
        // Terms each section is already scheduled for — those are skipped unless "Replace existing"
        $scheduled = \App\Models\Schedule::whereIn('section_id', $sections->pluck('id'))->get(['section_id', 'term'])
            ->groupBy('section_id')->map(fn ($g) => $g->pluck('term')->unique()->sort()->values());

        $day = ScheduleGenerator::gradeDay($v['grade']);
        [$used, $other] = ScheduleGenerator::gradeSubjects($v['grade'])->partition(fn ($s) => $taught->isEmpty() || $taught->contains($s->id));
        $perDay = ScheduleGenerator::periodsBefore($day['start_time'], $day['period_minutes'], $day['breaks'], $day['end_time']);
        $perWeek = ScheduleGenerator::spread($perDay * count($day['days']), $used->count());

        return response()->json($day + [
            'subjects' => $used->values()->map(fn ($s, $i) => ['id' => $s->id, 'name' => $s->name, 'per_week' => $perWeek[$i],
                                                              'teachers' => $covered[$s->id] ?? 0]),
            'other_subjects' => $other->pluck('name')->values(),
            'sections' => $sections->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'scheduled_terms' => $scheduled[$s->id] ?? []])->values(),
        ]);
    }

    public function preview(Request $request)
    {
        return response()->json(ScheduleGenerator::build($this->generatorInput($request)));
    }

    public function save(Request $request)
    {
        $result = ScheduleGenerator::save($this->generatorInput($request));
        return response()->json(['success' => true] + $result + [
            'message' => "Created {$result['created']} schedule slot(s) for {$result['sections']} section(s)"
                . ($result['skipped'] ? ", {$result['skipped']} skipped (already scheduled)" : '')
                . ($result['warnings'] ? ". {$result['warnings']} warning(s) to review in Schedule Management." : '.'),
        ]);
    }

    private function generatorInput(Request $request): array
    {
        return $request->validate([
            'school_year'         => 'required|string|max:20',
            'grade'               => 'required|in:' . self::GRADES,
            'section_ids'         => 'nullable|array',
            'section_ids.*'       => 'integer',
            'terms'             => 'required|array|min:1',
            'terms.*'             => 'integer|min:1|max:3',
            'days'                => 'required|array|min:1',
            'days.*'              => 'in:' . implode(',', ScheduleGenerator::DAYS),
            'start_time'          => 'required|date_format:H:i',
            'end_time'            => 'nullable|date_format:H:i|after:start_time',
            'period_minutes'      => 'required|integer|min:20|max:120',
            'breaks'              => 'array',
            'breaks.*.label'      => 'nullable|string|max:30',
            'breaks.*.start'      => 'required|date_format:H:i',
            'breaks.*.end'        => 'required|date_format:H:i|after:breaks.*.start',
            'subjects'            => 'required|array|min:1',
            'subjects.*.id'       => 'required|integer|exists:subjects,id',
            'subjects.*.per_week' => 'required|integer|min:0|max:15',
            'limits.week_hours'   => 'nullable|integer|min:1|max:60',
            'limits.day_periods'  => 'nullable|integer|min:1|max:12',
            'limits.consecutive'  => 'nullable|integer|min:1|max:12',
            'replace'             => 'boolean',
        ]);
    }
}
