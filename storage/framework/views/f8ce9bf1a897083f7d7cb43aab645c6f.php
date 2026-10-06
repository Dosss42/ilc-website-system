    <div id="section-grade-oversight" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-patch-check-fill" style="color:var(--gold);"></i> Grade Oversight</h1>
                <p>Review and approve grade submissions from teachers before students can view them</p>
            </div>
            <button class="btn-primary" onclick="loadGradeSubmissions()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>

        
        <div id="gradeOversightEmpty" style="display:none; text-align:center; padding:60px 20px; color:var(--muted);">
            <i class="bi bi-check2-all" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.2;"></i>
            <div style="font-size:15px; font-weight:600; margin-bottom:4px;">No Pending Submissions</div>
            <div style="font-size:12px;">All grade submissions have been reviewed.</div>
        </div>

        <div id="gradeSubmissionsList"></div>
    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/grade-oversight.blade.php ENDPATH**/ ?>