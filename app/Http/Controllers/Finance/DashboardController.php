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
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use \App\Support\Traits\BuildsSchoolPdf;

    /**
     * Id of whichever staff account actually performed the action.
     *
     * approvePayment/rejectPayment/processAdminPayment are reachable from
     * BOTH an admin-side route (default 'web' guard) and a finance-portal
     * route (finance guard) — Auth::guard('finance')->id() alone returned
     * null every time an admin/superadmin used the admin-side route, so
     * 'processed_by'/'reviewed_by' were silently never recorded for those.
     */
    private function actingStaffId(): ?int
    {
        return Auth::guard('finance')->id() ?? Auth::guard('web')->id();
    }

    /**
     * Common letterhead data every Finance PDF report shares (school
     * identity, who generated it and when) — kept in one place so the five
     * report PDFs can't quietly drift apart on this. Thin Finance-specific
     * wrapper around the portal-agnostic BuildsSchoolPdf trait (Cashier and
     * other portals have their own equally small wrapper).
     */
    private function financePdfLetterhead(): array
    {
        $staffId = $this->actingStaffId();
        $staff   = $staffId ? \App\Models\User::find($staffId) : null;

        return $this->schoolPdfLetterhead($staff->name ?? 'Finance Staff');
    }

    /**
     * Shared DomPDF options for every Finance report — see
     * BuildsSchoolPdf::schoolPdfOptions() for what this actually does.
     */
    private function financePdfOptions(\Barryvdh\DomPDF\PDF $pdf, array $meta = []): \Barryvdh\DomPDF\PDF
    {
        return $this->schoolPdfOptions($pdf, $meta);
    }

    /**
     * Show finance portal dashboard
     */
    public function index(Request $request)
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
        $sort = $request->get('sort', 'newest');
        $allStudentsPayment = \App\Models\User::where('role', 'student')
            ->whereNull('deleted_at')
            ->with(['enrollments' => function ($q) {
                $q->latest()->with('paymentInstallments');
            }])
            ->when($sort === 'name_asc',  fn($q) => $q->orderBy('name', 'asc'))
            ->when($sort === 'name_desc', fn($q) => $q->orderBy('name', 'desc'))
            ->when($sort === 'oldest',    fn($q) => $q->orderBy('created_at', 'asc'))
            ->when(!in_array($sort, ['name_asc', 'name_desc', 'oldest']), fn($q) => $q->orderByDesc('created_at'))
            ->paginate(15, ['*'], 'student_page');

        // Section list for filter dropdown
        $sections = \App\Models\Section::orderBy('name')->get();

        // Monthly collection trend (last 6 months) — one grouped query
        // instead of one sum() per month (was 6 queries, now 1).
        $monthlyTotals = PaymentTransaction::where('status', 'completed')
            ->where('processed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(processed_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $fnChMonths = []; $fnChCollected = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $fnChMonths[]     = $m->format('M Y');
            $fnChCollected[]  = (float) ($monthlyTotals[$m->format('Y-m')] ?? 0);
        }

        // Collection-by-method totals — one query instead of 3 separate sum() calls.
        $methodTotals = PaymentTransaction::where('status', 'completed')->selectRaw("
                SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
                SUM(CASE WHEN payment_method = 'gcash' THEN amount ELSE 0 END) as gcash,
                SUM(CASE WHEN payment_method NOT IN ('cash', 'gcash') THEN amount ELSE 0 END) as other
            ")->first();
        $fnCash   = (float) $methodTotals->cash;
        $fnGcash  = (float) $methodTotals->gcash;
        $fnXendit = (float) $methodTotals->other;

        return view('finance.dashboard', compact(
            'totalCollected', 'paidCount', 'partialCount', 'unpaidCount',
            'allStudentsPayment', 'sections', 'sort',
            'fnChMonths', 'fnChCollected', 'fnCash', 'fnGcash', 'fnXendit'
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

        $statusCounts = PaymentTransaction::whereIn('payment_type', ['walkin', 'admin', 'downpayment', 'online'])
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        $combinedPayStats = [
            'total'         => ($payStatsWalkin->total ?? 0) + ($payStatsXendit->total ?? 0),
            'walkin_amount' => $payStatsWalkin->total_amount ?? 0,
            'xendit_total'  => $payStatsXendit->total        ?? 0,
            'xendit_amount' => $payStatsXendit->total_amount ?? 0,
            'pending'       => $statusCounts->get('pending', 0),
            'completed'     => $statusCounts->get('completed', 0),
            'rejected'      => $statusCounts->get('rejected', 0),
        ];

        $schoolYears = $this->getSchoolYears();

        return view('finance.payments', compact(
            'walkInTransactions', 'xenditTransactions', 'combinedPayStats', 'schoolYears'
        ));
    }

    /**
     * Transaction Report — every payment transaction (walk-in, admin,
     * downpayment, online) in a date range, as a single downloadable PDF
     * list. Optional date_from/date_to narrow it; omitted, defaults to the
     * current month like the other Finance reports.
     */
    public function downloadTransactionsPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $transactions = PaymentTransaction::whereIn('payment_type', ['walkin', 'admin', 'downpayment', 'online'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->with(['user:id,name'])
            ->orderBy('created_at')
            ->get();

        $totalAmount = $transactions->where('status', '!=', 'rejected')->sum('amount');

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Transaction Report',
            'dateRangeLabel' => Carbon::parse($dateFrom)->format('M d, Y') . ' — ' . Carbon::parse($dateTo)->format('M d, Y'),
            'transactions'   => $transactions,
            'totalAmount'    => $totalAmount,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.transactions', $data), $data);

        return $pdf->download('Transaction_Report_' . $dateFrom . '_to_' . $dateTo . '.pdf');
    }

    /**
     * Official Receipt — single-transaction PDF, the downloadable
     * counterpart to the existing browser-print receipt. Only issued for a
     * transaction that actually completed; a pending/rejected one has
     * nothing to receipt yet.
     */
    public function downloadReceiptPdf(PaymentTransaction $transaction)
    {
        if ($transaction->status !== 'completed' && $transaction->status !== 'approved') {
            abort(404, 'No receipt available — this payment has not been completed.');
        }

        $transaction->load(['user', 'enrollment', 'installment', 'processedBy']);

        $description = $transaction->installment
            ? ($transaction->installment->month_name . ' Installment')
            : ($transaction->installment_month ?? ucfirst($transaction->payment_type) . ' Payment');

        $letterhead = $this->financePdfLetterhead();

        $data = array_merge($letterhead, [
            'reportTitle'    => 'Official Receipt',
            'dateRangeLabel' => null,
            'transaction'    => $transaction,
            'orNumber'       => 'OR-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
            'description'    => $description,
            'receivedBy'     => $transaction->processedBy->name ?? $letterhead['generatedBy'],
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.receipt', $data));
        $pdf->setPaper([0, 0, 288, 420], 'portrait'); // narrow receipt-style page (4in wide)

        return $pdf->download($data['orNumber'] . '.pdf');
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
            // 'user.guardian' feeds the promissory-note modal's guardian-name
            // auto-fill (DATABASE_NORMALIZATION_PLAN.md Phase 7) — staff can
            // still edit it, this just saves them re-typing what's on file.
            ->with(['user:id,name,email', 'user.guardian', 'paymentInstallments', 'promissoryNotes']);

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
     * Accounts Receivable Report — every currently-enrolled student who
     * still owes money, as a single downloadable PDF. Reuses
     * PaymentService::getExamPermitStatus() for the overdue-months figure so
     * this can't disagree with what the Exam Permit Hold feature itself
     * considers overdue.
     */
    public function downloadReceivablesPdf(Request $request)
    {
        $yearFilter = $request->get('school_year', $this->formatSchoolYear());

        $enrollments = Enrollment::where('school_year', $yearFilter)
            ->whereIn('status', ['enrolled', 'approved'])
            ->where('remaining_balance', '>', 0)
            ->with(['user:id,name', 'paymentInstallments', 'promissoryNotes'])
            ->orderByDesc('remaining_balance')
            ->get();

        $enrollments->each(function ($e) {
            $exam = PaymentService::getExamPermitStatus($e);
            $e->overdue_months = $exam['overdue_months'];
            $e->has_active_note = (bool) $exam['note'];
        });

        $totalReceivable = $enrollments->sum('remaining_balance');

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'     => 'Accounts Receivable Report',
            'dateRangeLabel'  => 'School Year ' . $yearFilter,
            'enrollments'     => $enrollments,
            'totalReceivable' => $totalReceivable,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.receivables', $data), $data);

        return $pdf->download('Accounts_Receivable_' . str_replace([' ', '-'], ['', '_'], $yearFilter) . '.pdf');
    }

    /**
     * Aging of Receivables — same underlying balances as the Accounts
     * Receivable Report, but bucketed by how overdue each one is (standard
     * 0/1-30/31-60/61-90/90+ day aging format) instead of a flat list, so
     * staff can prioritize who to follow up with first instead of reading
     * every row. "Days overdue" is based on the OLDEST unpaid installment's
     * due date — same installment data PaymentService::getExamPermitStatus()
     * already uses for the Exam Permit Hold, just bucketed by days here
     * instead of counting months.
     */
    public function downloadAgingReportPdf(Request $request)
    {
        $yearFilter = $request->get('school_year', $this->formatSchoolYear());
        $today = Carbon::today();

        $enrollments = Enrollment::where('school_year', $yearFilter)
            ->whereIn('status', ['enrolled', 'approved'])
            ->where('remaining_balance', '>', 0)
            ->with(['user:id,name', 'paymentInstallments'])
            ->get();

        $buckets = ['current' => [], '1_30' => [], '31_60' => [], '61_90' => [], '90_plus' => []];
        $bucketLabels = ['current' => 'Current', '1_30' => '1-30 Days', '31_60' => '31-60 Days', '61_90' => '61-90 Days', '90_plus' => 'Over 90 Days'];

        foreach ($enrollments as $e) {
            $oldestOverdue = $e->paymentInstallments
                ->filter(fn ($i) => $i->due_date && $i->due_date->lt($today) && $i->status !== 'paid')
                ->sortBy('due_date')
                ->first();

            $daysOverdue = $oldestOverdue ? (int) $today->diffInDays($oldestOverdue->due_date) : 0;
            $e->days_overdue = $daysOverdue;

            $bucketKey = match (true) {
                $daysOverdue <= 0  => 'current',
                $daysOverdue <= 30 => '1_30',
                $daysOverdue <= 60 => '31_60',
                $daysOverdue <= 90 => '61_90',
                default            => '90_plus',
            };
            $e->aging_bucket = $bucketKey;
            $buckets[$bucketKey][] = $e;
        }

        // Sort each bucket worst-first (most overdue at the top)
        foreach ($buckets as $key => $rows) {
            usort($rows, fn ($a, $b) => $b->days_overdue <=> $a->days_overdue);
            $buckets[$key] = $rows;
        }

        $bucketTotals = [];
        foreach ($buckets as $key => $rows) {
            $bucketTotals[$key] = [
                'count'  => count($rows),
                'amount' => array_sum(array_map(fn ($e) => (float) $e->remaining_balance, $rows)),
            ];
        }
        $grandTotal = array_sum(array_column($bucketTotals, 'amount'));
        $grandCount = array_sum(array_column($bucketTotals, 'count'));

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Aging of Receivables',
            'dateRangeLabel' => 'School Year ' . $yearFilter . ' — As of ' . $today->format('M d, Y'),
            'buckets'        => $buckets,
            'bucketLabels'   => $bucketLabels,
            'bucketTotals'   => $bucketTotals,
            'grandTotal'     => $grandTotal,
            'grandCount'     => $grandCount,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.aging', $data), $data);

        return $pdf->download('Aging_of_Receivables_' . str_replace([' ', '-'], ['', '_'], $yearFilter) . '.pdf');
    }

    /**
     * Statement of Account — one student's complete financial record: every
     * payment transaction plus their full installment schedule, with
     * running totals. Different from the Official Receipt (one transaction)
     * or any of the other reports (whole-school summaries) — this is the
     * document parents/registrar actually ask Finance for by name.
     */
    public function downloadStatementOfAccountPdf(Enrollment $enrollment)
    {
        $enrollment->load(['user', 'paymentInstallments' => fn ($q) => $q->orderBy('due_date')]);

        $transactions = PaymentTransaction::where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['completed', 'approved'])
            ->orderBy('created_at')
            ->get();

        $totalFee = (float) ($enrollment->total_fee ?? 0);
        $totalPaid = (float) ($enrollment->payment_amount ?? 0);
        $balance = (float) ($enrollment->remaining_balance ?? max(0, $totalFee - $totalPaid));

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Statement of Account',
            'dateRangeLabel' => null,
            'enrollment'     => $enrollment,
            'transactions'   => $transactions,
            'totalFee'       => $totalFee,
            'totalPaid'      => $totalPaid,
            'balance'        => $balance,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.statement', $data), $data);

        $filename = 'Statement_of_Account_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $enrollment->user->name ?? $enrollment->id) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Collection by Grade Level — total fee / collected / outstanding per
     * grade level, for a school year. Surfaces patterns a flat list hides
     * (e.g. one grade level running far behind on collections).
     */
    public function downloadGradeLevelReportPdf(Request $request)
    {
        $yearFilter = $request->get('school_year', $this->formatSchoolYear());

        $gradeOrder  = ['nursery' => 0, 'kindergarten' => 1, 'grade1' => 2, 'grade2' => 3, 'grade3' => 4, 'grade4' => 5, 'grade5' => 6, 'grade6' => 7];
        $gradeLabels = ['nursery' => 'Nursery', 'kindergarten' => 'Kindergarten', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2', 'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];

        $enrollments = Enrollment::where('school_year', $yearFilter)
            ->whereIn('status', ['enrolled', 'approved'])
            ->get(['grade_level', 'total_fee', 'payment_amount', 'remaining_balance']);

        $rows = $enrollments
            ->groupBy(fn ($e) => $e->grade_level ?? 'unspecified')
            ->map(function ($group, $grade) use ($gradeLabels) {
                return (object) [
                    'grade_level'   => $grade,
                    'grade_label'   => $gradeLabels[$grade] ?? ucfirst($grade),
                    'student_count' => $group->count(),
                    'total_fee'     => $group->sum(fn ($e) => (float) ($e->total_fee ?? 0)),
                    'total_paid'    => $group->sum(fn ($e) => (float) ($e->payment_amount ?? 0)),
                    'total_balance' => $group->sum(fn ($e) => (float) ($e->remaining_balance ?? 0)),
                ];
            })
            ->sortBy(fn ($r) => $gradeOrder[$r->grade_level] ?? 99)
            ->values();

        $grand = (object) [
            'student_count' => $rows->sum('student_count'),
            'total_fee'     => $rows->sum('total_fee'),
            'total_paid'    => $rows->sum('total_paid'),
            'total_balance' => $rows->sum('total_balance'),
        ];

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Collection by Grade Level',
            'dateRangeLabel' => 'School Year ' . $yearFilter,
            'rows'           => $rows,
            'grand'          => $grand,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.grade-level', $data), $data);

        return $pdf->download('Collection_by_Grade_Level_' . str_replace([' ', '-'], ['', '_'], $yearFilter) . '.pdf');
    }

    /**
     * Payment Plan Distribution — how many students are on Plan A (cash)
     * vs B/C/D (installment), and how each plan is actually performing
     * collection-wise, for a school year. Same cash-vs-installment
     * derivation as students()/installments() (payment_option === 'A', or
     * payment_type === 'full' with no B/C/D option set) — kept identical
     * so this report can't disagree with what those pages already show.
     */
    public function downloadPaymentPlanReportPdf(Request $request)
    {
        $yearFilter = $request->get('school_year', $this->formatSchoolYear());

        $planLabels = ['A' => 'Plan A — Cash', 'B' => 'Plan B — Installment', 'C' => 'Plan C — Installment', 'D' => 'Plan D — Installment'];

        $enrollments = Enrollment::where('school_year', $yearFilter)
            ->whereIn('status', ['enrolled', 'approved'])
            ->get(['payment_option', 'payment_type', 'total_fee', 'payment_amount', 'remaining_balance']);

        $rows = $enrollments
            ->groupBy(function ($e) {
                $isCash = $e->payment_option === 'A' || ($e->payment_type === 'full' && !in_array($e->payment_option, ['B', 'C', 'D']));
                return $isCash ? 'A' : ($e->payment_option ?: 'B');
            })
            ->map(function ($group, $plan) use ($planLabels) {
                $totalFee     = $group->sum(fn ($e) => (float) ($e->total_fee ?? 0));
                $totalPaid    = $group->sum(fn ($e) => (float) ($e->payment_amount ?? 0));
                $totalBalance = $group->sum(fn ($e) => (float) ($e->remaining_balance ?? 0));
                return (object) [
                    'plan'            => $plan,
                    'plan_label'      => $planLabels[$plan] ?? ('Plan ' . $plan),
                    'student_count'   => $group->count(),
                    'total_fee'       => $totalFee,
                    'total_paid'      => $totalPaid,
                    'total_balance'   => $totalBalance,
                    // Derived from the same outstanding-balance figure the
                    // Outstanding column shows, not a separate paid/fee
                    // ratio — those two can disagree (e.g. one student's
                    // overpayment inflating total_paid without reducing
                    // another's real remaining_balance), which showed up
                    // here as a self-contradictory "100% collected, ₱243k
                    // still outstanding" row during testing.
                    'collection_rate' => $totalFee > 0 ? round(max(0, min(100, ($totalFee - $totalBalance) / $totalFee * 100)), 1) : 0,
                ];
            })
            ->sortBy('plan')
            ->values();

        $grand = (object) [
            'student_count' => $rows->sum('student_count'),
            'total_fee'     => $rows->sum('total_fee'),
            'total_paid'    => $rows->sum('total_paid'),
            'total_balance' => $rows->sum('total_balance'),
        ];

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Payment Plan Distribution',
            'dateRangeLabel' => 'School Year ' . $yearFilter,
            'rows'           => $rows,
            'grand'          => $grand,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.payment-plan', $data), $data);

        return $pdf->download('Payment_Plan_Distribution_' . str_replace([' ', '-'], ['', '_'], $yearFilter) . '.pdf');
    }

    /**
     * Enrollment & Payment Status Report — reconciles to EVERY student
     * account in the system, not just the ones with an active enrollment.
     * The other reports (Grade Level, Payment Plan, Accounts Receivable,
     * Aging) are all deliberately scoped to enrolled/approved students only
     * — a declined applicant isn't on a payment plan and owes the school
     * nothing, so including them there wouldn't make sense. But that
     * scoping can look like missing data if nothing ever says so out loud
     * ("214 students" when the school has 290 student accounts looks like
     * 76 went missing). This report is the one place that accounts for
     * all of them: Paid / Partial / Pending / Declined / No Application.
     */
    public function downloadStatusOverviewPdf(Request $request)
    {
        $yearFilter = $request->get('school_year', $this->formatSchoolYear());

        $totalStudentAccounts = \App\Models\User::where('role', 'student')->count();

        $enrollmentsThisYear = Enrollment::where('school_year', $yearFilter)->get();
        $enrolled  = $enrollmentsThisYear->whereIn('status', ['enrolled', 'approved']);
        $declined  = $enrollmentsThisYear->where('status', 'declined')
            // A student who re-applied after an earlier decline has both a
            // declined row and an enrolled row for the same year — count
            // them as enrolled, not declined, in that case.
            ->whereNotIn('user_id', $enrolled->pluck('user_id'));

        $appliedUserIds = $enrollmentsThisYear->pluck('user_id')->unique();
        $noApplicationCount = max(0, $totalStudentAccounts - $appliedUserIds->count());

        $statusRows = collect([
            (object) [
                'label'         => 'Paid',
                'count'         => $enrolled->where('payment_status', 'paid')->count(),
                'total_fee'     => $enrolled->where('payment_status', 'paid')->sum(fn ($e) => (float) ($e->total_fee ?? 0)),
                'total_paid'    => $enrolled->where('payment_status', 'paid')->sum(fn ($e) => (float) ($e->payment_amount ?? 0)),
                'total_balance' => $enrolled->where('payment_status', 'paid')->sum(fn ($e) => (float) ($e->remaining_balance ?? 0)),
            ],
            (object) [
                'label'         => 'Partial',
                'count'         => $enrolled->where('payment_status', 'partial')->count(),
                'total_fee'     => $enrolled->where('payment_status', 'partial')->sum(fn ($e) => (float) ($e->total_fee ?? 0)),
                'total_paid'    => $enrolled->where('payment_status', 'partial')->sum(fn ($e) => (float) ($e->payment_amount ?? 0)),
                'total_balance' => $enrolled->where('payment_status', 'partial')->sum(fn ($e) => (float) ($e->remaining_balance ?? 0)),
            ],
            (object) [
                'label'         => 'Not Paid (Pending)',
                'count'         => $enrolled->whereNotIn('payment_status', ['paid', 'partial'])->count(),
                'total_fee'     => $enrolled->whereNotIn('payment_status', ['paid', 'partial'])->sum(fn ($e) => (float) ($e->total_fee ?? 0)),
                'total_paid'    => $enrolled->whereNotIn('payment_status', ['paid', 'partial'])->sum(fn ($e) => (float) ($e->payment_amount ?? 0)),
                'total_balance' => $enrolled->whereNotIn('payment_status', ['paid', 'partial'])->sum(fn ($e) => (float) ($e->remaining_balance ?? 0)),
            ],
            (object) [
                'label'         => 'Declined',
                'count'         => $declined->count(),
                'total_fee'     => null,
                'total_paid'    => null,
                'total_balance' => null,
            ],
            (object) [
                'label'         => 'No Application Submitted',
                'count'         => $noApplicationCount,
                'total_fee'     => null,
                'total_paid'    => null,
                'total_balance' => null,
            ],
        ]);

        // Within enrolled students only — cash (Plan A) vs installment
        // (B/C/D), same derivation as Payment Plan Distribution so the two
        // reports can't disagree.
        $cashCount = $enrolled->filter(fn ($e) => $e->payment_option === 'A' || ($e->payment_type === 'full' && !in_array($e->payment_option, ['B', 'C', 'D'])))->count();
        $installmentCount = $enrolled->count() - $cashCount;

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'          => 'Enrollment & Payment Status Report',
            'dateRangeLabel'       => 'School Year ' . $yearFilter,
            'totalStudentAccounts' => $totalStudentAccounts,
            'enrolledCount'        => $enrolled->count(),
            'statusRows'           => $statusRows,
            'cashCount'            => $cashCount,
            'installmentCount'     => $installmentCount,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.status-overview', $data), $data);

        return $pdf->download('Enrollment_Payment_Status_' . str_replace([' ', '-'], ['', '_'], $yearFilter) . '.pdf');
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
        $sort       = $request->input('sort', 'newest');

        $query = \App\Models\User::where('role', 'student')
            ->whereNull('deleted_at')
            ->whereHas('enrollments')
            ->with(['enrollments' => function ($q) {
                $q->latest()->limit(1);
            }]);

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

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

        // Paginate the already-filtered collection manually — the plan/status
        // filters above operate on derived fields (is_cash, computed from
        // payment_option/payment_type) rather than plain columns, so this
        // stays a PHP-side filter+paginate rather than risking a subtly
        // different SQL translation of that logic. $stats above is computed
        // from the full filtered set, before slicing to the current page.
        $page = (int) $request->get('page', 1);
        $perPage = 15;
        $allStudents = new \Illuminate\Pagination\LengthAwarePaginator(
            $allStudents->forPage($page, $perPage)->values(),
            $allStudents->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $schoolYears = $this->getSchoolYears();

        return view('finance.students', compact('allStudents', 'stats', 'search', 'planFilter', 'statusFilter', 'yearFilter', 'schoolYears', 'sort'));
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

        // Final payable total per (grade, option) — what a student on that
        // plan actually gets charged, not just the pre-discount base from
        // $feeBreakdowns above. Goes through the same FeeCalculator every
        // real enrollment uses, so this can never drift from what's
        // actually charged. Option C is Grade 1-6 only, Option D is
        // Nursery/Kinder only — those cells are simply omitted per grade
        // rather than showing a misleading ₱0.
        $optionsByGrade = [
            'nursery'      => ['A', 'B', 'D'],
            'kindergarten' => ['A', 'B', 'D'],
            'grade1'       => ['A', 'B', 'C'],
            'grade2'       => ['A', 'B', 'C'],
            'grade3'       => ['A', 'B', 'C'],
            'grade4'       => ['A', 'B', 'C'],
            'grade5'       => ['A', 'B', 'C'],
            'grade6'       => ['A', 'B', 'C'],
        ];
        $optionTotals = [];
        foreach ($gradeLevels as $grade) {
            foreach ($optionsByGrade[$grade] as $option) {
                $optionTotals[$grade][$option] = \App\Services\FeeCalculator::calculate($grade, $option);
            }
        }

        return view('finance.fees', compact('feeSettings', 'feeBreakdowns', 'optionTotals', 'optionsByGrade'));
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
                $feeSettings = FeeSetting::create($validated);
            }

            // Dual-write (DATABASE_NORMALIZATION_PLAN.md Phase 2): keep
            // fee_components in sync so FeeCalculator — which every real fee
            // calculation now reads from — picks up this change immediately,
            // not just the legacy fee_settings columns. Shared with
            // FeeSettingController::update() (the Admin-dashboard equivalent
            // of this same "Fee Settings" feature) so both can never drift
            // out of sync with each other again.
            \App\Services\FeeCalculator::syncComponents($feeSettings->fresh()->toArray());
            \App\Services\FeeCalculator::forgetCache();

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

        // For the Accounts Receivable Report section on this same page —
        // moved here from the Installments page so every downloadable
        // Finance report lives in one place.
        $schoolYears = $this->getSchoolYears();
        $currentSchoolYear = $this->formatSchoolYear();

        return view('finance.reports', compact('reportData', 'reportType', 'dateFrom', 'dateTo', 'schoolYears', 'currentSchoolYear'));
    }

    /**
     * Daily/Weekly/Monthly/Yearly Report, as a downloadable PDF instead of
     * the browser's own print-to-PDF (window.print()) — same filters as the
     * on-screen Reports page, reusing the same generateReport() so the two
     * can never show different numbers for the same query.
     */
    public function downloadReportPdf(Request $request)
    {
        $reportType = $request->get('type', 'daily');
        $dateFrom   = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo     = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $reportData = $this->generateReport($reportType, $dateFrom, $dateTo);

        $reportTypeLabels = ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];
        $reportTypeLabel  = $reportTypeLabels[$reportType] ?? 'Daily';

        $totalAmount = collect($reportData['daily_breakdown'])->sum('total_amount');

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => $reportTypeLabel . ' Financial Report',
            'dateRangeLabel' => Carbon::parse($dateFrom)->format('M d, Y') . ' — ' . Carbon::parse($dateTo)->format('M d, Y'),
            'reportData'     => $reportData,
            'reportTypeLabel'=> $reportTypeLabel,
            'totalAmount'    => $totalAmount,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.report-breakdown', $data), $data);

        $filename = 'Finance_Report_' . $reportType . '_' . $dateFrom . '_to_' . $dateTo . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Collection Summary — grand totals only (no per-period breakdown,
     * that's what the Daily/Weekly/Monthly/Yearly Report above already
     * covers), grouped by payment type and payment method instead. A
     * different cut of the same underlying data, not a duplicate report.
     */
    public function downloadCollectionSummaryPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $base = PaymentTransaction::whereIn('status', ['completed', 'approved'])
            ->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);

        $byType = (clone $base)
            ->selectRaw('payment_type, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('payment_type')
            ->get();

        $byMethod = (clone $base)
            ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('payment_method')
            ->get();

        $grandTotal = (clone $base)->sum('amount');
        $grandCount = (clone $base)->count();

        $data = array_merge($this->financePdfLetterhead(), [
            'reportTitle'    => 'Collection Summary',
            'dateRangeLabel' => Carbon::parse($dateFrom)->format('M d, Y') . ' — ' . Carbon::parse($dateTo)->format('M d, Y'),
            'byType'         => $byType,
            'byMethod'       => $byMethod,
            'grandTotal'     => $grandTotal,
            'grandCount'     => $grandCount,
        ]);

        $pdf = $this->financePdfOptions(\Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.collection-summary', $data), $data);

        return $pdf->download('Collection_Summary_' . $dateFrom . '_to_' . $dateTo . '.pdf');
    }

    /**
     * Audit Trail — deliberately scoped to the signed-in finance staff's own
     * actions, not every finance/admin's. It's their personal accountability
     * record (proof of what they approved/rejected/processed and when), not
     * a system-wide log — that stays a Super Admin-only view.
     */
    public function auditTrail(Request $request)
    {
        $userId = Auth::guard('finance')->id();

        $logs = \App\Models\ActivityLog::where('user_id', $userId)
            ->latest('created_at')
            ->paginate(25);

        return view('finance.audit-trail', compact('logs'));
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

        // Cheap pre-check — not authoritative on its own (see the locked
        // re-check below), just avoids opening a transaction for the common
        // case of an obviously-already-processed payment.
        if ($payment->status === 'completed') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This payment has already been processed.'], 409);
            }
            return redirect()->back()->with('error', 'This payment has already been processed.');
        }

        DB::beginTransaction();
        try {
            // Idempotency guard — re-checked here, under a row lock, not just
            // above. A double-click, a network retry, or two staff members
            // approving the same transaction within seconds can both pass the
            // pre-check above before either commits; without re-verifying
            // status after acquiring the lock, both would credit the
            // enrollment's payment amount below, double-crediting it. Mirrors
            // the already-correct pattern in CashierController::checkXenditStatus()
            // (lockForUpdate() on the PaymentTransaction, then re-check) /
            // PaymentService::completeXenditPayment()'s own guard.
            $payment = PaymentTransaction::where('id', $payment->id)->lockForUpdate()->first();
            if (!$payment || $payment->status === 'completed') {
                DB::rollBack();
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'This payment has already been processed.'], 409);
                }
                return redirect()->back()->with('error', 'This payment has already been processed.');
            }

            $payment->update([
                'status' => 'completed',
                'processed_by' => $this->actingStaffId(),
                'processed_at' => now(),
            ]);

            // If enrollment exists, update payment amounts
            if ($payment->enrollment_id) {
                // Row-locked re-fetch — see PaymentService::lockEnrollment()
                // — this method had no transaction or lock at all, so two
                // approvals landing at nearly the same time could each
                // compute from the same stale payment_amount.
                $enrollment = PaymentService::lockEnrollment($payment->enrollment_id);

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

                        // Must cover the full amount + late fee before the
                        // installment can be marked paid — otherwise an
                        // under-amount payment silently clears the whole
                        // installment and waives the late fee. Same
                        // hard-reject rule as PaymentService::processInstallmentPayment()
                        // and the walk-in/admin-payment paths. Staff should
                        // reject this payment instead if the amount is wrong.
                        if ($amountPaid < $totalDue) {
                            DB::rollBack();
                            $msg = 'This payment (₱' . number_format($amountPaid, 2) . ') does not cover the installment due (₱' . number_format($totalDue, 2) . ' incl. late fee). Reject it instead if the amount is incorrect.';
                            if ($request->expectsJson() || $request->ajax()) {
                                return response()->json(['success' => false, 'message' => $msg], 422);
                            }
                            return redirect()->back()->with('error', $msg);
                        }

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
            if ($payment->enrollment_id && isset($enrollment)) {
                $this->updateEnrollmentPaymentStatus($enrollment);
            }

            ActivityLogger::log(
                'payment_approved',
                'Approved payment #' . $payment->id . ' (₱' . number_format($payment->amount, 2) . ')',
                'PaymentTransaction',
                $payment->id
            );

            DB::commit();

            // Return JSON for AJAX requests, otherwise redirect
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment approved successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
                'reviewed_by' => $this->actingStaffId(),
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
                    'processed_by' => $this->actingStaffId(),
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

            ActivityLogger::log(
                'payment_approved',
                'Approved payment screenshot for ' . ($enrollment->student_data['first_name'] ?? '') . ' ' . ($enrollment->student_data['last_name'] ?? '') . ' (₱' . number_format($amountPaid ?? 0, 2) . ')',
                'StudentDocument',
                $document->id
            );

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
            // Same reasoning as processAdminPayment(): GCash always has a
            // real reference number, cash doesn't.
            'reference_number' => 'required_if:payment_method,gcash|nullable|string|max:100',
        ]);

        $methodLabel = $request->payment_method === 'gcash' ? 'GCash' : 'Cash';
        $amountPaid  = floatval($request->amount);

        DB::beginTransaction();
        try {
            // Row-locked re-fetch — see PaymentService::lockEnrollment() —
            // this method previously had no transaction at all, so two
            // walk-in payments for the same enrollment could both compute
            // from the same stale payment_amount.
            $enrollment = PaymentService::lockEnrollment($enrollment->id);

            // Double-click / retried-request guard — a cash walk-in payment
            // is recorded as already 'completed' immediately (unlike the
            // Xendit-link flow, there's no 'pending' state to reuse), so a
            // double-click here creates two independent, fully-credited
            // transactions for the same handover of cash. Treat an
            // identical amount+method for this enrollment in the last 15
            // seconds as the same click landing twice, not two payments.
            $recentDuplicate = PaymentTransaction::where('enrollment_id', $enrollment->id)
                ->where('payment_method', $request->payment_method)
                ->where('amount', $amountPaid)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->exists();
            if ($recentDuplicate) {
                DB::rollBack();
                return $this->walkInError($request, 'A matching payment was just recorded for this student — please check Payment History before submitting again.');
            }

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
                    DB::rollBack();
                    return $this->walkInError($request, 'Invalid installment record.');
                }

                if ($installment->status === 'paid') {
                    DB::rollBack();
                    return $this->walkInError($request, 'This installment is already paid.');
                }

                if ($installment->status === 'pending_approval') {
                    DB::rollBack();
                    return $this->walkInError($request, 'This installment has a student-submitted payment awaiting approval. Approve or reject it first.');
                }

                // Must cover the full amount + late fee before the
                // installment can be marked paid — otherwise a typo'd
                // partial amount (e.g. ₱200 entered for a ₱1,556 due
                // installment) silently clears the whole installment,
                // waiving the late fee and dropping it out of the overdue
                // count the Exam Permit Hold relies on. Same hard-reject
                // rule already enforced for the student-facing payment path
                // in PaymentService::processInstallmentPayment().
                $totalDue = floatval($installment->amount) + floatval($installment->late_fee ?? 0);
                if ($amountPaid < $totalDue) {
                    DB::rollBack();
                    return $this->walkInError($request, 'Amount must be at least ₱' . number_format($totalDue, 2) . ' (includes ₱' . number_format($installment->late_fee ?? 0, 2) . ' late fee) to mark this installment paid.');
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
                'processed_by'  => $this->actingStaffId(),
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->walkInError($request, 'Payment recording failed: ' . $e->getMessage());
        }

        $monthDisplay = $installmentMonth ?? ($isDownpayment ? 'Downpayment' : 'Payment');

        ActivityLogger::log(
            'walkin_payment',
            'Recorded walk-in ' . $methodLabel . ' payment of ₱' . number_format($amountPaid, 2) . ' for ' . $monthDisplay,
            'PaymentTransaction',
            $payment->id
        );

        $successMessage = 'Walk-in payment recorded for ' . $monthDisplay . ' — ₱' . number_format($amountPaid, 2) . ' via ' . $methodLabel . '.';

        // AJAX callers (the Installments and All Students "Pay" modals) get a
        // JSON response so the page can refresh its own numbers in place —
        // same immediate-feedback behavior as the Cashier portal's cash
        // payment flow, instead of redirecting away to the Payments page
        // (which is what both callers used to silently fall through to,
        // landing the staff member somewhere they didn't ask to go while the
        // page they were actually looking at stayed showing stale numbers).
        if ($request->expectsJson() || $request->ajax()) {
            $enrollment->refresh();
            return response()->json([
                'success'            => true,
                'message'            => $successMessage,
                'enrollment_id'      => $enrollment->id,
                'payment_amount'     => $enrollment->payment_amount,
                'remaining_balance'  => $enrollment->remaining_balance,
                'payment_status'     => $enrollment->payment_status,
                'reference_number'   => $payment->reference_number,
            ]);
        }

        return redirect()->route('finance.payments.index')->with('success', $successMessage . ' Payment is listed below.');
    }

    /**
     * Shared error responder for recordWalkInPayment() — JSON for the AJAX
     * callers (Installments/All Students "Pay" modals), redirect-back
     * fallback otherwise.
     */
    private function walkInError(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return redirect()->back()->with('error', $message);
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
            // GCash is an electronic transfer that always has a real
            // reference number — require it as a minimal evidence trail;
            // cash has no such concept, so it stays optional there.
            'payment_reference' => 'required_if:payment_method,gcash|nullable|string|max:255',
            'payment_option' => 'nullable|in:A,B,C,D',
            'installment_id' => 'nullable|integer|exists:payment_installments,id',
        ]);

        DB::beginTransaction();
        try {
            // Row-locked re-fetch — see PaymentService::lockEnrollment() —
            // so a concurrent payment for this same enrollment can't read
            // the same stale payment_amount this method is about to compute
            // a new absolute total from.
            $enrollment = PaymentService::lockEnrollment($enrollment->id);

            $methodLabel = $request->payment_method === 'gcash' ? 'GCash' : 'Cash';
            $amountPaid = (float) $request->payment_amount;

            // Double-click / retried-request guard — same reasoning as
            // recordWalkInPayment()'s matching check: this payment is
            // recorded as already 'completed' immediately, so a double
            // submit creates two independent, fully-credited transactions
            // for what was really one handover of cash/GCash.
            $recentDuplicate = PaymentTransaction::where('enrollment_id', $enrollment->id)
                ->where('payment_method', $request->payment_method)
                ->where('amount', $amountPaid)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->exists();
            if ($recentDuplicate) {
                DB::rollBack();
                return redirect()->back()->with('error', 'A matching payment was just recorded for this student — please check Payment History before submitting again.');
            }

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
                'processed_by'  => $this->actingStaffId(),
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
                $resolvedInstallmentId = null;

                // If specific installment ID provided, process that installment
                if ($request->installment_id) {
                    $installment = PaymentInstallment::find($request->installment_id);
                    if ($installment && $installment->enrollment_id === $enrollment->id) {
                        // Must cover the full amount + late fee before the
                        // installment can be marked paid — otherwise a typo'd
                        // partial amount silently clears the whole
                        // installment and waives the late fee. Same
                        // hard-reject rule as PaymentService::processInstallmentPayment()
                        // and the walk-in payment path above.
                        $totalDue = floatval($installment->amount) + floatval($installment->late_fee ?? 0);
                        if ($amountPaid < $totalDue) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Amount must be at least ₱' . number_format($totalDue, 2) . ' (includes ₱' . number_format($installment->late_fee ?? 0, 2) . ' late fee) to mark this installment paid.');
                        }
                        $installmentMonth = $installment->month_name ?? '';
                        $resolvedInstallmentId = $installment->id;
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
                        $totalDue = floatval($nextInstallment->amount) + floatval($nextInstallment->late_fee ?? 0);
                        if ($amountPaid < $totalDue) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Amount must be at least ₱' . number_format($totalDue, 2) . ' (includes ₱' . number_format($nextInstallment->late_fee ?? 0, 2) . ' late fee) to mark the next installment paid.');
                        }
                        $installmentMonth = $nextInstallment->month_name ?? '';
                        $resolvedInstallmentId = $nextInstallment->id;
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

                // Update payment description with installment month. installment_id is
                // stored alongside it (DATABASE_NORMALIZATION_PLAN.md Phase 7) so this
                // transaction stays linkable to its installment via the real relation —
                // installment_month itself stays, since it also has to carry the
                // "Downpayment" case below, which has no installment row to link to.
                if ($installmentMonth) {
                    $payment->update([
                        'description' => 'Admin payment via ' . $methodLabel . ' - ' . $installmentMonth . ' - ₱' . number_format($amountPaid, 2) . ($request->payment_reference ? ' (Ref: ' . $request->payment_reference . ')' : ''),
                        'installment_month' => $installmentMonth,
                        'installment_id' => $resolvedInstallmentId,
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

            ActivityLogger::log(
                'payment_processed',
                'Processed ' . $methodLabel . ' payment of ₱' . number_format($amountPaid, 2) . ' for enrollment #' . $enrollment->id,
                'PaymentTransaction',
                $payment->id
            );

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
            'processed_by' => $this->actingStaffId(),
            'processed_at' => now(),
        ]);

        // If there's an installment linked, set it back to pending
        if ($payment->installment) {
            $payment->installment->update([
                'status' => 'pending',
                'payment_transaction_id' => null,
            ]);
        }

        ActivityLogger::log(
            'payment_rejected',
            'Rejected payment #' . $payment->id . ' — ' . $validated['reject_reason'],
            'PaymentTransaction',
            $payment->id
        );

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
            'reviewed_by' => $this->actingStaffId(),
            'reviewed_at' => now(),
        ]);

        ActivityLogger::log(
            'payment_rejected',
            'Rejected payment screenshot #' . $document->id . ' — ' . $validated['reject_reason'],
            'StudentDocument',
            $document->id
        );

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
    /**
     * Delete a payment — accepts a raw ID rather than route-model-binding to
     * PaymentTransaction. Same ID-collision caution as approvePayment()/
     * rejectPayment(): the admin dashboard's pending-payments list mixes
     * StudentDocument (payment_screenshot) rows and PaymentTransaction rows,
     * and their IDs can collide numerically, so this must check both.
     */
    public function deletePayment($id)
    {
        $document = \App\Models\StudentDocument::where('id', $id)
            ->where('document_type', 'payment_screenshot')
            ->first();

        if ($document) {
            // A payment screenshot is an uploaded proof-of-payment, not a
            // recorded transaction — "delete" just discards the pending upload.
            if ($document->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending payment screenshots can be deleted.');
            }
            $document->delete();
            return redirect()->back()->with('success', 'Payment screenshot deleted successfully.');
        }

        $payment = PaymentTransaction::find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }

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
     * View payment details — same dual-lookup as deletePayment()/approvePayment().
     * Normalizes the differing "who reviewed it" field names (StudentDocument
     * uses reviewedBy/reviewed_at, PaymentTransaction uses processedBy/
     * processed_at) into two plain variables so the view doesn't need to know
     * which model it got.
     */
    public function paymentDetails($id)
    {
        // This view is reachable from two different logged-in contexts —
        // the Finance portal's own guard, and Admin/Super Admin viewing it
        // via /admin/payments/{id} (the 'admin' middleware, 'web' guard).
        // The sidebar/breadcrumb need to know which one so they don't link
        // to finance.* routes an admin session can never satisfy.
        $viewerContext = Auth::guard('finance')->check() ? 'finance' : 'admin';

        $document = \App\Models\StudentDocument::where('id', $id)
            ->where('document_type', 'payment_screenshot')
            ->first();

        if ($document) {
            $document->load(['enrollment', 'user', 'reviewedBy']);
            $reviewerName = $document->reviewedBy->name ?? 'System';
            $reviewedAt   = $document->reviewed_at;
            return view('finance.payment-details', compact('document', 'reviewerName', 'reviewedAt', 'viewerContext'));
        }

        $payment = PaymentTransaction::find($id);
        if (!$payment) {
            abort(404, 'Payment not found.');
        }
        $payment->load(['enrollment', 'user', 'processedBy']);
        $document     = $payment;
        $reviewerName = $payment->processedBy->name ?? 'System';
        $reviewedAt   = $payment->processed_at;

        return view('finance.payment-details', compact('document', 'reviewerName', 'reviewedAt', 'viewerContext'));
    }

    /**
     * Helper: current school year. Delegates to
     * Setting::getCurrentSchoolYear() — the single source of truth — so
     * editing "Current School Year" in Admin > Settings actually takes
     * effect here, which it didn't before (this used to derive the year
     * from Section data only, ignoring the setting entirely, and was a
     * near-exact duplicate of EnrollmentController's own copy of the same
     * logic). See docs/system-improvement-plan.md.
     */
    private function formatSchoolYear()
    {
        return \App\Models\Setting::getCurrentSchoolYear();
    }

    /**
     * Helper: Get list of school years for filters
     */
    private function getSchoolYears(): array
    {
        // Used to only look forward (current year to +10) — there was no
        // way to filter into a past school year's finance records even
        // though the data exists. See Setting::schoolYearOptions().
        return \App\Models\Setting::schoolYearOptions();
    }

    /**
     * Helper: Calculate fee breakdown (display only, Fee Management page).
     * Backed by the shared FeeCalculator (DATABASE_NORMALIZATION_PLAN.md
     * Phase 2) so this page always shows the same numbers every other
     * fee-quoting path does. $feeSettings param kept for call-site
     * compatibility but no longer read directly.
     */
    private function calculateFeeBreakdown($gradeLevel, $feeSettings)
    {
        return [
            'tuition' => \App\Services\FeeCalculator::base('tuition'),
            'misc' => \App\Services\FeeCalculator::base('misc'),
            'insurance' => \App\Services\FeeCalculator::base('insurance'),
            'electric' => \App\Services\FeeCalculator::base('electric'),
            'books' => \App\Services\FeeCalculator::books($gradeLevel),
            'base_total' => \App\Services\FeeCalculator::baseTotal($gradeLevel),
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
            // do NOT downgrade a 'paid' status that was correctly set when remaining_balance hit 0.
            // But DO clamp a negative value back to 0 — a payment recorded before the fee plan was
            // finalized decrements remaining_balance blindly and can drive it negative otherwise.
            $remainingBal = round(floatval($enrollment->remaining_balance ?? 0), 2);
            $enrollment->update([
                'payment_status'    => $remainingBal <= 0 ? 'paid' : 'partial',
                'remaining_balance' => max(0, $remainingBal),
            ]);
        }

        // If enrollment is 'approved' or 'pending' and the payment meets the
        // required minimum (downpayment for installment plans, full fee for
        // a one-shot plan — see PaymentService::requiredEnrollmentThreshold()),
        // mark as 'enrolled'. Previously any nonzero payment sufficed here.
        $threshold = PaymentService::requiredEnrollmentThreshold($enrollment);
        $meetsThreshold = $threshold > 0 ? $totalPaid >= $threshold : ($totalPaid > 0 || $hasApprovedPayments);
        if (in_array($enrollment->status, ['approved', 'pending', 'completed']) && $meetsThreshold) {
            $enrollment->update([
                'status'      => 'enrolled',
                'enrolled_at' => now(),
            ]);

            // Auto-assign student to section based on grade level (capacity-aware
            // — see PaymentService::assignSectionForEnrollment()).
            PaymentService::assignSectionForEnrollment($enrollment);
        }
    }

    /**
     * Helper: Generate report data
     */
    private function generateReport($type, $dateFrom, $dateTo)
    {
        $query = PaymentTransaction::whereIn('status', ['completed', 'approved'])
            ->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);

        // The report "type" (daily/weekly/monthly/yearly) used to be accepted
        // by the dropdown but never actually used here — every option
        // produced the exact same day-by-day breakdown. Group by the actual
        // requested period instead.
        [$dateExpr, $periodFormat] = match ($type) {
            'weekly'  => ["YEARWEEK(updated_at, 3)", 'W'],   // ISO week
            'monthly' => ["DATE_FORMAT(updated_at, '%Y-%m')", 'M'],
            'yearly'  => ["YEAR(updated_at)", 'Y'],
            default   => ["DATE(updated_at)", 'D'],          // daily
        };

        $breakdown = (clone $query)
            ->select(
                DB::raw($dateExpr . ' as period_key'),
                DB::raw('MIN(updated_at) as period_start'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(amount), 0) as total_amount')
            )
            ->groupBy('period_key')
            ->orderBy('period_key', 'desc')
            ->get()
            ->map(function ($row) use ($periodFormat) {
                $date = \Carbon\Carbon::parse($row->period_start);
                $row->period_label = match ($periodFormat) {
                    'W' => 'Week of ' . $date->startOfWeek()->format('M d, Y'),
                    'M' => $date->format('F Y'),
                    'Y' => $date->format('Y'),
                    default => $date->format('F d, Y'),
                };
                return $row;
            });

        return [
            'report_type' => $type,
            'total_payments' => $query->count(),
            'payments_by_method' => [
                'gcash' => (clone $query)->where('payment_method', 'gcash')->count(),
                'cash' => (clone $query)->where('payment_method', 'cash')->count(),
            ],
            'daily_breakdown' => $breakdown,
        ];
    }

    /**
     * Securely serve a student document file.
     *
     * Was previously not actually secure at all — the route only checked that
     * *someone* was logged in (default 'web' guard), never that they owned
     * this document or had a staff reason to see it. Any authenticated
     * student could view any other student's uploaded documents (birth
     * certificates, report cards, etc.) just by changing the numeric id in
     * the URL. Fixed to require the document's own owner or staff.
     */
    public function viewDocument(StudentDocument $document)
    {
        $user = Auth::user();

        $isOwner = $user && $document->user_id === $user->id;
        $isStaff = $user && in_array($user->role, ['admin', 'superadmin', 'finance', 'cashier']);

        if (!$isOwner && !$isStaff) {
            abort(403, 'You are not authorized to view this document.');
        }

        if (!$document->file_path) {
            abort(404, 'File not found.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $path = Storage::disk('local')->path($document->file_path);
        return response()->file($path);
    }
}
