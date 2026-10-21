<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Validation\Rules\Password;

/**
 * The Admin > Settings > Security values, read in one place so the login
 * pages, session lifetime and password validation all follow them.
 * Each value is clamped so a bad entry (e.g. 0 attempts) can't lock
 * everyone out or disable the check.
 */
class SecurityPolicy
{
    public static function maxLoginAttempts(): int
    {
        return self::clamp((int) Setting::get('max_login_attempts', 5), 1, 20);
    }

    public static function lockoutMinutes(): int
    {
        return self::clamp((int) Setting::get('lockout_duration', 15), 1, 1440);
    }

    public static function sessionMinutes(): int
    {
        return self::clamp((int) Setting::get('session_timeout', 120), 5, 1440);
    }

    public static function passwordMinLength(): int
    {
        return self::clamp((int) Setting::get('password_min_length', 8), 6, 64);
    }

    public static function requireUppercase(): bool
    {
        return (bool) Setting::get('require_password_uppercase', true);
    }

    public static function requireNumber(): bool
    {
        return (bool) Setting::get('require_password_number', true);
    }

    /** Used as Password::defaults(), so every Password::defaults() rule follows Settings. */
    public static function passwordRule(): Password
    {
        $extra = [];
        if (self::requireUppercase()) {
            $extra[] = function ($attribute, $value, $fail) {
                if (!preg_match('/[A-Z]/', (string) $value)) {
                    $fail('The password must contain at least one uppercase letter.');
                }
            };
        }
        if (self::requireNumber()) {
            $extra[] = function ($attribute, $value, $fail) {
                if (!preg_match('/[0-9]/', (string) $value)) {
                    $fail('The password must contain at least one number.');
                }
            };
        }

        return Password::min(self::passwordMinLength())->rules($extra);
    }

    private static function clamp(int $value, int $min, int $max): int
    {
        return max($min, min($max, $value));
    }
}
