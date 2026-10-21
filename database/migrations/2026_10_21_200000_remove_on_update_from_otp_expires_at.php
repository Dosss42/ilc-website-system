<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * otp_verifications.expires_at was created as `$table->timestamp('expires_at')`
 * (NOT NULL, first TIMESTAMP column). On MariaDB/MySQL with
 * explicit_defaults_for_timestamp=OFF that silently becomes
 * `DEFAULT current_timestamp() ON UPDATE current_timestamp()` — so recording a
 * wrong attempt (`$otp->increment('attempts')`) reset expires_at to "now" and the
 * code expired instantly: one typo killed the OTP, in both the password-change
 * (Student/Teacher) and online-enrollment email verification flows.
 *
 * Redefines the column as a plain nullable TIMESTAMP with no ON UPDATE rule.
 * Existing rows keep their expires_at values.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return; // SQLite etc. never add the implicit ON UPDATE rule
        }

        DB::statement('ALTER TABLE otp_verifications MODIFY expires_at TIMESTAMP NULL DEFAULT NULL');
    }

    public function down(): void
    {
        // Intentionally no-op: restoring the implicit ON UPDATE rule would bring the bug back.
    }
};
