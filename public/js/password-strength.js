/*
 * Shared password strength meter.
 *
 *   PwStrength.attach('new-pass-id', 'confirm-pass-id', { tips: true, forSelf: true, personal: 'name email' });
 *   PwStrength.refresh('new-pass-id');   // after clearing fields with .value = '' / form.reset()
 *
 * opts.personal      string or function returning the account's name/email (pieces of it rate as Weak)
 *                    — falls back to the input's data-pw-personal attribute
 * opts.tips          show the collapsible "How to make a strong password" box
 * opts.forSelf       tips wording for changing your own password (vs. setting someone else's)
 * opts.hideWhenEmpty hide the meter until the user types (for optional "leave blank" fields)
 */
(function () {
    if (window.PwStrength) return;

    var COMMON = ['password', '12345678', '123456789', '1234567890', 'qwerty123', 'qwertyuiop',
                  'iloveyou', '11111111', 'abcd1234', 'admin123', 'password1', 'iemelif', 'learningcenter'];

    var CSS =
        '.pws-meter{margin-top:9px;text-align:left}' +
        '.pws-meter.is-hidden{display:none}' +
        '.pws-bar{display:flex;gap:5px}' +
        '.pws-bar span{flex:1;height:6px;border-radius:99px;background:#e5e7eb;transition:background .25s}' +
        '.pws-meta{display:flex;justify-content:space-between;align-items:center;margin-top:5px;font-size:11px;color:#94a3b8}' +
        '.pws-label{font-weight:700;color:#94a3b8}' +
        '.pws-meter.weak .pws-bar span:nth-child(-n+1){background:#dc2626}' +
        '.pws-meter.good .pws-bar span:nth-child(-n+2){background:#f59e0b}' +
        '.pws-meter.strong .pws-bar span:nth-child(-n+3){background:#16a34a}' +
        '.pws-meter.weak .pws-label{color:#dc2626}' +
        '.pws-meter.good .pws-label{color:#d97706}' +
        '.pws-meter.strong .pws-label{color:#16a34a}' +
        '.pws-reason{margin-top:5px;font-size:11px;font-weight:600;color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:4px 8px}' +
        '.pws-checks{list-style:none;margin:7px 0 0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:3px 10px}' +
        '.pws-checks li{font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:5px;margin:0}' +
        '.pws-checks li.ok{color:#16a34a}' +
        '.pws-match{font-size:11px;margin-top:6px;display:flex;align-items:center;gap:5px;color:#94a3b8;text-align:left}' +
        '.pws-match.ok{color:#16a34a}.pws-match.bad{color:#dc2626}' +
        '.pws-tips{background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:10px 14px;margin:14px 0;text-align:left}' +
        '.pws-tips summary{font-size:12px;font-weight:700;color:#1a3a6c;cursor:pointer;list-style:none;display:flex;align-items:center;gap:7px}' +
        '.pws-tips summary::-webkit-details-marker{display:none}' +
        '.pws-tips summary .chev{margin-left:auto;transition:transform .2s}' +
        '.pws-tips[open] summary .chev{transform:rotate(180deg)}' +
        '.pws-tips ul{margin:8px 0 0;padding-left:18px}' +
        '.pws-tips li{font-size:11.5px;color:#1e3a5f;line-height:1.55;margin-bottom:3px}' +
        '.pws-tips .example{margin-top:6px;font-size:11.5px;color:#1e3a5f;background:#fff;border:1px dashed #93c5fd;border-radius:8px;padding:6px 10px}' +
        '.pws-tips .example code{color:#15803d;font-weight:700;background:none}';

    var meters = {};

    function injectCss() {
        if (document.getElementById('pws-style')) return;
        var s = document.createElement('style');
        s.id = 'pws-style';
        s.textContent = CSS;
        document.head.appendChild(s);
    }

    function rate(pw, personal) {
        var c = {
            len:  pw.length >= 8,
            kase: /[a-z]/.test(pw) && /[A-Z]/.test(pw),
            num:  /\d/.test(pw),
            sym:  /[^A-Za-z0-9]/.test(pw),
            long: pw.length >= 12
        };
        if (!pw) return { level: '', checks: c, reason: '' };
        var lower = pw.toLowerCase();
        // Each word of the name/email (juan.delacruz@x.com -> juan, delacruz) counts as personal info.
        var parts = (personal || '').toLowerCase().split(/[^a-z0-9]+/).filter(function (p) { return p.length >= 4; });
        var common = COMMON.filter(function (w) { return lower.indexOf(w) !== -1; })[0];
        var namePart = parts.filter(function (p) { return lower.indexOf(p) !== -1; })[0];
        var reason = '';
        if (!c.len)                   reason = 'Too short — use at least 8 characters.';
        else if (common)              reason = 'Contains a very common password ("' + common + '"), which is easy to guess.';
        else if (namePart)            reason = 'Contains "' + namePart + '" from the account\'s name or email, which is easy to guess.';
        else if (/^(.)\1+$/.test(pw)) reason = 'Uses the same character repeated.';
        if (reason) return { level: 'weak', checks: c, reason: reason };
        var points = 1 + (c.kase ? 1 : 0) + (c.num ? 1 : 0) + (c.sym ? 1 : 0) + (c.long ? 1 : 0);
        var level = points >= 4 ? 'strong' : (points === 3 ? 'good' : 'weak');
        return { level: level, checks: c, reason: level === 'weak' ? 'Add uppercase letters, numbers or symbols to make it stronger.' : '' };
    }

    // The visual box around the input: an icon/eye wrapper (positioned, no label inside) or the input itself.
    function anchorOf(el) {
        var p = el.parentElement;
        if (p && !p.querySelector('label') && window.getComputedStyle(p).position === 'relative') return p;
        return el;
    }

    function byId(x) { return typeof x === 'string' ? document.getElementById(x) : x; }

    function attach(passRef, confirmRef, opts) {
        opts = opts || {};
        var pass = byId(passRef);
        if (!pass || pass.dataset.pwMeter) return;
        pass.dataset.pwMeter = '1';
        injectCss();
        var confirm = confirmRef ? byId(confirmRef) : null;

        var meter = document.createElement('div');
        meter.className = 'pws-meter';
        meter.innerHTML =
            '<div class="pws-bar"><span></span><span></span><span></span></div>' +
            '<div class="pws-meta"><span>Password strength</span><span class="pws-label">—</span></div>' +
            '<div class="pws-reason" style="display:none"></div>' +
            '<ul class="pws-checks">' +
                '<li data-k="len"><i class="bi bi-circle"></i> At least 8 characters</li>' +
                '<li data-k="kase"><i class="bi bi-circle"></i> Upper &amp; lowercase</li>' +
                '<li data-k="num"><i class="bi bi-circle"></i> A number</li>' +
                '<li data-k="sym"><i class="bi bi-circle"></i> A symbol (!@#$?)</li>' +
                '<li data-k="long"><i class="bi bi-circle"></i> 12+ characters (bonus)</li>' +
            '</ul>';
        anchorOf(pass).insertAdjacentElement('afterend', meter);

        var match = null;
        if (confirm) {
            match = document.createElement('div');
            match.className = 'pws-match';
            anchorOf(confirm).insertAdjacentElement('afterend', match);
        }

        if (opts.tips) {
            var tips = document.createElement('details');
            tips.className = 'pws-tips';
            tips.innerHTML =
                '<summary><i class="bi bi-lightbulb-fill"></i> How to make a strong password <i class="bi bi-chevron-down chev"></i></summary>' +
                '<ul>' +
                    '<li>Use <strong>12 or more characters</strong> — length matters most.</li>' +
                    '<li>Mix <strong>UPPERCASE</strong>, <strong>lowercase</strong>, <strong>numbers</strong> and <strong>symbols</strong>.</li>' +
                    '<li>Try a <strong>passphrase</strong>: 3–4 unrelated words joined together.</li>' +
                    '<li>Avoid names, birthdays, student numbers, or the school\'s name.</li>' +
                    '<li>Avoid common ones like <em>password123</em> or <em>12345678</em>.</li>' +
                    (opts.forSelf
                        ? '<li>Don\'t reuse a password from another site, and never share it.</li>'
                        : '<li>Share the new password with the user privately, and ask them to change it after logging in.</li>') +
                '</ul>' +
                '<div class="example">Example: <code>Mango!Bike7Cloud</code> <span style="color:#6b7280;">(make your own)</span></div>';
            var last = confirm || pass;
            // Use a .row only when both fields sit side by side in it inside an actual form — never an outer layout row.
            var row = last.closest('.row');
            var form = last.closest('form');
            if (row && !(form && row.contains(pass) && row.closest('form') === form)) row = null;
            var group = row || anchorOf(last).parentElement;
            group.insertAdjacentElement('afterend', tips);
        }

        var update = function () {
            var personal = typeof opts.personal === 'function' ? opts.personal() : (opts.personal || pass.dataset.pwPersonal || '');
            var r = rate(pass.value, personal);
            meter.className = 'pws-meter' + (r.level ? ' ' + r.level : '') + (opts.hideWhenEmpty && !pass.value ? ' is-hidden' : '');
            meter.querySelector('.pws-label').textContent = { weak: 'Weak', good: 'Good', strong: 'Strong' }[r.level] || '—';
            var reasonEl = meter.querySelector('.pws-reason');
            reasonEl.textContent = r.reason;
            reasonEl.style.display = r.reason ? '' : 'none';
            var lis = meter.querySelectorAll('.pws-checks li');
            for (var i = 0; i < lis.length; i++) {
                var ok = !!r.checks[lis[i].getAttribute('data-k')];
                lis[i].classList.toggle('ok', ok);
                lis[i].querySelector('i').className = ok ? 'bi bi-check-circle-fill' : 'bi bi-circle';
            }
            if (match) {
                if (!confirm.value) {
                    match.className = 'pws-match';
                    match.innerHTML = '<i class="bi bi-circle"></i> Must match the password above.';
                } else if (confirm.value === pass.value) {
                    match.className = 'pws-match ok';
                    match.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match.';
                } else {
                    match.className = 'pws-match bad';
                    match.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords don\'t match yet.';
                }
            }
        };
        pass.addEventListener('input', update);
        if (confirm) confirm.addEventListener('input', update);
        meters[pass.id || passRef] = update;
        update();
    }

    function refresh(passRef) {
        var key = typeof passRef === 'string' ? passRef : (passRef && passRef.id);
        if (meters[key]) meters[key]();
    }

    window.PwStrength = { attach: attach, refresh: refresh, rate: rate };
})();
