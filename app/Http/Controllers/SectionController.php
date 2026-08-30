<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    // DepEd K-6 Curriculum Subjects by Grade Level
    private $curriculumSubjects = [
        'nursery' => [
            'Literacy, Language, and Communication',
            'Socio-Emotional Development',
            'Values Development',
            'Physical Health and Motor Development',
            'Aesthetic/Creative Development',
            'Cognitive Development'
        ],
        'kindergarten' => [
            'Literacy, Language, and Communication',
            'Socio-Emotional Development',
            'Values Development',
            'Physical Health and Motor Development',
            'Aesthetic/Creative Development',
            'Cognitive Development'
        ],
        'grade1' => [
            'Math',
            'GMRC',
            'Language',
            'Reading and Literacy',
            'Makabansa'
        ],
        'grade2' => [
            'English',
            'Filipino',
            'Math',
            'Makabansa',
            'GMRC'
        ],
        'grade3' => [
            'English',
            'Filipino',
            'Math',
            'Science',
            'Makabansa',
            'GMRC'
        ],
        'grade4' => [
            'English',
            'Filipino',
            'Math',
            'Science',
            'EPP',
            'AP',
            'Mapeh',
            'GMRC'
        ],
        'grade5' => [
            'English',
            'Filipino',
            'Math',
            'Science',
            'EPP',
            'AP',
            'Mapeh',
            'GMRC'
        ],
        'grade6' => [
            'English',
            'Filipino',
            'Math',
            'Science',
            'AP',
            'ESP',
            'TLE',
            'Mapeh'
        ]
    ];

    public function index(Request $request)
    {
        $query = Section::with([
            'teacher',
            'students' => fn($q) => $q->whereHas('enrollments', fn($q2) => $q2->where('status', 'enrolled')),
        ]);

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        $sections = $query->get();
        return response()->json(['sections' => $sections]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'teacher_id' => 'nullable|exists:users,id,role,teacher',
            'room_number' => 'nullable|string|max:50',
            'max_students' => 'nullable|integer|min:1|max:200',
            'school_year' => 'required|string|max:20',
            'is_active' => 'boolean'
        ]);

        if (!isset($validated['max_students'])) {
            $validated['max_students'] = 30;
        }

        $section = Section::create($validated);

        Cache::forget('current_school_year');

        // Automatically assign subjects based on DepEd curriculum
        $this->assignCurriculumSubjects($section);

        // If teacher_id provided, create advisory TeacherAssignment
        if (!empty($validated['teacher_id'])) {
            TeacherAssignment::create([
                'teacher_id' => $validated['teacher_id'],
                'section_id' => $section->id,
                'subject_id' => null,
                'is_advisory' => true,
                'school_year' => $validated['school_year'],
            ]);
        }

        return response()->json($section->load(['teacher', 'subjects']), 201);
    }

    /**
     * Automatically assign subjects to section based on DepEd curriculum
     */
    private function assignCurriculumSubjects(Section $section)
    {
        $gradeLevel = $section->grade_level;
        $subjectNames = $this->curriculumSubjects[$gradeLevel] ?? [];

        if (empty($subjectNames)) {
            return;
        }

        // Find subjects by name AND grade_level to ensure correct assignment
        $subjectIds = Subject::where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->where(function($query) use ($subjectNames) {
                foreach ($subjectNames as $name) {
                    $query->orWhere('name', $name);
                }
            })
            ->pluck('id')
            ->toArray();

        // Assign subjects to section
        if (!empty($subjectIds)) {
            $section->subjects()->sync($subjectIds);
        }
    }

    public function show(Section $section)
    {
        try {
            $section->load(['teacher', 'subjects', 'schedules']);
            $section->load(['students' => function($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.lrn', 'users.is_active')
                    ->whereHas('enrollments', fn($q) => $q->where('status', 'enrolled'))
                    ->with(['latestEnrollment' => function($q) {
                        $q->select('enrollments.id', 'enrollments.user_id', 'enrollments.grade_level', 'enrollments.section', 'enrollments.status', 'enrollments.payment_status');
                    }]);
            }]);
            return response()->json($section);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load section: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'teacher_id' => 'nullable|exists:users,id,role,teacher',
            'room_number' => 'nullable|string|max:50',
            'max_students' => 'nullable|integer|min:1|max:200',
            'school_year' => 'required|string|max:20',
            'is_active' => 'boolean'
        ]);

        $oldGradeLevel = $section->grade_level;
        $section->update($validated);

        Cache::forget('current_school_year');

        // Re-assign subjects if grade level changed
        if ($oldGradeLevel !== $section->grade_level) {
            $this->assignCurriculumSubjects($section);
        }

        // Sync TeacherAssignment advisory if teacher_id changed
        if (array_key_exists('teacher_id', $validated)) {
            if (!empty($validated['teacher_id'])) {
                TeacherAssignment::updateOrCreate(
                    [
                        'section_id' => $section->id,
                        'is_advisory' => true,
                    ],
                    [
                        'teacher_id' => $validated['teacher_id'],
                        'subject_id' => null,
                        'school_year' => $section->school_year,
                    ]
                );
            } else {
                // Adviser removed - delete advisory assignment
                TeacherAssignment::where('section_id', $section->id)
                    ->where('is_advisory', true)
                    ->delete();
            }
        }

        return response()->json($section->load(['teacher', 'subjects']));
    }

    public function destroy(Section $section)
    {
        // Remove related teacher assignments before deleting section
        TeacherAssignment::where('section_id', $section->id)->delete();
        $section->delete();
        return response()->json(['success' => true]);
    }

    public function assignTeacher(Request $request, Section $section)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id,role,teacher'
        ]);

        $section->update(['teacher_id' => $validated['teacher_id']]);

        // Sync advisory TeacherAssignment
        TeacherAssignment::updateOrCreate(
            [
                'section_id' => $section->id,
                'is_advisory' => true,
            ],
            [
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => null,
                'school_year' => $section->school_year,
            ]
        );

        return response()->json($section->load('teacher'));
    }

    public function addStudent(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student'
        ]);

        // Check capacity
        $maxStudents = $section->max_students ?? 30;
        if ($section->current_enrollment >= $maxStudents) {
            return response()->json(['error' => "Section {$section->name} is already full ({$maxStudents} students max)."], 422);
        }

        // Check if student is already in this section
        if ($section->students()->where('user_id', $validated['student_id'])->exists()) {
            return response()->json(['error' => 'Student is already in this section'], 400);
        }

        // Check if student is already in another section for the same grade level
        $existingSection = Section::whereHas('students', function($q) use ($validated) {
            $q->where('user_id', $validated['student_id']);
        })->where('grade_level', $section->grade_level)->first();

        if ($existingSection) {
            return response()->json(['error' => 'Student is already assigned to section ' . $existingSection->name], 400);
        }

        $section->students()->attach($validated['student_id']);
        $section->current_enrollment = $section->students()->count();
        $section->save();

        // Also update the enrollment's section field and status
        $user = User::find($validated['student_id']);
        if ($user) {
            $enrollment = $user->latestEnrollment;
            if ($enrollment) {
                $enrollment->section = $section->name;
                // If enrollment is still 'approved', upgrade to 'enrolled'
                if ($enrollment->status === 'approved') {
                    $enrollment->status = 'enrolled';
                    $enrollment->enrolled_at = now();
                }
                $enrollment->save();
            }
        }

        // Return the updated section with current enrollment count
        $section->refresh();
        return response()->json([
            'success' => true,
            'current_enrollment' => $section->current_enrollment,
            'students' => $section->students
        ]);
    }

    public function removeStudent(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student'
        ]);

        $section->students()->detach($validated['student_id']);
        $section->current_enrollment = $section->students()->count();
        $section->save();

        // Clear the enrollment section field
        $user = User::find($validated['student_id']);
        if ($user && $user->latestEnrollment) {
            $enrollment = $user->latestEnrollment;
            $enrollment->section = null;
            $enrollment->save();
        }

        return response()->json(['success' => true, 'current_enrollment' => $section->current_enrollment]);
    }

    public function transferStudent(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_id'        => 'required|exists:users,id,role,student',
            'target_section_id' => 'required|exists:sections,id',
        ]);

        if ((int) $validated['target_section_id'] === $section->id) {
            return response()->json(['error' => 'Student is already in this section.'], 422);
        }

        $target = Section::findOrFail($validated['target_section_id']);

        $max = $target->max_students ?? 30;
        if ($target->current_enrollment >= $max) {
            return response()->json(['error' => "Section {$target->name} is already full ({$max} students max)."], 422);
        }

        // Remove from current section
        $section->students()->detach($validated['student_id']);
        $section->current_enrollment = $section->students()->count();
        $section->save();

        // Add to target section
        $target->students()->syncWithoutDetaching([$validated['student_id']]);
        $target->current_enrollment = $target->students()->count();
        $target->save();

        // Update enrollment record
        $user = User::find($validated['student_id']);
        if ($user && $user->latestEnrollment) {
            $enrollment = $user->latestEnrollment;
            $enrollment->section = $target->name;
            $enrollment->save();
        }

        return response()->json([
            'success'            => true,
            'new_section'        => $target->name,
            'current_enrollment' => $target->current_enrollment,
        ]);
    }

    public function autoAssign(Request $request)
    {
        $gradeLevel = $request->input('grade_level');

        // All active sections (optionally filtered by grade)
        $sections = Section::where('is_active', true)
            ->when($gradeLevel, fn($q) => $q->where('grade_level', $gradeLevel))
            ->get();

        // Student IDs already in any section
        $assignedIds = DB::table('section_student')->pluck('user_id')->toArray();

        // Unassigned students with an active enrollment
        $students = User::where('role', 'student')
            ->whereNotIn('id', $assignedIds)
            ->with('latestEnrollment')
            ->get()
            ->filter(fn($u) =>
                $u->latestEnrollment
                && in_array($u->latestEnrollment->status, ['approved', 'enrolled'])
                && $u->latestEnrollment->grade_level
            );

        if ($gradeLevel) {
            $students = $students->filter(fn($u) => $u->latestEnrollment->grade_level === $gradeLevel);
        }

        $assigned = 0;
        $skipped  = 0;

        foreach ($students as $student) {
            $grade = $student->latestEnrollment->grade_level;

            // First section with available space for this grade
            $target = $sections
                ->where('grade_level', $grade)
                ->first(fn($s) => $s->current_enrollment < ($s->max_students ?? 30));

            if (!$target) { $skipped++; continue; }

            $target->students()->syncWithoutDetaching([$student->id]);
            $target->current_enrollment = $target->students()->count();
            $target->save();

            // Refresh in collection so next iteration sees updated count
            $sections = $sections->map(fn($s) => $s->id === $target->id ? $target : $s);

            $enrollment = $student->latestEnrollment;
            $enrollment->section = $target->name;
            $enrollment->save();

            $assigned++;
        }

        return response()->json([
            'success'  => true,
            'assigned' => $assigned,
            'skipped'  => $skipped,
            'message'  => "{$assigned} student(s) auto-assigned. {$skipped} skipped (no available section).",
        ]);
    }

    public function assignSubjects(Request $request, Section $section)
    {
        // Accept both formats: { subjects: [{id: 1}, {id: 2}] } and { subject_ids: [1, 2] }
        if ($request->has('subject_ids')) {
            $subjectIds = $request->input('subject_ids');
            if (!is_array($subjectIds)) {
                $subjectIds = [];
            }
        } else {
            $validated = $request->validate([
                'subjects' => 'required|array',
                'subjects.*.id' => 'required|exists:subjects,id'
            ]);
            $subjectIds = collect($validated['subjects'])->pluck('id')->toArray();
        }

        $section->subjects()->sync($subjectIds);

        return response()->json([
            'success' => true,
            'subjects' => $section->subjects
        ]);
    }

    public function getSubjects(Section $section)
    {
        return response()->json($section->subjects);
    }
}
