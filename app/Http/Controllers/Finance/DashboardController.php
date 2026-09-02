<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\FeeSetting;
use App\Models\PaymentInstallment;
use App\Models\StudentDocument;
use App\Models\Section;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show finance portal dashboard
     */
    public function index()
    {
        // Finance summary stats (one DB round-trip)
        $financeSummary = DB::selectOne("
            SELECT
                COUNT(CASE WHEN e.payment_status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN e.payment_status = 'partial' THEN 1 END) as partial_count,
                COUNT(CASE WHEN e.payment_status IS NULL OR e.payment_status = 'pending' THEN 1 END) as unpaid_count,
                COALESCE(SUM(CASE WHEN e.payment_status IN ('paid','partial') THEN e.payment_amount ELSE 0 END), 0) as total_collected
            FROM enrollments e
            WHERE e.id IN (SELECT MAX(id) FROM enrollments GROUP BY user_id)
        ");

        $totalCollected = $financeSummary->total_collected ?? 0;
        $paidCount      = $financeSummary->paid_count      ?? 0;
        $partialCount   = $financeSummary->partial_count   ?? 0;
        $unpaidCount    = $financeSummary->unpaid_count    ?? 0;

        // Student Payment Overview — paginated, with latest enrollment + installments
        $allStudentsPayment = \App\Models\User::where('role', 'student')
            ->whereNull('deleted_at')
            ->with(['enrollments' => function ($q) {
                $q->latest()->with('paymentInstallments');
            }])
            ->orderBy('name')
            ->paginate(15, ['*'], 'student_page');

        // Section list for filter dropdown
        $sections = \App\Models\Section::orderBy('name')->get();

        return view('finance.dashboard', compact(
            'totalCollected', 'paidCount', 'partialCount', 'unpaidCount',
            'allStudentsPayment', 'sections'
        ));
    }

    /**
     * Get financial statistics
     */
    private function getFinancialStats()
    {
        $today      = Carbon::today();
        $schoolYear = $this->formatSchoolYear();

        return [
            'total_collected_today' => PaymentTransaction::whereIn('status', ['approved', 'completed'])
                ->whereDate('updated_at', $today)
                ->count(),

            'total_collected_month' => PaymentTransaction::whereIn('status', ['approved', 'completed'])
                ->whereMonth('updated_at', $today->month)
                ->whereYear('updated_at', $today->year)
                ->count(),

            'pending_verification' => PaymentTransaction::where('status', 'pending')
                ->count(),

            'total_enrolled_students' => Enrollment::where('status', 'enrolled')
                ->where('school_year', $schoolYear)
                ->count(),

            'total_receivables' => Enrollment::where('status', 'enrolled')
                ->where('school_year', $schoolYear)
                ->sum('remaining_balance'),

            'overdue_installments' => Enrollment::where(function ($q) {
                    $q->where('payment_type', 'installment')
                        ->orWhereIn('payment_option', ['B', 'C', 'D']);
                })
                ->where('payment_status', '!=', 'paid')
                ->where('next_installment_date', '<', $today)
                ->count(),
        ];
    }

    /**
     * Get pending payments for verification
     */
    private function getPendingPayments()
    {
        return PaymentTransaction::where('status', 'pending')
            ->with(['enrollment', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        return PaymentTransaction::where('status', 'approved')
            ->with(['enrollment', 'user', 'processedBy'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get installment payment overview
     */
    private function getInstallmentOverview()
    {
        $today = Carbon::today();
        
        return [
            'total_installment_students' => Enrollment::where(function ($q) {
                    $q->where('payment_type', 'installment')
                        ->orWhereIn('payment_option', ['B', 'C', 'D']);
                })
                ->where('payment_status', '!=', 'paid')
                ->where('school_year', $this->formatSchoolYear())
                ->count(),
            
            'due_this_week' => Enrollment::where(function ($q) {
                    $q->where('payment_type', 'installment')
                        ->orWhereIn('payment_option', ['B', 'C', 'D']);
                })
                ->where('payment_status', '!=', 'paid')
                ->whereBetween('next_installment_date', [$today, $today->copy()->addDays(7)])
                ->count(),
            
            'overdue' => Enrollment::where(function ($q) {
                    $q->where('payment_type', 'installment')
                        ->orWhereIn('payment_option', ['B', 'C', 'D']);
                })
                ->where('payment_status', '!=', 'paid')
                ->where('next_installment_date', '<', $today)
                ->count(),
        ];
    }

    /**
     * Show all payments (Online screenshots + Walk-in transactions)
     */
    public function payments(Request $request)
    {
        $search     = $request->input('search');
        $yearFilter = $request->input('school_year');

        // ── Walk-in Transactions: PaymentTransaction (walkin/admin) ──
        $walkInTransactions = PaymentTransaction::whereIn('payment_type', ['walkin', 'admin'])
            ->whereHas('user')
            ->with(['enrollment', 'user', 'installment', 'processedBy'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'walkin_page');

        // ── Xendit / Online Transactions ──
        $xenditTransactions = PaymentTransaction::where('payment_type', 'online')
            ->whereHas('user')
            ->with(['enrollment', 'user', 'processedBy'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'xendit_page');

        // ── Stats ──
        $payStatsWalkin = PaymentTransaction::whereIn('payment_type', ['walkin', 'admin', 'downpayment'])
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount")
            ->first();
        $payStatsXendit = PaymentTransaction::where('payment_type', 'online')
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(CASE WHEN status='completed' THEN amount ELSE 0 END),0) as total_amount")
            ->first();

        $combinedPayStats = [
            'total'         => ($payStatsWalkin->total ?? 0) + ($payStatsXendit->total ?? 0),
            'walkin_amount' => $payStatsWalkin->total_amount ?? 0,
            'xendit_total'  => $payStatsXendit->total        ?? 0,
            'xendit_amount' => $payStatsXendit->total_amount ?? 0,
        ];

        $schoolYears = $this->getSchoolYears();

        return view('finance.payments', compact(
            'walkInTransactions', 'xenditTransactions', 'combinedPayStats', 'schoolYears'
        ));
    }

    /**
     * Show students with installments
     */
    public function installments(Request $request)
    {
        $sort = $request->input('sort', 'newest');

        $query = Enrollment::where(function ($q) {
                $q->where('payment_type', 'installment')
                    ->orWhereIn('payment_option', ['B', 'C', 'D']);
            })
            ->with(['user:id,name,email', 'paymentInstallments']);

        // Filter by school year
        $yearFilter = $request->input('school_year');
        if ($yearFilter && $yearFilter !== 'all') {
            $query->where('school_year', $yearFilter);
        } else {
            $query->where('school_year', $this->formatSchoolYear());
        }

        // Filter by status
        $paymentStatusFilter = $request->input('payment_status');
        if ($paymentStatusFilter && $paymentStatusFilter !== 'all') {
            $query->where('payment_status', $paymentStatusFilter);
        }

        // Search by student name or email
        $search = $request->input('search');
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $query->join('users', 'users.id', '=', 'enrollments.user_id')
                    ->orderBy('users.name', 'asc')
                    ->select('enrollments.*');
                break;
            case 'name_desc':
                $query->join('users', 'users.id', '=', 'enrollments.user_id')
                    ->orderBy('users.name', 'desc')
                    ->select('enrollments.*');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get enrollments first
        $enrollments = $query->paginate(15)->withQueryString();

        $schoolYears = $this->getSchoolYears();

        // Calculate next due date and progress for each enrollment
        foreach ($enrollments as $enrollment) {
            $isCashBasis = $enrollment->payment_option === 'A'
                || ($enrollment->payment_type === 'full' && !in_array($enrollment->payment_option, ['B','C','D']));

            if ($isCashBasis) {
                // Plan A — no installment schedule, just track balance
                $totalPaid = (float) ($enrollment->payment_amount ?? 0);
                $totalFee  = (float) ($enrollment->total_fee ?? 0);
                $balance   = max(0, $totalFee - $totalPaid);

                $enrollment->next_due_date        = null;
                $enrollment->next_due_amount      = $balance;
                $enrollment->next_month_name      = $balance <= 0 ? 'Fully Paid' : 'Full Payment';
                $enrollment->is_overdue           = false;
                $enrollment->weeks_overdue        = 0;
                $enrollment->total_late_fees      = 0;
                $enrollment->installment_progress = $totalFee > 0
                    ? min(100, ($totalPaid / $totalFee) * 100)
                    : ($totalPaid > 0 ? 100 : 0);
            } else {
                // Plans B / C / D — installment logic
                if ($enrollment->paymentInstallments->isEmpty()) {
                    if ((!$enrollment->monthly_amount || $enrollment->monthly_amount <= 0)
                        && in_array($enrollment->payment_option, ['B', 'C', 'D'])) {
                        $gradeLevel = $enrollment->grade_level
                            ?? ($enrollment->student_data['grade_level'] ?? 'grade1');
                        $breakdown = app(\App\Http\Controllers\EnrollmentController::class)
                            ->calculatePaymentBreakdown($gradeLevel, $enrollment->payment_option);
                        if (!empty($breakdown['monthly_amount']) && $breakdown['monthly_amount'] > 0) {
                            $fixData = ['monthly_amount' => $breakdown['monthly_amount'], 'payment_type' => 'installment'];
                            if (!$enrollment->downpayment_amount || $enrollment->downpayment_amount <= 0) {
                                $fixData['downpayment_amount'] = $breakdown['downpayment'] ?? 0;
                            }
                            if (!$enrollment->total_fee || $enrollment->total_fee <= 0) {
                                $fixData['total_fee']         = $breakdown['total_due'] ?? 0;
                                $fixData['remaining_balance'] = $breakdown['total_due'] ?? 0;
                            }
                            $enrollment->update($fixData);
                            $enrollment->refresh();
                        }
                    }
                    \App\Services\PaymentService::createInstallments($enrollment);
                    $enrollment->load('paymentInstallments');
                }

                \App\Services\PaymentService::reconcileInstallmentStatuses($enrollment);

                $nextPending = $enrollment->paymentInstallments
                    ->whereIn('status', ['pending', 'overdue', 'pending_approval'])
                    ->sortBy('due_date')
                    ->first();

                if ($nextPending) {
                    $enrollment->next_due_date   = $nextPending->due_date;
                    $enrollment->next_due_amount = $nextPending->total_due;
                    $enrollment->next_month_name = $nextPending->month_name;
                    $enrollment->is_overdue      = $nextPending->status === 'overdue'
                        || $nextPending->due_date < Carbon::today();
                    $enrollment->weeks_overdue   = $nextPending->weeks_overdue;
                } else {
                    $enrollment->next_due_date   = null;
                    $enrollment->next_due_amount = 0;
                    $enrollment->next_month_name = 'Fully Paid';
                    $enrollment->is_overdue      = false;
                    $enrollment->weeks_overdue   = 0;
                }

                $enrollment->total_late_fees = $enrollment->paymentInstallments->sum('late_fee');
                $totalInstallments = $enrollment->paymentInstallments->count();
                $paidInstallments  = $enrollment->paymentInstallments->where('status', 'paid')->count();
                $enrollment->installment_progress = $totalInstallments > 0
                    ? ($paidInstallments / $totalInstallments) * 100
                    : 0;
            }
        }

        // Filter by overdue after calculating
        if ($request->has('overdue') && $request->overdue === 'yes') {
            $enrollments = $enrollments->filter(function ($e) {
                return $e->is_overdue;
            });
        }

        // Stats (from loaded collection for accuracy on paginated + filtered results)
        $instStats = [
            'total_students' => $enrollments->count(),
            'overdue'        => $enrollments->where('is_overdue', true)->count(),
            'fully_paid'     => $enrollments->where('payment_status', 'paid')->count(),
            'partial'        => $enrollments->where('payment_status', 'partial')->count(),
            'total_late_fees' => $enrollments->sum('total_late_fees'),
        ];

        $installmentEnrollments = $enrollments;
        $totalLateFeesAll = $instStats['total_late_fees'] ?? 0;

        return view('finance.installments', compact('installmentEnrollments', 'sort', 'schoolYears', 'instStats', 'totalLateFeesAll'));
    }

    /**
     * All-students payment overview (cash + installment)
     */
    public function students(Request $request)
    {
        $search     = $request->input('search', '');
        $planFilter = $request->input('plan', 'all');   // all | cash | installment
        $statusFilter = $request->input('status', 'all'); // all | paid | partial | pending
        $yearFilter = $request->input('school_year', 'all');

        $query = \App\Models\User::where('role', 'student')
            ->whereNull('deleted_at')
            ->whereHas('enrollments')
            ->with(['enrollments' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allStudents = $query->get()->map(function ($user) {
            $enrollment = $user->enrollments->first();
            return (object) [
                'user'           => $user,
                'enrollment'     => $enrollment,
                'grade_level'    => $enrollment?->grade_level ?? '—',
                'school_year'    => $enrollment?->school_year ?? '—',
                'payment_option' => $enrollment?->payment_option ?? '—',
                'payment_type'   => $enrollment?->payment_type ?? '—',
                'total_fee'      => (float) ($enrollment?->total_fee ?? 0),
                'amount_paid'    => (float) ($enrollment?->payment_amount ?? 0),
                'balance'        => max(0, (float)($enrollment?->total_fee ?? 0) - (float)($enrollment?->payment_amount ?? 0)),
                'payment_status' => $enrollment?->payment_status ?? 'pending',
                'enrollment_id'  => $enrollment?->id,
                'is_cash'        => $enrollment?->payment_option === 'A' || ($enrollment?->payment_type === 'full' && !in_array($enrollment?->payment_option, ['B','C','D'])),
            ];
        });

        // Apply filters
        if ($planFilter !== 'all') {
            $allStudents = $allStudents->filter(fn($s) =>
                $planFilter === 'cash'
                    ? $s->is_cash
                    : !$s->is_cash
            );
        }
        if ($statusFilter !== 'all') {
            $allStudents = $allStudents->filter(fn($s) => $s->payment_status === $statusFilter);
        }
        if ($yearFilter !== 'all') {
            $allStudents = $allStudents->filter(fn($s) => $s->school_year === $yearFilter);
        }

        $stats = [
            'total'   => $allStudents->count(),
            'paid'    => $allStudents->where('payment_status', 'paid')->count(),
            'partial' => $allStudents->where('payment_status', 'partial')->count(),
            'pending' => $allStudents->filter(fn($s) => !in_array($s->payment_status, ['paid', 'partial']))->count(),
            'cash'    => $allStudents->where('is_cash', true)->count(),
            'installment' => $allStudents->where('is_cash', false)->count(),
            'total_collected' => $allStudents->sum('amount_paid'),
            'total_balance'   => $allStudents->sum('balance'),
        ];

        $schoolYears = $this->getSchoolYears();

        return view('finance.students', compact('allStudents', 'stats', 'search', 'planFilter', 'statusFilter', 'yearFilter', 'schoolYears'));
    }

    /**
     * Show fee management page
     */
    public function fees()
    {
        $feeSettings = FeeSetting::first();
        
        if (!$feeSettings) {
            $feeSettings = FeeSetting::create([
                'tuition' => 7505,
                'misc' => 2800,
                'insurance' => 300,
                'electric' => 600,
            ]);
        }

        // Calculate fee breakdowns for each grade
        $gradeLevels = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
        $feeBreakdowns = [];

        foreach ($gradeLevels as $grade) {
            $feeBreakdowns[$grade] = $this->calculateFeeBreakdown($grade, $feeSettings);
        }

        return view('finance.fees', compact('feeSettings', 'feeBreakdowns'));
    }

    /**
     * Return installment schedule for an enrollment as JSON (used by dashboard modal)
     */
    public function installmentDetails(Enrollment $enrollment)
    {
        $installments = $enrollment->paymentInstallments()->orderBy('due_date')->get()
            ->map(fn($i) => [
                'month_name' => $i->month_name,
                'due_date'   => $i->due_date?->format('M d, Y'),
                'amount'     => $i->amount,
                'late_fee'   => $i->late_fee ?? 0,
                'total_due'  => $i->total_due ?? ($i->amount + ($i->late_fee ?? 0)),
                'status'     => $i->status,
                'paid_at'    => $i->paid_at?->format('M d, Y h:i A'),
            ]);

        return response()->json(['installments' => $installments]);
    }

    /**
     * Update fee settings
     */
    public function updateFees(Request $request)
    {
        \Log::info('updateFees called', ['input' => $request->all()]);
        try {
            $validated = $request->validate([
                'tuition' => 'required|numeric|min:0',
                'misc' => 'required|numeric|min:0',
                'insurance' => 'required|numeric|min:0',
                'electric' => 'required|numeric|min:0',
                'books_nursery' => 'required|numeric|min:0',
                'books_grade1' => 'required|numeric|min:0',
                'books_grade3' => 'required|numeric|min:0',
                'books_grade4' => 'required|numeric|min:0',
                'option_a_discount' => 'required|numeric|min:0',
                // Option B
                'optb_monthly_tuition' => 'required|numeric|min:0',
                'optb_monthly_electric' => 'required|numeric|min:0',
                'optb_dp_nursery' => 'required|numeric|min:0',
                'optb_dp_kinder' => 'required|numeric|min:0',
                'optb_dp_grade1' => 'required|numeric|min:0',
                'optb_dp_grade3' => 'required|numeric|min:0',
                'optb_dp_grade4' => 'required|numeric|min:0',
                // Option C
                'optc_monthly_tuition' => 'required|numeric|min:0',
                'optc_monthly_misc' => 'required|numeric|min:0',
                'optc_monthly_electric' => 'required|numeric|min:0',
                'optc_dp_nursery' => 'required|numeric|min:0',
                'optc_dp_kinder' => 'required|numeric|min:0',
                'optc_dp_grade1' => 'required|numeric|min:0',
                'optc_dp_grade3' => 'required|numeric|min:0',
                'optc_dp_grade4' => 'required|numeric|min:0',
                // Option D
                'optd_monthly_tuition' => 'required|numeric|min:0',
                'optd_monthly_misc' => 'required|numeric|min:0',
                'optd_monthly_electric' => 'required|numeric|min:0',
                'optd_dp_nursery' => 'required|numeric|min:0',
                'optd_dp_kinder' => 'required|numeric|min:0',
                'optd_dp_grade1' => 'required|numeric|min:0',
                'optd_dp_grade3' => 'required|numeric|min:0',
                'optd_dp_grade4' => 'required|numeric|min:0',
            ]);

            $feeSettings = FeeSetting::first();
            if ($feeSettings) {
                $feeSettings->update($validated);
            } else {
                FeeSetting::create($validated);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Fee settings updated successfully.']);
            }

            return redirect()->back()->with('success', 'Fee settings updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save fee settings: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show financial reports
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'daily');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $reportData = $this->generateReport($reportType, $dateFrom, $dateTo);

        return view('finance.reports', compact('reportData', 'reportType', 'dateFrom', 'dateTo'));
    }

    /**
     * Approve a payment
     */
    public function approvePayment(Request $request, $id)
    {
        // Always check for a pending payment screenshot first.
        // The admin dashboard passes StudentDocument IDs, and those IDs can collide
        // numerically with PaymentTransaction IDs, causing the wrong approval path.
        $document = \App\Models\StudentDocument::where('id', $id)
            ->where('document_type', 'payment_screenshot')
            ->where('status', 'pending')
            ->first();

        if ($document) {
            return $this->approveDocumentPayment($request, $document);
        }

        $payment = PaymentTransaction::find($id);

        if (!$payment) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Payment not found.'], 404);
            }
            return redirect()->back()->with('error', 'Payment not found.');
        }

        try {
            $payment->update([
                'status' => 'completed',
                'processed_by' => Auth::guard('finance')->id(),
                'processed_at' => now(),
            ]);

            // If enrollment exists, update payment amounts
            if ($payment->enrollment) {
                $enrollment = $payment->enrollment;

                // Get amount from payment record
                $docAmount = floatval($payment->amount);

                // Determine effective payment type (handle legacy data missing payment_type)
                $effectivePaymentType = $enrollment->payment_type;
                if (!$effectivePaymentType && $enrollment->payment_option) {
                    $effectivePaymentType = $enrollment->payment_option === 'A' ? 'full' : 'installment';
                    // Auto-fix missing payment_type
                    $enrollment->update(['payment_type' => $effectivePaymentType]);
                }

                // Fallback: unknown payment type — treat as a general payment
                if (!in_array($effectivePaymentType, ['full', 'installment']) && $docAmount > 0) {
                    $newPaid  = floatval($enrollment->payment_amount ?? 0) + $docAmount;
                    $totalFee = floatval($enrollment->total_fee ?? 0);
                    $remainingBalance = max(0, $totalFee - $newPaid);
                    $enrollment->update([
                        'payment_amount'    => $newPaid,
                        'payment_status'    => ($totalFee > 0 && $newPaid >= $totalFee) ? 'paid' : 'partial',
                        'remaining_balance' => $remainingBalance,
                    ]);
                }

                if ($effectivePaymentType === 'full') {
                    // Full payment (Option A) — increment then compute from the new value directly
                    $newPaid = floatval($enrollment->payment_amount ?? 0) + $docAmount;
                    $totalFee = floatval($enrollment->total_fee ?? 0);
                    $remainingBalance = max(0, $totalFee - $newPaid);

                    $enrollment->update([
                        'payment_amount'   => $newPaid,
                        'remaining_balance' => $remainingBalance,
                        'payment_status'   => $remainingBalance <= 0 ? 'paid' : 'partial',
                    ]);
                } elseif ($effectivePaymentType === 'installment') {
                    // Check if this is a downpayment (not yet fully paid)
                    $downpaymentAmount = floatval($enrollment->downpayment_amount ?? 0);
                    $alreadyPaid       = floatval($enrollment->payment_amount ?? 0);
                    $isDownpayment     = $downpaymentAmount > 0 && $alreadyPaid < $downpaymentAmount;

                    // Find the installment that matches this payment
                    // Priority: 1) linked to payment, 2) pending_approval with payment id,
                    //           3) any pending_approval, 4) pending matching amount, 5) next pending
                    $installment = $payment->installment;

                    if (!$installment) {
                        $installment = $enrollment->paymentInstallments()
                            ->where('status', 'pending_approval')
                            ->where('payment_transaction_id', $payment->id)
                            ->first();
                    }

                    // Only do broader lookups when not a downpayment
                    if (!$installment && !$isDownpayment) {
                        $installment = $enrollment->paymentInstallments()
                            ->where('status', 'pending_approval')
                            ->orderBy('due_date')
                            ->first();

                        if (!$installment) {
                            $installment = $enrollment->paymentInstallments()
                                ->whereIn('status', ['pending', 'overdue'])
                                ->whereRaw('CAST(amount AS DECIMAL(10,2)) = ?', [$docAmount])
                                ->orderBy('due_date')
                                ->first();
                        }

                        if (!$installment && $docAmount > 0) {
                            $installment = $enrollment->paymentInstallments()
                                ->whereIn('status', ['pending', 'overdue'])
                                ->orderBy('due_date')
                                ->first();
                        }
                    }

                    // Process the found installment
                    if ($installment && !$isDownpayment) {
                        $totalDue    = floatval($installment->amount ?? 0) + floatval($installment->late_fee ?? 0);
                        $amountPaid  = $docAmount > 0 ? $docAmount : $totalDue;

                        $installment->update([
                            'status'         => 'paid',
                            'paid_at'        => now(),
                            'payment_method' => $payment->payment_method,
                            'payment_transaction_id' => $payment->id,
                            'amount_paid'    => $amountPaid,
                        ]);

                        // Compute new totals without an extra DB round-trip
                        $newPaid          = $alreadyPaid + $amountPaid;
                        $totalFee         = floatval($enrollment->total_fee ?? 0);
                        $remainingBalance = max(0, $totalFee - $newPaid);

                        $updateData = [
                            'payment_amount'    => $newPaid,
                            'remaining_balance' => $remainingBalance,
                            'payment_status'    => $remainingBalance <= 0 ? 'paid' : 'partial',
                        ];

                        if ($remainingBalance <= 0) {
                            $updateData['next_installment_date'] = null;
                        } else {
                            $nextPending = $enrollment->paymentInstallments()
                                ->whereIn('status', ['pending', 'overdue'])
                                ->orderBy('due_date')
                                ->first();
                            if ($nextPending) {
                                $updateData['next_installment_date'] = $nextPending->due_date;
                            }
                        }

                        $enrollment->update($updateData);
                    } elseif ($isDownpayment) {
                        // Downpayment — compute without extra DB round-trip
                        $newPaid          = $alreadyPaid + ($docAmount > 0 ? $docAmount : 0);
                        $totalFee         = floatval($enrollment->total_fee ?? 0);
                        $remainingBalance = max(0, $totalFee - $newPaid);

                        $dpUpdate = [
                            'payment_amount'    => $newPaid,
                            'remaining_balance' => $remainingBalance,
                            'payment_status'    => $remainingBalance <= 0 ? 'paid' : 'partial',
                        ];

                        // Point next_installment_date at the first monthly installment
                        $firstInstallment = $enrollment->paymentInstallments()
                            ->whereIn('status', ['pending', 'overdue'])
                            ->orderBy('due_date')
                            ->first();
                        if ($firstInstallment) {
                            $dpUpdate['next_installment_date'] = $firstInstallment->due_date;
                        }

                        $enrollment->update($dpUpdate);
                    }
                }
            }

            // Update enrollment payment status
            if ($payment->enrollment) {
                $this->updateEnrollmentPaymentStatus($payment->enrollment);
            }

            // Return JSON for AJAX requests, otherwise redirect
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment approved successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Payment approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Approve a payment screenshot (StudentDocument) from admin dashboard.
     * Creates a PaymentTransaction and updates enrollment payment amounts.
     */
    private function approveDocumentPayment(Request $request, \App\Models\StudentDocument $document)
    {
        try {
            DB::beginTransaction();

            $document->update([
                'status' => 'approved',
                'reviewed_by' => Auth::guard('finance')->id(),
                'reviewed_at' => now(),
            ]);

            $enrollment = $document->enrollment;
            $installment = $document->paymentInstallment;

            if ($enrollment) {
                if ($installment) {
                    $amountPaid = floatval($installment->amount) + floatval($installment->late_fee ?? 0);
                } else {
                    // Full payment / downpayment — extract amount from description
                    // Format: "Payment via GCash - ₱14,504.00"
                    preg_match('/₱([\d,]+\.\d{2})/', $document->description, $matches);
                    $amountPaid = isset($matches[1]) ? floatval(str_replace(',', '', $matches[1])) : 0;

                    // Fallback: use total_fee for full payments, downpayment_amount for installments
                    if ($amountPaid <= 0) {
                        $effectiveType = $enrollment->payment_type
                            ?? ($enrollment->payment_option === 'A' ? 'full' : 'installment');
                        $amountPaid = $effectiveType === 'full'
                            ? floatval($enrollment->total_fee ?? 0)
                            : floatval($enrollment->downpayment_amount ?? 0);
                    }
                }
                $oldPaid = floatval($enrollment->payment_amount ?? 0);
                $totalFee = round(floatval($enrollment->total_fee ?? 0), 2);
                $newPaid = round($oldPaid + $amountPaid, 2);
                $remainingBalance = max(0, round($totalFee - $newPaid, 2));

                // Resolve actual payment method: installment record is set during submission,
                // fall back to parsing the document description.
                $actualMethod = $installment?->payment_method
                    ?? (stripos($document->description ?? '', 'via Cash') !== false ? 'cash' : 'gcash');

                // Create a PaymentTransaction record
                $paymentTransaction = PaymentTransaction::create([
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $document->user_id,
                    'payment_type' => $installment ? 'installment' : 'full',
                    'payment_method' => $actualMethod,
                    'amount' => $amountPaid,
                    'reference_number' => null,
                    'description' => 'Payment screenshot approved - ' . ($installment ? $installment->month_name . ' Installment' : 'Full Payment'),
                    'status' => 'completed',
                    'installment_month' => $installment ? $installment->month_name : null,
                    'installment_id' => $installment ? $installment->id : null,
                    'processed_by' => Auth::guard('finance')->id(),
                    'processed_at' => now(),
                ]);

                // Update installment if linked
                if ($installment) {
                    $installment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'payment_method' => $actualMethod,
                        'payment_transaction_id' => $paymentTransaction->id,
                        'amount_paid' => $amountPaid,
                    ]);

                    // Set next installment date
                    $nextPending = $enrollment->paymentInstallments()
                        ->whereIn('status', ['pending', 'overdue'])
                        ->orderBy('due_date')
                        ->first();

                    $enrollment->update([
                        'payment_amount' => $newPaid,
                        'remaining_balance' => $remainingBalance,
                        'payment_status' => $remainingBalance <= 0 ? 'paid' : 'partial',
                        'next_installment_date' => $nextPending ? $nextPending->due_date : null,
                    ]);
                } else {
                    // Full payment path
                    $enrollment->update([
                        'payment_amount' => $newPaid,
                        'remaining_balance' => $remainingBalance,
                        'payment_status' => $remainingBalance <= 0 ? 'paid' : 'partial',
                    ]);
                }
            }

            // Update enrollment status (approved -> enrolled)
            if ($enrollment) {
                $this->updateEnrollmentPaymentStatus($enrollment);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment approved and student balance updated.',
                ]);
            }

            return redirect()->back()->with('success', 'Payment approved and student balance updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Record a walk-in cash/GCash payment directly from finance management.
     * The installment is marked paid immediately — no pending approval step.
     */
    public function recordWalkInPayment(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'installment_id'   => 'nullable|integer|exists:payment_installments,id',
            'payment_method'   => 'required|in:cash,gcash',
            'amount'           => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $methodLabel = $request->payment_method === 'gcash' ? 'GCash' : 'Cash';
        $amountPaid  = floatval($request->amount);

        // Check if this is a downpayment (not yet fully paid)
        $downpaymentAmount = floatval($enrollment->downpayment_amount ?? 0);
        $alreadyPaid       = floatval($enrollment->payment_amount ?? 0);
        $isDownpayment     = $downpaymentAmount > 0 && $alreadyPaid < $downpaymentAmount;

        $installment = null;
        $installmentMonth = null;

        // Only process installment if provided and not a downpayment
        if ($request->installment_id && !$isDownpayment) {
            $installment = PaymentInstallment::find($request->installment_id);

            if (!$installment || $installment->enrollment_id !== $enrollment->id) {
                return redirect()->back()->with('error', 'Invalid installment record.');
            }

            if ($installment->status === 'paid') {
                return redirect()->back()->with('error', 'This installment is already paid.');
            }

            if ($installment->status === 'pending_approval') {
                return redirect()->back()->with('error', 'This installment has a student-submitted payment awaiting approval. Approve or reject it first.');
            }

            $installmentMonth = $installment->month_name;
        } elseif ($isDownpayment) {
            $installmentMonth = 'Downpayment';
        }

        // Create an already-approved payment transaction so it appears in payment history
        $payment = PaymentTransaction::create([
            'user_id'       => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'payment_type'  => $isDownpayment ? 'downpayment' : 'walkin',
            'payment_method' => $request->payment_method,
            'amount'        => $amountPaid,
            'reference_number' => $request->reference_number,
            'description'   => 'Walk-in payment via ' . $methodLabel . ' - ' . ($installmentMonth ?? 'Payment') . ' - ₱' . number_format($amountPaid, 2),
            'status'        => 'completed',
            'installment_month' => $installmentMonth,
            'installment_id' => $installment ? $installment->id : null,
            'processed_by'  => Auth::guard('finance')->id(),
            'processed_at'  => now(),
        ]);

        // Mark the installment as paid immediately (only if not a downpayment)
        if ($installment && !$isDownpayment) {
            $installment->update([
                'status'           => 'paid',
                'paid_at'          => now(),
                'payment_method'   => $request->payment_method,
                'reference_number' => $request->reference_number,
                'payment_transaction_id' => $payment->id,
                'amount_paid'      => $amountPaid,
            ]);
        }

        // Update enrollment totals
        $alreadyPaid      = floatval($enrollment->payment_amount ?? 0);
        $newPaid          = $alreadyPaid + $amountPaid;
        $totalFee         = floatval($enrollment->total_fee ?? 0);
        $remainingBalance = max(0, $totalFee - $newPaid);

        $updateData = [
            'payment_amount'    => $newPaid,
            'remaining_balance' => $remainingBalance,
            'payment_status'    => $remainingBalance <= 0 ? 'paid' : 'partial',
        ];

        if ($remainingBalance <= 0) {
            $updateData['next_installment_date'] = null;
        } else {
            $next = $enrollment->paymentInstallments()
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('due_date')
                ->first();
            if ($next) {
                $updateData['next_installment_date'] = $next->due_date;
            }
        }

        $enrollment->update($updateData);

        // Reconcile installment statuses for installment plans
        if ($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D'])) {
            \App\Services\PaymentService::reconcileInstallmentStatuses($enrollment);
        }

        // Update enrollment status (approved -> enrolled)
        $this->updateEnrollmentPaymentStatus($enrollment);

        $monthDisplay = $installmentMonth ?? ($isDownpayment ? 'Downpayment' : 'Payment');
        return redirect()->route('finance.payments.index')
            ->with('success', 'Walk-in payment recorded for ' . $monthDisplay . ' — ₱' . number_format($amountPaid, 2) . ' via ' . $methodLabel . '. Payment is listed below.');
    }

    /**
     * Process admin payment from admin dashboard finance management
     * Uses PaymentService for proper handling like student portal
     */
    public function processAdminPayment(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'payment_method' => 'required|in:gcash,cash',
            'payment_amount' => 'required|numeric|min:1',
            'payment_reference' => 'nullable|string|max:255',
            'payment_option' => 'nullable|in:A,B,C,D',
            'installment_id' => 'nullable|integer|exists:payment_installments,id',
        ]);

        DB::beginTransaction();
        try {
            $methodLabel = $request->payment_method === 'gcash' ? 'GCash' : 'Cash';
            $amountPaid = (float) $request->payment_amount;

            // Persist total_fee from breakdown when the enrollment doesn't have it set yet
            $breakdown = $request->input('payment_breakdown', []);
            $breakdownTotal = floatval($breakdown['total'] ?? 0);
            if (floatval($enrollment->total_fee ?? 0) <= 0 && $breakdownTotal > 0) {
                $enrollment->update(['total_fee' => $breakdownTotal]);
                $enrollment->refresh();
            }

            // Create PaymentTransaction record (completed since it's admin payment)
            $payment = PaymentTransaction::create([
                'user_id'       => $enrollment->user_id,
                'enrollment_id' => $enrollment->id,
                'payment_type'  => 'admin',
                'payment_method' => $request->payment_method,
                'amount'        => $amountPaid,
                'reference_number' => $request->payment_reference,
                'description'   => 'Admin payment via ' . $methodLabel . ' - ₱' . number_format($amountPaid, 2) . ($request->payment_reference ? ' (Ref: ' . $request->payment_reference . ')' : ''),
                'status'        => 'completed',
                'processed_by'  => Auth::guard('finance')->id(),
                'processed_at'  => now(),
            ]);

            // Check if this is an installment plan
            $isInstallment = $enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']);

            if ($isInstallment) {
                // Ensure installments exist
                PaymentService::createInstallments($enrollment);

                // Detect if this payment is a downpayment (student hasn't paid downpayment yet)
                $downpaymentAmount = floatval($enrollment->downpayment_amount ?? 0);
                $alreadyPaid       = floatval($enrollment->payment_amount ?? 0);
                $isDownpayment     = $downpaymentAmount > 0 && $alreadyPaid < $downpaymentAmount;

                $installmentMonth = '';

                // If specific installment ID provided, process that installment
                if ($request->installment_id) {
                    $installment = PaymentInstallment::find($request->installment_id);
                    if ($installment && $installment->enrollment_id === $enrollment->id) {
                        $installmentMonth = $installment->month_name ?? '';
                        $installment->update([
                            'status'           => 'paid',
                            'paid_at'          => now(),
                            'payment_method'   => $request->payment_method,
                            'reference_number' => $request->payment_reference,
                            'payment_transaction_id' => $payment->id,
                            'amount_paid'      => $amountPaid,
                        ]);
                    }
                } elseif (!$isDownpayment) {
                    // Downpayment already paid — this is a monthly installment payment.
                    // Find next pending installment and mark it as paid.
                    $nextInstallment = $enrollment->paymentInstallments()
                        ->whereIn('status', ['pending', 'overdue'])
                        ->orderBy('due_date')
                        ->first();
                    if ($nextInstallment) {
                        $installmentMonth = $nextInstallment->month_name ?? '';
                        $nextInstallment->update([
                            'status'           => 'paid',
                            'paid_at'          => now(),
                            'payment_method'   => $request->payment_method,
                            'reference_number' => $request->payment_reference,
                            'payment_transaction_id' => $payment->id,
                            'amount_paid'      => $amountPaid,
                        ]);
                    }
                }
                // If $isDownpayment && no installment_id, do NOT mark any installment as paid.
                // The downpayment is a separate payment from the 9 monthly installments.

                // Update payment description with installment month
                if ($installmentMonth) {
                    $payment->update([
                        'description' => 'Admin payment via ' . $methodLabel . ' - ' . $installmentMonth . ' - ₱' . number_format($amountPaid, 2) . ($request->payment_reference ? ' (Ref: ' . $request->payment_reference . ')' : ''),
                        'installment_month' => $installmentMonth,
                    ]);
                }

                // Reconcile installment statuses
                PaymentService::reconcileInstallmentStatuses($enrollment);

                // Get payment summary for next due date
                $summary = PaymentService::getPaymentSummary($enrollment);

                // Calculate payment amount: existing amount + new payment
                // Don't use $summary['total_paid'] because it only sums installments,
                // not downpayments (which aren't attached to any installment)
                $currentPaid = floatval($enrollment->payment_amount ?? 0);
                $totalPaid = round($currentPaid + $amountPaid, 2);
                $totalFee  = round(floatval($enrollment->total_fee ?? 0), 2);
                $remainingBalance = max(0, round($totalFee - $totalPaid, 2));

                // Update enrollment
                $enrollment->update([
                    'payment_amount'    => $totalPaid,
                    'remaining_balance' => $remainingBalance,
                    'payment_status'    => $remainingBalance <= 0 ? 'paid' : 'partial',
                    'next_installment_date' => ($summary['next_due'] ?? null) ? $summary['next_due']['due_date'] : null,
                ]);
            } else {
                // Full payment plan
                $oldAmount = floatval($enrollment->payment_amount ?? 0);
                $newAmount = round($oldAmount + $amountPaid, 2);
                $totalFee  = round(floatval($enrollment->total_fee ?? 0), 2);
                $remainingBalance = max(0, round($totalFee - $newAmount, 2));

                $enrollment->update([
                    'payment_amount'    => $newAmount,
                    'remaining_balance' => $remainingBalance,
                    'payment_status'    => $remainingBalance <= 0 ? 'paid' : 'partial',
                    'payment_method'    => $request->payment_method,
                    'payment_reference' => $request->payment_reference,
                ]);
            }

            // Update enrollment status (approved -> enrolled)
            $this->updateEnrollmentPaymentStatus($enrollment);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully.',
                    'payment_amount' => $enrollment->payment_amount,
                    'remaining_balance' => $enrollment->remaining_balance,
                    'payment_status' => $enrollment->payment_status,
                ]);
            }

            return redirect()->back()->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject a payment
     */
    public function rejectPayment(Request $request, $id)
    {
        // Always check for a pending payment screenshot first (same ID-collision reason as approve).
        $document = \App\Models\StudentDocument::where('id', $id)
            ->where('document_type', 'payment_screenshot')
            ->where('status', 'pending')
            ->first();

        if ($document) {
            return $this->rejectDocumentPayment($request, $document);
        }

        $payment = PaymentTransaction::find($id);

        if (!$payment) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Payment not found.'], 404);
            }
            return redirect()->back()->with('error', 'Payment not found.');
        }

        $validated = $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        $payment->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'processed_by' => Auth::guard('finance')->id(),
            'processed_at' => now(),
        ]);

        // If there's an installment linked, set it back to pending
        if ($payment->installment) {
            $payment->installment->update([
                'status' => 'pending',
                'payment_transaction_id' => null,
            ]);
        }

        // Return JSON for AJAX requests, otherwise redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment rejected.',
            ]);
        }

        return redirect()->back()->with('success', 'Payment rejected.');
    }

    /**
     * Reject a payment screenshot (StudentDocument) from admin dashboard.
     */
    private function rejectDocumentPayment(Request $request, \App\Models\StudentDocument $document)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        $document->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'reviewed_by' => Auth::guard('finance')->id(),
            'reviewed_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment screenshot rejected.',
            ]);
        }

        return redirect()->back()->with('success', 'Payment screenshot rejected.');
    }

    /**
     * Delete a payment
     */
    public function deletePayment(PaymentTransaction $payment)
    {
        // Only allow deletion of pending payments
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending payments can be deleted.');
        }

        DB::beginTransaction();
        try {
            // If there's a linked installment, update it to remove the payment link
            if ($payment->installment) {
                $payment->installment->update([
                    'payment_transaction_id' => null,
                    'status' => 'pending',
                    'paid_at' => null,
                ]);
            }

            // Also revert any pending_approval installments linked to this payment
            if ($payment->enrollment) {
                $payment->enrollment->paymentInstallments()
                    ->where('status', 'pending_approval')
                    ->where('payment_transaction_id', $payment->id)
                    ->update([
                        'status' => 'pending',
                        'amount_paid' => null,
                        'payment_method' => null,
                        'reference_number' => null,
                        'payment_transaction_id' => null,
                    ]);
            }

            // Delete the payment transaction
            $payment->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    /**
     * View payment details
     */
    public function paymentDetails(PaymentTransaction $payment)
    {
        $payment->load(['enrollment', 'user', 'processedBy']);

        return view('finance.payment-details', ['document' => $payment]);
    }

    /**
     * Helper: Format school year — cached for 24 hours to avoid repeated DB hits
     */
    private function formatSchoolYear()
    {
        return \Illuminate\Support\Facades\Cache::remember('current_school_year', 86400, function () {
            $latestSchoolYear = Section::where('is_active', true)
                ->orderByDesc('school_year')
                ->value('school_year');

            if ($latestSchoolYear) {
                return $latestSchoolYear;
            }

            $currentYear = Carbon::now()->year;
            $month       = Carbon::now()->month;

            return $month >= 6
                ? $currentYear . '-' . ($currentYear + 1)
                : ($currentYear - 1) . '-' . $currentYear;
        });
    }

    /**
     * Helper: Get list of school years for filters
     */
    private function getSchoolYears(): array
    {
        $configured = \App\Models\Setting::where('key', 'current_school_year')->value('value') ?? '2026-2027';
        $base = (int) substr($configured, 0, 4);
        $years = [];
        for ($y = $base; $y <= $base + 10; $y++) {
            $years[] = $y . '-' . ($y + 1);
        }
        return $years;
    }

    /**
     * Helper: Calculate fee breakdown
     */
    private function calculateFeeBreakdown($gradeLevel, $feeSettings)
    {
        // Get correct book fee based on grade
        $bookFee = 0;
        if (in_array($gradeLevel, ['nursery'])) {
            $bookFee = $feeSettings->books_nursery ?? 0;
        } elseif (in_array($gradeLevel, ['kindergarten'])) {
            $bookFee = $feeSettings->books_nursery ?? 0;
        } elseif (in_array($gradeLevel, ['grade1', 'grade2'])) {
            $bookFee = $feeSettings->books_grade1 ?? 0;
        } elseif (in_array($gradeLevel, ['grade3'])) {
            $bookFee = $feeSettings->books_grade3 ?? 0;
        } elseif (in_array($gradeLevel, ['grade4', 'grade5', 'grade6'])) {
            $bookFee = $feeSettings->books_grade4 ?? 0;
        }

        $baseTotal = ($feeSettings->tuition ?? 0) + 
                     ($feeSettings->misc ?? 0) + 
                     ($feeSettings->insurance ?? 0) + 
                     ($feeSettings->electric ?? 0) + 
                     $bookFee;

        return [
            'tuition' => $feeSettings->tuition ?? 0,
            'misc' => $feeSettings->misc ?? 0,
            'insurance' => $feeSettings->insurance ?? 0,
            'electric' => $feeSettings->electric ?? 0,
            'books' => $bookFee,
            'base_total' => $baseTotal,
        ];
    }

    /**
     * Helper: Update enrollment payment status after approval
     */
    private function updateEnrollmentPaymentStatus($enrollment)
    {
        // Re-read from DB to get the latest values written by approvePayment()
        $enrollment = Enrollment::find($enrollment->id);

        // Check if there are any approved payment transactions for this enrollment
        $hasApprovedPayments = PaymentTransaction::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->exists();

        $totalPaid = round(floatval($enrollment->payment_amount ?? 0), 2);
        $totalFee  = round(floatval($enrollment->total_fee ?? 0), 2);

        // For installment plans, also count approved installment payments
        $isInstallment = $enrollment->payment_type === 'installment'
            || in_array($enrollment->payment_option, ['B', 'C', 'D']);

        if ($isInstallment) {
            // Sum all paid installment amounts for accurate total
            $installmentPaid = round(floatval($enrollment->paymentInstallments()
                ->where('status', 'paid')
                ->sum('amount_paid')), 2);
            // Use the higher of enrollment payment_amount or sum of paid installments
            $totalPaid = max($totalPaid, $installmentPaid);
        }

        // Update payment status
        if ($totalFee > 0) {
            // Total fee is known — compare paid vs total
            if ($totalPaid >= $totalFee) {
                $enrollment->update([
                    'payment_status'    => 'paid',
                    'remaining_balance' => 0,
                ]);
            } elseif ($totalPaid > 0) {
                $enrollment->update([
                    'payment_status'    => 'partial',
                    'remaining_balance' => max(0, $totalFee - $totalPaid),
                ]);
            } elseif ($hasApprovedPayments) {
                // Approved screenshots exist but amount is 0 — mark partial so portal no longer shows 'pending'
                $enrollment->update(['payment_status' => 'partial']);
            }
        } elseif ($totalPaid > 0 || $hasApprovedPayments) {
            // total_fee not set — trust remaining_balance already computed by the payment recorder;
            // do NOT downgrade a 'paid' status that was correctly set when remaining_balance hit 0
            $remainingBal = round(floatval($enrollment->remaining_balance ?? 0), 2);
            $enrollment->update(['payment_status' => $remainingBal <= 0 ? 'paid' : 'partial']);
        }

        // If enrollment is 'approved' or 'pending' and any payment has been approved, mark as 'enrolled'
        if (in_array($enrollment->status, ['approved', 'pending', 'completed']) && ($totalPaid > 0 || $hasApprovedPayments)) {
            $enrollment->update([
                'status'      => 'enrolled',
                'enrolled_at' => now(),
            ]);

            // Auto-assign student to section based on grade level
            if ($enrollment->user_id) {
                $user = \App\Models\User::find($enrollment->user_id);
                if ($user) {
                    // Support both direct grade_level column and student_data JSON
                    $gradeLevel = $enrollment->grade_level
                        ?? ($enrollment->student_data['grade_level'] ?? null);
                    $schoolYear = $enrollment->school_year
                        ?? (now()->year . '-' . (now()->year + 1));

                    if ($gradeLevel) {
                        $section = \App\Models\Section::where('grade_level', $gradeLevel)
                            ->where('school_year', $schoolYear)
                            ->where('is_active', true)
                            ->first();

                        if ($section) {
                            $enrollment->update(['section' => $section->name]);
                            $exists = \Illuminate\Support\Facades\DB::table('section_student')
                                ->where('section_id', $section->id)
                                ->where('user_id', $user->id)
                                ->exists();
                            if (!$exists) {
                                \Illuminate\Support\Facades\DB::table('section_student')->insert([
                                    'section_id' => $section->id,
                                    'user_id'    => $user->id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $section->increment('current_enrollment');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Helper: Generate report data
     */
    private function generateReport($type, $dateFrom, $dateTo)
    {
        $query = PaymentTransaction::whereIn('status', ['completed', 'approved'])
            ->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);

        return [
            'total_payments' => $query->count(),
            'payments_by_method' => [
                'gcash' => (clone $query)->where('payment_method', 'gcash')->count(),
                'cash' => (clone $query)->where('payment_method', 'cash')->count(),
            ],
            'daily_breakdown' => $query->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get(),
        ];
    }

    /**
     * Securely serve a student document file.
     */
    public function viewDocument(StudentDocument $document)
    {
        if (!$document->file_path) {
            abort(404, 'File not found.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $path = Storage::disk('public')->path($document->file_path);
        return response()->file($path);
    }
}
