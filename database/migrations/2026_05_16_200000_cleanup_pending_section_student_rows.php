<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove section_student rows where the student has no 'enrolled' enrollment
        // for the school year of that section. These rows were created prematurely
        // (before payment) by the old approve() logic for walk-in enrollments.
        $stale = DB::table('section_student as ss')
            ->join('sections as s', 's.id', '=', 'ss.section_id')
            ->whereNotExists(function ($q) {
                $q->from('enrollments as e')
                    ->whereColumn('e.user_id', 'ss.user_id')
                    ->where('e.status', 'enrolled')
                    ->whereColumn('e.school_year', 's.school_year');
            })
            ->pluck('ss.user_id', 'ss.section_id');

        // Delete stale rows
        DB::table('section_student as ss')
            ->join('sections as s', 's.id', '=', 'ss.section_id')
            ->whereNotExists(function ($q) {
                $q->from('enrollments as e')
                    ->whereColumn('e.user_id', 'ss.user_id')
                    ->where('e.status', 'enrolled')
                    ->whereColumn('e.school_year', 's.school_year');
            })
            ->delete();

        // Recalculate current_enrollment for every section
        $sectionIds = DB::table('sections')->pluck('id');
        foreach ($sectionIds as $sectionId) {
            $liveCount = DB::table('section_student')
                ->where('section_id', $sectionId)
                ->count();
            DB::table('sections')
                ->where('id', $sectionId)
                ->update(['current_enrollment' => $liveCount]);
        }
    }

    public function down(): void
    {
        // Not reversible — stale rows were invalid data
    }
};
