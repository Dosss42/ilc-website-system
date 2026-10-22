<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Emergency Recovery was used</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
                <tr>
                    <td style="background:linear-gradient(135deg,#b91c1c,#dc2626);padding:32px 40px;text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:.5px;">IEMELIF Learning Center</div>
                        <div style="font-size:13px;color:rgba(255,255,255,.8);margin-top:4px;">Emergency Recovery alert</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:40px;">
                        <div style="font-size:15px;color:#374151;margin-bottom:20px;line-height:1.6;">
                            The Emergency Recovery page of the school system was just used:
                        </div>
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:18px 20px;margin-bottom:24px;font-size:14px;color:#7f1d1d;line-height:1.6;">
                            <strong>{{ $description }}</strong><br>
                            When: {{ $when }}<br>
                            From address: {{ $ip }}
                        </div>
                        <div style="font-size:13px;color:#374151;line-height:1.6;">
                            If this was not planned, someone may have the Recovery Key. Log in, check
                            <strong>System Logs</strong>, and replace the key (<code>php artisan recovery:key</code>).
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
