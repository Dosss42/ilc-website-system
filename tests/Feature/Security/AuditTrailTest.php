<?php

namespace Tests\Feature\Security;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AuditLogRetention;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The audit trail is viewed only by Admin (every role except Super Admin) and Super Admin
 * (everything); Cashier and Finance no longer have one, but their actions are still logged.
 * The Super Admin alone sets how long entries are kept; older ones are cleared automatically.
 */
class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    private function log(?string $role = null, string $desc = 'Did something', string $type = 'update', $at = null): void
    {
        DB::table('activity_logs')->insert([
            'user_id' => null, 'user_name' => $role ? ucfirst($role) . ' User' : null, 'user_role' => $role,
            'event_type' => $type, 'description' => $desc, 'created_at' => $at ?? now(), 'updated_at' => $at ?? now(),
        ]);
    }

    public function test_admin_sees_every_role_except_super_admin(): void
    {
        $this->log('cashier', 'Cash payment of ₱1,000 for Ana', 'cash_payment');
        $this->log('finance', 'Approved payment #12', 'payment_approved');
        $this->log('superadmin', 'Database restored from backup');
        $this->log(null, 'Automatic clearing: 3 audit log(s) removed', 'delete');
        $admin = User::factory()->admin()->create();

        $html = $this->actingAs($admin, 'web')->get('/admin/section/audit')->assertOk()->getContent();
        $this->assertStringContainsString('Cash payment of ₱1,000 for Ana', $html);
        $this->assertStringContainsString('Approved payment #12', $html);
        $this->assertStringContainsString('Automatic clearing', $html, '"System" entries stay visible');
        $this->assertStringNotContainsString('Database restored from backup', $html);

        // Filters
        $this->actingAs($admin, 'web')->get('/admin/section/audit?audit_role=finance')->assertOk()
            ->assertSee('Approved payment #12')->assertDontSee('Cash payment of');
        $this->actingAs($admin, 'web')->get('/admin/section/audit?audit_type=cash_payment')->assertOk()
            ->assertSee('Cash payment of')->assertDontSee('Approved payment #12');
        $this->actingAs($admin, 'web')->get('/admin/section/audit?audit_search=Ana')->assertOk()
            ->assertSee('Cash payment of')->assertDontSee('Approved payment #12');
        $this->actingAs($admin, 'web')->get('/admin/section/audit?audit_role=superadmin')->assertOk()
            ->assertDontSee('Database restored from backup');

        // Read-only for the Admin: shows the period, no control to change it
        $this->actingAs($admin, 'web')->get('/admin/section/audit')->assertSee('kept for <strong>12 months</strong>', false)
            ->assertDontSee('saveLogRetention');
    }

    public function test_cashier_and_finance_have_no_audit_trail_page(): void
    {
        $cashier = User::factory()->cashier()->create();
        $finance = User::factory()->finance()->create();

        $this->actingAs($cashier, 'cashier')->get('/cashier/audit-trail')->assertNotFound();
        $this->actingAs($finance, 'finance')->get('/finance/audit-trail')->assertNotFound();

        $this->actingAs($cashier, 'cashier')->get('/cashier/dashboard')->assertOk()->assertDontSee('Audit Trail</span>', false);
        $this->actingAs($finance, 'finance')->get('/finance/dashboard')->assertOk()->assertDontSee('Audit Trail');

        // Students and teachers can't open the Admin one
        $this->actingAs(User::factory()->student()->create(), 'web')->get('/admin/section/audit')->assertForbidden();
    }

    public function test_old_entries_are_cleared_once_a_day(): void
    {
        $this->log('admin', 'Thirteen months old', 'update', now()->subMonths(13));
        $this->log('admin', 'Eleven months old', 'update', now()->subMonths(11));

        $this->assertSame('12', AuditLogRetention::period(), 'default is 12 months');
        $this->assertSame(1, AuditLogRetention::clearIfDue());
        $this->assertFalse(ActivityLog::where('description', 'Thirteen months old')->exists());
        $this->assertTrue(ActivityLog::where('description', 'Eleven months old')->exists());
        $this->assertTrue(ActivityLog::whereNull('user_role')->where('description', 'like', 'Automatic clearing: 1 audit log(s)%')->exists());

        // Already ran today → no second pass, even with new old rows
        $this->log('admin', 'Another old one', 'update', now()->subMonths(20));
        $this->assertSame(0, AuditLogRetention::clearIfDue());
        $this->assertTrue(ActivityLog::where('description', 'Another old one')->exists());
    }

    public function test_clearing_works_in_batches(): void
    {
        $old = now()->subMonths(14);
        $rows = array_fill(0, 2500, [
            'user_role' => 'student', 'event_type' => 'login', 'description' => 'old login', 'created_at' => $old, 'updated_at' => $old,
        ]);
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('activity_logs')->insert($chunk);
        }
        $this->log('student', 'recent login', 'login');

        $this->assertSame(2500, AuditLogRetention::clear());
        $this->assertSame(0, ActivityLog::where('description', 'old login')->count());
        $this->assertTrue(ActivityLog::where('description', 'recent login')->exists());
    }

    public function test_super_admin_system_logs_filters(): void
    {
        $this->log('student', 'Xendit payment of ₱1,056.10 confirmed', 'xendit_payment_completed', now()->subDays(1));
        $this->log('cashier', 'Cash payment of ₱500 for Ben', 'cash_payment', now()->subDays(10));
        $this->log('superadmin', 'Backup deleted: old.sql', 'delete', now()->subDays(40));
        $this->log(null, 'Automatic clearing: 4 audit log(s) removed', 'delete', now()->subDays(2));
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $page = fn (string $q) => $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=logs&' . $q)->assertOk();

        // Every recorded event type is offered, with readable labels in the table
        $page('')->assertSee('<option value="xendit_payment_completed"', false)->assertSee('<option value="payment_rejected"', false)
            ->assertSee('Online Payment Completed')->assertDontSee('Xendit_payment_completed');

        $page('log_type=cash_payment')->assertSee('Cash payment of ₱500')->assertDontSee('Xendit payment of');
        $page('log_role=system')->assertSee('Automatic clearing: 4')->assertDontSee('Cash payment of ₱500');
        $page('log_role=superadmin')->assertSee('Backup deleted: old.sql')->assertDontSee('Cash payment of ₱500');

        // Date range: last 7 days only
        $page('log_from=' . now()->subDays(7)->toDateString() . '&log_to=' . now()->toDateString())
            ->assertSee('Xendit payment of')->assertSee('Automatic clearing: 4')
            ->assertDontSee('Cash payment of ₱500')->assertDontSee('Backup deleted: old.sql');
        // An old single-day link still works
        $page('log_date=' . now()->subDays(10)->toDateString())->assertSee('Cash payment of ₱500')->assertDontSee('Xendit payment of');

        // Search treats % and _ literally
        $page('log_search=%25')->assertSee('No logs match the current filters.');

        // The filters stay on the page links, and Export Logs is a PDF of exactly the filtered rows
        foreach (range(1, 25) as $i) {
            $this->log('student', "Student $i logged in", 'login', now()->subDays(3));
        }
        $page('log_type=login')->assertSee('log_type=login&amp;', false);
        $res = $this->actingAs($superadmin, 'web')->get('/superadmin/logs/export?log_role=cashier&log_from=' . now()->subDays(30)->toDateString())->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));
        $this->assertStringContainsString('system-logs-' . now()->format('Y-m-d') . '.pdf', $res->headers->get('content-disposition'));
        $this->assertTrue(ActivityLog::where('description', 'System Logs exported as PDF (1 rows)')->exists(), 'only the cashier row');
    }

    public function test_logs_pdf_lists_the_filtered_rows_with_the_filters_in_the_header(): void
    {
        $this->log('cashier', 'Cash payment of ₱500 for Ben', 'cash_payment', now()->subDays(2));
        $this->log('student', 'Xendit payment of ₱1,056.10 confirmed', 'xendit_payment_completed', now()->subDays(1));
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $html = view('pdf.superadmin.audit-log-report', [
            'schoolLogoBase64' => null, 'schoolName' => 'ILC', 'reportTitle' => 'System Logs', 'generatedBy' => 'Super Admin', 'generatedAt' => now(),
            'dateRangeLabel' => (fn () => $this->logFiltersLabel(['role' => 'cashier', 'type' => 'cash_payment', 'from' => '2026-10-01', 'to' => '2026-10-23', 'search' => 'Ben']))
                ->call(app(\App\Http\Controllers\SuperAdminController::class)),
            'logs' => ActivityLog::where('user_role', 'cashier')->get(),
        ])->render();

        $this->assertStringContainsString('Oct 01, 2026 – Oct 23, 2026 · Cashier · Cash Payment · “Ben”', $html);
        $this->assertStringContainsString('Cash payment of ₱500 for Ben', $html);
        $this->assertStringNotContainsString('Xendit payment of', $html);
        $this->assertStringNotContainsString('font-family: \'Courier', $html);
        $this->assertStringNotContainsString('Showing the newest', $html, 'no notice when everything fits');

        // More matching logs than one PDF holds → says so (the export takes the newest LOGS_PDF_MAX_ROWS)
        $capped = view('pdf.superadmin.audit-log-report', [
            'schoolLogoBase64' => null, 'schoolName' => 'ILC', 'reportTitle' => 'System Logs', 'generatedBy' => 'Super Admin', 'generatedAt' => now(),
            'dateRangeLabel' => 'All activity', 'logs' => ActivityLog::all(), 'matching' => 3000,
        ])->render();
        $this->assertStringContainsString('Showing the newest 2 of 3,000 matching logs', $capped);

        // Reports › Audit Log Report is the same PDF
        $this->actingAs($superadmin, 'web')->get('/superadmin/reports/audit-log-report-pdf')->assertOk();
        $this->assertTrue(ActivityLog::where('description', 'like', 'Audit Log Report exported as PDF (%')->exists());
    }

    public function test_only_super_admin_sets_the_retention_period(): void
    {
        $this->log('admin', 'Two months old', 'update', now()->subMonths(2));
        $this->log('admin', 'Two weeks old', 'update', now()->subWeeks(2));
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->admin()->create();

        // Admin can't change it
        $this->actingAs($admin, 'web')->postJson('/superadmin/logs/retention', ['period' => '1'])->assertStatus(403);
        $this->assertSame('12', AuditLogRetention::period());

        // Only the listed choices
        foreach (['2', '3w', 'abc', ''] as $bad) {
            $this->actingAs($superadmin, 'web')->postJson('/superadmin/logs/retention', ['period' => $bad])->assertStatus(422);
        }

        // 1 month (the older "months" field still works): the older entry is cleared right away, and the change is logged
        $this->actingAs($superadmin, 'web')->postJson('/superadmin/logs/retention', ['months' => 1])
            ->assertOk()->assertJson(['success' => true, 'label' => '1 month', 'cleared' => 1]);
        $this->assertSame('1', AuditLogRetention::period());
        $this->assertFalse(ActivityLog::where('description', 'Two months old')->exists());
        $this->assertTrue(ActivityLog::where('description', 'Two weeks old')->exists());
        $this->assertTrue(ActivityLog::where('user_role', 'superadmin')->where('description', 'Audit log automatic clearing changed from 12 months to 1 month')->exists());

        // Weeks: 1 week clears the two-week-old entry, keeps a three-day-old one
        $this->log('admin', 'Three days old', 'update', now()->subDays(3));
        $this->actingAs($superadmin, 'web')->postJson('/superadmin/logs/retention', ['period' => '1w'])
            ->assertOk()->assertJson(['success' => true, 'label' => '1 week', 'cleared' => 1]);
        $this->assertFalse(ActivityLog::where('description', 'Two weeks old')->exists());
        $this->assertTrue(ActivityLog::where('description', 'Three days old')->exists());
        $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=logs')->assertOk()
            ->assertSee('<option value="1w" selected>Keep 1 week</option>', false)
            ->assertSee('<option value="2w" >Keep 2 weeks</option>', false);
        $this->actingAs($admin, 'web')->get('/admin/section/audit')->assertSee('kept for <strong>1 week</strong>', false);

        // Never: nothing is cleared
        $this->log('admin', 'Ancient', 'update', now()->subYears(3));
        $this->actingAs($superadmin, 'web')->postJson('/superadmin/logs/retention', ['period' => '0'])
            ->assertOk()->assertJson(['cleared' => 0]);
        $this->assertTrue(ActivityLog::where('description', 'Ancient')->exists());

        // The Super Admin page shows the control with the saved value
        $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=logs')->assertOk()
            ->assertSee('id="logRetentionSelect"', false)->assertSee('data-current="0"', false);
    }

    public function test_a_period_saved_before_weeks_existed_still_works(): void
    {
        // Saved as an integer setting, the way "12 months" is stored today
        \App\Models\Setting::set(AuditLogRetention::SETTING, 6, 'integer');
        $this->assertSame('6', AuditLogRetention::period());
        $this->assertSame('6 months', AuditLogRetention::label());
        $this->assertSame(now()->subMonths(6)->toDateString(), AuditLogRetention::cutoff()->toDateString());

        \App\Models\Setting::set(AuditLogRetention::SETTING, 'nonsense');
        $this->assertSame('12', AuditLogRetention::period(), 'anything unknown falls back to 12 months');
    }
}
