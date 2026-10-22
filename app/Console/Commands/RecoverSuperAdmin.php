<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use App\Services\SuperAdminRecovery;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Get a working Super Admin login back from the server's terminal, when no one can sign in
 * to the website (see App\Services\SuperAdminRecovery — the Emergency Recovery page does the
 * same without a terminal). See docs/DISASTER_RECOVERY.md.
 *
 *   php artisan superadmin:recover admin@school.edu
 */
#[Signature('superadmin:recover
    {email : Email of the Super Admin account to repair (created if it does not exist)}
    {--name=Super Admin : Name to use if the account has to be created}')]
#[Description('Restore/repair a Super Admin account and give it a new temporary password (no login needed)')]
class RecoverSuperAdmin extends Command
{
    public function handle(): int
    {
        $r = SuperAdminRecovery::recover((string) $this->argument('email'), (string) $this->option('name'));
        if (!$r['ok']) {
            $this->error($r['error']);
            return self::FAILURE;
        }

        ActivityLogger::log('update', ($r['created'] ? 'Super Admin account created' : 'Super Admin account recovered')
            . " from the server console: {$r['email']}" . ($r['fixed'] ? ' (' . implode(', ', $r['fixed']) . ')' : ''), 'User', $r['user_id']);

        $this->info(($r['created'] ? 'Created' : 'Recovered') . " Super Admin: {$r['email']}" . ($r['fixed'] ? ' — ' . implode(', ', $r['fixed']) : ''));
        $this->line('Temporary password:  ' . $r['password']);
        $this->warn('Log in at /login and change this password right away.');

        return self::SUCCESS;
    }
}
