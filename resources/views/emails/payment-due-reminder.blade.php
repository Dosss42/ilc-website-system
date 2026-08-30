<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Due Reminder - IEMELIF Learning Center</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header.urgent {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .alert-box {
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .alert-warning {
            background: #fff3cd;
            border: 2px solid #f5a623;
            color: #856404;
        }
        .alert-urgent {
            background: #f8d7da;
            border: 2px solid #e74c3c;
            color: #721c24;
        }
        .amount-box {
            background: #e8f0fb;
            border: 2px solid #2980b9;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .amount-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .details-table td:first-child {
            font-weight: 600;
            color: #555;
            width: 45%;
        }
        .info-box {
            background: #e8f0fb;
            border: 1px solid #b8d0f0;
            color: #1a3a6c;
            padding: 15px 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #e67e22;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $daysUntilDue <= 1 ? 'urgent' : '' }}">
            <h1>IEMELIF Learning Center</h1>
            <p>Payment Due Reminder</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $studentName }}</strong>,</p>

            @if($daysUntilDue <= 1)
            <div class="alert-box alert-urgent">
                <h2 style="margin:0 0 5px;">⚠ Payment Due Tomorrow!</h2>
                <p style="margin:0;">Your payment is due <strong>{{ $dueDate }}</strong>. Please settle your balance immediately.</p>
            </div>
            @elseif($daysUntilDue <= 3)
            <div class="alert-box alert-warning">
                <h2 style="margin:0 0 5px;">📋 Payment Reminder</h2>
                <p style="margin:0;">Your payment is due in <strong>{{ $daysUntilDue }} day{{ $daysUntilDue > 1 ? 's' : '' }}</strong> on <strong>{{ $dueDate }}</strong>.</p>
            </div>
            @endif

            <div class="amount-box">
                <p style="margin:0; color:#7f8c8d;">Amount Due</p>
                <div class="amount">₱{{ $amountDue }}</div>
                <p style="margin:0; font-size:13px; color:#95a5a6;">
                    {{ $paymentType === 'installment' ? 'Installment Payment' : 'Full Payment' }}
                </p>
            </div>

            <table class="details-table">
                <tr>
                    <td>Reference Number</td>
                    <td>{{ $referenceNumber }}</td>
                </tr>
                <tr>
                    <td>Payment Type</td>
                    <td>{{ ucfirst($paymentType) }}</td>
                </tr>
                <tr>
                    <td>Total Fee</td>
                    <td>₱{{ $totalFee }}</td>
                </tr>
                <tr>
                    <td>Amount Paid</td>
                    <td>₱{{ $amountPaid }}</td>
                </tr>
                @if($paymentType === 'installment')
                <tr>
                    <td>Remaining Balance</td>
                    <td><strong>₱{{ $remainingBalance }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td>Due Date</td>
                    <td><strong>{{ $dueDate }}</strong></td>
                </tr>
            </table>

            <div class="info-box">
                <h4 style="margin:0 0 10px;">Payment Methods Available:</h4>
                <ul style="margin:0; padding-left:20px;">
                    <li><strong>Cash</strong> — Pay directly at the school registrar's office</li>
                    <li><strong>GCash</strong> — Send to the official school GCash number</li>
                    <li><strong>Bank Transfer</strong> — Deposit to the school's bank account</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Go to Student Portal</a>
            </div>

            <div class="info-box">
                <p style="margin:0;"><strong>Need help?</strong> Contact us at the registrar's office during office hours (Mon-Fri, 7:00 AM - 3:00 PM).</p>
                <p style="margin:5px 0 0;"><strong>Email:</strong> info@iemelif-ilc.edu.ph</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} IEMELIF Learning Center. All rights reserved.</p>
            <p>This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
