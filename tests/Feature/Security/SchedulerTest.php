<?php

namespace Tests\Feature\Security;

use App\Models\Setting;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The scheduler runs the daily backup and audit clearing always, but the payment jobs
 * (reminder/warning emails, ₱500 late fees) only while a Super Admin has switched them on —
 * off by default, so test data never gets emailed or charged.
 */
class SchedulerTest extends TestCase
{
    use RefreshDatabase;

    /** command name => whether the scheduler would run it right now (filters only, not the time) */
    private function scheduled(): array
    {
        $out = [];
        foreach (app(Schedule::class)->events() as $event) {
            $name = $event->description ?: trim(preg_replace('/^.*artisan[\'"]?\s+/', '', (string) $event->command));
            $out[$name] = $event->filtersPass($this->app);
        }
        return $out;
    }

    public function test_payment_jobs_only_run_when_switched_on(): void
    {
        $jobs = $this->scheduled();
        $this->assertFalse(PaymentService::automationEnabled(), 'off by default');
        $this->assertFalse($jobs['app:check-payment-due-dates']);
        $this->assertFalse($jobs['payments:check-overdue']);
        $this->assertTrue($jobs['backup:run'], 'backups always run');
        $this->assertTrue($jobs['audit:clear']);
        $this->assertTrue($jobs['scheduler-heartbeat']);

        Setting::set(PaymentService::AUTOMATION_SETTING, true, 'boolean');
        $jobs = $this->scheduled();
        $this->assertTrue($jobs['app:check-payment-due-dates']);
        $this->assertTrue($jobs['payments:check-overdue']);
    }

    public function test_heartbeat_records_the_last_run(): void
    {
        $this->artisan('schedule:run')->assertSuccessful();
        $this->assertNotNull(Setting::get('scheduler_last_run'));

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=sys-settings')->assertOk()
            ->assertSee('Scheduler running');
    }

    public function test_only_super_admin_can_switch_payment_automation(): void
    {
        $this->actingAs(User::factory()->admin()->create(), 'web')
            ->postJson('/superadmin/settings/payment-automation')->assertStatus(403);
        $this->assertFalse(PaymentService::automationEnabled());

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=sys-settings')->assertOk()
            ->assertSee('Automatic Payment Reminders &amp; Late Fees', false)->assertSee('data-enabled="0"', false)
            ->assertSee('Scheduler not running');

        $this->actingAs($superadmin, 'web')->postJson('/superadmin/settings/payment-automation')
            ->assertOk()->assertJson(['success' => true, 'enabled' => true]);
        $this->assertTrue(PaymentService::automationEnabled());
        $this->assertDatabaseHas('activity_logs', ['description' => 'Automatic payment reminders & late fees turned ON']);

        $this->actingAs($superadmin, 'web')->postJson('/superadmin/settings/payment-automation')
            ->assertOk()->assertJson(['enabled' => false]);
        $this->assertFalse(PaymentService::automationEnabled());
    }
}
