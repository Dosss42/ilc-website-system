<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function getStudentGrades(Request $request)
    {
        $user = Auth::user();

        if (!$user->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $grades = Grade::with(['subject:id,name,code', 'teacher:id,name'])
            ->where('student_id', $user->id)
            ->where('status', 'approved')
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->orderBy('term')
            ->orderBy('subject_id')
            ->get();

        $pendingCount = Grade::where('student_id', $user->id)
            ->where('status', 'submitted')
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->count();

        return response()->json(['success' => true, 'data' => $grades, 'pending_count' => $pendingCount]);
    }

    public function getClassGrades(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $grades = Grade::with(['student:id,name,lrn', 'subject:id,name,code'])
            ->where('teacher_id', $user->id)
            ->where('status', 'submitted')
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->orderBy('student_id')
            ->get();

        return response()->json(['success' => true, 'data' => $grades]);
    }

    public function upsertGrades(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'grades'                => 'required|array',
            'grades.*.student_id'  => 'required|exists:users,id',
            'grades.*.subject_id'  => 'nullable|exists:subjects,id',
            'grades.*.term'        => 'required|integer|min:1|max:3',
            'grades.*.grade'       => 'nullable|numeric|min:0|max:100',
            'grades.*.school_year' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                $gradeValue = isset($gradeData['grade']) && $gradeData['grade'] !== '' ? $gradeData['grade'] : null;

                Grade::updateOrCreate(
                    [
                        'student_id'  => $gradeData['student_id'],
                        'subject_id'  => $gradeData['subject_id'] ?? null,
                        'term'        => $gradeData['term'],
                        'teacher_id'  => $user->id,
                        'school_year' => $gradeData['school_year'] ?? null,
                        'status'      => 'submitted',
                    ],
                    [
                        'grade'   => $gradeValue,
                        'remarks' => Grade::getRemarks($gradeValue),
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Grades saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getTeacherSubjects(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $subjects = \App\Models\TeacherAssignment::where('teacher_id', $user->id)
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year))
            ->with('subject:id,name,code')
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json(['success' => true, 'data' => $subjects]);
    }

    public function getGradeStatistics(Request $request)
    {
        $user = Auth::user();

        $query = Grade::where('status', 'submitted')
            ->when($user->isStudent(), fn($q) => $q->where('student_id', $user->id))
            ->when($user->isTeacher(), fn($q) => $q->where('teacher_id', $user->id))
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->school_year, fn($q) => $q->where('school_year', $request->school_year));

        $total   = (clone $query)->count();
        $passing = (clone $query)->where('grade', '>=', 75)->count();

        $statistics = [
            'total_students' => (clone $query)->distinct('student_id')->count('student_id'),
            'average_grade'  => round((clone $query)->avg('grade') ?? 0, 2),
            'highest_grade'  => (clone $query)->max('grade') ?? 0,
            'lowest_grade'   => (clone $query)->min('grade') ?? 0,
            'passing_rate'   => $total > 0 ? round($passing / $total * 100, 2) : 0,
            'grades_by_term' => (clone $query)->selectRaw('term, AVG(grade) as avg_grade, COUNT(*) as total')
                ->groupBy('term')
                ->get(),
        ];

        return response()->json(['success' => true, 'data' => $statistics]);
    }

    public function deleteGrade($id)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $grade = Grade::where('id', $id)->where('teacher_id', $user->id)->first();

        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
        }

        $grade->delete();
        return response()->json(['success' => true, 'message' => 'Grade deleted successfully!']);
    }
}
