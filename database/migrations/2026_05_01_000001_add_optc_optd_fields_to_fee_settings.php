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
        Schema::table('fee_settings', function (Blueprint $table) {
            // Option C - Additional fields
            if (!Schema::hasColumn('fee_settings', 'optc_dp_nursery')) {
                $table->decimal('optc_dp_nursery', 10, 2)->default(5400);
            }
            if (!Schema::hasColumn('fee_settings', 'optc_dp_kinder')) {
                $table->decimal('optc_dp_kinder', 10, 2)->default(5400);
            }
            
            // Option D - Additional fields
            if (!Schema::hasColumn('fee_settings', 'optd_dp_grade1')) {
                $table->decimal('optd_dp_grade1', 10, 2)->default(4800);
            }
            if (!Schema::hasColumn('fee_settings', 'optd_dp_grade3')) {
                $table->decimal('optd_dp_grade3', 10, 2)->default(5100);
            }
            if (!Schema::hasColumn('fee_settings', 'optd_dp_grade4')) {
                $table->decimal('optd_dp_grade4', 10, 2)->default(5600);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->dropColumn(['optc_dp_nursery', 'optc_dp_kinder', 'optd_dp_grade1', 'optd_dp_grade3', 'optd_dp_grade4']);
        });
    }
};
