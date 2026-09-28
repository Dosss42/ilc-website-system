<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance — IEMELIF Learning Center</title>
    <meta name="robots" content="noindex, nofollow">

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

        /* ── BACKGROUND (same treatment as the login page) ── */
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
            max-width: 440px;
        }

        .auth-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.08);
        }

        /* ── NAVY HEADER ── */
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

        .location-tag {
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

        /* ── BODY ── */
        .card-body-content {
            padding: 34px 32px 30px;
            text-align: center;
        }

        .icon-badge {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #e8f0fb, #dbeafe);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
        }

        .icon-badge i { font-size: 30px; color: #1a3a6c; }

        .status-pill {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff8ec; border: 1.5px solid #f5c667;
            color: #92610a; border-radius: 20px;
            padding: 6px 16px; font-size: 12px; font-weight: 600; margin-bottom: 20px;
        }

        .status-pill span {
            width: 7px; height: 7px; border-radius: 50%; background: #e6a817;
            animation: pulse 1.4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.4; transform: scale(0.75); }
        }

        h1 {
            font-size: 21px;
            font-weight: 700;
            color: #1a3a6c;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 13.5px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 18px;
            font-size: 12.5px;
            color: #6b7280;
            line-height: 1.7;
            text-align: left;
            margin-bottom: 22px;
        }

        .info-box strong { color: #374151; }

        .btn-main {
            width: 100%;
            background: linear-gradient(135deg, #1a3a6c, #1e5799);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(26,58,108,0.35);
        }

        .btn-main:hover {
            background: linear-gradient(135deg, #163062, #1a4a8a);
            box-shadow: 0 6px 20px rgba(26,58,108,0.45);
            transform: translateY(-1px);
            color: #fff;
        }

        .switch-text {
            text-align: center;
            margin-top: 18px;
            font-size: 11.5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="bg-image"></div>

<div class="auth-wrapper">
    <div class="auth-card">

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
            <div class="icon-badge">
                <i class="bi bi-tools"></i>
            </div>

            <div class="status-pill">
                <span></span> Under Maintenance
            </div>

            <h1>We'll be right back!</h1>
            <p class="subtitle">
                <?php if(($type ?? 'portal') === 'site'): ?>
                    Our website is currently undergoing scheduled maintenance.
                <?php else: ?>
                    This portal is currently undergoing scheduled maintenance.
                <?php endif; ?>
                We apologize for the inconvenience.
            </p>

            <div class="info-box">
                <strong>What's happening?</strong><br>
                Our team is performing updates to improve your experience.
                Things will be back online shortly. For urgent concerns,
                please contact the school office directly.
            </div>

            <?php if(($type ?? 'portal') !== 'site'): ?>
            <a href="<?php echo e(route('home')); ?>" class="btn-main">
                <i class="bi bi-house-fill"></i> Back to Home
            </a>
            <?php endif; ?>

            <div class="switch-text">IEMELIF Learning Center</div>
        </div>

    </div>
</div>

</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/maintenance.blade.php ENDPATH**/ ?>