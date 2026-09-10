<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Expand step of DATABASE_NORMALIZATION_PLAN.md Phase 2. Replaces the
 * columns-as-data pattern in fee_settings (option x grade_level encoded into
 * ~30 column names like optb_dp_grade1) with real rows: (fee_type, option,
 * grade_level, amount). option and grade_level are nullable — null means
 * "applies regardless" (e.g. tuition is the same for every grade; an
 * option's monthly components don't vary by grade), matching the actual
 * shape of the current hardcoded/fee_settings data rather than forcing every
 * row to name an option and grade it doesn't really have.
 *
 * This migration also backfills from the current fee_settings row so
 * fee_components starts populated with real data, not empty — the plan's
 * "one row per existing column" step, done here rather than as a separate
 * data migration since it's a one-time, idempotent transform of a single row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fee_components')) {
            Schema::create('fee_components', function (Blueprint $table) {
                $table->id();
                $table->string('option', 1)->nullable(); // A/B/C/D, null = applies to all options
                $table->string('grade_level', 20)->nullable(); // null = applies to all grades
                $table->string('fee_type', 30); // tuition, misc, insurance, electric, books, discount, downpayment, monthly_tuition, monthly_misc, monthly_electric
                $table->decimal('amount', 10, 2);
                $table->timestamps();
                // No unique constraint here on purpose — MySQL treats NULL as
                // distinct in unique indexes, so a (option, grade_level,
                // fee_type) unique key would silently allow duplicate rows
                // whenever option/grade_level are both null (the common case
                // here). Idempotency is instead handled below via
                // updateOrInsert(), which does a correct NULL-aware WHERE match.
                $table->index(['fee_type', 'option', 'grade_level']);
            });
        }

        $fee = DB::table('fee_settings')->first();
        if (!$fee) {
            return; // nothing to backfill yet — fine, the app creates a default row on first use
        }

        $add = function (?string $option, ?string $gradeLevel, string $feeType, $amount) {
            // updateOrInsert's WHERE half correctly matches NULLs (unlike a
            // DB unique index would), so re-running this migration is safe.
            $existing = DB::table('fee_components')
                ->where('option', $option)
                ->where('grade_level', $gradeLevel)
                ->where('fee_type', $feeType)
                ->exists();

            DB::table('fee_components')->updateOrInsert(
                ['option' => $option, 'grade_level' => $gradeLevel, 'fee_type' => $feeType],
                $existing
                    ? ['amount' => (float) $amount, 'updated_at' => now()]
                    : ['amount' => (float) $amount, 'created_at' => now(), 'updated_at' => now()]
            );
        };

        // Base fees — flat across every grade level
        $add(null, null, 'tuition', $fee->tuition);
        $add(null, null, 'misc', $fee->misc);
        $add(null, null, 'insurance', $fee->insurance);
        $add(null, null, 'electric', $fee->electric);

        // Books — varies by grade group (nursery+kinder share one rate, grade1+2
        // share another, grade3 its own, grade4+5+6 share another — matching the
        // exact grouping every current calculator already uses)
        $add(null, 'nursery', 'books', $fee->books_nursery);
        $add(null, 'kindergarten', 'books', $fee->books_nursery);
        $add(null, 'grade1', 'books', $fee->books_grade1);
        $add(null, 'grade2', 'books', $fee->books_grade1);
        $add(null, 'grade3', 'books', $fee->books_grade3);
        $add(null, 'grade4', 'books', $fee->books_grade4);
        $add(null, 'grade5', 'books', $fee->books_grade4);
        $add(null, 'grade6', 'books', $fee->books_grade4);

        // Option A — flat discount, no grade dependency
        $add('A', null, 'discount', $fee->option_a_discount);

        // Option B — downpayment varies by grade group; monthly components are flat
        $add('B', 'nursery', 'downpayment', $fee->optb_dp_nursery);
        $add('B', 'kindergarten', 'downpayment', $fee->optb_dp_kinder);
        $add('B', 'grade1', 'downpayment', $fee->optb_dp_grade1);
        $add('B', 'grade2', 'downpayment', $fee->optb_dp_grade1);
        $add('B', 'grade3', 'downpayment', $fee->optb_dp_grade3);
        $add('B', 'grade4', 'downpayment', $fee->optb_dp_grade4);
        $add('B', 'grade5', 'downpayment', $fee->optb_dp_grade4);
        $add('B', 'grade6', 'downpayment', $fee->optb_dp_grade4);
        $add('B', null, 'monthly_tuition', $fee->optb_monthly_tuition);
        $add('B', null, 'monthly_electric', $fee->optb_monthly_electric);

        // Option C — elementary only
        $add('C', 'grade1', 'downpayment', $fee->optc_dp_grade1);
        $add('C', 'grade2', 'downpayment', $fee->optc_dp_grade1);
        $add('C', 'grade3', 'downpayment', $fee->optc_dp_grade3);
        $add('C', 'grade4', 'downpayment', $fee->optc_dp_grade4);
        $add('C', 'grade5', 'downpayment', $fee->optc_dp_grade4);
        $add('C', 'grade6', 'downpayment', $fee->optc_dp_grade4);
        $add('C', null, 'monthly_tuition', $fee->optc_monthly_tuition);
        $add('C', null, 'monthly_misc', $fee->optc_monthly_misc);
        $add('C', null, 'monthly_electric', $fee->optc_monthly_electric);

        // Option D — pre-elementary only
        $add('D', 'nursery', 'downpayment', $fee->optd_dp_nursery);
        $add('D', 'kindergarten', 'downpayment', $fee->optd_dp_kinder);
        $add('D', null, 'monthly_tuition', $fee->optd_monthly_tuition);
        $add('D', null, 'monthly_misc', $fee->optd_monthly_misc);
        $add('D', null, 'monthly_electric', $fee->optd_monthly_electric);
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_components');
    }
};
