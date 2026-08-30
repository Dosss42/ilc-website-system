<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add payment option fields
            if (!Schema::hasColumn('enrollments', 'payment_option')) {
                $table->string('payment_option')->nullable()->after('payment_type'); // A, B, C, D
            }
            if (!Schema::hasColumn('enrollments', 'downpayment_amount')) {
                $table->decimal('downpayment_amount', 10, 2)->default(0)->after('payment_option');
            }
            if (!Schema::hasColumn('enrollments', 'monthly_amount')) {
                $table->decimal('monthly_amount', 10, 2)->default(0)->after('downpayment_amount');
            }
            if (!Schema::hasColumn('enrollments', 'payment_breakdown')) {
                $table->json('payment_breakdown')->nullable()->after('monthly_amount'); // Store detailed breakdown
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_option',
                'downpayment_amount',
                'monthly_amount',
                'payment_breakdown'
            ]);
        });
    }
};
