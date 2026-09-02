@extends('finance.layout')

@section('title', 'Installments')

@section('skeleton')
<div class="skel skel-header-title"></div>
<div class="skel skel-header-sub"></div>
<div class="skel-row-gap">
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div style="display:flex;gap:12px;">
        <div class="skel skel-form-fld" style="flex:1;"></div>
        <div class="skel skel-form-fld" style="flex:1;"></div>
        <div class="skel skel-form-fld" style="flex:1;"></div>
        <div class="skel skel-form-fld" style="flex:1;"></div>
    </div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
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
        <h1><i class="bi bi-calendar-check-fill" style="color:var(--gold);"></i> Installments</h1>
        <p>View all student installment schedules and payment tracking</p>
    </div>
</div>

{{-- Stats Row --}}
@php
    $totalInstStudents  = $installmentEnrollments->total();
    $overdueInstStudents = $instStats['overdue'] ?? 0;
    $fullyPaidInst      = $instStats['fully_paid'] ?? 0;
    $partialPaidInst    = $instStats['partial'] ?? 0;
@endphp

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-value">{{ $totalInstStudents }}</div>
            <div class="stat-label">Installment Students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $overdueInstStudents }}</div>
            <div class="stat-label">Overdue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $fullyPaidInst }}</div>
            <div class="stat-label">Fully Paid</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $partialPaidInst }}</div>
            <div class="stat-label">Partially Paid</div>
        </div>
    </div>
</div>

@if(($totalLateFeesAll ?? 0) > 0)
<div style="background:#fff3e0; border:1px solid #ffe0b2; border-radius:10px; padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#e65100; font-size:18px;"></i>
    <div>
        <span style="font-weight:600; color:#e65100;">Late Fees Accumulated:</span>
        <span style="font-weight:700; color:#bf360c; font-size:16px;">₱{{ number_format($totalLateFeesAll, 2) }}</span>
        <span style="color:#666; font-size:12px; margin-left:8px;">across {{ $overdueInstStudents }} overdue student(s)</span>
    </div>
</div>
@endif

{{-- Filter Panel --}}
<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Installments</h6>
    </div>
    <div style="padding:16px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:end;">
        <div style="flex:1; min-width:140px;">
            <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">School Year</label>
            <select id="instFilterYear" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterInstallments()">
                <option value="all">All Years</option>
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1; min-width:140px;">
            <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Payment Status</label>
            <select id="instFilterStatus" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterInstallments()">
                <option value="all">All Status</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div style="flex:1; min-width:140px;">
            <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Overdue Status</label>
            <select id="instFilterOverdue" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterInstallments()">
                <option value="all">All Students</option>
                <option value="yes">Overdue</option>
                <option value="no">Not Overdue</option>
            </select>
        </div>
        <div style="flex:1; min-width:180px;">
            <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Search Student</label>
            <input type="text" id="instFilterSearch" class="form-control" placeholder="Search by name or email..." style="font-size:13px; padding:8px 12px;" oninput="filterInstallments()">
        </div>
        <div>
            <button type="button" class="btn-dash btn-secondary" onclick="resetInstallmentFilters()" style="padding:8px 16px; font-size:13px;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>
</div>

