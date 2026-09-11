<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — IEMELIF Learning Center</title>

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

        <form method="POST" action="{{ route('password.update') }}">
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
                <div class="hint-text">Must match the password above.</div>
            </div>

            <button type="submit" class="btn-main">
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
</script>

</body>
</html>
