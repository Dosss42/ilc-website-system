<?php

namespace Tests\Feature\Security;

use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculator;
use App\Support\DefaultSettings;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A brand-new server: the start-up step (docker/entrypoint.sh → DefaultSettingsSeeder) gives an
 * empty database the default settings and the standard fees — never dummy accounts or sample
 * data, and never changing what is already saved. The first real account comes from /recovery.
 */
class FreshInstallTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_settings_are_added_once_and_never_overwrite_saved_ones(): void
    {
        Setting::set('school_name', 'Our Own School');

        $added = DefaultSettings::install();
        $this->assertSame(count(DefaultSettings::all()) - 1, $added);
        $this->assertSame('Our Own School', Setting::get('school_name'), 'a saved value is kept');
        $this->assertSame(15, Setting::get('session_timeout'));
        $this->assertSame('integer', Setting::where('key', 'session_timeout')->value('type'));

        $this->assertSame(0, DefaultSettings::install(), 'running it again adds nothing');
    }

    public function test_an_empty_database_gets_the_standard_fees_not_zero(): void
    {
        DB::table('fee_components')->delete();
        FeeCalculator::forgetCache(); // its per-request memory may hold an earlier test's fees
        $this->assertSame(0.0, (float) FeeCalculator::calculate('grade1', 'A')['total_due'], 'the problem: ₱0 tuition');

        $this->assertTrue(DefaultSettings::installFees());
        $this->assertGreaterThan(10000, FeeCalculator::calculate('grade1', 'A')['total_due']);
        $this->assertGreaterThan(0, FeeCalculator::calculate('nursery', 'D')['downpayment']);

        // Fees the school already set are never touched
        DB::table('fee_components')->where('fee_type', 'tuition')->update(['amount' => 9999]);
        $this->assertFalse(DefaultSettings::installFees());
        $this->assertSame(9999.0, (float) DB::table('fee_components')->where('fee_type', 'tuition')->value('amount'));
    }

    public function test_seeding_creates_no_accounts_and_dummy_seeders_refuse_on_the_live_server(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(0, User::count(), 'db:seed installs settings only');
        $this->assertTrue(Setting::where('key', 'session_timeout')->exists());

        // Called directly: `db:seed` itself stops to ask "Are you sure?" in production
        $this->app->detectEnvironment(fn () => 'production');
        foreach ([RoleAccountsSeeder::class, \Database\Seeders\FinanceUserSeeder::class, \Database\Seeders\AnnouncementNewsSeeder::class] as $seeder) {
            $this->app->make($seeder)->setContainer($this->app)->run();
        }
        $this->assertSame(0, User::count(), 'no accounts with known passwords on the live server');
        $this->assertSame(0, DB::table('announcements')->count(), 'no sample announcements either');

        $this->app->detectEnvironment(fn () => 'testing');
        $this->seed(RoleAccountsSeeder::class);
        $this->assertTrue(User::where('email', 'admin@ilc.com')->exists(), 'still available on a developer computer');
    }

    public function test_saving_a_setting_that_does_not_exist_yet_uses_its_proper_type(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web')->putJson('/admin/settings', ['settings' => [['key' => 'session_timeout', 'value' => '20']]])
            ->assertOk()->assertJson(['success' => true]);

        $row = Setting::where('key', 'session_timeout')->first();
        $this->assertSame(['integer', 'security'], [$row->type, $row->group], 'used to be saved as a "decimal" financial setting');
        $this->assertSame(20, \App\Support\SecurityPolicy::sessionMinutes());
    }
}
