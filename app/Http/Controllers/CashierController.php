<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class CashierController extends Controller
{
    use \App\Support\Traits\BuildsSchoolPdf;

    /**
     * Cashier-specific wrapper around the shared letterhead builder —
     * mirrors Finance\DashboardController::financePdfLetterhead(), just
     * using the cashier auth guard instead of finance/web.
     */
    private function cashierPdfLetterhead(): array
    {
        $user = Auth::guard('cashier')->user();

        return $this->schoolPdfLetterhead($user->name ?? 'Cashier');
    }

    // ── Auth ──────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('cashier')->check()) {
            return redirect()->route('cashier.dashboard');
        }
        return view('cashier.login');
    }

    public function login(Request $request)
    {
        // Verify reCAPTCHA if enabled
        if (\App\Services\RecaptchaService::isEnabled()) {
            $result = \App\Services\RecaptchaService::verify($request->input('g-recaptcha-response'));
            if (!$result['success']) {
                return back()->withErrors(['email' => $result['error'] ?? 'Please verify that you are not a robot.'])
                             ->withInput($request->except('password'));
            }
        }

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Limits come from Settings > Security. Keyed by email only — an
        // IP-inclusive key would let an attacker rotate source IP for a
        // fresh attempt budget every time.
        $lockKey   = 'cashier_login_' . md5($request->email);
        $unlockKey = 'cashier_unlock_' . md5($request->email);
        $attempts  = cache()->get($lockKey, 0);

        if ($attempts >= \App\Support\SecurityPolicy::maxLoginAttempts()) {
            $unlockAt = cache()->get($unlockKey);
            $mins = ($unlockAt && $unlockAt > now())
                ? max(1, (int) ceil(now()->diffInSeconds($unlockAt) / 60))
                : \App\Support\SecurityPolicy::lockoutMinutes();
            return back()->withErrors(['email' => "Too many failed attempts. Try again in {$mins} minute(s)."])
                         ->withInput($request->except('password'));
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !in_array($user->role, ['cashier', 'admin', 'superadmin'])) {
            $this->incrementAttempts($lockKey, $unlockKey, $attempts);
            return back()->withErrors(['email' => 'No cashier account found with these credentials.'])
                         ->withInput($request->except('password'));
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated. Contact the administrator.'])
                         ->withInput($request->except('password'));
        }

        if (Auth::guard('cashier')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            cache()->forget($lockKey);
            cache()->forget($unlockKey);
            Log::info('Cashier login', ['user_id' => $user->id, 'ip' => $request->ip()]);
            ActivityLogger::log('login', $user->name . ' logged in', 'User', $user->id);
            // Only go back to a page inside this portal: the remembered "intended" URL is shared
            // by every portal, so an Admin page opened while logged out used to land the cashier there
            $intended = $request->session()->pull('url.intended');
            return redirect(is_string($intended) && str_starts_with($intended, url('/cashier/')) ? $intended : route('cashier.dashboard'));
        }

        $this->incrementAttempts($lockKey, $unlockKey, $attempts);
        return back()->withErrors(['email' => 'Incorrect email or password.'])
                     ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('cashier')->user();
        if ($user) {
            ActivityLogger::log('logout', $user->name . ' logged out', 'User', $user->id);
        }
        Auth::guard('cashier')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($user) {
            Log::info('Cashier logout', ['user_id' => $user->id]);
        }
        return redirect()->route('cashier.login');
    }

    private function incrementAttempts(string $lockKey, string $unlockKey, int $current): void
    {
        $new = $current + 1;
        $ttl = now()->addMinutes(\App\Support\SecurityPolicy::lockoutMinutes());
        cache()->put($lockKey, $new, $ttl);
        if ($new >= \App\Support\SecurityPolicy::maxLoginAttempts()) {
            cache()->put($unlockKey, $ttl, $ttl);
        }
    }

    // ── Change Password ───────────────────────────────

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = Auth::guard('cashier')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        ActivityLogger::log('password_change', $user->name . ' changed their password', 'User', $user->id);

        return back()->with('password_success', 'Password changed successfully!');
    }

    // ── Update Profile (name) ─────────────────────────

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
        ]);

        $user    = Auth::guard('cashier')->user();
        $oldName = $user->name;
        $user->update(['name' => $validated['name']]);

        if ($oldName !== $user->name) {
            ActivityLogger::log('update', "{$oldName} changed their name to {$user->name}", 'User', $user->id);
        }

        return back()->with('profile_success', 'Profile updated successfully!');
    }

    // ── Dashboard ─────────────────────────────────────

    public function dashboard()
    {
        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        $todayTotal = PaymentTransaction::whereBetween('processed_at', [$todayStart, $todayEnd])
            ->where('status', 'completed')->sum('amount');

        $todayCount = PaymentTransaction::whereBetween('processed_at', [$todayStart, $todayEnd])
            ->where('status', 'completed')->count();

        $pendingCount = PaymentTransaction::where('status', 'pending')->count();

        $recentTransactions = PaymentTransaction::with(['user', 'enrollment'])
            ->where('status', 'completed')
            ->latest('processed_at')
            ->limit(50)
            ->get();

        $todayTransactions = PaymentTransaction::with(['user', 'enrollment'])
            ->where('status', 'completed')
            ->whereBetween('processed_at', [$todayStart, $todayEnd])
            ->latest('processed_at')
            ->get();

        $todayCash   = (float) $todayTransactions->where('payment_method', 'cash')->sum('amount');
        $todayOnline = (float) $todayTransactions->whereNotIn('payment_method', ['cash'])->sum('amount');

        // Last 7 days for the bar chart — one grouped query instead of one
        // sum() per day (was 7 queries, now 1).
        $dailyTotals = PaymentTransaction::where('status', 'completed')
            ->where('processed_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(processed_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $csChDays = []; $csChTotals = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $csChDays[]   = $d->format('D, M j');
            $csChTotals[] = (float) ($dailyTotals[$d->toDateString()] ?? 0);
        }

        return view('cashier.dashboard', compact(
            'todayTotal', 'todayCount', 'pendingCount',
            'recentTransactions', 'todayTransactions',
            'todayCash', 'todayOnline',
            'csChDays', 'csChTotals'
        ));
    }

    // ── Collection Summary (on-demand, Collection tab only) ──────────
    // Was previously computed unconditionally in the dashboard blade view
    // on every dashboard load (9 queries) even when this tab was never
    // opened — moved here as its own on-demand endpoint, and collapsed
    // from 9 queries down to 2.
    public function collectionSummary()
    {
        $monthlyTotals = PaymentTransaction::where('status', 'completed')
            ->where('processed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(processed_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $months = []; $totals = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = $m->format('M Y');
            $totals[] = (float) ($monthlyTotals[$m->format('Y-m')] ?? 0);
        }

        $methodTotals = PaymentTransaction::where('status', 'completed')->selectRaw("
                SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
                SUM(CASE WHEN payment_method = 'gcash' THEN amount ELSE 0 END) as gcash,
                SUM(CASE WHEN payment_method NOT IN ('cash', 'gcash') THEN amount ELSE 0 END) as other
            ")->first();

        return response()->json([
            'months' => $months,
            'totals' => $totals,
            'cash'   => (float) $methodTotals->cash,
            'gcash'  => (float) $methodTotals->gcash,
            'other'  => (float) $methodTotals->other,
        ]);
    }

    /**
     * PDF version of collectionSummary() above — same two queries, same
     * numbers, just tabulated for print instead of feeding the on-screen
     * Chart.js canvases. Same shared letterhead/running-header-footer
     * design as every Finance report.
     */
    public function downloadCollectionSummaryPdf()
    {
        $monthlyTotals = PaymentTransaction::where('status', 'completed')
            ->where('processed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(processed_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $months = []; $totals = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = $m->format('M Y');
            $totals[] = (float) ($monthlyTotals[$m->format('Y-m')] ?? 0);
        }

        $methodTotals = PaymentTransaction::where('status', 'completed')->selectRaw("
                SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
                SUM(CASE WHEN payment_method = 'gcash' THEN amount ELSE 0 END) as gcash,
                SUM(CASE WHEN payment_method NOT IN ('cash', 'gcash') THEN amount ELSE 0 END) as other
            ")->first();

        $data = array_merge($this->cashierPdfLetterhead(), [
            'reportTitle'    => 'Collection Summary',
            'dateRangeLabel' => $months[0] . ' — ' . $months[count($months) - 1],
            'months'         => $months,
            'totals'         => $totals,
            'cash'           => (float) $methodTotals->cash,
            'gcash'          => (float) $methodTotals->gcash,
            'other'          => (float) $methodTotals->other,
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cashier.collection-summary', $data),
            $data
        )->download('collection-summary-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Daily Report ─────────────────────────────────
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        // Aggregates computed separately from the paginated row listing below —
        // these must reflect the WHOLE day, not just whichever page of rows
        // is currently being displayed.
        $baseQuery = fn () => PaymentTransaction::where('status', 'completed')
            ->whereDate('processed_at', $date);

        $total  = $baseQuery()->sum('amount');
        $cash   = $baseQuery()->where('payment_method', 'cash')->sum('amount');
        $online = $baseQuery()->whereNotIn('payment_method', ['cash'])->sum('amount');
        $count  = $baseQuery()->count();

        $rows = PaymentTransaction::with(['user', 'enrollment', 'paidInstallments'])
            ->where('status', 'completed')
            ->whereDate('processed_at', $date)
            ->latest('processed_at')
            ->paginate((int) $request->get('per_page', 20))
            ->through(function ($tx) {
                return [
                    'date'        => $tx->processed_at?->format('M d, Y'), // used by the row's reprint button
                    'time'        => $tx->processed_at?->format('h:i A'),
                    'student'     => $tx->user?->name ?? '—',
                    'grade'       => $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—',
                    'school_year' => $tx->enrollment?->school_year ?? '—',
                    'type'        => ucwords(str_replace('_', ' ', $tx->payment_type ?? '—')),
                    'description' => $tx->description,
                    'late_fee'    => $tx->lateFeePortion(),
                    'method'      => ucfirst($tx->payment_method ?? '—'),
                    'amount'      => number_format($tx->amount, 2),
                    'reference'   => $tx->reference_number ?? '—',
                    'or_no'       => $tx->reference_number ?? '—',
                ];
            });

        return response()->json([
            'date'   => $date,
            'total'  => number_format($total, 2),
            'cash'   => number_format($cash, 2),
            'online' => number_format($online, 2),
            'count'  => $count,
            'rows'   => $rows,
        ]);
    }

    /**
     * PDF version of dailyReport() above — same aggregates, but $rows is
     * every transaction for the day (->get(), not ->paginate()), since a
     * printed report needs the whole day, not just one on-screen page of
     * it. Same shared letterhead/running-header-footer design as every
     * Finance report.
     */
    public function downloadDailyReportPdf(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $baseQuery = fn () => PaymentTransaction::where('status', 'completed')
            ->whereDate('processed_at', $date);

        $total  = $baseQuery()->sum('amount');
        $cash   = $baseQuery()->where('payment_method', 'cash')->sum('amount');
        $online = $baseQuery()->whereNotIn('payment_method', ['cash'])->sum('amount');
        $count  = $baseQuery()->count();

        $rows = PaymentTransaction::with(['user', 'enrollment'])
            ->where('status', 'completed')
            ->whereDate('processed_at', $date)
            ->latest('processed_at')
            ->get()
            ->map(function ($tx) {
                return [
                    'time'      => $tx->processed_at?->format('h:i A'),
                    'student'   => $tx->user?->name ?? '—',
                    'grade'     => $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—',
                    'type'      => ucwords(str_replace('_', ' ', $tx->payment_type ?? '—')),
                    'method'    => ucfirst($tx->payment_method ?? '—'),
                    'amount'    => $tx->amount,
                    'reference' => $tx->reference_number ?? '—',
                ];
            });

        $data = array_merge($this->cashierPdfLetterhead(), [
            'reportTitle'    => 'Daily Collection Report',
            'dateRangeLabel' => \Carbon\Carbon::parse($date)->format('F d, Y'),
            'total'          => $total,
            'cash'           => $cash,
            'online'         => $online,
            'count'          => $count,
            'rows'           => $rows,
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cashier.daily-report', $data),
            $data
        )->download('daily-report-' . $date . '.pdf');
    }

    // ── Receipts List ─────────────────────────────────
    public function receiptsList(Request $request)
    {
        $search = trim($request->get('q', ''));
        $userId = $request->get('user_id');
        // Receipts (the tab) only ever wants completed transactions, so that
        // stays the default — Payment History passes status=all (or a specific
        // status) to see pending/rejected ones too, via this same endpoint.
        $status = $request->get('status', 'completed');
        $method = $request->get('method');
        $date   = $request->get('date');

        $query = PaymentTransaction::with(['user', 'enrollment', 'paidInstallments'])
            ->latest('processed_at');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($method && $method !== 'all') {
            $method === 'cash' ? $query->where('payment_method', 'cash') : $query->where('payment_method', '!=', 'cash');
        }
        if ($date) {
            $query->whereDate('processed_at', $date);
        }

        if ($userId) {
            // Used by the Process Payment screen's "Recent Payments" mini panel
            // and by Payment History when opened for one student — scoped to
            // that student, not a name/reference search.
            $query->where('user_id', $userId);
        } elseif ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $txs = $query->paginate($perPage ?: 20)->through(function ($tx) {
            return [
                'or_no'       => $tx->reference_number ?? '—',
                'date'        => $tx->processed_at?->format('M d, Y'),
                'date_iso'    => $tx->processed_at?->toDateString(),
                'time'        => $tx->processed_at?->format('h:i A'),
                'student'     => $tx->user?->name ?? '—',
                'grade'       => $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—',
                'school_year' => $tx->enrollment?->school_year ?? '—',
                'type'        => ucwords(str_replace('_', ' ', $tx->payment_type ?? '—')),
                'description' => $tx->description,
                'late_fee'    => $tx->lateFeePortion(),
                'method'      => ucfirst($tx->payment_method ?? '—'),
                'amount'      => number_format($tx->amount, 2),
                'status'      => $tx->status,
            ];
        });

        return response()->json($txs);
    }

    // ── Student Search ────────────────────────────────

    public function searchStudent(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $students = User::where('role', 'student')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            // Previously only 'approved'/'enrolled' — a student mid-promotion
            // falls through the gap between those two statuses entirely (old
            // enrollment marked 'completed', new one starts 'pending' until
            // approved), making them briefly unfindable here with no
            // indication why. 'declined' (never a real student to collect
            // from) and 'cancelled' (an undone promotion artifact — see
            // EnrollmentController::undoPromotion()) are the only statuses
            // that genuinely shouldn't surface here.
            ->whereHas('enrollments', function ($qe) {
                $qe->whereNotIn('status', ['declined', 'cancelled']);
            })
            ->with(['enrollments' => function ($qe) {
                $qe->whereNotIn('status', ['declined', 'cancelled'])->latest();
            }])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function (User $user) {
                $enrollment = $user->enrollments->first();
                $prior = \App\Services\PaymentService::priorOutstandingBalance($user->id, $enrollment?->id);
                return [
                    'id'                 => $user->id,
                    'name'               => $user->name,
                    'email'              => $user->email,
                    'enrollment_id'      => $enrollment?->id,
                    'grade_level'        => $enrollment?->grade_level,
                    'balance'            => $enrollment?->remaining_balance ?? 0,
                    // smallest / largest payment the cashier may take now (PaymentService::paymentLimits)
                    'pay_limits'         => $enrollment ? \App\Services\PaymentService::paymentLimits($enrollment) : null,
                    'payment_status'     => $enrollment?->payment_status ?? 'unpaid',
                    'enrollment_status'  => $enrollment?->status ?? 'pending',
                    'payment_option'     => $enrollment?->payment_option ?? 'A',
                    'payment_type'       => $enrollment?->payment_type ?? 'full',
                    'total_fee'          => $enrollment?->total_fee ?? 0,
                    'payment_amount'     => $enrollment?->payment_amount ?? 0,
                    'downpayment_amount' => $enrollment?->downpayment_amount ?? 0,
                    'monthly_amount'     => $enrollment?->monthly_amount ?? 0,
                    'school_year'        => $enrollment?->school_year ?? '—',
                    // Phase 4 — a balance left on a DIFFERENT enrollment
                    // (almost always a prior school year), invisible unless
                    // surfaced explicitly: this screen only otherwise shows
                    // whichever enrollment is current.
                    'prior_balance'            => $prior['amount'],
                    'prior_balance_school_year'=> $prior['school_year'],
                ];
            });

        return response()->json($students);
    }

    // ── Refresh one already-selected student's card ───────────────────
    // Same JSON shape as searchStudent()'s map(), but keyed to one known
    // enrollment_id instead of a name/email search — used after an Xendit
    // payment is confirmed via polling, so the cashier's screen can update
    // the balance/status in place and stay on this student instead of a
    // full page reload bouncing them back to a blank search screen.
    public function refreshStudent(Request $request)
    {
        $request->validate(['enrollment_id' => 'required|exists:enrollments,id']);

        $enrollment = Enrollment::find($request->enrollment_id);
        $user       = User::find($enrollment->user_id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // Included so updateStudentCardStats() (shared with searchStudent()/
        // listStudents()) doesn't wipe the prior-balance warning back off
        // after a post-payment refresh just because this field was missing.
        $prior = \App\Services\PaymentService::priorOutstandingBalance($user->id, $enrollment->id);

        return response()->json([
            'success'            => true,
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'enrollment_id'      => $enrollment->id,
            'grade_level'        => $enrollment->grade_level,
            'balance'            => $enrollment->remaining_balance ?? 0,
            'pay_limits'         => \App\Services\PaymentService::paymentLimits($enrollment),
            'payment_status'     => $enrollment->payment_status ?? 'unpaid',
            'enrollment_status'  => $enrollment->status ?? 'pending',
            'payment_option'     => $enrollment->payment_option ?? 'A',
            'payment_type'       => $enrollment->payment_type ?? 'full',
            'total_fee'          => $enrollment->total_fee ?? 0,
            'payment_amount'     => $enrollment->payment_amount ?? 0,
            'downpayment_amount' => $enrollment->downpayment_amount ?? 0,
            'monthly_amount'     => $enrollment->monthly_amount ?? 0,
            'school_year'        => $enrollment->school_year ?? '—',
            'prior_balance'             => $prior['amount'],
            'prior_balance_school_year' => $prior['school_year'],
        ]);
    }

    // ── Student List (all approved) ───────────────────
    public function listStudents(Request $request)
    {
        $search = trim($request->get('q', ''));

        // Same fix as searchStudent() (docs/SCHOOL-YEAR-VISIBILITY-PHASED-PLAN.md
        // Phase 2) — this is actually the MORE heavily-used of the two (the
        // default "browse students" table, not just the name-search box), so
        // a student mid-promotion (old enrollment 'completed', new one still
        // 'pending' until approved) was invisible here too, for the same reason.
        //
        // Process Payment filters: School Year / Grade / Payment Status. Each row
        // shows the newest enrollment matching them — so picking an older year
        // shows (and lets the cashier collect) that year's balance.
        // School year opens on the Current School Year (Admin › Settings › Academic); "all" = every year
        $currentSy = \App\Models\Setting::getCurrentSchoolYear();
        $sy    = (string) $request->get('sy', $currentSy);
        $grade = (string) $request->get('grade', 'all');
        $pay   = (string) $request->get('pay', 'all');
        if ($sy !== 'all' && !in_array($sy, \App\Models\Setting::schoolYearOptions(), true)) $sy = $currentSy;
        if (!in_array($grade, ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'], true)) $grade = 'all';
        if (!in_array($pay, ['paid', 'partial', 'pending'], true)) $pay = 'all';

        $activeStatuses = fn ($qe) => $qe->whereNotIn('status', ['declined', 'cancelled'])
            ->when($sy !== 'all', fn ($q) => $q->where('school_year', $sy))
            ->when($grade !== 'all', fn ($q) => $q->where('grade_level', $grade))
            ->when($pay === 'pending', fn ($q) => $q->where(fn ($w) => $w->whereNull('payment_status')->orWhereNotIn('payment_status', ['paid', 'partial'])))
            ->when(in_array($pay, ['paid', 'partial'], true), fn ($q) => $q->where('payment_status', $pay));

        $query = User::where('role', 'student')
            ->whereHas('enrollments', $activeStatuses)
            // Newest-enrolled first, not alphabetical — a student who just
            // got approved should show up at the top of this list without
            // the cashier having to search for them by name.
            ->withMax(['enrollments as latest_enrollment_at' => $activeStatuses], 'created_at')
            ->with(['enrollments' => function ($qe) use ($activeStatuses) {
                // id breaks ties — a promotion can create the new enrollment in the same second
                $activeStatuses($qe)->latest()->orderByDesc('id');
            }]);

        if ($search !== '') {
            $query->where(function ($q) use ($search, $activeStatuses) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('enrollments', function ($qe) use ($search, $activeStatuses) {
                      $activeStatuses($qe)->where('reference_number', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderByDesc('latest_enrollment_at')
            ->paginate((int) $request->get('per_page', 20))
            ->through(function (User $user) {
                $e = $user->enrollments->first();
                $prior = \App\Services\PaymentService::priorOutstandingBalance($user->id, $e?->id);
                return [
                    'id'                 => $user->id,
                    'name'               => $user->name,
                    'email'              => $user->email,
                    'reference_no'       => $e?->reference_number ?? '—',
                    'enrollment_id'      => $e?->id,
                    'grade_level'        => $e?->grade_level ?? '—',
                    'section'            => $e?->section ?? '—',
                    'school_year'        => $e?->school_year ?? '—',
                    'payment_status'     => $e?->payment_status ?? 'unpaid',
                    'enrollment_status'  => $e?->status ?? 'approved',
                    'payment_option'     => $e?->payment_option ?? 'A',
                    'payment_type'       => $e?->payment_type ?? 'full',
                    'total_fee'          => $e?->total_fee ?? 0,
                    'payment_amount'     => $e?->payment_amount ?? 0,
                    'balance'            => $e?->remaining_balance ?? 0,
                    'pay_limits'         => $e ? \App\Services\PaymentService::paymentLimits($e) : null,
                    'downpayment_amount' => $e?->downpayment_amount ?? 0,
                    'monthly_amount'     => $e?->monthly_amount ?? 0,
                    'prior_balance'             => $prior['amount'],
                    'prior_balance_school_year' => $prior['school_year'],
                ];
            });

        return response()->json($students);
    }

    // ── Payment plan helpers ──────────────────────────

    /**
     * Delegates to the shared App\Services\FeeCalculator (the same source
     * every other quoting path — online enrollment, walk-in, Finance — uses)
     * instead of a hardcoded table. This used to be an independent copy of
     * the fee numbers, so changing anything on the Fee Settings page never
     * affected what Cashier quoted or charged — see FeeCalculator's own
     * docblock for the history of this exact bug class.
     */
    private function buildBreakdown(string $grade, string $option): array
    {
        // Option C is only offered for Grade 1-6, Option D only for
        // Nursery/Kindergarten (FeeCalculator::PLANS_BY_GRADE)
        if (!\App\Services\FeeCalculator::planAllowed($grade, $option)) {
            return [];
        }

        $calc = \App\Services\FeeCalculator::calculate($grade, $option);

        return [
            'tuition_fee'     => $calc['tuition'],
            'misc_reg_pta'    => $calc['misc'],
            'books'           => $calc['books'],
            'insurance'       => $calc['insurance'],
            'electric_bill'   => $calc['electric'],
            'base_total'      => $calc['base_total'],
            'discount'        => $calc['discount'] ?? 0,
            'total_due'       => $calc['total_due'],
            'payment_type'    => $calc['payment_type'],
            'downpayment'     => $calc['downpayment'] ?? 0,
            'monthly_amount'  => $calc['monthly_amount'] ?? 0,
            'duration_months' => $calc['duration_months'] ?? 0,
        ];
    }

    public function getPaymentOptions(Request $request)
    {
        $grade   = $request->get('grade', 'nursery');
        $options = [];
        foreach (['A','B','C','D'] as $opt) {
            $bd = $this->buildBreakdown($grade, $opt);
            if ($bd) $options[$opt] = $bd;
        }
        return response()->json($options);
    }

    /**
     * Month-by-month installment timeline for one enrollment — powers the
     * "which months are paid / which is next" view on the Process Payment
     * screen. Same shape as Finance's installmentDetails() so both portals
     * render it identically.
     */
    public function installmentTimeline($enrollmentId)
    {
        $enrollment = \App\Models\Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return response()->json(['installments' => [], 'downpayment' => null]);
        }

        $installments = $enrollment->paymentInstallments()->orderBy('due_date')->get()
            ->map(fn($i) => [
                'id'         => $i->id,
                'month_name' => $i->month_name,
                'due_date'   => $i->due_date?->format('Y-m-d'),
                'amount'     => (float) $i->amount,
                'late_fee'   => (float) ($i->late_fee ?? 0),
                'status'     => $i->status,
                'weeks_overdue' => $i->weeks_overdue ?? 0,
            ]);

        return response()->json([
            'installments' => $installments,
            'downpayment'  => [
                'amount' => (float) ($enrollment->downpayment_amount ?? 0),
                'paid'   => (float) ($enrollment->payment_amount ?? 0) >= (float) ($enrollment->downpayment_amount ?? 0),
            ],
        ]);
    }

    public function setEnrollmentPlan(Request $request)
    {
        $request->validate([
            'enrollment_id'  => 'required|exists:enrollments,id',
            'payment_option' => 'required|in:A,B,C,D',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        // Once money has been paid against the current plan, switching plans
        // here would recompute remaining_balance against a different total
        // without touching the installment rows already generated for the
        // old one — they'd keep the old amounts/due dates while the
        // enrollment now reports a different monthly_amount/downpayment,
        // silently going out of sync. Lock the plan as soon as there's a
        // real payment; staff needing to correct a wrong plan after that
        // point should handle it manually rather than through this endpoint.
        if ((float) ($enrollment->payment_amount ?? 0) > 0) {
            return response()->json([
                'error' => 'This student already has a payment recorded, so the plan can no longer be changed here — it would leave the existing payment and installment schedule out of sync with the new plan.',
            ], 422);
        }

        $bd = $this->buildBreakdown($enrollment->grade_level, $request->payment_option);

        if (empty($bd)) {
            return response()->json(['error' => 'This plan is not available for this grade level.'], 422);
        }

        $enrollment->update([
            'payment_option'     => $request->payment_option,
            'payment_type'       => $bd['payment_type'],
            'total_fee'          => $bd['total_due'],
            'remaining_balance'  => $bd['total_due'] - ($enrollment->payment_amount ?? 0),
            'payment_breakdown'  => json_encode($bd),
            'downpayment_amount' => $bd['downpayment'] ?? 0,
            'monthly_amount'     => $bd['monthly_amount'] ?? 0,
        ]);

        return response()->json(['success' => true, 'breakdown' => $bd, 'enrollment' => $enrollment->fresh()]);
    }

    // ── Cash Payment ──────────────────────────────────

    public function processCash(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount'        => 'required|numeric|min:1',
            'payment_type'  => 'required|string',
            'notes'         => 'nullable|string|max:500',
        ]);

        $amount    = (float) $request->amount;
        $reference = 'CASH-' . strtoupper(substr(md5(uniqid()), 0, 8));

        DB::beginTransaction();
        try {
            // Row-locked re-fetch — see PaymentService::lockEnrollment() — this
            // method previously had no transaction at all, so two cash payments
            // for the same enrollment could both compute from the same stale
            // payment_amount.
            $enrollment = \App\Services\PaymentService::lockEnrollment($request->enrollment_id);

            // No fee plan assessed yet — PaymentService::requiredEnrollmentThreshold()
            // falls back to "any payment > 0 counts" when it can't compute a
            // real threshold (no downpayment_amount AND no total_fee), which
            // let a single peso of cash flip an unassessed enrollment straight
            // to 'enrolled'. Block cash here instead of letting that fallback
            // fire — there's no real amount due to check the cash against yet.
            // Checking the threshold itself (not just payment_option) also
            // catches the rarer case of an option being set but total_fee
            // still 0 — any input that would trip the same fallback.
            if (\App\Services\PaymentService::requiredEnrollmentThreshold($enrollment) <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This student has not been assessed yet — no fee plan is set. Please complete the enrollment assessment before accepting payment.',
                ], 422);
            }

            // Already fully paid — nothing left to collect. Without this,
            // cash kept being accepted past the real balance, driving
            // remaining_balance negative and payment_amount past total_fee.
            if ((float) ($enrollment->remaining_balance ?? 0) <= 0 && $enrollment->payment_status === 'paid') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This student is already fully paid — remaining balance is ₱0.00. No further payment is needed.',
                ], 422);
            }

            // At least what is due now (down payment / next installment / full balance for plan A), at
            // most the remaining balance — paying ahead is fine, paying short or overpaying is not
            if ($limitError = \App\Services\PaymentService::paymentLimitError($enrollment, $amount)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $limitError], 422);
            }

            // Double-click / retried-request guard — a cash payment is recorded
            // as already 'completed' immediately, so a double-click here
            // creates two independent, fully-credited transactions for the
            // same handover of cash. Treat an identical amount+method for this
            // enrollment in the last 15 seconds as the same click landing
            // twice, not two payments. Same pattern as
            // Finance\DashboardController::recordWalkInPayment().
            $recentDuplicate = PaymentTransaction::where('enrollment_id', $enrollment->id)
                ->where('payment_method', 'cash')
                ->where('amount', $amount)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->exists();
            if ($recentDuplicate) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'A matching cash payment was just recorded for this student — please check Payment History before submitting again.',
                ], 422);
            }

            $transaction = PaymentTransaction::create([
                'enrollment_id'    => $enrollment->id,
                'user_id'          => $enrollment->user_id,
                'payment_type'     => 'walkin',
                'payment_method'   => 'cash',
                'amount'           => $amount,
                'reference_number' => $reference,
                'description'      => $request->payment_type . ($request->notes ? ' — ' . $request->notes : ''),
                'status'           => 'completed',
                'processed_by'     => Auth::guard('cashier')->id(),
                'processed_at'     => now(),
            ]);

            $enrollment->decrement('remaining_balance', $amount);
            $enrollment->increment('payment_amount', $amount);
            $enrollment->refresh();

            if ($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D'])) {
                \App\Services\PaymentService::reconcileInstallmentStatuses($enrollment, $transaction);
            }

            \App\Services\PaymentService::advanceEnrollmentAfterPayment($enrollment->fresh());

            ActivityLogger::log(
                'cash_payment',
                'Recorded cash payment of ₱' . number_format($amount, 2) . ' (Ref: ' . $reference . ')',
                'PaymentTransaction',
                $transaction->id
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Cash payment failed: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success'     => true,
            'reference'   => $reference,
            'message'     => 'Cash payment recorded successfully.',
            'transaction' => $transaction->id,
        ]);
    }

    // ── Xendit Link ───────────────────────────────────

    public function generateXenditLink(Request $request)
    {
        $request->validate([
            'enrollment_id'  => 'required|exists:enrollments,id',
            'amount'         => 'required|numeric|min:1',
            'payment_type'   => 'required|string',
            // GrabPay / bank transfer / OTC removed — out of scope for this
            // system. Maya is locked (UI shows it as "coming soon" but it's
            // not wired to Xendit) — rejecting it here too means a direct
            // API call can't bypass the disabled button and actually
            // generate a Maya link.
            'payment_method' => 'required|string|in:gcash',
            'student_name'   => 'required|string',
            'student_email'  => 'nullable|email',
        ]);

        $apiKey = config('services.xendit.secret_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Xendit API key is not configured.'], 500);
        }

        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        // No fee plan assessed yet — same gap as processCash(): once this
        // link is paid, completeXenditPayment() -> advanceEnrollmentAfterPayment()
        // falls back to "any payment > 0 counts" when there's no real
        // threshold to check against, flipping an unassessed enrollment
        // straight to 'enrolled'. Block generating the link here instead.
        if (\App\Services\PaymentService::requiredEnrollmentThreshold($enrollment) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This student has not been assessed yet — no fee plan is set. Please complete the enrollment assessment before generating a payment link.',
            ], 422);
        }

        // Already fully paid — same reasoning as processCash(): nothing left
        // to collect, so don't generate a link that would only overpay.
        if ((float) ($enrollment->remaining_balance ?? 0) <= 0 && $enrollment->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This student is already fully paid — remaining balance is ₱0.00. No further payment is needed.',
            ], 422);
        }

        // Same rule as cash: only an exact accepted amount (due now, + whole months in advance, full balance)
        if ($limitError = \App\Services\PaymentService::paymentLimitError($enrollment, (float) $request->amount)) {
            return response()->json(['success' => false, 'message' => $limitError], 422);
        }

        // One open link per student (shared with the Student Portal): the same amount → that link again;
        // a different amount → the old link is cancelled at Xendit first, so both can never be paid.
        // (It used to return the open link whatever its amount — e.g. a "due now" link when the cashier
        // asked for the full balance — without saying which amount it was for.)
        [$existingPending, $openError] = \App\Services\PaymentService::openXenditLink($enrollment->id, (float) $request->amount, $apiKey);
        if ($openError) {
            return response()->json(['success' => false, 'message' => $openError], 422);
        }
        if ($existingPending) {
            return response()->json([
                'success'     => true,
                'invoice_url' => $existingPending->xendit_invoice_url,
                'invoice_id'  => $existingPending->xendit_invoice_id,
                'reused'      => true,
                'amount'      => (float) $existingPending->amount,
                'message'     => 'A payment link for ₱' . number_format((float) $existingPending->amount, 2) . ' is already open — showing it again instead of making a duplicate.',
            ]);
        }

        $externalId = 'ILC-' . $request->enrollment_id . '-' . time();

        try {
            // The exact amount, centavos included — and the transaction records the amount Xendit accepted
            // (`(int) $request->amount` used to drop the centavos while still crediting them)
            [$response, $chargeAmount] = \App\Services\PaymentService::createXenditInvoice($apiKey, [
                    'external_id'          => $externalId,
                    'description'          => 'ILC Tuition — ' . $request->payment_type . ' (' . $request->student_name . ')',
                    'invoice_duration'     => 86400,
                    'currency'             => 'PHP',
                    'customer'             => [
                        'given_names' => $request->student_name,
                        'email'       => $request->student_email ?? '',
                    ],
                    'payment_methods'      => $this->xenditMethodsFor($request->payment_method),
                    // This tab is on the CUSTOMER's device, not the cashier's —
                    // it must not land on the internal (login-walled) cashier
                    // dashboard. The cashier's own tab already updates itself
                    // via polling (pollCashierXenditStatus), so this is just a
                    // thank-you screen for whoever actually paid.
                    'success_redirect_url' => route('cashier.payment-result'),
                    'failure_redirect_url' => route('cashier.payment-result', ['outcome' => 'failed']),
                ], (float) $request->amount);
        } catch (\Throwable $e) {
            Log::error('Xendit invoice request failed (network/connection error)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Could not reach the payment service. Please check your connection and try again.',
            ], 422);
        }

        if (!$response->successful()) {
            Log::error('Xendit invoice failed', ['response' => $response->json()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment link. ' . ($response->json()['message'] ?? ''),
            ], 422);
        }

        $invoice = $response->json();

        $transaction = PaymentTransaction::create([
            'enrollment_id'      => $request->enrollment_id,
            'user_id'            => $enrollment->user_id,
            'payment_type'       => 'online',
            'payment_method'     => $request->payment_method,
            'amount'             => $chargeAmount, // what Xendit actually charges — and what gets credited
            'reference_number'   => $externalId,
            'xendit_invoice_id'  => $invoice['id'],
            'xendit_invoice_url' => $invoice['invoice_url'],
            'description'        => $request->payment_type,
            'status'             => 'pending',
            'processed_by'       => Auth::guard('cashier')->id(),
            'processed_at'       => now(),
        ]);

        ActivityLogger::log(
            'xendit_link_generated',
            'Generated ' . $request->payment_method . ' payment link for ₱' . number_format($chargeAmount, 2) . ' — ' . $request->student_name,
            'PaymentTransaction',
            $transaction->id
        );

        return response()->json([
            'success'     => true,
            'invoice_url' => $invoice['invoice_url'],
            'invoice_id'  => $invoice['id'],
            'expiry'      => $invoice['expiry_date'] ?? null,
            'amount'      => $chargeAmount, // the amount on Xendit's page
        ]);
    }

    /**
     * Polled from the cashier's "payment link generated" panel — the actual
     * payment usually happens on the customer's own phone (scanning the
     * link/QR), so the cashier's screen otherwise has no way to know it went
     * through except by manually reloading. Route is behind CashierMiddleware
     * already, so any cashier/admin/superadmin can check any transaction —
     * same shared-resource trust model as the rest of this internal tool.
     *
     * Also actively reconciles against Xendit's own API when our local copy
     * still says "pending" — the webhook alone can only ever update this if
     * Xendit's servers can reach this app over the public internet, which a
     * local/APP_URL=127.0.0.1 setup with no tunnel never allows. Without
     * this, a payment could show as completed on Xendit's own dashboard
     * while sitting as "pending" here forever, no matter how many times the
     * page polls or is manually reloaded.
     */
    public function checkXenditStatus(Request $request)
    {
        $request->validate(['invoice_id' => 'required|string']);

        $transaction = PaymentTransaction::where('xendit_invoice_id', $request->invoice_id)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $status = \App\Services\PaymentService::reconcileXenditInvoice($transaction);
        $transaction->refresh();

        return response()->json([
            'status'       => $status,
            'amount'       => $transaction->amount,
            'reference'    => $transaction->reference_number,
            'processed_at' => $transaction->processed_at?->toIso8601String(),
        ]);
    }

    // ── Xendit Webhook ────────────────────────────────

    public function xenditWebhook(Request $request)
    {
        // No token configured (XENDIT_WEBHOOK_TOKEN missing) → refuse everything. Otherwise an empty
        // token would "match" a request with no x-callback-token header, letting anyone mark invoices paid.
        $expected = (string) config('services.xendit.webhook_token');
        if ($expected === '' || !hash_equals($expected, (string) $request->header('x-callback-token'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data   = $request->all();
        $status = $data['status'] ?? null;

        // An invoice that expired or was explicitly failed will never be paid —
        // mark the still-pending transaction so it doesn't sit as "Pending"
        // forever with no visibility to Finance/Cashier or the student. Only
        // touch it while it's still pending: if it somehow already completed
        // (e.g. a race with a PAID webhook), leave that alone.
        if (in_array($status, ['EXPIRED', 'FAILED'])) {
            return DB::transaction(function () use ($data, $status) {
                $transaction = PaymentTransaction::where('xendit_invoice_id', $data['id'])
                    ->lockForUpdate()
                    ->first();

                if ($transaction && $transaction->status === 'pending') {
                    $transaction->update([
                        'status' => 'expired',
                        'processed_at' => now(),
                    ]);
                }

                return response()->json(['message' => 'Handled: ' . $status]);
            });
        }

        if (!in_array($status, ['PAID', 'SETTLED'])) {
            return response()->json(['message' => 'Ignored: ' . $status]);
        }

        return DB::transaction(function () use ($data) {
            // Lock the row so a second, concurrently-delivered webhook for the same
            // invoice can't read the pre-update status before this one commits.
            $transaction = PaymentTransaction::where('xendit_invoice_id', $data['id'])
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return response()->json(['message' => 'Not found'], 404);
            }

            // Idempotency guard — Xendit can and does redeliver the same "paid"
            // webhook more than once (retries on timeout / non-2xx responses).
            // Without this, a redelivery would double-credit the enrollment.
            // (Also re-checked inside completeXenditPayment() itself, since
            // the live-status fallback in reconcileXenditInvoice() can race
            // with this webhook arriving at the same time.)
            if ($transaction->status === 'completed') {
                return response()->json(['message' => 'Already processed']);
            }

            // The token proves it came from Xendit; this proves it's about THIS payment, for THIS amount
            // (reference, amount to the centavo, pesos). A mismatch is never credited — it is logged and
            // left pending for staff to check. 200, so Xendit doesn't keep re-sending it.
            if ($why = \App\Services\PaymentService::xenditInvoiceMismatch($transaction, $data)) {
                \App\Services\PaymentService::logXenditMismatch($transaction, $why);
                return response()->json(['message' => 'Not credited: invoice does not match the payment']);
            }

            \App\Services\PaymentService::completeXenditPayment($transaction);

            return response()->json(['message' => 'OK']);
        });
    }

    private function xenditMethodsFor(string $method): array
    {
        // GrabPay / bank transfer / OTC removed — out of scope for this
        // system. Maya is locked (not wired up yet, see generateXenditLink's
        // validation above, which already rejects it before this is reached)
        // — no case for it here on purpose, so an unexpected value falls
        // through to the empty default rather than resolving to PAYMAYA.
        return match ($method) {
            'gcash' => ['GCASH'],
            default => [],
        };
    }

}
