/*
 * Clickable stat cards (Admin + Super Admin dashboards) — the same idea as Student Management's cards:
 * clicking a card filters the list it counts and scrolls to that list. Each card describes itself:
 *
 *   <div class="stat-card stat-card-link" data-jump='{"set":{"#statusFilter":"pending"},"to":"#enrollment-list"}'>
 *
 *   section  open this dashboard section first (e.g. a Reports card that opens Payments)
 *   set      { "selector": "value" } — sets the section's own filter controls and fires their
 *            change/input event, so each section's existing filter code does the filtering
 *   click    selector of a filter button to click (e.g. Messages' status chips)
 *   href     a URL to go to instead, for lists filtered by the page address (page reloads)
 *   to       selector of the list/table to scroll to (re-found after the section reloads; also
 *            remembered across a full page load, for filters that are plain links)
 *   active   true/false when the server already knows whether this card's filter is on
 *
 * A card whose "set" values match the current filters gets the .active outline. Cards without
 * "set" (e.g. totals that only scroll to the list) never show as active.
 */
(function () {
    function parse(card) {
        try { return JSON.parse(card.getAttribute('data-jump')) || {}; } catch (e) { return {}; }
    }
    function visibleSection() {
        return Array.prototype.find.call(document.querySelectorAll('.dash-section'), function (s) {
            return s.style.display !== 'none' && s.offsetParent !== null;
        });
    }
    // Resolves once fn() returns something, or after `ms`
    function waitFor(fn, ms) {
        return new Promise(function (resolve) {
            var t0 = Date.now();
            (function poll() {
                var v = fn();
                if (v || Date.now() - t0 > ms) return resolve(v);
                setTimeout(poll, 80);
            })();
        });
    }
    // Resolves when `root` has had no DOM changes for 250ms (a filter re-rendered or reloaded it)
    function settled(root) {
        return new Promise(function (resolve) {
            var quiet, stop = setTimeout(done, 3000);
            var mo = new MutationObserver(function () { clearTimeout(quiet); quiet = setTimeout(done, 250); });
            function done() { mo.disconnect(); clearTimeout(stop); clearTimeout(quiet); resolve(); }
            mo.observe(root, { childList: true, subtree: true });
            quiet = setTimeout(done, 400);
        });
    }
    function fire(el) {
        el.dispatchEvent(new Event(el.tagName === 'SELECT' ? 'change' : 'input', { bubbles: true }));
        if (el.tagName !== 'SELECT') {
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new KeyboardEvent('keyup', { bubbles: true })); // some search boxes filter on keyup
        }
    }

    function syncActive(scope) {
        (scope || document).querySelectorAll('[data-jump]').forEach(function (card) {
            var spec = parse(card), keys = spec.set ? Object.keys(spec.set) : [];
            if (typeof spec.active === 'boolean') { card.classList.toggle('active', spec.active); return; }
            var on = keys.length > 0 && keys.every(function (sel) {
                var el = document.querySelector(sel);
                return el && el.value === spec.set[sel];
            });
            if (spec.click) { var b = document.querySelector(spec.click); on = !!(b && b.classList.contains('active')); }
            card.classList.toggle('active', on);
        });
    }

    async function jump(card) {
        var spec = parse(card);
        // Filters that live in the page address (e.g. Super Admin › User Management): go there, then
        // scroll to the list once the page is back (see the load handler below)
        if (spec.href) {
            if (spec.to) try { sessionStorage.setItem('ilcStatJumpTo', JSON.stringify({ to: spec.to, at: Date.now() })); } catch (e) {}
            window.location.href = spec.href;
            return;
        }
        if (spec.section) {
            var target = document.getElementById('section-' + spec.section);
            var opening = target && (target.style.display === 'none' || target.offsetParent === null) && typeof window.showSection === 'function';
            var loaded = opening ? settled(target) : null;
            if (opening) window.showSection(spec.section);
            var first = spec.set ? Object.keys(spec.set)[0] : (spec.click || spec.to);
            if (first) await waitFor(function () { return document.querySelector(first); }, 6000);
            // Let the section's own first load finish, so it can't land after (and undo) the filtered one
            if (loaded) await loaded;
        }
        var root = (spec.section && document.getElementById('section-' + spec.section)) || card.closest('.dash-section') || visibleSection() || document.body;
        var watch = settled(root);
        // Set every value first, then fire ONE change event (on the last control that changed): each
        // section's filter code reads all its controls at once, and firing per control would start
        // several overlapping reloads in the sections that reload from the server
        var changed = false, last = null, lastSel = null;
        Object.keys(spec.set || {}).forEach(function (sel) {
            var el = document.querySelector(sel);
            if (!el || el.value === spec.set[sel]) return;
            el.value = spec.set[sel];
            last = el; lastSel = sel;
        });
        if (last) { fire(last); changed = true; }
        // Just opened the section: its own first load may still be on the way and land after ours,
        // so once ours settles, load once more with the same filters to be sure they win (the
        // control is found again — a reloaded section has new elements)
        if (last && loaded) {
            await watch;
            var again = document.querySelector(lastSel);
            if (again) {
                Object.keys(spec.set).forEach(function (sel) { var el = document.querySelector(sel); if (el) el.value = spec.set[sel]; });
                watch = settled(root);
                fire(again);
            }
        }
        if (spec.click) {
            var btn = document.querySelector(spec.click);
            if (btn) {
                // A plain link reloads the page: remember where to scroll once it's back
                if (spec.to) try { sessionStorage.setItem('ilcStatJumpTo', JSON.stringify({ to: spec.to, at: Date.now() })); } catch (e) {}
                btn.click();
                changed = true;
            }
        }
        if (changed) await watch;
        syncActive(document);
        scrollTo(spec.to);
    }
    function scrollTo(sel) {
        var list = sel && document.querySelector(sel);
        if (!list) return;
        // Stop below the dashboard's sticky top bar instead of under it
        var bar = document.querySelector('.dash-topbar');
        if (!list.style.scrollMarginTop) list.style.scrollMarginTop = ((bar ? bar.offsetHeight : 62) + 16) + 'px';
        list.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    // Back from a card whose filter was a plain link
    window.addEventListener('load', function () {
        var saved = null;
        try { saved = JSON.parse(sessionStorage.getItem('ilcStatJumpTo')); sessionStorage.removeItem('ilcStatJumpTo'); } catch (e) {}
        if (saved && Date.now() - saved.at < 15000) setTimeout(function () { scrollTo(saved.to); }, 300);
    });

    document.addEventListener('click', function (e) {
        var card = e.target.closest('[data-jump]');
        if (!card || e.target.closest('a[href]:not([href="#"]), button, input, select, textarea')) return;
        e.preventDefault();
        jump(card);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var card = e.target.closest && e.target.closest('[data-jump]');
        if (card) { e.preventDefault(); jump(card); }
    });
    // Any filter change (by hand or by a card) moves the outline to the matching card
    document.addEventListener('change', function () { setTimeout(syncActive, 0); });
    new MutationObserver(function () { clearTimeout(window._statJumpSync); window._statJumpSync = setTimeout(syncActive, 150); })
        .observe(document.documentElement, { childList: true, subtree: true });

    window.ilcStatJump = { jump: jump, sync: syncActive };
})();
