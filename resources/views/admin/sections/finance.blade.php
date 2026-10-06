    <div id="section-finance" class="dash-section" style="display:none;">

        <div class="section-header">

            <div>

                <h1>Finance Management</h1>

                <p>Manage fee payments, tuition, and financial records.</p>

            </div>

        </div>

        {{-- Finance Stats --}}

        <div class="row g-3 mb-4">

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>

                    <div>

                        <div class="stat-value">₱{{ number_format($totalCollected ?? 0, 2) }}</div>

                        <div class="stat-label">Total Collected</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $paidCount ?? 0 }}</div>

                        <div class="stat-label">Fully Paid</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>

                    <div>

                        <div class="stat-value">{{ $partialCount ?? 0 }}</div>

                        <div class="stat-label">Partial Payments</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $unpaidCount ?? 0 }}</div>

                        <div class="stat-label">Unpaid Students</div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Payment Status Legend 
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:16px;padding:12px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;font-size:12px;">
            <span style="font-weight:600;color:#555;margin-right:4px;">Payment Status:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Paid</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #e65100;border-radius:3px;display:inline-block;"></span> Partial</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Unpaid</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff8ec;border:1px solid #f5a623;border-radius:3px;display:inline-block;"></span> Pending Approval</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#ede7f6;border:1px solid #512da8;border-radius:3px;display:inline-block;"></span> Waived</span>
        </div> --}}

        {{-- Student Payment Overview --}}

        <div class="content-card mb-4">

            <div class="content-card-header">

                <h6><i class="bi bi-people-fill me-2" style="color:var(--blue);"></i>Student Payment Overview</h6>

            </div>

            <div class="module-toolbar">

                <div class="toolbar-search">

                    <i class="bi bi-search"></i>

                    <input type="text" id="financeSearchInput" placeholder="Search student name..." onkeyup="filterFinanceTable()">

                </div>

                <div class="toolbar-filter">

                    <select id="financePayFilter" onchange="filterFinanceTable()">

                        <option value="">All Payment Status</option>

                        <option value="paid">Paid</option>

                        <option value="partial">Partial</option>

                        <option value="unpaid">Unpaid</option>

                    </select>

                    <select id="financeSectionFilter" onchange="filterFinanceTable()">

                        <option value="">All Sections</option>

                        @foreach(($sections ?? collect()) as $sec)

                            <option value="{{ $sec->name }}">{{ $sec->name }}</option>

                        @endforeach

                    </select>

                    <span class="toolbar-count"><span id="financeVisibleCount">{{ ($allStudentsPayment ?? collect())->count() }}</span> of {{ ($allStudentsPayment ?? collect())->count() }} students</span>

                </div> 

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table" id="financeStudentTable">

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Section</th>

                            <th>Grade</th>

                            <th>Amount Paid</th>

                            <th>Balance</th>

                            <th>Status</th>

                            <th>Payment Plan</th>

                            <th>Next Due Date</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse(($allStudentsPayment ?? collect()) as $sp)

                        @php

                            $spEnr = $sp->enrollments->first() ?? $sp->latestEnrollment;

                            $spData = $spEnr->student_data ?? [];

                            $spGrade = $spData['grade_level'] ?? '';

                            $spGradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];

                            $spGradeDisplay = $spGradeMap[$spGrade] ?? ($spGrade ?: 'N/A');

                            $spSection = ($spEnr && $spEnr->section && $spEnr->section !== 'Unassigned') ? $spEnr->section : '—';

                            $spPayStatus = $spEnr->payment_status ?? 'unpaid';

                            $spAmountPaid = $spEnr->payment_amount ?? 0;
                            $spTotalFee = $spEnr->total_fee ?? 0;
                            $spBalance = $spEnr->remaining_balance ?? max(0, $spTotalFee - $spAmountPaid);

                            $spPayIcon = $spPayStatus === 'paid' ? '✓' : '✗';
                            $spPayColor = $spPayStatus === 'paid' ? '#28a745' : ($spPayStatus === 'partial' ? '#e67e00' : '#dc3545');
                            $spPayBg = $spPayStatus === 'paid' ? '#e8f5e9' : ($spPayStatus === 'partial' ? '#fff3e0' : '#ffebee');

                            $spPaymentType = $spEnr->payment_type ?? null;

                            // Calculate next due date and amount from paymentInstallments
                            $spNextPending = $spEnr?->paymentInstallments?->where('status', 'pending')?->sortBy('due_date')?->first();
                            $spNextDueDate = $spNextPending ? $spNextPending->due_date->format('M d, Y') : ($spEnr?->next_installment_date?->format('M d, Y') ?? '—');
                            $spNextMonth = $spNextPending?->month_name ?? '';
                            $spNextAmount = $spNextPending?->total_due ?? 0;
                            $spIsOverdue = $spNextPending && $spNextPending->due_date < now();
                            $spWeeksOverdue = $spNextPending?->weeks_overdue ?? 0;
                            $spTotalLateFees = $spEnr?->paymentInstallments?->sum('late_fee') ?? 0;
                            $spInstallmentProgress = $spEnr?->paymentInstallments?->count() > 0
                                ? ($spEnr->paymentInstallments->where('status', 'paid')->count() / $spEnr->paymentInstallments->count()) * 100
                                : 0;

                        @endphp

                        <tr data-search="{{ strtolower($sp->name . ' ' . $spSection) }}" data-pay="{{ $spPayStatus }}" data-section="{{ $spSection }}">

                            <td>

                                <div class="user-row-name">

                                    <div class="user-row-avatar">{{ strtoupper(substr($sp->name, 0, 2)) }}</div>

                                    <div>

                                        <div style="font-weight:600;">{{ $sp->name }}</div>

                                        <div class="user-row-sub">{{ $spEnr ? $spEnr->reference_number : 'ID: '.$sp->id }}</div>

                                    </div>

                                </div>

                            </td>

                            <td>{{ $spSection }}</td>

                            <td><span class="grade-chip">{{ $spGradeDisplay }}</span></td>

                            <td style="font-weight:600; white-space:nowrap;">{{ $spEnr ? '₱' . number_format($spAmountPaid, 2) : '—' }}</td>

                            <td>
                                @if(($spBalance ?? 0) > 0)
                                    <span style="font-weight:600; white-space:nowrap; color:#dc3545;">{{ $spEnr ? '₱' . number_format($spBalance ?? 0, 2) : '—' }}</span>
                                @else
                                    <span style="font-weight:600; white-space:nowrap; color:#28a745;">{{ $spEnr ? '₱' . number_format($spBalance ?? 0, 2) : '—' }}</span>
                                @endif
                            </td>

                            <td style="text-align:center;">
                                @php
                                    $spStatusLabel = match($spPayStatus) {
                                        'paid'    => 'Paid',
                                        'partial' => 'Partial',
                                        default   => 'Unpaid',
                                    };
                                    $spStatusStyle = match($spPayStatus) {
                                        'paid'    => 'background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;',
                                        'partial' => 'background:#fff3e0;color:#e65100;border:1px solid #ffcc80;',
                                        default   => 'background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;',
                                    };
                                @endphp
                                @if($spEnr)
                                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;{{ $spStatusStyle }}">{{ $spStatusLabel }}</span>
                                @else
                                    <span class="text-muted-alt">—</span>
                                @endif
                            </td>

                            <td style="text-align:center;">@if($spPaymentType)<span class="badge bg-{{ $spPaymentType === 'installment' ? 'primary' : 'secondary' }}">{{ ucfirst($spPaymentType) }}</span>@else<span class="text-muted-alt">—</span>@endif</td>

                            <td style="white-space:nowrap;">
                                @if($spNextPending)
                                    @if($spIsOverdue)
                                        <div style="font-weight:600; color:#dc3545;">
                                            {{ $spNextMonth }}: ₱{{ number_format($spNextAmount, 2) }}
                                        </div>
                                    @else
                                        <div style="font-weight:600;">
                                            {{ $spNextMonth }}: ₱{{ number_format($spNextAmount, 2) }}
                                        </div>
                                    @endif
                                    <div style="font-size:11px; color:#666;">Due: {{ $spNextDueDate }}</div>
                                    @if($spIsOverdue)
                                        <span style="font-size:10px; padding:2px 6px; border-radius:10px; background:#ffcdd2; color:#c62828;">
                                            <i class="bi bi-exclamation-triangle"></i> {{ $spWeeksOverdue }}w overdue
                                        </span>
                                    @endif
                                @else
                                    {{ $spNextDueDate }}
                                @endif
                            </td>


                            <td style="vertical-align:middle;">

                                @if($spEnr)

                                <div style="display:flex; align-items:center; gap:6px;">

                                    <button class="action-btn edit js-payment-update" title="Update Payment"
                                        data-id="{{ $spEnr->id }}"
                                        data-name="{{ $sp->name }}"
                                        data-status="{{ $spPayStatus }}"
                                        data-amount="{{ $spAmountPaid }}"
                                        data-method="{{ $spEnr->payment_method ?? '' }}"
                                        data-ref="{{ $spEnr->payment_reference ?? '' }}"
                                        data-payment-option="{{ $spEnr->payment_option ?? '' }}"
                                        data-downpayment="{{ $spEnr->downpayment_amount ?? 0 }}"
                                        data-monthly="{{ $spEnr->monthly_amount ?? 0 }}"
                                        data-total-fee="{{ $spEnr->total_fee ?? 0 }}"
                                        data-remaining="{{ $spEnr->remaining_balance ?? 0 }}"
                                        data-grade="{{ $spGrade }}"><i class="bi bi-cash-coin"></i></button>

                                    <button class="action-btn view js-payment-pay" title="Pay" data-id="{{ $spEnr->id }}" data-name="{{ $sp->name }}" data-grade="{{ $spGrade }}" data-amount-paid="{{ $spAmountPaid }}" data-downpayment="{{ $spEnr->downpayment_amount ?? 0 }}" data-monthly="{{ $spEnr->monthly_amount ?? 0 }}" data-payment-type="{{ $spEnr->payment_type ?? '' }}" data-payment-option="{{ $spEnr->payment_option ?? '' }}" data-total-fee="{{ $spEnr->total_fee ?? 0 }}"><i class="bi bi-credit-card"></i></button>

                                    @if($spEnr->paymentInstallments->count() > 0)
                                    <button class="action-btn view js-view-installments" title="View Installments"
                                        data-id="{{ $spEnr->id }}"
                                        data-name="{{ htmlspecialchars($sp->name, ENT_QUOTES, 'UTF-8') }}"
                                        data-grade="{{ $spGradeDisplay }}"
                                        data-option="{{ $spEnr->payment_type ?? ($spEnr->payment_option === 'A' ? 'full' : ($spEnr->payment_option ? 'installment' : '')) }}"
                                        data-monthly="{{ $spEnr->monthly_amount ?? 0 }}"
                                        data-downpayment="{{ $spEnr->downpayment_amount ?? 0 }}"
                                        data-total-fee="{{ $spEnr->total_fee ?? 0 }}"
                                        data-total-paid="{{ $spEnr->payment_amount ?? 0 }}">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                    @endif

                                </div>

                                @else

                                <span class="text-muted-alt">—</span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="9" style="text-align:center; color:var(--text); padding:40px;">

                                <i class="bi bi-people" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                No student records found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

                {{-- Finance Management Pagination --}}
                <div class="p-3 border-top" style="border-color:var(--border);">
                    @if(isset($allStudentsPayment))
                    {{ $allStudentsPayment->appends([
                        'sort' => $sort ?? 'newest',
                        'student_grade' => $studentGradeFilter ?? '',
                        'student_status' => $studentStatusFilter ?? '',
                        'student_payment' => $studentPaymentFilter ?? '',
                        'student_schoolyear' => $studentSchoolYearFilter ?? '',
                    ])->links() }}
                    @endif
                    @if(isset($allStudentsPayment) && $allStudentsPayment->count() > 0)
                    <div class="pagination-info">
                        Showing {{ $allStudentsPayment->firstItem() }} to {{ $allStudentsPayment->lastItem() }} of {{ $allStudentsPayment->total() }} records
                    </div>
                    @endif
                </div>

            </div>

        </div>

    </div>{{-- /section-finance --}}
