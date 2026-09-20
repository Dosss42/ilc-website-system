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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index('user_id', 'idx_enrollments_user_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('unique_enrollment_per_user_year');
        });

        DB::statement("
            ALTER TABLE enrollments
            ADD COLUMN active_enrollment_user_id BIGINT UNSIGNED
            GENERATED ALWAYS AS (CASE WHEN status = 'declined' THEN NULL ELSE user_id END) STORED
            AFTER user_id
        ");

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['active_enrollment_user_id', 'school_year'], 'unique_active_enrollment_per_user_year');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('unique_active_enrollment_per_user_year');
            $table->dropColumn('active_enrollment_user_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['user_id', 'school_year'], 'unique_enrollment_per_user_year');
            $table->dropIndex('idx_enrollments_user_id');
        });
    }
};
