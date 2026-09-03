<?php $__env->startSection('title', 'Fee Management'); ?>

<?php $__env->startSection('skeleton'); ?>
<div class="skel skel-header-title"></div>
<div class="skel skel-header-sub"></div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div class="skel-row-gap" style="margin-bottom:0;">
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
    </div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div class="skel-row-gap" style="margin-bottom:0;">
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;min-width:150px;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
    </div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="section-header">
    <div>
        <h1><i class="bi bi-cash-coin" style="color:var(--gold);"></i> Fee Management</h1>
        <p>Configure tuition fees and payment options (4 Options: A, B, C, D)</p>
    </div>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-grid-3x3 me-2"></i>Base Fee Components</h6>
    </div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-lbl">Tuition Fee</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-tuition" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->tuition ?? 7505); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Misc/Reg/PTA Fee</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-misc" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->misc ?? 2800); ?>" oninput="recalculateFromBaseFees()">
                </div>
                <small class="text-muted" style="font-size:10px;">2000+700+100</small>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Insurance Fee</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-insurance" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->insurance ?? 300); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Electric Bill Fee</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->electric ?? 600); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-book me-2"></i>Books Fees by Grade Level</h6>
    </div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-lbl">Nursery / Kinder</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-books-nursery" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_nursery ?? 5400); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Grade 1 &amp; 2</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-books-grade1" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade1 ?? 5500); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Grade 3</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-books-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade3 ?? 5800); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Grade 4-6</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-books-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade4 ?? 6300); ?>" oninput="recalculateFromBaseFees()">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-credit-card me-2"></i>Payment Options Configuration</h6>
    </div>
    <div class="p-4">

        
        <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-cash-coin me-2"></i>Option A: Cash Basis</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-lbl">Cash Discount Amount</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-opta-discount" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->option_a_discount ?? 500); ?>">
                </div>
                <small class="text-muted" style="font-size:10px;">Discount applied for full cash payment</small>
            </div>
        </div>

        
        <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-calendar-month me-2"></i>Option B: Monthly Payment (All Levels)</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto)</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="text" id="fee-optb-monthly" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;background:#f0f4ff;" readonly value="0.00">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Tuition/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-monthly-tuition" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_monthly_tuition ?? 2000); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Electric/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-monthly-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_monthly_electric ?? 600); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-2" style="width:20%;">
                <label class="form-lbl">Nursery DP</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-dp-nursery" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_nursery ?? 3200); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:20%;">
                <label class="form-lbl">Kinder DP</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-dp-kinder" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_kinder ?? 3200); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:20%;">
                <label class="form-lbl">Grade 1-2 DP</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-dp-grade1" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade1 ?? 3300); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:20%;">
                <label class="form-lbl">Grade 3 DP</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-dp-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade3 ?? 3600); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:20%;">
                <label class="form-lbl">Grade 4-6 DP</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optb-dp-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade4 ?? 4100); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>

        
        <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-mortarboard me-2"></i>Option C: Elem. Pupils Only (Grade 1-6)</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto)</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="text" id="fee-optc-monthly" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;background:#f0f4ff;" readonly value="0.00">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Tuition/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-monthly-tuition" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_monthly_tuition ?? 833.89); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Misc/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-monthly-misc" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_monthly_misc ?? 311.11); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Electric/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-monthly-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_monthly_electric ?? 222.22); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3" style="width:20%;">
                <label class="form-lbl">Grade 1-2 Downpayment</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-dp-grade1" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_dp_grade1 ?? 5500); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-3" style="width:20%;">
                <label class="form-lbl">Grade 3 Downpayment</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-dp-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_dp_grade3 ?? 6000); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-3" style="width:20%;">
                <label class="form-lbl">Grade 4-6 Downpayment</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optc-dp-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_dp_grade4 ?? 6500); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>

        
        <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-balloon me-2"></i>Option D: Pre-Elem Only (Nursery/Kinder)</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto)</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="text" id="fee-optd-monthly" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;background:#f0f4ff;" readonly value="0.00">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Tuition/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optd-monthly-tuition" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_monthly_tuition ?? 833.89); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Misc/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optd-monthly-misc" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_monthly_misc ?? 311.11); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-2" style="width:18%;">
                <label class="form-lbl">Electric/Mo</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optd-monthly-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_monthly_electric ?? 222.22); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4" style="width:20%;">
                <label class="form-lbl">Nursery Downpayment</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optd-dp-nursery" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_dp_nursery ?? 4505); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
            <div class="col-md-4" style="width:20%;">
                <label class="form-lbl">Kinder Downpayment</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                    <input type="number" id="fee-optd-dp-kinder" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_dp_kinder ?? 4505); ?>" oninput="recalculateMonthlyFees()">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="alert alert-info mb-4" style="background:#e3f2fd; border:1px solid #90caf9; border-radius:8px; padding:16px;">
    <h6 style="font-weight:700; color:#1565c0; margin-bottom:12px;"><i class="bi bi-info-circle me-2"></i>Quick Fee Summary</h6>
    <div class="row" style="font-size:12px;">
        <div class="col-md-3">
            <strong>Nursery/Kinder:</strong> <span id="summary-nursery">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_nursery ?? 5400) + ($feeSettings->insurance ?? 300) + ($feeSettings->electric ?? 600), 2)); ?></span><br>
            <small class="text-muted" id="summary-nursery-breakdown">(<?php echo e(implode('+', [$feeSettings->tuition ?? 7505, $feeSettings->misc ?? 2800, $feeSettings->books_nursery ?? 5400, $feeSettings->insurance ?? 300, $feeSettings->electric ?? 600])); ?>)</small>
        </div>
        <div class="col-md-3">
            <strong>Grade 1-2:</strong> <span id="summary-grade1">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade1 ?? 5500) + ($feeSettings->insurance ?? 300) + ($feeSettings->electric ?? 600), 2)); ?></span><br>
            <small class="text-muted" id="summary-grade1-breakdown">(<?php echo e(implode('+', [$feeSettings->tuition ?? 7505, $feeSettings->misc ?? 2800, $feeSettings->books_grade1 ?? 5500, $feeSettings->insurance ?? 300, $feeSettings->electric ?? 600])); ?>)</small>
        </div>
        <div class="col-md-3">
            <strong>Grade 3:</strong> <span id="summary-grade3">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade3 ?? 5800) + ($feeSettings->insurance ?? 300) + ($feeSettings->electric ?? 600), 2)); ?></span><br>
            <small class="text-muted" id="summary-grade3-breakdown">(<?php echo e(implode('+', [$feeSettings->tuition ?? 7505, $feeSettings->misc ?? 2800, $feeSettings->books_grade3 ?? 5800, $feeSettings->insurance ?? 300, $feeSettings->electric ?? 600])); ?>)</small>
        </div>
        <div class="col-md-3">
            <strong>Grade 4-6:</strong> <span id="summary-grade4">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade4 ?? 6300) + ($feeSettings->insurance ?? 300) + ($feeSettings->electric ?? 600), 2)); ?></span><br>
            <small class="text-muted" id="summary-grade4-breakdown">(<?php echo e(implode('+', [$feeSettings->tuition ?? 7505, $feeSettings->misc ?? 2800, $feeSettings->books_grade4 ?? 6300, $feeSettings->insurance ?? 300, $feeSettings->electric ?? 600])); ?>)</small>
        </div>
    </div>
