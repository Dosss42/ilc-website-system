<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'enrollment_id',
        'user_id',
        'payment_type',
        'payment_method',
        'amount',
        'reference_number',
        'description',
        'status',
        'installment_month',
        'installment_id',
        'processed_by',
        'processed_at',
        'xendit_invoice_id',
        'xendit_invoice_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installment()
    {
        return $this->belongsTo(PaymentInstallment::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
