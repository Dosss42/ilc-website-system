    <div id="section-schedules" class="dash-section" style="display:none;">

        <div class="section-header">
            <div>
                <h1>Schedule Management</h1>
                <p>Manage class schedules by grade level and section.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-dash btn-secondary" onclick="openCopyTermModal()">
                    <i class="bi bi-copy"></i> Copy Term
                </button>
                <button class="btn-dash btn-primary" onclick="openScheduleModal()">
                    <i class="bi bi-plus-lg"></i> Add Schedule
                </button>
            </div>
        </div>

        {{-- Schedule Filters --}}
        <div class="sched-filter-card">
            <div class="sched-filter-title">Filters</div>
            <div class="sched-toolbar">
                <div class="sched-field-group">
                    <label for="scheduleGradeFilter">Grade Level</label>
                    <select class="form-fld" id="scheduleGradeFilter">
                        <option value="">— Select Grade —</option>
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
                <div class="sched-field-group">
                    <label for="scheduleSectionFilter">Section</label>
                    <select class="form-fld" id="scheduleSectionFilter" onchange="loadScheduleGrid()">
                        <option value="">All Sections</option>
                    </select>
                </div>
                <div class="sched-field-group">
                    <label for="scheduleTermFilter">Term</label>
                    <select class="form-fld" id="scheduleTermFilter" onchange="loadScheduleGrid()">
                        <option value="1">1st Term</option>
                        <option value="2">2nd Term</option>
                        <option value="3">3rd Term</option>
                    </select>
                </div>
                <button class="btn-dash btn-primary" onclick="loadScheduleGrid()">
                    <i class="bi bi-search"></i> Load
                </button>
            </div>
        </div>

        {{-- Empty State --}}
        <div id="scheduleEmptyState" class="sched-empty-card">
            <div class="sched-empty-icon">
                <i class="bi bi-calendar-week"></i>
            </div>
            <div style="font-size:16px; font-weight:700; color:var(--text); margin-bottom:4px;">Select a grade level to view schedules</div>
            <div style="font-size:13px; color:var(--muted);">Choose a grade above, or jump straight to one below.</div>

            <div class="sched-quickpick">
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('nursery')"><i class="bi bi-lightning-fill"></i> Nursery</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('kindergarten')"><i class="bi bi-lightning-fill"></i> Kindergarten</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade1')"><i class="bi bi-lightning-fill"></i> Grade 1</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade2')"><i class="bi bi-lightning-fill"></i> Grade 2</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade3')"><i class="bi bi-lightning-fill"></i> Grade 3</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade4')"><i class="bi bi-lightning-fill"></i> Grade 4</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade5')"><i class="bi bi-lightning-fill"></i> Grade 5</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade6')"><i class="bi bi-lightning-fill"></i> Grade 6</button>
            </div>
        </div>

        {{-- Schedule Grid --}}
        <div id="scheduleGridContainer" class="content-card" style="display:none;">
            <div class="content-card-header">
                <h6 id="scheduleInfoText">Schedule</h6>
                <div style="display:flex;gap:8px;">
                    <button class="btn-dash btn-secondary" onclick="exportSchedule()" style="display:inline-flex;align-items:center;gap:7px;">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export CSV
                    </button>
                    <button class="btn-dash btn-primary" onclick="downloadScheduleAdminPDF()" style="display:inline-flex;align-items:center;gap:7px;">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                    </button>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:0 16px 12px;font-size:11.5px;color:#64748b;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:11px;"></i>Red = schedule conflict
            </div>
            <div style="overflow-x:auto;">
                <table class="schedule-grid-table">
                    <thead>
                        <tr>
                            <th style="width:100px;min-width:100px;">Time</th>
                            <th class="day-mon" style="width:calc((100% - 100px) / 5);min-width:120px;">Monday</th>
                            <th class="day-tue" style="width:calc((100% - 100px) / 5);min-width:120px;">Tuesday</th>
                            <th class="day-wed" style="width:calc((100% - 100px) / 5);min-width:120px;">Wednesday</th>
                            <th class="day-thu" style="width:calc((100% - 100px) / 5);min-width:120px;">Thursday</th>
                            <th class="day-fri" style="width:calc((100% - 100px) / 5);min-width:120px;">Friday</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleGridBody">
                        <!-- Schedule cells will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>


    </div>{{-- /section-schedules --}}