{{-- Installments Table --}}
<div class="content-card mb-4">
    <div class="content-card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h6><i class="bi bi-table me-2" style="color:var(--gold);"></i>Student Installments</h6>
        <span style="font-size:12px; color:var(--muted);" id="instRowCount">{{ $totalInstStudents }} student(s) on installment plans</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="dash-table" id="installmentsTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Grade Level</th>
                    <th>Payment Plan</th>
                    <th>Progress</th>
                    <th>Next Due Date / Amount</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($installmentEnrollments as $enrollment)
                @php
                    $totalPaid        = (float)($enrollment->payment_amount ?? 0);
                    $totalFee         = (float)($enrollment->total_fee ?? 0);
                    $progress         = $enrollment->installment_progress ?? ($totalFee > 0 ? min(100, $totalPaid / $totalFee * 100) : 0);
                    $isOverdue        = $enrollment->is_overdue ?? false;
                    $weeksOverdue     = $enrollment->weeks_overdue ?? 0;
                    $totalLateFees    = $enrollment->total_late_fees ?? 0;
                    $paidMonths       = $enrollment->paymentInstallments->where('status', 'paid')->count();
                    $totalMonths      = $enrollment->paymentInstallments->count();
                    $downpaymentAmount = (float)($enrollment->downpayment_amount ?? 0);
                    $downpaymentPaid  = $downpaymentAmount > 0 && $totalPaid >= $downpaymentAmount;
                    $isCashBasis      = $enrollment->payment_option === 'A'
                        || ($enrollment->payment_type === 'full' && !in_array($enrollment->payment_option, ['B','C','D']));
                    $balance          = max(0, $totalFee - $totalPaid);
                @endphp
                <tr style="{{ $isOverdue ? 'background:#fff8f8;' : '' }}"
                    data-status="{{ $enrollment->payment_status }}"
                    data-year="{{ $enrollment->school_year ?? '' }}"
                    data-overdue="{{ $isOverdue ? 'yes' : 'no' }}"
                    data-plan="{{ $isCashBasis ? 'cash' : 'installment' }}"
                    data-student="{{ strtolower($enrollment->user->name ?? '') }} {{ strtolower($enrollment->user->email ?? '') }}">
                    <td>
                        <div style="font-weight:600; color:var(--text);">{{ $enrollment->user->name ?? 'N/A' }}</div>
                        <div style="font-size:11px; color:var(--muted);">{{ $enrollment->user->email ?? '' }}</div>
                    </td>
                    <td><span class="grade-chip">{{ $enrollment->grade_level ?? 'N/A' }}</span></td>
                    <td>
                        @if($isCashBasis)
                            <div style="display:inline-flex;align-items:center;gap:5px;font-weight:700;color:#16a34a;background:#f0fdf4;padding:3px 10px;border-radius:20px;font-size:12px;">
                                <i class="bi bi-cash-stack"></i> Plan A – Cash
                            </div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">Full payment, ₱1,501 discount</div>
                        @else
                            <div style="font-weight:600; color:var(--blue);">Option {{ $enrollment->payment_option ?? 'N/A' }}</div>
                            <div style="font-size:11px; color:var(--muted);">
                                ₱{{ number_format($enrollment->monthly_amount ?? 0, 2) }}/month
                                @if($totalLateFees > 0)
                                    <span style="color:var(--red);">(+₱{{ number_format($totalLateFees, 0) }} fees)</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td style="width:180px;">
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                            <span style="font-weight:500;">
                                @if($isCashBasis)
                                    Full payment
                                @else
                                    @if($downpaymentAmount > 0)
                                        <span style="color:{{ $downpaymentPaid ? 'var(--green)' : 'var(--red)' }}; font-size:11px;"><i class="bi bi-{{ $downpaymentPaid ? 'check-circle' : 'circle' }}"></i> DP</span>
                                        <span style="margin:0 4px;">|</span>
                                    @endif
                                    {{ $paidMonths }}/{{ $totalMonths }} monthly
                                @endif
                            </span>
                            <span style="font-weight:600; color:{{ $progress >= 100 ? 'var(--green)' : 'var(--blue)' }};">{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div style="height:8px; background:#e8eaf0; border-radius:4px; overflow:hidden;">
                            <div class="installment-progress-bar"
                                 data-progress="{{ $progress }}"
                                 data-type="{{ $progress >= 100 ? 'complete' : ($isOverdue ? 'overdue' : 'normal') }}">
                            </div>
                        </div>
                        <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                            ₱{{ number_format($totalPaid, 0) }} of ₱{{ $totalFee > 0 ? number_format($totalFee, 0) : '—' }}
                        </div>
                    </td>
                    <td>
                        @if($isCashBasis)
                            @if($balance <= 0)
                                <span style="display:inline-flex;align-items:center;gap:4px;color:var(--green);font-weight:600;">
                                    <i class="bi bi-check-circle-fill"></i> Fully Paid
                                </span>
                            @else
                                <div style="font-weight:600; color:var(--text);">Remaining: ₱{{ number_format($balance, 2) }}</div>
                                <div style="font-size:11px; color:var(--muted);">Full payment upon enrollment</div>
                            @endif
                        @elseif($enrollment->next_due_date)
                            <div style="font-weight:600; color:{{ $isOverdue ? 'var(--red)' : 'var(--text)' }};">
                                {{ $enrollment->next_month_name ?? 'Monthly' }}: ₱{{ number_format($enrollment->next_due_amount ?? 0, 2) }}
                            </div>
                            <div style="font-size:11px; color:var(--muted);">Due: {{ $enrollment->next_due_date->format('M d, Y') }}</div>
                            @if($isOverdue)
                                <span style="display:inline-flex;align-items:center;gap:4px;margin-top:4px;padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;background:#ffebee;color:#c62828;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    {{ $weeksOverdue > 0 ? $weeksOverdue . 'w overdue' : 'Overdue' }}
                                </span>
                            @endif
                        @else
                            <span style="display:inline-flex;align-items:center;gap:4px;color:var(--green);font-weight:600;">
                                <i class="bi bi-check-circle-fill"></i> {{ $enrollment->next_month_name ?? 'Fully Paid' }}
                            </span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        @if($balance <= 0)
                            <div style="font-weight:700; color:var(--green);">
                                <i class="bi bi-check-circle-fill"></i> Fully Paid
                            </div>
                        @else
                            <div style="font-weight:700; color:{{ $isOverdue ? 'var(--red)' : 'var(--blue)' }}; font-size:14px;">
                                ₱{{ number_format($balance, 2) }}
                            </div>
                            <div style="font-size:11px; color:var(--muted);">remaining</div>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <button type="button" class="action-btn view js-view-installments" title="{{ $isCashBasis ? 'View Payment Summary' : 'View Installment Details' }}"
                            data-id="{{ $enrollment->id }}"
                            data-name="{{ htmlspecialchars($enrollment->user->name ?? 'N/A', ENT_QUOTES, 'UTF-8') }}"
                            data-grade="{{ $enrollment->grade_level ?? 'N/A' }}"
                            data-option="{{ $enrollment->payment_option ?? 'N/A' }}"
                            data-monthly="{{ $enrollment->monthly_amount ?? 0 }}"
                            data-downpayment="{{ $enrollment->downpayment_amount ?? 0 }}"
                            data-total-fee="{{ $totalFee }}"
                            data-total-paid="{{ $totalPaid }}"
                            data-is-cash="{{ $isCashBasis ? '1' : '0' }}"
                            onclick="showInstallmentModal(this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @if($balance > 0)
                        <button type="button" class="action-btn"
                            style="background:#e8f0fb;color:var(--blue);border:1px solid #bfdbfe;"
                            title="Record Walk-in Payment"
                            onclick="quickPayCash({{ $enrollment->id }}, {{ $balance }}, '{{ route('finance.installments.pay', $enrollment->id) }}')">
                            <i class="bi bi-cash-stack"></i>
                        </button>
                        @endif
                        @if($isOverdue)
                        <button type="button" class="action-btn"
                            style="background:#fff3e0;color:#e65100;border:1px solid #f5a623;"
                            title="Add Promissory Note"
                            onclick="openPromissoryModal({{ $enrollment->id }}, '{{ addslashes($enrollment->user->name ?? '') }}', {{ $balance }})">
                            <i class="bi bi-file-earmark-text"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr id="instEmptyRow">
                    <td colspan="7" style="text-align:center; color:var(--muted); padding:60px;">
                        <i class="bi bi-wallet2" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                        <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Payment Records</div>
                        <div style="font-size:12px;">Enrolled students with payment plans will appear here.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top" style="border-color:var(--border);">
        {{ $installmentEnrollments->links() }}
        <div class="pagination-info">
            Showing {{ $installmentEnrollments->firstItem() ?? 0 }} to {{ $installmentEnrollments->lastItem() ?? 0 }} of {{ $installmentEnrollments->total() }} installments
        </div>
    </div>
</div>

{{-- Installment Details Modal --}}
<div class="modal fade" id="installmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),var(--blue-light)); border-radius:12px 12px 0 0; border-bottom:none; padding:18px 24px;">
                <h5 class="modal-title text-white" style="font-weight:700; font-size:15px;">
                    <i class="bi bi-calendar-check me-2"></i>Payment Installment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px; max-height:75vh; overflow-y:auto;">
                <div id="installmentModalContent">
                    <div style="text-align:center; padding:40px; color:var(--muted);">
                        <div class="spinner-border spinner-border-sm me-2"></div>Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Walk-in Payment Modal --}}
