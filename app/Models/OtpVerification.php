<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = ['email', 'code', 'token', 'attempts', 'verified', 'expires_at'];

    protected $casts = [
        'verified'   => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= 3;
    }
}
