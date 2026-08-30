<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f1923;
            padding: 20px 0;
        }

        .bg-image {
            position: fixed; inset: 0;
            background: url('/images/bg.png') center/cover no-repeat;
            filter: brightness(0.35) blur(2px);
            z-index: 0;
        }

        .auth-wrapper {
            position: relative; z-index: 1;
            width: 100%; max-width: 420px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255,255,255,0.97);
            border-radius: 16px;
            padding: 38px 36px 34px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            animation: fadeUp 0.4s ease;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .logo-row {
            display: flex; align-items: center;
            justify-content: center; gap: 14px; margin-bottom: 16px;
        }

        .logo-circle {
            width: 58px; height: 58px;
            border-radius: 50%; overflow: hidden;
            border: 2px solid #ddd; background: #f0f0f0;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .logo-circle img { width:100%; height:100%; object-fit:cover; }

        .portal-title { text-align: center; margin-bottom: 26px; }
        .portal-title h2 {
            font-size: 19px; font-weight: 700; color: #1a3a6c;
            text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px;
        }
        .portal-title p { font-size: 12px; color: #888; margin: 0; }

        .form-label {
            font-size: 12px; font-weight: 600; color: #444;
            margin-bottom: 5px; display: block;
        }

        .form-control {
            width: 100%; border: 1.5px solid #e0e0e0;
            border-radius: 8px; padding: 11px 14px;
            font-size: 13px; font-family: 'Poppins', sans-serif;
            background: #fafafa; color: #333;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #1a3a6c;
            box-shadow: 0 0 0 3px rgba(26,58,108,0.1);
            background: #fff; outline: none;
        }

        .input-group { display: flex; }
        .input-group .form-control {
            border-right: none;
            border-radius: 8px 0 0 8px;
        }

        .btn-eye {
            border: 1.5px solid #e0e0e0; border-left: none;
            background: #fafafa; border-radius: 0 8px 8px 0;
            padding: 0 14px; color: #888; cursor: pointer;
            display: flex; align-items: center;
            transition: color 0.2s;
        }

        .btn-eye:hover { color: #1a3a6c; }

        .forgot-link {
            font-size: 12px; color: #1a3a6c;
            text-decoration: none; font-weight: 500;
            display: block; text-align: right; margin: 6px 0 4px;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-main {
            width: 100%; background: #1a3a6c; color: #fff;
            border: none; border-radius: 8px; padding: 13px;
            font-size: 14px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px; cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-main:hover { background: #2471a3; transform: translateY(-1px); }
        .btn-main:active { transform: translateY(0); }

        /* ── TWO BOTTOM BUTTONS ── */
        .bottom-actions {
            display: flex; gap: 10px; margin-top: 20px;
        }

        .btn-outline-admission {
            flex: 1; display: flex; align-items: center;
            justify-content: center; gap: 7px;
            padding: 11px; border: 1.5px solid #1a3a6c;
            border-radius: 8px; background: #fff;
            font-size: 13px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer; color: #1a3a6c;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-admission:hover {
            background: #1a3a6c; color: #fff;
        }

        .btn-outline-register {
            flex: 1; display: flex; align-items: center;
            justify-content: center; gap: 7px;
            padding: 11px; border: 1.5px solid #f5a623;
            border-radius: 8px; background: #fff;
            font-size: 13px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer; color: #c87800;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-register:hover {
            background: #f5a623; color: #fff;
        }

        .bottom-label {
            text-align: center; font-size: 11px;
            color: #aaa; margin-top: 14px;
        }

        .alert {
            border-radius: 8px; font-size: 13px;
            padding: 10px 14px; margin-bottom: 14px;
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
            <h2>AIMS</h2>
            {{-- CHANGE: Update school name --}}
            <p>IEMELIF Learning Center — General Tinio, Nueva Ecija</p>
        </div>

        {{-- Error Messages --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- Success (after registration by admin) --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- =============================================
             FORM ACTION: Change to your login route
             Example: action="{{ route('login.submit') }}"
             ============================================= --}}
        <form method="POST" action="/login">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       placeholder="Enter your email"
                       value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-1">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="lPass"
                           class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="btn-eye" onclick="togglePass('lPass','lEye')">
                        <i class="bi bi-eye" id="lEye"></i>
                    </button>
                </div>
            </div>

            {{-- =============================================
                 FORGOT PASSWORD: Change href to your route
                 Example: href="{{ route('password.request') }}"
                 ============================================= --}}
            <a href="#" class="forgot-link">Forgot password?</a>

            <button type="submit" class="btn-main">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        {{-- ══════════════════════════════════════
             TWO BOTTOM BUTTONS:
             1. Apply for Admission (public)
             2. Already applied? Register account
        ══════════════════════════════════════ --}}
        <div class="bottom-actions">
            {{-- Button 1: Go to Admission page --}}
            {{-- CHANGE: href="{{ route('admission') }}" --}}
            <a href="/admission" class="btn-outline-admission">
                <i class="bi bi-clipboard-check-fill"></i> Apply Now
            </a>

            {{-- Button 2: Already approved? Create account --}}
            {{-- This should only be accessible with an approval token
                 sent by admin via email. For now links to register page.
                 CHANGE: href="{{ route('register') }}" --}}
            <a href="/register" class="btn-outline-register">
                <i class="bi bi-person-plus-fill"></i> Register
            </a>
        </div>

        <div class="bottom-label">
            "Apply Now" for new students &nbsp;|&nbsp; "Register" if you have an approval code
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
</script>
</body>
</html>