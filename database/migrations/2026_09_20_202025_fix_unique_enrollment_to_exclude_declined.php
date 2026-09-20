<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * The original unique(user_id, school_year) constraint has no exception for
 * declined enrollments, contradicting the application's own intent (both
 * store() and walkInEnrollment() explicitly exclude 'declined' rows from
 * their own duplicate check, i.e. a declined applicant is meant to be able
 * to reapply the same year). Since a declined row is never deleted, that
 * reapplication currently fails at the database with a raw unique-violation
 * error. MySQL has no native partial/conditional unique index, so this uses
 * the standard generated-column workaround instead: a stored column that is
 * NULL for declined rows (NULLs never collide in a MySQL unique index) and
 * equal to user_id otherwise, with the unique index moved onto that column.
 *
 * Every step below is guarded with an existence check. MySQL/MariaDB DDL
 * auto-commits per statement (no transactional rollback on ALTER TABLE), so
 * if this migration fails partway through on one environment, an earlier
 * step's change persists even though Laravel never marks the migration as
 * run — the next deploy then retries from the top and fails immediately on
 * the step that already succeeded. Guarding each step makes this migration
 * safe to resume from wherever it previously got stuck.
 */
return new class extends Migration
{
    public function up(): void
    {
        // unique_enrollment_per_user_year is the only index covering user_id
        // as its leading column, so it's also what supports the
        // enrollments_user_id_foreign FK constraint — MySQL won't allow
        // dropping it without a replacement index for that FK to fall back
        // on first.
        if (!$this->indexExists('enrollments', 'idx_enrollments_user_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->index('user_id', 'idx_enrollments_user_id');
            });
        }

        if ($this->indexExists('enrollments', 'unique_enrollment_per_user_year')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropUnique('unique_enrollment_per_user_year');
            });
        }

        if (!Schema::hasColumn('enrollments', 'active_enrollment_user_id')) {
            DB::statement("
                ALTER TABLE enrollments
                ADD COLUMN active_enrollment_user_id BIGINT UNSIGNED
                GENERATED ALWAYS AS (CASE WHEN status = 'declined' THEN NULL ELSE user_id END) STORED
                AFTER user_id
            ");
        }

        if (!$this->indexExists('enrollments', 'unique_active_enrollment_per_user_year')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->unique(['active_enrollment_user_id', 'school_year'], 'unique_active_enrollment_per_user_year');
            });
        }
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

        if (!$this->indexExists('enrollments', 'unique_enrollment_per_user_year')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->unique(['user_id', 'school_year'], 'unique_enrollment_per_user_year');
            });
        }

        if ($this->indexExists('enrollments', 'idx_enrollments_user_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropIndex('idx_enrollments_user_id');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]);
        return count($result) > 0;
    }
};
