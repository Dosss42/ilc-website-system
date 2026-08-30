<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables confirmed unused:
     *   grade_import_drafts             – no model, no code reference
     *   otp_codes                       – model exists but zero controllers use it; otp_verifications is the real OTP table
     *   sessions                        – SESSION_DRIVER=file, never written to
     *   cache                           – CACHE_STORE=file, never written to
     *   cache_locks                     – same as cache
     *   job_batches                     – Laravel batch jobs feature not used anywhere
     *   password_reset_tokens           – no password-reset flow; system uses OTP instead
     *   examination_periods             – routes/controller exist but never wired into any view
     *   examination_period_grade_levels – same as above (child table)
     */
    public function up(): void
    {
        $tables = [
            'grade_import_drafts',
            'otp_codes',
            'sessions',
            'cache',
            'cache_locks',
            'job_batches',
            'password_reset_tokens',
            'examination_period_grade_levels', // child first (FK)
            'examination_periods',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Recreate the Laravel-standard tables on rollback.
     * Project-specific tables (grade_import_drafts, otp_codes, examination_*)
     * are not recreated — they were dead code.
     */
    public function down(): void
    {
        Schema::create('sessions', function ($t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });

        Schema::create('cache', function ($t) {
            $t->string('key')->primary();
            $t->mediumText('value');
            $t->integer('expiration');
        });

        Schema::create('cache_locks', function ($t) {
            $t->string('key')->primary();
            $t->string('owner');
            $t->integer('expiration');
        });

        Schema::create('job_batches', function ($t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->integer('total_jobs');
            $t->integer('pending_jobs');
            $t->integer('failed_jobs');
            $t->longText('failed_job_ids');
            $t->mediumText('options')->nullable();
            $t->integer('cancelled_at')->nullable();
            $t->integer('created_at');
            $t->integer('finished_at')->nullable();
        });

        Schema::create('password_reset_tokens', function ($t) {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });
    }
};
