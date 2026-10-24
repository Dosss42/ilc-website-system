<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Official School Forms: SF5 (DepEd Order 8, s. 2015) promotion rules, ordering and summary tables,
 * and SF9 / SF10 still generating as PDFs.
 */
class SchoolFormsTest extends TestCase
{
    use RefreshDatabase;

    private Section $section;
    private User $admin;
    private array $subjects = [];

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->admin   = User::factory()->admin()->create();
        $this->section = Section::create(['name' => 'Mabini', 'grade_level' => 'grade3', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        foreach (['English', 'Math', 'Science'] as $i => $name) {
            $this->subjects[$name] = Subject::create(['name' => $name, 'code' => 'G3-' . $i, 'grade_level' => 'grade3', 'is_active' => true]);
            $this->section->subjects()->attach($this->subjects[$name]->id);
        }
    }

    /** A learner in the section with per-subject [term1, term2, term3] grades (approved). */
    private function learner(string $first, string $last, string $gender, array $grades): User
    {
        $e = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => 'grade3', 'section' => 'Mabini',
            'student_data' => ['first_name' => $first, 'last_name' => $last, 'grade_level' => 'grade3', 'gender' => $gender]]);
        $this->section->students()->attach($e->user_id);
        StudentProfile::create(['user_id' => $e->user_id, 'first_name' => $first, 'last_name' => $last, 'gender' => $gender]);
        foreach ($grades as $subject => $terms) {
            foreach ($terms as $t => $g) {
                if ($g === null) continue;
                Grade::create(['student_id' => $e->user_id, 'teacher_id' => $this->admin->id, 'subject_id' => $this->subjects[$subject]->id,
                    'enrollment_id' => $e->id, 'term' => $t + 1, 'grade' => $g, 'school_year' => '2026-2027', 'status' => 'approved']);
            }
        }
        return User::find($e->user_id);
    }

    public function test_sf5_follows_deped_order_8_promotion_rules(): void
    {
        $all90 = ['English' => [90, 90, 90], 'Math' => [90, 90, 90], 'Science' => [90, 90, 90]];
        $this->learner('Carl', 'Bautista', 'male', ['Math' => [70, 70, 70]] + $all90);            // 1 subject below 75
        $this->learner('Ben', 'Abad', 'male', $all90);                                              // all 75+
        $this->learner('Dina', 'Cruz', 'female', ['English' => [70, 70, 70], 'Math' => [72, 72, 72], 'Science' => [60, 60, 60]]); // 3 below 75
        $this->learner('Ella', 'Diaz', 'female', ['Math' => [90, 90, null]] + $all90);            // term 3 missing

        $data = null;
        View::composer('teacher.sf5', function ($view) use (&$data) { $data = $view->getData(); });
        $res = $this->actingAs($this->admin, 'web')->get('/teacher/sf5?section_id=' . $this->section->id . '&school_year=2026-2027&print=1');
        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline', $res->headers->get('Content-Disposition'));

        // Males first, by last name; General Average is a whole number
        $this->assertSame(['ABAD, BEN', 'BAUTISTA, CARL'], array_column($data['males'], 'name'));
        $this->assertSame([90, 'PROMOTED', ''], [$data['males'][0]['avg'], $data['males'][0]['action'], $data['males'][0]['incomplete']]);
        $this->assertSame([83, 'CONDITIONALLY PROMOTED', 'Math'], [$data['males'][1]['avg'], $data['males'][1]['action'], $data['males'][1]['incomplete']]);

        $this->assertSame(['CRUZ, DINA', 'DIAZ, ELLA'], array_column($data['females'], 'name'));
        $this->assertSame([67, 'RETAINED', 'English, Math, Science'], [$data['females'][0]['avg'], $data['females'][0]['action'], $data['females'][0]['incomplete']]);
        $this->assertSame([null, ''], [$data['females'][1]['avg'], $data['females'][1]['action']], 'no General Average until all 3 terms are in');

        $summary = collect($data['summary'])->keyBy('label');
        $this->assertSame([1, 0, 1], [$summary['Promoted']['m'], $summary['Promoted']['f'], $summary['Promoted']['t']]);
        $this->assertSame([1, 0, 1], [$summary['*Conditionally Promoted']['m'], $summary['*Conditionally Promoted']['f'], $summary['*Conditionally Promoted']['t']]);
        $this->assertSame([0, 1, 1], [$summary['Retained']['m'], $summary['Retained']['f'], $summary['Retained']['t']]);

        $levels = collect($data['levels'])->keyBy('label');
        $this->assertSame(1, $levels['Outstanding']['m']);                 // 90
        $this->assertSame(1, $levels['Satisfactory']['m']);                // 83
        $this->assertSame(1, $levels['Did Not Meet Expectations']['f']);   // 67
        $this->assertSame(3, collect($data['levels'])->sum('t'), 'the learner without a General Average is not counted');
    }

    public function test_lrn_and_school_settings_from_admin_appear_on_the_forms(): void
    {
        $stu = $this->learner('Ben', 'Abad', 'male', ['English' => [90, 91, 92]]);
        Setting::set('school_id', '419002');
        Setting::set('principal_name', 'Mrs. Teofila Guillermo');

        // Student Management › Edit saves the LRN
        $this->actingAs($this->admin, 'web')->putJson('/admin/students/' . $stu->id, [
            'name' => $stu->name, 'email' => $stu->email, 'lrn' => '108765432101', 'is_active' => true,
        ])->assertOk();

        $views = [];
        foreach (['teacher.sf5', 'teacher.sf9', 'pdf.sf10'] as $name) {
            View::composer($name, function ($view) use (&$views, $name) { $views[$name] = $view->getData(); });
        }
        $this->get('/teacher/sf5?section_id=' . $this->section->id . '&school_year=2026-2027')->assertOk();
        $this->get('/teacher/sf9/' . $stu->id . '?school_year=2026-2027')->assertOk();
        $this->get('/admin/students/' . $stu->id . '/sf10')->assertOk();

        $this->assertSame('108765432101', $views['teacher.sf5']['males'][0]['lrn']);
        $this->assertSame('419002', $views['teacher.sf5']['schoolId']);
        $this->assertSame('Mrs. Teofila Guillermo', $views['teacher.sf5']['schoolHead']);
        $this->assertSame('108765432101', $views['teacher.sf9']['student']->lrn);
        $this->assertSame('Mrs. Teofila Guillermo', $views['teacher.sf9']['schoolHead']);
        $this->assertSame('108765432101', $views['pdf.sf10']['lrn']);
        $this->assertSame('419002', $views['pdf.sf10']['schoolId']);
        $this->assertSame('Mrs. Teofila Guillermo', $views['pdf.sf10']['principalName']);
    }

    public function test_sf9_and_sf10_still_generate(): void
    {
        $stu = $this->learner('Ben', 'Abad', 'male', ['English' => [90, 91, 92], 'Math' => [88, 89, 90], 'Science' => [85, 86, 87]]);

        $sf9 = $this->actingAs($this->admin, 'web')->get('/teacher/sf9/' . $stu->id . '?school_year=2026-2027&print=1');
        $sf9->assertOk();
        $this->assertSame('application/pdf', $sf9->headers->get('Content-Type'));

        $sf10 = $this->actingAs($this->admin, 'web')->get('/admin/students/' . $stu->id . '/sf10?print=1');
        $sf10->assertOk();
        $this->assertSame('application/pdf', $sf10->headers->get('Content-Type'));
    }
}
