<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Due Reminder - IEMELIF Learning Center</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;">
@php $lateFeeText = '&#8369;' . number_format(\App\Services\PaymentService::LATE_FEE_AMOUNT, 2); @endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #e2e8f0;">
    <tr><td>@include('emails.partials.letterhead', ['title' => $daysUntilDue <= 0 ? 'Payment Due Today' : 'Payment Reminder', 'tone' => $daysUntilDue <= 1 ? 'red' : 'navy'])</td></tr>
    <tr>
        <td style="padding:16px 32px 8px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#2d3748;">
            <p style="margin:0 0 14px;">Dear Parent/Guardian of <strong>{{ $studentName }}</strong>,</p>

            @if($daysUntilDue <= 0)
            <p style="margin:0 0 14px;">This is to remind you that the tuition installment is due <strong>today, {{ $dueDate }}</strong>. If it remains unpaid at the end of the day, a late fee of <strong>{!! $lateFeeText !!}</strong> will be added to the installment the following morning.</p>
            @elseif($daysUntilDue == 1)
            <p style="margin:0 0 14px;">This is to remind you that the tuition installment is due <strong>tomorrow, {{ $dueDate }}</strong>. Kindly settle it on or before the due date to avoid the late fee of <strong>{!! $lateFeeText !!}</strong>.</p>
            @else
            <p style="margin:0 0 14px;">This is a courteous reminder that the tuition installment is due in <strong>{{ $daysUntilDue }} days, on {{ $dueDate }}</strong>. A late fee of <strong>{!! $lateFeeText !!}</strong> is added if it is not settled by the end of the due date.</p>
            @endif

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:8px 0 16px;border:1px solid #e2e8f0;">
                <tr><td colspan="2" style="padding:10px 14px;background:#e8f0fb;font-weight:bold;color:#1a3a6c;">Account Details</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;width:45%;">Student</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $studentName }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Reference No.</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $referenceNumber }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Due Date</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">{{ $dueDate }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Total School Fees</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">&#8369;{{ $totalFee }}</td></tr>
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Amount Paid</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">&#8369;{{ $amountPaid }}</td></tr>
                @if($paymentType === 'installment')
                <tr><td style="padding:9px 14px;border-top:1px solid #e2e8f0;color:#718096;">Remaining Balance</td><td style="padding:9px 14px;border-top:1px solid #e2e8f0;">&#8369;{{ $remainingBalance }}</td></tr>
                @endif
                <tr><td style="padding:11px 14px;border-top:2px solid #1a3a6c;font-weight:bold;color:#1a3a6c;">Amount Due by {{ $dueDate }}</td><td style="padding:11px 14px;border-top:2px solid #1a3a6c;font-size:18px;font-weight:bold;color:#1a3a6c;">&#8369;{{ $amountDue }}</td></tr>
            </table>
        </td>
    </tr>
    <tr><td>@include('emails.partials.payment-footer')</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
