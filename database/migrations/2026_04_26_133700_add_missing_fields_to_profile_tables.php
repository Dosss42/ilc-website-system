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
        // Add missing fields to student_profiles
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('suffix', 10)->nullable()->after('middle_name');
            $table->string('place_of_birth', 150)->nullable()->after('birthdate');
            $table->string('nationality', 100)->nullable()->after('place_of_birth');
            $table->string('religious_affiliation', 100)->nullable()->after('nationality');
            $table->string('mother_name', 150)->nullable()->after('religious_affiliation');
            $table->integer('mother_age')->nullable()->after('mother_name');
            $table->string('father_name', 150)->nullable()->after('mother_age');
            $table->integer('father_age')->nullable()->after('father_name');
            $table->string('blood_type', 10)->nullable()->after('father_age');
            $table->string('allergies', 255)->nullable()->after('blood_type');
            $table->string('medical_conditions', 255)->nullable()->after('allergies');
            $table->string('student_email', 255)->nullable()->after('medical_conditions');
            $table->string('student_type', 50)->nullable()->after('student_email');
            $table->string('last_school', 255)->nullable()->after('student_type');
        });

        // Add missing fields to student_addresses
        Schema::table('student_addresses', function (Blueprint $table) {
            $table->string('city', 150)->nullable()->after('municipality');
            $table->string('street_address', 255)->nullable()->after('barangay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'suffix', 'place_of_birth', 'nationality', 'religious_affiliation',
                'mother_name', 'mother_age', 'father_name', 'father_age',
                'blood_type', 'allergies', 'medical_conditions',
                'student_email', 'student_type', 'last_school'
            ]);
        });

        Schema::table('student_addresses', function (Blueprint $table) {
            $table->dropColumn(['city', 'street_address']);
        });
    }
};
