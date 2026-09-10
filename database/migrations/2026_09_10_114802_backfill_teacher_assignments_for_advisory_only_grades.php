<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Follow-up to backfill_teacher_assignments_from_schedules — closes a gap
 * that one couldn't see, because it only had schedules to look at.
 *
 * For Nursery/Kindergarten, the single advisory teacher covers every subject
 * for their section — Teacher\DashboardController::index() already knows
 * this and invents these assignments at runtime (see its "Nursery/Kinder:
 * advisory teacher handles ALL subjects" block), because some of those
 * sections have zero real schedules rows to derive anything from. Confirmed
 * against real data: Nursery actually has real schedule rows (one teacher
 * across every subject) and was already fully covered by the previous
 * backfill; Kindergarten has ZERO schedule rows at all and was entirely
 * missed as a result — a real, live gap, not a hypothetical one.
 *
 * This makes those runtime-invented assignments permanent and real, using
 * the exact same rule the controller already applies (Grade::NURSERY_KINDER_LEVELS),
 * so it stays correct if this situation ever applies to another grade level
 * too. Purely additive and idempotent — safe to re-run.
 */
return new class extends Migration
{
    private const NURSERY_KINDER_LEVELS = ['nursery', 'kindergarten'];

    public function up(): void
    {
        $advisoryRows = DB::table('teacher_assignments')
            ->join('sections', 'sections.id', '=', 'teacher_assignments.section_id')
            ->where('teacher_assignments.is_advisory', true)
            ->whereIn('sections.grade_level', self::NURSERY_KINDER_LEVELS)
            ->select(
                'teacher_assignments.teacher_id',
                'teacher_assignments.section_id',
                'teacher_assignments.school_year',
                'sections.grade_level'
            )
            ->get();

        $created = 0;
        foreach ($advisoryRows as $row) {
            $subjectIds = DB::table('subjects')
                ->where('grade_level', $row->grade_level)
                ->where('is_active', true)
                ->pluck('id');

            foreach ($subjectIds as $subjectId) {
                $exists = DB::table('teacher_assignments')
                    ->where('teacher_id', $row->teacher_id)
                    ->where('subject_id', $subjectId)
                    ->where('section_id', $row->section_id)
                    ->where('school_year', $row->school_year)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('teacher_assignments')->insert([
                    'teacher_id' => $row->teacher_id,
                    'subject_id' => $subjectId,
                    'section_id' => $row->section_id,
                    'is_advisory' => false,
                    'school_year' => $row->school_year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }

        \Illuminate\Support\Facades\Log::info("backfill_teacher_assignments_for_advisory_only_grades: created {$created} rows");
    }

    public function down(): void
    {
        // Intentionally a no-op — these rows are indistinguishable from ones
        // the previous backfill (or real future usage) could also create,
        // so there's nothing safe to selectively remove here.
    }
};
