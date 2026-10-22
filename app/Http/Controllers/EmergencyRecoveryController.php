<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use App\Services\EmergencyRecovery;
use App\Services\SuperAdminRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Emergency Recovery page (/recovery) — see App\Services\EmergencyRecovery and
 * docs/EMERGENCY_RECOVERY.md. The routes run without sessions or CSRF (so they work when
 * the database is damaged); every action is guarded by the Recovery Key instead: the key
 * itself to unlock, then a short-lived token signed with it.
 */
class EmergencyRecoveryController extends Controller
{
    public function show()
    {
        return $this->page('recovery.page', ['unlocked' => false]);
    }

    public function unlock(Request $request)
    {
        $ip = (string) $request->ip();
        if (!EmergencyRecovery::isSetUp()) {
            return $this->page('recovery.page', ['unlocked' => false]);
        }
        if (EmergencyRecovery::isLockedOut($ip)) {
            return $this->page('recovery.page', ['unlocked' => false,
                'error' => 'Too many wrong keys. Emergency Recovery is locked for up to an hour.'], 429);
        }
        if (!EmergencyRecovery::checkKey((string) $request->input('recovery_key'), $ip)) {
            $left = EmergencyRecovery::triesLeft($ip);
            return $this->page('recovery.page', ['unlocked' => false,
                'error' => 'That Recovery Key is not correct.' . ($left > 0 ? " {$left} tr" . ($left === 1 ? 'y' : 'ies') . ' left.' : ' Emergency Recovery is now locked for up to an hour.')], 422);
        }

        return $this->panel();
    }

    public function repairSuperAdmin(Request $request)
    {
        if ($expired = $this->rejectToken($request)) {
            return $expired;
        }
        try {
            $r = SuperAdminRecovery::recover((string) $request->input('email'), (string) $request->input('name', 'Super Admin'));
        } catch (\Throwable $e) {
            return $this->panel(['error' => 'Could not repair the account: ' . $e->getMessage()
                . ' — if the database is damaged, restore a backup first (step 1).']);
        }
        if (!$r['ok']) {
            return $this->panel(['error' => $r['error']]);
        }

        EmergencyRecovery::record('update', ($r['created'] ? 'Emergency recovery: Super Admin account created: ' : 'Emergency recovery: Super Admin account repaired: ')
            . $r['email'] . ($r['fixed'] ? ' (' . implode(', ', $r['fixed']) . ')' : '') . ' (from ' . $request->ip() . ')', (string) $request->ip());

        return $this->panel(['account' => $r]);
    }

    public function restoreUpload(Request $request)
    {
        if ($expired = $this->rejectToken($request)) {
            return $expired;
        }
        $upload = $request->file('backup_file');
        if (!$upload || !$upload->isValid()) {
            return $this->panel(['error' => 'Choose a backup file (.sql) to upload.']);
        }
        if (strtolower($upload->getClientOriginalExtension()) !== 'sql') {
            return $this->panel(['error' => 'Choose a .sql backup file.']);
        }
        if ($upload->getSize() > 50 * 1024 * 1024) {
            return $this->panel(['error' => 'The backup file is larger than 50 MB.']);
        }

        $name = 'backup_' . now()->format('Y-m-d_His') . '_uploaded.sql';
        $upload->move(BackupService::backupDir(), $name);
        $path = BackupService::backupDir() . DIRECTORY_SEPARATOR . $name;
        if (!BackupService::looksLikeBackup($path)) {
            File::delete($path);
            return $this->panel(['error' => 'That file is not a database backup (.sql from Create Backup or mysqldump).']);
        }

        return $this->restore($request, $path, $upload->getClientOriginalName());
    }

    public function restoreFromServer(Request $request)
    {
        if ($expired = $this->rejectToken($request)) {
            return $expired;
        }
        $file = basename((string) $request->input('file'));
        $path = BackupService::backupDir() . DIRECTORY_SEPARATOR . $file;
        if (!BackupService::isValidBackupFilename($file) || !File::exists($path)) {
            return $this->panel(['error' => 'That backup is not on the server.']);
        }

        return $this->restore($request, $path, BackupService::displayName($file));
    }

    /** Restore with a safety copy when the database can still be saved; continue without one if it can't. */
    private function restore(Request $request, string $path, string $label)
    {
        try {
            $result = BackupService::restoreSafely($path, requireSafety: false);
        } catch (\Throwable $e) {
            return $this->panel(['error' => 'Restore failed: ' . $e->getMessage()]);
        }

        EmergencyRecovery::record('update', 'Emergency recovery: database restored from ' . $label
            . ($result['safety'] ? ' — the previous data was saved first as ' . $result['safety'] : ' — no safety copy (the database could not be saved)')
            . ' (from ' . $request->ip() . ')', (string) $request->ip());

        return $this->panel(['restored' => ['label' => $label, 'safety' => $result['safety']]]);
    }

    /** The unlocked panel, with a fresh token and what's on the server. */
    private function panel(array $data = [])
    {
        return $this->page('recovery.page', $data + [
            'unlocked' => true,
            'token'    => EmergencyRecovery::issueToken(),
            'backups'  => collect(File::files(BackupService::backupDir()))
                ->filter(fn ($f) => BackupService::isValidBackupFilename($f->getFilename()))
                ->sortByDesc(fn ($f) => $f->getMTime())->take(10)
                ->map(fn ($f) => ['name' => $f->getFilename(), 'label' => BackupService::displayName($f->getFilename()), 'size' => round($f->getSize() / 1024) . ' KB'])
                ->values()->all(),
            'database' => $this->databaseStatus(),
        ]);
    }

    private function rejectToken(Request $request)
    {
        if (EmergencyRecovery::validToken((string) $request->input('token'))) {
            return null;
        }
        return $this->page('recovery.page', ['unlocked' => false,
            'error' => 'This recovery session has ended (it lasts ' . EmergencyRecovery::TOKEN_MINUTES . ' minutes). Enter the Recovery Key again.'], 419);
    }

    /** A plain-language state of the database, without failing if it is broken. */
    private function databaseStatus(): array
    {
        try {
            DB::connection()->getPdo();
            $superadmins = DB::table('users')->where('role', 'superadmin')->where('is_active', true)->whereNull('deleted_at')->count();
            return ['ok' => true, 'text' => 'Database reachable — ' . $superadmins . ' active Super Admin account' . ($superadmins === 1 ? '' : 's') . '.', 'superadmins' => $superadmins];
        } catch (\Throwable $e) {
            return ['ok' => false, 'text' => 'The database has a problem (' . \Illuminate\Support\Str::limit($e->getMessage(), 140) . '). Restore a backup first.', 'superadmins' => 0];
        }
    }

    private function page(string $view, array $data, int $status = 200)
    {
        return response()->view($view, $data + ['setUp' => EmergencyRecovery::isSetUp()], $status)
            ->header('Cache-Control', 'no-store')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
