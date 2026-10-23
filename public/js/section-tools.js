/*
 * Section Management tools for Super Admin, matching Admin's: View Students (transfer / remove),
 * Add Student, Manage Subjects, Auto-Assign. Same /admin/sections endpoints Admin uses (Super
 * Admin is allowed on them); the server checks grade, school year, capacity and active status.
 *
 *   ilcSectionTools.viewStudents(id, name, onChange)
 *   ilcSectionTools.addStudent(id, name, enrolled, max, onChange)
 *   ilcSectionTools.manageSubjects(id, name, gradeLevel, onChange)
 *   ilcSectionTools.autoAssign(onChange)
 *
 * onChange() runs after anything changed, so the page can reload its list. Confirmations are
 * asked inside the window itself (no second pop-up that could open behind this one).
 */
(function () {
    'use strict';

    var GRADE = { nursery: 'Nursery', kindergarten: 'Kindergarten', grade1: 'Grade 1', grade2: 'Grade 2', grade3: 'Grade 3', grade4: 'Grade 4', grade5: 'Grade 5', grade6: 'Grade 6' };
    var PAY = { paid: ['enrolled', 'Paid'], partial: ['active', 'Partial'] };

    function esc(v) {
        return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return (m && m.content) || window.csrfToken || '';
    }

    // JSON request; throws Error(message from the server) when it fails
    function api(url, body) {
        var opts = { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
        if (body !== undefined) {
            opts.method = 'POST';
            opts.headers['Content-Type'] = 'application/json';
            opts.headers['X-CSRF-TOKEN'] = csrf();
            opts.body = JSON.stringify(body);
        }
        return fetch(url, opts).then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (d) {
                if (!r.ok || d.success === false) {
                    var msg = d.error || d.message || (d.errors ? Object.values(d.errors).flat().join(' ') : '') || ('Request failed (' + r.status + ')');
                    throw new Error(msg);
                }
                return d;
            });
        });
    }

    function toast(type, title, msg) {
        if (typeof window.saToast === 'function') window.saToast(type, title, msg);
        else window.alert(title + (msg ? '\n' + msg : ''));
    }

    // One window per tool, rebuilt each time; same look as Section Summary / Transaction History
    function modal(id, icon, title, subtitle) {
        var old = document.getElementById(id);
        if (old) { var inst = window.bootstrap && bootstrap.Modal.getInstance(old); if (inst) inst.dispose(); old.remove(); }
        document.body.insertAdjacentHTML('beforeend',
            '<div class="modal fade" id="' + id + '" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">' +
            '<div class="modal-content" style="border-radius:16px; overflow:hidden; border:none;">' +
            '<div class="modal-header" style="background:linear-gradient(135deg, #0056b3, #003d82); padding:18px 24px; border:none;">' +
            '<div style="display:flex; align-items:center; gap:12px;">' +
            '<div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi ' + icon + '" style="font-size:18px; color:#fff;"></i></div>' +
            '<div><h5 class="modal-title" style="color:#fff; font-weight:700; margin:0; font-size:16px;">' + esc(title) + '</h5>' +
            '<div class="js-sub" style="color:rgba(255,255,255,.8); font-size:12px;">' + esc(subtitle || '') + '</div></div></div>' +
            '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>' +
            '<div class="modal-body js-body" style="padding:20px 24px; background:#fff;"></div>' +
            '<div class="modal-footer js-foot" style="display:none; padding:12px 24px; background:#f8faff; border-top:1.5px solid #e2e8f0;"></div>' +
            '</div></div></div>');
        var el = document.getElementById(id);
        bootstrap.Modal.getOrCreateInstance(el).show();
        return { el: el, body: el.querySelector('.js-body'), foot: el.querySelector('.js-foot'), sub: el.querySelector('.js-sub'),
            hide: function () { bootstrap.Modal.getOrCreateInstance(el).hide(); } };
    }

    function loading(text) {
        return '<div style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>' + esc(text) + '</div>';
    }
    function failed(err) {
        return '<div style="text-align:center;padding:40px;color:var(--red);">' + esc(err && err.message ? err.message : 'Something went wrong.') + '</div>';
    }
    function capacityBar(enrolled, max) {
        var pct = max > 0 ? Math.min(100, Math.round(enrolled / max * 100)) : 0;
        var color = pct >= 90 ? 'var(--red)' : (pct >= 70 ? 'var(--gold, #f59e0b)' : 'var(--green)');
        return '<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">' +
            '<span style="font-size:12px;color:var(--muted);font-weight:600;">CAPACITY</span>' +
            '<div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;"><div style="height:100%;width:' + pct + '%;background:' + color + ';border-radius:4px;"></div></div>' +
            '<span style="font-size:12px;font-weight:700;white-space:nowrap;">' + enrolled + '/' + max + ' enrolled</span></div>';
    }

    // ── View Students: the section's class list, with Transfer and Remove ──
    function viewStudents(sectionId, sectionName, onChange) {
        var m = modal('secToolStudentsModal', 'bi-people-fill', 'Students — ' + sectionName, '');
        var section = null;

        function load() {
            m.body.innerHTML = loading('Loading students…');
            api('/admin/sections/' + encodeURIComponent(sectionId)).then(function (d) {
                section = d;
                m.sub.textContent = (GRADE[d.grade_level] || d.grade_level || '') + ' · S.Y. ' + (d.school_year || '');
                render();
            }).catch(function (e) { m.body.innerHTML = failed(e); });
        }

        function render() {
            var students = section.students || [];
            var html = capacityBar(students.length, section.max_students || 30);
            if (!students.length) {
                m.body.innerHTML = html + '<div style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-people" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>No students in this section yet.</div>';
                return;
            }
            html += '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr><th>LRN</th><th>Name</th><th>Payment</th><th style="text-align:center;">Actions</th></tr></thead><tbody>' +
                students.map(function (s) {
                    var e = s.latest_enrollment || {};
                    var p = PAY[e.payment_status] || ['pending', 'Unpaid'];
                    return '<tr data-id="' + s.id + '">' +
                        '<td style="font-size:12px;">' + esc(s.lrn || '—') + '</td>' +
                        '<td style="font-weight:600;">' + esc(s.name || '—') + '</td>' +
                        '<td><span class="status-badge ' + p[0] + '">' + p[1] + '</span></td>' +
                        '<td style="text-align:center;white-space:nowrap;">' +
                        '<button class="action-btn edit js-transfer" title="Transfer to Another Section"><i class="bi bi-arrow-left-right"></i></button>' +
                        '<button class="action-btn delete js-remove" title="Remove from Section"><i class="bi bi-person-dash-fill"></i></button></td></tr>';
                }).join('') + '</tbody></table></div>';
            m.body.innerHTML = html;
        }

        // Inline panel under a student's row (transfer target / remove confirmation)
        function panel(row, html) {
            var open = m.body.querySelector('tr.js-panel');
            if (open) open.remove();
            row.insertAdjacentHTML('afterend', '<tr class="js-panel"><td colspan="4" style="background:#f8fafc;">' + html + '</td></tr>');
            return row.nextElementSibling;
        }

        m.body.addEventListener('click', function (ev) {
            var btn = ev.target.closest('button');
            if (!btn) return;
            var row = btn.closest('tr[data-id]');
            var studentId = row ? parseInt(row.dataset.id, 10) : null;
            var name = row ? row.children[1].textContent : '';

            if (btn.classList.contains('js-remove')) {
                panel(row, '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:13px;">' +
                    '<span>Remove <strong>' + esc(name) + '</strong> from this section? They can be added to a section again later.</span>' +
                    '<button class="btn-dash btn-danger js-do-remove" data-id="' + studentId + '" style="padding:6px 14px;">Remove</button>' +
                    '<button class="btn-dash btn-secondary js-cancel" style="padding:6px 14px;">Cancel</button></div>');
            } else if (btn.classList.contains('js-transfer')) {
                var p = panel(row, loading('Loading sections…'));
                // Same grade and school year only (the server refuses anything else)
                api('/admin/sections?grade_level=' + encodeURIComponent(section.grade_level) + '&school_year=' + encodeURIComponent(section.school_year))
                    .then(function (d) {
                        var options = (d.sections || []).filter(function (s) { return s.id !== section.id && s.is_active; });
                        p.firstChild.innerHTML = options.length
                            ? '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:13px;"><span>Move <strong>' + esc(name) + '</strong> to</span>' +
                              '<select class="form-fld js-target" style="width:auto;min-width:220px;padding:6px 10px;">' +
                              options.map(function (s) {
                                  var n = (s.students || []).length, max = s.max_students || 30, full = n >= max;
                                  return '<option value="' + s.id + '"' + (full ? ' disabled' : '') + '>' + esc(s.name) + ' — ' + n + '/' + max + (full ? ' (FULL)' : '') + '</option>';
                              }).join('') + '</select>' +
                              '<button class="btn-dash btn-primary js-do-transfer" data-id="' + studentId + '" style="padding:6px 14px;">Transfer</button>' +
                              '<button class="btn-dash btn-secondary js-cancel" style="padding:6px 14px;">Cancel</button></div>'
                            : '<div style="font-size:13px;color:var(--muted);">No other active ' + esc(GRADE[section.grade_level] || '') + ' section in S.Y. ' + esc(section.school_year) + '. <button class="btn-dash btn-secondary js-cancel" style="padding:4px 12px;margin-left:6px;">Close</button></div>';
                    }).catch(function (e) { p.firstChild.innerHTML = failed(e); });
            } else if (btn.classList.contains('js-cancel')) {
                var open = m.body.querySelector('tr.js-panel');
                if (open) open.remove();
            } else if (btn.classList.contains('js-do-remove') || btn.classList.contains('js-do-transfer')) {
                var isRemove = btn.classList.contains('js-do-remove');
                var sid = parseInt(btn.dataset.id, 10);
                btn.disabled = true;
                var call = isRemove
                    ? api('/admin/sections/' + section.id + '/remove-student', { student_id: sid })
                    : api('/admin/sections/' + section.id + '/transfer-student', { student_id: sid, target_section_id: parseInt(m.body.querySelector('.js-target').value, 10) });
                call.then(function (d) {
                    toast('success', isRemove ? 'Student removed from the section.' : 'Student transferred.', isRemove ? '' : ('Now in ' + (d.new_section || 'the new section') + '.'));
                    if (onChange) onChange();
                    load();
                }).catch(function (e) { btn.disabled = false; toast('error', isRemove ? 'Could not remove the student.' : 'Could not transfer the student.', e.message); });
            }
        });

        load();
    }

    // ── Add Student: every student of this grade and school year without a section ──
    function addStudent(sectionId, sectionName, enrolled, max, onChange) {
        enrolled = parseInt(enrolled, 10) || 0; max = parseInt(max, 10) || 30;
        if (enrolled >= max) { toast('warning', 'Section full', sectionName + ' already has ' + max + ' students.'); return; }
        var m = modal('secToolAddStudentModal', 'bi-person-plus-fill', 'Add Student — ' + sectionName, enrolled + '/' + max + ' enrolled');
        var students = [], context = {}, selected = null;

        m.foot.style.display = 'flex';
        m.foot.innerHTML = '<button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>' +
            '<button type="button" class="btn-dash btn-primary js-add" disabled><i class="bi bi-check-lg me-1"></i>Add Student</button>';
        var addBtn = m.foot.querySelector('.js-add');

        function render() {
            var q = (m.body.querySelector('.js-search') || {}).value || '';
            q = q.trim().toLowerCase();
            var list = students.filter(function (s) {
                return !q || [s.lrn, s.name, s.email].some(function (v) { return (v || '').toLowerCase().indexOf(q) !== -1; });
            });
            var rows = list.map(function (s) {
                var p = PAY[s.payment_status] || ['pending', 'Unpaid'];
                var on = s.id === selected;
                return '<tr data-id="' + s.id + '" style="cursor:pointer;' + (on ? 'background:#eff6ff;' : '') + '">' +
                    '<td style="font-size:12px;">' + esc(s.lrn || '—') + '</td>' +
                    '<td style="font-weight:600;">' + esc(s.name || '—') + '</td>' +
                    '<td style="font-size:12px;color:var(--muted);">' + esc(s.email || '—') + '</td>' +
                    '<td><span class="status-badge ' + p[0] + '">' + p[1] + '</span></td>' +
                    '<td style="text-align:center;"><i class="bi ' + (on ? 'bi-check-circle-fill' : 'bi-circle') + '" style="color:' + (on ? 'var(--green)' : 'var(--border)') + ';font-size:16px;"></i></td></tr>';
            }).join('');
            var elsewhere = Object.keys(context.waiting_elsewhere || {});
            var empty = '<tr><td colspan="5" style="text-align:center;padding:36px;color:var(--muted);">' +
                (students.length ? 'No students match the search.' : 'No students waiting for a section. Everyone in this grade and school year already has one.') +
                (!students.length && elsewhere.length ? '<div style="margin-top:12px;font-size:12.5px;color:#92400e;">' +
                    elsewhere.map(function (sy) { return context.waiting_elsewhere[sy] + ' student(s) are waiting in S.Y. ' + esc(sy); }).join('; ') +
                    ' — they can only join a section of their own school year.</div>' : '') + '</td></tr>';
            m.body.querySelector('tbody').innerHTML = rows || empty;
        }

        m.body.innerHTML = '<div class="dash-search" style="width:100%;max-width:320px;margin-bottom:12px;"><i class="bi bi-search"></i><input type="text" class="js-search" placeholder="Search by LRN, name or email…"></div>' +
            '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr><th>LRN</th><th>Name</th><th>Email</th><th>Payment</th><th style="text-align:center;">Select</th></tr></thead>' +
            '<tbody><tr><td colspan="5">' + loading('Loading eligible students…') + '</td></tr></tbody></table></div>';
        m.body.querySelector('.js-search').addEventListener('input', render);
        m.body.addEventListener('click', function (ev) {
            var row = ev.target.closest('tr[data-id]');
            if (!row) return;
            selected = parseInt(row.dataset.id, 10);
            addBtn.disabled = false;
            render();
        });
        addBtn.addEventListener('click', function () {
            if (!selected) return;
            addBtn.disabled = true;
            api('/admin/sections/' + encodeURIComponent(sectionId) + '/add-student', { student_id: selected }).then(function () {
                var s = students.filter(function (x) { return x.id === selected; })[0];
                students = students.filter(function (x) { return x.id !== selected; });
                selected = null;
                enrolled += 1;
                m.sub.textContent = enrolled + '/' + max + ' enrolled';
                toast('success', 'Student added', (s ? s.name + ' → ' : '') + sectionName);
                if (onChange) onChange();
                if (enrolled >= max) { m.hide(); toast('warning', 'Section full', sectionName + ' now has ' + max + ' students.'); return; }
                render();
            }).catch(function (e) { addBtn.disabled = false; toast('error', 'Could not add the student.', e.message); });
        });

        api('/admin/sections/' + encodeURIComponent(sectionId) + '/eligible-students').then(function (d) {
            context = d; students = d.students || [];
            m.sub.textContent = (GRADE[d.grade_level] || d.grade_level || '') + ' · S.Y. ' + (d.school_year || '') + ' · ' + enrolled + '/' + max + ' enrolled';
            render();
        }).catch(function (e) { m.body.querySelector('tbody').innerHTML = '<tr><td colspan="5">' + failed(e) + '</td></tr>'; });
    }

    // ── Manage Subjects: tick the subjects this section takes (subjects of its grade) ──
    function manageSubjects(sectionId, sectionName, gradeLevel, onChange) {
        var m = modal('secToolSubjectsModal', 'bi-book-fill', 'Subjects — ' + sectionName, GRADE[gradeLevel] || gradeLevel || '');
        m.body.innerHTML = loading('Loading subjects…');
        m.foot.style.display = 'flex';
        m.foot.innerHTML = '<button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn-dash btn-primary js-save" disabled><i class="bi bi-check-lg me-1"></i>Save Changes</button>';
        var saveBtn = m.foot.querySelector('.js-save');

        Promise.all([api('/admin/subjects'), api('/admin/sections/' + encodeURIComponent(sectionId))]).then(function (res) {
            var all = res[0].subjects || [];
            var assigned = {};
            (res[1].subjects || []).forEach(function (s) { assigned[s.id] = true; });
            var list = all.filter(function (s) { return !gradeLevel || !s.grade_level || s.grade_level === gradeLevel; });
            if (!list.length) {
                m.body.innerHTML = '<div style="text-align:center;padding:40px;color:var(--muted);">No subjects for ' + esc(GRADE[gradeLevel] || gradeLevel || 'this grade') + ' yet. Add them in Subject Management first.</div>';
                return;
            }
            m.body.innerHTML = '<div style="font-size:12px;color:var(--muted);margin-bottom:10px;"><i class="bi bi-info-circle me-1"></i>Ticked subjects are taken by this section.</div>' +
                '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr><th style="width:50px;text-align:center;">Take</th><th>Code</th><th>Subject</th><th>Status</th></tr></thead><tbody>' +
                list.map(function (s) {
                    return '<tr><td style="text-align:center;"><input type="checkbox" class="form-check-input js-subj" value="' + s.id + '"' + (assigned[s.id] ? ' checked' : '') + '></td>' +
                        '<td style="font-weight:600;">' + esc(s.code || '—') + '</td><td>' + esc(s.name) + '</td>' +
                        '<td><span class="status-badge ' + (s.is_active ? 'active' : 'inactive') + '">' + (s.is_active ? 'Active' : 'Inactive') + '</span></td></tr>';
                }).join('') + '</tbody></table></div>';
            saveBtn.disabled = false;
        }).catch(function (e) { m.body.innerHTML = failed(e); });

        saveBtn.addEventListener('click', function () {
            var ids = Array.prototype.map.call(m.body.querySelectorAll('.js-subj:checked'), function (cb) { return { id: parseInt(cb.value, 10) }; });
            if (!ids.length) { toast('warning', 'Pick at least one subject.'); return; }
            saveBtn.disabled = true;
            api('/admin/sections/' + encodeURIComponent(sectionId) + '/subjects', { subjects: ids }).then(function () {
                m.hide();
                toast('success', 'Subjects saved', sectionName + ': ' + ids.length + ' subject(s).');
                if (onChange) onChange();
            }).catch(function (e) { saveBtn.disabled = false; toast('error', 'Could not save the subjects.', e.message); });
        });
    }

    // ── Auto-Assign: every enrolled student without a section, into a section of their grade with room ──
    function autoAssign(onChange) {
        var run = function () {
            api('/admin/sections/auto-assign', {}).then(function (d) {
                toast('success', 'Auto-assign complete', d.message || 'Students assigned.');
                if (onChange) onChange();
            }).catch(function (e) { toast('error', 'Auto-assign failed.', e.message); });
        };
        var msg = 'Put every enrolled student who has no section yet into an available section of their grade and school year (by capacity). Students already in a section are not moved.';
        if (typeof window.saConfirm === 'function') {
            window.saConfirm({ title: 'Auto-Assign Students', msg: msg, icon: 'bi-magic', btnLabel: 'Auto-Assign', onConfirm: run });
        } else if (window.confirm(msg)) {
            run();
        }
    }

    window.ilcSectionTools = { viewStudents: viewStudents, addStudent: addStudent, manageSubjects: manageSubjects, autoAssign: autoAssign };
})();
