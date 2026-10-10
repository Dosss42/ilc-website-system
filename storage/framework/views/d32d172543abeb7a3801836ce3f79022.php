<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo e($reportTitle); ?></title>
<?php echo $__env->make('pdf.shared._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <?php echo $__env->make('pdf.shared._letterhead', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $statusRows = [
            ['label' => 'Enrolled',  'key' => 'enrolled'],
            ['label' => 'Approved',  'key' => 'approved'],
            ['label' => 'Completed', 'key' => 'completed'],
            ['label' => 'Pending',   'key' => 'pending'],
            ['label' => 'Declined',  'key' => 'declined'],
            ['label' => 'Dropped',   'key' => 'dropped'],
        ];
        $totalSafe = max(1, $enrollTotal);
    ?>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-center">Count</th>
                <th class="text-center">Share</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $statusRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $count = $enrollAll->where('status', $sr['key'])->count();
                    $pct   = round($count / $totalSafe * 100);
                ?>
                <tr>
                    <td><?php echo e($sr['label']); ?></td>
                    <td class="text-center"><?php echo e($count); ?></td>
                    <td class="text-center"><?php echo e($pct); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center"><?php echo e($enrollTotal); ?></td>
                <td class="text-center">100%</td>
            </tr>
        </tfoot>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/enrollment-status.blade.php ENDPATH**/ ?>