<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - ILC Enrollment System</title>
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
        .highlight-warning {
            background: #fff8e1;
            padding: 15px;
            border-left: 4px solid #f59e0b;
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
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        .data-table th {
            background: #1a3a6c;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        .data-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">
            &#8592; Back
        </a>

        {{-- School Header --}}
        <div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0;">
            <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 15px;">
                <img src="{{ asset('images/logo1.png') }}" alt="ILC Logo" style="height: 80px; width: auto;">
                <img src="{{ asset('images/logoo.jpg') }}" alt="ILC Logo" style="height: 80px; width: auto;">
            </div>
            <h1 style="color: #1a3a6c; font-weight: 700; margin-bottom: 5px;">IEMELIF LEARNING CENTER</h1>
            <p style="color: #666; font-size: 16px; margin: 0;">General Tinio, Nueva Ecija</p>
        </div>

        <h2 style="text-align: center; color: #1a3a6c; font-weight: 700; margin-bottom: 10px;">Privacy Policy</h2>
        <p class="subtitle" style="text-align: center; margin-bottom: 40px;">Online Enrollment System</p>

        <div class="highlight">
            <strong>Last Updated:</strong> {{ now()->format('F j, Y') }}<br>
            <strong>Effective Date:</strong> {{ now()->format('F j, Y') }}
        </div>

        <p>
            IEMELIF Learning Center ("ILC", "we", "us", or "our") is committed to protecting the privacy
            and personal data of our students, parents, guardians, and all users of our Online Enrollment
            System. This Privacy Policy explains how we collect, use, store, share, and protect your
            personal information in compliance with <strong>Republic Act No. 10173</strong>, otherwise known as the
            <strong>Data Privacy Act of 2012</strong>, and its Implementing Rules and Regulations.
        </p>

        <p>
            By using the ILC Online Enrollment System and submitting an enrollment application, you
            acknowledge that you have read and understood this Privacy Policy and you consent to the
            collection, use, and processing of your personal data as described herein.
        </p>


        {{-- Section 1 --}}
        <h2>1. Data Controller Information</h2>
        <p>The personal data you provide through this system is collected and controlled by:</p>
        <div class="highlight">
            <strong>IEMELIF Learning Center</strong><br>
            General Tinio, Nueva Ecija, Philippines<br>
            Phone: 0951-989-9685<br>
            Email: Iemelif_learningcenter@gmail.com<br>
            Data Protection Officer: MRS. Teofila Guillermo
        </div>


        {{-- Section 2 --}}
        <h2>2. Personal Data We Collect</h2>
        <p>
            When you use our Online Enrollment System, we collect the following categories of personal
            data:
        </p>

        <h3>2.1 Student Information</h3>
        <ul>
            <li>Full name (first, middle, last, suffix)</li>
            <li>Date of birth, place of birth, and age</li>
            <li>Gender and nationality</li>
            <li>Learner's Reference Number (LRN)</li>
            <li>Grade level and student type (new, transferee, or returning)</li>
            <li>Previous school attended and last grade completed</li>
        </ul>

        <h3>2.2 Contact and Address Information</h3>
        <ul>
            <li>Home address (province, city/municipality, barangay, street address, ZIP code)</li>
            <li>Contact numbers</li>
            <li>Email address</li>
        </ul>

        <h3>2.3 Parent and Guardian Information</h3>
        <ul>
            <li>Full name of mother, father, and/or legal guardian</li>
            <li>Relationship to the student</li>
            <li>Occupation and age</li>
            <li>Contact number and email address</li>
        </ul>

        <h3>2.4 Health Information</h3>
        <ul>
            <li>Blood type</li>
            <li>Known allergies</li>
            <li>Existing medical conditions or special health needs</li>
        </ul>
        <div class="highlight-warning">
            <strong>Note:</strong> Health information is considered sensitive personal data under the Data Privacy
            Act of 2012. It is collected solely for the safety and well-being of the student and is
            treated with the highest level of confidentiality.
        </div>

        <h3>2.5 Financial Information</h3>
        <ul>
            <li>Selected payment option (A, B, C, or D)</li>
            <li>Payment amounts and outstanding balances</li>
            <li>Payment method (cash or GCash)</li>
            <li>Payment reference numbers</li>
            <li>Payment screenshots submitted for verification</li>
        </ul>

        <h3>2.6 Submitted Documents</h3>
        <ul>
            <li>Birth Certificate (PSA/NSO copy)</li>
            <li>Form 137 (Permanent Record)</li>
            <li>Form 138 (Report Card)</li>
            <li>Certificate of Good Moral Character</li>
            <li>Barangay Clearance</li>
            <li>Other documents required for enrollment</li>
        </ul>

        <h3>2.7 System and Account Data</h3>
        <ul>
            <li>Student portal login credentials (email and encrypted password)</li>
            <li>Account activity and login history</li>
            <li>Enrollment application status and history</li>
        </ul>


        {{-- Section 3 --}}
        <h2>3. Purpose and Legal Basis for Processing</h2>
        <p>We collect and process your personal data for the following purposes:</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Purpose</th>
                    <th>Legal Basis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Processing and managing your enrollment application</td>
                    <td>Fulfillment of a contract / legal obligation</td>
                </tr>
                <tr>
                    <td>Verifying student eligibility and reviewing documents</td>
                    <td>Legitimate interest / legal obligation</td>
                </tr>
                <tr>
                    <td>Creating and managing student portal accounts</td>
                    <td>Fulfillment of a contract</td>
                </tr>
                <tr>
                    <td>Processing payments and managing tuition accounts</td>
                    <td>Fulfillment of a contract / legal obligation</td>
                </tr>
                <tr>
                    <td>Maintaining health records for student safety</td>
                    <td>Protection of vital interests / consent</td>
                </tr>
                <tr>
                    <td>Sending enrollment updates, notifications, and reminders</td>
                    <td>Legitimate interest / consent</td>
                </tr>
                <tr>
                    <td>Assigning students to grade sections and schedules</td>
                    <td>Fulfillment of a contract</td>
                </tr>
                <tr>
                    <td>Compliance with DepEd and government reporting requirements</td>
                    <td>Legal obligation</td>
                </tr>
                <tr>
                    <td>Improving our enrollment system and services</td>
                    <td>Legitimate interest</td>
                </tr>
            </tbody>
        </table>


        {{-- Section 4 --}}
        <h2>4. How We Use Your Personal Data</h2>
        <p>Your personal data is used exclusively for school-related purposes, including:</p>
        <ul>
            <li>Reviewing, approving, or declining enrollment applications</li>
            <li>Creating and managing student accounts in our portal system</li>
            <li>Assigning students to appropriate grade levels and class sections</li>
            <li>Generating enrollment records, clearances, and official school documents</li>
            <li>Processing and tracking tuition payments and installment schedules</li>
            <li>Communicating enrollment status, payment reminders, and school announcements</li>
            <li>Monitoring student health needs to ensure a safe learning environment</li>
            <li>Complying with reporting obligations to the Department of Education (DepEd) and other
                government agencies</li>
            <li>Conducting academic performance tracking and promotion</li>
        </ul>


        {{-- Section 5 --}}
        <h2>5. Data Sharing and Disclosure</h2>
        <p>
            We do <strong>not</strong> sell, rent, or trade your personal data to any third party. Your data may
            only be shared in the following circumstances:
        </p>

        <h3>5.1 Within the School</h3>
        <p>
            Your data is accessible only to authorized school personnel whose roles require it,
            including school administrators, finance staff, class advisers, and the school principal.
            Access is role-based and limited to what is necessary.
        </p>

        <h3>5.2 Government Agencies</h3>
        <p>
            We may be required by law to share certain student data with government agencies such as
            the Department of Education (DepEd), the National Statistics Authority (PSA), or local
            government units, strictly for compliance and reporting purposes.
        </p>

        <h3>5.3 Emergency Situations</h3>
        <p>
            In the event of a medical emergency or situation involving the safety of a student, relevant
            health and contact information may be disclosed to emergency responders, medical
            professionals, or authorized family members.
        </p>

        <h3>5.4 Legal Requirements</h3>
        <p>
            We may disclose your information if required to do so by a court order, subpoena, or other
            valid legal process, or when disclosure is necessary to protect our legal rights or the
            rights of others.
        </p>


        {{-- Section 6 --}}
        <h2>6. Data Retention</h2>
        <p>
            We retain your personal data for as long as is necessary to fulfill the purposes for which
            it was collected, and in accordance with applicable laws and regulations:
        </p>
        <ul>
            <li><strong>Enrollment records</strong> — retained for the duration of the student's enrollment
                and for a minimum of <strong>10 years</strong> after the student's graduation or departure,
                in compliance with DepEd records management guidelines.</li>
            <li><strong>Payment records</strong> — retained for a minimum of <strong>5 years</strong> for
                audit and financial compliance purposes.</li>
            <li><strong>Health records</strong> — retained for the duration of enrollment and
                <strong>3 years</strong> thereafter.</li>
            <li><strong>Uploaded documents</strong> — retained for the duration of the student's
                enrollment and may be deleted upon the student's request after graduation, subject
                to school policy.</li>
            <li><strong>Account credentials</strong> — deactivated upon graduation or withdrawal and
                purged after the applicable retention period.</li>
        </ul>
        <p>
            Rejected or withdrawn applications are retained for a minimum of <strong>1 year</strong>
            for reference purposes, after which they are securely deleted or anonymized.
        </p>


        {{-- Section 7 --}}
        <h2>7. Data Security</h2>
        <p>
            ILC implements appropriate technical and organizational security measures to protect your
            personal data against unauthorized access, disclosure, alteration, or destruction. These
            measures include:
        </p>
        <ul>
            <li>Encrypted storage of passwords and sensitive data</li>
            <li>Role-based access controls limiting data access to authorized personnel only</li>
            <li>Secure file storage for uploaded documents and payment screenshots</li>
            <li>Regular system monitoring and security reviews</li>
            <li>Use of HTTPS/SSL encryption for data transmitted through the enrollment portal</li>
        </ul>
        <p>
            While we take every reasonable precaution, no system can guarantee absolute security. In
            the event of a data breach that poses a risk to your rights and freedoms, we will notify
            the National Privacy Commission (NPC) and affected individuals in accordance with the
            Data Privacy Act of 2012.
        </p>


        {{-- Section 8 --}}
        <h2>8. Your Rights as a Data Subject</h2>
        <p>
            Under the Data Privacy Act of 2012 (RA 10173), you have the following rights regarding
            your personal data:
        </p>
        <ul>
            <li>
                <strong>Right to be Informed</strong> — You have the right to know what personal data we
                collect, how it is used, and to whom it may be disclosed.
            </li>
            <li>
                <strong>Right to Access</strong> — You may request a copy of the personal data we hold
                about you at any time.
            </li>
            <li>
                <strong>Right to Rectification</strong> — You may request correction of inaccurate or
                incomplete personal data. Students or parents should inform the school registrar of
                any changes.
            </li>
            <li>
                <strong>Right to Erasure or Blocking</strong> — You may request the deletion or blocking
                of your personal data under certain circumstances, subject to the school's legal
                retention obligations.
            </li>
            <li>
                <strong>Right to Object</strong> — You may object to the processing of your personal data
                for specific purposes, such as marketing or non-essential communications.
            </li>
            <li>
                <strong>Right to Data Portability</strong> — You may request that we provide your personal
                data in a structured, commonly used, and machine-readable format.
            </li>
            <li>
                <strong>Right to Lodge a Complaint</strong> — You have the right to file a complaint with
                the National Privacy Commission (NPC) if you believe your data privacy rights have
                been violated.
            </li>
        </ul>
        <p>
            To exercise any of these rights, please contact our Data Protection Officer using the
            contact details in Section 1. We will respond to your request within <strong>15 working
            days</strong> of receipt.
        </p>


        {{-- Section 9 --}}
        <h2>9. Children's Privacy</h2>
        <p>
            The ILC Online Enrollment System is used to enroll minor students. The personal data of
            minors is collected with the full knowledge and consent of their parent or legal guardian,
            who is responsible for completing and submitting the enrollment application on the
            student's behalf.
        </p>
        <p>
            Parents and guardians have the right to access, correct, or request the deletion of their
            child's personal data at any time, subject to applicable laws and school policies.
        </p>


        {{-- Section 10 --}}
        <h2>10. Cookies and System Data</h2>
        <p>
            Our enrollment system uses session cookies to maintain your login session and ensure
            secure access. These cookies are temporary and are deleted when you close your browser
            or log out. We do not use cookies for tracking or advertising purposes.
        </p>


        {{-- Section 11 --}}
        <h2>11. Third-Party Services</h2>
        <p>
            Our enrollment system may use the following third-party services for technical
            functionality:
        </p>
        <ul>
            <li><strong>GCash</strong> — for processing online payments. Payments made through GCash
                are subject to GCash's own privacy policy and terms of service.</li>
            <li><strong>Email service providers</strong> — for sending enrollment notifications and
                updates. Only the necessary information (name, email address) is shared for
                delivery purposes.</li>
        </ul>
        <p>
            ILC is not responsible for the privacy practices of these third-party services. We
            encourage you to review their respective privacy policies.
        </p>


        {{-- Section 12 --}}
        <h2>12. Changes to This Privacy Policy</h2>
        <p>
            ILC reserves the right to update or modify this Privacy Policy at any time to reflect
            changes in our practices, legal requirements, or system improvements. Any changes will
            be posted on this page with a revised effective date. Continued use of the enrollment
            system after any changes constitutes your acceptance of the updated policy.
        </p>
        <p>
            For significant changes, we will notify parents and guardians through the contact
            information provided during enrollment.
        </p>


        {{-- Section 13 --}}
        <h2>13. Contact and Complaints</h2>
        <p>
            If you have questions, concerns, or requests regarding this Privacy Policy or how we
            handle your personal data, please contact us:
        </p>
        <div class="highlight">
            <strong>IEMELIF Learning Center</strong><br>
            General Tinio, Nueva Ecija, Philippines<br>
            Phone: [Insert Phone Number]<br>
            Email: [Insert School Email]<br>
            Data Protection Officer: [Insert DPO Name]<br>
            Office Hours: Monday to Friday, 8:00 AM – 5:00 PM
        </div>
        <p>
            If you are not satisfied with our response, you have the right to file a complaint
            with the <strong>National Privacy Commission (NPC)</strong>:
        </p>
        <div class="highlight">
            National Privacy Commission<br>
            3/F, Core G, GSIS Complex, Roxas Boulevard, Pasay City, 1308 Philippines<br>
            Website: <a href="https://www.privacy.gov.ph" target="_blank" style="color:#1a3a6c;">www.privacy.gov.ph</a><br>
            Email: info@privacy.gov.ph
        </div>


        {{-- Section 14 --}}
        <h2>14. Consent</h2>
        <p>
            By submitting an enrollment application through this system and checking the "I agree"
            checkbox, you confirm that:
        </p>
        <ul>
            <li>You have read and understood this Privacy Policy in full.</li>
            <li>You consent to the collection, use, and processing of the personal data described
                in this policy for the stated purposes.</li>
            <li>You are at least 18 years old, or you are acting as the parent or legal guardian
                of the student being enrolled.</li>
            <li>All information provided is true, accurate, and complete to the best of your
                knowledge.</li>
        </ul>

        <p class="last-updated">
            <strong>IEMELIF Learning Center</strong><br>
            This Privacy Policy was last updated on {{ now()->format('F j, Y') }}.
            We are committed to protecting your personal data in accordance with
            Republic Act No. 10173 (Data Privacy Act of 2012).
        </p>
    </div>
</body>
</html>
