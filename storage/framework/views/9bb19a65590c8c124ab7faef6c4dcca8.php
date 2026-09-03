<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules Management - ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        :root {
            --blue:#1a3a6c; --blue-light:#2471a3; --blue-pale:#e8f0fb;
            --gold:#f5a623; --red:#e74c3c; --green:#27ae60;
            --bg:#f0f4f8; --border:#e2e8f0; --text:#2d3748; --muted:#718096;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Open Sans',sans-serif; background:var(--bg); color:var(--text); font-size:14px; }

        /* ── Timetable Grid ── */
        .timetable-wrap { overflow-x:auto; }
        .timetable { border-collapse:collapse; min-width:700px; width:100%; font-size:12px; }
        .timetable th { background:var(--blue); color:#fff; padding:10px 8px; text-align:center; font-weight:700; white-space:nowrap; }
        .timetable td.time-col { background:#f8f9fa; color:var(--muted); font-size:11px; font-weight:700; padding:4px 10px; white-space:nowrap; border:1px solid var(--border); vertical-align:top; min-width:72px; }
        .timetable td.day-col { border:1px solid var(--border); vertical-align:top; width:18%; min-width:110px; padding:4px; position:relative; }
        .sched-block {
            border-radius:8px; padding:5px 7px; margin-bottom:4px; font-size:11px;
            cursor:pointer; transition:opacity .15s; border-left:3px solid transparent;
        }
        .sched-block:hover { opacity:.82; }
        .sched-block .sb-subject { font-weight:700; line-height:1.2; margin-bottom:2px; }
        .sched-block .sb-meta { opacity:.8; font-size:10px; }

        /* ── Conflict Banner ── */
        .conflict-box { border-radius:10px; padding:12px 14px; margin-top:12px; font-size:13px; display:none; }
        .conflict-box.has-conflict { background:#fff5f5; border:1.5px solid #fc8181; display:block; }
        .conflict-box.no-conflict  { background:#f0fff4; border:1.5px solid #68d391; display:block; }
        .conflict-item { display:flex; align-items:flex-start; gap:8px; margin-bottom:6px; }
        .conflict-item:last-child { margin-bottom:0; }

        /* ── Teacher Preview ── */
        .teacher-preview { background:#f8faff; border:1.5px solid var(--blue-pale); border-radius:10px; padding:12px; margin-top:8px; display:none; }
        .teacher-preview .tp-row { display:flex; align-items:center; gap:8px; padding:5px 8px; border-radius:6px; margin-bottom:4px; font-size:12px; }
        .teacher-preview .tp-row.tp-conflict { background:#fff5f5; border:1px solid #fc8181; }
        .teacher-preview .tp-row.tp-ok       { background:#f0fff4; }

        /* ── Filter chips ── */
        .filter-chips { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
        .filter-chip { padding:5px 12px; border-radius:20px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
        .filter-chip.active { background:var(--blue); color:#fff; border-color:var(--blue); }

        /* ── View toggle ── */
        .view-toggle { display:flex; gap:4px; }
        .vt-btn { padding:6px 14px; border-radius:8px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
        .vt-btn.active { background:var(--blue); color:#fff; border-color:var(--blue); }

        /* ── Schedule Table ── */
        .sched-table { width:100%; border-collapse:collapse; }
        .sched-table th { background:#f8f9fa; padding:10px 14px; font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid var(--border); text-align:left; }
        .sched-table td { padding:11px 14px; border-bottom:1px solid #f0f0f0; font-size:13px; vertical-align:middle; }
        .sched-table tr:hover td { background:#f8faff; }
        .day-pill { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .pill-Mon,.pill-Fri { background:#e8f0fb; color:#1a3a6c; }
        .pill-Tue,.pill-Thu { background:#f0fdf4; color:#166534; }
        .pill-Wed            { background:#fffbeb; color:#b45309; }
        .pill-Sat            { background:#fdf4ff; color:#7c3aed; }
        .pill-Sun            { background:#fff5f5; color:#c53030; }

        /* ── Misc ── */
        .badge-active   { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-inactive { background:#f3f4f6; color:#6b7280; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
        .action-btn { width:30px; height:30px; border-radius:7px; border:1.5px solid var(--border); background:#fff; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all .15s; }
        .action-btn:hover { background:var(--blue-pale); border-color:var(--blue-light); color:var(--blue); }
        .action-btn.del:hover { background:#fff5f5; border-color:#fc8181; color:var(--red); }

        /* ── Modal ── */
        .modal-content { border:0; border-radius:16px; overflow:hidden; }
        .modal-hdr { background:linear-gradient(135deg,var(--blue),var(--blue-light)); padding:18px 22px; }
        .modal-hdr .modal-title { color:#fff; font-weight:700; font-size:16px; }
        .form-lbl { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; display:block; }
        .form-fld { width:100%; border:1.5px solid var(--border); border-radius:9px; padding:9px 12px; font-size:13px; font-family:'Open Sans',sans-serif; outline:none; transition:border-color .2s; background:#f8faff; }
        .form-fld:focus { border-color:var(--blue-light); background:#fff; }
        select.form-fld { cursor:pointer; }

        .term-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
    </style>
</head>
<body>

<?php echo $__env->make('partials.admin-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="dash-main">

    
    <div class="section-header" style="margin-bottom:20px;">
        <div>
            <h1><i class="bi bi-calendar-week" style="color:var(--gold);margin-right:8px;"></i>Schedule Management</h1>
            <p>Assign teachers to sections with real-time conflict detection</p>
        </div>
        <button class="btn-dash btn-primary" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-2"></i> Add Schedule
        </button>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;" id="statsRow">
        <div class="stat-card"><div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div><div><div class="stat-value" id="statTotal">—</div><div class="stat-label">Total Schedules</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="bi bi-person-check"></i></div><div><div class="stat-value" id="statTeachers">—</div><div class="stat-label">Teachers Assigned</div></div></div>
        <div class="stat-card"><div class="stat-icon gold"><i class="bi bi-building"></i></div><div><div class="stat-value" id="statRooms">—</div><div class="stat-label">Rooms Used</div></div></div>
        <div class="stat-card"><div class="stat-icon red"><i class="bi bi-exclamation-triangle"></i></div><div><div class="stat-value" id="statConflicts">0</div><div class="stat-label">Conflicts</div></div></div>
    </div>

    
    <div class="content-card" style="margin-bottom:16px;">
        <div style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div class="filter-chips">
                <span style="font-size:12px;font-weight:700;color:var(--muted);">Filter by Day:</span>
                <button class="filter-chip active" data-day="all" onclick="setDayFilter('all',this)">All</button>
                <?php $__currentLoopData = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="filter-chip" data-day="<?php echo e($d); ?>" onclick="setDayFilter('<?php echo e($d); ?>',this)"><?php echo e(substr($d,0,3)); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <select class="form-fld" style="width:auto;padding:6px 12px;font-size:12px;" id="filterTeacher" onchange="applyFilters()">
                    <option value="">All Teachers</option>
                </select>
                <select class="form-fld" style="width:auto;padding:6px 12px;font-size:12px;" id="filterSection" onchange="applyFilters()">
                    <option value="">All Sections</option>
                </select>
                <div class="view-toggle">
                    <button class="vt-btn active" id="btnTableView" onclick="setView('table')"><i class="bi bi-list-ul"></i> List</button>
                    <button class="vt-btn" id="btnGridView"  onclick="setView('grid')"><i class="bi bi-grid-3x3-gap"></i> Grid</button>
                </div>
            </div>
        </div>
    </div>

    
    <div id="tableView" class="content-card">
        <div style="overflow-x:auto;">
            <table class="sched-table">
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Term</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="schedTableBody">
                    <tr><td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                        <i class="bi bi-hourglass-split" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                        Loading schedules…
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="gridView" style="display:none;" class="content-card">
        <div style="padding:14px 18px 4px;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:14px;font-weight:700;color:var(--blue);">
                <i class="bi bi-grid-3x3-gap me-2"></i> Weekly Timetable
            </div>
            <select class="form-fld" style="width:auto;padding:6px 12px;font-size:12px;" id="gridFilterSection" onchange="renderGrid()">
                <option value="">All Sections</option>
            </select>
        </div>
        <div class="timetable-wrap" style="padding:0 18px 18px;">
            <table class="timetable" id="timetableEl">
                <thead>
                    <tr>
                        <th style="width:72px;">Time</th>
                        <th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th>
                    </tr>
                </thead>
                <tbody id="timetableBody"></tbody>
            </table>
        </div>
    </div>

</div>


<div class="modal fade" id="scheduleModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-hdr d-flex justify-content-between align-items-center">
                <div>
                    <div class="modal-title" id="modalTitle">Add New Schedule</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:2px;">Conflicts are checked in real-time as you fill the form</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:22px;">

                
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-lbl">Section *</label>
                        <select id="f_section" class="form-fld" onchange="onFormChange()">
                            <option value="">Select section…</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-lbl">Subject *</label>
                        <select id="f_subject" class="form-fld" onchange="onFormChange()">
                            <option value="">Select subject…</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-lbl">Term *</label>
                        <select id="f_term" class="form-fld" onchange="onFormChange()">
                            <option value="">Select term…</option>
                            <option value="1">Term 1</option>
                            <option value="2">Term 2</option>
                            <option value="3">Term 3</option>
                        </select>
                    </div>
                </div>

                
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-lbl">Day *</label>
                        <select id="f_day" class="form-fld" onchange="onFormChange()">
                            <option value="">Select day…</option>
                            <?php $__currentLoopData = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d); ?>"><?php echo e($d); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-lbl">Start Time *</label>
                        <input type="time" id="f_start" class="form-fld" onchange="onFormChange()">
                    </div>
                    <div>
                        <label class="form-lbl">End Time *</label>
                        <input type="time" id="f_end" class="form-fld" onchange="onFormChange()">
                    </div>
                    <div>
                        <label class="form-lbl">Room</label>
                        <input type="text" id="f_room" class="form-fld" placeholder="e.g. Room 101" oninput="onFormChange()">
                    </div>
                </div>

                
                <div style="margin-bottom:4px;">
                    <label class="form-lbl">Teacher *</label>
                    <select id="f_teacher" class="form-fld" onchange="onFormChange(); renderTeacherPreview();">
                        <option value="">Select teacher…</option>
                    </select>
                </div>

                
                <div id="teacherPreview" class="teacher-preview">
                    <div style="font-size:11px;font-weight:700;color:var(--blue);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">
                        <i class="bi bi-calendar-day me-1"></i> <span id="tpLabel">Teacher's schedule on selected day</span>
                    </div>
                    <div id="tpRows"></div>
                </div>

                
                <div id="conflictBox" class="conflict-box" style="display:none;"></div>

                
                <div style="margin-top:14px;display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" id="f_active" class="form-check-input" checked style="width:18px;height:18px;cursor:pointer;">
                    <label for="f_active" style="font-size:13px;font-weight:600;cursor:pointer;">Active</label>
                </div>

            </div>
            <div style="padding:14px 22px;background:#f8faff;border-top:1.5px solid var(--border);display:flex;justify-content:space-between;align-items:center;gap:10px;">
                <div id="saveConflictNote" style="font-size:12px;color:#e53e3e;display:none;">
                    <i class="bi bi-exclamation-triangle me-1"></i> Conflicts detected — the server will reject this save.
                </div>
                <div style="display:flex;gap:10px;margin-left:auto;">
                    <button type="button" data-bs-dismiss="modal" style="padding:9px 18px;border-radius:9px;border:1.5px solid var(--border);background:#fff;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                    <button type="button" id="saveBtn" onclick="saveSchedule()" style="padding:9px 20px;border-radius:9px;border:none;background:var(--blue);color:#fff;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;">
                        <i class="bi bi-check-circle-fill"></i> Save Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <div style="background:#fff5f5;padding:20px 22px;border-bottom:1px solid #fed7d7;">
                <div style="font-size:15px;font-weight:700;color:#c53030;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-trash3-fill"></i> Delete Schedule
                </div>
            </div>
            <div style="padding:18px 22px;font-size:14px;color:var(--text);">
                Are you sure you want to delete <strong id="deleteSchedName"></strong>? This cannot be undone.
            </div>
            <div style="padding:12px 22px;background:#fafafa;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;">
                <button data-bs-dismiss="modal" style="padding:8px 16px;border-radius:8px;border:1.5px solid var(--border);background:#fff;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                <button onclick="confirmDelete()" style="padding:8px 18px;border-radius:8px;border:none;background:var(--red);color:#fff;font-size:13px;font-weight:700;cursor:pointer;">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.admin-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ══════════════════════════════════════════════
   DATA & STATE
══════════════════════════════════════════════ */
let _schedules = [];   // all schedules from server
let _sections  = [];
let _subjects  = [];
let _teachers  = [];
let _editId    = null;
let _deleteId  = null;
let _dayFilter = 'all';
let _currentView = 'table';

// Colour palette for timetable blocks
const COLORS = [
    ['#e8f0fb','#1a3a6c'], ['#f0fdf4','#166534'], ['#fffbeb','#92400e'],
    ['#fdf4ff','#6b21a8'], ['#fff5f5','#c53030'], ['#e0f2fe','#075985'],
    ['#fff7ed','#9a3412'], ['#f0fdf4','#14532d'], ['#fce7f3','#9d174d'],
];
const sectionColors = {};
let colorIdx = 0;
function getSectionColor(sectionId) {
    if (!sectionColors[sectionId]) {
        sectionColors[sectionId] = COLORS[colorIdx % COLORS.length];
        colorIdx++;
    }
    return sectionColors[sectionId];
}

/* ══════════════════════════════════════════════
   INIT
══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    loadDropdowns();
    loadSchedules();
});

async function loadDropdowns() {
    // Load sections
    try {
        const r = await fetch('/admin/sections', { headers:{ Accept:'application/json' } });
        const d = await r.json();
        _sections = Array.isArray(d) ? d : (d.data ?? []);
    } catch(e) { _sections = []; }

    // Load subjects
    try {
        const r = await fetch('/admin/subjects', { headers:{ Accept:'application/json' } });
        const d = await r.json();
        _subjects = Array.isArray(d) ? d : (d.data ?? []);
    } catch(e) { _subjects = []; }

    // Load teachers (users with role=teacher)
    try {
        const r = await fetch('/admin/teachers', { headers:{ Accept:'application/json' } });
        const d = await r.json();
        _teachers = Array.isArray(d) ? d : (d.data ?? []);
    } catch(e) { _teachers = []; }

    populateFormDropdowns();
    populateFilterDropdowns();
}

function populateFormDropdowns() {
    const sOpts = '<option value="">Select section…</option>' + _sections.map(s=>`<option value="${s.id}">${s.name} (${s.grade_level??''})</option>`).join('');
    const subOpts = '<option value="">Select subject…</option>' + _subjects.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    const tOpts = '<option value="">Select teacher…</option>' + _teachers.map(t=>`<option value="${t.id}">${t.name}</option>`).join('');
    document.getElementById('f_section').innerHTML = sOpts;
    document.getElementById('f_subject').innerHTML  = subOpts;
    document.getElementById('f_teacher').innerHTML  = tOpts;
}

function populateFilterDropdowns() {
    const tFilter = document.getElementById('filterTeacher');
    const sFilter = document.getElementById('filterSection');
    const gFilter = document.getElementById('gridFilterSection');

    tFilter.innerHTML = '<option value="">All Teachers</option>' + _teachers.map(t=>`<option value="${t.id}">${t.name}</option>`).join('');
    const sectionOpts = '<option value="">All Sections</option>' + _sections.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
    sFilter.innerHTML = sectionOpts;
    gFilter.innerHTML = sectionOpts;
}

async function loadSchedules() {
    try {
        const r = await fetch('/admin/schedules', { headers:{ Accept:'application/json' } });
        const d = await r.json();
        _schedules = Array.isArray(d) ? d : (d.schedules ?? d.data ?? []);
    } catch(e) { _schedules = []; }

    updateStats();
    applyFilters();
    renderGrid();
}

/* ══════════════════════════════════════════════
   STATS
══════════════════════════════════════════════ */
function updateStats() {
    const active = _schedules.filter(s=>s.is_active);
    document.getElementById('statTotal').textContent    = active.length;
    document.getElementById('statTeachers').textContent = [...new Set(active.filter(s=>s.teacher_id).map(s=>s.teacher_id))].length;
    document.getElementById('statRooms').textContent    = [...new Set(active.filter(s=>s.room).map(s=>s.room))].length;
}

/* ══════════════════════════════════════════════
   FILTERS & TABLE VIEW
══════════════════════════════════════════════ */
let _activeDayFilter = 'all';
function setDayFilter(day, btn) {
    _activeDayFilter = day;
    document.querySelectorAll('.filter-chip').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
}

function applyFilters() {
    const teacherF  = document.getElementById('filterTeacher').value;
    const sectionF  = document.getElementById('filterSection').value;

    let filtered = _schedules.filter(s => {
        if (_activeDayFilter !== 'all' && s.day_of_week !== _activeDayFilter) return false;
        if (teacherF && String(s.teacher_id) !== teacherF) return false;
        if (sectionF && String(s.section_id) !== sectionF) return false;
        return true;
    });
    renderTable(filtered);
}

function renderTable(rows) {
    const tbody = document.getElementById('schedTableBody');
    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
            <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3;"></i>
            No schedules match the current filter.
        </td></tr>`;
        return;
    }

    const dayClass = d => `pill-${d.substring(0,3)}`;

    tbody.innerHTML = rows.map(s => {
        const section = s.section ?? {};
        const subject = s.subject ?? {};
        const teacher = s.teacher ?? {};
        const start   = fmtTime(s.start_time);
        const end     = fmtTime(s.end_time);
        const termLabel = s.term ? `Term ${s.term}` : '—';
        return `
        <tr>
            <td><span style="font-weight:700;">${section.name??'—'}</span><br>
                <span style="font-size:11px;color:var(--muted);">${section.grade_level??''}</span></td>
            <td>${subject.name??'—'}</td>
            <td>
                ${teacher.name
                  ? `<span style="display:flex;align-items:center;gap:6px;"><span style="width:26px;height:26px;border-radius:50%;background:var(--blue);color:#fff;font-size:10px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;">${teacher.name.charAt(0).toUpperCase()}</span>${teacher.name}</span>`
                  : '<span style="color:var(--muted);font-size:12px;"><i class="bi bi-dash"></i> Unassigned</span>'}
            </td>
            <td><span class="day-pill ${dayClass(s.day_of_week)}">${s.day_of_week}</span></td>
            <td style="white-space:nowrap;font-weight:600;">${start} – ${end}</td>
            <td>${s.room ? `<span style="font-size:12px;background:#f1f5f9;padding:2px 8px;border-radius:6px;">${s.room}</span>` : '<span style="color:var(--muted);">—</span>'}</td>
            <td><span style="font-size:12px;background:#eff6ff;color:#1d4ed8;padding:3px 9px;border-radius:12px;font-weight:700;">${termLabel}</span></td>
            <td>${s.is_active
                  ? '<span class="badge-active">Active</span>'
                  : '<span class="badge-inactive">Inactive</span>'}</td>
            <td style="white-space:nowrap;">
                <button class="action-btn" onclick="openEditModal(${s.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="action-btn del" onclick="openDeleteModal(${s.id},'${(subject.name??'').replace(/'/g,"\\'")} – ${section.name??''}')" title="Delete" style="margin-left:4px;"><i class="bi bi-trash3"></i></button>
            </td>
        </tr>`;
    }).join('');
}

/* ══════════════════════════════════════════════
   TIMETABLE GRID VIEW
══════════════════════════════════════════════ */
const DAYS_GRID  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
const TIME_SLOTS = [];
for (let h=7; h<=19; h++) { TIME_SLOTS.push(`${String(h).padStart(2,'0')}:00`); }

function renderGrid() {
    const sectionF  = document.getElementById('gridFilterSection').value;
    const filtered  = sectionF
        ? _schedules.filter(s=>s.is_active && String(s.section_id)===sectionF)
        : _schedules.filter(s=>s.is_active);

    const tbody = document.getElementById('timetableBody');
    tbody.innerHTML = TIME_SLOTS.map(slot => {
        const slotH = parseInt(slot);
        const cells = DAYS_GRID.map(day => {
            const blocks = filtered.filter(s => {
                const sh = parseInt(s.start_time);
                const eh = parseInt(s.end_time);
                return s.day_of_week === day && sh <= slotH && eh > slotH;
            });
            if (!blocks.length) return `<td class="day-col"></td>`;
            const blockHtml = blocks.map(b => {
                const [bg,fg] = getSectionColor(b.section_id);
                const sName = b.section?.name ?? '—';
                const subName = b.subject?.name ?? '—';
                const tName = b.teacher?.name ?? 'Unassigned';
                return `<div class="sched-block" style="background:${bg};color:${fg};border-left-color:${fg};"
                            onclick="openEditModal(${b.id})" title="Click to edit">
                    <div class="sb-subject">${subName}</div>
                    <div class="sb-meta">${sName} · ${tName}</div>
                    <div class="sb-meta">${fmtTime(b.start_time)}–${fmtTime(b.end_time)}${b.room?' · '+b.room:''}</div>
                </div>`;
            }).join('');
            return `<td class="day-col">${blockHtml}</td>`;
        }).join('');
        return `<tr><td class="time-col">${slot}</td>${cells}</tr>`;
    }).join('');
}

/* ══════════════════════════════════════════════
   VIEW TOGGLE
══════════════════════════════════════════════ */
function setView(v) {
    _currentView = v;
    document.getElementById('tableView').style.display = v==='table' ? '' : 'none';
    document.getElementById('gridView').style.display  = v==='grid'  ? '' : 'none';
    document.getElementById('btnTableView').classList.toggle('active', v==='table');
    document.getElementById('btnGridView').classList.toggle('active',  v==='grid');
    if (v==='grid') renderGrid();
}

/* ══════════════════════════════════════════════
   MODAL — OPEN ADD / EDIT
══════════════════════════════════════════════ */
function openAddModal() {
    _editId = null;
    document.getElementById('modalTitle').textContent = 'Add New Schedule';
    document.getElementById('f_section').value = '';
    document.getElementById('f_subject').value = '';
    document.getElementById('f_term').value    = '';
    document.getElementById('f_day').value     = '';
    document.getElementById('f_start').value   = '';
    document.getElementById('f_end').value     = '';
    document.getElementById('f_room').value    = '';
    document.getElementById('f_teacher').value = '';
    document.getElementById('f_active').checked = true;
    hideConflictBox();
    hideTeacherPreview();
    new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

async function openEditModal(id) {
    _editId = id;
    document.getElementById('modalTitle').textContent = 'Edit Schedule';
    hideConflictBox();
    hideTeacherPreview();

    try {
        const r = await fetch(`/admin/schedules/${id}`, { headers:{ Accept:'application/json' } });
        const s = await r.json();
        document.getElementById('f_section').value  = s.section_id ?? '';
        document.getElementById('f_subject').value  = s.subject_id ?? '';
        document.getElementById('f_term').value     = s.term ?? '';
        document.getElementById('f_day').value      = s.day_of_week ?? '';
        document.getElementById('f_start').value    = s.start_time ?? '';
        document.getElementById('f_end').value      = s.end_time ?? '';
        document.getElementById('f_room').value     = s.room ?? '';
        document.getElementById('f_teacher').value  = s.teacher_id ?? '';
        document.getElementById('f_active').checked = !!s.is_active;
        onFormChange();
        renderTeacherPreview();
    } catch(e) {
        alert('Failed to load schedule data.');
        return;
    }
    new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

/* ══════════════════════════════════════════════
   REAL-TIME CONFLICT CHECK
══════════════════════════════════════════════ */
function onFormChange() {
    const day   = document.getElementById('f_day').value;
    const start = document.getElementById('f_start').value;
    const end   = document.getElementById('f_end').value;
    const tid   = document.getElementById('f_teacher').value;
    const sid   = document.getElementById('f_section').value;
    const room  = document.getElementById('f_room').value.trim();

    if (!day || !start || !end || start >= end) {
        hideConflictBox();
        return;
    }

    const conflicts = detectConflictsLocal(day, start, end, tid, sid, room);

    const box  = document.getElementById('conflictBox');
    const note = document.getElementById('saveConflictNote');

    if (!conflicts.length) {
        if (tid || sid) {
            box.className  = 'conflict-box no-conflict';
            box.innerHTML  = `<i class="bi bi-check-circle-fill me-2" style="color:#38a169;"></i><strong style="color:#276749;">No conflicts detected</strong> — this time slot is free.`;
            box.style.display = 'block';
        } else {
            hideConflictBox();
        }
        note.style.display = 'none';
    } else {
        box.className  = 'conflict-box has-conflict';
        box.innerHTML  = `<div style="font-weight:700;color:#c53030;margin-bottom:8px;"><i class="bi bi-exclamation-triangle-fill me-2"></i>${conflicts.length} conflict(s) detected</div>`
            + conflicts.map(c=>`<div class="conflict-item"><i class="bi bi-${c.icon}" style="color:#e53e3e;margin-top:2px;flex-shrink:0;"></i><span><strong>${c.type}:</strong> ${c.message}</span></div>`).join('');
        box.style.display = 'block';
        note.style.display = 'block';
    }
}

function detectConflictsLocal(day, start, end, teacherId, sectionId, room) {
    const conflicts = [];
    const active = _schedules.filter(s => s.is_active && s.day_of_week === day && s.id !== _editId);

    for (const s of active) {
        const overlaps = s.start_time < end && s.end_time > start;
        if (!overlaps) continue;

        const secName = s.section?.name ?? '?';
        const subName = s.subject?.name ?? '?';
        const tName   = s.teacher?.name ?? '?';
        const timeStr = `${fmtTime(s.start_time)}–${fmtTime(s.end_time)}`;

        // Teacher conflict (same teacher, different section)
        if (teacherId && String(s.teacher_id) === String(teacherId) && String(s.section_id) !== String(sectionId)) {
            conflicts.push({ icon:'person-fill-exclamation', type:'Teacher', message:`<em>${tName}</em> is already teaching <em>${subName}</em> for <em>${secName}</em> at ${timeStr} on ${day}.` });
        }
        // Section conflict (same section, overlapping time)
        if (sectionId && String(s.section_id) === String(sectionId)) {
            conflicts.push({ icon:'collection', type:'Section', message:`This section already has <em>${subName}</em> at ${timeStr} on ${day}.` });
        }
        // Room conflict
        if (room && s.room && s.room.toLowerCase() === room.toLowerCase()) {
            conflicts.push({ icon:'building-exclamation', type:'Room', message:`Room <em>${room}</em> is occupied by <em>${secName} – ${subName}</em> at ${timeStr} on ${day}.` });
        }
    }
    return conflicts;
}

/* ══════════════════════════════════════════════
   TEACHER SCHEDULE PREVIEW
══════════════════════════════════════════════ */
function renderTeacherPreview() {
    const tid = document.getElementById('f_teacher').value;
    const day = document.getElementById('f_day').value;
    const start = document.getElementById('f_start').value;
    const end   = document.getElementById('f_end').value;
    const preview = document.getElementById('teacherPreview');
    const rows    = document.getElementById('tpRows');
    const label   = document.getElementById('tpLabel');

    if (!tid || !day) { hideTeacherPreview(); return; }

    const tName = _teachers.find(t=>String(t.id)===String(tid))?.name ?? 'Teacher';
    label.textContent = `${tName}'s schedule on ${day}`;

    const existing = _schedules.filter(s =>
        s.is_active && String(s.teacher_id) === String(tid) && s.day_of_week === day && s.id !== _editId
    ).sort((a,b)=>a.start_time.localeCompare(b.start_time));

    if (!existing.length) {
        rows.innerHTML = `<div style="font-size:12px;color:var(--muted);"><i class="bi bi-check2 me-1"></i>No classes assigned on ${day} — fully available.</div>`;
        preview.style.display = 'block';
        return;
    }

    rows.innerHTML = existing.map(s => {
        const overlaps = start && end && s.start_time < end && s.end_time > start;
        const cls  = overlaps ? 'tp-conflict' : 'tp-ok';
        const icon = overlaps ? '<i class="bi bi-exclamation-circle-fill" style="color:#e53e3e;"></i>' : '<i class="bi bi-check-circle-fill" style="color:#38a169;"></i>';
        return `<div class="tp-row ${cls}">
            ${icon}
            <span style="font-weight:700;">${fmtTime(s.start_time)}–${fmtTime(s.end_time)}</span>
            <span>${s.subject?.name??'—'} (${s.section?.name??'—'})</span>
            ${s.room ? `<span style="color:var(--muted);font-size:11px;">${s.room}</span>` : ''}
            ${overlaps ? '<span style="font-size:11px;color:#c53030;font-weight:700;margin-left:auto;">⚡ Conflict</span>' : ''}
        </div>`;
    }).join('');

    preview.style.display = 'block';
}

function hideTeacherPreview() { document.getElementById('teacherPreview').style.display = 'none'; }
function hideConflictBox()    {
    document.getElementById('conflictBox').style.display = 'none';
    document.getElementById('saveConflictNote').style.display = 'none';
}

/* ══════════════════════════════════════════════
   SAVE SCHEDULE
══════════════════════════════════════════════ */
async function saveSchedule() {
    const btn = document.getElementById('saveBtn');
    const section  = document.getElementById('f_section').value;
    const subject  = document.getElementById('f_subject').value;
    const term     = document.getElementById('f_term').value;
    const day      = document.getElementById('f_day').value;
    const start    = document.getElementById('f_start').value;
    const end      = document.getElementById('f_end').value;
    const teacher  = document.getElementById('f_teacher').value;
    const room     = document.getElementById('f_room').value;
    const isActive = document.getElementById('f_active').checked;

    if (!section || !subject || !term || !day || !start || !end) {
        showInlineError('Please fill in all required fields (Section, Subject, Term, Day, Start Time, End Time).');
        return;
    }
    if (start >= end) {
        showInlineError('End time must be after start time.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving…';

    const body = { section_id:section, subject_id:subject, term, day_of_week:day, start_time:start, end_time:end, room, is_active:isActive?1:0 };
    if (teacher) body.teacher_id = teacher;

    const url    = _editId ? `/admin/schedules/${_editId}` : '/admin/schedules';
    const method = _editId ? 'PUT' : 'POST';

    try {
        const r = await fetch(url, {
            method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content, Accept:'application/json' },
            body: JSON.stringify(body)
        });
        const data = await r.json();

        if (r.ok && (data.success || data.id)) {
            bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
            await loadSchedules();
        } else if (data.conflicts && data.conflicts.length) {
            showInlineError(data.conflicts.join('\n'));
        } else if (data.errors) {
            showInlineError(Object.values(data.errors).flat().join('\n'));
        } else {
            showInlineError(data.message || 'An error occurred.');
        }
    } catch(e) {
        showInlineError('Network error — please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Save Schedule';
    }
}

function showInlineError(msg) {
    const box = document.getElementById('conflictBox');
    box.className = 'conflict-box has-conflict';
    box.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2" style="color:#c53030;"></i><strong>Cannot save:</strong> ${msg.replace(/\n/g,'<br>')}`;
    box.style.display = 'block';
}

/* ══════════════════════════════════════════════
   DELETE
══════════════════════════════════════════════ */
function openDeleteModal(id, name) {
    _deleteId = id;
    document.getElementById('deleteSchedName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

async function confirmDelete() {
    if (!_deleteId) return;
    try {
        const r = await fetch(`/admin/schedules/${_deleteId}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content, Accept:'application/json' }
        });
        const d = await r.json();
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        if (d.success) await loadSchedules();
        else showInlineError(d.message || 'Delete failed.');
    } catch(e) { alert('Network error.'); }
    _deleteId = null;
}

/* ══════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════ */
function fmtTime(t) {
    if (!t) return '—';
    const [hh, mm] = t.split(':');
    const h = parseInt(hh);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${mm} ${ampm}`;
}

function refreshSchedules() { loadSchedules(); }
</script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\admin\schedules\index.blade.php ENDPATH**/ ?>