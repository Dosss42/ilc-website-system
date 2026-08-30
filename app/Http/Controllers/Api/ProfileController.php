<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\PreviousSchool;
use App\Models\StudentAddress;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Check profile completion status
     */
    public function checkCompletionStatus()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $address = $user->address;
        $guardian = $user->guardian;
        $previousSchool = $user->previousSchool;
        $enrollment = $user->latestEnrollment;

        // Check if required documents are uploaded
        $requiredDocTypes = ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture'];
        $uploadedDocTypes = StudentDocument::where('enrollment_id', $enrollment?->id)
            ->whereIn('document_type', $requiredDocTypes)
            ->where('status', 'approved')
            ->pluck('document_type')
            ->unique()
            ->count();
        $hasRequiredDocuments = $uploadedDocTypes >= 1;

        $completion = [
            'personal' => $profile && $profile->birthdate && $profile->gender,
            'health' => $profile && $profile->birthdate, // Using birthdate as proxy for health info
            'address' => $address && $address->barangay && $address->municipality && $address->province,
            'guardian' => $guardian && $guardian->name && $guardian->relationship && $guardian->contact,
            'school' => $previousSchool && $previousSchool->school_name && $previousSchool->last_grade_completed,
            'enrollment' => $enrollment && $enrollment->grade_level,
            'payment' => $enrollment && ($enrollment->payment_status === 'paid'),
            'documents' => $hasRequiredDocuments,
        ];

        $totalComplete = count(array_filter($completion));
        $totalSections = count($completion);
        $percentage = ($totalComplete / $totalSections) * 100;

        return response()->json([
            'success' => true,
            'data' => [
                'completion' => $completion,
                'total_complete' => $totalComplete,
                'total_sections' => $totalSections,
                'percentage' => round($percentage),
                'is_complete' => $percentage === 100,
            ]
        ]);
    }

    /**
     * Update personal information
     */
    public function updatePersonal(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'birthdate' => 'required|date',
            'gender' => 'required|in:male,female',
            'contact' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        
        // Update user name
        $user->update([
            'name' => trim("{$request->first_name} {$request->last_name}")
        ]);

        // Update or create student profile
        $profileData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'contact' => $request->contact,
        ];
        
        if ($request->filled('birthdate')) {
            $profileData['birthdate'] = $request->birthdate;
        }
        if ($request->filled('gender')) {
            $profileData['gender'] = $request->gender;
        }
        
        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return response()->json([
            'success' => true,
            'message' => 'Personal information updated successfully!'
        ]);
    }

    /**
     * Update address information
     */
    public function updateAddress(Request $request)
    {
        $request->validate([
            'barangay' => 'required|string|max:150',
            'municipality' => 'required|string|max:150',
            'province' => 'required|string|max:150',
            'zip_code' => 'nullable|string|max:10',
        ]);

        $user = Auth::user();
        
        StudentAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'barangay' => $request->barangay,
                'municipality' => $request->municipality,
                'province' => $request->province,
                'zip_code' => $request->zip_code,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Address information updated successfully!'
        ]);
    }

    /**
     * Update guardian information
     */
    public function updateGuardian(Request $request)
    {
        $request->validate([
            'guardian_name' => 'required|string|max:150',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_contact' => 'required|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_occupation' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        
        Guardian::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->guardian_name,
                'relationship' => $request->guardian_relationship,
                'contact' => $request->guardian_contact,
                'email' => $request->guardian_email,
                'occupation' => $request->guardian_occupation,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Guardian information updated successfully!'
        ]);
    }

    /**
     * Update previous school information
     */
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

        return response()->json([
            'success' => true,
            'message' => 'Previous school information updated successfully!'
        ]);
    }

    /**
     * Update enrollment information
     */
    public function updateEnrollment(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|in:nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6',
            'section' => 'nullable|string|max:100',
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
                'section' => $request->section,
                'status' => 'pending',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Enrollment information updated successfully!'
        ]);
    }
}
