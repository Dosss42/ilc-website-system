{{-- Shared closing for the payment emails: how to pay, the signature and the automated-message note. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#2d3748;">
    <tr>
        <td style="padding:8px 32px 0;">
            <p style="margin:0 0 6px;font-weight:bold;color:#1a3a6c;">Payment may be made through either of the following:</p>
            <ul style="margin:0 0 16px;padding-left:20px;">
                <li><strong>Online (GCash)</strong> &mdash; through the Student Portal, Payment section</li>
                <li><strong>Cash</strong> &mdash; at the school cashier</li>
            </ul>
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:4px 0 20px;">
                <tr>
                    <td style="background:#1a3a6c;border-radius:4px;">
                        <a href="{{ route('login') }}" style="display:inline-block;padding:11px 26px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">Go to the Student Portal</a>
                    </td>
                </tr>
            </table>
            <p style="margin:0 0 4px;">If payment has already been made, kindly disregard this notice. For questions about your account, please visit the Finance Office during office hours.</p>
            <p style="margin:20px 0 0;">Respectfully,</p>
            <p style="margin:0 0 24px;"><strong>Finance Office</strong><br>IEMELIF Learning Center</p>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:16px 24px;background:#f0f4f8;border-top:1px solid #e2e8f0;font-size:12px;color:#718096;">
            This is an automated notice from the IEMELIF Learning Center school system. Please do not reply to this email.<br>
            &copy; {{ date('Y') }} IEMELIF Learning Center. All rights reserved.
        </td>
    </tr>
</table>
