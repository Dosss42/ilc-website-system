<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RECOVERED MIGRATION — this file was missing from source control even
 * though the `migrations` table already recorded it as run (id 57, batch
 * 39). These columns power the Assessment & Promotion module (admin
 * assessment decision + teacher advisory recommendation, per
 * ASSESSMENT_PROMOTION_ACTION_PLAN.md) and have existed live since then;
 * the file was lost without anyone noticing.
 *
 * Safe to run against the existing database: guarded with hasColumn(), so
 * it is a no-op there. Only matters for `migrate:fresh` / a new environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'assessment_status')) {
                $table->string('assessment_status', 30)->default('pending')->after('status');
            }
            if (!Schema::hasColumn('enrollments', 'assessment_notes')) {
                $table->text('assessment_notes')->nullable()->after('assessment_status');
            }
            if (!Schema::hasColumn('enrollments', 'assessed_by')) {
                $table->foreignId('assessed_by')->nullable()->after('assessment_notes')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('enrollments', 'assessed_at')) {
                $table->timestamp('assessed_at')->nullable()->after('assessed_by');
            }
            if (!Schema::hasColumn('enrollments', 'teacher_recommendation')) {
                $table->string('teacher_recommendation', 30)->nullable()->after('assessed_at');
            }
            if (!Schema::hasColumn('enrollments', 'teacher_recommendation_notes')) {
                $table->text('teacher_recommendation_notes')->nullable()->after('teacher_recommendation');
            }
            if (!Schema::hasColumn('enrollments', 'teacher_recommended_by')) {
                $table->foreignId('teacher_recommended_by')->nullable()->after('teacher_recommendation_notes')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('enrollments', 'teacher_recommended_at')) {
                $table->timestamp('teacher_recommended_at')->nullable()->after('teacher_recommended_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'teacher_recommended_by')) {
                $table->dropForeign(['teacher_recommended_by']);
            }
            if (Schema::hasColumn('enrollments', 'assessed_by')) {
                $table->dropForeign(['assessed_by']);
            }
            $table->dropColumn(array_filter([
                'assessment_status',
                'assessment_notes',
                'assessed_by',
                'assessed_at',
                'teacher_recommendation',
                'teacher_recommendation_notes',
                'teacher_recommended_by',
                'teacher_recommended_at',
            ], fn ($col) => Schema::hasColumn('enrollments', $col)));
        });
    }
};
