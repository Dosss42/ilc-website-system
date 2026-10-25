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

        // Otherwise any assignment to this section: advisory (class advisers manage their section) or a subject
        // assignment made in Teacher Assignments before the timetable exists — the same links My Students lists,
        // so a class shown there always opens.
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
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

        // Assigned this subject in this section (Teacher Assignments), or the section's adviser
        return TeacherAssignment::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where(fn ($q) => $q->where('subject_id', $subjectId)->orWhere('is_advisory', true))
            ->exists();
    }
}
