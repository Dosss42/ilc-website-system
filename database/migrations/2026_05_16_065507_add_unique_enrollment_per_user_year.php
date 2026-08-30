<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove any duplicate enrollments first (keep the latest per user+school_year)
        DB::statement("
            DELETE e1 FROM enrollments e1
            INNER JOIN enrollments e2
            ON e1.user_id = e2.user_id
            AND e1.school_year = e2.school_year
            AND e1.id < e2.id
        ");

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['user_id', 'school_year'], 'unique_enrollment_per_user_year');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('unique_enrollment_per_user_year');
        });
    }
};
