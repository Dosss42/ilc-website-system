<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RECOVERED MIGRATION — this file was missing from source control even
 * though the `migrations` table already recorded it as run (id 56, batch
 * 38). `users.student_status` has existed in the live database since then;
 * the file was lost without anyone noticing, because nothing re-runs an
 * already-applied migration.
 *
 * Safe to run against the existing database: guarded with hasColumn(), so
 * it is a no-op there. Only matters for `migrate:fresh` / a new environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_status')) {
                $table->string('student_status', 30)->default('active')->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_status')) {
                $table->dropColumn('student_status');
            }
        });
    }
};
