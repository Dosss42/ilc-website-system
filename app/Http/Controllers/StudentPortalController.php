<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Promotion;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use App\Models\Section;
use App\Models\Schedule;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use App\Models\PaymentInstallment;
use App\Models\OtpVerification;
use App\Mail\PasswordChangeOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentPortalController extends Controller
{
    /**
     * Show student portal dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // If we just landed back from a Xendit payment redirect, look up that transaction
        // so the view can show a persistent "Payment Received" confirmation card.
        $justPaidTransaction = null;
        if ($paidRef = request()->query('paid_ref')) {
            $justPaidTransaction = \App\Models\PaymentTransaction::where('reference_number', $paidRef)
                ->where('user_id', $user->id)
                ->first();
        }
        // 'failed' means Xendit sent the parent back via failure_redirect_url
        // (declined / cancelled at checkout). The transaction itself may still
        // show 'pending' at this instant (the EXPIRED webhook can arrive after
        // the redirect) so the view uses this flag rather than the raw status
        // to decide whether to show the error state.
        $paymentOutcome = request()->query('outcome');

        // Phase 4 — summer-class visibility: the student's own remediation
        // history across every school year, read-only (no self-enroll — see
        // the Phase 2 design decision). Keyed off the user directly, not the
        // current enrollment, so history from a prior year still shows even
        // after the student moves on to a new enrollment record.
        $summerClassEnrollments = \App\Models\SummerClassEnrollment::where('student_id', $user->id)
            ->with(['summerClass.subject:id,name,code', 'summerClass.teacher:id,name'])
            ->orderByDesc('id')
            ->get();

        // Prefer the newest school year first, then the most-advanced status within that year.
        // This ensures a just-approved re-enrollment for 2027-2028 takes over from 2026-2027 enrolled.
        $enrollment = $user->enrollments()
            ->orderByRaw("school_year DESC")
            ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
            ->latest('id')
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::where('student_data->student_email', $user->email)
                ->orderByRaw("school_year DESC")
                ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
                ->latest('id')
                ->first();
        }

        if (!$enrollment) {
            return view('studentportal', [
                'enrollment' => null,
                'progress' => null,
                'documents' => collect([]),
                'profileComplete' => false,
                'schedules' => collect([]),
                'justPaidTransaction' => $justPaidTransaction,
                'paymentOutcome' => $paymentOutcome,
                'summerClassEnrollments' => $summerClassEnrollments,
            ]);
        }

        // Get completion progress
        $progress = $this->getCompletionProgress($enrollment);

        // Check if profile is fully complete (all sections + payment + required documents)
        $d = $enrollment->student_data ?? [];
        
        // Check if required documents are uploaded
        $requiredDocTypes = ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture'];
        $uploadedDocTypes = StudentDocument::where(function($q) use ($enrollment, $user) {
                $q->where('enrollment_id', $enrollment->id)
                  ->orWhere('user_id', $user->id);
            })
            ->whereIn('document_type', $requiredDocTypes)
            ->where('status', 'approved')
            ->pluck('document_type')
            ->unique()
            ->count();
        $hasRequiredDocuments = $uploadedDocTypes >= 1; // At least 1 required document uploaded
        
        // For installment plans payment is considered complete once the downpayment is approved
        // (payment_status becomes 'partial'). Only Option A full-payment students need 'paid'.
        $isInstallmentPlan = $enrollment->payment_type === 'installment'
            || in_array($enrollment->payment_option, ['B', 'C', 'D']);
        $paymentDone = $isInstallmentPlan
            ? in_array($enrollment->payment_status ?? 'pending', ['partial', 'paid'])
            : ($enrollment->payment_status ?? 'pending') === 'paid';

        $profileComplete = !empty($d['first_name']) && !empty($d['last_name']) && !empty($d['birthdate']) && !empty($d['gender'])
            && !empty($d['province']) && !empty($d['city']) && !empty($d['barangay']) && !empty($d['street_address'])
            && !empty($d['guardian_name']) && !empty($d['relationship']) && !empty($d['guardian_phone'])
            && !empty($d['last_school']) && !empty($d['grade_level'])
            && !empty($d['student_type'])
            && $paymentDone
            && $hasRequiredDocuments;

        $documents = collect([]);

        // Get student's section from enrollment or pivot table
        $sectionName = $enrollment->section ?? null;
        $section = null;
        $schedules = collect([]);

        if ($sectionName) {
            $section = Section::where('name', $sectionName)
                ->where('grade_level', $d['grade_level'] ?? null)
                ->where('is_active', true)
                ->first();
        }

        // Fallback: find section via section_student pivot table
        if (!$section) {
            $section = Section::whereHas('students', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_active', true)->first();
        }

        if ($section) {
            $schedules = Schedule::where('section_id', $section->id)
                ->where('is_active', true)
                ->with(['subject', 'teacher'])
                ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->orderBy('start_time')
                ->get();
        }

        // Get payment installments for installment plans
        $paymentInstallments = collect([]);
        $paymentSummary = null;
        if ($enrollment && ($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']))) {
            // Create installments if they don't exist
            PaymentService::createInstallments($enrollment);

            // Fix installment statuses that weren't updated due to old overdue-lookup bug
            PaymentService::reconcileInstallmentStatuses($enrollment);

            // Get payment summary
            $paymentSummary = PaymentService::getPaymentSummary($enrollment);
            $paymentInstallments = $paymentSummary['installments'] ?? collect([]);
        }

        // Available sections for the student's grade level (for change-section feature)
        $availableSections = collect();
        if ($enrollment && $enrollment->status === 'enrolled' && $enrollment->grade_level) {
            $schoolYear = $enrollment->school_year ?? (now()->year . '-' . (now()->year + 1));
            $availableSections = Section::where('grade_level', $enrollment->grade_level)
                ->where('school_year', $schoolYear)
                ->where('is_active', true)
                ->with('teacher:id,name')
                ->get();
        }

        // ── Re-enrollment detection ──
        $enrollmentWindowOpen = Setting::get('enrollment_open', true);

        // Target school year: admin-configured, or derived from current_school_year setting
        $savedTargetYear  = Setting::get('enrollment_target_year', '');
        $adminCurrentSY   = Setting::get('current_school_year', '');

        if ($savedTargetYear) {
            $targetSchoolYear = $savedTargetYear;
        } elseif ($adminCurrentSY && preg_match('/^(\d{4})-(\d{4})$/', $adminCurrentSY, $m)) {
            // 2026-2027 → next year is 2027-2028
            $targetSchoolYear = $m[2] . '-' . ((int)$m[2] + 1);
        } else {
            $baseY = now()->month >= 6 ? now()->year : now()->year - 1;
            $targetSchoolYear = ($baseY + 1) . '-' . ($baseY + 2);
        }

        $currentSchoolYear  = $targetSchoolYear; // passed to view for display
        $needsReenrollment  = false;
        $reenrollmentOpen   = false;
        $suggestedGrade     = null;
        $promotionRecord    = null;

        // Full grade progression (used for auto-advance and transferee)
        $gradeProgressionMap = [
            'nursery'     => 'kindergarten',
            'kindergarten'=> 'grade1',
            'grade1'      => 'grade2',
            'grade2'      => 'grade3',
            'grade3'      => 'grade4',
            'grade4'      => 'grade5',
            'grade5'      => 'grade6',
            'grade6'      => 'graduated',
        ];
        // Grades that auto-advance without any assessment
        $autoAdvanceGrades = ['nursery', 'kindergarten'];

        if ($enrollment) {
            $hasTargetYear = Enrollment::where('user_id', $user->id)
                ->where('school_year', $targetSchoolYear)
                ->exists();

            if (!$hasTargetYear) {
                $needsReenrollment = true;
                $reenrollmentOpen  = $enrollmentWindowOpen;

                $currentGrade = $enrollment->student_data['grade_level']
                    ?? $enrollment->grade_level
                    ?? null;

                $studentType = $enrollment->student_data['student_type'] ?? 'returning';

                $promotionRecord = Promotion::where('student_id', $user->id)
                    ->where('from_school_year', $enrollment->school_year)
                    ->where('status', 'completed')
                    ->latest('id')->first();

                if ($promotionRecord) {
                    // Admin assessed this student → use promotion result
                    $suggestedGrade = $promotionRecord->to_grade;
                } elseif (in_array($currentGrade, $autoAdvanceGrades)) {
                    // Nursery/Kindergarten auto-advance, no assessment needed
                    $suggestedGrade = $gradeProgressionMap[$currentGrade];
                } elseif ($studentType === 'transferee') {
                    // Transferee: auto-advance to next grade, no assessment needed
                    $suggestedGrade = $gradeProgressionMap[$currentGrade] ?? $currentGrade;
                } else {
                    // Returning Grade 1-6 student: stays at same grade until admin assesses
                    $suggestedGrade = $currentGrade;
                }
            }
        }

        // Load normalized profile models for inline editing
        $profile        = $user->profile;
        $address        = $user->address;
        $guardian       = $user->guardian;
        $allGuardians   = $user->guardians;
        $mother         = $allGuardians->where('relationship', 'Mother')->first();
        $father         = $allGuardians->where('relationship', 'Father')->first();
        $previousSchool = $user->previousSchool;

        return view('studentportal', compact(
            'enrollment', 'progress', 'documents', 'profileComplete',
            'schedules', 'section', 'hasRequiredDocuments',
            'paymentInstallments', 'paymentSummary', 'availableSections',
            'needsReenrollment', 'reenrollmentOpen', 'suggestedGrade',
            'currentSchoolYear', 'promotionRecord', 'enrollmentWindowOpen',
            'profile', 'address', 'guardian', 'mother', 'father', 'previousSchool',
            'justPaidTransaction', 'paymentOutcome', 'summerClassEnrollments'
        ));
    }

    /**
     * Submit re-enrollment for existing student (new school year)
     */
    public function submitReenrollment(Request $request)
    {
        $user = Auth::user();

        // Block if enrollment window is closed
        if (!Setting::get('enrollment_open', true)) {
            return back()->with('error', 'Enrollment is currently closed. Please wait for the school to open the enrollment period.');
        }

        // Determine which school year we're enrolling INTO.
        // Priority: (1) admin enrollment_target_year setting, (2) current_school_year setting, (3) date fallback.
        $savedTarget = Setting::get('enrollment_target_year', '');
        $currentSY   = Setting::get('current_school_year', '');

        if ($savedTarget && preg_match('/^\d{4}-\d{4}$/', $savedTarget)) {
            $currentSchoolYear = $savedTarget;
        } elseif ($currentSY && preg_match('/^\d{4}-\d{4}$/', $currentSY)) {
            $currentSchoolYear = $currentSY;
        } else {
            $baseY = now()->month >= 6 ? now()->year : now()->year - 1;
            $currentSchoolYear = $baseY . '-' . ($baseY + 1);
        }

        // Prevent duplicate enrollment for the same target year (allow re-apply if declined)
        $existing = Enrollment::where('user_id', $user->id)
            ->where('school_year', $currentSchoolYear)
            ->whereNotIn('status', ['declined'])
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have an enrollment application for S.Y. ' . $currentSchoolYear . ' (Ref: ' . $existing->reference_number . ').');
        }

        // Get previous enrollment for pre-fill data
        $prevEnrollment = Enrollment::where('user_id', $user->id)
            ->whereIn('status', ['enrolled', 'completed', 'approved'])
            ->latest('id')->first();

        if (!$prevEnrollment) {
            return back()->with('error', 'No previous enrollment found. Please contact the admin.');
        }

        // Full grade progression map
        $gradeProgressionMap = [
            'nursery'     => 'kindergarten',
            'kindergarten'=> 'grade1',
            'grade1'      => 'grade2',
            'grade2'      => 'grade3',
            'grade3'      => 'grade4',
            'grade4'      => 'grade5',
            'grade5'      => 'grade6',
            'grade6'      => 'graduated',
        ];
        $autoAdvanceGrades = ['nursery', 'kindergarten'];

        $prevGrade   = $prevEnrollment->student_data['grade_level'] ?? $prevEnrollment->grade_level ?? null;
        $studentType = $prevEnrollment->student_data['student_type'] ?? 'returning';

        // Get promotion record (only relevant for Grade 1–6 returning students)
        $promotion = Promotion::where('student_id', $user->id)
            ->where('from_school_year', $prevEnrollment->school_year)
            ->where('status', 'completed')
            ->latest('id')->first();

        if ($promotion) {
            // Admin assessed → use the promotion result (promoted/retained/graduated)
            $newGrade = $promotion->to_grade;
        } elseif (in_array($prevGrade, $autoAdvanceGrades)) {
            // Nursery → Kindergarten, Kindergarten → Grade 1 (no assessment needed)
            $newGrade = $gradeProgressionMap[$prevGrade];
        } elseif ($studentType === 'transferee') {
            // Transferee: auto-advance to next grade, no assessment needed
            $newGrade = $gradeProgressionMap[$prevGrade] ?? $prevGrade;
        } else {
            // Returning Grade 1–6: requires admin assessment first
            // This path should normally not be reachable because the button is hidden
            // in the view until assessment is done — but as a safeguard, keep same grade.
            $newGrade = $prevGrade;
        }

        // Create new enrollment for current school year
        Enrollment::create([
            'user_id'          => $user->id,
            'grade_level'      => $newGrade,
            'school_year'      => $currentSchoolYear,
            'status'           => 'pending',
            'student_data'     => array_merge(
                $prevEnrollment->student_data ?? [],
                [
                    'grade_level'          => $newGrade,
                    'student_type'         => 'returning',
                    'previous_school_year' => $prevEnrollment->school_year,
                ]
            ),
            'payment_status'   => 'pending',
            'reference_number' => 'ENR-' . strtoupper(uniqid()),
        ]);

        return back()->with('success', 'Re-enrollment application submitted for S.Y. ' . $currentSchoolYear . '. Please wait for admin approval.');
    }

    private function getCurrentSchoolYear(): string
    {
        $latest = Section::where('is_active', true)->orderByDesc('school_year')->value('school_year');
        if ($latest) return $latest;
        $year = now()->month >= 6 ? now()->year : now()->year - 1;
        return $year . '-' . ($year + 1);
    }

    /**
     * Process payment submission
     */
    public function processPayment(Request $request)
    {
        $user = Auth::user();
        $enrollment = $user->enrollments()
            ->whereNotIn('status', ['declined'])
            ->orderByRaw("school_year DESC")
            ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending') ASC")
            ->latest('id')
            ->first();
        if (!$enrollment) {
            $enrollment = Enrollment::where('student_data->student_email', $user->email)
                ->whereNotIn('status', ['declined'])
                ->orderByRaw("school_year DESC")
                ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending') ASC")
                ->latest('id')
                ->first();
        }

        if (!$enrollment) {
            return redirect()->route('student.portal')
                ->with('error', 'No active enrollment found.');
        }

        $request->validate([
            'payment_method'    => 'required|in:cash',
            'amount'            => 'required|numeric|min:1',
            'reference_number'  => 'nullable|string|max:50',
            'payment_option'    => 'nullable|in:A,B,C,D',
            'total_amount'      => 'nullable|numeric',
            'downpayment_amount'=> 'nullable|numeric',
            'monthly_amount'    => 'nullable|numeric',
            'installment_id'    => 'nullable|integer',
            'number_of_months'  => 'nullable|integer|min:1',
        ]);

        // Check if there's already a pending payment for this enrollment (prevent duplicates)
        $existingPendingPayment = StudentDocument::where('enrollment_id', $enrollment->id)
            ->where('document_type', 'payment_screenshot')
            ->where('status', 'pending')
            ->exists();

        if ($existingPendingPayment) {
            return redirect()->route('student.portal')
                ->with('error', 'You already have a payment pending approval. Please wait for admin confirmation before submitting another.');
        }

        // For installment payments - use PaymentService
        $isInstallment = $enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']);
        $document = null;
        if ($isInstallment) {
            // Check if installments exist, create if not
            PaymentService::createInstallments($enrollment);

            // Check if account is blocked
            if ($enrollment->account_blocked) {
                return redirect()->route('student.portal')
                    ->with('error', 'Your portal access is blocked due to overdue payments. Please contact the Finance Office.');
            }

            // Process specific installment payment (from installment modal)
            if ($request->installment_id) {
                $result = PaymentService::processInstallmentPayment(
                    $enrollment,
                    $request->installment_id,
                    $request->amount,
                    $request->payment_method,
                    $request->reference_number,
                    $document
                );

                if (!$result['success']) {
                    return redirect()->route('student.portal')
                        ->with('error', $result['message']);
                }

                Log::info('Installment payment submitted', [
                    'user_id' => $user->id,
                    'enrollment_id' => $enrollment->id,
                    'installment_id' => $request->installment_id,
                    'amount' => $request->amount,
                ]);

                return redirect()->route('student.portal')
                    ->with('success', $result['message'] . ' Waiting for admin confirmation.');
            }

            // Process advance payment for multiple months
            if ($request->number_of_months && $request->number_of_months > 1) {
                $result = PaymentService::processAdvancePayment(
                    $enrollment,
                    $request->number_of_months,
                    $request->amount,
                    $request->payment_method,
                    $request->reference_number,
                    $document
                );

                if (!$result['success']) {
                    return redirect()->route('student.portal')
                        ->with('error', $result['message']);
                }

                Log::info('Advance payment submitted', [
                    'user_id' => $user->id,
                    'enrollment_id' => $enrollment->id,
                    'months' => $request->number_of_months,
                    'amount' => $request->amount,
                ]);

                return redirect()->route('student.portal')
                    ->with('success', $result['message'] . ' Waiting for admin confirmation.');
            }

            // Check if this is a downpayment (student hasn't paid downpayment yet)
            $downpaymentAmount = (float) ($enrollment->downpayment_amount ?? 0);
            $alreadyPaid = (float) ($enrollment->payment_amount ?? 0);
            $isDownpayment = $downpaymentAmount > 0 && $alreadyPaid < $downpaymentAmount;

            if ($isDownpayment) {
                // This is a downpayment - do NOT link to any installment
                // Fall through to the general payment handling below
                // The downpayment will be recorded as a general enrollment payment
                Log::info('Downpayment detected - treating as general payment', [
                    'user_id' => $user->id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => $request->amount,
                    'downpayment_required' => $downpaymentAmount,
                    'already_paid' => $alreadyPaid,
                ]);
            } else {
                // Downpayment already paid - this is a monthly installment payment
                // Find the next pending or overdue installment
                $nextPending = $enrollment->paymentInstallments()
                    ->whereIn('status', ['pending', 'overdue'])
                    ->orderBy('due_date')
                    ->first();

                if ($nextPending) {
                    $result = PaymentService::processInstallmentPayment(
                        $enrollment,
                        $nextPending->id,
                        $request->amount,
                        $request->payment_method,
                        $request->reference_number,
                        $document
                    );

                    if (!$result['success']) {
                        return redirect()->route('student.portal')
                            ->with('error', $result['message']);
                    }

                    Log::info('Monthly installment payment submitted', [
                        'user_id' => $user->id,
                        'enrollment_id' => $enrollment->id,
                        'installment_id' => $nextPending->id,
                        'amount' => $request->amount,
                    ]);

                    return redirect()->route('student.portal')
                        ->with('success', $result['message'] . ' Waiting for admin confirmation.');
                }
            }
        }

        // For downpayment/first-time payments (full payment or initial downpayment)
        // Do NOT update payment_amount yet - wait for finance approval
        $updateData = [
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->reference_number,
        ];

        // Save payment option and calculate financial fields if provided
        if ($request->payment_option) {
            $paymentOption = $request->payment_option;
            $updateData['payment_option'] = $paymentOption;

            // Calculate breakdown using EnrollmentController logic
            $gradeLevel = $enrollment->grade_level ?? ($enrollment->student_data['grade_level'] ?? 'grade1');
            $enrollmentController = app(\App\Http\Controllers\EnrollmentController::class);
            $breakdown = $enrollmentController->calculatePaymentBreakdown($gradeLevel, $paymentOption);

            $totalFee = $breakdown['total_due'] ?? $breakdown['base_total'] ?? 0;
            $updateData['total_fee'] = $totalFee;
            $updateData['payment_type'] = $breakdown['payment_type'] ?? ($paymentOption === 'A' ? 'full' : 'installment');
            $updateData['payment_breakdown'] = json_encode($breakdown);
            $updateData['downpayment_amount'] = $breakdown['downpayment'] ?? 0;
            $updateData['monthly_amount'] = $breakdown['monthly_amount'] ?? 0;

            // Calculate remaining balance (using current payment_amount, not including this payment yet)
            $currentPaid = (float) ($enrollment->payment_amount ?? 0);
            $remainingBalance = max(0, $totalFee - $currentPaid);
            $updateData['remaining_balance'] = $remainingBalance;

            // For installment plans, create the payment installments
            if ($breakdown['payment_type'] === 'installment' && $enrollment->paymentInstallments()->count() === 0) {
                PaymentService::createInstallments($enrollment);
                
                // Set next installment date to first pending installment
                $firstInstallment = $enrollment->paymentInstallments()->orderBy('due_date')->first();
                if ($firstInstallment) {
                    $updateData['next_installment_date'] = $firstInstallment->due_date;
                }
            }
        }

        // Payment status will be set to 'paid' only after admin approval
        // Student submission keeps status as 'partial' until verified

        $enrollment->update($updateData);

        Log::info('Payment submitted for review', [
            'user_id' => $user->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $request->amount,
            'method' => $request->payment_method,
            'payment_option' => $request->payment_option,
        ]);

        return redirect()->route('student.portal')
            ->with('success', 'Payment submitted! It will be confirmed by the admin.');
    }

    /**
     * Show student information form
     */
    public function showInfo()
    {
        $user = Auth::user();
        $enrollment = $user->enrollments()
            ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
            ->latest('id')
            ->first() ?? $user->latestEnrollment;
        $progress = $this->getCompletionProgress($enrollment);
        
        return view('student.info', compact('enrollment', 'progress'));
    }

    /**
     * Update student information
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();
        $enrollment = $user->latestEnrollment;
        
        $validated = $request->validate([
            'personal_info' => 'required|array',
            'address_info' => 'required|array',
            'guardian_info' => 'required|array',
        ]);

        // Update enrollment student_data
        $studentData = $enrollment->student_data;
        $studentData = array_merge($studentData, $validated);
        
        $enrollment->update(['student_data' => $studentData]);

        return redirect()->back()->with('success', 'Information updated successfully!');
    }

    /**
     * Show documents upload page
     */
    public function showDocuments()
    {
        $user = Auth::user();
        $enrollment = $user->enrollments()
            ->orderByRaw("FIELD(status, 'enrolled', 'approved', 'pending', 'declined') ASC")
            ->latest('id')
            ->first() ?? $user->latestEnrollment;
        $progress = $this->getCompletionProgress($enrollment);
        $documents = StudentDocument::where(function($q) use ($enrollment, $user) {
                $q->where('enrollment_id', $enrollment->id)
                  ->orWhere('user_id', $user->id);
            })->get();
        
        return view('student.documents', compact('enrollment', 'progress', 'documents'));
    }

    /**
     * Upload all required documents at once (bulk submit)
     */
    public function uploadAllDocuments(Request $request)
    {
        $user       = Auth::user();
        $enrollment = $user->latestEnrollment;
        if (!$enrollment) {
            return redirect()->route('student.portal')->with('error', 'No active enrollment found.');
        }

        $docTypes = ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture'];
        $allowed  = ['application/pdf', 'image/jpeg', 'image/png'];
        $sigs     = [
            'pdf' => "\x25\x50\x44\x46",
            'jpg' => "\xFF\xD8\xFF",
            'png' => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
        ];
        $danger = '/\.(php\d?|phtml|phar|pl|cgi|asp|aspx|js|html?|xml|svg|sh|exe|bat|cmd)(\.|$)/i';

        $saved   = 0;
        $errors  = [];

        foreach ($docTypes as $docType) {
            if (!$request->hasFile("files.$docType")) continue;

            $file = $request->file("files.$docType");
            if (!$file->isValid()) { $errors[] = "$docType: upload error."; continue; }

            // Magic byte check
            $handle = fopen($file->getRealPath(), 'rb');
            $magic  = fread($handle, 8);
            fclose($handle);

            $matched = false;
            foreach ($sigs as $sig) {
                if (str_starts_with($magic, $sig)) { $matched = true; break; }
            }
            if (!$matched) { $errors[] = ucfirst(str_replace('_', ' ', $docType)) . ': invalid file content.'; continue; }

            if (!in_array($file->getMimeType(), $allowed, true)) {
                $errors[] = ucfirst(str_replace('_', ' ', $docType)) . ': unsupported file type.'; continue;
            }

            $clientName = $file->getClientOriginalName();
            if (preg_match($danger, $clientName)) {
                $errors[] = ucfirst(str_replace('_', ' ', $docType)) . ': disallowed file extension.'; continue;
            }

            $safeExt  = strtolower($file->getClientOriginalExtension());
            if (!in_array($safeExt, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
                $safeExt = str_starts_with($magic, "\x25\x50\x44\x46") ? 'pdf'
                    : (str_starts_with($magic, "\xFF\xD8\xFF") ? 'jpg' : 'png');
            }
            $safeName = substr(preg_replace('/[^\w\s.\-]/', '', pathinfo($clientName, PATHINFO_FILENAME)) ?: 'document', 0, 80) . '.' . $safeExt;

            // Skip if already approved or pending
            $existing = StudentDocument::where(function ($q) use ($enrollment, $user) {
                $q->where('enrollment_id', $enrollment->id)->orWhere('user_id', $user->id);
            })->where('document_type', $docType)->whereIn('status', ['approved', 'pending'])->first();

            if ($existing) continue; // already submitted

            // Delete any rejected copy first
            StudentDocument::where(function ($q) use ($enrollment, $user) {
                $q->where('enrollment_id', $enrollment->id)->orWhere('user_id', $user->id);
            })->where('document_type', $docType)->where('status', 'rejected')->delete();

            $path = $file->store('student_documents/' . (int) $enrollment->id, 'public');

            StudentDocument::create([
                'user_id'       => $user->id,
                'enrollment_id' => $enrollment->id,
                'document_type' => $docType,
                'file_path'     => $path,
                'original_name' => $safeName,
                'mime_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
                'status'        => 'pending',
            ]);

            $saved++;
        }

        if ($saved === 0 && empty($errors)) {
            return redirect()->route('student.portal')->with('doc_error', 'No new documents to upload. Already submitted or no files selected.')->with('go_enrollment', true);
        }

        $msg = $saved > 0 ? "$saved document(s) submitted for review." : '';
        $errMsg = !empty($errors) ? implode(' ', $errors) : '';

        return redirect()->route('student.portal')
            ->with($saved > 0 ? 'doc_success' : 'doc_error', $msg ?: $errMsg)
            ->with('go_enrollment', true);
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request)
    {
        $user = Auth::user();

        // ── 1. Validate inputs first ──────────────────────────────────────
        $request->validate([
            'document_type' => 'required|in:birth_certificate,form_137,report_card,two_by_two_picture,others',
            'file'          => 'required|file|max:5120',   // size cap; MIME checked below
            'description'   => 'nullable|string|max:255',
        ]);

        // ── 2. Verify the actual file magic bytes ─────────────────────────
        $file = $request->file('file');

        // Read first 8 bytes to check magic signature
        $handle    = fopen($file->getRealPath(), 'rb');
        $magic     = fread($handle, 8);
        fclose($handle);

        $signatures = [
            'pdf'  => "\x25\x50\x44\x46",                             // %PDF
            'jpg'  => "\xFF\xD8\xFF",                                  // JFIF / EXIF
            'png'  => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",            // PNG
        ];

        $matched = false;
        foreach ($signatures as $sig) {
            if (str_starts_with($magic, $sig)) { $matched = true; break; }
        }

        if (!$matched) {
            return redirect()->back()
                ->with('doc_error', 'Invalid file content. Only PDF, JPG, and PNG files are accepted.')
                ->with('go_enrollment', true);
        }

        // ── 3. Confirm the detected MIME matches an allowed type ──────────
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
        $detectedMime = $file->getMimeType(); // PHP finfo reads actual content

        if (!in_array($detectedMime, $allowedMimes, true)) {
            return redirect()->back()
                ->with('doc_error', 'Unsupported file type detected. Only PDF, JPG, and PNG are allowed.')
                ->with('go_enrollment', true);
        }

        // ── 4. Block double extensions (e.g. evil.php.jpg) ───────────────
        $clientName = $file->getClientOriginalName();
        $dangerousExtensions = '/\.(php\d?|phtml|phar|pl|cgi|asp|aspx|js|html?|xml|svg|sh|exe|bat|cmd)(\.|$)/i';
        if (preg_match($dangerousExtensions, $clientName)) {
            return redirect()->back()
                ->with('doc_error', 'File name contains a disallowed extension.')
                ->with('go_enrollment', true);
        }

        // ── 5. Sanitize the original filename for safe DB storage ─────────
        $safeOriginalName = preg_replace('/[^\w\s.\-]/', '', pathinfo($clientName, PATHINFO_FILENAME));
        $safeOriginalName = preg_replace('/\.{2,}/', '.', trim($safeOriginalName));
        $safeOriginalName = substr($safeOriginalName ?: 'document', 0, 80);
        $safeExt          = strtolower($file->getClientOriginalExtension());
        if (!in_array($safeExt, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            $safeExt = match(true) {
                str_starts_with($magic, "\x25\x50\x44\x46") => 'pdf',
                str_starts_with($magic, "\xFF\xD8\xFF")      => 'jpg',
                default                                       => 'png',
            };
        }
        $safeOriginalName = $safeOriginalName . '.' . $safeExt;

        // ── 6. Sanitize optional description ─────────────────────────────
        $safeDescription = $request->description
            ? htmlspecialchars(strip_tags(trim($request->description)), ENT_QUOTES, 'UTF-8')
            : null;
        if ($safeDescription !== null) {
            $safeDescription = substr($safeDescription, 0, 255);
        }

        // ── 7. Find active enrollment ─────────────────────────────────────
        $enrollment = $user->latestEnrollment;
        if (!$enrollment) {
            $enrollment = Enrollment::where('student_data->student_email', $user->email)->latest()->first();
        }
        if (!$enrollment) {
            return redirect()->back()
                ->with('doc_error', 'No enrollment found. Please complete enrollment first.')
                ->with('go_enrollment', true);
        }
        if (in_array($enrollment->status, ['declined', 'cancelled'], true)) {
            return redirect()->back()
                ->with('doc_error', 'Document uploads are not allowed for a declined or cancelled enrollment.')
                ->with('go_enrollment', true);
        }

        // ── 8. Block re-upload of an already-approved document ────────────
        $alreadyApproved = StudentDocument::where('user_id', $user->id)
            ->where('document_type', $request->document_type)
            ->where('status', 'approved')
            ->exists();

        if ($alreadyApproved) {
            return redirect()->back()
                ->with('doc_error', 'This document has already been approved and cannot be replaced.')
                ->with('go_enrollment', true);
        }

        // ── 9. Store with Laravel-generated UUID filename ─────────────────
        // Using storeAs with a generated name prevents filename-based attacks.
        $storedPath = $file->store('student_documents/' . (int) $enrollment->id, 'public');

        StudentDocument::create([
            'user_id'       => $user->id,
            'enrollment_id' => $enrollment->id,
            'document_type' => $request->document_type,   // validated by `in:` rule
            'file_path'     => $storedPath,
            'original_name' => $safeOriginalName,
            'mime_type'     => $detectedMime,             // from finfo, not client header
            'file_size'     => $file->getSize(),
            'description'   => $safeDescription,
            'status'        => 'pending',
        ]);

        return redirect()->back()
            ->with('doc_success', 'Document uploaded successfully! It will be reviewed by the registrar.')
            ->with('go_enrollment', true);
    }

    /**
     * Delete document
     */
    public function deleteDocument(StudentDocument $document)
    {
        // Ownership: must belong to the authenticated user directly
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        // Cannot delete an approved document
        if ($document->status === 'approved') {
            return redirect()->back()->with('doc_error', 'Approved documents cannot be deleted.');
        }

        // Delete file from storage — validate path stays within expected directory
        $safePath = ltrim($document->file_path, '/');
        if ($safePath && str_starts_with($safePath, 'student_documents/')) {
            if (Storage::disk('public')->exists($safePath)) {
                Storage::disk('public')->delete($safePath);
            }
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully!');
    }

    /**
     * Get completion progress for enrollment
     */
    private function getCompletionProgress(Enrollment $enrollment)
    {
        $studentData = $enrollment->student_data ?? [];
        
        // Check if required documents are uploaded
        $requiredDocTypes = ['birth_certificate', 'form_137', 'report_card', 'two_by_two_picture'];
        $uploadedDocTypes = StudentDocument::where(function($q) use ($enrollment) {
                $q->where('enrollment_id', $enrollment->id)
                  ->orWhere('user_id', Auth::id());
            })
            ->whereIn('document_type', $requiredDocTypes)
            ->where('status', 'approved')
            ->pluck('document_type')
            ->unique()
            ->count();
        $hasRequiredDocuments = $uploadedDocTypes >= 1;
        
        // Installment plans are "payment complete" once downpayment is approved (partial or paid)
        $isInstallmentPlan = $enrollment->payment_type === 'installment'
            || in_array($enrollment->payment_option, ['B', 'C', 'D']);
        $paymentDone = $isInstallmentPlan
            ? in_array($enrollment->payment_status ?? 'pending', ['partial', 'paid'])
            : ($enrollment->payment_status ?? 'pending') === 'paid';

        $progress = [
            'personal_info'    => $this->checkPersonalInfoComplete($studentData),
            'address_info'     => $this->checkAddressInfoComplete($studentData),
            'guardian_info'    => $this->checkGuardianInfoComplete($studentData),
            'enrollment_status' => in_array($enrollment->status, ['approved', 'enrolled']),
            'payment_status'   => $paymentDone,
            'required_documents' => $hasRequiredDocuments,
        ];

        $totalSections = count($progress);
        $completedSections = count(array_filter($progress));
        $percentage = $totalSections > 0 ? round(($completedSections / $totalSections) * 100) : 0;

        return [
            'sections' => $progress,
            'completed' => $completedSections,
            'total' => $totalSections,
            'percentage' => $percentage,
            'status' => $this->getCompletionStatus($percentage),
            'enrollment' => $enrollment,
        ];
    }

    /**
     * Check if personal information is complete
     */
    private function checkPersonalInfoComplete($data)
    {
        $required = ['first_name', 'last_name', 'gender', 'birthdate', 'place_of_birth', 'grade_level'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if address information is complete
     */
    private function checkAddressInfoComplete($data)
    {
        $required = ['region', 'province', 'city', 'barangay', 'street_address'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if guardian information is complete
     */
    private function checkGuardianInfoComplete($data)
    {
        $required = ['guardian_name', 'relationship', 'guardian_phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get completion status based on percentage
     */
    private function getCompletionStatus($percentage)
    {
        if ($percentage >= 100) {
            return 'completed';
        } elseif ($percentage >= 75) {
            return 'almost_complete';
        } elseif ($percentage >= 50) {
            return 'in_progress';
        } else {
            return 'just_started';
        }
    }

    public function changeSection(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
        ]);

        $enrollment = $user->enrollments()
            ->where('status', 'enrolled')
            ->latest('id')
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'No active enrolled record found.'], 403);
        }

        $newSection = Section::find($validated['section_id']);

        if ($newSection->grade_level !== $enrollment->grade_level) {
            return response()->json(['success' => false, 'message' => 'Section does not match your grade level.'], 422);
        }

        if ($newSection->current_enrollment >= $newSection->max_students) {
            return response()->json(['success' => false, 'message' => "Section {$newSection->name} is full ({$newSection->max_students} max)."], 422);
        }

        // Remove from old section in pivot table
        $oldSection = Section::where('name', $enrollment->section)
            ->where('grade_level', $enrollment->grade_level)
            ->first();

        \Illuminate\Support\Facades\DB::table('section_student')
            ->where('user_id', $user->id)
            ->delete();

        if ($oldSection && $oldSection->id !== $newSection->id) {
            $oldSection->decrement('current_enrollment');
        }

        // Add to new section
        \Illuminate\Support\Facades\DB::table('section_student')->insert([
            'section_id' => $newSection->id,
            'user_id'    => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newSection->increment('current_enrollment');
        $enrollment->update(['section' => $newSection->name]);

        Log::info("Student {$user->id} changed section to {$newSection->name}");

        return response()->json([
            'success' => true,
            'message' => "Section changed to {$newSection->name} successfully.",
            'section_name' => $newSection->name,
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $user    = Auth::user();
        $profile = StudentProfile::where('user_id', $user->id)->first();

        // Delete old photo from storage
        if ($profile && $profile->photo && Storage::disk('public')->exists($profile->photo)) {
            Storage::disk('public')->delete($profile->photo);
        }

        $path = $request->file('photo')->store('student_photos', 'public');

        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['photo'   => $path]
        );

        return back()->with('photo_success', 'Profile photo updated successfully!')
                     ->with('settings_tab', 'account');
    }

    public function generateXenditLink(Request $request)
    {
        $request->validate([
            'enrollment_id'     => 'required|exists:enrollments,id',
            'amount'            => 'required|numeric|min:1',
            'payment_method'    => 'required|string',
            'payment_type'      => 'required|string',
            'payment_option'    => 'nullable|in:A,B,C,D',
            'total_fee'         => 'nullable|numeric|min:0',
            'downpayment_amount'=> 'nullable|numeric|min:0',
            'monthly_amount'    => 'nullable|numeric|min:0',
        ]);

        $apiKey = config('services.xendit.secret_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Online payment is not configured yet. Please pay via GCash or Cash.'], 500);
        }

        $user       = Auth::user();
        $enrollment = \App\Models\Enrollment::where('id', $request->enrollment_id)
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        // Save payment plan to enrollment if provided and not already set
        $planUpdate = [];
        if ($request->payment_option && !$enrollment->payment_option) {
            $planUpdate['payment_option'] = $request->payment_option;
            $planUpdate['payment_type']   = $request->payment_option === 'A' ? 'full' : 'installment';
        }
        if ($request->total_fee > 0 && !$enrollment->total_fee) {
            $planUpdate['total_fee']          = $request->total_fee;
            $planUpdate['remaining_balance']  = max(0, $request->total_fee - ($enrollment->payment_amount ?? 0));
        }
        if ($request->downpayment_amount > 0 && !$enrollment->downpayment_amount) {
            $planUpdate['downpayment_amount'] = $request->downpayment_amount;
        }
        if ($request->monthly_amount > 0 && !$enrollment->monthly_amount) {
            $planUpdate['monthly_amount'] = $request->monthly_amount;
        }
        if (!empty($planUpdate)) {
            $enrollment->update($planUpdate);
            $enrollment->refresh();
        }

        $externalId = 'STU-' . $user->id . '-' . $enrollment->id . '-' . time();

        $allowedMethods = match ($request->payment_method) {
            'gcash'   => ['GCASH'],
            'maya'    => ['PAYMAYA'],
            'grabpay' => ['GRABPAY'],
            'bank'    => ['DD_BPI', 'DD_UBP', 'DD_RCBC', 'DD_CHINABANK'],
            'otc'     => ['SEVEN_ELEVEN', 'CEBUANA', 'PALAWAN', 'MLHUILLIER'],
            default   => [],
        };

        $response = \Illuminate\Support\Facades\Http::withBasicAuth($apiKey, '')
            ->post('https://api.xendit.co/v2/invoices', [
                'external_id'          => $externalId,
                'amount'               => (int) $request->amount,
                'description'          => 'ILC Tuition — ' . $request->payment_type . ' (' . $user->name . ')',
                'invoice_duration'     => 86400,
                'currency'             => 'PHP',
                'customer'             => ['given_names' => $user->name, 'email' => $user->email],
                'payment_methods'      => $allowedMethods,
                'success_redirect_url' => route('student.portal', ['paid_ref' => $externalId]),
                // Carry the same reference on failure so the portal can look up
                // what happened (declined / cancelled / expired) and tell the
                // student clearly, instead of a bare redirect with zero context.
                'failure_redirect_url' => route('student.portal', ['paid_ref' => $externalId, 'outcome' => 'failed']),
            ]);

        if (!$response->successful()) {
            Log::error('Student Xendit invoice failed', ['response' => $response->json(), 'user' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment link. ' . ($response->json()['message'] ?? 'Please try again.'),
            ], 422);
        }

        $invoice = $response->json();

        // Record as pending transaction
        \App\Models\PaymentTransaction::create([
            'enrollment_id'      => $enrollment->id,
            'user_id'            => $user->id,
            'payment_type'       => 'online',
            'payment_method'     => $request->payment_method,
            'amount'             => $request->amount,
            'reference_number'   => $externalId,
            'xendit_invoice_id'  => $invoice['id'],
            'xendit_invoice_url' => $invoice['invoice_url'],
            'description'        => $request->payment_type,
            'status'             => 'pending',
            'processed_at'       => now(),
        ]);

        return response()->json([
            'success'     => true,
            'invoice_url' => $invoice['invoice_url'],
            'invoice_id'  => $invoice['id'],
            'expiry'      => $invoice['expiry_date'] ?? null,
        ]);
    }

    public function sendPasswordOtp(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
        }

        // Invalidate any previous pending OTPs for this user
        OtpVerification::where('email', $user->email)->delete();

        $code  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(40);

        OtpVerification::create([
            'email'      => $user->email,
            'code'       => Hash::make($code),
            'token'      => $token,
            'attempts'   => 0,
            'verified'   => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Store the new password hash temporarily in session
        session(['pwd_change_hash_' . $token => Hash::make($request->password)]);

        Mail::to($user->email)->send(new PasswordChangeOtpMail($code, $user->name));

        return response()->json(['success' => true, 'token' => $token, 'email' => $user->email]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'otp_token' => 'required|string',
            'otp_code'  => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $otp  = OtpVerification::where('token', $request->otp_token)
                    ->where('email', $user->email)
                    ->first();

        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['otp_code' => 'OTP expired or invalid. Please request a new one.'])
                         ->with('settings_tab', 'account');
        }

        if ($otp->hasExceededAttempts()) {
            $otp->delete();
            return back()->withErrors(['otp_code' => 'Too many incorrect attempts. Please request a new OTP.'])
                         ->with('settings_tab', 'account');
        }

        if (!Hash::check($request->otp_code, $otp->code)) {
            $otp->increment('attempts');
            return back()->withErrors(['otp_code' => 'Incorrect OTP code. ' . (2 - $otp->attempts) . ' attempt(s) remaining.'])
                         ->with('otp_token', $request->otp_token)
                         ->with('settings_tab', 'account');
        }

        $hashKey = 'pwd_change_hash_' . $request->otp_token;
        $newHash = session($hashKey);

        if (!$newHash) {
            return back()->withErrors(['otp_code' => 'Session expired. Please start over.'])
                         ->with('settings_tab', 'account');
        }

        $user->update(['password' => $newHash]);
        $otp->delete();
        session()->forget($hashKey);

        return back()->with('password_success', 'Password changed successfully!')
                     ->with('settings_tab', 'account');
    }
}
