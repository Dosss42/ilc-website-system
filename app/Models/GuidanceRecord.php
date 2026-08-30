<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'date',
        'concern_type',
        'concern_description',
        'action_taken',
        'recommendations',
        'follow_up_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
