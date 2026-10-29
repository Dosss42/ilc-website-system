<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\StudentDocument;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    use \App\Support\Traits\BuildsSchoolPdf;

    /** Documents every student must have approved (same list as the promotion check). */
    private const REQUIRED_DOCS = ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture'];

    private const GRADE_LABELS = [
        'nursery' => 'Nursery', 'kindergarten' => 'Kindergarten',
        'grade1' => 'Grade 1', 'grade2' => 'Grade 2', 'grade3' => 'Grade 3',
        'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6',
    ];

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

        // Optional School Year (?school_year=2026-2027 | all) — Super Admin's Section Management
        if ($request->filled('school_year') && $request->school_year !== 'all') {
            $query->where('school_year', $request->school_year);
        }
        // Super Admin's Section Management, same as Admin's: Active/Inactive filter, and
        // (?with_counts=1) the number of schedules and subjects per section
        if (in_array($request->input('status'), ['active', 'inactive'], true)) {
            $query->where('is_active', $request->input('status') === 'active');
        }
        if ($request->boolean('with_counts')) {
            $query->withCount(['schedules', 'subjects']);
        }

        // Pagination is opt-in (?paginated=1) — other callers (dropdown
        // population elsewhere, the dedicated Section Management page) need
        // the full list, so the default response stays unpaginated.
        if ($request->boolean('paginated')) {
            $statsBase = fn () => Section::query()->when(
                $request->filled('school_year') && $request->school_year !== 'all',
                fn ($q) => $q->where('school_year', $request->school_year)
            );
            $page = $query->paginate((int) $request->input('per_page', 15));
            return response()->json([
                'sections'     => $page->items(),
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'from'         => $page->firstItem(),
                'to'           => $page->lastItem(),
                // Admin's cards: sections and enrolled students of the chosen school year
                'stats'        => $request->boolean('with_counts') ? [
                    'total'    => $statsBase()->count(),
                    'active'   => $statsBase()->where('is_active', true)->count(),
                    'enrolled' => DB::table('section_student')->whereIn('section_id', $statsBase()->select('id'))->count(),
                ] : null,
            ]);
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
        $validated['name'] = \App\Support\NameCase::words($validated['name']); // "rizal b" → "Rizal B"

        if (!isset($validated['max_students'])) {
            $validated['max_students'] = 30;
        }

        $section = Section::create($validated);

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

        ActivityLogger::log('create', "Created section \"{$section->name}\" — {$section->grade_level} (S.Y. {$section->school_year})", 'Section', $section->id);

        // Students of this grade and year who are approved/enrolled but have no section yet (e.g. they
        // paid before any section existed) — the page offers to place them into the new section
        return response()->json(array_merge($section->load(['teacher', 'subjects'])->toArray(), [
            'waiting_count' => self::studentsWithoutSection($section->grade_level, $section->school_year)->count(),
        ]), 201);
    }

    /**
     * Approved/enrolled students of a grade and school year who aren't in any section OF THAT YEAR.
     * Last year's section doesn't count — the old checks treated "ever in any section" as placed, so a
     * returning student could never be auto-assigned. Used by store(), autoAssign() and Student Management.
     */
    public static function studentsWithoutSection(?string $gradeLevel, ?string $schoolYear)
    {
        return \App\Models\Enrollment::query()
            ->whereIn('status', ['approved', 'enrolled'])
            ->when($gradeLevel, fn ($q) => $q->where('grade_level', $gradeLevel))
            ->when($schoolYear, fn ($q) => $q->where('school_year', $schoolYear))
            ->whereHas('user', fn ($q) => $q->where('role', 'student'))
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))
                ->from('section_student')
                ->join('sections', 'sections.id', '=', 'section_student.section_id')
                ->whereColumn('section_student.user_id', 'enrollments.user_id')
                ->whereColumn('sections.school_year', 'enrollments.school_year'));
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

    /**
     * Section Summary: everyone in the section at a glance — boys/girls, student type,
     * payments and required documents — with the roster behind the numbers. The same
     * students as View Students; each one is counted from their enrollment for the
     * section's school year.
     */
    public function summary(Section $section)
    {
        return response()->json($this->buildSummary($section));
    }

    public function summaryPdf(Section $section)
    {
        $s = $this->buildSummary($section);

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Section Summary',
            'dateRangeLabel' => $s['section']['grade_label'] . ' — ' . $s['section']['name'] . ' · S.Y. ' . $s['section']['school_year'],
            'summary'        => $s,
        ]);

        $file = 'section-summary-' . \Illuminate\Support\Str::slug($s['section']['grade_label'] . ' ' . $s['section']['name'])
            . '-' . $s['section']['school_year'] . '.pdf';

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.section-summary', $pdfData),
            $pdfData
        )->download($file);
    }

    private function buildSummary(Section $section): array
    {
        $sy = $section->school_year;

        $students = $section->students()
            ->select('users.id', 'users.name', 'users.lrn')
            ->whereHas('enrollments', fn ($q) => $q->where('status', 'enrolled'))
            ->with([
                'profile:id,user_id,gender',
                'enrollments' => fn ($q) => $q->where('status', '!=', 'cancelled')->orderByDesc('id'),
            ])
            ->orderBy('users.name')
            ->get();

        $docsByStudent = StudentDocument::whereIn('user_id', $students->pluck('id'))
            ->whereIn('document_type', self::REQUIRED_DOCS)
            ->where('status', 'approved')
            ->get(['user_id', 'document_type'])
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->pluck('document_type')->unique()->count());

        $rows = $students->map(function ($student) use ($sy, $docsByStudent) {
            // This school year's enrollment (enrolled first); older data may lack a school year
            $forYear = $student->enrollments->where('school_year', $sy);
            $enrollment = $forYear->firstWhere('status', 'enrolled') ?? $forYear->first()
                ?? $student->enrollments->firstWhere('status', 'enrolled');
            $data = $enrollment?->student_data ?? [];

            $gender = strtolower((string) ($student->profile?->gender ?: ($data['gender'] ?? '')));
            $type   = strtolower((string) ($data['student_type'] ?? ''));
            $fee    = (float) ($enrollment?->total_fee ?? 0);
            $paid   = (float) ($enrollment?->payment_amount ?? 0);
            $due    = $enrollment ? PaymentService::balanceDue($enrollment) : null;
            $docs   = (int) ($docsByStudent[$student->id] ?? 0);

            return [
                'id'            => $student->id,
                'name'          => $student->name,
                'lrn'           => $student->lrn,
                'gender'        => in_array($gender, ['male', 'female'], true) ? $gender : null,
                'student_type'  => in_array($type, ['new', 'returning', 'transferee'], true) ? $type : null,
                'payment_plan'  => $enrollment?->payment_option,
                'total_fee'     => round($fee, 2),
                'amount_paid'   => round($paid, 2),
                'balance'       => $due,
                // From the amounts, not the stored payment_status
                'payment'       => ($due !== null && $due <= 0) ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'docs_approved' => $docs,
                'docs_complete' => $docs >= count(self::REQUIRED_DOCS),
            ];
        })->values();

        $count = fn (string $key, $value) => $rows->where($key, $value)->count();
        $adviser = $section->advisory_teacher ?? $section->teacher;

        return [
            'section' => [
                'id'          => $section->id,
                'name'        => $section->name,
                'grade_level' => $section->grade_level,
                'grade_label' => self::GRADE_LABELS[$section->grade_level] ?? $section->grade_level,
                'school_year' => $sy,
                'room'        => $section->room_number,
                'adviser'     => $adviser?->name,
                'max_students'=> (int) ($section->max_students ?? 30),
                'is_active'   => (bool) $section->is_active,
            ],
            'totals' => [
                'students'        => $rows->count(),
                'male'            => $count('gender', 'male'),
                'female'          => $count('gender', 'female'),
                'gender_unknown'  => $count('gender', null),
                'new'             => $count('student_type', 'new'),
                'returning'       => $count('student_type', 'returning'),
                'transferee'      => $count('student_type', 'transferee'),
                'type_unknown'    => $count('student_type', null),
                'paid'            => $count('payment', 'paid'),
                'partial'         => $count('payment', 'partial'),
                'unpaid'          => $count('payment', 'unpaid'),
                'total_fees'      => round($rows->sum('total_fee'), 2),
                'total_collected' => round($rows->sum('amount_paid'), 2),
                'total_balance'   => round($rows->sum(fn ($r) => (float) ($r['balance'] ?? 0)), 2),
                'docs_complete'   => $count('docs_complete', true),
                'docs_incomplete' => $count('docs_complete', false),
            ],
            'required_docs' => count(self::REQUIRED_DOCS),
            'students'      => $rows,
        ];
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
        $validated['name'] = \App\Support\NameCase::words($validated['name']);

        $oldGradeLevel  = $section->grade_level;
        $oldName        = $section->name;
        $oldSchoolYear  = $section->school_year;
        $section->update($validated);

        // Renamed: enrollments keep the section as plain text, so move this section's own students
        // to the new name — otherwise they would drop out of its class lists, SF forms and grade sheets.
        if ($oldName !== $section->name) {
            $moved = \App\Models\Enrollment::where('school_year', $oldSchoolYear)
                ->where('section', $oldName)
                ->whereIn('user_id', $section->students()->pluck('users.id'))
                ->update(['section' => $section->name]);
            if ($moved) {
                ActivityLogger::log('update', "Section renamed \"{$oldName}\" → \"{$section->name}\": {$moved} enrollment(s) updated", 'Section', $section->id);
            }
        }

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

        ActivityLogger::log('update', "Updated section \"{$section->name}\" — {$section->grade_level} (S.Y. {$section->school_year})", 'Section', $section->id);

        return response()->json($section->load(['teacher', 'subjects']));
    }

    public function destroy(Section $section)
    {
        // Block deletion while students are actively enrolled — otherwise the
        // section_student pivot rows cascade-delete silently (unassigning
        // every student) while enrollments.section (a plain string, not a
        // foreign key) keeps showing this section's name forever.
        if ($section->live_enrollment_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete \"{$section->name}\" — it has {$section->live_enrollment_count} enrolled student(s). Reassign them first.",
            ], 422);
        }

        $sectionName = $section->name;
        $gradeLevel  = $section->grade_level;
        // Remove related teacher assignments before deleting section
        TeacherAssignment::where('section_id', $section->id)->delete();
        $sectionId = $section->id;
        $section->delete();
        ActivityLogger::log('delete', "Deleted section \"{$sectionName}\" — {$gradeLevel}", 'Section', $sectionId);
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

        $teacherName = User::find($validated['teacher_id'])?->name ?? "#{$validated['teacher_id']}";
        ActivityLogger::log('update', "Assigned {$teacherName} as adviser for section \"{$section->name}\"", 'Section', $section->id);

        return response()->json($section->load('teacher'));
    }

    /**
     * Students the "Add Student" modal can offer for this section: an enrollment
     * for the section's grade level that is approved or enrolled (any payment
     * state), or the pending enrollment a Promotion created — and who aren't
     * already in a section of this grade. Unpaginated — the modal used to read
     * page 1 of /admin/students (15 of ~180 rows), so unassigned students on
     * later pages never showed up.
     *
     * Only the section's own school year is listed; `waiting_elsewhere` counts
     * same-grade students waiting for a section in OTHER school years (e.g.
     * promoted into next year) so the modal can say where they went.
     */
    public function eligibleStudents(Section $section)
    {
        $unplaced = fn () => \App\Models\Enrollment::query()
            ->whereNotNull('user_id')
            ->where('grade_level', $section->grade_level)
            ->where(fn ($q) => $q->whereIn('status', ['approved', 'enrolled'])
                // A promotion creates the next-year enrollment as 'pending'
                ->orWhere(fn ($p) => $p->where('status', 'pending')->whereExists(fn ($x) => $x->select(DB::raw(1))
                    ->from('promotions')->whereColumn('promotions.enrollment_id', 'enrollments.id')->where('promotions.status', 'completed'))))
            ->whereHas('user', fn ($q) => $q->where('role', 'student'))
            // Same rule addStudent() enforces: not already in a section of this grade — in that
            // enrollment's school year (last year's section doesn't count: a retained student was hidden)
            ->whereNotExists(function ($q) use ($section) {
                $q->select(DB::raw(1))
                  ->from('section_student')
                  ->join('sections', 'sections.id', '=', 'section_student.section_id')
                  ->whereColumn('section_student.user_id', 'enrollments.user_id')
                  ->whereColumn('sections.school_year', 'enrollments.school_year')
                  ->where('sections.grade_level', $section->grade_level);
            });

        $enrollments = $unplaced()
            ->where('school_year', $section->school_year)
            ->with('user:id,name,email,lrn')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id');

        $students = $enrollments->map(fn ($e) => [
            'id'             => $e->user->id,
            'name'           => $e->user->name,
            'email'          => $e->user->email,
            'lrn'            => $e->user->lrn,
            'status'         => $e->status,
            'payment_status' => $e->payment_status,
            'grade_level'    => $e->grade_level,
            'section'        => null,
        ])->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $waitingElsewhere = $unplaced()
            ->where('school_year', '!=', $section->school_year)
            ->selectRaw('school_year, COUNT(DISTINCT user_id) AS c')
            ->groupBy('school_year')
            ->pluck('c', 'school_year');

        return response()->json([
            'students'          => $students,
            'total'             => $students->count(),
            'school_year'       => $section->school_year,
            'grade_level'       => $section->grade_level,
            'waiting_elsewhere' => $waitingElsewhere,
        ]);
    }

    public function addStudent(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student'
        ]);

        $user = User::find($validated['student_id']);
        $enrollment = $user?->latestEnrollment;

        if (!$section->is_active) {
            return response()->json(['error' => "Section {$section->name} is not currently active."], 422);
        }

        // A section from a different school year than the student's own
        // enrollment shouldn't be assignable — matches the same constraint
        // already enforced for the student-initiated section change
        // (StudentPortalController::changeSection()).
        if ($enrollment && $enrollment->school_year && $section->school_year !== $enrollment->school_year) {
            return response()->json(['error' => "Section {$section->name} is not available for this student's school year ({$enrollment->school_year})."], 422);
        }

        // ...and only into a section of the student's own grade level
        if ($enrollment && $enrollment->grade_level && $section->grade_level !== $enrollment->grade_level) {
            return response()->json(['error' => "{$user->name} is enrolled in {$enrollment->grade_level}, but {$section->name} is a {$section->grade_level} section."], 422);
        }

        return DB::transaction(function () use ($section, $validated, $user, $enrollment) {
            // Lock the section row so two near-simultaneous adds/transfers
            // targeting it can't both pass the capacity check below before
            // either commits, pushing the section over max_students.
            $section = Section::where('id', $section->id)->lockForUpdate()->first();

            // Check capacity — live count, not the denormalized column
            // (see Section::getLiveEnrollmentCountAttribute())
            $maxStudents = $section->max_students ?? 30;
            if ($section->live_enrollment_count >= $maxStudents) {
                return response()->json(['error' => "Section {$section->name} is already full ({$maxStudents} students max)."], 422);
            }

            // Check if student is already in this section
            if ($section->students()->where('user_id', $validated['student_id'])->exists()) {
                return response()->json(['error' => 'Student is already in this section'], 400);
            }

            // Check if student is already in another section for the same grade level — this school
            // year (a retained student is still on last year's list of the same grade)
            $existingSection = Section::whereHas('students', function($q) use ($validated) {
                $q->where('user_id', $validated['student_id']);
            })->where('grade_level', $section->grade_level)->where('school_year', $section->school_year)->first();

            if ($existingSection) {
                return response()->json(['error' => 'Student is already assigned to section ' . $existingSection->name], 400);
            }

            $section->students()->attach($validated['student_id']);

            // Also update the enrollment's section field and status
            if ($user) {
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

            ActivityLogger::log('update', "Added {$user->name} to section \"{$section->name}\"", 'Section', $section->id);

            // Return the updated section with current enrollment count
            $section->refresh();
            return response()->json([
                'success' => true,
                'current_enrollment' => $section->live_enrollment_count,
                'students' => $section->students
            ]);
        });
    }

    public function removeStudent(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student'
        ]);

        $section->students()->detach($validated['student_id']);

        // Clear the enrollment section field
        $user = User::find($validated['student_id']);
        if ($user && $user->latestEnrollment) {
            $enrollment = $user->latestEnrollment;
            $enrollment->section = null;
            $enrollment->save();
        }

        ActivityLogger::log('update', ($user->name ?? 'Student') . " removed from section \"{$section->name}\"", 'Section', $section->id);

        return response()->json(['success' => true, 'current_enrollment' => $section->live_enrollment_count]);
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

        // Must actually confirm the student is in the source section before
        // doing anything — otherwise a stale/wrong $section id makes the
        // detach below a silent no-op while the attach to $target still
        // goes through, leaving the student enrolled in two sections at
        // once (their real section untouched, plus the new one).
        if (!$section->students()->where('user_id', $validated['student_id'])->exists()) {
            return response()->json(['error' => 'Student is not currently in section ' . $section->name . '.'], 422);
        }

        $user = User::find($validated['student_id']);
        $enrollment = $user?->latestEnrollment;

        if (!$target->is_active) {
            return response()->json(['error' => "Section {$target->name} is not currently active."], 422);
        }
        if ($enrollment && $enrollment->school_year && $target->school_year !== $enrollment->school_year) {
            return response()->json(['error' => "Section {$target->name} is not available for this student's school year ({$enrollment->school_year})."], 422);
        }

        // Everything from here on must happen inside a transaction — the
        // row locks below only serialize concurrent requests while a
        // transaction actually holds them.
        return DB::transaction(function () use ($section, $target, $validated, $user, $enrollment) {
            // Lock both section rows (consistently in ID order, to avoid two
            // concurrent transfers in opposite directions deadlocking each
            // other) so this capacity check can't race with another
            // transfer/add targeting the same destination section.
            $lockIds = [$section->id, $target->id];
            sort($lockIds);
            $locked = Section::whereIn('id', $lockIds)->lockForUpdate()->get()->keyBy('id');
            $section = $locked[$section->id];
            $target  = $locked[$target->id];

            // Live count, not the denormalized column (see
            // Section::getLiveEnrollmentCountAttribute())
            $max = $target->max_students ?? 30;
            if ($target->live_enrollment_count >= $max) {
                return response()->json(['error' => "Section {$target->name} is already full ({$max} students max)."], 422);
            }

            // Remove from current section
            $section->students()->detach($validated['student_id']);

            // Add to target section
            $target->students()->syncWithoutDetaching([$validated['student_id']]);

            // Update enrollment record
            if ($enrollment) {
                $enrollment->section = $target->name;
                $enrollment->save();
            }

            ActivityLogger::log('update', ($user->name ?? 'Student') . " transferred from \"{$section->name}\" to \"{$target->name}\"", 'Section', $target->id);

            return response()->json([
                'success'            => true,
                'new_section'        => $target->name,
                'current_enrollment' => $target->live_enrollment_count,
            ]);
        });
    }

    public function autoAssign(Request $request)
    {
        $gradeLevel = $request->input('grade_level');
        $schoolYear = $request->input('school_year'); // e.g. right after creating a section for that year

        // All active sections (optionally filtered by grade). current_enrollment
        // is initialized from the live section_student count below (the stored
        // column can drift — see Section::getLiveEnrollmentCountAttribute());
        // the loop further down keeps it in sync in-memory as students are added.
        $sections = Section::where('is_active', true)
            ->when($gradeLevel, fn($q) => $q->where('grade_level', $gradeLevel))
            ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear))
            ->withCount('students')
            ->get();
        $sections->each(fn($s) => $s->current_enrollment = $s->students_count);

        // Approved/enrolled students with no section in THEIR enrollment's school year. This used to
        // skip anyone who had ever been in a section — so every returning student (still on last
        // year's class list) was skipped. Each student is placed by that year's enrollment.
        $waiting = self::studentsWithoutSection($gradeLevel, $schoolYear)
            ->whereNotNull('grade_level')
            ->with('user')
            ->orderBy('id')
            ->get()
            ->unique('user_id');
        $students = $waiting->map(function ($e) {
            $u = $e->user;
            $u->setRelation('latestEnrollment', $e);
            return $u;
        });

        // Preview (nothing saved): per grade, how many are waiting, the free seats in that grade's
        // sections and how many would be placed — the page shows this before the Admin confirms
        if ($request->boolean('preview')) {
            $grades = $waiting->groupBy(fn ($e) => $e->school_year . '|' . $e->grade_level)->map(function ($rows, $key) use ($sections) {
                [$sy, $grade] = explode('|', $key);
                $gradeSections = $sections->where('grade_level', $grade)->where('school_year', $sy);
                $free = $gradeSections->sum(fn ($s) => max(0, ($s->max_students ?? 30) - $s->current_enrollment));
                return [
                    'school_year' => $sy,
                    'grade_level' => $grade,
                    'waiting'     => $rows->count(),
                    'sections'    => $gradeSections->pluck('name')->values(),
                    'free_seats'  => $free,
                    'will_place'  => min($rows->count(), $free),
                ];
            })->sortBy(fn ($g) => $g['school_year'] . array_search($g['grade_level'], ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6']))->values();

            return response()->json(['preview' => true, 'grades' => $grades, 'waiting' => $waiting->count(), 'will_place' => $grades->sum('will_place')]);
        }

        $assigned = 0;
        $skipped  = 0;

        foreach ($students as $student) {
            $grade = $student->latestEnrollment->grade_level;
            // A prior school year's section can still be is_active (e.g. a
            // dropout leaving an open seat) — matching on grade_level alone
            // could silently place a newly-approved student into a stale,
            // wrong-year section. Must match the student's own enrollment
            // school year too (same constraint already enforced for the
            // student-initiated section change in StudentPortalController::changeSection()).
            $schoolYear = $student->latestEnrollment->school_year;

            // First section with available space for this grade + year
            $target = $sections
                ->where('grade_level', $grade)
                ->where('school_year', $schoolYear)
                ->first(fn($s) => $s->current_enrollment < ($s->max_students ?? 30));

            if (!$target) { $skipped++; continue; }

            $target->students()->syncWithoutDetaching([$student->id]);
            // In-memory only — current_enrollment isn't a real column anymore
            // (dropped in Phase 1's Contract step), this just keeps the running
            // tally correct for the next iteration's capacity check above.
            $target->current_enrollment = $target->students()->count();

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
