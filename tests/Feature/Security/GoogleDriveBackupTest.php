<?php

namespace Tests\Feature\Security;

use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Google Drive backup signs in as a normal Google account (OAuth) — a service account has no
 * Drive storage of its own. These check the setup command and that a Drive problem is
 * reported (not silently called "not configured") without failing the backup itself.
 */
class GoogleDriveBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_command_needs_the_oauth_client_first(): void
    {
        config(['backup.google_drive.client_id' => null, 'backup.google_drive.client_secret' => null]);
        $this->artisan('backup:google-drive-setup')
            ->expectsOutputToContain('Set BACKUP_GDRIVE_CLIENT_ID and BACKUP_GDRIVE_CLIENT_SECRET')
            ->assertFailed();
    }

    public function test_setup_command_prints_a_google_sign_in_link_for_offline_drive_file_access(): void
    {
        config(['backup.google_drive.client_id' => 'abc123.apps.googleusercontent.com', 'backup.google_drive.client_secret' => 'shh']);

        // The link asks for long-lived ("offline") access to only the files the app creates
        parse_str((string) parse_url(\App\Services\GoogleDriveBackupUploader::oauthClient()->createAuthUrl(), PHP_URL_QUERY), $q);
        $this->assertSame('abc123.apps.googleusercontent.com', $q['client_id']);
        $this->assertSame('offline', $q['access_type']);
        $this->assertSame('consent', $q['prompt']);
        $this->assertSame('https://www.googleapis.com/auth/drive.file', $q['scope']);
        // Not plain http://localhost — XAMPP's Apache answers that and drops the code
        $this->assertSame('http://127.0.0.1:53682', $q['redirect_uri']);

        // (each printed line can satisfy only one expectsOutputToContain)
        $this->artisan('backup:google-drive-setup', ['--paste' => true])
            ->expectsOutputToContain('https://accounts.google.com/o/oauth2/')
            ->expectsQuestion('Paste the address (or just the code) here', 'http://127.0.0.1:53682/?error=access_denied')
            ->expectsOutputToContain('Google said: access_denied')
            ->assertFailed();
    }

    public function test_a_pasted_address_without_a_code_is_explained(): void
    {
        $r = \App\Console\Commands\GoogleDriveSetup::codeFrom('http://localhost/dashboard/');
        $this->assertStringStartsWith('That address has no code= in it (http://localhost/dashboard/)', $r['error']);
        $this->assertSame(['code' => '4/0Abc'], \App\Console\Commands\GoogleDriveSetup::codeFrom('http://127.0.0.1:53682/?code=4%2F0Abc&scope=x'));
        $this->assertSame(['code' => '4/0Abc'], \App\Console\Commands\GoogleDriveSetup::codeFrom('4%2F0Abc'));
    }

    public function test_the_command_catches_the_code_from_the_browser_itself(): void
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr); // any free port
        $address = stream_socket_get_name($server, false);

        // Play the browser: an unrelated request first, then Google's redirect. Both sit in
        // the listen queue until waitForCode() accepts them.
        $favicon = stream_socket_client("tcp://$address");
        fwrite($favicon, "GET /favicon.ico HTTP/1.1\r\nHost: 127.0.0.1\r\n\r\n");
        $browser = stream_socket_client("tcp://$address");
        fwrite($browser, "GET /?code=4%2F0AbcDEF&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fdrive.file HTTP/1.1\r\nHost: 127.0.0.1\r\n\r\n");

        $result = \App\Console\Commands\GoogleDriveSetup::waitForCode($server, 5);
        fclose($server);

        $this->assertSame(['code' => '4/0AbcDEF'], $result);
        $page = stream_get_contents($browser);
        $this->assertStringContainsString('HTTP/1.1 200 OK', $page);
        $this->assertStringContainsString('Connected to Google Drive', $page);
    }

    public function test_a_drive_problem_is_reported_but_the_backup_still_succeeds(): void
    {
        config([
            'backup.driver' => 'php',
            'backup.google_drive.enabled' => true,
            'backup.google_drive.refresh_token' => null,
            'backup.google_drive.folder_id' => 'some-folder',
            'backup.google_drive.credentials_path' => storage_path('app/does-not-exist.json'),
        ]);

        $result = BackupService::runFull();
        File::delete(BackupService::backupDir() . DIRECTORY_SEPARATOR . $result['filename']);

        $this->assertTrue($result['success']);
        $this->assertFalse($result['drive']);
        $this->assertStringContainsString('Google Drive is not connected — run php artisan backup:google-drive-setup', $result['drive_error']);
    }

    public function test_backups_get_a_readable_name_in_drive_downloads_and_the_list(): void
    {
        $name = fn ($f) => BackupService::displayName($f);
        $this->assertSame('ILC System Backup File - 2026-10-22 08-15 PM.sql', $name('backup_2026-10-22_201530.sql'));
        $this->assertSame('ILC System Backup File - 2026-10-22 08-15 AM (before restore).sql', $name('backup_2026-10-22_081500_before-restore.sql'));
        $this->assertSame('ILC System Backup File - 2026-01-05 12-00 PM (uploaded).sql', $name('backup_2026-01-05_120000_uploaded.sql'));
        $this->assertSame('something-else.sql', $name('something-else.sql'));

        // Google Drive receives the readable name (fake uploader: nothing leaves the machine)
        $uploaded = [];
        $this->app->instance(\App\Services\GoogleDriveBackupUploader::class, new class($uploaded) extends \App\Services\GoogleDriveBackupUploader {
            public function __construct(private array &$names) {}
            public function upload(string $localPath, string $filename): bool { $this->names[] = $filename; return true; }
            public function pruneOlderThan(int $keepDays): void {}
        });
        config(['backup.driver' => 'php', 'backup.google_drive.enabled' => true]);
        $result = BackupService::runFull();
        $path = BackupService::backupDir() . DIRECTORY_SEPARATOR . $result['filename'];

        try {
            $this->assertTrue($result['drive']);
            $this->assertSame([BackupService::displayName($result['filename'])], $uploaded);
            $this->assertStringStartsWith('ILC System Backup File - ' . now()->format('Y-m-d'), $uploaded[0]);

            // Download and the Backup History list use it too
            $superadmin = \App\Models\User::factory()->create(['role' => 'superadmin']);
            $this->actingAs($superadmin, 'web')->get('/superadmin/backup/download/' . $result['filename'])->assertOk()
                ->assertDownload(BackupService::displayName($result['filename']));
            $this->actingAs($superadmin, 'web')->get('/superadmin/dashboard?section=backup')->assertOk()
                ->assertSee(BackupService::displayName($result['filename']));
        } finally {
            File::delete($path);
        }
    }

    public function test_drive_off_is_just_skipped(): void
    {
        config(['backup.driver' => 'php', 'backup.google_drive.enabled' => false]);
        $result = BackupService::runFull();
        File::delete(BackupService::backupDir() . DIRECTORY_SEPARATOR . $result['filename']);

        $this->assertTrue($result['success']);
        $this->assertFalse($result['drive']);
        $this->assertNull($result['drive_error']);
    }
}
