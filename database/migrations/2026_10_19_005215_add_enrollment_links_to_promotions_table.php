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
            // Explicit links to the exact two Enrollment rows this
            // promotion touched — undoPromotion() uses these directly
            // instead of re-deriving "the" old/new enrollment by matching
            // user_id + school_year + grade_level, which stops being
            // unambiguous once a promotion can be undone and re-attempted
            // (two enrollment rows could then share the same year/grade).
            // Nullable: old Promotion rows created before this migration
            // won't have them and simply can't be undone (undoPromotion()
            // checks for this).
            $table->foreignId('enrollment_id')->nullable()->after('to_section_id')
                ->constrained('enrollments')->onDelete('set null');
            $table->foreignId('previous_enrollment_id')->nullable()->after('enrollment_id')
                ->constrained('enrollments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enrollment_id');
            $table->dropConstrainedForeignId('previous_enrollment_id');
        });
    }
};
