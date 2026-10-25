<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Teacher grade entry and the Excel export list students A–Z. The export shows No. | LRN | Name | Grade | Remarks
 * (+ the student ID in a hidden column F) with posted (incl. approved) grades, and a filled-in export imports back
 * as drafts — as .xlsx, as a .csv copy, as a hand-made sheet with LRNs only, or as an older export.
 */
class TeacherClassListOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private Section $section;
    private Subject $math;
    private array $ids = [];

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->teacher = User::factory()->teacher()->create();
        $this->section = Section::create(['name' => 'Rizal', 'grade_level' => 'grade4', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $this->math    = Subject::create(['name' => 'Math', 'code' => 'M4', 'grade_level' => 'grade4', 'is_active' => true]);
        $this->section->subjects()->attach($this->math->id);
        TeacherAssignment::create(['teacher_id' => $this->teacher->id, 'section_id' => $this->section->id, 'is_advisory' => true, 'school_year' => '2026-2027']);

        // Added out of order on purpose (and one lower-case name); Carlos has no LRN yet
        foreach (['Carlos Zamora' => null, 'ana Lopez' => '105432110001', 'Ben Cruz' => '105432110002'] as $name => $lrn) {
            $enr = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade4', 'section' => 'Rizal', 'status' => 'enrolled']);
            User::whereKey($enr->user_id)->update(['name' => $name, 'lrn' => $lrn]);
            $this->section->students()->attach($enr->user_id);
            $this->ids[$name] = $enr->user_id;
        }
    }

    private function scope(): array
    {
        return ['section_id' => $this->section->id, 'subject_id' => $this->math->id, 'term' => 1, 'school_year' => '2026-2027'];
    }

    private function exportSheet(): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $res = $this->actingAs($this->teacher, 'web')->get('/teacher/grades/export?' . http_build_query($this->scope()))->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'exp') . '.xlsx';
        file_put_contents($path, $res->streamedContent());
        return \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet();
    }

    private function headerRow($ws): int
    {
        foreach ($ws->getRowIterator() as $row) {
            if ($ws->getCell('A' . $row->getRowIndex())->getValue() === 'NO.') return $row->getRowIndex();
        }
        $this->fail('NO. header not found');
    }

    private function import(string $path, string $name): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->teacher, 'web')->post('/teacher/grades/import', $this->scope() + [
            'file' => new UploadedFile($path, $name, null, null, true)], ['Accept' => 'application/json']);
    }

    private function draft(string $student)
    {
        return Grade::where('student_id', $this->ids[$student])->where('status', 'draft')->value('grade');
    }

    public function test_grade_entry_lists_students_alphabetically(): void
    {
        $names = collect($this->actingAs($this->teacher, 'web')->postJson('/teacher/grades/load-class', $this->scope())->assertOk()->json('data'))->pluck('name')->all();
        $this->assertSame(['ana Lopez', 'Ben Cruz', 'Carlos Zamora'], $names);
    }

    public function test_export_layout_is_alphabetical_with_lrn_and_approved_grades(): void
    {
        Grade::create(['student_id' => $this->ids['Ben Cruz'], 'teacher_id' => $this->teacher->id, 'subject_id' => $this->math->id, 'term' => 1,
            'school_year' => '2026-2027', 'grade' => 88, 'remarks' => 'Passed', 'status' => 'approved']);

        $ws = $this->exportSheet();
        $h = $this->headerRow($ws);
        $this->assertSame(['NO.', 'LRN', 'STUDENT NAME', 'GRADE (Edit Only)', 'REMARKS', 'STUDENT ID'],
            array_map(fn($c) => $ws->getCell("{$c}{$h}")->getValue(), ['A', 'B', 'C', 'D', 'E', 'F']));
        $this->assertFalse($ws->getColumnDimension('F')->getVisible(), 'the student ID column is hidden');

        $rows = [];
        for ($r = $h + 1; $r <= $h + 3; $r++) {
            $d = $ws->getCell("D{$r}")->getValue();
            $rows[] = [(int) $ws->getCell("A{$r}")->getValue(), (string) $ws->getCell("B{$r}")->getValue(), $ws->getCell("C{$r}")->getValue(),
                $d === null ? null : (int) $d, (int) $ws->getCell("F{$r}")->getValue()];
        }
        $this->assertSame([
            [1, '105432110001', 'ana Lopez', null, $this->ids['ana Lopez']],
            [2, '105432110002', 'Ben Cruz', 88, $this->ids['Ben Cruz']],
            [3, '', 'Carlos Zamora', null, $this->ids['Carlos Zamora']],
        ], $rows);
        $this->assertSame(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING, $ws->getCell('B' . ($h + 1))->getDataType(), 'LRN kept as text (all 12 digits)');
    }

    public function test_filled_export_imports_back_as_xlsx_and_as_csv(): void
    {
        $ws = $this->exportSheet();
        $h = $this->headerRow($ws);
        $ws->setCellValue('D' . ($h + 1), 91);   // ana
        $ws->setCellValue('D' . ($h + 3), 79);   // Carlos — no LRN, matched by the hidden student ID

        $xlsx = tempnam(sys_get_temp_dir(), 'imp') . '.xlsx';
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ws->getParent()))->save($xlsx);
        $res = $this->import($xlsx, 'grades.xlsx')->assertOk();
        $this->assertSame([], $res->json('errors'));
        $this->assertEquals(91, $this->draft('ana Lopez'));
        $this->assertEquals(79, $this->draft('Carlos Zamora'));

        // A .csv copy of the same sheet (CLASS AVERAGE + footer rows included) — no bogus errors
        $ws->setCellValue('D' . ($h + 1), 95);
        $csv = tempnam(sys_get_temp_dir(), 'imp') . '.csv';
        (new \PhpOffice\PhpSpreadsheet\Writer\Csv($ws->getParent()))->setPreCalculateFormulas(false)->save($csv);
        $res = $this->import($csv, 'grades.csv')->assertOk();
        $this->assertSame([], $res->json('errors'));
        $this->assertEquals(95, $this->draft('ana Lopez'));
        $this->assertSame(1, Grade::where('student_id', $this->ids['ana Lopez'])->count(), 'the re-import replaced the draft');
    }

    public function test_sheet_with_lrn_only_is_matched_by_lrn(): void
    {
        $csv = tempnam(sys_get_temp_dir(), 'imp') . '.csv';
        file_put_contents($csv, "LRN,Student Name,Grade\n105432110002,Ben Cruz,84\n999999999999,Somebody Else,90\n");
        $res = $this->import($csv, 'grades.csv')->assertOk();
        $this->assertEquals(84, $this->draft('Ben Cruz'));
        $this->assertSame(['Somebody Else: LRN 999999999999 does not belong to this section.'], $res->json('errors'));
    }

    public function test_reimported_export_skips_posted_grades_and_blank_cells(): void
    {
        Grade::create(['student_id' => $this->ids['Ben Cruz'], 'teacher_id' => $this->teacher->id, 'subject_id' => $this->math->id, 'term' => 1,
            'school_year' => '2026-2027', 'grade' => 88, 'remarks' => 'Passed', 'status' => 'approved']);

        // Export (Ben's approved 88 is in it), fill only ana, leave Carlos blank, import it back
        $ws = $this->exportSheet();
        $h = $this->headerRow($ws);
        $ws->setCellValue('D' . ($h + 1), 90);
        $xlsx = tempnam(sys_get_temp_dir(), 'imp') . '.xlsx';
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ws->getParent()))->save($xlsx);
        $res = $this->import($xlsx, 'grades.xlsx')->assertOk();

        $this->assertSame(1, $res->json('draft_count'));
        $this->assertStringContainsString('1 skipped because they are already posted', $res->json('message'));
        $this->assertEquals(90, $this->draft('ana Lopez'));
        $this->assertSame(0, Grade::where('student_id', $this->ids['Ben Cruz'])->where('status', 'draft')->count(), 'no draft over the approved grade');
        $this->assertSame(0, Grade::where('student_id', $this->ids['Carlos Zamora'])->count(), 'a blank cell creates no draft');

        // Blanking ana's cell in a later import clears her draft
        $ws->setCellValue('D' . ($h + 1), null);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ws->getParent()))->save($xlsx);
        $this->import($xlsx, 'grades.xlsx')->assertOk();
        $this->assertSame(0, Grade::where('student_id', $this->ids['ana Lopez'])->count());
    }

    public function test_excel_csv_utf8_file_with_bom_imports(): void
    {
        $csv = tempnam(sys_get_temp_dir(), 'imp') . '.csv';
        file_put_contents($csv, "\xEF\xBB\xBFstudent_id,STUDENT NAME,LRN,GRADE (Edit Only),REMARKS\n{$this->ids['Carlos Zamora']},Carlos Zamora,,77,\n");
        $res = $this->import($csv, 'grades.csv')->assertOk();
        $this->assertSame([], $res->json('errors'));
        $this->assertEquals(77, $this->draft('Carlos Zamora'));
    }

    public function test_older_export_layout_still_imports(): void
    {
        // student_id | STUDENT NAME | LRN | GRADE | REMARKS — the layout exported before this change
        $csv = tempnam(sys_get_temp_dir(), 'imp') . '.csv';
        file_put_contents($csv, "student_id,STUDENT NAME,LRN,GRADE (Edit Only),REMARKS\n{$this->ids['Carlos Zamora']},Carlos Zamora,,82,\nCLASS AVERAGE,,,,\n");
        $res = $this->import($csv, 'grades.csv')->assertOk();
        $this->assertSame([], $res->json('errors'));
        $this->assertEquals(82, $this->draft('Carlos Zamora'));
    }
}
