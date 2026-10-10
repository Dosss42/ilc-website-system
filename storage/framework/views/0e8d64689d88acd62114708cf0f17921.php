    <div id="section-payments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-credit-card-fill" style="color:var(--gold);"></i> Payments</h1>
                <p>View and manage all student payment records</p>
            </div>
        </div>

        
        <?php
            $cps = $combinedPayStats ?? ['total'=>0,'pending'=>0,'completed'=>0,'rejected'=>0,'walkin_amount'=>0];
        ?>

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($cps['total']); ?></div>
                    <div class="stat-label">Total Payments</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($cps['pending']); ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($cps['completed']); ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($cps['rejected']); ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        
        <?php
            $currentYear = now()->year;
            $schoolYears = [];
            for ($i = -2; $i <= 2; $i++) {
                $start = $currentYear + $i;
                $schoolYears[] = $start . '-' . ($start + 1);
            }
        ?>

        <div class="content-card mb-4">
            <div class="content-card-header">
                <h6><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Filter Payments</h6>
            </div>
            <div style="padding:16px 20px; display:flex; flex-wrap:wrap; gap:12px; align-items:end;">
                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:block;">School Year</label>
                    <select id="payFilterYear" class="form-select" style="font-size:13px; padding:8px 12px;" onchange="filterPaymentTable()">
                        <option value="all">All Years</option>
                        <?php $__currentLoopData = $schoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

        
        <div class="content-card mb-4" style="overflow:hidden;">

            
            <ul class="nav nav-tabs mb-0" style="padding:0 16px; background:#f8f9fa; border-bottom:1px solid #dee2e6; margin:0;">
                <li class="nav-item">
                    <button type="button" id="adminPmtTab-online" class="nav-link active"
                        onclick="switchAdminPmtTab('online')"
                        style="font-size:13px; font-weight:600; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-phone"></i> Online Payments
                        <span class="badge bg-warning text-dark" style="font-size:10px;"><?php echo e(isset($financePayments) ? $financePayments->total() : 0); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" id="adminPmtTab-walkin" class="nav-link"
                        onclick="switchAdminPmtTab('walkin')"
                        style="font-size:13px; font-weight:600; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px;">
                        <i class="bi bi-cash-stack"></i> Walk-in Transactions
                        <span class="badge bg-primary" style="font-size:10px;"><?php echo e(isset($walkInTransactions) ? $walkInTransactions->total() : 0); ?></span>
                    </button>
                </li>
            </ul>

            
            <div id="adminPmtPanel-online">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-bottom:1px solid #f0f0f0;">
                <h6 style="margin:0; font-size:14px; font-weight:700;"><i class="bi bi-table me-2" style="color:var(--gold);"></i>Online Payment Records</h6>
                <span style="font-size:12px; color:var(--muted);">Walk-in Collected: ₱<?php echo e(number_format($combinedPayStats['walkin_amount'] ?? 0, 2)); ?></span>
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
                        <?php $__empty_1 = true; $__currentLoopData = ($financePayments ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $installment = $payment->paymentInstallment;
                            $hasLateFee = $installment && $installment->late_fee > 0;
                            $paymentAmount = $installment->amount ?? 0;
                            // Detect actual method: check installment record first, then parse description
                            $payMethod = $installment?->payment_method
                                ?? (stripos($payment->description ?? '', 'via Cash') !== false ? 'cash' : 'gcash');
                            // Extract submitted amount from description (e.g. "Payment via GCash - ₱4,505.00")
                            preg_match('/[₱P]([\d,]+\.?\d*)/', $payment->description ?? '', $_amtMatch);
                            $receiptAmount = isset($_amtMatch[1]) ? (float) str_replace(',', '', $_amtMatch[1]) : ($payment->enrollment->payment_amount ?? 0);
                        ?>
                        <tr data-status="<?php echo e($payment->status); ?>" data-method="<?php echo e($payMethod); ?>" data-year="<?php echo e($payment->enrollment->school_year ?? ''); ?>" data-student="<?php echo e(strtolower($payment->user->name ?? '')); ?> <?php echo e(strtolower($payment->user->email ?? '')); ?>">
                            <td style="font-weight:600;">#<?php echo e($payment->id); ?></td>
                            <td>
                                <div style="font-weight:600; color:var(--text);"><?php echo e($payment->user->name ?? 'N/A'); ?></div>
                                <div style="font-size:11px; color:var(--muted);"><?php echo e($payment->user->email ?? ''); ?></div>
                            </td>
                            <td><span class="grade-chip"><?php echo e($payment->enrollment->grade_level ?? 'N/A'); ?></span></td>
                            <td>
                                <?php if($installment): ?>
                                    <div style="font-weight:600; color:var(--blue);">
                                        <?php echo e($installment->month_name); ?> Installment
                                    </div>
                                    <div style="font-size:12px; color:var(--muted);">
                                        ₱<?php echo e(number_format($installment->amount ?? 0, 2)); ?>

                                        <?php if($hasLateFee): ?>
                                            <span style="color:var(--red);">+ ₱<?php echo e(number_format($installment->late_fee ?? 0, 2)); ?> late fee</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        Total: ₱<?php echo e(number_format($paymentAmount, 2)); ?>

                                    </div>
                                <?php else: ?>
                                    <div style="font-weight:600; color:var(--blue);">
                                        <?php echo e($payment->installment_month ?? $payment->description ?? 'Payment'); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($payMethod === 'gcash'): ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e3f2fd; color:#1565c0;">
                                        <i class="bi bi-phone"></i> GCash
                                    </span>
                                <?php else: ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-cash-stack"></i> Cash
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;"><?php echo e($payment->created_at->format('M d, Y')); ?><br><span style="font-size:11px; color:var(--muted);"><?php echo e($payment->created_at->format('h:i A')); ?></span></td>
                            <td>
                                <div style="display:flex; gap:6px; align-items:center;">
                                    <button type="button" class="action-btn view js-view-payment" title="View Details"
                                        data-id="<?php echo e($payment->id); ?>"
                                        data-payment-id="<?php echo e($payment->id); ?>"
                                        data-student-name="<?php echo e($payment->user->name ?? 'N/A'); ?>"
                                        data-email="<?php echo e($payment->user->email ?? ''); ?>"
                                        data-grade="<?php echo e($payment->enrollment->grade_level ?? 'N/A'); ?>"
                                        data-description="<?php echo e($payment->description ?? 'Payment'); ?>"
                                        data-submitted="<?php echo e($payment->created_at->format('M d, Y H:i')); ?>"
                                        data-status="<?php echo e($payment->status); ?>"
                                        data-installment='<?php echo json_encode($installment, 15, 512) ?>'
                                        data-total-amount="<?php echo e($paymentAmount); ?>"
                                        data-method="<?php echo e($payMethod); ?>"
                                        data-has-enrollment="<?php echo e($payment->enrollment ? 'true' : 'false'); ?>"
                                        data-payment-option="<?php echo e($payment->enrollment->payment_option ?? 'N/A'); ?>"
                                        data-total-fee="<?php echo e($payment->enrollment->total_fee ?? 0); ?>"
                                        data-amount-paid="<?php echo e($payment->enrollment->payment_amount ?? 0); ?>"
                                        data-balance="<?php echo e($payment->enrollment->remaining_balance ?? 0); ?>"
                                        data-enrollment-status="<?php echo e($payment->enrollment->payment_status ?? 'pending'); ?>"
                                        data-reviewed-by="<?php echo e($payment->reviewedBy->name ?? 'System'); ?>"
                                        data-reviewed-at="<?php echo e($payment->reviewed_at ? $payment->reviewed_at->format('M d, Y h:i A') : $payment->updated_at->format('M d, Y h:i A')); ?>"
                                        data-screenshot-url="<?php echo e($payment->file_path ? route('documents.view', $payment) : ''); ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($payment->file_path): ?>
                                        <button type="button" class="action-btn view js-view-screenshot" title="View Screenshot" style="background:#e8f5e9;"
                                            data-id="<?php echo e($payment->id); ?>"
                                            data-screenshot-url="<?php echo e(route('documents.view', $payment)); ?>">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if($payment->status === 'pending'): ?>
                                        <button type="button" class="action-btn edit js-approve-payment" title="Approve Payment" data-id="<?php echo e($payment->id); ?>">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="action-btn delete js-reject-payment" title="Reject Payment" data-id="<?php echo e($payment->id); ?>">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <span style="font-size:11px; color:var(--green); font-weight:500;"><i class="bi bi-check-circle-fill"></i> Processed</span>
                                        <button type="button" class="action-btn" title="Print Official Receipt"
                                            style="background:#e8f5e9;color:#2e7d32;"
                                            onclick="printOfficialReceipt({
                                                or_no: 'OR-<?php echo e(str_pad($payment->id, 6, "0", STR_PAD_LEFT)); ?>',
                                                date: '<?php echo e($payment->updated_at->format("F d, Y")); ?>',
                                                time: '<?php echo e($payment->updated_at->format("h:i A")); ?>',
                                                student_name: '<?php echo e(addslashes($payment->user->name ?? "N/A")); ?>',
                                                grade_level: '<?php echo e($payment->enrollment->grade_level ?? "N/A"); ?>',
                                                school_year: '<?php echo e($payment->enrollment->school_year ?? "N/A"); ?>',
                                                description: '<?php echo e(addslashes($installment ? ($installment->month_name." Installment") : ($payment->description ?? "Payment"))); ?>',
                                                amount: '<?php echo e(number_format($receiptAmount, 2)); ?>',
                                                method: '<?php echo e(ucfirst($payMethod)); ?>',
                                                received_by: '<?php echo e(addslashes($payment->reviewedBy->name ?? "Admin")); ?>',
                                                type: 'online'
                                            })">
                                            <i class="bi bi-printer-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-credit-card" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Payment Records</div>
                                <div style="font-size:12px;">Payment submissions will appear here once students upload screenshots.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-top" style="border-color:var(--border);">
                <?php if(isset($financePayments)): ?>
                <?php echo e($financePayments->links()); ?>

                <?php endif; ?>
                <?php if(isset($financePayments) && $financePayments->count() > 0): ?>
                <div class="pagination-info">
                    Showing <?php echo e($financePayments->firstItem()); ?> to <?php echo e($financePayments->lastItem()); ?> of <?php echo e($financePayments->total()); ?> payments
                </div>
                <?php endif; ?>
            </div>
            </div>

            
            <?php $walkInTotalAmount = ($walkInTransactions ?? collect())->sum('amount'); ?>
            <div id="adminPmtPanel-walkin" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-bottom:1px solid #f0f0f0;">
                <h6 style="margin:0; font-size:14px; font-weight:700;"><i class="bi bi-building me-2" style="color:var(--blue);"></i>Walk-in Payment Transactions</h6>
                <span style="font-size:12px; color:var(--muted);">Total Amount: ₱<?php echo e(number_format($walkInTotalAmount, 2)); ?></span>
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
                        <?php $__empty_1 = true; $__currentLoopData = ($walkInTransactions ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $walkInTx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $walkInInstallment = $walkInTx->installment;
                        ?>
                        <tr data-status="<?php echo e($walkInTx->status); ?>" data-student="<?php echo e(strtolower($walkInTx->user->name ?? '')); ?>">
                            <td style="font-weight:600;">#<?php echo e($walkInTx->id); ?></td>
                            <td>
                                <div style="font-weight:600; color:var(--text);"><?php echo e($walkInTx->user->name ?? 'N/A'); ?></div>
                                <div style="font-size:11px; color:var(--muted);"><?php echo e($walkInTx->user->email ?? ''); ?></div>
                            </td>
                            <td><span class="grade-chip"><?php echo e($walkInTx->enrollment->grade_level ?? 'N/A'); ?></span></td>
                            <td>
                                <?php if($walkInInstallment): ?>
                                    <div style="font-weight:600; color:var(--blue);">
                                        <?php echo e($walkInInstallment->month_name); ?> Installment
                                    </div>
                                    <div style="font-size:12px; color:var(--muted);">
                                        ₱<?php echo e(number_format($walkInInstallment->amount ?? 0, 2)); ?>

                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        Total: ₱<?php echo e(number_format($walkInTx->amount, 2)); ?>

                                    </div>
                                <?php else: ?>
                                    <div style="font-weight:600; color:var(--blue);">
                                        <?php echo e($walkInTx->installment_month ?? 'Downpayment'); ?>

                                    </div>
                                    <div style="font-size:11px; color:var(--green); font-weight:600;">
                                        ₱<?php echo e(number_format($walkInTx->amount, 2)); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($walkInTx->payment_method === 'gcash'): ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e3f2fd; color:#1565c0;">
                                        <i class="bi bi-phone"></i> GCash
                                    </span>
                                <?php else: ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:500; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-cash-stack"></i> Cash
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:12px; color:var(--text);"><?php echo e($walkInTx->processedBy->name ?? 'System'); ?></div>
                            </td>
                            <td style="white-space:nowrap;"><?php echo e($walkInTx->created_at->format('M d, Y')); ?><br><span style="font-size:11px; color:var(--muted);"><?php echo e($walkInTx->created_at->format('h:i A')); ?></span></td>
                            <td>
                                <?php if($walkInTx->status === 'completed'): ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:600; background:#e8f5e9; color:#2e7d32;">
                                        <i class="bi bi-check-circle"></i> Completed
                                    </span>
                                <?php else: ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:600; background:#fff3e0; color:#e65100;">
                                        <i class="bi bi-hourglass-split"></i> <?php echo e(ucfirst($walkInTx->status)); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if($walkInTx->payment_type === 'admin'): ?>
                                    <br>
                                    <span style="display:inline-flex; align-items:center; margin-top:3px; padding:2px 6px; border-radius:5px; font-size:10px; background:#e3f2fd; color:#1565c0; border:1px solid #90caf9;">
                                        <i class="bi bi-building me-1"></i>Cashier
                                    </span>
                                <?php elseif($walkInTx->payment_type === 'walkin'): ?>
                                    <br>
                                    <span style="display:inline-flex; align-items:center; margin-top:3px; padding:2px 6px; border-radius:5px; font-size:10px; background:#f3e5f5; color:#7b1fa2; border:1px solid #ce93d8;">
                                        <i class="bi bi-building me-1"></i>Cashier
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($walkInTx->status === 'completed'): ?>
                                    <button type="button" class="action-btn" title="Print Official Receipt"
                                        style="background:#e8f5e9;color:#2e7d32;"
                                        onclick="printOfficialReceipt({
                                            or_no: 'OR-<?php echo e(str_pad($walkInTx->id, 6, "0", STR_PAD_LEFT)); ?>',
                                            date: '<?php echo e($walkInTx->created_at->format("F d, Y")); ?>',
                                            time: '<?php echo e($walkInTx->created_at->format("h:i A")); ?>',
                                            student_name: '<?php echo e(addslashes($walkInTx->user->name ?? "N/A")); ?>',
                                            grade_level: '<?php echo e($walkInTx->enrollment->grade_level ?? "N/A"); ?>',
                                            school_year: '<?php echo e($walkInTx->enrollment->school_year ?? "N/A"); ?>',
                                            description: '<?php echo e(addslashes($walkInInstallment ? ($walkInInstallment->month_name." Installment") : ($walkInTx->installment_month ?? "Downpayment"))); ?>',
                                            amount: '<?php echo e(number_format($walkInTx->amount, 2)); ?>',
                                            method: '<?php echo e(ucfirst($walkInTx->payment_method ?? "Cash")); ?>',
                                            received_by: '<?php echo e(addslashes($walkInTx->processedBy->name ?? "Admin")); ?>',
                                            type: 'walkin'
                                        })">
                                        <i class="bi bi-printer-fill"></i>
                                    </button>
                                <?php else: ?>
                                    <span style="font-size:11px; color:var(--muted);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" style="text-align:center; color:var(--muted); padding:60px;">
                                <i class="bi bi-building" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Walk-in Transactions</div>
                                <div style="font-size:12px;">Walk-in payment records will appear here once processed at the cashier.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-top" style="border-color:var(--border);">
                <?php if(isset($walkInTransactions)): ?>
                <?php echo e($walkInTransactions->links()); ?>

                <?php endif; ?>
                <?php if(isset($walkInTransactions) && $walkInTransactions->count() > 0): ?>
                <div class="pagination-info">
                    Showing <?php echo e($walkInTransactions->firstItem()); ?> to <?php echo e($walkInTransactions->lastItem()); ?> of <?php echo e($walkInTransactions->total()); ?> walk-in transactions
                </div>
                <?php endif; ?>
            </div>
            </div>

        </div>
    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/payments.blade.php ENDPATH**/ ?>