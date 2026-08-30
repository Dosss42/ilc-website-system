<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }

        body {
            font-family:'Poppins',sans-serif;
            min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            background:#0f1923; padding:20px 0;
        }

        .bg-image {
            position:fixed; inset:0;
            background:url('/images/bg.png') center/cover no-repeat;
            filter:brightness(0.35) blur(2px); z-index:0;
        }

        .auth-wrapper {
            position:relative; z-index:1;
            width:100%; max-width:480px; padding:20px;
        }

        .auth-card {
            background:rgba(255,255,255,0.97);
            border-radius:16px; padding:36px 36px 32px;
            box-shadow:0 25px 60px rgba(0,0,0,0.5);
            animation:fadeUp 0.4s ease;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .logo-row {
            display:flex; align-items:center;
            justify-content:center; gap:14px; margin-bottom:16px;
        }

        .logo-circle {
            width:54px; height:54px; border-radius:50%;
            overflow:hidden; border:2px solid #ddd; background:#f0f0f0;
            display:flex; align-items:center; justify-content:center;
        }

        .logo-circle img { width:100%; height:100%; object-fit:cover; }

        .portal-title { text-align:center; margin-bottom:20px; }
        .portal-title h2 {
            font-size:18px; font-weight:700; color:#1a3a6c;
            text-transform:uppercase; letter-spacing:2px; margin-bottom:4px;
        }
        .portal-title p { font-size:12px; color:#888; margin:0; }

        /* ── NOTICE BOX ── */
        .notice-box {
            background:#e8f0fb; border:1px solid #b8d0f0;
            border-radius:8px; padding:12px 16px;
            display:flex; align-items:flex-start; gap:10px;
            font-size:12px; color:#1a3a6c; margin-bottom:20px;
            line-height:1.5;
        }
        .notice-box i { font-size:16px; flex-shrink:0; margin-top:1px; }

        /* ── STEP DOTS ── */
        .steps-row {
            display:flex; align-items:center; margin-bottom:6px;
        }

        .sdot {
            width:26px; height:26px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:700;
            border:2px solid #ddd; background:#fff; color:#bbb;
            transition:all 0.3s; flex-shrink:0;
        }

        .sdot.active { border-color:#1a3a6c; background:#1a3a6c; color:#fff; }
        .sdot.done   { border-color:#27ae60; background:#27ae60; color:#fff; }

        .sline {
            flex:1; height:2px; background:#ddd;
            margin:0 4px; transition:background 0.3s;
        }
        .sline.done { background:#27ae60; }

        .slabels {
            display:flex; justify-content:space-between; margin-bottom:20px;
        }
        .slabels span {
            font-size:10px; color:#aaa; font-weight:600;
            text-transform:uppercase; letter-spacing:0.5px;
            flex:1; text-align:center; transition:color 0.3s;
        }
        .slabels span.active { color:#1a3a6c; }
        .slabels span.done   { color:#27ae60; }

        /* ── FORM ── */
        .form-label {
            font-size:12px; font-weight:600; color:#444;
            margin-bottom:5px; display:block;
        }

        .form-control {
            width:100%; border:1.5px solid #e0e0e0;
            border-radius:8px; padding:10px 14px;
            font-size:13px; font-family:'Poppins',sans-serif;
            background:#fafafa; color:#333;
            transition:border 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color:#1a3a6c;
            box-shadow:0 0 0 3px rgba(26,58,108,0.1);
            background:#fff; outline:none;
        }

        select.form-control { cursor:pointer; }

        .input-group { display:flex; }
        .input-group .form-control {
            border-right:none;
            border-radius:8px 0 0 8px;
        }

        .btn-eye {
            border:1.5px solid #e0e0e0; border-left:none;
            background:#fafafa; border-radius:0 8px 8px 0;
            padding:0 14px; color:#888; cursor:pointer;
            display:flex; align-items:center; transition:color 0.2s;
        }
        .btn-eye:hover { color:#1a3a6c; }

        /* ── STRENGTH BAR ── */
        .strength-bar { display:flex; gap:4px; margin-top:5px; }
        .sbar-seg {
            flex:1; height:3px; border-radius:2px;
            background:#e0e0e0; transition:background 0.3s;
        }

        /* ── BUTTON ── */
        .btn-main {
            width:100%; background:#1a3a6c; color:#fff;
            border:none; border-radius:8px; padding:12px;
            font-size:14px; font-weight:600;
            font-family:'Poppins',sans-serif;
            cursor:pointer; transition:background 0.2s, transform 0.1s;
            margin-top:6px; display:flex;
            align-items:center; justify-content:center; gap:6px;
        }
        .btn-main:hover { background:#2471a3; transform:translateY(-1px); }
        .btn-main:active { transform:translateY(0); }
        .btn-main.gray { background:#6c757d; }
        .btn-main.gray:hover { background:#5a6268; }
        .btn-main.slim { width:46px; flex-shrink:0; padding:0; margin-top:6px; }

        /* ── SUMMARY BOX ── */
        .summary-box {
            background:#f8fafc; border:1px solid #e2e8f0;
            border-radius:10px; padding:14px 16px; margin-bottom:16px;
        }
        .summary-box h6 {
            font-size:11px; font-weight:700; color:#1a3a6c;
            text-transform:uppercase; letter-spacing:1px; margin-bottom:10px;
        }
        .sum-row {
            display:flex; justify-content:space-between;
            font-size:12px; margin-bottom:5px;
        }
        .sum-row span:first-child { color:#888; }
        .sum-row span:last-child  { font-weight:600; color:#333; }

        /* ── SWITCH TEXT ── */
        .switch-text {
            text-align:center; margin-top:16px;
            font-size:12px; color:#888;
        }
        .switch-text a {
            color:#1a3a6c; font-weight:600; text-decoration:none;
        }
        .switch-text a:hover { text-decoration:underline; }

        /* ── PASSWORD REQUIREMENTS ── */
        .pass-requirements {
            background:#f8fafc; border:1px solid #e2e8f0;
            border-radius:6px; padding:10px 14px;
            font-size:11px; color:#555; margin-top:6px;
        }
        .pass-req-item {
            display:flex; align-items:center; gap:6px;
            margin-bottom:3px;
        }
        .pass-req-item:last-child { margin-bottom:0; }
        .pass-req-item i { font-size:11px; color:#bbb; }
        .pass-req-item.valid i { color:#27ae60; }

        .alert {
            border-radius:8px; font-size:13px;
            padding:10px 14px; margin-bottom:14px;
        }
    </style>
</head>
<body>

<div class="bg-image"></div>

<div class="auth-wrapper">
<div class="auth-card">

    {{-- Logos --}}
    <div class="logo-row">
        <div class="logo-circle">
            <img src="/images/logo1.png" alt="Logo"
                 onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-building fs-4 text-secondary\'></i>'">
        </div>
        <div class="logo-circle">
            <img src="/images/logo.png" alt="Logo"
                 onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-shield-fill fs-4 text-secondary\'></i>'">
        </div>
    </div>

    {{-- Title --}}
    <div class="portal-title">
        <h2>Create Account</h2>
        <p>IEMELIF Learning Center — General Tinio, Nueva Ecija</p>
    </div>

    {{-- =============================================
         NOTICE: No role selection
         Public registration = Student/Parent only
         Admin/Teacher = created by Super Admin
         ============================================= --}}
    <div class="notice-box">
        <i class="bi bi-info-circle-fill"></i>
        <span>
            This form is for <strong>Students and Parents only</strong>.
            If you need an Admin or Teacher account, please contact the
            <strong>System Administrator</strong>.
        </span>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Step Progress --}}
    <div class="steps-row">
        <div class="sdot active" id="d1">1</div>
        <div class="sline" id="l1"></div>
        <div class="sdot" id="d2">2</div>
        <div class="sline" id="l2"></div>
        <div class="sdot" id="d3">3</div>
    </div>
    <div class="slabels">
        <span class="active" id="lb1">Personal</span>
        <span id="lb2">Account</span>
        <span id="lb3">Confirm</span>
    </div>

    {{-- =============================================
         FORM: Submits to AuthController@register
         Role is ALWAYS 'student' — hardcoded hidden field
         ============================================= --}}
    <form method="POST" action="{{ route('register.submit') }}" id="regForm">
        @csrf

        {{-- Role always student — cannot be changed by user --}}
        <input type="hidden" name="role" value="student">

        {{-- ── STEP 1: Personal Info ── --}}
        <div id="rs1">
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control"
                           placeholder="Juan"
                           value="{{ old('first_name') }}"
                           pattern="[a-zA-Z\s]+"
                           title="Letters only" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control"
                           placeholder="Dela Cruz"
                           value="{{ old('last_name') }}"
                           pattern="[a-zA-Z\s]+"
                           title="Letters only" required>
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="birthdate" class="form-control"
                           value="{{ old('birthdate') }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="" disabled selected>Select</option>
                        <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number *</label>
                <input type="text" name="contact" class="form-control"
                       placeholder="09XX XXX XXXX"
                       value="{{ old('contact') }}"
                       pattern="[0-9\+\-\s]+"
                       title="Numbers only" required>
            </div>
            <button type="button" class="btn-main" onclick="regStep(2)">
                Next <i class="bi bi-arrow-right-short"></i>
            </button>
        </div>

        {{-- ── STEP 2: Account Info ── --}}
        <div id="rs2" style="display:none;">
            <div class="mb-2">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control"
                       placeholder="juandelacruz@email.com"
                       value="{{ old('email') }}" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Password *</label>
                <div class="input-group">
                    <input type="password" name="password" id="rPass"
                           class="form-control"
                           placeholder="Create a strong password"
                           oninput="checkStrength(this.value)" required>
                    <button type="button" class="btn-eye" onclick="togglePass('rPass','rEye1')">
                        <i class="bi bi-eye" id="rEye1"></i>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div class="strength-bar">
                    <div class="sbar-seg" id="sb1"></div>
                    <div class="sbar-seg" id="sb2"></div>
                    <div class="sbar-seg" id="sb3"></div>
                    <div class="sbar-seg" id="sb4"></div>
                </div>
                <div style="font-size:11px;color:#aaa;margin-top:3px;font-weight:500;" id="sLbl">Enter a password</div>

                {{-- Password requirements checklist --}}
                <div class="pass-requirements mt-2">
                    <div class="pass-req-item" id="req-len">
                        <i class="bi bi-circle"></i> At least 8 characters
                    </div>
                    <div class="pass-req-item" id="req-upper">
                        <i class="bi bi-circle"></i> At least 1 uppercase letter (A-Z)
                    </div>
                    <div class="pass-req-item" id="req-num">
                        <i class="bi bi-circle"></i> At least 1 number (0-9)
                    </div>
                    <div class="pass-req-item" id="req-sym">
                        <i class="bi bi-circle"></i> At least 1 symbol (@$!%*#?&)
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password *</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="rPass2"
                           class="form-control" placeholder="Repeat your password" required>
                    <button type="button" class="btn-eye" onclick="togglePass('rPass2','rEye2')">
                        <i class="bi bi-eye" id="rEye2"></i>
                    </button>
                </div>
                <div style="font-size:11px;margin-top:4px;" id="matchLbl"></div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn-main gray slim" onclick="regStep(1)">
                    <i class="bi bi-arrow-left-short fs-5"></i>
                </button>
                <button type="button" class="btn-main" onclick="regStep(3)" style="flex:1; margin-top:6px;">
                    Next <i class="bi bi-arrow-right-short"></i>
                </button>
            </div>
        </div>

        {{-- ── STEP 3: Review & Confirm ── --}}
        <div id="rs3" style="display:none;">
            <div class="summary-box">
                <h6><i class="bi bi-person-check-fill me-2"></i>Review Your Information</h6>
                <div class="sum-row"><span>Full Name</span><span id="sumName">—</span></div>
                <div class="sum-row"><span>Email</span><span id="sumEmail">—</span></div>
                <div class="sum-row"><span>Gender</span><span id="sumGender">—</span></div>
                <div class="sum-row"><span>Contact</span><span id="sumContact">—</span></div>
                <div class="sum-row">
                    <span>Account Type</span>
                    <span style="color:#27ae60;font-weight:700;">
                        <i class="bi bi-mortarboard-fill me-1"></i>Student / Parent
                    </span>
                </div>
            </div>

            <div style="display:flex;align-items:flex-start;gap:10px;font-size:12px;color:#555;margin-bottom:16px;">
                <input type="checkbox" name="terms" id="terms"
                       style="margin-top:2px;accent-color:#1a3a6c;width:15px;height:15px;flex-shrink:0;" required>
                <label for="terms">
                    I agree to the
                    <a href="{{ route('terms') }}" target="_blank" style="color:#1a3a6c;font-weight:600;">Terms & Conditions</a>
                    and
                    <a href="#" style="color:#1a3a6c;font-weight:600;">Privacy Policy</a>
                    of IEMELIF Learning Center.
                </label>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn-main gray slim" onclick="regStep(2)">
                    <i class="bi bi-arrow-left-short fs-5"></i>
                </button>
                <button type="submit" class="btn-main" style="flex:1; margin-top:6px;">
                    <i class="bi bi-check-lg"></i> Create Account
                </button>
            </div>
        </div>

    </form>

    <div class="switch-text">
        Already have an account?
        <a href="{{ route('login') }}">Sign in here</a>
    </div>

    <div class="switch-text" style="margin-top:8px;">
        Want to enroll first?
        <a href="{{ route('admission') }}">Apply for Admission</a>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Toggle password visibility ──
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    // ── Password strength + requirements checker ──
    function checkStrength(val) {
        const colors  = ['#e74c3c','#e67e22','#f1c40f','#27ae60'];
        const labels  = ['Weak','Fair','Good','Strong'];
        const checks  = {
            len:   val.length >= 8,
            upper: /[A-Z]/.test(val),
            num:   /[0-9]/.test(val),
            sym:   /[@$!%*#?&]/.test(val),
        };

        let score = Object.values(checks).filter(Boolean).length;

        // Update strength bar
        [1,2,3,4].forEach(i => {
            document.getElementById('sb'+i).style.background =
                i <= score ? colors[score-1] : '#e0e0e0';
        });

        // Update strength label
        const lbl = document.getElementById('sLbl');
        lbl.textContent = score > 0 ? labels[score-1] : 'Enter a password';
        lbl.style.color = score > 0 ? colors[score-1] : '#aaa';

        // Update requirement items
        const reqs = {
            'req-len':   checks.len,
            'req-upper': checks.upper,
            'req-num':   checks.num,
            'req-sym':   checks.sym,
        };

        Object.entries(reqs).forEach(([id, valid]) => {
            const el = document.getElementById(id);
            el.className = 'pass-req-item' + (valid ? ' valid' : '');
            el.querySelector('i').className = valid
                ? 'bi bi-check-circle-fill'
                : 'bi bi-circle';
        });

        // Check password match
        checkMatch();
    }

    // ── Check if passwords match ──
    function checkMatch() {
        const p1  = document.getElementById('rPass').value;
        const p2  = document.getElementById('rPass2').value;
        const lbl = document.getElementById('matchLbl');
        if (!p2) { lbl.textContent = ''; return; }
        if (p1 === p2) {
            lbl.textContent = '✓ Passwords match';
            lbl.style.color = '#27ae60';
        } else {
            lbl.textContent = '✗ Passwords do not match';
            lbl.style.color = '#e74c3c';
        }
    }

    document.getElementById('rPass2').addEventListener('input', checkMatch);

    // ── Multi-step navigation ──
    function regStep(step) {
        if (step === 3) {
            const f  = document.getElementById('regForm');
            const fn = f.querySelector('[name=first_name]').value;
            const ln = f.querySelector('[name=last_name]').value;
            const em = f.querySelector('[name=email]').value;
            const gn = f.querySelector('[name=gender]').value;
            const ct = f.querySelector('[name=contact]').value;

            document.getElementById('sumName').textContent    = fn && ln ? `${fn} ${ln}` : '—';
            document.getElementById('sumEmail').textContent   = em || '—';
            document.getElementById('sumGender').textContent  = gn ? gn.charAt(0).toUpperCase()+gn.slice(1) : '—';
            document.getElementById('sumContact').textContent = ct || '—';
        }

        // Show/hide steps
        [1,2,3].forEach(s => {
            document.getElementById('rs'+s).style.display = s === step ? '' : 'none';
        });

        // Update dots
        [1,2,3].forEach(s => {
            const dot = document.getElementById('d'+s);
            const lbl = document.getElementById('lb'+s);
            if (s < step) {
                dot.className = 'sdot done'; dot.textContent = '✓'; lbl.className = 'done';
            } else if (s === step) {
                dot.className = 'sdot active'; dot.textContent = s; lbl.className = 'active';
            } else {
                dot.className = 'sdot'; dot.textContent = s; lbl.className = '';
            }
        });

        // Update lines
        [1,2].forEach(l => {
            document.getElementById('l'+l).className =
                'sline' + (l < step ? ' done' : '');
        });
    }

    // Auto-capitalize first letter of each word on text inputs
    function capitalizeFirst(input) {
        if (!input.value) return;
        var pos = input.selectionStart;
        input.value = input.value.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
        input.setSelectionRange(pos, pos);
    }
    document.addEventListener('input', function(e) {
        var el = e.target;
        if (el.tagName !== 'INPUT' || el.type !== 'text') return;
        var skip = ['email','search','password'];
        var name = (el.name || '').toLowerCase();
        var id = (el.id || '').toLowerCase();
        if (skip.some(function(s) { return name.indexOf(s) > -1 || id.indexOf(s) > -1; })) return;
        capitalizeFirst(el);
    });
</script>
</body>
</html>