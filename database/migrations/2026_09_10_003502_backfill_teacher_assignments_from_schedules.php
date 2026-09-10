<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Expand step of DATABASE_NORMALIZATION_PLAN.md Phase 3, subject-teaching
 * half. teacher_assignments currently only has advisory rows (subject_id is
 * NULL on every existing row) — real "teacher X teaches subject Y in
 * section Z" facts live only in schedules.teacher_id, which is NOT
 * represented in teacher_assignments at all. Teacher\DashboardController
 * even says so directly in a comment: "Subjects and sections are derived
 * from active schedules (not TeacherAssignment)".
 *
 * This creates one teacher_assignments row (is_advisory=false) for every
 * distinct (teacher, subject, section, school_year) combination found in
 * real schedule rows — additive only, doesn't touch schedules or drop
 * anything. school_year is resolved via the section (schedules itself has
 * no school_year column), falling back to the section's own value since
 * that's the authoritative year a section belongs to.
 *
 * Purely additive and idempotent — safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('schedules')
            ->join('sections', 'sections.id', '=', 'schedules.section_id')
            ->whereNotNull('schedules.teacher_id')
            ->whereNotNull('schedules.subject_id')
            ->select('schedules.teacher_id', 'schedules.subject_id', 'schedules.section_id', 'sections.school_year')
            ->distinct()
            ->get();

        $created = 0;
        foreach ($rows as $row) {
            $exists = DB::table('teacher_assignments')
                ->where('teacher_id', $row->teacher_id)
                ->where('subject_id', $row->subject_id)
                ->where('section_id', $row->section_id)
                ->where('school_year', $row->school_year)
                ->exists();

            if ($exists) {
                continue;
            }

            // Also guard against the unique index's other shape: an advisory
            // row for this exact (teacher, section, school_year) with a NULL
            // subject_id is a different fact and must stay separate — no
            // conflict here since we always insert a real subject_id.
            DB::table('teacher_assignments')->insert([
                'teacher_id' => $row->teacher_id,
                'subject_id' => $row->subject_id,
                'section_id' => $row->section_id,
                'is_advisory' => false,
                'school_year' => $row->school_year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $created++;
        }

        \Illuminate\Support\Facades\Log::info("backfill_teacher_assignments_from_schedules: created {$created} rows");
    }

    public function down(): void
    {
        // Only remove the rows this migration could have created (non-advisory,
        // i.e. real subject-teaching rows) — never touches advisory rows.
        DB::table('teacher_assignments')->where('is_advisory', false)->delete();
    }
};
