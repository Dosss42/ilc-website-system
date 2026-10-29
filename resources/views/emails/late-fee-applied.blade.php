<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Late Fee Notice - IEMELIF Learning Center</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #e2e8f0;">
    <tr><td>@include('emails.partials.letterhead', ['title' => 'Late Fee Notice', 'tone' => 'red'])</td></tr>
    <tr>
        <td style="padding:16px 32px 8px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#2d3748;">
            <p style="margin:0 0 14px;">Dear Parent/Guardian of <strong>{{ $student->name }}</strong>,</p>
            <p style="margin:0 0 14px;">We wish to inform you that the tuition installment for <strong>{{ $installment->due_date->format('F Y') }}</strong>, which was due on <strong>{{ $dueDate }}</strong>, was not settled by the end of its due date. In accordance with Section 5.2 of the school's Terms and Conditions, a late fee of <strong>&#8369;{{ $lateFee }}</strong> has been added to this installment.</p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:8px 0 16px;border:1px solid #e2e8f0;">
                <tr><td colspan="2" style="padding:10px 14px;background:#e8f0fb;font-weight:bold;color:#1a3a6c;">Account Details</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;width:45%;">Student</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $student->name }}</td></tr>
                @if($enrollment?->reference_number)
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Reference No.</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $enrollment->reference_number }}</td></tr>
                @endif
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Installment</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $installment->month_name }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Due Date</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $dueDate }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Installment Amount</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">&#8369;{{ number_format($installment->amount, 2) }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Late Fee</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#b91c1c;">+ &#8369;{{ $lateFee }}</td></tr>
                <tr><td style="padding:11px 14px;border-top:2px solid #1a3a6c;font-weight:bold;color:#1a3a6c;">Total Amount Now Due</td><td style="padding:11px 14px;border-top:2px solid #1a3a6c;font-size:18px;font-weight:bold;color:#1a3a6c;">&#8369;{{ $totalDue }}</td></tr>
            </table>

            <p style="margin:0 0 14px;padding:12px 14px;background:#f8fafc;border-left:3px solid #f5a623;font-size:13px;color:#4a5568;">The late fee is charged only once for this installment and does not affect the student's attendance or examinations. A reminder will be sent each week until the installment is settled.</p>
        </td>
    </tr>
    <tr><td>@include('emails.partials.payment-footer')</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
