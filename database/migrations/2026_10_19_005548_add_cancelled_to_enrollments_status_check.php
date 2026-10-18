<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'cancelled' to the chk_enrollments_status CHECK constraint (see
 * 2026_*_add_status_check_constraints_to_enrollments.php) — a new value
 * needed by undoPromotion() for the enrollment a reversed promotion
 * created. Must drop and re-add the whole constraint; MariaDB has no
 * ALTER CONSTRAINT to add a single allowed value in place.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE enrollments DROP CONSTRAINT chk_enrollments_status');

        DB::statement("
            ALTER TABLE enrollments
            ADD CONSTRAINT chk_enrollments_status
            CHECK (status IN ('pending','approved','enrolled','declined','completed','dropped','ghost','transferred','cancelled'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE enrollments DROP CONSTRAINT chk_enrollments_status');

        DB::statement("
            ALTER TABLE enrollments
            ADD CONSTRAINT chk_enrollments_status
            CHECK (status IN ('pending','approved','enrolled','declined','completed','dropped','ghost','transferred'))
        ");
    }
};
