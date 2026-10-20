<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — IEMELIF Learning Center</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px 16px;
        }
        .bg-image {
            position: fixed; inset: 0;
            background: url('/images/bg.png') center/cover no-repeat;
            filter: brightness(0.32) blur(3px);
            z-index: 0;
        }
        .auth-wrapper { position: relative; z-index: 1; width: 100%; max-width: 420px; }
        .auth-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.08);
        }
        .card-header-accent {
            background: linear-gradient(135deg, #0d2147 0%, #1a3a6c 60%, #1e5799 100%);
            padding: 34px 36px 28px; text-align: center; position: relative; overflow: hidden;
        }
        .logo-circle {
            width: 72px; height: 72px; border-radius: 50%; overflow: hidden;
            background: #fff; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .logo-circle img { width: 92%; height: 92%; object-fit: contain; }
        .card-header-accent h2 {
            font-size: 17px; font-weight: 800; color: #fff; letter-spacing: 1.5px;
            text-transform: uppercase; margin-bottom: 4px;
        }
        .card-header-accent p { font-size: 11.5px; color: rgba(255,255,255,0.65); }
        .card-body-content { padding: 32px 36px 36px; }
        .field-wrap { margin-bottom: 18px; }
        .field-wrap label { font-size: 12px; font-weight: 700; color: #374151; display: block; margin-bottom: 6px; }
        .field-inner { position: relative; display: flex; align-items: center; }
        .field-icon { position: absolute; left: 13px; color: #9ca3af; font-size: 14px; z-index: 1; }
        .form-control {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
            padding: 11px 38px 11px 38px; font-size: 13px; font-family: 'Poppins', sans-serif;
            background: #f9fafb; color: #111; transition: all 0.2s;
        }
        .form-control:focus { border-color: #1a3a6c; box-shadow: 0 0 0 3px rgba(26,58,108,0.1); background: #fff; outline: none; }
        .btn-eye {
            position: absolute; right: 4px; border: none; background: transparent;
            color: #9ca3af; cursor: pointer; padding: 8px 10px;
        }
        .btn-eye:hover { color: #1a3a6c; }
        .btn-main {
            width: 100%; background: linear-gradient(135deg, #1a3a6c, #1e5799); color: #fff; border: none;
            border-radius: 10px; padding: 13px; font-size: 14px; font-weight: 700; font-family: 'Poppins', sans-serif;
            letter-spacing: 0.8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(26,58,108,0.35); transition: all 0.2s;
        }
        .btn-main:hover { background: linear-gradient(135deg, #163062, #1a4a8a); transform: translateY(-1px); }
        .switch-text { text-align: center; margin-top: 20px; font-size: 12.5px; color: #6b7280; }
        .switch-text a { color: #1a3a6c; font-weight: 700; text-decoration: none; }
        .switch-text a:hover { text-decoration: underline; }
        .alert-box {
            border-radius: 10px; padding: 12px 14px; font-size: 12.5px; margin-bottom: 18px;
            display: flex; align-items: flex-start; gap: 8px; line-height: 1.5;
        }
        .alert-danger-box { background: #fef2f2; border: 1.5px solid #fecaca; color: #b91c1c; }
        .hint-text { font-size: 11px; color: #9ca3af; margin-top: 6px; }
        .auth-wrapper { max-width: 440px; }

        .strength-wrap { margin-top: 10px; }
        .strength-bar { display: flex; gap: 6px; }
        .strength-bar span {
            flex: 1; height: 6px; border-radius: 99px; background: #e5e7eb; transition: background 0.25s;
        }
        .strength-meta { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 11px; }
        .strength-label { font-weight: 700; color: #9ca3af; }
        .strength-reason { margin-top: 5px; font-size: 11px; font-weight: 600; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 4px 8px; }
        .strength-wrap.weak   .strength-bar span:nth-child(-n+1) { background: #dc2626; }
        .strength-wrap.good   .strength-bar span:nth-child(-n+2) { background: #f59e0b; }
        .strength-wrap.strong .strength-bar span:nth-child(-n+3) { background: #16a34a; }
        .strength-wrap.weak   .strength-label { color: #dc2626; }
        .strength-wrap.good   .strength-label { color: #d97706; }
        .strength-wrap.strong .strength-label { color: #16a34a; }

        .check-list { list-style: none; margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 4px 10px; }
        .check-list li { font-size: 11px; color: #9ca3af; display: flex; align-items: center; gap: 5px; }
        .check-list li i { font-size: 12px; }
        .check-list li.ok { color: #16a34a; }
        @media (max-width: 380px) { .check-list { grid-template-columns: 1fr; } }

        .match-text { font-size: 11px; margin-top: 6px; display: flex; align-items: center; gap: 5px; color: #9ca3af; }
        .match-text.ok { color: #16a34a; }
        .match-text.bad { color: #dc2626; }

        .tips-box {
            background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px;
            padding: 14px 16px; margin-bottom: 20px;
        }
        .tips-box summary {
            font-size: 12.5px; font-weight: 700; color: #1a3a6c; cursor: pointer; list-style: none;
            display: flex; align-items: center; gap: 8px;
        }
        .tips-box summary::-webkit-details-marker { display: none; }
        .tips-box summary .chev { margin-left: auto; transition: transform 0.2s; }
        .tips-box[open] summary .chev { transform: rotate(180deg); }
        .tips-box ul { margin: 10px 0 0; padding-left: 18px; }
        .tips-box li { font-size: 11.5px; color: #1e3a5f; line-height: 1.55; margin-bottom: 4px; }
        .tips-box .example {
            margin-top: 8px; font-size: 11.5px; color: #1e3a5f; background: #fff;
            border: 1px dashed #93c5fd; border-radius: 8px; padding: 8px 10px;
        }
        .tips-box .example code { color: #15803d; font-weight: 700; background: none; }
        .btn-main:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
    </style>
</head>
<body>

<div class="bg-image"></div>

<div class="auth-wrapper">
<div class="auth-card">

    <div class="card-header-accent">
        <div class="logo-circle">
            <img src="/images/logo.png" alt="ILC Logo"
                 onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-key-fill fs-3\' style=\'color:#1a3a6c\'></i>'">
        </div>
        <h2>Reset Password</h2>
        <p>IEMELIF Learning Center</p>
    </div>

    <div class="card-body-content">

        @if($errors->any())
            <div class="alert-box alert-danger-box">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <details class="tips-box">
            <summary>
                <i class="bi bi-lightbulb-fill"></i> How to make a strong password
                <i class="bi bi-chevron-down chev"></i>
            </summary>
            <ul>
                <li>Use <strong>12 or more characters</strong> — longer is the single biggest boost.</li>
                <li>Mix <strong>UPPERCASE</strong>, <strong>lowercase</strong>, <strong>numbers</strong> and <strong>symbols</strong> (like ! @ # $ ?).</li>
                <li>Try a <strong>passphrase</strong>: 3–4 unrelated words joined together, easy to remember but hard to guess.</li>
                <li>Avoid personal info: your name, birthday, student number, or the school's name.</li>
                <li>Avoid common ones like <em>password123</em>, <em>12345678</em> or <em>qwerty</em>.</li>
                <li>Don't reuse a password from another site, and never share it with anyone.</li>
                <li>Since you just forgot yours, keep the new one somewhere safe — a password manager or a private notebook.</li>
            </ul>
            <div class="example">
                Example: <code>Mango!Bike7Cloud</code> — words + a number + a symbol.
                <span style="color:#6b7280;">(Make your own; don't copy this one.)</span>
            </div>
        </details>

        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field-wrap">
                <label>Email Address</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" style="padding-right:14px;"
                           value="{{ old('email', $email) }}" required autofocus>
                </div>
            </div>

            <div class="field-wrap">
                <label>New Password</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" id="rPass" class="form-control"
                           placeholder="At least 8 characters" required>
                    <button type="button" class="btn-eye" onclick="togglePass('rPass','rEye')">
                        <i class="bi bi-eye" id="rEye"></i>
                    </button>
                </div>
                <div class="strength-wrap" id="strengthWrap">
                    <div class="strength-bar"><span></span><span></span><span></span></div>
                    <div class="strength-meta">
                        <span style="color:#9ca3af;">Password strength</span>
                        <span class="strength-label" id="strengthLabel">—</span>
                    </div>
                    <div class="strength-reason" id="strengthReason" style="display:none;"></div>
                </div>
                <ul class="check-list">
                    <li id="ck-len"><i class="bi bi-circle"></i> At least 8 characters</li>
                    <li id="ck-case"><i class="bi bi-circle"></i> Upper &amp; lowercase</li>
                    <li id="ck-num"><i class="bi bi-circle"></i> A number</li>
                    <li id="ck-sym"><i class="bi bi-circle"></i> A symbol (!@#$?)</li>
                    <li id="ck-long"><i class="bi bi-circle"></i> 12+ characters (bonus)</li>
                </ul>
            </div>

            <div class="field-wrap">
                <label>Confirm New Password</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password_confirmation" id="rPass2" class="form-control"
                           placeholder="Re-enter new password" required>
                    <button type="button" class="btn-eye" onclick="togglePass('rPass2','rEye2')">
                        <i class="bi bi-eye" id="rEye2"></i>
                    </button>
                </div>
                <div class="match-text" id="matchText"><i class="bi bi-circle"></i> Must match the password above.</div>
            </div>

            <button type="submit" class="btn-main" id="resetBtn" disabled>
                <i class="bi bi-check-circle-fill"></i> Reset Password
            </button>
        </form>

        <div class="switch-text">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>

    </div>
</div>
</div>

<script>
    function togglePass(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    (function () {
        var pass  = document.getElementById('rPass');
        var pass2 = document.getElementById('rPass2');
        var email = document.querySelector('input[name="email"]');
        var wrap  = document.getElementById('strengthWrap');
        var label = document.getElementById('strengthLabel');
        var match = document.getElementById('matchText');
        var btn   = document.getElementById('resetBtn');
        var COMMON = ['password', '12345678', '123456789', '1234567890', 'qwerty123', 'qwertyuiop',
                      'iloveyou', '11111111', 'abcd1234', 'admin123', 'password1', 'iemelif', 'learningcenter'];

        function setCheck(id, ok) {
            var li = document.getElementById(id);
            li.classList.toggle('ok', ok);
            li.querySelector('i').className = ok ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        }

        function rate(pw) {
            var c = {
                len:  pw.length >= 8,
                kase: /[a-z]/.test(pw) && /[A-Z]/.test(pw),
                num:  /\d/.test(pw),
                sym:  /[^A-Za-z0-9]/.test(pw),
                long: pw.length >= 12
            };
            setCheck('ck-len', c.len);
            setCheck('ck-case', c.kase);
            setCheck('ck-num', c.num);
            setCheck('ck-sym', c.sym);
            setCheck('ck-long', c.long);

            if (!pw) return '';
            var lower = pw.toLowerCase();
            // Each piece of the email name (juan.delacruz -> juan, delacruz) counts as personal info.
            var nameParts = (email && email.value.split('@')[0] || '').toLowerCase()
                .split(/[^a-z0-9]+/).filter(function (p) { return p.length >= 4; });
            var common = COMMON.filter(function (w) { return lower.indexOf(w) !== -1; })[0];
            var namePart = nameParts.filter(function (p) { return lower.indexOf(p) !== -1; })[0];
            if (!c.len)                   { reasonText = 'Too short — use at least 8 characters.'; return 'weak'; }
            if (common)                   { reasonText = 'Contains a very common password ("' + common + '"), which is easy to guess.'; return 'weak'; }
            if (namePart)                 { reasonText = 'Contains "' + namePart + '" from your email, which is easy to guess.'; return 'weak'; }
            if (/^(.)\1+$/.test(pw))      { reasonText = 'Uses the same character repeated.'; return 'weak'; }

            var points = 1 + (c.kase ? 1 : 0) + (c.num ? 1 : 0) + (c.sym ? 1 : 0) + (c.long ? 1 : 0);
            if (points >= 4) return 'strong';
            if (points === 3) return 'good';
            reasonText = 'Add uppercase letters, numbers or symbols to make it stronger.';
            return 'weak';
        }

        var reasonText = '';
        function update() {
            reasonText = '';
            var level = rate(pass.value);
            wrap.className = 'strength-wrap' + (level ? ' ' + level : '');
            label.textContent = { weak: 'Weak', good: 'Good', strong: 'Strong' }[level] || '—';
            var reasonEl = document.getElementById('strengthReason');
            reasonEl.textContent = reasonText;
            reasonEl.style.display = reasonText ? '' : 'none';

            var matches = pass2.value.length > 0 && pass.value === pass2.value;
            if (!pass2.value) {
                match.className = 'match-text';
                match.innerHTML = '<i class="bi bi-circle"></i> Must match the password above.';
            } else if (matches) {
                match.className = 'match-text ok';
                match.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match.';
            } else {
                match.className = 'match-text bad';
                match.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords don\'t match yet.';
            }

            btn.disabled = !(pass.value.length >= 8 && matches);
        }

        pass.addEventListener('input', update);
        pass2.addEventListener('input', update);
        if (email) email.addEventListener('input', update);
        update();
    })();
</script>

</body>
</html>
