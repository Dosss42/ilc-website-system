<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousSchool extends Model
{
    protected $fillable = [
        'user_id', 'school_name', 'school_address',
        'last_grade_completed', 'school_year_graduated', 'general_average',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
