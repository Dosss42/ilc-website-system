<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAddress extends Model
{
    protected $fillable = [
        'user_id', 'barangay', 'street_address', 'municipality', 'city', 'province', 'zip_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
