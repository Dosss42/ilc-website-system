<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'google_id',
        'email_verified_at',
        'lrn',
        'blocked',
        'profile_photo',
        'avatar',
        'mfa_enabled',
        'student_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'blocked'           => 'boolean',
        ];
    }

    // ── Role Helpers ──
    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isTeacher(): bool    { return $this->role === 'teacher'; }
    public function isStudent(): bool    { return $this->role === 'student'; }

    /**
     * Send the password reset notification using our own branded Mailable
     * instead of Laravel's default ResetPassword notification, so the email
     * matches the look of every other system email (OTP, enrollment, etc.).
     * Laravel still handles all the security-sensitive plumbing (hashed
     * token generation/expiry/verification) via the Password broker.
     */
    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        \Illuminate\Support\Facades\Mail::to($this->getEmailForPasswordReset())
            ->send(new \App\Mail\ForgotPasswordMail($this, $resetUrl));
    }

    // ── Relationships ──
    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentAddress::class);
    }

    public function guardian(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function guardians(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    public function previousSchool(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PreviousSchool::class);
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function latestEnrollment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }

    public function promotions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Promotion::class, 'student_id');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_student', 'user_id', 'section_id');
    }

    /**
     * This student's actual current section, sourced from section_student —
     * the real relational source of truth (DATABASE_NORMALIZATION_PLAN.md
     * Phase 4). enrollments.section is a denormalized string copy of this
     * same fact, kept in sync by writes in SectionController/EnrollmentController
     * but never guaranteed to agree — this accessor is what read paths
     * should use instead of trusting that column directly.
     */
    public function getCurrentSectionAttribute(): ?Section
    {
        return $this->sections()->where('sections.is_active', true)->first();
    }

    public function grades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    public function teacherAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id');
    }
}
