<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF9 — <?php echo e($student->name); ?> — <?php echo e($schoolYear); ?></title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        color: #000;
        background: #fff;
    }

    .page {
        width: 215.9mm;
        min-height: 279.4mm;
        margin: 0 auto;
        padding: 10mm 12mm;
    }

    /* ── HEADER ── */
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    .header-table td { vertical-align: middle; }
    .header-logo { width: 55px; text-align: center; }
    .header-logo img { width: 50px; height: 50px; object-fit: contain; }
    .header-center { text-align: center; padding: 0 6px; }
    .header-center .republic { font-size: 7.5pt; }
    .header-center .deped    { font-size: 8pt; font-weight: bold; }
    .header-center .school   { font-size: 10pt; font-weight: bold; text-transform: uppercase; }
    .header-center .address  { font-size: 7.5pt; }
    .header-right { width: 55px; text-align: center; }
    .header-right img { width: 50px; height: 50px; object-fit: contain; }

    .form-title {
        text-align: center;
        font-size: 9pt;
        font-weight: bold;
        border: 1.5px solid #000;
        padding: 3px;
        margin: 6px 0 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .subtitle {
        text-align: center;
        font-size: 8pt;
        margin-bottom: 6px;
        font-style: italic;
    }

    /* ── STUDENT INFO ── */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    .info-table td {
        border: 0.5px solid #000;
        padding: 2px 4px;
        font-size: 8pt;
        vertical-align: bottom;
    }
    .info-label { font-size: 6.5pt; color: #333; display: block; }
    .info-value { font-size: 9pt; font-weight: bold; border-bottom: 0.5px solid #000; min-height: 14px; display: block; }

    /* ── GRADES TABLE ── */
    .section-header {
        background: #1a3a6c;
        color: #fff;
        font-weight: bold;
        font-size: 8pt;
        padding: 3px 6px;
        margin-top: 6px;
        margin-bottom: 0;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .grades-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
        font-size: 8pt;
    }
    .grades-table th {
        background: #d9e1f2;
        border: 0.5px solid #000;
        padding: 3px 4px;
        text-align: center;
        font-size: 7.5pt;
        font-weight: bold;
    }
    .grades-table td {
        border: 0.5px solid #000;
        padding: 2px 4px;
        font-size: 8pt;
    }
    .grades-table td.subject-name { font-weight: 500; }
    .grades-table td.grade-cell   { text-align: center; font-weight: bold; }
    .grades-table td.remarks-cell { text-align: center; font-size: 7.5pt; }
    .grades-table tr:nth-child(even) { background: #f7f8fc; }

    .passed  { color: #155724; }
    .failed  { color: #721c24; }
    .pending { color: #856404; font-style: italic; }

    /* ── ATTENDANCE ── */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
        font-size: 8pt;
    }
    .attendance-table th {
        background: #d9e1f2;
        border: 0.5px solid #000;
        padding: 3px 4px;
        text-align: center;
        font-size: 7.5pt;
        font-weight: bold;
    }
    .attendance-table td {
        border: 0.5px solid #000;
        padding: 2px 4px;
        text-align: center;
        font-size: 8pt;
    }

    /* ── REMARKS / ACTION TAKEN ── */
    .action-box {
        border: 0.5px solid #000;
        padding: 4px 6px;
        margin-bottom: 5px;
        font-size: 8pt;
    }
    .action-box table { width: 100%; }
    .action-box td { padding: 1px 4px; font-size: 8pt; vertical-align: middle; }
    .checkbox-cell { width: 14px; text-align: center; }
    .checkbox { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; vertical-align: middle; text-align: center; line-height: 10px; font-size: 8pt; }
    .checkbox.checked::after { content: '✓'; font-weight: bold; }

    /* ── SIGNATURE ── */
    .sig-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .sig-table td { padding: 2px 6px; font-size: 8pt; vertical-align: bottom; width: 50%; }
    .sig-line { border-top: 0.5px solid #000; margin-top: 24px; text-align: center; padding-top: 2px; font-size: 7.5pt; }
    .sig-label { font-size: 7pt; color: #555; text-align: center; }

    .footer-note {
        font-size: 7pt;
        color: #555;
        margin-top: 6px;
        text-align: center;
        font-style: italic;
    }

    /* ── PRINT ── */
    @media print {
        body { margin: 0; }
        .page { margin: 0; padding: 8mm 10mm; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
</head>
<body>


<div class="no-print" style="background:#1a3a6c;padding:8px 16px;display:flex;align-items:center;gap:12px;">
    <button onclick="window.print()" style="background:#f5a623;color:#fff;border:none;border-radius:6px;padding:7px 20px;font-size:13px;font-weight:700;cursor:pointer;">
        🖨 Print / Save as PDF
    </button>
    <button onclick="window.close()" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:6px;padding:7px 16px;font-size:13px;cursor:pointer;">
        ✕ Close
    </button>
    <span style="color:rgba(255,255,255,0.7);font-size:12px;">SF9 — Learner's Progress Report Card | <?php echo e($student->name); ?> | S.Y. <?php echo e($schoolYear); ?></span>
</div>

<div class="page">

    
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <?php $logoPath = public_path('images/logo.png'); ?>
                <?php if(file_exists($logoPath)): ?>
                <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents($logoPath))); ?>" alt="ILC">
                <?php endif; ?>
            </td>
            <td class="header-center">
                <div class="republic">Republic of the Philippines</div>
                <div class="deped">Department of Education</div>
                <div style="font-size:7.5pt;">Region III — Central Luzon | Schools Division of Nueva Ecija</div>
                <div class="school">IEMELIF Learning Center</div>
                <div class="address">General Tinio, Nueva Ecija</div>
            </td>
            <td class="header-right">
                
                <div style="width:50px;height:50px;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:6pt;color:#999;text-align:center;">DepEd Logo</div>
            </td>
        </tr>
    </table>

    <div class="form-title">School Form 9 (SF9) — Learner's Progress Report Card</div>
    <div class="subtitle">MATATAG K to 6 Curriculum — School Year <?php echo e($schoolYear); ?></div>

    
    <table class="info-table">
        <tr>
            <td style="width:45%;">
                <span class="info-label">Learner's Name (Last, First, Middle)</span>
                <span class="info-value"><?php echo e($profile ? strtoupper("{$profile->last_name}, {$profile->first_name} {$profile->middle_name}") : strtoupper($student->name)); ?></span>
            </td>
            <td style="width:20%;">
                <span class="info-label">Learner Reference No. (LRN)</span>
                <span class="info-value"><?php echo e($student->lrn ?? '—'); ?></span>
            </td>
            <td style="width:10%;">
                <span class="info-label">Grade</span>
                <span class="info-value"><?php echo e($gradeLabel); ?></span>
            </td>
            <td style="width:25%;">
                <span class="info-label">Section</span>
                <span class="info-value"><?php echo e($sectionName); ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Complete Address</span>
                <span class="info-value" style="font-size:8pt;"><?php echo e($address ?? '—'); ?></span>
            </td>
            <td>
                <span class="info-label">Date of Birth</span>
                <span class="info-value"><?php echo e($profile?->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('F d, Y') : '—'); ?></span>
            </td>
            <td>
                <span class="info-label">Age</span>
                <span class="info-value"><?php echo e($age ?? '—'); ?></span>
            </td>
            <td>
                <span class="info-label">Sex</span>
                <span class="info-value"><?php echo e($profile ? ucfirst($profile->gender ?? '—') : '—'); ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="info-label">Parent / Guardian Name</span>
                <span class="info-value"><?php echo e($guardian?->name ?? '—'); ?></span>
            </td>
            <td colspan="2">
                <span class="info-label">Relationship / Contact</span>
                <span class="info-value"><?php echo e(($guardian?->relationship ?? '—') . ' / ' . ($guardian?->contact ?? '—')); ?></span>
            </td>
        </tr>
    </table>

    
    <div class="section-header">Academic Performance</div>

    <?php if($isDescriptive): ?>
    
    <table class="grades-table">
        <thead>
            <tr>
                <th style="text-align:left;width:38%;">Learning Area / Subject</th>
                <th style="width:20%;">Term 1</th>
                <th style="width:20%;">Term 2</th>
                <th style="width:22%;">Term 3</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="subject-name"><?php echo e($sub['name']); ?></td>
                <td class="grade-cell"><?php echo e($sub['term1_desc'] ?? '—'); ?></td>
                <td class="grade-cell"><?php echo e($sub['term2_desc'] ?? '—'); ?></td>
                <td class="grade-cell"><?php echo e($sub['term3_desc'] ?? '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div style="font-size:7pt;color:#555;margin-bottom:4px;">
        Rating Scale: <strong>O</strong> — Outstanding &nbsp;|&nbsp; <strong>VS</strong> — Very Satisfactory &nbsp;|&nbsp; <strong>S</strong> — Satisfactory &nbsp;|&nbsp; <strong>FS</strong> — Fairly Satisfactory &nbsp;|&nbsp; <strong>DNME</strong> — Did Not Meet Expectations
    </div>

    <?php else: ?>
    
    <table class="grades-table">
        <thead>
            <tr>
                <th style="text-align:left;width:34%;">Learning Area / Subject</th>
                <th style="width:12%;">Term 1</th>
                <th style="width:12%;">Term 2</th>
                <th style="width:12%;">Term 3</th>
                <th style="width:14%;">Final Grade</th>
                <th style="width:16%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $grades  = array_filter([$sub['term1'], $sub['term2'], $sub['term3']], fn($v) => $v !== null);
                $final   = count($grades) ? round(array_sum($grades) / count($grades)) : null;
                $remarks = $final !== null ? \App\Models\Grade::getRemarks($final) : '—';
                $cls     = $final === null ? 'pending' : ($final >= 75 ? 'passed' : 'failed');
            ?>
            <tr>
                <td class="subject-name"><?php echo e($sub['name']); ?></td>
                <td class="grade-cell <?php echo e($sub['term1'] !== null ? ($sub['term1'] >= 75 ? 'passed' : 'failed') : 'pending'); ?>"><?php echo e($sub['term1'] ?? '—'); ?></td>
                <td class="grade-cell <?php echo e($sub['term2'] !== null ? ($sub['term2'] >= 75 ? 'passed' : 'failed') : 'pending'); ?>"><?php echo e($sub['term2'] ?? '—'); ?></td>
                <td class="grade-cell <?php echo e($sub['term3'] !== null ? ($sub['term3'] >= 75 ? 'passed' : 'failed') : 'pending'); ?>"><?php echo e($sub['term3'] ?? '—'); ?></td>
                <td class="grade-cell <?php echo e($cls); ?>"><?php echo e($final ?? '—'); ?></td>
                <td class="remarks-cell <?php echo e($cls); ?>"><?php echo e($remarks); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($gwa !== null): ?>
            <tr style="background:#e8f0fb;font-weight:bold;">
                <td><strong>General Weighted Average (GWA)</strong></td>
                <td class="grade-cell"><?php echo e($term1Avg ?? '—'); ?></td>
                <td class="grade-cell"><?php echo e($term2Avg ?? '—'); ?></td>
                <td class="grade-cell"><?php echo e($term3Avg ?? '—'); ?></td>
                <td class="grade-cell" style="font-size:10pt;"><?php echo e($gwa); ?></td>
                <td class="remarks-cell <?php echo e($gwa >= 75 ? 'passed' : 'failed'); ?>"><?php echo e(\App\Models\Grade::getRemarks($gwa)); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>

    
    <div class="section-header">Action Taken</div>
    <div class="action-box">
        <table>
            <tr>
                <td class="checkbox-cell"><div class="checkbox <?php echo e($actionTaken === 'Promoted' ? 'checked' : ''); ?>"></div></td>
                <td style="width:22%;">Promoted to Grade <?php echo e($nextGradeLabel); ?></td>
                <td class="checkbox-cell"><div class="checkbox <?php echo e($actionTaken === 'Retained' ? 'checked' : ''); ?>"></div></td>
                <td style="width:22%;">Retained in Grade <?php echo e($gradeLabel); ?></td>
                <td class="checkbox-cell"><div class="checkbox <?php echo e($actionTaken === 'Graduated' ? 'checked' : ''); ?>"></div></td>
                <td style="width:22%;">Graduated</td>
                <td class="checkbox-cell"><div class="checkbox <?php echo e($actionTaken === 'Dropped' ? 'checked' : ''); ?>"></div></td>
                <td>Dropped</td>
            </tr>
        </table>
    </div>

    
    <div class="section-header">Certification</div>
    <div style="border:0.5px solid #000;padding:4px 6px;font-size:8pt;margin-bottom:5px;">
        I certify that the information contained in this form is true and accurate based on official records maintained by this school.
    </div>

    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line"><?php echo e($teacherName); ?></div>
                <div class="sig-label">Signature over Printed Name of Class Adviser</div>
            </td>
            <td>
                <div class="sig-line">&nbsp;</div>
                <div class="sig-label">Signature of Parent / Guardian</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top:10px;">
                <div class="sig-line">&nbsp;</div>
                <div class="sig-label">Date</div>
            </td>
            <td style="padding-top:10px;">
                <div class="sig-line">&nbsp;</div>
                <div class="sig-label">Principal's Signature over Printed Name</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Generated by IEMELIF Learning Center Student Information System &mdash; <?php echo e(now()->format('F d, Y h:i A')); ?>

    </div>

</div>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\teacher\sf9.blade.php ENDPATH**/ ?>