<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummerClassEnrollment extends Model
{
    protected $fillable = [
        'summer_class_id', 'student_id',
        'original_grade', 'summer_grade', 'remarks', 'status',
    ];

    protected function casts(): array
    {
        return [
            'original_grade' => 'decimal:2',
            'summer_grade' => 'decimal:2',
        ];
    }

    public function summerClass()
    {
        return $this->belongsTo(SummerClass::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
