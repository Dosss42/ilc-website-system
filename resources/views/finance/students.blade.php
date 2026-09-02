@extends('finance.layout')

@section('title', 'All Students')

@section('skeleton')
<div class="skel skel-header-title"></div>
<div class="skel skel-header-sub"></div>
<div class="skel-row-gap">
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
</div>
<div class="skel-row-gap">
    <div class="skel skel-stat-card" style="min-width:45%;height:76px;"></div>
    <div class="skel skel-stat-card" style="min-width:45%;height:76px;"></div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div style="display:flex;gap:12px;">
        <div class="skel skel-form-fld" style="flex:1;"></div>
        <div class="skel skel-form-fld" style="width:160px;"></div>
        <div class="skel skel-form-fld" style="width:150px;"></div>
    </div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
</div>
@endsection

@section('content')
<div class="section-header">
    <div>
        <h1><i class="bi bi-people-fill" style="color:var(--gold);"></i> All Students</h1>
        <p>Complete payment overview for all enrolled students — cash basis and installment plans</p>
    </div>
</div>

{{-- Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['paid'] }}</div>
            <div class="stat-label">Fully Paid</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $stats['partial'] + $stats['pending'] }}</div>
            <div class="stat-label">With Balance</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-currency-exchange"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($stats['total_balance'], 0) }}</div>
            <div class="stat-label">Total Outstanding</div>
        </div>
    </div>
</div>