<div class="modal fade" id="walkInPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.18);">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2c5282); border-radius:12px 12px 0 0; border-bottom:none; padding:18px 24px 14px;">
                <h5 class="modal-title text-white" style="font-weight:700; font-size:15px;">
                    <i class="bi bi-cash-stack me-2"></i>Record Walk-in Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="walkInPayForm" method="POST" action="">
                @csrf
                <input type="hidden" name="installment_id" id="wiInstallmentId">
                <div class="modal-body" style="padding:20px 24px;">
                    <div style="background:#f0f4ff; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
                        <div style="font-size:12px; color:#666; margin-bottom:2px;">Installment Month</div>
                        <div id="wiMonthLabel" style="font-weight:700; font-size:16px; color:#1e3a5f;"></div>
                        <div style="font-size:12px; color:#666; margin-top:4px;">
                            Amount Due: <strong id="wiAmountDue" style="color:#c62828;"></strong>
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px; font-weight:600; color:#333; display:block; margin-bottom:6px;">Payment Method <span class="text-danger">*</span></label>
                        <div style="display:flex; gap:10px;">
                            <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer;" id="wiCashLabel">
                                <input type="radio" name="payment_method" value="cash" id="wiCashRadio" style="accent-color:#1e3a5f;" onchange="wiMethodChange()">
                                <span style="font-weight:600; font-size:13px;"><i class="bi bi-cash-stack me-1" style="color:#28a745;"></i>Cash</span>
                            </label>
                            <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer;" id="wiGcashLabel">
                                <input type="radio" name="payment_method" value="gcash" id="wiGcashRadio" style="accent-color:#1e3a5f;" onchange="wiMethodChange()">
                                <span style="font-weight:600; font-size:13px;"><i class="bi bi-phone me-1" style="color:#1976d2;"></i>GCash</span>
                            </label>
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px; font-weight:600; color:#333; display:block; margin-bottom:6px;">Amount Paid (₱) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="wiAmount" step="0.01" min="1"
                            style="width:100%; padding:10px 14px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px; font-weight:600; color:#1e3a5f;"
                            placeholder="0.00" required>
                    </div>
                    <div style="margin-bottom:6px;">
                        <label style="font-size:13px; font-weight:600; color:#333; display:block; margin-bottom:6px;">Reference No. <span style="font-weight:400; color:#999;">(optional)</span></label>
                        <input type="text" name="reference_number" id="wiReference"
                            style="width:100%; padding:10px 14px; border:2px solid #e0e0e0; border-radius:8px; font-size:13px;"
                            placeholder="GCash ref / receipt no.">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:14px 24px; gap:8px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px; font-size:13px;">Cancel</button>
                    <button type="submit" style="background:#1e3a5f; color:#fff; border:none; border-radius:8px; padding:9px 22px; font-weight:700; font-size:13px; cursor:pointer;">
                        <i class="bi bi-check-circle me-1"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Promissory Note Modal --}}
