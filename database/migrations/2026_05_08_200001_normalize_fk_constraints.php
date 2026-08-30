<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Clean orphan references before adding FK constraints ──────────

        // grades.subject_id — null out rows where subject was hard-deleted
        DB::statement('UPDATE grades SET subject_id = NULL
            WHERE subject_id IS NOT NULL
              AND subject_id NOT IN (SELECT id FROM subjects)');

        // grades.enrollment_id — null out rows where enrollment no longer exists
        DB::statement('UPDATE grades SET enrollment_id = NULL
            WHERE enrollment_id IS NOT NULL
              AND enrollment_id NOT IN (SELECT id FROM enrollments)');

        // student_documents.reviewed_by — null out rows where reviewer no longer exists
        DB::statement('UPDATE student_documents SET reviewed_by = NULL
            WHERE reviewed_by IS NOT NULL
              AND reviewed_by NOT IN (SELECT id FROM users)');

        // enrollments.approved_by / declined_by — null out deleted approvers
        DB::statement('UPDATE enrollments SET approved_by = NULL
            WHERE approved_by IS NOT NULL
              AND approved_by NOT IN (SELECT id FROM users)');

        DB::statement('UPDATE enrollments SET declined_by = NULL
            WHERE declined_by IS NOT NULL
              AND declined_by NOT IN (SELECT id FROM users)');

        // ── 2. Add missing FK constraints ────────────────────────────────────

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('approved_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('declined_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        Schema::table('student_documents', function (Blueprint $table) {
            $table->foreign('reviewed_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('subject_id')
                ->references('id')->on('subjects')
                ->nullOnDelete();

            $table->foreign('enrollment_id')
                ->references('id')->on('enrollments')
                ->nullOnDelete();
        });

        // ── 3. Drop dormant student_profiles.section_id ──────────────────────
        // Column exists in DB but is NOT in the model's $fillable and
        // is never read/written by any controller — purely orphaned.

        if (Schema::hasColumn('student_profiles', 'section_id')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropForeign(['subject_id']);
        });

        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['declined_by']);
            $table->dropForeign(['approved_by']);
        });

        // Restore the dormant column
        if (!Schema::hasColumn('student_profiles', 'section_id')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->foreignId('section_id')
                    ->nullable()
                    ->after('contact')
                    ->constrained('sections')
                    ->nullOnDelete();
            });
        }
    }
};
