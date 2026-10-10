        <div class="section-header">

            <div>

                <h1>Guidance Records</h1>

                <p>Student behavioral and counseling records.</p>

            </div>

            <a href="#" class="btn-dash btn-primary" onclick="openGuidanceModal()">

                <i class="bi bi-plus-lg"></i> Add Record

            </a>

        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-journal-medical"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($guidanceTotalCount ?? 0); ?></div>
                        <div class="stat-label">Total Records</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-exclamation-circle-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($guidanceOpenCount ?? 0); ?></div>
                        <div class="stat-label">Open</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($guidanceResolvedCount ?? 0); ?></div>
                        <div class="stat-label">Resolved / Closed</div>
                    </div>
                </div>
            </div>
        </div>

        
        <form method="GET" action="<?php echo e(url()->current()); ?>" id="guidance-filter-form">
            <input type="hidden" name="section" value="guidance">
            <div class="module-toolbar">
                <div class="toolbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="guidance_search" placeholder="Search student name..." value="<?php echo e($guidanceSearch ?? ''); ?>" oninput="debounceFormSubmit(this)">
                </div>
                <div class="toolbar-filter">
                    <select name="guidance_concern" onchange="this.form.requestSubmit()">
                        <option value="">All Concerns</option>
                        <option value="Behavioral" <?php echo e(($guidanceConcern ?? '') === 'Behavioral' ? 'selected' : ''); ?>>Behavioral</option>
                        <option value="Academic" <?php echo e(($guidanceConcern ?? '') === 'Academic' ? 'selected' : ''); ?>>Academic</option>
                        <option value="Emotional" <?php echo e(($guidanceConcern ?? '') === 'Emotional' ? 'selected' : ''); ?>>Emotional</option>
                        <option value="Family" <?php echo e(($guidanceConcern ?? '') === 'Family' ? 'selected' : ''); ?>>Family</option>
                        <option value="Social" <?php echo e(($guidanceConcern ?? '') === 'Social' ? 'selected' : ''); ?>>Social</option>
                        <option value="Health" <?php echo e(($guidanceConcern ?? '') === 'Health' ? 'selected' : ''); ?>>Health</option>
                        <option value="Other" <?php echo e(($guidanceConcern ?? '') === 'Other' ? 'selected' : ''); ?>>Other</option>
                    </select>
                    <select name="guidance_status" onchange="this.form.requestSubmit()">
                        <option value="">All Status</option>
                        <option value="open" <?php echo e(($guidanceStatus ?? '') === 'open' ? 'selected' : ''); ?>>Open</option>
                        <option value="in_progress" <?php echo e(($guidanceStatus ?? '') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="resolved" <?php echo e(($guidanceStatus ?? '') === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                        <option value="closed" <?php echo e(($guidanceStatus ?? '') === 'closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                    <select name="guidance_sort" onchange="this.form.requestSubmit()">
                        <option value="date_desc" <?php echo e(($guidanceSort ?? 'date_desc') === 'date_desc' ? 'selected' : ''); ?>>Newest First</option>
                        <option value="date_asc" <?php echo e(($guidanceSort ?? 'date_desc') === 'date_asc' ? 'selected' : ''); ?>>Oldest First</option>
                    </select>
                    <?php if(($guidanceSearch ?? '') || ($guidanceConcern ?? '') || ($guidanceStatus ?? '')): ?>
                    <a href="<?php echo e(url()->current()); ?>?section=guidance" class="btn-dash btn-secondary" style="padding:8px 14px;">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                    <?php endif; ?>
                </div>
                <span class="toolbar-count"><?php echo e(isset($guidanceRecords) ? $guidanceRecords->total() : 0); ?> record(s)</span>
            </div>
        </form>

        <div class="content-card">

            <div class="content-card-header">
                <h6><i class="bi bi-list-ul me-2" style="color:var(--blue);"></i>All Guidance Records</h6>
            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Date</th>

                            <th>Concern</th>

                            <th>Counselor</th>

                            <th>Status</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>
                        <?php if(isset($guidanceRecords) && $guidanceRecords->count() > 0): ?>
                            <?php $__currentLoopData = $guidanceRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="user-row-name">
                                        <div class="user-row-avatar"><?php echo e(strtoupper(substr($g->student->name ?? '?', 0, 2))); ?></div>
                                        <div>
                                            <div style="font-weight:600;"><?php echo e($g->student->name ?? 'Unknown'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($g->date->format('M d, Y')); ?></td>
                                <td>
                                    <span class="grade-chip"><?php echo e($g->concern_type); ?></span>
                                    <div style="font-size:11px; color:var(--muted); margin-top:4px;"><?php echo e(Str::limit($g->concern_description, 50)); ?></div>
                                </td>
                                <td><?php echo e($g->counselor->name ?? 'Unassigned'); ?></td>
                                <td>
                                    <?php
                                        $statusBadge = match($g->status) {
                                            'open' => 'pending',
                                            'in_progress' => 'approved',
                                            'resolved' => 'enrolled',
                                            'closed' => 'inactive',
                                            default => 'inactive'
                                        };
                                    ?>
                                    <span class="status-badge <?php echo e($statusBadge); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $g->status))); ?></span>
                                </td>
                                <td>
                                    <button class="action-btn view js-guidance-view" title="View" data-id="<?php echo e($g->id); ?>">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="action-btn edit js-guidance-edit" title="Edit" data-id="<?php echo e($g->id); ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="action-btn delete js-guidance-delete" title="Delete" data-id="<?php echo e($g->id); ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--text); padding:40px;">
                                    <i class="bi bi-journal-medical" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>
                                    No guidance records yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>

            </div>


            <div class="p-3 border-top" style="border-color:var(--border);">
                <?php if(isset($guidanceRecords)): ?>
                <?php echo e($guidanceRecords->appends([
                    'section'          => 'guidance',
                    'guidance_search'  => $guidanceSearch ?? '',
                    'guidance_concern' => $guidanceConcern ?? '',
                    'guidance_status'  => $guidanceStatus ?? '',
                    'guidance_sort'    => $guidanceSort ?? 'date_desc',
                ])->links()); ?>

                <div class="pagination-info">
                    Showing <?php echo e($guidanceRecords->firstItem() ?? 0); ?> to <?php echo e($guidanceRecords->lastItem() ?? 0); ?> of <?php echo e($guidanceRecords->total()); ?> records
                </div>
                <?php endif; ?>
            </div>


        </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/guidance-content.blade.php ENDPATH**/ ?>