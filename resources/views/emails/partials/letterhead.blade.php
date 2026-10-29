{{--
    Formal school letterhead for the payment emails: logo, school name, a gold rule and the notice title.
    The logo is embedded in the email itself (cid:), so it shows in Gmail/Outlook even though the app runs
    on localhost; outside a real send (e.g. $mailable->render() previews) it falls back to an inline copy.
    Params: $title (e.g. "Late Fee Notice"), optional $tone: 'navy' (default) | 'red' | 'amber'.
--}}
@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = isset($message)
        ? $message->embed($logoPath)
        : 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    $toneColor = ['red' => '#b91c1c', 'amber' => '#b45309'][$tone ?? 'navy'] ?? '#1a3a6c';
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
    <tr>
        <td align="center" style="padding:28px 24px 16px;">
            <img src="{{ $logoSrc }}" width="84" height="84" alt="IEMELIF Learning Center" style="display:block;width:84px;height:84px;border:0;">
            <div style="margin-top:12px;font-family:Arial,Helvetica,sans-serif;font-size:20px;font-weight:bold;letter-spacing:1px;color:#1a3a6c;">IEMELIF LEARNING CENTER</div>
            <div style="margin-top:2px;font-family:Arial,Helvetica,sans-serif;font-size:12px;letter-spacing:.5px;color:#718096;">Finance Office</div>
        </td>
    </tr>
    <tr>
        <td style="padding:0 24px;"><div style="height:3px;background:#f5a623;line-height:3px;font-size:0;">&nbsp;</div></td>
    </tr>
    <tr>
        <td align="center" style="padding:18px 24px 6px;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;color:{{ $toneColor }};">
            {{ $title }}
        </td>
    </tr>
</table>
