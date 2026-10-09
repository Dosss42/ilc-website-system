    <div id="section-finance" class="dash-section" style="display:none;">

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
                                    <span style="font-weight:600; white-space:nowrap; color:#dc3545;"><?php echo e($spEnr ? '₱' . number_format($spBalance ?? 0, 2) : '—'); ?></span>
                                <?php else: ?>
                                    <span style="font-weight:600; white-space:nowrap; color:#28a745;"><?php echo e($spEnr ? '₱' . number_format($spBalance ?? 0, 2) : '—'); ?></span>
                                <?php endif; ?>
                            </td>

                            <td style="text-align:center;">
                                <?php
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
                                ?>
                                <?php if($spEnr): ?>
                                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;<?php echo e($spStatusStyle); ?>"><?php echo e($spStatusLabel); ?></span>
                                <?php else: ?>
                                    <span class="text-muted-alt">—</span>
                                <?php endif; ?>
                            </td>

                            <td style="text-align:center;"><?php if($spPaymentType): ?><span class="badge bg-<?php echo e($spPaymentType === 'installment' ? 'primary' : 'secondary'); ?>"><?php echo e(ucfirst($spPaymentType)); ?></span><?php else: ?><span class="text-muted-alt">—</span><?php endif; ?></td>

                            <td style="white-space:nowrap;">
                                <?php if($spNextPending): ?>
                                    <?php if($spIsOverdue): ?>
                                        <div style="font-weight:600; color:#dc3545;">
                                            <?php echo e($spNextMonth); ?>: ₱<?php echo e(number_format($spNextAmount, 2)); ?>

                                        </div>
                                    <?php else: ?>
                                        <div style="font-weight:600;">
                                            <?php echo e($spNextMonth); ?>: ₱<?php echo e(number_format($spNextAmount, 2)); ?>

                                        </div>
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

                                <?php if($spEnr): ?>

                                <div style="display:flex; align-items:center; gap:6px;">

                                    <button class="action-btn edit js-payment-update" title="Update Payment"
                                        data-id="<?php echo e($spEnr->id); ?>"
                                        data-name="<?php echo e($sp->name); ?>"
                                        data-status="<?php echo e($spPayStatus); ?>"
                                        data-amount="<?php echo e($spAmountPaid); ?>"
                                        data-method="<?php echo e($spEnr->payment_method ?? ''); ?>"
                                        data-ref="<?php echo e($spEnr->payment_reference ?? ''); ?>"
                                        data-payment-option="<?php echo e($spEnr->payment_option ?? ''); ?>"
                                        data-downpayment="<?php echo e($spEnr->downpayment_amount ?? 0); ?>"
                                        data-monthly="<?php echo e($spEnr->monthly_amount ?? 0); ?>"
                                        data-total-fee="<?php echo e($spEnr->total_fee ?? 0); ?>"
                                        data-remaining="<?php echo e($spEnr->remaining_balance ?? 0); ?>"
                                        data-grade="<?php echo e($spGrade); ?>"><i class="bi bi-cash-coin"></i></button>

                                    <button class="action-btn view js-payment-pay" title="Pay" data-id="<?php echo e($spEnr->id); ?>" data-name="<?php echo e($sp->name); ?>" data-grade="<?php echo e($spGrade); ?>" data-amount-paid="<?php echo e($spAmountPaid); ?>" data-downpayment="<?php echo e($spEnr->downpayment_amount ?? 0); ?>" data-monthly="<?php echo e($spEnr->monthly_amount ?? 0); ?>" data-payment-type="<?php echo e($spEnr->payment_type ?? ''); ?>" data-payment-option="<?php echo e($spEnr->payment_option ?? ''); ?>" data-total-fee="<?php echo e($spEnr->total_fee ?? 0); ?>"><i class="bi bi-credit-card"></i></button>

                                    <?php if($spEnr->paymentInstallments->count() > 0): ?>
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
                                    <?php endif; ?>

                                </div>

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

                
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <?php if(isset($allStudentsPayment)): ?>
                    <?php echo e($allStudentsPayment->appends([
                        'sort' => $sort ?? 'newest',
                        'student_grade' => $studentGradeFilter ?? '',
                        'student_status' => $studentStatusFilter ?? '',
                        'student_payment' => $studentPaymentFilter ?? '',
                        'student_schoolyear' => $studentSchoolYearFilter ?? '',
                    ])->links()); ?>

                    <?php endif; ?>
                    <?php if(isset($allStudentsPayment) && $allStudentsPayment->count() > 0): ?>
                    <div class="pagination-info">
                        Showing <?php echo e($allStudentsPayment->firstItem()); ?> to <?php echo e($allStudentsPayment->lastItem()); ?> of <?php echo e($allStudentsPayment->total()); ?> records
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/finance.blade.php ENDPATH**/ ?>