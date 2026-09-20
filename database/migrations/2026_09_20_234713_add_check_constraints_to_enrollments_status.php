<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * status/payment_status validity previously relied entirely on application
 * code — a raw query, seeder, or typo'd literal could silently persist an
 * invalid value with nothing at the DB level to reject it. This database
 * runs MariaDB 10.4.32, which genuinely enforces CHECK constraints (added
 * in MariaDB 10.2.1), so this is a safe, real backstop, not a no-op.
 *
 * Value sets derived from every validation rule / literal assignment found
 * across the codebase (not just what's currently in the table), so this
 * doesn't reject any state the application can legitimately produce.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE enrollments
            ADD CONSTRAINT chk_enrollments_status
            CHECK (status IN ('pending','approved','enrolled','declined','completed','dropped','ghost','transferred'))
        ");

        DB::statement("
            ALTER TABLE enrollments
            ADD CONSTRAINT chk_enrollments_payment_status
            CHECK (payment_status IN ('pending','paid','partial'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE enrollments DROP CONSTRAINT chk_enrollments_status');
        DB::statement('ALTER TABLE enrollments DROP CONSTRAINT chk_enrollments_payment_status');
    }
};
