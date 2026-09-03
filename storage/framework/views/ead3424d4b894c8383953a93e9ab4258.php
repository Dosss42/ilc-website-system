<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — IEMELIF Learning Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── BACKGROUND ── */
        .bg-image {
            position: fixed;
            inset: 0;
            background: url('/images/bg.png') center/cover no-repeat;
            filter: brightness(0.32) blur(3px);
            z-index: 0;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        /* ── CARD ── */
        .auth-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.08);
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .panel { display: none; }
        .panel.active {
            display: block;
            animation: fadeSlideIn 0.35s ease;
        }

        /* ── CARD HEADER (navy) ── */
        .card-header-accent {
            background: linear-gradient(135deg, #0d2147 0%, #1a3a6c 60%, #1e5799 100%);
            padding: 34px 36px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header-accent::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 130px; height: 130px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .card-header-accent::after {
            content: '';
            position: absolute;
            bottom: -30px; left: -30px;
            width: 100px; height: 100px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .logo-circle {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            overflow: hidden;
            border: none;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        .logo-circle img { width: 92%; height: 92%; object-fit: contain; }

        .card-header-accent h2 {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }

        .card-header-accent p {
            font-size: 11.5px;
            color: rgba(255,255,255,0.65);
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .card-header-accent .location-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 10px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 10.5px;
            color: rgba(255,255,255,0.75);
            position: relative;
            z-index: 1;
        }

        /* ── CARD BODY ── */
        .card-body-content {
            padding: 30px 32px 28px;
        }

        /* ── ALERT ── */
        .alert {
            border-radius: 10px;
            font-size: 12.5px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-danger {
            background: #fff1f0;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        /* ── FIELD WRAP (icon + input) ── */
        .field-wrap {
            position: relative;
            margin-bottom: 14px;
        }

        .field-wrap label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .field-inner {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 13px;
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
            z-index: 1;
            transition: color 0.2s;
        }

        .field-inner:focus-within .field-icon { color: #1a3a6c; }

        .form-control {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 11px 14px 11px 38px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            background: #f9fafb;
            color: #111;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .form-control:focus {
            border-color: #1a3a6c;
            box-shadow: 0 0 0 3px rgba(26,58,108,0.1);
            background: #fff;
            outline: none;
        }

        .form-control.no-icon {
            padding-left: 14px;
        }

        /* password field has eye button */
        .form-control.has-eye { border-radius: 10px 0 0 10px; border-right: none; }

        .btn-eye {
            border: 1.5px solid #e5e7eb;
            border-left: none;
            background: #f9fafb;
            border-radius: 0 10px 10px 0;
            padding: 0 13px;
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
            display: flex;
            align-items: center;
            align-self: stretch;
        }

        .btn-eye:hover { color: #1a3a6c; background: #fff; }

        /* ── FORGOT ── */
        .forgot-link {
            font-size: 11.5px;
            color: #1a3a6c;
            text-decoration: none;
            font-weight: 500;
            display: block;
            text-align: right;
            margin-bottom: 18px;
            margin-top: -4px;
        }

        .forgot-link:hover { text-decoration: underline; color: #1a3a6c; }

        /* ── SIGN IN BUTTON ── */
        .btn-main {
            width: 100%;
            background: linear-gradient(135deg, #1a3a6c, #1e5799);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(26,58,108,0.35);
        }

        .btn-main:hover {
            background: linear-gradient(135deg, #163062, #1a4a8a);
            box-shadow: 0 6px 20px rgba(26,58,108,0.45);
            transform: translateY(-1px);
        }

        .btn-main:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(26,58,108,0.3); }

        /* ── SWITCH TEXT ── */
        .switch-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12.5px;
            color: #6b7280;
        }

        .switch-text a {
            color: #1a3a6c;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .switch-text a:hover { text-decoration: underline; }

        /* ── DIVIDER ── */
        .divider-line {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            color: #d1d5db;
            font-size: 11px;
            color: #9ca3af;
        }

        .divider-line::before,
        .divider-line::after { content:''; flex:1; height:1px; background:#f0f0f0; }

        /* ── REGISTER PANEL icon box ── */
        .icon-badge {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #e8f0fb, #dbeafe);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }

        /* ── RECAPTCHA center ── */
        .recaptcha-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 14px;
        }

        /* ── STEP DOTS (kept for register panel) ── */
        .steps-row { display: flex; align-items: center; margin-bottom: 6px; }
        .sdot { width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;border:2px solid #ddd;background:#fff;color:#bbb;transition:all 0.3s;flex-shrink:0; }
        .sdot.active { border-color:#1a3a6c;background:#1a3a6c;color:#fff; }
        .sdot.done   { border-color:#27ae60;background:#27ae60;color:#fff; }
        .sline { flex:1;height:2px;background:#ddd;margin:0 4px;transition:background 0.3s; }
        .sline.done { background:#27ae60; }
        .slabels { display:flex;justify-content:space-between;margin-bottom:18px; }
        .slabels span { font-size:10px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;flex:1;text-align:center;transition:color 0.3s; }
        .slabels span.active { color:#1a3a6c; }
        .slabels span.done   { color:#27ae60; }

        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="bg-image"></div>

<div class="auth-wrapper">
<div class="auth-card">

    
    <div class="panel active" id="panelLogin">

        
        <div class="card-header-accent">
            <div class="logo-circle">
                <img src="/images/logo.png" alt="ILC Logo"
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-shield-fill fs-3 text-white\'></i>'">
            </div>
            <h2>AIMS</h2>
            <p>IEMELIF Learning Center</p>
            <span class="location-tag">
                <i class="bi bi-geo-alt-fill"></i> General Tinio, Nueva Ecija
            </span>
        </div>

        
        <div class="card-body-content">

            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?php echo csrf_field(); ?>

                <div class="field-wrap">
                    <label>Email Address</label>
                    <div class="field-inner">
                        <span class="field-icon"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control"
                               placeholder="Enter your email"
                               value="<?php echo e(old('email')); ?>" required autofocus>
                    </div>
                </div>

                <div class="field-wrap">
                    <label>Password</label>
                    <div class="field-inner">
                        <span class="field-icon"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="lPass"
                               class="form-control has-eye"
                               placeholder="Enter your password" required>
                        <button type="button" class="btn-eye" onclick="togglePass('lPass','lEye')">
                            <i class="bi bi-eye" id="lEye"></i>
                        </button>
                    </div>
                </div>

                <a href="#" class="forgot-link">Forgot password?</a>

                <?php if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'your_site_key_here'): ?>
                <div class="recaptcha-wrap">
                    <div class="g-recaptcha" data-sitekey="<?php echo e(config('services.recaptcha.site_key')); ?>"></div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-main">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="switch-text">
                Don't have an account?
                <a href="<?php echo e(route('enrollment.form')); ?>">Apply for Enrollment</a>
            </div>

        </div>
    </div>


    
    <div class="panel" id="panelRegister">

        <div class="card-header-accent">
            <div class="logo-circle">
                <img src="/images/logo.png" alt="ILC Logo"
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-shield-fill fs-3 text-white\'></i>'">
            </div>
            <h2>Get an Account</h2>
            <p>IEMELIF Learning Center</p>
            <span class="location-tag">
                <i class="bi bi-geo-alt-fill"></i> General Tinio, Nueva Ecija
            </span>
        </div>

        <div class="card-body-content" style="text-align:center;">
            <div class="icon-badge">
                <i class="bi bi-person-plus-fill" style="font-size:28px;color:#1a3a6c;"></i>
            </div>
            <h5 style="font-weight:700;color:#1a3a6c;margin-bottom:8px;font-size:15px;">Enrollment Required</h5>
            <p style="font-size:12.5px;color:#6b7280;margin-bottom:24px;line-height:1.7;">
                Student accounts are created automatically when you complete the enrollment process.
                Please apply for enrollment first to receive your login credentials.
            </p>
            <a href="<?php echo e(route('enrollment.form')); ?>"
               style="display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#1a3a6c,#1e5799);color:#fff;border-radius:10px;padding:13px 20px;font-weight:700;font-size:14px;text-decoration:none;margin-bottom:10px;box-shadow:0 4px 14px rgba(26,58,108,0.35);">
                <i class="bi bi-clipboard-check"></i> Apply for Enrollment
            </a>
            <a href="<?php echo e(route('admission')); ?>"
               style="display:flex;align-items:center;justify-content:center;gap:8px;background:#f3f4f6;color:#374151;border-radius:10px;padding:12px 20px;font-weight:600;font-size:13px;text-decoration:none;">
                <i class="bi bi-info-circle"></i> Learn About Admission
            </a>

            <div class="switch-text">
                Already have an account?
                <a onclick="showPanel('panelLogin')" style="cursor:pointer;">Sign in here</a>
            </div>
        </div>

    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'your_site_key_here'): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script>
    function showPanel(id) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.getElementById(id).classList.add('active');
    }

    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
</script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\login_register.blade.php ENDPATH**/ ?>