</div>


<div class="d-flex justify-content-end mb-4">
    <button id="btn-save-fees" class="btn-dash btn-primary" onclick="confirmSaveFeeSettings()">
        <span id="btn-save-fees-text"><i class="bi bi-floppy-fill me-1"></i> Save Fee Settings</span>
        <span id="btn-save-fees-loading" style="display:none;"><i class="bi bi-arrow-repeat me-1"></i> Saving...</span>
    </button>
</div>


<div class="content-card mb-4">
    <div class="content-card-header">
        <h6><i class="bi bi-table me-2"></i>Fee Breakdown Preview by Grade</h6>
        <small class="text-muted">Updates automatically as you change base fees above</small>
    </div>
    <div style="overflow-x:auto; padding:0 20px 20px;">
        <table class="dash-table" style="margin-top:16px;">
            <thead>
                <tr>
                    <th>Grade Level</th>
                    <th>Tuition</th>
                    <th>Misc</th>
                    <th>Insurance</th>
                    <th>Electric</th>
                    <th>Books</th>
                    <th style="font-weight:700; color:var(--blue);">Total Base</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $feeBreakdowns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade => $breakdown): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-weight:600; text-transform:capitalize;">
                        <?php echo e(str_replace(['grade', 'nursery', 'kindergarten', 'kinder'], ['Grade ', 'Nursery', 'Kindergarten', 'Kinder'], $grade)); ?>

                    </td>
                    <td id="preview-tuition-<?php echo e($grade); ?>">₱<?php echo e(number_format($breakdown['tuition'], 2)); ?></td>
                    <td id="preview-misc-<?php echo e($grade); ?>">₱<?php echo e(number_format($breakdown['misc'], 2)); ?></td>
                    <td id="preview-insurance-<?php echo e($grade); ?>">₱<?php echo e(number_format($breakdown['insurance'], 2)); ?></td>
                    <td id="preview-electric-<?php echo e($grade); ?>">₱<?php echo e(number_format($breakdown['electric'], 2)); ?></td>
                    <td id="preview-books-<?php echo e($grade); ?>">₱<?php echo e(number_format($breakdown['books'], 2)); ?></td>
                    <td id="preview-total-<?php echo e($grade); ?>" style="font-weight:700; color:var(--blue);">₱<?php echo e(number_format($breakdown['base_total'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="saveConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#e6a700,#b38600); border-radius:12px 12px 0 0; border-bottom:none;">
                <h5 class="modal-title" style="color:#fff; font-weight:700;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Save Fee Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <p style="color:#555; margin:0; font-size:14px;">
                    You are about to update fee settings.
                    <strong>This will affect all future enrollment fee calculations.</strong>
                    Existing enrollments already processed will not be changed.
                </p>
                <p style="color:#e65100; font-size:13px; margin-top:12px; margin-bottom:0;">
                    <i class="bi bi-info-circle me-1"></i>Are you sure you want to proceed?
                </p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:12px 24px; gap:8px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" id="confirmSaveBtn" class="btn btn-warning text-white" style="border-radius:8px; font-weight:600;" onclick="saveFeeSettings()">
                    <i class="bi bi-floppy-fill me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function recalculateMonthlyFees() {
    const optbTuition  = parseFloat(document.getElementById('fee-optb-monthly-tuition').value)  || 0;
    const optbElectric = parseFloat(document.getElementById('fee-optb-monthly-electric').value) || 0;
    document.getElementById('fee-optb-monthly').value = (optbTuition + optbElectric).toFixed(2);

    const optcTuition  = parseFloat(document.getElementById('fee-optc-monthly-tuition').value)  || 0;
    const optcMisc     = parseFloat(document.getElementById('fee-optc-monthly-misc').value)     || 0;
    const optcElectric = parseFloat(document.getElementById('fee-optc-monthly-electric').value) || 0;
    document.getElementById('fee-optc-monthly').value = (optcTuition + optcMisc + optcElectric).toFixed(2);

    const optdTuition  = parseFloat(document.getElementById('fee-optd-monthly-tuition').value)  || 0;
    const optdMisc     = parseFloat(document.getElementById('fee-optd-monthly-misc').value)     || 0;
    const optdElectric = parseFloat(document.getElementById('fee-optd-monthly-electric').value) || 0;
    document.getElementById('fee-optd-monthly').value = (optdTuition + optdMisc + optdElectric).toFixed(2);
}

function recalculateFromBaseFees() {
    const tuition      = parseFloat(document.getElementById('fee-tuition').value)       || 0;
    const misc         = parseFloat(document.getElementById('fee-misc').value)          || 0;
    const insurance    = parseFloat(document.getElementById('fee-insurance').value)     || 0;
    const electric     = parseFloat(document.getElementById('fee-electric').value)      || 0;
    const booksNursery = parseFloat(document.getElementById('fee-books-nursery').value) || 0;
    const booksGrade1  = parseFloat(document.getElementById('fee-books-grade1').value)  || 0;
    const booksGrade3  = parseFloat(document.getElementById('fee-books-grade3').value)  || 0;
    const booksGrade4  = parseFloat(document.getElementById('fee-books-grade4').value)  || 0;

    const fmt = n => '₱' + n.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 2});

    const nurseryTotal = tuition + misc + booksNursery + insurance + electric;
    const grade1Total  = tuition + misc + booksGrade1  + insurance + electric;
    const grade3Total  = tuition + misc + booksGrade3  + insurance + electric;
    const grade4Total  = tuition + misc + booksGrade4  + insurance + electric;

    document.getElementById('summary-nursery').textContent = fmt(nurseryTotal);
    document.getElementById('summary-nursery-breakdown').textContent = '(' + [tuition, misc, booksNursery, insurance, electric].join('+') + ')';
    document.getElementById('summary-grade1').textContent  = fmt(grade1Total);
    document.getElementById('summary-grade1-breakdown').textContent  = '(' + [tuition, misc, booksGrade1, insurance, electric].join('+') + ')';
    document.getElementById('summary-grade3').textContent  = fmt(grade3Total);
    document.getElementById('summary-grade3-breakdown').textContent  = '(' + [tuition, misc, booksGrade3, insurance, electric].join('+') + ')';
    document.getElementById('summary-grade4').textContent  = fmt(grade4Total);
    document.getElementById('summary-grade4-breakdown').textContent  = '(' + [tuition, misc, booksGrade4, insurance, electric].join('+') + ')';
}

