<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
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
                        <div style="font-size:15px;color:#374151;margin-bottom:8px;">Hello, <strong>{{ $name }}</strong></div>
                        <div style="font-size:15px;color:#374151;margin-bottom:24px;line-height:1.6;">
                            We received a request to reset the password for your account.<br>
                            Click the button below to choose a new password:
                        </div>
                        <div style="text-align:center;margin-bottom:28px;">
                            <a href="{{ $resetUrl }}"
                               style="display:inline-block;background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;font-weight:700;font-size:14px;padding:14px 32px;border-radius:10px;text-decoration:none;letter-spacing:.3px;">
                                Reset Password
                            </a>
                            <div style="font-size:12px;color:#94a3b8;margin-top:14px;">This link expires in <strong>60 minutes</strong></div>
                        </div>
                        <div style="background:#fff8f0;border:1px solid #fed7aa;border-radius:8px;padding:14px 16px;margin-bottom:24px;">
                            <div style="font-size:13px;color:#92400e;display:flex;align-items:flex-start;gap:8px;">
                                <span style="font-size:16px;">⚠️</span>
                                <span><strong>Did not request this?</strong> You can safely ignore this email — your password will not be changed unless you click the link above and set a new one.</span>
                            </div>
                        </div>
                        <div style="font-size:12px;color:#94a3b8;line-height:1.6;word-break:break-all;">
                            If the button doesn't work, copy and paste this link into your browser:<br>
                            <a href="{{ $resetUrl }}" style="color:#2471a3;">{{ $resetUrl }}</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8faff;border-top:1px solid #e8edf5;padding:20px 40px;text-align:center;">
                        <div style="font-size:12px;color:#94a3b8;">
                            &copy; {{ date('Y') }} IEMELIF Learning Center — General Tinio, Nueva Ecija<br>
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
