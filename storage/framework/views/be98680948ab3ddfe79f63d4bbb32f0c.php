<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo e($reportTitle); ?></title>
<?php echo $__env->make('pdf.shared._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <?php echo $__env->make('pdf.shared._letterhead', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $genderTotal = $male + $female; ?>
    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($enrollments->count()); ?></div>
            <div class="fin-stat-label">Enrolled Students</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($male); ?></div>
            <div class="fin-stat-label">Male</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($female); ?></div>
            <div class="fin-stat-label">Female</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($genderTotal); ?></div>
            <div class="fin-stat-label">Total Enrolled</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Grade Level</th>
                <th class="text-center">Gender</th>
                <th class="text-center">Type</th>
                <th class="text-center">Status</th>
                <th class="text-center">Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sd = $e->student_data ?? [];
                    $fullName = trim(($sd['first_name'] ?? '') . ' ' . ($sd['last_name'] ?? '')) ?: ($e->full_name ?? '—');
                    $gl = $sd['grade_level'] ?? ($e->grade_level ?? '');
                    $glLabel = $gradeLevels[$gl] ?? $gl;
                    $stype = ucfirst($sd['student_type'] ?? '—');
                ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($fullName); ?></td>
                    <td><?php echo e($glLabel); ?></td>
                    <td class="text-center"><?php echo e(ucfirst(strtolower($sd['gender'] ?? '—'))); ?></td>
                    <td class="text-center"><?php echo e($stype); ?></td>
                    <td class="text-center"><?php echo e(ucfirst($e->status ?? 'pending')); ?></td>
                    <td class="text-center">
                        <?php echo e(($e->payment_status ?? '') === 'paid' ? 'Paid' : (($e->payment_status ?? '') === 'partial' ? 'Partial' : 'Unpaid')); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center">No enrolled students found for this school year.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Total Enrolled</td>
                <td class="text-center"><?php echo e($genderTotal); ?></td>
            </tr>
        </tfoot>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/master-list.blade.php ENDPATH**/ ?>