function confirmSaveFeeSettings() {
    new bootstrap.Modal(document.getElementById('saveConfirmModal')).show();
}

async function saveFeeSettings() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('saveConfirmModal'));
    if (modal) modal.hide();

    const btnText    = document.getElementById('btn-save-fees-text');
    const btnLoading = document.getElementById('btn-save-fees-loading');
    btnText.style.display    = 'none';
    btnLoading.style.display = 'inline';

    const getVal = id => { const el = document.getElementById(id); return el ? (parseFloat(el.value) || 0) : 0; };

    const flatSettings = {
        tuition:               getVal('fee-tuition'),
        misc:                  getVal('fee-misc'),
        insurance:             getVal('fee-insurance'),
        electric:              getVal('fee-electric'),
        books_nursery:         getVal('fee-books-nursery'),
        books_grade1:          getVal('fee-books-grade1'),
        books_grade3:          getVal('fee-books-grade3'),
        books_grade4:          getVal('fee-books-grade4'),
        option_a_discount:     getVal('fee-opta-discount'),
        optb_monthly_tuition:  getVal('fee-optb-monthly-tuition'),
        optb_monthly_electric: getVal('fee-optb-monthly-electric'),
        optb_dp_nursery:       getVal('fee-optb-dp-nursery'),
        optb_dp_kinder:        getVal('fee-optb-dp-kinder'),
        optb_dp_grade1:        getVal('fee-optb-dp-grade1'),
        optb_dp_grade3:        getVal('fee-optb-dp-grade3'),
        optb_dp_grade4:        getVal('fee-optb-dp-grade4'),
        optc_monthly_tuition:  getVal('fee-optc-monthly-tuition'),
        optc_monthly_misc:     getVal('fee-optc-monthly-misc'),
        optc_monthly_electric: getVal('fee-optc-monthly-electric'),
        optc_dp_nursery:       0,
        optc_dp_kinder:        0,
        optc_dp_grade1:        getVal('fee-optc-dp-grade1'),
        optc_dp_grade3:        getVal('fee-optc-dp-grade3'),
        optc_dp_grade4:        getVal('fee-optc-dp-grade4'),
        optd_monthly_tuition:  getVal('fee-optd-monthly-tuition'),
        optd_monthly_misc:     getVal('fee-optd-monthly-misc'),
        optd_monthly_electric: getVal('fee-optd-monthly-electric'),
        optd_dp_nursery:       getVal('fee-optd-dp-nursery'),
        optd_dp_kinder:        getVal('fee-optd-dp-kinder'),
        optd_dp_grade1:        0,
        optd_dp_grade3:        0,
        optd_dp_grade4:        0,
    };

    try {
        const response = await fetch('<?php echo e(route("finance.fees.update")); ?>', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(flatSettings)
        });
        const data = await response.json().catch(() => ({}));
        if (response.ok && data.success) {
            alert('Fee settings saved successfully.');
            window.location.reload();
        } else {
            alert('Failed to save fee settings: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        alert('Error saving fee settings: ' + err.message);
    } finally {
        btnText.style.display    = 'inline';
        btnLoading.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    recalculateMonthlyFees();
    recalculateFromBaseFees();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('finance.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\finance\fees.blade.php ENDPATH**/ ?>