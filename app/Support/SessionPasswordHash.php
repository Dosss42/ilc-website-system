<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Sanctum's AuthenticateSession middleware (on the /api routes) keeps a hash
 * of the logged-in user's password in the session and logs the session out
 * as soon as it no longer matches. Without this, a user who changes their OWN
 * password is logged out by the very next API call (e.g. the Student Portal's
 * fee requests after the reload). Call this right after saving the new
 * password: the current session stays signed in, other devices still using
 * the old password are still signed out.
 */
class SessionPasswordHash
{
    public static function refresh(string $guard = 'web'): void
    {
        $guardInstance = Auth::guard($guard);
        $user = $guardInstance->user();
        if (!$user || !request()->hasSession()) {
            return;
        }

        request()->session()->put(
            "password_hash_{$guard}",
            method_exists($guardInstance, 'hashPasswordForCookie')
                ? $guardInstance->hashPasswordForCookie($user->getAuthPassword())
                : $user->getAuthPassword()
        );
    }
}