{{-- Plan Breakdown Strip --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:24px;">
    <div style="background:#fff; border-radius:12px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.05); display:flex; align-items:center; gap:14px;">
        <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:20px;color:#16a34a;flex-shrink:0;">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:700;color:var(--blue);">{{ $stats['cash'] }}</div>
            <div style="font-size:12px;color:#666;">Plan A – Cash Basis students</div>
        </div>
    </div>
    <div style="background:#fff; border-radius:12px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.05); display:flex; align-items:center; gap:14px;">
        <div style="width:44px;height:44px;border-radius:10px;background:#e8f0fb;display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--blue);flex-shrink:0;">
            <i class="bi bi-calendar-month"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:700;color:var(--blue);">{{ $stats['installment'] }}</div>
            <div style="font-size:12px;color:#666;">Plans B/C/D – Installment students</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Students</h6>
    </div>
    <form method="GET" action="{{ route('finance.students.index') }}" style="padding:16px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:end;">
        <div style="flex:1; min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Search Student</label>
            <input type="text" name="search" value="{{ $search }}"
                placeholder="Name or email…"
                class="form-fld" style="padding:8px 12px; font-size:13px;">
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Plan Type</label>
            <select name="plan" class="form-select" style="font-size:13px; padding:8px 12px;">
                <option value="all"         {{ $planFilter === 'all'         ? 'selected' : '' }}>All Plans</option>
                <option value="cash"        {{ $planFilter === 'cash'        ? 'selected' : '' }}>Plan A – Cash</option>
                <option value="installment" {{ $planFilter === 'installment' ? 'selected' : '' }}>Plans B/C/D – Installment</option>
            </select>
        </div>
        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Payment Status</label>
            <select name="status" class="form-select" style="font-size:13px; padding:8px 12px;">
                <option value="all"     {{ $statusFilter === 'all'     ? 'selected' : '' }}>All Status</option>
                <option value="paid"    {{ $statusFilter === 'paid'    ? 'selected' : '' }}>Fully Paid</option>
                <option value="partial" {{ $statusFilter === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">School Year</label>
            <select name="school_year" class="form-select" style="font-size:13px; padding:8px 12px;">
                <option value="all" {{ $yearFilter === 'all' ? 'selected' : '' }}>All Years</option>
                @foreach($schoolYears as $yr)
                    <option value="{{ $yr }}" {{ $yearFilter === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn-dash btn-primary" style="padding:9px 18px; font-size:13px;">
                <i class="bi bi-search"></i> Search
            </button>
            <a href="{{ route('finance.students.index') }}" class="btn-dash btn-secondary" style="padding:9px 14px; font-size:13px;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Students Table --}}
<div class="content-card">
    <div class="content-card-header">
        <h6><i class="bi bi-table me-2" style="color:var(--gold);"></i>Student Payment Records</h6>
        <span style="font-size:12px;color:var(--muted);">{{ $stats['total'] }} student(s) found</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Grade</th>
                    <th>School Year</th>
                    <th>Plan</th>
                    <th>Total Fee</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allStudents as $s)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text);">{{ $s->user->name }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $s->user->email }}</div>
                    </td>
                    <td><span class="grade-chip">{{ ucfirst($s->grade_level) }}</span></td>
                    <td style="font-size:12px;color:var(--muted);">{{ $s->school_year }}</td>
                    <td>
                        @if($s->is_cash)
                            <span style="display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;border:1px solid #bbf7d0;">
                                <i class="bi bi-cash-stack"></i> Plan A
                            </span>
                        @elseif($s->payment_option !== '—')
                            <span style="display:inline-flex;align-items:center;gap:5px;background:#e8f0fb;color:var(--blue);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;border:1px solid #bfdbfe;">
                                <i class="bi bi-calendar-month"></i> Plan {{ $s->payment_option }}
                            </span>
                        @else
                            <span style="font-size:12px;color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="font-weight:600;color:var(--text);">
                        {{ $s->total_fee > 0 ? '₱' . number_format($s->total_fee, 2) : '—' }}
                    </td>
                    <td style="font-weight:600;color:#16a34a;">
                        {{ $s->amount_paid > 0 ? '₱' . number_format($s->amount_paid, 2) : '₱0.00' }}
                    </td>
                    <td>
                        @if($s->balance <= 0 && $s->amount_paid > 0)
                            <span style="color:#16a34a;font-weight:700;font-size:13px;">₱0.00</span>
                        @elseif($s->total_fee <= 0)
                            <span style="color:var(--muted);font-size:12px;">Not set</span>
                        @else
                            <span style="color:#dc3545;font-weight:700;font-size:13px;">₱{{ number_format($s->balance, 2) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($s->payment_status === 'paid')
                            <span style="display:inline-flex;align-items:center;gap:4px;background:#e8f5e9;color:#2e7d32;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">
                                <i class="bi bi-check-circle-fill"></i> Paid
                            </span>
                        @elseif($s->payment_status === 'partial')
                            <span style="display:inline-flex;align-items:center;gap:4px;background:#fff8e1;color:#f57c00;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">
                                <i class="bi bi-hourglass-split"></i> Partial
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:4px;background:#ffebee;color:#c62828;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">
                                <i class="bi bi-circle"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        @if($s->enrollment_id && $s->balance > 0)
                            <button type="button" class="btn-dash btn-primary"
                                style="padding:6px 14px;font-size:12px;border-radius:8px;"
                                onclick="openPayModal({{ $s->enrollment_id }}, '{{ addslashes($s->user->name) }}', {{ $s->balance }}, {{ $s->total_fee }}, {{ $s->amount_paid }}, '{{ $s->is_cash ? 'Plan A – Cash' : 'Plan ' . $s->payment_option }}')">
                                <i class="bi bi-cash-stack"></i> Pay
                            </button>
                        @elseif($s->balance <= 0 && $s->amount_paid > 0)
                            <span style="font-size:12px;color:#16a34a;font-weight:600;">
                                <i class="bi bi-check-circle-fill"></i> Settled
                            </span>
                        @else
                            <span style="font-size:12px;color:var(--muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:60px;color:var(--muted);">
                        <i class="bi bi-people" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.2;"></i>
                        <div style="font-size:15px;font-weight:600;margin-bottom:4px;">No Students Found</div>
                        <div style="font-size:12px;">Try adjusting your filters.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Walk-in Payment Modal --}}
<div class="modal fade" id="studPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 10px 40px rgba(0,0,0,.18);">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),#2471a3);border-radius:14px 14px 0 0;border:none;padding:18px 24px 14px;">
                <div>
                    <h5 class="modal-title text-white" style="font-weight:700;font-size:15px;margin:0;">
                        <i class="bi bi-cash-stack me-2"></i>Record Walk-in Payment
                    </h5>
                    <div style="font-size:11px;color:rgba(255,255,255,.7);margin-top:2px;" id="spModalPlan"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="studPayForm" method="POST" action="">
                @csrf
                <input type="hidden" name="installment_id" value="">
                <div class="modal-body" style="padding:22px 24px;">

                    {{-- Student summary --}}
                    <div style="background:#f0f4ff;border-radius:10px;padding:14px 16px;margin-bottom:18px;">
                        <div style="font-size:11px;color:#666;margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Student</div>
                        <div id="spModalName" style="font-weight:700;font-size:16px;color:var(--blue);"></div>
                        <div style="display:flex;gap:20px;margin-top:10px;font-size:12px;">
                            <div>
                                <div style="color:#888;">Total Fee</div>
                                <div id="spModalTotal" style="font-weight:700;color:var(--text);"></div>
                            </div>
                            <div>
                                <div style="color:#888;">Already Paid</div>
                                <div id="spModalPaid" style="font-weight:700;color:#16a34a;"></div>
                            </div>
                            <div>
                                <div style="color:#888;">Balance</div>
                                <div id="spModalBalance" style="font-weight:700;color:#dc3545;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment method --}}
                    <div style="margin-bottom:16px;">
                        <label style="font-size:13px;font-weight:600;color:#333;display:block;margin-bottom:8px;">Payment Method <span style="color:red;">*</span></label>
                        <div style="display:flex;gap:10px;">
                            <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;cursor:pointer;" id="spCashLabel">
                                <input type="radio" name="payment_method" value="cash" id="spCashRadio" onchange="spMethodChange()" style="accent-color:var(--blue);">
                                <span style="font-weight:600;font-size:13px;"><i class="bi bi-cash-stack me-1" style="color:#28a745;"></i>Cash</span>
                            </label>
                            <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;cursor:pointer;" id="spGcashLabel">
                                <input type="radio" name="payment_method" value="gcash" id="spGcashRadio" onchange="spMethodChange()" style="accent-color:var(--blue);">
                                <span style="font-weight:600;font-size:13px;"><i class="bi bi-phone me-1" style="color:#1976d2;"></i>GCash</span>
                            </label>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;font-weight:600;color:#333;display:block;margin-bottom:6px;">Amount (₱) <span style="color:red;">*</span></label>
                        <input type="number" name="amount" id="spAmount" step="0.01" min="1" required
                            style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:15px;font-weight:700;color:var(--blue);"
                            placeholder="0.00">
                    </div>

                    {{-- Reference --}}
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#333;display:block;margin-bottom:6px;">Reference No. <span style="font-weight:400;color:#999;">(optional)</span></label>
                        <input type="text" name="reference_number" id="spReference"
                            style="width:100%;padding:10px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:13px;"
                            placeholder="Receipt no. / GCash ref">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0;padding:14px 24px;gap:8px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;font-size:13px;">Cancel</button>
                    <button type="submit" id="spSubmitBtn"
                        style="background:linear-gradient(135deg,var(--blue),#2471a3);color:#fff;border:none;border-radius:8px;padding:9px 22px;font-weight:700;font-size:13px;cursor:pointer;">
                        <i class="bi bi-check-circle me-1"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const fmt = n => '₱' + parseFloat(n || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});

function openPayModal(enrollmentId, name, balance, totalFee, amountPaid, plan) {
    document.getElementById('spModalName').textContent    = name;
    document.getElementById('spModalPlan').textContent    = plan;
    document.getElementById('spModalTotal').textContent   = fmt(totalFee);
    document.getElementById('spModalPaid').textContent    = fmt(amountPaid);
    document.getElementById('spModalBalance').textContent = fmt(balance);
    document.getElementById('spAmount').value             = parseFloat(balance).toFixed(2);
    document.getElementById('spReference').value          = '';
    document.getElementById('spCashRadio').checked        = true;
    document.getElementById('spGcashRadio').checked       = false;
    spMethodChange();
    document.getElementById('studPayForm').action = '/finance/installments/' + enrollmentId + '/pay';
    new bootstrap.Modal(document.getElementById('studPayModal')).show();
}

function spMethodChange() {
    const isCash = document.getElementById('spCashRadio').checked;
    document.getElementById('spCashLabel').style.borderColor  = isCash  ? 'var(--blue)' : '#e0e0e0';
    document.getElementById('spGcashLabel').style.borderColor = !isCash ? 'var(--blue)' : '#e0e0e0';
}

document.getElementById('studPayForm').addEventListener('submit', function(e) {
    const method = document.querySelector('input[name="payment_method"]:checked');
    if (!method) { e.preventDefault(); alert('Please select a payment method.'); return; }
    const amount = parseFloat(document.getElementById('spAmount').value);
    if (!amount || amount <= 0) { e.preventDefault(); alert('Please enter a valid amount.'); return; }
    document.getElementById('spSubmitBtn').disabled    = true;
    document.getElementById('spSubmitBtn').textContent = 'Processing…';
});
</script>
@endsection
