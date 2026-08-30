<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summer_classes', function (Blueprint $table) {
            $table->id();
            $table->string('school_year', 20);
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('grade_level', 30);
            $table->string('room')->nullable();
            $table->string('schedule_description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->integer('max_slots')->default(40);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('summer_class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('summer_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('original_grade', 5, 2)->nullable();
            $table->decimal('summer_grade', 5, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->enum('status', ['enrolled', 'passed', 'failed', 'dropped'])->default('enrolled');
            $table->timestamps();

            $table->unique(['summer_class_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summer_class_enrollments');
        Schema::dropIfExists('summer_classes');
    }
};
