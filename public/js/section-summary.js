/*
 * Section Summary window, shared by Admin and Super Admin Section Management.
 * ilcSectionSummary.open(sectionId, sectionName) loads /admin/sections/{id}/summary and
 * shows totals (boys/girls, student type, payments, documents) plus the class roster.
 * Built from the dashboards' own pieces (stat-card, dash-table, status-badge, form-fld) so it
 * looks the same as Student Management › Transaction History.
 */
(function () {
    'use strict';

    var TYPE = { 'new': 'New', 'returning': 'Returning', 'transferee': 'Transferee' };
    // Admin's payment badges (paid/partial/unpaid); Super Admin only has active/pending/inactive
    var PAY = {
        paid:    ['active paid', 'Fully Paid'],
        partial: ['pending partial', 'Partially Paid'],
        unpaid:  ['inactive unpaid', 'No Payment']
    };
    var last = null; // last loaded summary, for the roster filter

    function esc(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function peso(v) {
        return '₱' + Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Same header and layout as the Transaction History window
    function ensureModal() {
        var m = document.getElementById('sectionSummaryModal');
        if (m) return m;
        document.body.insertAdjacentHTML('beforeend',
            '<div class="modal fade" id="sectionSummaryModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">' +
            '<div class="modal-content" style="border-radius:16px; overflow:hidden; border:none;">' +
            '<div class="modal-header" style="background:linear-gradient(135deg, #0056b3, #003d82); padding:18px 24px; border:none;">' +
            '<div style="display:flex; align-items:center; gap:12px;">' +
            '<div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-clipboard-data" style="font-size:18px; color:#fff;"></i></div>' +
            '<div><h5 class="modal-title" style="color:#fff; font-weight:700; margin:0; font-size:16px;">Section Summary — <span id="secSumTitle"></span></h5>' +
            '<div id="secSumSubtitle" style="color:rgba(255,255,255,.8); font-size:12px;"></div></div></div>' +
            '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>' +
            '<div class="modal-body" id="secSumBody" style="padding:20px 24px; background:#fff;"></div>' +
            '</div></div></div>');
        return document.getElementById('sectionSummaryModal');
    }

    // Same tile as Transaction History's summary row
    function tile(icon, color, value, label, sub) {
        return '<div class="col-md-3 col-6"><div class="stat-card h-100" style="padding:14px 16px;">' +
            '<div class="stat-icon ' + color + '" style="width:40px;height:40px;"><i class="bi ' + icon + '"></i></div>' +
            '<div><div class="stat-value" style="font-size:18px;">' + value + '</div><div class="stat-label">' + label + '</div>' +
            (sub ? '<div style="font-size:11px;color:var(--muted);">' + sub + '</div>' : '') + '</div></div></div>';
    }

    function rosterRows(students, filter) {
        var list = students.filter(function (s) {
            if (filter === 'balance') return s.payment !== 'paid';
            if (filter === 'docs') return !s.docs_complete;
            return true;
        });
        if (!list.length) {
            return '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>' +
                (students.length ? 'No students match this filter.' : 'No students in this section yet.') + '</td></tr>';
        }
        var req = last ? last.required_docs : 4;
        return list.map(function (s, i) {
            var p = PAY[s.payment] || PAY.unpaid;
            var docColor = s.docs_complete ? 'var(--green)' : 'var(--red)';
            return '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td style="font-size:12px;">' + esc(s.lrn || '—') + '</td>' +
                '<td>' + esc(s.name) + '</td>' +
                '<td>' + (s.gender ? esc(s.gender.charAt(0).toUpperCase() + s.gender.slice(1)) : '—') + '</td>' +
                '<td>' + esc(TYPE[s.student_type] || '—') + '</td>' +
                '<td><span class="status-badge ' + p[0] + '">' + p[1] + '</span>' +
                (s.payment_plan ? '<div style="font-size:11px;color:var(--muted);">Plan ' + esc(s.payment_plan) + '</div>' : '') + '</td>' +
                '<td style="text-align:right;font-weight:700;white-space:nowrap;">' + (s.balance === null ? '<span style="font-weight:400;color:var(--muted);">No fee set</span>' : peso(s.balance)) + '</td>' +
                '<td style="text-align:center;font-weight:700;color:' + docColor + ';">' + s.docs_approved + '/' + req + '</td>' +
                '</tr>';
        }).join('');
    }

    function render(d) {
        var sec = d.section, t = d.totals;

        var counts =
            tile('bi-people-fill', 'blue', t.students + ' / ' + sec.max_students, 'Students',
                t.male + ' boys · ' + t.female + ' girls' + (t.gender_unknown ? ' · ' + t.gender_unknown + ' not set' : '')) +
            tile('bi-person-lines-fill', 'purple', t['new'] + ' / ' + t.returning + ' / ' + t.transferee, 'New / Returning / Transferee',
                t.type_unknown ? t.type_unknown + ' not set' : '') +
            tile('bi-cash-stack', 'green', t.paid + ' / ' + t.partial + ' / ' + t.unpaid, 'Paid / Partial / No Payment', '') +
            tile('bi-folder-check', 'teal', t.docs_complete + ' / ' + t.students, 'Documents Complete',
                t.docs_incomplete ? t.docs_incomplete + ' still missing' : '');
        var money =
            tile('bi-wallet2', 'gold', peso(t.total_fees), 'Total Tuition', 'S.Y. ' + esc(sec.school_year)) +
            tile('bi-cash-coin', 'green', peso(t.total_collected), 'Collected', 'completed payments') +
            tile('bi-hourglass-split', t.total_balance > 0 ? 'red' : 'green', peso(t.total_balance), 'Remaining Balance', 'S.Y. ' + esc(sec.school_year)) +
            tile('bi-person-badge-fill', 'blue', esc(sec.adviser || 'Unassigned'), 'Adviser', 'Room: ' + esc(sec.room || '—'));

        return '<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">' +
            '<div style="font-size:12px; color:var(--muted);"><i class="bi bi-info-circle me-1"></i>Documents = approved Birth Certificate, Form 137, Report Card and 2x2 Picture.</div>' +
            '<div style="display:flex; gap:8px; align-items:center;">' +
            '<select id="secSumFilter" class="form-fld" style="width:auto; min-width:190px; padding:7px 12px; font-size:13px;">' +
            '<option value="">All Students</option><option value="balance">With Balance</option><option value="docs">Missing Documents</option></select>' +
            '<a href="/admin/sections/' + encodeURIComponent(sec.id) + '/summary/pdf" class="action-btn view" title="Download PDF" style="width:auto; height:36px; padding:0 14px; margin:0; font-weight:600;"><i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF</a>' +
            '</div></div>' +
            '<div class="row g-3 mb-3">' + counts + '</div>' +
            '<div class="row g-3 mb-3">' + money + '</div>' +
            '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr>' +
            '<th>#</th><th>LRN</th><th>Name</th><th>Sex</th><th>Type</th><th>Payment</th><th style="text-align:right;">Balance</th><th style="text-align:center;">Documents</th>' +
            '</tr></thead><tbody id="secSumRows">' + rosterRows(d.students, '') + '</tbody></table></div>';
    }

    function open(sectionId, sectionName) {
        var modal = ensureModal();
        var body = document.getElementById('secSumBody');
        document.getElementById('secSumTitle').textContent = sectionName || '';
        document.getElementById('secSumSubtitle').textContent = 'Loading…';
        body.innerHTML = '<div style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading summary…</div>';
        bootstrap.Modal.getOrCreateInstance(modal).show();

        return fetch('/admin/sections/' + encodeURIComponent(sectionId) + '/summary', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (d) {
                last = d;
                var sec = d.section;
                document.getElementById('secSumSubtitle').textContent = sec.grade_label + ' · S.Y. ' + sec.school_year;
                body.innerHTML = render(d);
                document.getElementById('secSumFilter').addEventListener('change', function () {
                    document.getElementById('secSumRows').innerHTML = rosterRows(last.students, this.value);
                });
            })
            .catch(function () {
                document.getElementById('secSumSubtitle').textContent = '';
                body.innerHTML = '<div style="text-align:center;padding:40px;color:var(--red);">Could not load the section summary. Please try again.</div>';
            });
    }

    window.ilcSectionSummary = { open: open, render: render };
})();
