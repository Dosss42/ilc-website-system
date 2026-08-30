<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'user_id',
        'month_name',
        'due_date',
        'amount',
        'amount_paid',
        'late_fee',
        'status',
        'paid_at',
        'payment_method',
        'reference_number',
        'payment_transaction_id',
        'document_id',
        'weeks_overdue',
        'warning_sent',
        'late_fee_applied',
        'block_notice_sent',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(StudentDocument::class, 'document_id');
    }

    /**
     * Get total amount due (monthly + late fee)
     */
    public function getTotalDueAttribute()
    {
        return $this->amount + $this->late_fee;
    }

    /**
     * Get remaining balance for this installment
     */
    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_due - $this->amount_paid);
    }

    /**
     * Check if installment is fully paid
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->amount_paid >= $this->total_due;
    }

    /**
     * Scope: Overdue installments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereNotIn('status', ['paid', 'pending_approval']);
    }

    /**
     * Scope: Pending installments (not yet due)
     */
    public function scopePending($query)
    {
        return $query->where('due_date', '>=', now())
                     ->where('status', 'pending');
    }

    /**
     * Scope: Paid installments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Pending approval installments (student submitted, awaiting finance approval)
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }
}
