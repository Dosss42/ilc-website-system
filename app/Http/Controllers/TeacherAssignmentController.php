<?php

namespace App\Http\Controllers;

use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $schoolYear = $request->get('school_year', $this->getCurrentSchoolYear());
        $sort = $request->get('sort', 'newest'); // default: newest first

        $query = TeacherAssignment::with(['teacher', 'subject', 'section'])
            ->forSchoolYear($schoolYear);

        // Apply sorting
        switch ($sort) {
            case 'teacher_asc':
                $query->join('users', 'users.id', '=', 'teacher_assignments.teacher_id')
                    ->orderBy('users.name', 'asc')
                    ->select('teacher_assignments.*');
                break;
            case 'teacher_desc':
                $query->join('users', 'users.id', '=', 'teacher_assignments.teacher_id')
                    ->orderBy('users.name', 'desc')
                    ->select('teacher_assignments.*');
                break;
            case 'subject_asc':
                $query->leftJoin('subjects', 'subjects.id', '=', 'teacher_assignments.subject_id')
                    ->orderBy('subjects.name', 'asc')
                    ->select('teacher_assignments.*');
                break;
            case 'subject_desc':
                $query->leftJoin('subjects', 'subjects.id', '=', 'teacher_assignments.subject_id')
                    ->orderBy('subjects.name', 'desc')
                    ->select('teacher_assignments.*');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $assignments = $query->paginate(15)->withQueryString();

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'assignments' => $assignments
            ]);
        }

        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $sections = Section::where('school_year', $schoolYear)->where('is_active', true)->orderBy('grade_level')->orderBy('name')->get();

        $schoolYears = $this->getSchoolYearRange();

        return view('adminDashboard', compact('assignments', 'teachers', 'subjects', 'sections', 'schoolYear', 'schoolYears', 'sort'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'is_advisory' => 'boolean',
            'school_year' => 'required|string',
        ]);

        // Check for duplicate (handle null subject_id)
        $dupQuery = TeacherAssignment::where('teacher_id', $validated['teacher_id'])
            ->where('section_id', $validated['section_id'])
            ->where('school_year', $validated['school_year']);
        if (!empty($validated['subject_id'])) {
            $dupQuery->where('subject_id', $validated['subject_id']);
        } else {
            $dupQuery->whereNull('subject_id');
        }
        $exists = $dupQuery->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'This assignment already exists.'], 422);
        }

        $assignment = TeacherAssignment::create($validated);

        // Sync section.teacher_id to the first advisory teacher if any
        if (!empty($validated['is_advisory'])) {
            $firstAdvisory = TeacherAssignment::where('section_id', $validated['section_id'])
                ->where('school_year', $validated['school_year'])
                ->where('is_advisory', true)
                ->orderBy('id')
                ->first();
            if ($firstAdvisory) {
                Section::where('id', $validated['section_id'])->update(['teacher_id' => $firstAdvisory->teacher_id]);
            }
        }

        $assignment->load(['teacher', 'subject', 'section']);

        return response()->json(['success' => true, 'assignment' => $assignment]);
    }

    public function update(Request $request, $id)
    {
        $assignment = TeacherAssignment::findOrFail($id);

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'is_advisory' => 'boolean',
            'school_year' => 'required|string',
        ]);

        // Check for duplicate (excluding current, handle null subject_id)
        $dupQuery = TeacherAssignment::where('teacher_id', $validated['teacher_id'])
            ->where('section_id', $validated['section_id'])
            ->where('school_year', $validated['school_year'])
            ->where('id', '!=', $id);
        if (!empty($validated['subject_id'])) {
            $dupQuery->where('subject_id', $validated['subject_id']);
        } else {
            $dupQuery->whereNull('subject_id');
        }
        $exists = $dupQuery->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'This assignment already exists.'], 422);
        }

        // Track old section for advisory sync
        $oldSectionId = $assignment->section_id;
        $wasAdvisory = $assignment->is_advisory;

        $assignment->update($validated);

        // Sync section.teacher_id to the first advisory teacher
        $sectionIds = array_unique(array_filter([$oldSectionId, $validated['section_id']]));
        foreach ($sectionIds as $sid) {
            $firstAdvisory = TeacherAssignment::where('section_id', $sid)
                ->where('school_year', $validated['school_year'])
                ->where('is_advisory', true)
                ->orderBy('id')
                ->first();
            Section::where('id', $sid)->update(['teacher_id' => $firstAdvisory?->teacher_id]);
        }

        $assignment->load(['teacher', 'subject', 'section']);

        return response()->json(['success' => true, 'assignment' => $assignment]);
    }

    public function destroy($id)
    {
        $assignment = TeacherAssignment::findOrFail($id);

        // Re-sync section.teacher_id after removing this advisory
        if ($assignment->is_advisory) {
            $firstAdvisory = TeacherAssignment::where('section_id', $assignment->section_id)
                ->where('school_year', $assignment->school_year)
                ->where('is_advisory', true)
                ->where('id', '!=', $id)
                ->orderBy('id')
                ->first();
            Section::where('id', $assignment->section_id)->update(['teacher_id' => $firstAdvisory?->teacher_id]);
        }

        $assignment->delete();

        return response()->json(['success' => true]);
    }

    public function getAssignments(Request $request)
    {
        $schoolYear = $request->input('school_year', $this->getCurrentSchoolYear());
        $sort = $request->input('sort', 'newest');

        $query = TeacherAssignment::with(['teacher', 'subject', 'section.students'])
            ->forSchoolYear($schoolYear);

        // Apply sorting
        switch ($sort) {
            case 'teacher_asc':
                $query->join('users', 'users.id', '=', 'teacher_assignments.teacher_id')
                    ->orderBy('users.name', 'asc')
                    ->select('teacher_assignments.*');
                break;
            case 'teacher_desc':
                $query->join('users', 'users.id', '=', 'teacher_assignments.teacher_id')
                    ->orderBy('users.name', 'desc')
                    ->select('teacher_assignments.*');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $paginated = $query->paginate(15)->withQueryString();

        $assignments = $paginated->map(function ($a) {
            return [
                'id' => $a->id,
                'teacher_id' => $a->teacher_id,
                'teacher_name' => $a->teacher->name,
                'subject_id' => $a->subject_id,
                'subject_name' => $a->subject ? $a->subject->name : null,
                'section_id' => $a->section_id,
                'section_name' => $a->section->name,
                'grade_level' => $a->section->grade_level,
                'student_count' => $a->section ? $a->section->students->count() : 0,
                'is_advisory' => $a->is_advisory,
                'school_year' => $a->school_year,
            ];
        });

        // Build paginator-compatible response structure
        return response()->json([
            'success' => true,
            'assignments' => [
                'data' => $assignments,
                'currentPage' => $paginated->currentPage(),
                'lastPage' => $paginated->lastPage(),
                'perPage' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'hasPages' => $paginated->hasPages(),
                'hasMorePages' => $paginated->hasMorePages(),
                'onFirstPage' => $paginated->onFirstPage(),
                'path' => $paginated->path(),
                'links' => $paginated->toArray()['links'] ?? []
            ]
        ]);
    }

    private function getCurrentSchoolYear(): string
    {
        // Get the most recent active school year from sections
        $latestSchoolYear = Section::where('is_active', true)
            ->orderByDesc('school_year')
            ->value('school_year');

        if ($latestSchoolYear) {
            return $latestSchoolYear;
        }

        // Fallback: compute from current date
        $now = now();
        $year = $now->month >= 6 ? $now->year : $now->year - 1;
        return $year . '-' . ($year + 1);
    }

    private function getSchoolYearRange(): array
    {
        $configured = \App\Models\Setting::where('key', 'current_school_year')->value('value') ?? '2026-2027';
        $base = (int) substr($configured, 0, 4);
        $years = [];
        for ($y = $base; $y <= $base + 10; $y++) {
            $years[] = $y . '-' . ($y + 1);
        }
        return $years;
    }
}
