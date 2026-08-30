<?php

namespace App\Http\Controllers;

use App\Models\FeeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeeSettingController extends Controller
{
    /**
     * Get all fee settings
     */
    public function index()
    {
        $fee = FeeSetting::current();

        return response()->json([
            'success' => true,
            'settings' => $fee->toArray()
        ]);
    }

    /**
     * Update fee settings
     */
    public function update(Request $request)
    {
        try {
            $fee = FeeSetting::current();

            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'nullable',
            ]);

            // Map of input keys to model column names
            $keyToColumn = [
                'fee_tuition' => 'tuition',
                'fee_misc' => 'misc',
                'fee_insurance' => 'insurance',
                'fee_electric' => 'electric',
                'fee_books_nursery' => 'books_nursery',
                'fee_books_grade1' => 'books_grade1',
                'fee_books_grade3' => 'books_grade3',
                'fee_books_grade4' => 'books_grade4',
                'payment_option_a_discount' => 'option_a_discount',
                'fee_optb_monthly_tuition' => 'optb_monthly_tuition',
                'fee_optb_monthly_electric' => 'optb_monthly_electric',
                'dp_b_nursery' => 'optb_dp_nursery',
                'dp_b_kinder' => 'optb_dp_kinder',
                'dp_b_grade1' => 'optb_dp_grade1',
                'dp_b_grade3' => 'optb_dp_grade3',
                'dp_b_grade4' => 'optb_dp_grade4',
                'fee_optc_monthly_tuition' => 'optc_monthly_tuition',
                'fee_optc_monthly_misc' => 'optc_monthly_misc',
                'fee_optc_monthly_electric' => 'optc_monthly_electric',
                'dp_c_grade1' => 'optc_dp_grade1',
                'dp_c_grade3' => 'optc_dp_grade3',
                'dp_c_grade4' => 'optc_dp_grade4',
                'fee_optd_monthly_tuition' => 'optd_monthly_tuition',
                'fee_optd_monthly_misc' => 'optd_monthly_misc',
                'fee_optd_monthly_electric' => 'optd_monthly_electric',
                'dp_d_nursery' => 'optd_dp_nursery',
                'dp_d_kinder' => 'optd_dp_kinder',
            ];

            $updateData = [];
            foreach ($validated['settings'] as $item) {
                $key = $item['key'];
                $value = $item['value'];

                if (isset($keyToColumn[$key])) {
                    $updateData[$keyToColumn[$key]] = $value !== null && $value !== '' ? (float) $value : 0;
                }
            }

            if (!empty($updateData)) {
                $fee->fill($updateData);
                $fee->save();
            }

            Log::info('FeeSetting updated: ' . json_encode($updateData));

            return response()->json([
                'success' => true,
                'message' => 'Fee settings updated successfully',
                'saved' => $fee->fresh()->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('FeeSetting update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fee settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
