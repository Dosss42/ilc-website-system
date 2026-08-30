<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentTeacherConference extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'guardian_name',
        'meeting_date',
        'meeting_time',
        'purpose',
        'venue',
        'notes',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
