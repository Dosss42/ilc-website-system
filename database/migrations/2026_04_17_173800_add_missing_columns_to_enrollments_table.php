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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('enrollments', 'reference_number')) {
                $table->string('reference_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('enrollments', 'status')) {
                $table->string('status')->default('pending')->after('reference_number');
            }
            if (!Schema::hasColumn('enrollments', 'student_data')) {
                $table->json('student_data')->after('status');
            }
            if (!Schema::hasColumn('enrollments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('student_data');
            }
            if (!Schema::hasColumn('enrollments', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('enrollments', 'declined_at')) {
                $table->timestamp('declined_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('enrollments', 'declined_by')) {
                $table->unsignedBigInteger('declined_by')->nullable()->after('declined_at');
            }
            if (!Schema::hasColumn('enrollments', 'decline_reason')) {
                $table->text('decline_reason')->nullable()->after('declined_by');
            }
            if (!Schema::hasColumn('enrollments', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('decline_reason');
            }
            if (!Schema::hasColumn('enrollments', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('enrollments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_amount');
            }
            if (!Schema::hasColumn('enrollments', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('enrollments', 'payment_updated_at')) {
                $table->timestamp('payment_updated_at')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('enrollments', 'enrolled_at')) {
                $table->timestamp('enrolled_at')->nullable()->after('payment_updated_at');
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
                'reference_number',
                'status', 
                'student_data',
                'approved_at',
                'approved_by',
                'declined_at',
                'declined_by',
                'decline_reason',
                'payment_status',
                'payment_amount',
                'payment_method',
                'payment_reference',
                'payment_updated_at',
                'enrolled_at'
            ]);
        });
    }
};
