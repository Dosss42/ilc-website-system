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

        {{-- Schedule Filters — slim inline toolbar, no boxed card --}}
        <div class="sched-toolbar">
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
            <select class="form-fld" id="scheduleSectionFilter" onchange="loadScheduleGrid()">
                <option value="">All Sections</option>
            </select>
            <select class="form-fld" id="scheduleTermFilter" onchange="loadScheduleGrid()">
                <option value="1">1st Term</option>
                <option value="2">2nd Term</option>
                <option value="3">3rd Term</option>
            </select>
            <button class="btn-dash btn-primary" onclick="loadScheduleGrid()">
                <i class="bi bi-search"></i> Load
            </button>
        </div>

        {{-- Empty State --}}
        <div id="scheduleEmptyState" style="text-align:center; padding:60px; color:var(--muted);">
            <i class="bi bi-calendar-week" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.3;"></i>
            <div style="font-size:15px; font-weight:600; margin-bottom:4px;">Select a grade level to view schedules</div>
            <div style="font-size:13px;">Choose a grade level above to load the schedule grid.</div>
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
