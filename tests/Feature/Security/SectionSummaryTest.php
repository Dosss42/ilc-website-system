<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Section Management › Section Summary: boys/girls, student type, payments and required
 * documents for everyone in a section, counted from their enrollment for the section's
 * school year, with a PDF of the same numbers.
 */
class SectionSummaryTest extends TestCase
{
    use RefreshDatabase;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->section = Section::create(['name' => 'Rizal', 'grade_level' => 'grade1', 'room_number' => '101', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
    }

    private function student(array $data, array $attrs = [], ?Section $into = null): Enrollment
    {
        $e = Enrollment::factory()->create(array_merge([
            'student_data' => array_merge(['first_name' => 'A', 'last_name' => 'B', 'grade_level' => 'grade1'], $data),
        ], $attrs));
        ($into ?? $this->section)->students()->attach($e->user_id);
        return $e;
    }

    private function docs(Enrollment $e, array $types, string $status = 'approved'): void
    {
        foreach ($types as $type) {
            StudentDocument::create(['user_id' => $e->user_id, 'document_type' => $type, 'file_path' => 'x', 'original_name' => 'x.pdf', 'status' => $status]);
        }
    }

    public function test_summary_counts_the_section(): void
    {
        $paid = $this->student(['gender' => 'male', 'student_type' => 'new'], ['payment_status' => 'paid', 'payment_amount' => 14504, 'remaining_balance' => 0]);
        $this->docs($paid, ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture']);

        $partial = $this->student(['gender' => 'female', 'student_type' => 'returning'], ['payment_status' => 'partial', 'payment_option' => 'B', 'payment_amount' => 3000]);
        $this->docs($partial, ['birth_certificate', 'form_137']);
        $this->docs($partial, ['report_card'], 'pending');

        // Paid last year, nothing yet this year → counted from this year's enrollment
        $unpaid = $this->student(['gender' => 'female', 'student_type' => 'transferee']);
        Enrollment::factory()->fullyPaid()->create(['user_id' => $unpaid->user_id, 'school_year' => '2025-2026', 'enrolled_at' => now()->subYear()]);

        // In the pivot but never enrolled, and a student of another section → not listed
        $this->student(['gender' => 'male'], ['status' => 'pending']);
        $other = Section::create(['name' => 'Bonifacio', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $this->student(['gender' => 'male'], [], $other);

        $res = $this->actingAs(User::factory()->admin()->create(), 'web')
            ->getJson("/admin/sections/{$this->section->id}/summary")->assertOk();

        $res->assertJsonPath('section.grade_label', 'Grade 1')
            ->assertJsonPath('section.room', '101')
            ->assertJson(['totals' => [
                'students' => 3, 'male' => 1, 'female' => 2,
                'new' => 1, 'returning' => 1, 'transferee' => 1,
                'paid' => 1, 'partial' => 1, 'unpaid' => 1,
                'docs_complete' => 1, 'docs_incomplete' => 2,
            ]]);
        $this->assertEquals(14504 * 3, $res->json('totals.total_fees'));
        $this->assertEquals(14504 + 3000, $res->json('totals.total_collected'));
        $this->assertEquals(11504 + 14504, $res->json('totals.total_balance'));

        $rows = collect($res->json('students'))->keyBy('id');
        $this->assertSame(2, $rows[$partial->user_id]['docs_approved'], 'pending documents are not counted');
        $this->assertSame('B', $rows[$partial->user_id]['payment_plan']);
        $this->assertSame('unpaid', $rows[$unpaid->user_id]['payment']);
        $this->assertEquals(0, $rows[$paid->user_id]['balance']);
    }

    public function test_pdf_downloads_and_staff_outside_admin_cannot_open_it(): void
    {
        $this->student(['gender' => 'male', 'student_type' => 'new']);

        $res = $this->actingAs(User::factory()->superadmin()->create(), 'web')
            ->get("/admin/sections/{$this->section->id}/summary/pdf")->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));
        $this->assertStringContainsString('section-summary-grade-1-rizal-2026-2027.pdf', $res->headers->get('content-disposition'));

        $this->app['auth']->forgetGuards();
        $teacher = $this->actingAs(User::factory()->teacher()->create(), 'web')
            ->getJson("/admin/sections/{$this->section->id}/summary");
        $this->assertNotSame(200, $teacher->status());
    }

    public function test_the_button_is_on_the_section_list(): void
    {
        $this->actingAs(User::factory()->admin()->create(), 'web')
            ->get('/admin/dashboard')->assertOk()
            ->assertSee('js-section-summary', false)
            ->assertSee('js/section-summary.js', false);
    }
}
