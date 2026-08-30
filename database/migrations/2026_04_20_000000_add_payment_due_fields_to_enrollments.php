<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('payment_type', 20)->default('full')->after('payment_status');
            // full = one-time payment, installment = multiple payments
            $table->date('payment_due_date')->nullable()->after('payment_type');
            $table->decimal('total_fee', 10, 2)->default(0)->after('payment_due_date');
            $table->decimal('remaining_balance', 10, 2)->default(0)->after('total_fee');
            $table->date('next_installment_date')->nullable()->after('remaining_balance');
            $table->boolean('reminder_sent')->default(false)->after('next_installment_date');
            $table->timestamp('reminder_sent_at')->nullable()->after('reminder_sent');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'payment_due_date',
                'total_fee',
                'remaining_balance',
                'next_installment_date',
                'reminder_sent',
                'reminder_sent_at',
            ]);
        });
    }
};
