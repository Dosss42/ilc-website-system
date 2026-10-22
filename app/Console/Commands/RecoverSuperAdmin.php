<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Get a working Super Admin login back from the server's terminal, when no one can sign in
 * to the website: the account was deleted/archived, deactivated, had its role changed,
 * its password is lost, or it is locked out after failed attempts. Creates the account if
 * it doesn't exist. See docs/DISASTER_RECOVERY.md.
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
        $email = strtolower(trim((string) $this->argument('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Not a valid email address: ' . $email);
            return self::FAILURE;
        }

        $password = Str::password(12, symbols: false);
        $user = User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->first();
        $created = !$user;
        $fixed = [];

        if ($created) {
            $user = new User(['name' => $this->option('name'), 'email' => $email]);
        } else {
            if ($user->trashed()) { $user->restore(); $fixed[] = 'restored from archive'; }
            if ($user->role !== 'superadmin') { $fixed[] = "role {$user->role} → superadmin"; }
            if (!$user->is_active) { $fixed[] = 'reactivated'; }
        }

        $user->forceFill([
            'role'              => 'superadmin',
            'is_active'         => true,
            'password'          => bcrypt($password),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        // Clear a login lockout from failed attempts (web login keys, AuthController)
        cache()->forget('login_attempts_' . md5($email));
        cache()->forget('login_unlock_' . md5($email));

        ActivityLogger::log('update', ($created ? 'Super Admin account created' : 'Super Admin account recovered')
            . " from the server console: {$email}" . ($fixed ? ' (' . implode(', ', $fixed) . ')' : ''), 'User', $user->id);

        $this->info(($created ? 'Created' : 'Recovered') . " Super Admin: {$email}" . ($fixed ? ' — ' . implode(', ', $fixed) : ''));
        $this->line('Temporary password:  ' . $password);
        $this->warn('Log in at /login and change this password right away (Settings › Account).');

        return self::SUCCESS;
    }
}
