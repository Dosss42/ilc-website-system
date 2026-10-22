<!-- Enrollment View Modal -->
<div class="modal fade" id="enrollmentViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,.22);">

            {{-- ── HEADER ── --}}
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#1d5ebd 60%,#2471a3 100%);padding:22px 26px;position:relative;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-badge-fill" style="font-size:24px;color:#fff;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <h5 style="color:#fff;font-weight:800;font-size:18px;margin:0;line-height:1.2;">Enrollment Application</h5>
                            <span id="modal-type-badge" style="display:none;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);"></span>
                        </div>
                        <div style="margin-top:4px;font-size:12px;color:rgba(255,255,255,.7);">
                            Ref: <span id="modal-reference-number" style="font-weight:600;color:rgba(255,255,255,.9);"></span>
                            &nbsp;·&nbsp; S.Y. <span id="modal-school-year" style="font-weight:600;color:rgba(255,255,255,.9);"></span>
                        </div>
                    </div>
                    <div id="modal-status-pill" style="flex-shrink:0;"></div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- ── TAB NAV ── --}}
            <div style="background:#f8faff;border-bottom:1.5px solid #e2e8f0;padding:0 24px;">
                <div style="display:flex;gap:0;">
                    <button type="button" onclick="evmTab('details')" id="evm-tab-details"
                        style="display:flex;align-items:center;gap:7px;padding:13px 18px;border:none;border-bottom:3px solid #1a3a6c;background:transparent;color:#1a3a6c;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:inherit;">
                        <i class="bi bi-person-lines-fill"></i> Application Details
                    </button>
                    <button type="button" onclick="evmTab('documents')" id="evm-tab-documents"
                        style="display:flex;align-items:center;gap:7px;padding:13px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;">
                        <i class="bi bi-folder2-open"></i> Documents
                        <span id="evm-doc-count" style="display:none;background:#1a3a6c;color:#fff;font-size:10px;font-weight:700;border-radius:10px;padding:1px 7px;"></span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0" style="background:#f4f7fb;">

                {{-- ── DETAILS TAB ── --}}
                <div id="evm-pane-details" style="padding:24px;">
                    <div class="row g-3">

                        {{-- Student Info --}}
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d5ebd,#2471a3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-person-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Student Information</div>
                                        <div style="font-size:11px;color:#94a3b8;">Personal details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Full Name</label><div id="modal-student-name" class="evm-val"></div></div>
                                    <div class="evm-field"><label>Email Address</label><div id="modal-student-email" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Grade Level</label><div id="modal-grade-level" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Student Type</label><div id="modal-student-type" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Phone</label><div id="modal-student-phone" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Date of Birth</label><div id="modal-student-birthdate" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Guardian Info --}}
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0891b2,#0e7490);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-shield-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Guardian Information</div>
                                        <div style="font-size:11px;color:#94a3b8;">Parent / Guardian details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Full Name</label><div id="modal-guardian-name" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Relationship</label><div id="modal-relationship" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Occupation</label><div id="modal-guardian-occupation" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Email</label><div id="modal-guardian-email" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Phone</label><div id="modal-guardian-phone" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Info --}}
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#16a34a,#15803d);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-geo-alt-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Address</div>
                                        <div style="font-size:11px;color:#94a3b8;">Residential details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Street Address</label><div id="modal-street-address" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Barangay</label><div id="modal-barangay" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>City / Municipality</label><div id="modal-city" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Province</label><div id="modal-province" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Zip Code</label><div id="modal-zip-code" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#d97706,#b45309);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-clock-history" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Application Timeline</div>
                                        <div style="font-size:11px;color:#94a3b8;">Important dates &amp; status</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:flex;flex-direction:column;gap:10px;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #1a3a6c;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Application Date</span>
                                        <span id="modal-application-date" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #0891b2;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Last Updated</span>
                                        <span id="modal-updated-at" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #d97706;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Processing Status</span>
                                        <span id="modal-processing-status" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section Assignment (approved only) --}}
                        <div class="col-12" id="section-assignment-card" style="display:none;">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="bi bi-people-fill" style="color:#fff;font-size:16px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Section Assignment</div>
                                            <div style="font-size:11px;color:#94a3b8;">Current assigned section</div>
                                        </div>
                                    </div>
                                    <button type="button" class="evm-sec-btn" id="btn-change-section" onclick="evmStartSectionEdit()">
                                        <i class="bi bi-arrow-left-right"></i> Change Section
                                    </button>
                                </div>
                                <div style="padding:18px;">
                                    <div class="row g-3">
                                        <div class="col-md-6"><div class="evm-field"><label>Current Section</label><div id="modal-current-section" class="evm-val">Not assigned</div></div></div>
                                        <div class="col-md-6"><div class="evm-field"><label>Grade Level</label><div id="modal-section-grade-level" class="evm-val"></div></div></div>
                                    </div>

                                    {{-- Inline editor (replaces the old stacked "Change Section" modal, whose
                                         extra backdrop was left behind after saving and dimmed the screen) --}}
                                    <div id="evm-sec-editor" class="evm-sec-editor" style="display:none;">
                                        <label for="evm-sec-select">Move to section</label>
                                        <div class="evm-sec-editor-row">
                                            <select id="evm-sec-select" class="form-select form-select-sm"></select>
                                            <button type="button" class="evm-sec-save" id="evm-sec-save" onclick="evmSaveSection()">
                                                <i class="bi bi-check-lg"></i> Save
                                            </button>
                                            <button type="button" class="evm-sec-cancel" onclick="evmCancelSectionEdit()">Cancel</button>
                                        </div>
                                        <div class="evm-sec-hint">Only active sections for this student's grade level and school year are listed.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── DOCUMENTS TAB ── --}}
                <div id="evm-pane-documents" style="display:none;padding:24px;">
                    <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;">
                        <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-folder2-open" style="color:#fff;font-size:16px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Submitted Documents</div>
                                <div style="font-size:11px;color:#94a3b8;">Review and approve/reject documents uploaded by the student</div>
                            </div>
                        </div>
                        <div style="padding:18px;">
                            <div id="evm-docs-loading" style="text-align:center;padding:32px;color:#94a3b8;">
                                <div class="spinner-border spinner-border-sm me-2" style="color:#1a3a6c;"></div>
                                Loading documents…
                            </div>
                            <div id="evm-docs-content" style="display:none;"></div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── FOOTER ── --}}
            <div style="padding:16px 24px;border-top:1.5px solid #e8edf5;background:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <small style="color:#94a3b8;font-size:12px;"><i class="bi bi-info-circle me-1"></i>Review all information before taking action</small>
                <div id="modal-action-buttons" style="display:flex;gap:8px;flex-wrap:wrap;"></div>
            </div>

        </div>
    </div>
