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
use App\Services\ActivityLogger;

class EnrollmentController extends Controller
{
    use \App\Support\Traits\BuildsSchoolPdf;

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

        // Rate limit: block if an OTP was sent in the last 60 seconds. Keyed on
        // created_at — comparing expires_at (sent + 3 min) against now-60s made
        // the wait about 4 minutes instead of the 60 seconds the message promises.
        $recent = OtpVerification::where('email', $email)
            ->where('created_at', '>', now()->subSeconds(60))
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
            'expires_at' => now()->addMinutes(3),
        ]);

        // Sent via the real queue (a supervised `queue:work` worker runs
        // alongside php-fpm/nginx on Railway — see docker/entrypoint.sh)
        // rather than dispatch(...)->afterResponse(). The job is durably
        // stored in the `jobs` table first, so if the worker ever fails or
        // gets killed mid-send, Laravel records the exception in
        // `failed_jobs` instead of the attempt just vanishing with no trace
        // — which is what we saw happen with afterResponse() on Railway.
        Log::info('OTP mail dispatch registered', ['email' => $email]);

        dispatch(function () use ($email, $otp) {
            Log::info('OTP mail closure entered', ['email' => $email]);
            try {
                Mail::to($email)->send(new EnrollmentOtpMail($otp, $email));
                Log::info('OTP mail sent', ['email' => $email]);
            } catch (\Throwable $e) {
                Log::error('OTP mail failed: ' . get_class($e) . ': ' . $e->getMessage(), ['email' => $email]);
            }
        });

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
            $anyForEmail = OtpVerification::where('email', $email)->latest()->first();
            Log::warning('Verify OTP: no unverified code found', [
                'email' => $email,
                'most_recent_row' => $anyForEmail ? [
                    'verified' => $anyForEmail->verified,
                    'attempts' => $anyForEmail->attempts,
                    'created_at' => $anyForEmail->created_at,
                    'expires_at' => $anyForEmail->expires_at,
                ] : null,
            ]);
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
            $remaining = max(0, 3 - $otp->attempts); // OtpVerification::hasExceededAttempts() allows 3
            if ($remaining === 0) {
                $otp->delete();
                return response()->json(['success' => false, 'message' => 'Incorrect code. No attempts left — please request a new code.'], 422);
            }
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
            Log::warning('Enrollment submission rejected: no matching verified OTP', ['email' => $email, 'otp_token' => $otpToken]);
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

            // Create or update student address. 'street' was never a real
            // column (street_address is) so this silently wrote nothing for
            // it, and 'city' was never populated at all — only its twin
            // 'municipality' column — confirmed via real student data showing
            // both empty. Both address-shaped columns are set here now so
            // any existing reader of either name gets the real value. See
            // DATABASE_NORMALIZATION_PLAN.md Phase 6.
            StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street_address' => $validated['street_address'],
                    'barangay' => $validated['barangay'],
                    'municipality' => $validated['city'],
                    'city' => $validated['city'],
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
                // 'cancelled' = an undone promotion (see undoPromotion()) —
                // doesn't represent a real claim on that school year either,
                // same reasoning as excluding 'declined'.
                ->whereNotIn('status', ['declined', 'cancelled'])
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
            Log::warning('Enrollment submission validation failed', ['errors' => $e->errors()]);
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

            // Defense in depth against the rare race window between the
            // duplicate-check above and this INSERT (two near-simultaneous
            // submissions) — surface the same friendly message the primary
            // check produces instead of a raw SQL error to the applicant.
            $message = ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000')
                ? 'You already have an enrollment application for S.Y. ' . ($schoolYear ?? '') . '.'
                : 'Submission failed: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            return redirect()->back()
                ->with('error', $message)
                ->withInput();
        }
    }

    /**
     * Calculate payment breakdown based on grade level and payment option.
     *
     * Used to hardcode every rate directly in this method, completely
     * ignoring fee_settings — meaning an admin editing Fee Management had
     * zero effect on what a real enrollment was actually charged, even
     * though FeeController's quote APIs (shown to admins/students before
     * they submit) DID read the DB and would show a different number. A
     * live ₱1 discrepancy on the Option A discount (DB said 1500, this
     * method charged 1501) was found this way. Now backed by the same
     * FeeCalculator (DATABASE_NORMALIZATION_PLAN.md Phase 2) every other
     * fee-quoting endpoint uses, so the number a parent is quoted always
     * matches what they're actually charged.
     */
    public function calculatePaymentBreakdown($gradeLevel, $paymentOption)
    {
        $calc = \App\Services\FeeCalculator::calculate($gradeLevel, $paymentOption);

        $breakdown = [
            'tuition_fee' => $calc['tuition'],
            'misc_reg_pta' => $calc['misc'],
            'books' => $calc['books'],
            'insurance' => $calc['insurance'],
            'electric_bill' => $calc['electric'],
            'base_total' => $calc['base_total'],
        ];

        if ($paymentOption === 'A') {
            $breakdown['discount'] = $calc['discount'];
            $breakdown['total_due'] = $calc['total_due'];
            $breakdown['payment_type'] = $calc['payment_type'];
        } elseif (in_array($paymentOption, ['B', 'C', 'D'], true)) {
            $breakdown['downpayment'] = $calc['downpayment'];
            $breakdown['monthly_amount'] = $calc['monthly_amount'];
            $breakdown['duration_months'] = $calc['duration_months'];
            $breakdown['total_due'] = $calc['total_due'];
            $breakdown['payment_type'] = $calc['payment_type'];
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

            if ($planError = \App\Services\FeeCalculator::planError($validated['grade_level'], $validated['payment_option'])) {
                return response()->json(['success' => false, 'message' => $planError, 'errors' => ['payment_option' => [$planError]]], 422);
            }

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

            // Create or update student address. 'street' was never a real
            // column (street_address is) so this silently wrote nothing for
            // it, and 'city' was never populated at all — only its twin
            // 'municipality' column — confirmed via real student data showing
            // both empty. Both address-shaped columns are set here now so
            // any existing reader of either name gets the real value. See
            // DATABASE_NORMALIZATION_PLAN.md Phase 6.
            StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street_address' => $validated['street_address'],
                    'barangay' => $validated['barangay'],
                    'municipality' => $validated['city'],
                    'city' => $validated['city'],
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
                // 'cancelled' = an undone promotion (see undoPromotion()) —
                // doesn't represent a real claim on that school year either,
                // same reasoning as excluding 'declined'.
                ->whereNotIn('status', ['declined', 'cancelled'])
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

            // Defense in depth against the rare race window between the
            // duplicate-check above and this INSERT — surface the same
            // friendly message the primary check produces instead of a raw
            // SQL error.
            $message = ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000')
                ? 'This student already has an enrollment application for S.Y. ' . ($schoolYear ?? '') . '. Please check the Enrollment Management section.'
                : 'Submission failed: ' . $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => $message
            ], 422);
        }
    }

    /**
     * Display admin enrollment management dashboard
     */
    public function adminIndex(Request $request)
    {
        // Get sort and filter parameters
        $sort = $request->get('sort', 'newest'); // default: newest first

        // ── 1. Enrollment records — moved to sectionEnrollment(), loaded
        // on-demand via GET /admin/section/enrollment. $sort stays here
        // (not moved) since Student Management's own sort dropdown and
        // query also read it — see docs/system-improvement-plan.md item #2.

        // ── 2. Recent Students for Dashboard (10 per page) ──
        // latestEnrollment eager-loaded — the dashboard.blade.php partial
        // derives Grade/Section from it (same pattern students-content.blade.php
        // already uses); without it every row showed "-" for both columns,
        // since grade/section live on Enrollment, not on the User row itself.
        $recentStudents = User::where('role', 'student')
            ->with('latestEnrollment')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'dashboard_page');

        // ── 3. Student Management & Payment Overview — moved to
        // sectionStudents(), loaded on-demand via GET /admin/section/students.
        // $allStudentsPayment (= the same query, aliased for the Finance tab)
        // removed along with it — Finance is permanently redirect-only dead
        // UI within the admin dashboard (see _PORTAL_SECTIONS in
        // showSection()), so nothing else needs it.

        // Archived students/teachers (soft-deleted) — the full paginated/
        // eager-loaded lists moved to sectionArchives(), loaded on-demand.
        // Only cheap counts stay here, for the sidebar "Archives" badge
        // shown on every page.
        $archivedStudentsCount = User::onlyTrashed()->where('role', 'student')->count();
        $archivedTeachersCount = User::onlyTrashed()->where('role', 'teacher')->count();

        // ── 3. Finance aggregates – single raw query instead of 6 separate ones ──
        $financeSummary = DB::selectOne("
            SELECT
                COUNT(CASE WHEN e.payment_status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN e.payment_status = 'partial' THEN 1 END) as partial_count,
                COUNT(CASE WHEN e.payment_status IS NULL OR e.payment_status = 'pending' THEN 1 END) as unpaid_count,
                COUNT(*) as total_with_enrollment,
                COALESCE(SUM(CASE WHEN e.payment_status IN ('paid', 'partial') THEN e.payment_amount ELSE 0 END), 0) as total_collected
            FROM enrollments e
            WHERE e.id IN (SELECT MAX(id) FROM enrollments WHERE status <> 'cancelled' GROUP BY user_id)
        ");
        $paidCount = $financeSummary->paid_count ?? 0;
        $partialCount = $financeSummary->partial_count ?? 0;
        $unpaidCount = $financeSummary->unpaid_count ?? 0;
        $totalCollected = $financeSummary->total_collected ?? 0;
        $pendingPayments = $partialCount;

        // ── 4 through 4c were: $paymentEnrollments, $allDocuments /
        // $paymentScreenshots, $onlinePayments (→ $financePayments),
        // $walkInTransactions, $combinedPayStats, $installmentEnrollments
        // (+ a per-row progress-calculation loop). All removed 2026-10-06
        // — every one of them fed exclusively into the Finance / Payments
        // / Installments / Fees tabs, which are permanently redirect-only
        // dead UI (see _PORTAL_SECTIONS in showSection()) and can never
        // actually be displayed. Confirmed via grep against every blade
        // file before removal. See docs/system-improvement-plan.md.
        $currentSchoolYear = $this->getCurrentSchoolYear();

        // ── 5. Academic data – select only needed columns, eager-load slim ──
        // $subjects — moved to sectionSubjects(), loaded on-demand via
        // GET /admin/section/subjects. See docs/system-improvement-plan.md
        // item #2. $teachersWithSubjectsCount below replaces Teacher
        // Management's own dependency on $subjects (it used to count
        // unique teacher_ids from the paginated $subjects list — which
        // only ever covered one page's worth of rows, the same
        // counts-from-a-paginated-list bug already fixed for Student
        // Management — now a real, independent, correct count).
        // Teachers actually teaching this school year: subject loads live in
        // teacher_assignments (subjects.teacher_id is never filled in, so the old
        // count was always 0 on the Dashboard and Teacher Management cards).
        $teachersWithSubjectsCount = \App\Models\TeacherAssignment::where('school_year', $currentSchoolYear)
            ->whereNotNull('subject_id')
            ->distinct()
            ->count('teacher_id');

        // select() MUST come before withCount() here — withCount() appends a
        // subquery column, but select() called afterward would wipe it out
        // (Eloquent's select() replaces the whole column list unconditionally).
        // 'teacher' relation (sections.teacher_id) intentionally not eager-loaded
        // here — confirmed unused by this page; advisory teacher display reads
        // TeacherAssignment directly instead (see $secAdvisory below in the blade).
        $sectionsQuery = \App\Models\Section::with(['subjects:id,name,code'])->select('id', 'name', 'grade_level', 'teacher_id', 'room_number', 'max_students', 'school_year', 'is_active')
            ->withCount('students')
            ->orderByDesc('created_at');

        // School Year filter (?section_sy=2027-2028 | all). Defaults to the current
        // school year so next year's sections (for promoted students) get their own
        // view; anything outside the shared range falls back to the current year.
        $sectionSyFilter = (string) $request->get('section_sy', $currentSchoolYear);
        if ($sectionSyFilter !== 'all' && !in_array($sectionSyFilter, \App\Models\Setting::schoolYearOptions(), true)) {
            $sectionSyFilter = $currentSchoolYear;
        }
        if ($sectionSyFilter !== 'all') {
            $sectionsQuery->where('school_year', $sectionSyFilter);
        }

        // Stat cards cover the whole filtered year, not just the 15 rows on this page
        $sectionStatsBase = \App\Models\Section::query()
            ->when($sectionSyFilter !== 'all', fn ($q) => $q->where('school_year', $sectionSyFilter));
        $sectionStats = [
            'total'    => (clone $sectionStatsBase)->count(),
            'active'   => (clone $sectionStatsBase)->where('is_active', true)->count(),
            'enrolled' => \Illuminate\Support\Facades\DB::table('section_student')
                ->whereIn('section_id', (clone $sectionStatsBase)->select('id'))
                ->count(),
        ];

        $sections = $sectionsQuery->paginate(15, ['*'], 'section_page')
            ->appends(['section' => 'sections', 'section_sy' => $sectionSyFilter]);
        // current_enrollment is a virtual, in-memory-only attribute here (the
        // column itself was dropped in Phase 1's Contract step — see
        // DATABASE_NORMALIZATION_PLAN.md) fed from the live section_student
        // count, so existing blade/JS reading $sec->current_enrollment keeps working.
        foreach ($sections as $sec) {
            $sec->current_enrollment = $sec->students_count;
        }
        // $schedules — removed 2026-10-06. Confirmed via grep against every
        // blade file: never referenced by its own partial (Schedule
        // Management is already fully self-contained AJAX via
        // loadScheduleGrid()). Pure dead weight, same as Teacher
        // Assignments' removed query. $allSchedules below (a different,
        // unfiltered query) IS still used — by Subjects and Teachers.

        $allSchedules = \App\Models\Schedule::with(['section', 'subject', 'teacher'])
            ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();
        $teachers  = User::where('role', 'teacher')->select('id', 'name', 'email', 'is_active', 'created_at')->orderByDesc('created_at')->paginate(15, ['*'], 'teacher_page');
        // Real count, not $teachers->count() — that only ever reflects one
        // page's worth of rows (currently happens to be correct only because
        // the real total is under the 15-per-page size; same
        // counts-from-a-paginated-list bug already fixed for Student
        // Management, missed here).
        $teachersCount = User::where('role', 'teacher')->count();

        // Complete (non-paginated) subject/teacher lists — $teachers above
        // is paginated for the Teacher Management table and only ever
        // carries one page's worth of rows, so dropdowns elsewhere (e.g.
        // the Summer Class modal) need their own unpaginated source.
        $allActiveSubjects = \App\Models\Subject::where('is_active', true)
            ->select('id', 'code', 'name', 'grade_level')
            ->orderBy('grade_level')->orderBy('name')
            ->get();
        $allActiveTeachers = User::where('role', 'teacher')->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // ── 5b. Teacher Assignments — removed 2026-10-06. The Advisory
        // Teacher Assignments tab is already fully self-contained AJAX
        // (loadTeacherAssignments() fetches /admin/teacher-assignments
        // directly); this paginated query was never referenced by its own
        // partial at all, confirmed via grep. Pure dead weight.

        // ── 6. Guidance records ── moved to sectionGuidance(), loaded
        // on-demand via GET /admin/section/guidance when that tab is
        // actually opened — see docs/system-improvement-plan.md item #2.

        // Same reasoning for Summer Class Management's stat cards — the table
        // rows themselves load client-side (loadSummerClasses()), but the
        // headline counts are computed once here at page load like every
        // other module's stat row.
        $summerTotalCount     = \App\Models\SummerClass::count();
        $summerOngoingCount   = \App\Models\SummerClass::where('status', 'ongoing')->count();
        $summerCompletedCount = \App\Models\SummerClass::where('status', 'completed')->count();

        // ── 7. Fee Breakdown Preview — removed 2026-10-06, same reasoning
        // as the block above: $feeBreakdowns/$feeSettings fed only the
        // Fees tab, which is permanently redirect-only dead UI. ──

        // Kept (unlike $feeBreakdowns, which has a safe `?? []` default
        // elsewhere) because resources/views/admin/sections/fees.blade.php
        // calls ->tuition / ->misc / etc. on this directly in ~25 places
        // with no isset() guard — removing it entirely would throw an
        // undefined-variable fatal, not just show stale data. Still just
        // one cheap single-row query either way.
        $feeSettings = FeeSetting::first() ?? new FeeSetting();

        $enrollmentOpen  = Setting::get('enrollment_open', true);
        $maintenanceMode = Setting::get('maintenance_mode', false);

        // ── Assessment & Promotion: Grade 1-6 students (non-transferee, enrolled/completed) ──
        // Filter grade_level in SQL (uses indexed column) — only PHP-filter the JSON student_type
        $assessGradeFilter  = $request->get('assess_grade', 'all');
        $assessStatusFilter = $request->get('assess_status', 'all'); // all|pending|done
        $assessSearchTerm   = trim((string) $request->get('assess_search', ''));
        // Section within the chosen grade ('' = all sections); only applies once a grade is chosen
        $assessSectionFilter = $assessGradeFilter !== 'all' ? trim((string) $request->get('assess_section', '')) : '';

        $assessGradeLevels = ['nursery','kindergarten','grade1','grade2','grade3','grade4','grade5','grade6'];

        // School Year being assessed (?assess_sy=), default the current one. Students
        // are listed by their enrollment IN that year, so anyone already promoted out
        // of it (old enrollment now 'completed') stays listed as Assessed instead of
        // vanishing, and a past year can still be reviewed.
        $assessSchoolYear = (string) $request->get('assess_sy', $currentSchoolYear);
        if (!in_array($assessSchoolYear, Setting::schoolYearOptions(), true)) {
            $assessSchoolYear = $currentSchoolYear;
        }

        $assessEligibleFor = function (string $sy) use ($assessGradeLevels) {
            $inYear = fn($q) => $q->where('school_year', $sy)
                ->whereIn('status', ['enrolled', 'completed'])
                ->whereIn('grade_level', $assessGradeLevels);

            return \App\Models\User::where('role', 'student')
                ->whereHas('enrollments', $inYear)
                ->with(['enrollments' => fn($q) => $inYear($q)->orderByDesc('id'),
                        'promotions' => fn($q) => $q->where('status', 'completed')->where('from_school_year', $sy)->orderByDesc('id')])
                ->orderByDesc('created_at')
                ->get()
                // The view reads ->latestEnrollment: point it at THIS year's enrollment.
                // "Assessed" = a live (not undone/skipped/failed) promotion out of this year.
                ->each(fn($s) => $s->setRelation('latestEnrollment', $s->enrollments->first()))
                ->filter(fn($s) => $this->isPromotionEligibleByType($s->id, $s->latestEnrollment->student_data ?? []))
                ->values();
        };

        $assessEligible = $assessEligibleFor($assessSchoolYear);

        // Sidebar badge: students still waiting for assessment in the CURRENT year,
        // whatever year/page/filter the Assessment tab happens to be showing.
        $sidebarAssessCount = ($assessSchoolYear === $currentSchoolYear ? $assessEligible : $assessEligibleFor($currentSchoolYear))
            ->filter(fn($s) => $s->promotions->isEmpty())
            ->count();

        // Stat cards + grade chips always reflect the FULL eligible set,
        // independent of whichever filter/page is currently applied below.
        $assessTotal   = $assessEligible->count();
        $assessPending = $assessEligible->filter(fn($s) => $s->promotions->isEmpty())->count();
        $assessDone    = $assessEligible->filter(fn($s) => $s->promotions->isNotEmpty())->count();
        // Assessed, but the family hasn't re-enrolled from the portal yet — the school follows these up
        $assessWaiting = $assessEligible->filter(fn($s) => $s->promotions->isNotEmpty() && !$s->promotions->first()->enrollment_id)->count();

        $assessByGrade = [];
        foreach ($assessGradeLevels as $gk) {
            $assessByGrade[$gk] = $assessEligible->filter(function ($s) use ($gk) {
                $enr = $s->latestEnrollment;
                $gl  = $enr ? ($enr->student_data['grade_level'] ?? ($enr->grade_level ?? '')) : '';
                return $gl === $gk;
            })->count();
        }

        // Section chips for the chosen grade: each section of that grade in this school year, with its count
        $assessBySection = [];
        if ($assessGradeFilter !== 'all') {
            $assessBySection = $assessEligible
                ->filter(fn ($s) => ($s->latestEnrollment->student_data['grade_level'] ?? ($s->latestEnrollment->grade_level ?? '')) === $assessGradeFilter)
                ->groupBy(fn ($s) => $s->latestEnrollment->section ?: '—')
                ->map->count()
                ->sortKeys(SORT_NATURAL | SORT_FLAG_CASE)
                ->all();
        }

        // Apply grade / section / status / search filters (need the PHP-derived grade_level
        // and is_done values above, not raw columns), then hand one page of the
        // result to the view via a manual paginator.
        $assessFiltered = $assessEligible->filter(function ($s) use ($assessGradeFilter, $assessSectionFilter, $assessStatusFilter, $assessSearchTerm) {
            $enr = $s->latestEnrollment;
            $gl  = $enr ? ($enr->student_data['grade_level'] ?? ($enr->grade_level ?? '')) : '';
            $isDone = $s->promotions->isNotEmpty();

            if ($assessGradeFilter !== 'all' && $gl !== $assessGradeFilter) {
                return false;
            }
            if ($assessSectionFilter !== '' && ($enr->section ?: '—') !== $assessSectionFilter) {
                return false;
            }
            if ($assessStatusFilter === 'pending' && $isDone) {
                return false;
            }
            if ($assessStatusFilter === 'done' && !$isDone) {
                return false;
            }
            if ($assessStatusFilter === 'waiting' && (!$isDone || $s->promotions->first()->enrollment_id)) {
                return false;
            }
            if ($assessSearchTerm !== '' && stripos($s->name, $assessSearchTerm) === false) {
                return false;
            }

            return true;
        })->values();

        $assessPerPage = 15;
        $assessPageNum = max(1, (int) $request->get('assess_page', 1));
        $assessStudents = new \Illuminate\Pagination\LengthAwarePaginator(
            $assessFiltered->forPage($assessPageNum, $assessPerPage)->values(),
            $assessFiltered->count(),
            $assessPerPage,
            $assessPageNum,
            // pageName must match the assess_page read above — without it the links
            // said ?page=N, which this ignored, so every page click showed page 1
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'assess_page']
        );

        // Badge lookups scoped to just the current page, not the full eligible set.
        $assessPageIds = collect($assessStudents->items())->pluck('id');

        // Open/in-progress guidance concern counts, batched in one query rather than
        // per-student, so the assessment table can flag them without an N+1.
        $assessGuidanceCounts = \App\Models\GuidanceRecord::whereIn('student_id', $assessPageIds)
            ->whereIn('status', ['open', 'in_progress'])
            ->selectRaw('student_id, count(*) as c')
            ->groupBy('student_id')
            ->pluck('c', 'student_id');

        // Summer-class remediation status per student — informational only, does
        // not block promotion. A subject counts as "failed" when its TERM AVERAGE
        // is below 75, computed from APPROVED grades only — a registrar-rejected
        // or still-pending grade isn't official yet, so it shouldn't be able to
        // flag a student as failing (or clear them) before it's actually approved.
        // "Cleared" means a SummerClassEnrollment for that same subject has
        // status = 'passed'.
        $assessGradeRows = \App\Models\Grade::whereIn('student_id', $assessPageIds)
            ->where('school_year', $assessSchoolYear)
            ->where('status', 'approved')
            ->whereNotNull('grade')
            ->get(['student_id', 'subject_id', 'grade']);

        $assessFailingSubjectIds = [];
        foreach ($assessGradeRows->groupBy('student_id') as $studentId => $rows) {
            $failed = $rows->groupBy('subject_id')
                ->filter(fn($subjectRows) => $subjectRows->avg('grade') < 75)
                ->keys();
            if ($failed->isNotEmpty()) {
                $assessFailingSubjectIds[$studentId] = $failed;
            }
        }

        $assessSummerCleared = \App\Models\SummerClassEnrollment::whereIn('student_id', $assessPageIds)
            ->where('status', 'passed')
            ->with('summerClass:id,subject_id')
            ->get()
            ->groupBy('student_id')
            ->map(fn($rows) => $rows->pluck('summerClass.subject_id')->filter()->unique());

        $assessSummerStatus = [];
        foreach ($assessFailingSubjectIds as $studentId => $failedSubjectIds) {
            $clearedSubjectIds = $assessSummerCleared[$studentId] ?? collect();
            $assessSummerStatus[$studentId] = [
                'failed'  => $failedSubjectIds->count(),
                'cleared' => $failedSubjectIds->intersect($clearedSubjectIds)->count(),
            ];
        }

        // Promote INTO the year after the one being assessed (bulk / auto-advance modals)
        $assessNextSchoolYear = $this->nextSchoolYear($assessSchoolYear);

        // Staff who can be logged as the counselor on a guidance record.
        // Stays here (not moved to sectionGuidance()) because the "Add
        // Record" modal that uses it lives outside #section-guidance, in
        // the main page — it's rendered once on initial load, not
        // re-rendered on each AJAX guidance-tab refresh.
        $guidanceCounselors = \App\Models\User::whereIn('role', ['admin', 'superadmin', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        // Contact messages inbox — latest 200 + separate counts; shared with Super Admin
        // (see ContactMessage::inboxData() and docs/system-improvement-plan.md item #3).
        [
            'contactMessages'      => $contactMessages,
            'unreadMessagesCount'  => $unreadMessagesCount,
            'totalMessagesCount'   => $totalMessagesCount,
            'readMessagesCount'    => $readMessagesCount,
            'repliedMessagesCount' => $repliedMessagesCount,
        ] = \App\Models\ContactMessage::inboxData();

        // Announcements — moved to sectionAnnouncements(), loaded on-demand
        // via GET /admin/section/announcements. See
        // docs/system-improvement-plan.md item #2.

        // News — moved to sectionNews(), loaded on-demand via
        // GET /admin/section/news. Same reasoning.

        // Sidebar badge counts — unfiltered totals, independent of the
        // paginated/filtered $students and $enrollments lists above.
        // $enrollmentCount is deliberately the PENDING count, not a total —
        // it's a "needs your attention" queue badge (like Guidance Records'
        // badge), not a historical record count. A raw Enrollment::count()
        // happened to equal $studentCount here (one enrollment per student,
        // single school year so far) which made the two badges look
        // identically redundant.
        $studentCount = User::where('role', 'student')->count();
        $enrollmentCount = Enrollment::where('status', 'pending')->count();
        // Sidebar totals for Content, same as the Super Admin sidebar shows
        $announcementCount = \App\Models\Announcement::count();
        $newsCount = \App\Models\News::count();

        return view('adminDashboard', compact(
            'studentCount', 'enrollmentCount', 'announcementCount', 'newsCount',
            'pendingPayments', 'totalCollected',
            'unpaidCount', 'partialCount', 'paidCount',
            'sort', 'archivedStudentsCount', 'archivedTeachersCount',
            'sections', 'sectionSyFilter', 'sectionStats', 'teachers', 'teachersCount', 'teachersWithSubjectsCount',
            'allActiveSubjects', 'allActiveTeachers',
            'summerTotalCount', 'summerOngoingCount', 'summerCompletedCount',
            'feeSettings', 'recentStudents', 'allSchedules',
            'currentSchoolYear', 'enrollmentOpen', 'maintenanceMode', 'assessStudents', 'assessGuidanceCounts',
            'assessSummerStatus', 'guidanceCounselors',
            'assessGradeFilter', 'assessStatusFilter', 'assessSearchTerm', 'assessSectionFilter', 'assessBySection',
            'assessTotal', 'assessPending', 'assessDone', 'assessWaiting', 'assessByGrade', 'assessNextSchoolYear',
            'assessSchoolYear', 'sidebarAssessCount',
            'contactMessages', 'unreadMessagesCount', 'totalMessagesCount', 'readMessagesCount', 'repliedMessagesCount'
        ));
    }

    /**
     * Guidance Records tab content, loaded on demand (GET
     * /admin/section/guidance) instead of being computed on every
     * adminIndex() page load — see docs/system-improvement-plan.md item
     * #2. Logic here is an exact relocation of what adminIndex() used to
     * compute inline; no behavior changed, only when it runs.
     */
    public function sectionGuidance(Request $request)
    {
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
        if ($guidanceStatus === 'resolved_closed') {
            // the "Resolved / Closed" stat card's filter — same rule as its count below
            $guidanceQuery->whereIn('status', ['resolved', 'closed']);
        } elseif ($guidanceStatus) {
            $guidanceQuery->where('status', $guidanceStatus);
        }
        if ($guidanceConcern) {
            $guidanceQuery->where('concern_type', $guidanceConcern);
        }
        $guidanceQuery->orderBy('date', $guidanceSort === 'date_asc' ? 'asc' : 'desc');

        $guidanceRecords = $guidanceQuery->paginate(15, ['*'], 'guidance_page');

        $guidanceTotalCount    = \App\Models\GuidanceRecord::count();
        $guidanceOpenCount     = \App\Models\GuidanceRecord::where('status', 'open')->count();
        $guidanceResolvedCount = \App\Models\GuidanceRecord::whereIn('status', ['resolved', 'closed'])->count();

        return view('admin.sections.guidance-content', compact(
            'guidanceRecords', 'guidanceSearch', 'guidanceStatus', 'guidanceConcern', 'guidanceSort',
            'guidanceTotalCount', 'guidanceOpenCount', 'guidanceResolvedCount'
        ));
    }

    /**
     * Announcements tab content, loaded on demand (GET
     * /admin/section/announcements) — see
     * docs/system-improvement-plan.md item #2.
     */
    public function sectionAnnouncements(Request $request)
    {
        $annCategoryFilter = $request->input('ann_category');

        $announcements = \App\Models\Announcement::orderByDesc('created_at')
            ->when($annCategoryFilter, fn($q) => $q->where('category', $annCategoryFilter))
            ->paginate(10, ['*'], 'ann_page')
            ->withQueryString();

        return view('admin.sections.announcements-content', compact('announcements', 'annCategoryFilter'));
    }

    /**
     * News tab content, loaded on demand (GET /admin/section/news) — see
     * docs/system-improvement-plan.md item #2.
     */
    public function sectionNews(Request $request)
    {
        $newsCategoryFilter = $request->input('news_category');

        $news = \App\Models\News::orderByDesc('created_at')
            ->when($newsCategoryFilter, fn($q) => $q->where('category', $newsCategoryFilter))
            ->paginate(10, ['*'], 'news_page')
            ->withQueryString();

        return view('admin.sections.news-content', compact('news', 'newsCategoryFilter'));
    }

    /**
     * Student Management tab content, loaded on demand (GET
     * /admin/section/students) — see docs/system-improvement-plan.md item
     * #2. Exact relocation of what adminIndex() used to compute inline,
     * reading the same request params fresh.
     */
    public function sectionStudents(Request $request)
    {
        return view('admin.sections.students-content', $this->studentManagementData($request));
    }

    /**
     * Super Admin's Student Management: exactly Admin's list — same filters, rows and stat
     * cards (studentManagementData) — as JSON for its on-page table.
     */
    public function studentsData(Request $request)
    {
        $d = $this->studentManagementData($request);
        $grades = ['nursery' => 'Nursery', 'kindergarten' => 'Kinder', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2', 'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];
        $page = $d['students'];

        $rows = $page->getCollection()->map(function ($s) use ($grades) {
            $enr = $s->latestEnrollment;
            $grade = ($enr->student_data['grade_level'] ?? null) ?: ($enr->grade_level ?? null);
            return [
                'id'               => $s->id,
                'name'             => $s->name,
                'email'            => $s->email,
                'lrn'              => $s->lrn,
                'enrollment_id'    => $enr?->id,
                'reference_number' => $enr?->reference_number,
                'grade_level'      => $grade,
                'grade_label'      => $grades[$grade] ?? ($grade ?: 'N/A'),
                'section'          => ($enr && $enr->section && $enr->section !== 'Unassigned') ? $enr->section : null,
                'school_year'      => $enr?->school_year,
                'status'           => $enr ? $enr->status : 'inactive',
                'payment_status'   => $enr->payment_status ?? 'unpaid',
                // A later school year's enrollment (see studentManagementData) — "→ Grade 2 · S.Y. 2027-2028 (Enrolled)"
                'next'             => ($n = $s->relationLoaded('nextEnrollment') ? $s->nextEnrollment : null) ? [
                    'grade_label' => $grades[$n->grade_level] ?? ($n->grade_level ?: '—'),
                    'school_year' => $n->school_year,
                    'status'      => ucfirst($n->status),
                ] : null,
            ];
        })->values();

        return response()->json([
            'students'     => $rows,
            'stats'        => [
                'total' => $d['smTotal'], 'enrolled' => $d['smEnrolled'], 'approved' => $d['smApproved'], 'pending' => $d['smPending'],
                'not_enrolled' => $d['smNotEnrolled'], 'paid' => $d['smPaid'], 'balance' => $d['smBalance'],
                'no_section' => $d['smNoSection'],
            ],
            'school_year'  => $d['studentSchoolYearFilter'],
            'current_page' => $page->currentPage(),
            'last_page'    => $page->lastPage(),
            'per_page'     => $page->perPage(),
            'total'        => $page->total(),
            'from'         => $page->firstItem(),
            'to'           => $page->lastItem(),
        ]);
    }

    /** Student Management's list, filters and stat cards — shared by Admin's page and Super Admin's JSON. */
    private function studentManagementData(Request $request): array
    {
        $sort = $request->get('sort', 'newest');
        $studentSearch = $request->get('student_search') ?? '';
        $studentGradeFilter = $request->get('student_grade', 'all');
        $studentStatusFilter = $request->get('student_status', 'all');
        $studentPaymentFilter = $request->get('student_payment', 'all');
        // Opens on the Current School Year (Admin › Settings › Academic); "all" = every year
        $studentSchoolYearFilter = (string) $request->get('student_schoolyear', $this->getCurrentSchoolYear());
        if ($studentSchoolYearFilter !== 'all' && !in_array($studentSchoolYearFilter, Setting::schoolYearOptions(), true)) {
            $studentSchoolYearFilter = $this->getCurrentSchoolYear();
        }
        $gradeLevel = $request->get('grade_level', 'all'); // for mass promotion

        // Show ALL students with role='student' — including pending enrollments
        $studentsQuery = User::where('role', 'student');

        // Which enrollment the grade/status/payment filters, stat cards and rows
        // look at: the student's latest one, or — once a School Year is picked —
        // their enrollment IN that year (so 2026-2027 doesn't show a promoted
        // student's 2027-2028 "Grade 3 · Pending" row).
        $studentSy = ($studentSchoolYearFilter && $studentSchoolYearFilter !== 'all') ? $studentSchoolYearFilter : null;
        $hasEnr = fn ($q, \Closure $cb) => $studentSy
            ? $q->whereHas('enrollments', fn ($e) => $cb($e->where('school_year', $studentSy)->where('status', '!=', 'cancelled')))
            : $q->whereHas('latestEnrollment', $cb);
        $lacksEnr = fn ($q, \Closure $cb) => $studentSy
            ? $q->whereDoesntHave('enrollments', fn ($e) => $cb($e->where('school_year', $studentSy)->where('status', '!=', 'cancelled')))
            : $q->whereDoesntHave('latestEnrollment', $cb);

        if ($studentSearch) {
            $studentsQuery->where(function ($q) use ($studentSearch) {
                $q->where('name', 'like', '%' . $studentSearch . '%')
                  ->orWhere('email', 'like', '%' . $studentSearch . '%')
                  ->orWhere('lrn', 'like', '%' . $studentSearch . '%');
            });
        }

        if ($studentGradeFilter && $studentGradeFilter !== 'all') {
            $hasEnr($studentsQuery, fn ($q) => $q->where('student_data->grade_level', $studentGradeFilter));
        }

        // Apply grade_level filter for mass promotion
        if ($gradeLevel && $gradeLevel !== 'all') {
            $hasEnr($studentsQuery, fn ($q) => $q->where('student_data->grade_level', $gradeLevel));
        }

        if ($studentStatusFilter && $studentStatusFilter !== 'all') {
            if ($studentStatusFilter === 'not_enrolled') {
                if ($studentSy) {
                    $lacksEnr($studentsQuery, fn ($q) => $q->whereIn('status', ['enrolled', 'approved', 'pending']));
                } else {
                    $studentsQuery->whereDoesntHave('enrollments', function ($q) {
                        $q->whereIn('status', ['enrolled', 'approved', 'pending']);
                    });
                }
            } else {
                $hasEnr($studentsQuery, fn ($q) => $q->where('status', $studentStatusFilter));
            }
        }

        if ($studentPaymentFilter === 'balance') {
            // "With Balance" — same rule as the With Balance stat card below
            $hasEnr($studentsQuery, fn ($q) => $q->whereIn('payment_status', ['partial', 'pending', 'unpaid']));
        } elseif ($studentPaymentFilter === 'unpaid') {
            // "Not Paid" — nothing paid yet. Enrollments store that as 'pending' (or no status at all);
            // matching only 'unpaid' listed nobody. Same rule as Enrollment's "Pending Payments" card.
            $hasEnr($studentsQuery, fn ($q) => $q->where(fn ($p) => $p->whereIn('payment_status', ['pending', 'unpaid'])->orWhereNull('payment_status')));
        } elseif ($studentPaymentFilter && $studentPaymentFilter !== 'all') {
            $hasEnr($studentsQuery, fn ($q) => $q->where('payment_status', $studentPaymentFilter));
        }

        // School year filter: students with a (non-cancelled) enrollment in that year
        if ($studentSy) {
            $hasEnr($studentsQuery, fn ($q) => $q);
        }

        // Enrolled but no section yet (e.g. paid before any section of their grade/year existed)
        $noSection = fn ($q) => $q->where('status', 'enrolled')
            ->where(fn ($s) => $s->whereNull('section')->orWhereIn('section', ['', 'Unassigned']));
        $studentSectionFilter = $request->get('student_section') === 'none' ? 'none' : 'all';
        $smNoSection = $hasEnr(clone $studentsQuery, $noSection)->count();
        if ($studentSectionFilter === 'none') {
            $hasEnr($studentsQuery, $noSection);
        }

        // Student Management stat cards — counted from the full filtered set
        // via clones of $studentsQuery (all filters already applied above),
        // not from the paginated $students list below.
        $smTotal = (clone $studentsQuery)->count();
        $smEnrolled = $hasEnr(clone $studentsQuery, fn($q) => $q->where('status', 'enrolled'))->count();
        $smApproved = $hasEnr(clone $studentsQuery, fn($q) => $q->where('status', 'approved'))->count();
        $smPending = $hasEnr(clone $studentsQuery, fn($q) => $q->where('status', 'pending'))->count();
        $smNotEnrolled = $lacksEnr(clone $studentsQuery, fn($q) => $q->whereIn('status', ['approved', 'enrolled', 'pending']))->count();
        $smPaid = $hasEnr(clone $studentsQuery, fn($q) => $q->where('payment_status', 'paid'))->count();
        $smBalance = $hasEnr(clone $studentsQuery, fn($q) => $q->whereIn('payment_status', ['partial', 'pending', 'unpaid']))->count();

        $studentsQuery->with(['latestEnrollment' => function ($query) {
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

        // Rows read ->latestEnrollment; with a School Year picked, show that year's
        // enrollment instead (a real one ahead of a declined one, newest first).
        if ($studentSy) {
            $yearEnrollments = Enrollment::whereIn('user_id', $students->pluck('id'))
                ->where('school_year', $studentSy)
                ->where('status', '!=', 'cancelled')
                ->with('paymentInstallments')
                ->orderByRaw("status = 'declined'")
                ->orderByDesc('id')
                ->get()
                ->groupBy('user_id');
            foreach ($students as $s) {
                $s->setRelation('latestEnrollment', $yearEnrollments->get($s->id)?->first());
            }

            // Already moved on to a later school year (e.g. Completed in 2026-2027, Grade 2 in 2027-2028):
            // the row says where, so a "Completed · Grade 1" row doesn't look like the student is stuck
            $laterEnrollments = Enrollment::whereIn('user_id', $students->pluck('id'))
                ->where('school_year', '>', $studentSy)
                ->whereNotIn('status', ['cancelled', 'declined'])
                ->orderBy('school_year')->orderByDesc('id')
                ->get(['id', 'user_id', 'school_year', 'grade_level', 'status', 'section'])
                ->groupBy('user_id');
            foreach ($students as $s) {
                $s->setRelation('nextEnrollment', $laterEnrollments->get($s->id)?->first());
            }
        }

        return compact(
            'students', 'sort', 'studentSearch', 'studentGradeFilter',
            'studentStatusFilter', 'studentPaymentFilter', 'studentSchoolYearFilter', 'studentSectionFilter',
            'smTotal', 'smEnrolled', 'smApproved', 'smPending', 'smNotEnrolled', 'smPaid', 'smBalance', 'smNoSection'
        );
    }

    /**
     * Reports tab content, loaded on demand (GET /admin/section/reports)
     * — see docs/system-improvement-plan.md item #2. Unlike every other
     * section converted so far, this one never had any of its data
     * computed in adminIndex() at all — reports.blade.php ran all of
     * these queries itself, inline, in a top-of-file @php block that
     * still executed on every single page load regardless of whether
     * the Reports tab was ever opened (same "hidden sections still
     * PHP-execute" trap found on finance.blade.php earlier). This method
     * is an exact relocation of that block into the controller, with
     * three deliberate differences — see docs/system-improvement-plan.md
     * for the full writeup:
     *   1. $rptTotalTeachers / $rptTotalSections / $rptStudentList
     *      removed — confirmed via grep, computed but never displayed
     *      anywhere (not in this template, not in the chart JS).
     *   2. The three sub-report pagination links' page/tab/subreport
     *      params are read via request() exactly as before — Laravel's
     *      request() helper works the same from a controller method as
     *      from a view.
     *   3. $rptChartGradeLabels/$rptChartGradeData — previously a second,
     *      separate @php block right after the header — folded in here
     *      since it's just one more derived value.
     */
    public function sectionReports(Request $request)
    {
        return view('admin.sections.reports-content', $this->buildSectionReportsData());
    }

    /**
     * All 16 Reports-tab sub-reports' data, computed once and shared by the
     * on-screen tab (sectionReports() above) and every download*ReportPdf()
     * method below — same query pass, same figures, never drifting apart.
     * Previously this WAS sectionReports()'s body; extracted unchanged
     * except for 'studentDocuments', which reports-content.blade.php:406
     * has always expected but which was never actually computed or passed
     * — the Document Compliance sub-report's table was silently empty in
     * production (only its 4 stat cards, computed from separate counts
     * below, were ever real). Added here using the same
     * document_type != payment_screenshot filter those stat cards already
     * use.
     */
    private function buildSectionReportsData(): array
    {
        $currentSchoolYear = $this->getCurrentSchoolYear();

        $rptGradeLevels = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];

        // Paginates an already-fetched Collection (report data here is built
        // from full in-memory collections shared across several stats, not
        // fresh per-panel queries — same technique used for the Assessment
        // & Promotion table's own pagination).
        $rptPaginate = function ($collection, int $perPage, string $pageName) {
            $page = (int) request()->get($pageName, 1);
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($page, $perPage)->values(),
                $collection->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => $pageName]
            );
        };

        // ── REAL DATA: Direct DB queries — no paginator limits ──

        $rptTotalStudents  = User::where('role','student')->count();
        $rptActiveStudents = User::where('role','student')->where('is_active',true)->count();

        $rptEnrollAll = Enrollment::where('school_year', $currentSchoolYear)
            ->get(['id','user_id','status','payment_status','payment_amount','total_fee',
                   'remaining_balance','payment_option','student_data','created_at']);

        $rptEnrollTotal     = $rptEnrollAll->count();
        $rptEnrollApproved  = $rptEnrollAll->whereIn('status',['enrolled','approved','completed'])->count();
        $rptEnrollPending   = $rptEnrollAll->where('status','pending')->count();
        $rptEnrollDeclined  = $rptEnrollAll->where('status','declined')->count();
        $rptEnrollDropped   = $rptEnrollAll->whereIn('status',['dropped','ghost','transferred'])->count();

        $rptEnrollNew        = $rptEnrollAll->filter(fn($e) => ($e->student_data['student_type'] ?? '') === 'new')->count();
        $rptEnrollReturning  = $rptEnrollAll->filter(fn($e) => in_array($e->student_data['student_type'] ?? '', ['returning','re-enrolled']))->count();
        $rptEnrollTransferee = $rptEnrollAll->filter(fn($e) => ($e->student_data['student_type'] ?? '') === 'transferee')->count();

        $rptMale   = $rptEnrollAll->whereIn('status',['enrolled','approved','completed'])
            ->filter(fn($e) => strtolower($e->student_data['gender'] ?? '') === 'male')->count();
        $rptFemale = $rptEnrollAll->whereIn('status',['enrolled','approved','completed'])
            ->filter(fn($e) => strtolower($e->student_data['gender'] ?? '') === 'female')->count();

        $rptEnrollByGrade = $rptEnrollAll
            ->groupBy(fn($e) => $e->student_data['grade_level'] ?? '')
            ->map(fn($g) => [
                'total'      => $g->count(),
                'approved'   => $g->whereIn('status',['enrolled','approved','completed'])->count(),
                'pending'    => $g->where('status','pending')->count(),
                'dropped'    => $g->whereIn('status',['dropped','ghost','transferred'])->count(),
                'new'        => $g->filter(fn($e) => ($e->student_data['student_type'] ?? '') === 'new')->count(),
                'returning'  => $g->filter(fn($e) => in_array($e->student_data['student_type'] ?? '',['returning','re-enrolled']))->count(),
                'transferee' => $g->filter(fn($e) => ($e->student_data['student_type'] ?? '') === 'transferee')->count(),
            ]);

        $rptActiveSYEnrolls = $rptEnrollAll->whereIn('status',['enrolled','approved','completed']);
        $rptMasterListPage  = $rptPaginate($rptActiveSYEnrolls->values(), 20, 'rpt_master_page');

        $rptStudentsByGrade = $rptActiveSYEnrolls
            ->groupBy(fn($e) => $e->student_data['grade_level'] ?? '')
            ->map->count();

        $rptFinAll         = $rptEnrollAll; // alias used throughout the financial tab template
        $rptTotalFees      = $rptEnrollAll->sum('total_fee');
        $rptTotalCollected = $rptEnrollAll->sum('payment_amount');
        $rptOutstanding    = $rptEnrollAll->sum('remaining_balance');
        $rptPaid           = $rptEnrollAll->where('payment_status','paid')->count();
        $rptPartial        = $rptEnrollAll->where('payment_status','partial')->count();
        $rptUnpaid         = $rptEnrollAll->whereNotIn('payment_status',['paid','partial'])->count();
        $rptCollectionRate = $rptTotalFees > 0 ? round($rptTotalCollected / $rptTotalFees * 100, 1) : 0;

        $rptFinByGrade = $rptEnrollAll
            ->groupBy(fn($e) => $e->student_data['grade_level'] ?? '')
            ->map(fn($g) => [
                'count'       => $g->count(),
                'total_fee'   => $g->sum('total_fee'),
                'collected'   => $g->sum('payment_amount'),
                'outstanding' => $g->sum('remaining_balance'),
                'paid'        => $g->where('payment_status','paid')->count(),
                'partial'     => $g->where('payment_status','partial')->count(),
            ]);

        $rptFinByOption = $rptEnrollAll->whereNotNull('payment_option')
            ->groupBy('payment_option')
            ->map(fn($g) => [
                'count'       => $g->count(),
                'total_fee'   => $g->sum('total_fee'),
                'collected'   => $g->sum('payment_amount'),
                'outstanding' => $g->sum('remaining_balance'),
                'paid'        => $g->where('payment_status','paid')->count(),
            ]);

        $rptOutstandingList = $rptEnrollAll
            ->filter(fn($e) => ($e->remaining_balance ?? 0) > 0)
            ->sortByDesc('remaining_balance')
            ->values();
        $rptOutstandingPage     = $rptPaginate($rptOutstandingList, 20, 'rpt_outstanding_page');
        $rptOutstandingTotalBal = $rptOutstandingList->sum('remaining_balance');

        // Section capacity — full table, independent of Section Management's
        // own paginated $sections (adminIndex() caps that at 15 rows — using
        // it here would have undercounted capacity for any school year with
        // more sections than one page's worth; this report always needs the
        // true total).
        $rptAllSections  = \App\Models\Section::select('id','name','grade_level','max_students','school_year')->withCount('students')->get();
        $rptAllSections->each(fn($s) => $s->current_enrollment = $s->students_count);
        $rptTotalCapacity    = $rptAllSections->sum('max_students');
        $rptTotalEnrolledSec = $rptAllSections->sum('current_enrollment');

        $rptApprovedDocs = \App\Models\StudentDocument::where('document_type','!=','payment_screenshot')->where('status','approved')->count();
        $rptPendingDocs  = \App\Models\StudentDocument::where('document_type','!=','payment_screenshot')->where('status','pending')->count();
        $rptRejectedDocs = \App\Models\StudentDocument::where('document_type','!=','payment_screenshot')->where('status','rejected')->count();
        $rptTotalDocs    = $rptApprovedDocs + $rptPendingDocs + $rptRejectedDocs;

        // Fixes reports-content.blade.php:406's table, which has always
        // expected this variable but never received it — see this method's
        // docblock.
        $studentDocuments = \App\Models\StudentDocument::with('user:id,name')
            ->where('document_type', '!=', 'payment_screenshot')
            ->latest()
            ->get();

        $rptInstTotal   = \App\Models\PaymentInstallment::count();
        $rptInstOverdue = \App\Models\PaymentInstallment::where('status','pending')
            ->where('due_date','<',\Carbon\Carbon::today())->count();

        // PROMOTION / ASSESSMENT report data — sliced by the school year
        // that's ending (from_school_year), same eligibility rule as the
        // Assessment & Promotion page itself: Nursery-Grade6, enrolled or
        // completed, non-transferee.
        $rptPromoAll = \App\Models\Promotion::where('from_school_year', $currentSchoolYear)
            ->get(['student_id', 'from_grade', 'to_grade', 'from_school_year', 'to_school_year']);

        $rptPromoPromoted  = $rptPromoAll->filter(fn($p) => $p->to_grade !== $p->from_grade && $p->to_grade !== 'graduated')->count();
        $rptPromoRetained  = $rptPromoAll->filter(fn($p) => $p->to_grade === $p->from_grade)->count();
        $rptPromoGraduated = $rptPromoAll->filter(fn($p) => $p->to_grade === 'graduated')->count();

        $rptPromoEligible = User::where('role', 'student')
            ->with(['latestEnrollment'])
            ->whereHas('latestEnrollment', fn($q) => $q
                ->where('school_year', $currentSchoolYear)
                ->whereIn('status', ['enrolled', 'completed'])
                ->whereIn('grade_level', array_keys($rptGradeLevels))
            )
            ->get()
            ->filter(fn($s) => $this->isPromotionEligibleByType($s->id, $s->latestEnrollment->student_data ?? []));

        $rptPromoEligibleTotal = $rptPromoEligible->count();
        $rptPromoPending       = max(0, $rptPromoEligibleTotal - $rptPromoAll->count());

        $rptPromoByGrade = [];
        foreach ($rptGradeLevels as $gk => $gl) {
            $gradeEligible = $rptPromoEligible->filter(fn($s) => ($s->latestEnrollment->grade_level ?? '') === $gk)->count();
            $gradePromos   = $rptPromoAll->where('from_grade', $gk);
            $rptPromoByGrade[$gk] = [
                'eligible'  => $gradeEligible,
                'promoted'  => $gradePromos->filter(fn($p) => $p->to_grade !== $p->from_grade && $p->to_grade !== 'graduated')->count(),
                'retained'  => $gradePromos->filter(fn($p) => $p->to_grade === $p->from_grade)->count(),
                'graduated' => $gradePromos->filter(fn($p) => $p->to_grade === 'graduated')->count(),
                'pending'   => max(0, $gradeEligible - $gradePromos->count()),
            ];
        }

        $rptPromoStudentList = $rptPromoEligible->map(function ($s) use ($rptPromoAll, $rptGradeLevels) {
            $promo = $rptPromoAll->where('student_id', $s->id)->sortByDesc('id')->first();
            $gl    = $s->latestEnrollment->grade_level ?? '';
            $result = 'Pending';
            $toGradeLabel = null;
            if ($promo) {
                if ($promo->to_grade === 'graduated') {
                    $result = 'Graduated';
                } elseif ($promo->to_grade === $promo->from_grade) {
                    $result = 'Retained';
                } else {
                    $result = 'Promoted';
                    $toGradeLabel = $rptGradeLevels[$promo->to_grade] ?? ucfirst($promo->to_grade);
                }
            }
            return (object) [
                'id'          => $s->id,
                'name'        => $s->name,
                'grade_level' => $rptGradeLevels[$gl] ?? ucfirst($gl),
                'section'     => $s->latestEnrollment->section ?? '—',
                'result'      => $result,
                'to_grade'    => $toGradeLabel,
            ];
        })->sortBy('name')->values();

        $rptPromoListPage = $rptPaginate($rptPromoStudentList, 20, 'rpt_promo_page');

        $rptDailyDays = [];
        for ($d = 6; $d >= 0; $d--) {
            $day = \Carbon\Carbon::today()->subDays($d);
            $rptDailyDays[] = [
                'label' => $day->format('D, M j'),
                'count' => Enrollment::where('school_year', $currentSchoolYear)
                    ->whereDate('created_at', $day->toDateString())->count(),
            ];
        }

        $rptChartGradeLabels = collect($rptStudentsByGrade ?? [])->keys()->map(fn($k)=>ucwords(str_replace(['grade','_'],['Grade ',' '],$k)))->values()->toArray();
        $rptChartGradeData   = collect($rptStudentsByGrade ?? [])->values()->toArray();

        return compact(
            'currentSchoolYear', 'rptGradeLevels',
            'rptTotalStudents', 'rptActiveStudents',
            'rptEnrollTotal', 'rptEnrollApproved', 'rptEnrollPending', 'rptEnrollDeclined', 'rptEnrollDropped',
            'rptEnrollNew', 'rptEnrollReturning', 'rptEnrollTransferee',
            'rptMale', 'rptFemale', 'rptEnrollByGrade', 'rptStudentsByGrade', 'rptMasterListPage', 'rptActiveSYEnrolls',
            'rptEnrollAll', 'rptFinAll', 'rptTotalFees', 'rptTotalCollected', 'rptOutstanding',
            'rptPaid', 'rptPartial', 'rptUnpaid', 'rptCollectionRate',
            'rptFinByGrade', 'rptFinByOption',
            'rptOutstandingList', 'rptOutstandingPage', 'rptOutstandingTotalBal',
            'rptAllSections', 'rptTotalCapacity', 'rptTotalEnrolledSec',
            'rptApprovedDocs', 'rptPendingDocs', 'rptRejectedDocs', 'rptTotalDocs', 'studentDocuments',
            'rptInstTotal', 'rptInstOverdue',
            'rptPromoByGrade', 'rptPromoEligibleTotal', 'rptPromoPending',
            'rptPromoPromoted', 'rptPromoRetained', 'rptPromoGraduated', 'rptPromoListPage', 'rptPromoStudentList',
            'rptDailyDays', 'rptChartGradeLabels', 'rptChartGradeData'
        );
    }

    // ── Reports tab PDFs — each pulls from the same buildSectionReportsData()
    // pass as the on-screen tab, on the shared pdf.shared.* design (see
    // app/Support/Traits/BuildsSchoolPdf.php) instead of the old client-side
    // window.print()/html2pdf.js popups. ──

    public function downloadMasterListReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Student Master List',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'enrollments'    => $data['rptActiveSYEnrolls']->values(),
            'gradeLevels'    => $data['rptGradeLevels'],
            'male'           => $data['rptMale'],
            'female'         => $data['rptFemale'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.master-list', $pdfData),
            $pdfData
        )->download('student-master-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadStudentsByGradeReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'     => 'Students by Grade Level',
            'dateRangeLabel'  => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'     => $data['rptGradeLevels'],
            'studentsByGrade' => $data['rptStudentsByGrade'],
            'allSections'     => $data['rptAllSections'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.students-by-grade', $pdfData),
            $pdfData
        )->download('students-by-grade-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadStudentsNewVsReturningReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'     => 'Students — New vs Returning',
            'dateRangeLabel'  => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'     => $data['rptGradeLevels'],
            'enrollByGrade'   => $data['rptEnrollByGrade'],
            'newCount'        => $data['rptEnrollNew'],
            'returningCount'  => $data['rptEnrollReturning'],
            'transfereeCount' => $data['rptEnrollTransferee'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.students-new-vs-returning', $pdfData),
            $pdfData
        )->download('students-new-vs-returning-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadDocumentComplianceReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Document Compliance',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'approvedDocs'   => $data['rptApprovedDocs'],
            'pendingDocs'    => $data['rptPendingDocs'],
            'rejectedDocs'   => $data['rptRejectedDocs'],
            'totalDocs'      => $data['rptTotalDocs'],
            'documents'      => $data['studentDocuments'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.document-compliance', $pdfData),
            $pdfData
        )->download('document-compliance-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadEnrollmentStatusReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Enrollment Status Summary',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'enrollAll'      => $data['rptEnrollAll'],
            'enrollTotal'    => $data['rptEnrollTotal'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.enrollment-status', $pdfData),
            $pdfData
        )->download('enrollment-status-summary-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadEnrollmentByGradeReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Enrollment by Grade Level',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'    => $data['rptGradeLevels'],
            'enrollByGrade'  => $data['rptEnrollByGrade'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.enrollment-by-grade', $pdfData),
            $pdfData
        )->download('enrollment-by-grade-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadEnrollmentNewVsReturningReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Enrollment — New vs Returning',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'    => $data['rptGradeLevels'],
            'enrollByGrade'  => $data['rptEnrollByGrade'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.enrollment-new-vs-returning', $pdfData),
            $pdfData
        )->download('enrollment-new-vs-returning-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadEnrollmentDailyTrendReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Daily Enrollment Trend',
            'dateRangeLabel' => 'Last 7 Days',
            'dailyDays'      => $data['rptDailyDays'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.enrollment-daily-trend', $pdfData),
            $pdfData
        )->download('enrollment-daily-trend-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadAdminCollectionSummaryReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Collection Summary',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'finAll'         => $data['rptFinAll'],
            'paid'           => $data['rptPaid'],
            'partial'        => $data['rptPartial'],
            'unpaid'         => $data['rptUnpaid'],
            'totalFees'      => $data['rptTotalFees'],
            'totalCollected' => $data['rptTotalCollected'],
            'collectionRate' => $data['rptCollectionRate'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.collection-summary', $pdfData),
            $pdfData
        )->download('admin-collection-summary-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadFinancialByGradeReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Financial — By Grade Level',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'    => $data['rptGradeLevels'],
            'finByGrade'     => $data['rptFinByGrade'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.financial-by-grade', $pdfData),
            $pdfData
        )->download('financial-by-grade-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadFinancialByOptionReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Financial — By Payment Option',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'finByOption'    => $data['rptFinByOption'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.financial-by-option', $pdfData),
            $pdfData
        )->download('financial-by-payment-option-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadOutstandingBalancesReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'      => 'Outstanding Balances',
            'dateRangeLabel'   => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'      => $data['rptGradeLevels'],
            'outstandingList'  => $data['rptOutstandingList'],
            'outstandingTotal' => $data['rptOutstandingTotalBal'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.outstanding-balances', $pdfData),
            $pdfData
        )->download('outstanding-balances-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadPromotionOverviewReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Assessment & Promotion Overview',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'promoted'       => $data['rptPromoPromoted'],
            'retained'       => $data['rptPromoRetained'],
            'graduated'      => $data['rptPromoGraduated'],
            'pending'        => $data['rptPromoPending'],
            'eligibleTotal'  => $data['rptPromoEligibleTotal'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.promotion-overview', $pdfData),
            $pdfData
        )->download('promotion-overview-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadPromotionByGradeReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Promotion — By Grade Level',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'gradeLevels'    => $data['rptGradeLevels'],
            'promoByGrade'   => $data['rptPromoByGrade'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.promotion-by-grade', $pdfData),
            $pdfData
        )->download('promotion-by-grade-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadPromotionStudentListReportPdf()
    {
        $data = $this->buildSectionReportsData();

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'Promotion Student List',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'students'       => $data['rptPromoStudentList'],
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.promotion-student-list', $pdfData),
            $pdfData
        )->download('promotion-student-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadKpiOverviewReportPdf()
    {
        abort_unless(Auth::user()->role === 'superadmin', 403);

        $data     = $this->buildSectionReportsData();
        $kpiCards = $this->buildKpiCards($data);

        $pdfData = array_merge($this->schoolPdfLetterhead(Auth::user()->name ?? 'Admin'), [
            'reportTitle'    => 'KPI Dashboard',
            'dateRangeLabel' => 'School Year ' . $data['currentSchoolYear'],
            'kpiCards'       => $kpiCards,
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admin.kpi-overview', $pdfData),
            $pdfData
        )->download('kpi-dashboard-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Same 8-metric computation reports-content.blade.php:1177-1218 does
     * inline for the on-screen KPI tab — moved here too so the PDF doesn't
     * re-derive it a second, independent way. Target thresholds match that
     * Blade block exactly; icon/color fields are dropped since the PDF
     * view styles status from met/warn directly rather than via Bootstrap
     * icon classes (Dompdf can't render icon-font glyphs, same reason no
     * other report view under pdf/finance or pdf/admin uses
     * <i class="bi ..."> either).
     */
    private function buildKpiCards(array $data): array
    {
        $kpiEnrollTotal   = max(1, $data['rptEnrollTotal']);
        $kpiEnrollRate    = round($data['rptEnrollApproved'] / $kpiEnrollTotal * 100, 1);
        $kpiColRate       = $data['rptCollectionRate'];
        $kpiTotalCap      = max(1, $data['rptTotalCapacity']);
        $kpiSecUtil       = round($data['rptTotalEnrolledSec'] / $kpiTotalCap * 100, 1);
        $kpiDropout       = round($data['rptEnrollDropped'] / $kpiEnrollTotal * 100, 1);
        $kpiDocCompDenom  = max(1, $data['rptTotalDocs']);
        $kpiDocComp       = round($data['rptApprovedDocs'] / $kpiDocCompDenom * 100, 1);
        $kpiNewGrowth     = round($data['rptEnrollNew'] / $kpiEnrollTotal * 100, 1);
        $kpiInstTotalSafe = max(1, $data['rptInstTotal']);
        $kpiOverdueRate   = round($data['rptInstOverdue'] / $kpiInstTotalSafe * 100, 1);
        $kpiOnline        = $data['rptEnrollAll']->filter(fn($e) => empty($e->student_data['is_walk_in']))->count();
        $kpiOnlineRate    = round($kpiOnline / $kpiEnrollTotal * 100, 1);

        return [
            ['label' => 'Enrollment Completion Rate', 'value' => $kpiEnrollRate . '%', 'target' => '≥ 85%',
             'met' => $kpiEnrollRate >= 85, 'warn' => $kpiEnrollRate >= 70, 'bar' => min(100, $kpiEnrollRate)],
            ['label' => 'Collection Rate', 'value' => $kpiColRate . '%', 'target' => '≥ 90%',
             'met' => $kpiColRate >= 90, 'warn' => $kpiColRate >= 70, 'bar' => min(100, $kpiColRate)],
            ['label' => 'Section Utilization', 'value' => $kpiSecUtil . '%', 'target' => '80–95%',
             'met' => $kpiSecUtil >= 80 && $kpiSecUtil <= 95, 'warn' => $kpiSecUtil >= 65, 'bar' => min(100, $kpiSecUtil)],
            ['label' => 'Dropout Rate', 'value' => $kpiDropout . '%', 'target' => '≤ 5%',
             'met' => $kpiDropout <= 5, 'warn' => $kpiDropout <= 10, 'bar' => min(100, $kpiDropout * 5)],
            ['label' => 'Document Compliance', 'value' => $kpiDocComp . '%', 'target' => '≥ 95%',
             'met' => $kpiDocComp >= 95, 'warn' => $kpiDocComp >= 80, 'bar' => min(100, $kpiDocComp)],
            ['label' => 'New Student Growth', 'value' => $kpiNewGrowth . '%', 'target' => 'Growing',
             'met' => $kpiNewGrowth >= 20, 'warn' => $kpiNewGrowth >= 10, 'bar' => min(100, $kpiNewGrowth * 2)],
            ['label' => 'Overdue Installment Rate', 'value' => $kpiOverdueRate . '%', 'target' => '≤ 10%',
             'met' => $kpiOverdueRate <= 10, 'warn' => $kpiOverdueRate <= 20, 'bar' => min(100, $kpiOverdueRate * 3)],
            ['label' => 'Online Enrollment Adoption', 'value' => $kpiOnlineRate . '%', 'target' => 'Growing',
             'met' => $kpiOnlineRate >= 50, 'warn' => $kpiOnlineRate >= 30, 'bar' => min(100, $kpiOnlineRate)],
        ];
    }

    /**
     * Subject Management tab content, loaded on demand (GET
     * /admin/section/subjects) — see docs/system-improvement-plan.md item
     * #2. $allSchedules is re-fetched here (not shared from adminIndex(),
     * which only renders the main page shell) because the partial's "In
     * Schedule" column needs it — same query adminIndex() still runs for
     * the Schedule Management grid and Teacher Management.
     */
    public function sectionSubjects(Request $request)
    {
        $subjects = \App\Models\Subject::with('teacher:id,name')
            ->select('id', 'code', 'name', 'description', 'grade_level', 'teacher_id', 'is_active')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'subject_page');

        $allSchedules = \App\Models\Schedule::with(['section', 'subject', 'teacher'])
            ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();

        // Stat cards — one grouped query for active/inactive, $subjects->total()
        // (already a paginator) for the overall total rather than a 4th query.
        $subjectStatusCounts = \App\Models\Subject::selectRaw(
            "SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count,
             SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_count"
        )->first();
        $activeSubjectsCount = $subjectStatusCounts->active_count ?? 0;
        $inactiveSubjectsCount = $subjectStatusCounts->inactive_count ?? 0;

        return view('admin.sections.subjects-content', compact(
            'subjects', 'allSchedules', 'activeSubjectsCount', 'inactiveSubjectsCount'
        ));
    }

    /**
     * Archives tab content, loaded on demand (GET /admin/section/archives)
     * — see docs/system-improvement-plan.md item #2.
     */
    public function sectionArchives(Request $request)
    {
        $archivedStudents = User::onlyTrashed()
            ->where('role', 'student')
            ->with(['latestEnrollment'])
            ->orderByDesc('deleted_at')
            ->paginate(15, ['*'], 'archive_page');

        $archivedTeachers = User::onlyTrashed()
            ->where('role', 'teacher')
            ->withCount('teacherAssignments')
            ->orderByDesc('deleted_at')
            ->paginate(15, ['*'], 'archive_teacher_page');

        return view('admin.sections.archives-content', compact('archivedStudents', 'archivedTeachers'));
    }

    /**
     * Enrollment Management tab content, loaded on demand (GET
     * /admin/section/enrollment) — see docs/system-improvement-plan.md
     * item #2. Exact relocation of what adminIndex() used to compute
     * inline, reading the same request params fresh.
     */
    public function sectionEnrollment(Request $request)
    {
        $sort = $request->get('sort', 'newest');
        $statusFilter = $request->get('status', 'all');
        $gradeFilter = $request->get('grade', 'all');
        $enrollmentSearch = trim((string) $request->get('enrollment_search', ''));

        // School Year filter (?enrollment_sy=): opens on the Current School Year
        // (Admin › Settings › Academic); "all" = every year. Pending applications
        // for other years are flagged in a banner so none get missed.
        $enrollmentSyFilter = (string) $request->get('enrollment_sy', Setting::getCurrentSchoolYear());
        if ($enrollmentSyFilter !== 'all' && !in_array($enrollmentSyFilter, Setting::schoolYearOptions(), true)) {
            $enrollmentSyFilter = Setting::getCurrentSchoolYear();
        }
        $enrollmentSy = $enrollmentSyFilter !== 'all' ? $enrollmentSyFilter : null;
        $pendingOtherYears = $enrollmentSy
            ? Enrollment::where('status', 'pending')->where('school_year', '!=', $enrollmentSy)
                ->selectRaw('school_year, COUNT(*) as c')->groupBy('school_year')->orderBy('school_year')->pluck('c', 'school_year')
            : collect();

        $enrollmentQuery = Enrollment::select('id', 'user_id', 'reference_number', 'status', 'school_year', 'student_data', 'payment_status', 'payment_amount', 'payment_method', 'payment_reference', 'payment_type', 'total_fee', 'remaining_balance', 'payment_due_date', 'created_at', 'updated_at');

        if ($enrollmentSy) {
            $enrollmentQuery->where('school_year', $enrollmentSy);
        }

        if ($statusFilter !== 'all') {
            $enrollmentQuery->where('status', $statusFilter);
        } else {
            // 'cancelled' rows are leftovers of an undone promotion — nothing to
            // act on; still reachable through the Cancelled status option.
            $enrollmentQuery->where('status', '!=', 'cancelled');
        }

        if ($gradeFilter !== 'all') {
            $enrollmentQuery->where('student_data->grade_level', $gradeFilter);
        }

        if ($enrollmentSearch !== '') {
            $enrollmentQuery->where(function ($q) use ($enrollmentSearch) {
                $term = '%' . $enrollmentSearch . '%';
                $q->where('reference_number', 'like', $term)
                  ->orWhere('student_data->first_name', 'like', $term)
                  ->orWhere('student_data->middle_name', 'like', $term)
                  ->orWhere('student_data->last_name', 'like', $term)
                  ->orWhere('student_data->student_email', 'like', $term)
                  ->orWhereRaw("CONCAT(JSON_UNQUOTE(JSON_EXTRACT(student_data, '$.first_name')), ' ', JSON_UNQUOTE(JSON_EXTRACT(student_data, '$.last_name'))) LIKE ?", [$term]);
            });
        }

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
        $enrollmentOpen = Setting::get('enrollment_open', true);

        // Stat cards — scoped to the whole table, not the current page/filter,
        // same "full query, not the paginated subset" rule the other stat
        // cards in this controller already follow.
        // "Enrolled" card: the picked school year, or the current one under All.
        $enrolledStatYear = $enrollmentSy ?? Setting::getCurrentSchoolYear();
        $enrolledThisYear = Enrollment::where('status', 'enrolled')
            ->where('school_year', $enrolledStatYear)
            ->count();

        // Same aggregate query adminIndex() already uses for its own
        // paid/unpaid/collected stat cards (one DB round-trip, scoped to
        // each student's latest enrollment) — reused here for consistency
        // rather than computing a second, possibly-diverging figure. With a
        // School Year picked: each student's latest enrollment IN that year.
        // Cancelled rows (undone promotions) never count as the latest one.
        $enrollmentFinanceSummary = DB::selectOne("
            SELECT
                COUNT(CASE WHEN e.payment_status IS NULL OR e.payment_status = 'pending' THEN 1 END) as unpaid_count,
                COALESCE(SUM(CASE WHEN e.payment_status IN ('paid','partial') THEN e.payment_amount ELSE 0 END), 0) as total_collected
            FROM enrollments e
            WHERE e.id IN (
                SELECT MAX(id) FROM enrollments
                WHERE status <> 'cancelled'" . ($enrollmentSy ? " AND school_year = ?" : "") . "
                GROUP BY user_id
            )
        ", $enrollmentSy ? [$enrollmentSy] : []);
        $totalFeesCollected = $enrollmentFinanceSummary->total_collected ?? 0;
        $pendingPaymentsCount = $enrollmentFinanceSummary->unpaid_count ?? 0;

        // Pending / Approved / Declined cards — the same count as Super Admin's Enrollments page
        // (SuperAdminController::enrollmentsData): applications per status in the picked school year,
        // or in every year under "All"
        $enrollmentStatusCounts = Enrollment::selectRaw('status, count(*) as cnt')
            ->when($enrollmentSy, fn ($q) => $q->where('school_year', $enrollmentSy))
            ->groupBy('status')->pluck('cnt', 'status');

        return view('admin.sections.enrollment-content', compact(
            'enrollments', 'sort', 'statusFilter', 'gradeFilter', 'enrollmentSearch', 'enrollmentOpen',
            'enrolledThisYear', 'totalFeesCollected', 'pendingPaymentsCount', 'enrollmentStatusCounts',
            'enrollmentSyFilter', 'enrolledStatYear', 'pendingOtherYears'
        ));
    }

    /**
     * Show enrollment details for admin
     */
    public function adminShow(Enrollment $enrollment)
    {
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            $data = $enrollment->toArray();
            $studentData = $data['student_data'] ?? [];

            // Overlay the normalized profile/address/guardian tables onto the
            // submission-time JSON snapshot — ProfileController writes student
            // self-service edits only to these normalized tables, never back
            // to student_data, so without this the admin's enrollment view
            // keeps showing whatever the student originally submitted even
            // after they've since updated their address/guardian info. Only
            // overwrites a field when the normalized record actually has a
            // value, so nothing here is ever blanked out.
            if ($enrollment->user_id) {
                $user = User::find($enrollment->user_id);
                if ($user) {
                    if ($profile = $user->profile) {
                        if ($profile->first_name)  $studentData['first_name']  = $profile->first_name;
                        if ($profile->last_name)   $studentData['last_name']   = $profile->last_name;
                        if ($profile->middle_name) $studentData['middle_name'] = $profile->middle_name;
                        if ($profile->birthdate)   $studentData['birthdate']   = $profile->birthdate;
                    }
                    if ($address = $user->address) {
                        if ($address->street_address) $studentData['street_address'] = $address->street_address;
                        if ($address->barangay)        $studentData['barangay']       = $address->barangay;
                        if ($address->city)            $studentData['city']           = $address->city;
                        if ($address->province)        $studentData['province']       = $address->province;
                        if ($address->zip_code)         $studentData['zip_code']      = $address->zip_code;
                    }
                    if ($guardian = $user->guardian) {
                        if ($guardian->name)         $studentData['guardian_name']       = $guardian->name;
                        if ($guardian->relationship) $studentData['relationship']        = strtolower($guardian->relationship);
                        if ($guardian->contact)      $studentData['guardian_phone']      = $guardian->contact;
                        if ($guardian->occupation)   $studentData['guardian_occupation'] = $guardian->occupation;
                    }
                }
            }

            $data['student_data'] = $studentData;
            return response()->json($data);
        }

        return view('adminDashboard', compact('enrollment'));
    }

    /**
     * Approve enrollment
     */
    public function approve(Enrollment $enrollment)
    {
        // Only a fresh application makes sense to approve — approving an
        // already-approved/enrolled enrollment a second time would reset
        // enrolled_at and flip status back to 'approved' without touching
        // the section/payment records that already moved on from it.
        if ($enrollment->status !== 'pending') {
            $message = "This enrollment is already {$enrollment->status} and can no longer be approved through this action.";
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 409);
            }
            return redirect()->back()->with('error', $message);
        }

        try {
            $studentData = $enrollment->student_data;

            // Calculate payment fields only if payment_option is explicitly set in student_data
            // (walk-in enrollments have it; online enrollments choose later in student portal)
            $paymentUpdate = [];
            // As a number — an unset fee is the string "0.00", which empty() treats as set
            if ((float) $enrollment->total_fee <= 0) {
                $paymentOption = $studentData['payment_option'] ?? ($studentData['paymentOption'] ?? null);
                $gradeLevel = $studentData['grade_level'] ?? $enrollment->grade_level;
                // A plan this grade can't take (C: Grade 1-6, D: Nursery/Kinder) isn't priced —
                // the student chooses a valid one in the portal instead
                if ($paymentOption && !\App\Services\FeeCalculator::planAllowed($gradeLevel, $paymentOption)) {
                    $paymentOption = null;
                }

                if ($paymentOption) {
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

            // Resolve the student account. Prefer the account this enrollment is
            // already linked to (created at submission). Looking the student up
            // only by the form's email used to create a second account whenever
            // the two differed (e.g. the email was corrected before approval),
            // leaving the student's earlier records — such as installments — on
            // the old account.
            $formEmail = trim($studentData['student_email'] ?? '');
            $existingUser = $enrollment->user_id ? User::withTrashed()->find($enrollment->user_id) : null;
            $newEmail = null;

            if ($existingUser) {
                if ($formEmail !== '' && strcasecmp($existingUser->email, $formEmail) !== 0) {
                    $emailOwner = User::withTrashed()->where('email', $formEmail)->where('id', '!=', $existingUser->id)->first();
                    if ($emailOwner) {
                        // The credentials email goes to the form's address but the student logs in
                        // with the account's email, so guessing either account would break the login.
                        $message = "The email on this application ({$formEmail}) already belongs to another account ({$emailOwner->name}). Correct the email before approving.";
                        if (request()->expectsJson()) {
                            return response()->json(['success' => false, 'message' => $message], 422);
                        }
                        return redirect()->back()->with('error', $message);
                    }
                    // Keep one account per student and send the credentials where the form says.
                    $newEmail = $formEmail;
                }
            } elseif ($formEmail !== '') {
                $existingUser = User::withTrashed()->where('email', $formEmail)->first();
            }

            if (!$existingUser && $formEmail === '') {
                $message = 'This application has no student email, so no account can be created. Add the email before approving.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->back()->with('error', $message);
            }

            // Wrap all DB writes in a single transaction — faster (one commit) and atomic
            DB::beginTransaction();

            // Row-locked re-check — closes the race the pre-check above can't:
            // two near-simultaneous approve clicks (or a double-click) would
            // otherwise both pass that check before either commits, each
            // generating a different password and sending a different
            // "approved" email for the same enrollment. Same pattern as
            // approveDocument() below.
            $locked = Enrollment::where('id', $enrollment->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'pending') {
                DB::rollBack();
                $message = "This enrollment is already " . ($locked->status ?? 'unknown') . " and can no longer be approved through this action.";
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 409);
                }
                return redirect()->back()->with('error', $message);
            }

            // Update enrollment status + payment fields
            $enrollment->update(array_merge([
                'status'      => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'enrolled_at' => null,
            ], $paymentUpdate));

            // Create student user account (or link the existing one resolved above)
            $studentData = $enrollment->student_data;
            $password = null;
            $isReEnrollment = ($studentData['student_type'] ?? '') === 'returning';

            if ($existingUser) {
                if ($existingUser->trashed()) {
                    $existingUser->restore();
                }
                $user = $existingUser;
                $emailUpdate = $newEmail ? ['email' => $newEmail] : [];

                if ($isReEnrollment && !$newEmail) {
                    // Re-enrollment: student already knows their password — just activate
                    $user->update(['role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);
                    // $password stays null → no credential email sent
                } else {
                    // New/transferee, or the login email just changed — reset password and notify
                    $password = $this->generateStudentPassword();
                    $user->update(array_merge($emailUpdate, ['password' => bcrypt($password), 'role' => 'student', 'is_active' => true, 'email_verified_at' => now()]));
                }
                $enrollment->update(['user_id' => $user->id]);
            } else {
                // Brand-new student account
                $password = $this->generateStudentPassword();
                $user = User::create([
                    'name' => trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? '')),
                    'email' => $formEmail,
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

            // Same 'street' -> street_address / city-not-populated fix as the
            // other two dual-write sites — see DATABASE_NORMALIZATION_PLAN.md Phase 6.
            \App\Models\StudentAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street_address' => $studentData['street_address'] ?? '',
                    'barangay' => $studentData['barangay'] ?? '',
                    'municipality' => $studentData['city'] ?? '',
                    'city' => $studentData['city'] ?? '',
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

            $studentName = trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? '')) ?: ($studentData['student_email'] ?? 'student');
            ActivityLogger::log('update', "Approved enrollment for {$studentName} (#{$enrollment->reference_number})", 'Enrollment', $enrollment->id);

            // Section assignment happens when Finance processes payment → updateEnrollmentPaymentStatus()

            // Sent via the real queue (see docker/entrypoint.sh's supervised
            // queue:work worker) so the admin isn't blocked, and so a failed
            // send lands in `failed_jobs` instead of vanishing silently.
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
                    } catch (\Throwable $mailException) {
                        Log::warning('Failed to send approval email: ' . get_class($mailException) . ': ' . $mailException->getMessage());
                    }
                });
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
        // Only a fresh application makes sense to decline — an enrollment
        // that already progressed past 'pending' (approved/enrolled/etc.)
        // should be handled via changeEnrollmentStatus() instead, not
        // silently overwritten here.
        if ($enrollment->status !== 'pending') {
            $message = "This enrollment is already {$enrollment->status} and can no longer be declined through this action.";
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 409);
            }
            return redirect()->back()->with('error', $message);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'storage' => 'nullable|string|max:50',
        ]);

        try {
            $declineFields = [
                'status' => 'declined',
                'declined_at' => now(),
                'declined_by' => Auth::id(),
                'decline_reason' => $validated['reason'],
                'decline_storage' => $validated['storage'] ?? 'pending',
            ];

            // An enrollment created by Assessment & Promotion: declining it undoes
            // the promotion, so the student goes back to their previous enrollment
            // and can be re-assessed (the decline details are kept on the row).
            $reversedPromotion = $this->reversiblePromotionFor($enrollment);
            if ($reversedPromotion) {
                DB::transaction(fn () => $this->reversePromotion($reversedPromotion, $enrollment, $declineFields));
            } else {
                $enrollment->update($declineFields);
            }

            // Sent via the real queue (see docker/entrypoint.sh's supervised
            // queue:work worker) — a failed send lands in `failed_jobs`
            // instead of vanishing silently, unlike dispatch(...)->afterResponse().
            try {
                $studentData = $enrollment->student_data;
                $recipientEmail = $studentData['student_email'] ?? '';
                $declineReason = $validated['reason'];
                dispatch(function () use ($recipientEmail, $enrollment, $declineReason) {
                    try {
                        Mail::to($recipientEmail)->send(new EnrollmentDeclined($enrollment, $declineReason));
                    } catch (\Throwable $mailException) {
                        Log::warning('Failed to send decline email: ' . get_class($mailException) . ': ' . $mailException->getMessage());
                    }
                });
            } catch (\Exception $mailException) {
                Log::warning('Failed to dispatch decline email: ' . $mailException->getMessage());
                // Continue without failing the entire decline process
            }

            $studentData = $enrollment->student_data;
            $studentName = trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? '')) ?: ($studentData['student_email'] ?? 'student');
            ActivityLogger::log('delete', "Declined enrollment for {$studentName} (#{$enrollment->reference_number}) — reason: {$validated['reason']}" . ($reversedPromotion ? ' (promotion undone)' : ''), 'Enrollment', $enrollment->id);

            $declineMessage = $reversedPromotion
                ? "Enrollment declined and the promotion undone — the student is back on their {$reversedPromotion->from_school_year} enrollment and can be re-assessed."
                : 'Enrollment declined successfully.';

            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $declineMessage
                ]);
            }

            return redirect()->route('admin.enrollments.show', $enrollment)
                ->with('success', $declineMessage);

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
        // Delegates to the single shared, capacity-aware implementation —
        // see PaymentService::assignSectionForEnrollment() for why this used
        // to be three independent (and inconsistently safe) copies.
        \App\Services\PaymentService::assignSectionForEnrollment($enrollment);
    }

    // updatePayment() was removed — it was an independently-written second
    // implementation of admin payment recording, shadowed by a duplicate
    // route registration (see routes/web.php) that made it unreachable in
    // production; the live path is Finance\DashboardController::
    // processAdminPayment(). Confirmed zero references anywhere before
    // removal. It also carried its own bugs (an undefined-array-key on
    // payment_method for plan-only changes, and a phantom ₱0.00 payment
    // audit record for the same case) that are moot now that it's gone.

    // destroy() was removed — it cascaded a single-enrollment delete into
    // hard-deleting the linked student's User account, every enrollment
    // they've ever had, and (via DB cascade) their entire payment history,
    // with no confirmation step. It had zero references anywhere (no route,
    // no internal caller, no test) and existed only as a landmine for a
    // future Route::delete(...) wired up without realizing what it did.
    // Use deleteStudent() for the real, confirmation-gated archive flow.

    /**
     * Change section assignment for an enrollment
     */
    public function changeSection(Request $request, Enrollment $enrollment)
    {
        try {
            $validated = $request->validate([
                'section_id' => 'required|exists:sections,id',
            ]);

            // Same lock-then-recheck shape as SectionController::addStudent()
            // — without it, two near-simultaneous section changes targeting
            // the same destination section could both pass the capacity
            // check below before either commits, pushing it over max_students.
            return DB::transaction(function () use ($validated, $enrollment) {
                $section = \App\Models\Section::where('id', $validated['section_id'])->lockForUpdate()->first();

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

                // Same rules as the student's own Change Section: an active section
                // in the enrollment's school year (the sections list includes
                // inactive and other-year sections too)
                if (!$section->is_active) {
                    return response()->json(['success' => false, 'message' => "Section {$section->name} is not active."], 422);
                }
                if ($enrollment->school_year && $section->school_year !== $enrollment->school_year) {
                    return response()->json(['success' => false, 'message' => "Section {$section->name} belongs to S.Y. {$section->school_year}, not {$enrollment->school_year}."], 422);
                }

                // Check if section has available capacity (live count, not the
                // denormalized column — see Section::getLiveEnrollmentCountAttribute())
                if ($section->live_enrollment_count >= $section->max_students) {
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
                }

                return response()->json([
                    'success'      => true,
                    'message'      => 'Section changed successfully.',
                    'section_name' => $section->name,
                ]);
            });

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
        if ($document->status === 'approved') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This document has already been approved.'], 409);
            }
            return redirect()->back()->with('error', 'This document has already been approved.');
        }

        // Every document type goes through this same simple path — approving
        // a document never credits a payment. (It used to have a separate,
        // more complex branch for 'payment_screenshot' documents that both
        // approved the document and credited the enrollment's payment amount
        // under a lock; removed along with the rest of that flow now that
        // Xendit handles online payments and Cashier handles in-person ones.)
        $document->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        ActivityLogger::log('update', "Approved {$document->document_type} for " . ($document->user->name ?? 'student #' . $document->user_id), 'StudentDocument', $document->id);

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

        // Every document type goes through this same simple path. (It used
        // to have a separate branch reversing payment-crediting side effects
        // for 'payment_screenshot' documents specifically — removed along
        // with the rest of that flow now that Xendit handles online payments
        // and Cashier handles in-person ones, so rejecting a document never
        // needs to reverse a credited payment anymore.)
        $document->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        ActivityLogger::log('delete', "Rejected {$document->document_type} for " . ($document->user->name ?? 'student #' . $document->user_id) . " — reason: {$request->reject_reason}", 'StudentDocument', $document->id);

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
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
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
     * Student Management › Transaction History (admin API): every payment
     * transaction of one student — cashier walk-ins and online (Xendit) — across
     * all school years, optionally narrowed with ?school_year=, plus each year's
     * fee/paid/balance so the modal can show where the account stands.
     */
    public function studentTransactions(Request $request, User $user)
    {
        abort_unless($user->role === 'student', 404);

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('school_year')->orderByDesc('id')
            ->get(['id', 'school_year', 'grade_level', 'status', 'payment_option', 'total_fee', 'payment_amount', 'remaining_balance', 'payment_status']);

        $schoolYear = (string) $request->query('school_year', 'all');
        if ($schoolYear !== 'all' && !$enrollments->contains('school_year', $schoolYear)) {
            $schoolYear = 'all';
        }

        $methodLabels = ['cash' => 'Cash', 'gcash' => 'GCash', 'maya' => 'Maya', 'grabpay' => 'GrabPay', 'bank' => 'Bank Transfer', 'otc' => 'Over-the-Counter'];
        $transactions = \App\Models\PaymentTransaction::where('user_id', $user->id)
            ->with(['enrollment:id,school_year,grade_level', 'installment:id,month_name', 'processedBy:id,name', 'paidInstallments:id,payment_transaction_id,late_fee'])
            ->when($schoolYear !== 'all', fn ($q) => $q->whereHas('enrollment', fn ($e) => $e->where('school_year', $schoolYear)))
            ->orderByDesc('created_at')->orderByDesc('id')
            ->get()
            ->map(function ($t) use ($methodLabels) {
                $when = $t->processed_at ?? $t->created_at;
                $online = $t->payment_type === 'online';
                $description = $t->installment
                    ? $t->installment->month_name . ' Installment'
                    : ($t->description ?: ($t->installment_month ? $t->installment_month . ' Installment' : 'Payment'));
                $lateFee = $t->lateFeePortion();
                if ($lateFee > 0 && stripos($description, 'late fee') === false) {
                    $description .= ' (incl. ₱' . number_format($lateFee, 2) . ' late fee)';
                }
                return [
                    'id'           => $t->id,
                    'date'         => $when?->format('M d, Y'),
                    'time'         => $when?->format('h:i A'),
                    'reference'    => $t->reference_number ?: ('OR-' . str_pad($t->id, 6, '0', STR_PAD_LEFT)),
                    'description'  => $description,
                    'late_fee'     => $lateFee,
                    'school_year'  => $t->enrollment->school_year ?? '—',
                    'grade_level'  => $t->enrollment->grade_level ?? '—',
                    'method'       => ($methodLabels[$t->payment_method] ?? ucfirst((string) $t->payment_method)) . ($online ? ' (Online)' : ''),
                    'channel'      => $online ? 'online' : 'walkin',
                    'processed_by' => $online ? 'Online payment (Xendit)' : ($t->processedBy->name ?? 'Cashier'),
                    'amount'       => (float) $t->amount,
                    'status'       => $t->status,
                ];
            });

        $completed = $transactions->where('status', 'completed');
        $years = $enrollments->map(fn ($e) => [
            'school_year' => $e->school_year,
            'grade_level' => $e->grade_level,
            'status'      => $e->status,
            'plan'        => $e->payment_option,
            'total_fee'   => (float) $e->total_fee,
            'paid'        => (float) $e->payment_amount,
            'balance'     => (float) ($e->remaining_balance ?? max(0, $e->total_fee - $e->payment_amount)),
            'pay_status'  => $e->payment_status,
        ])->unique('school_year')->values();

        return response()->json([
            'student'      => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'lrn' => $user->lrn],
            'school_year'  => $schoolYear,
            'school_years' => $years,
            'summary'      => [
                'count'      => $transactions->count(),
                'total_paid' => round((float) $completed->sum('amount'), 2),
                'pending'    => $transactions->where('status', 'pending')->count(),
            ],
            'transactions' => $transactions->values(),
        ]);
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

        // Scoped to the current school year — without this, every
        // returning/re-enrolled student adds another row per year, so this
        // query grew with all-time enrollment history instead of current
        // enrollment count.
        $schoolYear = Setting::get('current_school_year') ?: $this->getCurrentSchoolYear();

        $enrollmentQuery = Enrollment::whereNotNull('user_id')
            ->where('school_year', $schoolYear)
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

        // Real current section per student, from section_student (the actual
        // membership — DATABASE_NORMALIZATION_PLAN.md Phase 4), batched in one
        // query rather than resolved per-row. enrollments.section is a
        // denormalized copy that isn't guaranteed to stay in sync, so it's
        // used only as a fallback for students section_student hasn't caught up on yet.
        $realSectionByUser = DB::table('section_student')
            ->join('sections', 'sections.id', '=', 'section_student.section_id')
            ->where('sections.is_active', true)
            ->whereIn('section_student.user_id', $userIds)
            ->pluck('sections.name', 'section_student.user_id');

        $data = $enrollments->map(function($enrollment) use ($users, $realSectionByUser) {
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
                'section' => $realSectionByUser->get($user->id) ?? $enrollment->section,
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

        ActivityLogger::log('update', "Updated student record for {$user->name} ({$user->email})", 'User', $user->id);

        return response()->json(['success' => true, 'message' => 'Student updated successfully.']);
    }

    /**
     * Delete student (admin)
     */
    private function removeStudentFromSections(int $userId): void
    {
        // No longer resyncs sections.current_enrollment here — every read path
        // now derives enrollment count live from section_student (Phase 1 of
        // DATABASE_NORMALIZATION_PLAN.md), so there's nothing left to keep in sync.
        DB::table('section_student')->where('user_id', $userId)->delete();
    }

    /**
     * Archive a student (soft delete) — data is preserved and can be restored.
     */
    public function deleteStudent(Request $request, User $user)
    {
        if (!\Illuminate\Support\Facades\Hash::check((string) $request->input('password'), Auth::user()->password)) {
            ActivityLogger::log('denied', "Wrong password entered while trying to archive student {$user->name}", 'User', $user->id);
            return response()->json(['success' => false, 'message' => 'Incorrect password. Please try again.'], 422);
        }

        $this->removeStudentFromSections($user->id);
        $user->delete(); // soft delete — sets deleted_at
        ActivityLogger::log('delete', "Archived student {$user->name} ({$user->email})", 'User', $user->id);
        return response()->json(['success' => true, 'message' => 'Student archived successfully. You can restore them from the Archives tab.']);
    }

    /**
     * Restore an archived student.
     */
    public function restoreStudent($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        ActivityLogger::log('update', "Restored student {$user->name} ({$user->email}) from archive", 'User', $user->id);
        return response()->json(['success' => true, 'message' => 'Student restored successfully.']);
    }

    /**
     * Permanently delete a student — removes all records and files. Cannot be undone.
     */
    public function forceDeleteStudent(Request $request, $id)
    {
        $targetUser = User::withTrashed()->find($id);
        // Super Admin's Archives page confirms destructively via its own
        // saConfirm() dialog (no password field anywhere in that dashboard,
        // same reasoning as TeacherController::destroy()/forceDelete()) —
        // skip the password gate for that role, keep it for Admin.
        if (Auth::user()->role !== 'superadmin' && !\Illuminate\Support\Facades\Hash::check((string) $request->input('password'), Auth::user()->password)) {
            ActivityLogger::log('denied', "Wrong password entered while trying to PERMANENTLY delete student " . ($targetUser->name ?? "#{$id}"), 'User', $id);
            return response()->json(['success' => false, 'message' => 'Incorrect password. Please try again.'], 422);
        }

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
            if (Storage::disk('local')->exists($path)) Storage::disk('local')->delete($path);
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
        $deletedName  = $user->name;
        $deletedEmail = $user->email;
        $user->forceDelete();

        ActivityLogger::log('delete', "PERMANENTLY deleted student {$deletedName} ({$deletedEmail}) and all their records", 'User', $id);

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
                $oldSection = null;
                $user = User::find($studentId);
                if (!$user) continue;

                // Check if student already has an enrollment for the target
                // school year — a declined row doesn't count (matches
                // store()/walkInEnrollment()'s own exclusion, and the unique
                // index itself: 2026_09_20_202025_fix_unique_enrollment_to_exclude_declined.php).
                // Otherwise a student whose re-application was declined for
                // some reason becomes permanently unpromotable.
                $existingEnrollment = Enrollment::where('user_id', $studentId)
                    ->where('school_year', $toSchoolYear)
                    // 'cancelled' = an undone promotion (see undoPromotion()) —
                // doesn't represent a real claim on that school year either,
                // same reasoning as excluding 'declined'.
                ->whereNotIn('status', ['declined', 'cancelled'])
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

                // The school year being left must be fully paid (see PaymentService::promotionBlockReason)
                $fromEnrollment = Enrollment::where('user_id', $studentId)
                    ->where('school_year', $validated['from_school_year'])
                    ->whereIn('status', ['enrolled', 'approved', 'completed'])
                    ->latest('id')->first() ?? $user->latestEnrollment;
                if ($blocked = PaymentService::promotionBlockReason($fromEnrollment)) {
                    $skippedCount++;
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
                        'error_message' => $blocked,
                    ]);
                    continue;
                }

                // Grade 1–6 need all four documents approved too (same rule as assessPromotion)
                $approvedDocs = \App\Models\StudentDocument::approvedRequiredCount($studentId);
                $alreadyAssessed = Promotion::where('student_id', $studentId)
                    ->where('from_school_year', $validated['from_school_year'])->where('status', 'completed')->exists();
                $skipReason = $alreadyAssessed ? 'Already assessed for ' . $validated['from_school_year']
                    : (!in_array($fromGrade, ['nursery', 'kindergarten'], true) && $approvedDocs < count(\App\Models\StudentDocument::REQUIRED_TYPES)
                        ? "Required documents incomplete ({$approvedDocs}/4 approved)" : null);
                if ($skipReason) {
                    $skippedCount++;
                    Promotion::create([
                        'student_id' => $studentId, 'lrn' => $user->lrn, 'from_grade' => $fromGrade, 'to_grade' => $nextGrade,
                        'from_school_year' => $validated['from_school_year'], 'to_school_year' => $toSchoolYear,
                        'promoted_by' => Auth::id(), 'promoted_at' => now(), 'status' => 'skipped', 'error_message' => $skipReason,
                    ]);
                    continue;
                }

                $oldSection = $fromEnrollment?->section
                    ? Section::where('name', $fromEnrollment->section)->where('grade_level', $fromEnrollment->grade_level)
                        ->where('school_year', $validated['from_school_year'])->first()
                    : null;

                // Only the result is recorded — the student re-enrolls from the portal, which creates the
                // new enrollment, completes the old one and moves the section (see assessPromotion()).
                Promotion::create([
                    'student_id' => $studentId,
                    'lrn' => $user->lrn,
                    'from_grade' => $fromGrade,
                    'to_grade' => $nextGrade,
                    'from_school_year' => $validated['from_school_year'],
                    'to_school_year' => $toSchoolYear,
                    'from_section_id' => $oldSection?->id,
                    'to_section_id' => null,
                    'from_status' => $fromEnrollment?->status,
                    'enrollment_id' => null,
                    'previous_enrollment_id' => $fromEnrollment?->id,
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

        $message = "$promotedCount student(s) assessed as promoted to " . ucfirst(str_replace('grade', 'Grade ', $nextGrade)) . " — they can now re-enroll for $toSchoolYear from the Student Portal.";
        if ($skippedCount > 0) {
            $message .= " $skippedCount student(s) were skipped (already enrolled or assessed, unpaid balance, or incomplete documents).";
        }

        ActivityLogger::log('update', "Mass promoted {$promotedCount} student(s) from {$fromGrade} to {$nextGrade} ({$validated['from_school_year']} → {$toSchoolYear}), {$skippedCount} skipped", 'Promotion');

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
        $query = Promotion::with(['student:id,name,lrn', 'fromSection:id,name', 'toSection:id,name', 'promotedBy:id,name', 'enrollment:id,status'])
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

        // can_undo mirrors undoPromotion()'s own guard exactly, so the
        // button only ever appears when the action would actually succeed —
        // computed here (one extra query per row, bounded by per_page) rather
        // than trusted to the frontend.
        $promotions->getCollection()->transform(function ($p) {
            $p->can_undo = $p->status === 'completed' && (
                (!$p->enrollment_id && $p->previous_enrollment_id) // assessed, not re-enrolled yet
                || ($p->enrollment
                    && $p->enrollment->status === 'pending'
                    && !\App\Models\PaymentTransaction::where('enrollment_id', $p->enrollment_id)->exists()));
            return $p;
        });

        return response()->json([
            'success' => true,
            'data' => $promotions,
        ]);
    }

    /**
     * Reverse a promotion — only while the new enrollment it created is
     * still completely untouched (still 'pending', no payment recorded
     * against it). See docs/SCHOOL-YEAR-VISIBILITY-PHASED-PLAN.md Phase 1:
     * once the new enrollment has been approved or paid against, reversing
     * it cleanly would mean unwinding whatever else happened to it too,
     * which this deliberately does not attempt — that stays a manual fix.
     */
    public function undoPromotion(Promotion $promotion)
    {
        if ($promotion->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This promotion is already "' . $promotion->status . '" and cannot be undone.',
            ], 422);
        }

        // Assessed, student hasn't re-enrolled yet: nothing was created or moved — just withdraw the result
        if (!$promotion->enrollment_id && $promotion->previous_enrollment_id) {
            $promotion->update(['status' => 'reversed', 'undone_at' => now(), 'undone_by' => Auth::id()]);
            $studentName = $promotion->student?->name ?? ('student #' . $promotion->student_id);
            ActivityLogger::log('update', "Undid assessment for {$studentName}: {$promotion->from_grade} → {$promotion->to_grade} ({$promotion->from_school_year}) — not yet re-enrolled", 'Promotion', $promotion->id);
            return response()->json(['success' => true, 'message' => 'Assessment undone — ' . $studentName . ' can be assessed again.']);
        }

        if (!$promotion->enrollment_id || !$promotion->previous_enrollment_id || !$promotion->from_status) {
            return response()->json([
                'success' => false,
                'message' => 'This promotion record predates the Undo feature and is missing the data needed to reverse it safely. Undo it manually instead.',
            ], 422);
        }

        $newEnrollment = Enrollment::find($promotion->enrollment_id);
        if (!$newEnrollment) {
            return response()->json(['success' => false, 'message' => 'The enrollment this promotion created no longer exists.'], 422);
        }

        if ($newEnrollment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This student\'s new enrollment is already "' . $newEnrollment->status . '" — too far along to undo automatically. Reverse it manually instead.',
            ], 422);
        }

        if (\App\Models\PaymentTransaction::where('enrollment_id', $newEnrollment->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This student already has a payment recorded against the new enrollment — undo it manually instead.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $this->reversePromotion($promotion, $newEnrollment);

            $studentName = $promotion->student?->name ?? ('student #' . $promotion->student_id);
            ActivityLogger::log(
                'update',
                "Undid promotion for {$studentName}: {$promotion->from_grade} ({$promotion->from_school_year}) → {$promotion->to_grade} ({$promotion->to_school_year})",
                'Promotion',
                $promotion->id
            );

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Promotion undone — ' . $studentName . ' is back on their ' . $promotion->from_school_year . ' enrollment.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Undo promotion failed for promotion #{$promotion->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to undo promotion: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Put a promoted student back on the enrollment they were promoted FROM:
     * cancel the new enrollment, swap the section pivot back, restore the old
     * enrollment's status, mark the promotion reversed. Callers own the
     * transaction and the guards. Shared by undoPromotion() and by declining
     * the enrollment a promotion created — declining it without this left the
     * old enrollment 'completed', so the student vanished from Assessment &
     * Promotion and could neither be re-assessed nor re-enroll.
     */
    private function reversePromotion(Promotion $promotion, Enrollment $newEnrollment, array $newEnrollmentExtra = []): void
    {
        // Cancel the new enrollment — not hard-deleted. Enrollment has
        // no SoftDeletes trait, so a real delete would leave no record
        // this ever happened; 'cancelled' is excluded everywhere the
        // app looks for "this student's current enrollment" (User::
        // latestEnrollment, the duplicate-enrollment checks, the
        // student portal's own enrollment resolver).
        $newEnrollment->update(array_merge($newEnrollmentExtra, ['status' => 'cancelled']));

        if ($newEnrollment->section) {
            $newSection = Section::where('name', $newEnrollment->section)
                ->where('grade_level', $newEnrollment->grade_level)
                ->where('school_year', $newEnrollment->school_year)
                ->first();
            $newSection?->students()->detach($promotion->student_id);
        }

        $oldEnrollment = Enrollment::find($promotion->previous_enrollment_id);
        if ($oldEnrollment) {
            $oldEnrollment->update([
                'status' => $promotion->from_status,
                'completed_at' => null,
            ]);

            if ($promotion->from_section_id) {
                Section::find($promotion->from_section_id)?->students()->syncWithoutDetaching([$promotion->student_id]);
            }
        }

        $promotion->update([
            'status' => 'reversed',
            'undone_at' => now(),
            'undone_by' => Auth::id(),
        ]);
    }

    /**
     * The completed promotion that created this enrollment, if it can still be
     * reversed cleanly (old enrollment known, nothing paid against the new one).
     */
    private function reversiblePromotionFor(Enrollment $enrollment): ?Promotion
    {
        $promotion = Promotion::where('enrollment_id', $enrollment->id)->where('status', 'completed')->latest('id')->first();

        if (!$promotion || !$promotion->previous_enrollment_id || !$promotion->from_status
            || \App\Models\PaymentTransaction::where('enrollment_id', $enrollment->id)->exists()) {
            return null;
        }

        return $promotion;
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
     * Promote or retain a single student — records the result (Promotion); the student re-enrolls from the portal
     */
    public function assessPromotion(Request $request, User $user)
    {
        $validated = $request->validate([
            'action'          => 'required|in:promote,retain',
            'from_school_year'=> 'nullable|string|max:20',
            'to_school_year'  => 'required|string|max:20',
            'to_section_id'   => 'nullable|exists:sections,id',
            'remarks'         => 'nullable|string|max:1000',
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

        // The school year being left must be fully paid — promote or retain, every grade
        // (Nursery/Kinder included). Bulk promote and auto-advance go through here too.
        if ($blocked = PaymentService::promotionBlockReason($currentEnrollment)) {
            return response()->json(['success' => false, 'message' => $blocked . '. Settle it before moving the student to the next school year.'], 422);
        }

        $fromGrade = $currentEnrollment->grade_level;

        // Grade 1–6: all four required documents approved too — the same checklist the student sees on
        // the portal's re-enrollment card. Assessing creates the next year's enrollment, so this is where
        // the documents step is actually enforced (Nursery/Kinder advance without document checks).
        if (!in_array($fromGrade, ['nursery', 'kindergarten'], true)
            && ($approvedDocs = \App\Models\StudentDocument::approvedRequiredCount($user->id)) < count(\App\Models\StudentDocument::REQUIRED_TYPES)) {
            return response()->json(['success' => false, 'message' => "Required documents incomplete ({$approvedDocs}/4 approved). Approve the student's documents before moving them to the next school year."], 422);
        }
        $toGrade   = $validated['action'] === 'promote'
            ? ($gradeMap[$fromGrade] ?? null)
            : $fromGrade; // retain = same grade

        if ($validated['action'] === 'promote' && !$toGrade) {
            return response()->json(['success' => false, 'message' => 'Student is at the last grade level or already graduated.'], 422);
        }

        // Check if already has enrollment for next year — a declined row
        // doesn't count (see massPromote()'s matching fix for the same
        // reasoning; this method is also reused by bulkPromote()/autoAdvance()).
        // 'cancelled' (an undone promotion — see undoPromotion()) doesn't
        // count either, so re-promoting after an undo isn't blocked.
        $existingNext = Enrollment::where('user_id', $user->id)
            ->where('school_year', $validated['to_school_year'])
            ->whereNotIn('status', ['declined', 'cancelled'])
            ->first();

        if ($existingNext) {
            return response()->json(['success' => false, 'message' => 'Student already has an enrollment for ' . $validated['to_school_year'] . '.'], 422);
        }

        // Already assessed out of this school year (and not undone) — change it through Undo instead
        $alreadyAssessed = Promotion::where('student_id', $user->id)
            ->where('from_school_year', $currentEnrollment->school_year)
            ->where('status', 'completed')
            ->exists();
        if ($alreadyAssessed) {
            return response()->json(['success' => false, 'message' => $user->name . ' is already assessed for S.Y. ' . $currentEnrollment->school_year . '. Use Undo in Assessment History to change it.'], 422);
        }

        $toSchoolYear = $validated['to_school_year'];
        $toSection = !empty($validated['to_section_id']) ? Section::find($validated['to_section_id']) : null;
        $fromSection = $currentEnrollment->section
            ? Section::where('name', $currentEnrollment->section)->where('grade_level', $fromGrade)
                ->where('school_year', $currentEnrollment->school_year)->first()
            : null;

        // The assessment only RECORDS the result (decision October 28, 2026). The student then
        // re-enrolls from the portal — or not — and that creates the new enrollment, completes this
        // one and moves the section (StudentPortalController::submitReenrollment). Every grade now
        // re-enrolls the same way; the parent confirms the student is coming back.
        $promotion = Promotion::create([
            'student_id'             => $user->id,
            'lrn'                    => $user->lrn,
            'from_grade'             => $fromGrade,
            'to_grade'               => $toGrade,
            'from_school_year'       => $currentEnrollment->school_year,
            'to_school_year'         => $toSchoolYear,
            'from_section_id'        => $fromSection?->id,
            'to_section_id'          => $toSection?->id,
            'from_status'            => $currentEnrollment->status,
            'enrollment_id'          => null, // set when the student re-enrolls
            'previous_enrollment_id' => $currentEnrollment->id,
            'promoted_by'            => Auth::id(),
            'promoted_at'            => now(),
            'status'                 => 'completed',
            'remarks'                => $validated['remarks'] ?? null,
        ]);

        $actionLabel = $validated['action'] === 'promote'
            ? 'Promoted to ' . ucfirst(str_replace('_', ' ', $toGrade))
            : 'Retained at ' . ucfirst(str_replace('_', ' ', $toGrade));

        ActivityLogger::log('update', "{$user->name}: {$actionLabel} (from {$fromGrade}, {$currentEnrollment->school_year} → {$toSchoolYear}) — awaiting re-enrollment", 'Promotion', $user->id);

        return response()->json([
            'success'      => true,
            'message'      => $user->name . ' — ' . $actionLabel . '. They can now re-enroll for ' . $toSchoolYear . ' from the Student Portal.',
            'promotion_id' => $promotion->id,
        ]);
    }

    /**
     * Split students in a grade/school-year into who can be safely bulk-promoted
     * vs who still needs individual review — used by both the bulk preview and
     * the actual bulk-promote action so they always agree on the same set.
     *
     * Excluded (kept out of bulk, must go through the individual assess modal):
     *   - already has a promotion record for this from_school_year
     *   - has an open/in-progress guidance concern
     *   - has a failing subject (term average < 75, approved grades only) not
     *     yet cleared via a passed summer class
     *
     * @return array{eligible: \Illuminate\Support\Collection, excluded: \Illuminate\Support\Collection}
     */
    /**
     * Whether a student counts toward promotion assessment, given their
     * student_type. Non-transferees always do. A transferee is excluded
     * ONLY while they have no approved proof of having passed their
     * previous grade at their former school — per DepEd policy on private
     * schools: a school cannot withhold promotion solely because Form 137
     * (the Permanent Record) hasn't arrived yet from the old school, as
     * long as SF9 (this system's "report_card" document type) or an
     * equivalent certification of completion is on file. Once that
     * document is approved, the transferee is treated the same as any
     * other student here — even in their very first year, not just after
     * a second year's worth of history accumulates at this school.
     *
     * Previously this was a blanket "student_type !== transferee" check in
     * three places (this page, the Reports tab's own copy, and bulk
     * promotion) — permanently excluding every transferee from promotion
     * forever, including ones who legitimately proved they passed
     * elsewhere. This is the one place all three now call.
     */
    private function isPromotionEligibleByType(int $userId, array $studentData): bool
    {
        if (($studentData['student_type'] ?? '') !== 'transferee') {
            return true;
        }

        return \App\Models\StudentDocument::where('user_id', $userId)
            ->where('document_type', 'report_card')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Grade1-Grade6 promotion policy at this school: nobody is academically
     * retained (private school, no failed students), so grades/guidance are
     * WARNINGS only — shown to the admin but never block promotion. The real
     * gates are administrative: any balance left on the ending school year
     * (PaymentService::promotionBlockReason) or incomplete documents block
     * bulk promotion.
     */
    private function bulkPromotionCandidates(string $grade, string $fromSchoolYear): array
    {
        $students = User::where('role', 'student')
            ->whereHas('enrollments', fn($q) => $q
                ->where('school_year', $fromSchoolYear)
                ->where('grade_level', $grade)
                ->whereIn('status', ['enrolled', 'approved', 'completed'])
            )
            ->with(['enrollments' => fn($q) => $q
                ->where('school_year', $fromSchoolYear)
                ->where('grade_level', $grade)
            ])
            ->get()
            ->filter(fn($s) => $this->isPromotionEligibleByType($s->id, $s->enrollments->first()->student_data ?? []))
            ->values();

        $studentIds = $students->pluck('id');

        // Only live promotions — an undone (reversed), skipped or failed one
        // must not keep the student out of bulk promotion
        $alreadyAssessedIds = Promotion::whereIn('student_id', $studentIds)
            ->where('from_school_year', $fromSchoolYear)
            ->where('status', 'completed')
            ->pluck('student_id')
            ->unique();

        $guidanceCounts = \App\Models\GuidanceRecord::whereIn('student_id', $studentIds)
            ->whereIn('status', ['open', 'in_progress'])
            ->selectRaw('student_id, count(*) as c')
            ->groupBy('student_id')
            ->pluck('c', 'student_id');

        // Numeric failing-grade check — approved grades only, and only the
        // 'grade' column (nursery/kinder use 'descriptive_grade' instead and
        // have no numeric pass/fail threshold, so they never appear here).
        $gradeRows = \App\Models\Grade::whereIn('student_id', $studentIds)
            ->where('school_year', $fromSchoolYear)
            ->where('status', 'approved')
            ->whereNotNull('grade')
            ->get(['student_id', 'subject_id', 'grade']);

        $failingSubjectIds = [];
        foreach ($gradeRows->groupBy('student_id') as $studentId => $rows) {
            $failed = $rows->groupBy('subject_id')
                ->filter(fn($subjectRows) => $subjectRows->avg('grade') < 75)
                ->keys();
            if ($failed->isNotEmpty()) {
                $failingSubjectIds[$studentId] = $failed;
            }
        }

        $summerCleared = \App\Models\SummerClassEnrollment::whereIn('student_id', $studentIds)
            ->where('status', 'passed')
            ->with('summerClass:id,subject_id')
            ->get()
            ->groupBy('student_id')
            ->map(fn($rows) => $rows->pluck('summerClass.subject_id')->filter()->unique());

        // Grade-completeness — a subject still in draft/submitted (or never
        // entered) isn't officially failing OR passing yet. Informational
        // only here (see class-level note above), same as the failing check.
        $requiredSubjectIds = \App\Models\Subject::where('grade_level', $grade)
            ->where('is_active', true)
            ->pluck('id');

        $approvedSubjectsByStudent = collect();
        if ($requiredSubjectIds->isNotEmpty()) {
            $approvedSubjectsByStudent = \App\Models\Grade::whereIn('student_id', $studentIds)
                ->whereIn('subject_id', $requiredSubjectIds)
                ->where('school_year', $fromSchoolYear)
                ->where('status', 'approved')
                ->get(['student_id', 'subject_id'])
                ->groupBy('student_id')
                ->map(fn($rows) => $rows->pluck('subject_id')->unique());
        }

        // Document / payment gates — the actual blockers. Same thresholds as
        // StudentPortalController::getCompletionProgress().
        // All four approved (this used to pass a student with any ONE approved document)
        $studentsWithDocs = collect($studentIds)
            ->filter(fn ($id) => \App\Models\StudentDocument::approvedRequiredCount($id) >= count(\App\Models\StudentDocument::REQUIRED_TYPES))
            ->values();

        $eligible = collect();
        $excluded = collect();

        foreach ($students as $s) {
            $warnings = [];
            $reasons  = [];

            if ($alreadyAssessedIds->contains($s->id)) {
                $reasons[] = 'Already assessed for this school year';
            }

            // Fully paid, installment plans included (a downpayment alone used to count)
            if ($unpaid = PaymentService::promotionBlockReason($s->enrollments->first())) {
                $reasons[] = $unpaid;
            }

            if (!$studentsWithDocs->contains($s->id)) {
                $reasons[] = 'Incomplete required documents';
            }

            if (($guidanceCounts[$s->id] ?? 0) > 0) {
                $warnings[] = $guidanceCounts[$s->id] . ' open guidance concern(s)';
            }

            if (isset($failingSubjectIds[$s->id])) {
                $stillFailing = $failingSubjectIds[$s->id]->diff($summerCleared[$s->id] ?? collect());
                if ($stillFailing->isNotEmpty()) {
                    $warnings[] = $stillFailing->count() . ' failing subject(s) not yet cleared';
                }
            }

            if ($requiredSubjectIds->isNotEmpty()) {
                $approvedIds = $approvedSubjectsByStudent[$s->id] ?? collect();
                $missingIds  = $requiredSubjectIds->diff($approvedIds);
                if ($missingIds->isNotEmpty()) {
                    $warnings[] = $missingIds->count() . ' subject(s) with grades not yet approved';
                }
            }

            if ($reasons) {
                $excluded->push(['student' => $s, 'reasons' => $reasons]);
            } else {
                $eligible->push(['student' => $s, 'warnings' => $warnings]);
            }
        }

        return ['eligible' => $eligible, 'excluded' => $excluded];
    }

    /**
     * Preview who a bulk promotion for this grade/school-year would include vs
     * exclude, without writing anything — powers the confirmation step.
     */
    public function bulkPromotePreview(Request $request)
    {
        $validated = $request->validate([
            'grade'            => 'required|string|max:30',
            'from_school_year' => 'required|string|max:20',
        ]);

        $result = $this->bulkPromotionCandidates($validated['grade'], $validated['from_school_year']);

        return response()->json([
            'success'        => true,
            'eligible_count' => $result['eligible']->count(),
            'eligible'       => $result['eligible']->map(fn($e) => [
                'id'       => $e['student']->id,
                'name'     => $e['student']->name,
                'warnings' => $e['warnings'],
            ])->values(),
            'excluded_count' => $result['excluded']->count(),
            'excluded'       => $result['excluded']->map(fn($e) => [
                'id'      => $e['student']->id,
                'name'    => $e['student']->name,
                'reasons' => $e['reasons'],
            ])->values(),
        ]);
    }

    /**
     * Promote every eligible (non-excluded) student in one grade for the given
     * school-year cycle in one action. Reuses assessPromotion() per student —
     * same validation, same transaction-per-student safety, same audit trail —
     * so this is strictly "assess many students at once", not a separate code
     * path. Anyone flagged by bulkPromotionCandidates() is skipped here and
     * must still go through the individual assess modal.
     */
    public function bulkPromote(Request $request)
    {
        $validated = $request->validate([
            'grade'            => 'required|string|max:30',
            'from_school_year' => 'required|string|max:20',
            'to_school_year'   => 'required|string|max:20',
        ]);

        $result = $this->bulkPromotionCandidates($validated['grade'], $validated['from_school_year']);

        $promoted = [];
        $failed   = [];

        foreach ($result['eligible'] as $entry) {
            $student = $entry['student'];
            $subRequest = Request::create('', 'POST', [
                'action'           => 'promote',
                'from_school_year' => $validated['from_school_year'],
                'to_school_year'   => $validated['to_school_year'],
            ]);

            $response = $this->assessPromotion($subRequest, $student);
            $data     = json_decode($response->getContent(), true);

            if ($data['success'] ?? false) {
                $promoted[] = ['id' => $student->id, 'name' => $student->name];
            } else {
                $failed[] = ['id' => $student->id, 'name' => $student->name, 'message' => $data['message'] ?? 'Unknown error'];
            }
        }

        return response()->json([
            'success'        => true,
            'promoted_count' => count($promoted),
            'skipped_count'  => $result['excluded']->count(),
            'failed_count'   => count($failed),
            'promoted'       => $promoted,
            'failed'         => $failed,
            'skipped'        => $result['excluded']->map(fn($e) => [
                'id'      => $e['student']->id,
                'name'    => $e['student']->name,
                'reasons' => $e['reasons'],
            ])->values(),
            'message'        => count($promoted) . ' student(s) promoted. '
                . $result['excluded']->count() . ' skipped (unpaid balance / incomplete documents). '
                . (count($failed) ? count($failed) . ' failed.' : ''),
        ]);
    }

    /**
     * Nursery→Kindergarten and Kindergarten→Grade1 are developmental, not
     * academic, transitions at this school — no grades, guidance, or document
     * checks apply. The school year must still be fully paid: anyone with a
     * balance is returned under 'excluded' with the reason. Someone already
     * holding an enrollment for the target year isn't listed at all, so
     * clicking this twice doesn't error out on duplicates.
     */
    private function preElemAdvanceCandidates(string $grade, string $fromSchoolYear, string $toSchoolYear): array
    {
        $fromYear = fn($q) => $q
            ->where('school_year', $fromSchoolYear)
            ->where('grade_level', $grade)
            ->whereIn('status', ['enrolled', 'approved', 'completed']);

        $students = User::where('role', 'student')
            ->whereHas('enrollments', $fromYear)
            ->with(['enrollments' => fn($q) => $fromYear($q)->latest('id')])
            // An undone (cancelled) or declined next-year row doesn't count — same as assessPromotion()
            ->whereDoesntHave('enrollments', fn($q) => $q->where('school_year', $toSchoolYear)->whereNotIn('status', ['declined', 'cancelled']))
            // …and anyone already advanced (assessment only records it now; they re-enroll from the portal)
            ->whereDoesntHave('promotions', fn($q) => $q->where('from_school_year', $fromSchoolYear)->where('status', 'completed'))
            ->get();

        $eligible = collect();
        $excluded = collect();
        foreach ($students as $s) {
            if ($reason = PaymentService::promotionBlockReason($s->enrollments->first())) {
                $excluded->push(['student' => $s, 'reasons' => [$reason]]);
            } else {
                $eligible->push($s);
            }
        }

        return ['eligible' => $eligible, 'excluded' => $excluded];
    }

    /**
     * Preview how many Nursery/Kindergarten students would auto-advance.
     */
    public function autoAdvancePreview(Request $request)
    {
        $validated = $request->validate([
            'grade'            => 'required|in:nursery,kindergarten',
            'from_school_year' => 'required|string|max:20',
            'to_school_year'   => 'required|string|max:20',
        ]);

        $result = $this->preElemAdvanceCandidates($validated['grade'], $validated['from_school_year'], $validated['to_school_year']);

        return response()->json([
            'success'        => true,
            'count'          => $result['eligible']->count(),
            'students'       => $result['eligible']->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values(),
            'excluded_count' => $result['excluded']->count(),
            'excluded'       => $result['excluded']->map(fn($e) => [
                'id'      => $e['student']->id,
                'name'    => $e['student']->name,
                'reasons' => $e['reasons'],
            ])->values(),
        ]);
    }

    /**
     * Advance every fully paid Nursery/Kindergarten student in one grade (no
     * grade/guidance/document checks). Still reuses assessPromotion() per
     * student for the actual write (same transaction safety, same audit trail
     * as every other promotion path in this system).
     */
    public function autoAdvance(Request $request)
    {
        $validated = $request->validate([
            'grade'            => 'required|in:nursery,kindergarten',
            'from_school_year' => 'required|string|max:20',
            'to_school_year'   => 'required|string|max:20',
        ]);

        $result = $this->preElemAdvanceCandidates($validated['grade'], $validated['from_school_year'], $validated['to_school_year']);

        $promoted = [];
        $failed   = [];

        foreach ($result['eligible'] as $student) {
            $subRequest = Request::create('', 'POST', [
                'action'           => 'promote',
                'from_school_year' => $validated['from_school_year'],
                'to_school_year'   => $validated['to_school_year'],
            ]);

            $response = $this->assessPromotion($subRequest, $student);
            $data     = json_decode($response->getContent(), true);

            if ($data['success'] ?? false) {
                $promoted[] = ['id' => $student->id, 'name' => $student->name];
            } else {
                $failed[] = ['id' => $student->id, 'name' => $student->name, 'message' => $data['message'] ?? 'Unknown error'];
            }
        }

        $skipped = $result['excluded']->count();

        return response()->json([
            'success'        => true,
            'promoted_count' => count($promoted),
            'skipped_count'  => $skipped,
            'failed_count'   => count($failed),
            'promoted'       => $promoted,
            'failed'         => $failed,
            'message'        => count($promoted) . ' student(s) advanced.'
                . ($skipped ? ' ' . $skipped . ' skipped (unpaid balance).' : '')
                . (count($failed) ? ' ' . count($failed) . ' failed.' : ''),
        ]);
    }

    /**
     * Return sections available for a given grade level and school year
     */
    public function sectionsForGrade(Request $request)
    {
        // current_enrollment in the response is the live section_student count,
        // not the denormalized column — see Section::getLiveEnrollmentCountAttribute()
        $sections = Section::where('grade_level', $request->grade)
            ->where('school_year', $request->school_year)
            ->where('is_active', true)
            ->withCount('students')
            ->orderBy('name')
            ->get(['id', 'name', 'max_students'])
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'current_enrollment' => $s->students_count,
                'max_students' => $s->max_students,
            ]);

        return response()->json(['success' => true, 'data' => $sections]);
    }

    /**
     * Change enrollment status (admin — supports dropped, ghost, transferred, etc.)
     */
    public function changeEnrollmentStatus(Request $request, Enrollment $enrollment)
    {
        // 'enrolled' is deliberately not an allowed target here — that
        // transition must only happen through the real payment flow
        // (updatePayment() / processAdminPayment() / PaymentService), which
        // enforces the actual payment requirement and assigns a section.
        // This endpoint is for admin corrections (dropped, ghost,
        // transferred, completed, declined, or reverting to pending/approved).
        $request->validate([
            'status'  => 'required|in:pending,approved,declined,completed,dropped,ghost,transferred',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($enrollment->status === $request->status) {
            return response()->json([
                'success' => true,
                'message' => 'Status is already ' . ucfirst($request->status) . '.',
            ]);
        }

        // Declining an enrollment that Assessment & Promotion created undoes the
        // promotion (same as decline()), instead of stranding the student.
        $reversedPromotion = $request->status === 'declined' ? $this->reversiblePromotionFor($enrollment) : null;
        if ($reversedPromotion) {
            DB::transaction(fn () => $this->reversePromotion($reversedPromotion, $enrollment, [
                'declined_at' => now(),
                'declined_by' => Auth::id(),
                'decline_reason' => $request->remarks,
            ]));
        } else {
            $enrollment->update([
                'status' => $request->status,
            ]);
        }

        ActivityLogger::log(
            'update',
            "Changed enrollment status to {$request->status}" . ($request->remarks ? " ({$request->remarks})" : '') . ($reversedPromotion ? ' (promotion undone)' : ''),
            'Enrollment',
            $enrollment->id
        );

        return response()->json([
            'success' => true,
            'message' => $reversedPromotion
                ? "Declined and the promotion undone — the student is back on their {$reversedPromotion->from_school_year} enrollment and can be re-assessed."
                : 'Status updated to ' . ucfirst($request->status) . '.',
        ]);
    }

    /**
     * Return submitted grades grouped by subject for the assess modal
     */
    public function getStudentGradesForAssess(User $user)
    {
        $enrollment = $user->enrollments()
            ->whereNotIn('status', ['declined', 'cancelled'])
            ->orderByRaw("school_year DESC")
            ->latest('id')
            ->first();

        $gradeLevel = $enrollment ? ($enrollment->grade_level ?? ($enrollment->student_data['grade_level'] ?? null)) : null;
        $schoolYear = $enrollment ? $enrollment->school_year : null;

        // All subjects for this grade level
        $subjects = $gradeLevel
            ? \App\Models\Subject::where('grade_level', $gradeLevel)->where('is_active', true)->orderBy('name')->get()
            : collect();

        // Submitted and approved grades only — a teacher's draft is unfinished work (not shown), and
        // a returned grade is being redone. Approved = official; submitted = waiting in Grade Oversight.
        $grades = Grade::where('student_id', $user->id)
            ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear))
            ->whereIn('status', ['submitted', 'approved'])
            ->with('subject:id,name')
            ->get();

        $gradesBySubjectTerm = $grades->groupBy('subject_id')->map(fn($rows) => $rows->keyBy('term'));

        $isDescriptive = \App\Models\Grade::isNurseryKinder($gradeLevel ?? '');

        // "submitted" while any term still waits for approval, "approved" once every entered term is
        $subjectStatus = function ($termGrades) {
            $statuses = $termGrades->pluck('status')->unique()->values()->toArray();
            if (in_array('submitted', $statuses)) return 'submitted';
            if (in_array('approved', $statuses))  return 'approved';
            return 'not_entered';
        };

        $result = $subjects->map(function ($subject) use ($gradesBySubjectTerm, $isDescriptive, $subjectStatus) {
            $termGrades = $gradesBySubjectTerm->get($subject->id, collect());
            $t1 = $termGrades->get(1);
            $t2 = $termGrades->get(2);
            $t3 = $termGrades->get(3);
            $termStatus = ['term1_status' => $t1?->status, 'term2_status' => $t2?->status, 'term3_status' => $t3?->status];

            if ($isDescriptive) {
                return [
                    'subject'          => $subject->name,
                    'term1'            => $t1?->descriptive_grade,
                    'term2'            => $t2?->descriptive_grade,
                    'term3'            => $t3?->descriptive_grade,
                    'average'          => null,
                    'remarks'          => $termGrades->sortByDesc('term')->first()?->remarks ?? null,
                    'status'           => $subjectStatus($termGrades),
                    'is_descriptive'   => true,
                ] + $termStatus;
            }

            // Average and remarks count APPROVED terms only (official), like the promotion warnings
            $approved = $termGrades->where('status', 'approved')->pluck('grade')->filter(fn($v) => $v !== null)->map(fn($v) => (float) $v);
            $avg  = $approved->count() ? round($approved->avg(), 2) : null;

            return [
                'subject'        => $subject->name,
                'term1'          => $t1?->grade,
                'term2'          => $t2?->grade,
                'term3'          => $t3?->grade,
                'average'        => $avg,
                'remarks'        => $avg !== null ? Grade::getRemarks($avg) : null,
                'status'         => $subjectStatus($termGrades),
                'is_descriptive' => false,
            ] + $termStatus;
        })->values();

        // If no subjects found for grade level, fall back to whatever grade records exist
        if ($result->isEmpty() && $grades->isNotEmpty()) {
            $result = $grades->groupBy('subject_id')->map(function ($rows) use ($subjectStatus) {
                $byTerm = $rows->keyBy('term');
                $approved = $rows->where('status', 'approved')->pluck('grade')->filter(fn($v) => $v !== null)->map(fn($v) => (float) $v);
                $avg = $approved->count() ? round($approved->avg(), 2) : null;
                return [
                    'subject' => optional($rows->first()->subject)->name ?? 'Unknown',
                    'term1'   => optional($byTerm->get(1))->grade,
                    'term2'   => optional($byTerm->get(2))->grade,
                    'term3'   => optional($byTerm->get(3))->grade,
                    'term1_status' => optional($byTerm->get(1))->status,
                    'term2_status' => optional($byTerm->get(2))->status,
                    'term3_status' => optional($byTerm->get(3))->status,
                    'average' => $avg,
                    'remarks' => $avg !== null ? Grade::getRemarks($avg) : null,
                    'status'  => $subjectStatus($rows),
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

    /**
     * Guidance records for a student, shown in the assessment modal so the
     * admin sees behavioral/counseling history before deciding promote/retain.
     * Informational only — does not block or auto-decide anything.
     */
    public function getStudentGuidanceForAssess(User $user)
    {
        $records = \App\Models\GuidanceRecord::where('student_id', $user->id)
            ->with('counselor:id,name')
            ->orderByDesc('date')
            ->get(['id', 'counselor_id', 'date', 'concern_type', 'concern_description', 'status', 'follow_up_date'])
            ->toArray();

        return response()->json(['records' => $records]);
    }

    /**
     * Lightweight student lookup for type-ahead pickers (e.g. the Guidance
     * Records "Add Record" modal). The Student Management table's $students
     * is paginated 15-at-a-time and filtered by whatever tab filters are
     * active, so it can't be reused as a full roster source once the school
     * has 250+ students — this queries the full students table directly.
     */
    public function searchStudents(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Used by the Guidance student picker and the top-bar quick jump (Admin / Super Admin)
        $grades = ['nursery' => 'Nursery', 'kindergarten' => 'Kinder', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2', 'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];
        $students = User::where('role', 'student')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('lrn', 'like', "%{$q}%");
            })
            ->with('latestEnrollment')
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email', 'lrn'])
            ->map(function ($s) use ($grades) {
                $enr   = $s->latestEnrollment;
                $grade = ($enr->student_data['grade_level'] ?? null) ?: ($enr->grade_level ?? null);
                return [
                    'id'          => $s->id,
                    'name'        => $s->name,
                    'email'       => $s->email,
                    'lrn'         => $s->lrn,
                    'grade_label' => $grades[$grade] ?? null,
                    'section'     => ($enr && $enr->section && $enr->section !== 'Unassigned') ? $enr->section : null,
                ];
            });

        return response()->json($students);
    }

    /**
     * Failing subjects (term average < 75, same rule as the Grades tab) for a
     * student, and whether each one has been cleared via a passed summer class.
     * Informational only — shown in the assessment modal, does not block anything.
     */
    public function getStudentSummerForAssess(User $user)
    {
        $schoolYear = $this->getCurrentSchoolYear();

        // Approved grades only — matches the same registrar gate the student
        // portal now enforces, so a pending/rejected grade can't flag a subject
        // as failing here before it's actually official.
        $grades = \App\Models\Grade::where('student_id', $user->id)
            ->where('school_year', $schoolYear)
            ->where('status', 'approved')
            ->whereNotNull('grade')
            ->with('subject:id,name')
            ->get();

        $failing = $grades->groupBy('subject_id')
            ->map(fn($rows) => [
                'subject_id'   => $rows->first()->subject_id,
                'subject_name' => $rows->first()->subject->name ?? 'Subject #' . $rows->first()->subject_id,
                'average'      => round($rows->avg('grade'), 2),
            ])
            ->filter(fn($row) => $row['average'] < 75)
            ->values();

        $subjects = $failing->map(function ($row) use ($user, $schoolYear) {
            $enrollment = \App\Models\SummerClassEnrollment::where('student_id', $user->id)
                ->whereHas('summerClass', fn($q) => $q->where('subject_id', $row['subject_id']))
                ->with('summerClass:id,subject_id,start_date,end_date')
                ->latest('id')
                ->first();

            // Not enrolled anywhere yet — check whether a summer class already
            // exists for this subject this year that the admin could enroll them
            // into directly, without leaving this modal.
            $availableClassId = null;
            if (!$enrollment) {
                $availableClassId = \App\Models\SummerClass::where('subject_id', $row['subject_id'])
                    ->where('school_year', $schoolYear)
                    ->whereIn('status', ['upcoming', 'ongoing'])
                    ->value('id');
            }

            return [
                'subject_id'         => $row['subject_id'],
                'subject_name'       => $row['subject_name'],
                'failing_grade'      => $row['average'],
                'summer_status'      => $enrollment->status ?? 'not_enrolled', // enrolled, passed, failed, dropped, not_enrolled
                'summer_grade'       => $enrollment->summer_grade ?? null,
                'available_class_id' => $availableClassId,
            ];
        })->values();

        return response()->json(['subjects' => $subjects]);
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
     * Get current school year. Delegates to Setting::getCurrentSchoolYear()
     * — the single source of truth — so editing "Current School Year" in
     * Admin > Settings actually takes effect here, which it didn't before
     * (this used to derive the year from Section data only, ignoring the
     * setting entirely). See docs/system-improvement-plan.md.
     */
    private function getCurrentSchoolYear(): string
    {
        return \App\Models\Setting::getCurrentSchoolYear();
    }

    // ── GRADE OVERSIGHT ──────────────────────────────────────────────

    public function pendingGrades(Request $request)
    {
        $grades = Grade::with(['student:id,name,lrn', 'teacher:id,name', 'subject:id,name,code,grade_level', 'enrollment:id,section,grade_level'])
            ->where('status', 'submitted')
            ->get();

        // Grade level + section of each grade: its own enrollment, else the learner's enrollment
        // for that school year (grades saved from the grade table often have no enrollment_id).
        $yearEnrollments = Enrollment::whereIn('user_id', $grades->pluck('student_id')->unique())
            ->whereIn('school_year', $grades->pluck('school_year')->unique())
            ->whereNotIn('status', ['declined', 'cancelled'])
            ->orderByDesc('id')
            ->get(['id', 'user_id', 'school_year', 'section', 'grade_level'])
            ->unique(fn ($e) => $e->user_id . '|' . $e->school_year)
            ->keyBy(fn ($e) => $e->user_id . '|' . $e->school_year);
        $placeOf = fn ($g) => $g->enrollment ?? $yearEnrollments->get($g->student_id . '|' . $g->school_year);

        $gradeLabels = ['nursery' => 'Nursery', 'kindergarten' => 'Kindergarten', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2',
                        'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];

        // One batch = one teacher's subject, term and school year for ONE section (so approving a
        // card never touches the same subject in the teacher's other section)
        $groups = $grades
            ->groupBy(fn ($g) => implode('|', [
                $g->teacher_id, $g->subject_id ?? 'null', $g->term, $g->school_year,
                $placeOf($g)?->grade_level ?? $g->subject?->grade_level ?? '', $placeOf($g)?->section ?? '',
            ]))
            ->map(function ($rows) use ($placeOf, $gradeLabels) {
                $first   = $rows->first();
                $place   = $placeOf($first);
                $level   = $place?->grade_level ?? $first->subject?->grade_level;
                $numbers = $rows->pluck('grade')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
                return [
                    'teacher'      => $first->teacher?->name ?? 'Unknown',
                    'subject'      => $first->subject?->name ?? 'General',
                    'term'         => $first->term,
                    'school_year'  => $first->school_year,
                    'teacher_id'   => $first->teacher_id,
                    'subject_id'   => $first->subject_id,
                    'grade_level'  => $level,
                    'grade_label'  => $gradeLabels[$level] ?? ($level ? ucfirst($level) : '—'),
                    'section'      => $place?->section ?? '—',
                    'submitted_at' => optional($rows->max('updated_at'))->format('M d, Y g:i A'),
                    'count'        => $rows->count(),
                    'average'      => $numbers->count() ? round($numbers->avg(), 2) : null,
                    'passed'       => $numbers->filter(fn ($v) => $v >= 75)->count(),
                    'failed'       => $numbers->filter(fn ($v) => $v < 75)->count(),
                    'ids'          => $rows->pluck('id')->values(),
                    'grades'       => $rows->sortBy(fn ($g) => $g->student?->name)->map(fn ($g) => $this->oversightGradeRow($g))->values(),
                ];
            })
            ->sortBy(fn ($b) => [$b['school_year'], $b['grade_level'], $b['section'], $b['subject'], $b['term']])
            ->values();

        return response()->json(['success' => true, 'data' => $groups]);
    }

    /**
     * Registrar correction of one grade waiting for review (Grade Oversight › Edit). Keeps the
     * teacher's original value so the teacher's grade table shows "Edited by registrar: 70 → 78",
     * and writes the change to the System Logs. The grade stays "submitted" until approved.
     */
    public function editSubmittedGrade(Request $request, Grade $grade)
    {
        if ($grade->status !== 'submitted') {
            return response()->json(['success' => false, 'message' => 'Only grades waiting for review can be edited here.'], 422);
        }

        $fmt = fn ($v) => $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
        $isDescriptive = $grade->descriptive_grade !== null && $grade->descriptive_grade !== '';

        if ($isDescriptive) {
            $new = $request->validate(['descriptive_grade' => 'required|in:' . implode(',', Grade::DESCRIPTIVE_GRADES)])['descriptive_grade'];
            $old = $grade->descriptive_grade;
        } else {
            $new = $fmt($request->validate(['grade' => 'required|numeric|min:0|max:100'])['grade']);
            $old = $fmt($grade->grade);
        }

        if ($new === $old) {
            return response()->json(['success' => true, 'message' => 'No change.', 'grade' => $this->oversightGradeRow($grade)]);
        }

        // The teacher's value is kept from the first correction; changing it back clears the note
        $original = $grade->original_grade ?? $old;
        $backToTeacher = $new === $original;

        $grade->update([
            'grade'             => $isDescriptive ? $grade->grade : (float) $new,
            'descriptive_grade' => $isDescriptive ? $new : $grade->descriptive_grade,
            'remarks'           => $isDescriptive ? Grade::getDescriptiveLabel($new) : Grade::getRemarks((float) $new),
            'original_grade'    => $backToTeacher ? null : $original,
            'edited_by'         => $backToTeacher ? null : Auth::id(),
            'edited_at'         => $backToTeacher ? null : now(),
        ]);

        $grade->loadMissing(['student:id,name', 'subject:id,name', 'teacher:id,name']);
        ActivityLogger::log('update', "Changed {$grade->student?->name}'s {$grade->subject?->name} Term {$grade->term} grade from {$old} to {$new}"
            . " (S.Y. {$grade->school_year}, teacher: {$grade->teacher?->name})", 'Grade', $grade->id);

        return response()->json(['success' => true, 'message' => "Grade changed from {$old} to {$new}.", 'grade' => $this->oversightGradeRow($grade->fresh(['student:id,name,lrn']))]);
    }

    /** One learner row of a Grade Oversight card. */
    private function oversightGradeRow(Grade $g): array
    {
        return [
            'id'                => $g->id,
            'student'           => $g->student?->name ?? 'Unknown',
            'lrn'               => $g->student?->lrn,
            'grade'             => $g->grade,
            'descriptive_grade' => $g->descriptive_grade,
            'remarks'           => $g->remarks,
            'original_grade'    => $g->original_grade,
        ];
    }

    /**
     * The submitted grades an Approve / Return acts on: exactly the batch's grade_ids when given
     * (the Grade Oversight cards send these), else every submitted grade of that teacher, subject,
     * term and school year.
     */
    private function submittedGradesFor(Request $request)
    {
        return Grade::where('status', 'submitted')
            ->when($request->filled('grade_ids'), fn ($q) => $q->whereIn('id', $request->grade_ids))
            ->when(!$request->filled('grade_ids'), fn ($q) => $q
                ->where('teacher_id', $request->teacher_id)
                ->where('term', $request->term)
                ->where('school_year', $request->school_year)
                ->when($request->subject_id, fn ($q) => $q->where('subject_id', $request->subject_id))
                ->when(!$request->subject_id, fn ($q) => $q->whereNull('subject_id')));
    }

    public function approveGrades(Request $request)
    {
        $request->validate([
            'grade_ids'   => 'nullable|array',
            'grade_ids.*' => 'integer',
            'teacher_id'  => 'required|exists:users,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer',
            'school_year' => 'required|string',
        ]);

        $count = $this->submittedGradesFor($request)->update(['status' => 'approved']);

        $teacherName = User::find($request->teacher_id)?->name ?? "#{$request->teacher_id}";
        ActivityLogger::log('update', "Approved {$count} grade(s) submitted by {$teacherName} (Term {$request->term}, S.Y. {$request->school_year})", 'Grade');

        return response()->json(['success' => true, 'message' => "$count grade(s) approved."]);
    }

    public function rejectGrades(Request $request)
    {
        $request->validate([
            'grade_ids'   => 'nullable|array',
            'grade_ids.*' => 'integer',
            'teacher_id'  => 'required|exists:users,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'term'        => 'required|integer',
            'school_year' => 'required|string',
            'reason'      => 'nullable|string|max:500',
        ]);

        $count = $this->submittedGradesFor($request)->update(['status' => 'rejected']);

        $teacherName = User::find($request->teacher_id)?->name ?? "#{$request->teacher_id}";
        ActivityLogger::log('delete', "Returned {$count} grade(s) to {$teacherName} for revision (Term {$request->term}, S.Y. {$request->school_year})" . ($request->reason ? " — reason: {$request->reason}" : ''), 'Grade');

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

        // Load subjects per enrollment via section relationship. This walks
        // every school year the student was ever enrolled in — section_student
        // only tracks CURRENT membership, so it can only be trusted for
        // whichever one of these rows is the actual latest enrollment; every
        // other (past) row must keep using its own historical enrollments.section
        // string, which is the only record of where they were that year. See
        // DATABASE_NORMALIZATION_PLAN.md Phase 4.
        $latestEnrollmentId = $user->latestEnrollment?->id;
        $advisers = [];
        foreach ($enrollments as $enr) {
            $section = ($enr->id === $latestEnrollmentId ? $user->current_section : null)
                ?? \App\Models\Section::where('name', $enr->section)
                    ->where('grade_level', $enr->grade_level)
                    ->first();
            $enr->setRelation('subjects', $section ? $section->subjects()->orderBy('name')->get() : collect());
            $advisers[$enr->id] = $section?->advisory_teacher?->name
                ?? ($section?->teacher_id ? User::find($section->teacher_id)?->name : null);
        }

        // All grades for this student — approved only. SF10 is the official
        // Learner's Permanent Academic Record; a draft, still-pending, or
        // registrar-rejected grade isn't final and shouldn't appear on it.
        $grades = \App\Models\Grade::where('student_id', $user->id)
            ->where('status', 'approved')
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
            'schoolName'          => ($settings['school_name'] ?? '') ?: 'IEMELIF Learning Center',
            'schoolAddress'       => ($settings['school_address'] ?? '') ?: 'General Tinio, Nueva Ecija',
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

        // ── Official SF10-ES: one Scholastic Record block per school year ──
        $gradeLabels = ['nursery' => 'Nursery', 'kindergarten' => 'Kindergarten', 'grade1' => '1', 'grade2' => '2',
                        'grade3' => '3', 'grade4' => '4', 'grade5' => '5', 'grade6' => '6'];
        $nextGrade   = ['nursery' => 'Kindergarten', 'kindergarten' => '1', 'grade1' => '2', 'grade2' => '3',
                        'grade3' => '4', 'grade4' => '5', 'grade5' => '6', 'grade6' => ''];
        $data['records'] = $enrollments->map(function ($enr) use ($grades, $gradeLabels, $advisers) {
            $isDescriptive = \App\Models\Grade::isNurseryKinder($enr->grade_level ?? '');
            $rows = $enr->subjects->map(function ($sub) use ($grades, $enr, $isDescriptive) {
                $mine  = $grades->where('subject_id', $sub->id)->where('school_year', $enr->school_year);
                $terms = array_map(function ($n) use ($mine, $isDescriptive) {
                    $g = $mine->firstWhere('term', $n);
                    return $isDescriptive ? $g?->descriptive_grade : $g?->grade;
                }, [1, 2, 3]);
                // Final Rating only once all 3 terms are in (numeric grades only)
                $final = (!$isDescriptive && !in_array(null, $terms, true)) ? (int) round(array_sum($terms) / 3) : null;
                return [
                    'name'    => $sub->name,
                    'terms'   => $terms,
                    'final'   => $final,
                    'remarks' => $final === null ? '' : ($final >= 75 ? 'Passed' : 'Failed'),
                ];
            })->values()->all();
            $finals = array_column($rows, 'final');
            $ga = ($finals && !in_array(null, $finals, true)) ? (int) round(array_sum($finals) / count($finals)) : null;
            return [
                'grade'      => $gradeLabels[$enr->grade_level] ?? ucfirst((string) $enr->grade_level),
                'gradeLevel' => $enr->grade_level,
                'section'    => $enr->section,
                'schoolYear' => $enr->school_year,
                'adviser'    => $advisers[$enr->id] ?? '',
                'rows'       => $rows,
                'ga'         => $ga,
                'gaRemarks'  => $ga === null ? '' : ($ga >= 75 ? 'Passed' : 'Failed'),
            ];
        })->values()->all();

        // Eligibility for Elementary School Enrolment: Kinder done here, or the school they came from
        $kinderHere = $enrollments->firstWhere('grade_level', 'kindergarten');
        $prev = $user->previousSchool;
        $data['eligibility'] = [
            'kinderProgressReport' => (bool) $kinderHere,
            'schoolName'    => $kinderHere ? $data['schoolName'] : ($prev->school_name ?? ''),
            'schoolAddress' => $kinderHere ? $data['schoolAddress'] : ($prev->school_address ?? ''),
        ];

        // Certification: eligible for the next grade when the latest year's General Average passed
        $last = end($data['records']) ?: null;
        $data['eligibleForGrade'] = ($last && $last['ga'] !== null && $last['ga'] >= 75) ? ($nextGrade[$last['gradeLevel']] ?? '') : '';
        $data['lastSchoolYear']   = $last['schoolYear'] ?? '';
        $data['lrn']              = $user->lrn ?? '';
        $data['schoolId']         = \App\Models\Setting::get('school_id') ?: '';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sf10', $data);
        // Official SF10-ES is printed on legal-size paper
        $pdf->setPaper('legal', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultPaperSize'     => 'legal',
            'marginTop'            => 1,
            'marginRight'          => 1,
            'marginBottom'         => 1,
            'marginLeft'           => 1,
        ]);

        $filename = 'SF10_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name) . '_' . date('Ymd') . '.pdf';

        // ?print=1: shown in the browser for its print window (which can also save as PDF)
        return request()->boolean('print') ? $pdf->stream($filename) : $pdf->download($filename);
    }
}
