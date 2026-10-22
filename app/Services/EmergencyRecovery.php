<?php

namespace App\Services;

use App\Mail\EmergencyRecoveryAlert;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Emergency Recovery (/recovery) — lets the school get back in without an IT person or a
 * terminal: repair the Super Admin account, or restore a backup. It opens only with the
 * Recovery Key (RECOVERY_KEY on the server — printed once and kept sealed by the school
 * head; never stored in the database). See docs/EMERGENCY_RECOVERY.md.
 *
 * It must work when the database is damaged, so it keeps no session: after the key is
 * accepted, each step carries a short-lived token signed with the key itself. Wrong keys are
 * limited (3 per hour per address, 10 per hour in total), and every attempt is logged and
 * emailed to the Super Admins.
 */
class EmergencyRecovery
{
    public const MIN_KEY_LENGTH = 16;
    public const TOKEN_MINUTES  = 15;
    public const TRIES_PER_IP   = 3;
    public const TRIES_TOTAL    = 10;
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789'; // no 0/O, 1/I/L — easy to read off paper

    /** The key as configured, normalized (letters/digits only, upper case). */
    private static function key(): string
    {
        return self::normalize((string) config('backup.recovery_key'));
    }

    public static function normalize(string $key): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $key));
    }

    /** Set up = a key of at least 16 letters/digits is configured (a short one is refused). */
    public static function isSetUp(): bool
    {
        return strlen(self::key()) >= self::MIN_KEY_LENGTH;
    }

    /** A new random key like K7Q2-9XMP-4RTA-WB6N-CH3D-ZE8F (120 bits). */
    public static function generateKey(): string
    {
        $chars = '';
        for ($i = 0; $i < 24; $i++) {
            $chars .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }
        return implode('-', str_split($chars, 4));
    }

    // ── wrong-key limit (cache only — works without the database) ──

    private static function triesKey(string $ip): string
    {
        return 'recovery_tries_' . md5($ip);
    }

    public static function isLockedOut(string $ip): bool
    {
        return cache()->get(self::triesKey($ip), 0) >= self::TRIES_PER_IP
            || cache()->get('recovery_tries_all', 0) >= self::TRIES_TOTAL;
    }

    public static function triesLeft(string $ip): int
    {
        return max(0, self::TRIES_PER_IP - (int) cache()->get(self::triesKey($ip), 0));
    }

    private static function countFailure(string $ip): void
    {
        foreach ([self::triesKey($ip), 'recovery_tries_all'] as $k) {
            cache()->put($k, (int) cache()->get($k, 0) + 1, now()->addHour());
        }
    }

    /** Check a typed key. Counts a failure (and alerts) when wrong. */
    public static function checkKey(string $input, string $ip): bool
    {
        $ok = self::isSetUp() && hash_equals(self::key(), self::normalize($input));
        if (!$ok) {
            self::countFailure($ip);
            self::record('denied', "Emergency recovery: wrong Recovery Key entered (from {$ip})", $ip);
        } else {
            cache()->forget(self::triesKey($ip));
            self::record('update', "Emergency recovery page unlocked with the Recovery Key (from {$ip})", $ip);
        }
        return $ok;
    }

    // ── step tokens: signed with the key, valid TOKEN_MINUTES ──

    public static function issueToken(): string
    {
        $expires = now()->addMinutes(self::TOKEN_MINUTES)->getTimestamp();
        return $expires . '.' . hash_hmac('sha256', 'ilc-recovery|' . $expires, self::key());
    }

    public static function validToken(?string $token): bool
    {
        if (!self::isSetUp() || !$token || !preg_match('/^(\d+)\.([a-f0-9]{64})$/', $token, $m)) {
            return false;
        }
        return (int) $m[1] >= time()
            && hash_equals(hash_hmac('sha256', 'ilc-recovery|' . $m[1], self::key()), $m[2]);
    }

    /**
     * Log to the audit trail and email the active Super Admins. Neither may stop the
     * recovery: both are skipped quietly if the database or mail is unavailable.
     */
    public static function record(string $event, string $description, string $ip): void
    {
        ActivityLogger::log($event, $description, 'EmergencyRecovery', null, ['ip' => $ip]);

        try {
            $emails = User::where('role', 'superadmin')->where('is_active', true)->pluck('email')->filter()->all();
            if ($emails) {
                Mail::to($emails)->send(new EmergencyRecoveryAlert($description, $ip));
            }
        } catch (\Throwable $e) {
            Log::warning('Emergency recovery alert email not sent', ['error' => $e->getMessage()]);
        }
    }
}
