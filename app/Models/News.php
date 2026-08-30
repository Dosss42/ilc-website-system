<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['posted_by', 'title', 'body', 'image', 'category', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function author()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }
}
