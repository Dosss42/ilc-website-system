<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Get student grades — all subjects for the grade level, with submitted grades
     */
    public function getGrades(Request $request)
    {
        $user        = Auth::user();
        $term        = (int) $request->query('quarter', 1); // 'quarter' kept for backward-compat
        $schoolYear  = $request->query('school_year'); // override from filter

        // If school_year requested differs from latest enrollment, find that specific enrollment
        $enrollment = $schoolYear
            ? $user->enrollments()->where('school_year', $schoolYear)->latest('id')->first()
              ?? $user->latestEnrollment
            : $user->latestEnrollment;

        $gradeLevel  = $enrollment ? ($enrollment->student_data['grade_level'] ?? $enrollment->grade_level) : null;
        $schoolYear  = $schoolYear ?: ($enrollment ? $enrollment->school_year : null);
        $sectionName = $enrollment ? $enrollment->section : null;

        // 1. All active subjects for this grade level
        $allSubjects = Subject::where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        // 2. Submitted grades for this student/term/school year.
        //    Include records with NULL school_year (migrated from old system before
        //    school_year column existed). Year-specific records take priority via ordering —
        //    keyBy keeps the last item per subject_id, so NULL records must come first.
        $grades = Grade::where('student_id', $user->id)
            ->where('term', $term)
            ->whereIn('status', ['submitted', 'approved'])
            ->when($schoolYear, fn($q) => $q->where(function ($q2) use ($schoolYear) {
                $q2->where('school_year', $schoolYear)->orWhereNull('school_year');
            }))
            ->orderByRaw('(school_year IS NULL) DESC') // NULL rows first → keyBy replaces them with year-specific rows
            ->orderByRaw("FIELD(status,'approved','submitted') ASC") // approved takes priority
            ->get()
            ->keyBy('subject_id');

        // 3. Teacher assignments for this section keyed by subject_id
        $sectionModel = Section::where('name', $sectionName)
            ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear))
            ->first();

        $teacherMap = [];
        if ($sectionModel) {
            TeacherAssignment::where('section_id', $sectionModel->id)
                ->whereNotNull('subject_id')
                ->with('teacher:id,name')
                ->get()
                ->each(function ($a) use (&$teacherMap) {
                    if ($a->teacher) {
                        $teacherMap[$a->subject_id] = $a->teacher->name;
                    }
                });
        }

        $isDescriptive = \App\Models\Grade::isNurseryKinder($gradeLevel ?? '');

        // Build teacher map from Schedule (primary) and fallback to TeacherAssignment
        if ($sectionModel) {
            \App\Models\Schedule::where('section_id', $sectionModel->id)
                ->whereNotNull('subject_id')
                ->where('is_active', true)
                ->with('teacher:id,name')
                ->get()
                ->each(function ($s) use (&$teacherMap) {
                    if ($s->teacher && !isset($teacherMap[$s->subject_id])) {
                        $teacherMap[$s->subject_id] = $s->teacher->name;
                    }
                });

            // Nursery/Kinder: advisory teacher handles all subjects.
            // Fill any subject still missing a teacher from the advisory assignment.
            if ($isDescriptive && empty(array_filter($teacherMap))) {
                $advisory = \App\Models\TeacherAssignment::where('section_id', $sectionModel->id)
                    ->where('is_advisory', true)
                    ->with('teacher:id,name')
                    ->first();
                if ($advisory?->teacher) {
                    foreach ($allSubjects as $sub) {
                        if (!isset($teacherMap[$sub->id])) {
                            $teacherMap[$sub->id] = $advisory->teacher->name;
                        }
                    }
                }
            }
        }

        // 4. Merge: show all subjects, blank grade if not yet encoded
        $subjects = $allSubjects->map(function ($sub) use ($grades, $teacherMap, $isDescriptive) {
            $gradeRecord      = $grades->get($sub->id);
            $gradeValue       = $gradeRecord?->grade;
            $descriptiveValue = $gradeRecord?->descriptive_grade;

            if ($isDescriptive) {
                return [
                    'code'             => $sub->code ?? '—',
                    'name'             => $sub->name,
                    'teacher'          => $teacherMap[$sub->id] ?? '—',
                    'final_grade'      => null,
                    'descriptive_grade'=> $descriptiveValue,
                    'remarks'          => $descriptiveValue
                        ? \App\Models\Grade::getDescriptiveLabel($descriptiveValue)
                        : '',
                ];
            }

            return [
                'code'             => $sub->code ?? '—',
                'name'             => $sub->name,
                'teacher'          => $teacherMap[$sub->id] ?? '—',
                'final_grade'      => $gradeValue,
                'descriptive_grade'=> null,
                'remarks'          => $gradeValue !== null ? Grade::getRemarks($gradeValue) : '',
            ];
        });

        $pendingCount = Grade::where('student_id', $user->id)
            ->where('term', $term)
            ->where('status', 'submitted')
            ->when($schoolYear, fn($q) => $q->where(function ($q2) use ($schoolYear) {
                $q2->where('school_year', $schoolYear)->orWhereNull('school_year');
            }))
            ->count();

        return response()->json([
            'success'       => true,
            'pending_count' => $pendingCount,
            'data'          => [
                'term'          => $term,
                'grade_level'   => $gradeLevel,
                'school_year'   => $schoolYear,
                'is_descriptive'=> $isDescriptive,
                'has_grades'    => $grades->isNotEmpty(),
                'subjects'      => $subjects,
            ],
        ]);
    }

    /**
     * Get student schedule — supports school_year and term filters
     */
    public function getSchedule(Request $request)
    {
        $user       = Auth::user();
        $schoolYear = $request->query('school_year');
        $term       = (int) $request->query('term', 1);

        // Pick enrollment for the requested school year, fall back to latest
        $enrollment = $schoolYear
            ? ($user->enrollments()->where('school_year', $schoolYear)->latest('id')->first()
               ?? $user->latestEnrollment)
            : $user->latestEnrollment;

        $gradeLevel  = $enrollment ? ($enrollment->student_data['grade_level'] ?? $enrollment->grade_level) : null;
        $sectionName = $enrollment ? $enrollment->section : null;
        $currentSY   = $enrollment ? $enrollment->school_year : null;

        $glMap = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1',
                  'grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4',
                  'grade5'=>'Grade 5','grade6'=>'Grade 6'];

        // Find section, scoped to the enrollment's school year when available
        $section = null;
        if ($sectionName) {
            $section = Section::where('name', $sectionName)
                ->where('is_active', true)
                ->when($currentSY, fn($q) => $q->where('school_year', $currentSY))
                ->first();
        }
        if (!$section) {
            $section = Section::whereHas('students', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_active', true)->first();
        }

        $schedules     = [];
        $totalSubjects = 0;

        if ($section) {
            $rawSchedules = Schedule::where('section_id', $section->id)
                ->where('is_active', true)
                ->where('term', $term)
                ->with(['subject:id,name,code', 'teacher:id,name'])
                ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->orderBy('start_time')
                ->get();

            $totalSubjects = $rawSchedules->count();
            $grouped       = $rawSchedules->groupBy('day_of_week');
            $dayOrder      = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

            foreach ($dayOrder as $day) {
                if (!isset($grouped[$day])) continue;
                $classes = $grouped[$day]->map(fn($s) => [
                    'start_time'   => (string) $s->start_time,
                    'end_time'     => (string) $s->end_time,
                    'subject_name' => $s->subject->name ?? '—',
                    'subject_code' => $s->subject->code ?? '',
                    'room'         => $s->room ?? '',
                    'teacher'      => $s->teacher->name ?? '—',
                ])->values()->toArray();

                $schedules[] = ['day' => $day, 'classes' => $classes];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'grade_level'    => $gradeLevel,
                'grade_label'    => $glMap[$gradeLevel] ?? ucfirst($gradeLevel ?? ''),
                'section'        => $section ? $section->name : ($sectionName ?: 'Not yet assigned'),
                'school_year'    => $currentSY,
                'term'           => $term,
                'total_subjects' => $totalSubjects,
                'schedule'       => $schedules,
            ],
        ]);
    }

    /**
     * Get student announcements
     */
    public function getAnnouncements()
    {
        $user = Auth::user();

        $enrollment = $user->latestEnrollment;
        $section = null;

        if ($enrollment && $enrollment->section) {
            $section = Section::where('name', $enrollment->section)
                ->where('is_active', true)->first();
        }

        if (!$section) {
            $section = Section::whereHas('students', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_active', true)->first();
        }

        // Get announcements for all, or for student's section
        $announcements = Announcement::where('is_active', true)
            ->where(function ($q) use ($section) {
                $q->where('audience', 'all')
                  ->orWhere('audience', 'parents')
                  ->when($section, fn($q2) => $q2->orWhere(function ($q3) use ($section) {
                      $q3->where('audience', 'section')->where('section_id', $section->id);
                  }));
            })
            ->with('teacher:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'content' => $a->content,
                    'type' => ucfirst($a->category),
                    'date' => $a->created_at->format('M d, Y'),
                    'author' => $a->teacher->name ?? 'Admin',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    public function getPortalData()
    {
        $user = Auth::user();
        $enrollment = $user->latestEnrollment;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'name', 'email']),
                'enrollment' => $enrollment ? $enrollment->only(['id', 'grade_level', 'section', 'status', 'payment_status']) : null,
            ]
        ]);
    }

    public function getDashboardStats()
    {
        return response()->json(['success' => true, 'data' => []]);
    }
}
