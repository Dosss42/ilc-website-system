<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    // Nursery/Kinder use descriptive ratings stored in descriptive_grade.
    // Grade 1-6 use the numeric grade column.
    const DESCRIPTIVE_GRADES = ['O', 'VS', 'S', 'FS', 'DNME'];
    const NURSERY_KINDER_LEVELS = ['nursery', 'kindergarten'];

    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'enrollment_id',
        'term',
        'grade',
        'descriptive_grade',
        'school_year',
        'status',
        'remarks',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public static function getRemarks($grade)
    {
        if ($grade === null || $grade === '') return '';
        if ($grade >= 75) return 'Passed';
        if ($grade >= 70) return 'Passed with Remedial';
        return 'Failed';
    }

    public static function getDescriptiveLabel(string $code): string
    {
        return match($code) {
            'O'    => 'Outstanding',
            'VS'   => 'Very Satisfactory',
            'S'    => 'Satisfactory',
            'FS'   => 'Fairly Satisfactory',
            'DNME' => 'Did Not Meet Expectations',
            default => $code,
        };
    }

    public static function isNurseryKinder(string $gradeLevel): bool
    {
        return in_array($gradeLevel, self::NURSERY_KINDER_LEVELS);
    }
}
