<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummerClass extends Model
{
    protected $fillable = [
        'school_year', 'subject_id', 'teacher_id', 'section_id',
        'grade_level', 'room', 'schedule_description',
        'start_date', 'end_date', 'status', 'max_slots', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'max_slots' => 'integer',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function enrollments()
    {
        return $this->hasMany(SummerClassEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'summer_class_enrollments', 'summer_class_id', 'student_id')
            ->withPivot('original_grade', 'summer_grade', 'remarks', 'status')
            ->withTimestamps();
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_slots - $this->enrolled_count);
    }
}
