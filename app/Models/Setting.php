<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        // Cache a plain array, not the Eloquent model — prevents unserialize errors
        $data = Cache::remember('setting_' . $key, 3600, function () use ($key) {
            $row = static::where('key', $key)->first();
            return $row ? ['value' => $row->value, 'type' => $row->type] : null;
        });

        if (!$data) {
            return $default;
        }

        return match ($data['type']) {
            'integer' => (int) $data['value'],
            'boolean' => filter_var($data['value'], FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($data['value'], true),
            'float'   => (float) $data['value'],
            default   => $data['value'],
        };
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        $storedValue = match ($type) {
            'json' => is_string($value) ? $value : json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type]
        );

        Cache::forget('setting_' . $key);
    }

    /**
     * Get all settings by group
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::get($setting->key)];
            })
            ->toArray();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }
    }

    /**
     * The system's single source of truth for "what school year is it right
     * now." Previously reimplemented independently in at least four places
     * (EnrollmentController, Finance\DashboardController,
     * TeacherAssignmentController, ProfileController) — two of which
     * ignored this setting entirely and derived the year from whichever
     * active Section had the newest `school_year` instead. That meant
     * editing "Current School Year" in Admin > Settings silently had no
     * effect on Reports, the Finance dashboard, or most of the Admin
     * dashboard's stats — only new enrollment applications actually
     * respected it. All of those call sites now delegate here instead.
     *
     * Priority: (1) this setting, when an admin has actually configured it
     * — authoritative, since editing it is the one explicit control meant
     * to drive this system-wide; (2) the latest active Section's
     * school_year, for the period before anyone has set it; (3) a
     * date-based guess (new school year starts in June) as the last resort.
     */
    public static function getCurrentSchoolYear(): string
    {
        $configured = static::get('current_school_year');
        if ($configured && preg_match('/^\d{4}-\d{4}$/', $configured)) {
            return $configured;
        }

        $latestSectionYear = \App\Models\Section::where('is_active', true)
            ->orderByDesc('school_year')
            ->value('school_year');
        if ($latestSectionYear) {
            return $latestSectionYear;
        }

        $year = now()->month >= 6 ? now()->year : now()->year - 1;
        return $year . '-' . ($year + 1);
    }

    /** First and last start-year of the shared School Year range (2020-2021 … 2030-2031). */
    public const SCHOOL_YEAR_FIRST = 2020;
    public const SCHOOL_YEAR_LAST  = 2030;

    /**
     * Shared "school year" dropdown range used by every School Year filter in
     * the system, e.g. ['2030-2031', ..., '2026-2027', ..., '2020-2021'].
     *
     * Several school-year filters across the app were copy-pasted with their
     * own ranges and drifted apart; this is the single implementation they
     * all use now. It always covers SCHOOL_YEAR_FIRST..SCHOOL_YEAR_LAST and
     * widens automatically if the current school year gets within
     * $yearsBack / $yearsForward of either end.
     *
     * Descending order (most recent first) since that's what every one of
     * those dropdowns actually wants to show by default.
     */
    public static function schoolYearOptions(int $yearsBack = 5, int $yearsForward = 4): array
    {
        $baseYear = (int) substr(static::getCurrentSchoolYear(), 0, 4);
        $from     = min(self::SCHOOL_YEAR_FIRST, $baseYear - $yearsBack);
        $to       = max(self::SCHOOL_YEAR_LAST, $baseYear + $yearsForward);

        $years = [];
        for ($y = $to; $y >= $from; $y--) {
            $years[] = $y . '-' . ($y + 1);
        }
        return $years;
    }
}
