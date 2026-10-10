<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo e($reportTitle); ?></title>
<?php echo $__env->make('pdf.shared._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <?php echo $__env->make('pdf.shared._letterhead', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱<?php echo e(number_format($totalFees, 2)); ?></div>
            <div class="fin-stat-label">Total Assessed</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱<?php echo e(number_format($totalCollected, 2)); ?></div>
            <div class="fin-stat-label">Total Collected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($collectionRate); ?>%</div>
            <div class="fin-stat-label">Collection Rate</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($paid + $partial + $unpaid); ?></div>
            <div class="fin-stat-label">Total Students</div>
        </div>
    </div>

    <?php
        $rows = [
            ['label' => 'Fully Paid', 'amount' => $finAll->where('payment_status', 'paid')->sum('payment_amount'), 'count' => $paid],
            ['label' => 'Partial',    'amount' => $finAll->where('payment_status', 'partial')->sum('payment_amount'), 'count' => $partial],
            ['label' => 'Unpaid',     'amount' => 0, 'count' => $unpaid],
        ];
        $totalCountSafe = max(1, $paid + $partial + $unpaid);
    ?>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Payment Status</th>
                <th class="text-center">Students</th>
                <th class="text-right">Amount Collected</th>
                <th class="text-center">Share</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($r['label']); ?></td>
                    <td class="text-center"><?php echo e($r['count']); ?></td>
                    <td class="text-right"><?php echo e($r['amount'] > 0 ? '₱' . number_format($r['amount'], 2) : '—'); ?></td>
                    <td class="text-center"><?php echo e(round($r['count'] / $totalCountSafe * 100)); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total Assessed</td>
                <td class="text-center"><?php echo e($paid + $partial + $unpaid); ?></td>
                <td class="text-right">₱<?php echo e(number_format($totalFees, 2)); ?></td>
                <td></td>
            </tr>
            <tr>
                <td>Total Collected</td>
                <td></td>
                <td class="text-right">₱<?php echo e(number_format($totalCollected, 2)); ?></td>
                <td class="text-center"><?php echo e($collectionRate); ?>%</td>
            </tr>
        </tfoot>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/collection-summary.blade.php ENDPATH**/ ?>