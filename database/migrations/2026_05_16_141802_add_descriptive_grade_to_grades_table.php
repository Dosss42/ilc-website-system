<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Nursery/Kinder use descriptive ratings: O, VS, S, FS, DNME
            // Grade 1-6 use the existing numeric `grade` column
            $table->string('descriptive_grade', 10)->nullable()->after('grade');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('descriptive_grade');
        });
    }
};
