<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','finance','cashier','teacher','student') DEFAULT 'student'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','finance','teacher','student') DEFAULT 'student'");
    }
};
