<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * `lrn` is a transitive dependency, copied from `users.lrn` at the moment a
 * promotion record is created (see EnrollmentController's promotion logic).
 * Real-world drift risk is low — a DepEd LRN is assigned once and normally
 * never changes — but unlike the app's other deliberate denormalizations
 * (see PaymentTransaction, PaymentInstallment, PromissoryNote, Grade) this
 * one wasn't previously documented or guarded. Treat it the same way: if
 * `php artisan db:verify-normalization` ever reports a mismatch here, that's
 * a real bug in whatever wrote the stale lrn — not a reason to remove the
 * column, since a promotion record should still show the LRN as it was at
 * promotion time even if it were ever corrected afterward.
 */
class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lrn',
        'from_grade',
        'to_grade',
        'from_school_year',
        'to_school_year',
        'from_section_id',
        'to_section_id',
        'promoted_by',
        'promoted_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function promotedBy()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    public function fromSection()
    {
        return $this->belongsTo(Section::class, 'from_section_id');
    }

    public function toSection()
    {
        return $this->belongsTo(Section::class, 'to_section_id');
    }
}
