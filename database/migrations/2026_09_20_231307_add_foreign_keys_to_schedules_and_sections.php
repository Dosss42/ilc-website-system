<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * schedules.section_id / subject_id / teacher_id and sections.teacher_id
 * have never had foreign key constraints (both create-table migrations
 * self-document this as the real live state, not an oversight). Deleting a
 * Section, Subject, or teacher User leaves orphaned rows behind — real
 * enough that a dedicated /admin/schedule-cleanup screen already exists
 * just to find and remove them. Confirmed zero orphaned rows exist right
 * now, so these constraints can be added cleanly with no backfill needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // NOT NULL columns — a schedule with no section/subject is
            // meaningless, so removing either cascades.
            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            // Nullable — an unassigned-teacher schedule is already a valid
            // state, so losing the teacher clears the reference instead of
            // destroying the schedule.
            $table->foreign('teacher_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['teacher_id']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });
    }
};
