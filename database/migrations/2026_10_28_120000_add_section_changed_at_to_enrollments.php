<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A student may change their own section once per school year, before classes start
 * (StudentPortalController::changeSection). This records when they used that one change;
 * the Admin can still move a student any time.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('section_changed_at')->nullable()->after('section');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('section_changed_at');
        });
    }
};
