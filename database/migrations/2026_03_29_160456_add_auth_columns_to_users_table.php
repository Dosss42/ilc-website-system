<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ════════════════════════════════════════════════════════
// MIGRATION: add_auth_columns_to_users_table
//
// Place this file in: database/migrations/
// Rename with date prefix:
// 2026_03_30_000001_add_auth_columns_to_users_table.php
// ════════════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // ── Role: who this user is ──
            // Only added if column doesn't exist yet
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['superadmin', 'admin', 'teacher', 'student'])
                      ->default('student')
                      ->after('email');
            }

            // ── Google OAuth ID ──
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('role');
            }

            // ── Avatar URL (from Google) ──
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }

            // ── MFA toggle ──
            if (!Schema::hasColumn('users', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('avatar');
            }

            // ── Account active/inactive toggle ──
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('mfa_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'google_id',
                'avatar',
                'mfa_enabled',
                'is_active',
            ]);
        });
    }
};