<div class="modal fade" id="promissoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#e65100,#f5a623); color:#fff; border:0; border-radius:14px 14px 0 0; padding:18px 24px;">
                <div>
                    <h5 class="modal-title mb-0" style="font-weight:700; font-size:16px;">
                        <i class="bi bi-file-earmark-text me-2"></i>Promissory Note
                    </h5>
                    <div style="font-size:11px; opacity:0.85; margin-top:2px;">Record parent payment commitment</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" id="pn-enrollment-id">
                <div style="background:#f8f9fa; border-radius:8px; padding:12px 16px; margin-bottom:18px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase;">Student</div>
                    <div style="font-size:15px; font-weight:700; color:var(--blue);" id="pn-student-display">—</div>
                </div>
                <input type="hidden" id="pn-student-name">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-lbl">Outstanding Balance</label>
                        <input type="number" id="pn-amount-overdue" step="0.01" readonly class="form-fld" style="background:#f8f9fa;">
                    </div>
                    <div class="col-6">
                        <label class="form-lbl">Amount Promised <span style="color:red;">*</span></label>
                        <input type="number" id="pn-amount-promised" step="0.01" min="1" required class="form-fld">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-lbl">Promise Date <span style="color:red;">*</span></label>
                    <input type="date" id="pn-promise-date" required class="form-fld">
                </div>
                <div class="mb-3">
                    <label class="form-lbl">Parent / Guardian Name</label>
                    <input type="text" id="pn-guardian" placeholder="e.g. Juan dela Cruz" class="form-fld">
                </div>
                <div class="mb-2">
                    <label class="form-lbl">Remarks</label>
                    <textarea id="pn-remarks" rows="2" placeholder="Optional notes..." class="form-fld" style="resize:none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:14px 24px; gap:8px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="pnSaveBtn" onclick="savePromissoryNote()" style="background:linear-gradient(135deg,#e65100,#f5a623); color:#fff; border:none; border-radius:8px; padding:9px 22px; font-weight:700; font-size:13px; cursor:pointer;">
                    <i class="bi bi-file-earmark-check me-1"></i>Save Promissory Note
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── Server-side filter (plan type — requires page reload) ──
function serverFilter() {
    const plan = document.getElementById('instFilterPlan').value;
    const url  = new URL(window.location.href);
    url.searchParams.set('plan_type', plan);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// ── Client-side filter ──
function filterInstallments() {
    const year    = document.getElementById('instFilterYear').value.toLowerCase();
    const status  = document.getElementById('instFilterStatus').value.toLowerCase();
    const overdue = document.getElementById('instFilterOverdue').value.toLowerCase();
    const search  = document.getElementById('instFilterSearch').value.toLowerCase();
    const rows    = document.querySelectorAll('#installmentsTable tbody tr[data-student]');
    let visible   = 0;

    rows.forEach(row => {
        const rowYear    = (row.dataset.year    || '').toLowerCase();
        const rowStatus  = (row.dataset.status  || '').toLowerCase();
        const rowOverdue = (row.dataset.overdue || '').toLowerCase();
        const rowStudent = (row.dataset.student || '').toLowerCase();

        const matchYear    = year    === 'all' || rowYear    === year;
        const matchStatus  = status  === 'all' || rowStatus  === status;
        const matchOverdue = overdue === 'all' || rowOverdue === overdue;
        const matchSearch  = !search || rowStudent.includes(search);

        if (matchYear && matchStatus && matchOverdue && matchSearch) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    const countEl = document.getElementById('instRowCount');
    if (countEl) countEl.textContent = visible + ' student(s) found';

    const emptyRow = document.getElementById('instEmptyRow');
    if (emptyRow) emptyRow.style.display = visible === 0 ? '' : 'none';
}

function resetInstallmentFilters() {
    document.getElementById('instFilterYear').value    = 'all';
    document.getElementById('instFilterStatus').value  = 'all';
    document.getElementById('instFilterOverdue').value = 'all';
    document.getElementById('instFilterSearch').value  = '';
    filterInstallments();
}

// ── Quick cash pay (for Plan A or any enrollment without selecting an installment) ──
function quickPayCash(enrollmentId, balance, payRoute) {
    document.getElementById('wiInstallmentId').value = '';
    document.getElementById('wiMonthLabel').textContent = 'Payment';
    document.getElementById('wiAmountDue').textContent  = '₱' + parseFloat(balance).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('wiAmount').value           = parseFloat(balance).toFixed(2);
    document.getElementById('wiReference').value        = '';
    document.getElementById('wiCashRadio').checked      = true;
    document.getElementById('wiGcashRadio').checked     = false;
    wiMethodChange();
    document.getElementById('walkInPayForm').action = payRoute;
    new bootstrap.Modal(document.getElementById('walkInPayModal')).show();
}

// ── Progress bars ──
document.querySelectorAll('.installment-progress-bar').forEach(function(bar) {
    const progress = parseFloat(bar.dataset.progress) || 0;
    const type = bar.dataset.type;
    bar.style.height = '100%';
    bar.style.borderRadius = '4px';
    bar.style.width = progress + '%';
    bar.style.transition = 'width 0.3s';
    bar.style.background = type === 'complete' ? 'var(--green)' : type === 'overdue' ? 'var(--red)' : 'var(--blue)';
});

// ── Show Installment Modal (AJAX) ──
let _currentPayRoute = '';
let _currentCsrf     = '{{ csrf_token() }}';

function showInstallmentModal(btn) {
    const id          = btn.dataset.id;
    const name        = btn.dataset.name;
    const grade       = btn.dataset.grade;
    const option      = btn.dataset.option;
    const monthly     = parseFloat(btn.dataset.monthly)     || 0;
    const downpayment = parseFloat(btn.dataset.downpayment) || 0;
    const totalFee    = parseFloat(btn.dataset.totalFee)    || 0;
    const totalPaid   = parseFloat(btn.dataset.totalPaid)   || 0;
    const isCash      = btn.dataset.isCash === '1';

    _currentPayRoute = '/finance/installments/' + id + '/pay';

    // Plan A — show a simple payment summary instead of installment schedule
    if (isCash) {
        const balance = Math.max(0, totalFee - totalPaid);
        const fmt     = n => '₱' + (n || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
        const pct     = totalFee > 0 ? Math.min(100, Math.round(totalPaid / totalFee * 100)) : 0;
        document.getElementById('installmentModalContent').innerHTML = `
            <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:20px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <i class="bi bi-cash-stack" style="font-size:22px;color:#16a34a;"></i>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:#166534;">Plan A – Cash Basis</div>
                        <div style="font-size:12px;color:#4b7c5c;">Full payment with ₱1,501 discount</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;">
                    <div><strong>Name:</strong> ${name}</div>
                    <div><strong>Grade:</strong> ${grade}</div>
                    <div><strong>Total Fee:</strong> ${fmt(totalFee)}</div>
                    <div><strong>Amount Paid:</strong> <span style="color:#16a34a;font-weight:700;">${fmt(totalPaid)}</span></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;text-align:center;">
                <div style="background:#e3f2fd;padding:14px;border-radius:10px;">
                    <div style="font-size:11px;color:#666;">Total Fee</div>
                    <div style="font-weight:700;color:var(--blue);font-size:16px;">${fmt(totalFee)}</div>
                </div>
                <div style="background:#e8f5e9;padding:14px;border-radius:10px;">
                    <div style="font-size:11px;color:#666;">Paid</div>
                    <div style="font-weight:700;color:#16a34a;font-size:16px;">${fmt(totalPaid)}</div>
                </div>
                <div style="background:${balance <= 0 ? '#e8f5e9' : '#fff3e0'};padding:14px;border-radius:10px;">
                    <div style="font-size:11px;color:#666;">Balance</div>
                    <div style="font-weight:700;color:${balance <= 0 ? '#16a34a' : '#e65100'};font-size:16px;">${balance <= 0 ? 'Fully Paid' : fmt(balance)}</div>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                    <span style="font-weight:600;">Payment Progress</span>
                    <span style="font-weight:700;color:${pct >= 100 ? '#16a34a' : 'var(--blue)'};">${pct}%</span>
                </div>
                <div style="height:10px;background:#e8eaf0;border-radius:5px;overflow:hidden;">
                    <div style="height:100%;width:${pct}%;background:${pct >= 100 ? '#16a34a' : 'var(--blue)'};border-radius:5px;transition:width .4s;"></div>
                </div>
            </div>
            ${balance > 0 ? `
            <button type="button" onclick="openWalkInFromModal(0, 'Full Payment', ${balance})"
                style="width:100%;padding:12px;background:linear-gradient(135deg,#166534,#16a34a);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                <i class="bi bi-cash-stack"></i> Record Cash Payment (${fmt(balance)} remaining)
            </button>` : `
            <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:10px;color:#166534;font-weight:700;font-size:14px;">
                <i class="bi bi-check-circle-fill me-2"></i> Fully Paid – No outstanding balance
            </div>`}
        `;
        new bootstrap.Modal(document.getElementById('installmentModal')).show();
        return;
    }

    const fmt = n => '₱' + (n || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});

    // Show modal with loading spinner
    document.getElementById('installmentModalContent').innerHTML = `
        <div style="text-align:center; padding:40px; color:var(--muted);">
            <div class="spinner-border spinner-border-sm me-2"></div>Loading installment schedule...
        </div>`;
    new bootstrap.Modal(document.getElementById('installmentModal')).show();

    // Fetch installment details
    fetch('/finance/installments/' + id + '/details', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        const installments = data.installments || [];
        const dpPaid = totalPaid >= downpayment && downpayment > 0;

        let html = `
            <div style="background:linear-gradient(135deg,#f8f9fa,#e9ecef); border-radius:12px; padding:20px; margin-bottom:20px;">
                <h6 style="color:#2c3e50; margin-bottom:12px; font-weight:600;"><i class="bi bi-person-circle me-2"></i>Student Information</h6>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:13px;">
                    <div><strong>Name:</strong> ${name}</div>
                    <div><strong>Grade:</strong> ${grade}</div>
                    <div><strong>Payment Plan:</strong> Option ${option}</div>
                    <div><strong>Monthly:</strong> ${fmt(monthly)}</div>
                    <div><strong>Downpayment:</strong> ${fmt(downpayment)}</div>
                    <div><strong>Total Fee:</strong> ${fmt(totalFee)}</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px; text-align:center;">
                <div style="background:#e3f2fd; padding:10px; border-radius:8px;">
                    <div style="font-size:11px; color:#666;">Total Fee</div>
                    <div style="font-weight:700; color:var(--blue);">${fmt(totalFee)}</div>
                </div>
                <div style="background:#e8f5e9; padding:10px; border-radius:8px;">
                    <div style="font-size:11px; color:#666;">Amount Paid</div>
                    <div style="font-weight:700; color:var(--green);">${fmt(totalPaid)}</div>
                </div>
                <div style="background:#fff3e0; padding:10px; border-radius:8px;">
                    <div style="font-size:11px; color:#666;">Balance</div>
                    <div style="font-weight:700; color:#e65100;">${fmt(Math.max(0, totalFee - totalPaid))}</div>
                </div>
                <div style="background:#f3e5f5; padding:10px; border-radius:8px;">
                    <div style="font-size:11px; color:#666;">Monthly</div>
                    <div style="font-weight:700; color:#6a1b9a;">${fmt(monthly)}</div>
                </div>
            </div>
            <h6 style="font-weight:600; color:var(--blue); margin-bottom:12px;">
                <i class="bi bi-list-check me-2"></i>Monthly Installment Schedule
            </h6>
            <div style="max-height:380px; overflow-y:auto;">
        `;

        // Downpayment row
        if (downpayment > 0) {
            html += `
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; margin-bottom:8px;
                            background:${dpPaid ? '#e8f5e9' : '#fff3e0'}; border-radius:8px;
                            border-left:4px solid ${dpPaid ? '#4caf50' : '#ff9800'};">
                    <div>
                        <div style="font-weight:700;">Downpayment</div>
                        <div style="font-size:11px; color:#666;">One-time payment</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;">${fmt(downpayment)}</div>
                        <span style="font-size:11px; padding:2px 8px; border-radius:10px;
                                     background:${dpPaid ? '#c8e6c9' : '#ffe0b2'}; color:${dpPaid ? '#2e7d32' : '#e65100'};">
                            ${dpPaid ? 'Paid' : 'Pending'}
                        </span>
                    </div>
                </div>
            `;
        }

        if (installments.length === 0) {
            html += `<div style="text-align:center; padding:40px; color:#6c757d;">
                <i class="bi bi-calendar-x" style="font-size:48px; margin-bottom:16px; display:block; opacity:0.3;"></i>
                <div style="font-weight:600;">No installment schedule generated yet.</div>
                <div style="font-size:12px;margin-top:4px;">Installments are auto-created when a payment is first recorded.</div>
            </div>`;
        }

        installments.forEach(inst => {
            let statusColor, statusBg, statusText, statusIcon;
            const isInstOverdue = inst.status !== 'paid' && inst.status !== 'pending_approval' && inst.late_fee > 0;

            if (inst.status === 'paid') {
                statusColor = '#2e7d32'; statusBg = '#c8e6c9';
                statusText  = 'Paid' + (inst.paid_at ? ' (' + inst.paid_at + ')' : '');
                statusIcon  = 'bi-check-circle';
            } else if (inst.status === 'pending_approval') {
                statusColor = '#1565c0'; statusBg = '#bbdefb';
                statusText  = 'For Approval'; statusIcon = 'bi-hourglass-split';
            } else if (isInstOverdue) {
                statusColor = '#c62828'; statusBg = '#ffcdd2';
                statusText  = 'Overdue'; statusIcon = 'bi-exclamation-triangle';
            } else {
                statusColor = '#616161'; statusBg = '#e0e0e0';
                statusText  = 'Pending'; statusIcon = 'bi-circle';
            }

            const rowBg     = inst.status === 'paid'              ? '#e8f5e9'
                            : inst.status === 'pending_approval'  ? '#e3f2fd'
                            : isInstOverdue                       ? '#ffebee' : '#fafafa';
            const rowBorder = inst.status === 'paid'              ? '#c8e6c9'
                            : inst.status === 'pending_approval'  ? '#bbdefb'
                            : isInstOverdue                       ? '#ffcdd2' : '#e0e0e0';
            const canPay    = inst.status !== 'paid' && inst.status !== 'pending_approval';

            html += `
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; margin-bottom:6px;
                            background:${rowBg}; border-radius:8px; border:1px solid ${rowBorder};">
                    <div>
                        <div style="font-weight:600; display:flex; align-items:center; gap:8px;">
                            ${inst.month_name}
                            <span style="font-size:10px; padding:2px 8px; border-radius:10px; background:${statusBg}; color:${statusColor};">
                                <i class="bi ${statusIcon}"></i> ${statusText}
                            </span>
                        </div>
                        <div style="font-size:11px; color:#666;">Due: ${inst.due_date || 'N/A'}</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="text-align:right;">
                            <div style="font-weight:600; color:${inst.late_fee > 0 ? '#dc3545' : 'inherit'};">${fmt(inst.total_due)}</div>
                            ${inst.late_fee > 0 ? `<div style="font-size:10px; color:#dc3545;">Incl. ${fmt(inst.late_fee)} late fee</div>` : ''}
                            ${inst.status === 'paid' ? `<div style="font-size:10px; color:#28a745;">Paid: ${inst.paid_at || 'N/A'}</div>` : ''}
                        </div>
                        ${canPay ? `<button type="button"
                            onclick="openWalkInFromModal(${inst.id || 0}, '${(inst.month_name||'').replace(/'/g,"\\'")}', ${inst.total_due || 0})"
                            style="background:#1e3a5f; color:#fff; border:none; border-radius:6px; padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap;">
                            <i class="bi bi-cash-stack"></i> Pay
                        </button>` : ''}
                    </div>
                </div>
            `;
        });

        html += '</div>';
        document.getElementById('installmentModalContent').innerHTML = html;
    })
    .catch(err => {
        document.getElementById('installmentModalContent').innerHTML = `
            <div style="text-align:center; padding:40px; color:#dc3545;">
                <i class="bi bi-exclamation-circle" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                <div>Failed to load installment details. Please try again.</div>
            </div>`;
    });
}

// ── Walk-in from table row ──
function quickPay(installmentId, month, totalDue, payRoute) {
    document.getElementById('wiInstallmentId').value = installmentId;
    document.getElementById('wiMonthLabel').textContent = month;
    document.getElementById('wiAmountDue').textContent  = '₱' + parseFloat(totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('wiAmount').value           = parseFloat(totalDue).toFixed(2);
    document.getElementById('wiReference').value        = '';
    document.getElementById('wiCashRadio').checked      = true;
    document.getElementById('wiGcashRadio').checked     = false;
    wiMethodChange();
    document.getElementById('walkInPayForm').action = payRoute;
    new bootstrap.Modal(document.getElementById('walkInPayModal')).show();
}

// ── Walk-in from inside installment modal ──
function openWalkInFromModal(installmentId, month, totalDue) {
    document.getElementById('wiInstallmentId').value = installmentId;
    document.getElementById('wiMonthLabel').textContent = month;
    document.getElementById('wiAmountDue').textContent  = '₱' + parseFloat(totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('wiAmount').value           = parseFloat(totalDue).toFixed(2);
    document.getElementById('wiReference').value        = '';
    document.getElementById('wiCashRadio').checked      = true;
    document.getElementById('wiGcashRadio').checked     = false;
    wiMethodChange();
    document.getElementById('walkInPayForm').action = _currentPayRoute;

    const instModal = bootstrap.Modal.getInstance(document.getElementById('installmentModal'));
    if (instModal) instModal.hide();
    setTimeout(() => {
        new bootstrap.Modal(document.getElementById('walkInPayModal')).show();
    }, 300);
}

function wiMethodChange() {
    const isCash = document.getElementById('wiCashRadio').checked;
    document.getElementById('wiCashLabel').style.borderColor  = isCash  ? '#1e3a5f' : '#e0e0e0';
    document.getElementById('wiGcashLabel').style.borderColor = !isCash ? '#1e3a5f' : '#e0e0e0';
}

document.getElementById('walkInPayForm').addEventListener('submit', function(e) {
    const method = document.querySelector('input[name="payment_method"]:checked');
    if (!method) { e.preventDefault(); alert('Please select a payment method.'); return; }
    const amount = parseFloat(document.getElementById('wiAmount').value);
    if (!amount || amount <= 0) { e.preventDefault(); alert('Please enter a valid amount.'); return; }
});

// ── Promissory Note ──
function openPromissoryModal(enrollmentId, studentName, balance) {
    document.getElementById('pn-enrollment-id').value     = enrollmentId;
    document.getElementById('pn-student-name').value      = studentName;
    document.getElementById('pn-student-display').textContent = studentName;
    document.getElementById('pn-amount-overdue').value    = balance;
    document.getElementById('pn-amount-promised').value   = balance;
    document.getElementById('pn-promise-date').value      = '';
    document.getElementById('pn-guardian').value          = '';
    document.getElementById('pn-remarks').value           = '';
    new bootstrap.Modal(document.getElementById('promissoryModal')).show();
}

function savePromissoryNote() {
    const btn = document.getElementById('pnSaveBtn');
    btn.disabled    = true;
    btn.textContent = 'Saving...';

    fetch('/promissory-notes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            enrollment_id:   document.getElementById('pn-enrollment-id').value,
            amount_overdue:  document.getElementById('pn-amount-overdue').value,
            amount_promised: document.getElementById('pn-amount-promised').value,
            promise_date:    document.getElementById('pn-promise-date').value,
            parent_guardian: document.getElementById('pn-guardian').value,
            remarks:         document.getElementById('pn-remarks').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled    = false;
        btn.innerHTML   = '<i class="bi bi-file-earmark-check me-1"></i>Save Promissory Note';
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('promissoryModal')).hide();
            alert('Promissory note ' + (data.reference || '') + ' created successfully.');
        } else {
            alert(data.message || 'Failed to create promissory note.');
        }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-file-earmark-check me-1"></i>Save Promissory Note';
        alert('Network error. Please try again.');
    });
}
</script>
@endsection
