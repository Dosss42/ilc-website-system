<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * `user_id` is a deliberate transitive denormalization — it's already
 * reachable via enrollment_id -> enrollments.user_id, but stored directly
 * here anyway so payment queries (which run often, and often need the
 * student without caring about the enrollment) don't need an extra join.
 *
 * This is DATABASE_NORMALIZATION_PLAN.md Phase 5's Option B: the schema
 * stays as-is on purpose — do NOT "fix" this into a join instead. What
 * closes the actual risk (this drifting from the enrollment's real owner)
 * is `php artisan db:verify-normalization`, which checks it automatically.
 * If that check ever reports a mismatch here, that's a real bug to fix in
 * whatever wrote the wrong user_id — not a reason to remove the column.
 */
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
        'reject_reason',
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
