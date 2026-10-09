        <div class="section-header">
            <div>
                <h1><i class="bi bi-archive-fill" style="color:#b45309; margin-right:8px;"></i>Archives</h1>
                <p>Archived students are preserved here. Restore them to return them to Student Management, or permanently delete to remove all records.</p>
            </div>
        </div>

        
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-archive-fill"></i></div>
                    <div>
                        <div class="stat-value" style="color:#b45309;"><?php echo e(isset($archivedStudents) ? $archivedStudents->total() : 0); ?></div>
                        <div class="stat-label">Total Archived</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-arrow-counterclockwise"></i></div>
                    <div>
                        <div class="stat-value">
                            <?php echo e(($archivedStudents ?? collect())->filter(fn($a) => $a->latestEnrollment && in_array($a->latestEnrollment->status, ['enrolled','approved']))->count()); ?>

                        </div>
                        <div class="stat-label">Restorable</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12" style="display:flex; align-items:center;">
                <div style="background:#fff8e1; border:1px solid #fde68a; border-radius:10px; padding:12px 16px; font-size:13px; color:#92400e; width:100%;">
                    <i class="bi bi-info-circle-fill me-2" style="color:#b45309;"></i>
                    Archived students keep all records intact.
                    Use <strong>Restore</strong> to return them to Student Management,
                    or <strong>Permanently Delete</strong> to remove all data forever.
                </div>
            </div>
        </div>

        
        <div class="content-card mb-4">
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div style="position:relative;">
                            <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#aaa;font-size:13px;"></i>
                            <input type="text" id="archive-search-input" class="form-fld" placeholder="Search name, email, or LRN…" style="padding-left:32px;" oninput="filterArchiveRows()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="archive-grade-filter" class="form-fld" onchange="filterArchiveRows()">
                            <option value="">All Grades</option>
                            <option value="Nursery">Nursery</option>
                            <option value="Kinder">Kindergarten</option>
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-4" style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:12px;color:var(--muted);" id="archive-count-label">
                            Showing <?php echo e(isset($archivedStudents) ? $archivedStudents->total() : 0); ?> archived student(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-people me-2" style="color:#b45309;"></i>Archived Students</h6>
            </div>

            <?php if(!isset($archivedStudents) || $archivedStudents->isEmpty()): ?>
                <div style="text-align:center; padding:60px 24px; color:#aaa;">
                    <div style="width:72px;height:72px;background:#fff8e1;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="bi bi-archive" style="font-size:32px; color:#f5c842;"></i>
                    </div>
                    <div style="font-size:16px; font-weight:700; color:#555; margin-bottom:8px;">No Archived Students</div>
                    <div style="font-size:13px; color:#999; max-width:360px; margin:0 auto;">
                        When you archive a student from Student Management, they will appear here.
                        Their records are safely preserved and can be restored at any time.
                    </div>
                    <button class="btn-dash btn-secondary mt-3" onclick="showSection('students')">
                        <i class="bi bi-people-fill"></i> Go to Student Management
                    </button>
                </div>
            <?php else: ?>
                <div style="overflow-x:auto;" id="archive-table-wrap">
                    <table class="dash-table" style="min-width:760px;" id="archive-table">
                        <thead>
                            <tr>
                                <th style="min-width:190px;">Student</th>
                                <th style="min-width:120px;">LRN</th>
                                <th style="min-width:110px;">Last Grade</th>
                                <th style="min-width:110px;">Last S.Y.</th>
                                <th style="min-width:100px;">Status</th>
                                <th style="min-width:120px; white-space:nowrap;">Archived On</th>
                                <th style="min-width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="archive-tbody">
                            <?php $__currentLoopData = $archivedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $arcEnr  = $arc->latestEnrollment;
                                $arcSd   = $arcEnr ? ($arcEnr->student_data ?? []) : [];
                                $arcGr   = $arcSd['grade_level'] ?? ($arcEnr ? ($arcEnr->grade_level ?? null) : null);
                                $arcGrMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
                                $arcGrDisplay = $arcGrMap[$arcGr] ?? $arcGr;
                                $arcStatus = $arcEnr ? $arcEnr->status : null;
                                $arcStatusClass = match($arcStatus) {
                                    'enrolled'    => 'color:#166534;background:#dcfce7;',
                                    'approved'    => 'color:#1e40af;background:#dbeafe;',
                                    'completed'   => 'color:#1e40af;background:#dbeafe;',
                                    'dropped'     => 'color:#9a3412;background:#ffedd5;',
                                    'transferred' => 'color:#0e7490;background:#cffafe;',
                                    'pending'     => 'color:#92400e;background:#fff8e1;',
                                    default       => 'color:#6b7280;background:#f3f4f6;',
                                };
                            ?>
                            <tr data-search="<?php echo e(strtolower($arc->name . ' ' . $arc->email . ' ' . ($arc->lrn ?? ''))); ?>"
                                data-grade="<?php echo e($arcGrDisplay); ?>">
                                <td>
                                    <div class="user-row-name">
                                        <div class="user-row-avatar" style="background:#fff8e1;color:#b45309;">
                                            <?php echo e(strtoupper(substr($arc->name, 0, 2))); ?>

                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text);"><?php echo e($arc->name); ?></div>
                                            <div style="font-size:11px; color:var(--muted);"><?php echo e($arc->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:12px; color:var(--muted);"><?php echo e($arc->lrn ?: '—'); ?></td>
                                <td>
                                    <?php if($arcGrDisplay): ?>
                                        <span class="grade-chip"><?php echo e($arcGrDisplay); ?></span>
                                    <?php else: ?>
                                        <span style="color:#aaa;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:12px;"><?php echo e($arcEnr ? ($arcEnr->school_year ?? '—') : '—'); ?></td>
                                <td>
                                    <?php if($arcStatus): ?>
                                        <span class="status-badge" style="<?php echo e($arcStatusClass); ?>"><?php echo e(ucfirst($arcStatus)); ?></span>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space:nowrap;">
                                    <div style="font-size:12px; color:#92400e; font-weight:600;">
                                        <?php echo e($arc->deleted_at->format('M d, Y')); ?>

                                    </div>
                                    <div style="font-size:10px; color:#bbb;"><?php echo e($arc->deleted_at->diffForHumans()); ?></div>
                                </td>
                                <td style="white-space:nowrap;">
                                    <a href="#" class="action-btn js-student-restore"
                                       data-id="<?php echo e($arc->id); ?>" data-name="<?php echo e(addslashes($arc->name)); ?>"
                                       title="Restore Student" style="background:#e8f8f0;color:#1a6b2d;">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.students.sf10', $arc->id)); ?>" target="_blank"
                                       class="action-btn" title="Download SF10"
                                       style="background:#eef2ff;color:#3730a3;">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                    <a href="#" class="action-btn js-student-force-delete"
                                       data-id="<?php echo e($arc->id); ?>" data-name="<?php echo e(addslashes($arc->name)); ?>"
                                       title="Permanently Delete" style="background:#fdecea;color:#c0392b;">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    
                    <div id="archive-no-results" style="display:none; text-align:center; padding:40px; color:#aaa;">
                        <i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:10px; opacity:0.4;"></i>
                        No archived students match your search.
                    </div>
                </div>
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <?php echo e($archivedStudents->appends(['section' => 'archives'])->links()); ?>

                    <div class="pagination-info">
                        Showing <?php echo e($archivedStudents->firstItem()); ?> to <?php echo e($archivedStudents->lastItem()); ?> of <?php echo e($archivedStudents->total()); ?> archived student(s)
                    </div>
                </div>
            <?php endif; ?>
        </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/archives-content.blade.php ENDPATH**/ ?>