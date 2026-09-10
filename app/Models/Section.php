<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
        'teacher_id',
        'room_number',
        'max_students',
        'school_year',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'section_student', 'section_id', 'user_id');
    }

    /**
     * Live enrollment count, derived from section_student — the actual
     * source of truth. The stored `current_enrollment` column is a
     * denormalized cache that has drifted out of sync in production before
     * (see the cleanup_stale_section_student_rows / cleanup_pending_
     * section_student_rows migrations); this accessor is Phase 1 of
     * DATABASE_NORMALIZATION_PLAN.md's fix — read paths use this instead of
     * trusting the column. Prefers an eager-loaded withCount('students')
     * result when present, to avoid an extra query per section in list views.
     */
    public function getLiveEnrollmentCountAttribute(): int
    {
        if (array_key_exists('students_count', $this->attributes)) {
            return (int) $this->attributes['students_count'];
        }
        return $this->students()->count();
    }

    /**
     * The advisory (homeroom) teacher for this section, sourced from
     * teacher_assignments — the real source of truth for advisory
     * relationships (DATABASE_NORMALIZATION_PLAN.md Phase 3). `teacher_id`
     * on this table is kept as a synced cache column (TeacherAssignmentController
     * writes it whenever an advisory assignment changes) for any raw-SQL
     * queries that still filter/sort by it, but reads should use this instead.
     */
    public function getAdvisoryTeacherAttribute(): ?User
    {
        return TeacherAssignment::where('section_id', $this->id)
            ->where('is_advisory', true)
            ->with('teacher')
            ->first()?->teacher;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'section_subject');
    }
}
