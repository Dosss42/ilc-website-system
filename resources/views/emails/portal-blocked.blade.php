<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #721c24; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .alert { background: #f8d7da; border-left: 4px solid #721c24; padding: 15px; margin: 15px 0; color: #721c24; }
        .blocked { background: #721c24; color: white; padding: 20px; text-align: center; margin: 20px 0; }
        .details { background: white; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .amount { font-size: 24px; font-weight: bold; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Portal Access Suspended</h2>
        </div>
        
        <div class="content">
            <p>Dear Parent/Guardian of {{ $student->name }},</p>
            
            <div class="blocked">
                <h3>⚠️ STUDENT PORTAL ACCESS BLOCKED</h3>
                <p>Due to non-payment of tuition fees for over 3 weeks</p>
            </div>
            
            <p>Your student portal access has been temporarily blocked due to non-payment of the <strong>{{ $installment->month_name }}</strong> tuition fee.</p>
            
            <div class="details">
                <h3>Outstanding Balance:</h3>
                <p><strong>Student:</strong> {{ $student->name }}</p>
                <p><strong>Month:</strong> {{ $installment->month_name }}</p>
                <p><strong>Original Due Date:</strong> {{ $dueDate }}</p>
                <p><strong>Total Amount Due (with late fee):</strong> <span class="amount">₱{{ $totalDue }}</span></p>
            </div>
            
            <div class="alert">
                <strong>🚫 Access Restricted:</strong><br>
                • Cannot view grades<br>
                • Cannot view schedule<br>
                • Cannot download documents<br>
                • Cannot make new payments online (must pay at cashier)
            </div>
            
            <h3>To Restore Portal Access:</h3>
            <ol>
                <li>Visit the school cashier to pay the outstanding balance</li>
                <li>or Pay via GCash and contact the Finance Office with proof of payment</li>
                <li>Once payment is verified, portal access will be restored within 24 hours</li>
            </ol>
            
            <p><strong>For inquiries, contact:</strong><br>
            Finance Office: (046) 123-4567<br>
            Email: finance@iemelif.edu.ph</p>
        </div>
        
        <div class="footer">
            <p>Please settle your balance immediately to restore portal access.</p>
            <p><strong>{{ config('app.name') }}</strong><br>
            This is an urgent automated notification.</p>
        </div>
    </div>
</body>
</html>
