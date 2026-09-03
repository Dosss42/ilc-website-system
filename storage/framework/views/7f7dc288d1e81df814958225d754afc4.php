<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #1a3a6c 0%, #0f2349 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 52px 48px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        }
        .icon-wrap {
            width: 88px; height: 88px;
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 8px 24px rgba(245,166,35,0.25);
        }
        .icon-wrap i { font-size: 40px; color: #e67e22; }
        h1 { font-size: 26px; font-weight: 700; color: #1a3a6c; margin-bottom: 10px; }
        .subtitle { font-size: 15px; color: #555; line-height: 1.7; margin-bottom: 28px; }
        .badge-maintenance {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff8ec; border: 1.5px solid #f5a623;
            color: #b45309; border-radius: 20px;
            padding: 6px 18px; font-size: 13px; font-weight: 600; margin-bottom: 28px;
        }
        .badge-maintenance span {
            width: 8px; height: 8px; border-radius: 50%; background: #f5a623;
            animation: pulse 1.2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.75); }
        }
        .info-box {
            background: #f8fafc; border-radius: 12px; padding: 18px 20px;
            font-size: 13px; color: #666; line-height: 1.7;
            border: 1px solid #e5e7eb; margin-bottom: 28px;
        }
        .school-name {
            font-size: 13px; font-weight: 600; color: #1a3a6c; opacity: 0.6; margin-top: 8px;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            background: #1a3a6c; color: #fff; border: none;
            border-radius: 10px; padding: 12px 28px;
            font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-back:hover { background: #2471a3; color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="bi bi-tools"></i>
        </div>

        <div class="badge-maintenance">
            <span></span> System Maintenance
        </div>

        <h1>We'll be right back!</h1>
        <p class="subtitle">
            The portal is currently undergoing scheduled maintenance.<br>
            We apologize for the inconvenience.
        </p>

        <div class="info-box">
            <strong>What's happening?</strong><br>
            Our team is performing updates to improve your experience.
            The system will be back online shortly.<br><br>
            For urgent concerns, please contact the school office directly.
        </div>

        <a href="<?php echo e(route('home')); ?>" class="btn-back">
            <i class="bi bi-house-fill"></i> Back to Home
        </a>

        <div class="school-name">IEMELIF Learning Center</div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\maintenance.blade.php ENDPATH**/ ?>