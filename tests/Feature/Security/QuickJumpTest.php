<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Top-bar quick jump: the student lookup it uses (/admin/students/search, also the Guidance picker)
 * matches name, email or LRN and returns the grade/section/LRN hint; only staff can call it; each
 * dashboard has the quick-jump box and no placeholder bell/envelope.
 */
class QuickJumpTest extends TestCase
{
    use RefreshDatabase;

    private function student(string $name, ?string $lrn, string $grade = 'grade3', string $section = 'Rizal'): User
    {
        $enr = Enrollment::factory()->create(['school_year' => '2026-2027', 'grade_level' => $grade, 'section' => $section, 'status' => 'enrolled',
            'student_data' => ['grade_level' => $grade]]);
        User::whereKey($enr->user_id)->update(['name' => $name, 'lrn' => $lrn]);
        return User::find($enr->user_id);
    }

    public function test_student_lookup_matches_name_email_and_lrn(): void
    {
        $juan = $this->student('Juan Cruz', '419002260001');
        $this->student('Ben Cruz', null, 'kindergarten', 'Unassigned');
        $this->student('Ana Lopez', '419002260003');
        $admin = User::factory()->admin()->create();

        $byName = $this->actingAs($admin, 'web')->getJson('/admin/students/search?q=cruz')->assertOk()->json();
        $this->assertSame(['Ben Cruz', 'Juan Cruz'], array_column($byName, 'name'));
        $this->assertSame(['id' => $juan->id, 'name' => 'Juan Cruz', 'email' => $juan->email, 'lrn' => '419002260001', 'grade_label' => 'Grade 3', 'section' => 'Rizal'],
            collect($byName)->firstWhere('name', 'Juan Cruz'));
        $this->assertNull(collect($byName)->firstWhere('name', 'Ben Cruz')['section'], '"Unassigned" is not shown as a section');

        $this->assertSame(['Ana Lopez'], array_column($this->getJson('/admin/students/search?q=260003')->json(), 'name'), 'by LRN');
        $this->assertSame(['Juan Cruz'], array_column($this->getJson('/admin/students/search?q=' . urlencode($juan->email))->json(), 'name'), 'by email');
        $this->assertSame([], $this->getJson('/admin/students/search?q=c')->json(), 'needs 2+ characters');
    }

    public function test_superadmin_can_use_it_and_others_cannot(): void
    {
        $this->student('Juan Cruz', '419002260001');
        $this->actingAs(User::factory()->create(['role' => 'superadmin']), 'web')->getJson('/admin/students/search?q=juan')->assertOk()->assertJsonCount(1);
        $res = $this->actingAs(User::factory()->teacher()->create(), 'web')->getJson('/admin/students/search?q=juan');
        $this->assertNotSame(200, $res->getStatusCode(), 'teachers cannot look up students here');
    }

    public function test_each_dashboard_has_the_quick_jump_and_no_placeholder_icons(): void
    {
        $pages = [
            [User::factory()->admin()->create(), '/admin/dashboard', 'Go to a page or find a student…'],
            [User::factory()->create(['role' => 'superadmin']), '/superadmin/dashboard', 'Go to a page or find a student…'],
            [User::factory()->teacher()->create(), '/teacher/dashboard', 'Go to a page or subject…'],
        ];
        foreach ($pages as [$user, $url, $placeholder]) {
            $html = $this->actingAs($user, 'web')->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('id="qjInput" placeholder="' . $placeholder . '"', $html, $url);
            $this->assertStringContainsString('/js/quick-jump.js?v=', $html, $url);
            $this->assertStringNotContainsString('class="bi bi-bell"', $html, "$url: placeholder bell hidden");
        }
    }
}
