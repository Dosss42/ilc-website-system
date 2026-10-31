/*
 * Phone & tablet layout for the portal dashboards (Admin, Super Admin, Teacher, Student).
 * Loaded together with css/dashboard-mobile.css.
 *
 *  1. Slide-out sidebar — pages that don't have their own (Admin, Super Admin) get a ☰ button in the
 *     top bar and a dark backdrop, the same pattern as the Teacher and Student portals
 *     (toggleMobileSidebar / closeMobileSidebar). Choosing a menu item closes it.
 *  2. Tables as cards — on a phone every .dash-table row is shown as a small card (see the CSS).
 *     Each cell gets its column heading as data-label, so the card reads "SUBJECT  English".
 *     Tables are often redrawn by the page's own code (term switch, filters, paging), so cells are
 *     labelled again whenever a table changes. Add class "no-stack" to keep a table as it is, or
 *     "ilc-stackable" to stack a table that isn't a .dash-table ("ilc-stack-hide-dash": hide "—" cells).
 */
(function () {
    if (window.__ilcDashboardMobile) return;
    window.__ilcDashboardMobile = true;

    // ── 1. Slide-out sidebar ──
    function setUpSidebar() {
        var sidebar = document.querySelector('.dash-sidebar');
        var topbar  = document.querySelector('.dash-topbar');
        if (!sidebar || !topbar || document.querySelector('.sidebar-toggle-btn')) return; // the page has its own

        document.documentElement.classList.add('ilc-mobile-sidebar'); // turns on the sidebar/top bar rules in the CSS
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sidebar-toggle-btn';
        btn.setAttribute('aria-label', 'Menu');
        btn.innerHTML = '<i class="bi bi-list"></i>';
        topbar.insertBefore(btn, topbar.firstChild);

        var backdrop = document.getElementById('sidebarBackdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            backdrop.id = 'sidebarBackdrop';
            document.body.appendChild(backdrop);
        }

        window.toggleMobileSidebar = window.toggleMobileSidebar || function () {
            sidebar.classList.toggle('mobile-open');
            backdrop.classList.toggle('show');
        };
        window.closeMobileSidebar = window.closeMobileSidebar || function () {
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
        };
        btn.addEventListener('click', function () { window.toggleMobileSidebar(); });
        backdrop.addEventListener('click', function () { window.closeMobileSidebar(); });
        // Choosing a page closes the menu (the dashboards' showSection() doesn't know about it)
        sidebar.addEventListener('click', function (e) {
            if (e.target.closest('.sidebar-link')) window.closeMobileSidebar();
        });
    }

    // ── 2. Tables as cards ──
    function headings(table) {
        var row = table.tHead && table.tHead.rows[table.tHead.rows.length - 1];
        if (!row) return null;
        var list = [];
        Array.prototype.forEach.call(row.cells, function (th) {
            // data-label on a heading wins (for headings with extra lines, e.g. "Mon / Monday / TODAY")
            var text = th.getAttribute('data-label') || th.textContent.replace(/\s+/g, ' ').trim();
            for (var i = 0; i < (th.colSpan || 1); i++) list.push(text);
        });
        return list;
    }

    function labelTable(table) {
        if (table.classList.contains('no-stack')) return;
        var heads = headings(table);
        if (!heads || !heads.length) return;
        table.classList.add('ilc-stack');
        var dashIsEmpty = table.classList.contains('ilc-stack-hide-dash'); // timetables: "—" = no class that day
        Array.prototype.forEach.call(table.tBodies, function (body) {
            Array.prototype.forEach.call(body.rows, function (tr) {
                var col = 0;
                Array.prototype.forEach.call(tr.cells, function (td) {
                    var span = td.colSpan || 1;
                    if (span > 1 && span >= heads.length - 1) {
                        td.classList.add('ilc-full');           // "No records found", loading rows, breaks
                    } else {
                        td.setAttribute('data-label', heads[col] || '');
                        // Nothing to show (e.g. the repeated Day cell of a timetable): hide it on a phone
                        var text = td.textContent.trim();
                        var empty = (!text && !td.querySelector('input, select, button, a, img, i, svg, textarea'))
                            || (dashIsEmpty && /^[—–-]$/.test(text));
                        td.classList.toggle('ilc-empty', empty);
                    }
                    col += span;
                });
            });
        });
    }

    function labelAll() {
        document.querySelectorAll('table.dash-table, table.ilc-stackable').forEach(labelTable);
    }

    var pending = null;
    function scheduleLabel() {
        if (pending) return;
        pending = setTimeout(function () { pending = null; labelAll(); }, 120);
    }

    function start() {
        setUpSidebar();
        labelAll();
        new MutationObserver(function (records) {
            for (var i = 0; i < records.length; i++) {
                var t = records[i].target;
                if (t.nodeType === 1 && (t.tagName === 'TBODY' || t.tagName === 'TABLE' || t.tagName === 'TR' || t.querySelector && t.querySelector('table.dash-table'))) {
                    scheduleLabel();
                    return;
                }
            }
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
})();
