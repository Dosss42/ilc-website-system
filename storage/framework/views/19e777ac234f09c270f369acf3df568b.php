    <div id="section-sections" class="dash-section" style="display:none;">

        <div class="section-header">

            <div>

                <h1>Section Management</h1>

            </div>

            <div class="d-flex gap-2">
                <button class="btn-dash btn-secondary" onclick="autoAssignStudents()">
                    <i class="bi bi-magic me-1"></i> Auto-Assign
                </button>
                <button class="btn-dash btn-primary" onclick="openSectionModal()">
                    <i class="bi bi-plus-lg"></i> Add Section
                </button>
            </div>

        </div>

        <?php

            $sections = $sections ?? collect();

            $totalSections = $sections->total();

            $activeSections = $sections->where('is_active', true)->count();

            $totalEnrolled = $sections->sum('current_enrollment');

        ?>

        <div class="row g-3 mb-4">

            <div class="col-md-4 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-diagram-3-fill"></i></div>

                    <div>

                        <div class="stat-value"><?php echo e($totalSections); ?></div>

                        <div class="stat-label">Total Sections</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div>

                    <div>

                        <div class="stat-value"><?php echo e($activeSections); ?></div>

                        <div class="stat-label">Active Sections</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon gold"><i class="bi bi-people-fill"></i></div>

                    <div>

                        <div class="stat-value"><?php echo e($totalEnrolled); ?></div>

                        <div class="stat-label">Total Enrolled</div>

                    </div>

                </div>

            </div>

        </div>

        <div class="module-toolbar">
            <div class="toolbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="sectionSearchInput" placeholder="Search sections..." onkeyup="filterSectionTable()">
            </div>
            <div class="toolbar-filter">
                <select id="sectionGradeFilterQuick" onchange="filterSectionTable()">
                    <option value="">All Grades</option>
                    <option value="nursery">Nursery</option>
                    <option value="kindergarten">Kindergarten</option>
                    <option value="grade1">Grade 1</option>
                    <option value="grade2">Grade 2</option>
                    <option value="grade3">Grade 3</option>
                    <option value="grade4">Grade 4</option>
                    <option value="grade5">Grade 5</option>
                    <option value="grade6">Grade 6</option>
                </select>
            </div>
            <div class="toolbar-filter">
                <select id="sectionStatusFilter" onchange="filterSectionTable()">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <span class="toolbar-count"><span id="sectionVisibleCount"><?php echo e($totalSections); ?></span> of <?php echo e($totalSections); ?> sections</span>
        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6><i class="bi bi-list-ul me-2" style="color:var(--green);"></i>All Sections</h6>

            </div>

            <div class="p-3">

            <div style="overflow-x:auto;">

                <table class="dash-table" id="sectionTable">

                    <thead>

                        <tr>
                            <th>Section</th>
                            <th>Grade Level</th>
                            <th>Advisory Teacher</th>
                            <th>Subjects / Schedules</th>
                            <th style="text-align:center;">Enrollment</th>
                            <th>Status</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php $__empty_1 = true; $__currentLoopData = ($sections ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <?php
                            $sec = collect([$sec])->first();
                            // Advisory teacher for this section (from TeacherAssignment, is_advisory=true)
                            $secAdvisory = \App\Models\TeacherAssignment::where('section_id', $sec->id)
                                ->where('is_advisory', true)
                                ->with('teacher:id,name')
                                ->first();
                            $secAdviserName = $secAdvisory?->teacher?->name ?? null;
                            // Schedule count for this section
                            $secScheduleCount = ($allSchedules ?? collect())->where('section_id', $sec->id)->count();
                            // Unique subjects in schedules for this section
                            $secScheduleSubjects = ($allSchedules ?? collect())
                                ->where('section_id', $sec->id)
                                ->filter(fn($s) => $s->subject)
                                ->pluck('subject.name')->unique()->values();
                        ?>

                        <tr data-grade="<?php echo e($sec->grade_level); ?>" data-status="<?php echo e($sec->is_active ? 'active' : 'inactive'); ?>" data-search="<?php echo e(strtolower($sec->name . ' ' . ($secAdviserName ?? '') . ' ' . ($sec->room_number ?? ''))); ?>">

                            <td>
                                <div class="section-name-badge">
                                    <span class="section-dot <?php echo e($sec->is_active ? 'dot-active' : 'dot-inactive'); ?>"></span>
                                    <span style="font-weight:600;"><?php echo e($sec->name); ?></span>
                                </div>
                                <div style="font-size:11px; color:var(--muted); margin-top:2px; padding-left:14px;">
                                    S.Y. <?php echo e($sec->school_year ?? '—'); ?>

                                    <?php if($sec->room_number): ?>
                                        &nbsp;·&nbsp;<i class="bi bi-geo-alt-fill" style="color:var(--blue);font-size:10px;"></i> <?php echo e($sec->room_number); ?>

                                    <?php endif; ?>
                                </div>
                            </td>

                            <td><span class="grade-chip"><?php echo e(ucfirst(str_replace(['grade','_'],[' Grade ',''],$sec->grade_level ?? ''))); ?></span></td>

                            <td>
                                <?php if($secAdviserName): ?>
                                    <div style="display:flex;align-items:center;gap:5px;">
                                        <span style="width:26px;height:26px;border-radius:50%;background:#e0f2fe;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#0369a1;flex-shrink:0;">
                                            <?php echo e(strtoupper(substr($secAdviserName, 0, 2))); ?>

                                        </span>
                                        <div>
                                            <div style="font-size:12px;font-weight:600;"><?php echo e($secAdviserName); ?></div>
                                            <div style="font-size:10px;color:var(--muted);">Advisory</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size:12px;color:#bbb;">
                                        <i class="bi bi-exclamation-circle me-1"></i>Not assigned
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($secScheduleCount > 0): ?>
                                    <div style="font-size:12px;font-weight:600;color:#166534;margin-bottom:3px;">
                                        <i class="bi bi-calendar3 me-1"></i><?php echo e($secScheduleCount); ?> slot(s)
                                    </div>
                                    <div style="font-size:10px;color:var(--muted);">
                                        <?php echo e($secScheduleSubjects->take(3)->implode(', ')); ?><?php echo e($secScheduleSubjects->count() > 3 ? ' +'.($secScheduleSubjects->count()-3).' more' : ''); ?>

                                    </div>
                                <?php else: ?>
                                    <span style="font-size:12px;color:#bbb;"><i class="bi bi-calendar-x me-1"></i>No schedule yet</span>
                                <?php endif; ?>
                            </td>

                            <td style="text-align:center;">
                                <?php
                                    $secEnrolled = $sec->current_enrollment ?? 0;
                                    $secMax = $sec->max_students ?? 30;
                                    $secPct = $secMax > 0 ? min(100, round($secEnrolled / $secMax * 100)) : 0;
                                    $secColorClass = $secPct >= 90 ? 'red' : ($secPct >= 70 ? 'gold' : 'green');
                                ?>
                                <div style="font-size:12px; font-weight:700; color:var(--<?php echo e($secColorClass); ?>); margin-bottom:3px;"><?php echo e($secEnrolled); ?>/<?php echo e($secMax); ?></div>
                                <div style="height:5px; background:#e2e8f0; border-radius:4px; overflow:hidden; width:72px; margin:0 auto;">
                                    <div style="height:100%; background:var(--<?php echo e($secColorClass); ?>); width:<?php echo e($secPct); ?>%; border-radius:4px; transition:width .3s;"></div>
                                </div>
                            </td>

                            <td>

                                <?php if($sec->is_active): ?>

                                    <span class="status-badge active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>

                                <?php else: ?>

                                    <span class="status-badge inactive"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>

                                <?php endif; ?>

                            </td>

                            <td style="text-align:center;white-space:nowrap;">
                                <button class="action-btn view js-section-view-students" title="View Students" data-id="<?php echo e($sec->id); ?>" data-name="<?php echo e($sec->name); ?>"><i class="bi bi-people-fill"></i></button>
                                <button class="action-btn view js-section-view-subjects" title="View Subjects" data-id="<?php echo e($sec->id); ?>" data-name="<?php echo e($sec->name); ?>"><i class="bi bi-book-fill"></i></button>
<button class="action-btn edit js-section-manage-subjects" title="Manage Subjects" data-id="<?php echo e($sec->id); ?>" data-name="<?php echo e($sec->name); ?>" data-grade="<?php echo e($sec->grade_level); ?>"><i class="bi bi-gear-fill"></i></button>
                                <button class="action-btn add-student" title="Add Student" data-id="<?php echo e($sec->id); ?>" data-name="<?php echo e($sec->name); ?>" data-enrolled="<?php echo e($sec->current_enrollment ?? 0); ?>" data-max="<?php echo e($sec->max_students ?? 30); ?>"><i class="bi bi-person-plus-fill"></i></button>
                                <button class="action-btn edit js-section-edit" title="Edit Section"
                                    data-id="<?php echo e($sec->id); ?>"
                                    data-name="<?php echo e($sec->name); ?>"
                                    data-grade="<?php echo e($sec->grade_level); ?>"
                                    data-room="<?php echo e($sec->room_number); ?>"
                                    data-sy="<?php echo e($sec->school_year); ?>"
                                    data-active="<?php echo e($sec->is_active ? '1' : '0'); ?>"
                                    data-max="<?php echo e($sec->max_students ?? 30); ?>"><i class="bi bi-pencil-fill"></i></button>
                                <button class="action-btn delete js-section-delete" title="Delete" data-id="<?php echo e($sec->id); ?>"><i class="bi bi-trash-fill"></i></button>
                            </td>

                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td colspan="6" style="text-align:center; padding:60px 20px;">

                                <div style="background:#e8f8f0; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">

                                    <i class="bi bi-diagram-3" style="font-size:32px; color:var(--green);"></i>

                                </div>

                                <div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">No Sections Yet</div>

                                <div style="font-size:13px; color:var(--muted); margin-bottom:16px;">Create your first class section to get started.</div>

                                <button class="btn-dash btn-primary" onclick="openSectionModal()">

                                    <i class="bi bi-plus-lg"></i> Add First Section

                                </button>

                            </td>

                        </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

                <div class="p-3 border-top" style="border-color:var(--border);">
                    <?php if(isset($sections)): ?>
                    <?php echo e($sections->links()); ?>

                    <?php endif; ?>
                    <?php if(isset($sections) && $sections->count() > 0): ?>
                    <div class="pagination-info">
                        Showing <?php echo e($sections->firstItem()); ?> to <?php echo e($sections->lastItem()); ?> of <?php echo e($sections->total()); ?> sections
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            </div>

        </div>

    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/sections.blade.php ENDPATH**/ ?>