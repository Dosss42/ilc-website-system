<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add google_id to users (for Google Sign In) ──
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id');
        });

        // ── Student Profiles ──
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->date('birthdate');
            $table->enum('gender', ['male', 'female']);
            $table->string('contact', 20);
            $table->timestamps();
        });

        // ── Student Addresses ──
        Schema::create('student_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('barangay', 150);
            $table->string('municipality', 150);
            $table->string('province', 150);
            $table->string('zip_code', 10)->nullable();
            $table->timestamps();
        });

        // ── Guardians ──
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('relationship', 50); // Mother, Father, Guardian, etc.
            $table->string('contact', 20);
            $table->string('email', 255)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->timestamps();
        });

        // ── Previous Schools ──
        Schema::create('previous_schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('school_name', 255);
            $table->string('school_address', 255)->nullable();
            $table->string('last_grade_completed', 50); // e.g. Grade 6
            $table->year('school_year_graduated')->nullable();
            $table->string('general_average', 10)->nullable();
            $table->timestamps();
        });

        // ── Enrollments (grade & section per school year) ──
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('grade_level', ['7', '8', '9', '10']);
            $table->string('section', 100)->nullable();
            $table->string('school_year', 20); // e.g. 2025-2026
            $table->enum('status', ['pending', 'enrolled', 'dropped', 'completed'])
                  ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
        });
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('previous_schools');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('student_addresses');
        Schema::dropIfExists('student_profiles');
    }
};
