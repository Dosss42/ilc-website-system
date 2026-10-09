        <div class="section-header">

            <div>

                <h1>Enrollment Management</h1>

                <p>Manage student enrollment applications and approvals.</p>

            </div>

            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

                
                <?php $isEnrollOpen = $enrollmentOpen ?? true; ?>
                <div id="enrollment-window-badge"
                     style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;
                            background:<?php echo e($isEnrollOpen ? '#e8f8f0' : '#fdecea'); ?>;
                            color:<?php echo e($isEnrollOpen ? '#1a7a44' : '#c0392b'); ?>;
                            border:1.5px solid <?php echo e($isEnrollOpen ? '#27ae60' : '#e74c3c'); ?>;">
                    <span style="width:8px;height:8px;border-radius:50%;display:inline-block;
                                 background:<?php echo e($isEnrollOpen ? '#27ae60' : '#e74c3c'); ?>;
                                 box-shadow:0 0 0 2px <?php echo e($isEnrollOpen ? '#a8d5b5' : '#f5b7b1'); ?>;"></span>
                    <span id="enrollment-window-label">Enrollment <?php echo e($isEnrollOpen ? 'OPEN' : 'CLOSED'); ?></span>
                </div>

                <button class="btn-dash <?php echo e($isEnrollOpen ? 'btn-secondary' : 'btn-primary'); ?>"
                        id="enrollment-toggle-btn"
                        onclick="toggleEnrollmentWindow()">
                    <i class="bi bi-<?php echo e($isEnrollOpen ? 'lock-fill' : 'unlock-fill'); ?> me-1"></i>
                    <span id="enrollment-toggle-label"><?php echo e($isEnrollOpen ? 'Close Enrollment' : 'Open Enrollment'); ?></span>
                </button>

                <a href="#" class="btn-dash btn-primary" onclick="openWalkInEnrollmentModal()">
                    <i class="bi bi-person-plus-fill"></i> New Enrollment
                </a>

            </div>

        </div>

        
        <?php if(!$isEnrollOpen): ?>
        <div style="background:#fff8ec;border:1px solid #f5a623;border-radius:10px;padding:12px 18px;margin-bottom:16px;display:flex;align-items:center;gap:10px;font-size:13px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#d68910;font-size:16px;"></i>
            <span><strong>Online enrollment is currently closed.</strong> Students cannot submit new applications. Walk-in enrollment is still available through this panel.</span>
        </div>
        <?php endif; ?>

        

        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-clipboard-check-fill"></i></div>

                    <div>

                        <div class="stat-value"><?php echo e($enrolledThisYear ?? 0); ?></div>

                        <div class="stat-label">Enrolled This Year</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon gold"><i class="bi bi-cash-stack"></i></div>

                    <div>

                        <div class="stat-value">₱<?php echo e(number_format($totalFeesCollected ?? 0, 2)); ?></div>

                        <div class="stat-label">Total Fees Collected</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-exclamation-circle-fill"></i></div>

                    <div>

                        <div class="stat-value"><?php echo e($pendingPaymentsCount ?? 0); ?></div>

                        <div class="stat-label">Pending Payments</div>

                    </div>

                </div>

            </div>

        </div>

        

        <div id="admin-dashboard-charts"></div>

        <div class="content-card mb-4">
            <div class="p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="enrollmentSearchInput" class="form-fld"
                                   placeholder="Search by name, email, or reference number..."
                                   value="<?php echo e($enrollmentSearch ?? ''); ?>"
                                   oninput="debouncedEnrollmentSearch()"
                                   onkeydown="if(event.key==='Enter'){ event.preventDefault(); clearTimeout(_enrollmentSearchTimer); filterEnrollments(); }">
                        </div>
                    </div>
                    <?php if(($enrollmentSearch ?? '') !== ''): ?>
                    <div class="col-md-2">
                        <button type="button" class="btn-dash btn-secondary w-100" onclick="clearEnrollmentSearch()"><i class="bi bi-x-lg"></i> Clear</button>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-fld" id="statusFilter" data-sort="<?php echo e($sort ?? 'newest'); ?>" data-grade="<?php echo e($gradeFilter ?? 'all'); ?>" onchange="filterEnrollments()">
                            <option value="all" <?php echo e(($statusFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>All Status</option>
                            <option value="pending" <?php echo e(($statusFilter ?? 'all') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="approved" <?php echo e(($statusFilter ?? 'all') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                            <option value="enrolled" <?php echo e(($statusFilter ?? 'all') === 'enrolled' ? 'selected' : ''); ?>>Enrolled</option>
                            <option value="rejected" <?php echo e(($statusFilter ?? 'all') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-fld" id="gradeFilter" data-sort="<?php echo e($sort ?? 'newest'); ?>" data-status="<?php echo e($statusFilter ?? 'all'); ?>" onchange="filterEnrollmentsGrade()">
                            <option value="all" <?php echo e(($gradeFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>All Grades</option>
                            <option value="nursery" <?php echo e(($gradeFilter ?? 'all') === 'nursery' ? 'selected' : ''); ?>>Nursery</option>
                            <option value="kindergarten" <?php echo e(($gradeFilter ?? 'all') === 'kindergarten' ? 'selected' : ''); ?>>Kindergarten</option>
                            <option value="grade1" <?php echo e(($gradeFilter ?? 'all') === 'grade1' ? 'selected' : ''); ?>>Grade 1</option>
                            <option value="grade2" <?php echo e(($gradeFilter ?? 'all') === 'grade2' ? 'selected' : ''); ?>>Grade 2</option>
                            <option value="grade3" <?php echo e(($gradeFilter ?? 'all') === 'grade3' ? 'selected' : ''); ?>>Grade 3</option>
                            <option value="grade4" <?php echo e(($gradeFilter ?? 'all') === 'grade4' ? 'selected' : ''); ?>>Grade 4</option>
                            <option value="grade5" <?php echo e(($gradeFilter ?? 'all') === 'grade5' ? 'selected' : ''); ?>>Grade 5</option>
                            <option value="grade6" <?php echo e(($gradeFilter ?? 'all') === 'grade6' ? 'selected' : ''); ?>>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-fld" id="enrollmentSortFilter" data-status="<?php echo e($statusFilter ?? 'all'); ?>" data-grade="<?php echo e($gradeFilter ?? 'all'); ?>">
                            <option value="newest" <?php echo e(($sort ?? 'newest') === 'newest' ? 'selected' : ''); ?>>Newest First</option>
                            <option value="oldest" <?php echo e(($sort ?? 'newest') === 'oldest' ? 'selected' : ''); ?>>Oldest First</option>
                            <option value="name_asc" <?php echo e(($sort ?? 'newest') === 'name_asc' ? 'selected' : ''); ?>>Name (A-Z)</option>
                            <option value="name_desc" <?php echo e(($sort ?? 'newest') === 'name_desc' ? 'selected' : ''); ?>>Name (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="text-muted" style="font-size:13px;">
                            <?php if(isset($enrollments)): ?>
                            Showing <?php echo e($enrollments->firstItem() ?? 0); ?> to <?php echo e($enrollments->lastItem() ?? 0); ?> of <?php echo e($enrollments->total()); ?> enrollments
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">

            <div class="content-card-header"><h6>Enrollment Records</h6></div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Student ID</th>

                            <th>Name</th>

                            <th>Grade</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if(isset($enrollments) && $enrollments->count() > 0): ?>

                            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <tr>

                                <td><?php echo e($e->reference_number); ?></td>

                                <td>
                                    <?php echo e($e->student_data['first_name'] ?? ''); ?>

                                    <?php echo e($e->student_data['middle_name'] ?? ''); ?>

                                    <?php echo e($e->student_data['last_name'] ?? ''); ?>

                                    <?php $eType = $e->student_data['student_type'] ?? ''; ?>
                                    <?php if($eType === 'returning'): ?>
                                        <span style="display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px;vertical-align:middle;">
                                            <i class="bi bi-arrow-repeat"></i> Re-Enroll
                                        </span>
                                    <?php elseif($eType === 'transferee'): ?>
                                        <span style="display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;color:#15803d;border:1px solid #86efac;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px;vertical-align:middle;">
                                            <i class="bi bi-box-arrow-in-right"></i> Transferee
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>

                                    <?php echo e($e->student_data['grade_level'] ?? ''); ?>


                                </td>

                                <td>

                                    <?php
                                        $enrollIcon = match($e->status) {
                                            'enrolled'  => 'bi-check-circle-fill',
                                            'approved'  => 'bi-shield-check',
                                            'pending'   => 'bi-clock',
                                            'declined'  => 'bi-x-circle-fill',
                                            'completed' => 'bi-archive-fill',
                                            default     => 'bi-circle',
                                        };
                                    ?>
                                    <span class="status-badge status-<?php echo e($e->status); ?>">
                                        <i class="bi <?php echo e($enrollIcon); ?>"></i> <?php echo e(ucfirst($e->status)); ?>

                                    </span>

                                </td>

                                <td><?php echo e($e->created_at); ?></td>

                                <td>

                                    <a href="#" class="btn btn-sm js-enrollment-view" data-id="<?php echo e($e->id); ?>" title="View Application Details"
                                        style="background:#eff6ff;color:#1d4ed8;border:none;">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if($e->status === 'pending'): ?>
                                        <button class="btn btn-sm js-enrollment-approve" data-id="<?php echo e($e->id); ?>" title="Approve Enrollment"
                                            style="background:#f0fdf4;color:#16a34a;border:none;">
                                            <i class="bi bi-check"></i>
                                        </button>

                                        <button class="btn btn-sm js-enrollment-decline" data-id="<?php echo e($e->id); ?>" title="Decline Enrollment"
                                            style="background:#fef2f2;color:#dc2626;border:none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    <?php endif; ?>

                                </td>

                            </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" style="text-align:center; color:var(--text); padding:40px;">

                                    <i class="bi bi-clipboard" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                    No enrollment records yet.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

                
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <?php if(isset($enrollments)): ?>
                    <?php echo e($enrollments->appends([
                        'sort' => $sort ?? 'newest',
                        'status' => $statusFilter ?? 'all',
                        'grade' => $gradeFilter ?? 'all',
                    ])->links()); ?>

                    <?php endif; ?>
                    <?php if(isset($enrollments) && $enrollments->count() > 0): ?>
                    <div class="pagination-info">
                        Showing <?php echo e($enrollments->firstItem()); ?> to <?php echo e($enrollments->lastItem()); ?> of <?php echo e($enrollments->total()); ?> enrollments
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/enrollment-content.blade.php ENDPATH**/ ?>