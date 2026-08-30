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
        Schema::create('examination_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('term'); // 1, 2, or 3
            $table->date('start_date');
            $table->date('end_date');
            $table->string('school_year', 20); // e.g., 2026-2027
            $table->json('grade_levels'); // Array of grade levels affected
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['term', 'school_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_periods');
    }
};
