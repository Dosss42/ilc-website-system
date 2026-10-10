    <div id="section-reports" class="dash-section" style="display:none;">
        
        <?php if(isset($rptTotalStudents)): ?>
            <?php echo $__env->make('admin.sections.reports-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/reports.blade.php ENDPATH**/ ?>