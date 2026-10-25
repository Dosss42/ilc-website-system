<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Super Admin's Enrollment, Teachers, Subjects and Sections panels show what Admin's do: school
 * year filter, all statuses, payment and student type, inactive teachers, sections handled,
 * search across pages, schedule counts and the same stat cards.
 */
class SuperAdminParityTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->superadmin = User::factory()->superadmin()->create();
    }

    public function test_enrollment_list_has_admins_school_year_statuses_and_payment(): void
    {
        $data = fn (string $type) => ['first_name' => 'A', 'last_name' => 'B', 'grade_level' => 'grade1', 'student_type' => $type];
        Enrollment::factory()->create(['school_year' => '2026-2027', 'payment_status' => 'partial', 'student_data' => $data('returning')]);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'cancelled', 'student_data' => $data('new')]);
        Enrollment::factory()->create(['school_year' => '2027-2028', 'status' => 'pending', 'student_data' => $data('transferee')]);

        $res = $this->actingAs($this->superadmin, 'web')->getJson('/superadmin/enrollments-data')->assertOk();
        $this->assertSame('2026-2027', $res->json('school_year'), 'opens on the current school year');
        $this->assertSame(1, $res->json('total'), 'cancelled (undone promotion) is hidden under All Status');
        $this->assertEquals(['payment_status' => 'partial', 'student_type' => 'returning', 'school_year' => '2026-2027'],
            collect($res->json('enrollments.0'))->only(['payment_status', 'student_type', 'school_year'])->all());
        $this->assertSame(['2027-2028' => 1], $res->json('pending_other_years'));
        $this->assertNull($res->json('counts.pending'), 'counts are for the chosen year');

        $this->assertSame(1, $this->getJson('/superadmin/enrollments-data?status=cancelled')->json('total'));
        $this->assertSame(2, $this->getJson('/superadmin/enrollments-data?school_year=all')->json('total'));
        $this->assertSame('2026-2027', $this->getJson('/superadmin/enrollments-data?school_year=garbage')->json('school_year'));
    }

    public function test_teachers_list_keeps_inactive_teachers_and_shows_sections_handled(): void
    {
        $ana = User::factory()->teacher()->create(['name' => 'Ana Reyes']);
        $ben = User::factory()->teacher()->create(['name' => 'Ben Cruz', 'is_active' => false]);
        $section = Section::create(['name' => 'Rizal', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        $other = Section::create(['name' => 'Bonifacio', 'grade_level' => 'grade2', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        TeacherAssignment::create(['teacher_id' => $ana->id, 'section_id' => $section->id, 'is_advisory' => true, 'school_year' => '2026-2027']);
        $subject = Subject::create(['name' => 'Math', 'code' => 'M2', 'grade_level' => 'grade2', 'is_active' => true]);
        Schedule::create(['section_id' => $other->id, 'subject_id' => $subject->id, 'teacher_id' => $ana->id, 'day_of_week' => 'Monday', 'start_time' => '07:00', 'end_time' => '08:00', 'term' => 1, 'is_active' => true]);

        // Dropdowns elsewhere: active teachers only, as before
        $this->actingAs($this->superadmin, 'web')->getJson('/admin/teachers')->assertOk()->assertJsonCount(1, 'teachers');

        $res = $this->getJson('/admin/teachers?paginated=1&include_inactive=1&with_sections=1')->assertOk();
        $this->assertSame(['total' => 2, 'active' => 1, 'inactive' => 1, 'with_schedules' => 1], $res->json('stats'));
        $this->assertEqualsCanonicalizing(['Ana Reyes', 'Ben Cruz'], collect($res->json('teachers'))->pluck('name')->all(), 'a deactivated teacher stays listed');
        $a = collect($res->json('teachers'))->firstWhere('name', 'Ana Reyes');
        $this->assertSame(['Rizal'], collect($a['advisory_sections'])->pluck('name')->all());
        $this->assertSame(['Bonifacio'], collect($a['teaching_sections'])->pluck('name')->all());

        // Search and status run on the server (every page, not just the one shown)
        $this->assertSame(['Ben Cruz'], collect($this->getJson('/admin/teachers?paginated=1&include_inactive=1&search=cruz')->json('teachers'))->pluck('name')->all());
        $this->assertSame(['Ben Cruz'], collect($this->getJson('/admin/teachers?paginated=1&include_inactive=1&status=inactive')->json('teachers'))->pluck('name')->all());
    }

    public function test_subjects_and_sections_lists_have_admins_counts_and_filters(): void
    {
        $teacher = User::factory()->teacher()->create();
        $math = Subject::create(['name' => 'Mathematics', 'code' => 'MATH1', 'grade_level' => 'grade1', 'is_active' => true]);
        Subject::create(['name' => 'Old Subject', 'code' => 'OLD1', 'grade_level' => 'grade1', 'is_active' => false]);
        $rizal = Section::create(['name' => 'Rizal', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => true]);
        Section::create(['name' => 'Closed', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2026-2027', 'is_active' => false]);
        Section::create(['name' => 'NextYear', 'grade_level' => 'grade1', 'max_students' => 30, 'school_year' => '2027-2028', 'is_active' => true]);
        $rizal->subjects()->sync([$math->id]);
        Schedule::create(['section_id' => $rizal->id, 'subject_id' => $math->id, 'teacher_id' => $teacher->id, 'day_of_week' => 'Monday', 'start_time' => '07:00', 'end_time' => '08:00', 'term' => 1, 'is_active' => true]);
        $student = Enrollment::factory()->create(['school_year' => '2026-2027']);
        $rizal->students()->attach($student->user_id);

        $s = $this->actingAs($this->superadmin, 'web')->getJson('/admin/subjects?paginated=1&with_counts=1&search=math')->assertOk();
        $this->assertSame(['total' => 2, 'active' => 1, 'inactive' => 1], $s->json('stats'));
        $this->assertSame([['MATH1', 1]], collect($s->json('subjects'))->map(fn ($x) => [$x['code'], $x['schedules_count']])->all());
        $this->assertSame(['OLD1'], collect($this->getJson('/admin/subjects?paginated=1&is_active=0')->json('subjects'))->pluck('code')->all());

        $c = $this->getJson('/admin/sections?paginated=1&with_counts=1&school_year=2026-2027')->assertOk();
        $this->assertSame(['total' => 2, 'active' => 1, 'enrolled' => 1], $c->json('stats'), 'cards are for the chosen school year');
        $row = collect($c->json('sections'))->firstWhere('name', 'Rizal');
        $this->assertSame([1, 1, 1], [$row['subjects_count'], $row['schedules_count'], count($row['students'])]);
        $this->assertSame(['Closed'], collect($this->getJson('/admin/sections?paginated=1&school_year=2026-2027&status=inactive')->json('sections'))->pluck('name')->all());
    }

    public function test_student_management_data_is_admins_list_and_cards(): void
    {
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'enrolled', 'payment_status' => 'partial', 'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => 'grade1']]);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'enrolled', 'payment_status' => 'paid', 'student_data' => ['first_name' => 'Ben', 'last_name' => 'Dela', 'grade_level' => 'grade2']]);
        Enrollment::factory()->create(['school_year' => '2027-2028', 'status' => 'pending', 'student_data' => ['first_name' => 'Cy', 'last_name' => 'Next', 'grade_level' => 'grade3']]);

        $admin = User::factory()->admin()->create();
        $html = $this->actingAs($admin, 'web')->get('/admin/section/students?student_payment=partial')->assertOk()->getContent();

        $res = $this->actingAs($this->superadmin, 'web')->getJson('/admin/section/students-data?student_payment=partial')->assertOk();
        $this->assertSame('2026-2027', $res->json('school_year'), 'opens on the current school year, like Admin');
        $this->assertSame(1, $res->json('total'));
        $row = $res->json('students.0');
        $this->assertSame(['enrolled', 'partial', 'Grade 1', '2026-2027'], [$row['status'], $row['payment_status'], $row['grade_label'], $row['school_year']]);
        $this->assertStringContainsString('>' . $row['name'] . '<', $html, 'the same student Admin lists');
        $this->assertStringContainsString('id="sm-total-count">1<', $html);
        $this->assertSame(1, $res->json('stats.total'));

        // All school years: every card equals the number on Admin's page
        $all = $this->getJson('/admin/section/students-data?student_schoolyear=all')->assertOk();
        $this->assertSame(3, $all->json('total'));
        $adminHtml = $this->actingAs($admin, 'web')->get('/admin/section/students?student_schoolyear=all')->getContent();
        preg_match_all('/id="sm-(total|enrolled|approved|pending|notenrolled|paid|balance)-count">(\d+)</', $adminHtml, $m);
        $adminCards = array_combine($m[1], array_map('intval', $m[2]));
        $s = $all->json('stats');
        $this->assertSame($adminCards, ['total' => $s['total'], 'enrolled' => $s['enrolled'], 'approved' => $s['approved'], 'pending' => $s['pending'],
            'notenrolled' => $s['not_enrolled'], 'paid' => $s['paid'], 'balance' => $s['balance']]);

        // Students and teachers can't read it
        $this->actingAs(User::factory()->teacher()->create(), 'web')->getJson('/admin/section/students-data')->assertForbidden();
    }

    public function test_enrollment_shows_admins_money_cards(): void
    {
        Enrollment::factory()->create(['school_year' => '2026-2027', 'payment_status' => 'paid', 'payment_amount' => 15000]);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'payment_status' => 'partial', 'payment_amount' => 3000]);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'payment_status' => 'pending', 'payment_amount' => 0]);

        $res = $this->actingAs($this->superadmin, 'web')->getJson('/superadmin/enrollments-data')->assertOk();
        $this->assertEquals(18000, $res->json('fees_collected'));
        $this->assertSame(1, $res->json('pending_payments'));
    }

    public function test_super_admin_page_has_the_new_controls(): void
    {
        $this->actingAs($this->superadmin, 'web')->get('/superadmin/dashboard')->assertOk()
            ->assertSee('id="enr-sy-filter"', false)->assertSee('<option value="cancelled">', false)
            ->assertSee('id="tch-status-filter"', false)->assertSee('id="tch-cnt-total"', false)
            ->assertSee('id="subj-search"', false)->assertSee('<th>In Schedule</th>', false)
            ->assertSee('id="sec-status-filter"', false)->assertSee('ilcSectionTools.autoAssign', false)
            ->assertSee('js/section-tools.js', false)
            // Schedule: Download PDF only — the Export CSV button was removed (same as Admin)
            ->assertSee('id="saSchedulePdfLink"', false)->assertDontSee('saExportScheduleCsv', false)
            ->assertSee('id="nav-students"', false)->assertSee('id="section-students"', false)
            ->assertSee('id="enr-fees-collected"', false)->assertSee('saAssignAdvisory(', false);

        // The student windows are the same files on both pages
        foreach (['studentViewModal', 'studentEditModal', 'changeStatusModal', 'studentTransactionsModal'] as $id) {
            $this->get('/superadmin/dashboard')->assertSee('id="' . $id . '"', false);
            $this->actingAs(User::factory()->admin()->create(), 'web')->get('/admin/dashboard')->assertSee('id="' . $id . '"', false);
            $this->actingAs($this->superadmin, 'web');
        }
    }
}
