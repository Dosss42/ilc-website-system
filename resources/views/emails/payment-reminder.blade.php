<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a3a6c; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .alert { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
        .details { background: white; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .amount { font-size: 24px; font-weight: bold; color: #1a3a6c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Reminder</h2>
        </div>
        
        <div class="content">
            <p>Dear Parent/Guardian of {{ $student->name }},</p>
            
            <p>This is a friendly reminder that your tuition payment for <strong>{{ $installment->month_name }}</strong> is now overdue.</p>
            
            <div class="alert">
                <strong>⚠️ Please settle within 1 week to avoid late fees.</strong>
            </div>
            
            <div class="details">
                <h3>Payment Details:</h3>
                <p><strong>Student:</strong> {{ $student->name }}</p>
                <p><strong>Month:</strong> {{ $installment->month_name }}</p>
                <p><strong>Due Date:</strong> {{ $dueDate }}</p>
                <p><strong>Amount Due:</strong> <span class="amount">₱{{ $amount }}</span></p>
                @if($enrollment->grade_level)
                    <p><strong>Grade Level:</strong> {{ $enrollment->grade_level_display }}</p>
                @endif
            </div>
            
            <p><strong>Payment Options:</strong></p>
            <ul>
                <li>GCash - Transfer to school account and upload screenshot</li>
                <li>Cash - Pay directly at the school cashier</li>
            </ul>
            
            <p>Please log in to your student portal to make the payment or visit the school cashier.</p>
            
            <p><a href="{{ route('student.portal') }}" style="background: #1a3a6c; color: white; padding: 12px 24px; text-decoration: none; display: inline-block; border-radius: 4px;">Access Student Portal</a></p>
        </div>
        
        <div class="footer">
            <p>Thank you for your prompt attention to this matter.</p>
            <p><strong>{{ config('app.name') }}</strong><br>
            This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
