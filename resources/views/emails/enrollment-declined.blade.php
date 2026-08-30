<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Update - IEMELIF Learning Center</title>
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
            background: #1a3a6c;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .decline-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .decline-box i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .reason-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .reason-box h4 {
            margin-top: 0;
            color: #856404;
        }
        .info-box {
            background: #e8f0fb;
            border: 1px solid #b8d0f0;
            color: #1a3a6c;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #f5a623;
            color: #1a3a6c;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn:hover {
            background: #e89611;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .details h4 {
            margin-top: 0;
            color: #1a3a6c;
        }
        .details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IEMELIF Learning Center</h1>
            <p>General Tinio, Nueva Ecija</p>
        </div>
        
        <div class="content">
            <div class="decline-box">
                <i class="bi bi-x-circle-fill"></i>
                <h2>Enrollment Application Update</h2>
                <p>We regret to inform you that your enrollment application has been declined.</p>
            </div>

            <div class="details">
                <h4>Application Details</h4>
                <p><strong>Student Name:</strong> {{ $studentName }}</p>
                <p><strong>Reference Number:</strong> {{ $referenceNumber }}</p>
                <p><strong>Grade Level:</strong> {{ $gradeLevel }}</p>
            </div>

            <div class="reason-box">
                <h4><i class="bi bi-exclamation-triangle me-2"></i>Reason for Decline</h4>
                <p>{{ $reason }}</p>
            </div>

            <div class="info-box">
                <h4>What You Can Do:</h4>
                <ol>
                    <li>Review the reason provided above</li>
                    <li>Complete any missing requirements if applicable</li>
                    <li>Contact the school registrar for clarification</li>
                    <li>You may submit a new application once issues are resolved</li>
                </ol>
            </div>

            <div class="info-box">
                <h4>Contact Information:</h4>
                <p>If you have questions or need assistance, please contact us:</p>
                <p><strong>Office Hours:</strong> Monday to Friday, 7:00 AM - 3:00 PM</p>
                <p><strong>Phone:</strong> {{ $contactPhone }}</p>
                <p><strong>Email:</strong> {{ $contactEmail }}</p>
                <p><strong>Address:</strong> Brgy Poblacion Central, General Tinio, Nueva Ecija</p>
            </div>

            <div style="text-align: center;">
                <p>We encourage you to reach out to us if you believe this decision was made in error or if you need assistance with the enrollment process.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} IEMELIF Learning Center. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
