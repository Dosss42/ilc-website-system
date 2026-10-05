<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for tuition/fee calculations, reading from
 * fee_components (DATABASE_NORMALIZATION_PLAN.md Phase 2).
 *
 * Before this existed, there were two independent fee calculators that could
 * (and did) disagree: FeeController read fee_settings correctly, but
 * EnrollmentController::calculatePaymentBreakdown() — the one that actually
 * decides what a real enrollment gets charged — used a hardcoded array that
 * never picked up admin changes to Fee Management. A live ₱1 discrepancy on
 * the Option A discount was found and confirmed this way. Every caller now
 * goes through this class instead, so there is exactly one number for any
 * given (grade_level, option) and it always reflects the current
 * fee_components data — no more silent drift between what a parent is quoted
 * and what they're actually charged.
 *
 * Existing callers keep their own distinct response shapes (EnrollmentController's
 * breakdown array, FeeController's JSON structure, etc.) — only the underlying
 * numbers are now shared, so nothing downstream (frontend JS, stored JSON
 * snapshots) needed to change shape.
 */
class FeeCalculator
{
    /** Grade-group fallback for fee types that don't vary within a group
     * (matches the exact grouping every prior calculator already used). */
    private const GRADE_GROUP = [
        'nursery' => 'nursery',
        'kindergarten' => 'kindergarten',
        'grade1' => 'grade1',
        'grade2' => 'grade1',
        'grade3' => 'grade3',
        'grade4' => 'grade4',
        'grade5' => 'grade4',
        'grade6' => 'grade4',
    ];

    private static ?array $cache = null;

    /** All fee_components rows, cached for the life of the request. */
    private static function rows(): array
    {
        if (self::$cache === null) {
            self::$cache = DB::table('fee_components')->get()->all();
        }
        return self::$cache;
    }

    /**
     * Look up one component's amount. $option/$gradeLevel null means "match
     * only rows that themselves have null there" (i.e. universal rows) —
     * pass the real option/grade to match a specific row, falling back to
     * the universal row if no grade/option-specific one exists.
     */
    private static function lookup(string $feeType, ?string $option = null, ?string $gradeLevel = null): ?float
    {
        $group = $gradeLevel ? (self::GRADE_GROUP[$gradeLevel] ?? $gradeLevel) : null;

        foreach (self::rows() as $row) {
            if ($row->fee_type !== $feeType) {
                continue;
            }
            if (($row->option ?? null) !== $option) {
                continue;
            }
            if (($row->grade_level ?? null) !== $group) {
                continue;
            }
            return (float) $row->amount;
        }

        return null;
    }

    public static function base(string $feeType): float
    {
        return self::lookup($feeType) ?? 0.0;
    }

    public static function books(string $gradeLevel): float
    {
        return self::lookup('books', null, $gradeLevel) ?? self::lookup('books', null, 'grade1') ?? 0.0;
    }

    public static function baseTotal(string $gradeLevel): float
    {
        return self::base('tuition') + self::base('misc') + self::base('insurance')
            + self::base('electric') + self::books($gradeLevel);
    }

    public static function discount(string $option): float
    {
        return self::lookup('discount', $option) ?? 0.0;
    }

    public static function downpayment(string $option, string $gradeLevel): float
    {
        return self::lookup('downpayment', $option, $gradeLevel) ?? 0.0;
    }

    /** Sum of every monthly_* component registered for the option (tuition
     * + electric for B; tuition + misc + electric for C/D). */
    public static function monthlyTotal(string $option): float
    {
        $total = 0.0;
        foreach (self::rows() as $row) {
            if ($row->option === $option && str_starts_with($row->fee_type, 'monthly_')) {
                $total += (float) $row->amount;
            }
        }
        return round($total, 2);
    }

    /**
     * Full breakdown for one (grade_level, option) pair — every field any
     * existing caller needs, so each can reshape into its own response
     * format without recomputing anything itself.
     */
    public static function calculate(string $gradeLevel, string $option): array
    {
        $breakdown = [
            'tuition' => self::base('tuition'),
            'misc' => self::base('misc'),
            'books' => self::books($gradeLevel),
            'insurance' => self::base('insurance'),
            'electric' => self::base('electric'),
            'base_total' => self::baseTotal($gradeLevel),
        ];

        switch ($option) {
            case 'A':
                $discount = self::discount('A');
                $breakdown['discount'] = $discount;
                $breakdown['total_due'] = $breakdown['base_total'] - $discount;
                $breakdown['payment_type'] = 'full';
                break;

            case 'B':
            case 'C':
            case 'D':
                $downpayment = self::downpayment($option, $gradeLevel);
                $monthly = self::monthlyTotal($option);
                $breakdown['downpayment'] = $downpayment;
                $breakdown['monthly_amount'] = $monthly;
                $breakdown['duration_months'] = 9;
                $breakdown['total_due'] = round($downpayment + ($monthly * 9), 2);
                $breakdown['payment_type'] = 'installment';
                break;
        }

        return $breakdown;
    }

    /** Clear the in-request cache — call after writing to fee_components
     * (e.g. right after syncComponents()) so a subsequent calculation in the
     * same request sees fresh values. */
    public static function forgetCache(): void
    {
        self::$cache = null;
    }

    /**
     * Dual-write fee_components from a fee_settings row (DATABASE_NORMALIZATION_PLAN.md
     * Phase 2) — the single place this mapping is defined, called by every
     * controller that can update fee_settings. $v should be the FULL current
     * state of the FeeSetting row (e.g. $fee->fresh()->toArray()), not just
     * whatever subset of fields a particular request happened to submit, so
     * a partial update never leaves fee_components half-written.
     *
     * Before this was shared, two separate controllers each wrote
     * fee_settings for the exact same "Fee Settings" feature (one reachable
     * from the Admin dashboard, one from the Finance portal) but only one of
     * them remembered to also sync fee_components. Since FeeCalculator —
     * every real enrollment's fee computation — reads fee_components, a fee
     * edit made through the other controller silently never took effect:
     * Finance's own settings page showed the new number, but a newly
     * enrolling student was still charged the old one.
     */
    public static function syncComponents(array $v): void
    {
        $set = function (?string $option, ?string $gradeLevel, string $feeType, $amount) {
            DB::table('fee_components')->updateOrInsert(
                ['option' => $option, 'grade_level' => $gradeLevel, 'fee_type' => $feeType],
                ['amount' => (float) ($amount ?? 0), 'updated_at' => now()]
            );
        };

        $set(null, null, 'tuition', $v['tuition'] ?? 0);
        $set(null, null, 'misc', $v['misc'] ?? 0);
        $set(null, null, 'insurance', $v['insurance'] ?? 0);
        $set(null, null, 'electric', $v['electric'] ?? 0);

        $set(null, 'nursery', 'books', $v['books_nursery'] ?? 0);
        $set(null, 'kindergarten', 'books', $v['books_nursery'] ?? 0);
        $set(null, 'grade1', 'books', $v['books_grade1'] ?? 0);
        $set(null, 'grade2', 'books', $v['books_grade1'] ?? 0);
        $set(null, 'grade3', 'books', $v['books_grade3'] ?? 0);
        $set(null, 'grade4', 'books', $v['books_grade4'] ?? 0);
        $set(null, 'grade5', 'books', $v['books_grade4'] ?? 0);
        $set(null, 'grade6', 'books', $v['books_grade4'] ?? 0);

        $set('A', null, 'discount', $v['option_a_discount'] ?? 0);

        $set('B', 'nursery', 'downpayment', $v['optb_dp_nursery'] ?? 0);
        $set('B', 'kindergarten', 'downpayment', $v['optb_dp_kinder'] ?? 0);
        $set('B', 'grade1', 'downpayment', $v['optb_dp_grade1'] ?? 0);
        $set('B', 'grade2', 'downpayment', $v['optb_dp_grade1'] ?? 0);
        $set('B', 'grade3', 'downpayment', $v['optb_dp_grade3'] ?? 0);
        $set('B', 'grade4', 'downpayment', $v['optb_dp_grade4'] ?? 0);
        $set('B', 'grade5', 'downpayment', $v['optb_dp_grade4'] ?? 0);
        $set('B', 'grade6', 'downpayment', $v['optb_dp_grade4'] ?? 0);
        $set('B', null, 'monthly_tuition', $v['optb_monthly_tuition'] ?? 0);
        $set('B', null, 'monthly_electric', $v['optb_monthly_electric'] ?? 0);

        $set('C', 'grade1', 'downpayment', $v['optc_dp_grade1'] ?? 0);
        $set('C', 'grade2', 'downpayment', $v['optc_dp_grade1'] ?? 0);
        $set('C', 'grade3', 'downpayment', $v['optc_dp_grade3'] ?? 0);
        $set('C', 'grade4', 'downpayment', $v['optc_dp_grade4'] ?? 0);
        $set('C', 'grade5', 'downpayment', $v['optc_dp_grade4'] ?? 0);
        $set('C', 'grade6', 'downpayment', $v['optc_dp_grade4'] ?? 0);
        $set('C', null, 'monthly_tuition', $v['optc_monthly_tuition'] ?? 0);
        $set('C', null, 'monthly_misc', $v['optc_monthly_misc'] ?? 0);
        $set('C', null, 'monthly_electric', $v['optc_monthly_electric'] ?? 0);

        $set('D', 'nursery', 'downpayment', $v['optd_dp_nursery'] ?? 0);
        $set('D', 'kindergarten', 'downpayment', $v['optd_dp_kinder'] ?? 0);
        $set('D', null, 'monthly_tuition', $v['optd_monthly_tuition'] ?? 0);
        $set('D', null, 'monthly_misc', $v['optd_monthly_misc'] ?? 0);
        $set('D', null, 'monthly_electric', $v['optd_monthly_electric'] ?? 0);
    }
}
