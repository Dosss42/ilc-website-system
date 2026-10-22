<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

/**
 * Uploads/retires backup files on Google Drive — the off-site copy meant to survive a
 * ransomware attack (or any other disaster) on the machine running this app.
 *
 * Two ways to sign in:
 *  - OAuth as a normal Google account (BACKUP_GDRIVE_CLIENT_ID / _CLIENT_SECRET /
 *    _REFRESH_TOKEN) — what a personal Gmail Drive needs. Set up with
 *    `php artisan backup:google-drive-setup`; see docs/GOOGLE_DRIVE_BACKUP.md.
 *  - a service account JSON key (BACKUP_GDRIVE_CREDENTIALS_PATH) — only works with a
 *    Google Workspace shared drive: Google gives service accounts no storage of their own.
 */
class GoogleDriveBackupUploader
{
    /** drive.file: the app only sees files and folders it created itself */
    public const SCOPE = GoogleDrive::DRIVE_FILE;

    /** Where Google sends the browser after "Allow" — the setup command listens there. Not
     * plain http://localhost: XAMPP's Apache answers that and redirects to /dashboard/,
     * which throws the code away. Desktop-app OAuth clients accept any loopback port. */
    public const REDIRECT_PORT = 53682;

    public static function redirectUri(): string
    {
        return 'http://127.0.0.1:' . self::REDIRECT_PORT;
    }

    /** OAuth client for the setup command (no token yet) */
    public static function oauthClient(): GoogleClient
    {
        $client = self::newClient();
        $client->setClientId((string) config('backup.google_drive.client_id'));
        $client->setClientSecret((string) config('backup.google_drive.client_secret'));
        $client->setRedirectUri(self::redirectUri());
        $client->addScope(self::SCOPE);
        $client->setAccessType('offline'); // → a refresh token that keeps working
        $client->setPrompt('consent');     // always return the refresh token
        return $client;
    }

    /**
     * A Google client whose HTTPS certificate checks work on Windows too: a PHP without
     * curl.cainfo set (common outside XAMPP's own PHP) can't verify Google's certificate
     * ("cURL error 60"). Verification stays ON — it is just given a CA list to check
     * against: BACKUP_CA_BUNDLE, or the one XAMPP ships. Linux servers (Railway) use the
     * system's list and are left alone.
     */
    private static function newClient(): GoogleClient
    {
        $client = new GoogleClient();
        if (!ini_get('curl.cainfo') && !ini_get('openssl.cafile')) {
            foreach ([config('backup.ca_bundle'), 'C:\\xampp\\apache\\bin\\curl-ca-bundle.crt', 'C:\\xampp\\php\\extras\\ssl\\cacert.pem'] as $bundle) {
                if ($bundle && is_file($bundle)) {
                    // Same options Google's own default client uses, plus the CA list
                    $client->setHttpClient(new \GuzzleHttp\Client([
                        'base_uri' => GoogleClient::API_BASE_PATH, 'http_errors' => false, 'verify' => $bundle,
                    ]));
                    break;
                }
            }
        }
        return $client;
    }

    private function client(): GoogleClient
    {
        if (config('backup.google_drive.refresh_token')) {
            if (!config('backup.google_drive.client_id') || !config('backup.google_drive.client_secret')) {
                throw new \RuntimeException('BACKUP_GDRIVE_CLIENT_ID / BACKUP_GDRIVE_CLIENT_SECRET are not configured.');
            }
            $client = self::oauthClient();
            $token = $client->fetchAccessTokenWithRefreshToken((string) config('backup.google_drive.refresh_token'));
            if (isset($token['error'])) {
                throw new \RuntimeException('Google sign-in failed (' . $token['error'] . ': ' . ($token['error_description'] ?? '') . ') — run php artisan backup:google-drive-setup again.');
            }
            return $client;
        }

        $credentialsPath = config('backup.google_drive.credentials_path');
        if (empty($credentialsPath) || !file_exists($credentialsPath)) {
            throw new \RuntimeException('Google Drive is not connected — run php artisan backup:google-drive-setup (see docs/GOOGLE_DRIVE_BACKUP.md).');
        }

        $client = self::newClient();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(self::SCOPE);

        return $client;
    }

    public function upload(string $localPath, string $filename): bool
    {
        $folderId = config('backup.google_drive.folder_id');
        if (empty($folderId)) {
            throw new \RuntimeException('BACKUP_GDRIVE_FOLDER_ID is not configured.');
        }

        $drive = new GoogleDrive($this->client());

        $fileMetadata = new DriveFile([
            'name'    => $filename,
            'parents' => [$folderId],
        ]);

        $drive->files->create($fileMetadata, [
            'data'       => file_get_contents($localPath),
            'mimeType'   => 'application/sql',
            'uploadType' => 'multipart',
            'fields'     => 'id',
        ]);

        return true;
    }

    /**
     * Create the backup folder in the signed-in account's Drive (the app can only use
     * folders it created itself) and return its id. Used by the setup command.
     */
    public static function createFolder(GoogleClient $client, string $name): string
    {
        $folder = (new GoogleDrive($client))->files->create(new DriveFile([
            'name'     => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]), ['fields' => 'id']);

        return $folder->getId();
    }

    /**
     * Delete uploaded backups older than $keepDays from the target Drive
     * folder — mirrors the local retention policy so the off-site copy
     * doesn't grow forever either.
     */
    public function pruneOlderThan(int $keepDays): void
    {
        if ($keepDays <= 0) {
            return;
        }

        $folderId = config('backup.google_drive.folder_id');
        if (empty($folderId)) {
            return;
        }

        $drive  = new GoogleDrive($this->client());
        $cutoff = now()->subDays($keepDays)->format(\DateTime::RFC3339);

        $results = $drive->files->listFiles([
            'q'      => "'{$folderId}' in parents and createdTime < '{$cutoff}' and trashed = false",
            'fields' => 'files(id, name, createdTime)',
        ]);

        foreach ($results->getFiles() as $file) {
            try {
                $drive->files->delete($file->getId());
            } catch (\Throwable $e) {
                Log::warning('Failed to prune Drive backup', ['file' => $file->getName(), 'error' => $e->getMessage()]);
            }
        }
    }
}
