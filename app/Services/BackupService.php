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

    /** backup_<date>_<time>.sql, optionally tagged: _uploaded (Restore from File) or
     * _before-restore (the automatic safety copy made just before a restore). */
    public static function isValidBackupFilename(string $file): bool
    {
        return (bool) preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{6}(_(uploaded|before-restore))?\.sql$/', basename($file));
    }

    /** Does this file look like a database backup (mysqldump or the app's PHP backup)? */
    public static function looksLikeBackup(string $path): bool
    {
        $head = (string) @file_get_contents($path, false, null, 0, 65536);
        return (bool) preg_match('/^-- (MySQL dump|MariaDB dump|ILC SYSTEM database backup)/m', $head)
            && str_contains($head, 'CREATE TABLE');
    }

    /**
     * Restore a backup, first saving the current data as a "before-restore" backup so a
     * wrong restore can be undone by restoring that copy. With $requireSafety (the web
     * page), the restore doesn't go ahead if the safety copy can't be made; the server
     * command can skip it when the database is too broken to back up.
     *
     * @return array{statements: int, safety: ?string}
     */
    public static function restoreSafely(string $path, bool $requireSafety = true): array
    {
        @set_time_limit(600);
        $safety = self::createDump('before-restore');
        if (!$safety['success'] && $requireSafety) {
            throw new \RuntimeException('Could not save the current data first (' . $safety['message'] . ') — nothing was restored.');
        }

        $statements = self::restore($path);
        \App\Models\Setting::clearCache(); // cached settings would otherwise outlive the restore by up to an hour

        return ['statements' => $statements, 'safety' => $safety['success'] ? $safety['filename'] : null];
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
     * Create a fresh backup (.sql) in the primary backup folder — with mysqldump when it is
     * available and works (XAMPP), otherwise with the app's own PHP dump (Railway's
     * container has no mysqldump). Both produce a plain .sql file that restore() reads.
     *
     * @return array{success: bool, filename: ?string, path: ?string, message: ?string}
     */
    public static function createDump(?string $tag = null): array
    {
        $filename = 'backup_' . now()->format('Y-m-d_His') . ($tag ? '_' . $tag : '') . '.sql';
        $path     = self::backupDir() . DIRECTORY_SEPARATOR . $filename;
        $driver   = config('backup.driver', 'auto');

        if ($driver !== 'php') {
            $error = self::mysqldumpTo($path);
            if ($error === null) {
                return ['success' => true, 'filename' => $filename, 'path' => $path, 'message' => null];
            }
            File::delete($path);
            if ($driver === 'mysqldump') {
                return ['success' => false, 'filename' => null, 'path' => null, 'message' => 'Backup failed: ' . $error];
            }
            Log::info('mysqldump unavailable, using the PHP backup instead', ['reason' => $error]);
        }

        try {
            self::phpDumpTo($path);
            return ['success' => true, 'filename' => $filename, 'path' => $path, 'message' => null];
        } catch (\Throwable $e) {
            File::delete($path);
            Log::error('PHP backup failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'filename' => null, 'path' => null, 'message' => 'Backup failed: ' . $e->getMessage()];
        }
    }

    /** Run mysqldump into $path. Returns null on success, or why it failed. */
    private static function mysqldumpTo(string $path): ?string
    {
        $db = config('database.connections.' . config('database.default'));
        $pass = $db['password'] ?? '';
        $errFile = $path . '.err';
        // stderr goes to its own file: a warning written into the dump would corrupt it
        $cmd = sprintf(
            '%s --host=%s --port=%s -u%s %s %s > %s 2> %s',
            escapeshellarg(self::mysqldumpBin()),
            escapeshellarg($db['host'] ?? '127.0.0.1'),
            escapeshellarg((string) ($db['port'] ?? 3306)),
            escapeshellarg($db['username'] ?? 'root'),
            $pass ? '-p' . escapeshellarg($pass) : '',
            escapeshellarg($db['database'] ?? ''),
            escapeshellarg($path),
            escapeshellarg($errFile)
        );
        try {
            exec($cmd, $output, $code);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
        $stderr = File::exists($errFile) ? trim(File::get($errFile)) : '';
        File::delete($errFile);

        if ($code !== 0 || !File::exists($path) || File::size($path) < 100
            || !str_contains(File::get($path, false), '-- Dump completed')) {
            return 'mysqldump exited with code ' . $code . ($stderr !== '' ? ': ' . $stderr : '');
        }
        return null;
    }

    /**
     * A separate database connection for backup/restore work, so its session settings
     * (UTC time zone, foreign key checks off, a read snapshot) never leak into the
     * connection the rest of the request uses.
     */
    private static function sideConnection(?string $database = null): \Illuminate\Database\Connection
    {
        $default = config('database.default');
        config(['database.connections.backup_side' => array_merge(
            config('database.connections.' . $default),
            $database ? ['database' => $database] : []
        )]);
        \Illuminate\Support\Facades\DB::purge('backup_side');
        return \Illuminate\Support\Facades\DB::connection('backup_side');
    }

    /**
     * Dump every table (structure + rows) to $path in plain SQL, without mysqldump.
     * Times are written in UTC (TIME_ZONE '+00:00', like mysqldump) so a restore puts
     * TIMESTAMP values back exactly; rows are read from one consistent snapshot.
     */
    public static function phpDumpTo(string $path): void
    {
        $conn = self::sideConnection();
        $pdo  = $conn->getPdo();
        $fh   = fopen($path, 'wb');
        if (!$fh) {
            throw new \RuntimeException('Cannot write ' . basename($path));
        }

        try {
            $pdo->exec("SET SESSION time_zone = '+00:00'");
            $pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ');
            $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');

            $database = $conn->getDatabaseName();
            fwrite($fh, "-- ILC SYSTEM database backup (made by the app's PHP backup, no mysqldump needed)\n"
                . "-- Database: {$database}   Created: " . now()->format('Y-m-d H:i:s T') . "\n\n"
                . "SET NAMES utf8mb4;\nSET TIME_ZONE='+00:00';\nSET FOREIGN_KEY_CHECKS=0;\nSET UNIQUE_CHECKS=0;\n"
                . "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

            $tables = array_map(fn ($r) => array_values((array) $r)[0], $conn->select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"));
            foreach ($tables as $table) {
                $q = '`' . str_replace('`', '``', $table) . '`';
                $create = array_values((array) $conn->selectOne("SHOW CREATE TABLE {$q}"))[1];
                fwrite($fh, "--\n-- Table {$table}\n--\n\nDROP TABLE IF EXISTS {$q};\n{$create};\n\n");

                // Generated columns are computed by the database and can't be inserted
                $columns = array_values(array_filter($conn->select("SHOW COLUMNS FROM {$q}"),
                    fn ($c) => !preg_match('/\b(VIRTUAL|STORED|PERSISTENT)\b/i', (string) $c->Extra)));
                if (!$columns) {
                    continue;
                }
                $names  = implode(',', array_map(fn ($c) => '`' . str_replace('`', '``', $c->Field) . '`', $columns));
                $binary = array_map(fn ($c) => (bool) preg_match('/blob|binary|^bit/i', (string) $c->Type), $columns);

                $stmt = $pdo->query("SELECT {$names} FROM {$q}");
                $batch = [];
                $size = 0;
                while (($row = $stmt->fetch(\PDO::FETCH_NUM)) !== false) {
                    $values = [];
                    foreach ($row as $i => $v) {
                        $values[] = $v === null ? 'NULL' : ($binary[$i] ? '0x' . bin2hex($v) : $pdo->quote((string) $v));
                    }
                    $batch[] = '(' . implode(',', $values) . ')';
                    $size += strlen(end($batch));
                    if (count($batch) >= 200 || $size > 500000) {
                        fwrite($fh, "INSERT INTO {$q} ({$names}) VALUES\n" . implode(",\n", $batch) . ";\n");
                        $batch = [];
                        $size = 0;
                    }
                }
                $stmt->closeCursor();
                if ($batch) {
                    fwrite($fh, "INSERT INTO {$q} ({$names}) VALUES\n" . implode(",\n", $batch) . ";\n");
                }
                fwrite($fh, "\n");
            }

            fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\nSET UNIQUE_CHECKS=1;\n\n-- Dump completed " . now()->format('Y-m-d H:i:s') . "\n");
            $pdo->exec('COMMIT');
        } finally {
            fclose($fh);
            \Illuminate\Support\Facades\DB::purge('backup_side');
        }
    }

    /**
     * Restore a backup file (mysqldump or PHP backup) into the database — or into
     * $database when given, which is how restores are tested without touching the real
     * data. Runs each statement through PHP, so no mysql client program is needed.
     *
     * @return int statements executed
     */
    public static function restore(string $path, ?string $database = null): int
    {
        if (!self::looksLikeBackup($path)) {
            throw new \RuntimeException(basename($path) . ' is not a database backup file.');
        }
        $conn = self::sideConnection($database);
        $pdo  = $conn->getPdo();
        $fh   = fopen($path, 'rb');
        if (!$fh) {
            throw new \RuntimeException('Cannot read ' . basename($path));
        }

        $count = 0;
        $statement = '';
        try {
            // Both formats keep every string literal on one line (newlines are escaped as \n),
            // so a line ending in ";" always ends a statement.
            while (($line = fgets($fh)) !== false) {
                $trimmed = rtrim($line, "\r\n");
                if ($statement === '' && ($trimmed === '' || str_starts_with($trimmed, '--'))) {
                    continue;
                }
                if ($statement === '' && stripos($trimmed, 'DELIMITER') === 0) {
                    throw new \RuntimeException('Backups with triggers/routines (DELIMITER) are not supported by the PHP restore.');
                }
                $statement .= $line;
                if (str_ends_with(rtrim($trimmed), ';')) {
                    $pdo->exec($statement);
                    $count++;
                    $statement = '';
                }
            }
            if (trim($statement) !== '') {
                $pdo->exec($statement);
                $count++;
            }
        } finally {
            fclose($fh);
            \Illuminate\Support\Facades\DB::purge('backup_side');
        }

        return $count;
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
    /** Why the last Google Drive upload failed (shown in the Backup Log), or null. */
    public static ?string $lastDriveError = null;

    public static function uploadToGoogleDrive(string $path, string $filename): bool
    {
        self::$lastDriveError = null;
        if (!config('backup.google_drive.enabled')) {
            return false;
        }

        try {
            return app(GoogleDriveBackupUploader::class)->upload($path, $filename);
        } catch (\Throwable $e) {
            self::$lastDriveError = \Illuminate\Support\Str::limit($e->getMessage(), 300);
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
            'success'     => true,
            'filename'    => $dump['filename'],
            'message'     => 'Backup created: ' . $dump['filename'],
            'secondary'   => $secondaryOk,
            'drive'       => $driveOk,
            'drive_error' => self::$lastDriveError,
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
