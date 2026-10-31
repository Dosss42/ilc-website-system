<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\PreviousSchool;
use App\Models\Setting;
use App\Models\StudentAddress;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Demonstration students: 50 realistic (invented) students — 40 new, 10 transferees — each with an
 * Approved enrollment for the current school year, no payment plan and no transactions, exactly like
 * an online application the registrar has approved. They log in with one shared password.
 *
 * Emails are plus-addresses on ONE Gmail the school owns (school+juan.delacruz@gmail.com), so every
 * email the system might send lands in that inbox, never with a stranger. Nothing is emailed here.
 *
 *   php artisan demo:students --gmail=school@gmail.com           create (existing ones are skipped)
 *   php artisan demo:students --remove                           delete them and everything they have
 *
 * The demo students are recognised by student_data.demo_account = true on their enrollment.
 */
#[Signature('demo:students {--gmail= : The Gmail that receives every demo student\'s email (plus-addresses)} {--password=Demo@2026 : Shared password} {--remove : Delete the demo students instead} {--force : Skip the confirmation}')]
#[Description('Create (or remove) 50 demonstration students: 40 new + 10 transferees, Approved, no payment plan')]
class DemoStudents extends Command
{
    // first, middle, last, gender, grade level, type ('new' | 'transferee')
    private const STUDENTS = [
        // ── New (40) ──
        ['Althea', 'Mae', 'Dela Cruz', 'female', 'nursery', 'new'],
        ['Juan Miguel', 'Santos', 'Reyes', 'male', 'nursery', 'new'],
        ['Princess', 'Joy', 'Bautista', 'female', 'nursery', 'new'],
        ['Carl', 'Andrei', 'Mendoza', 'male', 'nursery', 'new'],
        ['Ysabel', 'Grace', 'Ramos', 'female', 'nursery', 'new'],
        ['Liam', 'Gabriel', 'Aquino', 'male', 'nursery', 'new'],
        ['Kristine', 'Faith', 'Villanueva', 'female', 'nursery', 'new'],
        ['Ethan', 'James', 'Castillo', 'male', 'nursery', 'new'],
        ['Sophia', 'Nicole', 'Garcia', 'female', 'kindergarten', 'new'],
        ['Mark', 'Joseph', 'Torres', 'male', 'kindergarten', 'new'],
        ['Angel', 'Rose', 'Fernandez', 'female', 'kindergarten', 'new'],
        ['Rafael', 'Luis', 'Navarro', 'male', 'kindergarten', 'new'],
        ['Chloe', 'Marie', 'Pascual', 'female', 'kindergarten', 'new'],
        ['Nathan', 'Paul', 'Salazar', 'male', 'kindergarten', 'new'],
        ['Bianca', 'Louise', 'Domingo', 'female', 'kindergarten', 'new'],
        ['Joshua', 'Daniel', 'Manalo', 'male', 'kindergarten', 'new'],
        ['Andrea', 'Kate', 'Soriano', 'female', 'grade1', 'new'],
        ['Kenneth', 'Ray', 'Gonzales', 'male', 'grade1', 'new'],
        ['Jasmine', 'Claire', 'Lopez', 'female', 'grade1', 'new'],
        ['Vincent', 'Allen', 'Cruz', 'male', 'grade1', 'new'],
        ['Hannah', 'Mae', 'Agustin', 'female', 'grade1', 'new'],
        ['Adrian', 'Kyle', 'Valdez', 'male', 'grade1', 'new'],
        ['Samantha', 'Jade', 'Rivera', 'female', 'grade2', 'new'],
        ['Christian', 'Jay', 'Morales', 'male', 'grade2', 'new'],
        ['Erica', 'Joy', 'Santiago', 'female', 'grade2', 'new'],
        ['John Paul', 'Lim', 'Panganiban', 'male', 'grade2', 'new'],
        ['Patricia', 'Anne', 'Ocampo', 'female', 'grade3', 'new'],
        ['Gian', 'Carlo', 'Dizon', 'male', 'grade3', 'new'],
        ['Mikaela', 'Rose', 'Galang', 'female', 'grade3', 'new'],
        ['Aaron', 'Jake', 'Tolentino', 'male', 'grade3', 'new'],
        ['Rhea', 'Mae', 'Mercado', 'female', 'grade4', 'new'],
        ['Jerome', 'Neil', 'Cabrera', 'male', 'grade4', 'new'],
        ['Clarisse', 'Ann', 'Ignacio', 'female', 'grade4', 'new'],
        ['Paolo', 'Miguel', 'Samson', 'male', 'grade5', 'new'],
        ['Denise', 'Faith', 'Marquez', 'female', 'grade5', 'new'],
        ['Rodel', 'Jun', 'Pineda', 'male', 'grade5', 'new'],
        ['Shaira', 'Lyn', 'Enriquez', 'female', 'grade6', 'new'],
        ['Kevin', 'Mark', 'Bernardo', 'male', 'grade6', 'new'],
        ['Trisha', 'Mae', 'Lacson', 'female', 'grade6', 'new'],
        ['Jericho', 'Dave', 'Natividad', 'male', 'grade6', 'new'],
        // ── Transferees (10) ──
        ['Mariel', 'Joy', 'Esguerra', 'female', 'grade1', 'transferee'],
        ['Dominic', 'Ace', 'Sarmiento', 'male', 'grade1', 'transferee'],
        ['Lovely', 'Grace', 'Alcantara', 'female', 'grade2', 'transferee'],
        ['Ronald', 'James', 'Mallari', 'male', 'grade2', 'transferee'],
        ['Kimberly', 'Anne', 'De Guzman', 'female', 'grade3', 'transferee'],
        ['Jonathan', 'Mark', 'Velasco', 'male', 'grade3', 'transferee'],
        ['Precious', 'Mae', 'Concepcion', 'female', 'grade4', 'transferee'],
        ['Francis', 'Lloyd', 'Bañez', 'male', 'grade4', 'transferee'],
        ['Nicole', 'Ann', 'Quiambao', 'female', 'grade5', 'transferee'],
        ['Emmanuel', 'John', 'Robles', 'male', 'grade6', 'transferee'],
    ];

