<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Support\Carbon;

/**
 * Automatic clearing of the audit trail (activity_logs). The Super Admin picks how long
 * entries are kept; older ones are deleted. Runs at most once a day, the first time an
 * Admin or Super Admin opens the logs (and from the daily scheduler where one runs) —
 * Railway has no cron, so clearing can't depend on the scheduler alone.
 *
 * Reason for clearing: personal data (who did what, from which IP) shouldn't be kept
 * longer than needed (Data Privacy Act, RA 10173), and it keeps backups and log search
 * from growing without limit.
 */
class AuditLogRetention
{
    public const SETTING = 'audit_log_retention_months';
    private const LAST_RUN = 'audit_log_last_cleared_on';

    /** Months to keep => label. 0 = never clear. Weeks aren't offered: payment disputes
     * can surface weeks later, and the log must still be there to settle them. */
    public const OPTIONS = [
        1  => '1 month',
        3  => '3 months',
        6  => '6 months',
        9  => '9 months',
        12 => '12 months',
        0  => 'Never (keep all)',
    ];

    public const DEFAULT_MONTHS = 12;

    /** Rows deleted per statement, so a big clear never locks the table for long. */
    private const BATCH = 1000;

    public static function months(): int
    {
        $months = Setting::get(self::SETTING);
        $months = ($months === null || $months === '') ? self::DEFAULT_MONTHS : (int) $months;
        return array_key_exists($months, self::OPTIONS) ? $months : self::DEFAULT_MONTHS;
    }

    public static function label(?int $months = null): string
    {
        return self::OPTIONS[$months ?? self::months()];
    }

    /** Entries older than this are cleared; null when set to keep everything. */
    public static function cutoff(): ?Carbon
    {
        $months = self::months();
        return $months > 0 ? now()->subMonths($months) : null;
    }

    public static function setMonths(int $months): void
    {
        Setting::set(self::SETTING, $months, 'integer');
    }

    /** Clear old entries unless that already ran today. Returns how many were deleted. */
    public static function clearIfDue(): int
    {
        if (Setting::get(self::LAST_RUN) === now()->toDateString()) {
            return 0;
        }
        return self::clear();
    }

    /** Clear every entry older than the retention period now. Returns how many were deleted. */
    public static function clear(): int
    {
        Setting::set(self::LAST_RUN, now()->toDateString());

        $cutoff = self::cutoff();
        if (!$cutoff) {
            return 0;
        }

        $deleted = 0;
        do {
            $n = ActivityLog::where('created_at', '<', $cutoff)->limit(self::BATCH)->delete();
            $deleted += $n;
        } while ($n === self::BATCH);

        if ($deleted > 0) {
            // Recorded as "System" (no user): it isn't the action of whoever happened to open the page
            ActivityLog::create([
                'event_type'   => 'delete',
                'description'  => "Automatic clearing: {$deleted} audit log(s) older than " . self::label()
                    . ' removed (before ' . $cutoff->format('M d, Y') . ')',
                'subject_type' => 'ActivityLog',
            ]);
        }

        return $deleted;
    }
}
