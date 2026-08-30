<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Poppins',sans-serif;
            min-height:100vh; display:flex;
            align-items:center; justify-content:center;
            background:#0f1923;
        }
        .bg-image {
            position:fixed; inset:0;
            background:url('/images/bg.png') center/cover no-repeat;
            filter:brightness(0.35) blur(2px); z-index:0;
        }
        .wrapper {
            position:relative; z-index:1;
            width:100%; max-width:400px; padding:20px;
        }
        .card {
            background:rgba(255,255,255,0.97);
            border-radius:16px; padding:38px 36px;
            box-shadow:0 25px 60px rgba(0,0,0,0.5);
            text-align:center;
        }
        .otp-icon {
            width:70px; height:70px; background:#e8f0fb;
            border-radius:50%; display:flex;
            align-items:center; justify-content:center;
            margin:0 auto 18px;
        }
        .otp-icon i { font-size:30px; color:#1a3a6c; }
        h2 { font-size:20px; font-weight:700; color:#1a3a6c; margin-bottom:6px; }
        p  { font-size:12px; color:#888; margin-bottom:24px; }

        .otp-inputs {
            display:flex; gap:10px;
            justify-content:center; margin-bottom:8px;
        }
        .otp-inputs input {
            width:46px; height:54px; text-align:center;
            font-size:20px; font-weight:700;
            border:2px solid #e0e0e0; border-radius:8px;
            font-family:'Poppins',sans-serif; transition:border 0.2s;
        }
        .otp-inputs input:focus {
            border-color:#1a3a6c; outline:none;
            box-shadow:0 0 0 3px rgba(26,58,108,0.1);
        }
        .timer { font-size:12px; color:#aaa; margin-bottom:16px; }
        .timer span { font-weight:700; color:#e74c3c; }

        .btn-verify {
            width:100%; background:#1a3a6c; color:#fff;
            border:none; border-radius:8px; padding:13px;
            font-size:14px; font-weight:600;
            font-family:'Poppins',sans-serif;
            cursor:pointer; transition:background 0.2s;
        }
        .btn-verify:hover { background:#2471a3; }

        .resend-area {
            margin-top:18px; font-size:12px; color:#888;
        }

        .btn-resend {
            background:none; border:none; padding:0;
            cursor:pointer; color:#1a3a6c; font-weight:600;
            font-family:'Poppins',sans-serif; font-size:12px;
            text-decoration:none;
        }
        .btn-resend:hover { text-decoration:underline; }
        .btn-resend:disabled { color:#aaa; cursor:not-allowed; }

        .alert {
            border-radius:8px; font-size:13px;
            padding:10px 14px; margin-bottom:16px;
            text-align:left;
        }
        .back-link {
            display:block; margin-top:14px;
            font-size:12px; color:#888; text-decoration:none;
        }
        .back-link:hover { color:#1a3a6c; }
    </style>
</head>
<body>
<div class="bg-image"></div>
<div class="wrapper">
    <div class="card">

        <div class="otp-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <h2>OTP Verification</h2>
        <p>Enter the 6-digit code sent to your registered email address.</p>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify.submit') }}" id="otpForm">
            @csrf
            <input type="hidden" name="otp" id="otpHidden">

            {{-- 6 individual input boxes --}}
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
            </div>

            <div class="timer">
                Expires in: <span id="countdown">05:00</span>
            </div>

            <button type="submit" class="btn-verify">
                <i class="bi bi-shield-check me-1"></i> Verify OTP
            </button>
        </form>

        <div class="resend-area">
            Didn't receive it?
            <form method="POST" action="{{ route('otp.resend') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-resend" id="resendBtn">
                    Resend OTP
                </button>
            </form>
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left me-1"></i> Back to Login
        </a>

    </div>
</div>

<script>
    // ── Auto-move between OTP boxes ──
    const boxes = document.querySelectorAll('.otp-box');

    boxes.forEach((box, i) => {
        box.addEventListener('input', () => {
            // Only allow numbers
            box.value = box.value.replace(/[^0-9]/g, '');
            if (box.value && i < boxes.length - 1) boxes[i+1].focus();
            updateHidden();
        });

        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && i > 0) {
                boxes[i-1].focus();
            }
        });

        // Handle paste
        box.addEventListener('paste', e => {
            e.preventDefault();
            const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g,'');
            [...pasted].forEach((char, j) => {
                if (boxes[i+j]) boxes[i+j].value = char;
            });
            updateHidden();
            const next = Math.min(i + pasted.length, boxes.length - 1);
            boxes[next].focus();
        });
    });

    function updateHidden() {
        document.getElementById('otpHidden').value =
            [...boxes].map(b => b.value).join('');
    }

    // ── Countdown timer (5 minutes) ──
    let totalSeconds = 300;
    const timerEl = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');

    const interval = setInterval(() => {
        totalSeconds--;
        const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const s = String(totalSeconds % 60).padStart(2, '0');
        timerEl.textContent = `${m}:${s}`;

        if (totalSeconds <= 0) {
            clearInterval(interval);
            timerEl.textContent = 'Expired';
            timerEl.style.color = '#e74c3c';
            resendBtn.disabled = false;
        }
    }, 1000);
</script>
</body>
</html>