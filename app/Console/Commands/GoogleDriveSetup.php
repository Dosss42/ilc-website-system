<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveBackupUploader;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * One-time connection of the backups to a Google account's Drive (OAuth). Needs the OAuth
 * client's id/secret in .env first; prints the values to put in .env / Railway Variables.
 * Step-by-step guide: docs/GOOGLE_DRIVE_BACKUP.md.
 *
 * After "Allow", Google sends the browser to http://127.0.0.1:53682/?code=… — this command
 * listens there and picks the code up itself (nothing to copy). If it can't listen, it asks
 * for the address to be pasted instead.
 */
#[Signature('backup:google-drive-setup
    {--folder=ILC System Backups : Name of the Drive folder to create for the backups}
    {--paste : Paste the address by hand instead of waiting for the browser}
    {--test : Only test the current settings by uploading a small file}')]
#[Description('Connect database backups to your Google Drive (one-time setup)')]
class GoogleDriveSetup extends Command
{
    private const WAIT_SECONDS = 300;

    public function handle(): int
    {
        if ($this->option('test')) {
            return $this->testUpload();
        }

        if (!config('backup.google_drive.client_id') || !config('backup.google_drive.client_secret')) {
            $this->error('Set BACKUP_GDRIVE_CLIENT_ID and BACKUP_GDRIVE_CLIENT_SECRET in .env first (step 1 of docs/GOOGLE_DRIVE_BACKUP.md),');
            $this->line('then run:  php artisan config:clear   and this command again.');
            return self::FAILURE;
        }

        $client = GoogleDriveBackupUploader::oauthClient();
        $server = $this->option('paste') ? false
            : @stream_socket_server('tcp://127.0.0.1:' . GoogleDriveBackupUploader::REDIRECT_PORT, $errno, $errstr);

        $this->info('1) Open this link, sign in with the Google account whose Drive should keep the backups, and click Allow:');
        $this->newLine();
        $this->line($client->createAuthUrl());
        $this->newLine();
        $this->line('   (If Google says the app isn\'t verified: Advanced → Go to … (unsafe). It is your own app.)');

        if ($server) {
            $this->line('2) Waiting for you to click Allow (up to ' . (self::WAIT_SECONDS / 60) . ' minutes)…');
            $result = self::waitForCode($server, self::WAIT_SECONDS);
            fclose($server);
        } else {
            $this->line('2) After Allow the browser opens an address starting with ' . GoogleDriveBackupUploader::redirectUri() . '/?code=…');
            $this->line('   (the page may not load — that is expected). Copy the WHOLE address from the address bar.');
            $result = self::codeFrom(trim((string) $this->ask('Paste the address (or just the code) here')));
        }

        if (isset($result['error'])) {
            $this->error($result['error']);
            return self::FAILURE;
        }

        $token = $client->fetchAccessTokenWithAuthCode($result['code']);
        if (isset($token['error'])) {
            $this->error('Google refused the code: ' . $token['error'] . ' ' . ($token['error_description'] ?? '') . ' — codes work once and expire quickly; run the command again.');
            return self::FAILURE;
        }
        if (empty($token['refresh_token'])) {
            $this->error('Google returned no refresh token. Remove the app at https://myaccount.google.com/permissions and run this again.');
            return self::FAILURE;
        }

        $folderId = GoogleDriveBackupUploader::createFolder($client, (string) $this->option('folder'));

        $this->newLine();
        $this->info('Connected. Created the Drive folder "' . $this->option('folder') . '".');
        $this->line('3) Put these in .env (XAMPP) and in Railway › your service › Variables:');
        $this->newLine();
        $this->line('BACKUP_GDRIVE_ENABLED=true');
        $this->line('BACKUP_GDRIVE_CLIENT_ID=' . config('backup.google_drive.client_id'));
        $this->line('BACKUP_GDRIVE_CLIENT_SECRET=' . config('backup.google_drive.client_secret'));
        $this->line('BACKUP_GDRIVE_REFRESH_TOKEN=' . $token['refresh_token']);
        $this->line('BACKUP_GDRIVE_FOLDER_ID=' . $folderId);
        $this->newLine();
        $this->line('4) Then:  php artisan config:clear  and  php artisan backup:google-drive-setup --test');
        $this->warn('Keep the client secret and refresh token private — anyone with them can add files to that Drive folder.');

        return self::SUCCESS;
    }

    /**
     * Accept the browser's redirect on the listening socket and read ?code= from it,
     * answering with a "you can close this tab" page. Ignores unrelated requests (e.g.
     * /favicon.ico).
     *
     * @param  resource  $server
     * @return array{code?: string, error?: string}
     */
    public static function waitForCode($server, int $timeout): array
    {
        $deadline = time() + $timeout;
        while (($left = $deadline - time()) > 0) {
            $conn = @stream_socket_accept($server, $left);
            if (!$conn) {
                break;
            }
            stream_set_timeout($conn, 5);
            $requestLine = (string) fgets($conn);
            preg_match('#^GET\s+(\S+)#', $requestLine, $m);
            $result = self::codeFrom('http://127.0.0.1' . ($m[1] ?? '/'));

            $ok = isset($result['code']);
            $html = '<!doctype html><meta charset="utf-8"><title>ILC System Backup</title>'
                . '<body style="font-family:sans-serif;text-align:center;padding:60px;">'
                . ($ok ? '<h2>&#10003; Connected to Google Drive</h2><p>You can close this tab and go back to the terminal.</p>'
                       : '<h2>Not connected</h2><p>' . htmlspecialchars($result['error']) . '</p>');
            fwrite($conn, "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=utf-8\r\nConnection: close\r\nContent-Length: " . strlen($html) . "\r\n\r\n" . $html);
            fclose($conn);

            if ($ok || !str_starts_with($result['error'], 'That address has no code')) {
                return $result; // the code, or Google's own error (e.g. access_denied)
            }
        }
        return ['error' => 'No answer from the browser in time — run the command again (or add --paste).'];
    }

    /**
     * The code from a pasted/redirected address (or a bare code).
     *
     * @return array{code?: string, error?: string}
     */
    public static function codeFrom(string $input): array
    {
        if ($input === '') {
            return ['error' => 'Nothing was pasted.'];
        }
        if (!preg_match('#^https?://#i', $input)) {
            return ['code' => urldecode($input)]; // a bare code
        }
        parse_str((string) parse_url($input, PHP_URL_QUERY), $query);
        if (isset($query['error'])) {
            return ['error' => 'Google said: ' . $query['error'] . ' (was "Allow" clicked?)'];
        }
        if (empty($query['code'])) {
            return ['error' => 'That address has no code= in it (' . $input . '). If it ended up on another page '
                . '(e.g. XAMPP\'s /dashboard/), run the command again — it now catches the code itself.'];
        }
        return ['code' => $query['code']];
    }

    private function testUpload(): int
    {
        if (!config('backup.google_drive.enabled')) {
            $this->error('BACKUP_GDRIVE_ENABLED is not true.');
            return self::FAILURE;
        }
        $tmp = tempnam(sys_get_temp_dir(), 'gd');
        file_put_contents($tmp, 'ILC SYSTEM Google Drive backup test ' . now());
        try {
            (new GoogleDriveBackupUploader())->upload($tmp, 'connection-test.txt');
            $this->info('Upload to Google Drive works (a file "connection-test.txt" was added to the backup folder — you can delete it).');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Google Drive upload failed: ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            @unlink($tmp);
        }
    }
}
