<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // payment_installments.user_id is a copy of enrollments.user_id kept for
        // fast filtering (see db:verify-normalization, "user_id/student_id vs
        // enrollments.user_id"). The old approve() logic looked the student up
        // by the email on the application form and, when it did not match the
        // linked account, created a second account and moved the enrollment to
        // it — leaving that enrollment's installments on the old account
        // (45 rows across 5 enrollments on 2026-10-05). Re-point every
        // installment to its enrollment's current owner. Only rows that differ
        // are touched, so this is a no-op on a database without the problem.
        DB::table('payment_installments')
            ->whereExists(function ($q) {
                $q->from('enrollments as e')
                    ->whereColumn('e.id', 'payment_installments.enrollment_id')
                    ->whereNotNull('e.user_id')
                    ->whereColumn('e.user_id', '<>', 'payment_installments.user_id');
            })
            ->update([
                'user_id' => DB::raw('(SELECT e.user_id FROM enrollments e WHERE e.id = payment_installments.enrollment_id)'),
            ]);
    }

    public function down(): void
    {
        // Not reversible — the previous values pointed at the wrong account
    }
};
