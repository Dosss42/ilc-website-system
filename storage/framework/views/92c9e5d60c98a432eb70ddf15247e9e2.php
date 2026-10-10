        
        <div class="section-header">
            <div>
                <h1><i class="bi bi-bar-chart-line-fill" style="color:var(--blue);margin-right:8px;"></i>Reports</h1>
                <p>S.Y. <?php echo e($currentSchoolYear); ?> &mdash; Comprehensive reports for students, enrollment, financials, and KPIs.</p>
            </div>
        </div>

        
        <script type="application/json" id="rpt-chart-data"><?php echo json_encode([
            'gradeLabels' => $rptChartGradeLabels ?? [],
            'gradeData' => $rptChartGradeData ?? [],
            'enrollApproved' => $rptEnrollApproved ?? 0,
            'enrollPending' => $rptEnrollPending ?? 0,
            'enrollDeclined' => $rptEnrollDeclined ?? 0,
            'enrollDropped' => $rptEnrollDropped ?? 0,
            'dailyLabels' => collect($rptDailyDays ?? [])->pluck('label'),
            'dailyData' => collect($rptDailyDays ?? [])->pluck('count'),
        ]); ?></script>
        <div class="row g-3 mb-4">
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header"><h6><i class="bi bi-bar-chart-fill me-2" style="color:var(--blue);"></i>Students by Grade Level</h6></div>
                    <div class="p-3" style="height:200px;"><canvas id="rptGradeBar"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header"><h6><i class="bi bi-pie-chart-fill me-2" style="color:var(--blue);"></i>Enrollment Status</h6></div>
                    <div class="p-3" style="height:200px;display:flex;justify-content:center;"><canvas id="rptEnrollStatusDoughnut"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header"><h6><i class="bi bi-activity me-2" style="color:var(--blue);"></i>7-Day Enrollment Activity</h6></div>
                    <div class="p-3" style="height:200px;"><canvas id="rptDailyLine"></canvas></div>
                </div>
            </div>
        </div>

        
        <div style="display:flex;gap:4px;background:#fff;border:1px solid var(--border);border-radius:10px;padding:5px;margin-bottom:24px;flex-wrap:wrap;width:fit-content;">
            <button class="rpt-tab-btn active" id="rpt-tab-students" onclick="switchRptTab('students')">
                <i class="bi bi-people-fill"></i> Student Reports
            </button>
            <button class="rpt-tab-btn" id="rpt-tab-enrollment" onclick="switchRptTab('enrollment')">
                <i class="bi bi-clipboard-check-fill"></i> Enrollment Reports
            </button>
            <button class="rpt-tab-btn" id="rpt-tab-financial" onclick="switchRptTab('financial')">
                <i class="bi bi-cash-stack"></i> Financial Reports
            </button>
            <button class="rpt-tab-btn" id="rpt-tab-promotion" onclick="switchRptTab('promotion')">
                <i class="bi bi-mortarboard-fill"></i> Promotion Reports
            </button>
            <?php if(Auth::user()->role === 'superadmin'): ?>
            <button class="rpt-tab-btn" id="rpt-tab-kpi" onclick="switchRptTab('kpi')">
                <i class="bi bi-speedometer2"></i> KPI Dashboard
            </button>
            <?php endif; ?>
        </div>

        
        <div id="rpt-panel-students" class="rpt-panel">

            
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptTotalStudents); ?></div><div class="stat-label">Total Students</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptActiveStudents); ?></div><div class="stat-label">Active Students</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="bi bi-person-plus-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollNew); ?></div><div class="stat-label">New This Year</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#7c3aed;"><i class="bi bi-arrow-repeat"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollReturning); ?></div><div class="stat-label">Returning</div></div>
                    </div>
                </div>
            </div>

            
            <div class="rpt-subnav">
                <button class="rpt-sub-btn active" data-subreport="master" onclick="switchRptSubReport('students','master')">
                    <i class="bi bi-list-ul"></i> Master List
                </button>
                <button class="rpt-sub-btn" data-subreport="grade" onclick="switchRptSubReport('students','grade')">
                    <i class="bi bi-diagram-3"></i> By Grade Level
                </button>
                <button class="rpt-sub-btn" data-subreport="newret" onclick="switchRptSubReport('students','newret')">
                    <i class="bi bi-people"></i> New vs Returning
                </button>
                <button class="rpt-sub-btn" data-subreport="docs" onclick="switchRptSubReport('students','docs')">
                    <i class="bi bi-file-earmark-check"></i> Document Compliance
                </button>
            </div>

            
            <div id="rpt-sub-students-master" class="rpt-sub-panel">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-table" style="color:var(--blue);margin-right:6px;"></i>Student Master List &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.master-list-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">Gender</th>
                                    <th style="text-align:center;">Type</th>
                                    <th style="text-align:center;">Status</th>
                                    <th style="text-align:center;">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rptMasterListPage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $rptMasterN = $rptMasterListPage->firstItem() + $loop->index;
                                        $sd = $e->student_data ?? [];
                                        $fullName = trim(($sd['first_name'] ?? '') . ' ' . ($sd['last_name'] ?? '')) ?: ($e->full_name ?? '—');
                                        $gl = $sd['grade_level'] ?? ($e->grade_level ?? '');
                                        $glLabel = $rptGradeLevels[$gl] ?? $gl;
                                        $stype = ucfirst($sd['student_type'] ?? '—');
                                    ?>
                                    <tr>
                                        <td style="color:var(--muted);font-size:12px;"><?php echo e($rptMasterN); ?></td>
                                        <td style="font-weight:600;"><?php echo e($fullName); ?></td>
                                        <td><?php echo e($glLabel); ?></td>
                                        <td style="text-align:center;">
                                            <?php if(strtolower($sd['gender'] ?? '') === 'male'): ?>
                                                <span style="color:#2563eb;font-size:12px;font-weight:600;"><i class="bi bi-gender-male"></i> Male</span>
                                            <?php elseif(strtolower($sd['gender'] ?? '') === 'female'): ?>
                                                <span style="color:#db2777;font-size:12px;font-weight:600;"><i class="bi bi-gender-female"></i> Female</span>
                                            <?php else: ?>
                                                <span style="color:var(--muted);font-size:12px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align:center;font-size:12px;"><?php echo e($stype); ?></td>
                                        <td style="text-align:center;">
                                            <span class="status-badge status-<?php echo e($e->status ?? 'pending'); ?>"><?php echo e(ucfirst($e->status ?? 'pending')); ?></span>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php if(($e->payment_status ?? '') === 'paid'): ?>
                                                <span class="status-badge" style="background:#d1fae5;color:#065f46;">Paid</span>
                                            <?php elseif(($e->payment_status ?? '') === 'partial'): ?>
                                                <span class="status-badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                                            <?php else: ?>
                                                <span class="status-badge" style="background:#f1f5f9;color:var(--muted);">Unpaid</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:28px;">No enrolled students found for S.Y. <?php echo e($currentSchoolYear); ?>.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top" style="border-color:var(--border);">
                        <?php echo e($rptMasterListPage->appends(['section' => 'reports', 'rpt_tab' => 'students', 'rpt_subreport' => 'master'])->links()); ?>

                        <div class="pagination-info">
                            Showing <?php echo e($rptMasterListPage->firstItem() ?? 0); ?> to <?php echo e($rptMasterListPage->lastItem() ?? 0); ?> of <?php echo e($rptMasterListPage->total()); ?> enrolled student(s)
                        </div>
                    </div>
                    <?php $rptGenderTotal = $rptMale + $rptFemale; ?>
                    <?php if($rptGenderTotal > 0): ?>
                    <div style="display:flex;gap:24px;padding:14px 16px;border-top:1px solid var(--border);font-size:13px;">
                        <span><i class="bi bi-gender-male" style="color:#2563eb;"></i> Male: <strong><?php echo e($rptMale); ?></strong></span>
                        <span><i class="bi bi-gender-female" style="color:#db2777;"></i> Female: <strong><?php echo e($rptFemale); ?></strong></span>
                        <span style="color:var(--muted);">Total Enrolled: <strong><?php echo e($rptGenderTotal); ?></strong></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div id="rpt-sub-students-grade" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-diagram-3" style="color:var(--blue);margin-right:6px;"></i>Students by Grade Level &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.students-by-grade-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">Enrolled</th>
                                    <th style="text-align:center;">Sections</th>
                                    <th style="text-align:center;">Capacity</th>
                                    <th style="text-align:center;">Utilization</th>
                                    <th style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptSGrandEnrolled = 0; $rptSGrandCap = 0; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $cnt    = $rptStudentsByGrade[$key] ?? 0;
                                        $rptSGrandEnrolled += $cnt;
                                        $secCnt = ($rptAllSections ?? collect())->where('grade_level', $key)->count();
                                        $cap    = ($rptAllSections ?? collect())->where('grade_level', $key)->sum('max_students') ?: ($secCnt * 40);
                                        $rptSGrandCap += $cap;
                                        $util   = $cap > 0 ? min(100, round($cnt / $cap * 100)) : 0;
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:center;font-size:16px;font-weight:700;color:var(--blue);"><?php echo e($cnt); ?></td>
                                        <td style="text-align:center;"><?php echo e($secCnt ?: '—'); ?></td>
                                        <td style="text-align:center;color:var(--muted);"><?php echo e($cap ?: '—'); ?></td>
                                        <td style="text-align:center;">
                                            <?php if($cap > 0): ?>
                                            <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                                <div style="width:70px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                                    <div style="width:<?php echo e($util); ?>%;height:100%;background:<?php echo e($util >= 95 ? 'var(--red)' : ($util >= 80 ? 'var(--green)' : 'var(--gold)')); ?>;border-radius:3px;"></div>
                                                </div>
                                                <span style="font-size:11px;color:var(--muted);"><?php echo e($util); ?>%</span>
                                            </div>
                                            <?php else: ?>
                                                <span style="color:var(--muted);font-size:12px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php if($cnt > 0): ?>
                                                <span class="status-badge status-enrolled">Active</span>
                                            <?php else: ?>
                                                <span style="color:var(--muted);font-size:12px;">No data</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e($rptSGrandEnrolled); ?></td>
                                    <td style="text-align:center;"><?php echo e(($rptAllSections ?? collect())->count()); ?></td>
                                    <td style="text-align:center;"><?php echo e($rptSGrandCap ?: '—'); ?></td>
                                    <td></td><td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-students-newret" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-people" style="color:var(--blue);margin-right:6px;"></i>New vs Returning vs Transferee &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.students-new-vs-returning-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <?php $rptNRTotal = max(1, $rptEnrollNew + $rptEnrollReturning + $rptEnrollTransferee); ?>
                    <div style="display:flex;gap:16px;padding:16px;flex-wrap:wrap;">
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon blue"><i class="bi bi-person-plus-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptEnrollNew); ?></div>
                                <div class="stat-label">New Students</div>
                                <div class="stat-change" style="color:var(--blue);"><?php echo e(round($rptEnrollNew / $rptNRTotal * 100)); ?>% of enrollees</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon green"><i class="bi bi-arrow-repeat"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptEnrollReturning); ?></div>
                                <div class="stat-label">Returning</div>
                                <div class="stat-change" style="color:var(--green);"><?php echo e(round($rptEnrollReturning / $rptNRTotal * 100)); ?>% of enrollees</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon gold"><i class="bi bi-signpost-split-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptEnrollTransferee); ?></div>
                                <div class="stat-label">Transferee</div>
                                <div class="stat-change" style="color:var(--gold);"><?php echo e(round($rptEnrollTransferee / $rptNRTotal * 100)); ?>% of enrollees</div>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">New</th>
                                    <th style="text-align:center;">Returning</th>
                                    <th style="text-align:center;">Transferee</th>
                                    <th style="text-align:center;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptNRGNew=0;$rptNRGRet=0;$rptNRGTrans=0;$rptNRGTot=0; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $row = $rptEnrollByGrade[$key] ?? ['new'=>0,'returning'=>0,'transferee'=>0,'total'=>0];
                                        $rptNRGNew   += $row['new'] ?? 0;
                                        $rptNRGRet   += $row['returning'] ?? 0;
                                        $rptNRGTrans += $row['transferee'] ?? 0;
                                        $rptNRGTot   += $row['total'] ?? 0;
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:center;color:#2563eb;font-weight:600;"><?php echo e($row['new'] ?? 0); ?></td>
                                        <td style="text-align:center;color:var(--green);font-weight:600;"><?php echo e($row['returning'] ?? 0); ?></td>
                                        <td style="text-align:center;color:var(--gold);font-weight:600;"><?php echo e($row['transferee'] ?? 0); ?></td>
                                        <td style="text-align:center;font-weight:700;"><?php echo e($row['total'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;color:#2563eb;"><?php echo e($rptNRGNew); ?></td>
                                    <td style="text-align:center;color:var(--green);"><?php echo e($rptNRGRet); ?></td>
                                    <td style="text-align:center;color:var(--gold);"><?php echo e($rptNRGTrans); ?></td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e($rptNRGTot); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-students-docs" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-file-earmark-check" style="color:var(--blue);margin-right:6px;"></i>Document Compliance &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.document-compliance-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <?php $rptDocTotal = max(1, $rptTotalDocs); ?>
                    <div style="display:flex;gap:16px;padding:16px;flex-wrap:wrap;">
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptApprovedDocs); ?></div>
                                <div class="stat-label">Approved</div>
                                <div class="stat-change" style="color:var(--green);"><?php echo e(round($rptApprovedDocs / $rptDocTotal * 100)); ?>%</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptPendingDocs); ?></div>
                                <div class="stat-label">Pending Review</div>
                                <div class="stat-change" style="color:var(--gold);"><?php echo e(round($rptPendingDocs / $rptDocTotal * 100)); ?>%</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptRejectedDocs); ?></div>
                                <div class="stat-label">Rejected</div>
                                <div class="stat-change" style="color:var(--red);"><?php echo e(round($rptRejectedDocs / $rptDocTotal * 100)); ?>%</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon blue"><i class="bi bi-file-earmark-text-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptTotalDocs); ?></div>
                                <div class="stat-label">Total Documents</div>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Document Type</th>
                                    <th style="text-align:center;">Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = ($studentDocuments ?? collect())->take(30); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($doc->user->name ?? '—'); ?></td>
                                        <td style="font-size:12px;"><?php echo e(ucwords(str_replace('_',' ', $doc->document_type ?? ''))); ?></td>
                                        <td style="text-align:center;">
                                            <?php if(($doc->status ?? '') === 'approved'): ?>
                                                <span class="status-badge" style="background:#d1fae5;color:#065f46;">Approved</span>
                                            <?php elseif(($doc->status ?? '') === 'pending'): ?>
                                                <span class="status-badge" style="background:#fef3c7;color:#92400e;">Pending</span>
                                            <?php elseif(($doc->status ?? '') === 'rejected'): ?>
                                                <span class="status-badge" style="background:#fee2e2;color:#991b1b;">Rejected</span>
                                            <?php else: ?>
                                                <span class="status-badge" style="background:#f1f5f9;color:var(--muted);"><?php echo e(ucfirst($doc->status ?? '—')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-size:12px;color:var(--muted);"><?php echo e($doc->created_at ? $doc->created_at->format('M j, Y') : '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">No document records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        
        <div id="rpt-panel-enrollment" class="rpt-panel" style="display:none;">

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-clipboard-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollTotal); ?></div><div class="stat-label">Total Enrollments</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollApproved); ?></div><div class="stat-label">Approved / Enrolled</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollPending); ?></div><div class="stat-label">Pending</div></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="bi bi-person-dash-fill"></i></div>
                        <div><div class="stat-value"><?php echo e($rptEnrollDropped); ?></div><div class="stat-label">Dropped</div></div>
                    </div>
                </div>
            </div>

            <div class="rpt-subnav">
                <button class="rpt-sub-btn active" data-subreport="status" onclick="switchRptSubReport('enrollment','status')">
                    <i class="bi bi-bar-chart"></i> Status Summary
                </button>
                <button class="rpt-sub-btn" data-subreport="grade" onclick="switchRptSubReport('enrollment','grade')">
                    <i class="bi bi-diagram-3"></i> By Grade Level
                </button>
                <button class="rpt-sub-btn" data-subreport="newret" onclick="switchRptSubReport('enrollment','newret')">
                    <i class="bi bi-people"></i> New vs Returning
                </button>
                <button class="rpt-sub-btn" data-subreport="trend" onclick="switchRptSubReport('enrollment','trend')">
                    <i class="bi bi-graph-up"></i> Daily Trend
                </button>
            </div>

            
            <div id="rpt-sub-enrollment-status" class="rpt-sub-panel">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-bar-chart" style="color:var(--blue);margin-right:6px;"></i>Enrollment Status Summary &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.enrollment-status-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <?php
                            $rptStatusRows = [
                                ['label'=>'Enrolled',  'key'=>'enrolled',  'color'=>'var(--blue)',  'icon'=>'bi-person-badge-fill'],
                                ['label'=>'Approved',  'key'=>'approved',  'color'=>'var(--green)', 'icon'=>'bi-check-circle-fill'],
                                ['label'=>'Completed', 'key'=>'completed', 'color'=>'#7c3aed',      'icon'=>'bi-patch-check-fill'],
                                ['label'=>'Pending',   'key'=>'pending',   'color'=>'var(--gold)',  'icon'=>'bi-hourglass-split'],
                                ['label'=>'Declined',  'key'=>'declined',  'color'=>'var(--red)',   'icon'=>'bi-x-circle-fill'],
                                ['label'=>'Dropped',   'key'=>'dropped',   'color'=>'#f97316',      'icon'=>'bi-person-dash-fill'],
                            ];
                            $rptEnrollTotalSafe = max(1, $rptEnrollTotal);
                        ?>
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th style="text-align:center;">Count</th>
                                    <th style="text-align:center;">Share</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rptStatusRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $srCount = $rptEnrollAll->where('status', $sr['key'])->count();
                                        $srPct   = round($srCount / $rptEnrollTotalSafe * 100);
                                    ?>
                                    <tr>
                                        <td>
                                            <span style="display:inline-flex;align-items:center;gap:7px;">
                                                <i class="bi <?php echo e($sr['icon']); ?>" style="color:<?php echo e($sr['color']); ?>;"></i>
                                                <span style="font-weight:600;"><?php echo e($sr['label']); ?></span>
                                            </span>
                                        </td>
                                        <td style="text-align:center;font-weight:700;font-size:16px;color:<?php echo e($sr['color']); ?>;"><?php echo e($srCount); ?></td>
                                        <td style="text-align:center;font-size:12px;color:var(--muted);"><?php echo e($srPct); ?>%</td>
                                        <td>
                                            <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;max-width:200px;">
                                                <div style="width:<?php echo e($srPct); ?>%;height:100%;background:<?php echo e($sr['color']); ?>;border-radius:4px;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;font-size:16px;color:var(--blue);"><?php echo e($rptEnrollTotal); ?></td>
                                    <td style="text-align:center;">100%</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-enrollment-grade" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-diagram-3" style="color:var(--blue);margin-right:6px;"></i>Enrollment by Grade Level &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.enrollment-by-grade-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">Total</th>
                                    <th style="text-align:center;">Approved</th>
                                    <th style="text-align:center;">Pending</th>
                                    <th style="text-align:center;">Dropped</th>
                                    <th style="text-align:center;">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptEGGT=0;$rptEGGA=0;$rptEGGP=0;$rptEGGD=0; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $row = $rptEnrollByGrade[$key] ?? ['total'=>0,'approved'=>0,'pending'=>0,'dropped'=>0];
                                        $rptEGGT += $row['total'];
                                        $rptEGGA += $row['approved'];
                                        $rptEGGP += $row['pending'];
                                        $rptEGGD += $row['dropped'] ?? 0;
                                        $pct = $row['total'] > 0 ? round($row['approved'] / $row['total'] * 100) : 0;
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:center;font-weight:700;"><?php echo e($row['total']); ?></td>
                                        <td style="text-align:center;color:var(--green);font-weight:600;"><?php echo e($row['approved']); ?></td>
                                        <td style="text-align:center;color:var(--gold);font-weight:600;"><?php echo e($row['pending']); ?></td>
                                        <td style="text-align:center;color:var(--red);font-weight:600;"><?php echo e($row['dropped'] ?? 0); ?></td>
                                        <td style="text-align:center;">
                                            <?php if($row['total'] > 0): ?>
                                            <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                                <div style="width:70px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                                    <div style="width:<?php echo e($pct); ?>%;height:100%;background:var(--green);border-radius:3px;"></div>
                                                </div>
                                                <span style="font-size:11px;color:var(--muted);"><?php echo e($pct); ?>%</span>
                                            </div>
                                            <?php else: ?>
                                                <span style="color:var(--muted);font-size:12px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e($rptEGGT); ?></td>
                                    <td style="text-align:center;color:var(--green);"><?php echo e($rptEGGA); ?></td>
                                    <td style="text-align:center;color:var(--gold);"><?php echo e($rptEGGP); ?></td>
                                    <td style="text-align:center;color:var(--red);"><?php echo e($rptEGGD); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-enrollment-newret" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-people" style="color:var(--blue);margin-right:6px;"></i>New vs Returning vs Transferee &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.enrollment-new-vs-returning-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">New</th>
                                    <th style="text-align:center;">Returning</th>
                                    <th style="text-align:center;">Transferee</th>
                                    <th style="text-align:center;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptENRN=0;$rptENRR=0;$rptENRT=0;$rptENRTot=0; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $row=$rptEnrollByGrade[$key]??['new'=>0,'returning'=>0,'transferee'=>0,'total'=>0];
                                        $rptENRN   += $row['new']??0;
                                        $rptENRR   += $row['returning']??0;
                                        $rptENRT   += $row['transferee']??0;
                                        $rptENRTot += $row['total']??0;
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:center;color:#2563eb;font-weight:600;"><?php echo e($row['new']??0); ?></td>
                                        <td style="text-align:center;color:var(--green);font-weight:600;"><?php echo e($row['returning']??0); ?></td>
                                        <td style="text-align:center;color:var(--gold);font-weight:600;"><?php echo e($row['transferee']??0); ?></td>
                                        <td style="text-align:center;font-weight:700;"><?php echo e($row['total']??0); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;color:#2563eb;"><?php echo e($rptENRN); ?></td>
                                    <td style="text-align:center;color:var(--green);"><?php echo e($rptENRR); ?></td>
                                    <td style="text-align:center;color:var(--gold);"><?php echo e($rptENRT); ?></td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e($rptENRTot); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-enrollment-trend" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-graph-up" style="color:var(--blue);margin-right:6px;"></i>Daily Enrollment &mdash; Last 7 Days</h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.enrollment-daily-trend-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <?php $rptDailyMax = max(1, collect($rptDailyDays)->max('count')); ?>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th style="text-align:center;">Applications</th>
                                    <th>Chart</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rptDailyDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $barW = round($day['count'] / $rptDailyMax * 100); ?>
                                    <tr>
                                        <td style="font-weight:600;white-space:nowrap;"><?php echo e($day['label']); ?></td>
                                        <td style="text-align:center;font-weight:700;color:var(--blue);font-size:16px;"><?php echo e($day['count']); ?></td>
                                        <td>
                                            <div style="height:10px;background:#e2e8f0;border-radius:5px;overflow:hidden;max-width:240px;">
                                                <div style="width:<?php echo e($barW); ?>%;height:100%;background:var(--blue);border-radius:5px;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>7-Day Total</td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e(collect($rptDailyDays)->sum('count')); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        
        <div id="rpt-panel-financial" class="rpt-panel" style="display:none;">

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:18px;">&#8369;<?php echo e(number_format($rptTotalFees, 0)); ?></div>
                            <div class="stat-label">Total Assessed</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:18px;">&#8369;<?php echo e(number_format($rptTotalCollected, 0)); ?></div>
                            <div class="stat-label">Total Collected</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="bi bi-exclamation-circle-fill"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:18px;">&#8369;<?php echo e(number_format($rptOutstanding, 0)); ?></div>
                            <div class="stat-label">Outstanding</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="bi bi-percent"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:22px;"><?php echo e($rptCollectionRate); ?>%</div>
                            <div class="stat-label">Collection Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rpt-subnav">
                <button class="rpt-sub-btn active" data-subreport="collection" onclick="switchRptSubReport('financial','collection')">
                    <i class="bi bi-cash"></i> Collection Summary
                </button>
                <button class="rpt-sub-btn" data-subreport="grade" onclick="switchRptSubReport('financial','grade')">
                    <i class="bi bi-diagram-3"></i> By Grade Level
                </button>
                <button class="rpt-sub-btn" data-subreport="option" onclick="switchRptSubReport('financial','option')">
                    <i class="bi bi-list-check"></i> By Payment Option
                </button>
                <button class="rpt-sub-btn" data-subreport="outstanding" onclick="switchRptSubReport('financial','outstanding')">
                    <i class="bi bi-exclamation-triangle"></i> Outstanding Balances
                </button>
            </div>

            
            <div id="rpt-sub-financial-collection" class="rpt-sub-panel">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-cash" style="color:var(--blue);margin-right:6px;"></i>Collection Summary &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.admin-collection-summary-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <?php
                        $rptFinStatusRows = [
                            ['label'=>'Fully Paid', 'color'=>'var(--green)', 'amount'=>$rptFinAll->where('payment_status','paid')->sum('payment_amount'),   'count'=>$rptPaid],
                            ['label'=>'Partial',    'color'=>'var(--gold)',  'amount'=>$rptFinAll->where('payment_status','partial')->sum('payment_amount'), 'count'=>$rptPartial],
                            ['label'=>'Unpaid',     'color'=>'var(--muted)', 'amount'=>0,                                                                   'count'=>$rptUnpaid],
                        ];
                        $rptFinTotalCount = max(1, $rptPaid + $rptPartial + $rptUnpaid);
                    ?>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Payment Status</th>
                                    <th style="text-align:center;">Students</th>
                                    <th style="text-align:right;">Amount Collected</th>
                                    <th style="text-align:center;">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rptFinStatusRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fsr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $fsrPct = round($fsr['count'] / $rptFinTotalCount * 100); ?>
                                    <tr>
                                        <td style="font-weight:600;color:<?php echo e($fsr['color']); ?>;"><?php echo e($fsr['label']); ?></td>
                                        <td style="text-align:center;font-weight:700;font-size:16px;color:<?php echo e($fsr['color']); ?>;"><?php echo e($fsr['count']); ?></td>
                                        <td style="text-align:right;font-weight:600;"><?php echo $fsr['amount'] > 0 ? '&#8369;'.number_format($fsr['amount'],0) : '&mdash;'; ?></td>
                                        <td style="text-align:center;font-size:12px;color:var(--muted);"><?php echo e($fsrPct); ?>%</td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL ASSESSED</td>
                                    <td style="text-align:center;color:var(--blue);font-size:15px;"><?php echo e($rptPaid + $rptPartial + $rptUnpaid); ?></td>
                                    <td style="text-align:right;color:var(--blue);">&#8369;<?php echo e(number_format($rptTotalFees, 0)); ?></td>
                                    <td></td>
                                </tr>
                                <tr style="background:#f0f9ff;font-weight:700;">
                                    <td style="color:var(--green);">TOTAL COLLECTED</td>
                                    <td></td>
                                    <td style="text-align:right;color:var(--green);font-size:15px;">&#8369;<?php echo e(number_format($rptTotalCollected, 0)); ?></td>
                                    <td style="text-align:center;color:var(--green);"><?php echo e($rptCollectionRate); ?>%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-financial-grade" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-diagram-3" style="color:var(--blue);margin-right:6px;"></i>Financial by Grade Level &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.financial-by-grade-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:right;">Assessed</th>
                                    <th style="text-align:right;">Collected</th>
                                    <th style="text-align:right;">Outstanding</th>
                                    <th style="text-align:center;">Paid</th>
                                    <th style="text-align:center;">Collection Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptFGF=0;$rptFGC=0;$rptFGO=0;$rptFGP=0; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $row = $rptFinByGrade[$key] ?? ['total_fee'=>0,'collected'=>0,'outstanding'=>0,'paid'=>0,'partial'=>0,'count'=>0];
                                        $rptFGF += $row['total_fee'];
                                        $rptFGC += $row['collected'];
                                        $rptFGO += $row['outstanding'];
                                        $rptFGP += $row['paid'];
                                        $colRate = $row['total_fee'] > 0 ? round($row['collected'] / $row['total_fee'] * 100) : 0;
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:right;">&#8369;<?php echo e(number_format($row['total_fee'], 0)); ?></td>
                                        <td style="text-align:right;color:var(--green);font-weight:600;">&#8369;<?php echo e(number_format($row['collected'], 0)); ?></td>
                                        <td style="text-align:right;color:var(--red);font-weight:600;">&#8369;<?php echo e(number_format($row['outstanding'], 0)); ?></td>
                                        <td style="text-align:center;"><?php echo e($row['paid']); ?></td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                                <div style="width:70px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                                    <div style="width:<?php echo e($colRate); ?>%;height:100%;background:<?php echo e($colRate >= 90 ? 'var(--green)' : ($colRate >= 60 ? 'var(--gold)' : 'var(--red)')); ?>;border-radius:3px;"></div>
                                                </div>
                                                <span style="font-size:11px;color:var(--muted);"><?php echo e($colRate); ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:right;">&#8369;<?php echo e(number_format($rptFGF,0)); ?></td>
                                    <td style="text-align:right;color:var(--green);">&#8369;<?php echo e(number_format($rptFGC,0)); ?></td>
                                    <td style="text-align:right;color:var(--red);">&#8369;<?php echo e(number_format($rptFGO,0)); ?></td>
                                    <td style="text-align:center;"><?php echo e($rptFGP); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-financial-option" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-list-check" style="color:var(--blue);margin-right:6px;"></i>By Payment Option &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.financial-by-option-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <?php $rptOptionLabels = ['A'=>'Option A — Full Payment','B'=>'Option B — 2 Installments','C'=>'Option C — 3 Installments','D'=>'Option D — Monthly']; ?>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Payment Option</th>
                                    <th style="text-align:center;">Students</th>
                                    <th style="text-align:right;">Assessed</th>
                                    <th style="text-align:right;">Collected</th>
                                    <th style="text-align:right;">Outstanding</th>
                                    <th style="text-align:center;">Fully Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rptFinByOption; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($rptOptionLabels[$opt] ?? 'Option '.$opt); ?></td>
                                        <td style="text-align:center;font-weight:700;color:var(--blue);"><?php echo e($row['count']); ?></td>
                                        <td style="text-align:right;">&#8369;<?php echo e(number_format($row['total_fee'],0)); ?></td>
                                        <td style="text-align:right;color:var(--green);font-weight:600;">&#8369;<?php echo e(number_format($row['collected'],0)); ?></td>
                                        <td style="text-align:right;color:var(--red);font-weight:600;">&#8369;<?php echo e(number_format($row['outstanding'],0)); ?></td>
                                        <td style="text-align:center;"><?php echo e($row['paid']); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No payment option data available.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-financial-outstanding" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-exclamation-triangle" style="color:var(--red);margin-right:6px;"></i>Outstanding Balances &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.outstanding-balances-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Grade Level</th>
                                    <th>Option</th>
                                    <th style="text-align:right;">Total Fee</th>
                                    <th style="text-align:right;">Paid</th>
                                    <th style="text-align:right;">Balance</th>
                                    <th style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rptOutstandingPage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $rptObN = $rptOutstandingPage->firstItem() + $loop->index;
                                        $sd = $e->student_data ?? [];
                                        $obName = trim(($sd['first_name']??'').(' '.($sd['last_name']??''))) ?: '—';
                                        $obGl   = $rptGradeLevels[$sd['grade_level'] ?? $e->grade_level ?? ''] ?? '—';
                                    ?>
                                    <tr>
                                        <td style="color:var(--muted);font-size:12px;"><?php echo e($rptObN); ?></td>
                                        <td style="font-weight:600;"><?php echo e($obName); ?></td>
                                        <td style="font-size:12px;"><?php echo e($obGl); ?></td>
                                        <td style="text-align:center;font-size:12px;"><?php echo e($e->payment_option ? 'Option '.$e->payment_option : '—'); ?></td>
                                        <td style="text-align:right;">&#8369;<?php echo e(number_format($e->total_fee ?? 0, 0)); ?></td>
                                        <td style="text-align:right;color:var(--green);font-weight:600;">&#8369;<?php echo e(number_format($e->payment_amount ?? 0, 0)); ?></td>
                                        <td style="text-align:right;color:var(--red);font-weight:700;">&#8369;<?php echo e(number_format($e->remaining_balance ?? 0, 0)); ?></td>
                                        <td style="text-align:center;">
                                            <span class="status-badge" style="background:#fee2e2;color:#991b1b;">Outstanding</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:28px;"><i class="bi bi-check-circle-fill" style="color:var(--green);margin-right:6px;"></i>No outstanding balances.</td></tr>
                                <?php endif; ?>
                                <?php if($rptOutstandingList->count() > 0): ?>
                                <tr style="background:#fef2f2;font-weight:700;">
                                    <td colspan="6" style="text-align:right;color:var(--red);">Total Outstanding (all accounts):</td>
                                    <td style="text-align:right;color:var(--red);font-size:15px;">&#8369;<?php echo e(number_format($rptOutstandingTotalBal, 0)); ?></td>
                                    <td></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top" style="border-color:var(--border);">
                        <?php echo e($rptOutstandingPage->appends(['section' => 'reports', 'rpt_tab' => 'financial', 'rpt_subreport' => 'outstanding'])->links()); ?>

                        <div class="pagination-info">
                            Showing <?php echo e($rptOutstandingPage->firstItem() ?? 0); ?> to <?php echo e($rptOutstandingPage->lastItem() ?? 0); ?> of <?php echo e($rptOutstandingPage->total()); ?> account(s) with outstanding balances
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
        <div id="rpt-panel-promotion" class="rpt-panel" style="display:none;">

            
            <div class="rpt-subnav">
                <button class="rpt-sub-btn active" data-subreport="overview" onclick="switchRptSubReport('promotion','overview')">
                    <i class="bi bi-graph-up"></i> Overview
                </button>
                <button class="rpt-sub-btn" data-subreport="grade" onclick="switchRptSubReport('promotion','grade')">
                    <i class="bi bi-diagram-3"></i> By Grade Level
                </button>
                <button class="rpt-sub-btn" data-subreport="list" onclick="switchRptSubReport('promotion','list')">
                    <i class="bi bi-list-ul"></i> Student List
                </button>
            </div>

            
            <div id="rpt-sub-promotion-overview" class="rpt-sub-panel">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-mortarboard-fill" style="color:var(--blue);margin-right:6px;"></i>Assessment &amp; Promotion Overview &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.promotion-overview-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;padding:16px;flex-wrap:wrap;">
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon green"><i class="bi bi-arrow-up-circle-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptPromoPromoted); ?></div>
                                <div class="stat-label">Promoted</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon gold"><i class="bi bi-arrow-repeat"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptPromoRetained); ?></div>
                                <div class="stat-label">Retained</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon blue"><i class="bi bi-star-fill"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptPromoGraduated); ?></div>
                                <div class="stat-label">Graduated</div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex:1;min-width:180px;">
                            <div class="stat-icon red"><i class="bi bi-hourglass-split"></i></div>
                            <div>
                                <div class="stat-value"><?php echo e($rptPromoPending); ?></div>
                                <div class="stat-label">Pending Assessment</div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:0 16px 16px;font-size:12px;color:var(--muted);">
                        Out of <?php echo e($rptPromoEligibleTotal); ?> eligible student(s) (Nursery&ndash;Grade 6, excluding first-year transferees).
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-promotion-grade" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-diagram-3" style="color:var(--blue);margin-right:6px;"></i>Promotion by Grade Level &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.promotion-by-grade-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Grade Level</th>
                                    <th style="text-align:center;">Eligible</th>
                                    <th style="text-align:center;">Promoted</th>
                                    <th style="text-align:center;">Retained</th>
                                    <th style="text-align:center;">Graduated</th>
                                    <th style="text-align:center;">Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rptPromoGTot=['eligible'=>0,'promoted'=>0,'retained'=>0,'graduated'=>0,'pending'=>0]; ?>
                                <?php $__currentLoopData = $rptGradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $row = $rptPromoByGrade[$key] ?? ['eligible'=>0,'promoted'=>0,'retained'=>0,'graduated'=>0,'pending'=>0];
                                        foreach ($rptPromoGTot as $k => $v) { $rptPromoGTot[$k] += $row[$k] ?? 0; }
                                    ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo e($label); ?></td>
                                        <td style="text-align:center;"><?php echo e($row['eligible']); ?></td>
                                        <td style="text-align:center;color:var(--green);font-weight:600;"><?php echo e($row['promoted']); ?></td>
                                        <td style="text-align:center;color:var(--gold);font-weight:600;"><?php echo e($row['retained']); ?></td>
                                        <td style="text-align:center;color:var(--blue);font-weight:600;"><?php echo e($row['graduated']); ?></td>
                                        <td style="text-align:center;color:var(--red);font-weight:600;"><?php echo e($row['pending']); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background:#f8fafc;font-weight:700;">
                                    <td>TOTAL</td>
                                    <td style="text-align:center;"><?php echo e($rptPromoGTot['eligible']); ?></td>
                                    <td style="text-align:center;color:var(--green);"><?php echo e($rptPromoGTot['promoted']); ?></td>
                                    <td style="text-align:center;color:var(--gold);"><?php echo e($rptPromoGTot['retained']); ?></td>
                                    <td style="text-align:center;color:var(--blue);"><?php echo e($rptPromoGTot['graduated']); ?></td>
                                    <td style="text-align:center;color:var(--red);"><?php echo e($rptPromoGTot['pending']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="rpt-sub-promotion-list" class="rpt-sub-panel" style="display:none;">
                <div class="content-card">
                    <div class="content-card-header" style="justify-content:space-between;">
                        <h6><i class="bi bi-list-ul" style="color:var(--blue);margin-right:6px;"></i>Promotion Student List &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('admin.reports.promotion-student-list-pdf')); ?>" class="btn-dash btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Grade Level</th>
                                    <th>Section</th>
                                    <th style="text-align:center;">Result</th>
                                    <th>To Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rptPromoListPage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $rptPlN = $rptPromoListPage->firstItem() + $loop->index;
                                        $rptPlColor = match($s->result) {
                                            'Promoted'  => 'var(--green)',
                                            'Retained'  => 'var(--gold)',
                                            'Graduated' => 'var(--blue)',
                                            default     => 'var(--red)',
                                        };
                                    ?>
                                    <tr>
                                        <td style="color:var(--muted);font-size:12px;"><?php echo e($rptPlN); ?></td>
                                        <td style="font-weight:600;"><?php echo e($s->name); ?></td>
                                        <td><?php echo e($s->grade_level); ?></td>
                                        <td style="font-size:12px;"><?php echo e($s->section); ?></td>
                                        <td style="text-align:center;font-weight:700;color:<?php echo e($rptPlColor); ?>;"><?php echo e($s->result); ?></td>
                                        <td style="font-size:12px;"><?php echo e($s->to_grade ?? '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:28px;">No eligible students found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top" style="border-color:var(--border);">
                        <?php echo e($rptPromoListPage->appends(['section' => 'reports', 'rpt_tab' => 'promotion', 'rpt_subreport' => 'list'])->links()); ?>

                        <div class="pagination-info">
                            Showing <?php echo e($rptPromoListPage->firstItem() ?? 0); ?> to <?php echo e($rptPromoListPage->lastItem() ?? 0); ?> of <?php echo e($rptPromoListPage->total()); ?> student(s)
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
        <?php if(Auth::user()->role === 'superadmin'): ?>
        <div id="rpt-panel-kpi" class="rpt-panel" style="display:none;">

            <?php
                $kpiEnrollTotal  = max(1, $rptEnrollTotal);
                $kpiEnrollRate   = round($rptEnrollApproved / $kpiEnrollTotal * 100, 1);
                $kpiColRate      = $rptCollectionRate;
                $kpiTotalCap     = max(1, $rptTotalCapacity);
                $kpiSecUtil      = round($rptTotalEnrolledSec / $kpiTotalCap * 100, 1);
                $kpiDropout      = round($rptEnrollDropped / $kpiEnrollTotal * 100, 1);
                $kpiDocCompDenom = max(1, $rptTotalDocs);
                $kpiDocComp      = round($rptApprovedDocs / $kpiDocCompDenom * 100, 1);
                $kpiNewGrowth    = round($rptEnrollNew / $kpiEnrollTotal * 100, 1);
                $kpiInstTotalSafe= max(1, $rptInstTotal);
                $kpiOverdueRate  = round($rptInstOverdue / $kpiInstTotalSafe * 100, 1);
                $kpiOnline       = $rptEnrollAll->filter(fn($e) => empty($e->student_data['is_walk_in']))->count();
                $kpiOnlineRate   = round($kpiOnline / $kpiEnrollTotal * 100, 1);

                $kpiCards = [
                    ['label'=>'Enrollment Completion Rate','value'=>$kpiEnrollRate.'%','target'=>'â‰¥ 85%','icon'=>'bi-clipboard-check-fill','color'=>'var(--blue)',
                     'met'=>$kpiEnrollRate>=85,'warn'=>$kpiEnrollRate>=70,'bar'=>min(100,$kpiEnrollRate),
                     'barcolor'=>$kpiEnrollRate>=85?'var(--green)':($kpiEnrollRate>=70?'var(--gold)':'var(--red)')],
                    ['label'=>'Collection Rate','value'=>$kpiColRate.'%','target'=>'â‰¥ 90%','icon'=>'bi-cash-stack','color'=>'var(--green)',
                     'met'=>$kpiColRate>=90,'warn'=>$kpiColRate>=70,'bar'=>min(100,$kpiColRate),
                     'barcolor'=>$kpiColRate>=90?'var(--green)':($kpiColRate>=70?'var(--gold)':'var(--red)')],
                    ['label'=>'Section Utilization','value'=>$kpiSecUtil.'%','target'=>'80“95%','icon'=>'bi-people-fill','color'=>'var(--blue)',
                     'met'=>$kpiSecUtil>=80&&$kpiSecUtil<=95,'warn'=>$kpiSecUtil>=65,'bar'=>min(100,$kpiSecUtil),
                     'barcolor'=>($kpiSecUtil>=80&&$kpiSecUtil<=95)?'var(--green)':($kpiSecUtil<65?'var(--red)':'var(--gold)')],
                    ['label'=>'Dropout Rate','value'=>$kpiDropout.'%','target'=>'â‰¤ 5%','icon'=>'bi-person-dash-fill','color'=>'var(--red)',
                     'met'=>$kpiDropout<=5,'warn'=>$kpiDropout<=10,'bar'=>min(100,$kpiDropout*5),
                     'barcolor'=>$kpiDropout<=5?'var(--green)':($kpiDropout<=10?'var(--gold)':'var(--red)')],
                    ['label'=>'Document Compliance','value'=>$kpiDocComp.'%','target'=>'â‰¥ 95%','icon'=>'bi-file-earmark-check-fill','color'=>'var(--blue)',
                     'met'=>$kpiDocComp>=95,'warn'=>$kpiDocComp>=80,'bar'=>min(100,$kpiDocComp),
                     'barcolor'=>$kpiDocComp>=95?'var(--green)':($kpiDocComp>=80?'var(--gold)':'var(--red)')],
                    ['label'=>'New Student Growth','value'=>$kpiNewGrowth.'%','target'=>'Growing','icon'=>'bi-graph-up-arrow','color'=>'var(--gold)',
                     'met'=>$kpiNewGrowth>=20,'warn'=>$kpiNewGrowth>=10,'bar'=>min(100,$kpiNewGrowth*2),
                     'barcolor'=>$kpiNewGrowth>=20?'var(--green)':($kpiNewGrowth>=10?'var(--gold)':'var(--red)')],
                    ['label'=>'Overdue Installment Rate','value'=>$kpiOverdueRate.'%','target'=>'â‰¤ 10%','icon'=>'bi-calendar-x-fill','color'=>'var(--red)',
                     'met'=>$kpiOverdueRate<=10,'warn'=>$kpiOverdueRate<=20,'bar'=>min(100,$kpiOverdueRate*3),
                     'barcolor'=>$kpiOverdueRate<=10?'var(--green)':($kpiOverdueRate<=20?'var(--gold)':'var(--red)')],
                    ['label'=>'Online Enrollment Adoption','value'=>$kpiOnlineRate.'%','target'=>'Growing','icon'=>'bi-globe2','color'=>'var(--blue)',
                     'met'=>$kpiOnlineRate>=50,'warn'=>$kpiOnlineRate>=30,'bar'=>min(100,$kpiOnlineRate),
                     'barcolor'=>$kpiOnlineRate>=50?'var(--green)':($kpiOnlineRate>=30?'var(--gold)':'var(--red)')],
                ];
            ?>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
                <div>
                    <h5 style="font-weight:700;color:var(--blue);margin:0;"><i class="bi bi-speedometer2" style="margin-right:8px;"></i>KPI Dashboard</h5>
                    <p style="color:var(--muted);font-size:13px;margin:4px 0 0;">Key Performance Indicators &mdash; S.Y. <?php echo e($currentSchoolYear); ?></p>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="<?php echo e(route('admin.reports.kpi-overview-pdf')); ?>" class="btn-dash btn-primary"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                </div>
            </div>

            <div id="rpt-sub-kpi-overview" class="rpt-sub-panel">
                
                <div class="row g-3 mb-4">
                    <?php $__currentLoopData = $kpiCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="kpi-card">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                                <div class="kpi-label"><?php echo e($kpi['label']); ?></div>
                                <i class="bi <?php echo e($kpi['icon']); ?>" style="font-size:18px;color:<?php echo e($kpi['color']); ?>;opacity:0.7;"></i>
                            </div>
                            <div class="kpi-value" style="color:<?php echo e($kpi['barcolor']); ?>;"><?php echo e($kpi['value']); ?></div>
                            <div class="kpi-target">Target: <?php echo e($kpi['target']); ?></div>
                            <div class="kpi-bar-wrap">
                                <div class="kpi-bar" style="width:<?php echo e($kpi['bar']); ?>%;background:<?php echo e($kpi['barcolor']); ?>;"></div>
                            </div>
                            <div style="margin-top:8px;font-size:11px;font-weight:600;">
                                <?php if($kpi['met']): ?>
                                    <span class="kpi-status-dot kpi-dot-met"></span><span class="kpi-status-met">Target Met</span>
                                <?php elseif($kpi['warn']): ?>
                                    <span class="kpi-status-dot kpi-dot-warn"></span><span class="kpi-status-warn">Near Target</span>
                                <?php else: ?>
                                    <span class="kpi-status-dot kpi-dot-low"></span><span class="kpi-status-low">Below Target</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-table" style="color:var(--blue);margin-right:6px;"></i>KPI Summary Table &mdash; S.Y. <?php echo e($currentSchoolYear); ?></h6>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>KPI Metric</th>
                                    <th style="text-align:center;">Current Value</th>
                                    <th style="text-align:center;">Target</th>
                                    <th style="text-align:center;">Progress</th>
                                    <th style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $kpiCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-weight:600;">
                                        <i class="bi <?php echo e($kpi['icon']); ?>" style="color:<?php echo e($kpi['color']); ?>;margin-right:6px;"></i><?php echo e($kpi['label']); ?>

                                    </td>
                                    <td style="text-align:center;font-weight:700;font-size:15px;color:<?php echo e($kpi['barcolor']); ?>;"><?php echo e($kpi['value']); ?></td>
                                    <td style="text-align:center;font-size:12px;color:var(--muted);"><?php echo e($kpi['target']); ?></td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                            <div style="width:80px;height:7px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                                                <div style="width:<?php echo e($kpi['bar']); ?>%;height:100%;background:<?php echo e($kpi['barcolor']); ?>;border-radius:4px;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if($kpi['met']): ?>
                                            <span class="status-badge" style="background:#d1fae5;color:#065f46;"><i class="bi bi-check-circle-fill"></i> Met</span>
                                        <?php elseif($kpi['warn']): ?>
                                            <span class="status-badge" style="background:#fef3c7;color:#92400e;"><i class="bi bi-dash-circle-fill"></i> Near</span>
                                        <?php else: ?>
                                            <span class="status-badge" style="background:#fee2e2;color:#991b1b;"><i class="bi bi-x-circle-fill"></i> Below</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="display:flex;gap:20px;padding:12px 16px;border-top:1px solid var(--border);font-size:12px;flex-wrap:wrap;">
                        <span><span class="kpi-status-dot kpi-dot-met"></span> Target Met</span>
                        <span><span class="kpi-status-dot kpi-dot-warn"></span> Near Target</span>
                        <span><span class="kpi-status-dot kpi-dot-low"></span> Below Target</span>
                        <span style="color:var(--muted);margin-left:auto;">Generated: <?php echo e(now()->format('F j, Y')); ?></span>
                    </div>
                </div>
            </div>

        </div>
        <?php endif; ?>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/reports-content.blade.php ENDPATH**/ ?>