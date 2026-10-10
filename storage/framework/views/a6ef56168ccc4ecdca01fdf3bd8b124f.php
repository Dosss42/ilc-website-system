    <div id="section-summer" class="dash-section" style="display:none;">

        <div class="section-header">

            <div>

                <h1>Summer Class Management</h1>

                <p>Manage summer classes for students who need remedial or make-up courses.</p>

            </div>

            <button class="btn-dash btn-primary" onclick="openCreateSummerModal()">

                <i class="bi bi-plus-lg"></i> Create Summer Class

            </button>

        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-sun-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($summerTotalCount ?? 0); ?></div>
                        <div class="stat-label">Total Classes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($summerOngoingCount ?? 0); ?></div>
                        <div class="stat-label">Ongoing</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($summerCompletedCount ?? 0); ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="module-toolbar">
            <div class="toolbar-filter">
                <select id="summer-sy-filter" onchange="loadSummerClasses()">
                    <option value="">All School Years</option>
                    <?php
                        $currentSY = now()->month >= 6 ? now()->year : now()->year - 1;
                        for ($y = $currentSY + 1; $y >= 1994; $y--) {
                            $sy = $y . '-' . ($y + 1);
                            echo "<option value=\"$sy\">$sy</option>";
                        }
                    ?>
                </select>
                <select id="summer-status-filter" onchange="loadSummerClasses()">
                    <option value="">All Status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6><i class="bi bi-list-ul me-2" style="color:var(--blue);"></i>Summer Classes</h6>

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Subject</th>

                            <th>Grade Level</th>

                            <th>Teacher</th>

                            <th>Schedule</th>

                            <th>Period</th>

                            <th>Students</th>

                            <th>Status</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody id="summer-classes-body">

                        <tr>

                            <td colspan="8" style="text-align:center; color:var(--text); padding:40px;">

                                <i class="bi bi-sun" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                No summer classes created yet. Click "Create Summer Class" to get started.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <div id="summer-pagination" class="p-3 border-top" style="border-color:var(--border);">
                <!-- Pagination will be rendered here -->
            </div>

        </div>

    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/summer.blade.php ENDPATH**/ ?>