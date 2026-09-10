<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RECOVERED MIGRATION — this file was missing from source control even
 * though the `migrations` table already recorded it as run (id 10, batch 1).
 * The `subjects` table has existed in the live database since early in the
 * project's history; this file was lost at some point without anyone
 * noticing, because nothing re-runs an already-applied migration.
 *
 * Content below reconstructs the table's *original* shape (grade_level as
 * a nullable integer) so that later on-disk migrations which ALTER it
 * (2026_04_20_140808_change_subjects_grade_level_to_string,
 * 2026_05_09_200000_add_performance_indexes) apply cleanly on a fresh
 * install, exactly as they already do against the live database.
 *
 * Safe to run against the existing database: guarded with hasTable(), so
 * it is a no-op there. Only matters for `migrate:fresh` / a new environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subjects')) {
            return;
        }

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('grade_level')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
