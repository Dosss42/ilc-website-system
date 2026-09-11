<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Shared backup logic used by both the manual "Create Backup" button
 * (Super Admin portal) and the automatic scheduled backup — so a backup
 * created either way goes through the exact same dump/copy/retention
 * pipeline, rather than maintaining two versions of this logic.
 *
 * Ransomware-resilience design: a backup that only ever lives in
 * storage/app/backups (this same machine, same disk as the app) offers
 * no real protection — an attack that encrypts the server encrypts the
 * backups sitting right next to it. Every backup this service creates is
 * therefore optionally also copied to a second local/USB path and/or
 * uploaded to Google Drive, each independently retained on its own
 * schedule, so a single point of failure can't take out every copy.
 */
class BackupService
{
    public static function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    public static function isValidBackupFilename(string $file): bool
    {
        return (bool) preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.sql$/', basename($file));
    }

    /**
     * Resolve mysqldump/mysql to an actual executable path. A Windows Task
     * Scheduler job doesn't always inherit the same PATH XAMPP's own
     * control panel gives a manually-run backup, so a scheduled backup
     * silently failing every night (with no one watching) is exactly the
     * kind of "worked once, never again" gap this feature exists to close.
     */
    private static function resolveExecutable(string $configKey, string $command, array $commonPaths): string
    {
        $configured = config('backup.' . $configKey);
        if (!empty($configured) && File::exists($configured)) {
            return $configured;
        }
        foreach ($commonPaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }
        return $command; // fall back to PATH resolution
    }

    public static function mysqldumpBin(): string
    {
        return self::resolveExecutable('mysqldump_path', 'mysqldump', [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
        ]);
    }

    public static function mysqlBin(): string
    {
        return self::resolveExecutable('mysql_path', 'mysql', [
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysql.exe',
        ]);
    }

    /**
     * Create a fresh mysqldump into the primary backup folder.
     *
     * @return array{success: bool, filename: ?string, path: ?string, message: ?string}
     */
    public static function createDump(): array
    {
        try {
            $db   = config('database.connections.' . config('database.default'));
            $host = $db['host']     ?? '127.0.0.1';
            $port = $db['port']     ?? 3306;
            $user = $db['username'] ?? 'root';
            $pass = $db['password'] ?? '';
            $name = $db['database'] ?? '';

            $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';
            $path     = self::backupDir() . DIRECTORY_SEPARATOR . $filename;

            $passPart = $pass ? '-p' . escapeshellarg($pass) : '';
            $cmd = sprintf(
                '%s --host=%s --port=%s -u%s %s %s > %s 2>&1',
                escapeshellarg(self::mysqldumpBin()),
                escapeshellarg($host),
                escapeshellarg((string) $port),
                escapeshellarg($user),
                $passPart,
                escapeshellarg($name),
                escapeshellarg($path)
            );

            exec($cmd, $output, $code);

            if ($code !== 0 || !File::exists($path) || File::size($path) < 100) {
                File::delete($path);
                Log::error('mysqldump failed', ['code' => $code, 'output' => implode("\n", $output)]);
                return ['success' => false, 'filename' => null, 'path' => null, 'message' => 'Backup failed. mysqldump not found or errored — set MYSQLDUMP_PATH in .env if it is installed somewhere other than the default XAMPP location.'];
            }

            return ['success' => true, 'filename' => $filename, 'path' => $path, 'message' => null];
        } catch (\Throwable $e) {
            Log::error('Backup dump failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'filename' => null, 'path' => null, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Copy a completed backup to the configured secondary local/USB path,
     * if one is configured. Silently skipped (not an error) if unset or
     * the drive isn't currently reachable — a disconnected USB drive
     * shouldn't fail the whole backup run.
     */
    public static function copyToSecondary(string $path, string $filename): bool
    {
        $secondary = config('backup.secondary_path');
        if (empty($secondary)) {
            return false;
        }

        try {
            if (!File::exists($secondary)) {
                File::makeDirectory($secondary, 0755, true);
            }
            File::copy($path, rtrim($secondary, '\\/') . DIRECTORY_SEPARATOR . $filename);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Secondary backup copy failed', ['error' => $e->getMessage(), 'target' => $secondary]);
            return false;
        }
    }

    /**
     * Upload a completed backup to Google Drive, if configured. Silently
     * skipped if disabled/not configured — see BACKUP_SETUP.md.
     */
    public static function uploadToGoogleDrive(string $path, string $filename): bool
    {
        if (!config('backup.google_drive.enabled')) {
            return false;
        }

        try {
            return app(GoogleDriveBackupUploader::class)->upload($path, $filename);
        } catch (\Throwable $e) {
            Log::warning('Google Drive backup upload failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Run the full pipeline: dump -> secondary copy -> Drive upload ->
     * apply retention on all three locations independently.
     *
     * @return array{success: bool, filename: ?string, message: ?string, secondary: bool, drive: bool}
     */
    public static function runFull(): array
    {
        $dump = self::createDump();
        if (!$dump['success']) {
            return ['success' => false, 'filename' => null, 'message' => $dump['message'], 'secondary' => false, 'drive' => false];
        }

        $secondaryOk = self::copyToSecondary($dump['path'], $dump['filename']);
        $driveOk     = self::uploadToGoogleDrive($dump['path'], $dump['filename']);

        self::applyRetention();

        ActivityLogger::log(
            'create',
            'Database backup created: ' . $dump['filename']
                . ($secondaryOk ? ' (+ secondary copy)' : '')
                . ($driveOk ? ' (+ Google Drive)' : ''),
            'Backup'
        );

        return [
            'success'   => true,
            'filename'  => $dump['filename'],
            'message'   => 'Backup created: ' . $dump['filename'],
            'secondary' => $secondaryOk,
            'drive'     => $driveOk,
        ];
    }

    /**
     * Delete backups older than each location's own retention window.
     * Each copy is retained independently (the off-site copies are kept
     * longer by default) so pruning the primary folder never touches the
     * copies meant to survive an incident on this machine.
     */
    public static function applyRetention(): void
    {
        self::pruneDirectory(self::backupDir(), (int) config('backup.keep_days', 30));

        $secondary = config('backup.secondary_path');
        if (!empty($secondary) && File::exists($secondary)) {
            self::pruneDirectory($secondary, (int) config('backup.secondary_keep_days', 60));
        }

        if (config('backup.google_drive.enabled')) {
            try {
                app(GoogleDriveBackupUploader::class)->pruneOlderThan((int) config('backup.google_drive.keep_days', 90));
            } catch (\Throwable $e) {
                Log::warning('Google Drive backup retention failed', ['error' => $e->getMessage()]);
            }
        }
    }

    private static function pruneDirectory(string $dir, int $keepDays): void
    {
        if ($keepDays <= 0) {
            return; // 0 or negative = keep forever, don't auto-delete
        }
        $cutoff = now()->subDays($keepDays)->getTimestamp();
        foreach (File::files($dir) as $file) {
            if ($file->getExtension() === 'sql' && $file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
            }
        }
    }
}
