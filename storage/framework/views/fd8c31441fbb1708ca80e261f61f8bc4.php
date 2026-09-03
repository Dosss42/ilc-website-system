<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Change Verification</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
                <tr>
                    <td style="background:linear-gradient(135deg,#1a3a6c,#2471a3);padding:32px 40px;text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:.5px;">IEMELIF Learning Center</div>
                        <div style="font-size:13px;color:rgba(255,255,255,.7);margin-top:4px;">Integrated School Management System</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:40px;">
                        <div style="font-size:15px;color:#374151;margin-bottom:8px;">Hello, <strong><?php echo e($name); ?></strong></div>
                        <div style="font-size:15px;color:#374151;margin-bottom:24px;line-height:1.6;">
                            We received a request to change your account password.<br>
                            Use the verification code below to confirm this action:
                        </div>
                        <div style="background:#f0f7ff;border:2px dashed #2471a3;border-radius:12px;padding:28px;text-align:center;margin-bottom:28px;">
                            <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:2px;margin-bottom:12px;">Verification Code</div>
                            <div style="letter-spacing:14px;font-size:42px;font-weight:900;color:#1a3a6c;font-family:monospace;"><?php echo e($otp); ?></div>
                            <div style="font-size:12px;color:#94a3b8;margin-top:12px;">This code expires in <strong>10 minutes</strong></div>
                        </div>
                        <div style="background:#fff8f0;border:1px solid #fed7aa;border-radius:8px;padding:14px 16px;margin-bottom:24px;">
                            <div style="font-size:13px;color:#92400e;display:flex;align-items:flex-start;gap:8px;">
                                <span style="font-size:16px;">⚠️</span>
                                <span><strong>Do not share this code</strong> with anyone. If you did not request a password change, contact your administrator immediately.</span>
                            </div>
                        </div>
                        <div style="font-size:13px;color:#94a3b8;line-height:1.6;">
                            If you did not make this request, please ignore this email. Your password will not be changed.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8faff;border-top:1px solid #e8edf5;padding:20px 40px;text-align:center;">
                        <div style="font-size:12px;color:#94a3b8;">
                            &copy; <?php echo e(date('Y')); ?> IEMELIF Learning Center — General Tinio, Nueva Ecija<br>
                            This is an automated email, please do not reply.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\emails\password-change-otp.blade.php ENDPATH**/ ?>