    private const FATHERS = ['Roberto', 'Eduardo', 'Ricardo', 'Manuel', 'Fernando', 'Antonio', 'Danilo', 'Rogelio', 'Ernesto', 'Romeo', 'Arnel', 'Jonathan', 'Rodrigo', 'Alfredo', 'Noel'];
    private const MOTHERS = ['Maricel', 'Rowena', 'Jocelyn', 'Marites', 'Lorna', 'Analyn', 'Rosalie', 'Cristina', 'Evelyn', 'Glenda', 'Sheila', 'Mylene', 'Lea', 'Imelda', 'Josephine'];
    private const OCCUPATIONS = ['Teacher', 'Farmer', 'Vendor', 'Driver', 'Nurse', 'Office Clerk', 'Housewife', 'OFW', 'Engineer', 'Store Owner', 'Barangay Staff', 'Mechanic'];
    // Barangays of General Tinio, Nueva Ecija
    private const BARANGAYS = ['Poblacion East', 'Poblacion West', 'Poblacion Central', 'Concepcion', 'Nazareth', 'Padolina', 'Pias', 'Pulong Matong', 'Rio Chico', 'Sampaguita', 'San Pedro', 'Bago'];
    private const STREETS = ['Rizal St.', 'Mabini St.', 'Bonifacio St.', 'Luna St.', 'Del Pilar St.', 'Burgos St.', 'Aguinaldo St.', 'Quezon Ave.'];
    private const PREVIOUS_SCHOOLS = ['General Tinio Central School', 'Gapan East Central School', 'Peñaranda Central School', 'Papaya Elementary School', 'San Isidro Central School'];
    // Age at the start of the school year for each grade
    private const AGE = ['nursery' => 4, 'kindergarten' => 5, 'grade1' => 6, 'grade2' => 7, 'grade3' => 8, 'grade4' => 9, 'grade5' => 10, 'grade6' => 11];
    private const GRADE_BEFORE = ['grade1' => 'kindergarten', 'grade2' => 'grade1', 'grade3' => 'grade2', 'grade4' => 'grade3', 'grade5' => 'grade4', 'grade6' => 'grade5'];

