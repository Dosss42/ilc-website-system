<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // What status the OLD enrollment was in before this promotion —
            // needed so undoPromotion() can restore it exactly, instead of
            // guessing 'enrolled' (the common case, but not the only one).
            $table->string('from_status', 30)->nullable()->after('from_school_year');
            $table->timestamp('undone_at')->nullable()->after('status');
            $table->foreignId('undone_by')->nullable()->after('undone_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('undone_by');
            $table->dropColumn(['from_status', 'undone_at']);
        });
    }
};
