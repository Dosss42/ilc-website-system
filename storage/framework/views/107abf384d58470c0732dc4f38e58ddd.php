<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo e($reportTitle); ?></title>
<?php echo $__env->make('pdf.shared._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <?php echo $__env->make('pdf.shared._letterhead', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th class="text-center">Enrolled</th>
                <th class="text-center">Sections</th>
                <th class="text-center">Capacity</th>
                <th class="text-center">Utilization</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $grandEnrolled = 0; $grandCap = 0; $grandSections = 0; ?>
            <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $cnt    = $studentsByGrade[$key] ?? 0;
                    $grandEnrolled += $cnt;
                    $secCnt = $allSections->where('grade_level', $key)->count();
                    $grandSections += $secCnt;
                    $cap    = $allSections->where('grade_level', $key)->sum('max_students') ?: ($secCnt * 40);
                    $grandCap += $cap;
                    $util   = $cap > 0 ? min(100, round($cnt / $cap * 100)) : 0;
                ?>
                <tr>
                    <td><?php echo e($label); ?></td>
                    <td class="text-center"><?php echo e($cnt); ?></td>
                    <td class="text-center"><?php echo e($secCnt ?: '—'); ?></td>
                    <td class="text-center"><?php echo e($cap ?: '—'); ?></td>
                    <td class="text-center"><?php echo e($cap > 0 ? $util . '%' : '—'); ?></td>
                    <td class="text-center"><?php echo e($cnt > 0 ? 'Active' : 'No data'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center"><?php echo e($grandEnrolled); ?></td>
                <td class="text-center"><?php echo e($grandSections); ?></td>
                <td class="text-center"><?php echo e($grandCap ?: '—'); ?></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/students-by-grade.blade.php ENDPATH**/ ?>