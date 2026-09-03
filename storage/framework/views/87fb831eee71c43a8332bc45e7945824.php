<?php $__env->startSection('title', 'Finance Dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.blue { background: #e3f2fd; color: #1976d2; }
    .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.orange { background: #fff3e0; color: #e65100; }
    .stat-icon.purple { background: #f3e5f5; color: #6a1b9a; }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--blue);
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
    }

    /* Content Cards */
    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--blue);
    }

    .card-body {
        padding: 20px 24px;
    }

    /* Tables */
    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        text-align: left;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
    }

    .data-table td {
        padding: 14px 16px;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
    }

    .data-table tr:hover td {
        background: #f8f9fa;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-badge.pending { background: #fff3e0; color: #e65100; }
    .status-badge.approved { background: #e8f5e9; color: #2e7d32; }
    .status-badge.rejected { background: #ffebee; color: #c62828; }

    /* Buttons */
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-action.btn-view { background: #e3f2fd; color: #1976d2; }
    .btn-action.btn-approve { background: #e8f5e9; color: #2e7d32; }
    .btn-action.btn-reject { background: #ffebee; color: #c62828; }
    .btn-action:hover { opacity: 0.8; transform: translateY(-1px); }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .quick-action-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
    }

    .quick-action-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .quick-action-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .quick-action-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--blue);
        margin-bottom: 4px;
    }

    .quick-action-info p {
        font-size: 12px;
        color: #666;
        margin: 0;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Header -->
<div class="page-header">
    <h1 class="page-title">Finance Dashboard</h1>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="<?php echo e(route('finance.payments.index')); ?>?status=pending" class="quick-action-card">
        <div class="quick-action-icon" style="background: #fff3e0; color: #e65100;">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="quick-action-info">
            <h4>Verify Payments</h4>
            <p><?php echo e($stats['pending_verification']); ?> pending verification</p>
        </div>
    </a>

    <a href="<?php echo e(route('finance.installments.index')); ?>?overdue=yes" class="quick-action-card">
        <div class="quick-action-icon" style="background: #ffebee; color: #c62828;">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="quick-action-info">
            <h4>Overdue Payments</h4>
            <p><?php echo e($stats['overdue_installments']); ?> students overdue</p>
        </div>
    </a>

    <a href="<?php echo e(route('finance.fees.index')); ?>" class="quick-action-card">
        <div class="quick-action-icon" style="background: #e3f2fd; color: #1976d2;">
            <i class="bi bi-gear"></i>
        </div>
        <div class="quick-action-info">
            <h4>Fee Settings</h4>
            <p>Manage fee structure</p>
        </div>
    </a>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo e($stats['total_collected_today']); ?></div>
        <div class="stat-label">Payments Verified Today</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo e($stats['total_collected_month']); ?></div>
        <div class="stat-label">Payments This Month</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo e($stats['pending_verification']); ?></div>
        <div class="stat-label">Pending Verification</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon purple">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo e($stats['total_enrolled_students']); ?></div>
        <div class="stat-label">Enrolled Students</div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="row">
    <!-- Pending Payments -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-hourglass-split" style="color: var(--gold);"></i>
                    Pending Verifications
                </h3>
                <a href="<?php echo e(route('finance.payments.index')); ?>?status=pending" class="btn-action btn-view">
                    View All
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($payment->user->name ?? 'N/A'); ?></div>
                                        <div style="font-size: 11px; color: #666;"><?php echo e($payment->enrollment->grade_level ?? ''); ?></div>
                                    </td>
                                    <td style="font-weight: 600; color: var(--blue);">
                                        <?php echo e($payment->description ?? 'Payment'); ?>

                                    </td>
                                    <td>
                                        <?php if(strpos($payment->description, 'GCash') !== false): ?>
                                            <span class="status-badge" style="background: #e3f2fd; color: #1976d2;">
                                                <i class="bi bi-phone"></i> GCash
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge" style="background: #e8f5e9; color: #2e7d32;">
                                                <i class="bi bi-cash-stack"></i> Cash
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #666;">
                                        <?php echo e($payment->created_at->format('M d, Y')); ?>

                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="<?php echo e(route('finance.payments.details', $payment)); ?>" class="btn-action btn-view" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form method="POST" action="<?php echo e(route('finance.payments.approve', $payment)); ?>" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn-action btn-approve" title="Approve" onclick="return confirm('Approve this payment?')">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                                        <i class="bi bi-check-circle" style="font-size: 48px; color: #28a745; display: block; margin-bottom: 12px;"></i>
                                        No pending payments to verify
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-clock-history" style="color: var(--gold);"></i>
                    Recent Verified Payments
                </h3>
                <a href="<?php echo e(route('finance.payments.index')); ?>?status=approved" class="btn-action btn-view">
                    View All
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Verified By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($transaction->user->name ?? 'N/A'); ?></div>
                                        <div style="font-size: 11px; color: #666;"><?php echo e($transaction->enrollment->grade_level ?? ''); ?></div>
                                    </td>
                                    <td style="font-weight: 600; color: var(--blue);">
                                        <?php echo e($transaction->description ?? 'Payment'); ?>

                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: #666;">
                                            <?php echo e($transaction->reviewedBy->name ?? 'System'); ?>

                                        </span>
                                    </td>
                                    <td style="color: #666;">
                                        <?php echo e($transaction->reviewed_at->format('M d, Y') ?? $transaction->updated_at->format('M d, Y')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 12px;"></i>
                                        No recent transactions
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Installment Overview -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-calendar-check" style="color: var(--gold);"></i>
            Installment Overview
        </h3>
        <a href="<?php echo e(route('finance.installments.index')); ?>" class="btn-action btn-view">
            View All Installments
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <div style="font-size: 36px; font-weight: 700; color: var(--blue);">
                        <?php echo e($installmentOverview['total_installment_students']); ?>

                    </div>
                    <div style="color: #666; font-size: 14px;">Active Installment Students</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="text-align: center; padding: 20px; background: #fff3e0; border-radius: 10px;">
                    <div style="font-size: 36px; font-weight: 700; color: #e65100;">
                        <?php echo e($installmentOverview['due_this_week']); ?>

                    </div>
                    <div style="color: #666; font-size: 14px;">Due This Week</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="text-align: center; padding: 20px; background: #ffebee; border-radius: 10px;">
                    <div style="font-size: 36px; font-weight: 700; color: #c62828;">
                        <?php echo e($installmentOverview['overdue']); ?>

                    </div>
                    <div style="color: #666; font-size: 14px;">Overdue Installments</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('finance.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\finance\dashboard-simple.blade.php ENDPATH**/ ?>