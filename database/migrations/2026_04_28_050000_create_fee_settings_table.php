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
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();

            // Base fee components
            $table->decimal('tuition', 10, 2)->default(7505);
            $table->decimal('misc', 10, 2)->default(2800);
            $table->decimal('insurance', 10, 2)->default(150);
            $table->decimal('electric', 10, 2)->default(2000);

            // Books fees by grade level
            $table->decimal('books_nursery', 10, 2)->default(3550);
            $table->decimal('books_grade1', 10, 2)->default(4550);
            $table->decimal('books_grade3', 10, 2)->default(5050);
            $table->decimal('books_grade4', 10, 2)->default(5550);

            // Option A - Cash discount
            $table->decimal('option_a_discount', 10, 2)->default(1501);

            // Option B - Monthly Payment (all levels)
            $table->decimal('optb_monthly_tuition', 10, 2)->default(833.89);
            $table->decimal('optb_monthly_electric', 10, 2)->default(222.22);
            $table->decimal('optb_dp_nursery', 10, 2)->default(6500);
            $table->decimal('optb_dp_kinder', 10, 2)->default(6500);
            $table->decimal('optb_dp_grade1', 10, 2)->default(7500);
            $table->decimal('optb_dp_grade3', 10, 2)->default(8000);
            $table->decimal('optb_dp_grade4', 10, 2)->default(8500);

            // Option C - Elem. Pupils Only Monthly
            $table->decimal('optc_monthly_tuition', 10, 2)->default(833.89);
            $table->decimal('optc_monthly_misc', 10, 2)->default(311.11);
            $table->decimal('optc_monthly_electric', 10, 2)->default(222.22);
            $table->decimal('optc_dp_grade1', 10, 2)->default(5500);
            $table->decimal('optc_dp_grade3', 10, 2)->default(6000);
            $table->decimal('optc_dp_grade4', 10, 2)->default(6500);

            // Option D - Pre-Elem Pupils Only Monthly
            $table->decimal('optd_monthly_tuition', 10, 2)->default(833.89);
            $table->decimal('optd_monthly_misc', 10, 2)->default(311.11);
            $table->decimal('optd_monthly_electric', 10, 2)->default(222.22);
            $table->decimal('optd_dp_nursery', 10, 2)->default(4505);
            $table->decimal('optd_dp_kinder', 10, 2)->default(4505);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_settings');
    }
};
