<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromissoryNote extends Model
{
    protected $fillable = [
        'reference_number', 'enrollment_id', 'student_id', 'created_by',
        'amount_overdue', 'amount_promised', 'promise_date', 'date_issued',
        'parent_guardian', 'remarks', 'status', 'extended_date', 'fulfilled_at',
    ];

    protected $casts = [
        'promise_date'  => 'date',
        'date_issued'   => 'date',
        'extended_date' => 'date',
        'fulfilled_at'  => 'datetime',
        'amount_overdue'  => 'decimal:2',
        'amount_promised' => 'decimal:2',
    ];

    public function enrollment()  { return $this->belongsTo(Enrollment::class); }
    public function student()     { return $this->belongsTo(User::class, 'student_id'); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'fulfilled' => 'success',
            'broken'    => 'danger',
            'extended'  => 'warning',
            default     => 'secondary',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && Carbon::today()->gt($this->promise_date);
    }

    public static function generateReference(): string
    {
        $prefix = 'PN-' . date('Ymd') . '-';
        $last = static::where('reference_number', 'like', $prefix . '%')
            ->orderByDesc('id')->value('reference_number');
        $seq = $last ? (intval(substr($last, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
