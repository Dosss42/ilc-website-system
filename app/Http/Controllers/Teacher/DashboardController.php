<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Announcement;
use App\Models\ParentTeacherConference;
use App\Models\OtpVerification;
use App\Mail\PasswordChangeOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $currentSchoolYear = $this->getCurrentSchoolYear();

        // Advisory assignments: no school_year filter — the admin modal defaults to
        // next year which causes mismatches. Show all advisory assignments for
        // active sections so the teacher always sees their full advisory load.
        $adviserAssignments = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('is_advisory', true)
            ->whereHas('section', fn($q) => $q->where('is_active', true))
            ->with(['section', 'subject'])
            ->get();

        $adviserSections = $adviserAssignments
            ->pluck('section')->filter()->unique('id')->values()
            ->map(fn($section) => $this->loadSectionStudents($section));

        // Subjects and sections are derived from active schedules (not TeacherAssignment)
        $teacherSchedules = Schedule::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with(['subject', 'section'])
            ->get();

        $subjects = $teacherSchedules->pluck('subject')->filter()->unique('id')->values();

        $allSections = $teacherSchedules->pluck('section')->filter()->unique('id')->values();
        $sections = $allSections->map(fn($section) => $this->loadSectionStudents($section));

        // Grade entry rows come from Schedule (section + specific subject).
        $teacherAssignments = $teacherSchedules
            ->filter(fn($s) => $s->section && $s->subject)
            ->groupBy(fn($s) => $s->section_id . '-' . $s->subject_id)
            ->map(function ($rows) {
                $first = $rows->first();
                return (object)[
                    'section'    => $first->section,
                    'subject'    => $first->subject,
                    'section_id' => $first->section_id,
                    'subject_id' => $first->subject_id,
                    'is_advisory'=> false,
                ];
            })->values();

        // Nursery/Kinder: advisory teacher handles ALL subjects for their section.
        // Auto-add all active subjects for those sections without needing individual schedules.
        foreach ($adviserSections as $advSection) {
            if (!\App\Models\Grade::isNurseryKinder($advSection->grade_level ?? '')) continue;

            $sectionSubjects = Subject::where('grade_level', $advSection->grade_level)
                ->where('is_active', true)
                ->get();

            foreach ($sectionSubjects as $subject) {
                $alreadyExists = $teacherAssignments->first(
                    fn($a) => $a->section_id == $advSection->id && $a->subject_id == $subject->id
                );
                if (!$alreadyExists) {
                    $teacherAssignments->push((object)[
                        'section'    => $advSection,
                        'subject'    => $subject,
                        'section_id' => $advSection->id,
                        'subject_id' => $subject->id,
                        'is_advisory'=> true,
                    ]);
                }
            }

            // Ensure the section appears in the sections list
            if (!$sections->contains('id', $advSection->id)) {
                $sections->push($advSection);
            }
        }

        $gradeLevels = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
        $sectionsByGrade = [];
        foreach ($gradeLevels as $grade) {
            $sectionsByGrade[$grade] = $sections->where('grade_level', $grade);
        }

        $gradeLabels = [
            'nursery'      => 'Nursery',
            'kindergarten' => 'Kindergarten',
            'grade1'       => 'Grade 1',
            'grade2'       => 'Grade 2',
            'grade3'       => 'Grade 3',
            'grade4'       => 'Grade 4',
            'grade5'       => 'Grade 5',
            'grade6'       => 'Grade 6',
        ];

        // Schedules for the schedule section.
        // For Nursery/Kinder advisory sections, include ALL section schedules regardless
        // of teacher_id — the advisory teacher owns all subjects for those sections.
        $nkAdvisorySectionIds = $adviserSections
            ->filter(fn($s) => \App\Models\Grade::isNurseryKinder($s->grade_level ?? ''))
            ->pluck('id');

        $schedules = Schedule::where(function ($q) use ($teacher, $nkAdvisorySectionIds) {
                $q->where('teacher_id', $teacher->id);
                if ($nkAdvisorySectionIds->isNotEmpty()) {
                    $q->orWhereIn('section_id', $nkAdvisorySectionIds);
                }
            })
            ->where('is_active', true)
            ->with(['section:id,name,grade_level', 'subject:id,name,code', 'teacher:id,name'])
            ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();

        // Count pending drafts for badge
        $draftCount = Grade::where('teacher_id', $teacher->id)
            ->where('school_year', $currentSchoolYear)
            ->where('status', 'draft')
            ->count();

        $rejectedCount = Grade::where('teacher_id', $teacher->id)
            ->where('school_year', $currentSchoolYear)
            ->where('status', 'rejected')
            ->count();

        return view('teacherDashboard', compact(
            'teacher', 'subjects', 'sections', 'sectionsByGrade', 'schedules',
            'adviserSections', 'gradeLabels', 'teacherAssignments',
            'currentSchoolYear', 'draftCount', 'rejectedCount'
        ));
    }

    private function loadSectionStudents($section)
    {
        $section->load(['students' => fn($q) => $q
            ->select('users.id', 'users.name', 'users.email', 'users.lrn')
            ->whereHas('enrollments', fn($q2) => $q2->where('status', 'enrolled'))
        ]);
        if ($section->students->isEmpty()) {
            $enrolled = User::whereHas('enrollments', fn($q) => $q
                ->where('section', $section->name)
                ->where('status', 'enrolled')
                ->where('school_year', $section->school_year)
            )->select('users.id', 'users.name', 'users.email', 'users.lrn')->get();
            if ($enrolled->isNotEmpty()) {
                $section->setRelation('students', $enrolled);
            }
        }
        return $section;
    }

    private function getCurrentSchoolYear(): string
    {
        $latest = Section::where('is_active', true)->orderByDesc('school_year')->value('school_year');
        if ($latest) return $latest;
        $year = now()->month >= 6 ? now()->year : now()->year - 1;
        return $year . '-' . ($year + 1);
    }

    private function teacherOwnsSection(int $teacherId, int $sectionId): bool
    {
        // Check active schedules first (primary source for subject teachers)
        $inSchedule = Schedule::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_active', true)
            ->exists();
        if ($inSchedule) return true;

        // Fallback: advisory assignment (class advisers can also manage their section)
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_advisory', true)
            ->exists();
    }

    private function teacherOwnsSubjectInSection(int $teacherId, int $sectionId, ?int $subjectId): bool
    {
        if (!$subjectId) {
            // No subject filter — check if teacher has any schedule for this section
            return Schedule::where('teacher_id', $teacherId)
                ->where('section_id', $sectionId)
                ->where('is_active', true)
                ->exists()
                || TeacherAssignment::where('teacher_id', $teacherId)
                    ->where('section_id', $sectionId)
                    ->where('is_advisory', true)
                    ->exists();
        }

        // Check schedule for this exact subject
        $inSchedule = Schedule::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();
        if ($inSchedule) return true;

        // Advisory teachers can enter general grades for their section
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_advisory', true)
            ->exists();
    }

    // ── MY STUDENTS: load student list with grades ──

    public function loadClassRecord(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this section.'], 403);
        }

        if (!$this->teacherOwnsSubjectInSection($teacher->id, $request->section_id, $request->subject_id)) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this subject in section.'], 403);
        }

        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);
        $students = $section->students;

        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();

        // Include grades with NULL school_year (old records before migration) so they
        // remain visible. Year-specific records take priority: NULL rows come first so
        // keyBy replaces them with the year-specific row for the same student.
        $existingGrades = Grade::where('teacher_id', $teacher->id)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->where('term', $request->term)
            ->where(fn($q) => $q->where('school_year', $schoolYear)->orWhereNull('school_year'))
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->whereIn('student_id', $students->pluck('id'))
            ->orderByRaw('(school_year IS NULL) DESC')
            ->get()
            ->keyBy('student_id');

        $draftGrades = Grade::where('teacher_id', $teacher->id)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->where('term', $request->term)
            ->where(fn($q) => $q->where('school_year', $schoolYear)->orWhereNull('school_year'))
            ->where('status', 'draft')
            ->whereIn('student_id', $students->pluck('id'))
            ->orderByRaw('(school_year IS NULL) DESC')
            ->get()
            ->keyBy('student_id');

        $gradeLevel  = $section->grade_level ?? '';
        $isDescriptive = \App\Models\Grade::isNurseryKinder($gradeLevel);

        $classRecord = $students->map(function ($student) use ($existingGrades, $draftGrades, $isDescriptive) {
            $grade = $existingGrades->get($student->id);
            $draft = $draftGrades->get($student->id);

            if ($isDescriptive) {
                $gradeVal      = $grade?->descriptive_grade ?? '';
                $draftGradeVal = $draft?->descriptive_grade ?? null;
                $remarks       = $grade?->descriptive_grade
                    ? \App\Models\Grade::getDescriptiveLabel($grade->descriptive_grade)
                    : '';
                $draftRemarks  = $draft?->descriptive_grade
                    ? \App\Models\Grade::getDescriptiveLabel($draft->descriptive_grade)
                    : null;
            } else {
                $gradeVal      = $grade?->grade ?? '';
                $draftGradeVal = $draft?->grade ?? null;
                $remarks       = $grade?->remarks ?? '';
                $draftRemarks  = null;
            }

            return [
                'student_id'      => $student->id,
                'name'            => $student->name,
                'lrn'             => $student->lrn ?? '',
                'enrollment_id'   => $student->latestEnrollment?->id ?? null,
                'grade'           => $gradeVal,
                'remarks'         => $remarks,
                'grade_status'    => $grade?->status ?? null,
                'draft_grade'     => $draftGradeVal,
                'draft_remarks'   => $draftRemarks,
                'draft_id'        => $draft?->id ?? null,
                'has_draft'       => $draft !== null,
            ];
        });

        return response()->json([
            'success'       => true,
            'data'          => $classRecord,
            'section'       => $section->name,
            'grade_level'   => $gradeLevel,
            'is_descriptive'=> $isDescriptive,
            'draft_count'   => $draftGrades->count(),
        ]);
    }

    // ── SAVE GRADES (manual input — goes directly as submitted) ──

    public function saveGrades(Request $request)
    {
        $teacher = Auth::user();
        $isDraft = (bool) $request->input('draft', false);

        $validated = $request->validate([
            'grades'                          => 'required|array',
            'grades.*.student_id'            => 'required|exists:users,id',
            'grades.*.subject_id'            => 'nullable|exists:subjects,id',
            'grades.*.enrollment_id'         => 'nullable|exists:enrollments,id',
            'grades.*.term'                  => 'required|integer|min:1|max:3',
            'grades.*.grade'                 => 'nullable|numeric|min:0|max:100',
            'grades.*.descriptive_grade'     => 'nullable|in:O,VS,S,FS,DNME',
            'grades.*.school_year'           => 'nullable|string|max:20',
        ]);

        $schoolYear = $this->getCurrentSchoolYear();

        // Ownership check — same for both draft and submit
        foreach ($validated['grades'] as $gradeData) {
            if (!$this->teacherOwnsSubjectInSection(
                $teacher->id,
                $request->input('section_id', 0),
                $gradeData['subject_id'] ?? null
            )) {
                $hasAssignment = TeacherAssignment::where('teacher_id', $teacher->id)
                    ->where(fn($q) => $q->where('subject_id', $gradeData['subject_id'] ?? null)
                        ->orWhereNull('subject_id'))
                    ->exists();
                if (!$hasAssignment) {
                    return response()->json(['success' => false, 'message' => 'Not assigned to this subject.'], 403);
                }
            }
        }

        DB::beginTransaction();
        try {
            if ($isDraft) {
                // ── Draft path: mirrors importGrades draft logic ──────────────
                // Pull scope from first grade row
                $first      = $validated['grades'][0];
                $subjectId  = $first['subject_id'] ?? null;
                $term       = $first['term'];
                $sy         = $first['school_year'] ?? $schoolYear;
                $studentIds = array_column($validated['grades'], 'student_id');

                // Replace previous manual drafts for this exact scope
                Grade::where('teacher_id', $teacher->id)
                    ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
                    ->when(!$subjectId, fn($q) => $q->whereNull('subject_id'))
                    ->where('term', $term)
                    ->where('school_year', $sy)
                    ->where('status', 'draft')
                    ->whereIn('student_id', $studentIds)
                    ->delete();

                $draftCount = 0;
                foreach ($validated['grades'] as $gradeData) {
                    $descriptive = $gradeData['descriptive_grade'] ?? null;
                    $gradeValue  = isset($gradeData['grade']) && $gradeData['grade'] !== '' ? (float) $gradeData['grade'] : null;
                    if ($gradeValue === null && $descriptive === null) continue; // skip blanks

                    Grade::create([
                        'student_id'        => $gradeData['student_id'],
                        'teacher_id'        => $teacher->id,
                        'subject_id'        => $gradeData['subject_id'] ?? null,
                        'term'              => $gradeData['term'],
                        'school_year'       => $gradeData['school_year'] ?? $schoolYear,
                        'grade'             => $gradeValue,
                        'descriptive_grade' => $descriptive,
                        'remarks'           => $descriptive
                            ? \App\Models\Grade::getDescriptiveLabel($descriptive)
                            : Grade::getRemarks($gradeValue),
                        'status'            => 'draft',
                        'enrollment_id'     => $gradeData['enrollment_id'] ?? null,
                    ]);
                    $draftCount++;
                }

                DB::commit();
                return response()->json([
                    'success'     => true,
                    'draft'       => true,
                    'draft_count' => $draftCount,
                    'message'     => $draftCount . ' grade(s) saved as draft. Review and click Submit Grades when ready.',
                ]);
            }

            // ── Submit path ───────────────────────────────────────────────────
            foreach ($validated['grades'] as $gradeData) {
                $descriptive = $gradeData['descriptive_grade'] ?? null;
                $gradeValue  = isset($gradeData['grade']) && $gradeData['grade'] !== '' ? $gradeData['grade'] : null;
                $remarks     = $descriptive
                    ? \App\Models\Grade::getDescriptiveLabel($descriptive)
                    : Grade::getRemarks($gradeValue);

                $update = [
                    'grade'             => $gradeValue,
                    'descriptive_grade' => $descriptive,
                    'remarks'           => $remarks,
                    'school_year'       => $gradeData['school_year'] ?? $schoolYear,
                    'status'            => 'submitted',
                ];
                if (!empty($gradeData['enrollment_id'])) {
                    $update['enrollment_id'] = $gradeData['enrollment_id'];
                }

                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'subject_id' => $gradeData['subject_id'] ?? null,
                        'term'       => $gradeData['term'],
                        'teacher_id' => $teacher->id,
                        'school_year'=> $gradeData['school_year'] ?? $schoolYear,
                        'status'     => 'submitted',
                    ],
                    $update
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Grades submitted successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to save grades: ' . $e->getMessage()], 500);
        }
    }

    // ── EXPORT: proper .xlsx via PhpSpreadsheet ──

    public function exportGrades(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            abort(403, 'Not assigned to this section.');
        }

        $section     = Section::findOrFail($request->section_id);
        $section     = $this->loadSectionStudents($section);
        $students    = $section->students;
        $subject     = $request->subject_id ? Subject::find($request->subject_id) : null;
        $schoolYear  = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();
        $termLabel   = 'Term ' . $request->term;
        $subjectName = $subject?->name ?? 'All Subjects';
        $gradeLevel  = strtoupper(str_replace(['_', '-'], ' ', $section->grade_level ?? ''));

        $existingGrades = Grade::where('teacher_id', $teacher->id)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->where('term', $request->term)
            ->where('school_year', $schoolYear)
            ->where('status', 'submitted')
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // ── Build styled xlsx (same design as grade template) ──
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Grade Export')
            ->setSubject($subjectName)
            ->setCreator($teacher->name)
            ->setDescription('IEMELIF Learning Center — Grade Export (Import-Ready)');

        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle('Grade Export');

        // Shared styles
        $blue       = '1a3a6c';
        $white      = 'FFFFFF';
        $lightBlue  = 'e8f0fb';
        $gold       = 'F5A623';
        $green      = 'E2EFDA';
        $lastCol    = 'E'; // A=No, B=Student Name, C=LRN, D=Grade, E=Remarks

        $thinBorder = ['borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color'       => ['rgb' => 'b0b8c4'],
        ]]];

        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(30);
        $ws->getColumnDimension('C')->setWidth(18);
        $ws->getColumnDimension('D')->setWidth(12);
        $ws->getColumnDimension('E')->setWidth(20);

        $r = 1;

        // ── Logo (if file exists) ──
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('ILC Logo');
            $drawing->setDescription('IEMELIF Learning Center');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(4);
            $drawing->setOffsetY(4);
            $drawing->setWorksheet($ws);
        }

        // Row 1 — School Name (beside logo)
        $ws->mergeCells("B1:{$lastCol}1");
        $ws->setCellValue('B1', 'IEMELIF LEARNING CENTER');
        $ws->getStyle('B1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $blue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(28);

        // Row 2 — Address
        $ws->mergeCells("B2:{$lastCol}2");
        $ws->setCellValue('B2', 'General Tinio, Nueva Ecija');
        $ws->getStyle('B2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '555555'], 'italic' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(16);

        // Row 3 — Document title
        $ws->mergeCells("A3:{$lastCol}3");
        $ws->setCellValue('A3', 'GRADE EXPORT — IMPORT-READY');
        $ws->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $white]],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(3)->setRowHeight(22);

        // Divider row
        $ws->mergeCells("A4:{$lastCol}4");
        $ws->getStyle("A4:{$lastCol}4")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gold]],
        ]);
        $ws->getRowDimension(4)->setRowHeight(4);

        $r = 5;
        // ── Meta info rows ──
        $metaLabelStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => $blue]],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $lightBlue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ];
        $metaValueStyle = [
            'font'      => ['bold' => false, 'color' => ['rgb' => '222222']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ];

        $metas = [
            ['School Year', $schoolYear,  'Grade Level', $gradeLevel],
            ['Section',     $section->name, 'Subject', $subjectName],
            ['Term',        $termLabel,   'Teacher',     $teacher->name],
            ['Exported',    now()->format('F d, Y  h:i A'), '', ''],
        ];

        foreach ($metas as [$l1, $v1, $l2, $v2]) {
            $ws->setCellValue("A{$r}", $l1);
            $ws->mergeCells("B{$r}:C{$r}");
            $ws->setCellValue("B{$r}", $v1);
            $ws->getStyle("A{$r}")->applyFromArray(array_merge($metaLabelStyle, $thinBorder));
            $ws->getStyle("B{$r}:C{$r}")->applyFromArray(array_merge($metaValueStyle, $thinBorder));

            if ($l2) {
                $ws->setCellValue("D{$r}", $l2);
                $ws->setCellValue("E{$r}", $v2);
                $ws->getStyle("D{$r}")->applyFromArray(array_merge($metaLabelStyle, $thinBorder));
                $ws->getStyle("E{$r}")->applyFromArray(array_merge($metaValueStyle, $thinBorder));
            } else {
                $ws->mergeCells("D{$r}:E{$r}");
            }
            $ws->getRowDimension($r)->setRowHeight(16);
            $r++;
        }

        $r++; // spacer

        // ── Instructions note ──
        $ws->mergeCells("A{$r}:{$lastCol}{$r}");
        $ws->setCellValue("A{$r}", 'INSTRUCTIONS: Only edit column D (Grade). Do NOT change student_id, Student Name, or LRN. You can import this file directly (.xlsx) or save as .csv first.');
        $ws->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7f4f00']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $gold]]],
        ]);
        $ws->getRowDimension($r)->setRowHeight(18);
        $r++;
        $r++; // spacer

        // ── Column headers ──
        // Column A = student_id (required by import), B = Name, C = LRN, D = Grade, E = Remarks
        $colHeaders = ['student_id', 'STUDENT NAME', 'LRN', 'GRADE (Edit Only)', 'REMARKS'];
        foreach ($colHeaders as $i => $h) {
            $col = chr(65 + $i); // A, B, C, D, E
            $ws->setCellValue("{$col}{$r}", $h);
        }
        $ws->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 11],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '3a5a8c']]],
        ]);
        // Highlight the editable grade column header
        $ws->getStyle("D{$r}")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gold]],
            'font' => ['bold' => true, 'color' => ['rgb' => '7f4f00']],
        ]);
        $ws->getRowDimension($r)->setRowHeight(24);
        $dataStartRow = $r;
        $r++;

        // ── Student data rows ──
        foreach ($students as $i => $student) {
            $gradeRecord = $existingGrades->get($student->id);
            $grade       = $gradeRecord?->grade;
            $remarks     = $grade !== null ? \App\Models\Grade::getRemarks($grade) : '';
            $rowBg       = $i % 2 === 0 ? 'FFFFFF' : 'f5f7fa';

            $ws->setCellValue("A{$r}", $student->id); // student_id — required for import
            $ws->setCellValue("B{$r}", $student->name);
            $ws->setCellValue("C{$r}", $student->lrn ?? '');
            if ($grade !== null) {
                $ws->setCellValue("D{$r}", $grade);
            }
            $ws->setCellValue("E{$r}", $remarks);

            $ws->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray(array_merge($thinBorder, [
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
            ]));
            // Center: #, Grade
            foreach (["A{$r}", "C{$r}", "D{$r}"] as $c) {
                $ws->getStyle($c)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            // Highlight grade cell (editable)
            $ws->getStyle("D{$r}")->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $green]],
                'font' => ['bold' => true],
            ]);
            // Color remarks
            if ($grade !== null) {
                $remarkColor = $grade >= 75 ? '2e7d32' : 'c62828';
                $ws->getStyle("E{$r}")->getFont()->getColor()->setRGB($remarkColor);
            }
            $ws->getRowDimension($r)->setRowHeight(16);
            $r++;
        }

        // ── Total row ──
        $ws->mergeCells("A{$r}:C{$r}");
        $ws->setCellValue("A{$r}", 'CLASS AVERAGE');
        $dataEnd = $r - 1;
        $ws->setCellValue("D{$r}", "=IF(COUNTA(D" . ($dataStartRow + 1) . ":D{$dataEnd})=0,\"\",AVERAGE(D" . ($dataStartRow + 1) . ":D{$dataEnd}))");
        $ws->getStyle("D{$r}")->getNumberFormat()->setFormatCode('0.00');
        $ws->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $white]],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => $blue]]],
        ]);
        $ws->getRowDimension($r)->setRowHeight(18);

        // ── Freeze panes below header row ──
        $ws->freezePane('A' . ($dataStartRow + 1));

        // ── Footer note ──
        $r += 2;
        $ws->mergeCells("A{$r}:{$lastCol}{$r}");
        $ws->setCellValue("A{$r}", 'To import: save a copy as .csv (comma-separated), then upload via Teacher Portal > Import Grades.');
        $ws->getStyle("A{$r}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '888888']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        // ── Output ──
        $filename = implode('_', array_filter([
            'grades_export',
            preg_replace('/[^A-Za-z0-9\-]/', '-', $section->name),
            $subject?->code ?? 'all',
            'term' . $request->term,
            str_replace('-', '_', $schoolYear),
        ])) . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── DOWNLOAD GRADE TEMPLATE: 3-sheet DepEd-compliant workbook ──

    public function downloadGradeTemplate(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            abort(403, 'Not assigned to this section.');
        }

        $section     = Section::findOrFail($request->section_id);
        $section     = $this->loadSectionStudents($section);
        $students    = $section->students;
        $subject     = $request->subject_id ? Subject::find($request->subject_id) : null;
        $schoolYear  = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();
        $termLabel   = 'Term ' . $request->term;
        $subjectName = $subject?->name ?? 'All Subjects';
        $gradeLevel  = $section->grade_level ?? '';
        $isNurseryKinder = in_array($gradeLevel, ['nursery', 'kindergarten']);

        $filename = implode('_', array_filter([
            'grade_template',
            preg_replace('/[^A-Za-z0-9\-]/', '-', $section->name),
            $subject?->code ?? 'all',
            'term' . $request->term,
            str_replace('-', '_', $schoolYear),
        ])) . '.xlsx';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Grade Template')
            ->setSubject($subjectName)
            ->setCreator($teacher->name);

        // ═══════════════════════════════════════════
        // SHEET 1 — GRADE COMPUTATION
        // ═══════════════════════════════════════════
        $gs = $spreadsheet->getActiveSheet();
        $gs->setTitle('Grade Computation');

        $blue   = '1a3a6c';
        $white  = 'FFFFFF';
        $gold   = 'FFF2CC';
        $goldBorder = 'F5A623';

        $thinBorder = ['borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color'       => ['rgb' => 'b0b8c4'],
        ]]];

        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]];
        $wrapCenter  = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true]];

        // Determine column count
        $lastDataCol = $isNurseryKinder ? 'J' : 'X';
        $totalCols   = $isNurseryKinder ? 10 : 24;

        // ── Header ──
        $r = 1;
        foreach ([
            ['IEMELIF LEARNING CENTER', 14, true, $blue],
            ['General Tinio, Nueva Ecija', 10, false, '666666'],
            [$isNurseryKinder ? 'DEVELOPMENTAL ASSESSMENT SHEET' : 'GRADE COMPUTATION SHEET', 12, true, $blue],
        ] as [$text, $size, $bold, $color]) {
            $gs->mergeCells("A{$r}:{$lastDataCol}{$r}");
            $gs->setCellValue("A{$r}", $text);
            $gs->getStyle("A{$r}")->applyFromArray([
                'font'      => ['size' => $size, 'bold' => $bold, 'color' => ['rgb' => $color]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $gs->getRowDimension($r)->setRowHeight($r === 1 ? 26 : 16);
            $r++;
        }

        // ── Meta info ──
        $r++; // spacer
        foreach ([
            ['School Year', $schoolYear, 'Term', $termLabel],
            ['Subject',     $subjectName, 'Section', $section->name],
            ['Teacher',     $teacher->name, 'Grade Level', strtoupper(str_replace(['_','-'], ' ', $gradeLevel))],
        ] as [$l1, $v1, $l2, $v2]) {
            $gs->setCellValue("A{$r}", $l1); $gs->mergeCells("B{$r}:E{$r}"); $gs->setCellValue("B{$r}", $v1);
            $gs->setCellValue("G{$r}", $l2); $gs->mergeCells("H{$r}:{$lastDataCol}{$r}"); $gs->setCellValue("H{$r}", $v2);
            $gs->getStyle("A{$r}:E{$r}")->applyFromArray($thinBorder);
            $gs->getStyle("G{$r}:{$lastDataCol}{$r}")->applyFromArray($thinBorder);
            $labelStyle = ['font' => ['bold' => true, 'color' => ['rgb' => $blue]], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f0fb']]];
            $gs->getStyle("A{$r}")->applyFromArray($labelStyle);
            $gs->getStyle("G{$r}")->applyFromArray($labelStyle);
            $r++;
        }
        $r++; // spacer

        if ($isNurseryKinder) {
            // ═══════════════════════════════════════
            // NURSERY / KINDER — Descriptive Rating
            // ═══════════════════════════════════════
            $gs->getColumnDimension('A')->setWidth(4);
            $gs->getColumnDimension('B')->setWidth(30);
            $gs->getColumnDimension('C')->setWidth(16);
            $gs->getColumnDimension('D')->setWidth(22);  // RATING column
            $gs->getColumnDimension('E')->setWidth(30);  // REMARKS (auto)

            $lastDataCol = 'E';

            // Rating scale legend
            $gs->mergeCells("A{$r}:E{$r}");
            $gs->setCellValue("A{$r}", 'DESCRIPTIVE RATING SCALE: O = Outstanding | VS = Very Satisfactory | S = Satisfactory | FS = Fairly Satisfactory | DNME = Did Not Meet Expectations');
            $gs->getStyle("A{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $blue]],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEEAF1']],
                'alignment' => ['wrapText' => true, 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $gs->getRowDimension($r)->setRowHeight(24);
            $r++;
            $r++; // spacer

            // Column headers
            $headerRow = $r;
            $kHeaders = ['#', 'STUDENT NAME', 'LRN', 'RATING', 'DESCRIPTOR'];
            $kCols    = ['A', 'B', 'C', 'D', 'E'];
            foreach ($kHeaders as $i => $h) {
                $gs->setCellValue($kCols[$i] . $r, $h);
            }
            $gs->getStyle("A{$r}:E{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 10],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '3a5a8c']]],
            ]);
            $gs->getRowDimension($r)->setRowHeight(28);
            $r++;

            // Student rows with dropdown validation on RATING column
            $descriptiveOptions = '"O,VS,S,FS,DNME"';
            $dataStartRow = $r;
            foreach ($students as $i => $student) {
                $rowBg = $i % 2 === 0 ? 'FFFFFF' : 'f5f7fa';
                $gs->setCellValue("A{$r}", $i + 1);
                $gs->setCellValue("B{$r}", $student->name);
                $gs->setCellValue("C{$r}", $student->lrn ?? '');
                // D: dropdown rating — teacher selects O/VS/S/FS/DNME
                // E: auto descriptor from D
                $gs->setCellValue("E{$r}", "=IF(D{$r}=\"\",\"\",IF(D{$r}=\"O\",\"Outstanding\",IF(D{$r}=\"VS\",\"Very Satisfactory\",IF(D{$r}=\"S\",\"Satisfactory\",IF(D{$r}=\"FS\",\"Fairly Satisfactory\",IF(D{$r}=\"DNME\",\"Did Not Meet Expectations\",\"\"))))))");

                // Dropdown validation on D column
                $validation = $gs->getCell("D{$r}")->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Invalid Rating');
                $validation->setError('Please select: O, VS, S, FS, or DNME');
                $validation->setPromptTitle('Select Rating');
                $validation->setPrompt('O=Outstanding, VS=Very Satisfactory, S=Satisfactory, FS=Fairly Satisfactory, DNME=Did Not Meet Expectations');
                $validation->setFormula1($descriptiveOptions);

                $rowStyle = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]]];
                $gs->getStyle("A{$r}:E{$r}")->applyFromArray(array_merge($rowStyle, $thinBorder));
                $gs->getStyle("A{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $gs->getStyle("D{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $gs->getStyle("D{$r}:E{$r}")->applyFromArray(['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF9E6']]]);
                $gs->getStyle("D{$r}")->getFont()->setBold(true);
                $gs->getRowDimension($r)->setRowHeight(16);
                $r++;
            }
        } else {
            // ═══════════════════════
            // GRADE 1-6 layout
            // ═══════════════════════
            $gs->getColumnDimension('A')->setWidth(4);
            $gs->getColumnDimension('B')->setWidth(26);
            $gs->getColumnDimension('C')->setWidth(14);
            foreach (['D','E','F','G','H'] as $col) $gs->getColumnDimension($col)->setWidth(6);  // WW items
            $gs->getColumnDimension('I')->setWidth(8);   // WW Total
            $gs->getColumnDimension('J')->setWidth(8);   // WW HPS
            $gs->getColumnDimension('K')->setWidth(8);   // WW %
            foreach (['L','M','N','O'] as $col) $gs->getColumnDimension($col)->setWidth(6);       // PT items
            $gs->getColumnDimension('P')->setWidth(8);   // PT Total
            $gs->getColumnDimension('Q')->setWidth(8);   // PT HPS
            $gs->getColumnDimension('R')->setWidth(8);   // PT %
            $gs->getColumnDimension('S')->setWidth(8);   // QA Score
            $gs->getColumnDimension('T')->setWidth(8);   // QA HPS
            $gs->getColumnDimension('U')->setWidth(8);   // QA %
            $gs->getColumnDimension('V')->setWidth(10);  // Initial Grade
            $gs->getColumnDimension('W')->setWidth(11);  // Transmuted
            $gs->getColumnDimension('X')->setWidth(10);  // Remarks

            // DepEd weights notice
            $gs->mergeCells("A{$r}:X{$r}");
            $gs->setCellValue("A{$r}", 'DepEd Grading Weights: Written Works (WW) = 25%  |  Performance Tasks (PT) = 50%  |  Quarterly Assessment (QA) = 25%');
            $gs->getStyle("A{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $blue]],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEEAF1']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $gs->getRowDimension($r)->setRowHeight(18);
            $r++;
            $r++; // spacer

            // ── Group header row (WW / PT / QA / Final) ──
            $groupRow = $r;
            foreach ([
                ['A', 'A', ''],
                ['B', 'B', ''],
                ['C', 'C', ''],
                ['D', 'K', 'WRITTEN WORKS (25%)'],
                ['L', 'R', 'PERFORMANCE TASKS (50%)'],
                ['S', 'U', 'QUARTERLY ASSESSMENT (25%)'],
                ['V', 'X', 'FINAL GRADE'],
            ] as [$from, $to, $label]) {
                if ($from !== $to) $gs->mergeCells("{$from}{$r}:{$to}{$r}");
                if ($label) {
                    $gs->setCellValue("{$from}{$r}", $label);
                    $bgColor = match($from) {
                        'D' => 'DEEAF1', 'L' => 'E2EFDA', 'S' => 'FCE4D6', 'V' => 'F2F2F2', default => $white,
                    };
                    $gs->getStyle("{$from}{$r}:{$to}{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $blue]],
                        'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'b0b8c4']]],
                    ]);
                }
            }
            $gs->getRowDimension($r)->setRowHeight(18);
            $r++;

            // ── Column header row ──
            $headerRow = $r;
            $allHeaders = [
                'A' => 'STUDENT\nID',  'B' => 'STUDENT NAME', 'C' => 'LRN',
                'D' => 'WW1',       'E' => 'WW2',          'F' => 'WW3',     'G' => 'WW4',  'H' => 'WW5',
                'I' => 'WW\nTotal', 'J' => 'WW\nHPS',      'K' => 'WW\n%',
                'L' => 'PT1',       'M' => 'PT2',           'N' => 'PT3',     'O' => 'PT4',
                'P' => 'PT\nTotal', 'Q' => 'PT\nHPS',      'R' => 'PT\n%',
                'S' => 'QA\nScore', 'T' => 'QA\nHPS',      'U' => 'QA\n%',
                'V' => 'Initial\nGrade', 'W' => 'Transmuted\nGrade', 'X' => 'Remarks',
            ];
            foreach ($allHeaders as $col => $label) {
                $gs->setCellValue("{$col}{$r}", str_replace('\n', "\n", $label));
            }
            $gs->getStyle("A{$r}:X{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 9],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '3a5a8c']]],
            ]);
            $gs->getRowDimension($r)->setRowHeight(30);
            $r++;

            // ── HPS row (teacher fills highest possible scores) ──
            $hpsRow = $r;
            $gs->setCellValue("A{$r}", '');
            $gs->setCellValue("B{$r}", 'HIGHEST POSSIBLE SCORE (HPS)');
            $gs->mergeCells("B{$r}:C{$r}");
            // WW HPS total = sum of individual WW HPS
            $gs->setCellValue("I{$r}", "=SUM(D{$r}:H{$r})");
            // PT HPS total
            $gs->setCellValue("P{$r}", "=SUM(L{$r}:O{$r})");
            $gs->getStyle("A{$r}:X{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => '7D4000']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gold]],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $goldBorder]]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            // Label formula cells in HPS row differently
            $hpsFormulaStyle = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE699']]];
            $gs->getStyle("I{$r}")->applyFromArray($hpsFormulaStyle);
            $gs->getStyle("P{$r}")->applyFromArray($hpsFormulaStyle);
            $gs->getStyle("B{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            // Add comment instructions
            $comment = $gs->getComment("D{$r}");
            $comment->getText()->createTextRun('Enter the max score for each WW item here (e.g. 20, 30...). Total auto-computes in column I.');
            $gs->getRowDimension($r)->setRowHeight(18);
            $r++;

            // ── Student rows ──
            $transmutationIFS = fn(string $cell): string =>
                "=IF({$cell}=\"\",\"\",IFS({$cell}>=100,100,{$cell}>=98.4,99,{$cell}>=96.8,98,{$cell}>=95.2,97,{$cell}>=93.6,96,{$cell}>=92,95,{$cell}>=90.4,94,{$cell}>=88.8,93,{$cell}>=87.2,92,{$cell}>=85.6,91,{$cell}>=84,90,{$cell}>=82.4,89,{$cell}>=80.8,88,{$cell}>=79.2,87,{$cell}>=77.6,86,{$cell}>=76,85,{$cell}>=74.4,84,{$cell}>=72.8,83,{$cell}>=71.2,82,{$cell}>=69.6,81,{$cell}>=68,80,{$cell}>=66.4,79,{$cell}>=64.8,78,{$cell}>=63.2,77,{$cell}>=61.6,76,{$cell}>=60,75,{$cell}>=56,74,{$cell}>=52,73,{$cell}>=48,72,{$cell}>=44,71,{$cell}>=40,70,{$cell}>=36,69,{$cell}>=32,68,{$cell}>=28,67,{$cell}>=24,66,{$cell}>=20,65,TRUE,60))";

            foreach ($students as $i => $student) {
                $rowBg    = $i % 2 === 0 ? 'FFFFFF' : 'f5f7fa';
                $inputBg  = $i % 2 === 0 ? 'FFFFFF' : 'f5f7fa';
                $formulaBg = 'E2EFDA';

                $gs->setCellValue("A{$r}", $student->id); // real user_id — required for import
                $gs->setCellValue("B{$r}", $student->name);
                $gs->setCellValue("C{$r}", $student->lrn ?? '');
                // D-H: WW items (teacher fills)
                // I: WW Total
                $gs->setCellValue("I{$r}", "=SUM(D{$r}:H{$r})");
                // K: WW %
                $gs->setCellValue("K{$r}", "=IF(I\${$hpsRow}=0,\"\",ROUND(I{$r}/I\${$hpsRow}*100,2))");
                // L-O: PT items (teacher fills)
                // P: PT Total
                $gs->setCellValue("P{$r}", "=SUM(L{$r}:O{$r})");
                // R: PT %
                $gs->setCellValue("R{$r}", "=IF(P\${$hpsRow}=0,\"\",ROUND(P{$r}/P\${$hpsRow}*100,2))");
                // S: QA Score (teacher fills)
                // T: QA HPS (teacher fills)
                // U: QA %
                $gs->setCellValue("U{$r}", "=IF(T{$r}=0,\"\",ROUND(S{$r}/T{$r}*100,2))");
                // V: Initial Grade
                $gs->setCellValue("V{$r}", "=IF(OR(K{$r}=\"\",R{$r}=\"\",U{$r}=\"\"),\"\",ROUND(K{$r}*0.25+R{$r}*0.50+U{$r}*0.25,2))");
                // W: Transmuted Grade
                $gs->setCellValue("W{$r}", $transmutationIFS("V{$r}"));
                // X: Remarks
                $gs->setCellValue("X{$r}", "=IF(W{$r}=\"\",\"\",IF(W{$r}>=75,\"Passed\",\"Failed\"))");

                // Row base style
                $gs->getStyle("A{$r}:X{$r}")->applyFromArray([
                    'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'b0b8c4']]],
                ]);
                // Formula cells (auto-computed): green tint
                $formulaStyle = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $formulaBg]], 'font' => ['bold' => false]];
                foreach (["I{$r}", "K{$r}", "P{$r}", "R{$r}", "U{$r}", "V{$r}", "W{$r}"] as $fc) {
                    $gs->getStyle($fc)->applyFromArray($formulaStyle);
                }
                // Remarks: color by result
                $gs->getStyle("X{$r}")->applyFromArray(['font' => ['bold' => true]]);

                // Center alignment for numeric/formula cols
                foreach (['A','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X'] as $col) {
                    $gs->getStyle("{$col}{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                $gs->getRowDimension($r)->setRowHeight(16);
                $r++;
            }

            // Legend note
            $r++;
            $gs->mergeCells("A{$r}:X{$r}");
            $gs->setCellValue("A{$r}", 'LEGEND:  Yellow row = enter Highest Possible Score (HPS)  |  Green cells = auto-computed  |  White/Gray cells = enter raw scores  |  IMPORT: Save file then use "Import Grades" button — system reads column W (Transmuted Grade) automatically');
            $gs->getStyle("A{$r}")->applyFromArray(['font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '555555']]]);
        }

        // Freeze panes at first data row, column D
        $gs->freezePane('D' . ($r - count($students)));

        // ═══════════════════════════════════════════
        // SHEET 2 — ATTENDANCE TRACKER
        // ═══════════════════════════════════════════
        $as = $spreadsheet->createSheet();
        $as->setTitle('Attendance');

        $as->getColumnDimension('A')->setWidth(4);
        $as->getColumnDimension('B')->setWidth(26);
        $as->getColumnDimension('C')->setWidth(14);
        for ($d = 1; $d <= 31; $d++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 3);
            $as->getColumnDimension($col)->setWidth(4);
        }
        $summaryStartIdx = 35; // col AI
        $colAI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartIdx);
        $colAJ = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartIdx + 1);
        $colAK = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartIdx + 2);
        $colAL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartIdx + 3);
        $colAM = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartIdx + 4);
        $lastAttCol = $colAM;

        foreach ([$colAI, $colAJ, $colAK, $colAL] as $sc) $as->getColumnDimension($sc)->setWidth(7);
        $as->getColumnDimension($colAM)->setWidth(10);

        $ar = 1;
        // Header
        $as->mergeCells("A{$ar}:{$lastAttCol}{$ar}");
        $as->setCellValue("A{$ar}", 'IEMELIF LEARNING CENTER');
        $as->getStyle("A{$ar}")->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => $blue]], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
        $as->getRowDimension($ar)->setRowHeight(22); $ar++;

        $as->mergeCells("A{$ar}:{$lastAttCol}{$ar}");
        $as->setCellValue("A{$ar}", 'General Tinio, Nueva Ecija');
        $as->getStyle("A{$ar}")->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '666666']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
        $ar++;
        $as->mergeCells("A{$ar}:{$lastAttCol}{$ar}");
        $as->setCellValue("A{$ar}", 'ATTENDANCE SHEET');
        $as->getStyle("A{$ar}")->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $blue]], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
        $ar++;

        $ar++; // spacer
        foreach ([['School Year', $schoolYear, 'Term', $termLabel], ['Subject', $subjectName, 'Section', $section->name]] as [$l1,$v1,$l2,$v2]) {
            $as->setCellValue("A{$ar}", $l1); $as->mergeCells("B{$ar}:F{$ar}"); $as->setCellValue("B{$ar}", $v1);
            $as->setCellValue("H{$ar}", $l2); $as->mergeCells("I{$ar}:M{$ar}"); $as->setCellValue("I{$ar}", $v2);
            $attLabelStyle = ['font' => ['bold' => true, 'color' => ['rgb' => $blue]], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8f0fb']]];
            $as->getStyle("A{$ar}")->applyFromArray($attLabelStyle);
            $as->getStyle("H{$ar}")->applyFromArray($attLabelStyle);
            $ar++;
        }
        $ar++; // spacer

        // Legend
        $as->mergeCells("A{$ar}:{$lastAttCol}{$ar}");
        $as->setCellValue("A{$ar}", 'LEGEND:  P = Present  |  A = Absent  |  L = Late  |  E = Excused');
        $as->getStyle("A{$ar}")->applyFromArray(['font' => ['italic' => true, 'size' => 9, 'bold' => true, 'color' => ['rgb' => '555555']]]);
        $ar++;
        $ar++; // spacer

        // Column headers
        $attHeaderRow = $ar;
        $as->setCellValue("A{$ar}", '#');
        $as->setCellValue("B{$ar}", 'STUDENT NAME');
        $as->setCellValue("C{$ar}", 'LRN');
        for ($d = 1; $d <= 31; $d++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 3);
            $as->setCellValue("{$col}{$ar}", $d);
        }
        $as->setCellValue("{$colAI}{$ar}", "PRES.");
        $as->setCellValue("{$colAJ}{$ar}", "ABS.");
        $as->setCellValue("{$colAK}{$ar}", "LATE");
        $as->setCellValue("{$colAL}{$ar}", "EXC.");
        $as->setCellValue("{$colAM}{$ar}", "ATT. %");
        $as->getStyle("A{$ar}:{$lastAttCol}{$ar}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 9],
            'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '3a5a8c']]],
        ]);
        $as->getRowDimension($ar)->setRowHeight(22);
        $ar++;

        // Student attendance rows
        $dayStart = 'D';
        $dayEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(34); // AH
        foreach ($students as $i => $student) {
            $rowBg = $i % 2 === 0 ? 'FFFFFF' : 'f5f7fa';
            $as->setCellValue("A{$ar}", $i + 1);
            $as->setCellValue("B{$ar}", $student->name);
            $as->setCellValue("C{$ar}", $student->lrn ?? '');
            // D-AH: attendance cells (teacher marks P/A/L/E)
            // AI-AM: summary formulas
            $as->setCellValue("{$colAI}{$ar}", "=COUNTIF({$dayStart}{$ar}:{$dayEnd}{$ar},\"P\")");
            $as->setCellValue("{$colAJ}{$ar}", "=COUNTIF({$dayStart}{$ar}:{$dayEnd}{$ar},\"A\")");
            $as->setCellValue("{$colAK}{$ar}", "=COUNTIF({$dayStart}{$ar}:{$dayEnd}{$ar},\"L\")");
            $as->setCellValue("{$colAL}{$ar}", "=COUNTIF({$dayStart}{$ar}:{$dayEnd}{$ar},\"E\")");
            $as->setCellValue("{$colAM}{$ar}", "=IF({$colAI}{$ar}+{$colAJ}{$ar}+{$colAK}{$ar}+{$colAL}{$ar}=0,\"\",ROUND({$colAI}{$ar}/({$colAI}{$ar}+{$colAJ}{$ar}+{$colAK}{$ar}+{$colAL}{$ar})*100,1)&\"%\")");

            $as->getStyle("A{$ar}:{$lastAttCol}{$ar}")->applyFromArray([
                'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'b0b8c4']]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            // Name left-aligned
            $as->getStyle("B{$ar}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            // Summary cols: green tint
            $as->getStyle("{$colAI}{$ar}:{$colAM}{$ar}")->applyFromArray(['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']]]);
            $as->getRowDimension($ar)->setRowHeight(16);
            $ar++;
        }
        $as->freezePane("D{$attHeaderRow}");

        // ═══════════════════════════════════════════
        // SHEET 3 — DEPED REFERENCE
        // ═══════════════════════════════════════════
        $rs = $spreadsheet->createSheet();
        $rs->setTitle('DepEd Reference');
        $rs->getColumnDimension('A')->setWidth(28);
        $rs->getColumnDimension('B')->setWidth(24);
        $rs->getColumnDimension('C')->setWidth(20);
        $rs->getColumnDimension('D')->setWidth(20);

        $rr = 1;
        $rs->mergeCells("A{$rr}:D{$rr}");
        $rs->setCellValue("A{$rr}", 'DEPED K-12 GRADING REFERENCE (DepEd Order No. 8, s. 2015)');
        $rs->getStyle("A{$rr}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $blue]], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
        $rs->getRowDimension($rr)->setRowHeight(22); $rr++;

        $rr++; // spacer

        // Grading weights
        $rs->mergeCells("A{$rr}:D{$rr}");
        $rs->setCellValue("A{$rr}", 'GRADING WEIGHTS — GRADE 1 to 6');
        $rs->getStyle("A{$rr}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => $white]], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]]]);
        $rs->getRowDimension($rr)->setRowHeight(18); $rr++;

        foreach ([['Written Works (WW)', '25%', 'Quizzes, written activities, essays'],['Performance Tasks (PT)', '50%', 'Projects, experiments, oral reports'],['Quarterly Assessment (QA)', '25%', 'Quarterly exam / test']] as [$comp, $wt, $desc]) {
            $rs->setCellValue("A{$rr}", $comp);
            $rs->setCellValue("B{$rr}", $wt);
            $rs->setCellValue("C{$rr}", $desc);
            $rs->getStyle("A{$rr}:D{$rr}")->applyFromArray($thinBorder);
            $rs->getStyle("B{$rr}")->applyFromArray(['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
            $rr++;
        }

        $rr++; // spacer

        // Grade descriptors
        $rs->mergeCells("A{$rr}:D{$rr}");
        $rs->setCellValue("A{$rr}", 'GRADE DESCRIPTORS');
        $rs->getStyle("A{$rr}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => $white]], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]]]);
        $rs->getRowDimension($rr)->setRowHeight(18); $rr++;

        foreach ([
            ['90 – 100', 'Outstanding',               'O',  '4'],
            ['85 – 89',  'Very Satisfactory',          'VS', '3'],
            ['80 – 84',  'Satisfactory',               'S',  '2'],
            ['75 – 79',  'Fairly Satisfactory',        'FS', '1'],
            ['Below 75', 'Did Not Meet Expectations',  'DNME', '0'],
        ] as [$range, $desc, $code, $pts]) {
            $rs->setCellValue("A{$rr}", $range);
            $rs->setCellValue("B{$rr}", $desc);
            $rs->setCellValue("C{$rr}", $code);
            $rs->setCellValue("D{$rr}", 'Points: ' . $pts);
            $rs->getStyle("A{$rr}:D{$rr}")->applyFromArray($thinBorder);
            $bgMap = ['90 – 100' => 'E2EFDA', '85 – 89' => 'EBF5E1', '80 – 84' => 'F2F9EE', '75 – 79' => 'FFF9EC', 'Below 75' => 'FDE9E8'];
            $rs->getStyle("A{$rr}:D{$rr}")->applyFromArray(['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgMap[$range]]]]);
            $rr++;
        }

        $rr++; // spacer

        // Transmutation table
        $rs->mergeCells("A{$rr}:D{$rr}");
        $rs->setCellValue("A{$rr}", 'DEPED TRANSMUTATION TABLE (Initial Grade → Transmuted Grade)');
        $rs->getStyle("A{$rr}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => $white]], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]]]);
        $rs->getRowDimension($rr)->setRowHeight(18); $rr++;

        $rs->setCellValue("A{$rr}", 'Initial Grade Range');
        $rs->setCellValue("B{$rr}", 'Transmuted Grade');
        $rs->setCellValue("C{$rr}", 'Initial Grade Range');
        $rs->setCellValue("D{$rr}", 'Transmuted Grade');
        $rs->getStyle("A{$rr}:D{$rr}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEEAF1']], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'b0b8c4']]]]);
        $rr++;

        $transmutationTable = [
            ['100',         '100'], ['98.40 – 99.99', '99'],  ['96.80 – 98.39', '98'],
            ['95.20 – 96.79','97'], ['93.60 – 95.19', '96'],  ['92.00 – 93.59', '95'],
            ['90.40 – 91.99','94'], ['88.80 – 90.39', '93'],  ['87.20 – 88.79', '92'],
            ['85.60 – 87.19','91'], ['84.00 – 85.59', '90'],  ['82.40 – 83.99', '89'],
            ['80.80 – 82.39','88'], ['79.20 – 80.79', '87'],  ['77.60 – 79.19', '86'],
            ['76.00 – 77.59','85'], ['74.40 – 75.99', '84'],  ['72.80 – 74.39', '83'],
            ['71.20 – 72.79','82'], ['69.60 – 71.19', '81'],  ['68.00 – 69.59', '80'],
            ['66.40 – 67.99','79'], ['64.80 – 66.39', '78'],  ['63.20 – 64.79', '77'],
            ['61.60 – 63.19','76'], ['60.00 – 61.59', '75'],  ['56.00 – 59.99', '74'],
            ['52.00 – 55.99','73'], ['48.00 – 51.99', '72'],  ['44.00 – 47.99', '71'],
            ['40.00 – 43.99','70'], ['36.00 – 39.99', '69'],  ['32.00 – 35.99', '68'],
            ['28.00 – 31.99','67'], ['24.00 – 27.99', '66'],  ['20.00 – 23.99', '65'],
            ['Below 20.00',  '60'],
        ];

        $half = (int)ceil(count($transmutationTable) / 2);
        for ($ti = 0; $ti < $half; $ti++) {
            [$r1, $g1] = $transmutationTable[$ti];
            $rowBg = $ti % 2 === 0 ? 'FFFFFF' : 'f5f7fa';
            $rs->setCellValue("A{$rr}", $r1);
            $rs->setCellValue("B{$rr}", $g1);
            if (isset($transmutationTable[$ti + $half])) {
                [$r2, $g2] = $transmutationTable[$ti + $half];
                $rs->setCellValue("C{$rr}", $r2);
                $rs->setCellValue("D{$rr}", $g2);
            }
            $rs->getStyle("A{$rr}:D{$rr}")->applyFromArray([
                'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'b0b8c4']]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $rr++;
        }

        // ── Active sheet back to 0 ──
        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'tpl_') . '.xlsx';
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── IMPORT: parse CSV → save as draft ──

    public function importGrades(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
            'file'        => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this section.'], 403);
        }

        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);
        $validStudentIds = $section->students->pluck('id')->toArray();
        $isDescriptive   = \App\Models\Grade::isNurseryKinder($section->grade_level ?? '');

        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();

        $uploadedFile = $request->file('file');
        $extension    = strtolower($uploadedFile->getClientOriginalExtension());
        $rows         = [];
        $errors       = [];

        // ── Parse xlsx / xls using PhpSpreadsheet ──
        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadedFile->getRealPath());
                $sheet       = $spreadsheet->getActiveSheet();
                $headerFound = false;

                foreach ($sheet->getRowIterator() as $sheetRow) {
                    $cells = [];
                    foreach ($sheetRow->getCellIterator() as $cell) {
                        $cells[] = trim((string) $cell->getValue());
                    }
                    if (empty(array_filter($cells))) continue;

                    $firstCell = strtolower($cells[0] ?? '');

                    // Skip rows until we find the header
                    if (!$headerFound) {
                        // Detect header: first cell is '#' comment or 'student_id' or numeric (actual data row)
                        if (str_starts_with($firstCell, '#')) continue;
                        if ($firstCell === 'student_id' || $firstCell === '#') { $headerFound = true; continue; }
                        // If first cell looks like a number, treat it as a data row (no explicit header)
                        if (is_numeric($cells[0])) $headerFound = true;
                        else continue;
                    }

                    // Skip footer/summary rows (CLASS AVERAGE, notes, etc.) — column A must be a positive integer
                    if (!is_numeric($cells[0]) || (int)$cells[0] <= 0) continue;

                    $studentId = (int) ($cells[0] ?? 0);
                    // Export template: A=student_id, B=Name, C=LRN, D=Grade (index 3), E=Remarks
                    // Both Grade 1-6 and Nursery/Kinder read from column D (index 3)
                    $rawVal = strtoupper(trim($cells[3] ?? ''));

                    if (!in_array($studentId, $validStudentIds)) {
                        $errors[] = "Row with student_id {$studentId} does not belong to this section.";
                        continue;
                    }

                    if ($isDescriptive) {
                        if ($rawVal !== '' && !in_array($rawVal, \App\Models\Grade::DESCRIPTIVE_GRADES)) {
                            $errors[] = "Student ID {$studentId}: rating \"{$rawVal}\" is invalid (must be O, VS, S, FS, or DNME).";
                            continue;
                        }
                        $rows[] = ['student_id' => $studentId, 'grade' => null, 'descriptive_grade' => $rawVal !== '' ? $rawVal : null, 'name' => trim($cells[2] ?? '')];
                    } else {
                        if ($rawVal !== '' && (!is_numeric($rawVal) || (float)$rawVal < 0 || (float)$rawVal > 100)) {
                            $errors[] = "Student ID {$studentId}: grade \"{$rawVal}\" is invalid (must be 0–100 or blank).";
                            continue;
                        }
                        $rows[] = ['student_id' => $studentId, 'grade' => $rawVal !== '' ? (float) $rawVal : null, 'descriptive_grade' => null, 'name' => trim($cells[2] ?? '')];
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Could not read Excel file: ' . $e->getMessage()], 422);
            }

            // Skip to DB save section below
        } else {
        // ── Parse CSV / TXT ──
        $handle = fopen($uploadedFile->getRealPath(), 'r');
        if (!$handle) {
            return response()->json(['success' => false, 'message' => 'Could not read file.'], 422);
        }

        $headerFound = false;

        while (($row = fgetcsv($handle)) !== false) {
            // Skip comment/info rows
            if (isset($row[0]) && str_starts_with(trim($row[0]), '#')) continue;
            // Skip header row
            if (!$headerFound && isset($row[0]) && strtolower(trim($row[0])) === 'student_id') {
                $headerFound = true;
                continue;
            }
            if (!$headerFound) continue;

            $studentId = (int) ($row[0] ?? 0);
            $rawVal    = strtoupper(trim($row[3] ?? ''));

            if (!in_array($studentId, $validStudentIds)) {
                $errors[] = "Row with student_id {$studentId} does not belong to this section.";
                continue;
            }

            if ($isDescriptive) {
                if ($rawVal !== '' && !in_array($rawVal, \App\Models\Grade::DESCRIPTIVE_GRADES)) {
                    $errors[] = "Student ID {$studentId}: rating \"{$rawVal}\" is invalid (must be O, VS, S, FS, or DNME).";
                    continue;
                }
                $rows[] = ['student_id' => $studentId, 'grade' => null, 'descriptive_grade' => $rawVal !== '' ? $rawVal : null, 'name' => trim($row[2] ?? '')];
            } else {
                if ($rawVal !== '' && (!is_numeric($rawVal) || (float)$rawVal < 0 || (float)$rawVal > 100)) {
                    $errors[] = "Student ID {$studentId}: grade \"{$rawVal}\" is invalid (must be 0–100 or blank).";
                    continue;
                }
                $rows[] = ['student_id' => $studentId, 'grade' => $rawVal !== '' ? (float) $rawVal : null, 'descriptive_grade' => null, 'name' => trim($row[2] ?? '')];
            }
        }
        fclose($handle);
        } // end CSV else block

        if (empty($rows) && empty($errors)) {
            return response()->json(['success' => false, 'message' => 'No valid data rows found in file.'], 422);
        }

        DB::beginTransaction();
        try {
            // Remove previous drafts for same teacher/section/subject/term/school_year
            Grade::where('teacher_id', $teacher->id)
                ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
                ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
                ->where('term', $request->term)
                ->where('school_year', $schoolYear)
                ->where('status', 'draft')
                ->whereIn('student_id', $validStudentIds)
                ->delete();

            foreach ($rows as $row) {
                $descriptive = $row['descriptive_grade'] ?? null;
                $gradeVal    = $row['grade'] ?? null;
                Grade::create([
                    'student_id'        => $row['student_id'],
                    'teacher_id'        => $teacher->id,
                    'subject_id'        => $request->subject_id ?? null,
                    'term'              => $request->term,
                    'school_year'       => $schoolYear,
                    'grade'             => $gradeVal,
                    'descriptive_grade' => $descriptive,
                    'remarks'           => $descriptive
                        ? \App\Models\Grade::getDescriptiveLabel($descriptive)
                        : Grade::getRemarks($gradeVal),
                    'status'            => 'draft',
                ]);
            }
            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => count($rows) . ' student grades saved as draft.',
                'draft_rows'=> $rows,
                'errors'    => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    // ── GET DRAFTS for review ──

    public function getDrafts(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();

        $drafts = Grade::where('teacher_id', $teacher->id)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->where('term', $request->term)
            ->where('school_year', $schoolYear)
            ->where('status', 'draft')
            ->with('student:id,name,lrn')
            ->get()
            ->map(fn($g) => [
                'draft_id'  => $g->id,
                'student_id'=> $g->student_id,
                'name'      => $g->student?->name ?? '—',
                'lrn'       => $g->student?->lrn ?? '',
                'grade'     => $g->grade,
                'remarks'   => $g->remarks,
            ]);

        return response()->json(['success' => true, 'data' => $drafts]);
    }

    // ── SUBMIT DRAFTS → move from draft to submitted ──

    public function submitDraft(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this section.'], 403);
        }

        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();
        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);
        $validStudentIds = $section->students->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            $drafts = Grade::where('teacher_id', $teacher->id)
                ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
                ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
                ->where('term', $request->term)
                ->where('school_year', $schoolYear)
                ->where('status', 'draft')
                ->whereIn('student_id', $validStudentIds)
                ->get();

            if ($drafts->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No draft grades to submit.'], 422);
            }

            foreach ($drafts as $draft) {
                // Remove any existing submitted or rejected grade for same student/subject/term
                Grade::where('student_id', $draft->student_id)
                    ->where('teacher_id', $teacher->id)
                    ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
                    ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
                    ->where('term', $request->term)
                    ->where('school_year', $schoolYear)
                    ->whereIn('status', ['submitted', 'rejected'])
                    ->delete();

                $draft->update(['status' => 'submitted']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $drafts->count() . ' grades submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Submit failed: ' . $e->getMessage()], 500);
        }
    }

    // ── DISCARD DRAFTS ──

    public function discardDraft(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer|min:1|max:3',
            'school_year' => 'nullable|string|max:20',
        ]);

        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();
        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);
        $validStudentIds = $section->students->pluck('id')->toArray();

        $deleted = Grade::where('teacher_id', $teacher->id)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->where('term', $request->term)
            ->where('school_year', $schoolYear)
            ->where('status', 'draft')
            ->whereIn('student_id', $validStudentIds)
            ->delete();

        return response()->json(['success' => true, 'message' => $deleted . ' draft grades discarded.']);
    }

    // ── ATTENDANCE ──

    public function loadAttendance(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date'       => 'required|date',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this section.'], 403);
        }

        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);

        $attendances = Attendance::where('teacher_id', $teacher->id)
            ->where('section_id', $request->section_id)
            ->where('date', $request->date)
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->get()->keyBy('student_id');

        $records = $section->students->map(fn($student) => [
            'student_id' => $student->id,
            'name'       => $student->name,
            'lrn'        => $student->lrn ?? '',
            'status'     => $attendances->get($student->id)?->status ?? '',
            'remarks'    => $attendances->get($student->id)?->remarks ?? '',
        ]);

        return response()->json(['success' => true, 'data' => $records, 'section' => $section->name]);
    }

    public function saveAttendance(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'section_id'              => 'required|exists:sections,id',
            'subject_id'              => 'nullable|exists:subjects,id',
            'date'                    => 'required|date',
            'records'                 => 'required|array',
            'records.*.student_id'    => 'required|exists:users,id',
            'records.*.status'        => 'required|in:present,absent,late,excused',
            'records.*.remarks'       => 'nullable|string',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $validated['section_id'])) {
            return response()->json(['success' => false, 'message' => 'Not assigned to this section.'], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['records'] as $rec) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $rec['student_id'],
                        'section_id' => $validated['section_id'],
                        'subject_id' => $validated['subject_id'] ?? null,
                        'teacher_id' => $teacher->id,
                        'date'       => $validated['date'],
                    ],
                    ['status' => $rec['status'], 'remarks' => $rec['remarks'] ?? null]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Attendance saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ── ANNOUNCEMENTS ──

    public function storeAnnouncement(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'audience'   => 'required|in:all,section,parents,teachers',
            'section_id' => 'nullable|exists:sections,id',
            'category'   => 'required|in:academic,reminder,activity,general,enrollment',
        ]);

        $announcement = Announcement::create([
            'teacher_id' => $teacher->id,
            'title'      => $request->title,
            'content'    => $request->content,
            'audience'   => $request->audience,
            'section_id' => $request->section_id,
            'category'   => $request->category,
        ]);

        return response()->json(['success' => true, 'data' => $announcement], 201);
    }

    public function getAnnouncements()
    {
        $teacher = Auth::user();
        $announcements = Announcement::where('teacher_id', $teacher->id)
            ->with('section:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $announcements]);
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        if ($announcement->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $announcement->delete();
        return response()->json(['success' => true]);
    }

    // ── PARENT-TEACHER CONFERENCE ──

    public function storePtc(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'student_id'   => 'required|exists:users,id',
            'guardian_name'=> 'nullable|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required|date_format:H:i',
            'purpose'      => 'nullable|string|max:255',
            'venue'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $ptc = ParentTeacherConference::create([
            'teacher_id'   => $teacher->id,
            'student_id'   => $request->student_id,
            'guardian_name'=> $request->guardian_name,
            'meeting_date' => $request->meeting_date,
            'meeting_time' => $request->meeting_time,
            'purpose'      => $request->purpose,
            'venue'        => $request->venue,
            'notes'        => $request->notes,
        ]);

        return response()->json(['success' => true, 'data' => $ptc->load('student:id,name')], 201);
    }

    public function getPtcMeetings()
    {
        $meetings = ParentTeacherConference::where('teacher_id', Auth::id())
            ->with('student:id,name')
            ->orderBy('meeting_date', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $meetings]);
    }

    public function updatePtcStatus(Request $request, ParentTeacherConference $ptc)
    {
        if ($ptc->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $request->validate(['status' => 'required|in:scheduled,completed,cancelled,rescheduled']);
        $ptc->update(['status' => $request->status]);
        return response()->json(['success' => true, 'data' => $ptc]);
    }

    // ── REPORTS ──

    public function getClassGradeReport(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'term'       => 'nullable|integer|min:1|max:3',
        ]);

        $section = Section::findOrFail($request->section_id);
        $section = $this->loadSectionStudents($section);
        $schoolYear = $this->getCurrentSchoolYear();

        $gradeQuery = Grade::where('teacher_id', $teacher->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'submitted')
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->whereIn('student_id', $section->students->pluck('id'));

        $grades = $gradeQuery->get()->groupBy('student_id');

        $report = $section->students->map(fn($student) => [
            'student_id' => $student->id,
            'name'       => $student->name,
            'lrn'        => $student->lrn ?? '',
            'grades'     => ($grades->get($student->id) ?? collect())->map(fn($g) => [
                'term'    => $g->term,
                'grade'   => $g->grade,
                'remarks' => $g->remarks,
            ])->values(),
        ]);

        return response()->json(['success' => true, 'data' => $report, 'section' => $section->name]);
    }

    public function getStudentReportCard(Request $request)
    {
        $teacher = Auth::user();
        $request->validate(['student_id' => 'required|exists:users,id']);

        $student = User::select('id', 'name', 'lrn')->findOrFail($request->student_id);
        $schoolYear = $this->getCurrentSchoolYear();

        $grades = Grade::where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'submitted')
            ->with('subject:id,name,code')
            ->orderBy('term')
            ->get()
            ->groupBy('term');

        return response()->json([
            'success' => true,
            'data'    => ['student' => $student, 'grades_by_term' => $grades],
        ]);
    }

    // ── SF9: Learner's Progress Report Card (PDF) ──
    public function printSF9(Request $request, User $student)
    {
        $teacher    = Auth::user();
        $schoolYear = $request->query('school_year') ?: $this->getCurrentSchoolYear();
        $term       = (int) $request->query('term', 0); // 0 = all terms

        // Enrollment for this school year
        $enrollment = $student->enrollments()
            ->where('school_year', $schoolYear)
            ->latest('id')->first()
            ?? $student->latestEnrollment;

        $gradeLevel  = $enrollment ? ($enrollment->student_data['grade_level'] ?? $enrollment->grade_level) : null;
        $sectionName = $enrollment ? $enrollment->section : '—';

        $glMap = [
            'nursery'      => 'Nursery',
            'kindergarten' => 'Kindergarten',
            'grade1'       => 'Grade 1',
            'grade2'       => 'Grade 2',
            'grade3'       => 'Grade 3',
            'grade4'       => 'Grade 4',
            'grade5'       => 'Grade 5',
            'grade6'       => 'Grade 6',
        ];
        $nextGlMap = [
            'nursery'      => 'Kindergarten',
            'kindergarten' => 'Grade 1',
            'grade1'       => 'Grade 2',
            'grade2'       => 'Grade 3',
            'grade3'       => 'Grade 4',
            'grade4'       => 'Grade 5',
            'grade5'       => 'Grade 6',
            'grade6'       => '—',
        ];

        $gradeLabel   = $glMap[$gradeLevel] ?? ucfirst($gradeLevel ?? '—');
        $nextGradeLabel = $nextGlMap[$gradeLevel] ?? '—';
        $isDescriptive  = Grade::isNurseryKinder($gradeLevel ?? '');

        // Profile / address / guardian
        $profile  = $student->profile;
        $guardian = $student->guardian;
        $addrObj  = $student->address;
        $address  = $addrObj
            ? implode(', ', array_filter([$addrObj->street_address, $addrObj->barangay, $addrObj->municipality ?: $addrObj->city, $addrObj->province]))
            : null;

        // Age
        $age = $profile?->birthdate
            ? \Carbon\Carbon::parse($profile->birthdate)->age
            : null;

        // All subjects for this grade level
        $allSubjects = Subject::where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->orderBy('name')
            ->get();

        // Grades for all 3 terms, include approved
        $gradesRaw = Grade::where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'approved'])
            ->where('school_year', $schoolYear)
            ->orderByRaw("FIELD(status,'approved','submitted') ASC")
            ->get()
            ->groupBy('term');

        // Build subjects array with term data
        $subjects = $allSubjects->map(function ($sub) use ($gradesRaw, $isDescriptive) {
            $t1 = $gradesRaw->get(1)?->firstWhere('subject_id', $sub->id);
            $t2 = $gradesRaw->get(2)?->firstWhere('subject_id', $sub->id);
            $t3 = $gradesRaw->get(3)?->firstWhere('subject_id', $sub->id);

            if ($isDescriptive) {
                return [
                    'name'       => $sub->name,
                    'term1_desc' => $t1?->descriptive_grade,
                    'term2_desc' => $t2?->descriptive_grade,
                    'term3_desc' => $t3?->descriptive_grade,
                ];
            }
            return [
                'name'  => $sub->name,
                'term1' => $t1?->grade,
                'term2' => $t2?->grade,
                'term3' => $t3?->grade,
            ];
        })->toArray();

        // GWA calculation (Grade 1-6 only)
        $gwa = $term1Avg = $term2Avg = $term3Avg = null;
        if (!$isDescriptive) {
            $calcAvg = function ($termNum) use ($gradesRaw) {
                $grades = $gradesRaw->get($termNum)?->pluck('grade')->filter(fn($v) => $v !== null)->values();
                return ($grades && $grades->count()) ? round($grades->sum() / $grades->count(), 2) : null;
            };
            $term1Avg = $calcAvg(1);
            $term2Avg = $calcAvg(2);
            $term3Avg = $calcAvg(3);
            $avgs = array_filter([$term1Avg, $term2Avg, $term3Avg], fn($v) => $v !== null);
            $gwa  = count($avgs) ? round(array_sum($avgs) / count($avgs)) : null;
        }

        // Action taken based on GWA
        $actionTaken = null;
        if (!$isDescriptive && $gwa !== null) {
            $actionTaken = $gwa >= 75 ? ($gradeLevel === 'grade6' ? 'Graduated' : 'Promoted') : 'Retained';
        }

        // Teacher name
        $teacherName = $teacher->name;

        $data = compact(
            'student', 'profile', 'guardian', 'address', 'age',
            'gradeLabel', 'nextGradeLabel', 'sectionName', 'schoolYear',
            'isDescriptive', 'subjects', 'gwa', 'term1Avg', 'term2Avg', 'term3Avg',
            'actionTaken', 'teacherName'
        );

        $html = view('teacher.sf9', $data)->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('letter', 'portrait')
            ->setOptions(['defaultFont' => 'Arial', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $filename = 'SF9_' . str_replace(' ', '_', $student->name) . '_' . str_replace('-', '_', $schoolYear) . '.pdf';

        return $pdf->download($filename);
    }

    // ── SF5: Report on Promotions and Level of Proficiency (Excel) ──
    public function exportSF5(Request $request)
    {
        $teacher = Auth::user();
        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'school_year' => 'nullable|string|max:20',
        ]);

        if (!$this->teacherOwnsSection($teacher->id, $request->section_id)) {
            abort(403, 'Not assigned to this section.');
        }

        $section    = Section::findOrFail($request->section_id);
        $section    = $this->loadSectionStudents($section);
        $students   = $section->students;
        $schoolYear = filled($request->school_year) ? $request->school_year : $this->getCurrentSchoolYear();
        $gradeLevel = $section->grade_level ?? '';

        $glMap = [
            'nursery'=>'Nursery','kindergarten'=>'Kindergarten',
            'grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3',
            'grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6',
        ];
        $gradeLabel    = $glMap[$gradeLevel] ?? ucfirst($gradeLevel);
        $isDescriptive = Grade::isNurseryKinder($gradeLevel);

        $subjects = Subject::where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->orderBy('name')
            ->get();

        // Grades for all terms (approved/submitted)
        $allGrades = Grade::whereIn('student_id', $students->pluck('id'))
            ->where('school_year', $schoolYear)
            ->whereIn('status', ['submitted', 'approved'])
            ->get()
            ->groupBy('student_id');

        // ── Build Excel ──
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('SF5')
            ->setSubject("SF5 — {$section->name} — {$schoolYear}")
            ->setCreator($teacher->name);

        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle('SF5');

        $blue      = '1a3a6c';
        $white     = 'FFFFFF';
        $lightBlue = 'd9e1f2';
        $gold      = 'F5A623';

        $thin = ['borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color'       => ['rgb' => '000000'],
        ]]];

        // Logo
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('ILC Logo')->setPath($logoPath)->setHeight(55)
                ->setCoordinates('A1')->setOffsetX(4)->setOffsetY(4)->setWorksheet($ws);
        }

        $subjectCount = $subjects->count();
        $lastDataCol  = 4 + ($subjectCount * 3); // No + LRN + Name + Sex + (T1,T2,Final)*subjects + Avg + Action
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol + 2);

        // Row 1 - School name
        $ws->mergeCells("B1:{$lastColLetter}1");
        $ws->setCellValue('B1', 'IEMELIF LEARNING CENTER');
        $ws->getStyle('B1')->applyFromArray(['font' => ['bold'=>true,'size'=>14,'color'=>['rgb'=>$blue]],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $ws->getRowDimension(1)->setRowHeight(28);

        // Row 2 - Address
        $ws->mergeCells("B2:{$lastColLetter}2");
        $ws->setCellValue('B2', 'General Tinio, Nueva Ecija | Schools Division of Nueva Ecija, Region III');
        $ws->getStyle('B2')->applyFromArray(['font' => ['size'=>9,'italic'=>true,'color'=>['rgb'=>'555555']],'alignment'=>['horizontal'=>'center']]);

        // Row 3 - Form title
        $ws->mergeCells("A3:{$lastColLetter}3");
        $ws->setCellValue('A3', 'School Form 5 (SF5) — Report on Promotions and Level of Proficiency');
        $ws->getStyle("A3:{$lastColLetter}3")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>11,'color'=>['rgb'=>$white]],
            'fill'      => ['fillType'=>'solid','startColor'=>['rgb'=>$blue]],
            'alignment' => ['horizontal'=>'center','vertical'=>'center'],
        ]);
        $ws->getRowDimension(3)->setRowHeight(20);

        // Row 4 - Divider
        $ws->mergeCells("A4:{$lastColLetter}4");
        $ws->getStyle("A4:{$lastColLetter}4")->applyFromArray(['fill'=>['fillType'=>'solid','startColor'=>['rgb'=>$gold]]]);
        $ws->getRowDimension(4)->setRowHeight(4);

        // Rows 5-7 - Meta
        $r = 5;
        foreach ([
            ['School Year', $schoolYear, 'Grade Level', $gradeLabel],
            ['Section', $section->name, 'Adviser', $teacher->name],
            ['Generated', now()->format('F d, Y'), 'Total Learners', $students->count()],
        ] as [$l1,$v1,$l2,$v2]) {
            $ws->setCellValue("A{$r}", $l1 . ':');
            $ws->setCellValue("B{$r}", $v1);
            $ws->setCellValue("D{$r}", $l2 . ':');
            $ws->setCellValue("E{$r}", $v2);
            $ws->getStyle("A{$r}")->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>$blue]],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>$lightBlue]]]);
            $r++;
        }
        $r++;

        // Column headers
        $colIdx = 1;
        $headerRow = $r;
        $hStyle = ['font'=>['bold'=>true,'size'=>8,'color'=>['rgb'=>$white]],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>$blue]],'alignment'=>['horizontal'=>'center','vertical'=>'center','wrapText'=>true],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'000000']]]];

        foreach ([['No.','A'],['LRN','B'],['Learner\'s Name','C'],['Sex','D']] as [$hdr, $col]) {
            $ws->setCellValue("{$col}{$headerRow}", $hdr);
            $ws->getStyle("{$col}{$headerRow}")->applyFromArray($hStyle);
        }
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(16);
        $ws->getColumnDimension('C')->setWidth(28);
        $ws->getColumnDimension('D')->setWidth(6);
        $ws->getRowDimension($headerRow)->setRowHeight(30);

        $subColStart = 5;
        foreach ($subjects as $sub) {
            // Merge 3 columns for subject name
            $c1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subColStart);
            $c3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subColStart + 2);
            $ws->mergeCells("{$c1}{$headerRow}:{$c3}{$headerRow}");
            $ws->setCellValue("{$c1}{$headerRow}", $sub->name);
            $ws->getStyle("{$c1}{$headerRow}:{$c3}{$headerRow}")->applyFromArray($hStyle);

            // Sub-headers on next row
            $subHeaderRow = $headerRow + 1;
            foreach (['T1','T2','T3'] as $ti => $tlabel) {
                $tc = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subColStart + $ti);
                $ws->setCellValue("{$tc}{$subHeaderRow}", $tlabel);
                $ws->getStyle("{$tc}{$subHeaderRow}")->applyFromArray([
                    'font'=>['bold'=>true,'size'=>7,'color'=>['rgb'=>$white]],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'2c5282']],
                    'alignment'=>['horizontal'=>'center'],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'000000']]],
                ]);
                $ws->getColumnDimension($tc)->setWidth(7);
            }
            $subColStart += 3;
        }

        // Average + Action Taken headers
        $avgCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subColStart);
        $actionCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subColStart + 1);
        $ws->setCellValue("{$avgCol}{$headerRow}", 'Final Avg');
        $ws->setCellValue("{$actionCol}{$headerRow}", 'Action Taken');
        $ws->getStyle("{$avgCol}{$headerRow}")->applyFromArray($hStyle);
        $ws->getStyle("{$actionCol}{$headerRow}")->applyFromArray($hStyle);
        $ws->getColumnDimension($avgCol)->setWidth(9);
        $ws->getColumnDimension($actionCol)->setWidth(14);

        // Merge static header cols across sub-header row
        $subHeaderRow = $headerRow + 1;
        foreach (['A','B','C','D'] as $col) {
            $ws->mergeCells("{$col}{$headerRow}:{$col}{$subHeaderRow}");
        }
        $ws->mergeCells("{$avgCol}{$headerRow}:{$avgCol}{$subHeaderRow}");
        $ws->mergeCells("{$actionCol}{$headerRow}:{$actionCol}{$subHeaderRow}");

        $r = $subHeaderRow + 1;

        // Data rows
        $rowNum = 1;
        foreach ($students as $stu) {
            $stuGrades = $allGrades->get($stu->id) ?? collect();

            $ws->setCellValue("A{$r}", $rowNum++);
            $ws->setCellValue("B{$r}", $stu->lrn ?? '');
            $ws->setCellValue("C{$r}", $stu->name);
            $profile = $stu->profile;
            $ws->setCellValue("D{$r}", $profile ? strtoupper(substr($profile->gender ?? '', 0, 1)) : '');

            $termAvgs = [];
            $subCol   = 5;
            foreach ($subjects as $sub) {
                foreach ([1,2,3] as $term) {
                    $g = $stuGrades->where('subject_id', $sub->id)->where('term', $term)->first();
                    $tc = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($subCol + $term - 1);
                    if ($isDescriptive) {
                        $ws->setCellValue("{$tc}{$r}", $g?->descriptive_grade ?? '');
                    } else {
                        $ws->setCellValue("{$tc}{$r}", $g?->grade ?? '');
                    }
                }
                // Per-subject average (T1+T2+T3)/3 for avg column
                $subGrades = $stuGrades->where('subject_id', $sub->id)->pluck('grade')->filter()->values();
                if ($subGrades->count()) $termAvgs[] = $subGrades->sum() / $subGrades->count();
                $subCol += 3;
            }

            // Final average
            $avg = count($termAvgs) ? round(array_sum($termAvgs) / count($termAvgs)) : null;
            $ws->setCellValue("{$avgCol}{$r}", $avg ?? '');
            if ($avg !== null) {
                $ws->getStyle("{$avgCol}{$r}")->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=> $avg >= 75 ? '155724' : '721c24']]]);
            }

            // Action taken
            $action = '';
            if (!$isDescriptive && $avg !== null) {
                if ($section->grade_level === 'grade6') {
                    $action = $avg >= 75 ? 'Graduated' : 'Retained';
                } else {
                    $action = $avg >= 75 ? 'Promoted' : 'Retained';
                }
            }
            $ws->setCellValue("{$actionCol}{$r}", $action);

            // Row style
            $rowBg = ($rowNum % 2 === 0) ? 'f7f8fc' : 'FFFFFF';
            $ws->getStyle("A{$r}:{$actionCol}{$r}")->applyFromArray([
                'fill'    => ['fillType'=>'solid','startColor'=>['rgb'=>$rowBg]],
                'borders' => ['allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'cccccc']]],
                'font'    => ['size'=>8],
            ]);
            $ws->getStyle("C{$r}")->applyFromArray(['alignment'=>['horizontal'=>'left']]);
            $ws->getStyle("A{$r}:{$actionCol}{$r}")->getAlignment()->setVertical('center');
            $ws->getRowDimension($r)->setRowHeight(15);
            $r++;
        }

        // Summary row
        $r++;
        $ws->mergeCells("A{$r}:C{$r}");
        $ws->setCellValue("A{$r}", 'SUMMARY:');
        $ws->getStyle("A{$r}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['rgb'=>$blue]]]);

        $promoted  = $students->filter(fn($s) => !$isDescriptive)->count(); // simplified
        $ws->setCellValue("D{$r}", "Total: {$students->count()} learners");
        $ws->getStyle("A{$r}:D{$r}")->applyFromArray(['fill'=>['fillType'=>'solid','startColor'=>['rgb'=>$lightBlue]]]);

        // Freeze panes
        $ws->freezePane('C' . ($subHeaderRow + 1));

        $filename = 'SF5_' . preg_replace('/[^A-Za-z0-9\-]/', '-', $section->name) . '_' . str_replace('-', '_', $schoolYear) . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function sendPasswordOtp(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
        }

        OtpVerification::where('email', $user->email)->delete();

        $code  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(40);

        OtpVerification::create([
            'email'      => $user->email,
            'code'       => Hash::make($code),
            'token'      => $token,
            'attempts'   => 0,
            'verified'   => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        session(['pwd_change_hash_' . $token => Hash::make($request->password)]);

        Mail::to($user->email)->send(new PasswordChangeOtpMail($code, $user->name));

        return response()->json(['success' => true, 'token' => $token, 'email' => $user->email]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'otp_token' => 'required|string',
            'otp_code'  => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $otp  = OtpVerification::where('token', $request->otp_token)
                    ->where('email', $user->email)
                    ->first();

        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['otp_code' => 'OTP expired or invalid. Please request a new one.'])
                         ->with('settings_tab', 'password');
        }

        if ($otp->hasExceededAttempts()) {
            $otp->delete();
            return back()->withErrors(['otp_code' => 'Too many incorrect attempts. Please request a new OTP.'])
                         ->with('settings_tab', 'password');
        }

        if (!Hash::check($request->otp_code, $otp->code)) {
            $otp->increment('attempts');
            return back()->withErrors(['otp_code' => 'Incorrect OTP. ' . (2 - $otp->attempts) . ' attempt(s) remaining.'])
                         ->with('otp_token', $request->otp_token)
                         ->with('settings_tab', 'password');
        }

        $hashKey = 'pwd_change_hash_' . $request->otp_token;
        $newHash = session($hashKey);

        if (!$newHash) {
            return back()->withErrors(['otp_code' => 'Session expired. Please start over.'])
                         ->with('settings_tab', 'password');
        }

        $user->update(['password' => $newHash]);
        $otp->delete();
        session()->forget($hashKey);

        return back()->with('password_success', 'Password changed successfully!')
                     ->with('settings_tab', 'password');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('teacher-photos', 'public');
        $user->update(['profile_photo' => $path]);

        return back()->with('photo_success', 'Profile photo updated successfully.')
                     ->with('settings_tab', 'photo');
    }
}
