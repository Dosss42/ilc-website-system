<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Backups must work where mysqldump doesn't exist (Railway's container): the app falls back
 * to its own PHP dump, and restores run through PHP (no mysql client). DDL here only ever
 * runs on a separate connection against a scratch database, never the test database.
 */
class BackupTest extends TestCase
{
    use RefreshDatabase;

    private array $made = [];

    protected function tearDown(): void
    {
        foreach ($this->made as $f) {
            File::delete($f);
        }
        parent::tearDown();
    }

    public function test_backup_falls_back_to_the_php_dump_when_mysqldump_fails(): void
    {
        // Something that isn't mysqldump: exits with an error, like a missing binary on Railway
        config(['backup.driver' => 'auto', 'backup.mysqldump_path' => PHP_BINARY]);

        $result = BackupService::createDump();
        $this->made[] = $result['path'];

        $this->assertTrue($result['success'], (string) $result['message']);
        $sql = File::get($result['path']);
        $this->assertStringContainsString("made by the app's PHP backup", $sql);
        $this->assertStringContainsString("SET TIME_ZONE='+00:00';", $sql);
        $this->assertStringContainsString('CREATE TABLE `users`', $sql);
        $this->assertStringContainsString('CREATE TABLE `activity_logs`', $sql);
        $this->assertStringContainsString('-- Dump completed', $sql);
        $this->assertFileDoesNotExist($result['path'] . '.err');
    }

    public function test_only_mysqldump_mode_reports_the_failure(): void
    {
        config(['backup.driver' => 'mysqldump', 'backup.mysqldump_path' => PHP_BINARY]);
        $result = BackupService::createDump();
        $this->assertFalse($result['success']);
        $this->assertStringStartsWith('Backup failed: mysqldump exited with code', $result['message']);
    }

    public function test_a_php_backup_restores_into_an_empty_database(): void
    {
        $file = storage_path('app/backups/test_roundtrip.sql');
        $this->made[] = $file;
        BackupService::phpDumpTo($file);

        $scratch = DB::connection()->getDatabaseName() . '_restore_check';
        config(['database.connections.scratch_admin' => config('database.connections.' . config('database.default'))]);
        $admin = DB::connection('scratch_admin');
        $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
        $admin->statement("CREATE DATABASE `$scratch`");
        try {
            $tzBefore = DB::selectOne('SELECT @@session.time_zone tz')->tz;
            $this->assertGreaterThan(30, BackupService::restore($file, $scratch));

            $tables = fn ($db) => collect($admin->select("SHOW FULL TABLES FROM `$db` WHERE Table_type = 'BASE TABLE'"))
                ->map(fn ($r) => array_values((array) $r)[0])->sort()->values()->all();
            $this->assertSame($tables(DB::connection()->getDatabaseName()), $tables($scratch));
            // The restore's UTC session setting stayed on its own connection
            $this->assertSame($tzBefore, DB::selectOne('SELECT @@session.time_zone tz')->tz);
        } finally {
            $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
            DB::purge('scratch_admin');
        }
    }

