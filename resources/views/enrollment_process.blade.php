<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Process - IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1e3a5f;
            --gold: #c5a059;
            --blue-light: #2c5282;
            --gold-light: #d4b36a;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--blue) 0%, var(--blue-light) 50%, #1a472a 100%);
            padding: 40px 20px;
        }
        
        .process-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .process-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .process-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--blue), var(--gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
        }
        
        .process-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--blue);
            margin-bottom: 8px;
        }
        
        .process-subtitle {
            font-size: 15px;
            color: #666;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--gold);
        }
        
        .steps-list {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
        }
        
        .steps-list li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid var(--gold);
        }
        
        .step-number {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--blue), var(--blue-light));
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }
        
        .step-content p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }
        
        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #fff8e1;
            border-radius: 10px;
            border: 1px solid var(--gold-light);
        }
        
        .requirement-item i {
            color: var(--gold);
            font-size: 18px;
        }
        
        .requirement-item span {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }
        
        .important-notes {
            background: #e3f2fd;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--blue);
        }
        
        .important-notes h4 {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .important-notes ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .important-notes li {
            font-size: 13px;
            color: #444;
            padding: 6px 0;
            padding-left: 20px;
            position: relative;
        }
        
        .important-notes li:before {
            content: "•";
            color: var(--blue);
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-start {
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--blue), var(--blue-light));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 95, 0.3);
            color: #fff;
        }
        
        .btn-back {
            padding: 14px 32px;
            background: #f0f0f0;
            color: #666;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
            color: #444;
        }
        
        .contact-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .contact-section p {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .contact-section a {
            color: var(--blue);
            font-weight: 600;
            text-decoration: none;
        }
        
        .contact-section a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 576px) {
            .process-card {
                padding: 24px;
            }
            
            .process-title {
                font-size: 22px;
            }
            
            .requirements-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-start, .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="process-container">
        <div class="process-card">
            <div class="process-header">
                <div class="logo-container">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h1 class="process-title">Student Enrollment Process</h1>
                <p class="process-subtitle">IEMELIF Learning Center - Complete Guide</p>
            </div>

            <!-- Enrollment Steps -->
            <h3 class="section-title">
                <i class="bi bi-list-ol"></i>
                Enrollment Steps
            </h3>
            <ul class="steps-list">
                <li>
                    <span class="step-number">1</span>
                    <div class="step-content">
                        <h4>Create Your Account</h4>
                        <p>Register with your email and create a secure password to access the student portal.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">2</span>
                    <div class="step-content">
                        <h4>Fill Student Information</h4>
                        <p>Provide complete student details including full name, birthdate, and contact information.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">3</span>
                    <div class="step-content">
                        <h4>Select Grade Level</h4>
                        <p>Choose the appropriate grade level based on age and academic requirements.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">4</span>
                    <div class="step-content">
                        <h4>Upload Required Documents</h4>
                        <p>Submit scanned copies of all necessary documents for verification.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">5</span>
                    <div class="step-content">
                        <h4>Pay Enrollment Fees</h4>
                        <p>Complete the payment process. Once verified, you'll receive confirmation.</p>
                    </div>
                </li>
            </ul>

            <!-- Requirements -->
            <h3 class="section-title">
                <i class="bi bi-folder-check"></i>
                Required Documents
            </h3>
            <div class="requirements-grid">
                <div class="requirement-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Birth Certificate</span>
                </div>
                <div class="requirement-item">
                    <i class="bi bi-card-image"></i>
                    <span>2x2 ID Photo (2 copies)</span>
                </div>
                <div class="requirement-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Report Card/Transcript</span>
                </div>
 
            </div>

            <!-- Important Notes -->
            <div class="important-notes">
                <h4><i class="bi bi-info-circle"></i> Important Reminders</h4>
                <ul>
                    <li>Enrollment period opens every school year from January to March</li>
                    <li>Grade level placement is based on age and previous academic records</li>
                    <li>All uploaded documents must be clear and legible</li>
                    <li>Enrollment is not complete until payment is verified</li>
                    <li>For questions, contact the registrar's office during business hours</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('enrollment.form') }}" class="btn-start">
                    <i class="bi bi-arrow-right-circle"></i>
                    Start Application
                </a>
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Back to Login
                </a>
            </div>

            <!-- Contact -->
            <div class="contact-section">
                <p>Need assistance with enrollment?</p>
                <p><i class="bi bi-telephone"></i> Call us: <a href="tel:+63123456789">+63 123 456 789</a></p>
                <p><i class="bi bi-envelope"></i> Email: <a href="mailto:registrar@iemelif.edu.ph">registrar@iemelif.edu.ph</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
