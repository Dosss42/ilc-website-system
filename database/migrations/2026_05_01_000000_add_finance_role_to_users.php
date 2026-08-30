<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing role column to include 'finance'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'finance', 'teacher', 'student') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'teacher', 'student') DEFAULT 'student'");
    }
};
