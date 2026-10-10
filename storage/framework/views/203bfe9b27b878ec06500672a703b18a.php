    <div id="section-installments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-calendar-check-fill" style="color:var(--gold);"></i> Installments</h1>
                <p>View all student installment schedules and payment tracking</p>
            </div>
        </div>

        
        <?php
            $totalInstStudents = ($installmentEnrollments ?? collect())->count();
            $overdueInstStudents = ($installmentEnrollments ?? collect())->where('is_overdue', true)->count();
            $fullyPaidInst = ($installmentEnrollments ?? collect())->where('payment_status', 'paid')->count();
            $partialPaidInst = ($installmentEnrollments ?? collect())->where('payment_status', 'partial')->count();
            $totalLateFeesAll = ($installmentEnrollments ?? collect())->sum('total_late_fees');
        ?>
        
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($totalInstStudents); ?></div>
                    <div class="stat-label">Installment Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($overdueInstStudents); ?></div>
                    <div class="stat-label">Overdue</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($fullyPaidInst); ?></div>
                    <div class="stat-label">Fully Paid</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($partialPaidInst); ?></div>
                    <div class="stat-label">Partially Paid</div>
                </div>
            </div>
        </div>

        <?php if($totalLateFeesAll > 0): ?>
        <div style="background:#fff3e0; border:1px solid #ffe0b2; border-radius:10px; padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#e65100; font-size:18px;"></i>
            <div>
                <span style="font-weight:600; color:#e65100;">Late Fees Accumulated:</span>
                <span style="font-weight:700; color:#bf360c; font-size:16px;">₱<?php echo e(number_format($totalLateFeesAll, 2)); ?></span>
                <span style="color:#666; font-size:12px; margin-left:8px;">across <?php echo e($overdueInstStudents); ?> overdue student(s)</span>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="content-card mb-4">
            <div class="content-card-header">
                <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Installments</h6>
            </div>
            <div style="padding:16px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:end;">
                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">School Year</label>
                    <select id="instFilterYear" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterInstallments()">
                        <option value="all">All Years</option>
                        <?php $__currentLoopData = $schoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

        

        <div class="content-card mb-4">
            <div class="content-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h6><i class="bi bi-table me-2" style="color:var(--gold);"></i>Student Installments</h6>
                <span style="font-size:12px; color:var(--muted);"><?php echo e($totalInstStudents); ?> student(s) on installment plans</span>
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
                        <?php $__empty_1 = true; $__currentLoopData = ($installmentEnrollments ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
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
                        ?>
                        <?php if($isOverdue): ?>
                        <tr style="background:#fff8f8;" data-status="<?php echo e($enrollment->payment_status); ?>" data-year="<?php echo e($enrollment->school_year ?? ''); ?>" data-overdue="yes" data-student="<?php echo e(strtolower($enrollment->user->name ?? '')); ?> <?php echo e(strtolower($enrollment->user->email ?? '')); ?>">
                        <?php else: ?>
                        <tr data-status="<?php echo e($enrollment->payment_status); ?>" data-year="<?php echo e($enrollment->school_year ?? ''); ?>" data-overdue="no" data-student="<?php echo e(strtolower($enrollment->user->name ?? '')); ?> <?php echo e(strtolower($enrollment->user->email ?? '')); ?>">
                        <?php endif; ?>
                            <td>
                                <div style="font-weight:600; color:var(--text);"><?php echo e($enrollment->user->name ?? 'N/A'); ?></div>
                                <div style="font-size:11px; color:var(--muted);"><?php echo e($enrollment->user->email ?? ''); ?></div>
                            </td>
                            <td><span class="grade-chip"><?php echo e($enrollment->grade_level ?? 'N/A'); ?></span></td>
                            <td>
                                <div style="font-weight:600; color:var(--blue);">Option <?php echo e($enrollment->payment_option ?? 'N/A'); ?></div>
                                <div style="font-size:11px; color:var(--muted);">
                                    ₱<?php echo e(number_format($enrollment->monthly_amount ?? 0, 2)); ?>/month
                                    <?php if($totalLateFees > 0): ?>
                                        <span style="color:var(--red);">(+₱<?php echo e(number_format($totalLateFees, 0)); ?> fees)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="width:180px;">
                                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                    <span style="font-weight:500;">
                                        <?php if($downpaymentAmount > 0): ?>
                                            <span style="color:<?php echo e($downpaymentPaid ? 'var(--green)' : 'var(--red)'); ?>; font-size:11px;"><i class="bi bi-<?php echo e($downpaymentPaid ? 'check-circle' : 'circle'); ?>"></i> DP</span>
                                            <span style="margin:0 4px;">|</span>
                                        <?php endif; ?>
                                        <?php echo e($paidMonths); ?>/<?php echo e($totalMonths); ?> monthly
                                    </span>
                                    <?php if($progress >= 100): ?>
                                        <span style="font-weight:600; color:var(--green);"><?php echo e(number_format($progress, 0)); ?>%</span>
                                    <?php else: ?>
                                        <span style="font-weight:600; color:var(--blue);"><?php echo e(number_format($progress, 0)); ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div style="height:8px; background:#e8eaf0; border-radius:4px; overflow:hidden;">
                                    <?php if($progress >= 100): ?>
                                        <div class="installment-progress-bar" data-progress="<?php echo e($progress); ?>" data-type="complete"></div>
                                    <?php elseif($isOverdue): ?>
                                        <div class="installment-progress-bar" data-progress="<?php echo e($progress); ?>" data-type="overdue"></div>
                                    <?php else: ?>
                                        <div class="installment-progress-bar" data-progress="<?php echo e($progress); ?>" data-type="normal"></div>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                                    ₱<?php echo e(number_format($totalPaid, 0)); ?> of ₱<?php echo e(number_format($totalFee, 0)); ?>

                                </div>
                            </td>
                            <td>
                                <?php if($enrollment->next_due_date): ?>
                                    <?php if($isOverdue): ?>
                                        <div style="font-weight:600; color:var(--red);">
                                            <?php echo e($enrollment->next_month_name ?? 'Monthly'); ?>: ₱<?php echo e(number_format($enrollment->next_due_amount ?? 0, 2)); ?>

                                        </div>
                                    <?php else: ?>
                                        <div style="font-weight:600; color:var(--text);">
                                            <?php echo e($enrollment->next_month_name ?? 'Monthly'); ?>: ₱<?php echo e(number_format($enrollment->next_due_amount ?? 0, 2)); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div style="font-size:11px; color:var(--muted);">
                                        Due: <?php echo e($enrollment->next_due_date->format('M d, Y')); ?>

                                    </div>
                                    <?php if($isOverdue): ?>
                                        <span style="display:inline-flex; align-items:center; gap:4px; margin-top:4px; padding:3px 10px; border-radius:12px; font-size:10px; font-weight:700; background:#ffebee; color:#c62828;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <?php echo e($weeksOverdue > 0 ? $weeksOverdue . 'w overdue' : 'Overdue'); ?>

                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; color:var(--green); font-weight:600;">
                                        <i class="bi bi-check-circle-fill"></i> <?php echo e($enrollment->next_month_name ?? 'Fully Paid'); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <?php $balance = $enrollment->remaining_balance ?? ($totalFee - $totalPaid); ?>
                                <?php if($balance <= 0): ?>
                                    <div style="font-weight:700; color:var(--green);">
                                        <i class="bi bi-check-circle-fill"></i> Fully Paid
                                    </div>
                                <?php else: ?>
                                    <div style="font-weight:700; color:<?php echo e($isOverdue ? 'var(--red)' : 'var(--blue)'); ?>; font-size:14px;">
                                        ₱<?php echo e(number_format($balance, 2)); ?>

                                    </div>
                                    <div style="font-size:11px; color:var(--muted);">remaining</div>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <button type="button" class="action-btn view js-view-installments" title="View Installment Details"
                                    data-id="<?php echo e($enrollment->id); ?>"
                                    data-name="<?php echo e(htmlspecialchars($enrollment->user->name ?? 'N/A', ENT_QUOTES, 'UTF-8')); ?>"
                                    data-grade="<?php echo e($enrollment->grade_level ?? 'N/A'); ?>"
                                    data-option="<?php echo e($enrollment->payment_type ?? ($enrollment->payment_option === 'A' ? 'full' : ($enrollment->payment_option ? 'installment' : 'N/A'))); ?>"
                                    data-monthly="<?php echo e($enrollment->monthly_amount ?? 0); ?>"
                                    data-downpayment="<?php echo e($enrollment->downpayment_amount ?? 0); ?>"
                                    data-total-fee="<?php echo e($enrollment->total_fee ?? 0); ?>"
                                    data-total-paid="<?php echo e($enrollment->payment_amount ?? 0); ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if($enrollment->is_overdue ?? false): ?>
                                <button type="button" class="action-btn"
                                    style="background:#fff3e0;color:#e65100;border:1px solid #f5a623;"
                                    title="Add Promissory Note"
                                    onclick="openAdminPromissoryModal(<?php echo e($enrollment->id); ?>, '<?php echo e(addslashes($enrollment->user->name ?? '')); ?>', <?php echo e($enrollment->remaining_balance ?? 0); ?>)">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-calendar-check" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Installment Records</div>
                                <div style="font-size:12px;">Students on installment plans will appear here.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-top" style="border-color:var(--border);">
                <?php if(isset($installmentEnrollments)): ?>
                <?php echo e($installmentEnrollments->links()); ?>

                <?php endif; ?>
                <?php if(isset($installmentEnrollments)): ?>
                <div class="pagination-info">
                    Showing <?php echo e($installmentEnrollments->firstItem() ?? 0); ?> to <?php echo e($installmentEnrollments->lastItem() ?? 0); ?> of <?php echo e($installmentEnrollments->total()); ?> installments
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/installments.blade.php ENDPATH**/ ?>