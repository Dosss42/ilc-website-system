<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove pivot rows for soft-deleted users
        DB::table('section_student')
            ->join('users', 'users.id', '=', 'section_student.user_id')
            ->whereNotNull('users.deleted_at')
            ->delete();

        // Recalculate current_enrollment for every section from live data
        $sectionIds = DB::table('sections')->pluck('id');
        foreach ($sectionIds as $sectionId) {
            $liveCount = DB::table('section_student')
                ->join('users', 'users.id', '=', 'section_student.user_id')
                ->whereNull('users.deleted_at')
                ->where('section_student.section_id', $sectionId)
                ->count();

            DB::table('sections')
                ->where('id', $sectionId)
                ->update(['current_enrollment' => $liveCount]);
        }
    }

    public function down(): void
    {
        // Not reversible — data was stale/orphaned
    }
};
