<?php
// ═══════════════════════════════════════════════
// Copy each class into its own file:
//   app/Models/StudentProfile.php
//   app/Models/StudentAddress.php
//   app/Models/Guardian.php
//   app/Models/PreviousSchool.php
//   app/Models/Enrollment.php
// ═══════════════════════════════════════════════

// ── app/Models/StudentProfile.php ──────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name',
        'middle_name', 'suffix', 'birthdate', 'gender', 'contact',
        'place_of_birth', 'nationality', 'religious_affiliation',
        'blood_type', 'allergies', 'medical_conditions',
        'student_email', 'student_type', 'last_school', 'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
