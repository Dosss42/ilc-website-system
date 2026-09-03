<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promissory Note — <?php echo e($note->reference_number); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #fff; padding: 40px; }

        .header { text-align: center; border-bottom: 3px solid #1a3a6c; padding-bottom: 16px; margin-bottom: 24px; }
        .header img { width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto 10px; }
        .school-name { font-size: 20px; font-weight: 700; color: #1a3a6c; }
        .school-addr { font-size: 12px; color: #555; margin-top: 3px; }
        .doc-title { font-size: 16px; font-weight: 700; color: #1a3a6c; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; }
        .ref-num { font-size: 11px; color: #888; margin-top: 4px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px; margin: 20px 0; }
        .info-item { border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .info-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 13px; font-weight: 600; color: #1a3a6c; margin-top: 2px; }

        .amount-box { background: #f0f4f8; border: 2px solid #1a3a6c; border-radius: 8px; padding: 16px 20px; margin: 20px 0; text-align: center; }
        .amount-label { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .amount-value { font-size: 28px; font-weight: 700; color: #1a3a6c; margin-top: 4px; }
        .amount-sub { font-size: 11px; color: #888; margin-top: 4px; }

        .promise-box { border: 2px dashed #f5a623; border-radius: 8px; padding: 14px 20px; margin: 16px 0; background: #fffbf0; }
        .promise-label { font-size: 11px; font-weight: 700; color: #c8860a; text-transform: uppercase; }
        .promise-date { font-size: 18px; font-weight: 700; color: #1a3a6c; margin-top: 4px; }

        .remarks-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px 16px; margin: 16px 0; min-height: 50px; }
        .remarks-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 6px; }

        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-pending   { background: #e3f2fd; color: #1565c0; }
        .status-fulfilled { background: #e8f5e9; color: #2e7d32; }
        .status-broken    { background: #ffebee; color: #c62828; }
        .status-extended  { background: #fff3e0; color: #e65100; }

        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; }
        .sig-line { border-top: 1px solid #333; padding-top: 6px; text-align: center; }
        .sig-label { font-size: 11px; color: #555; }
        .sig-name  { font-size: 12px; font-weight: 700; color: #1a3a6c; margin-top: 2px; }

        .footer { margin-top: 32px; border-top: 1px solid #ddd; padding-top: 10px; display: flex; justify-content: space-between; font-size: 10px; color: #aaa; }

        .notice { background: #fff8e1; border-left: 3px solid #f5a623; padding: 10px 14px; margin: 16px 0; font-size: 12px; color: #555; border-radius: 0 4px 4px 0; }

        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#1a3a6c;color:#fff;padding:10px 20px;border-radius:6px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
    <span style="font-weight:600;font-size:13px;">Promissory Note — <?php echo e($note->reference_number); ?></span>
    <button onclick="window.print()" style="background:#f5a623;color:#1a3a6c;border:none;border-radius:5px;padding:7px 18px;font-weight:700;font-size:13px;cursor:pointer;">
        🖨️ Print
    </button>
</div>

<div class="header">
    <img src="/images/logo.png" alt="ILC Logo" onerror="this.style.display='none'">
    <div class="school-name">IEMELIF LEARNING CENTER</div>
    <div class="school-addr">General Tinio, Nueva Ecija</div>
    <div class="doc-title">Promissory Note</div>
    <div class="ref-num">Reference No.: <?php echo e($note->reference_number); ?></div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <span style="font-size:11px;color:#888;">Date Issued:</span>
        <span style="font-weight:700;margin-left:6px;"><?php echo e($note->date_issued->format('F d, Y')); ?></span>
    </div>
    <div>
        <span style="font-size:11px;color:#888;">Status:</span>
        <span class="status-badge status-<?php echo e($note->status); ?>" style="margin-left:6px;"><?php echo e(ucfirst($note->status)); ?></span>
    </div>
</div>

<div class="info-grid">
    <div class="info-item">
        <div class="info-label">Student Name</div>
        <div class="info-value"><?php echo e($note->student->name ?? '—'); ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Parent / Guardian</div>
        <div class="info-value"><?php echo e($note->parent_guardian ?: '—'); ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Grade Level</div>
        <div class="info-value"><?php echo e(ucfirst(str_replace(['grade','nursery','kindergarten'], ['Grade ','Nursery','Kindergarten'], $note->enrollment->student_data['grade_level'] ?? '—'))); ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">School Year</div>
        <div class="info-value">S.Y. <?php echo e($note->enrollment->school_year ?? '—'); ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Enrollment Reference</div>
        <div class="info-value"><?php echo e($note->enrollment->reference_number ?? '—'); ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Received By</div>
        <div class="info-value"><?php echo e($note->createdBy->name ?? '—'); ?></div>
    </div>
</div>

<div class="amount-box">
    <div class="amount-label">Amount Promised to Pay</div>
    <div class="amount-value">₱<?php echo e(number_format($note->amount_promised, 2)); ?></div>
    <?php if($note->amount_overdue > 0): ?>
        <div class="amount-sub">Outstanding balance at time of note: ₱<?php echo e(number_format($note->amount_overdue, 2)); ?></div>
    <?php endif; ?>
</div>

<div class="promise-box">
    <div class="promise-label">⏰ Promised Payment Date</div>
    <div class="promise-date"><?php echo e($note->promise_date->format('F d, Y')); ?></div>
    <?php if($note->extended_date): ?>
        <div style="font-size:11px;color:#888;margin-top:4px;">Extended to: <?php echo e($note->extended_date->format('F d, Y')); ?></div>
    <?php endif; ?>
    <?php if($note->fulfilled_at): ?>
        <div style="font-size:11px;color:#2e7d32;margin-top:4px;font-weight:700;">✓ Fulfilled on <?php echo e($note->fulfilled_at->format('F d, Y')); ?></div>
    <?php endif; ?>
</div>

<?php if($note->remarks): ?>
<div class="remarks-box">
    <div class="remarks-label">Remarks</div>
    <div><?php echo e($note->remarks); ?></div>
</div>
<?php endif; ?>

<div class="notice">
    This promissory note serves as a written commitment by the parent/guardian to settle the outstanding school fees on the specified date. Failure to comply may result in suspension of privileges and access to school records.
</div>

<div class="signatures">
    <div>
        <div style="height:50px;"></div>
        <div class="sig-line">
            <div class="sig-name"><?php echo e($note->parent_guardian ?: '________________________'); ?></div>
            <div class="sig-label">Parent / Guardian Signature</div>
        </div>
    </div>
    <div>
        <div style="height:50px;"></div>
        <div class="sig-line">
            <div class="sig-name"><?php echo e($note->createdBy->name ?? '________________________'); ?></div>
            <div class="sig-label">Finance Officer / Received By</div>
        </div>
    </div>
</div>

<div class="footer">
    <span>IEMELIF Learning Center — Official Promissory Note</span>
    <span><?php echo e($note->reference_number); ?> | Printed: <?php echo e(now()->format('F d, Y')); ?></span>
</div>

</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\promissory-note-print.blade.php ENDPATH**/ ?>