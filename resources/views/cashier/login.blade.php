<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cashier Portal Login - ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        * { font-family: 'Open Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a1628;
            position: relative;
            overflow: hidden;
        }

        .bg-image {
            position: fixed; inset: 0;
            background: url('/images/bg.png') center/cover no-repeat;
            filter: brightness(0.3) blur(3px);
            z-index: 0;
        }

        /* Animated accent circles */
        .accent-circle {
            position: fixed;
            border-radius: 50%;
            opacity: .08;
            animation: floatCircle 8s ease-in-out infinite;
            z-index: 0;
        }
        .accent-circle.c1 { width:420px;height:420px;background:#2471a3;top:-120px;right:-80px;animation-delay:0s; }
        .accent-circle.c2 { width:300px;height:300px;background:#c5a059;bottom:-80px;left:-60px;animation-delay:3s; }
        .accent-circle.c3 { width:180px;height:180px;background:#2471a3;bottom:140px;right:60px;animation-delay:5s; }
        @keyframes floatCircle { 0%,100%{transform:translateY(0) scale(1);} 50%{transform:translateY(-20px) scale(1.04);} }

        .login-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255,255,255,0.97);
            border-radius: 22px;
            padding: 40px 38px 36px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.08);
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }
        .logo-badge {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: #1a3a6c;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 24px rgba(26,58,108,.3);
        }
        .logo-badge i { font-size: 34px; color: #fff; }
        .logo-title {
            font-size: 20px; font-weight: 800;
            color: #1a3a6c; line-height: 1.1;
            text-align: center;
        }
        .logo-sub {
            font-size: 12px; color: #64748b;
            font-weight: 500; margin-top: 4px;
            letter-spacing: .3px;
        }
        .portal-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #e8f0fb; color: #2471a3;
            font-size: 11px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
            margin-top: 8px; letter-spacing: .4px;
            border: 1.5px solid #b8cfe8;
        }

        /* Form */
        .form-label-custom {
            font-size: 11px; font-weight: 700;
            color: #475569; text-transform: uppercase;
            letter-spacing: .5px; margin-bottom: 6px;
            display: block;
        }
        .form-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            color: #1e293b;
            background: #f8faff;
            transition: all .2s;
            outline: none;
            font-family: 'Open Sans', sans-serif;
        }
        .form-input:focus {
            border-color: #2471a3;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26,58,108,.1);
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            font-size: 16px; color: #94a3b8;
            pointer-events: none;
        }
        .input-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; cursor: pointer; font-size: 16px;
            padding: 0;
        }
        .input-toggle:hover { color: #2471a3; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1a3a6c;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: background .2s, transform .1s;
            letter-spacing: .3px;
        }
        .btn-login:hover { background: #2471a3; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: .7; cursor: not-allowed; transform: none; }

        .divider-line {
            display: flex; align-items: center; gap: 10px;
            color: #cbd5e1; font-size: 11px;
            margin: 20px 0;
        }
        .divider-line::before, .divider-line::after {
            content: ''; flex: 1; height: 1px;
            background: #e2e8f0;
        }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            color: #64748b; font-size: 12px; font-weight: 500;
            text-decoration: none;
            transition: color .15s;
        }
        .back-link:hover { color: #2471a3; }

        .alert-error {
            background: #fef2f2; border: 1.5px solid #fecaca;
            border-radius: 10px; padding: 12px 14px;
            color: #dc2626; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 18px;
        }

        .footer-note {
            text-align: center;
            color: rgba(255,255,255,.35);
            font-size: 11px;
            margin-top: 18px;
        }
    </style>
</head>
<body>
    <div class="bg-image"></div>
    <div class="accent-circle c1"></div>
    <div class="accent-circle c2"></div>
    <div class="accent-circle c3"></div>

    <div class="login-wrap">
        <div class="login-card">

            {{-- Logo --}}
            <div class="logo-wrap">
                <div class="logo-badge">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="logo-title">IEMELIF Learning Center</div>
                <div class="logo-sub">Integrated School Management System</div>
                <div class="portal-badge">
                    <i class="bi bi-cash-coin"></i> Cashier Portal
                </div>
            </div>

            @if(session('error'))
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('cashier.login.post') }}" id="cashierLoginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label-custom">Email Address</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" name="email" class="form-input"
                            placeholder="cashier@iemelif.edu.ph"
                            value="{{ old('email') }}" autocomplete="email" autofocus required>
                    </div>
                    @error('email')
                        <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" id="cashierPassword" class="form-input"
                            placeholder="Enter your password"
                            autocomplete="current-password" required style="padding-right:42px;">
                        <button type="button" class="input-toggle" onclick="togglePwd()">
                            <i class="bi bi-eye-fill" id="pwd-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- reCAPTCHA --}}
                @if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'your_site_key_here')
                <div style="margin-bottom:18px;display:flex;justify-content:center;">
                    <div id="recaptcha-cashier" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
                @endif

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div style="margin-top:16px;padding:12px;background:#f8f9fa;border-radius:10px;border-left:4px solid #c5a059;">
                <p style="font-size:12px;color:#666;margin:0;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-shield-lock" style="color:#c5a059;font-size:16px;"></i>
                    <span>Authorized cashier personnel only. Unauthorized access is prohibited.</span>
                </p>
            </div>

            <div style="text-align:center;margin-top:16px;padding-top:14px;border-top:1px solid #f0f0f0;">
                <a href="{{ url('/') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i> Back to Main Website
                </a>
            </div>

        </div>
        <div class="footer-note">
            &copy; {{ date('Y') }} IEMELIF Learning Center &nbsp;&bull;&nbsp; Cashier Portal
        </div>
    </div>

    @if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'your_site_key_here')
    <script>
        function onRecaptchaLoad() {
            grecaptcha.render('recaptcha-cashier', {
                'sitekey': '{{ config('services.recaptcha.site_key') }}'
            });
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
    @endif

    <script>
        function togglePwd() {
            var input = document.getElementById('cashierPassword');
            var eye   = document.getElementById('pwd-eye');
            if (input.type === 'password') {
                input.type = 'text';
                eye.className = 'bi bi-eye-slash-fill';
            } else {
                input.type = 'password';
                eye.className = 'bi bi-eye-fill';
            }
        }
        document.getElementById('cashierLoginForm').addEventListener('submit', function() {
            var btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Signing in…';
        });
    </script>
</body>
</html>
