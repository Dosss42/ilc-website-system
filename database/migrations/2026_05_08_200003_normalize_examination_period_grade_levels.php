<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1NF fix: examination_periods.grade_levels was a JSON array column.
        // Replace with a proper junction table.

        Schema::dropIfExists('examination_period_grade_levels');
        Schema::create('examination_period_grade_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_period_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('grade_level', 50);
            $table->timestamps();

            $table->unique(['examination_period_id', 'grade_level'], 'epgl_period_level_unique');
        });

        // Migrate existing JSON data into the new junction table
        DB::table('examination_periods')
            ->select('id', 'grade_levels')
            ->get()
            ->each(function ($period) {
                $levels = json_decode($period->grade_levels ?? '[]', true) ?? [];
                foreach ($levels as $level) {
                    DB::table('examination_period_grade_levels')->insertOrIgnore([
                        'examination_period_id' => $period->id,
                        'grade_level'           => $level,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }
            });

        // Drop the now-redundant JSON column (guard in case it was already dropped)
        if (Schema::hasColumn('examination_periods', 'grade_levels')) {
            Schema::table('examination_periods', function (Blueprint $table) {
                $table->dropColumn('grade_levels');
            });
        }
    }

    public function down(): void
    {
        // Restore the JSON column and repopulate from the junction table
        Schema::table('examination_periods', function (Blueprint $table) {
            $table->json('grade_levels')->nullable()->after('school_year');
        });

        DB::table('examination_periods')->get()->each(function ($period) {
            $levels = DB::table('examination_period_grade_levels')
                ->where('examination_period_id', $period->id)
                ->pluck('grade_level')
                ->toArray();

            DB::table('examination_periods')
                ->where('id', $period->id)
                ->update(['grade_levels' => json_encode($levels)]);
        });

        Schema::dropIfExists('examination_period_grade_levels');
    }
};
