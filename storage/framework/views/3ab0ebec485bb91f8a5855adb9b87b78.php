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
            <div class="fin-stat-value"><?php echo e($approvedDocs); ?></div>
            <div class="fin-stat-label">Approved</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($pendingDocs); ?></div>
            <div class="fin-stat-label">Pending Review</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($rejectedDocs); ?></div>
            <div class="fin-stat-label">Rejected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value"><?php echo e($totalDocs); ?></div>
            <div class="fin-stat-label">Total Documents</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Document Type</th>
                <th class="text-center">Status</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($doc->user->name ?? '—'); ?></td>
                    <td><?php echo e(ucwords(str_replace('_', ' ', $doc->document_type ?? ''))); ?></td>
                    <td class="text-center"><?php echo e(ucfirst($doc->status ?? '—')); ?></td>
                    <td><?php echo e($doc->created_at ? $doc->created_at->format('M j, Y') : '—'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center">No document records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php echo $__env->make('pdf.shared._bottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/pdf/admin/document-compliance.blade.php ENDPATH**/ ?>