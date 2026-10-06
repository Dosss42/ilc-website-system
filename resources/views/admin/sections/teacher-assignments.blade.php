    <div id="section-teacher-assignments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-journal-bookmark-fill" style="color:var(--purple); margin-right:8px;"></i>Advisory Teacher Assignments</h1>
                <p>Assign advisory (homeroom) teachers to sections.</p>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <select id="ta-school-year-filter" class="dash-form-control" style="width:180px;" onchange="loadTeacherAssignments()">
                    @php
                        $taDefault = $currentSchoolYear ?? '';
                    @endphp
                    @foreach(\App\Models\Setting::schoolYearOptions() as $sy)
                        <option value="{{ $sy }}" {{ $sy === $taDefault ? 'selected' : '' }}>{{ $sy }}</option>
                    @endforeach
                </select>
                <select id="ta-sort-filter" class="dash-form-control" style="width:180px;" onchange="loadTeacherAssignments()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="teacher_asc">Teacher (A-Z)</option>
                    <option value="teacher_desc">Teacher (Z-A)</option>
                </select>
                <button class="btn-dash btn-primary" onclick="openAddAssignmentModal()">
                    <i class="bi bi-plus-lg me-1"></i> Advisory Assignment
                </button>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4" id="ta-summary-cards">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <div class="stat-value" id="ta-advisory-count">0</div>
                        <div class="stat-label">Advisory Assignments</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="stat-value" id="ta-teacher-count">0</div>
                        <div class="stat-label">Active Teachers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="stat-value" id="ta-section-count">0</div>
                        <div class="stat-label">Sections Covered</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assignments Table --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <h6><i class="bi bi-table me-2"></i>Advisory Assignments</h6>
            </div>
            <div style="padding:16px; overflow-x:auto;">
                <table class="dash-table" id="ta-table">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Section</th>
                            <th>Grade Level</th>
                            <th>School Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ta-table-body">
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text);">
                            <i class="bi bi-hourglass-split" style="font-size:24px; display:block; margin-bottom:8px;"></i>Loading assignments...
                        </td></tr>
                    </tbody>
                </table>
                <div id="ta-pagination"></div>
            </div>
        </div>
    </div>{{-- /section-teacher-assignments --}}
