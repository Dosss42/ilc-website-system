<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: #0f1923;
        }
        .bg-image {
            position: fixed; inset: 0;
            background: url('/images/bg.png') center/cover no-repeat;
            filter: brightness(0.35) blur(2px);
            z-index: 0;
        }
        .card-wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px; padding: 20px;
        }
        .verify-card {
            background: #fff; border-radius: 16px;
            padding: 40px 36px; text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }
        .icon-wrap {
            width: 72px; height: 72px;
            background: #eaf3fb; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-wrap i { font-size: 34px; color: #1a3a6c; }
        h2 { font-size: 20px; font-weight: 700; color: #1a3a6c; margin-bottom: 8px; }
        p  { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .btn-main {
            width: 100%; background: #1a3a6c; color: #fff;
            border: none; border-radius: 8px; padding: 13px;
            font-size: 14px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-main:hover { background: #2471a3; }
        .logout-link {
            display: block; margin-top: 16px;
            font-size: 12px; color: #aaa; text-decoration: none;
        }
        .logout-link:hover { color: #e74c3c; }
        .alert {
            border-radius: 8px; font-size: 13px;
            padding: 10px 14px; margin-bottom: 16px;
        }
    </style>
</head>
<body>
<div class="bg-image"></div>

<div class="card-wrap">
    <div class="verify-card">

        <div class="icon-wrap">
            <i class="bi bi-envelope-check"></i>
        </div>

        <h2>Verify Your Email</h2>
        <p>
            Thanks for registering! We sent a verification link to<br>
            <strong>{{ Auth::user()->email }}</strong><br><br>
            Click the link in your email to activate your account.
            Check your spam/junk folder if you don't see it.
        </p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Resend verification email --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-main">
                <i class="bi bi-send me-1"></i> Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;">
                <i class="bi bi-box-arrow-left me-1"></i> Back to Login
            </button>
        </form>

    </div>
</div>
</body>
</html>
