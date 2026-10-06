    <div id="section-installments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-calendar-check-fill" style="color:var(--gold);"></i> Installments</h1>
                <p>View all student installment schedules and payment tracking</p>
            </div>
        </div>

        {{-- Stats Row --}}
        @php
            $totalInstStudents = ($installmentEnrollments ?? collect())->count();
            $overdueInstStudents = ($installmentEnrollments ?? collect())->where('is_overdue', true)->count();
            $fullyPaidInst = ($installmentEnrollments ?? collect())->where('payment_status', 'paid')->count();
            $partialPaidInst = ($installmentEnrollments ?? collect())->where('payment_status', 'partial')->count();
            $totalLateFeesAll = ($installmentEnrollments ?? collect())->sum('total_late_fees');
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

        @if($totalLateFeesAll > 0)
        <div style="background:#fff3e0; border:1px solid #ffe0b2; border-radius:10px; padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#e65100; font-size:18px;"></i>
            <div>
                <span style="font-weight:600; color:#e65100;">Late Fees Accumulated:</span>
                <span style="font-weight:700; color:#bf360c; font-size:16px;">₱{{ number_format($totalLateFeesAll, 2) }}</span>
                <span style="color:#666; font-size:12px; margin-left:8px;">across {{ $overdueInstStudents }} overdue student(s)</span>
            </div>
        </div>
        @endif

        {{-- Installment Filters --}}
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

        {{-- Installment Status Legend 
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:16px;padding:12px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;font-size:12px;">
            <span style="font-weight:600;color:#555;margin-right:4px;">Installment Status:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Paid</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #e65100;border-radius:3px;display:inline-block;"></span> Partial</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Unpaid / Overdue</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff8ec;border:1px solid #f5a623;border-radius:3px;display:inline-block;"></span> Pending</span>
        </div> --}}

        <div class="content-card mb-4">
            <div class="content-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h6><i class="bi bi-table me-2" style="color:var(--gold);"></i>Student Installments</h6>
                <span style="font-size:12px; color:var(--muted);">{{ $totalInstStudents }} student(s) on installment plans</span>
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
                        @forelse(($installmentEnrollments ?? collect()) as $enrollment)
                        @php
                            $totalPaid = $enrollment->payment_amount ?? 0;
                            $totalFee = $enrollment->total_fee ?? 1;
                            $progress = $enrollment->installment_progress ?? min(100, ($totalPaid / $totalFee) * 100);
                            $isOverdue = $enrollment->is_overdue ?? false;
                            $weeksOverdue = $enrollment->weeks_overdue ?? 0;
                            $totalLateFees = $enrollment->total_late_fees ?? 0;
                            $paidMonths = $enrollment->paymentInstallments->where('status', 'paid')->count();
                            $totalMonths = $enrollment->paymentInstallments->count();
                            $downpaymentAmount = $enrollment->downpayment_amount ?? 0;
                            $downpaymentPaid = $downpaymentAmount > 0 && $totalPaid >= $downpaymentAmount;
                        @endphp
                        @if($isOverdue)
                        <tr style="background:#fff8f8;" data-status="{{ $enrollment->payment_status }}" data-year="{{ $enrollment->school_year ?? '' }}" data-overdue="yes" data-student="{{ strtolower($enrollment->user->name ?? '') }} {{ strtolower($enrollment->user->email ?? '') }}">
                        @else
                        <tr data-status="{{ $enrollment->payment_status }}" data-year="{{ $enrollment->school_year ?? '' }}" data-overdue="no" data-student="{{ strtolower($enrollment->user->name ?? '') }} {{ strtolower($enrollment->user->email ?? '') }}">
                        @endif
                            <td>
                                <div style="font-weight:600; color:var(--text);">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $enrollment->user->email ?? '' }}</div>
                            </td>
                            <td><span class="grade-chip">{{ $enrollment->grade_level ?? 'N/A' }}</span></td>
                            <td>
                                <div style="font-weight:600; color:var(--blue);">Option {{ $enrollment->payment_option ?? 'N/A' }}</div>
                                <div style="font-size:11px; color:var(--muted);">
                                    ₱{{ number_format($enrollment->monthly_amount ?? 0, 2) }}/month
                                    @if($totalLateFees > 0)
                                        <span style="color:var(--red);">(+₱{{ number_format($totalLateFees, 0) }} fees)</span>
                                    @endif
                                </div>
                            </td>
                            <td style="width:180px;">
                                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                    <span style="font-weight:500;">
                                        @if($downpaymentAmount > 0)
                                            <span style="color:{{ $downpaymentPaid ? 'var(--green)' : 'var(--red)' }}; font-size:11px;"><i class="bi bi-{{ $downpaymentPaid ? 'check-circle' : 'circle' }}"></i> DP</span>
                                            <span style="margin:0 4px;">|</span>
                                        @endif
                                        {{ $paidMonths }}/{{ $totalMonths }} monthly
                                    </span>
                                    @if($progress >= 100)
                                        <span style="font-weight:600; color:var(--green);">{{ number_format($progress, 0) }}%</span>
                                    @else
                                        <span style="font-weight:600; color:var(--blue);">{{ number_format($progress, 0) }}%</span>
                                    @endif
                                </div>
                                <div style="height:8px; background:#e8eaf0; border-radius:4px; overflow:hidden;">
                                    @if($progress >= 100)
                                        <div class="installment-progress-bar" data-progress="{{ $progress }}" data-type="complete"></div>
                                    @elseif($isOverdue)
                                        <div class="installment-progress-bar" data-progress="{{ $progress }}" data-type="overdue"></div>
                                    @else
                                        <div class="installment-progress-bar" data-progress="{{ $progress }}" data-type="normal"></div>
                                    @endif
                                </div>
                                <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                                    ₱{{ number_format($totalPaid, 0) }} of ₱{{ number_format($totalFee, 0) }}
                                </div>
                            </td>
                            <td>
                                @if($enrollment->next_due_date)
                                    @if($isOverdue)
                                        <div style="font-weight:600; color:var(--red);">
                                            {{ $enrollment->next_month_name ?? 'Monthly' }}: ₱{{ number_format($enrollment->next_due_amount ?? 0, 2) }}
                                        </div>
                                    @else
                                        <div style="font-weight:600; color:var(--text);">
                                            {{ $enrollment->next_month_name ?? 'Monthly' }}: ₱{{ number_format($enrollment->next_due_amount ?? 0, 2) }}
                                        </div>
                                    @endif
                                    <div style="font-size:11px; color:var(--muted);">
                                        Due: {{ $enrollment->next_due_date->format('M d, Y') }}
                                    </div>
                                    @if($isOverdue)
                                        <span style="display:inline-flex; align-items:center; gap:4px; margin-top:4px; padding:3px 10px; border-radius:12px; font-size:10px; font-weight:700; background:#ffebee; color:#c62828;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            {{ $weeksOverdue > 0 ? $weeksOverdue . 'w overdue' : 'Overdue' }}
                                        </span>
                                    @endif
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:4px; color:var(--green); font-weight:600;">
                                        <i class="bi bi-check-circle-fill"></i> {{ $enrollment->next_month_name ?? 'Fully Paid' }}
                                    </span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                @php $balance = $enrollment->remaining_balance ?? ($totalFee - $totalPaid); @endphp
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
                                <button type="button" class="action-btn view js-view-installments" title="View Installment Details"
                                    data-id="{{ $enrollment->id }}"
                                    data-name="{{ htmlspecialchars($enrollment->user->name ?? 'N/A', ENT_QUOTES, 'UTF-8') }}"
                                    data-grade="{{ $enrollment->grade_level ?? 'N/A' }}"
                                    data-option="{{ $enrollment->payment_type ?? ($enrollment->payment_option === 'A' ? 'full' : ($enrollment->payment_option ? 'installment' : 'N/A')) }}"
                                    data-monthly="{{ $enrollment->monthly_amount ?? 0 }}"
                                    data-downpayment="{{ $enrollment->downpayment_amount ?? 0 }}"
                                    data-total-fee="{{ $enrollment->total_fee ?? 0 }}"
                                    data-total-paid="{{ $enrollment->payment_amount ?? 0 }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($enrollment->is_overdue ?? false)
                                <button type="button" class="action-btn"
                                    style="background:#fff3e0;color:#e65100;border:1px solid #f5a623;"
                                    title="Add Promissory Note"
                                    onclick="openAdminPromissoryModal({{ $enrollment->id }}, '{{ addslashes($enrollment->user->name ?? '') }}', {{ $enrollment->remaining_balance ?? 0 }})">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-calendar-check" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Installment Records</div>
                                <div style="font-size:12px;">Students on installment plans will appear here.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Installment Records Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                @if(isset($installmentEnrollments))
                {{ $installmentEnrollments->links() }}
                @endif
                @if(isset($installmentEnrollments))
                <div class="pagination-info">
                    Showing {{ $installmentEnrollments->firstItem() ?? 0 }} to {{ $installmentEnrollments->lastItem() ?? 0 }} of {{ $installmentEnrollments->total() }} installments
                </div>
                @endif
            </div>
        </div>
    </div>{{-- /section-installments --}}
