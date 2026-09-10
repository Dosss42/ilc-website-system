<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RECOVERED MIGRATION — same situation as create_sections_table: the
 * `schedules` table exists live but has no row in the `migrations` table.
 * Dated before 2026_04_20_141908_fix_nullable_teacher_id_in_sections_and_
 * schedules (the earliest on-disk migration that ALTERs this table).
 *
 * Matches the live structure exactly, including no foreign keys on
 * section_id / subject_id / teacher_id — that's the real current state.
 *
 * Safe to run against the existing database: guarded with hasTable(), so
 * it is a no-op there. Only matters for `migrate:fresh` / a new environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('schedules')) {
            return;
        }

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->enum('day_of_week', [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
            ]);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
