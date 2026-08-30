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

class CashierController extends Controller
{
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

        $lockKey   = 'cashier_login_' . md5($request->ip() . $request->email);
        $unlockKey = 'cashier_unlock_' . md5($request->ip() . $request->email);
        $attempts  = cache()->get($lockKey, 0);

        if ($attempts >= 5) {
            $unlockAt = cache()->get($unlockKey);
            $mins = ($unlockAt && $unlockAt > now())
                ? max(1, (int) ceil(now()->diffInSeconds($unlockAt) / 60))
                : 15;
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
            return redirect()->intended(route('cashier.dashboard'));
        }

        $this->incrementAttempts($lockKey, $unlockKey, $attempts);
        return back()->withErrors(['email' => 'Incorrect email or password.'])
                     ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('cashier')->user();
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
        $ttl = now()->addMinutes(15);
        cache()->put($lockKey, $new, $ttl);
        if ($new >= 5) {
            cache()->put($unlockKey, $ttl, $ttl);
        }
    }

    // ── Change Password ───────────────────────────────

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('cashier')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('password_success', 'Password changed successfully!');
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

        // Last 7 days for the bar chart
        $csChDays = []; $csChTotals = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $csChDays[]   = $d->format('D, M j');
            $csChTotals[] = (float) PaymentTransaction::where('status', 'completed')
                ->whereDate('processed_at', $d->toDateString())->sum('amount');
        }

        return view('cashier.dashboard', compact(
            'todayTotal', 'todayCount', 'pendingCount',
            'recentTransactions', 'todayTransactions',
            'todayCash', 'todayOnline',
            'csChDays', 'csChTotals'
        ));
    }

    // ── Daily Report ─────────────────────────────────
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $txs = PaymentTransaction::with(['user', 'enrollment'])
            ->where('status', 'completed')
            ->whereDate('processed_at', $date)
            ->latest('processed_at')
            ->get();

        $total  = $txs->sum('amount');
        $cash   = $txs->where('payment_method', 'cash')->sum('amount');
        $online = $txs->whereNotIn('payment_method', ['cash'])->sum('amount');
        $count  = $txs->count();

        $rows = $txs->map(function ($tx) {
            return [
                'time'        => $tx->processed_at?->format('h:i A'),
                'student'     => $tx->user?->name ?? '—',
                'grade'       => $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—',
                'school_year' => $tx->enrollment?->school_year ?? '—',
                'type'        => ucwords(str_replace('_', ' ', $tx->payment_type ?? '—')),
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

    // ── Receipts List ─────────────────────────────────
    public function receiptsList(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = PaymentTransaction::with(['user', 'enrollment'])
            ->where('status', 'completed')
            ->latest('processed_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $txs = $query->limit(100)->get()->map(function ($tx) {
            return [
                'or_no'       => $tx->reference_number ?? '—',
                'date'        => $tx->processed_at?->format('M d, Y'),
                'time'        => $tx->processed_at?->format('h:i A'),
                'student'     => $tx->user?->name ?? '—',
                'grade'       => $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—',
                'school_year' => $tx->enrollment?->school_year ?? '—',
                'type'        => ucwords(str_replace('_', ' ', $tx->payment_type ?? '—')),
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
            ->whereHas('enrollments', function ($qe) {
                $qe->whereIn('status', ['approved','enrolled']);
            })
            ->with(['enrollments' => function ($qe) {
                $qe->whereIn('status', ['approved','enrolled'])->latest();
            }])
            ->limit(10)
            ->get()
            ->map(function (User $user) {
                $enrollment = $user->enrollments->first();
                return [
                    'id'                 => $user->id,
                    'name'               => $user->name,
                    'email'              => $user->email,
                    'enrollment_id'      => $enrollment?->id,
                    'grade_level'        => $enrollment?->grade_level,
                    'balance'            => $enrollment?->remaining_balance ?? 0,
                    'payment_status'     => $enrollment?->payment_status ?? 'unpaid',
                    'enrollment_status'  => $enrollment?->status ?? 'pending',
                    'payment_option'     => $enrollment?->payment_option ?? 'A',
                    'payment_type'       => $enrollment?->payment_type ?? 'full',
                    'total_fee'          => $enrollment?->total_fee ?? 0,
                    'payment_amount'     => $enrollment?->payment_amount ?? 0,
                    'downpayment_amount' => $enrollment?->downpayment_amount ?? 0,
                    'monthly_amount'     => $enrollment?->monthly_amount ?? 0,
                    'school_year'        => $enrollment?->school_year ?? '—',
                ];
            });

        return response()->json($students);
    }

    // ── Student List (all approved) ───────────────────
    public function listStudents(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = User::where('role', 'student')
            ->whereHas('enrollments', function ($qe) {
                $qe->whereIn('status', ['approved','enrolled']);
            })
            ->with(['enrollments' => function ($qe) {
                $qe->whereIn('status', ['approved','enrolled'])->latest();
            }]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('enrollments', function ($qe) use ($search) {
                      $qe->whereIn('status', ['approved','enrolled'])
                         ->where('reference_number', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('name')->get()
            ->map(function (User $user) {
                $e = $user->enrollments->first();
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
                    'downpayment_amount' => $e?->downpayment_amount ?? 0,
                    'monthly_amount'     => $e?->monthly_amount ?? 0,
                ];
            });

        return response()->json($students);
    }

    // ── Payment plan helpers ──────────────────────────

    private function paymentRates(): array
    {
        return [
            'nursery'      => ['total'=>16005,'tuition'=>7505,'misc'=>2800,'books'=>3550,'insurance'=>150,'electric'=>2000],
            'kindergarten' => ['total'=>16005,'tuition'=>7505,'misc'=>2800,'books'=>3550,'insurance'=>150,'electric'=>2000],
            'grade1'       => ['total'=>17005,'tuition'=>7505,'misc'=>2800,'books'=>4550,'insurance'=>150,'electric'=>2000],
            'grade2'       => ['total'=>17005,'tuition'=>7505,'misc'=>2800,'books'=>4550,'insurance'=>150,'electric'=>2000],
            'grade3'       => ['total'=>17505,'tuition'=>7505,'misc'=>2800,'books'=>5050,'insurance'=>150,'electric'=>2000],
            'grade4'       => ['total'=>18005,'tuition'=>7505,'misc'=>2800,'books'=>5550,'insurance'=>150,'electric'=>2000],
            'grade5'       => ['total'=>18005,'tuition'=>7505,'misc'=>2800,'books'=>5550,'insurance'=>150,'electric'=>2000],
            'grade6'       => ['total'=>18005,'tuition'=>7505,'misc'=>2800,'books'=>5550,'insurance'=>150,'electric'=>2000],
        ];
    }

    private function buildBreakdown(string $grade, string $option): array
    {
        $rates = $this->paymentRates()[$grade] ?? $this->paymentRates()['grade1'];
        $b = [
            'tuition_fee'  => $rates['tuition'],
            'misc_reg_pta' => $rates['misc'],
            'books'        => $rates['books'],
            'insurance'    => $rates['insurance'],
            'electric_bill'=> $rates['electric'],
            'base_total'   => $rates['total'],
        ];

        $bDownB = ['nursery'=>6500,'kindergarten'=>6500,'grade1'=>7500,'grade2'=>7500,
                   'grade3'=>8000,'grade4'=>8500,'grade5'=>8500,'grade6'=>8500];
        $bDownC = ['grade1'=>5500,'grade2'=>5500,'grade3'=>6000,'grade4'=>6500,'grade5'=>6500,'grade6'=>6500];
        $bDownD = ['nursery'=>4505,'kindergarten'=>4505];

        switch ($option) {
            case 'A':
                $b['discount']     = 1501;
                $b['total_due']    = $rates['total'] - 1501;
                $b['payment_type'] = 'full';
                $b['downpayment']  = 0;
                $b['monthly_amount'] = 0;
                break;
            case 'B':
                $b['downpayment']       = $bDownB[$grade] ?? 6500;
                $b['monthly_amount']    = 1056.10;
                $b['duration_months']   = 9;
                $b['total_due']         = $b['downpayment'] + (1056.10 * 9);
                $b['payment_type']      = 'installment';
                break;
            case 'C':
                if (!isset($bDownC[$grade])) return [];  // not available for this grade
                $b['downpayment']       = $bDownC[$grade];
                $b['monthly_amount']    = 1278.32;
                $b['duration_months']   = 9;
                $b['total_due']         = $b['downpayment'] + (1278.32 * 9);
                $b['payment_type']      = 'installment';
                break;
            case 'D':
                if (!isset($bDownD[$grade])) return [];  // not available for this grade
                $b['downpayment']       = $bDownD[$grade];
                $b['monthly_amount']    = 1278.32;
                $b['duration_months']   = 9;
                $b['total_due']         = $b['downpayment'] + (1278.32 * 9);
                $b['payment_type']      = 'installment';
                break;
            default:
                return [];
        }
        return $b;
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

    public function setEnrollmentPlan(Request $request)
    {
        $request->validate([
            'enrollment_id'  => 'required|exists:enrollments,id',
            'payment_option' => 'required|in:A,B,C,D',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);
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

        $enrollment = Enrollment::findOrFail($request->enrollment_id);
        $reference  = 'CASH-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $transaction = PaymentTransaction::create([
            'enrollment_id'    => $enrollment->id,
            'user_id'          => $enrollment->user_id,
            'payment_type'     => 'walkin',
            'payment_method'   => 'cash',
            'amount'           => $request->amount,
            'reference_number' => $reference,
            'description'      => $request->payment_type . ($request->notes ? ' — ' . $request->notes : ''),
            'status'           => 'completed',
            'processed_by'     => Auth::guard('cashier')->id(),
            'processed_at'     => now(),
        ]);

        $enrollment->decrement('remaining_balance', $request->amount);
        $enrollment->increment('payment_amount', $request->amount);
        $this->advanceEnrollmentAfterPayment($enrollment->fresh());

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
            'payment_method' => 'required|string',
            'student_name'   => 'required|string',
            'student_email'  => 'nullable|email',
        ]);

        $apiKey = config('services.xendit.secret_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Xendit API key is not configured.'], 500);
        }

        $externalId = 'ILC-' . $request->enrollment_id . '-' . time();

        $response = Http::withBasicAuth($apiKey, '')
            ->post('https://api.xendit.co/v2/invoices', [
                'external_id'          => $externalId,
                'amount'               => (int) $request->amount,
                'description'          => 'ILC Tuition — ' . $request->payment_type . ' (' . $request->student_name . ')',
                'invoice_duration'     => 86400,
                'currency'             => 'PHP',
                'customer'             => [
                    'given_names' => $request->student_name,
                    'email'       => $request->student_email ?? '',
                ],
                'payment_methods'      => $this->xenditMethodsFor($request->payment_method),
                'success_redirect_url' => route('cashier.dashboard'),
                'failure_redirect_url' => route('cashier.dashboard'),
            ]);

        if (!$response->successful()) {
            Log::error('Xendit invoice failed', ['response' => $response->json()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment link. ' . ($response->json()['message'] ?? ''),
            ], 422);
        }

        $invoice = $response->json();

        PaymentTransaction::create([
            'enrollment_id'      => $request->enrollment_id,
            'user_id'            => Enrollment::find($request->enrollment_id)->user_id,
            'payment_type'       => 'online',
            'payment_method'     => $request->payment_method,
            'amount'             => $request->amount,
            'reference_number'   => $externalId,
            'xendit_invoice_id'  => $invoice['id'],
            'xendit_invoice_url' => $invoice['invoice_url'],
            'description'        => $request->payment_type,
            'status'             => 'pending',
            'processed_by'       => Auth::guard('cashier')->id(),
            'processed_at'       => now(),
        ]);

        return response()->json([
            'success'     => true,
            'invoice_url' => $invoice['invoice_url'],
            'invoice_id'  => $invoice['id'],
            'expiry'      => $invoice['expiry_date'] ?? null,
        ]);
    }

    // ── Xendit Webhook ────────────────────────────────

    public function xenditWebhook(Request $request)
    {
        if ($request->header('x-callback-token') !== config('services.xendit.webhook_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data   = $request->all();
        $status = $data['status'] ?? null;

        if (!in_array($status, ['PAID', 'SETTLED'])) {
            return response()->json(['message' => 'Ignored: ' . $status]);
        }

        $transaction = PaymentTransaction::where('xendit_invoice_id', $data['id'])->first();
        if (!$transaction) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $transaction->update(['status' => 'completed', 'processed_at' => now()]);

        $enrollment = $transaction->enrollment;
        if ($enrollment) {
            $enrollment->decrement('remaining_balance', $transaction->amount);
            $enrollment->increment('payment_amount', $transaction->amount);
            $fresh = $enrollment->fresh();

            // Create installment schedule if this is an installment plan and none exist yet
            if (in_array($fresh->payment_option, ['B', 'C', 'D']) || $fresh->payment_type === 'installment') {
                \App\Services\PaymentService::createInstallments($fresh);
                // Reconcile: mark the correct number of months as paid based on total paid
                \App\Services\PaymentService::reconcileInstallmentStatuses($fresh);
            }

            $this->advanceEnrollmentAfterPayment($fresh->fresh());
        }

        return response()->json(['message' => 'OK']);
    }

    private function xenditMethodsFor(string $method): array
    {
        return match ($method) {
            'gcash'   => ['GCASH'],
            'maya'    => ['PAYMAYA'],
            'grabpay' => ['GRABPAY'],
            'otc'     => ['SEVEN_ELEVEN', 'CEBUANA', 'PALAWAN', 'MLHUILLIER'],
            'bank'    => ['DD_BPI', 'DD_UBP', 'DD_RCBC', 'DD_CHINABANK'],
            default   => [],
        };
    }

    // Advance enrollment status and payment_status after any payment — mirrors Finance portal logic.
    private function advanceEnrollmentAfterPayment(Enrollment $enrollment): void
    {
        $totalPaid = (float) ($enrollment->payment_amount ?? 0);
        $totalFee  = (float) ($enrollment->total_fee ?? 0);

        // Update payment_status
        if ($totalFee > 0) {
            if ($totalPaid >= $totalFee) {
                $enrollment->update(['payment_status' => 'paid', 'remaining_balance' => 0]);
            } elseif ($totalPaid > 0) {
                $enrollment->update([
                    'payment_status'    => 'partial',
                    'remaining_balance' => max(0, $totalFee - $totalPaid),
                ]);
            }
        } elseif ($totalPaid > 0) {
            $remaining = (float) ($enrollment->remaining_balance ?? 0);
            $enrollment->update(['payment_status' => $remaining <= 0 ? 'paid' : 'partial']);
        }

        // Advance enrollment status to 'enrolled' and assign section (same as Finance)
        if (in_array($enrollment->status, ['approved', 'pending']) && $totalPaid > 0) {
            $enrollment->update(['status' => 'enrolled', 'enrolled_at' => now()]);

            $gradeLevel = $enrollment->grade_level ?? ($enrollment->student_data['grade_level'] ?? null);
            $schoolYear = $enrollment->school_year ?? (now()->year . '-' . (now()->year + 1));

            if ($gradeLevel && $enrollment->user_id) {
                $section = \App\Models\Section::where('grade_level', $gradeLevel)
                    ->where('school_year', $schoolYear)
                    ->where('is_active', true)
                    ->first();

                if ($section) {
                    $enrollment->update(['section' => $section->name]);
                    $exists = \Illuminate\Support\Facades\DB::table('section_student')
                        ->where('section_id', $section->id)
                        ->where('user_id', $enrollment->user_id)
                        ->exists();
                    if (!$exists) {
                        \Illuminate\Support\Facades\DB::table('section_student')->insert([
                            'section_id' => $section->id,
                            'user_id'    => $enrollment->user_id,
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
