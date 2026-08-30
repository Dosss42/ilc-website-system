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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable();
            $table->foreignId('enrollment_id')->nullable();
            $table->integer('quarter'); // 1, 2, 3, 4
            $table->decimal('written_works', 5, 2)->default(0); // 25%
            $table->decimal('performance_task', 5, 2)->default(0); // 50%
            $table->decimal('assessment', 5, 2)->default(0); // 25%
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('remarks')->default('Passed');
            $table->timestamps();
            
            $table->index(['student_id', 'subject_id', 'quarter']);
            $table->index(['teacher_id', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
