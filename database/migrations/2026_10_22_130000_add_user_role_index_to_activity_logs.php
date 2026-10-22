<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The Admin Audit Trail filters by role (and always leaves out Super Admin entries)
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_role');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_role']);
        });
    }
};
