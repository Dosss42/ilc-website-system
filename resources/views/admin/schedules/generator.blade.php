{{--
    Schedule Management › Subject Teachers and › Generate Schedule — shared by the Admin and Super Admin
    dashboards (both include this file). Rules: App\Services\ScheduleGenerator. Self-contained: own helpers,
    CSRF from the page's meta tag, messages shown inside the windows. Existing classes only.
--}}
@php
    // Schedules are made for the current school year (Settings) or the next one being prepared
    $sgenCurrentSy = \App\Models\Setting::getCurrentSchoolYear();
    $sgenNextSy = ((int) substr($sgenCurrentSy, 0, 4) + 1) . '-' . ((int) substr($sgenCurrentSy, 0, 4) + 2);
    $sgenYears = [$sgenCurrentSy => $sgenCurrentSy . ' (current)', $sgenNextSy => $sgenNextSy . ' (next)'];
@endphp
{{--
--}}

{{-- ── Subject Teachers ── --}}
<div class="modal fade" id="sgenTeachersModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;">
                <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-person-video3 me-2"></i>Subject Teachers</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;background:#f8fafc;">
                {{-- Pickers + summary --}}
                <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:14px;">
                    <div><label style="font-size:12px;font-weight:600;">School Year</label>
                        <select class="form-fld" id="sgenTSy" onchange="sgenLoadTeachers()">
                            @foreach($sgenYears as $sy => $label)<option value="{{ $sy }}">{{ $label }}</option>@endforeach
                        </select></div>
                    <div><label style="font-size:12px;font-weight:600;">Grade</label>
                        <select class="form-fld" id="sgenTGrade" onchange="sgenLoadTeachers()"></select></div>
                    <div id="sgenTSummary" style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;"></div>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Choose who teaches each subject in each section. The badge under a choice is that teacher's current teaching load;
                    over {{ \App\Services\ScheduleGenerator::DEFAULTS['limits']['week_hours'] }}h a week shows as <span style="color:#b91c1c;font-weight:600;">overload</span> (allowed, but check it).
                    Changing a teacher also moves that subject's existing timetable slots to the new teacher.
                </div>
                <div id="sgenTMsg"></div>

                <div style="background:#fff;border:1px solid var(--border,#e2e8f0);border-radius:12px;overflow-x:auto;">
                    <table class="dash-table" id="sgenTTable" style="margin:0;"></table>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px;">
                    <button type="button" class="btn-dash btn-secondary" id="sgenTMore" style="display:none;padding:5px 12px;font-size:12px;" onclick="sgenToggleMore()"></button>
                    <button type="button" class="btn-dash btn-secondary" id="sgenLastYearBtn" style="display:none;padding:5px 12px;font-size:12px;" onclick="sgenUseLastYear()">
                        <i class="bi bi-clock-history"></i> Fill empty cells with last year's teachers</button>
                </div>

                {{-- Teachers used in this grade --}}
                <div id="sgenTLoad" style="margin-top:16px;"></div>
            </div>
            <div class="modal-footer" style="justify-content:space-between;">
                <span id="sgenTDirty" style="font-size:12px;color:var(--muted);"></span>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- One button: "Save Teachers (n)" while there are changes, then "Next: Generate Schedule" --}}
                    <button type="button" class="btn-dash btn-primary" id="sgenTSaveBtn" onclick="sgenSaveTeachers()"><i class="bi bi-check-lg"></i> Save Teachers</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Generate Schedule ── --}}
