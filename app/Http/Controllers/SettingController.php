<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
        $settings = Setting::all()->groupBy('group');

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
                // Get existing setting to preserve type/group, or create new
                $existing = Setting::where('key', $item['key'])->first();
                $type = $existing?->type ?? 'decimal';
                $group = $existing?->group ?? 'financial';
                
                Log::info("Saving setting: key={$item['key']}, value={$item['value']}, type={$type}, group={$group}");
                
                // Update or create with group
                Setting::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => $item['value'],
                        'type' => $type,
                        'group' => $group,
                        'label' => $existing?->label ?? str_replace('_', ' ', ucwords($item['key'])),
                    ]
                );
                Cache::forget('setting_' . $item['key']);
                
                // Verify the save
                $verify = Setting::where('key', $item['key'])->first();
                Log::info("Verified setting: key={$item['key']}, saved_value={$verify?->value}");
            }

            Setting::clearCache();

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

        return response()->json([
            'success'          => true,
            'enrollment_open'  => $newValue,
            'unassessed_count' => $unassessedCount,
            'message'          => $newValue ? 'Enrollment is now OPEN.' : 'Enrollment is now CLOSED.',
        ]);
    }

    /**
     * Toggle maintenance mode on/off
     */
    public function toggleMaintenance()
    {
        $current  = Setting::get('maintenance_mode', false);
        $newValue = !$current;

        Setting::set('maintenance_mode', $newValue, 'boolean');

        return response()->json([
            'success'          => true,
            'maintenance_mode' => $newValue,
            'message'          => $newValue
                ? 'Maintenance mode ON — student and teacher portals are now blocked.'
                : 'Maintenance mode OFF — portals are accessible again.',
        ]);
    }

    /**
     * Initialize default settings if not present
     */
    public function seedDefaults()
    {
        $defaults = [
            // School Information
            ['key' => 'school_name', 'value' => 'IEMELIF Learning Center', 'type' => 'string', 'group' => 'school', 'label' => 'School Name', 'description' => 'Official school name displayed across the system'],
            ['key' => 'school_address', 'value' => 'General Tinio, Nueva Ecija', 'type' => 'string', 'group' => 'school', 'label' => 'School Address', 'description' => 'Official school address'],
            ['key' => 'school_phone', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'School Phone', 'description' => 'Contact phone number'],
            ['key' => 'school_email', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'School Email', 'description' => 'Contact email address'],
            ['key' => 'school_logo', 'value' => '/images/logo.png', 'type' => 'string', 'group' => 'school', 'label' => 'School Logo Path', 'description' => 'Path to school logo image'],
            ['key' => 'principal_name', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'Principal Name', 'description' => 'Name of school principal/director'],
            ['key' => 'school_motto', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'School Motto', 'description' => 'School motto or vision statement'],
            ['key' => 'gcash_number', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'GCash Number', 'description' => 'GCash mobile number shown to students for payment'],
            ['key' => 'gcash_account_name', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'GCash Account Name', 'description' => 'Account name associated with the GCash number'],
            ['key' => 'gcash_qr_path', 'value' => '', 'type' => 'string', 'group' => 'school', 'label' => 'GCash QR Code Path', 'description' => 'Relative path inside storage/ for the GCash QR code image'],

            // Academic Settings
            ['key' => 'current_school_year', 'value' => '2026-2027', 'type' => 'string', 'group' => 'academic', 'label' => 'Current School Year', 'description' => 'Active school year (e.g. 2026-2027)'],
            ['key' => 'school_year_start', 'value' => '2026-06-01', 'type' => 'string', 'group' => 'academic', 'label' => 'School Year Start', 'description' => 'Start date of the school year'],
            ['key' => 'school_year_end', 'value' => '2027-03-31', 'type' => 'string', 'group' => 'academic', 'label' => 'School Year End', 'description' => 'End date of the school year'],
            ['key' => 'passing_grade', 'value' => '75', 'type' => 'integer', 'group' => 'academic', 'label' => 'Passing Grade', 'description' => 'Minimum grade to pass a subject'],
            ['key' => 'grade_scale_max', 'value' => '100', 'type' => 'integer', 'group' => 'academic', 'label' => 'Grade Scale Maximum', 'description' => 'Maximum possible grade'],
            ['key' => 'ww_weight', 'value' => '30', 'type' => 'integer', 'group' => 'academic', 'label' => 'Written Works Weight (%)', 'description' => 'Percentage weight for written works'],
            ['key' => 'pt_weight', 'value' => '50', 'type' => 'integer', 'group' => 'academic', 'label' => 'Performance Task Weight (%)', 'description' => 'Percentage weight for performance tasks'],
            ['key' => 'qa_weight', 'value' => '20', 'type' => 'integer', 'group' => 'academic', 'label' => 'Assessment Weight (%)', 'description' => 'Percentage weight for quarterly assessment'],
            ['key' => 'total_terms', 'value' => '3', 'type' => 'integer', 'group' => 'academic', 'label' => 'Total Terms', 'description' => 'Number of terms in a school year'],
            ['key' => 'enrollment_open', 'value' => '1', 'type' => 'boolean', 'group' => 'academic', 'label' => 'Enrollment Open', 'description' => 'Whether online enrollment is currently accepting applications'],
            ['key' => 'enrollment_target_year', 'value' => '', 'type' => 'string', 'group' => 'academic', 'label' => 'Enrollment Target School Year', 'description' => 'The school year students are enrolling INTO (e.g. 2027-2028). Leave blank to auto-compute from current date.'],

            // Financial Settings
            // Fee Component Settings (editable per component)
            ['key' => 'fee_tuition', 'value' => '7505', 'type' => 'float', 'group' => 'financial', 'label' => 'Tuition Fee', 'description' => 'Base tuition fee amount'],
            ['key' => 'fee_misc', 'value' => '2800', 'type' => 'float', 'group' => 'financial', 'label' => 'Misc/Registration/PTA Fee', 'description' => 'Miscellaneous, Registration, PTA fees (2000+700+100)'],
            ['key' => 'fee_insurance', 'value' => '150', 'type' => 'float', 'group' => 'financial', 'label' => 'Insurance Fee', 'description' => 'Insurance fee amount'],
            ['key' => 'fee_electric', 'value' => '2000', 'type' => 'float', 'group' => 'financial', 'label' => 'Electric Bill Fee', 'description' => 'Electric bill fee amount'],
            ['key' => 'fee_books_nursery', 'value' => '3550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Nursery/Kinder', 'description' => 'Books fee for Nursery and Kindergarten'],
            ['key' => 'fee_books_grade1', 'value' => '4550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 1', 'description' => 'Books fee for Grade 1'],
            ['key' => 'fee_books_grade2', 'value' => '4550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 2', 'description' => 'Books fee for Grade 2'],
            ['key' => 'fee_books_grade3', 'value' => '5050', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 3', 'description' => 'Books fee for Grade 3'],
            ['key' => 'fee_books_grade4', 'value' => '5550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 4', 'description' => 'Books fee for Grade 4'],
            ['key' => 'fee_books_grade5', 'value' => '5550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 5', 'description' => 'Books fee for Grade 5'],
            ['key' => 'fee_books_grade6', 'value' => '5550', 'type' => 'float', 'group' => 'financial', 'label' => 'Books Fee - Grade 6', 'description' => 'Books fee for Grade 6'],

            // Payment Option Settings
            ['key' => 'payment_option_a_discount', 'value' => '1501', 'type' => 'float', 'group' => 'financial', 'label' => 'Option A: Cash Discount Amount', 'description' => 'Cash basis discount amount (e.g., 1501 for ~20% off)'],
            ['key' => 'payment_option_b_monthly', 'value' => '1056.10', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B: Monthly Payment', 'description' => 'Monthly payment amount for Option B (July-March)'],
            ['key' => 'payment_option_c_monthly', 'value' => '1278.32', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C: Monthly Payment', 'description' => 'Monthly payment amount for Option C (July-March)'],
            ['key' => 'payment_option_d_monthly', 'value' => '1278.32', 'type' => 'float', 'group' => 'financial', 'label' => 'Option D: Monthly Payment', 'description' => 'Monthly payment amount for Option D (July-March)'],

            // Down Payment Settings - Option B
            ['key' => 'dp_b_nursery', 'value' => '6500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Nursery', 'description' => 'Downpayment for Nursery in Option B'],
            ['key' => 'dp_b_kinder', 'value' => '6500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Kinder', 'description' => 'Downpayment for Kinder in Option B'],
            ['key' => 'dp_b_grade1', 'value' => '7500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 1', 'description' => 'Downpayment for Grade 1 in Option B'],
            ['key' => 'dp_b_grade2', 'value' => '7500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 2', 'description' => 'Downpayment for Grade 2 in Option B'],
            ['key' => 'dp_b_grade3', 'value' => '8000', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 3', 'description' => 'Downpayment for Grade 3 in Option B'],
            ['key' => 'dp_b_grade4', 'value' => '8500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 4', 'description' => 'Downpayment for Grade 4 in Option B'],
            ['key' => 'dp_b_grade5', 'value' => '8500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 5', 'description' => 'Downpayment for Grade 5 in Option B'],
            ['key' => 'dp_b_grade6', 'value' => '8500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option B Downpayment - Grade 6', 'description' => 'Downpayment for Grade 6 in Option B'],

            // Down Payment Settings - Option C (Elementary)
            ['key' => 'dp_c_grade1', 'value' => '5500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 1', 'description' => 'Downpayment for Grade 1 in Option C'],
            ['key' => 'dp_c_grade2', 'value' => '5500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 2', 'description' => 'Downpayment for Grade 2 in Option C'],
            ['key' => 'dp_c_grade3', 'value' => '6000', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 3', 'description' => 'Downpayment for Grade 3 in Option C'],
            ['key' => 'dp_c_grade4', 'value' => '6500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 4', 'description' => 'Downpayment for Grade 4 in Option C'],
            ['key' => 'dp_c_grade5', 'value' => '6500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 5', 'description' => 'Downpayment for Grade 5 in Option C'],
            ['key' => 'dp_c_grade6', 'value' => '6500', 'type' => 'float', 'group' => 'financial', 'label' => 'Option C Downpayment - Grade 6', 'description' => 'Downpayment for Grade 6 in Option C'],

            // Down Payment Settings - Option D (Pre-Elementary)
            ['key' => 'dp_d_nursery', 'value' => '4505', 'type' => 'float', 'group' => 'financial', 'label' => 'Option D Downpayment - Nursery', 'description' => 'Downpayment for Nursery in Option D'],
            ['key' => 'dp_d_kinder', 'value' => '4505', 'type' => 'float', 'group' => 'financial', 'label' => 'Option D Downpayment - Kinder', 'description' => 'Downpayment for Kinder in Option D'],

            // Legacy settings (keep for compatibility)
            ['key' => 'late_payment_penalty', 'value' => '0', 'type' => 'float', 'group' => 'financial', 'label' => 'Late Payment Penalty (%)', 'description' => 'Penalty percentage for late payments'],
            ['key' => 'discount_sibling', 'value' => '0', 'type' => 'float', 'group' => 'financial', 'label' => 'Sibling Discount (%)', 'description' => 'Discount for siblings enrolled together'],

            // Security Settings
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'integer', 'group' => 'security', 'label' => 'Session Timeout (minutes)', 'description' => 'Minutes of inactivity before auto logout'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security', 'label' => 'Max Login Attempts', 'description' => 'Maximum failed login attempts before lockout'],
            ['key' => 'lockout_duration', 'value' => '15', 'type' => 'integer', 'group' => 'security', 'label' => 'Lockout Duration (minutes)', 'description' => 'Minutes to lock account after max failed attempts'],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'group' => 'security', 'label' => 'Min Password Length', 'description' => 'Minimum password character length'],
            ['key' => 'require_password_uppercase', 'value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'Require Uppercase in Password', 'description' => 'Password must contain at least one uppercase letter'],
            ['key' => 'require_password_number', 'value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'Require Number in Password', 'description' => 'Password must contain at least one number'],
        ];

        $created = 0;
        foreach ($defaults as $setting) {
            if (!Setting::where('key', $setting['key'])->exists()) {
                Setting::create($setting);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Settings seeded. {$created} new settings created.",
            'created' => $created
        ]);
    }
}
