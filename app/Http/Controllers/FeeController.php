<?php

namespace App\Http\Controllers;

use App\Models\FeeSetting;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Get all fee settings (public API for student portal)
     */
    public function getFeeSettings()
    {
        $fee = FeeSetting::current();

        return response()->json([
            'success' => true,
            'settings' => $fee->toArray()
        ]);
    }

    /**
     * Calculate fee breakdown for a grade level
     */
    public function calculateFee(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'payment_option' => 'required|string|in:A,B,C,D'
        ]);

        $gradeLevel = $request->grade_level;
        $paymentOption = $request->payment_option;

        $calculation = $this->calculateFeeBreakdown($gradeLevel, $paymentOption);

        return response()->json([
            'success' => true,
            'data' => $calculation
        ]);
    }

    /**
     * Get available payment options for a grade level
     */
    public function getPaymentOptions(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string'
        ]);

        $gradeLevel = strtolower(str_replace(' ', '', $request->grade_level));

        // Determine available options based on grade level
        $options = [];

        // Option A (Cash Basis) - Available to all
        $options['A'] = [
            'label' => 'Option A: Cash Basis',
            'description' => '20% discount on total fee',
            'available' => true
        ];

        // Option B (Monthly Payment) - Available to all
        $options['B'] = [
            'label' => 'Option B: Monthly Payment',
            'description' => '₱1,056.10/month (July-March)',
            'available' => true
        ];

        // Option C (Elem. Pupils Only Monthly) - Grade 1-6 only
        $isElementary = in_array($gradeLevel, ['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6']);
        $options['C'] = [
            'label' => 'Option C: Elem. Pupils Only Monthly',
            'description' => '₱1,278.32/month (July-March)',
            'available' => $isElementary
        ];

        // Option D (Pre-Elem Pupils Only Monthly) - Nursery/Kinder only
        $isPreElementary = in_array($gradeLevel, ['nursery', 'kindergarten']);
        $options['D'] = [
            'label' => 'Option D: Pre-Elem Pupils Only Monthly',
            'description' => '₱1,278.32/month (July-March)',
            'available' => $isPreElementary
        ];

        return response()->json([
            'success' => true,
            'grade_level' => $gradeLevel,
            'options' => $options
        ]);
    }

    /**
     * Internal method to calculate fee breakdown. Backed by the shared
     * FeeCalculator (DATABASE_NORMALIZATION_PLAN.md Phase 2) — same source
     * every other fee-quoting/charging path now uses, so this endpoint's
     * quote always matches what EnrollmentController actually charges.
     */
    private function calculateFeeBreakdown($gradeLevel, $paymentOption)
    {
        $gradeLevel = strtolower(str_replace(' ', '', $gradeLevel));
        $months = 9;

        $calc = \App\Services\FeeCalculator::calculate($gradeLevel, $paymentOption);

        $result = [
            'grade_level' => $gradeLevel,
            'payment_option' => $paymentOption,
            'components' => [
                'tuition' => $calc['tuition'],
                'misc' => $calc['misc'],
                'books' => $calc['books'],
                'insurance' => $calc['insurance'],
                'electric' => $calc['electric'],
            ],
            'base_total' => $calc['base_total'],
        ];

        if ($paymentOption === 'A') {
            $result['discount'] = $calc['discount'];
            $result['discount_description'] = 'Cash Basis Discount';
            $result['total_payable'] = $calc['total_due'];
            $result['downpayment'] = 0;
            $result['monthly_payment'] = 0;
            $result['months'] = 0;
        } elseif (in_array($paymentOption, ['B', 'C', 'D'], true)) {
            $result['downpayment'] = $calc['downpayment'];
            $result['monthly_payment'] = $calc['monthly_amount'];
            $result['months'] = $months;
            $result['total_monthly'] = round($calc['monthly_amount'] * $months, 2);
            $result['total_payable'] = $calc['total_due'];
            $result['discount'] = 0;
        }

        return $result;
    }

    /**
     * Get fee summary for all grade levels
     */
    public function getFeeSummary()
    {
        $gradeLevels = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
        $summary = [];

        foreach ($gradeLevels as $grade) {
            $summary[$grade] = [
                'option_a' => $this->calculateFeeBreakdown($grade, 'A'),
                'option_b' => $this->calculateFeeBreakdown($grade, 'B'),
            ];

            // Add Option C for elementary grades
            if (in_array($grade, ['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'])) {
                $summary[$grade]['option_c'] = $this->calculateFeeBreakdown($grade, 'C');
            }

            // Add Option D for pre-elementary grades
            if (in_array($grade, ['nursery', 'kindergarten'])) {
                $summary[$grade]['option_d'] = $this->calculateFeeBreakdown($grade, 'D');
            }
        }

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }
}
