    <div id="section-fees" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Fee Management</h1>
                <p>Configure tuition fees and payment options (4 Options: A, B, C, D)</p>
            </div>
        </div>

        
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
                            <input type="number" id="fee-insurance" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->insurance ?? 150); ?>" oninput="recalculateFromBaseFees()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Electric Bill Fee</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->electric ?? 2000); ?>" oninput="recalculateFromBaseFees()">
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
                            <input type="number" id="fee-books-nursery" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_nursery ?? 3550); ?>" oninput="recalculateFromBaseFees()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Grade 1 & 2</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-books-grade1" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade1 ?? 4550); ?>" oninput="recalculateFromBaseFees()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Grade 3</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-books-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade3 ?? 5050); ?>" oninput="recalculateFromBaseFees()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Grade 4-6</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-books-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->books_grade4 ?? 5550); ?>" oninput="recalculateFromBaseFees()">
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
                            <input type="number" id="fee-opta-discount" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->option_a_discount ?? 1501); ?>">
                        </div>
                        <small class="text-muted" style="font-size:10px;">~20% discount on total</small>
                    </div>
                </div>

                
                <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-calendar-month me-2"></i>Option B: Monthly Payment (All Levels)</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-2" style="width:18%;">
                        <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto-calculated)</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="text" id="fee-optb-monthly" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;background:#f0f4ff;" readonly value="0.00">
                        </div>
                    </div>
                    <div class="col-md-2" style="width:18%;">
                        <label class="form-lbl">Tuition/Mo</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-monthly-tuition" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_monthly_tuition ?? 833.89); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                    </div>
                    <div class="col-md-2" style="width:18%;">
                        <label class="form-lbl">Electric/Mo</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-monthly-electric" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_monthly_electric ?? 222.22); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-2" style="width:20%;">
                        <label class="form-lbl">Nursery DP</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-dp-nursery" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_nursery ?? 6500); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Misc/REG./PTA)</small>
                    </div>
                    <div class="col-md-2" style="width:20%;">
                        <label class="form-lbl">Kinder DP</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-dp-kinder" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_kinder ?? 6500); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Misc/REG./PTA)</small>
                    </div>
                    <div class="col-md-2" style="width:20%;">
                        <label class="form-lbl">Grade 1-2 DP</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-dp-grade1" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade1 ?? 7500); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Misc/REG./PTA)</small>
                    </div>
                    <div class="col-md-2" style="width:20%;">
                        <label class="form-lbl">Grade 3 DP</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-dp-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade3 ?? 8000); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Misc/REG./PTA)</small>
                    </div>
                    <div class="col-md-2" style="width:20%;">
                        <label class="form-lbl">Grade 4-6 DP</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optb-dp-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optb_dp_grade4 ?? 8500); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Misc/REG./PTA)</small>
                    </div>
                </div>

                
                <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-mortarboard me-2"></i>Option C: Elem. Pupils Only (Grade 1-6)</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-2" style="width:18%;">
                        <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto-calculated)</span></label>
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
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Registration/PTA)</small>
                    </div>
                    <div class="col-md-3" style="width:20%;">
                        <label class="form-lbl">Grade 3 Downpayment</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optc-dp-grade3" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_dp_grade3 ?? 6000); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Registration/PTA)</small>
                    </div>
                    <div class="col-md-3" style="width:20%;">
                        <label class="form-lbl">Grade 4-6 Downpayment</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optc-dp-grade4" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optc_dp_grade4 ?? 6500); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Registration/PTA)</small>
                    </div>
                </div>

                
                <h6 class="mb-3" style="font-weight:700; color:var(--text);"><i class="bi bi-balloon me-2"></i>Option D: Pre-Elem Only (Nursery/Kinder)</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-2" style="width:18%;">
                        <label class="form-lbl">Monthly Total <span class="text-muted" style="font-size:10px;">(auto-calculated)</span></label>
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
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Registration/PTA)</small>
                    </div>
                    <div class="col-md-4" style="width:20%;">
                        <label class="form-lbl">Kinder Downpayment</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--blue-pale);border:1.5px solid var(--border);border-right:none;font-size:12px;">₱</span>
                            <input type="number" id="fee-optd-dp-kinder" class="form-fld" style="border-top-left-radius:0;border-bottom-left-radius:0;" min="0" step="0.01" value="<?php echo e($feeSettings->optd_dp_kinder ?? 4505); ?>" oninput="recalculateMonthlyFees()">
                        </div>
                        <small class="text-muted" style="font-size:9px;">(Books + Insurance + Registration/PTA)</small>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="alert alert-info mb-4" style="background:#e3f2fd;border:1px solid #90caf9;border-radius:8px;padding:16px;">
            <h6 style="font-weight:700;color:#1565c0;margin-bottom:12px;"><i class="bi bi-info-circle me-2"></i>Fee Summary Reference</h6>
            <div class="row" style="font-size:12px;">
                <div class="col-md-3">
                    <strong>Nursery/Kinder:</strong> <span id="summary-nursery">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_nursery ?? 3550) + ($feeSettings->insurance ?? 150) + ($feeSettings->electric ?? 2000), 2)); ?></span><br>
                    <small class="text-muted" id="summary-nursery-breakdown">(<?php echo e(($feeSettings->tuition ?? 7505) . '+' . ($feeSettings->misc ?? 2800) . '+' . ($feeSettings->books_nursery ?? 3550) . '+' . ($feeSettings->insurance ?? 150) . '+' . ($feeSettings->electric ?? 2000)); ?>)</small>
                </div>
                <div class="col-md-3">
                    <strong>Grade 1-2:</strong> <span id="summary-grade1">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade1 ?? 4550) + ($feeSettings->insurance ?? 150) + ($feeSettings->electric ?? 2000), 2)); ?></span><br>
                    <small class="text-muted" id="summary-grade1-breakdown">(<?php echo e(($feeSettings->tuition ?? 7505) . '+' . ($feeSettings->misc ?? 2800) . '+' . ($feeSettings->books_grade1 ?? 4550) . '+' . ($feeSettings->insurance ?? 150) . '+' . ($feeSettings->electric ?? 2000)); ?>)</small>
                </div>
                <div class="col-md-3">
                    <strong>Grade 3:</strong> <span id="summary-grade3">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade3 ?? 5050) + ($feeSettings->insurance ?? 150) + ($feeSettings->electric ?? 2000), 2)); ?></span><br>
                    <small class="text-muted" id="summary-grade3-breakdown">(<?php echo e(($feeSettings->tuition ?? 7505) . '+' . ($feeSettings->misc ?? 2800) . '+' . ($feeSettings->books_grade3 ?? 5050) . '+' . ($feeSettings->insurance ?? 150) . '+' . ($feeSettings->electric ?? 2000)); ?>)</small>
                </div>
                <div class="col-md-3">
                    <strong>Grade 4-6:</strong> <span id="summary-grade4">₱<?php echo e(number_format(($feeSettings->tuition ?? 7505) + ($feeSettings->misc ?? 2800) + ($feeSettings->books_grade4 ?? 5550) + ($feeSettings->insurance ?? 150) + ($feeSettings->electric ?? 2000), 2)); ?></span><br>
                    <small class="text-muted" id="summary-grade4-breakdown">(<?php echo e(($feeSettings->tuition ?? 7505) . '+' . ($feeSettings->misc ?? 2800) . '+' . ($feeSettings->books_grade4 ?? 5550) . '+' . ($feeSettings->insurance ?? 150) . '+' . ($feeSettings->electric ?? 2000)); ?>)</small>
                </div>
            </div>
        </div>

        
        <div class="d-flex justify-content-end">
            <button id="btn-save-fees" class="btn-dash btn-primary" onclick="confirmSaveFeeSettings()">
                <span id="btn-save-fees-text"><i class="bi bi-floppy-fill me-1"></i> Save Fee Settings</span>
                <span id="btn-save-fees-loading" style="display:none;"><i class="bi bi-arrow-repeat me-1" style="animation:spin 1s linear infinite;"></i> Saving...</span>
            </button>
        </div>

        <script>
        function recalculateMonthlyFees() {
            // Option B
            const optbTuition = parseFloat(document.getElementById('fee-optb-monthly-tuition').value) || 0;
            const optbElectric = parseFloat(document.getElementById('fee-optb-monthly-electric').value) || 0;
            document.getElementById('fee-optb-monthly').value = (optbTuition + optbElectric).toFixed(2);

            // Option C
            const optcTuition = parseFloat(document.getElementById('fee-optc-monthly-tuition').value) || 0;
            const optcMisc = parseFloat(document.getElementById('fee-optc-monthly-misc').value) || 0;
            const optcElectric = parseFloat(document.getElementById('fee-optc-monthly-electric').value) || 0;
            document.getElementById('fee-optc-monthly').value = (optcTuition + optcMisc + optcElectric).toFixed(2);

            // Option D
            const optdTuition = parseFloat(document.getElementById('fee-optd-monthly-tuition').value) || 0;
            const optdMisc = parseFloat(document.getElementById('fee-optd-monthly-misc').value) || 0;
            const optdElectric = parseFloat(document.getElementById('fee-optd-monthly-electric').value) || 0;
            document.getElementById('fee-optd-monthly').value = (optdTuition + optdMisc + optdElectric).toFixed(2);
        }

        function recalculateFromBaseFees() {
            const tuition = parseFloat(document.getElementById('fee-tuition').value) || 0;
            const misc = parseFloat(document.getElementById('fee-misc').value) || 0;
            const insurance = parseFloat(document.getElementById('fee-insurance').value) || 0;
            const electric = parseFloat(document.getElementById('fee-electric').value) || 0;
            const booksNursery = parseFloat(document.getElementById('fee-books-nursery').value) || 0;
            const booksGrade1 = parseFloat(document.getElementById('fee-books-grade1').value) || 0;
            const booksGrade3 = parseFloat(document.getElementById('fee-books-grade3').value) || 0;
            const booksGrade4 = parseFloat(document.getElementById('fee-books-grade4').value) || 0;

            const nurseryTotal = tuition + misc + booksNursery + insurance + electric;
            const grade1Total = tuition + misc + booksGrade1 + insurance + electric;
            const grade3Total = tuition + misc + booksGrade3 + insurance + electric;
            const grade4Total = tuition + misc + booksGrade4 + insurance + electric;

            document.getElementById('summary-nursery').textContent = '₱' + nurseryTotal.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('summary-nursery-breakdown').textContent = '(' + [tuition, misc, booksNursery, insurance, electric].join('+') + ')';

            document.getElementById('summary-grade1').textContent = '₱' + grade1Total.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('summary-grade1-breakdown').textContent = '(' + [tuition, misc, booksGrade1, insurance, electric].join('+') + ')';

            document.getElementById('summary-grade3').textContent = '₱' + grade3Total.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('summary-grade3-breakdown').textContent = '(' + [tuition, misc, booksGrade3, insurance, electric].join('+') + ')';

            document.getElementById('summary-grade4').textContent = '₱' + grade4Total.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('summary-grade4-breakdown').textContent = '(' + [tuition, misc, booksGrade4, insurance, electric].join('+') + ')';
        }

        function confirmSaveFeeSettings() {
            showConfirm(
                'Save Fee Settings',
                'You are about to change fee settings. This will affect all future payment calculations. Are you sure you want to proceed?',
                saveFeeSettings,
                {
                    btnText: 'Save Changes',
                    btnClass: 'btn btn-warning',
                    btnIcon: 'bi-exclamation-triangle-fill',
                    headerBg: 'linear-gradient(135deg,#e6a700,#b38600)',
                    headerIcon: 'bi-exclamation-triangle-fill'
                }
            );
        }

        async function saveFeeSettings() {
            const btnText = document.getElementById('btn-save-fees-text');
            const btnLoading = document.getElementById('btn-save-fees-loading');
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            showLoading('Saving fee settings...');

            const settings = [];
            const pushVal = (key, id) => {
                const el = document.getElementById(id);
                if (el) settings.push({key: key, value: el.value});
            };

            pushVal('fee_tuition', 'fee-tuition');
            pushVal('fee_misc', 'fee-misc');
            pushVal('fee_insurance', 'fee-insurance');
            pushVal('fee_electric', 'fee-electric');
            pushVal('fee_books_nursery', 'fee-books-nursery');
            pushVal('fee_books_grade1', 'fee-books-grade1');
            pushVal('fee_books_grade3', 'fee-books-grade3');
            pushVal('fee_books_grade4', 'fee-books-grade4');
            pushVal('payment_option_a_discount', 'fee-opta-discount');
            pushVal('fee_optb_monthly_tuition', 'fee-optb-monthly-tuition');
            pushVal('fee_optb_monthly_electric', 'fee-optb-monthly-electric');
            pushVal('dp_b_nursery', 'fee-optb-dp-nursery');
            pushVal('dp_b_kinder', 'fee-optb-dp-kinder');
            pushVal('dp_b_grade1', 'fee-optb-dp-grade1');
            pushVal('dp_b_grade3', 'fee-optb-dp-grade3');
            pushVal('dp_b_grade4', 'fee-optb-dp-grade4');
            pushVal('fee_optc_monthly_tuition', 'fee-optc-monthly-tuition');
            pushVal('fee_optc_monthly_misc', 'fee-optc-monthly-misc');
            pushVal('fee_optc_monthly_electric', 'fee-optc-monthly-electric');
            pushVal('dp_c_grade1', 'fee-optc-dp-grade1');
            pushVal('dp_c_grade3', 'fee-optc-dp-grade3');
            pushVal('dp_c_grade4', 'fee-optc-dp-grade4');
            pushVal('fee_optd_monthly_tuition', 'fee-optd-monthly-tuition');
            pushVal('fee_optd_monthly_misc', 'fee-optd-monthly-misc');
            pushVal('fee_optd_monthly_electric', 'fee-optd-monthly-electric');
            pushVal('dp_d_nursery', 'fee-optd-dp-nursery');
            pushVal('dp_d_kinder', 'fee-optd-dp-kinder');

            try {
                const response = await fetch('/admin/fee-settings', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ settings: settings })
                });
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Non-JSON response:', text.substring(0, 500));
                    alert('Server returned an unexpected response. Check console for details.');
                    return;
                }
                hideLoading();
                if (data.success) {
                    showCustomAlert('success', 'Saved!', 'Fee settings saved successfully.');
                } else {
                    showCustomAlert('error', 'Error', 'Failed to save fee settings: ' + (data.message || 'Unknown error'));
                }
            } catch (err) {
                hideLoading();
                showCustomAlert('error', 'Error', 'Error saving fee settings: ' + err.message);
            } finally {
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        }

        // Initialize calculations on page load with saved DB values
        recalculateFromBaseFees();
        recalculateMonthlyFees();
        </script>

        <!-- Fee Breakdown Preview -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-table" style="color: var(--gold);"></i>
                    Fee Breakdown Preview by Grade
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Grade Level</th>
                                <th>Tuition</th>
                                <th>Misc</th>
                                <th>Insurance</th>
                                <th>Electric</th>
                                <th>Books</th>
                                <th style="font-weight: 700;">Total Base</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $feeBreakdowns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade => $breakdown): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="text-transform: capitalize;"><?php echo e(str_replace(['grade', 'nursery', 'kindergarten'], ['Grade ', 'Nursery', 'Kindergarten'], $grade)); ?></td>
                                    <td>₱<?php echo e(number_format($breakdown['tuition'], 2)); ?></td>
                                    <td>₱<?php echo e(number_format($breakdown['misc'], 2)); ?></td>
                                    <td>₱<?php echo e(number_format($breakdown['insurance'], 2)); ?></td>
                                    <td>₱<?php echo e(number_format($breakdown['electric'], 2)); ?></td>
                                    <td>₱<?php echo e(number_format($breakdown['books'], 2)); ?></td>
                                    <td style="font-weight: 700; color: var(--blue);">₱<?php echo e(number_format($breakdown['base_total'], 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/fees.blade.php ENDPATH**/ ?>