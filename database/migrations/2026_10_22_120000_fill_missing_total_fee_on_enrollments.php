<?php

use App\Services\FeeCalculator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // generateXenditLink() and approve() checked `!total_fee` / `empty(total_fee)`, but an
        // unset fee is the string "0.00" (column default 0, cast decimal:2), which PHP treats as
        // set — so a student who picked a plan and paid online without the fee ever being saved
        // was left at total_fee 0 (enrollment 2157 on 2026-10-22: Kindergarten, Option A, paid
        // ₱14,505.99 against ₱0). Fill the fee from the chosen plan, exactly as those two places
        // now do. Only enrollments with a plan and no fee are touched, so this is a no-op on a
        // database without the problem.
        $rows = DB::table('enrollments')
            ->where('total_fee', '<=', 0)
            ->whereNotNull('payment_option')
            ->where('status', '!=', 'cancelled')
            ->get(['id', 'grade_level', 'student_data', 'payment_option', 'payment_amount', 'payment_status', 'downpayment_amount', 'monthly_amount']);

        foreach ($rows as $e) {
            $grade = $e->grade_level ?: (json_decode($e->student_data ?? '', true)['grade_level'] ?? null);
            if (!$grade) {
                continue;
            }
            $calc = FeeCalculator::calculate($grade, $e->payment_option);
            $total = (float) ($calc['total_due'] ?? 0);
            if ($total <= 0) {
                continue;
            }
            $remaining = max(0, round($total - (float) $e->payment_amount, 2));

            $update = ['total_fee' => $total, 'remaining_balance' => $remaining, 'updated_at' => now()];
            if ((float) $e->downpayment_amount <= 0 && isset($calc['downpayment'])) {
                $update['downpayment_amount'] = $calc['downpayment'];
            }
            if ((float) $e->monthly_amount <= 0 && isset($calc['monthly_amount'])) {
                $update['monthly_amount'] = $calc['monthly_amount'];
            }
            // With a ₱0 fee any payment counted as "fully paid"
            if ($remaining > 0 && $e->payment_status === 'paid') {
                $update['payment_status'] = 'partial';
            }
            DB::table('enrollments')->where('id', $e->id)->update($update);
        }
    }

    public function down(): void
    {
        // Not reversible — the previous value (0) was wrong
    }
};
