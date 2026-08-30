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
     * Internal method to calculate fee breakdown
     */
    private function calculateFeeBreakdown($gradeLevel, $paymentOption)
    {
        $gradeLevel = strtolower(str_replace(' ', '', $gradeLevel));
        $fee = FeeSetting::current();
        $months = 9;

        $tuition = (float) $fee->tuition;
        $misc = (float) $fee->misc;
        $insurance = (float) $fee->insurance;
        $electric = (float) $fee->electric;

        // Get books fee based on grade level
        $booksMap = [
            'nursery' => (float) $fee->books_nursery,
            'kindergarten' => (float) $fee->books_nursery,
            'grade1' => (float) $fee->books_grade1,
            'grade2' => (float) $fee->books_grade1,
            'grade3' => (float) $fee->books_grade3,
            'grade4' => (float) $fee->books_grade4,
            'grade5' => (float) $fee->books_grade4,
            'grade6' => (float) $fee->books_grade4,
        ];
        $books = $booksMap[$gradeLevel] ?? (float) $fee->books_grade1;

        // Calculate base total
        $baseTotal = $tuition + $misc + $books + $insurance + $electric;

        $result = [
            'grade_level' => $gradeLevel,
            'payment_option' => $paymentOption,
            'components' => [
                'tuition' => $tuition,
                'misc' => $misc,
                'books' => $books,
                'insurance' => $insurance,
                'electric' => $electric
            ],
            'base_total' => $baseTotal
        ];

        switch ($paymentOption) {
            case 'A': // Cash Basis - discount
                $discount = (float) $fee->option_a_discount;
                $result['discount'] = $discount;
                $result['discount_description'] = 'Cash Basis Discount';
                $result['total_payable'] = $baseTotal - $discount;
                $result['downpayment'] = 0;
                $result['monthly_payment'] = 0;
                $result['months'] = 0;
                break;

            case 'B': // Monthly Payment
                $dpMap = [
                    'nursery' => (float) $fee->optb_dp_nursery,
                    'kindergarten' => (float) $fee->optb_dp_kinder,
                    'grade1' => (float) $fee->optb_dp_grade1,
                    'grade2' => (float) $fee->optb_dp_grade1,
                    'grade3' => (float) $fee->optb_dp_grade3,
                    'grade4' => (float) $fee->optb_dp_grade4,
                    'grade5' => (float) $fee->optb_dp_grade4,
                    'grade6' => (float) $fee->optb_dp_grade4,
                ];
                $downpayment = $dpMap[$gradeLevel] ?? (float) $fee->optb_dp_nursery;
                $tuitionMo = (float) $fee->optb_monthly_tuition;
                $electricMo = (float) $fee->optb_monthly_electric;
                $monthly = round($tuitionMo + $electricMo, 2);

                $result['downpayment'] = $downpayment;
                $result['monthly_payment'] = $monthly;
                $result['months'] = $months;
                $result['total_monthly'] = round($monthly * $months, 2);
                $result['total_payable'] = round($downpayment + ($monthly * $months), 2);
                $result['discount'] = 0;
                break;

            case 'C': // Elem. Pupils Only Monthly
                $dpMap = [
                    'grade1' => (float) $fee->optc_dp_grade1,
                    'grade2' => (float) $fee->optc_dp_grade1,
                    'grade3' => (float) $fee->optc_dp_grade3,
                    'grade4' => (float) $fee->optc_dp_grade4,
                    'grade5' => (float) $fee->optc_dp_grade4,
                    'grade6' => (float) $fee->optc_dp_grade4,
                ];
                $downpayment = $dpMap[$gradeLevel] ?? (float) $fee->optc_dp_grade1;
                $tuitionMo = (float) $fee->optc_monthly_tuition;
                $miscMo = (float) $fee->optc_monthly_misc;
                $electricMo = (float) $fee->optc_monthly_electric;
                $monthly = round($tuitionMo + $miscMo + $electricMo, 2);

                $result['downpayment'] = $downpayment;
                $result['monthly_payment'] = $monthly;
                $result['months'] = $months;
                $result['total_monthly'] = round($monthly * $months, 2);
                $result['total_payable'] = round($downpayment + ($monthly * $months), 2);
                $result['discount'] = 0;
                break;

            case 'D': // Pre-Elem Pupils Only Monthly
                $dpMap = [
                    'nursery' => (float) $fee->optd_dp_nursery,
                    'kindergarten' => (float) $fee->optd_dp_kinder,
                ];
                $downpayment = $dpMap[$gradeLevel] ?? (float) $fee->optd_dp_nursery;
                $tuitionMo = (float) $fee->optd_monthly_tuition;
                $miscMo = (float) $fee->optd_monthly_misc;
                $electricMo = (float) $fee->optd_monthly_electric;
                $monthly = round($tuitionMo + $miscMo + $electricMo, 2);

                $result['downpayment'] = $downpayment;
                $result['monthly_payment'] = $monthly;
                $result['months'] = $months;
                $result['total_monthly'] = round($monthly * $months, 2);
                $result['total_payable'] = round($downpayment + ($monthly * $months), 2);
                $result['discount'] = 0;
                break;
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
