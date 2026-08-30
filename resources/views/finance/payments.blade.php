@extends('finance.layout')

@section('title', 'Payments')

@section('styles')
<style>
    .status-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600; }
    .status-badge.pending  { background:#fff3e0;color:#e65100; }
    .status-badge.approved { background:#e8f5e9;color:#2e7d32; }
    .status-badge.rejected { background:#ffebee;color:#c62828; }
    .nav-tabs .nav-link { font-size:13px;font-weight:600;border-radius:6px 6px 0 0;display:flex;align-items:center;gap:7px; }
    .nav-tabs .nav-link.active { color:var(--blue); }
</style>
@endsection

@section('content')
<div class="section-header">
    <div>
        <h1><i class="bi bi-credit-card-fill" style="color:var(--gold);"></i> Payments</h1>
        <p>View and manage all student payment records</p>
    </div>
</div>

{{-- Stats --}}
@php $cps = $combinedPayStats ?? ['total'=>0,'walkin_amount'=>0,'xendit_total'=>0,'xendit_amount'=>0]; @endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-receipt"></i></div>
        <div><div class="stat-value">{{ $cps['total'] }}</div><div class="stat-label">Total Payments</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
        <div><div class="stat-value">{{ $cps['pending'] }}</div><div class="stat-label">Pending Review</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div><div class="stat-value">{{ $cps['completed'] }}</div><div class="stat-label">Completed</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div><div class="stat-value">{{ $cps['rejected'] }}</div><div class="stat-label">Rejected</div></div>
    </div>
</div>

{{-- Filters --}}
@php
    $currentYear = now()->year;
    $schoolYears = $schoolYears ?? [];
@endphp
<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Payments</h6>
    </div>
    <div style="padding:16px 20px;display:flex;flex-wrap:wrap;gap:12px;align-items:end;">
        <div style="flex:1;min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">School Year</label>
            <select id="payFilterYear" class="form-select" style="font-size:13px;padding:8px 12px;">
                <option value="all">All Years</option>
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Status</label>
            <select id="payFilterStatus" class="form-select" style="font-size:13px;padding:8px 12px;">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Payment Method</label>
            <select id="payFilterMethod" class="form-select" style="font-size:13px;padding:8px 12px;">
                <option value="all">All Methods</option>
                <option value="gcash">GCash</option>
                <option value="cash">Cash</option>
            </select>
        </div>
        <div style="flex:1;min-width:180px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;display:block;">Search Student</label>
            <input type="text" id="payFilterSearch" class="form-control" placeholder="Search by name or email..." style="font-size:13px;padding:8px 12px;">
        </div>
        <div>
            <button type="button" class="btn-dash btn-secondary" onclick="resetPaymentFilters()" style="padding:8px 16px;font-size:13px;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>
</div>

{{-- Tabbed Payment Tables --}}
<div class="content-card mb-4" style="overflow:hidden;">
    {{-- Tab nav --}}
    <ul class="nav nav-tabs mb-0" style="padding:0 16px;background:#f8f9fa;border-bottom:1px solid #dee2e6;margin:0;">
        <li class="nav-item">
            <button type="button" id="pmtTab-walkin" class="nav-link active" onclick="switchPmtTab('walkin')"
                style="font-size:13px;font-weight:600;border-radius:6px 6px 0 0;display:flex;align-items:center;gap:7px;">
                <i class="bi bi-cash-stack"></i> Cash Transactions
                <span class="badge bg-primary" style="font-size:10px;">{{ isset($walkInTransactions) ? $walkInTransactions->total() : 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" id="pmtTab-xendit" class="nav-link" onclick="switchPmtTab('xendit')"
                style="font-size:13px;font-weight:600;border-radius:6px 6px 0 0;display:flex;align-items:center;gap:7px;">
                <i class="bi bi-credit-card-2-front"></i> Online (Xendit)
                <span class="badge bg-success" style="font-size:10px;">{{ isset($xenditTransactions) ? $xenditTransactions->total() : 0 }}</span>
            </button>
        </li>
    </ul>

    @php $walkInTotalAmount = ($walkInTransactions ?? collect())->sum('amount'); @endphp
    <div id="pmtPanel-walkin">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #f0f0f0;">
            <h6 style="margin:0;font-size:14px;font-weight:700;"><i class="bi bi-building me-2" style="color:var(--blue);"></i>Walk-in Payment Transactions</h6>
            <span style="font-size:12px;color:var(--muted);">Total Amount: ₱{{ number_format($walkInTotalAmount, 2) }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dash-table" id="walkInPaymentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Grade Level</th>
                        <th>Installment / Amount</th>
                        <th>Method</th>
                        <th>Processed By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($walkInTransactions ?? collect()) as $walkInTx)
                    @php $walkInInstallment = $walkInTx->installment; @endphp
                    <tr data-status="{{ $walkInTx->status }}" data-student="{{ strtolower($walkInTx->user->name ?? '') }}">
                        <td style="font-weight:600;">#{{ $walkInTx->id }}</td>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $walkInTx->user->name ?? 'N/A' }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $walkInTx->user->email ?? '' }}</div>
                        </td>
                        <td><span class="grade-chip">{{ $walkInTx->enrollment->grade_level ?? 'N/A' }}</span></td>
                        <td>
                            @if($walkInInstallment)
                                <div style="font-weight:600;color:var(--blue);">{{ $walkInInstallment->month_name }} Installment</div>
                                <div style="font-size:12px;color:var(--muted);">₱{{ number_format($walkInInstallment->amount ?? 0, 2) }}</div>
                                <div style="font-size:11px;color:var(--green);font-weight:600;">Total: ₱{{ number_format($walkInTx->amount, 2) }}</div>
                            @else
                                <div style="font-weight:600;color:var(--blue);">{{ $walkInTx->installment_month ?? 'Downpayment' }}</div>
                                <div style="font-size:11px;color:var(--green);font-weight:600;">₱{{ number_format($walkInTx->amount, 2) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($walkInTx->payment_method === 'gcash')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:500;background:#e3f2fd;color:#1565c0;">
                                    <i class="bi bi-phone"></i> GCash
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:500;background:#e8f5e9;color:#2e7d32;">
                                    <i class="bi bi-cash-stack"></i> Cash
                                </span>
                            @endif
                        </td>
                        <td><div style="font-size:12px;color:var(--text);">{{ $walkInTx->processedBy->name ?? 'System' }}</div></td>
                        <td style="white-space:nowrap;">
                            {{ $walkInTx->created_at->format('M d, Y') }}<br>
                            <span style="font-size:11px;color:var(--muted);">{{ $walkInTx->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            @if($walkInTx->status === 'completed')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;">
                                    <i class="bi bi-check-circle"></i> Completed
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#fff3e0;color:#e65100;">
                                    <i class="bi bi-hourglass-split"></i> {{ ucfirst($walkInTx->status) }}
                                </span>
                            @endif
                            @if($walkInTx->payment_type === 'admin')
                                <br><span style="display:inline-flex;align-items:center;margin-top:3px;padding:2px 6px;border-radius:5px;font-size:10px;background:#e3f2fd;color:#1565c0;border:1px solid #90caf9;">
                                    <i class="bi bi-building me-1"></i>Cashier
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($walkInTx->status === 'completed')
                                <button type="button" class="action-btn" title="Print Official Receipt" style="background:#e8f5e9;color:#2e7d32;"
                                    onclick="printOfficialReceipt({
                                        or_no: 'OR-{{ str_pad($walkInTx->id, 6, '0', STR_PAD_LEFT) }}',
                                        date: '{{ $walkInTx->created_at->format('F d, Y') }}',
                                        time: '{{ $walkInTx->created_at->format('h:i A') }}',
                                        student_name: '{{ addslashes($walkInTx->user->name ?? 'N/A') }}',
                                        grade_level: '{{ $walkInTx->enrollment->grade_level ?? 'N/A' }}',
                                        school_year: '{{ $walkInTx->enrollment->school_year ?? 'N/A' }}',
                                        description: '{{ addslashes($walkInInstallment ? ($walkInInstallment->month_name.' Installment') : ($walkInTx->installment_month ?? 'Downpayment')) }}',
                                        amount: '{{ number_format($walkInTx->amount, 2) }}',
                                        method: '{{ ucfirst($walkInTx->payment_method ?? 'Cash') }}',
                                        received_by: '{{ addslashes($walkInTx->processedBy->name ?? 'Admin') }}',
                                        type: 'walkin'
                                    })">
                                    <i class="bi bi-printer-fill"></i>
                                </button>
                            @else
                                <span style="font-size:11px;color:var(--muted);">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;color:var(--muted);padding:60px;">
                            <i class="bi bi-building" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.2;"></i>
                            <div style="font-size:15px;font-weight:600;margin-bottom:4px;">No Walk-in Transactions</div>
                            <div style="font-size:12px;">Walk-in payment records will appear here once processed at the cashier.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            @if(isset($walkInTransactions)) {{ $walkInTransactions->links() }} @endif
            @if(isset($walkInTransactions) && $walkInTransactions->count() > 0)
                <div class="pagination-info">Showing {{ $walkInTransactions->firstItem() }} to {{ $walkInTransactions->lastItem() }} of {{ $walkInTransactions->total() }} walk-in transactions</div>
            @endif
        </div>
    </div>{{-- /pmtPanel-walkin --}}

    {{-- Panel: Xendit / Online Transactions --}}
    @php $xenditTotalAmount = ($xenditTransactions ?? collect())->where('status','completed')->sum('amount'); @endphp
    <div id="pmtPanel-xendit" style="display:none;">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #f0f0f0;">
            <h6 style="margin:0;font-size:14px;font-weight:700;"><i class="bi bi-credit-card-2-front me-2" style="color:var(--green,#28a745);"></i>Xendit / Online Payment Transactions</h6>
            <span style="font-size:12px;color:var(--muted);">Collected: ₱{{ number_format($combinedPayStats['xendit_amount'] ?? 0, 2) }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Grade Level</th>
                        <th>Description / Amount</th>
                        <th>Method</th>
                        <th>Invoice ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($xenditTransactions ?? collect()) as $xTx)
                    <tr>
                        <td style="font-weight:600;">#{{ $xTx->id }}</td>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $xTx->user->name ?? 'N/A' }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ $xTx->user->email ?? '' }}</div>
                        </td>
                        <td><span class="grade-chip">{{ $xTx->enrollment->grade_level ?? 'N/A' }}</span></td>
                        <td>
                            <div style="font-weight:600;color:var(--blue);">{{ $xTx->description ?? 'Online Payment' }}</div>
                            <div style="font-size:11px;color:var(--green,#28a745);font-weight:600;">₱{{ number_format($xTx->amount, 2) }}</div>
                        </td>
                        <td>
                            @php $methodLabels = ['gcash'=>['GCash','bi-phone','#e3f2fd','#1565c0'],'maya'=>['Maya','bi-wallet','#f3e5f5','#6a1b9a'],'grabpay'=>['GrabPay','bi-bag','#e8f5e9','#1b5e20'],'bank'=>['Bank','bi-bank','#fff3e0','#e65100'],'otc'=>['OTC','bi-building','#fce4ec','#880e4f']]; $ml = $methodLabels[$xTx->payment_method] ?? [ucfirst($xTx->payment_method),'bi-credit-card','#f5f5f5','#333']; @endphp
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:500;background:{{ $ml[2] }};color:{{ $ml[3] }};">
                                <i class="bi {{ $ml[1] }}"></i> {{ $ml[0] }}
                            </span>
                        </td>
                        <td style="font-size:11px;color:var(--muted);">{{ $xTx->xendit_invoice_id ? Str::limit($xTx->xendit_invoice_id, 20) : '—' }}</td>
                        <td style="white-space:nowrap;">
                            {{ $xTx->created_at->format('M d, Y') }}<br>
                            <span style="font-size:11px;color:var(--muted);">{{ $xTx->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            @if($xTx->status === 'completed')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;">
                                    <i class="bi bi-check-circle"></i> Completed
                                </span>
                            @elseif($xTx->status === 'pending')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#fff3e0;color:#e65100;">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#666;">
                                    {{ ucfirst($xTx->status) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($xTx->xendit_invoice_url)
                                <a href="{{ $xTx->xendit_invoice_url }}" target="_blank"
                                    title="Open Xendit Invoice"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;background:#e8f5e9;color:#2e7d32;text-decoration:none;">
                                    <i class="bi bi-box-arrow-up-right"></i> Invoice
                                </a>
                            @else
                                <span style="font-size:11px;color:var(--muted);">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;color:var(--muted);padding:60px;">
                            <i class="bi bi-credit-card-2-front" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.2;"></i>
                            <div style="font-size:15px;font-weight:600;margin-bottom:4px;">No Xendit Transactions Yet</div>
                            <div style="font-size:12px;">Online payments via GCash, Maya, GrabPay will appear here once processed.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            @if(isset($xenditTransactions)) {{ $xenditTransactions->links() }} @endif
            @if(isset($xenditTransactions) && $xenditTransactions->count() > 0)
                <div class="pagination-info">Showing {{ $xenditTransactions->firstItem() }} to {{ $xenditTransactions->lastItem() }} of {{ $xenditTransactions->total() }} Xendit transactions</div>
            @endif
        </div>
    </div>{{-- /pmtPanel-xendit --}}
</div>{{-- /tabbed card --}}

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#ffebee;border-bottom:none;">
                <h5 class="modal-title" style="color:#c62828;"><i class="bi bi-x-circle"></i> Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="color:#666;margin-bottom:16px;">Please provide a reason for rejecting this payment:</p>
                    <textarea name="reject_reason" class="form-control" rows="3" placeholder="Enter rejection reason..." required style="border-radius:8px;"></textarea>
                </div>
                <div class="modal-footer" style="border-top:none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Generic Confirm Modal --}}
<div class="modal fade" id="financeConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" id="financeConfirmHeader" style="border-radius:12px 12px 0 0;border-bottom:none;padding:20px 24px 12px;">
                <h5 class="modal-title" id="financeConfirmTitle" style="font-weight:700;font-size:16px;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:8px 24px 20px;">
                <p id="financeConfirmMessage" style="color:#555;margin:0;font-size:14px;"></p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;padding:12px 24px;gap:8px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" id="financeConfirmOk" class="btn text-white" style="border-radius:8px;font-weight:600;">Confirm</button>
            </div>
        </div>
    </div>
</div>

{{-- Payment Details Modal --}}
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),var(--blue-light));border-radius:12px 12px 0 0;">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-credit-card me-2"></i>Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color:var(--blue);font-weight:600;margin-bottom:16px;"><i class="bi bi-person me-2"></i>Student Information</h6>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Student Name</div><div id="detailStudentName" style="font-weight:600;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Email</div><div id="detailStudentEmail" style="font-weight:500;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Grade Level</div><div id="detailGradeLevel" style="font-weight:500;"></div></div>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color:var(--blue);font-weight:600;margin-bottom:16px;"><i class="bi bi-receipt me-2"></i>Payment Information</h6>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Payment ID</div><div id="detailPaymentId" style="font-weight:600;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Description</div><div id="detailDescription" style="font-weight:500;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Payment Method</div><div id="detailMethod" style="font-weight:500;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Submitted On</div><div id="detailSubmitted" style="font-weight:500;"></div></div>
                        <div style="margin-bottom:12px;"><div style="font-size:12px;color:#666;">Status</div><div id="detailStatus"></div></div>
                    </div>
                </div>
                <hr style="margin:20px 0;">
                <div id="installmentDetailsSection" style="display:none;">
                    <h6 style="color:var(--blue);font-weight:600;margin-bottom:16px;"><i class="bi bi-calendar-check me-2"></i>Installment Details</h6>
                    <div class="row">
                        <div class="col-md-4"><div style="background:#e3f2fd;padding:16px;border-radius:8px;text-align:center;"><div style="font-size:12px;color:#666;margin-bottom:4px;">Month</div><div id="detailMonthName" style="font-weight:700;color:var(--blue);font-size:16px;"></div></div></div>
                        <div class="col-md-4"><div style="background:#e8f5e9;padding:16px;border-radius:8px;text-align:center;"><div style="font-size:12px;color:#666;margin-bottom:4px;">Base Amount</div><div id="detailBaseAmount" style="font-weight:700;color:#28a745;font-size:16px;"></div></div></div>
                        <div class="col-md-4"><div style="background:#fff3e0;padding:16px;border-radius:8px;text-align:center;"><div style="font-size:12px;color:#666;margin-bottom:4px;">Total with Fees</div><div id="detailTotalAmount" style="font-weight:700;color:#f57c00;font-size:16px;"></div></div></div>
                    </div>
                    <div id="lateFeeRow" style="margin-top:12px;padding:12px;background:#ffebee;border-radius:8px;display:none;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#c62828;"><i class="bi bi-exclamation-triangle me-2"></i>Late Fee Applied</span>
                            <span id="detailLateFee" style="font-weight:700;color:#c62828;"></span>
                        </div>
                    </div>
                </div>
                <div id="enrollmentSummarySection" style="display:none;margin-top:20px;">
                    <h6 style="color:var(--blue);font-weight:600;margin-bottom:16px;"><i class="bi bi-person-badge me-2"></i>Enrollment Summary</h6>
                    <div style="background:#f8f9fa;padding:16px;border-radius:8px;">
                        <div class="row">
                            <div class="col-md-3" style="text-align:center;"><div style="font-size:12px;color:#666;">Payment Plan</div><div id="detailPaymentOption" style="font-weight:700;color:var(--blue);"></div></div>
                            <div class="col-md-3" style="text-align:center;"><div style="font-size:12px;color:#666;">Total Fee</div><div id="detailTotalFee" style="font-weight:700;"></div></div>
                            <div class="col-md-3" style="text-align:center;"><div style="font-size:12px;color:#666;">Amount Paid</div><div id="detailAmountPaid" style="font-weight:700;color:#28a745;"></div></div>
                            <div class="col-md-3" style="text-align:center;"><div style="font-size:12px;color:#666;">Balance</div><div id="detailBalance" style="font-weight:700;color:#e65100;"></div></div>
                        </div>
                    </div>
                </div>
                <div id="reviewInfoSection" style="display:none;margin-top:20px;">
                    <h6 style="color:var(--blue);font-weight:600;margin-bottom:16px;"><i class="bi bi-check2-square me-2"></i>Review Information</h6>
                    <div style="background:#f8f9fa;padding:16px;border-radius:8px;">
                        <div style="margin-bottom:8px;"><span style="color:#666;">Reviewed By:</span><span id="detailReviewedBy" style="font-weight:600;margin-left:8px;"></span></div>
                        <div><span style="color:#666;">Reviewed On:</span><span id="detailReviewedAt" style="font-weight:600;margin-left:8px;"></span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e0e0e0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="detailViewScreenshot" href="#" class="btn btn-success" style="display:none;"><i class="bi bi-image me-1"></i>View GCash Screenshot</a>
            </div>
        </div>
    </div>
</div>

{{-- Screenshot Modal --}}
<div class="modal fade" id="screenshotModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),var(--blue-light));border-radius:12px 12px 0 0;">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-image me-2"></i>Payment Screenshot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:24px;">
                <img id="screenshotImage" src="" alt="Payment Screenshot" style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                <div style="margin-top:16px;">
                    <a id="screenshotDownload" href="" download class="btn btn-secondary"><i class="bi bi-download me-1"></i>Download</a>
                    <a id="screenshotOpen" href="" target="_blank" class="btn btn-primary ms-2"><i class="bi bi-box-arrow-up-right me-1"></i>Open in New Tab</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── Tab switching ── */
function switchPmtTab(tab) {
    ['walkin','xendit'].forEach(t => {
        document.getElementById('pmtPanel-' + t).style.display = tab === t ? '' : 'none';
        document.getElementById('pmtTab-' + t).classList.toggle('active', tab === t);
    });
}

/* ── Filters ── */
function resetPaymentFilters() {
    ['payFilterYear','payFilterStatus','payFilterMethod'].forEach(id => { const el=document.getElementById(id); if(el) el.value='all'; });
    const s=document.getElementById('payFilterSearch'); if(s) s.value='';
    filterPayments();
}

function filterPayments() {
    const year   = (document.getElementById('payFilterYear')?.value   || 'all').toLowerCase();
    const status = (document.getElementById('payFilterStatus')?.value || 'all').toLowerCase();
    const method = (document.getElementById('payFilterMethod')?.value || 'all').toLowerCase();
    const search = (document.getElementById('payFilterSearch')?.value || '').toLowerCase();
    document.querySelectorAll('#paymentsTable tbody tr[data-status]').forEach(row => {
        const matchYear   = year   === 'all' || row.dataset.year   === year;
        const matchStatus = status === 'all' || row.dataset.status === status;
        const matchMethod = method === 'all' || row.dataset.method === method;
        const matchSearch = !search || row.dataset.student.includes(search);
        row.style.display = (matchYear && matchStatus && matchMethod && matchSearch) ? '' : 'none';
    });
}
['payFilterYear','payFilterStatus','payFilterMethod'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', filterPayments);
});
document.getElementById('payFilterSearch')?.addEventListener('input', filterPayments);

/* ── Reject Modal ── */
function showRejectModal(paymentId) {
    document.getElementById('rejectForm').action = '/finance/payments/' + paymentId + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

/* ── Screenshot Modal ── */
function showScreenshotModal(url) {
    document.getElementById('screenshotImage').src   = url;
    document.getElementById('screenshotDownload').href = url;
    document.getElementById('screenshotOpen').href   = url;
    new bootstrap.Modal(document.getElementById('screenshotModal')).show();
}

/* ── Payment Details Modal ── */
document.querySelectorAll('.js-view-payment').forEach(btn => {
    btn.addEventListener('click', function() {
        const installment = this.dataset.installment ? JSON.parse(this.dataset.installment) : null;
        document.getElementById('detailPaymentId').textContent    = '#' + this.dataset.paymentId;
        document.getElementById('detailStudentName').textContent  = this.dataset.studentName;
        document.getElementById('detailStudentEmail').textContent = this.dataset.email;
        document.getElementById('detailGradeLevel').textContent   = this.dataset.grade;
        document.getElementById('detailDescription').textContent  = this.dataset.description;
        document.getElementById('detailSubmitted').textContent    = this.dataset.submitted;
        document.getElementById('detailMethod').innerHTML = this.dataset.method === 'gcash'
            ? '<i class="bi bi-phone"></i> GCash' : '<i class="bi bi-cash-stack"></i> Cash';

        const status = this.dataset.status;
        const statusEl = document.getElementById('detailStatus');
        const styles = {pending:'#fff3e0;color:#e65100', approved:'#e8f5e9;color:#2e7d32', rejected:'#ffebee;color:#c62828'};
        const icons  = {pending:'hourglass-split', approved:'check-circle', rejected:'x-circle'};
        const labels = {pending:'Pending', approved:'Approved', rejected:'Rejected'};
        statusEl.innerHTML = `<span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:500;background:${styles[status] ?? styles.pending}"><i class="bi bi-${icons[status] ?? icons.pending}"></i> ${labels[status] ?? status}</span>`;

        const instSection = document.getElementById('installmentDetailsSection');
        if (installment) {
            instSection.style.display = 'block';
            document.getElementById('detailMonthName').textContent   = installment.month_name || 'N/A';
            document.getElementById('detailBaseAmount').textContent  = '₱' + Number(installment.amount||0).toLocaleString('en-PH',{minimumFractionDigits:2});
            document.getElementById('detailTotalAmount').textContent = '₱' + Number(this.dataset.totalAmount).toLocaleString('en-PH',{minimumFractionDigits:2});
            const lateFeeRow = document.getElementById('lateFeeRow');
            lateFeeRow.style.display = installment.late_fee > 0 ? 'block' : 'none';
            if (installment.late_fee > 0) document.getElementById('detailLateFee').textContent = '₱' + Number(installment.late_fee).toLocaleString('en-PH',{minimumFractionDigits:2});
        } else { instSection.style.display = 'none'; }

        const enrollSection = document.getElementById('enrollmentSummarySection');
        if (this.dataset.hasEnrollment === 'true') {
            enrollSection.style.display = 'block';
            document.getElementById('detailPaymentOption').textContent = 'Option ' + this.dataset.paymentOption;
            document.getElementById('detailTotalFee').textContent      = '₱' + Number(this.dataset.totalFee).toLocaleString('en-PH',{minimumFractionDigits:2});
            document.getElementById('detailAmountPaid').textContent    = '₱' + Number(this.dataset.amountPaid).toLocaleString('en-PH',{minimumFractionDigits:2});
            document.getElementById('detailBalance').textContent       = '₱' + Number(this.dataset.balance).toLocaleString('en-PH',{minimumFractionDigits:2});
        } else { enrollSection.style.display = 'none'; }

        const reviewSection = document.getElementById('reviewInfoSection');
        if (status !== 'pending') {
            reviewSection.style.display = 'block';
            document.getElementById('detailReviewedBy').textContent = this.dataset.reviewedBy;
            document.getElementById('detailReviewedAt').textContent = this.dataset.reviewedAt;
        } else { reviewSection.style.display = 'none'; }

        const ssLink = document.getElementById('detailViewScreenshot');
        const ssUrl = this.dataset.screenshotUrl;
        if (ssUrl) {
            ssLink.style.display = '';
            ssLink.onclick = (e) => { e.preventDefault(); showScreenshotModal(ssUrl); };
        } else { ssLink.style.display = 'none'; }

        new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
    });
});

/* ── Screenshot buttons ── */
document.querySelectorAll('.js-view-screenshot').forEach(btn => {
    btn.addEventListener('click', function() { showScreenshotModal(this.dataset.screenshotUrl); });
});

/* ── Generic Confirm Modal ── */
let _confirmFormId = null;
function showFinanceConfirm(btn) {
    _confirmFormId = btn.dataset.confirmForm;
    const color = btn.dataset.confirmColor || '#1565c0';
    document.getElementById('financeConfirmTitle').textContent   = btn.dataset.confirmTitle;
    document.getElementById('financeConfirmMessage').textContent = btn.dataset.confirmMessage;
    document.getElementById('financeConfirmHeader').style.background = color + '18';
    document.getElementById('financeConfirmTitle').style.color   = color;
    document.getElementById('financeConfirmOk').style.background  = color;
    document.getElementById('financeConfirmOk').style.borderColor = color;
    new bootstrap.Modal(document.getElementById('financeConfirmModal')).show();
}
document.getElementById('financeConfirmOk').addEventListener('click', function() {
    if (_confirmFormId) {
        bootstrap.Modal.getInstance(document.getElementById('financeConfirmModal')).hide();
        document.getElementById(_confirmFormId).submit();
    }
});

/* ── Print Official Receipt ── */
function printOfficialReceipt(data) {
    const w = window.open('', '_blank', 'width=700,height=600');
    w.document.write(`<!DOCTYPE html><html><head><title>Official Receipt</title>
    <style>body{font-family:'Open Sans',Arial,sans-serif;margin:0;padding:30px;}
    .receipt{max-width:600px;margin:0 auto;border:2px solid #1e3a5f;border-radius:10px;overflow:hidden;}
    .r-header{background:#1e3a5f;color:#fff;padding:20px 24px;text-align:center;}
    .r-header h2{margin:0;font-size:20px;}.r-header p{margin:4px 0 0;font-size:12px;opacity:.85;}
    .r-body{padding:20px 24px;}.r-row{display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px;}
    .r-label{color:#666;font-weight:600;}.r-value{font-weight:700;color:#1e3a5f;}
    .r-divider{border-top:1px solid #e0e0e0;margin:14px 0;}
    .r-total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;background:#f0f4ff;padding:12px;border-radius:6px;}
    .r-footer{text-align:center;font-size:11px;color:#888;padding:14px 24px;background:#f8f9fa;}
    @media print{button{display:none!important;}}</style></head><body>
    <div class="receipt">
        <div class="r-header">
            <h2>IEMELIF Learning Center</h2>
            <p>Official Receipt</p>
        </div>
        <div class="r-body">
            <div class="r-row"><span class="r-label">OR No.:</span><span class="r-value">${data.or_no}</span></div>
            <div class="r-row"><span class="r-label">Date:</span><span class="r-value">${data.date} ${data.time}</span></div>
            <div class="r-divider"></div>
            <div class="r-row"><span class="r-label">Student Name:</span><span class="r-value">${data.student_name}</span></div>
            <div class="r-row"><span class="r-label">Grade Level:</span><span class="r-value">${data.grade_level}</span></div>
            <div class="r-row"><span class="r-label">School Year:</span><span class="r-value">${data.school_year}</span></div>
            <div class="r-divider"></div>
            <div class="r-row"><span class="r-label">Description:</span><span class="r-value">${data.description}</span></div>
            <div class="r-row"><span class="r-label">Payment Method:</span><span class="r-value">${data.method}</span></div>
            <div class="r-divider"></div>
            <div class="r-total"><span>Amount Paid:</span><span>₱${data.amount}</span></div>
            <div style="margin-top:20px;text-align:right;">
                <div style="font-size:12px;color:#666;">Received by:</div>
                <div style="font-weight:700;font-size:14px;">${data.received_by}</div>
            </div>
        </div>
        <div class="r-footer">This is an official receipt. Thank you for your payment.</div>
    </div>
    <div style="text-align:center;margin-top:20px;"><button onclick="window.print()" style="padding:10px 30px;background:#1e3a5f;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;">Print</button></div>
    </body></html>`);
    w.document.close();
}
</script>
@endsection