<div class="modal fade" id="sgenGenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;">
                <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-magic me-2"></i>Generate Schedule <span id="sgenStepLabel" style="font-weight:400;opacity:.8;font-size:13px;"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <div id="sgenGMsg"></div>

                {{-- Step 1: settings — cards on the left, the subject list on the right, a live summary below --}}
                <div id="sgenStep1">
                    <style>
                        #sgenGenModal .sgen-card { background:#fff; border:1px solid var(--border,#e2e8f0); border-radius:12px; padding:14px 16px; margin-bottom:12px; }
                        #sgenGenModal .sgen-card-title { font-size:12px; font-weight:700; color:#1a3a6c; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
                        #sgenGenModal .sgen-lbl { font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; display:block; }
                        #sgenGenModal .sgen-pill { display:inline-flex; align-items:center; padding:5px 12px; border-radius:20px; border:1.5px solid #dbe3ef; background:#fff; font-size:12.5px; font-weight:600; color:#475569; cursor:pointer; user-select:none; margin:0 6px 6px 0; }
                        #sgenGenModal .sgen-pill.on { background:#1a3a6c; border-color:#1a3a6c; color:#fff; }
                        #sgenGenModal .sgen-pill input { display:none; }
                        #sgenGenModal .sgen-subject { display:flex; align-items:center; gap:10px; padding:7px 4px; border-bottom:1px solid #f1f5f9; }
                        #sgenGenModal .sgen-subject.off { opacity:.55; }
                        #sgenGenModal .sgen-mini { width:26px; height:26px; border:1px solid #e2e8f0; border-radius:6px; background:#fff; color:#475569; display:inline-flex; align-items:center; justify-content:center; padding:0; font-size:12px; }
                        #sgenGenModal .sgen-mini:hover { background:#f1f5f9; }
                        #sgenGenModal .sgen-perweek { width:44px; text-align:center; padding:4px; }
                    </style>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="sgen-card">
                                <div class="sgen-card-title"><i class="bi bi-mortarboard"></i> Class &amp; days</div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="sgen-lbl">School Year</label>
                                        <select class="form-fld" id="sgenSy" onchange="sgenLoadDefaults()">
                                            @foreach($sgenYears as $sy => $label)<option value="{{ $sy }}">{{ $label }}</option>@endforeach
                                        </select></div>
                                    <div class="col-6"><label class="sgen-lbl">Grade</label>
                                        <select class="form-fld" id="sgenGrade" onchange="sgenLoadDefaults()"></select></div>
                                </div>
                                <label class="sgen-lbl">Sections <span style="font-weight:400;color:var(--muted);">— which ones to build</span></label>
                                <div id="sgenSections"></div>
                                <label class="sgen-lbl" style="margin-top:4px;">Terms</label>
                                <div>@foreach([1,2,3] as $t)<label class="sgen-pill {{ $t === 1 ? 'on' : '' }}"><input type="checkbox" class="sgen-term" value="{{ $t }}" {{ $t === 1 ? 'checked' : '' }}>Term {{ $t }}</label>@endforeach</div>
                                <label class="sgen-lbl" style="margin-top:4px;">Days</label>
                                <div>@foreach(\App\Services\ScheduleGenerator::DAYS as $d)<label class="sgen-pill {{ $d !== 'Saturday' ? 'on' : '' }}"><input type="checkbox" class="sgen-day" value="{{ $d }}" {{ $d !== 'Saturday' ? 'checked' : '' }}>{{ substr($d, 0, 3) }}</label>@endforeach</div>
                            </div>

                            <div class="sgen-card">
                                <div class="sgen-card-title"><i class="bi bi-clock"></i> School day</div>
                                <div class="row g-2 mb-2">
                                    <div class="col-4"><label class="sgen-lbl">Classes start</label><input type="time" class="form-fld" id="sgenStart" value="07:00"></div>
                                    <div class="col-4"><label class="sgen-lbl">Dismissal</label><input type="time" class="form-fld" id="sgenEnd" value="14:00"></div>
                                    <div class="col-4"><label class="sgen-lbl">Minutes / period</label><input type="number" class="form-fld" id="sgenMinutes" value="60" min="20" max="120"></div>
                                </div>
                                <label class="sgen-lbl">Breaks (no classes)</label>
                                <div id="sgenBreaks"></div>
                                <button type="button" class="btn-dash btn-secondary" style="padding:3px 10px;font-size:12px;" onclick="sgenAddBreak();sgenSummary();"><i class="bi bi-plus"></i> Add break</button>
                            </div>

                            <div class="sgen-card">
                                <div class="sgen-card-title"><i class="bi bi-person-badge"></i> Teacher limits <span style="font-weight:400;color:var(--muted);">— overload is flagged, not blocked</span></div>
                                <div class="row g-2">
                                    <div class="col-4"><label class="sgen-lbl">Hours / week</label><input type="number" class="form-fld" id="sgenLimWeek" value="30" min="1" max="60"></div>
                                    <div class="col-4"><label class="sgen-lbl">Periods / day</label><input type="number" class="form-fld" id="sgenLimDay" value="6" min="1" max="12"></div>
                                    <div class="col-4"><label class="sgen-lbl">In a row</label><input type="number" class="form-fld" id="sgenLimRow" value="4" min="1" max="12"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="sgen-card" style="height:calc(100% - 12px);">
                                <div class="sgen-card-title" style="justify-content:space-between;">
                                    <span><i class="bi bi-journal-bookmark"></i> Subjects <span style="font-weight:400;color:var(--muted);">— order &amp; times a week</span></span>
                                    <a href="#" style="font-size:12px;font-weight:600;" onclick="bootstrap.Modal.getInstance(document.getElementById('sgenGenModal')).hide();openSubjectTeachersModal();return false;"><i class="bi bi-person-video3"></i> Assign teachers</a>
                                </div>
                                <div id="sgenSubjects"></div>
                                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:8px;">
                                    <span id="sgenSubjectsTotal" style="font-size:12px;color:var(--muted);"></span>
                                    <a href="#" style="font-size:12px;font-weight:600;" onclick="sgenSpread();return false;" title="Share the day's periods evenly among the subjects"><i class="bi bi-distribute-horizontal"></i> Spread evenly</a>
                                </div>
                                <div id="sgenOtherSubjects" style="font-size:11.5px;color:var(--muted);margin-top:6px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Live summary: how long the day gets, and what happens to each section --}}
                    <div id="sgenSummaryBox" style="position:sticky;bottom:-20px;background:#eef4ff;border:1px solid #c7d7f2;border-radius:12px;padding:12px 16px;margin-top:4px;font-size:13px;color:#1a3a6c;">
                        <div id="sgenSummaryLine" style="font-weight:600;"></div>
                        <div id="sgenSummarySections" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                        <label style="margin-top:8px;display:block;font-size:12.5px;"><input type="checkbox" id="sgenReplace" onchange="sgenSummary()"> <strong>Replace existing</strong> — rebuild sections that already have a schedule for the chosen terms (otherwise they are skipped and left as they are)</label>
                    </div>
                </div>

                {{-- Step 2: preview --}}
                <div id="sgenStep2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-dash btn-secondary" id="sgenBackBtn" style="display:none;" onclick="sgenShowStep(1)"><i class="bi bi-arrow-left"></i> Back</button>
                <button type="button" class="btn-dash btn-primary" id="sgenPreviewBtn" onclick="sgenPreview()"><i class="bi bi-eye"></i> Preview</button>
                <button type="button" class="btn-dash btn-primary" id="sgenSaveBtn" style="display:none;" onclick="sgenSave()"><i class="bi bi-check-lg"></i> Save Schedule</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const GRADES = { nursery: 'Nursery', kindergarten: 'Kindergarten', grade1: 'Grade 1', grade2: 'Grade 2', grade3: 'Grade 3', grade4: 'Grade 4', grade5: 'Grade 5', grade6: 'Grade 6' };
    const EP = {
        grid: '{{ route('admin.schedules.subject-teachers') }}', gridSave: '{{ route('admin.schedules.subject-teachers.save') }}',
        defaults: '{{ route('admin.schedules.generator.defaults') }}', preview: '{{ route('admin.schedules.generator.preview') }}', save: '{{ route('admin.schedules.generator.save') }}',
    };
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const $ = id => document.getElementById(id);
    const token = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const api = async (url, method, body) => {
        const r = await fetch(url, { method: method || 'GET', credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token() }, body: body ? JSON.stringify(body) : undefined });
        const d = await r.json().catch(() => ({}));
        if (!r.ok) throw new Error(d.message || (d.errors ? Object.values(d.errors).flat().join(' ') : 'Request failed.'));
        return d;
    };
    const msg = (id, type, text) => {
        const c = { success: ['#f0fdf4', '#86efac', '#166534'], error: ['#fef2f2', '#fecaca', '#b91c1c'], info: ['#eff6ff', '#bfdbfe', '#1e40af'] }[type];
        $(id).innerHTML = text ? '<div style="padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;background:' + c[0] + ';border:1px solid ' + c[1] + ';color:' + c[2] + ';">' + text + '</div>' : '';
    };
    const fillGrades = sel => { sel.innerHTML = Object.entries(GRADES).map(([k, v]) => '<option value="' + k + '">' + v + '</option>').join(''); };
    const pageValue = (id, fallback) => { const el = $(id); return el && el.value ? el.value : fallback; };
    // The page's year when it is the current or next one, otherwise the current school year
    const setYear = (sel, sy) => { sel.value = sy; if (!sel.value) sel.value = @json($sgenCurrentSy); };

    // ── Subject Teachers ──
    // Subjects down the side, sections across (a grade has few sections, so it fits without scrolling
    // sideways). The badge under each choice is that teacher's current load; changed cells are marked.
    let T = null;          // last loaded grid
    let showAll = false;   // also show subjects no section of this grade has
    window.openSubjectTeachersModal = function () {
        fillGrades($('sgenTGrade'));
        setYear($('sgenTSy'), pageValue('scheduleYearFilter', ''));
        $('sgenTGrade').value = pageValue('scheduleGradeFilter', 'grade1');
        new bootstrap.Modal($('sgenTeachersModal')).show();
        sgenLoadTeachers();
    };
    const teacher = id => T.teachers.find(t => String(t.id) === String(id));
    const loadBadge = id => {
        const t = id && teacher(id);
        if (!t) return '<span style="font-size:11px;color:var(--muted);">No teacher</span>';
        const lim = T.limits.week_hours;
        const [bg, fg, label] = t.hours > lim ? ['#fef2f2', '#b91c1c', 'Overload'] : (t.hours >= lim * 0.9 ? ['#fff7ed', '#b45309', 'Near limit'] : ['#f0fdf4', '#166534', 'Load']);
        return '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;background:' + bg + ';color:' + fg + ';">' + label + ' · ' + t.hours + 'h/wk</span>';
    };
    window.sgenLoadTeachers = async function () {
        msg('sgenTMsg', 'info', 'Loading…');
        ['sgenTTable', 'sgenTSummary', 'sgenTLoad', 'sgenTDirty'].forEach(id => $(id).innerHTML = '');
        try {
            T = await api(EP.grid + '?school_year=' + encodeURIComponent($('sgenTSy').value) + '&grade=' + encodeURIComponent($('sgenTGrade').value));
        } catch (e) { msg('sgenTMsg', 'error', esc(e.message)); return; }
        msg('sgenTMsg', null, '');
        $('sgenTMore').style.display = 'none'; $('sgenLastYearBtn').style.display = 'none';
        if (!T.sections.length) { msg('sgenTMsg', 'info', 'No active sections for ' + esc(GRADES[T.grade]) + ' in S.Y. ' + esc(T.school_year) + ' yet — create them in Section Management first.'); return; }
        if (!T.subjects.length) { msg('sgenTMsg', 'info', 'No active subjects for ' + esc(GRADES[T.grade]) + ' — add them in Subject Management first.'); return; }
        renderTeacherGrid();
    };
    function renderTeacherGrid() {
        const subjects = T.subjects.filter(s => showAll || s.taught);
        const hidden = T.subjects.length - T.subjects.filter(s => s.taught).length;
        $('sgenTMore').style.display = hidden ? '' : 'none';
        $('sgenTMore').innerHTML = showAll ? '<i class="bi bi-eye-slash"></i> Hide subjects not taught in this grade'
                                           : '<i class="bi bi-eye"></i> Show ' + hidden + ' more subject(s) not taught in this grade';
        $('sgenLastYearBtn').style.display = Object.keys(T.last_year || {}).length ? '' : 'none';

        const options = sel => '<option value="">— No teacher —</option>' + T.teachers.map(t =>
            '<option value="' + t.id + '"' + (String(t.id) === String(sel ?? '') ? ' selected' : '') + '>' + esc(t.name) + '</option>').join('');
        let h = '<thead><tr><th style="text-transform:none;min-width:170px;">Subject</th>' + T.sections.map(sec => {
            const adv = sec.adviser_id && teacher(sec.adviser_id);
            return '<th style="text-transform:none;min-width:210px;">' + esc(sec.name)
                + '<div style="font-weight:400;font-size:11px;color:var(--muted);margin-top:2px;">' + (adv ? 'Adviser: ' + esc(adv.name) : 'No adviser') + '</div>'
                + (adv ? '<button type="button" class="btn-dash btn-secondary" style="padding:2px 8px;font-size:11px;margin-top:4px;" onclick="sgenAdviserAll(' + sec.id + ')">'
                    + '<i class="bi bi-person-check"></i> Adviser teaches all</button>' : '') + '</th>';
        }).join('') + '</tr></thead><tbody>';
        subjects.forEach(sub => {
            h += '<tr' + (sub.taught ? '' : ' style="background:#fafafa;"') + '><td><strong>' + esc(sub.name) + '</strong>'
                + (sub.taught ? '' : '<div style="font-size:11px;color:var(--muted);">Not taught in this grade yet</div>')
                + (T.sections.length > 1 ? '<div><button type="button" class="btn-dash btn-secondary" style="padding:2px 8px;font-size:11px;margin-top:4px;" onclick="sgenFillColumn(' + sub.id + ')">'
                    + '<i class="bi bi-arrow-right"></i> Same teacher in all sections</button></div>' : '') + '</td>';
            T.sections.forEach(sec => {
                const cur = (T.cells[sec.id] || {})[sub.id] ?? '';
                h += '<td><select class="form-fld sgen-cell" style="font-size:13px;padding:6px 8px;width:100%;" data-section="' + sec.id + '" data-subject="' + sub.id + '" data-orig="' + cur + '"'
                    + ' onchange="sgenCellChanged(this)">' + options(cur) + '</select><div class="sgen-badge" style="margin-top:4px;">' + loadBadge(cur) + '</div></td>';
            });
            h += '</tr>';
        });
        $('sgenTTable').innerHTML = h + '</tbody>';
        document.querySelectorAll('.sgen-cell').forEach(markCell);
        refreshTeacherSummary();
    }
    function markCell(c) {
        const changed = c.value !== c.dataset.orig;
        c.style.borderColor = changed ? '#f5a623' : '';
        c.style.background = changed ? '#fffbeb' : '';
        c.nextElementSibling.innerHTML = loadBadge(c.value);
    }
    window.sgenCellChanged = function (c) { markCell(c); refreshTeacherSummary(); };
    function refreshTeacherSummary() {
        const cells = [...document.querySelectorAll('.sgen-cell')];
        const filled = cells.filter(c => c.value).length;
        const changed = cells.filter(c => c.value !== c.dataset.orig).length;
        const chip = (txt, bg, fg) => '<span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:12px;background:' + bg + ';color:' + fg + ';">' + txt + '</span>';
        $('sgenTSummary').innerHTML = chip(filled + ' of ' + cells.length + ' assigned', '#e8f0fb', '#1a3a6c')
            + (cells.length - filled ? chip((cells.length - filled) + ' without a teacher', '#fff7ed', '#b45309') : chip('All assigned', '#f0fdf4', '#166534'));
        $('sgenTDirty').innerHTML = changed ? '<span style="color:#b45309;font-weight:600;"><i class="bi bi-pencil-fill"></i> ' + changed + ' unsaved change(s)</span>' : 'No unsaved changes';
        $('sgenTSaveBtn').innerHTML = changed ? '<i class="bi bi-check-lg"></i> Save Teachers (' + changed + ')'
            : '<i class="bi bi-magic"></i> Next: Generate Schedule <i class="bi bi-arrow-right"></i>';

        // Teachers used in this grade: how many subjects here, and their current load
        const used = {};
        cells.filter(c => c.value).forEach(c => { used[c.value] = (used[c.value] || 0) + 1; });
        const ids = Object.keys(used).sort((a, b) => used[b] - used[a]);
        $('sgenTLoad').innerHTML = ids.length ? '<div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px;">Teachers in ' + esc(GRADES[T.grade]) + '</div>'
            + '<div style="display:flex;gap:8px;flex-wrap:wrap;">' + ids.map(id => {
                const t = teacher(id);
                return '<div style="background:#fff;border:1px solid var(--border,#e2e8f0);border-radius:10px;padding:8px 12px;font-size:12px;">'
                    + '<strong>' + esc(t ? t.name : '?') + '</strong> · ' + used[id] + ' subject(s) here<div style="margin-top:4px;">' + loadBadge(id) + '</div></div>';
            }).join('') + '</div>' : '';
    }
    window.sgenToggleMore = function () { showAll = !showAll; keepChoices(renderTeacherGrid); };
    // Re-rendering (show/hide subjects) keeps what was picked but not saved yet
    function keepChoices(render) {
        const picked = {};
        document.querySelectorAll('.sgen-cell').forEach(c => { picked[c.dataset.section + '|' + c.dataset.subject] = c.value; });
        render();
        document.querySelectorAll('.sgen-cell').forEach(c => {
            const k = c.dataset.section + '|' + c.dataset.subject;
            if (k in picked) { c.value = picked[k]; markCell(c); }
        });
        refreshTeacherSummary();
    }
    const cell = (sec, sub) => document.querySelector('.sgen-cell[data-section="' + sec + '"][data-subject="' + sub + '"]');
    window.sgenAdviserAll = function (sectionId) {
        const sec = T.sections.find(s => s.id === sectionId);
        document.querySelectorAll('.sgen-cell[data-section="' + sectionId + '"]').forEach(c => { c.value = sec.adviser_id; markCell(c); });
        refreshTeacherSummary();
    };
    window.sgenFillColumn = function (subjectId) {
        const first = T.sections.map(s => cell(s.id, subjectId)).find(c => c && c.value);
        if (!first) { msg('sgenTMsg', 'info', 'Pick the teacher in one section first, then use "Same teacher in all sections".'); return; }
        T.sections.forEach(s => { const c = cell(s.id, subjectId); if (c) { c.value = first.value; markCell(c); } });
        refreshTeacherSummary();
    };
    window.sgenUseLastYear = function () {
        let n = 0;
        document.querySelectorAll('.sgen-cell').forEach(c => {
            const hint = T.last_year[c.dataset.subject];
            if (!c.value && hint && c.querySelector('option[value="' + hint + '"]')) { c.value = hint; markCell(c); n++; }
        });
        refreshTeacherSummary();
        msg('sgenTMsg', 'info', n + ' empty cell(s) filled with last year\'s teachers — check them, then Save.');
    };
    // Saves the changed cells; with nothing to save, moves on to Generate Schedule for this year and grade
    window.sgenSaveTeachers = async function () {
        // Only what changed — hidden subjects keep their teachers
        const cells = [...document.querySelectorAll('.sgen-cell')].filter(c => c.value !== c.dataset.orig)
            .map(c => ({ section_id: +c.dataset.section, subject_id: +c.dataset.subject, teacher_id: c.value ? +c.value : null }));
        if (!cells.length) {
            if (!T) return;
            bootstrap.Modal.getInstance($('sgenTeachersModal')).hide();
            openGenerateScheduleModal(T.school_year, T.grade);
            return;
        }
        try {
            const d = await api(EP.gridSave, 'POST', { school_year: T.school_year, cells });
            if (typeof loadScheduleGrid === 'function') loadScheduleGrid();
            await sgenLoadTeachers();
            msg('sgenTMsg', 'success', esc(d.message));
        } catch (e) { msg('sgenTMsg', 'error', esc(e.message)); }
    };

    // ── Generate Schedule ──
    let lastInput = null;
    window.openGenerateScheduleModal = function (sy, grade) {
        fillGrades($('sgenGrade'));
        setYear($('sgenSy'), sy || pageValue('scheduleYearFilter', ''));
        $('sgenGrade').value = grade || pageValue('scheduleGradeFilter', 'grade1');
        sgenShowStep(1);
        msg('sgenGMsg', null, '');
        new bootstrap.Modal($('sgenGenModal')).show();
        sgenLoadDefaults();
    };
    window.sgenShowStep = function (n) {
        $('sgenStep1').style.display = n === 1 ? '' : 'none';
        $('sgenStep2').style.display = n === 2 ? '' : 'none';
        $('sgenBackBtn').style.display = n === 2 ? '' : 'none';
        $('sgenPreviewBtn').style.display = n === 1 ? '' : 'none';
        $('sgenSaveBtn').style.display = n === 2 ? '' : 'none';
        $('sgenStepLabel').textContent = n === 1 ? '· Step 1 of 2: Settings' : '· Step 2 of 2: Preview';
    };
    let D = null; // defaults for the chosen grade (sections, subjects, teacher coverage)
    window.sgenLoadDefaults = async function () {
        try { D = await api(EP.defaults + '?grade=' + encodeURIComponent($('sgenGrade').value) + '&school_year=' + encodeURIComponent($('sgenSy').value)); }
        catch (e) { msg('sgenGMsg', 'error', esc(e.message)); return; }
        // The grade's usual day: Nursery/Kinder 7:00–10:20, Grades 1–6 7:00–2:00
        $('sgenStart').value = D.start_time; $('sgenEnd').value = D.end_time; $('sgenMinutes').value = D.period_minutes;
        $('sgenBreaks').innerHTML = '';
        (D.breaks || []).forEach(b => sgenAddBreak(b));
        $('sgenOtherSubjects').innerHTML = (D.other_subjects || []).length
            ? '<i class="bi bi-info-circle"></i> Not scheduled (not linked to these sections in Section Management › Subjects): ' + D.other_subjects.map(esc).join(', ') : '';
        $('sgenSections').innerHTML = D.sections.length
            ? D.sections.map(s => '<label class="sgen-pill on"><input type="checkbox" class="sgen-section" value="' + s.id + '" checked>' + esc(s.name) + '</label>').join('')
            : '<div style="font-size:12px;color:#b45309;margin-bottom:6px;">No sections yet for this grade and year.</div>';
        renderSubjects(D.subjects || []);
        msg('sgenGMsg', null, '');
        if (!D.sections.length) msg('sgenGMsg', 'info', 'No active sections for ' + esc(GRADES[$('sgenGrade').value]) + ' in S.Y. ' + esc($('sgenSy').value) + ' — create them in Section Management first.');
        else if (!(D.subjects || []).length) msg('sgenGMsg', 'info', 'No active subjects for this grade — add them in Subject Management first.');
        sgenSummary();
    };
    window.sgenAddBreak = function (b) {
        b = b || { label: 'Break', start: '', end: '' };
        const row = document.createElement('div');
        row.className = 'sgen-break';
        row.style.cssText = 'display:flex;gap:6px;align-items:center;margin-bottom:6px;';
        row.innerHTML = '<input class="form-fld sgen-break-label" style="width:100px;" value="' + esc(b.label) + '">'
            + '<input type="time" class="form-fld sgen-break-start" value="' + esc(b.start) + '"><span>–</span><input type="time" class="form-fld sgen-break-end" value="' + esc(b.end) + '">'
            + '<button type="button" class="sgen-mini" title="Remove" onclick="this.parentNode.remove();sgenSummary();"><i class="bi bi-x-lg"></i></button>';
        $('sgenBreaks').appendChild(row);
    };
    // Compact rows: ↑ ↓ · name · teacher coverage · − n +
    function renderSubjects(list) {
        const n = (D.sections || []).length;
        $('sgenSubjects').innerHTML = list.map(s => {
            const chip = !n ? '' : s.teachers >= n
                ? '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;background:#f0fdf4;color:#166534;">' + s.teachers + '/' + n + ' teacher' + (n > 1 ? 's' : '') + '</span>'
                : '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;background:#fff7ed;color:#b45309;">' + (s.teachers ? s.teachers + '/' + n + ' teachers' : 'No teacher') + '</span>';
            return '<div class="sgen-subject" data-id="' + s.id + '">'
                + '<span style="display:flex;gap:3px;"><button type="button" class="sgen-mini" title="Move up" onclick="sgenMove(this,-1)"><i class="bi bi-chevron-up"></i></button>'
                + '<button type="button" class="sgen-mini" title="Move down" onclick="sgenMove(this,1)"><i class="bi bi-chevron-down"></i></button></span>'
                + '<span style="flex:1;font-size:13px;font-weight:600;">' + esc(s.name) + '</span>'
                + '<span class="sgen-chip" data-chip="' + esc(chip) + '">' + chip + '</span>'
                + '<span style="display:flex;align-items:center;gap:4px;"><button type="button" class="sgen-mini" onclick="sgenStep(this,-1)"><i class="bi bi-dash"></i></button>'
                + '<input type="number" class="form-fld sgen-perweek" min="0" max="15" value="' + s.per_week + '">'
                + '<button type="button" class="sgen-mini" onclick="sgenStep(this,1)"><i class="bi bi-plus"></i></button></span></div>';
        }).join('');
        document.querySelectorAll('.sgen-subject').forEach(markSubject);
    }
    function markSubject(row) {
        const off = !(+row.querySelector('.sgen-perweek').value);
        row.classList.toggle('off', off);
        const chip = row.querySelector('.sgen-chip');
        chip.innerHTML = off ? '<span style="font-size:11px;color:var(--muted);">Left out</span>' : chip.dataset.chip;
    }
    window.sgenStep = function (btn, d) {
        const inp = btn.parentNode.querySelector('.sgen-perweek');
        inp.value = Math.max(0, Math.min(15, (+inp.value || 0) + d));
        markSubject(btn.closest('.sgen-subject'));
        sgenSummary();
    };
    window.sgenMove = function (btn, dir) {
        const row = btn.closest('.sgen-subject');
        const other = dir < 0 ? row.previousElementSibling : row.nextElementSibling;
        if (other) dir < 0 ? row.parentNode.insertBefore(row, other) : row.parentNode.insertBefore(other, row);
    };

    // Same period rule as the server (ScheduleGenerator::slots): back to back, jumping over breaks
    function daySlots(start, minutes, breaks, count) {
        const toMin = t => { const [h, m] = (t || '0:0').split(':').map(Number); return h * 60 + m; };
        const fmt = m => { const h = Math.floor(m / 60), mm = String(m % 60).padStart(2, '0'); return ((h + 11) % 12 + 1) + ':' + mm + (h < 12 ? ' AM' : ' PM'); };
        let t = toMin(start); const out = []; let guard = 0;
        while (out.length < count && guard++ < 40) {
            const end = t + minutes;
            const hit = breaks.find(b => t < toMin(b.end) && end > toMin(b.start));
            if (hit) { t = toMin(hit.end); continue; }
            out.push([t, end]); t = end;
        }
        return { out, fmt };
    }
    // Periods that fit before dismissal, per day
    function capacity(c) {
        const minutes = Math.max(20, Math.min(120, c.period_minutes || 60));
        const end = (t => { const [h, m] = (t || '0:0').split(':').map(Number); return h * 60 + m; })(c.end_time);
        return daySlots(c.start_time, minutes, c.breaks, 40).out.filter(s => s[1] <= end).length;
    }
    // Share the week's periods evenly, top subjects first getting the extra (15 over 6 → 3,3,3,2,2,2)
    window.sgenSpread = function () {
        const c = collect();
        const rows = [...document.querySelectorAll('.sgen-subject')];
        const total = capacity(c) * (c.days.length || 1);
        rows.forEach((r, i) => { r.querySelector('.sgen-perweek').value = Math.floor(total / rows.length) + (i < total % rows.length ? 1 : 0); markSubject(r); });
        sgenSummary();
    };
    window.sgenSummary = function () {
        if (!D) return;
        const c = collect();
        const total = c.subjects.reduce((a, s) => a + s.per_week, 0);
        const days = c.days.length || 1;
        const perDay = Math.ceil(total / days);
        const minutes = Math.max(20, Math.min(120, c.period_minutes || 60));
        const { out, fmt } = daySlots(c.start_time, minutes, c.breaks, perDay);
        const cap = capacity(c), fits = cap * days;
        $('sgenSubjectsTotal').innerHTML = c.subjects.filter(s => s.per_week).length + ' subject(s) · ' + total + ' of ' + fits + ' period(s) a week'
            + (total > fits ? ' <span style="color:#b91c1c;font-weight:600;">— ' + (total - fits) + ' past dismissal</span>'
               : total < fits ? ' <span style="color:#b45309;font-weight:600;">— ' + (fits - total) + ' free</span>' : ' <span style="color:#166534;font-weight:600;">— day is full</span>');

        $('sgenSummaryLine').innerHTML = !total ? 'Set at least one subject to 1 or more times a week.'
            : '<i class="bi bi-calendar-week"></i> ' + total + ' periods a week · ' + perDay + ' a day · classes '
              + (out.length ? fmt(out[0][0]) + ' – ' + '<span style="' + (perDay > cap ? 'color:#b91c1c;' : '') + '">' + fmt(out[out.length - 1][1]) + '</span>' : '—')
              + (perDay > cap ? ' <span style="color:#b91c1c;">(past dismissal — lower the times a week)</span>' : '')
              + ' · ' + (c.days.length ? c.days.map(d => d.slice(0, 3)).join(', ') : '<span style="color:#b91c1c;">no days chosen</span>')
              + ' · Term ' + (c.terms.length ? c.terms.join(', ') : '<span style="color:#b91c1c;">none</span>');

        const missing = (D.subjects || []).filter(s => c.subjects.find(x => x.id === s.id && x.per_week) && s.teachers < D.sections.length).length;
        const chosen = D.sections.filter(sec => c.section_ids.includes(sec.id));
        $('sgenSummarySections').innerHTML = (D.sections.length && !chosen.length
            ? '<span style="font-size:12px;font-weight:600;color:#b91c1c;">No section chosen</span>' : '') + chosen.map(sec => {
            const clash = sec.scheduled_terms.filter(t => c.terms.includes(t));
            const [bg, fg, txt] = !clash.length ? ['#f0fdf4', '#166534', 'will be built']
                : (c.replace ? ['#fff7ed', '#b45309', 'replaces its Term ' + clash.join('/') + ' schedule'] : ['#f1f5f9', '#475569', 'skipped — already has Term ' + clash.join('/')]);
            return '<span style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:12px;background:' + bg + ';color:' + fg + ';">' + esc(sec.name) + ': ' + txt + '</span>';
        }).join('') + (missing ? '<span style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:12px;background:#fff7ed;color:#b45309;">'
            + missing + ' subject(s) missing a teacher in some section</span>' : '');
    };
    // Pills (terms/days) and every setting update the summary
    document.addEventListener('change', e => {
        const pill = e.target.closest('#sgenGenModal .sgen-pill');
        if (pill) pill.classList.toggle('on', e.target.checked);
        if (e.target.closest('#sgenStep1')) {
            if (e.target.classList.contains('sgen-perweek')) markSubject(e.target.closest('.sgen-subject'));
            sgenSummary();
        }
    });
    document.addEventListener('input', e => { if (e.target.closest('#sgenStep1') && e.target.type !== 'checkbox') sgenSummary(); });
    function collect() {
        return {
            school_year: $('sgenSy').value, grade: $('sgenGrade').value,
            section_ids: [...document.querySelectorAll('.sgen-section:checked')].map(c => +c.value),
            terms: [...document.querySelectorAll('.sgen-term:checked')].map(c => +c.value),
            days: [...document.querySelectorAll('.sgen-day:checked')].map(c => c.value),
            start_time: $('sgenStart').value, end_time: $('sgenEnd').value, period_minutes: +$('sgenMinutes').value,
            breaks: [...document.querySelectorAll('.sgen-break')].map(r => ({ label: r.querySelector('.sgen-break-label').value, start: r.querySelector('.sgen-break-start').value, end: r.querySelector('.sgen-break-end').value }))
                .filter(b => b.start && b.end),
            subjects: [...document.querySelectorAll('.sgen-subject')].map(r => ({ id: +r.dataset.id, per_week: +(r.querySelector('.sgen-perweek').value || 0) })),
            limits: { week_hours: +$('sgenLimWeek').value, day_periods: +$('sgenLimDay').value, consecutive: +$('sgenLimRow').value },
            replace: $('sgenReplace').checked,
        };
    }
    window.sgenPreview = async function () {
        lastInput = collect();
        if (!lastInput.section_ids.length || !lastInput.terms.length || !lastInput.days.length) { msg('sgenGMsg', 'error', 'Choose at least one section, one term and one day.'); return; }
        msg('sgenGMsg', 'info', 'Building the preview…');
        let p;
        try { p = await api(EP.preview, 'POST', lastInput); } catch (e) { msg('sgenGMsg', 'error', esc(e.message)); return; }
        msg('sgenGMsg', null, '');
        $('sgenStep2').innerHTML = renderPreview(p);
        $('sgenSaveBtn').disabled = !p.will_build;
        sgenShowStep(2);
    };
    function renderPreview(p) {
        const tn = id => (p.teacher_names || {})[id] || '';
        let h = '<div style="font-size:13px;margin-bottom:12px;"><strong>' + esc(GRADES[p.grade]) + ' · S.Y. ' + esc(p.school_year) + ' · Term ' + p.terms.join(', ') + '</strong> — '
            + p.will_build + ' section(s) will be built' + (p.skipped ? ', ' + p.skipped + ' skipped' : '')
            + (p.warnings ? ', <span style="color:#b91c1c;font-weight:600;">' + p.warnings + ' warning(s)</span> (red cells — hover for details)' : ', no warnings') + '.</div>';

        if (p.teacher_load.length) {
            h += '<div style="font-weight:700;font-size:13px;margin:6px 0;">Teacher Load (hours a week · limit ' + p.limits.week_hours + 'h)</div>'
                + '<table class="dash-table" style="margin-bottom:14px;"><thead><tr><th>Teacher</th><th>Now</th><th>+ This grade</th><th>Total</th><th>Status</th></tr></thead><tbody>'
                + p.teacher_load.map(l => '<tr><td>' + esc(l.name) + '</td><td>' + l.now + 'h</td><td>+' + l.added + 'h</td><td><strong>' + l.total + 'h</strong></td><td>'
                    + ({ ok: '<span class="status-badge paid">OK</span>', near: '<span class="status-badge partial">Near limit</span>',
                         over: '<span class="status-badge unpaid">Overload +' + Math.round((l.total - p.limits.week_hours) * 10) / 10 + 'h</span>' })[l.status] + '</td></tr>').join('')
                + '</tbody></table>';
        }
        if (p.missing_teachers.length) {
            h += '<div style="padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;background:#fff8f0;border:1px solid #fed7aa;color:#92400e;">'
                + '<strong>' + p.missing_teachers.length + ' subject(s) have no teacher yet</strong> — their periods are created without a teacher: '
                + p.missing_teachers.map(m => esc(m.section) + ' · ' + esc(m.subject)).join(', ')
                + '. <a href="#" onclick="bootstrap.Modal.getInstance(document.getElementById(\'sgenGenModal\')).hide();openSubjectTeachersModal();return false;">Assign subject teachers</a></div>';
        }
        p.sections.forEach(s => {
            h += '<div style="font-weight:700;font-size:14px;margin:14px 0 6px;">' + esc(s.name)
                + (s.skipped ? ' <span style="font-weight:400;font-size:12px;color:var(--muted);">— skipped: ' + esc(s.reason) + '</span>'
                             : (s.replacing ? ' <span style="font-weight:400;font-size:12px;color:#b45309;">— replaces its current schedule</span>' : '')) + '</div>';
            if (s.skipped) return;
            h += '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr><th style="width:110px;">Time</th>' + p.days.map(d => '<th>' + d + '</th>').join('') + '</tr></thead><tbody>';
            p.slots.forEach((slot, j) => {
                const brk = (p.breaks || []).find(b => b.start >= (j ? p.slots[j - 1].end : '00:00') && b.end <= slot.start);
                if (brk) h += '<tr><td colspan="' + (p.days.length + 1) + '" style="text-align:center;font-size:12px;color:var(--muted);background:#f8fafc;">' + esc(brk.label || 'Break') + ' · ' + esc(brk.start) + '–' + esc(brk.end) + '</td></tr>';
                h += '<tr><td style="white-space:nowrap;font-size:12px;">' + slot.start + '–' + slot.end + '</td>' + p.days.map(d => {
                    const c = (s.grid[d] || []).find(r => r.slot === j);
                    if (!c) return '<td></td>';
                    const bad = c.notes.length;
                    return '<td title="' + esc(c.notes.join(' · ')) + '" style="font-size:12px;' + (bad ? 'background:#fef2f2;color:#b91c1c;' : '') + '"><strong>' + esc(c.subject) + '</strong><div>'
                        + (c.teacher_id ? esc(tn(c.teacher_id)) : '<em>No teacher</em>') + (bad ? ' <i class="bi bi-exclamation-triangle-fill"></i>' : '') + '</div></td>';
                }).join('') + '</tr>';
            });
            h += '</tbody></table></div>';
        });
        return h;
    }
    window.sgenSave = async function () {
        if (!lastInput) return;
        $('sgenSaveBtn').disabled = true;
        try {
            const d = await api(EP.save, 'POST', lastInput);
            msg('sgenGMsg', 'success', esc(d.message));
            $('sgenStep2').innerHTML = '';
            $('sgenSaveBtn').style.display = 'none';
            const yf = $('scheduleYearFilter'), gf = $('scheduleGradeFilter');
            if (yf) yf.value = lastInput.school_year;
            if (gf) gf.value = lastInput.grade;
            if (typeof loadScheduleGrid === 'function') loadScheduleGrid();
        } catch (e) { msg('sgenGMsg', 'error', esc(e.message)); $('sgenSaveBtn').disabled = false; }
    };
})();
</script>
