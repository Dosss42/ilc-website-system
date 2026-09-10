<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contract step of DATABASE_NORMALIZATION_PLAN.md Phase 1. Every read path
 * was migrated to a live section_student count (Section::
 * getLiveEnrollmentCountAttribute() / withCount('students')) and every write
 * to this column was removed beforehand — confirmed via a full grep sweep
 * and real HTTP-kernel tests (add/remove/transfer student, admin dashboard,
 * student portal) before this migration was written. Guarded with
 * hasColumn() so it's a safe no-op if already applied.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('sections', 'current_enrollment')) {
            return;
        }
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('current_enrollment');
        });
    }

    /**
     * Reverse the migrations. Recreated as a plain 0-default counter, not
     * backfilled with historical values — it was a denormalized cache to
     * begin with; if ever needed again, resync it from section_student
     * directly (the values this column held before dropping were already
     * confirmed correct via `php artisan db:verify-normalization` first).
     */
    public function down(): void
    {
        if (Schema::hasColumn('sections', 'current_enrollment')) {
            return;
        }
        Schema::table('sections', function (Blueprint $table) {
            $table->integer('current_enrollment')->default(0)->after('room_number');
        });
    }
};
