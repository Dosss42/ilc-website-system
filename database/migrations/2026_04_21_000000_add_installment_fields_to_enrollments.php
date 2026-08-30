<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->integer('installment_schedule')->nullable()->after('payment_type'); // 15 or 30 days
            $table->integer('installment_number')->default(1)->after('installment_schedule'); // Current installment number
            $table->decimal('penalty_amount', 10, 2)->default(0)->after('remaining_balance'); // Penalty for late payments
            $table->integer('late_payment_count')->default(0)->after('penalty_amount'); // Track late payments
            $table->boolean('account_blocked')->default(false)->after('reminder_sent_at'); // Block access after final warning
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'installment_schedule',
                'installment_number',
                'penalty_amount',
                'late_payment_count',
                'account_blocked',
            ]);
        });
    }
};
