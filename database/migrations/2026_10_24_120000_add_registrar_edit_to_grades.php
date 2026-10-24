<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The registrar can correct a submitted grade in Grade Oversight. These keep the teacher's
 * original value and who changed it, so the teacher's grade table can show
 * "Edited by registrar: 70 → 78" (the change is also written to the System Logs).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->string('original_grade', 10)->nullable()->after('descriptive_grade');
            $table->foreignId('edited_by')->nullable()->after('original_grade')->constrained('users')->nullOnDelete();
            $table->timestamp('edited_at')->nullable()->after('edited_by');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('edited_by');
            $table->dropColumn(['original_grade', 'edited_at']);
        });
    }
};
