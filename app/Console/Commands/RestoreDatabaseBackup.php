<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use App\Services\BackupService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Disaster recovery from the server's terminal — works when nobody can log in to the
 * website (e.g. the Admin/Super Admin accounts or the users table are damaged), unlike
 * Super Admin › Backup & Restore. See docs/DISASTER_RECOVERY.md.
 *
 *   php artisan backup:restore --list                 show the backups on this server
 *   php artisan backup:restore --latest               restore the newest backup
 *   php artisan backup:restore backup_2026-10-22_143000.sql
 *   php artisan backup:restore "C:\Users\me\Downloads\ILC System Backup File - 2026-10-22 02-30 PM.sql"
 */
#[Signature('backup:restore
    {file? : A backup name from storage/app/backups, or the full path to any .sql backup file}
    {--latest : Restore the newest backup on this server}
    {--list : Only list the backups on this server}
    {--force : Do not ask for confirmation, and restore even if the current data cannot be saved first}')]
#[Description('Restore the database from a backup file (no login needed) — saves the current data first')]
class RestoreDatabaseBackup extends Command
{
    public function handle(): int
    {
        $backups = collect(File::files(BackupService::backupDir()))
            ->filter(fn ($f) => BackupService::isValidBackupFilename($f->getFilename()))
            ->sortByDesc(fn ($f) => $f->getMTime())->values();

        if ($this->option('list') || (!$this->argument('file') && !$this->option('latest'))) {
            if ($backups->isEmpty()) {
                $this->warn('No backups on this server (' . BackupService::backupDir() . ').');
            } else {
                $this->table(['Backup', 'Created', 'Size'], $backups->map(fn ($f) => [
                    $f->getFilename(), date('Y-m-d H:i', $f->getMTime()), round($f->getSize() / 1024) . ' KB',
                ]));
            }
            if (!$this->option('list')) {
                $this->line('Restore one with:  php artisan backup:restore <name>   or   php artisan backup:restore --latest');
            }
            return self::SUCCESS;
        }

        $path = $this->option('latest') ? $backups->first()?->getPathname() : $this->resolve((string) $this->argument('file'));
        if (!$path || !File::exists($path)) {
            $this->error('Backup not found: ' . ($this->argument('file') ?: '(no backups on this server)'));
            return self::FAILURE;
        }
        if (!BackupService::looksLikeBackup($path)) {
            $this->error(basename($path) . ' is not a database backup file.');
            return self::FAILURE;
        }

        $this->warn('This replaces ALL data in database "' . config('database.connections.' . config('database.default') . '.database') . '"');
        $this->line('with ' . basename($path) . ' (' . date('Y-m-d H:i', filemtime($path)) . ', ' . round(filesize($path) / 1024) . ' KB).');
        $this->line('The current data is saved first as a "before-restore" backup, so this can be undone.');
        if (!$this->option('force') && !$this->confirm('Restore now?')) {
            $this->line('Cancelled — nothing changed.');
            return self::FAILURE;
        }

        try {
            $result = BackupService::restoreSafely($path, requireSafety: !$this->option('force'));
        } catch (\Throwable $e) {
            $this->error('Restore failed: ' . $e->getMessage());
            if (!$this->option('force')) {
                $this->line('If the database is too damaged to save first, run again with --force.');
            }
            return self::FAILURE;
        }

        ActivityLogger::log('update', 'Database restored from the server console: ' . basename($path)
            . ($result['safety'] ? ' — the previous data was saved first as ' . $result['safety'] : ''), 'Backup');

        $this->info('Restored ' . basename($path) . ' (' . $result['statements'] . ' statements).');
        $this->line($result['safety']
            ? 'Previous data saved as ' . $result['safety'] . ' — to undo:  php artisan backup:restore ' . $result['safety']
            : 'No safety copy was made (--force).');
        $this->line('If nobody can log in afterwards:  php artisan superadmin:recover <email>');

        return self::SUCCESS;
    }

    /** A name in the backups folder, or a path to a .sql file anywhere. */
    private function resolve(string $file): ?string
    {
        if (File::exists($file)) {
            return realpath($file) ?: $file;
        }
        $inDir = BackupService::backupDir() . DIRECTORY_SEPARATOR . basename($file);
        return File::exists($inDir) ? $inDir : null;
    }
}
