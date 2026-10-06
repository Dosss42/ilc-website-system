        <div class="section-header">

            <div>

                <h1>Student Management</h1>

                <p>View, add, and manage all student records.</p>

            </div>

       

        </div>

        
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-total-count"><?php echo e($smTotal ?? 0); ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-enrolled-count"><?php echo e($smEnrolled ?? 0); ?></div>
                        <div class="stat-label">Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon teal"><i class="bi bi-check2-square"></i></div>
                    <div>
                        <div class="stat-value" id="sm-approved-count"><?php echo e($smApproved ?? 0); ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-value" id="sm-pending-count"><?php echo e($smPending ?? 0); ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-notenrolled-count"><?php echo e($smNotEnrolled ?? 0); ?></div>
                        <div class="stat-label">Not Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="stat-value" id="sm-paid-count"><?php echo e($smPaid ?? 0); ?></div>
                        <div class="stat-label">Fully Paid</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-balance-count"><?php echo e($smBalance ?? 0); ?></div>
                        <div class="stat-label">With Balance</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:16px;padding:12px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;font-size:12px;">
            <span style="font-weight:600;color:#555;margin-right:4px;">Enrollment:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Enrolled</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e3f2fd;border:1px solid #1565c0;border-radius:3px;display:inline-block;"></span> Approved</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff8ec;border:1px solid #f5a623;border-radius:3px;display:inline-block;"></span> Pending</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Declined</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#ede7f6;border:1px solid #512da8;border-radius:3px;display:inline-block;"></span> Completed</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #bf360c;border-radius:3px;display:inline-block;"></span> Dropped</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#f5f5f5;border:1px solid #546e7a;border-radius:3px;display:inline-block;"></span> Ghost</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e0f7fa;border:1px solid #006064;border-radius:3px;display:inline-block;"></span> Transferred</span>
            <span style="width:1px;height:16px;background:#ddd;margin:0 6px;"></span>
            <span style="font-weight:600;color:#555;margin-right:4px;">Payment:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Paid</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #e65100;border-radius:3px;display:inline-block;"></span> Partial</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Unpaid</span>
        </div>


        
        <div class="content-card mb-4">
            <div class="p-3">
                <form method="GET" action="<?php echo e(url()->current()); ?>" class="row g-3" id="student-filter-form">
                    <input type="hidden" name="section" value="students">
                    <div class="col-md-4">
                        <input type="text" name="student_search" class="form-fld" placeholder="Search student name, LRN, or email..." value="<?php echo e($studentSearch ?? ''); ?>" oninput="debounceFormSubmit(this)">
                    </div>
                    <div class="col-md-2">
                        <select name="student_grade" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" <?php echo e((!$studentGradeFilter || ($studentGradeFilter ?? 'all') === 'all') ? 'selected' : ''); ?>>All Grades</option>
                            <option value="nursery" <?php echo e(($studentGradeFilter ?? '') === 'nursery' ? 'selected' : ''); ?>>Nursery</option>
                            <option value="kindergarten" <?php echo e(($studentGradeFilter ?? '') === 'kindergarten' ? 'selected' : ''); ?>>Kindergarten</option>
                            <option value="grade1" <?php echo e(($studentGradeFilter ?? '') === 'grade1' ? 'selected' : ''); ?>>Grade 1</option>
                            <option value="grade2" <?php echo e(($studentGradeFilter ?? '') === 'grade2' ? 'selected' : ''); ?>>Grade 2</option>
                            <option value="grade3" <?php echo e(($studentGradeFilter ?? '') === 'grade3' ? 'selected' : ''); ?>>Grade 3</option>
                            <option value="grade4" <?php echo e(($studentGradeFilter ?? '') === 'grade4' ? 'selected' : ''); ?>>Grade 4</option>
                            <option value="grade5" <?php echo e(($studentGradeFilter ?? '') === 'grade5' ? 'selected' : ''); ?>>Grade 5</option>
                            <option value="grade6" <?php echo e(($studentGradeFilter ?? '') === 'grade6' ? 'selected' : ''); ?>>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="student_status" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" <?php echo e((!$studentStatusFilter || ($studentStatusFilter ?? 'all') === 'all') ? 'selected' : ''); ?>>All Status</option>
                            <option value="enrolled"    <?php echo e(($studentStatusFilter ?? '') === 'enrolled'    ? 'selected' : ''); ?>>Enrolled</option>
                            <option value="approved"    <?php echo e(($studentStatusFilter ?? '') === 'approved'    ? 'selected' : ''); ?>>Approved</option>
                            <option value="pending"     <?php echo e(($studentStatusFilter ?? '') === 'pending'     ? 'selected' : ''); ?>>Pending</option>
                            <option value="completed"   <?php echo e(($studentStatusFilter ?? '') === 'completed'   ? 'selected' : ''); ?>>Completed</option>
                            <option value="dropped"     <?php echo e(($studentStatusFilter ?? '') === 'dropped'     ? 'selected' : ''); ?>>Dropped</option>
                            <option value="ghost"       <?php echo e(($studentStatusFilter ?? '') === 'ghost'       ? 'selected' : ''); ?>>Ghost</option>
                            <option value="transferred" <?php echo e(($studentStatusFilter ?? '') === 'transferred' ? 'selected' : ''); ?>>Transferred</option>
                            <option value="declined"    <?php echo e(($studentStatusFilter ?? '') === 'declined'    ? 'selected' : ''); ?>>Declined</option>
                            <option value="not_enrolled"<?php echo e(($studentStatusFilter ?? '') === 'not_enrolled'? 'selected' : ''); ?>>Not Enrolled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="student_payment" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" <?php echo e((!$studentPaymentFilter || ($studentPaymentFilter ?? 'all') === 'all') ? 'selected' : ''); ?>>All Payment</option>
                            <option value="paid" <?php echo e(($studentPaymentFilter ?? '') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                            <option value="partial" <?php echo e(($studentPaymentFilter ?? '') === 'partial' ? 'selected' : ''); ?>>Partial</option>
                            <option value="unpaid" <?php echo e(($studentPaymentFilter ?? '') === 'unpaid' ? 'selected' : ''); ?>>Not Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="newest" <?php echo e(($sort ?? 'newest') === 'newest' ? 'selected' : ''); ?>>Newest First</option>
                            <option value="oldest" <?php echo e(($sort ?? 'newest') === 'oldest' ? 'selected' : ''); ?>>Oldest First</option>
                            <option value="name_asc" <?php echo e(($sort ?? 'newest') === 'name_asc' ? 'selected' : ''); ?>>Name (A-Z)</option>
                            <option value="name_desc" <?php echo e(($sort ?? 'newest') === 'name_desc' ? 'selected' : ''); ?>>Name (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="student_schoolyear" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" <?php echo e((!$studentSchoolYearFilter || $studentSchoolYearFilter === '' || $studentSchoolYearFilter === 'all') ? 'selected' : ''); ?>>All School Years</option>
                            <?php $__currentLoopData = \App\Models\Setting::schoolYearOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sy); ?>" <?php echo e(($studentSchoolYearFilter ?? '') === $sy ? 'selected' : ''); ?>><?php echo e($sy); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6>All Students</h6>

                <a href="#"><i class="bi bi-download me-1"></i>Export</a>

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Grade</th>

                            <th>Section</th>

                            <th>Contact</th>

                            <th>Status</th>

                            <th>Payment</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if(isset($students) && $students->count() > 0): ?>

                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php

                                $enr = $s->latestEnrollment;

                                $sd = $enr->student_data ?? [];

                                $gradeRaw = $sd['grade_level'] ?? ($enr->grade_level ?? null);

                                $gradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];

                                $gradeDisplay = $gradeMap[$gradeRaw] ?? ($gradeRaw ?: 'N/A');

                                $sectionDisplay = ($enr && $enr->section && $enr->section !== 'Unassigned') ? $enr->section : '—';

                                $enrollStatus = $enr ? $enr->status : 'inactive';

                                $statusClass = match($enrollStatus) {
                                    'enrolled'    => 'enrolled',
                                    'approved'    => 'approved',
                                    'pending'     => 'pending',
                                    'declined'    => 'declined',
                                    'completed'   => 'completed',
                                    'dropped'     => 'dropped',
                                    'ghost'       => 'ghost',
                                    'transferred' => 'transferred',
                                    default       => 'inactive',
                                };

                                $statusLabel = match($enrollStatus) {
                                    'enrolled'    => 'Enrolled',
                                    'approved'    => 'Approved',
                                    'pending'     => 'Pending',
                                    'declined'    => 'Declined',
                                    'completed'   => 'Completed',
                                    'dropped'     => 'Dropped',
                                    'ghost'       => 'Ghost',
                                    'transferred' => 'Transferred',
                                    default       => 'Not Enrolled',
                                };

                                $isAssessable = $enr && in_array($enrollStatus, ['enrolled','completed'])
                                    && in_array($gradeRaw, ['grade1','grade2','grade3','grade4','grade5','grade6']);

                                $payStatus = $enr->payment_status ?? 'unpaid';

                                $payIcon = $payStatus === 'paid' ? '✓' : '✗';
                                $payColor = $payStatus === 'paid' ? '#28a745' : ($payStatus === 'partial' ? '#e67e00' : '#dc3545');
                                $payBg = $payStatus === 'paid' ? '#e8f5e9' : ($payStatus === 'partial' ? '#fff3e0' : '#ffebee');

                            ?>

                            <tr data-grade="<?php echo e($gradeRaw ?? ''); ?>" data-status="<?php echo e($enrollStatus); ?>" data-payment="<?php echo e($payStatus); ?>" data-schoolyear="<?php echo e($enr->school_year ?? ''); ?>" data-search="<?php echo e(strtolower($s->name . ' ' . ($s->lrn ?? '') . ' ' . $s->email)); ?>">

                                <td>

                                    <div class="user-row-name">

                                        <div class="user-row-avatar"><?php echo e(strtoupper(substr($s->name, 0, 2))); ?></div>

                                        <div>

                                            <div style="font-weight:600;"><?php echo e($s->name); ?></div>

                                            <div class="user-row-sub"><?php echo e($enr ? $enr->reference_number : 'ID: '.$s->id); ?></div>

                                        </div>

                                    </div>

                                </td>

                                <td><?php echo e($gradeDisplay); ?></td>

                                <td><span class="<?php echo e($sectionDisplay === '—' ? 'text-muted-alt' : ''); ?>"><?php echo e($sectionDisplay); ?></span></td>

                                <td class="fs-12"><?php echo e($s->email); ?></td>

                                <td><span class="status-badge <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span></td>

                                <td>
                                    <?php if($payStatus === 'paid'): ?>
                                        <span class="status-badge paid"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                    <?php elseif($payStatus === 'partial' || $payStatus === 'partially_paid'): ?>
                                        <span class="status-badge partial"><i class="bi bi-hourglass-split"></i> Partial</span>
                                    <?php elseif($payStatus === 'pending'): ?>
                                        <span class="status-badge pending"><i class="bi bi-clock"></i> Pending</span>
                                    <?php else: ?>
                                        <span class="status-badge unpaid"><i class="bi bi-exclamation-circle-fill"></i> Unpaid</span>
                                    <?php endif; ?>
                                </td>

                                <td style="white-space:nowrap;">

                                    <a href="#" class="action-btn view js-student-view" data-id="<?php echo e($s->id); ?>" title="View"><i class="bi bi-eye-fill"></i></a>

                                    <a href="#" class="action-btn edit js-student-edit" data-id="<?php echo e($s->id); ?>" title="Edit"><i class="bi bi-pencil-fill"></i></a>

                                    <a href="<?php echo e(route('admin.students.sf10', $s->id)); ?>" target="_blank"
                                       class="action-btn" title="Download SF10"
                                       style="background:#e8f8f0;color:#1a6b2d;">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>

                                    <a href="#" class="action-btn" title="Change Status"
                                       style="background:#f0f4f8;color:#555;"
                                       onclick="openChangeStatusModal(<?php echo e($s->id); ?>, <?php echo e($enr ? $enr->id : 'null'); ?>, '<?php echo e($enrollStatus); ?>', '<?php echo e(addslashes($s->name)); ?>')">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </a>
                                    

                                    <a href="#" class="action-btn js-student-archive"
                                       data-id="<?php echo e($s->id); ?>" data-name="<?php echo e(addslashes($s->name)); ?>"
                                       title="Archive Student"
                                       style="background:#fff8e1;color:#b45309;">
                                        <i class="bi bi-archive-fill"></i>
                                    </a>

                                </td>

                            </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="7" style="text-align:center; color:var(--text); padding:40px;">

                                    <i class="bi bi-people" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                    No students enrolled yet.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

                
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <?php if(isset($students)): ?>
                    <?php echo e($students->appends([
                        'section' => 'students',
                        'sort' => $sort ?? 'newest',
                        'student_search' => $studentSearch ?? '',
                        'student_grade' => $studentGradeFilter ?? '',
                        'student_status' => $studentStatusFilter ?? '',
                        'student_payment' => $studentPaymentFilter ?? '',
                        'student_schoolyear' => $studentSchoolYearFilter ?? '',
                    ])->links()); ?>

                    <div class="pagination-info">
                        Showing <?php echo e($students->firstItem() ?? 0); ?> to <?php echo e($students->lastItem() ?? 0); ?> of <?php echo e($students->total()); ?> students
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/students-content.blade.php ENDPATH**/ ?>