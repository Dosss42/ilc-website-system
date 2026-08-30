<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->change();
            $table->string('gender', 10)->nullable()->change();
            $table->string('contact', 20)->nullable()->change();
            $table->foreignId('section_id')->nullable()->after('contact')->constrained('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->date('birthdate')->nullable(false)->change();
            $table->enum('gender', ['male', 'female'])->nullable(false)->change();
            $table->string('contact', 20)->nullable(false)->change();
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
