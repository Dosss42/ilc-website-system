<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_year',
        'reference_number',
        'status', // pending, approved, declined, enrolled
        'student_data', // JSON object with all student information
        'grade_level',
        'approved_at',
        'approved_by',
        'declined_at',
        'declined_by',
        'decline_reason',
        'decline_storage',
        'payment_status', // pending, paid, partial
        'payment_type', // full, installment
        'payment_option', // A, B, C, D
        'payment_amount',
        'payment_method', // gcash, cash — maya/bank_transfer are locked, not accepted
        'payment_reference',
        'payment_updated_at',
        'payment_due_date',
        'total_fee',
        'remaining_balance',
        'downpayment_amount',
        'monthly_amount',
        'payment_breakdown', // JSON with detailed payment breakdown
        'next_installment_date',
        'reminder_sent',
        'reminder_sent_at',
        'enrolled_at',
        'section',
        'section_changed_at', // the student's one self-service section change this school year
        'installment_schedule',
        'installment_number',
        'penalty_amount',
        'late_payment_count',
        'account_blocked',
        'assessment_status',
        'assessment_notes',
        'assessed_by',
        'assessed_at',
        'teacher_recommendation',
        'teacher_recommendation_notes',
        'teacher_recommended_by',
        'teacher_recommended_at',
    ];

    protected $casts = [
        'student_data' => 'array',
        'payment_breakdown' => 'array',
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
        'payment_updated_at' => 'datetime',
        'payment_due_date' => 'date',
        'total_fee' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'next_installment_date' => 'date',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'enrolled_at' => 'datetime',
        'section_changed_at' => 'datetime',
        'assessed_at' => 'datetime',
        'teacher_recommended_at' => 'datetime',
        'installment_schedule' => 'integer',
        'installment_number' => 'integer',
        'penalty_amount' => 'decimal:2',
        'late_payment_count' => 'integer',
        'account_blocked' => 'boolean',
    ];

    /** First day of classes of this enrollment's school year: the School Year Start setting's month/day (default June 1). */
    public function classesStartDate(): \Carbon\Carbon
    {
        preg_match('/^(\d{4})/', (string) $this->school_year, $m);
        $start = \Carbon\Carbon::parse(Setting::get('school_year_start', '') ?: '2000-06-01');
        return \Carbon\Carbon::create((int) ($m[1] ?? now()->year), $start->month, $start->day)->startOfDay();
    }

    /**
     * Why the student can't change their own section right now, or null when they can.
     * Rule (school decision, October 28, 2026): once per school year, before classes start.
     * After that the school office (Admin) moves the student.
     */
    public function sectionChangeBlockReason(): ?string
    {
        if ($this->status !== 'enrolled') {
            return 'You can change your section once your enrollment is confirmed.';
        }
        if ($this->section_changed_at) {
            return 'You already changed your section once this school year (' . $this->section_changed_at->format('F j, Y')
                . '). For another change, please ask the school office.';
        }
        $start = $this->classesStartDate();
        if (now()->gte($start)) {
            return 'Section changes closed when classes started on ' . $start->format('F j, Y') . '. For a change, please ask the school office.';
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function declinedBy()
    {
        return $this->belongsTo(User::class, 'declined_by');
    }

    public function paymentInstallments()
    {
        return $this->hasMany(PaymentInstallment::class)->orderBy('due_date');
    }

    public function promissoryNotes()
    {
        return $this->hasMany(PromissoryNote::class)->orderByDesc('created_at');
    }

    public function getFullNameAttribute()
    {
        $data = $this->student_data ?? [];
        $middle = !empty($data['middle_name']) ? $data['middle_name'] . ' ' : '';
        $suffix = !empty($data['suffix']) ? ' ' . $data['suffix'] : '';
        return $middle . ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') . $suffix;
    }

    public function getGradeLevelDisplayAttribute()
    {
        $grade = $this->student_data['grade_level'] ?? '';
        $gradeLevels = [
            'pre-elementary' => 'Pre-Elementary',
            'kindergarten' => 'Kindergarten',
            'grade1' => 'Grade 1',
            'grade2' => 'Grade 2',
            'grade3' => 'Grade 3',
            'grade4' => 'Grade 4',
            'grade5' => 'Grade 5',
            'grade6' => 'Grade 6',
        ];
        
        return $gradeLevels[$grade] ?? $grade;
    }

    public function getStudentTypeDisplayAttribute()
    {
        $type = $this->student_data['student_type'] ?? '';
        $types = [
            'new' => 'New Student',
            'transferee' => 'Transferee',
            'returning' => 'Returning Student',
        ];
        
        return $types[$type] ?? $type;
    }
}
