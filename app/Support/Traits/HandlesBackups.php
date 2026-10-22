<?php

namespace App\Support\Traits;

use App\Services\ActivityLogger;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Backup & Restore actions shared by the Super Admin and Admin portals (create, download,
 * restore from the list, Restore from File), so both run the exact same code. Deleting
 * backups stays in SuperAdminController only — an Admin can't erase the safety copies.
 * Every action is logged with who did it (Super Admin › System Logs).
 */
trait HandlesBackups
{
    private function backupDir(): string
    {
        return BackupService::backupDir();
    }

    private function isValidBackupFilename(string $file): bool
    {
        return BackupService::isValidBackupFilename($file);
    }

    private function listBackups(): array
    {
        $backups = [];
        foreach (File::files($this->backupDir()) as $file) {
            if ($file->getExtension() === 'sql') {
                $size = $file->getSize();
                $backups[] = [
                    'name'     => $file->getFilename(),
                    'label'    => BackupService::displayName($file->getFilename()),
                    'size'     => $size >= 1048576 ? round($size / 1048576, 2) . ' MB' : ($size >= 1024 ? round($size / 1024, 1) . ' KB' : $size . ' B'),
                    'size_raw' => $size,
                    'created'  => date('M d, Y h:i A', $file->getMTime()),
                    'ts'       => $file->getMTime(),
                ];
            }
        }
        usort($backups, fn ($a, $b) => $b['ts'] - $a['ts']);
        return $backups;
    }

    public function createBackup(Request $request)
    {
        // Same pipeline as the automatic 2:00 AM backup (backup:run): dump → secondary copy →
        // Google Drive → retention
        $result = BackupService::runFull();

        return response()->json([
            'success'     => $result['success'],
            'message'     => $result['message'],
            'filename'    => $result['filename'],
            'secondary'   => $result['secondary'],
            'drive'       => $result['drive'],
            'drive_error' => $result['drive_error'] ?? null,
        ]);
    }

    public function downloadBackup(string $file)
    {
        if (!$this->isValidBackupFilename($file)) {
            abort(404, 'Backup file not found.');
        }
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . basename($file);
        if (!File::exists($path)) {
            abort(404, 'Backup file not found.');
        }
        ActivityLogger::log('update', 'Backup downloaded: ' . basename($file), 'Backup');
        // Stored name stays backup_<date>_<time>.sql (restore/delete validate it); the saved copy
        // gets the readable "ILC System Backup File - <date> <time>.sql" name
        return response()->download($path, BackupService::displayName($file));
    }

    public function restoreBackup(Request $request, string $file)
    {
        try {
            if (!$this->isValidBackupFilename($file)) {
                return response()->json(['success' => false, 'message' => 'Backup file not found.']);
            }
            $path = $this->backupDir() . DIRECTORY_SEPARATOR . basename($file);
            if (!File::exists($path)) {
                return response()->json(['success' => false, 'message' => 'Backup file not found.']);
            }

            return $this->restoreFrom($path, basename($file));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore from File: upload a .sql backup (e.g. one downloaded earlier — Railway's disk is
     * wiped on every deploy, so the server may have none left) and restore it. The file is
     * kept in the backup list as backup_<date>_uploaded.sql.
     */
    public function uploadRestoreBackup(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|max:51200'], [
            'backup_file.max' => 'The backup file is larger than 50 MB.',
        ]);
        $upload = $request->file('backup_file');
        if (strtolower($upload->getClientOriginalExtension()) !== 'sql') {
            return response()->json(['success' => false, 'message' => 'Choose a .sql backup file.'], 422);
        }

        $name = 'backup_' . now()->format('Y-m-d_His') . '_uploaded.sql';
        $upload->move($this->backupDir(), $name);
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $name;
        if (!BackupService::looksLikeBackup($path)) {
            File::delete($path);
            return response()->json(['success' => false, 'message' => 'That file is not a database backup (.sql from Create Backup or mysqldump).'], 422);
        }

        return $this->restoreFrom($path, $upload->getClientOriginalName() . ' (saved as ' . $name . ')');
    }

    /** Shared by Restore and Restore from File: safety copy first, then restore (see BackupService). */
    private function restoreFrom(string $path, string $label)
    {
        try {
            $result = BackupService::restoreSafely($path);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()]);
        }

        // Logged after the restore: anything written before it was replaced with the backup's own log
        ActivityLogger::log('update', 'Database restored from backup: ' . $label
            . ($result['safety'] ? ' — the previous data was saved first as ' . $result['safety'] : ''), 'Backup');

        return response()->json([
            'success' => true,
            'safety'  => $result['safety'],
            'message' => 'Database restored from ' . $label . '.'
                . ($result['safety'] ? ' Your previous data was saved as ' . $result['safety'] . ' — restore it to undo.' : ''),
        ]);
    }
}
