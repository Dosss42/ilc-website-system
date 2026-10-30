<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Support\DefaultSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get all settings grouped
     */
    public function index()
    {
        // A setting added later (e.g. guidance_counselor_name) has no row until Settings is saved once —
        // show its default in the field (not saved here; Save stores it) instead of an empty box
        $stored = Setting::all();
        $missing = collect(DefaultSettings::all())->whereNotIn('key', $stored->pluck('key'))
            ->map(fn ($d) => new Setting($d));
        $settings = $stored->concat($missing)->groupBy('group');

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Get settings by group
     */
    public function getGroup(string $group)
    {
        $settings = Setting::where('group', $group)->get();

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update settings (bulk)
     */
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'nullable',
            ]);

            foreach ($data['settings'] as $item) {
                // Get existing setting to preserve type/group, or create new — typed like its
                // default (a missing session_timeout used to be saved as a 'decimal' fee)
                $existing = Setting::where('key', $item['key'])->first();
                $default = DefaultSettings::find($item['key']);
                $type = $existing?->type ?? $default['type'] ?? 'decimal';
                $group = $existing?->group ?? $default['group'] ?? 'financial';
                
                Log::info("Saving setting: key={$item['key']}, value={$item['value']}, type={$type}, group={$group}");
                
                // Update or create with group
                Setting::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => $item['value'],
                        'type' => $type,
                        'group' => $group,
                        'label' => $existing?->label ?? $default['label'] ?? str_replace('_', ' ', ucwords($item['key'])),
                    ]
                );
                Cache::forget('setting_' . $item['key']);
                
                // Verify the save
                $verify = Setting::where('key', $item['key'])->first();
                Log::info("Verified setting: key={$item['key']}, saved_value={$verify?->value}");
            }

            Setting::clearCache();

            $keys = implode(', ', array_column($data['settings'], 'key'));
            ActivityLogger::log('update', 'Updated system settings: ' . (mb_strlen($keys) > 200 ? mb_substr($keys, 0, 200) . '…' : $keys), 'Setting');

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle enrollment open/closed — with optional unassessed student count
     */
    public function toggleEnrollment(Request $request)
    {
        $force = $request->input('force'); // 'open' or 'close'

        $current = Setting::get('enrollment_open', true);
        $newValue = $force === 'open' ? true : ($force === 'close' ? false : !$current);

        // If opening, count Grade 1-6 enrolled students with no promotion record yet
        $unassessedCount = 0;
        if ($newValue) {
            $unassessedCount = DB::table('enrollments')
                ->whereIn('status', ['enrolled', 'completed'])
                ->whereIn(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(student_data, '$.grade_level'))"),
                    ['grade1','grade2','grade3','grade4','grade5','grade6'])
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('promotions')
                      ->whereColumn('promotions.student_id', 'enrollments.user_id');
                })
                ->count();
        }

        Setting::set('enrollment_open', $newValue, 'boolean');

        ActivityLogger::log('update', 'Enrollment turned ' . ($newValue ? 'ON' : 'OFF'), 'Setting');

        return response()->json([
            'success'          => true,
            'enrollment_open'  => $newValue,
            'unassessed_count' => $unassessedCount,
            'message'          => $newValue ? 'Enrollment is now OPEN.' : 'Enrollment is now CLOSED.',
        ]);
    }

    /**
     * Toggle PORTAL maintenance mode on/off — blocks the Student and
     * Teacher portals only. Separate from site maintenance mode below so
     * the two can be switched independently.
     */
    public function toggleMaintenance()
    {
        $current  = Setting::get('maintenance_mode', false);
        $newValue = !$current;

        Setting::set('maintenance_mode', $newValue, 'boolean');

        ActivityLogger::log('update', 'Portal maintenance mode turned ' . ($newValue ? 'ON' : 'OFF'), 'Setting');

        return response()->json([
            'success'          => true,
            'maintenance_mode' => $newValue,
            'message'          => $newValue
                ? 'Portal maintenance mode ON — student and teacher portals are now blocked.'
                : 'Portal maintenance mode OFF — portals are accessible again.',
        ]);
    }

    /**
     * Toggle WEBSITE maintenance mode on/off — blocks the public site
     * (home page and other public routes) only. Student/Teacher portal
     * access is controlled separately by toggleMaintenance() above.
     */
    public function toggleSiteMaintenance()
    {
        $current  = Setting::get('site_maintenance_mode', false);
        $newValue = !$current;

        Setting::set('site_maintenance_mode', $newValue, 'boolean');

        ActivityLogger::log('update', 'Website maintenance mode turned ' . ($newValue ? 'ON' : 'OFF'), 'Setting');

        return response()->json([
            'success'               => true,
            'site_maintenance_mode' => $newValue,
            'message'               => $newValue
                ? 'Website maintenance mode ON — the public site is now showing a maintenance notice.'
                : 'Website maintenance mode OFF — the public site is accessible again.',
        ]);
    }

    /**
     * Initialize default settings if not present
     */
    public function seedDefaults()
    {
        $created = DefaultSettings::install();

        return response()->json([
            'success' => true,
            'message' => "Settings seeded. {$created} new settings created.",
            'created' => $created
        ]);
    }
}
