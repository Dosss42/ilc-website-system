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
            <div class="fin-stat-value"><?php echo e($newCount); ?></div>
            <div class="fin-stat-label">New Students</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($returningCount); ?></div>
            <div class="fin-stat-label">Returning</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($transfereeCount); ?></div>
            <div class="fin-stat-label">Transferee</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($newCount + $returningCount + $transfereeCount); ?></div>
            <div class="fin-stat-label">Total</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th class="text-center">New</th>
                <th class="text-center">Returning</th>
                <th class="text-center">Transferee</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $gNew = 0; $gRet = 0; $gTrans = 0; $gTot = 0; ?>
            <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $row = $enrollByGrade[$key] ?? ['new' => 0, 'returning' => 0, 'transferee' => 0, 'total' => 0];
                    $gNew   += $row['new'] ?? 0;
                    $gRet   += $row['returning'] ?? 0;
                    $gTrans += $row['transferee'] ?? 0;
                    $gTot   += $row['total'] ?? 0;
                ?>
                <tr>
                    <td><?php echo e($label); ?></td>
                    <td class="text-center"><?php echo e($row['new'] ?? 0); ?></td>
                    <td class="text-center"><?php echo e($row['returning'] ?? 0); ?></td>
                    <td class="text-center"><?php echo e($row['transferee'] ?? 0); ?></td>
                    <td class="text-center"><?php echo e($row['total'] ?? 0); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center"><?php echo e($gNew); ?></td>
                <td class="text-center"><?php echo e($gRet); ?></td>
                <td class="text-center"><?php echo e($gTrans); ?></td>
                <td class="text-center"><?php echo e($gTot); ?></td>
            </tr>
        </tfoot>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/students-new-vs-returning.blade.php ENDPATH**/ ?>