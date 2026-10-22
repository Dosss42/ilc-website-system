<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * Get a working Super Admin login back when nobody can sign in: the account was archived,
 * deactivated, had its role changed, lost its password, or is locked out — or doesn't exist.
 * Used by `php artisan superadmin:recover` and the Emergency Recovery page (/recovery).
 */
class SuperAdminRecovery
{
    /**
     * @return array{ok: bool, error?: string, email?: string, password?: string, created?: bool, fixed?: string[], user_id?: int}
     */
    public static function recover(string $email, string $name = 'Super Admin'): array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Not a valid email address: ' . $email];
        }

        $password = Str::password(12, symbols: false);
        $user = User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->first();
        $created = !$user;
        $fixed = [];

        if ($created) {
            $user = new User(['name' => trim($name) ?: 'Super Admin', 'email' => $email]);
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

        return ['ok' => true, 'email' => $email, 'password' => $password, 'created' => $created, 'fixed' => $fixed, 'user_id' => $user->id];
    }
}