    public function test_restore_from_file_rejects_files_that_are_not_backups(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $before = File::files(BackupService::backupDir());

        $this->actingAs($superadmin, 'web')->post('/superadmin/backup/upload-restore', [
            'backup_file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('notes.txt', 'hello'),
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJson(['message' => 'Choose a .sql backup file.']);

        // A plain file (not UploadedFile::fake(), whose open temp handle keeps Windows from
        // deleting the moved file until the test ends — a real upload has no such handle)
        $tmp = tempnam(sys_get_temp_dir(), 'bk');
        file_put_contents($tmp, "DROP TABLE users;\n");
        $this->actingAs($superadmin, 'web')->post('/superadmin/backup/upload-restore', [
            'backup_file' => new \Illuminate\Http\UploadedFile($tmp, 'evil.sql', 'application/sql', null, true),
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJson(['message' => 'That file is not a database backup (.sql from Create Backup or mysqldump).']);

        $after = File::files(BackupService::backupDir());
        $this->assertCount(count($before), $after, 'rejected uploads are not kept: ' . implode(', ', array_map(fn ($f) => $f->getFilename(), $after)));
        $this->actingAs(User::factory()->admin()->create(), 'web')->post('/superadmin/backup/upload-restore')->assertStatus(403);
    }

    public function test_console_restore_saves_the_current_data_first_and_needs_no_login(): void
    {
        $file = storage_path('app/backups/test_console_restore.sql');
        $this->made[] = $file;
        BackupService::phpDumpTo($file);

        // Point the default connection's config at a scratch database (the open test
        // connection keeps using the test database), so the restore never touches it
        $default = config('database.default');
        $scratch = DB::connection()->getDatabaseName() . '_console_check';
        config(['database.connections.scratch_admin' => config("database.connections.$default")]);
        $admin = DB::connection('scratch_admin');
        $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
        $admin->statement("CREATE DATABASE `$scratch`");
        $realDb = config("database.connections.$default.database");
        config(["database.connections.$default.database" => $scratch]);
        try {
            $this->artisan('backup:restore', ['file' => $file])
                ->expectsConfirmation('Restore now?', 'yes')
                ->expectsOutputToContain('Restored test_console_restore.sql')
                ->expectsOutputToContain('Previous data saved as backup_')
                ->assertSuccessful();

            $this->assertGreaterThan(30, count($admin->select("SHOW TABLES FROM `$scratch`")));
            $safety = collect(File::files(BackupService::backupDir()))->first(fn ($f) => str_ends_with($f->getFilename(), '_before-restore.sql'));
            $this->assertNotNull($safety, 'a safety copy was saved before restoring');
            $this->made[] = $safety->getPathname();
        } finally {
            config(["database.connections.$default.database" => $realDb]);
            $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
            DB::purge('scratch_admin');
        }

        // Cancelling or a bad file changes nothing
        $this->artisan('backup:restore', ['file' => $file])->expectsConfirmation('Restore now?', 'no')->assertFailed();
        $this->artisan('backup:restore', ['file' => 'backup_1999-01-01_000000.sql'])->assertFailed();
        $this->artisan('backup:restore', ['--list' => true])->assertSuccessful();
    }

    public function test_restore_from_file_through_the_web_page(): void
    {
        $src = tempnam(sys_get_temp_dir(), 'bk');
        BackupService::phpDumpTo($src);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $default = config('database.default');
        $scratch = DB::connection()->getDatabaseName() . '_upload_check';
        config(['database.connections.scratch_admin' => config("database.connections.$default")]);
        $admin = DB::connection('scratch_admin');
        $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
        $admin->statement("CREATE DATABASE `$scratch`");
        $realDb = config("database.connections.$default.database");
        config(["database.connections.$default.database" => $scratch]);
        try {
            $res = $this->actingAs($superadmin, 'web')->post('/superadmin/backup/upload-restore', [
                'backup_file' => new \Illuminate\Http\UploadedFile($src, 'Ilc-backup-files_2026-10-22_101500.sql', 'application/sql', null, true),
            ], ['Accept' => 'application/json'])->assertOk()->assertJson(['success' => true]);

            $this->assertStringContainsString('Ilc-backup-files_2026-10-22_101500.sql', $res->json('message'));
            $this->assertStringEndsWith('_before-restore.sql', $res->json('safety'));
            $this->assertGreaterThan(30, count($admin->select("SHOW TABLES FROM `$scratch`")));
            foreach (File::files(BackupService::backupDir()) as $f) {
                if (preg_match('/_(uploaded|before-restore)\.sql$/', $f->getFilename())) {
                    $this->made[] = $f->getPathname();
                }
            }
            $this->assertCount(2, $this->made, 'the upload and the safety copy are both listed');
        } finally {
            config(["database.connections.$default.database" => $realDb]);
            $admin->statement("DROP DATABASE IF EXISTS `$scratch`");
            DB::purge('scratch_admin');
        }
    }

    public function test_super_admin_can_be_recovered_from_the_console(): void
    {
        $broken = User::factory()->create(['role' => 'admin', 'email' => 'boss@ilc.edu', 'is_active' => false]);
        $broken->delete(); // archived
        cache()->put('login_attempts_' . md5('boss@ilc.edu'), 99, 600);

        \Illuminate\Support\Facades\Artisan::call('superadmin:recover', ['email' => 'Boss@ILC.edu']);
        $out = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringContainsString('restored from archive', $out);
        $this->assertStringContainsString('role admin → superadmin', $out);
        $this->assertStringContainsString('reactivated', $out);
        preg_match('/Temporary password:\s+(\S+)/', $out, $m);

        $user = User::where('email', 'boss@ilc.edu')->first();
        $this->assertNotNull($user, 'no longer archived');
        $this->assertSame('superadmin', $user->role);
        $this->assertTrue((bool) $user->is_active);
        $this->assertNull(cache()->get('login_attempts_' . md5('boss@ilc.edu')), 'lockout cleared');

        // The printed password really logs in
        $this->post('/login', ['email' => 'boss@ilc.edu', 'password' => $m[1]])->assertRedirect();
        $this->assertAuthenticatedAs($user, 'web');

        // A missing account is created
        $this->artisan('superadmin:recover', ['email' => 'new.head@ilc.edu'])->expectsOutputToContain('Created Super Admin')->assertSuccessful();
        $this->assertSame('superadmin', User::where('email', 'new.head@ilc.edu')->value('role'));
    }

    public function test_super_admin_create_backup_works_without_mysqldump(): void
    {
        config(['backup.driver' => 'php']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $res = $this->actingAs($superadmin, 'web')->postJson('/superadmin/backup/create')->assertOk()->assertJson(['success' => true]);
        $this->made[] = storage_path('app/backups/' . $res->json('filename'));
        $this->assertFileExists(end($this->made));
        $this->assertMatchesRegularExpression('/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.sql$/', $res->json('filename'));
    }
}
