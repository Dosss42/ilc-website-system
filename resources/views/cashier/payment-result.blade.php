<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $outcome === 'failed' ? 'Payment Not Completed' : 'Payment Received' }} — IEMELIF Learning Center</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f0f4f8;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(26,58,108,0.12);
        max-width: 420px;
        width: 100%;
        padding: 40px 32px;
        text-align: center;
    }
    .icon {
        width: 64px; height: 64px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 18px;
        font-size: 30px; color: #fff;
    }
    .icon.success { background: #16a34a; }
    .icon.failed  { background: #dc2626; }
    h1 { font-size: 19px; color: #1a3a6c; margin-bottom: 8px; }
    p { font-size: 13.5px; color: #64748b; line-height: 1.6; margin-bottom: 4px; }
    .school { margin-top: 26px; padding-top: 18px; border-top: 1px solid #eef1f5; font-size: 11px; color: #9aa5b1; }
</style>
</head>
<body>
    <div class="card">
        @if($outcome === 'failed')
            <div class="icon failed">&#10005;</div>
            <h1>Payment Not Completed</h1>
            <p>This payment link expired or was not completed.</p>
            <p>Please go back and try again, or ask the cashier for a new payment link.</p>
        @else
            <div class="icon success">&#10003;</div>
            <h1>Payment Received!</h1>
            <p>Thank you — your payment has been submitted successfully.</p>
            <p>You may now close this tab.</p>
        @endif
        <div class="school">IEMELIF Learning Center &middot; General Tinio, Nueva Ecija</div>
    </div>
</body>
</html>
