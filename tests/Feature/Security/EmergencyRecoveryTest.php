<?php

namespace Tests\Feature\Security;

use App\Mail\EmergencyRecoveryAlert;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\BackupService;
use App\Services\EmergencyRecovery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Emergency Recovery (/recovery): with the sealed Recovery Key, the school can repair the
 * Super Admin account or restore a backup without an IT person or a login. Wrong keys are
 * limited and every use is logged and emailed; the page needs no session (so it works with a
 * damaged database).
 */
class EmergencyRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'K7Q2-9XMP-4RTA-WB6N-CH3D-ZE8F';
    private array $made = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['backup.recovery_key' => self::KEY, 'backup.driver' => 'php']);
        cache()->forget('recovery_tries_all');
        cache()->forget('recovery_tries_' . md5('127.0.0.1'));
        Mail::fake();
    }

    protected function tearDown(): void
    {
        foreach ($this->made as $f) {
            File::delete($f);
        }
        cache()->forget('recovery_tries_all');
        cache()->forget('recovery_tries_' . md5('127.0.0.1'));
        parent::tearDown();
    }

    private function unlockToken(): string
    {
        $html = $this->post('/recovery', ['recovery_key' => self::KEY])->assertOk()->getContent();
        preg_match('/name="token" value="([^"]+)"/', $html, $m);
        $this->assertNotEmpty($m[1] ?? null, 'the unlocked page carries a step token');
        return $m[1];
    }

    public function test_without_a_key_on_the_server_the_page_does_nothing(): void
    {
        config(['backup.recovery_key' => null]);
        $this->get('/recovery')->assertOk()->assertSee('not set up')->assertDontSee('name="recovery_key"', false);
        $this->post('/recovery', ['recovery_key' => 'anything'])->assertOk()->assertDontSee('name="token"', false);

        config(['backup.recovery_key' => 'short-key']); // fewer than 16 letters/digits: refused
        $this->assertFalse(EmergencyRecovery::isSetUp());
    }

    public function test_wrong_keys_are_limited_logged_and_emailed(): void
    {
        User::factory()->create(['role' => 'superadmin', 'email' => 'head@ilc.edu']);

        $this->post('/recovery', ['recovery_key' => 'WRONG-KEY-1234-5678'])->assertStatus(422)->assertSee('2 tries left');
        $this->post('/recovery', ['recovery_key' => 'WRONG-KEY-1234-5678'])->assertStatus(422)->assertSee('1 try left');
        $this->post('/recovery', ['recovery_key' => 'WRONG-KEY-1234-5678'])->assertStatus(422)->assertSee('now locked');
        // Even the right key is refused while locked
        $this->post('/recovery', ['recovery_key' => self::KEY])->assertStatus(429)->assertDontSee('name="token"', false);

        $this->assertSame(3, ActivityLog::where('event_type', 'denied')->where('description', 'like', 'Emergency recovery: wrong Recovery Key%')->count());
        Mail::assertSent(EmergencyRecoveryAlert::class, fn ($m) => $m->hasTo('head@ilc.edu'));
    }

    public function test_the_right_key_unlocks_without_a_session_and_is_reported(): void
    {
        User::factory()->create(['role' => 'superadmin', 'email' => 'head@ilc.edu']);

        // Spaces/lowercase are fine — read off paper
        $res = $this->post('/recovery', ['recovery_key' => strtolower(str_replace('-', ' ', self::KEY))])->assertOk()
            ->assertSee('Restore the data from a backup')->assertSee('Get the Super Admin login back')
            ->assertSee('1 active Super Admin account');
        $res->assertCookieMissing(config('session.cookie'));

        // The routes really skip the session and CSRF middleware (so a damaged DB can't break them)
        $router = app('router');
        foreach (['recovery.show', 'recovery.unlock', 'recovery.superadmin', 'recovery.restore-upload', 'recovery.restore-server'] as $name) {
            $mw = $router->gatherRouteMiddleware($router->getRoutes()->getByName($name));
            $this->assertNotContains(\Illuminate\Session\Middleware\StartSession::class, $mw, $name);
            $this->assertNotContains(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class, $mw, $name);
            $this->assertNotContains(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, $mw, $name);
        }

        $this->assertTrue(ActivityLog::where('description', 'like', 'Emergency recovery page unlocked%')->exists());
        Mail::assertSent(EmergencyRecoveryAlert::class);
    }

    public function test_repair_super_admin_needs_a_valid_token(): void
    {
        $boss = User::factory()->create(['role' => 'teacher', 'email' => 'boss@ilc.edu', 'is_active' => false]);
        $boss->delete();

        // A made-up or expired token does nothing
        $this->post('/recovery/superadmin', ['token' => '123.' . str_repeat('a', 64), 'email' => 'boss@ilc.edu'])->assertStatus(419);
        $expired = (now()->subMinute()->getTimestamp()) . '.' . hash_hmac('sha256', 'ilc-recovery|' . now()->subMinute()->getTimestamp(), EmergencyRecovery::normalize(self::KEY));
        $this->post('/recovery/superadmin', ['token' => $expired, 'email' => 'boss@ilc.edu'])->assertStatus(419);
        $this->assertNull(User::where('email', 'boss@ilc.edu')->first(), 'still archived');

        $html = $this->post('/recovery/superadmin', ['token' => $this->unlockToken(), 'email' => 'boss@ilc.edu'])->assertOk()
            ->assertSee('Super Admin repaired:')->assertSee('restored from archive')->getContent();
        preg_match('/<div class="password">([^<]+)<\/div>/', $html, $m);

        $boss = User::where('email', 'boss@ilc.edu')->first();
        $this->assertSame('superadmin', $boss->role);
        $this->post('/login', ['email' => 'boss@ilc.edu', 'password' => trim($m[1])])->assertRedirect();
        $this->assertAuthenticatedAs($boss, 'web');
        $this->assertTrue(ActivityLog::where('description', 'like', 'Emergency recovery: Super Admin account repaired: boss@ilc.edu%')->exists());
    }

    public function test_restore_from_an_uploaded_backup(): void
    {
        $src = tempnam(sys_get_temp_dir(), 'bk');
        BackupService::phpDumpTo($src);
        $token = $this->unlockToken();

        // Restore into a scratch database (the open test connection stays on the test DB)
        $default = config('database.default');
        $scratch = DB::connection()->getDatabaseName() . '_recovery_check';
        config(['database.connections.scratch_admin' => config("database.connections.$default")]);
        $admin = DB::connection('scratch_admin');
        $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
        $admin->statement("CREATE DATABASE `$scratch`");
        $realDb = config("database.connections.$default.database");
        config(["database.connections.$default.database" => $scratch]);
        try {
            $this->post('/recovery/restore-upload', [
                'token' => $token,
                'backup_file' => new \Illuminate\Http\UploadedFile($src, 'ILC System Backup File - 2026-10-22 09-00 PM.sql', 'application/sql', null, true),
            ])->assertOk()->assertSee('Database restored');
            $this->assertGreaterThan(30, count($admin->select("SHOW TABLES FROM `$scratch`")));
        } finally {
            config(["database.connections.$default.database" => $realDb]);
            $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
            DB::purge('scratch_admin');
            foreach (File::files(BackupService::backupDir()) as $f) {
                if (preg_match('/_(uploaded|before-restore)\.sql$/', $f->getFilename()) && $f->getMTime() >= time() - 60) {
                    $this->made[] = $f->getPathname();
                }
            }
        }
        $this->assertTrue(ActivityLog::where('description', 'like', 'Emergency recovery: database restored from ILC System Backup File%')->exists());

        // Without a token nothing is restored
        $this->post('/recovery/restore', ['file' => 'backup_2026-10-21_003354.sql'])->assertStatus(419);
    }

    public function test_key_generator_makes_readable_strong_keys(): void
    {
        $key = EmergencyRecovery::generateKey();
        $this->assertMatchesRegularExpression('/^([A-HJ-NP-Z2-9]{4}-){5}[A-HJ-NP-Z2-9]{4}$/', $key);
        $this->assertNotSame($key, EmergencyRecovery::generateKey());
        $this->artisan('recovery:key')->expectsOutputToContain('New Recovery Key')->assertSuccessful();
    }
}
