<?php $__env->startSection('title', 'Audit Trail'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .status-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap; }
    .status-badge.success { background:#e8f5e9;color:#2e7d32; }
    .status-badge.danger  { background:#ffebee;color:#c62828; }
    .status-badge.active  { background:#e8f0fb;color:var(--blue); }
    .status-badge.primary { background:#eff6ff;color:#2471a3; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="section-header">
    <div>
        <h1><i class="bi bi-journal-check" style="color:var(--gold);"></i> Audit Trail</h1>
        <p>A record of payments and actions you've performed — for your own reference and accountability.</p>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <div class="card-title">My Activity</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $badgeClass = [
                        'login' => 'success', 'logout' => 'active',
                        'payment_approved' => 'success', 'payment_rejected' => 'danger',
                        'payment_processed' => 'success', 'walkin_payment' => 'success',
                        'password_change' => 'primary',
                    ];
                    $badgeLabel = [
                        'login' => 'Login', 'logout' => 'Logout',
                        'payment_approved' => 'Payment Approved', 'payment_rejected' => 'Payment Rejected',
                        'payment_processed' => 'Payment Processed', 'walkin_payment' => 'Walk-in Payment',
                        'password_change' => 'Password Change',
                    ];
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <span class="status-badge <?php echo e($badgeClass[$log->event_type] ?? 'active'); ?>">
                            <?php echo e($badgeLabel[$log->event_type] ?? ucfirst(str_replace('_', ' ', $log->event_type))); ?>

                        </span>
                    </td>
                    <td style="max-width:480px;word-break:break-word;"><?php echo e($log->description); ?></td>
                    <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                        <?php echo e($log->created_at?->format('M d, Y h:i A')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" style="text-align:center;padding:48px;color:#94a3b8;">
                        <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        No activity recorded yet.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:16px 24px;">
        <?php echo e($logs->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('finance.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/finance/audit-trail.blade.php ENDPATH**/ ?>