    public function handle(): int
    {
        if ($this->option('remove')) {
            return $this->remove();
        }
        $gmail = strtolower(trim((string) $this->option('gmail')));
        if (!preg_match('/^[a-z0-9.]+@gmail\.com$/', $gmail)) {
            $this->error('Give the Gmail that should receive the demo students\' email, e.g. --gmail=school@gmail.com');
            return self::FAILURE;
        }

        return $this->create($gmail);
    }

    private function create(string $gmail): int
    {
        $password = (string) $this->option('password');
        $schoolYear = Setting::getCurrentSchoolYear();
        $approver = User::whereIn('role', ['admin', 'superadmin'])->orderBy('id')->value('id');
        [$local] = explode('@', $gmail);

        if (!$this->option('force') && !$this->confirm('Create ' . count(self::STUDENTS) . " demo students (Approved, S.Y. {$schoolYear}) with emails {$local}+firstname.lastname@gmail.com?", true)) {
            return self::SUCCESS;
        }

        $created = $skipped = 0;
        $rows = [];
        foreach (self::STUDENTS as $i => [$first, $middle, $last, $gender, $grade, $type]) {
            $email = $local . '+' . Str::slug($first . '.' . $last, '.') . '@gmail.com';
            if (User::withTrashed()->where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($i, $first, $middle, $last, $gender, $grade, $type, $email, $password, $schoolYear, $approver, &$rows) {
                $startYear  = (int) substr($schoolYear, 0, 4);
                $birthdate  = sprintf('%d-%02d-%02d', $startYear - self::AGE[$grade] - 1, ($i % 12) + 1, ($i * 7 % 27) + 1);
                $father     = self::FATHERS[$i % count(self::FATHERS)] . ' ' . $last;
                $mother     = self::MOTHERS[($i * 3) % count(self::MOTHERS)] . ' ' . $last;
                $guardianIsMother = $i % 3 !== 0;
                $phone      = '9000000' . str_pad((string) ($i + 101), 3, '0', STR_PAD_LEFT); // 0900-000-0xxx: an obviously fake number, stored without the 0
                $barangay   = self::BARANGAYS[$i % count(self::BARANGAYS)];
                $street     = (($i * 5) % 90 + 1) . ' ' . self::STREETS[$i % count(self::STREETS)];
                $lastSchool = $type === 'transferee' ? self::PREVIOUS_SCHOOLS[$i % count(self::PREVIOUS_SCHOOLS)] : '';
                $lrn        = $this->uniqueLrn();

                $user = User::create([
                    'name'              => $first . ' ' . $last,
                    'email'             => $email,
                    'password'          => bcrypt($password),
                    'role'              => 'student',
                    'is_active'         => true,
                    'lrn'               => $lrn,
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();

                StudentProfile::create([
                    'user_id' => $user->id, 'first_name' => $first, 'middle_name' => $middle, 'last_name' => $last,
                    'birthdate' => $birthdate, 'gender' => $gender, 'contact' => $phone,
                ]);
                StudentAddress::create([
                    'user_id' => $user->id, 'street_address' => $street, 'barangay' => $barangay,
                    'municipality' => 'General Tinio', 'city' => 'General Tinio', 'province' => 'Nueva Ecija', 'zip_code' => '3104',
                ]);
                Guardian::create([
                    'user_id' => $user->id, 'name' => $guardianIsMother ? $mother : $father,
                    'relationship' => $guardianIsMother ? 'mother' : 'father', 'contact' => $phone, 'email' => $email,
                    'occupation' => self::OCCUPATIONS[$i % count(self::OCCUPATIONS)],
                ]);
                if ($type === 'transferee') {
                    PreviousSchool::create([
                        'user_id' => $user->id, 'school_name' => $lastSchool, 'last_grade_completed' => self::GRADE_BEFORE[$grade],
                    ]);
                }

                // Same shape as an approved online application (no payment option yet)
                Enrollment::create([
                    'user_id'          => $user->id,
                    'reference_number' => 'ENR-' . strtoupper(Str::random(8)),
                    'status'           => 'approved',
                    'grade_level'      => $grade,
                    'school_year'      => $schoolYear,
                    'approved_at'      => now(),
                    'approved_by'      => $approver,
                    'payment_status'   => 'pending',
                    'payment_type'     => 'full',
                    'total_fee'        => 0,
                    'remaining_balance' => 0,
                    'payment_amount'   => 0,
                    'student_data'     => [
                        'first_name' => $first, 'middle_name' => $middle, 'last_name' => $last, 'suffix' => '',
                        'gender' => $gender, 'birthdate' => $birthdate, 'place_of_birth' => 'General Tinio, Nueva Ecija',
                        'grade_level' => $grade, 'student_type' => $type, 'last_school' => $lastSchool,
                        'mother_name' => $mother, 'mother_age' => 30 + $i % 12, 'father_name' => $father, 'father_age' => 32 + $i % 12,
                        'religious_affiliation' => $i % 4 === 0 ? 'Roman Catholic' : 'IEMELIF',
                        'region' => 'Region III (Central Luzon)', 'province' => 'Nueva Ecija', 'city' => 'General Tinio',
                        'barangay' => $barangay, 'street_address' => $street, 'zip_code' => '3104',
                        'guardian_name' => $guardianIsMother ? $mother : $father, 'relationship' => $guardianIsMother ? 'mother' : 'father',
                        'guardian_occupation' => self::OCCUPATIONS[$i % count(self::OCCUPATIONS)], 'guardian_phone' => $phone,
                        'guardian_email' => $email, 'student_email' => $email, 'lrn' => $lrn,
                        'blood_type' => '', 'allergies' => '', 'medical_conditions' => '',
                        'demo_account' => true,
                    ],
                ]);

                $rows[] = [$first . ' ' . $last, $grade, $type, $email];
            });
            $created++;
        }

        if ($rows) {
            $this->table(['Name', 'Grade', 'Type', 'Email (login)'], $rows);
        }
        $this->info("Created {$created} demo student(s)" . ($skipped ? ", skipped {$skipped} that already exist" : '') . '.');
        $this->line("Status: Approved (S.Y. {$schoolYear}) · no payment plan · no transactions · password: {$password}");
        $this->line('Remove them later with:  php artisan demo:students --remove');

        return self::SUCCESS;
    }

    private function remove(): int
    {
        // A text match on the marker works the same on MySQL, MariaDB and SQLite
        $ids = Enrollment::where('student_data', 'like', '%"demo_account":true%')->pluck('user_id')->filter()->unique();
        if ($ids->isEmpty()) {
            $this->info('No demo students found.');
            return self::SUCCESS;
        }
        if (!$this->option('force') && !$this->confirm("Delete {$ids->count()} demo students and everything they have (enrollments, documents, payments, grades)? This cannot be undone.", false)) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($ids) {
            // Not deleted by the database itself when the user goes
            Enrollment::whereIn('user_id', $ids)->delete();
            DB::table('student_documents')->whereIn('user_id', $ids)->delete();
            // Profiles, address, guardian, previous school, grades, payments, installments, sections… cascade
            User::withTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
        });
        $this->info("Deleted {$ids->count()} demo student(s).");

        return self::SUCCESS;
    }

    // 12-digit LRN, school ID 419002 + "26" + 4 digits, not used by anyone yet
    private function uniqueLrn(): string
    {
        do {
            $lrn = '41900226' . str_pad((string) random_int(5000, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::withTrashed()->where('lrn', $lrn)->exists());

        return $lrn;
    }
}
