<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'enrollment_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'description',
        'status',
        'reject_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function paymentInstallment()
    {
        return $this->hasOne(PaymentInstallment::class, 'document_id');
    }

    public function getDocumentTypeDisplayAttribute()
    {
        $types = [
            'birth_certificate'  => 'Birth Certificate',
            'form_137'           => 'Form 137 (Permanent Record)',
            'report_card'        => 'Grades / Report Card',
            'two_by_two_picture' => '2x2 ID Picture',
            'payment_screenshot' => 'Payment Screenshot',
            'others'             => 'Other Documents',
            // legacy keys kept for old records
            'form_138'           => 'Grades / Report Card',
            'good_moral'         => 'Certificate of Good Moral Character',
            'barangay_clearance' => 'Barangay Clearance',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return '<span class="badge bg-success">Approved</span>';
            case 'rejected':
                return '<span class="badge bg-danger">Rejected</span>';
            default:
                return '<span class="badge bg-warning">Pending</span>';
        }
    }

    public function getFileSizeDisplayAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
