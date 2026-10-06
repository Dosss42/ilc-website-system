    <div id="section-payments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-credit-card-fill" style="color:var(--gold);"></i> Payments</h1>
                <p>View and manage all student payment records</p>
            </div>
        </div>

        {{-- Payment Stats (online + walk-in combined) --}}
        @php
            $cps = $combinedPayStats ?? ['total'=>0,'pending'=>0,'completed'=>0,'rejected'=>0,'walkin_amount'=>0];
        @endphp

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-value">{{ $cps['total'] }}</div>
                    <div class="stat-label">Total Payments</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-value">{{ $cps['pending'] }}</div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $cps['completed'] }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $cps['rejected'] }}</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        {{-- Payment Filters --}}
        @php
            $currentYear = now()->year;
            $schoolYears = [];
            for ($i = -2; $i <= 2; $i++) {
                $start = $currentYear + $i;
                $schoolYears[] = $start . '-' . ($start + 1);
            }
        @endphp

        <div class="content-card mb-4">
            <div class="content-card-header">
                <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Payments</h6>
            </div>
            <div style="padding:16px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:end;">
                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">School Year</label>
                    <select id="payFilterYear" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterPaymentTable()">
                        <option value="all">All Years</option>
                        @foreach($schoolYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Status</label>
                    <select id="payFilterStatus" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterPaymentTable()">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Payment Method</label>
                    <select id="payFilterMethod" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterPaymentTable()">
                        <option value="all">All Methods</option>
                        <option value="gcash">GCash</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div style="flex:1; min-width:180px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">Search Student</label>
                    <input type="text" id="payFilterSearch" class="form-control" placeholder="Search by name or email..." style="font-size:13px; padding:8px 12px;" oninput="filterPaymentTable()">
                </div>
                <div>
                    <button type="button" class="btn-dash btn-secondary" onclick="resetPaymentFilters()" style="padding:8px 16px; font-size:13px;">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Payment Tables — tabbed --}}
        <div class="content-card mb-4" style="overflow:hidden;">

            {{-- Tab nav --}}
            <ul class="nav nav-tabs mb-0" style="padding:0 16px; background:#f8f9fa; border-bottom:1px solid #dee2e6; margin:0;">
                <li class="nav-item">
                    <button type="button" id="adminPmtTab-online" class="nav-link active"
                        onclick="switchAdminPmtTab('online')"
                        style="font-size:13px; font-weight:600; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-phone"></i> Online Payments
                        <span class="badge bg-warning text-dark" style="font-size:10px;">{{ isset($financePayments) ? $financePayments->total() : 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" id="adminPmtTab-walkin" class="nav-link"
                        onclick="switchAdminPmtTab('walkin')"
                        style="font-size:13px; font-weight:600; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-cash-stack"></i> Walk-in Transactions
                        <span class="badge bg-primary" style="font-size:10px;">{{ isset($walkInTransactions) ? $walkInTransactions->total() : 0 }}</span>
                    </button>
                </li>
            </ul>

            {{-- Panel: Online Payment Records --}}
            <div id="adminPmtPanel-online">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-bottom:1px solid #f0f0f0;">
                <h6 style="margin:0; font-size:14px; font-weight:700;"><i class="bi bi-table me-2" style="color:var(--gold);"></i>Online Payment Records</h6>
                <span style="font-size:12px; color:var(--muted);">Walk-in Collected: ₱{{ number_format($combinedPayStats['walkin_amount'] ?? 0, 2) }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Grade Level</th>
                            <th>Installment / Amount</th>
                            <th>Method</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($financePayments ?? collect()) as $payment)
                        @php
                            $installment = $payment->paymentInstallment;
                            $hasLateFee = $installment && $installment->late_fee > 0;
                            $paymentAmount = $installment->amount ?? 0;
                            // Detect actual method: check installment record first, then parse description
                            $payMethod = $installment?->payment_method
                                ?? (stripos($payment->description ?? '', 'via Cash') !== false ? 'cash' : 'gcash');
                            // Extract submitted amount from description (e.g. "Payment via GCash - ₱4,505.00")
                            preg_match('/[₱P]([\d,]+\.?\d*)/', $payment->description ?? '', $_amtMatch);
                            $receiptAmount = isset($_amtMatch[1]) ? (float) str_replace(',', '', $_amtMatch[1]) : ($payment->enrollment->payment_amount ?? 0);
                        @endphp
                        <tr data-status="{{ $payment->status }}" data-method="{{ $payMethod }}" data-year="{{ $payment->enrollment->school_year ?? '' }}" data-student="{{ strtolower($payment->user->name ?? '') }} {{ strtolower($payment->user->email ?? '') }}">
                            <td style="font-weight:600;">#{{ $payment->id }}</td>
                            <td>
                                <div style="font-weight:600; color:var(--text);">{{ $payment->user->name ?? 'N/A' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $payment->user->email ?? '' }}</div>
                            </td>
                            <td><span class="grade-chip">{{ $payment->enrollment->grade_level ?? 'N/A' }}</span></td>
                            <td>
                                @if($installment)
                                    <div style="font-weight:600; color:var(--blue);">
                                        {{ $installment->month_name }} Installment
                                    </div>
                                    <div style="font-size:12px; color:var(--muted);">
                                        ₱{{ number_format($installment->amount ?? 0, 2) }}
                                        @if($hasLateFee)
                                            <span style="color:var(--red);">+ ₱{{ number_format($installment->late_fee ?? 0, 2) }} late fee</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        Total: ₱{{ number_format($paymentAmount, 2) }}
                                    </div>
                                @else
                                    <div style="font-weight:600; color:var(--blue);">
                                        {{ $payment->installment_month ?? $payment->description ?? 'Payment' }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($payMethod === 'gcash')
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e3f2fd; color:#1565c0;">
                                        <i class="bi bi-phone"></i> GCash
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-cash-stack"></i> Cash
                                    </span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $payment->created_at->format('M d, Y') }}<br><span style="font-size:11px; color:var(--muted);">{{ $payment->created_at->format('h:i A') }}</span></td>
                            <td>
                                <div style="display:flex; gap:6px; align-items:center;">
                                    <button type="button" class="action-btn view js-view-payment" title="View Details"
                                        data-id="{{ $payment->id }}"
                                        data-payment-id="{{ $payment->id }}"
                                        data-student-name="{{ $payment->user->name ?? 'N/A' }}"
                                        data-email="{{ $payment->user->email ?? '' }}"
                                        data-grade="{{ $payment->enrollment->grade_level ?? 'N/A' }}"
                                        data-description="{{ $payment->description ?? 'Payment' }}"
                                        data-submitted="{{ $payment->created_at->format('M d, Y H:i') }}"
                                        data-status="{{ $payment->status }}"
                                        data-installment='@json($installment)'
                                        data-total-amount="{{ $paymentAmount }}"
                                        data-method="{{ $payMethod }}"
                                        data-has-enrollment="{{ $payment->enrollment ? 'true' : 'false' }}"
                                        data-payment-option="{{ $payment->enrollment->payment_option ?? 'N/A' }}"
                                        data-total-fee="{{ $payment->enrollment->total_fee ?? 0 }}"
                                        data-amount-paid="{{ $payment->enrollment->payment_amount ?? 0 }}"
                                        data-balance="{{ $payment->enrollment->remaining_balance ?? 0 }}"
                                        data-enrollment-status="{{ $payment->enrollment->payment_status ?? 'pending' }}"
                                        data-reviewed-by="{{ $payment->reviewedBy->name ?? 'System' }}"
                                        data-reviewed-at="{{ $payment->reviewed_at ? $payment->reviewed_at->format('M d, Y h:i A') : $payment->updated_at->format('M d, Y h:i A') }}"
                                        data-screenshot-url="{{ $payment->file_path ? route('documents.view', $payment) : '' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($payment->file_path)
                                        <button type="button" class="action-btn view js-view-screenshot" title="View Screenshot" style="background:#e8f5e9;"
                                            data-id="{{ $payment->id }}"
                                            data-screenshot-url="{{ route('documents.view', $payment) }}">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @endif
                                    @if($payment->status === 'pending')
                                        <button type="button" class="action-btn edit js-approve-payment" title="Approve Payment" data-id="{{ $payment->id }}">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="action-btn delete js-reject-payment" title="Reject Payment" data-id="{{ $payment->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @else
                                        <span style="font-size:11px; color:var(--green); font-weight:500;"><i class="bi bi-check-circle-fill"></i> Processed</span>
                                        <button type="button" class="action-btn" title="Print Official Receipt"
                                            style="background:#e8f5e9;color:#2e7d32;"
                                            onclick="printOfficialReceipt({
                                                or_no: 'OR-{{ str_pad($payment->id, 6, "0", STR_PAD_LEFT) }}',
                                                date: '{{ $payment->updated_at->format("F d, Y") }}',
                                                time: '{{ $payment->updated_at->format("h:i A") }}',
                                                student_name: '{{ addslashes($payment->user->name ?? "N/A") }}',
                                                grade_level: '{{ $payment->enrollment->grade_level ?? "N/A" }}',
                                                school_year: '{{ $payment->enrollment->school_year ?? "N/A" }}',
                                                description: '{{ addslashes($installment ? ($installment->month_name." Installment") : ($payment->description ?? "Payment")) }}',
                                                amount: '{{ number_format($receiptAmount, 2) }}',
                                                method: '{{ ucfirst($payMethod) }}',
                                                received_by: '{{ addslashes($payment->reviewedBy->name ?? "Admin") }}',
                                                type: 'online'
                                            })">
                                            <i class="bi bi-printer-fill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-credit-card" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Payment Records</div>
                                <div style="font-size:12px;">Payment submissions will appear here once students upload screenshots.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Payment Records Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                @if(isset($financePayments))
                {{ $financePayments->links() }}
                @endif
                @if(isset($financePayments) && $financePayments->count() > 0)
                <div class="pagination-info">
                    Showing {{ $financePayments->firstItem() }} to {{ $financePayments->lastItem() }} of {{ $financePayments->total() }} payments
                </div>
                @endif
            </div>
            </div>{{-- /adminPmtPanel-online --}}

            {{-- Panel: Walk-in Payment Transactions --}}
            @php $walkInTotalAmount = ($walkInTransactions ?? collect())->sum('amount'); @endphp
            <div id="adminPmtPanel-walkin" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-bottom:1px solid #f0f0f0;">
                <h6 style="margin:0; font-size:14px; font-weight:700;"><i class="bi bi-building me-2" style="color:var(--blue);"></i>Walk-in Payment Transactions</h6>
                <span style="font-size:12px; color:var(--muted);">Total Amount: ₱{{ number_format($walkInTotalAmount, 2) }}</span>
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
                        @php
                            $walkInInstallment = $walkInTx->installment;
                        @endphp
                        <tr data-status="{{ $walkInTx->status }}" data-student="{{ strtolower($walkInTx->user->name ?? '') }}">
                            <td style="font-weight:600;">#{{ $walkInTx->id }}</td>
                            <td>
                                <div style="font-weight:600; color:var(--text);">{{ $walkInTx->user->name ?? 'N/A' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $walkInTx->user->email ?? '' }}</div>
                            </td>
                            <td><span class="grade-chip">{{ $walkInTx->enrollment->grade_level ?? 'N/A' }}</span></td>
                            <td>
                                @if($walkInInstallment)
                                    <div style="font-weight:600; color:var(--blue);">
                                        {{ $walkInInstallment->month_name }} Installment
                                    </div>
                                    <div style="font-size:12px; color:var(--muted);">
                                        ₱{{ number_format($walkInInstallment->amount ?? 0, 2) }}
                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        Total: ₱{{ number_format($walkInTx->amount, 2) }}
                                    </div>
                                @else
                                    <div style="font-weight:600; color:var(--blue);">
                                        {{ $walkInTx->installment_month ?? 'Downpayment' }}
                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        ₱{{ number_format($walkInTx->amount, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($walkInTx->payment_method === 'gcash')
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e3f2fd; color:#1565c0;">
                                        <i class="bi bi-phone"></i> GCash
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-cash-stack"></i> Cash
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:12px; color:var(--text);">{{ $walkInTx->processedBy->name ?? 'System' }}</div>
                            </td>
                            <td style="white-space:nowrap;">{{ $walkInTx->created_at->format('M d, Y') }}<br><span style="font-size:11px; color:var(--muted);">{{ $walkInTx->created_at->format('h:i A') }}</span></td>
                            <td>
                                @if($walkInTx->status === 'completed')
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:600; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-check-circle"></i> Completed
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:600; background:#fff3e0; color:#e65100;">
                                        <i class="bi bi-hourglass-split"></i> {{ ucfirst($walkInTx->status) }}
                                    </span>
                                @endif
                                @if($walkInTx->payment_type === 'admin')
                                    <br>
                                    <span style="display:inline-flex; align-items:center; margin-top:3px; padding:2px 6px; border-radius:5px; font-size:10px; background:#e3f2fd; color:#1565c0; border:1px solid #90caf9;">
                                        <i class="bi bi-building me-1"></i>Cashier
                                    </span>
                                @elseif($walkInTx->payment_type === 'walkin')
                                    <br>
                                    <span style="display:inline-flex; align-items:center; margin-top:3px; padding:2px 6px; border-radius:5px; font-size:10px; background:#f3e5f5; color:#7b1fa2; border:1px solid #ce93d8;">
                                        <i class="bi bi-building me-1"></i>Cashier
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($walkInTx->status === 'completed')
                                    <button type="button" class="action-btn" title="Print Official Receipt"
                                        style="background:#e8f5e9;color:#2e7d32;"
                                        onclick="printOfficialReceipt({
                                            or_no: 'OR-{{ str_pad($walkInTx->id, 6, "0", STR_PAD_LEFT) }}',
                                            date: '{{ $walkInTx->created_at->format("F d, Y") }}',
                                            time: '{{ $walkInTx->created_at->format("h:i A") }}',
                                            student_name: '{{ addslashes($walkInTx->user->name ?? "N/A") }}',
                                            grade_level: '{{ $walkInTx->enrollment->grade_level ?? "N/A" }}',
                                            school_year: '{{ $walkInTx->enrollment->school_year ?? "N/A" }}',
                                            description: '{{ addslashes($walkInInstallment ? ($walkInInstallment->month_name." Installment") : ($walkInTx->installment_month ?? "Downpayment")) }}',
                                            amount: '{{ number_format($walkInTx->amount, 2) }}',
                                            method: '{{ ucfirst($walkInTx->payment_method ?? "Cash") }}',
                                            received_by: '{{ addslashes($walkInTx->processedBy->name ?? "Admin") }}',
                                            type: 'walkin'
                                        })">
                                        <i class="bi bi-printer-fill"></i>
                                    </button>
                                @else
                                    <span style="font-size:11px; color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-building" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Walk-in Transactions</div>
                                <div style="font-size:12px;">Walk-in payment records will appear here once processed at the cashier.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Walk-in Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                @if(isset($walkInTransactions))
                {{ $walkInTransactions->links() }}
                @endif
                @if(isset($walkInTransactions) && $walkInTransactions->count() > 0)
                <div class="pagination-info">
                    Showing {{ $walkInTransactions->firstItem() }} to {{ $walkInTransactions->lastItem() }} of {{ $walkInTransactions->total() }} walk-in transactions
                </div>
                @endif
            </div>
            </div>{{-- /adminPmtPanel-walkin --}}

        </div>{{-- /tabbed card --}}
    </div>{{-- /section-payments --}}
