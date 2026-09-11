<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

/**
 * Uploads/retires backup files on Google Drive via a service account —
 * the off-site copy meant to survive a ransomware attack (or any other
 * disaster) on the machine actually running this app. See BACKUP_SETUP.md
 * for how to obtain the service-account credentials and folder id this
 * needs.
 */
class GoogleDriveBackupUploader
{
    private function client(): GoogleClient
    {
        $credentialsPath = config('backup.google_drive.credentials_path');

        if (empty($credentialsPath) || !file_exists($credentialsPath)) {
            throw new \RuntimeException('Google Drive credentials file not found at: ' . $credentialsPath);
        }

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(GoogleDrive::DRIVE_FILE);

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

        $content = file_get_contents($localPath);

        $drive->files->create($fileMetadata, [
            'data'       => $content,
            'mimeType'   => 'application/sql',
            'uploadType' => 'multipart',
            'fields'     => 'id',
        ]);

        return true;
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
