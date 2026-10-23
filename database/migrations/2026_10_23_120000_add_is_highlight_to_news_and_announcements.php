<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets Admin / Super Admin pick news articles and announcements to feature in
 * the home page's "Highlights" column (next to the fixed school head,
 * enrollment and academics cards).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_highlight')->default(false)->after('is_active');
        });
        Schema::table('announcements', function (Blueprint $table) {
            $table->boolean('is_highlight')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('is_highlight');
        });
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('is_highlight');
        });
    }
};
