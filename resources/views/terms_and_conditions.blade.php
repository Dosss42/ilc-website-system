<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - ILC Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
            line-height: 1.8;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        h1 {
            color: #1a3a6c;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        h2 {
            color: #1a3a6c;
            font-size: 22px;
            font-weight: 600;
            margin-top: 35px;
            margin-bottom: 15px;
        }
        h3 {
            color: #2471a3;
            font-size: 18px;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        p, li {
            color: #4a5568;
            font-size: 15px;
            margin-bottom: 12px;
        }
        ul, ol {
            padding-left: 25px;
            margin-bottom: 20px;
        }
        li {
            margin-bottom: 10px;
        }
        .highlight {
            background: #e8f0fb;
            padding: 15px;
            border-left: 4px solid #1a3a6c;
            border-radius: 4px;
            margin: 20px 0;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #1a3a6c;
            text-decoration: none;
            font-weight: 600;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .last-updated {
            color: #888;
            font-size: 13px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        
        <!-- Logo and School Name Header -->
        <div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0;">
            <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 15px;">
                <img src="{{ asset('images/logo1.png') }}" alt="ILC Logo" style="height: 80px; width: auto;">
                <img src="{{ asset('images/logoo.jpg') }}" alt="ILC Logo" style="height: 80px; width: auto;">
            </div>
            <h1 style="color: #1a3a6c; font-weight: 700; margin-bottom: 5px;">IEMELIF LEARNING CENTER</h1>
            <p style="color: #666; font-size: 16px; margin: 0;">General Tinio, Nueva Ecija</p>
        </div>
        
        <h2 style="text-align: center; color: #1a3a6c; font-weight: 700; margin-bottom: 10px;">Terms and Conditions</h2>
        <p class="subtitle" style="text-align: center; margin-bottom: 40px;">Online Enrollment System</p>
        
        <div class="highlight">
            <strong>Last Updated:</strong> {{ now()->format('F j, Y') }}<br>
            <strong>Effective Date:</strong> {{ now()->format('F j, Y') }}
        </div>

        <h2>1. Acceptance of Terms</h2>
        <p>By accessing, registering, and using the ILC Online Enrollment System, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use this system.</p>
        
        <h2>2. Eligibility</h2>
        <p>The ILC Online Enrollment System is available to:</p>
        <ul>
            <li>Students seeking enrollment at Immaculate Learning Center</li>
            <li>Parents or legal guardians of students</li>
            <li>Authorized school staff and administrators</li>
        </ul>
        <p>You must be at least 18 years old or have parental consent to use this system on behalf of a minor.</p>

        <h2>3. User Account Responsibilities</h2>
        <h3>3.1 Account Creation</h3>
        <p>Users are responsible for maintaining the confidentiality of their account credentials. You agree to:</p>
        <ul>
            <li>Keep your password secure and not share it with anyone</li>
            <li>Notify the school immediately if you suspect unauthorized access</li>
            <li>Provide accurate and complete information during registration</li>
            <li>Update your information when it changes</li>
        </ul>
        
        <h3>3.2 Account Security</h3>
        <p>You are solely responsible for all activities that occur under your account. ILC is not liable for any loss or damage arising from your failure to secure your account.</p>

        <h2>4. Enrollment Policies</h2>
        <h3>4.1 Application Process</h3>
        <p>Enrollment applications are subject to review and approval by ILC. Submission of an application does not guarantee enrollment. The school reserves the right to:</p>
        <ul>
            <li>Accept or reject enrollment applications</li>
            <li>Request additional documentation</li>
            <li>Cancel enrollment for non-compliance with requirements</li>
        </ul>

        <h3>4.2 Required Documents</h3>
        <p>Students must submit all required documents as specified in the enrollment checklist, including but not limited to:</p>
        <ul>
            <li>Birth certificate (NSO/PSA copy)</li>
            <li>Report cards or Form 138</li>
            <li>Good moral certificate</li>
            <li>Recent ID photos</li>
            <li>Other documents as required by the school</li>
        </ul>

        <h3>4.3 Enrollment Payment</h3>
        <p>Enrollment is considered complete upon:</p>
        <ul>
            <li>Submission of all required documents</li>
            <li>Payment of enrollment fees or down payment</li>
            <li>Approval of the application by the school administration</li>
        </ul>
        <p>Payment plans are available as per the school's fee schedule. Failure to pay fees on time may result in enrollment cancellation.</p>

        <h2>5. Payment Terms</h2>
        <h3>5.1 Payment Methods</h3>
        <p>ILC accepts payment through:</p>
        <ul>
            <li>Cash (at the school's finance office)</li>
            <li>GCash</li>
            <li>Bank transfer (details provided upon request)</li>
        </ul>

        <h3>5.2 Payment Deadlines</h3>
        <p>All payments must be made by the specified deadlines. Late payments may incur penalties as outlined in the school's fee schedule.</p>

        <h3>5.3 Refund Policy</h3>
        <p>Refunds are subject to the school's refund policy:</p>
        <ul>
            <li>Full refund if withdrawal is made before the start of classes</li>
            <li>Partial refund if withdrawal is made within the first week of classes</li>
            <li>No refund for withdrawals after the first week of classes</li>
            <li>Special circumstances may be considered on a case-by-case basis</li>
        </ul>

        <h2>6. Academic Integrity and Conduct</h2>
        <h3>6.1 Student Conduct</h3>
        <p>Students enrolled at ILC are expected to:</p>
        <ul>
            <li>Adhere to the school's code of conduct and student handbook</li>
            <li>Respect teachers, staff, and fellow students</li>
            <li>Attend classes regularly and complete assignments on time</li>
            <li>Maintain academic honesty in all school work</li>
        </ul>

        <h3>6.2 Academic Honesty</h3>
        <p>Cheating, plagiarism, or any form of academic dishonesty is strictly prohibited and may result in disciplinary action, including suspension or expulsion.</p>

        <h2>7. Use of School Systems</h2>
        <h3>7.1 Acceptable Use</h3>
        <p>The ILC Online Enrollment System and other school-provided technology must be used for educational purposes only. Prohibited activities include:</p>
        <ul>
            <li>Attempting to hack, disrupt, or damage school systems</li>
            <li>Uploading malicious software or viruses</li>
            <li>Sharing copyrighted material without permission</li>
            <li>Using the system for commercial purposes</li>
            <li>Harassing or threatening other users</li>
        </ul>

        <h3>7.2 Monitoring</h3>
        <p>ILC reserves the right to monitor system usage for security and compliance purposes. Users have no expectation of privacy when using school systems.</p>

        <h2>8. Privacy and Data Protection</h2>
        <p>ILC is committed to protecting your personal information. Our Privacy Policy, which can be found separately, details how we collect, use, store, and protect your data. By using this system, you consent to our data practices as outlined in our Privacy Policy.</p>

        <h2>9. Intellectual Property</h2>
        <p>All content on the ILC Online Enrollment System, including text, graphics, logos, and software, is the property of Immaculate Learning Center and is protected by copyright laws. Users may not reproduce, distribute, or create derivative works without explicit permission.</p>

        <h2>10. Communication</h2>
        <p>By enrolling, you agree to receive communications from ILC through:</p>
        <ul>
            <li>Email (enrollment updates, announcements, reminders)</li>
            <li>SMS (important alerts and notifications)</li>
            <li>Phone calls (for urgent matters)</li>
        </ul>
        <p>You may opt out of non-essential communications by contacting the school administration.</p>

        <h2>11. Termination</h2>
        <p>ILC reserves the right to suspend or terminate access to the enrollment system and cancel enrollment for:</p>
        <ul>
            <li>Violation of these Terms and Conditions</li>
            <li>Non-payment of fees</li>
            <li>Providing false or misleading information</li>
            <li>Behavior that disrupts the learning environment</li>
            <li>Other violations of school policies</li>
        </ul>

        <h2>12. Limitation of Liability</h2>
        <p>ILC shall not be liable for any indirect, incidental, special, or consequential damages arising from the use or inability to use the enrollment system, including but not limited to loss of data, loss of enrollment opportunities, or business interruption.</p>

        <h2>13. Modifications to Terms</h2>
        <p>ILC reserves the right to modify these Terms and Conditions at any time. Changes will be posted on this page with an updated revision date. Continued use of the system after changes constitutes acceptance of the new terms.</p>

        <h2>14. Governing Law</h2>
        <p>These Terms and Conditions are governed by the laws of the Republic of the Philippines. Any disputes arising from these terms shall be resolved in the appropriate courts of the Philippines.</p>

        <h2>15. Contact Information</h2>
        <p>For questions about these Terms and Conditions or the enrollment process, please contact:</p>
        <div class="highlight">
            <strong>Immaculate Learning Center</strong><br>
            Address: Brgy Poblacion Central General Tinio Nueva Ecija<br>
            Phone: 0951-989-9685<br>
            Email: Iemelif_learningcenter@gmail.com<br>
            Office Hours: Monday – Friday 7:30 AM – 5:00 PM
        </div>

        <h2>16. Agreement</h2>
        <p>By clicking "I Agree" or using the ILC Online Enrollment System, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>

        <p class="last-updated">
            <strong>Immaculate Learning Center</strong><br>
            These Terms and Conditions were last updated on {{ now()->format('F j, Y') }}.
        </p>
    </div>
</body>
</html>
