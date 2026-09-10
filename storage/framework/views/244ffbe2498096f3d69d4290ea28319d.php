<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF5 — <?php echo e($section->name); ?> — <?php echo e($schoolYear); ?></title>
<style>
    /* See the same rule in teacher/sf9.blade.php — without this, dompdf's
       own default page margin stacks on top of the full-page-sized .page
       box below and pushes an entirely blank second page onto every PDF. */
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        font-size: 8pt;
        color: #000;
        background: #fff;
    }

    .page {
        width: 355.6mm;
        /* No min-height: a box asked to be exactly one physical page tall
           overflows by a hair under dompdf's own rounding and pushes an
           entirely blank extra page onto every PDF. Content naturally
           fills the page anyway. */
        margin: 0 auto;
        padding: 8mm 10mm;
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

    /* ── META ── */
    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .meta-table td { border: 0.5px solid #000; padding: 3px 6px; font-size: 8pt; }
    .meta-table .lbl { font-weight: bold; background: #d9e1f2; color: #1a3a6c; width: 12%; }
    .meta-table .val { width: 21%; }

    /* ── ROSTER TABLE ── */
    .roster-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
    .roster-table th {
        background: #1a3a6c; color: #fff;
        border: 0.5px solid #000; padding: 3px 4px;
        text-align: center; font-size: 7pt; font-weight: bold;
        vertical-align: middle;
    }
    .roster-table th.sub-term {
        background: #2c5282; font-size: 6.5pt;
    }
    .roster-table td {
        border: 0.5px solid #000; padding: 2px 4px;
        text-align: center; font-size: 7.5pt;
    }
    .roster-table td.name-cell { text-align: left; font-weight: 500; }
    .roster-table tr:nth-child(even) td { background: #f7f8fc; }
    .roster-table td.avg-pass { font-weight: bold; color: #155724; }
    .roster-table td.avg-fail { font-weight: bold; color: #721c24; }

    /* ── SUMMARY ── */
    .summary-box {
        margin-top: 8px; padding: 6px 10px;
        border: 0.5px solid #000; background: #d9e1f2;
        font-size: 8pt;
    }
    .summary-box b { color: #1a3a6c; }
    .summary-box span { margin-right: 22px; }

    /* ── SIGNATURE ── */
    .sig-table { width: 100%; border-collapse: collapse; margin-top: 18px; }
    .sig-table td { padding: 2px 6px; font-size: 8pt; vertical-align: bottom; width: 33.33%; }
    .sig-line { border-top: 0.5px solid #000; margin-top: 24px; text-align: center; padding-top: 2px; font-size: 7.5pt; }
    .sig-label { font-size: 7pt; color: #555; text-align: center; }

    .footer-note {
        font-size: 7pt; color: #555; margin-top: 8px;
        text-align: center; font-style: italic;
    }

    /* ── PRINT ── */
    @media print {
        body { margin: 0; }
        .page { margin: 0; padding: 6mm 8mm; width: 100%; }
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
    <span style="color:rgba(255,255,255,0.7);font-size:12px;">SF5 — Report on Promotions and Level of Proficiency | <?php echo e($section->name); ?> | S.Y. <?php echo e($schoolYear); ?></span>
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

    <div class="form-title">School Form 5 (SF5) — Report on Promotions and Level of Proficiency</div>

    
    <table class="meta-table">
        <tr>
            <td class="lbl">School Year</td><td class="val"><?php echo e($schoolYear); ?></td>
            <td class="lbl">Grade Level</td><td class="val"><?php echo e($gradeLabel); ?></td>
        </tr>
        <tr>
            <td class="lbl">Section</td><td class="val"><?php echo e($section->name); ?></td>
            <td class="lbl">Adviser</td><td class="val"><?php echo e($teacher->name); ?></td>
        </tr>
        <tr>
            <td class="lbl">Generated</td><td class="val"><?php echo e(now()->format('F d, Y')); ?></td>
            <td class="lbl">Total Learners</td><td class="val"><?php echo e(count($rows)); ?></td>
        </tr>
    </table>

    
    <table class="roster-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:3%;">No.</th>
                <th rowspan="2" style="width:9%;">LRN</th>
                <th rowspan="2" style="width:18%;">Learner's Name</th>
                <th rowspan="2" style="width:4%;">Sex</th>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th colspan="3"><?php echo e($sub->name); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th rowspan="2" style="width:6%;">Final<br>Avg</th>
                <th rowspan="2" style="width:9%;">Action Taken</th>
            </tr>
            <tr>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="sub-term">T1</th>
                    <th class="sub-term">T2</th>
                    <th class="sub-term">T3</th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i + 1); ?></td>
                <td><?php echo e($row['lrn']); ?></td>
                <td class="name-cell"><?php echo e($row['name']); ?></td>
                <td><?php echo e($row['sex']); ?></td>
                <?php $__currentLoopData = $row['subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $terms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td><?php echo e($terms[1]); ?></td>
                    <td><?php echo e($terms[2]); ?></td>
                    <td><?php echo e($terms[3]); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td class="<?php echo e($row['avg'] !== null ? ($row['avg'] >= 75 ? 'avg-pass' : 'avg-fail') : ''); ?>"><?php echo e($row['avg'] ?? ''); ?></td>
                <td><?php echo e($row['action']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="<?php echo e(6 + count($subjects) * 3); ?>" style="padding:14px;">No enrolled learners in this section.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <div class="summary-box">
        <b>SUMMARY:</b>
        <span>Total Learners: <?php echo e(count($rows)); ?></span>
        <?php if(!$isDescriptive): ?>
            <span>Promoted: <?php echo e($promotedCount); ?></span>
            <span>Retained: <?php echo e($retainedCount); ?></span>
            <?php if($graduatedCount > 0): ?>
                <span>Graduated: <?php echo e($graduatedCount); ?></span>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line"><?php echo e($teacher->name); ?></div>
                <div class="sig-label">Class Adviser</div>
            </td>
            <td>
                <div class="sig-line">&nbsp;</div>
                <div class="sig-label">Principal / School Head</div>
            </td>
            <td>
                <div class="sig-line"><?php echo e(now()->format('F d, Y')); ?></div>
                <div class="sig-label">Date Generated</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">This is a system-generated report. IEMELIF Learning Center — General Tinio, Nueva Ecija.</div>

</div>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\teacher\sf5.blade.php ENDPATH**/ ?>