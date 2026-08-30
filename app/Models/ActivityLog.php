<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'user_role',
        'event_type', 'description',
        'subject_type', 'subject_id',
        'ip_address', 'extra',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
