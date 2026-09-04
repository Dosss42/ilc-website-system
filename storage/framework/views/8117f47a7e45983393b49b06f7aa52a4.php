<?php $__env->startSection('title', 'Finance Management'); ?>

<?php $__env->startSection('skeleton'); ?>
<div class="skel skel-header-title"></div>
<div class="skel skel-header-sub"></div>
<div class="skel-row-gap">
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
</div>
<div class="skel-row-gap">
    <div class="skel-card" style="flex:2;min-width:320px;">
        <div class="skel skel-card-header"></div>
        <div class="skel skel-chart"></div>
    </div>
    <div class="skel-card" style="flex:1;min-width:220px;">
        <div class="skel skel-card-header"></div>
        <div class="skel skel-chart" style="height:180px;"></div>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="section-header">
    <div>
        <h1>Finance Management</h1>
        <p>Manage fee payments, tuition, and financial records.</p>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value">₱<?php echo e(number_format($totalCollected ?? 0, 2)); ?></div>
                <div class="stat-label">Total Collected</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-value"><?php echo e($paidCount ?? 0); ?></div>
                <div class="stat-label">Fully Paid</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-value"><?php echo e($partialCount ?? 0); ?></div>
                <div class="stat-label">Partial Payments</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-value"><?php echo e($unpaidCount ?? 0); ?></div>
                <div class="stat-label">Unpaid Students</div>
            </div>
        </div>
    </div>
</div>


<?php
    $fnChMonths=[]; $fnChCollected=[];
    for($i=5;$i>=0;$i--){
        $m=now()->subMonths($i);
        $fnChMonths[]=$m->format('M Y');
        $fnChCollected[]=(float)\App\Models\PaymentTransaction::where('status','completed')
            ->whereYear('processed_at',$m->year)->whereMonth('processed_at',$m->month)->sum('amount');
    }
    $fnPaid    = \App\Models\Enrollment::where('payment_status','paid')->count();
    $fnPartial = \App\Models\Enrollment::where('payment_status','partial')->count();
    $fnUnpaid  = \App\Models\Enrollment::whereNotIn('payment_status',['paid','partial'])->count();
    $fnCash    = \App\Models\PaymentTransaction::where('status','completed')->where('payment_method','cash')->sum('amount');
    $fnGcash   = \App\Models\PaymentTransaction::where('status','completed')->where('payment_method','gcash')->sum('amount');
    $fnXendit  = \App\Models\PaymentTransaction::where('status','completed')->whereNotIn('payment_method',['cash','gcash'])->sum('amount');
