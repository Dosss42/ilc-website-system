<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\StudentAddress;
use App\Models\Guardian;
use App\Models\PreviousSchool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\EnrollmentApproved;
use App\Mail\EnrollmentDeclined;
use App\Models\StudentDocument;
use App\Models\FeeSetting;
use App\Models\Section;
use App\Models\TeacherAssignment;
use App\Models\Promotion;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Setting;
use App\Models\OtpVerification;
use App\Mail\EnrollmentOtpMail;
use App\Services\PaymentService;

class EnrollmentController extends Controller
{
    /**
     * Show enrollment application form
     */
    public function create()
    {
        return view('enrollment.create');
    }

    /**
     * Send OTP to student's Gmail for email verification
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'regex:/^[^\s@]+@gmail\.com$/i'],
        ], [
            'email.regex' => 'Only Gmail addresses are accepted.',
        ]);

        $email = strtolower(trim($request->email));

        // Rate limit: block if a non-expired OTP was sent in the last 60 seconds
        $recent = OtpVerification::where('email', $email)
            ->where('expires_at', '>', now()->subSeconds(60))
            ->where('verified', false)
            ->latest()->first();

        if ($recent) {
            return response()->json([
                'success' => false,
                'message' => 'A code was already sent. Please wait 60 seconds before requesting a new one.',
            ], 429);
        }

        // Delete any previous unverified OTPs for this email
        OtpVerification::where('email', $email)->where('verified', false)->delete();

        // Generate 6-digit OTP
        $otp   = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        OtpVerification::create([
            'email'      => $email,
            'code'       => bcrypt($otp),
            'token'      => $token,
            'attempts'   => 0,
            'verified'   => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($email)->send(new EnrollmentOtpMail($otp, $email));
        } catch (\Exception $e) {
            Log::error('OTP mail failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please check your Gmail address and try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to ' . $email,
        ]);
    }

    /**
     * Verify OTP code submitted by the student
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $email = strtolower(trim($request->email));
        $otp   = OtpVerification::where('email', $email)
            ->where('verified', false)
            ->latest()->first();

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'No verification code found. Please request a new one.'], 422);
        }

        if ($otp->isExpired()) {
            $otp->delete();
            return response()->json(['success' => false, 'message' => 'Your code has expired. Please request a new one.'], 422);
        }

        if ($otp->hasExceededAttempts()) {
            $otp->delete();
            return response()->json(['success' => false, 'message' => 'Too many incorrect attempts. Please request a new code.'], 422);
        }

        if (!\Hash::check($request->code, $otp->code)) {
            $otp->increment('attempts');
            $remaining = 3 - ($otp->attempts);
            return response()->json([
                'success' => false,
                'message' => 'Incorrect code. ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining.',
            ], 422);
        }

        // Mark as verified
        $otp->update(['verified' => true]);

        return response()->json([
            'success' => true,
            'token'   => $otp->token,
            'message' => 'Email verified successfully!',
        ]);
    }

    /**
     * Store enrollment application
     */
    public function store(Request $request)
    {
        // Block online enrollment when window is closed
        if (!Setting::get('enrollment_open', true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Enrollment is currently closed.'], 403);
            }
            return redirect()->route('admission')->with('enrollment_closed', true);
        }

        // Verify OTP token before processing enrollment
        $otpToken = $request->input('otp_token');
        $email    = strtolower(trim($request->input('student_email', '')));
        $validOtp = OtpVerification::where('email', $email)
            ->where('token', $otpToken)
            ->where('verified', true)
            ->first();

        if (!$validOtp) {
            $msg = 'Email verification required. Please verify your Gmail address before submitting.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->withErrors(['otp' => $msg])->withInput();
        }

        // Consume the token so it can't be reused
        $validOtp->delete();

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'suffix' => 'nullable|string|max:10',
                'gender' => 'required|in:male,female',
                'birthdate' => 'required|date',
                'place_of_birth' => 'required|string|max:255',
                'grade_level' => 'required|in:nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6',
                'student_type' => 'required|in:new,transferee,returning',
                'last_school' => 'nullable|string|max:255',
                'mother_name' => 'required|string|max:255',
                'mother_age' => 'required|integer|min:1|max:120',
                'father_name' => 'required|string|max:255',
                'father_age' => 'required|integer|min:1|max:120',
                'religious_affiliation' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'street_address' => 'required|string|max:255',
                'zip_code' => 'nullable|string|max:10',
                'guardian_name' => 'required|string|max:255',
                'relationship' => 'required|in:father,mother,guardian,sibling,grandparent,relative',
                'guardian_occupation' => 'nullable|string|max:255',
                'guardian_phone' => ['required', 'string', 'max:20', 'regex:/^(\+639[0-9]{9}|09[0-9]{9}|09[0-9]{2}-[0-9]{3}-[0-9]{4}|9[0-9]{9})$/'],
                'student_email' => ['required', 'email:rfc,dns', 'max:255', 'regex:/^[^\s@]+@gmail\.com$/i'],
                'blood_type' => 'nullable|string|max:10',
                'allergies' => 'nullable|string|max:255',
                'medical_conditions' => 'nullable|string|max:255',
            ], [
                'student_email.email'  => 'Please enter a valid email address.',
                'student_email.regex'  => 'Only Gmail addresses are accepted (e.g. yourname@gmail.com).',
                'guardian_phone.regex' => 'Phone must be a valid Philippine mobile number (e.g. 9123456789, 09123456789, or +639123456789).',
            ]);

            // Use transaction for faster and safer operations
            DB::beginTransaction();
            
            // Get or create user (include soft-deleted so we don't hit the unique constraint)
            $user = User::withTrashed()->where('email', $validated['student_email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['student_email'],
                    'password' => bcrypt(Str::random(10)),
                    'role' => 'student',
                    'is_active' => false,
                ]);
            } elseif ($user->trashed()) {
                $user->restore();
                $user->update([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'role' => 'student',
                    'is_active' => false,
                ]);
            }

            // Create or update student profile
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'],
                    'gender' => $validated['gender'],
                    'birthdate' => $validated['birthdate'],
                    'contact' => $validated['guardian_phone'],
                ]
            );

            // Create or update student address
            StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street' => $validated['street_address'],
                    'barangay' => $validated['barangay'],
                    'municipality' => $validated['city'],
                    'province' => $validated['province'],
                    'zip_code' => $validated['zip_code'],
                ]
            );

            // Create or update guardian
            Guardian::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['guardian_name'],
                    'relationship' => $validated['relationship'],
                    'contact' => $validated['guardian_phone'],
                    'email' => $validated['student_email'],
                    'occupation' => $validated['guardian_occupation'],
                ]
            );

            // Create or update previous school
            if ($validated['last_school']) {
                PreviousSchool::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_name' => $validated['last_school'],
                        'last_grade_completed' => $validated['grade_level'],
                    ]
                );
            }

            // Create enrollment record — use admin-configured current school year
            $schoolYear = Setting::get('current_school_year') ?: $this->getCurrentSchoolYear();

            // Prevent duplicate enrollment for the same school year
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('school_year', $schoolYear)
                ->whereNotIn('status', ['declined'])
                ->first();

            if ($existingEnrollment) {
                DB::rollBack();
                $msg = 'You already have an enrollment application for S.Y. ' . $schoolYear . ' (Ref: ' . $existingEnrollment->reference_number . ').';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->withErrors(['duplicate' => $msg])->withInput();
            }

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'reference_number' => 'ENR-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'grade_level' => $validated['grade_level'],
                'school_year' => $schoolYear,
                'student_data' => array_merge($validated, ['student_type' => $validated['student_type'], 'place_of_birth' => $validated['place_of_birth'] ?? '', 'suffix' => $validated['suffix'] ?? '', 'blood_type' => $validated['blood_type'] ?? '', 'allergies' => $validated['allergies'] ?? '', 'medical_conditions' => $validated['medical_conditions'] ?? '', 'religious_affiliation' => $validated['religious_affiliation'] ?? '', 'street_address' => $validated['street_address'] ?? '', 'mother_name' => $validated['mother_name'] ?? '', 'mother_age' => $validated['mother_age'] ?? '', 'father_name' => $validated['father_name'] ?? '', 'father_age' => $validated['father_age'] ?? '']),
                'payment_status' => 'pending',
                'created_at' => now(),
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment application submitted successfully! Your reference number is: ' . $enrollment->reference_number,
                    'reference_number' => $enrollment->reference_number
                ]);
            }
            return redirect()->back()
                ->with('success', 'Enrollment application submitted successfully! Your reference number is: ' . $enrollment->reference_number)
                ->with('reference_number', $enrollment->reference_number);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enrollment submission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Submission failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calculate payment breakdown based on grade level and payment option
     */
    public function calculatePaymentBreakdown($gradeLevel, $paymentOption)
    {
        $paymentRates = [
            'nursery' => ['total' => 16005, 'tuition' => 7505, 'misc' => 2800, 'books' => 3550, 'insurance' => 150, 'electric' => 2000],
            'kindergarten' => ['total' => 16005, 'tuition' => 7505, 'misc' => 2800, 'books' => 3550, 'insurance' => 150, 'electric' => 2000],
            'grade1' => ['total' => 17005, 'tuition' => 7505, 'misc' => 2800, 'books' => 4550, 'insurance' => 150, 'electric' => 2000],
            'grade2' => ['total' => 17005, 'tuition' => 7505, 'misc' => 2800, 'books' => 4550, 'insurance' => 150, 'electric' => 2000],
            'grade3' => ['total' => 17505, 'tuition' => 7505, 'misc' => 2800, 'books' => 5050, 'insurance' => 150, 'electric' => 2000],
            'grade4' => ['total' => 18005, 'tuition' => 7505, 'misc' => 2800, 'books' => 5550, 'insurance' => 150, 'electric' => 2000],
            'grade5' => ['total' => 18005, 'tuition' => 7505, 'misc' => 2800, 'books' => 5550, 'insurance' => 150, 'electric' => 2000],
            'grade6' => ['total' => 18005, 'tuition' => 7505, 'misc' => 2800, 'books' => 5550, 'insurance' => 150, 'electric' => 2000],
        ];

        $rates = $paymentRates[$gradeLevel] ?? $paymentRates['grade1'];
        $breakdown = [
            'tuition_fee' => $rates['tuition'],
            'misc_reg_pta' => $rates['misc'],
            'books' => $rates['books'],
            'insurance' => $rates['insurance'],
            'electric_bill' => $rates['electric'],
            'base_total' => $rates['total'],
        ];

        switch ($paymentOption) {
            case 'A': // Cash Basis
                $breakdown['discount'] = 1501;
                $breakdown['total_due'] = $rates['total'] - 1501;
                $breakdown['payment_type'] = 'full';
                break;
            case 'B': // Monthly Payment
                $downpayments = [
                    'nursery' => 6500, 'kindergarten' => 6500,
                    'grade1' => 7500, 'grade2' => 7500, 'grade3' => 8000,
                    'grade4' => 8500, 'grade5' => 8500, 'grade6' => 8500
                ];
                $breakdown['downpayment'] = $downpayments[$gradeLevel] ?? 6500;
                $breakdown['monthly_amount'] = 1056.10;
                $breakdown['duration_months'] = 9;
                $breakdown['total_due'] = $breakdown['downpayment'] + (1056.10 * 9);
                $breakdown['payment_type'] = 'installment';
                break;
            case 'C': // Elementary Monthly
                $downpayments = [
                    'grade1' => 5500, 'grade2' => 5500, 'grade3' => 6000,
                    'grade4' => 6500, 'grade5' => 6500, 'grade6' => 6500
                ];
                $breakdown['downpayment'] = $downpayments[$gradeLevel] ?? 5500;
                $breakdown['monthly_amount'] = 1278.32;
                $breakdown['duration_months'] = 9;
                $breakdown['total_due'] = $breakdown['downpayment'] + (1278.32 * 9);
                $breakdown['payment_type'] = 'installment';
                break;
            case 'D': // Nursery Monthly
                $downpayments = ['nursery' => 4505, 'kindergarten' => 4505];
                $breakdown['downpayment'] = $downpayments[$gradeLevel] ?? 4505;
                $breakdown['monthly_amount'] = 1278.32;
                $breakdown['duration_months'] = 9;
                $breakdown['total_due'] = $breakdown['downpayment'] + (1278.32 * 9);
                $breakdown['payment_type'] = 'installment';
                break;
        }

        return $breakdown;
    }

    /**
     * Store walk-in enrollment (admin creates enrollment on behalf of parent)
     */
    public function walkInEnrollment(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'suffix' => 'nullable|string|max:10',
                'gender' => 'required|in:male,female',
                'birthdate' => 'required|date',
                'place_of_birth' => 'required|string|max:255',
                'nationality' => 'nullable|string|max:255',
                'grade_level' => 'required|in:nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6',
                'student_type' => 'required|in:new,transferee,returning',
                'last_school' => 'nullable|string|max:255',
                'region' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'street_address' => 'required|string|max:255',
                'zip_code' => 'nullable|string|max:10',
                'mother_name' => 'required|string|max:255',
                'mother_age' => 'required|integer|min:1|max:120',
                'father_name' => 'required|string|max:255',
                'father_age' => 'required|integer|min:1|max:120',
                'religious_affiliation' => 'required|string|max:255',
                'guardian_name' => 'required|string|max:255',
                'relationship' => 'required|in:father,mother,guardian,sibling,grandparent,relative',
                'guardian_occupation' => 'nullable|string|max:255',
                'guardian_phone' => ['required', 'string', 'max:20', 'regex:/^(\+639[0-9]{9}|09[0-9]{9}|09[0-9]{2}-[0-9]{3}-[0-9]{4}|9[0-9]{9})$/'],
                'guardian_email' => ['required', 'email:rfc,dns', 'max:255', 'regex:/^[^\s@]+@gmail\.com$/i'],
                'blood_type' => 'nullable|string|max:10',
                'allergies' => 'nullable|string|max:255',
                'medical_conditions' => 'nullable|string|max:255',
                'payment_option' => 'required|in:A,B,C,D',
                'downpayment_amount' => 'required|numeric|min:0',
                'monthly_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'lrn' => 'nullable|string|max:20',
                'email' => ['nullable', 'email:rfc,dns', 'max:255'],
                'reenroll_student_id' => 'nullable|integer|exists:users,id',
            ], [
                'guardian_email.email' => 'Please enter a valid email address.',
                'guardian_email.regex' => 'Only Gmail addresses are accepted (e.g. yourname@gmail.com).',
                'email.email'          => 'Please enter a valid email address.',
                'guardian_phone.regex' => 'Phone must be a valid Philippine mobile number (e.g. 9123456789, 09123456789, or +639123456789).',
            ]);

            $isReEnrollment = !empty($validated['reenroll_student_id']);

            // Use transaction for faster and safer operations
            DB::beginTransaction();

            // Get or create user
            if ($isReEnrollment) {
                // Re-enrollment: use existing student user
                $user = User::findOrFail($validated['reenroll_student_id']);
                // Update LRN if provided
                if (!empty($validated['lrn'])) {
                    $user->lrn = $validated['lrn'];
                    $user->save();
                }
            } else {
                $user = User::withTrashed()->where('email', $validated['guardian_email'])->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => $validated['guardian_email'],
                        'password' => bcrypt(Str::random(10)),
                        'role' => 'student',
                        'is_active' => false,
                        'lrn' => $validated['lrn'] ?? null,
                    ]);
                } elseif ($user->trashed()) {
                    $user->restore();
                    $user->update([
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'role' => 'student',
                        'is_active' => false,
                        'lrn' => $validated['lrn'] ?? $user->lrn,
                    ]);
                }
            }

            // Create or update student profile
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'],
                    'gender' => $validated['gender'],
                    'birthdate' => $validated['birthdate'],
                    'contact' => $validated['guardian_phone'],
                ]
            );

            // Create or update student address
            StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street' => $validated['street_address'],
                    'barangay' => $validated['barangay'],
                    'municipality' => $validated['city'],
                    'province' => $validated['province'],
                    'zip_code' => $validated['zip_code'],
                ]
            );

            // Create or update guardian
            Guardian::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['guardian_name'],
                    'relationship' => $validated['relationship'],
                    'contact' => $validated['guardian_phone'],
                    'email' => $validated['guardian_email'],
                    'occupation' => $validated['guardian_occupation'],
                ]
            );

            // Create or update previous school
            if ($validated['last_school']) {
                PreviousSchool::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_name' => $validated['last_school'],
                        'last_grade_completed' => $validated['grade_level'],
                    ]
                );
            }

            // Calculate payment breakdown
            $paymentBreakdown = $this->calculatePaymentBreakdown(
                $validated['grade_level'],
                $validated['payment_option']
            );

            // Prevent duplicate enrollment for the same school year
            $schoolYear = Setting::get('current_school_year') ?: $this->getCurrentSchoolYear();
            $existingWalkIn = Enrollment::where('user_id', $user->id)
                ->where('school_year', $schoolYear)
                ->whereNotIn('status', ['declined'])
                ->first();

            if ($existingWalkIn) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This student already has an enrollment application for S.Y. ' . $schoolYear . ' (Ref: ' . $existingWalkIn->reference_number . '). Please check the Enrollment Management section.',
                ], 422);
            }

            // Create enrollment record with walk-in flag and payment details
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'reference_number' => 'ENR-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'grade_level' => $validated['grade_level'],
                'school_year' => $schoolYear,
                'student_data' => array_merge($validated, ['student_type' => $isReEnrollment ? 'returning' : $validated['student_type'], 'student_email' => $validated['guardian_email'], 'is_walk_in' => true, 'is_re_enrollment' => $isReEnrollment]),
                'payment_option' => $validated['payment_option'],
                'downpayment_amount' => $validated['downpayment_amount'],
                'monthly_amount' => $validated['monthly_amount'],
                'total_fee' => $validated['total_amount'],
                'payment_breakdown' => json_encode($paymentBreakdown),
                'payment_type' => $validated['payment_option'] === 'A' ? 'full' : 'installment',
                'payment_status' => 'pending',
                'created_at' => now(),
            ]);

            // Note: downpayment_amount and monthly_amount are fee breakdown values only.
            // No payment transaction is created here — payment is recorded separately
            // when the user actually processes a payment.
            $enrollment->update([
                'payment_amount' => 0,
                'remaining_balance' => $validated['total_amount'],
                'payment_status' => 'pending',
            ]);

            Log::info('Walk-in enrollment created', [
                'enrollment_id' => $enrollment->id,
                'payment_amount' => $enrollment->payment_amount,
                'payment_status' => $enrollment->payment_status,
                'downpayment_amount' => $enrollment->downpayment_amount,
            ]);

            DB::commit();
            
            // Refresh to see if something changed after commit
            $enrollment->refresh();
            Log::info('Walk-in enrollment after commit', [
                'enrollment_id' => $enrollment->id,
                'payment_amount' => $enrollment->payment_amount,
                'payment_status' => $enrollment->payment_status,
            ]);

            return response()->json([
                'success' => true,
                'reference_number' => $enrollment->reference_number,
                'message' => 'Walk-in enrollment submitted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Walk-in enrollment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Submission failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display admin enrollment management dashboard
     */
    public function adminIndex(Request $request)
    {
        // Get sort and filter parameters
        $sort = $request->get('sort', 'newest'); // default: newest first
        $statusFilter = $request->get('status', 'all');
        $gradeFilter = $request->get('grade', 'all');
        $studentSearch = $request->get('student_search') ?? '';
        $studentGradeFilter = $request->get('student_grade', 'all');
        $studentStatusFilter = $request->get('student_status', 'all');
        $studentPaymentFilter = $request->get('student_payment', 'all');
        $studentSchoolYearFilter = $request->get('student_schoolyear', 'all');
        $gradeLevel = $request->get('grade_level', 'all'); // for mass promotion

        // ── 1. Enrollment records (paginated with sorting) ──
        $enrollmentQuery = Enrollment::select('id', 'user_id', 'reference_number', 'status', 'student_data', 'payment_status', 'payment_amount', 'payment_method', 'payment_reference', 'payment_type', 'total_fee', 'remaining_balance', 'payment_due_date', 'created_at', 'updated_at');

        // Apply status filter
        if ($statusFilter !== 'all') {
            $enrollmentQuery->where('status', $statusFilter);
        }

        // Apply grade level filter
        if ($gradeFilter !== 'all') {
            $enrollmentQuery->where('student_data->grade_level', $gradeFilter);
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $enrollmentQuery->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(student_data, '$.last_name')) ASC");
                break;
            case 'name_desc':
                $enrollmentQuery->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(student_data, '$.last_name')) DESC");
                break;
            case 'oldest':
                $enrollmentQuery->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $enrollmentQuery->orderBy('created_at', 'desc');
                break;
        }

        $enrollments = $enrollmentQuery->paginate(20, ['*'], 'enrollment_page');

        // ── 2. Recent Students for Dashboard (10 per page) ──
        $recentStudents = User::where('role', 'student')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'dashboard_page');

        // ── 3. Student Management & Payment Overview (shared query) ──
        // Show ALL students with role='student' — including pending enrollments
        $studentsQuery = User::where('role', 'student');

        // Apply search filter
        if ($studentSearch) {
            $studentsQuery->where(function($q) use ($studentSearch) {
                $q->where('name', 'like', '%' . $studentSearch . '%')
                  ->orWhere('email', 'like', '%' . $studentSearch . '%')
                  ->orWhere('lrn', 'like', '%' . $studentSearch . '%');
            });
        }

        // Apply grade filter
        if ($studentGradeFilter && $studentGradeFilter !== 'all') {
            $studentsQuery->whereHas('latestEnrollment', function($q) use ($studentGradeFilter) {
                $q->where('student_data->grade_level', $studentGradeFilter);
            });
        }

        // Apply grade_level filter for mass promotion
        if ($gradeLevel && $gradeLevel !== 'all') {
            $studentsQuery->whereHas('latestEnrollment', function($q) use ($gradeLevel) {
                $q->where('student_data->grade_level', $gradeLevel);
            });
        }

        // Apply status filter
        if ($studentStatusFilter && $studentStatusFilter !== 'all') {
            if ($studentStatusFilter === 'not_enrolled') {
                $studentsQuery->whereDoesntHave('enrollments', function($q) {
                    $q->whereIn('status', ['enrolled', 'approved', 'pending']);
                });
            } else {
                $studentsQuery->whereHas('latestEnrollment', function($q) use ($studentStatusFilter) {
                    $q->where('status', $studentStatusFilter);
                });
            }
        }

        // Apply payment filter
        if ($studentPaymentFilter && $studentPaymentFilter !== 'all') {
            $studentsQuery->whereHas('latestEnrollment', function($q) use ($studentPaymentFilter) {
                $q->where('payment_status', $studentPaymentFilter);
            });
        }

        // Apply school year filter — check any enrollment, not just the latest
        if ($studentSchoolYearFilter && $studentSchoolYearFilter !== 'all') {
            $studentsQuery->whereHas('enrollments', function($q) use ($studentSchoolYearFilter) {
                $q->where('school_year', $studentSchoolYearFilter);
            });
        }

        $students = $studentsQuery->with(['latestEnrollment' => function ($query) {
                $query->with('paymentInstallments');
            }, 'enrollments' => function ($query) {
                $query->with('paymentInstallments')
                    ->where(function ($q) {
                    $q->whereIn('status', ['approved', 'enrolled', 'completed', 'dropped', 'transferred'])
                      ->orWhere('payment_amount', '>', 0)
                      ->orWhere('student_data->is_walk_in', true);
                })->orderBy('id', 'desc');
            }, 'promotions' => function ($query) {
                $query->latest()->limit(1);
            }]);

        // Apply sorting for students
        switch ($sort) {
            case 'name_asc':
                $studentsQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $studentsQuery->orderBy('name', 'desc');
                break;
            case 'oldest':
                $studentsQuery->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $studentsQuery->orderBy('created_at', 'desc');
                break;
        }

        $students = $studentsQuery->paginate(15, ['*'], 'student_page');

        // Archived students (soft-deleted)
        $archivedStudents = User::onlyTrashed()
            ->where('role', 'student')
            ->with(['latestEnrollment'])
            ->orderByDesc('deleted_at')
            ->paginate(15, ['*'], 'archive_page');

        $allStudentsPayment = $students;

        // ── 3. Finance aggregates – single raw query instead of 6 separate ones ──
        $financeSummary = DB::selectOne("
            SELECT
                COUNT(CASE WHEN e.payment_status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN e.payment_status = 'partial' THEN 1 END) as partial_count,
                COUNT(CASE WHEN e.payment_status IS NULL OR e.payment_status = 'pending' THEN 1 END) as unpaid_count,
                COUNT(*) as total_with_enrollment,
                COALESCE(SUM(CASE WHEN e.payment_status IN ('paid', 'partial') THEN e.payment_amount ELSE 0 END), 0) as total_collected,
                (SELECT COUNT(*) FROM student_documents WHERE document_type = 'payment_screenshot' AND status = 'pending') as pending_screenshots
            FROM enrollments e
            WHERE e.id IN (SELECT MAX(id) FROM enrollments GROUP BY user_id)
        ");
        $paidCount = $financeSummary->paid_count ?? 0;
        $partialCount = $financeSummary->partial_count ?? 0;
        $unpaidCount = $financeSummary->unpaid_count ?? 0;
        $totalCollected = $financeSummary->total_collected ?? 0;
        $pendingPayments = $partialCount;
        $pendingScreenshots = $financeSummary->pending_screenshots ?? 0;

        // ── 4. Payment enrollments & documents (limited) ──
        // Get approved/enrolled enrollments for payment tracking — keep limit tight
        $paymentEnrollments = Enrollment::select('id', 'user_id', 'reference_number', 'student_data', 'status', 'payment_status', 'payment_amount', 'payment_method', 'payment_reference', 'updated_at', 'total_fee', 'remaining_balance', 'payment_option', 'payment_type', 'downpayment_amount', 'monthly_amount')
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'enrolled'])
                  ->orWhere('payment_amount', '>', 0);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(50)->get();

        // Get recent documents — limit to 50 to keep page load fast
        $allDocuments = \App\Models\StudentDocument::with('user:id,name', 'enrollment:id,reference_number', 'paymentInstallment')
            ->whereHas('user')
            ->latest()->limit(50)->get();

        // Separate payment screenshots from other document types
        $paymentScreenshots = $allDocuments->where('document_type', 'payment_screenshot');

        // ── 4b. Finance Portal data: Online Payments (payment_screenshot documents) ──
        $onlinePayments = \App\Models\StudentDocument::where('document_type', 'payment_screenshot')
            ->whereHas('user')
            ->with(['enrollment', 'user', 'paymentInstallment'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'payment_page');

        // ── 4b2. Walk-in Payment Transactions (from PaymentTransaction) ──
        // Includes both 'walkin' (installment payments) and 'admin' (direct admin payments)
        // These are all processed by admin/cashier, NOT by students online
        $walkInTransactions = \App\Models\PaymentTransaction::whereIn('payment_type', ['walkin', 'admin'])
            ->whereHas('user')
            ->whereHas('enrollment')
            ->with(['enrollment', 'user', 'installment'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'walkin_page');

        // ── 4b3. Combined payment stats (online + walk-in) ──
        $payStatsOnline = \App\Models\StudentDocument::where('document_type', 'payment_screenshot')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status IN ('approved','completed') THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")->first();
        $payStatsWalkin = \App\Models\PaymentTransaction::whereIn('payment_type', ['walkin', 'admin', 'downpayment'])
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount")
            ->first();
        $combinedPayStats = [
            'total'         => ($payStatsOnline->total    ?? 0) + ($payStatsWalkin->total    ?? 0),
            'pending'       => $payStatsOnline->pending   ?? 0,
            'completed'     => ($payStatsOnline->completed ?? 0) + ($payStatsWalkin->total    ?? 0),
            'rejected'      => $payStatsOnline->rejected  ?? 0,
            'walkin_amount' => $payStatsWalkin->total_amount ?? 0,
        ];

        // ── 4c. Finance Portal data: Installments (enrollments with actual installment records) ──
        $currentSchoolYear = $this->getCurrentSchoolYear();
        $installmentEnrollments = Enrollment::where('school_year', $currentSchoolYear)
            ->where(function ($query) {
                $query->where('payment_type', 'installment')
                    ->orWhereIn('payment_option', ['B', 'C', 'D']);
            })
            ->whereHas('paymentInstallments') // Only show if installments actually exist
            ->with(['user', 'paymentInstallments'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'installment_page');

        // Calculate next due date and progress for each installment enrollment
        foreach ($installmentEnrollments as $enrollment) {
            $nextPending = $enrollment->paymentInstallments
                ->where('status', 'pending')
                ->sortBy('due_date')
                ->first();

            if ($nextPending) {
                $enrollment->next_due_date = $nextPending->due_date;
                $enrollment->next_due_amount = $nextPending->total_due;
                $enrollment->next_month_name = $nextPending->month_name;
                $enrollment->is_overdue = $nextPending->due_date < \Carbon\Carbon::today();
                $enrollment->weeks_overdue = $nextPending->weeks_overdue;
            } else {
                $lastPaid = $enrollment->paymentInstallments
                    ->where('status', 'paid')
                    ->sortByDesc('due_date')
                    ->first();
                $enrollment->next_due_date = null;
                $enrollment->next_due_amount = 0;
                $enrollment->next_month_name = $lastPaid ? 'Fully Paid' : 'N/A';
                $enrollment->is_overdue = false;
                $enrollment->weeks_overdue = 0;
            }

            $enrollment->total_late_fees = $enrollment->paymentInstallments->sum('late_fee');
            $totalInst = $enrollment->paymentInstallments->count();
            $paidInst = $enrollment->paymentInstallments->where('status', 'paid')->count();
            $enrollment->installment_progress = $totalInst > 0 ? ($paidInst / $totalInst) * 100 : 0;
        }

        // ── 5. Academic data – select only needed columns, eager-load slim ──
        $subjectsQuery = \App\Models\Subject::with('teacher:id,name')->select('id', 'code', 'name', 'description', 'grade_level', 'teacher_id', 'is_active')
            ->orderBy('name', 'asc');

        $subjects = $subjectsQuery->paginate(15, ['*'], 'subject_page');

        $sectionsQuery = \App\Models\Section::with(['teacher:id,name', 'subjects:id,name,code'])->select('id', 'name', 'grade_level', 'teacher_id', 'room_number', 'current_enrollment', 'max_students', 'school_year', 'is_active')
            ->orderBy('name', 'asc');
        
        $sections = $sectionsQuery->paginate(15, ['*'], 'section_page');
        $schedules = \App\Models\Schedule::with(['section:id,name', 'subject:id,name,code', 'teacher:id,name'])
            ->select('id', 'section_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time', 'room', 'is_active')
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $allSchedules = \App\Models\Schedule::with(['section', 'subject', 'teacher'])
            ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();
        $teachers  = User::where('role', 'teacher')->select('id', 'name', 'email', 'is_active', 'created_at')->orderByDesc('created_at')->paginate(15, ['*'], 'teacher_page');

        // ── 5b. Teacher Assignments – for schedule teacher filtering ──
        $teacherAssignments = TeacherAssignment::with(['teacher:id,name', 'section:id,name,grade_level', 'subject:id,name,code'])
            ->select('id', 'teacher_id', 'section_id', 'subject_id', 'is_advisory', 'school_year')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'ta_page');

        // ── 6. Guidance records ──
        $guidanceSearch  = $request->get('guidance_search', '');
        $guidanceStatus  = $request->get('guidance_status', '');
        $guidanceConcern = $request->get('guidance_concern', '');
        $guidanceSort    = $request->get('guidance_sort', 'date_desc');

        $guidanceQuery = \App\Models\GuidanceRecord::with(['student:id,name', 'counselor:id,name']);

        if ($guidanceSearch) {
            $guidanceQuery->whereHas('student', function ($q) use ($guidanceSearch) {
                $q->where('name', 'like', '%' . $guidanceSearch . '%');
            });
        }
        if ($guidanceStatus) {
            $guidanceQuery->where('status', $guidanceStatus);
        }
        if ($guidanceConcern) {
            $guidanceQuery->where('concern_type', $guidanceConcern);
        }
        $guidanceQuery->orderBy('date', $guidanceSort === 'date_asc' ? 'asc' : 'desc');

        $guidanceRecords = $guidanceQuery->paginate(15, ['*'], 'guidance_page');

        // ── 7. Fee Breakdown Preview ──
        $feeSettings = FeeSetting::first() ?? new FeeSetting();
        $feeBreakdowns = [];
        $gradeLevels = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
        foreach ($gradeLevels as $grade) {
            if (in_array($grade, ['nursery', 'kindergarten'])) {
                $bookFee = $feeSettings->books_nursery ?? 0;
            } elseif (in_array($grade, ['grade1', 'grade2'])) {
                $bookFee = $feeSettings->books_grade1 ?? 0;
            } elseif ($grade === 'grade3') {
                $bookFee = $feeSettings->books_grade3 ?? 0;
            } else {
                $bookFee = $feeSettings->books_grade4 ?? 0;
            }
            $feeBreakdowns[$grade] = [
                'tuition'    => $feeSettings->tuition    ?? 0,
                'misc'       => $feeSettings->misc       ?? 0,
                'insurance'  => $feeSettings->insurance  ?? 0,
                'electric'   => $feeSettings->electric   ?? 0,
                'books'      => $bookFee,
                'base_total' => ($feeSettings->tuition ?? 0) + ($feeSettings->misc ?? 0) + ($feeSettings->insurance ?? 0) + ($feeSettings->electric ?? 0) + $bookFee,
            ];
        }

        // Return JSON for AJAX requests (Mass Promotion)
        if ($request->wantsJson()) {
            return response()->json($students);
        }

        // Alias onlinePayments back to financePayments for backward compatibility
        $financePayments = $onlinePayments;

        $enrollmentOpen  = Setting::get('enrollment_open', true);
        $maintenanceMode = Setting::get('maintenance_mode', false);

        // ── Assessment & Promotion: Grade 1-6 students (non-transferee, enrolled/completed) ──
        // Filter grade_level in SQL (uses indexed column) — only PHP-filter the JSON student_type
        $assessStudents = \App\Models\User::where('role', 'student')
            ->with(['latestEnrollment', 'promotions' => fn($q) => $q->orderByDesc('id')])
            ->whereHas('latestEnrollment', fn($q) => $q
                ->whereIn('status', ['enrolled', 'completed'])
                ->whereIn('grade_level', ['nursery','kindergarten','grade1','grade2','grade3','grade4','grade5','grade6'])
            )
            ->orderBy('name')
            ->get()
            ->filter(fn($s) => ($s->latestEnrollment->student_data['student_type'] ?? '') !== 'transferee')
            ->values();

        // Contact messages for admin inbox
        $contactMessages      = \App\Models\ContactMessage::orderByDesc('created_at')->get();
        $unreadMessagesCount  = $contactMessages->where('status', 'unread')->count();

        // Announcements & News for admin management
        $announcements = \App\Models\Announcement::orderByDesc('created_at')->take(20)->get();
        $news          = \App\Models\News::orderByDesc('created_at')->take(20)->get();

        return view('adminDashboard', compact(
            'enrollments', 'students', 'paymentEnrollments',
            'pendingPayments', 'totalCollected', 'pendingScreenshots', 'allDocuments',
            'paymentScreenshots',
            'allStudentsPayment', 'unpaidCount', 'partialCount', 'paidCount',
            'financePayments', 'walkInTransactions', 'installmentEnrollments', 'combinedPayStats',
            'sort', 'statusFilter', 'gradeFilter',
            'studentSearch', 'studentGradeFilter', 'studentStatusFilter', 'studentPaymentFilter', 'studentSchoolYearFilter', 'archivedStudents',
            'subjects', 'sections', 'schedules', 'teachers', 'teacherAssignments', 'guidanceRecords',
            'guidanceSearch', 'guidanceStatus', 'guidanceConcern', 'guidanceSort',
            'feeBreakdowns', 'feeSettings', 'recentStudents', 'allSchedules',
            'currentSchoolYear', 'enrollmentOpen', 'maintenanceMode', 'assessStudents',
            'contactMessages', 'unreadMessagesCount',
            'announcements', 'news'
        ));
    }

    /**
     * Show enrollment details for admin
     */
    public function adminShow(Enrollment $enrollment)
    {
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($enrollment);
        }

        return view('adminDashboard', compact('enrollment'));
    }

    /**
     * Approve enrollment
     */
    public function approve(Enrollment $enrollment)
    {
        try {
            $studentData = $enrollment->student_data;

            // Calculate payment fields only if payment_option is explicitly set in student_data
            // (walk-in enrollments have it; online enrollments choose later in student portal)
            $paymentUpdate = [];
            if (empty($enrollment->total_fee)) {
                $paymentOption = $studentData['payment_option'] ?? ($studentData['paymentOption'] ?? null);

                if ($paymentOption) {
                    $gradeLevel = $studentData['grade_level'] ?? $enrollment->grade_level;
                    $breakdown = $this->calculatePaymentBreakdown($gradeLevel, $paymentOption);

                    $paymentUpdate['total_fee'] = $breakdown['total_due'] ?? $breakdown['base_total'] ?? 0;
                    $paymentUpdate['remaining_balance'] = $breakdown['total_due'] ?? $breakdown['base_total'] ?? 0;
                    $paymentUpdate['payment_type'] = $breakdown['payment_type'] ?? ($paymentOption === 'A' ? 'full' : 'installment');
                    $paymentUpdate['payment_breakdown'] = json_encode($breakdown);
                    $paymentUpdate['payment_option'] = $paymentOption;
                    $paymentUpdate['downpayment_amount'] = $breakdown['downpayment'] ?? 0;
                    $paymentUpdate['monthly_amount'] = $breakdown['monthly_amount'] ?? 0;

                    // Set next installment date for installment payments
                    if (($breakdown['payment_type'] ?? '') === 'installment') {
                        $paymentUpdate['next_installment_date'] = now()->addDays(30);
                    }
                }
                // If no payment_option, leave total_fee empty — student will choose in portal
            }

            // Both walk-in and online enrollments go to 'approved' after admin approval.
            // Status changes to 'enrolled' only after payment is processed via Finance portal.
            $isWalkIn = !empty($studentData['is_walk_in']);

            // Wrap all DB writes in a single transaction — faster (one commit) and atomic
            DB::beginTransaction();

            // Update enrollment status + payment fields
            $enrollment->update(array_merge([
                'status'      => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'enrolled_at' => null,
            ], $paymentUpdate));

            // Create student user account (or link existing one)
            $studentData = $enrollment->student_data;
            $password = null;
            $isReEnrollment = ($studentData['student_type'] ?? '') === 'returning';
            $existingUser = User::withTrashed()->where('email', $studentData['student_email'] ?? '')->first();

            if ($existingUser) {
                if ($existingUser->trashed()) {
                    $existingUser->restore();
                }
                $user = $existingUser;

                if ($isReEnrollment) {
                    // Re-enrollment: student already knows their password — just activate
                    $user->update(['role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
                    // $password stays null → no credential email sent
                } else {
                    // New/transferee linked to existing account — reset password and notify
                    $password = $this->generateStudentPassword();
                    $user->update(['password' => bcrypt($password), 'role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
                }
                $enrollment->update(['user_id' => $user->id]);
            } else {
                // Brand-new student account
                $password = $this->generateStudentPassword();
                $user = User::create([
                    'name' => trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? '')),
                    'email' => $studentData['student_email'] ?? '',
                    'password' => bcrypt($password),
                    'role' => 'student',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                $enrollment->update(['user_id' => $user->id]);
            }

            // Populate profile tables from student_data
            // Parse birthdate: type="date" sends YYYY-MM-DD, old data may have MM/DD/YYYY
            $rawBirthdate = $studentData['birthdate'] ?? null;
            $parsedBirthdate = null;
            if ($rawBirthdate) {
                try {
                    // Try YYYY-MM-DD first (from type="date" input)
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawBirthdate)) {
                        $parsedBirthdate = $rawBirthdate;
                    } else {
                        // Fallback: MM/DD/YYYY from old text input
                        $dt = \Carbon\Carbon::createFromFormat('m/d/Y', $rawBirthdate);
                        $parsedBirthdate = $dt ? $dt->format('Y-m-d') : null;
                    }
                } catch (\Exception $e) {
                    try {
                        $parsedBirthdate = \Carbon\Carbon::parse($rawBirthdate)->format('Y-m-d');
                    } catch (\Exception $e2) {
                        $parsedBirthdate = null;
                    }
                }
            }

            \App\Models\StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $studentData['first_name'] ?? '',
                    'last_name' => $studentData['last_name'] ?? '',
                    'middle_name' => $studentData['middle_name'] ?? null,
                    'birthdate' => $parsedBirthdate,
                    'gender' => $studentData['gender'] ?? 'male',
                    'contact' => $studentData['guardian_phone'] ?? '',
                ]
            );

            \App\Models\StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street' => $studentData['street_address'] ?? '',
                    'barangay' => $studentData['barangay'] ?? '',
                    'municipality' => $studentData['city'] ?? '',
                    'province' => $studentData['province'] ?? '',
                    'zip_code' => $studentData['zip_code'] ?? null,
                ]
            );

            \App\Models\Guardian::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $studentData['guardian_name'] ?? '',
                    'relationship' => ucfirst($studentData['relationship'] ?? 'Guardian'),
                    'contact' => $studentData['guardian_phone'] ?? '',
                    'email' => $studentData['student_email'] ?? null,
                    'occupation' => $studentData['guardian_occupation'] ?? null,
                ]
            );

            if (!empty($studentData['last_school'])) {
                \App\Models\PreviousSchool::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_name' => $studentData['last_school'],
                        'school_address' => null,
                        'last_grade_completed' => $studentData['grade_level'] ?? '',
                    ]
                );
            }

            DB::commit(); // single commit for all writes above

            // Section assignment happens when Finance processes payment → updateEnrollmentPaymentStatus()

            // Send approval email after the response is returned so the admin is not blocked
            if ($password) {
                $recipientEmail = ($studentData['is_walk_in'] ?? false)
                    ? ($studentData['guardian_email'] ?? $studentData['student_email'] ?? '')
                    : ($studentData['student_email'] ?? '');

                $capturedEmail      = $recipientEmail;
                $capturedEnrollment = $enrollment;
                $capturedPassword   = $password;

                dispatch(function () use ($capturedEmail, $capturedEnrollment, $capturedPassword) {
                    try {
                        Mail::to($capturedEmail)->send(new EnrollmentApproved($capturedEnrollment, $capturedPassword));
                    } catch (\Exception $mailException) {
                        Log::warning('Failed to send approval email: ' . $mailException->getMessage());
                    }
                })->afterResponse();
            }

            $msg = $existingUser
                ? 'Enrollment approved successfully! Linked to existing student account.'
                : 'Enrollment approved successfully! Student account created.';

            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg
                ]);
            }

            return redirect()->route('admin.enrollments.show', $enrollment)
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enrollment approval error: ' . $e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'student_data' => $enrollment->student_data,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error approving enrollment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error approving enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Decline enrollment
     */
    public function decline(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'storage' => 'nullable|string|max:50',
        ]);

        try {
            // Update enrollment status
            $enrollment->update([
                'status' => 'declined',
                'declined_at' => now(),
                'declined_by' => Auth::id(),
                'decline_reason' => $validated['reason'],
                'decline_storage' => $validated['storage'] ?? 'pending',
            ]);

            // Try to send decline email, but don't fail if it doesn't work
            try {
                $studentData = $enrollment->student_data;
                Mail::to($studentData['student_email'] ?? '')->queue(new EnrollmentDeclined($enrollment, $validated['reason']));
            } catch (\Exception $mailException) {
                Log::warning('Failed to send decline email: ' . $mailException->getMessage());
                // Continue without failing the entire decline process
            }

            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment declined successfully.'
                ]);
            }

            return redirect()->route('admin.enrollments.show', $enrollment)
                ->with('success', 'Enrollment declined successfully.');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Enrollment decline error: ' . $e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'student_data' => $enrollment->student_data,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error declining enrollment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error declining enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Generate a readable student password
     * Format: YEAR + 4-6 random digits (e.g., 2026-1234)
     */
    private function generateStudentPassword(): string
    {
        $year = date('Y');
        $randomDigits = str_pad(mt_rand(1000, 999999), 4, '0', STR_PAD_LEFT);
        return $year . '-' . $randomDigits;
    }

    /**
     * Automatic Section Assignment
     * Assigns student to the section for their grade level
     * Since ILC has only one section per grade level, this is straightforward
     * Manual override available via admin interface
     */
    private function assignSection(Enrollment $enrollment, User $user)
    {
        try {
            $gradeLevel = $enrollment->grade_level;
            $schoolYear = $enrollment->school_year ?? (now()->year . '-' . (now()->year + 1));

            // Find the least-filled active section with available capacity
            $section = \App\Models\Section::where('grade_level', $gradeLevel)
                ->where('school_year', $schoolYear)
                ->where('is_active', true)
                ->whereRaw('current_enrollment < max_students')
                ->orderBy('current_enrollment', 'asc')
                ->first();

            if ($section) {
                // Update enrollment with section name
                $enrollment->update([
                    'section' => $section->name,
                ]);

                // Add student to section_student pivot table
                // Check if already exists to avoid duplicates
                $existing = DB::table('section_student')
                    ->where('section_id', $section->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existing) {
                    DB::table('section_student')->insert([
                        'section_id' => $section->id,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update section's current enrollment count
                    $section->increment('current_enrollment');
                }

                Log::info("Student {$user->id} assigned to section {$section->name} (Grade: {$gradeLevel})");
            } else {
                // No section found - log warning but don't fail approval
                Log::warning("No active section found for grade level: {$gradeLevel}, school year: {$schoolYear}");
                
                // Admin will need to create section first or assign manually
                // This is expected behavior for new school years before sections are created
            }
        } catch (\Exception $e) {
            Log::error("Section assignment error: " . $e->getMessage());
            // Don't fail the entire approval process if section assignment fails
            // Admin can assign section manually later
        }
    }

    /**
     * Update enrollment payment status
     */
    public function updatePayment(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,partial',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:gcash,cash',
            'payment_reference' => 'nullable|string|max:255',
            'payment_option' => 'nullable|in:A,B,C,D',
            'payment_type' => 'nullable|in:full,installment',
            'payment_breakdown.downpayment' => 'nullable|numeric',
            'payment_breakdown.monthly' => 'nullable|numeric',
            'payment_breakdown.total' => 'nullable|numeric',
        ]);

        $oldAmount = (float) ($enrollment->payment_amount ?? 0);
        $action = $request->input('payment_action', 'set');

        // plan-only: preserve existing amount, just update plan type + status
        if ($action === 'plan-only') {
            $newTotal = $oldAmount;
        } elseif ($action === 'increment') {
            $incomingAmount = (float) ($validated['payment_amount'] ?? 0);
            $newTotal = $oldAmount + $incomingAmount;
        } else {
            $incomingAmount = isset($validated['payment_amount']) && $validated['payment_amount'] !== null
                ? (float) $validated['payment_amount']
                : $oldAmount;
            $newTotal = $incomingAmount;
        }

        // Save payment option and breakdown if provided (admin payment modal sends these)
        $paymentOption = $request->input('payment_option');
        $breakdown = $request->input('payment_breakdown');

        if ($paymentOption) {
            $validated['payment_option'] = $paymentOption;
            $validated['payment_type'] = $paymentOption === 'A' ? 'full' : 'installment';
        } elseif (!empty($validated['payment_type'])) {
            // plan-only with installment: payment_option stays as-is, but payment_type is updated
        }
        if ($breakdown) {
            if (isset($breakdown['downpayment']) && $breakdown['downpayment'] !== '') {
                $validated['downpayment_amount'] = $breakdown['downpayment'];
            }
            if (isset($breakdown['monthly']) && $breakdown['monthly'] !== '') {
                $validated['monthly_amount'] = $breakdown['monthly'];
            }
            if (isset($breakdown['total']) && $breakdown['total'] !== '' && $breakdown['total'] > 0) {
                $validated['total_fee'] = $breakdown['total'];
            }
        }

        $totalFee = (float) ($enrollment->total_fee ?? ($breakdown['total'] ?? 0));
        $remainingBalance = max(0, $totalFee - $newTotal);

        // If balance is fully paid, override status to 'paid'
        if ($remainingBalance <= 0 && $newTotal > 0) {
            $validated['payment_status'] = 'paid';
        }

        $updateData = [
            'payment_status'    => $validated['payment_status'],
            'payment_amount'    => $newTotal,
            'payment_method'    => $validated['payment_method'] ?? $enrollment->payment_method,
            'payment_reference' => $validated['payment_reference'] ?? $enrollment->payment_reference,
            'payment_updated_at' => now(),
            'remaining_balance' => $remainingBalance,
        ];

        // Save payment breakdown fields if they were set above
        if (isset($validated['payment_option'])) {
            $updateData['payment_option'] = $validated['payment_option'];
        }
        if (isset($validated['downpayment_amount'])) {
            $updateData['downpayment_amount'] = $validated['downpayment_amount'];
        }
        if (isset($validated['monthly_amount'])) {
            $updateData['monthly_amount'] = $validated['monthly_amount'];
        }
        if (isset($validated['total_fee'])) {
            $updateData['total_fee'] = $validated['total_fee'];
        }
        if (isset($validated['payment_type'])) {
            $updateData['payment_type'] = $validated['payment_type'];
        }

        // Only advance schedule when total amount actually increased AND it's an installment
        $effectiveMonthly = (float) ($enrollment->monthly_amount ?? $validated['monthly_amount'] ?? 0);
        if ($newTotal > $oldAmount && $effectiveMonthly > 0 && $validated['payment_status'] !== 'paid') {
            $nextDate = $enrollment->next_installment_date;
            if (!$nextDate) {
                $nextDate = now();
            } else {
                if ($nextDate->isPast()) {
                    $nextDate = now();
                }
            }
            $schedule = $enrollment->installment_schedule ?? 30;
            $updateData['next_installment_date'] = $nextDate->copy()->addDays($schedule);
            $updateData['installment_number'] = ($enrollment->installment_number ?? 0) + 1;
        }

        if ($validated['payment_status'] === 'paid') {
            $updateData['next_installment_date'] = null;
            $updateData['remaining_balance'] = 0;
        }

        // Check if this is the first payment (downpayment) - if so, mark as enrolled
        $downpaymentAmount = (float) ($enrollment->downpayment_amount ?? $validated['downpayment_amount'] ?? 0);
        $isFirstPayment = $oldAmount == 0 && $newTotal > 0;
        $isDownpaymentPaid = $newTotal >= $downpaymentAmount && $downpaymentAmount > 0;
        
        // Advance enrollment status when admin makes a payment
        // Admin payment implies approval, so pending → enrolled directly
        if (in_array($enrollment->status, ['pending', 'approved']) && ($isFirstPayment || $isDownpaymentPaid || $validated['payment_status'] === 'paid')) {
            if ($enrollment->status === 'pending') {
                $updateData['approved_at'] = now();
            }
            $updateData['status'] = 'enrolled';
            $updateData['enrolled_at'] = now();
            
            // Assign section only when student is officially enrolled (after payment)
            if ($enrollment->user_id) {
                $user = User::find($enrollment->user_id);
                if ($user) {
                    $this->assignSection($enrollment, $user);
                }
            }
        }

        $enrollment->update($updateData);

        // Create a payment record in StudentDocument for admin finance management payments
        $methodLabel = $validated['payment_method'] === 'gcash' ? 'GCash' : 'Cash';
        StudentDocument::create([
            'user_id'       => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'document_type' => 'payment_screenshot',
            'file_path'     => null,
            'original_name' => null,
            'mime_type'     => null,
            'file_size'     => null,
            'description'   => 'Admin payment via ' . $methodLabel . ' - ₱' . number_format($incomingAmount, 2) . ($validated['payment_reference'] ? ' (Ref: ' . $validated['payment_reference'] . ')' : ''),
            'status'        => 'approved',
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);

        // Create installment schedule records if this is an installment plan and they don't exist yet
        $enrollment->refresh();
        $isInstallment = $enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']);
        if ($isInstallment && $enrollment->monthly_amount > 0 && $enrollment->paymentInstallments()->count() === 0) {
            // Fix missing payment_type for legacy data
            if (!$enrollment->payment_type) {
                $enrollment->update(['payment_type' => 'installment']);
                $enrollment->refresh();
            }
            PaymentService::createInstallments($enrollment);

            // Set next installment date to first pending installment
            $firstInstallment = $enrollment->paymentInstallments()->orderBy('due_date')->first();
            if ($firstInstallment && !$enrollment->next_installment_date) {
                $enrollment->update(['next_installment_date' => $firstInstallment->due_date]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment updated successfully.']);
        }

        return redirect()->back()
            ->with('success', 'Payment information updated successfully.');
    }

    /**
     * Delete enrollment (admin only)
     */
    public function destroy(Enrollment $enrollment)
    {
        try {
            // Delete physical document files from storage, then remove DB rows
            $filePaths = DB::table('student_documents')
                ->where('enrollment_id', $enrollment->id)
                ->orWhere(function($q) use ($enrollment) {
                    if ($enrollment->user_id) {
                        $q->where('user_id', $enrollment->user_id);
                    }
                })
                ->pluck('file_path')
                ->filter()
                ->toArray();

            foreach ($filePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            DB::table('student_documents')
                ->where('enrollment_id', $enrollment->id)
                ->orWhere(function($q) use ($enrollment) {
                    if ($enrollment->user_id) {
                        $q->where('user_id', $enrollment->user_id);
                    }
                })
                ->delete();

            // Delete associated student account if linked
            if ($enrollment->user_id) {
                $linkedUser = User::find($enrollment->user_id);
                if ($linkedUser && $linkedUser->role === 'student') {
                    $this->removeStudentFromSections($linkedUser->id);
                    $linkedUser->enrollments()->delete();
                    $linkedUser->delete();
                }
            }

            $enrollment->delete();

            return redirect()->route('admin.enrollments.index')
                ->with('success', 'Enrollment record deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Change section assignment for an enrollment
     */
    public function changeSection(Request $request, Enrollment $enrollment)
    {
        try {
            $validated = $request->validate([
                'section_id' => 'required|exists:sections,id',
            ]);

            $section = \App\Models\Section::find($validated['section_id']);
            
            if (!$section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section not found.'
                ], 404);
            }

            // Check if section matches enrollment grade level
            if ($section->grade_level !== $enrollment->grade_level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section grade level does not match enrollment grade level.'
                ], 400);
            }

            // Check if section has available capacity
            if ($section->current_enrollment >= $section->max_students) {
                return response()->json([
                    'success' => false,
                    'message' => "Section {$section->name} is already full ({$section->max_students} students max)."
                ], 422);
            }

            // Update enrollment section
            $enrollment->update([
                'section' => $section->name,
            ]);

            // Update section_student pivot table
            $user = $enrollment->user;
            if ($user) {
                // Remove from old section
                DB::table('section_student')
                    ->where('user_id', $user->id)
                    ->delete();

                // Add to new section
                DB::table('section_student')->insert([
                    'section_id' => $section->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update old section's enrollment count (decrement)
                // We need to find the old section first
                $oldSection = \App\Models\Section::where('name', $enrollment->getOriginal('section'))
                    ->where('grade_level', $enrollment->grade_level)
                    ->first();
                if ($oldSection) {
                    $oldSection->decrement('current_enrollment');
                }

                // Update new section's enrollment count (increment)
                $section->increment('current_enrollment');
            }

            return response()->json([
                'success' => true,
                'message' => 'Section changed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Section change error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error changing section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle React form submission (REST API)
     */
    public function submitApi(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'zipCode' => 'required|string|max:10',
                'guardianName' => 'required|string|max:255',
                'guardianRelationship' => 'required|in:Parent,Guardian,Other',
                'guardianPhone' => 'required|string|max:20',
                'guardianEmail' => 'nullable|email|max:255',
                'previousSchool' => 'required|string|max:255',
                'lastGradeLevel' => 'required|string|max:255',
                'transferReason' => 'required|string|max:500',
                'gradeLevel' => 'required|string|max:255',
                'schoolYear' => 'required|string|max:20',
                'termsAccepted' => 'required|boolean',
            ]);

            // Create enrollment record with React form data
            $enrollmentData = [
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'zip_code' => $validated['zipCode'],
                'guardian_name' => $validated['guardianName'],
                'guardian_relationship' => $validated['guardianRelationship'],
                'guardian_phone' => $validated['guardianPhone'],
                'guardian_email' => $validated['guardianEmail'],
                'previous_school' => $validated['previousSchool'],
                'last_grade_level' => $validated['lastGradeLevel'],
                'transfer_reason' => $validated['transferReason'],
                'grade_level' => $validated['gradeLevel'],
                'school_year' => $validated['schoolYear'],
                'terms_accepted' => $validated['termsAccepted'],
            ];

            // Create or get user so enrollment is linked and visible in admin dashboard
            $user = User::withTrashed()->where('email', $validated['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['firstName'] . ' ' . $validated['lastName'],
                    'email' => $validated['email'],
                    'password' => bcrypt(Str::random(10)),
                    'role' => 'student',
                    'is_active' => false,
                ]);
            } elseif ($user->trashed()) {
                $user->restore();
                $user->update([
                    'name' => $validated['firstName'] . ' ' . $validated['lastName'],
                    'role' => 'student',
                    'is_active' => false,
                ]);
            }

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'reference_number' => 'ENR-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'grade_level' => $validated['gradeLevel'] ?? null,
                'school_year' => Setting::get('current_school_year') ?: $this->getCurrentSchoolYear(),
                'student_data' => array_merge($enrollmentData, ['student_type' => $request->input('studentType', 'new')]),
                'payment_status' => 'pending',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enrollment application submitted successfully!',
                'reference_number' => $enrollment->reference_number
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting enrollment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a student document (admin)
     */
    public function approveDocument(Request $request, StudentDocument $document)
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // If it's a payment screenshot, always process payment on approval
        if ($document->document_type === 'payment_screenshot' && $document->enrollment_id) {
            $enrollment = Enrollment::find($document->enrollment_id);
            if ($enrollment) {
                DB::beginTransaction();
                try {
                    // Get payment amount from linked installment or calculate from document
                    $paymentAmount = 0;
                    $installment = $document->paymentInstallment;
                    
                    if ($installment) {
                        $paymentAmount = $installment->total_due;
                        
                        // Mark installment as paid
                        $installment->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'amount_paid' => $paymentAmount,
                        ]);
                    } elseif ($enrollment->payment_type === 'installment') {
                        // For installment enrollments without a linked installment
                        $desc = $document->description ?? '';
                        preg_match('/₱([\d,]+\.?\d*)/', $desc, $matches);
                        $docAmount = isset($matches[1]) ? (float) str_replace(',', '', $matches[1]) : 0;

                        // Check if this is a downpayment (not yet fully paid)
                        $downpaymentAmount = (float) ($enrollment->downpayment_amount ?? 0);
                        $alreadyPaid = (float) ($enrollment->payment_amount ?? 0);
                        $isDownpayment = $downpaymentAmount > 0 && $alreadyPaid < $downpaymentAmount;

                        if ($isDownpayment) {
                            // Downpayment - don't link to any installment, just use the amount
                            $paymentAmount = $docAmount > 0 ? $docAmount : $downpaymentAmount;
                        } else {
                            // Not a downpayment - try to find a pending installment matching the payment amount
                            $installment = $enrollment->paymentInstallments()
                                ->where('status', 'pending')
                                ->whereRaw('CAST(amount AS DECIMAL(10,2)) = ?', [$docAmount])
                                ->orderBy('due_date')
                                ->first();

                            // If no exact match, try the next pending installment
                            if (!$installment && $docAmount > 0) {
                                $installment = $enrollment->paymentInstallments()
                                    ->where('status', 'pending')
                                    ->orderBy('due_date')
                                    ->first();
                            }

                            if ($installment) {
                                $totalDue = (float) $installment->amount + (float) ($installment->late_fee ?? 0);
                                $paymentAmount = $docAmount > 0 ? $docAmount : $totalDue;

                                // Link document and mark installment as paid
                                $installment->update([
                                    'status' => 'paid',
                                    'paid_at' => now(),
                                    'payment_method' => (stripos($desc, 'gcash') !== false) ? 'gcash' : 'cash',
                                    'document_id' => $document->id,
                                    'amount_paid' => $paymentAmount,
                                ]);
                            } else {
                                // Fallback: use amount from description
                                $paymentAmount = $docAmount > 0 ? $docAmount : ((float) ($enrollment->downpayment_amount ?? 0) ?: (float) ($enrollment->monthly_amount ?? 0));
                            }
                        }
                    } else {
                        // For non-installment payments, try to get amount from description or use monthly amount
                        $paymentAmount = $enrollment->downpayment_amount ?? $enrollment->monthly_amount ?? 0;
                    }

                    // Add payment amount to enrollment's total paid (cast to float for proper comparison)
                    $currentPaid = (float) ($enrollment->payment_amount ?? 0);
                    $paymentAmountFloat = (float) $paymentAmount;
                    $newTotalPaid = $currentPaid + $paymentAmountFloat;
                    $totalFee = (float) ($enrollment->total_fee ?? 0);
                    $remainingBalance = max(0, $totalFee - $newTotalPaid);

                    // Determine payment status based on remaining balance
                    $paymentStatus = ($remainingBalance <= 0) ? 'paid' : 'partial';

                    $updateData = [
                        'payment_amount' => $newTotalPaid,
                        'payment_status' => $paymentStatus,
                        'remaining_balance' => $remainingBalance,
                        'payment_updated_at' => now(),
                    ];

                    // Update next installment date for installment plans
                    if ($enrollment->payment_type === 'installment') {
                        if ($remainingBalance <= 0) {
                            $updateData['next_installment_date'] = null;
                        } else {
                            $nextPending = $enrollment->paymentInstallments()
                                ->where('status', 'pending')
                                ->orderBy('due_date')
                                ->first();
                            if ($nextPending) {
                                $updateData['next_installment_date'] = $nextPending->due_date;
                            }
                        }
                    }

                    // Update enrollment with new payment info
                    $enrollment->update($updateData);

                    // If enrollment is still 'approved' or 'pending', upgrade to 'enrolled'
                    if (in_array($enrollment->status, ['approved', 'pending']) && $newTotalPaid > 0) {
                        $enrollment->update([
                            'status' => 'enrolled',
                            'enrolled_at' => now(),
                        ]);

                        // Assign student to section
                        if ($enrollment->user_id) {
                            $user = User::find($enrollment->user_id);
                            if ($user) {
                                $this->assignSection($enrollment, $user);
                            }
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Payment approval failed: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to process payment: ' . $e->getMessage());
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Document approved successfully.']);
        }
        return redirect()->back()->with('success', 'Document approved successfully.');
    }

    /**
     * Reject a student document (admin)
     */
    public function rejectDocument(Request $request, StudentDocument $document)
    {
        $request->validate(['reject_reason' => 'required|string|max:500']);

        $document->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Document rejected.']);
        }
        return redirect()->back()->with('success', 'Document rejected.');
    }

    /**
     * Delete a student document (admin)
     */
    public function deleteDocument(StudentDocument $document)
    {
        // Only allow deletion of pending documents
        if ($document->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending documents can be deleted.');
        }

        DB::beginTransaction();
        try {
            // If there's a linked installment, update it to remove the document link
            if ($document->paymentInstallment) {
                $document->paymentInstallment->update([
                    'document_id' => null,
                    'status' => 'pending',
                    'paid_at' => null,
                ]);
            }

            // Delete the document file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Soft delete the document
            $document->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }

    /**
     * Show student details (admin API)
     */
    public function showStudent(User $user)
    {
        $user->load(['latestEnrollment', 'profile', 'guardian', 'address', 'previousSchool']);

        $gradeMap = [
            'nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2',
            'grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'
        ];
        $enr = $user->latestEnrollment;
        $sd = $enr ? $enr->student_data : [];
        $gradeRaw = $sd['grade_level'] ?? ($enr ? $enr->grade_level : null);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'lrn' => $user->lrn,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at->format('M d, Y'),
            'enrollment' => $enr ? [
                'id' => $enr->id,
                'reference_number' => $enr->reference_number,
                'status' => $enr->status,
                'grade_level' => $gradeMap[$gradeRaw] ?? ($gradeRaw ?: 'N/A'),
                'section' => $enr->section ?? 'Unassigned',
                'payment_status' => $enr->payment_status ?? 'pending',
                'payment_amount' => $enr->payment_amount ?? 0,
                'payment_method' => $enr->payment_method,
            ] : null,
            'student_data' => $sd,
            'profile' => $user->profile,
            'guardian' => $user->guardian,
            'address' => $user->address,
            'previous_school' => $user->previousSchool,
        ]);
    }

    /**
     * Index students with filters (for add student to section modal)
     */
    public function indexStudents(Request $request)
    {
        $paymentStatus = $request->query('payment_status');
        $gradeLevel = $request->query('grade_level');
        $enrollmentStatus = $request->query('enrollment_status');
        $sort = $request->query('sort', 'name_asc');
        $page = $request->query('page', 1);

        $allowedStatuses = $enrollmentStatus && in_array($enrollmentStatus, ['approved', 'enrolled'])
            ? [$enrollmentStatus]
            : ['approved', 'enrolled'];

        $enrollmentQuery = Enrollment::whereNotNull('user_id')
            ->whereIn('status', $allowedStatuses);

        // Filter by payment status (supports comma-separated values)
        // Also supports 'approved' as a special value meaning enrollment status=approved
        if ($paymentStatus) {
            $statuses = explode(',', $paymentStatus);
            $paymentStatuses = array_filter($statuses, fn($s) => in_array($s, ['paid', 'partial', 'pending']));
            $includeApproved = in_array('approved', $statuses);

            if ($paymentStatuses) {
                $enrollmentQuery->where(function($q) use ($paymentStatuses, $includeApproved) {
                    $q->whereIn('payment_status', $paymentStatuses);
                    if ($includeApproved) {
                        $q->orWhere('status', 'approved');
                    }
                });
            } elseif ($includeApproved) {
                // Only 'approved' was requested — show approved enrollments regardless of payment
                // No additional filter needed since we already filter by status
            }
        }

        // Filter by grade level
        if ($gradeLevel) {
            $enrollmentQuery->where('grade_level', $gradeLevel);
        }

        // Get the latest enrollment for each user
        $enrollments = $enrollmentQuery
            ->orderBy('user_id')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('user_id');

        // Get user data for these enrollments
        $userIds = $enrollments->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)
            ->where('role', 'student')
            ->select('id', 'name', 'email', 'is_active', 'created_at', 'lrn')
            ->get()
            ->keyBy('id');

        $data = $enrollments->map(function($enrollment) use ($users) {
            $user = $users->get($enrollment->user_id);
            if (!$user) return null;

            $sd = is_array($enrollment->student_data) ? $enrollment->student_data : json_decode($enrollment->student_data, true) ?? [];
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'lrn' => $user->lrn,
                'is_active' => $user->is_active,
                'status' => $enrollment->status,
                'payment_status' => $enrollment->payment_status,
                'grade_level' => $sd['grade_level'] ?? $enrollment->grade_level,
                'section' => $enrollment->section,
            ];
        })->filter()->values();

        // Apply sorting
        switch ($sort) {
            case 'name_desc':
                $data = $data->sortByDesc('name')->values();
                break;
            case 'grade_asc':
                $data = $data->sortBy('grade_level')->values();
                break;
            case 'grade_desc':
                $data = $data->sortByDesc('grade_level')->values();
                break;
            case 'name_asc':
            default:
                $data = $data->sortBy('name')->values();
                break;
        }

        // Paginate manually
        $perPage = 15;
        $total = $data->count();
        $offset = ($page - 1) * $perPage;
        $paginatedData = $data->slice($offset, $perPage)->values();

        return response()->json([
            'data' => $paginatedData,
            'students' => $paginatedData,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    /**
     * Update student (admin)
     */
    public function updateStudent(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'lrn' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'grade_level' => 'nullable|in:nursery,kindergarten,grade1,grade2,grade3,grade4,grade5,grade6',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'lrn' => $validated['lrn'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Update enrollment grade level if provided
        if (isset($validated['grade_level'])) {
            $enrollment = $user->latestEnrollment;
            if ($enrollment) {
                // Update both the grade_level column and student_data
                $studentData = $enrollment->student_data ?? [];
                $studentData['grade_level'] = $validated['grade_level'];
                
                $enrollment->update([
                    'grade_level' => $validated['grade_level'],
                    'student_data' => $studentData,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Student updated successfully.']);
    }

    /**
     * Delete student (admin)
     */
    private function removeStudentFromSections(int $userId): void
    {
        $sectionIds = DB::table('section_student')->where('user_id', $userId)->pluck('section_id');
        if ($sectionIds->isEmpty()) return;

        DB::table('section_student')->where('user_id', $userId)->delete();

        // Recalculate each affected section's live count (only non-deleted users)
        foreach ($sectionIds as $sectionId) {
            $liveCount = DB::table('section_student')
                ->join('users', 'users.id', '=', 'section_student.user_id')
                ->whereNull('users.deleted_at')
                ->where('section_student.section_id', $sectionId)
                ->count();
            \App\Models\Section::where('id', $sectionId)->update(['current_enrollment' => $liveCount]);
        }
    }

    /**
     * Archive a student (soft delete) — data is preserved and can be restored.
     */
    public function deleteStudent(User $user)
    {
        $this->removeStudentFromSections($user->id);
        $user->delete(); // soft delete — sets deleted_at
        return response()->json(['success' => true, 'message' => 'Student archived successfully. You can restore them from the Archives tab.']);
    }

    /**
     * Restore an archived student.
     */
    public function restoreStudent($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['success' => true, 'message' => 'Student restored successfully.']);
    }

    /**
     * Permanently delete a student — removes all records and files. Cannot be undone.
     */
    public function forceDeleteStudent($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $enrollmentIds = $user->enrollments()->pluck('id')->toArray();

        // Delete physical document files
        $filePaths = DB::table('student_documents')
            ->where('user_id', $user->id)
            ->orWhere(function($q) use ($enrollmentIds) {
                if (!empty($enrollmentIds)) $q->whereIn('enrollment_id', $enrollmentIds);
            })
            ->pluck('file_path')->filter()->toArray();
        foreach ($filePaths as $path) {
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
        }

        // Delete profile photo
        $profile = $user->profile;
        if ($profile && $profile->photo && Storage::disk('public')->exists($profile->photo)) {
            Storage::disk('public')->delete($profile->photo);
        }

        // Hard-delete all related records
        DB::table('student_documents')
            ->where('user_id', $user->id)
            ->orWhere(function($q) use ($enrollmentIds) {
                if (!empty($enrollmentIds)) $q->whereIn('enrollment_id', $enrollmentIds);
            })->delete();
        DB::table('payment_installments')->whereIn('enrollment_id', $enrollmentIds)->delete();
        DB::table('payment_transactions')->where('user_id', $user->id)->delete();
        DB::table('otp_verifications')->where('email', $user->email)->delete();
        DB::table('activity_logs')->where('user_id', $user->id)->delete();
        Grade::where('student_id', $user->id)->delete();
        \App\Models\Promotion::where('student_id', $user->id)->delete();
        \App\Models\GuidanceRecord::where('student_id', $user->id)->delete();
        DB::table('student_profiles')->where('user_id', $user->id)->delete();
        DB::table('student_addresses')->where('user_id', $user->id)->delete();
        DB::table('guardians')->where('user_id', $user->id)->delete();
        DB::table('previous_schools')->where('user_id', $user->id)->delete();
        $user->enrollments()->forceDelete();
        $user->forceDelete();

        return response()->json(['success' => true, 'message' => 'Student permanently deleted.']);
    }

    /**
     * Mass promote students to next grade level
     */
    public function massPromote(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:users,id',
            'from_school_year' => 'required|string|max:20',
            'to_school_year' => 'required|string|max:20',
            'from_grade' => 'required|string|max:30',
        ]);

        $studentIds = $validated['student_ids'];
        $toSchoolYear = $validated['to_school_year'];
        $fromGrade = $validated['from_grade'];

        // Grade promotion mapping
        $gradeMap = [
            'nursery'      => 'kindergarten',
            'kindergarten' => 'grade1',
            'grade1'       => 'grade2',
            'grade2'       => 'grade3',
            'grade3'       => 'grade4',
            'grade4'       => 'grade5',
            'grade5'       => 'grade6',
            'grade6'       => 'graduated',
        ];

        $nextGrade = $gradeMap[$fromGrade] ?? null;
        if (!$nextGrade) {
            return response()->json(['success' => false, 'message' => 'Invalid grade level or already graduated.'], 422);
        }

        $promotedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($studentIds as $studentId) {
            try {
                $user = User::find($studentId);
                if (!$user) continue;

                // Check if student already has an enrollment for the target school year
                $existingEnrollment = Enrollment::where('user_id', $studentId)
                    ->where('school_year', $toSchoolYear)
                    ->first();

                if ($existingEnrollment) {
                    $skippedCount++;
                    // Log skipped promotion for audit
                    Promotion::create([
                        'student_id' => $studentId,
                        'lrn' => $user->lrn,
                        'from_grade' => $fromGrade,
                        'to_grade' => $nextGrade,
                        'from_school_year' => $validated['from_school_year'],
                        'to_school_year' => $toSchoolYear,
                        'promoted_by' => Auth::id(),
                        'promoted_at' => now(),
                        'status' => 'skipped',
                        'error_message' => 'Already has an enrollment for ' . $toSchoolYear,
                    ]);
                    continue;
                }

                // Get latest enrollment
                $latestEnrollment = $user->latestEnrollment;

                // Update existing enrollment status to completed
                if ($latestEnrollment) {
                    $latestEnrollment->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    // Remove from old section if assigned
                    $oldSectionName = $latestEnrollment->section;
                    if ($oldSectionName) {
                        $oldSection = Section::where('name', $oldSectionName)
                            ->where('grade_level', $latestEnrollment->grade_level)
                            ->where('school_year', $validated['from_school_year'])
                            ->first();
                        if ($oldSection) {
                            $oldSection->students()->detach($user->id);
                            $oldSection->current_enrollment = $oldSection->students()->count();
                            $oldSection->save();
                        }
                    }
                }

                // Find default section in new grade for new school year (lowest enrollment)
                $defaultSection = Section::where('grade_level', $nextGrade)
                    ->where('school_year', $toSchoolYear)
                    ->where('is_active', true)
                    ->orderBy('current_enrollment', 'asc')
                    ->first();

                $sectionName = $defaultSection ? $defaultSection->name : null;

                // Create new enrollment for next school year
                $newEnrollment = Enrollment::create([
                    'user_id' => $studentId,
                    'grade_level' => $nextGrade,
                    'school_year' => $toSchoolYear,
                    'status' => 'pending',
                    'section' => $sectionName,
                    'student_data' => $latestEnrollment ? array_merge(
                        $latestEnrollment->student_data ?? [],
                        ['grade_level' => $nextGrade, 'previous_school_year' => $validated['from_school_year']]
                    ) : ['grade_level' => $nextGrade],
                    'payment_status' => 'pending',
                ]);

                // Assign to new section if found
                if ($defaultSection) {
                    $defaultSection->students()->attach($user->id);
                    $defaultSection->current_enrollment = $defaultSection->students()->count();
                    $defaultSection->save();
                }

                // Record promotion in promotions table
                Promotion::create([
                    'student_id' => $studentId,
                    'lrn' => $user->lrn,
                    'from_grade' => $fromGrade,
                    'to_grade' => $nextGrade,
                    'from_school_year' => $validated['from_school_year'],
                    'to_school_year' => $toSchoolYear,
                    'from_section_id' => $oldSection ? $oldSection->id : null,
                    'to_section_id' => $defaultSection ? $defaultSection->id : null,
                    'promoted_by' => Auth::id(),
                    'promoted_at' => now(),
                    'status' => 'completed',
                ]);

                $promotedCount++;
            } catch (\Exception $e) {
                $errors[] = "Student ID $studentId: " . $e->getMessage();
                Log::error("Mass promotion failed for student $studentId: " . $e->getMessage());

                // Record failed promotion attempt
                try {
                    Promotion::create([
                        'student_id' => $studentId,
                        'lrn' => $user ? $user->lrn : null,
                        'from_grade' => $fromGrade,
                        'to_grade' => $nextGrade,
                        'from_school_year' => $validated['from_school_year'],
                        'to_school_year' => $toSchoolYear,
                        'promoted_by' => Auth::id(),
                        'promoted_at' => now(),
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                } catch (\Exception $logErr) {
                    Log::error("Failed to log promotion error for student $studentId: " . $logErr->getMessage());
                }
            }
        }

        $message = "$promotedCount student(s) promoted successfully to " . ucfirst(str_replace('grade', 'Grade ', $nextGrade)) . " for $toSchoolYear.";
        if ($skippedCount > 0) {
            $message .= " $skippedCount student(s) were skipped because they already have an enrollment for $toSchoolYear.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'promoted_count' => $promotedCount,
            'skipped_count' => $skippedCount,
            'next_grade' => $nextGrade,
            'errors' => $errors,
        ]);
    }

    /**
     * Get promotion history
     */
    public function promotionHistory(Request $request)
    {
        $query = Promotion::with(['student:id,name,lrn', 'fromSection:id,name', 'toSection:id,name', 'promotedBy:id,name'])
            ->orderByDesc('promoted_at');

        if ($request->has('school_year')) {
            $query->where('to_school_year', $request->get('school_year'));
        }
        if ($request->has('grade')) {
            $query->where('from_grade', $request->get('grade'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $perPage = $request->get('per_page', 20);
        $promotions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $promotions,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // ASSESSMENT & PROMOTION (per-student)
    // ─────────────────────────────────────────────────────────────

    /**
     * List enrolled students from the current school year as promotion candidates
     */
    public function assessmentCandidates(Request $request)
    {
        $schoolYear = $request->get('school_year', $this->getCurrentSchoolYear());
        $gradeFilter = $request->get('grade');

        $query = Enrollment::with(['user:id,name,lrn,email'])
            ->where('school_year', $schoolYear)
            ->whereIn('status', ['enrolled', 'approved'])
            ->when($gradeFilter, fn($q) => $q->where('grade_level', $gradeFilter))
            ->orderBy('grade_level')
            ->orderBy('section');

        $enrollments = $query->get()->map(function ($e) use ($schoolYear) {
            // Check if already assessed/promoted for NEXT year
            $nextYear = $this->nextSchoolYear($schoolYear);
            $promoted = Promotion::where('student_id', $e->user_id)
                ->where('from_school_year', $schoolYear)
                ->first();
            $hasNextEnrollment = Enrollment::where('user_id', $e->user_id)
                ->where('school_year', $nextYear)
                ->exists();

            return [
                'enrollment_id'   => $e->id,
                'student_id'      => $e->user_id,
                'name'            => $e->user?->name ?? '—',
                'lrn'             => $e->user?->lrn ?? '',
                'grade_level'     => $e->grade_level,
                'section'         => $e->section ?? '—',
                'status'          => $e->status,
                'promotion_status'=> $promoted?->status ?? null,
                'to_grade'        => $promoted?->to_grade ?? null,
                'already_enrolled_next' => $hasNextEnrollment,
            ];
        });

        return response()->json(['success' => true, 'data' => $enrollments, 'school_year' => $schoolYear]);
    }

    /**
     * Promote or retain a single student — creates Promotion record + pending enrollment for next year
     */
    public function assessPromotion(Request $request, User $user)
    {
        $validated = $request->validate([
            'action'          => 'required|in:promote,retain',
            'from_school_year'=> 'nullable|string|max:20',
            'to_school_year'  => 'required|string|max:20',
            'to_section_id'   => 'nullable|exists:sections,id',
        ]);

        $gradeMap = [
            'nursery' => 'kindergarten', 'kindergarten' => 'grade1',
            'grade1'  => 'grade2',       'grade2'        => 'grade3',
            'grade3'  => 'grade4',       'grade4'        => 'grade5',
            'grade5'  => 'grade6',       'grade6'        => 'graduated',
        ];

        // Get student's current enrollment — scoped by school year if provided, else latest
        $enrollmentQuery = Enrollment::where('user_id', $user->id)
            ->whereIn('status', ['enrolled', 'approved', 'completed']);

        if (!empty($validated['from_school_year'])) {
            $enrollmentQuery->where('school_year', $validated['from_school_year']);
        }

        $currentEnrollment = $enrollmentQuery->latest('id')->first();

        if (!$currentEnrollment) {
            return response()->json(['success' => false, 'message' => 'No active enrollment found for this student.'], 422);
        }

        $fromGrade = $currentEnrollment->grade_level;
        $toGrade   = $validated['action'] === 'promote'
            ? ($gradeMap[$fromGrade] ?? null)
            : $fromGrade; // retain = same grade

        if ($validated['action'] === 'promote' && !$toGrade) {
            return response()->json(['success' => false, 'message' => 'Student is at the last grade level or already graduated.'], 422);
        }

        // Check if already has enrollment for next year
        $existingNext = Enrollment::where('user_id', $user->id)
            ->where('school_year', $validated['to_school_year'])
            ->first();

        if ($existingNext) {
            return response()->json(['success' => false, 'message' => 'Student already has an enrollment for ' . $validated['to_school_year'] . '.'], 422);
        }

        $toSectionId   = $validated['to_section_id']   ?? null;
        $fromSchoolYear = $validated['from_school_year'] ?? null;
        $toSchoolYear   = $validated['to_school_year'];

        \DB::beginTransaction();
        try {
            // Find or use specified section for new year
            $toSection = null;
            if ($toSectionId) {
                $toSection = Section::find($toSectionId);
            } else {
                $toSection = Section::where('grade_level', $toGrade)
                    ->where('school_year', $toSchoolYear)
                    ->where('is_active', true)
                    ->orderBy('current_enrollment', 'asc')
                    ->first();
            }

            // Mark old enrollment as completed
            $currentEnrollment->update(['status' => 'completed', 'completed_at' => now()]);

            // Remove from old section
            $oldSection = $currentEnrollment->section
                ? Section::where('name', $currentEnrollment->section)
                    ->where('grade_level', $fromGrade)
                    ->where('school_year', $fromSchoolYear)
                    ->first()
                : null;
            if ($oldSection) {
                $oldSection->students()->detach($user->id);
                $oldSection->current_enrollment = $oldSection->students()->count();
                $oldSection->save();
            }

            // Create new pending enrollment
            $newEnrollment = Enrollment::create([
                'user_id'      => $user->id,
                'grade_level'  => $toGrade,
                'school_year'  => $toSchoolYear,
                'status'       => 'pending',
                'section'      => $toSection?->name,
                'student_data' => array_merge(
                    $currentEnrollment->student_data ?? [],
                    [
                        'grade_level'          => $toGrade,
                        'student_type'         => 'returning',
                        'previous_school_year' => $fromSchoolYear,
                    ]
                ),
                'payment_status'    => 'pending',
                'reference_number'  => 'ENR-' . strtoupper(uniqid()),
            ]);

            // Attach to new section
            if ($toSection) {
                $toSection->students()->attach($user->id);
                $toSection->current_enrollment = $toSection->students()->count();
                $toSection->save();
            }

            // Record promotion
            Promotion::create([
                'student_id'       => $user->id,
                'lrn'              => $user->lrn,
                'from_grade'       => $fromGrade,
                'to_grade'         => $toGrade,
                'from_school_year' => $currentEnrollment->school_year,
                'to_school_year'   => $toSchoolYear,
                'from_section_id'  => $oldSection?->id,
                'to_section_id'    => $toSection?->id,
                'promoted_by'      => Auth::id(),
                'promoted_at'      => now(),
                'status'           => 'completed',
            ]);

            \DB::commit();

            $actionLabel = $validated['action'] === 'promote'
                ? "Promoted to " . ucfirst(str_replace('_', ' ', $toGrade))
                : "Retained at " . ucfirst(str_replace('_', ' ', $toGrade));

            return response()->json([
                'success'       => true,
                'message'       => $user->name . ' — ' . $actionLabel . '. Enrollment created for ' . $toSchoolYear . '.',
                'enrollment_id' => $newEnrollment->id,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return sections available for a given grade level and school year
     */
    public function sectionsForGrade(Request $request)
    {
        $sections = Section::where('grade_level', $request->grade)
            ->where('school_year', $request->school_year)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'current_enrollment', 'max_students']);

        return response()->json(['success' => true, 'data' => $sections]);
    }

    /**
     * Change enrollment status (admin — supports dropped, ghost, transferred, etc.)
     */
    public function changeEnrollmentStatus(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'status'  => 'required|in:pending,approved,enrolled,declined,completed,dropped,ghost,transferred',
            'remarks' => 'nullable|string|max:500',
        ]);

        $enrollment->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . ucfirst($request->status) . '.',
        ]);
    }

    /**
     * Return submitted grades grouped by subject for the assess modal
     */
    public function getStudentGradesForAssess(User $user)
    {
        $enrollment = $user->enrollments()
            ->whereNotIn('status', ['declined'])
            ->orderByRaw("school_year DESC")
            ->latest('id')
            ->first();

        $gradeLevel = $enrollment ? ($enrollment->grade_level ?? ($enrollment->student_data['grade_level'] ?? null)) : null;
        $schoolYear = $enrollment ? $enrollment->school_year : null;

        // All subjects for this grade level
        $subjects = $gradeLevel
            ? \App\Models\Subject::where('grade_level', $gradeLevel)->where('is_active', true)->orderBy('name')->get()
            : collect();

        // All grade records for this student (any status)
        $grades = Grade::where('student_id', $user->id)
            ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear))
            ->with('subject:id,name')
            ->get();

        $gradesBySubjectTerm = $grades->groupBy('subject_id')->map(fn($rows) => $rows->keyBy('term'));

        $isDescriptive = \App\Models\Grade::isNurseryKinder($gradeLevel ?? '');

        $result = $subjects->map(function ($subject) use ($gradesBySubjectTerm, $isDescriptive) {
            $termGrades = $gradesBySubjectTerm->get($subject->id, collect());
            $t1 = $termGrades->get(1);
            $t2 = $termGrades->get(2);
            $t3 = $termGrades->get(3);

            $statuses = $termGrades->pluck('status')->unique()->values()->toArray();
            $status = 'not_entered';
            if (in_array('approved', $statuses))      $status = 'approved';
            elseif (in_array('submitted', $statuses)) $status = 'submitted';
            elseif (in_array('draft', $statuses))     $status = 'draft';

            if ($isDescriptive) {
                return [
                    'subject'          => $subject->name,
                    'term1'            => $t1?->descriptive_grade,
                    'term2'            => $t2?->descriptive_grade,
                    'term3'            => $t3?->descriptive_grade,
                    'average'          => null,
                    'remarks'          => $termGrades->sortByDesc('term')->first()?->remarks ?? null,
                    'status'           => $status,
                    'is_descriptive'   => true,
                ];
            }

            $vals = collect([$t1?->grade, $t2?->grade, $t3?->grade])->filter()->values();
            $avg  = $vals->count() ? round($vals->avg(), 2) : null;

            return [
                'subject'        => $subject->name,
                'term1'          => $t1?->grade,
                'term2'          => $t2?->grade,
                'term3'          => $t3?->grade,
                'average'        => $avg,
                'remarks'        => $termGrades->sortByDesc('term')->first()?->remarks ?? null,
                'status'         => $status,
                'is_descriptive' => false,
            ];
        })->values();

        // If no subjects found for grade level, fall back to whatever grade records exist
        if ($result->isEmpty() && $grades->isNotEmpty()) {
            $result = $grades->groupBy('subject_id')->map(function ($rows) {
                $byTerm = $rows->keyBy('term');
                $vals   = $rows->pluck('grade')->filter()->values();
                $statuses = $rows->pluck('status')->unique()->values()->toArray();
                $status = 'not_entered';
                if (in_array('approved', $statuses))   $status = 'approved';
                elseif (in_array('submitted', $statuses)) $status = 'submitted';
                elseif (in_array('draft', $statuses))  $status = 'draft';
                return [
                    'subject' => optional($rows->first()->subject)->name ?? 'Unknown',
                    'term1'   => optional($byTerm->get(1))->grade,
                    'term2'   => optional($byTerm->get(2))->grade,
                    'term3'   => optional($byTerm->get(3))->grade,
                    'average' => $vals->count() ? round($vals->avg(), 2) : null,
                    'remarks' => $rows->sortByDesc('term')->first()?->remarks ?? null,
                    'status'  => $status,
                ];
            })->values();
        }

        return response()->json([
            'grades'      => $result,
            'grade_level' => $gradeLevel,
            'school_year' => $schoolYear,
        ]);
    }

    /**
     * Return non-payment documents for the assess modal
     */
    public function getStudentDocumentsForAssess(User $user)
    {
        $documents = StudentDocument::where('user_id', $user->id)
            ->where('document_type', '!=', 'payment_screenshot')
            ->orderByDesc('created_at')
            ->get(['id', 'document_type', 'original_name', 'file_path', 'status', 'reject_reason', 'created_at'])
            ->toArray();

        return response()->json(['documents' => $documents]);
    }

    private function nextSchoolYear(string $schoolYear): string
    {
        $parts = explode('-', $schoolYear);
        if (count($parts) === 2) {
            return ($parts[0] + 1) . '-' . ($parts[1] + 1);
        }
        return $schoolYear;
    }

    /**
     * Get current school year — cached for 24 hours to avoid repeated DB hits
     */
    private function getCurrentSchoolYear(): string
    {
        return \Illuminate\Support\Facades\Cache::remember('current_school_year', 86400, function () {
            $latestSchoolYear = Section::where('is_active', true)
                ->orderByDesc('school_year')
                ->value('school_year');

            if ($latestSchoolYear) {
                return $latestSchoolYear;
            }

            $now  = now();
            $year = $now->month >= 6 ? $now->year : $now->year - 1;
            return $year . '-' . ($year + 1);
        });
    }

    // ── GRADE OVERSIGHT ──────────────────────────────────────────────

    public function pendingGrades(Request $request)
    {
        $groups = Grade::with(['student:id,name,lrn', 'teacher:id,name', 'subject:id,name,code'])
            ->where('status', 'submitted')
            ->get()
            ->groupBy(fn($g) => implode('|', [
                $g->teacher_id, $g->subject_id ?? 'null', $g->term, $g->school_year
            ]))
            ->map(fn($rows) => [
                'teacher'     => $rows->first()->teacher?->name ?? 'Unknown',
                'subject'     => $rows->first()->subject?->name ?? 'General',
                'term'        => $rows->first()->term,
                'school_year' => $rows->first()->school_year,
                'teacher_id'  => $rows->first()->teacher_id,
                'subject_id'  => $rows->first()->subject_id,
                'count'       => $rows->count(),
                'grades'      => $rows->map(fn($g) => [
                    'id'               => $g->id,
                    'student'          => $g->student?->name ?? 'Unknown',
                    'grade'            => $g->grade,
                    'descriptive_grade'=> $g->descriptive_grade,
                    'remarks'          => $g->remarks,
                ]),
            ])
            ->values();

        return response()->json(['success' => true, 'data' => $groups]);
    }

    public function approveGrades(Request $request)
    {
        $request->validate([
            'teacher_id'  => 'required|exists:users,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer',
            'school_year' => 'required|string',
        ]);

        $count = Grade::where('teacher_id', $request->teacher_id)
            ->where('term', $request->term)
            ->where('school_year', $request->school_year)
            ->where('status', 'submitted')
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->update(['status' => 'approved']);

        return response()->json(['success' => true, 'message' => "$count grade(s) approved."]);
    }

    public function rejectGrades(Request $request)
    {
        $request->validate([
            'teacher_id'  => 'required|exists:users,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer',
            'school_year' => 'required|string',
            'reason'      => 'nullable|string|max:500',
        ]);

        $count = Grade::where('teacher_id', $request->teacher_id)
            ->where('term', $request->term)
            ->where('school_year', $request->school_year)
            ->where('status', 'submitted')
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when(!$request->subject_id, fn($q) => $q->whereNull('subject_id'))
            ->update(['status' => 'rejected']);

        return response()->json(['success' => true, 'message' => "$count grade(s) returned to teacher."]);
    }

    /**
     * Generate SF10 (Learner's Permanent Academic Record) PDF for a student.
     */
    public function downloadSF10($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->load(['profile', 'guardian', 'address', 'previousSchool']);

        // All enrollments with subjects and grades
        $enrollments = \App\Models\Enrollment::where('user_id', $user->id)
            ->whereIn('status', ['enrolled', 'completed', 'approved'])
            ->orderBy('school_year')
            ->get();

        // Load subjects per enrollment via section relationship
        foreach ($enrollments as $enr) {
            $section = \App\Models\Section::where('name', $enr->section)
                ->where('grade_level', $enr->grade_level)
                ->first();
            $enr->setRelation('subjects', $section ? $section->subjects()->orderBy('name')->get() : collect());
        }

        // All grades for this student
        $grades = \App\Models\Grade::where('student_id', $user->id)
            ->with('subject')
            ->get();

        // School settings
        $settings = \App\Models\Setting::pluck('value', 'key');

        $sd = optional($enrollments->last())->student_data ?? [];
        $profile = $user->profile;
        $address = $user->address;
        $guardian = $user->guardian;

        // Encode school logo as base64 so DomPDF can embed it without remote access
        $logoPath = public_path('images/logo.png');
        $schoolLogoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        // DepEd logo (sun logo) — use same school logo if no separate file
        $depedLogoPath = public_path('images/deped_logo.png');
        if (!file_exists($depedLogoPath)) {
            $depedLogoPath = public_path('images/logo1.png');
        }
        $depedLogoBase64 = file_exists($depedLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($depedLogoPath))
            : $schoolLogoBase64;

        $data = [
            'student'             => $user,
            'enrollments'         => $enrollments,
            'grades'              => $grades,
            'schoolLogoBase64'    => $schoolLogoBase64,
            'depedLogoBase64'     => $depedLogoBase64,
            'schoolName'          => $settings['school_name'] ?? 'IEMELIF Learning Center',
            'schoolAddress'       => $settings['school_address'] ?? 'General Tinio, Nueva Ecija',
            'schoolYear'          => $settings['current_school_year'] ?? date('Y') . '-' . (date('Y') + 1),
            'principalName'       => $settings['principal_name'] ?? '',
            'totalTerms'          => (int)($settings['total_terms'] ?? 3),

            // Learner info — prefer profile, fall back to student_data
            'lastName'            => $profile->last_name   ?? $sd['last_name']   ?? $user->name,
            'firstName'           => $profile->first_name  ?? $sd['first_name']  ?? '',
            'middleName'          => $profile->middle_name ?? $sd['middle_name'] ?? '',
            'suffix'              => $profile->suffix       ?? $sd['suffix']      ?? '',
            'gender'              => $profile->gender       ?? $sd['gender']      ?? '',
            'birthdate'           => $profile->birthdate    ? \Carbon\Carbon::parse($profile->birthdate)->format('m/d/Y') : ($sd['birthdate'] ?? '—'),
            'placeOfBirth'        => $profile->place_of_birth ?? $sd['place_of_birth'] ?? '',
            'motherTongue'        => $sd['nationality'] ?? 'Filipino',
            'religiousAffiliation'=> $profile->religious_affiliation ?? $sd['religious_affiliation'] ?? '',
            'bloodType'           => $profile->blood_type   ?? $sd['blood_type']  ?? '',
            'allergies'           => $profile->allergies    ?? $sd['allergies']   ?? '',
            'lastSchool'          => $profile->last_school  ?? $sd['last_school'] ?? '',

            // Address
            'streetAddress'       => $address->street_address ?? $sd['street_address'] ?? '',
            'barangay'            => $address->barangay       ?? $sd['barangay']       ?? '',
            'city'                => $address->municipality   ?? $address->city ?? $sd['city'] ?? '',
            'province'            => $address->province       ?? $sd['province']       ?? '',

            // Parents / Guardian
            'motherName'          => $sd['mother_name']    ?? ($guardian ? ($guardian->relationship === 'mother' ? $guardian->name : '') : ''),
            'fatherName'          => $sd['father_name']    ?? ($guardian ? ($guardian->relationship === 'father' ? $guardian->name : '') : ''),
            'guardianName'        => $guardian->name        ?? $sd['guardian_name']   ?? '',
            'guardianRelation'    => $guardian->relationship ?? $sd['relationship']   ?? '',
            'guardianPhone'       => $guardian->contact     ?? $sd['guardian_phone']  ?? '',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sf10', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultPaperSize'     => 'letter',
            'marginTop'            => 1,
            'marginRight'          => 1,
            'marginBottom'         => 1,
            'marginLeft'           => 1,
        ]);

        $filename = 'SF10_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
