<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'content',
        'audience',
        'section_id',
        'category',
        'is_active',
        'is_highlight',
        'image',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_highlight' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