?>
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-graph-up me-2" style="color:var(--blue);"></i>Monthly Collections</h6>
                <span style="font-size:11px;color:var(--muted);">Last 6 months</span>
            </div>
            <div class="p-3" style="height:220px;"><canvas id="fnCollectLine"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-pie-chart-fill me-2" style="color:var(--blue);"></i>Payment Status</h6>
            </div>
            <div class="p-3" style="height:220px;display:flex;justify-content:center;"><canvas id="fnPayStatusDoughnut"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-credit-card-fill me-2" style="color:var(--blue);"></i>Collection by Method</h6>
            </div>
            <div class="p-3" style="height:200px;display:flex;justify-content:center;"><canvas id="fnMethodDoughnut"></canvas></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-bar-chart-fill me-2" style="color:var(--blue);"></i>Grade Level — Collection Overview</h6>
            </div>
            <div class="p-3" style="height:200px;">
                <?php
                    $fnGradeLabels = []; $fnGradeCollected = [];
                    $fnGradeGroups = \App\Models\Enrollment::selectRaw('grade_level, SUM(payment_amount) as total')
                        ->whereNotNull('grade_level')->groupBy('grade_level')->orderBy('grade_level')->get();
                    foreach($fnGradeGroups as $g){
                        $fnGradeLabels[] = ucwords(str_replace(['grade','_'],['Grade ',' '],$g->grade_level));
                        $fnGradeCollected[] = (float)$g->total;
                    }
                ?>
                <canvas id="fnGradeBar"></canvas>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){
    const _FC={blue:'#1a3a6c',mid:'#2471a3',gold:'#c5a059',green:'#16a34a',red:'#dc2626'};
    Chart.defaults.font.family="'Poppins',sans-serif";
    Chart.defaults.font.size=11;

    // Monthly collection line
    new Chart(document.getElementById('fnCollectLine'),{type:'line',
        data:{labels:<?php echo json_encode($fnChMonths, 15, 512) ?>,
            datasets:[{label:'Collections (₱)',data:<?php echo json_encode($fnChCollected, 15, 512) ?>,
                borderColor:_FC.blue,backgroundColor:'rgba(26,58,108,.08)',
                borderWidth:2,pointRadius:4,tension:0.4,fill:true}]},
        options:{responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
    });

    // Payment status doughnut
    new Chart(document.getElementById('fnPayStatusDoughnut'),{type:'doughnut',
        data:{labels:['Paid','Partial','Unpaid'],
            datasets:[{data:[<?php echo e($fnPaid); ?>,<?php echo e($fnPartial); ?>,<?php echo e($fnUnpaid); ?>],
                backgroundColor:[_FC.green,_FC.gold,_FC.red],borderWidth:0,hoverOffset:4}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'68%',
            plugins:{legend:{position:'bottom',labels:{padding:12}}}}
    });

    // Collection by method doughnut
    new Chart(document.getElementById('fnMethodDoughnut'),{type:'doughnut',
        data:{labels:['Cash','GCash','E-Payment'],
            datasets:[{data:[<?php echo e($fnCash); ?>,<?php echo e($fnGcash); ?>,<?php echo e($fnXendit); ?>],
                backgroundColor:[_FC.green,_FC.mid,_FC.gold],borderWidth:0,hoverOffset:4}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'68%',
            plugins:{legend:{position:'bottom',labels:{padding:12}}}}
    });

    // Grade level bar
    new Chart(document.getElementById('fnGradeBar'),{type:'bar',
        data:{labels:<?php echo json_encode($fnGradeLabels, 15, 512) ?>,
            datasets:[{label:'Collected (₱)',data:<?php echo json_encode($fnGradeCollected, 15, 512) ?>,
                backgroundColor:_FC.blue,borderRadius:5,borderSkipped:false}]},
        options:{responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
    });
})();
</script>
<?php $__env->stopPush(); ?>


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
                <?php $__currentLoopData = ($sections ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($sec->name); ?>"><?php echo e($sec->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span class="toolbar-count"><span id="financeVisibleCount"><?php echo e(($allStudentsPayment ?? collect())->count()); ?></span> of <?php echo e(($allStudentsPayment ?? collect())->count()); ?> students</span>
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
                <?php $__empty_1 = true; $__currentLoopData = ($allStudentsPayment ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $spEnr = $sp->enrollments->first() ?? null;
                    $spData = $spEnr->student_data ?? [];
                    $spGrade = $spData['grade_level'] ?? '';
                    $spGradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
                    $spGradeDisplay = $spGradeMap[$spGrade] ?? ($spGrade ?: 'N/A');
                    $spSection = ($spEnr && $spEnr->section && $spEnr->section !== 'Unassigned') ? $spEnr->section : '—';
                    $spPayStatus = $spEnr->payment_status ?? 'unpaid';
                    $spAmountPaid = $spEnr->payment_amount ?? 0;
                    $spTotalFee = $spEnr->total_fee ?? 0;
                    $spBalance = $spEnr->remaining_balance ?? max(0, $spTotalFee - $spAmountPaid);
                    $spPaymentType = $spEnr->payment_type ?? null;

                    // Next installment due
                    $spNextPending = $spEnr?->paymentInstallments?->where('status', 'pending')?->sortBy('due_date')?->first();
                    $spNextDueDate = $spNextPending ? $spNextPending->due_date->format('M d, Y') : ($spEnr?->next_installment_date?->format('M d, Y') ?? '—');
                    $spNextMonth = $spNextPending?->month_name ?? '';
                    $spNextAmount = $spNextPending?->total_due ?? 0;
                    $spIsOverdue = $spNextPending && $spNextPending->due_date < now();
                    $spWeeksOverdue = $spNextPending?->weeks_overdue ?? 0;

                    $spStatusStyle = match($spPayStatus) {
                        'paid'    => 'background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;',
                        'partial' => 'background:#fff3e0;color:#e65100;border:1px solid #ffcc80;',
                        default   => 'background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;',
                    };
                    $spStatusLabel = match($spPayStatus) {
                        'paid'    => 'Paid',
                        'partial' => 'Partial',
                        default   => 'Unpaid',
                    };
                ?>
                <tr data-search="<?php echo e(strtolower($sp->name . ' ' . $spSection)); ?>" data-pay="<?php echo e($spPayStatus); ?>" data-section="<?php echo e($spSection); ?>">
                    <td>
                        <div class="user-row-name">
                            <div class="user-row-avatar"><?php echo e(strtoupper(substr($sp->name, 0, 2))); ?></div>
                            <div>
                                <div style="font-weight:600;"><?php echo e($sp->name); ?></div>
                                <div class="user-row-sub"><?php echo e($spEnr ? $spEnr->reference_number : 'ID: '.$sp->id); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo e($spSection); ?></td>
                    <td><span class="grade-chip"><?php echo e($spGradeDisplay); ?></span></td>
                    <td style="font-weight:600; white-space:nowrap;"><?php echo e($spEnr ? '₱' . number_format($spAmountPaid, 2) : '—'); ?></td>
                    <td>
                        <?php if(($spBalance ?? 0) > 0): ?>
                            <span style="font-weight:600; white-space:nowrap; color:#dc3545;"><?php echo e($spEnr ? '₱' . number_format($spBalance, 2) : '—'); ?></span>
                        <?php else: ?>
                            <span style="font-weight:600; white-space:nowrap; color:#28a745;"><?php echo e($spEnr ? '₱' . number_format($spBalance, 2) : '—'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if($spEnr): ?>
                            <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;<?php echo e($spStatusStyle); ?>"><?php echo e($spStatusLabel); ?></span>
                        <?php else: ?>
                            <span class="text-muted-alt">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if($spPaymentType): ?>
                            <span class="badge bg-<?php echo e($spPaymentType === 'installment' ? 'primary' : 'secondary'); ?>"><?php echo e(ucfirst($spPaymentType)); ?></span>
                        <?php else: ?>
                            <span class="text-muted-alt">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <?php if($spNextPending): ?>
                            <?php if($spIsOverdue): ?>
                                <div style="font-weight:600; color:#dc3545;"><?php echo e($spNextMonth); ?>: ₱<?php echo e(number_format($spNextAmount, 2)); ?></div>
                            <?php else: ?>
                                <div style="font-weight:600;"><?php echo e($spNextMonth); ?>: ₱<?php echo e(number_format($spNextAmount, 2)); ?></div>
                            <?php endif; ?>
                            <div style="font-size:11px; color:#666;">Due: <?php echo e($spNextDueDate); ?></div>
                            <?php if($spIsOverdue): ?>
                                <span style="font-size:10px; padding:2px 6px; border-radius:10px; background:#ffcdd2; color:#c62828;">
                                    <i class="bi bi-exclamation-triangle"></i> <?php echo e($spWeeksOverdue); ?>w overdue
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo e($spNextDueDate); ?>

                        <?php endif; ?>
                    </td>
                    <td style="vertical-align:middle;">
                        <?php if($spEnr && $spEnr->paymentInstallments->count() > 0): ?>
                        <button class="action-btn view js-view-installments" title="View Installments"
                            data-id="<?php echo e($spEnr->id); ?>"
                            data-name="<?php echo e(htmlspecialchars($sp->name, ENT_QUOTES, 'UTF-8')); ?>"
                            data-grade="<?php echo e($spGradeDisplay); ?>"
                            data-option="<?php echo e($spEnr->payment_type ?? ($spEnr->payment_option === 'A' ? 'full' : ($spEnr->payment_option ? 'installment' : ''))); ?>"
                            data-monthly="<?php echo e($spEnr->monthly_amount ?? 0); ?>"
                            data-downpayment="<?php echo e($spEnr->downpayment_amount ?? 0); ?>"
                            data-total-fee="<?php echo e($spEnr->total_fee ?? 0); ?>"
                            data-total-paid="<?php echo e($spEnr->payment_amount ?? 0); ?>">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <?php elseif($spEnr): ?>
                            <span class="text-muted-alt">—</span>
                        <?php else: ?>
                            <span class="text-muted-alt">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align:center; color:var(--text); padding:40px;">
                        <i class="bi bi-people" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>
                        No student records found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
        <div class="p-3 border-top">
            <?php if(isset($allStudentsPayment)): ?>
                <?php echo e($allStudentsPayment->links()); ?>

            <?php endif; ?>
            <?php if(isset($allStudentsPayment) && $allStudentsPayment->count() > 0): ?>
                <div class="pagination-info">
                    Showing <?php echo e($allStudentsPayment->firstItem()); ?> to <?php echo e($allStudentsPayment->lastItem()); ?> of <?php echo e($allStudentsPayment->total()); ?> records
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="modal fade" id="viewInstallmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),var(--blue-light));border:0;border-radius:14px 14px 0 0;padding:18px 24px;">
                <div>
                    <h5 class="modal-title mb-0" style="font-weight:700;font-size:16px;color:#fff;">
                        <i class="bi bi-calendar-check me-2"></i>Installment Schedule
                    </h5>
                    <div id="instModalSubtitle" style="font-size:11px;opacity:0.85;margin-top:2px;color:#fff;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="row g-3 mb-4" id="instSummaryCards"></div>
                <div style="overflow-x:auto;">
                    <table class="dash-table" id="installmentDetailTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Month</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Late Fee</th>
                                <th>Total Due</th>
                                <th>Status</th>
                                <th>Paid On</th>
                            </tr>
                        </thead>
                        <tbody id="installmentDetailBody">
                            <tr><td colspan="8" style="text-align:center;padding:40px;color:#666;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;padding:14px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function filterFinanceTable() {
    const search  = (document.getElementById('financeSearchInput').value || '').toLowerCase();
    const pay     = (document.getElementById('financePayFilter').value || '').toLowerCase();
    const section = (document.getElementById('financeSectionFilter').value || '').toLowerCase();
    const rows    = document.querySelectorAll('#financeStudentTable tbody tr[data-search]');
    let visible   = 0;
    rows.forEach(row => {
        const matchSearch  = !search  || row.dataset.search.includes(search);
        const matchPay     = !pay     || row.dataset.pay === pay;
        const matchSection = !section || row.dataset.section.toLowerCase() === section;
        const show = matchSearch && matchPay && matchSection;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const el = document.getElementById('financeVisibleCount');
    if (el) el.textContent = visible;
}

/* View Installments Modal */
document.querySelectorAll('.js-view-installments').forEach(btn => {
    btn.addEventListener('click', function() {
        const enrollmentId = this.dataset.id;
        const name         = this.dataset.name;
        const grade        = this.dataset.grade;
        const option       = this.dataset.option;
        const monthly      = parseFloat(this.dataset.monthly) || 0;
        const dp           = parseFloat(this.dataset.downpayment) || 0;
        const totalFee     = parseFloat(this.dataset.totalFee) || 0;
        const totalPaid    = parseFloat(this.dataset.totalPaid) || 0;

        document.getElementById('instModalSubtitle').textContent = name + ' · ' + grade + ' · ' + (option === 'full' ? 'Full Payment' : 'Installment');

        const summaryCards = document.getElementById('instSummaryCards');
        summaryCards.innerHTML = `
            <div class="col-md-3">
                <div style="background:#e3f2fd;padding:16px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#666;margin-bottom:4px;">Total Fee</div>
                    <div style="font-weight:700;color:var(--blue);font-size:18px;">₱${totalFee.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#e8f5e9;padding:16px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#666;margin-bottom:4px;">Total Paid</div>
                    <div style="font-weight:700;color:#2e7d32;font-size:18px;">₱${totalPaid.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#fff3e0;padding:16px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#666;margin-bottom:4px;">Downpayment</div>
                    <div style="font-weight:700;color:#e65100;font-size:18px;">₱${dp.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#f3e5f5;padding:16px;border-radius:8px;text-align:center;">
                    <div style="font-size:11px;color:#666;margin-bottom:4px;">Monthly</div>
                    <div style="font-weight:700;color:#7b1fa2;font-size:18px;">₱${monthly.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                </div>
            </div>
        `;

        const tbody = document.getElementById('installmentDetailBody');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#666;"><i class="bi bi-arrow-repeat" style="font-size:24px;"></i><br>Loading...</td></tr>';
        new bootstrap.Modal(document.getElementById('viewInstallmentsModal')).show();

        fetch('/finance/installments/' + enrollmentId + '/details')
            .then(r => r.json())
            .then(data => {
                if (!data.installments || data.installments.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#666;">No installment records found.</td></tr>';
                    return;
                }
                tbody.innerHTML = data.installments.map((inst, i) => {
                    const statusStyle = inst.status === 'paid'
                        ? 'background:#e8f5e9;color:#2e7d32;'
                        : inst.status === 'overdue'
                            ? 'background:#ffebee;color:#c62828;'
                            : 'background:#fff3e0;color:#e65100;';
                    return `<tr>
                        <td>${i+1}</td>
                        <td style="font-weight:600;">${inst.month_name}</td>
                        <td>${inst.due_date}</td>
                        <td>₱${parseFloat(inst.amount).toLocaleString('en-PH',{minimumFractionDigits:2})}</td>
                        <td>${inst.late_fee > 0 ? '<span style="color:#c62828;">₱'+parseFloat(inst.late_fee).toLocaleString('en-PH',{minimumFractionDigits:2})+'</span>' : '—'}</td>
                        <td style="font-weight:700;">₱${parseFloat(inst.total_due).toLocaleString('en-PH',{minimumFractionDigits:2})}</td>
                        <td><span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;${statusStyle}">${inst.status.charAt(0).toUpperCase()+inst.status.slice(1)}</span></td>
                        <td style="font-size:12px;color:#666;">${inst.paid_at || '—'}</td>
                    </tr>`;
                }).join('');
            })
            .catch(() => {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#c62828;">Failed to load installments.</td></tr>';
            });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('finance.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/finance/dashboard.blade.php ENDPATH**/ ?>