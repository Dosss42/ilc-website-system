<?php

namespace Tests\Feature\Security;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Admin › Backup & Restore: create, download, restore from the list and Restore from File —
 * the same code as the Super Admin page — but no deleting (safety copies can't be erased).
 */
class AdminBackupTest extends TestCase
{
    use RefreshDatabase;

    private array $made = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['backup.driver' => 'php']);
    }

    protected function tearDown(): void
    {
        foreach ($this->made as $f) {
            File::delete($f);
        }
        parent::tearDown();
    }

    public function test_admin_can_create_list_and_download_backups_but_not_delete_them(): void
    {
        $admin = User::factory()->admin()->create();

        $res = $this->actingAs($admin, 'web')->postJson('/admin/backup/create')->assertOk()->assertJson(['success' => true]);
        $file = $res->json('filename');
        $this->made[] = BackupService::backupDir() . DIRECTORY_SEPARATOR . $file;

        $this->actingAs($admin, 'web')->get('/admin/section/backup')->assertOk()
            ->assertSee(BackupService::displayName($file))
            ->assertSee('Restore from File')
            ->assertSee('data-file="' . $file . '"', false)
            ->assertDontSee('Delete Backup');

        $this->actingAs($admin, 'web')->get('/admin/backup/download/' . $file)->assertOk()->assertDownload(BackupService::displayName($file));
        $this->actingAs($admin, 'web')->get('/admin/backup/download/..%2F.env')->assertNotFound();

        // Deleting stays Super Admin only
        $this->actingAs($admin, 'web')->deleteJson('/superadmin/backup/' . $file)->assertStatus(403);
        $this->assertFileExists(end($this->made));

        // The sidebar has the new section
        $this->actingAs($admin, 'web')->get('/admin/dashboard')->assertOk()->assertSee('id="nav-backup"', false);

        // Logged with who did it
        $this->assertTrue(ActivityLog::where('user_role', 'admin')->where('description', 'like', 'Database backup created:%')->exists());
    }

    public function test_admin_can_restore_with_a_safety_copy_first(): void
    {
        $admin = User::factory()->admin()->create();
        $file = 'backup_2026-01-02_030405.sql';
        $path = BackupService::backupDir() . DIRECTORY_SEPARATOR . $file;
        $this->made[] = $path;
        BackupService::phpDumpTo($path);

        // Restore into a scratch database (the open test connection stays on the test DB)
        $default = config('database.default');
        $scratch = DB::connection()->getDatabaseName() . '_admin_check';
        config(['database.connections.scratch_admin' => config("database.connections.$default")]);
        $adminDb = DB::connection('scratch_admin');
        $adminDb->statement("DROP DATABASE IF EXISTS `$scratch`");
        $adminDb->statement("CREATE DATABASE `$scratch`");
        $realDb = config("database.connections.$default.database");
        config(["database.connections.$default.database" => $scratch]);
        try {
            $res = $this->actingAs($admin, 'web')->postJson('/admin/backup/restore/' . $file)->assertOk()->assertJson(['success' => true]);
            $this->made[] = BackupService::backupDir() . DIRECTORY_SEPARATOR . $res->json('safety');
            $this->assertStringEndsWith('_before-restore.sql', $res->json('safety'));
            $this->assertGreaterThan(30, count($adminDb->select("SHOW TABLES FROM `$scratch`")));
        } finally {
            config(["database.connections.$default.database" => $realDb]);
            $adminDb->statement("DROP DATABASE IF EXISTS `$scratch`");
            DB::purge('scratch_admin');
        }
        $this->assertTrue(ActivityLog::where('user_role', 'admin')->where('description', 'like', 'Database restored from backup: ' . $file . '%')->exists());
    }

    public function test_only_admins_reach_it(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student, 'web')->get('/admin/section/backup')->assertForbidden();
        $this->actingAs($student, 'web')->postJson('/admin/backup/create')->assertForbidden();

        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher, 'web')->postJson('/admin/backup/upload-restore')->assertForbidden();
    }
}
