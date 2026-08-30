<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'user_id', 'name', 'relationship',
        'contact', 'email', 'occupation', 'age',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
