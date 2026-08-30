<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Account Created - IEMELIF Learning Center</title>
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
            background: linear-gradient(135deg, #7b1fa2, #9c27b0);
            color: white;
            padding: 30px;
            text-align: center;
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
        .welcome-box {
            background: #f3e8ff;
            border: 1px solid #d1b3ff;
            color: #4a148c;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
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
            background: #7b1fa2;
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
        <div class="header">
            <h1>IEMELIF Learning Center</h1>
            <p>General Tinio, Nueva Ecija</p>
        </div>

        <div class="content">
            <div class="welcome-box">
                <h2>Welcome, {{ $teacherName }}!</h2>
                <p>Your teacher account has been created by the school administrator.</p>
            </div>

            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <p>Use the following credentials to access the Teacher Portal:</p>

                <div>
                    <p><strong>Email:</strong> {{ $teacherEmail }}</p>
                    <p><strong>Password:</strong></p>
                    <div class="password">{{ $password }}</div>
                </div>

                <div class="info-box">
                    <strong>Important:</strong> Please change your password after your first login for security purposes.
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Access Teacher Portal</a>
            </div>

            <div class="info-box">
                <h4>What You Can Do:</h4>
                <ol>
                    <li>Log in to your teacher dashboard</li>
                    <li>View your assigned subjects and sections</li>
                    <li>Manage your class schedules</li>
                    <li>Update your profile information</li>
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
