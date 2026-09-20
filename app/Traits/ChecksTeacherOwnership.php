<?php

namespace App\Traits;

use App\Models\Schedule;
use App\Models\TeacherAssignment;

/**
 * Shared "does this teacher actually own this section/subject" checks —
 * previously a private copy inside Teacher\DashboardController only,
 * while Api\GradeController::upsertGrades() had no equivalent check at
 * all. Both controllers now use this one implementation instead of
 * risking a second copy silently drifting from this one over time.
 */
trait ChecksTeacherOwnership
{
    private function teacherOwnsSection(int $teacherId, int $sectionId): bool
    {
        // Check active schedules first (primary source for subject teachers)
        $inSchedule = Schedule::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_active', true)
            ->exists();
        if ($inSchedule) return true;

        // Fallback: advisory assignment (class advisers can also manage their section)
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_advisory', true)
            ->exists();
    }

    private function teacherOwnsSubjectInSection(int $teacherId, int $sectionId, ?int $subjectId): bool
    {
        if (!$subjectId) {
            // No subject filter — check if teacher has any schedule for this section
            return Schedule::where('teacher_id', $teacherId)
                ->where('section_id', $sectionId)
                ->where('is_active', true)
                ->exists()
                || TeacherAssignment::where('teacher_id', $teacherId)
                    ->where('section_id', $sectionId)
                    ->where('is_advisory', true)
                    ->exists();
        }

        // Check schedule for this exact subject
        $inSchedule = Schedule::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();
        if ($inSchedule) return true;

        // Advisory teachers can enter general grades for their section
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('is_advisory', true)
            ->exists();
    }
}
