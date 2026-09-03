<?php

namespace App\Http\Controllers;

use App\Models\SummerClass;
use App\Models\SummerClassEnrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummerClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SummerClass::with(['subject:id,name,code', 'teacher:id,name', 'section:id,name'])
            ->withCount('enrollments')
            ->orderByDesc('start_date');

        // Apply filters
        if ($request->has('school_year') && $request->school_year) {
            $query->where('school_year', $request->school_year);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $paginated = $query->paginate(15)->withQueryString();

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'subjects' => $subjects,
            'teachers' => $teachers,
            'pagination' => [
                'currentPage' => $paginated->currentPage(),
                'lastPage' => $paginated->lastPage(),
                'perPage' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'hasPages' => $paginated->hasPages(),
                'hasMorePages' => $paginated->hasMorePages(),
                'onFirstPage' => $paginated->onFirstPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'grade_level' => 'required|string|max:30',
            'room' => 'nullable|string|max:100',
            'schedule_description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_slots' => 'required|integer|min:1|max:100',
            'remarks' => 'nullable|string',
        ]);

        $summerClass = SummerClass::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Summer class created successfully.',
            'data' => $summerClass->load(['subject:id,name,code', 'teacher:id,name']),
        ]);
    }

    public function show(SummerClass $summerClass)
    {
        $summerClass->load([
            'subject:id,name,code',
            'teacher:id,name',
            'section:id,name',
            'enrollments.student:id,name,email,lrn',
        ]);

        return response()->json([
            'success' => true,
            'data' => $summerClass,
        ]);
    }

    public function update(Request $request, SummerClass $summerClass)
    {
        $validated = $request->validate([
            'school_year' => 'sometimes|string|max:20',
            'subject_id' => 'sometimes|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'grade_level' => 'sometimes|string|max:30',
            'room' => 'nullable|string|max:100',
            'schedule_description' => 'nullable|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:upcoming,ongoing,completed,cancelled',
            'max_slots' => 'sometimes|integer|min:1|max:100',
            'remarks' => 'nullable|string',
        ]);

        $summerClass->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Summer class updated successfully.',
            'data' => $summerClass->fresh(['subject:id,name,code', 'teacher:id,name']),
        ]);
    }

    public function destroy(SummerClass $summerClass)
    {
        $summerClass->delete();

        return response()->json([
            'success' => true,
            'message' => 'Summer class deleted successfully.',
        ]);
    }

    /**
     * Enroll a student into a summer class
     */
    public function enrollStudent(Request $request, SummerClass $summerClass)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'original_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($summerClass->available_slots <= 0) {
            return response()->json(['success' => false, 'message' => 'No available slots.'], 422);
        }

        $exists = SummerClassEnrollment::where('summer_class_id', $summerClass->id)
            ->where('student_id', $validated['student_id'])->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Student is already enrolled.'], 422);
        }

        $enrollment = SummerClassEnrollment::create([
            'summer_class_id' => $summerClass->id,
            'student_id' => $validated['student_id'],
            'original_grade' => $validated['original_grade'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student enrolled in summer class.',
            'data' => $enrollment->load('student:id,name,email,lrn'),
        ]);
    }

    /**
     * Remove a student from a summer class
     */
    public function removeStudent(SummerClass $summerClass, $studentId)
    {
        SummerClassEnrollment::where('summer_class_id', $summerClass->id)
            ->where('student_id', $studentId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student removed from summer class.',
        ]);
    }

    /**
     * Update a summer class student's grade
     */
    public function updateGrade(Request $request, SummerClass $summerClass, $studentId)
    {
        $validated = $request->validate([
            'summer_grade' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        $enrollment = SummerClassEnrollment::where('summer_class_id', $summerClass->id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $status = $validated['summer_grade'] >= 75 ? 'passed' : 'failed';

        $enrollment->update([
            'summer_grade' => $validated['summer_grade'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Summer grade updated.',
            'data' => $enrollment,
        ]);
    }

    /**
     * Get failing students eligible for summer class (grade < 75)
     */
    public function getEligibleStudents(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'school_year' => 'required|string',
        ]);

        // Approved grades only — a submitted-but-not-yet-approved or rejected
        // grade isn't official, so it shouldn't make a student eligible here.
        $failingStudents = DB::table('grades')
            ->join('users', 'grades.student_id', '=', 'users.id')
            ->where('grades.subject_id', $request->subject_id)
            ->where('grades.school_year', $request->school_year)
            ->where('grades.status', 'approved')
            ->where('grades.grade', '<', 75)
            ->whereNotNull('grades.grade')
            ->select('users.id', 'users.name', 'users.email', 'users.lrn', 'grades.grade', 'grades.term')
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $failingStudents,
        ]);
    }
}
