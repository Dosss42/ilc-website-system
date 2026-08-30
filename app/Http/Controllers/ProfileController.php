<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\PreviousSchool;
use App\Models\StudentAddress;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // ══════════════════════════════════════════
    // SHOW PROFILE COMPLETION PAGE
    // ══════════════════════════════════════════
    public function showCompleteProfile()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $address = $user->address;
        $guardian = $user->guardian;
        $previousSchool = $user->previousSchool;

        // Load mother and father from the normalized guardians table
        $allGuardians = $user->guardians;
        $mother = $allGuardians->where('relationship', 'Mother')->first();
        $father = $allGuardians->where('relationship', 'Father')->first();

        // Find enrollment: first by user_id, then by email in student_data
        $enrollment = $user->latestEnrollment;
        if (!$enrollment) {
            $enrollment = Enrollment::where('student_data->student_email', $user->email)
                ->latest()
                ->first();
        }

        $studentData = $enrollment ? ($enrollment->student_data ?? []) : [];

        return view('profile.complete', compact(
            'user', 'profile', 'address', 'guardian', 'mother', 'father',
            'previousSchool', 'enrollment', 'studentData'
        ));
    }

    // ══════════════════════════════════════════
    // UPDATE PERSONAL INFORMATION
    // ══════════════════════════════════════════
    public function updatePersonal(Request $request)
    {
        $request->validate([
            'first_name'           => 'required|string|max:100',
            'last_name'            => 'required|string|max:100',
            'middle_name'          => 'nullable|string|max:100',
            'suffix'               => 'nullable|string|max:10',
            'birthdate'            => 'required|date',
            'gender'               => 'required|in:male,female',
            'place_of_birth'       => 'nullable|string|max:150',
            'nationality'          => 'nullable|string|max:100',
            'religious_affiliation'=> 'nullable|string|max:100',
            'mother_name'          => 'nullable|string|max:150',
            'mother_age'           => 'nullable|integer|min:1|max:120',
            'father_name'          => 'nullable|string|max:150',
            'father_age'           => 'nullable|integer|min:1|max:120',
            'contact'              => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        // Update user name
        $user->update([
            'name' => trim("{$request->first_name} {$request->last_name}"),
        ]);

        // Update core profile fields (mother/father now live in guardians table)
        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'middle_name'           => $request->middle_name,
                'suffix'                => $request->suffix,
                'birthdate'             => $request->birthdate,
                'gender'                => $request->gender,
                'place_of_birth'        => $request->place_of_birth,
                'nationality'           => $request->nationality,
                'religious_affiliation' => $request->religious_affiliation,
                'contact'               => $request->contact,
            ]
        );

        // Save mother / father into the normalized guardians table
        if ($request->filled('mother_name')) {
            Guardian::updateOrCreate(
                ['user_id' => $user->id, 'relationship' => 'Mother'],
                ['name' => $request->mother_name, 'age' => $request->mother_age]
            );
        }

        if ($request->filled('father_name')) {
            Guardian::updateOrCreate(
                ['user_id' => $user->id, 'relationship' => 'Father'],
                ['name' => $request->father_name, 'age' => $request->father_age]
            );
        }

        return back()->with('success', 'Personal information updated successfully!');
    }

    // ══════════════════════════════════════════
    // UPDATE HEALTH INFORMATION
    // ══════════════════════════════════════════
    public function updateHealth(Request $request)
    {
        $request->validate([
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'blood_type' => $request->blood_type,
                'allergies' => $request->allergies,
                'medical_conditions' => $request->medical_conditions,
            ]
        );

        return back()->with('success', 'Health information updated successfully!');
    }

    // ══════════════════════════════════════════
    // UPDATE ADDRESS INFORMATION
    // ══════════════════════════════════════════
    public function updateAddress(Request $request)
    {
        $request->validate([
            'province' => 'required|string|max:150',
            'city' => 'required|string|max:150',
            'barangay' => 'required|string|max:150',
            'street_address' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ]);

        $user = Auth::user();
        
        StudentAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'province' => $request->province,
                'city' => $request->city,
                'municipality' => $request->city,
                'barangay' => $request->barangay,
                'street_address' => $request->street_address,
                'zip_code' => $request->zip_code,
            ]
        );

        return back()->with('success', 'Address information updated successfully!');
    }

    // ══════════════════════════════════════════
    // UPDATE GUARDIAN INFORMATION
    // ══════════════════════════════════════════
    public function updateGuardian(Request $request)
    {
        $request->validate([
            'guardian_name' => 'required|string|max:150',
            'relationship' => 'required|string|max:50',
            'guardian_occupation' => 'nullable|string|max:100',
            'guardian_phone' => 'required|string|max:20',
            'student_email' => 'nullable|email|max:255',
        ]);

        $user = Auth::user();
        
        Guardian::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->guardian_name,
                'relationship' => $request->relationship,
                'contact' => $request->guardian_phone,
                'email' => $request->student_email,
                'occupation' => $request->guardian_occupation,
            ]
        );

        // Also save student_email to profile
        if ($request->filled('student_email')) {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['student_email' => $request->student_email]
            );
        }

        return back()->with('success', 'Guardian information updated successfully!');
    }

    // ══════════════════════════════════════════
    // UPDATE PREVIOUS SCHOOL INFORMATION
    // ══════════════════════════════════════════
    public function updatePreviousSchool(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:255',
            'last_grade_completed' => 'required|string|max:50',
            'school_year_graduated' => 'nullable|digits:4',
            'general_average' => 'nullable|string|max:10',
        ]);

        $user = Auth::user();
        
        PreviousSchool::updateOrCreate(
            ['user_id' => $user->id],
            [
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'last_grade_completed' => $request->last_grade_completed,
                'school_year_graduated' => $request->school_year_graduated,
                'general_average' => $request->general_average,
            ]
        );

        return back()->with('success', 'Previous school information updated successfully!');
    }

    // ══════════════════════════════════════════
    // UPDATE ENROLLMENT INFORMATION
    // ══════════════════════════════════════════
    public function updateEnrollment(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|in:nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6',
            'student_type' => 'required|in:new,transferee,returning',
            'last_school' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        $currentYear = now()->year;
        $schoolYear = now()->month >= 6
            ? "{$currentYear}-" . ($currentYear + 1)
            : ($currentYear - 1) . "-{$currentYear}";

        Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'school_year' => $schoolYear],
            [
                'grade_level' => $request->grade_level,
                'status' => 'pending',
            ]
        );

        // Also save student_type and last_school to profile
        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_type' => $request->student_type,
                'last_school' => $request->last_school,
            ]
        );

        return back()->with('success', 'Enrollment information updated successfully!');
    }

    // ══════════════════════════════════════════
    // CHECK PROFILE COMPLETION STATUS
    // ══════════════════════════════════════════
    public function checkCompletionStatus()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Always pick the most-advanced enrollment so a newer pending record
        // (e.g. from mass promotion) never hides an existing approved/enrolled one.
        $enrollment = $user->enrollments()
            ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
            ->latest('id')
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::where('student_data->student_email', $user->email)
                ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
                ->latest('id')
                ->first();
        }

        $d = $enrollment ? ($enrollment->student_data ?? []) : [];

        // Payment is complete when fully paid OR downpayment is approved (installment plans)
        $isInstallment = $enrollment
            && ($enrollment->payment_type === 'installment'
                || in_array($enrollment->payment_option, ['B', 'C', 'D']));
        $paymentDone = $enrollment && in_array(
            $enrollment->payment_status ?? 'pending',
            $isInstallment ? ['partial', 'paid'] : ['paid']
        );

        // Documents are tracked in the student portal separately — not counted here
        $completion = [
            'personal'   => !empty($d['first_name']) && !empty($d['last_name']) && !empty($d['birthdate']) && !empty($d['gender']),
            'health'     => !empty($d['blood_type']) || !empty($d['allergies']) || !empty($d['medical_conditions'])
                            || ($profile && (!empty($profile->blood_type) || !empty($profile->allergies) || !empty($profile->medical_conditions))),
            'address'    => !empty($d['province']) && !empty($d['city']) && !empty($d['barangay']) && !empty($d['street_address']),
            'guardian'   => !empty($d['guardian_name']) && !empty($d['relationship']) && !empty($d['guardian_phone']),
            'school'     => !empty($d['last_school']) && !empty($d['grade_level']),
            'enrollment' => $enrollment && in_array($enrollment->status, ['approved', 'enrolled']),
            'payment'    => $paymentDone,
        ];

        $totalComplete = count(array_filter($completion));
        $totalSections = count($completion);
        $percentage = ($totalComplete / $totalSections) * 100;

        return response()->json([
            'completion' => $completion,
            'total_complete' => $totalComplete,
            'total_sections' => $totalSections,
            'percentage' => round($percentage),
            'is_complete' => $percentage === 100,
        ]);
    }
}
