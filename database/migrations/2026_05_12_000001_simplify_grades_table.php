<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Add new simplified columns
            $table->decimal('grade', 5, 2)->nullable()->after('enrollment_id');
            $table->string('school_year', 20)->nullable()->after('grade');
            $table->string('status', 20)->default('submitted')->after('school_year'); // draft or submitted
            $table->integer('term')->nullable()->after('status'); // 1, 2, 3
        });

        // Migrate existing final_grade data to new grade column
        DB::statement('UPDATE grades SET grade = final_grade WHERE final_grade IS NOT NULL');
        // Migrate quarter to term
        DB::statement('UPDATE grades SET term = quarter WHERE quarter IS NOT NULL AND quarter <= 3');

        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['written_works', 'performance_task', 'assessment', 'final_grade', 'quarter']);
        });

        // Update index
        Schema::table('grades', function (Blueprint $table) {
            $table->index(['student_id', 'subject_id', 'term', 'school_year'], 'grades_student_subject_term_sy');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->decimal('written_works', 5, 2)->default(0);
            $table->decimal('performance_task', 5, 2)->default(0);
            $table->decimal('assessment', 5, 2)->default(0);
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->integer('quarter')->nullable();
        });

        DB::statement('UPDATE grades SET final_grade = grade WHERE grade IS NOT NULL');
        DB::statement('UPDATE grades SET quarter = term WHERE term IS NOT NULL');

        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('grades_student_subject_term_sy');
            $table->dropColumn(['grade', 'school_year', 'status', 'term']);
        });
    }
};
