/*
 * Automatic logout after inactivity, on every portal page (included by
 * resources/views/partials/idle-logout.blade.php).
 *
 * "Inactive" = the user does nothing (no mouse, key, click, scroll, touch) — not "no server
 * requests": several pages poll the server in the background, which would otherwise keep a
 * session alive forever. The limit is Admin › Settings › Security › Session Timeout.
 *
 *  - 60 s before the limit: a dialog with a countdown and "Stay logged in". It stays until
 *    that button is clicked — moving the mouse doesn't dismiss it.
 *  - At the limit: POST /session/expire (logs out + audit entry), then this portal's login
 *    form with a "You have been logged out" notice in front of it (/session-expired).
 *  - All open tabs share one timer (localStorage), so working in one keeps the others in.
 *  - While active, the server session is refreshed (GET /session/ping) at most once a minute,
 *    so it never expires under someone who is working.
 *  - A request answered 419 / 401 (session already gone) goes to the same page.
 */
(function () {
    var el = document.currentScript;
    if (!el || window.__ilcIdleLogout) return;
    window.__ilcIdleLogout = true;

    var LIMIT_MS   = Math.max(1, parseInt(el.dataset.minutes, 10) || 15) * 60000;
    var WARN_MS    = Math.min(60000, LIMIT_MS / 2);
    var PING_EVERY = 60000;
    var expireUrl  = el.dataset.expireUrl;
    var pingUrl    = el.dataset.pingUrl;
    var expiredUrl = el.dataset.expiredUrl;
    var csrf       = el.dataset.csrf || '';
    var K_ACTIVE = 'ilc_idle_last_active', K_PING = 'ilc_idle_last_ping', K_OUT = 'ilc_idle_logged_out';

    var store = {
        get: function (k) { try { return parseInt(localStorage.getItem(k), 10) || 0; } catch (e) { return 0; } },
        set: function (k, v) { try { localStorage.setItem(k, String(v)); } catch (e) {} }
    };
    var loadedAt = Date.now(), localActive = loadedAt, localPing = loadedAt, leaving = false;
    // Instant "logged out" message to the other windows/tabs of this browser (all share one login)
    var channel = null;
    try { if (typeof BroadcastChannel !== 'undefined') channel = new BroadcastChannel('ilc-idle-logout'); } catch (e) {}
    if (channel) channel.onmessage = function (e) { if (e && e.data === 'logged-out') goToExpiredPage(); };
    store.set(K_ACTIVE, localActive);  // this page load is activity
    store.set(K_PING, localPing);      // ... and refreshed the server session

    function lastActive() { return Math.max(localActive, store.get(K_ACTIVE)); }

    function ping() {
        localPing = Date.now();
        store.set(K_PING, localPing);
        try {
            nativeFetch(pingUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(function (res) { if (res.status === 401 || res.status === 419) goToExpiredPage(); }, function () {});
        } catch (e) {}
    }

    var lastWrite = 0;
    function warningVisible() { return !!dialog && dialog.style.display !== 'none'; }

    // Mouse/keyboard/scroll before the warning resets the timer. Once the warning is showing
    // it is ignored — only "Stay logged in" confirms the user is really there (stay()).
    function activity() {
        if (leaving || warningVisible()) return;
        markActive();
    }
    function markActive(force) {
        var now = Date.now();
        localActive = now;
        if (force || now - lastWrite > 5000) { lastWrite = now; store.set(K_ACTIVE, now); }
        if (force || now - Math.max(localPing, store.get(K_PING)) > PING_EVERY) ping();
    }
    function stay() {
        if (leaving) return;
        markActive(true); // tell the server and the other tabs right away
        hideWarning();
    }
    ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'wheel'].forEach(function (ev) {
        window.addEventListener(ev, activity, { passive: true, capture: true });
    });

    // ── warning dialog (plain DOM — works on every portal, with or without Bootstrap) ──
    var dialog = null, countEl = null;
    function showWarning(secondsLeft) {
        if (!dialog) {
            dialog = document.createElement('div');
            dialog.id = 'ilc-idle-warning';
            dialog.setAttribute('role', 'alertdialog');
            dialog.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:2147483000;display:none;align-items:center;justify-content:center;padding:20px;';
            dialog.innerHTML =
                '<div style="background:#fff;border-radius:16px;max-width:380px;width:100%;padding:26px 24px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.3);font-family:inherit;">' +
                  '<div style="width:52px;height:52px;border-radius:50%;background:#fff8ec;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:26px;color:#b45309;">&#9201;</div>' +
                  '<div style="font-size:16px;font-weight:700;color:#1a3a6c;margin-bottom:6px;">Are you still there?</div>' +
                  '<div style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:18px;">For your security you will be logged out in <strong id="ilc-idle-count" style="color:#b91c1c;">60</strong> seconds because of inactivity.</div>' +
                  '<button type="button" id="ilc-idle-stay" style="width:100%;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,#1a3a6c,#1e5799);cursor:pointer;">Stay logged in</button>' +
                  '<button type="button" id="ilc-idle-out" style="width:100%;border:none;background:none;margin-top:8px;padding:6px;font-size:12.5px;color:#64748b;cursor:pointer;">Log out now</button>' +
                '</div>';
            document.body.appendChild(dialog);
            countEl = dialog.querySelector('#ilc-idle-count');
            dialog.querySelector('#ilc-idle-stay').addEventListener('click', stay);
            dialog.querySelector('#ilc-idle-out').addEventListener('click', function () { expire(); });
        }
        countEl.textContent = String(Math.max(0, secondsLeft));
        if (dialog.style.display !== 'flex') {
            dialog.style.display = 'flex';
            try { dialog.querySelector('#ilc-idle-stay').focus(); } catch (e) {} // Enter = stay
        }
    }
    function hideWarning() { if (dialog) dialog.style.display = 'none'; }

    function goToExpiredPage() {
        if (leaving) return;
        leaving = true;
        window.location.replace(expiredUrl);
    }

    function expire() {
        if (leaving) return;
        store.set(K_OUT, Date.now()); // other tabs follow (storage event / next tick)
        try { if (channel) channel.postMessage('logged-out'); } catch (e) {}
        var body = new URLSearchParams({ _token: csrf });
        var done = function () { goToExpiredPage(); };
        try {
            nativeFetch(expireUrl, {
                method: 'POST', credentials: 'same-origin', keepalive: true, body: body,
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
            }).then(done, done);
        } catch (e) { done(); }
        setTimeout(done, 3000); // never hang on a slow network
    }

    function tick() {
        if (leaving) return;
        // Another window/tab logged out after this page was opened → this one is logged out too
        // (one shared login), even if the mouse has been moving here since
        if (store.get(K_OUT) > loadedAt) return goToExpiredPage();
        var idle = Date.now() - lastActive();
        if (idle >= LIMIT_MS) return expire();
        if (idle >= LIMIT_MS - WARN_MS) showWarning(Math.ceil((LIMIT_MS - idle) / 1000));
        else hideWarning();
    }
    setInterval(tick, 1000);
    window.addEventListener('storage', function (e) {
        if (e.key === K_OUT && parseInt(e.newValue, 10) > loadedAt) return goToExpiredPage();
        if (e.key === K_ACTIVE) tick();
    });
    // Background tabs get their timers slowed or frozen — check again the moment the tab is back
    // (the server also refuses a request that comes after the limit: EnforceIdleTimeout)
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') tick(); });
    window.addEventListener('focus', tick);
    window.addEventListener('pageshow', tick);

    // ── a request answered "session gone" (419, or 401 to a JSON request) → same page ──
    var nativeFetch = window.fetch.bind(window);
    window.fetch = function (input, init) {
        return nativeFetch(input, init).then(function (res) {
            try {
                var url = new URL(typeof input === 'string' ? input : input.url, location.href);
                if (url.origin === location.origin && url.pathname.indexOf('/session/') !== 0
                    && (res.status === 419 || res.status === 401)) {
                    goToExpiredPage();
                }
            } catch (e) {}
            return res;
        });
    };

    // For tests / debugging
    window.ilcIdleLogout = { tick: tick, limitMs: LIMIT_MS, warnMs: WARN_MS };
})();
