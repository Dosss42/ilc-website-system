<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Approved - IEMELIF Learning Center</title>
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
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .success-box i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .info-box {
            background: #e8f0fb;
            border: 1px solid #b8d0f0;
            color: #1a3a6c;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .credentials {
            background: #fff8dc;
            border: 2px solid #f5a623;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .credentials h3 {
            color: #1a3a6c;
            margin-top: 0;
        }
        .credentials .password {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            letter-spacing: 3px;
            background: #fff;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 15px 0;
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
            <div class="success-box">
                <i class="bi bi-check-circle-fill"></i>
                <h2>Enrollment Approved!</h2>
                <p>Your enrollment application has been successfully approved.</p>
            </div>

            <div class="details">
                <h4>Enrollment Details</h4>
                <p><strong>Student Name:</strong> {{ $studentName }}</p>
                <p><strong>Reference Number:</strong> {{ $referenceNumber }}</p>
                <p><strong>Grade Level:</strong> {{ $gradeLevel }}</p>
                <p><strong>Email:</strong> {{ $studentEmail }}</p>
            </div>

            <div class="credentials">
                <h3>Student Portal Access</h3>
                <p>Your student account has been created. Below are your login credentials:</p>
                
                <div class="credentials-inner">
                    <p><strong>Email:</strong> {{ $studentEmail }}</p>
                    <p><strong>Password:</strong></p>
                    <div class="password">{{ $password }}</div>
                </div>

                <div class="info-box">
                    <i class="bi bi-info-circle"></i>
                    <strong>Important:</strong> Please change your password after your first login for security purposes.
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">
                    <i class="bi bi-box-arrow-in-right"></i> Access Student Portal
                </a>
            </div>

            <div class="info-box">
                <h4>Next Steps:</h4>
                <ol>
                    <li>Log in to your student portal using the credentials above</li>
                    <li>Complete your profile information</li>
                    <li>Pay the enrollment fees (GCash, Cash, or Bank Transfer)</li>
                    <li>Submit required documents to the school office</li>
                    <li>Wait for section assignment and schedule</li>
                </ol>
            </div>

            <div class="info-box">
                <h4>Contact Information:</h4>
                <p><strong>Office Hours:</strong> Monday to Friday, 7:00 AM - 3:00 PM</p>
                <p><strong>Phone:</strong> (046) 000-0000</p>
                <p><strong>Email:</strong> info@iemelif-ilc.edu.ph</p>
                <p><strong>Address:</strong> Brgy Poblacion Central, General Tinio, Nueva Ecija</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} IEMELIF Learning Center. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
