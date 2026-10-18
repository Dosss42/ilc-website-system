<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * unique_active_enrollment_per_user_year (see
 * 2026_09_20_202025_fix_unique_enrollment_to_exclude_declined.php) is
 * backed by a generated column that only treats 'declined' rows as not
 * occupying a (user_id, school_year) slot. undoPromotion() introduces a
 * second status with the same "doesn't represent a real claim on that
 * year" meaning — 'cancelled' — so the generated column's CASE expression
 * needs to exclude it too, or re-promoting a student into the same target
 * year after an undo hits a duplicate-key error at the database level
 * (confirmed live while testing Phase 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->indexExists('enrollments', 'unique_active_enrollment_per_user_year')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropUnique('unique_active_enrollment_per_user_year');
            });
        }

        if (Schema::hasColumn('enrollments', 'active_enrollment_user_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('active_enrollment_user_id');
            });
        }

        DB::statement("
            ALTER TABLE enrollments
            ADD COLUMN active_enrollment_user_id BIGINT UNSIGNED
            GENERATED ALWAYS AS (CASE WHEN status IN ('declined', 'cancelled') THEN NULL ELSE user_id END) VIRTUAL
        ");

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['active_enrollment_user_id', 'school_year'], 'unique_active_enrollment_per_user_year');
        });
    }

    public function down(): void
    {
        if ($this->indexExists('enrollments', 'unique_active_enrollment_per_user_year')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropUnique('unique_active_enrollment_per_user_year');
            });
        }

        if (Schema::hasColumn('enrollments', 'active_enrollment_user_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('active_enrollment_user_id');
            });
        }

        DB::statement("
            ALTER TABLE enrollments
            ADD COLUMN active_enrollment_user_id BIGINT UNSIGNED
            GENERATED ALWAYS AS (CASE WHEN status = 'declined' THEN NULL ELSE user_id END) VIRTUAL
        ");

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['active_enrollment_user_id', 'school_year'], 'unique_active_enrollment_per_user_year');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]);
        return count($result) > 0;
    }
};