</div>

<style>
.evm-field { display:flex;flex-direction:column;gap:4px; }
.evm-field label { font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px; }
.evm-val { font-size:13px;font-weight:500;color:#1e293b;background:#f8faff;border:1px solid #e8edf5;border-radius:7px;padding:7px 11px;min-height:34px;display:flex;align-items:center; }

/* Section Assignment — Change Section button + inline editor */
.evm-sec-btn { display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;border:1px solid #c7d2fe;background:#eef2ff;color:#3730a3;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s; }
.evm-sec-btn:hover { background:#e0e7ff;border-color:#a5b4fc; }
.evm-sec-btn:disabled { opacity:.5;cursor:not-allowed; }
.evm-sec-editor { margin-top:16px;padding:14px;border:1px dashed #c7d2fe;border-radius:10px;background:#f8faff; }
.evm-sec-editor label { display:block;font-size:10.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.evm-sec-editor-row { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }
.evm-sec-editor-row select { flex:1;min-width:200px;font-size:13px; }
.evm-sec-save { display:inline-flex;align-items:center;gap:5px;padding:6px 16px;border:none;border-radius:8px;background:#1a3a6c;color:#fff;font-size:12.5px;font-weight:600;cursor:pointer; }
.evm-sec-save:disabled { opacity:.6;cursor:not-allowed; }
.evm-sec-cancel { padding:6px 14px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#475569;font-size:12.5px;font-weight:600;cursor:pointer; }
.evm-sec-hint { font-size:11px;color:#94a3b8;margin-top:8px; }
</style>

<script>
/*
 * Section Assignment inline editor — shared by the Admin and Super Admin
 * dashboards (both include this partial and both set
 * window._currentEnrollmentData when the details modal loads).
 * Each dashboard may define window.onEnrollmentSectionChanged(id, name) to
 * refresh its own enrollment list afterwards.
 */
function _evmNotify(type, msg) {
    if (typeof window.saToast === 'function') { window.saToast(type, msg); return; }
    if (typeof window.showCustomAlert === 'function') {
        window.showCustomAlert(type, type === 'success' ? 'Success!' : (type === 'warning' ? 'Warning' : 'Error'), msg);
        return;
    }
    alert(msg);
}

function evmCancelSectionEdit() {
    var ed = document.getElementById('evm-sec-editor');
    if (ed) ed.style.display = 'none';
    var btn = document.getElementById('btn-change-section');
    if (btn) btn.disabled = false;
}

function evmStartSectionEdit() {
    var data = window._currentEnrollmentData;
    if (!data) { _evmNotify('error', 'No enrollment loaded.'); return; }
    var grade  = (data.student_data || {}).grade_level || data.grade_level || '';
    var select = document.getElementById('evm-sec-select');
    select.innerHTML = '<option value="">Loading sections…</option>';
    select.disabled = true;
    document.getElementById('evm-sec-save').disabled = true;
    document.getElementById('evm-sec-editor').style.display = 'block';
    document.getElementById('btn-change-section').disabled = true;

    fetch('/admin/sections?grade_level=' + encodeURIComponent(grade), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '' }
    })
    .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
    .then(function (d) {
        // Same rules the server enforces: active, same school year, not full
        var sections = (d.sections || []).filter(function (s) {
            return s.is_active && (!data.school_year || s.school_year === data.school_year);
        });
        if (!sections.length) {
            select.innerHTML = '<option value="">No active sections for this grade and school year</option>';
            return;
        }
        select.innerHTML = '<option value="">— Select a section —</option>' + sections.map(function (s) {
            var count     = (s.students || []).length;
            var isCurrent = data.section && s.name === data.section;
            var full      = !isCurrent && s.max_students && count >= s.max_students;
            var label     = s.name + ' — ' + count + '/' + (s.max_students || '?') + ' students'
                          + (isCurrent ? ' (current)' : (full ? ' (full)' : ''));
            var opt = document.createElement('option');
            opt.value = s.id; opt.textContent = label; opt.disabled = !!(isCurrent || full);
            return opt.outerHTML;
        }).join('');
        select.disabled = false;
        document.getElementById('evm-sec-save').disabled = false;
    })
    .catch(function () {
        select.innerHTML = '<option value="">Failed to load sections — close and try again</option>';
    });
}

function evmSaveSection() {
    var data      = window._currentEnrollmentData;
    var sectionId = document.getElementById('evm-sec-select').value;
    if (!data) return;
    if (!sectionId) { _evmNotify('warning', 'Please select a section.'); return; }

    var btn = document.getElementById('evm-sec-save');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving…';

    fetch('/admin/enrollments/' + data.id + '/change-section', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', 'Accept': 'application/json',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
        },
        body: JSON.stringify({ section_id: sectionId })
    })
    .then(function (r) { return r.json().catch(function () { return { success: false, message: 'Server error (' + r.status + ').' }; }); })
    .then(function (d) {
        if (!d.success) { _evmNotify('error', d.message || 'Failed to change section.'); return; }
        // Update the card in place — no second modal, no re-opening, so no stray backdrop
        data.section = d.section_name || data.section;
        document.getElementById('modal-current-section').textContent = data.section || 'Not assigned';
        evmCancelSectionEdit();
        _evmNotify('success', d.message || 'Section changed successfully.');
        if (typeof window.onEnrollmentSectionChanged === 'function') window.onEnrollmentSectionChanged(data.id, data.section);
    })
    .catch(function () { _evmNotify('error', 'Network error while changing section.'); })
    .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Save'; });
}

// Each time the details modal opens for a student, start with the editor closed
document.addEventListener('show.bs.modal', function (e) {
    if (e.target && e.target.id === 'enrollmentViewModal') evmCancelSectionEdit();
});
</script>
