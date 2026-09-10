<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RECOVERED MIGRATION — the `sections` table exists in the live database
 * but has NO corresponding row in the `migrations` table at all (unlike
 * `subjects`, which was recorded but lost its file). It was created
 * directly against the database, outside the migration system, before this
 * table's first referencing migration (2026_04_19_200000_create_section_
 * subject_table). Dated before that so a fresh `migrate:fresh` creates
 * things in the right order.
 *
 * Columns/constraints below match the table's live structure exactly,
 * including the absence of a foreign key on `teacher_id` — that's the real
 * current state, not an oversight introduced here. (Worth fixing later as
 * part of the normalization plan's Phase 3, not silently "corrected" in
 * this recovery migration.)
 *
 * Safe to run against the existing database: guarded with hasTable(), so
 * it is a no-op there. Only matters for `migrate:fresh` / a new environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sections')) {
            return;
        }

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade_level');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('capacity')->nullable()->default(40);
            $table->integer('current_enrollment')->default(0);
            $table->string('school_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
