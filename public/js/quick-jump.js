/**
 * Top-bar "Go to…" quick jump (Admin, Super Admin, Teacher).
 *
 * It does not filter lists — every page keeps its own search box for that. It only gets you
 * somewhere fast: a page from the sidebar, or a role-specific item (a student for Admin /
 * Super Admin, a subject's grade sheet for a Teacher).
 *
 *   ilcQuickJump.init({
 *     input:       <input> in the top bar,
 *     show:        name => showSection(name),               // how this dashboard opens a page
 *     search:      async q => [{ icon, label, hint, run }]  // optional extra results (debounced)
 *     searchLabel: 'Students',                               // heading for those results
 *   });
 *
 * Pages are read from the sidebar's showSection('…') links, so the list always matches
 * what this user can open. Keys: ↑ ↓ Enter Esc, and Ctrl+K (⌘K) focuses the box.
 */
(function () {
    var STYLE = '' +
        '.qj-panel{position:absolute;top:calc(100% + 6px);left:0;right:0;min-width:320px;background:#fff;border:1px solid #e5e7eb;' +
        'border-radius:12px;box-shadow:0 10px 32px rgba(0,0,0,.14);z-index:2000;max-height:420px;overflow-y:auto;padding:6px 0;display:none;}' +
        '.qj-panel.open{display:block;}' +
        '.qj-head{font-size:10.5px;font-weight:700;color:var(--muted,#888);text-transform:uppercase;letter-spacing:.6px;padding:8px 14px 4px;}' +
        '.qj-item{display:flex;align-items:center;gap:10px;padding:8px 14px;cursor:pointer;font-size:13px;color:var(--text,#222);}' +
        '.qj-item i.qj-ic{width:26px;height:26px;border-radius:7px;background:var(--blue-pale,#e8f0fb);color:var(--blue,#1a3a6c);' +
        'display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}' +
        '.qj-item .qj-tx{min-width:0;flex:1;}' +
        '.qj-item .qj-lb{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}' +
        '.qj-item .qj-lb mark{background:#fff3c4;color:inherit;padding:0;border-radius:2px;}' +
        '.qj-item .qj-hn{font-size:11px;color:var(--muted,#888);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}' +
        '.qj-item.active,.qj-item:hover{background:var(--blue-pale,#e8f0fb);}' +
        '.qj-empty{padding:14px;font-size:12.5px;color:var(--muted,#888);text-align:center;}' +
        '.qj-foot{border-top:1px solid #f0f0f0;margin-top:4px;padding:7px 14px 3px;font-size:10.5px;color:#aaa;}' +
        '.qj-kbd{font-size:10px;font-weight:700;color:#999;border:1px solid #ddd;border-radius:5px;padding:1px 6px;margin-left:auto;flex-shrink:0;background:#fafafa;}';

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function highlight(text, q) {
        var i = q ? String(text).toLowerCase().indexOf(q) : -1;
        if (i < 0) return esc(text);
        return esc(text.slice(0, i)) + '<mark>' + esc(text.slice(i, i + q.length)) + '</mark>' + esc(text.slice(i + q.length));
    }

    // The sidebar heading a link sits under (MAIN, MANAGEMENT…), only within its own group of links
    function groupOf(el) {
        for (var p = el.previousElementSibling; p; p = p.previousElementSibling) {
            if (p.classList.contains('sidebar-section-lbl')) return p.textContent.trim();
        }
        return '';
    }

    // Sidebar → [{ icon, label, hint, name }]
    function sidebarPages() {
        var out = [], seen = {};
        document.querySelectorAll('.dash-sidebar [onclick*="showSection("]').forEach(function (el) {
            var group = groupOf(el);
            var m = (el.getAttribute('onclick') || '').match(/showSection\('([\w-]+)'/);
            if (!m || seen[m[1]] || el.style.display === 'none' || el.closest('[style*="display:none"],[style*="display: none"]')) return;
            var label = '';
            el.childNodes.forEach(function (n) { if (n.nodeType === 3) label += n.textContent; });
            label = label.replace(/\s+/g, ' ').trim();
            if (!label) return;
            var ic = el.querySelector('i.bi');
            seen[m[1]] = true;
            out.push({ name: m[1], label: label, hint: group ? group.charAt(0) + group.slice(1).toLowerCase() : '',
                       icon: ic ? ic.className.match(/bi-[\w-]+/g).filter(function (c) { return c !== 'bi'; })[0] : 'bi-arrow-right-circle' });
        });
        return out;
    }

    function init(opts) {
        var input = opts.input;
        if (!input || input._qj) return;
        input._qj = true;
        if (!document.getElementById('qj-style')) {
            var st = document.createElement('style'); st.id = 'qj-style'; st.textContent = STYLE; document.head.appendChild(st);
        }
        var box = input.parentElement;
        box.style.position = 'relative';
        input.setAttribute('autocomplete', 'off');
        input.setAttribute('aria-label', input.placeholder);
        var kbd = document.createElement('span');
        kbd.className = 'qj-kbd';
        kbd.textContent = /Mac|iPhone|iPad/.test(navigator.platform) ? '⌘K' : 'Ctrl K';
        box.appendChild(kbd);
        var panel = document.createElement('div');
        panel.className = 'qj-panel';
        box.appendChild(panel);

        var pages = null, items = [], active = 0, timer = null, seq = 0;

        function render(pageHits, extra, q, loading) {
            items = pageHits.map(function (p) {
                return { icon: p.icon, label: p.label, hint: p.hint, run: function () { opts.show(p.name); } };
            }).concat(extra || []);
            var html = '', idx = 0;
            function row(it) {
                return '<div class="qj-item' + (idx === active ? ' active' : '') + '" data-i="' + (idx++) + '">' +
                    '<i class="bi ' + esc(it.icon) + ' qj-ic"></i><div class="qj-tx"><div class="qj-lb">' + highlight(it.label, q) + '</div>' +
                    (it.hint ? '<div class="qj-hn">' + esc(it.hint) + '</div>' : '') + '</div></div>';
            }
            if (pageHits.length) {
                html += '<div class="qj-head">Pages</div>';
                pageHits.forEach(function (p) { html += row({ icon: p.icon, label: p.label, hint: p.hint }); });
            }
            if (opts.search && q.length >= 2) {
                html += '<div class="qj-head">' + esc(opts.searchLabel || 'Results') + '</div>';
                if (loading) html += '<div class="qj-empty">Searching…</div>';
                else if (extra && extra.length) extra.forEach(function (it) { html += row(it); });
                else html += '<div class="qj-empty">No matches.</div>';
            }
            if (!pageHits.length && !(opts.search && q.length >= 2)) html += '<div class="qj-empty">No pages match. ' + (opts.search ? 'Type 2+ letters to search ' + esc((opts.searchLabel || '').toLowerCase()) + '.' : '') + '</div>';
            html += '<div class="qj-foot">↑ ↓ to move · Enter to open · Esc to close</div>';
            panel.innerHTML = html;
            panel.classList.add('open');
        }

        function update() {
            var q = input.value.trim().toLowerCase();
            pages = pages || sidebarPages();
            var hits = pages.filter(function (p) { return !q || (p.label + ' ' + p.name.replace(/-/g, ' ')).toLowerCase().indexOf(q) > -1; })
                .sort(function (a, b) { return (a.label.toLowerCase().indexOf(q) === 0 ? 0 : 1) - (b.label.toLowerCase().indexOf(q) === 0 ? 0 : 1); });
            if (!q) hits = hits.slice(0, 8);
            active = 0;
            clearTimeout(timer);
            if (!opts.search || q.length < 2) { render(hits, [], q, false); return; }
            render(hits, [], q, true);
            var my = ++seq;
            timer = setTimeout(function () {
                Promise.resolve(opts.search(q)).then(function (extra) {
                    if (my === seq) render(hits, (extra || []).slice(0, 6), q, false);
                }).catch(function () { if (my === seq) render(hits, [], q, false); });
            }, 250);
        }

        function open(i) {
            var it = items[i];
            if (!it) return;
            close();
            input.value = '';
            input.blur();
            it.run();
        }
        function close() { panel.classList.remove('open'); seq++; clearTimeout(timer); }
        function move(d) {
            if (!items.length) return;
            active = (active + d + items.length) % items.length;
            panel.querySelectorAll('.qj-item').forEach(function (el) { el.classList.toggle('active', +el.dataset.i === active); });
            var cur = panel.querySelector('.qj-item.active');
            if (cur) cur.scrollIntoView({ block: 'nearest' });
        }

        input.addEventListener('input', update);
        input.addEventListener('focus', update);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
            else if (e.key === 'Enter') { e.preventDefault(); open(active); }
            else if (e.key === 'Escape') { close(); input.blur(); }
        });
        panel.addEventListener('mousedown', function (e) {
            var el = e.target.closest('.qj-item');
            if (el) { e.preventDefault(); open(+el.dataset.i); }
        });
        document.addEventListener('mousedown', function (e) { if (!box.contains(e.target)) close(); });
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) { e.preventDefault(); input.focus(); input.select(); }
        });
    }

    window.ilcQuickJump = { init: init, esc: esc };
})();
