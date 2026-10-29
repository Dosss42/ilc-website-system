<?php

namespace Tests\Feature\Security;

use App\Models\ActivityLog;
use App\Models\Enrollment;
use App\Models\GuidanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clicking a stat card filters the list it counts (public/js/stat-jump.js). These are the filters added
 * for cards that had none — each must list exactly what its card counts.
 */
class StatCardFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_not_paid_filter_lists_students_with_nothing_paid(): void
    {
        // Enrollments store "nothing paid yet" as 'pending'; "Not Paid" used to match only 'unpaid' (nobody)
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'enrolled', 'payment_status' => 'pending']);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'enrolled', 'payment_status' => 'paid']);
        Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => 'enrolled', 'payment_status' => 'partial']);

        $res = $this->actingAs(User::factory()->admin()->create(), 'web')
            ->getJson('/admin/section/students-data?student_schoolyear=all&student_payment=unpaid')->assertOk();
        $this->assertSame(1, $res->json('total'));
        $this->assertSame('pending', $res->json('students.0.payment_status'));
    }

    public function test_guidance_resolved_closed_filter_matches_its_card(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create(['role' => 'student']);
        foreach (['open', 'in_progress', 'resolved', 'closed'] as $status) {
            GuidanceRecord::create(['student_id' => $student->id, 'counselor_id' => $admin->id, 'date' => now()->toDateString(),
                'concern_type' => 'Academic', 'concern_description' => 'x', 'status' => $status]);
        }

        $html = $this->actingAs($admin, 'web')->get('/admin/section/guidance?guidance_status=resolved_closed')->assertOk()->getContent();
        $this->assertStringContainsString('2 record(s)', $html, 'resolved + closed, as the Resolved / Closed card counts');
        $this->assertStringContainsString('#guidance-records-card', html_entity_decode($html), 'cards point at the list');
    }

    public function test_super_admin_user_cards_filter_user_management(): void
    {
        $sa = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        User::factory()->admin()->create(['is_active' => true]);
        User::factory()->teacher()->create(['is_active' => false]);
        User::factory()->count(2)->create(['role' => 'student', 'is_active' => true]);

        $this->actingAs($sa, 'web');
        $listed = function (string $query) {
            preg_match('/id="userCountLabel">\s*(\d+) account/', $this->get('/superadmin/dashboard?section=users&' . $query)->assertOk()->getContent(), $m);
            return (int) ($m[1] ?? -1);
        };
        $this->assertSame(1, $listed('user_status=inactive'), 'Inactive card = inactive accounts');
        $this->assertSame(User::where('is_active', true)->count(), $listed('user_status=active'), 'Active card = active accounts');
        $this->assertSame(2, $listed('role=admins'), "Dashboard's Admins card = Admins + Super Admins");
        $this->assertSame(User::count(), $listed('role=all'));

        // The new User Management cards are there and clickable
        $html = html_entity_decode($this->get('/superadmin/dashboard?section=users')->getContent());
        $this->assertStringContainsString('user_status=inactive', $html);
        $this->assertStringContainsString('"to":"#usersListCard"', $html);
    }

    public function test_roles_and_permissions_page_is_hidden_from_super_admin(): void
    {
        $sa = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        $html = $this->actingAs($sa, 'web')->get('/superadmin/dashboard?section=roles')->assertOk()->getContent();
        $this->assertStringNotContainsString('id="nav-roles"', $html, 'no sidebar link (so not in quick jump either)');
        $this->assertMatchesRegularExpression('/id="section-roles" class="dash-section"\s+style="display:none;"/', $html, '?section=roles does not open it');
        $this->assertDoesNotMatchRegularExpression('/id="section-dashboard" class="dash-section"\s+style="display:none;"/', $html, 'the Dashboard opens instead');
    }

    public function test_admin_enrollment_status_cards_match_super_admin(): void
    {
        foreach (['pending', 'pending', 'approved', 'declined', 'enrolled'] as $status) {
            Enrollment::factory()->create(['school_year' => '2026-2027', 'status' => $status]);
        }
        Enrollment::factory()->create(['school_year' => '2025-2026', 'status' => 'pending']); // another year

        $sa = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        $admin = User::factory()->admin()->create();
        foreach (['2026-2027', 'all'] as $sy) {
            $counts = $this->actingAs($sa, 'web')->getJson('/superadmin/enrollments-data?school_year=' . $sy)->assertOk()->json('counts');
            $html = $this->actingAs($admin, 'web')->get('/admin/section/enrollment?enrollment_sy=' . $sy)->assertOk()->getContent();
            foreach (['Pending' => 'pending', 'Approved' => 'approved', 'Declined' => 'declined'] as $label => $status) {
                $this->assertMatchesRegularExpression('/stat-value">' . ($counts[$status] ?? 0) . '<\/div>\s*<div class="stat-label">' . $label . '</', $html, "$label · $sy");
            }
        }
    }

    public function test_audit_failed_denied_filter_matches_its_card(): void
    {
        foreach (['failed_login', 'denied', 'login', 'update'] as $type) {
            ActivityLog::create(['user_name' => 'X', 'user_role' => 'admin', 'event_type' => $type, 'description' => 'd']);
        }
        ActivityLog::create(['user_name' => 'X', 'user_role' => 'admin', 'event_type' => 'denied', 'description' => 'old'])
            ->forceFill(['created_at' => now()->subDays(30)])->save();

        $admin = User::factory()->admin()->create();
        $weekAgo = today()->subDays(7)->toDateString();
        $html = $this->actingAs($admin, 'web')->get('/admin/section/audit?audit_type=failed_denied&audit_from=' . $weekAgo)->assertOk()->getContent();
        $this->assertStringContainsString('2 entries', $html, 'failed login + denied of the last 7 days, as the card counts');
        // The card itself shows the same number and carries that filter
        $all = html_entity_decode($this->get('/admin/section/audit')->getContent());
        $this->assertStringContainsString('"#audit-filter-form [name=audit_type]":"failed_denied"', $all);
        $this->assertStringContainsString('"#audit-filter-form [name=audit_from]":"' . $weekAgo . '"', $all);
    }
}
