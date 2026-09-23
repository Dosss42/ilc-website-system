<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enrollment Application - IEMELIF Learning Center</title>
    

    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --ilc-blue: #003d82;
            --ilc-gold: #ffd700;
            --ilc-light-blue: #e8f0fb;
        }

        /* Top Header */
        .top-header {
            background: var(--ilc-blue);
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .school-logo img {
            height: 40px;
            margin-right: 10px;
        }

        .school-title h1 {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .school-title p {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            margin: 0;
        }

        .search-form input {
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
        }

        .btn-search {
            background: var(--ilc-gold);
            color: var(--ilc-blue);
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            margin-left: -5px;
        }

        /* Main Navigation */
        .main-nav {
            background: var(--ilc-blue);
            border-bottom: 2px solid var(--ilc-gold);
        }

        .main-nav .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 15px 20px !important;
            transition: all 0.3s ease;
        }

        .main-nav .nav-link:hover,
        .main-nav .nav-link.active {
            background: var(--ilc-gold) !important;
            color: var(--ilc-blue) !important;
        }

        /* Page Banner */
        .page-banner {
            background: var(--ilc-blue);
            padding: 30px 0;
            text-align: center;
            border-bottom: 4px solid var(--ilc-gold);
        }

        /* Form Container */
        .form-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .form-header {
            background: var(--ilc-blue);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .form-header h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-header p {
            opacity: 0.9;
            margin: 0;
        }

        .form-card {
            background: white;
            border: 2px solid var(--ilc-gold);
            border-radius: 0 0 15px 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Step Progress */
        .step-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #666;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background: var(--ilc-blue);
            color: white;
        }

        .step-circle.done {
            background: var(--ilc-gold);
            color: var(--ilc-blue);
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #ddd;
            margin: 0 10px;
            position: relative;
            top: -1px;
        }

        .step-line.done {
            background: var(--ilc-gold);
        }

        .step-labels {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .step-label {
            font-size: 12px;
            color: #666;
            text-align: center;
            flex: 1;
        }

        .step-label.active {
            color: var(--ilc-blue);
            font-weight: 600;
        }

        .step-label.done {
            color: var(--ilc-gold);
            font-weight: 600;
        }

        /* Form Elements */
        .form-section-label {
            background: var(--ilc-light-blue);
            color: var(--ilc-blue);
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .app-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .app-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .app-input:focus {
            outline: none;
            border-color: var(--ilc-blue);
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        .app-input:required {
            border-color: #ddd;
        }

        /* Step Buttons */
        .step-btn-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-step-back, .btn-step-next, .btn-step-submit {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-step-back {
            background: #f5f5f5;
            color: #666;
        }

        .btn-step-back:hover {
            background: #e0e0e0;
        }

        .btn-step-next {
            background: var(--ilc-blue);
            color: white;
        }

        .btn-step-next:hover {
            background: #002866;
            transform: translateY(-2px);
        }

        .btn-step-submit {
            background: var(--ilc-gold);
            color: var(--ilc-blue);
        }

        .btn-step-submit:hover {
            background: #ffcc00;
            transform: translateY(-2px);
        }

        /* Summary Section */
        .summary-section {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-section h6 {
            color: var(--ilc-blue);
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .sum-row:last-child {
            border-bottom: none;
        }

        .sum-row span:first-child {
            color: #666;
            font-weight: 500;
        }

        .sum-row span:last-child {
            color: #333;
            font-weight: 600;
        }

        /* Navigation */
        .nav-breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }

        .nav-breadcrumb a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 12px;
        }

        .nav-breadcrumb a:hover {
            color: white;
        }

        .nav-breadcrumb span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        /* Error Messages */
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }

        .success-notification {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        /* Payment Option Cards */
        .payment-option-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
        }

        .payment-option-card:hover {
            border-color: var(--ilc-blue);
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 61, 130, 0.15);
        }

        .payment-option-card.selected {
            border-color: var(--ilc-blue);
            box-shadow: 0 4px 15px rgba(0, 61, 130, 0.2);
        }

        .payment-option-card.selected .payment-option-header {
            background: var(--ilc-gold);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .step-progress {
                flex-wrap: wrap;
                gap: 10px;
            }

            .step-line {
                display: none;
            }

            .step-labels {
                flex-wrap: wrap;
                gap: 10px;
            }

            .step-btn-row {
                flex-direction: column;
                gap: 15px;
            }

            .btn-step-back, .btn-step-next, .btn-step-submit {
                width: 100%;
                justify-content: center;
            }
        }

        /* ── Skeleton loading ── */
        @keyframes enrSkelShimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        .eskel {
            background: linear-gradient(90deg, #e8edf2 25%, #f5f7fa 50%, #e8edf2 75%);
            background-size: 600px 100%;
            animation: enrSkelShimmer 1.4s ease-in-out infinite;
            border-radius: 6px;
            display: block;
        }
        .skel-section { display: block; }
        .real-section  { display: none; opacity: 0; transition: opacity 0.38s ease; }
        body:not(.page-loading) .skel-section { display: none !important; }
        body:not(.page-loading) .real-section  { display: block; opacity: 1; }
    </style>
</head>
<body class="page-loading">

<!-- Top Header -->
<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
        {{--    <div class="school-logo">
                <a class="nav-link" href="{{ route('home') }}">
                    <img src="/images/logo1.png" alt="Logo 1"></a>
            </div>--}}
            <div class="school-logo">
                <a class="nav-link" href="{{ route('home') }}">
                    <img src="/images/logo.png" alt="Logo 2"></a>
            </div>
            <div class="school-title">
                <h1>IEMELIF Learning Center</h1>
                <p>General Tinio, Nueva Ecija ILC</p>
            </div>
        </div>
    </div>
</header>


<!-- ══ SKELETON ══ -->
<div class="skel-section" id="skel-enr-content">

    <!-- Banner skeleton -->
    <div style="background:#003d82;padding:30px 0;text-align:center;border-bottom:4px solid #ffd700;">
        <span class="eskel" style="height:26px;width:240px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:14px;display:flex;justify-content:center;gap:8px;align-items:center;">
            <span class="eskel" style="height:11px;width:50px;display:inline-block;border-radius:4px;opacity:.6;"></span>
            <span style="color:rgba(255,255,255,.3);">|</span>
            <span class="eskel" style="height:11px;width:70px;display:inline-block;border-radius:4px;opacity:.6;"></span>
            <span style="color:rgba(255,255,255,.3);">|</span>
            <span class="eskel" style="height:11px;width:100px;display:inline-block;border-radius:4px;opacity:.6;"></span>
        </div>
    </div>

    <!-- Form card skeleton -->
    <div style="max-width:820px;margin:32px auto;padding:0 16px 48px;">
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);overflow:hidden;">

            <!-- Card header -->
            <div style="padding:22px 28px;border-bottom:1px solid #f0f0f0;">
                <span class="eskel" style="height:22px;width:220px;display:block;border-radius:4px;margin-bottom:8px;"></span>
                <span class="eskel" style="height:12px;width:300px;display:block;border-radius:4px;"></span>
            </div>

            <div style="padding:24px 28px;">
                <!-- Step progress circles -->
                <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:10px;">
                    @for($i=0;$i<5;$i++)
                        <span class="eskel" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:block;"></span>
                        @if($i<4)<span class="eskel" style="flex:1;max-width:80px;height:3px;border-radius:2px;display:block;"></span>@endif
                    @endfor
                </div>
                <!-- Step labels -->
                <div style="display:flex;justify-content:space-between;margin-bottom:28px;">
                    @for($i=0;$i<5;$i++)
                    <span class="eskel" style="height:10px;width:60px;border-radius:4px;display:block;"></span>
                    @endfor
                </div>

                <!-- Section label -->
                <span class="eskel" style="height:16px;width:180px;display:block;border-radius:4px;margin-bottom:20px;"></span>

                <!-- Form fields — 2-column grid rows -->
                @for($r=0;$r<4;$r++)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <span class="eskel" style="height:11px;width:80px;display:block;border-radius:3px;margin-bottom:6px;"></span>
                        <span class="eskel" style="height:42px;width:100%;display:block;border-radius:8px;"></span>
                    </div>
                    <div>
                        <span class="eskel" style="height:11px;width:90px;display:block;border-radius:3px;margin-bottom:6px;"></span>
                        <span class="eskel" style="height:42px;width:100%;display:block;border-radius:8px;"></span>
                    </div>
                </div>
                @endfor

                <!-- Full-width field -->
                <div style="margin-bottom:14px;">
                    <span class="eskel" style="height:11px;width:110px;display:block;border-radius:3px;margin-bottom:6px;"></span>
                    <span class="eskel" style="height:42px;width:100%;display:block;border-radius:8px;"></span>
                </div>

                <!-- Navigation buttons -->
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:28px;padding-top:20px;border-top:1px solid #f0f0f0;">
                    <span class="eskel" style="height:44px;width:130px;border-radius:8px;display:block;"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══ REAL CONTENT ══ -->
<div class="real-section" id="real-enr-content">

<!-- Page Banner -->
<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Enrollment Application</h2>
    <div class="nav-breadcrumb" style="background: rgba(255, 255, 255, 0.1); padding: 8px 16px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px;">
        <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.8); text-decoration:none; font-size:12px;">Home</a>
        <span style="color:rgba(255,255,255,0.6);">|</span>
        <a href="{{ route('admission') }}" style="color:rgba(255,255,255,0.8); text-decoration:none; font-size:12px;">Enrollment</a>
        <span style="color:rgba(255,255,255,0.6);">|</span>
        <span style="color:#fff; font-size:12px;">Application Form</span>
    </div>
</div>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h3>Enrollment Application Form</h3>
        <p>Please fill out all required fields to complete your application.</p>
    </div>

    <div class="form-card">
        <!-- Step Progress -->
        <div class="step-progress">
            <div class="step-circle active" id="sc1">1</div>
            <div class="step-line" id="sl1"></div>
            <div class="step-circle" id="sc2">2</div>
            <div class="step-line" id="sl2"></div>
            <div class="step-circle" id="sc3">3</div>
            <div class="step-line" id="sl3"></div>
            <div class="step-circle" id="sc4">4</div>
            <div class="step-line" id="sl4"></div>
            <div class="step-circle" id="sc5">5</div>
        </div>
        <div class="step-labels">
            <span class="step-label active" id="slb1">Personal Info</span>
            <span class="step-label" id="slb2">Health</span>
            <span class="step-label" id="slb3">Address</span>
            <span class="step-label" id="slb4">Guardian</span>
            <span class="step-label" id="slb5">Review</span>
        </div>

        {{-- Draft restored banner --}}
        <div id="draft-banner" style="display:none;background:#e8f4ff;border:1.5px solid #93c5fd;border-radius:10px;padding:12px 18px;margin-bottom:16px;display:none;align-items:center;gap:12px;">
            <i class="bi bi-floppy-fill" style="font-size:18px;color:#1d4ed8;flex-shrink:0;"></i>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:700;color:#1e40af;">Draft Restored</div>
                <div style="font-size:11px;color:#3b82f6;margin-top:1px;">Your previous answers have been loaded. Review and continue where you left off.</div>
            </div>
            <button type="button" onclick="clearEnrollmentDraft()" style="background:#fff;border:1.5px solid #93c5fd;color:#1d4ed8;border-radius:8px;padding:5px 14px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
                <i class="bi bi-trash me-1"></i> Clear Draft
            </button>
        </div>

        <form id="appForm" action="{{ route('enrollment.submit') }}" method="POST">
            @csrf

            <!-- STEP 1: Personal Information -->
            <div id="appStep1" class="form-step">
                <div class="form-section-label">
                    <i class="bi bi-person-fill"></i> Personal Information
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="app-label">First Name *</label>
                        <input type="text" name="first_name" class="app-input" placeholder="Juan" required title="First name should only contain letters" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Middle Name</label>
                        <input type="text" name="middle_name" class="app-input" placeholder="Santos" title="Middle name should only contain letters" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Last Name *</label>
                        <input type="text" name="last_name" class="app-input" placeholder="Cruz" required title="Last name should only contain letters" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Suffix</label>
                        <select name="suffix" class="app-input">
                            <option value="">None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="Sr.">Sr.</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="app-label">Date of Birth *</label>
                        <input type="date" name="birthdate" class="app-input" required max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Gender *</label>
                        <select name="gender" class="app-input" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Place of Birth *</label>
                        <input type="text" name="place_of_birth" class="app-input" placeholder="General Tinio, Nueva Ecija" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Nationality *</label>
                        <input type="text" name="nationality" class="app-input" placeholder="Filipino" required oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="app-label">Grade Level Applying For *</label>
                        <select name="grade_level" class="app-input" required>
                            <option value="" disabled selected>Select Grade</option>
                            <option value="nursery">Nursery</option>
                            <option value="kindergarten">Kindergarten</option>
                            <option value="grade1">Grade 1</option>
                            <option value="grade2">Grade 2</option>
                            <option value="grade3">Grade 3</option>
                            <option value="grade4">Grade 4</option>
                            <option value="grade5">Grade 5</option>
                            <option value="grade6">Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="app-label">Type of Student *</label>
                        <select name="student_type" class="app-input" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="new">New Student</option>
                            <option value="transferee">Transferee</option>
                            <option value="returning">Returning Student</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="app-label">Last School Attended</label>
                        <input type="text" name="last_school" class="app-input" placeholder="School Name" oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="app-label">Mother's Full Name *</label>
                        <input type="text" name="mother_name" class="app-input" placeholder="Mother's full name" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Mother's Age *</label>
                        <input type="number" name="mother_age" class="app-input" placeholder="Age" required min="1" max="120">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Religious Affiliation *</label>
                        <input type="text" name="religious_affiliation" class="app-input" placeholder="e.g., Roman Catholic" required oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="app-label">Father's Full Name *</label>
                        <input type="text" name="father_name" class="app-input" placeholder="Father's full name" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Father's Age *</label>
                        <input type="number" name="father_age" class="app-input" placeholder="Age" required min="1" max="120">
                    </div>
                </div>

                <div class="step-btn-row">
                    <div></div>
                    <button type="button" class="btn-step-next" onclick="appStep(2)">
                        Next <i class="bi bi-arrow-right-short"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Health Information -->
            <div id="appStep2" class="form-step" style="display: none;">
                <div class="form-section-label">
                    <i class="bi bi-heart-pulse-fill"></i> Health Information
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="app-label">Blood Type</label>
                        <select name="blood_type" class="app-input">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="unknown">Unknown</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="app-label">Allergies</label>
                        <input type="text" name="allergies" class="app-input" placeholder="List any known allergies" oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="app-label">Medical Conditions</label>
                        <input type="text" name="medical_conditions" class="app-input" placeholder="Any chronic conditions or medications" oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="step-btn-row">
                    <button type="button" class="btn-step-back" onclick="appStep(1)">
                        <i class="bi bi-arrow-left-short"></i> Back
                    </button>
                    <button type="button" class="btn-step-next" onclick="appStep(3)">
                        Next <i class="bi bi-arrow-right-short"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Address -->
            <div id="appStep3" class="form-step" style="display: none;">
                <div class="form-section-label">
                    <i class="bi bi-geo-alt-fill"></i> Address Information
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="app-label">Region *</label>
                        <select name="region" id="enroll-region" class="app-input" data-required="true">
                            <option value="">Loading regions...</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Province *</label>
                        <select name="province" id="enroll-province" class="app-input" data-required="true" disabled>
                            <option value="">Select Province</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">City / Municipality *</label>
                        <select name="city" id="enroll-city" class="app-input" data-required="true" disabled>
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Barangay *</label>
                        <select name="barangay" id="enroll-barangay" class="app-input" data-required="true" disabled>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="app-label">Street / Purok / House No. *</label>
                        <input type="text" name="street_address" class="app-input" placeholder="House No., Street, Purok" data-required="true" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-6">
                        <label class="app-label">Zip Code</label>
                        <input type="text" name="zip_code" class="app-input" placeholder="3100">
                    </div>
                </div>
                
                <div class="step-btn-row">
                    <button type="button" class="btn-step-back" onclick="appStep(2)">
                        <i class="bi bi-arrow-left-short"></i> Back
                    </button>
                    <button type="button" class="btn-step-next" onclick="appStep(4)">
                        Next <i class="bi bi-arrow-right-short"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Guardian Information -->
            <div id="appStep4" class="form-step" style="display: none;">
                <div class="form-section-label">
                    <i class="bi bi-people-fill"></i> Parent / Guardian Information
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="app-label">Full Name *</label>
                        <input type="text" name="guardian_name" class="app-input" placeholder="Full name of parent/guardian" data-required="true" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Relationship *</label>
                        <select name="relationship" class="app-input" data-required="true">
                            <option value="" disabled selected>Select Type</option>
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="guardian">Guardian</option>
                            <option value="sibling">Sibling</option>
                            <option value="grandparent">Grandparent</option>
                            <option value="relative">Relative</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="app-label">Occupation</label>
                        <input type="text" name="guardian_occupation" class="app-input" placeholder="Job title or profession" oninput="capitalizeFirst(this)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="app-label">Phone *</label>
                        <div style="display:flex;align-items:stretch;border:1.5px solid #c8d6f0;border-radius:6px;overflow:hidden;background:#fff;">
                            <span style="padding:0 12px;background:#f0f4ff;color:#1d3557;font-weight:700;font-size:13px;display:flex;align-items:center;border-right:1.5px solid #c8d6f0;white-space:nowrap;">+63</span>
                            <input type="tel" name="guardian_phone" id="guardian_phone" class="app-input"
                                style="border:none;border-radius:0;flex:1;min-width:0;"
                                placeholder="9XXXXXXXXX" maxlength="10"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                data-required="true">
                        </div>
                        <small style="font-size:11px;color:#888;">Format: +63 9XXXXXXXXX (10 digits)</small>
                    </div>
                    <div class="col-md-7">
                        <label class="app-label">Student Email Address *</label>
                        <input type="email" name="student_email" id="student_email" class="app-input"
                            placeholder="example@gmail.com" data-required="true"
                            oninput="validateGmail(this)">
                        <small id="student_email_hint" style="font-size:11px;color:#888;">Must be a Gmail address (@gmail.com)</small>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#fff8ec; border:1px solid #f5d98a; border-radius:6px; padding:12px 14px; font-size:12px; color:#7d5a00; margin-top:22px;">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Your login credentials will be sent to this email once approved by the admin.
                        </div>
                    </div>
                </div>

                <div class="step-btn-row">
                    <button type="button" class="btn-step-back" onclick="appStep(3)">
                        <i class="bi bi-arrow-left-short"></i> Back
                    </button>
                    <button type="button" class="btn-step-next" onclick="openTcModal()">
                        Next <i class="bi bi-arrow-right-short"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 5: Review & Submit -->
            <div id="appStep5" class="form-step" style="display: none;">
                <div style="text-align:center; margin-bottom:24px;">
                    <h5 style="font-size:16px; font-weight:700; color:var(--ilc-blue);">Review Your Application</h5>
                    <p style="font-size:13px; color:#888;">Please review all information before submitting.</p>
                </div>

                <!-- Summary boxes -->
                <div class="summary-section">
                    <h6><i class="bi bi-person-badge-fill me-2"></i>Student Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="sum-row"><span>Full Name</span><span id="rev-name">-</span></div>
                            <div class="sum-row"><span>Gender</span><span id="rev-gender">-</span></div>
                            <div class="sum-row"><span>Date of Birth</span><span id="rev-dob">-</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="sum-row"><span>Grade Level</span><span id="rev-grade">-</span></div>
                            <div class="sum-row"><span>Student Type</span><span id="rev-type">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="summary-section">
                    <h6><i class="bi bi-heart-pulse-fill me-2"></i>Health Information</h6>
                    <div class="sum-row"><span>Blood Type</span><span id="rev-blood-type">-</span></div>
                    <div class="sum-row"><span>Allergies</span><span id="rev-allergies">-</span></div>
                    <div class="sum-row"><span>Medical Conditions</span><span id="rev-medical">-</span></div>

                </div>

                <div class="summary-section">
                    <h6><i class="bi bi-geo-alt-fill me-2"></i>Address</h6>
                    <div class="sum-row"><span>Region</span><span id="rev-region">-</span></div>
                    <div class="sum-row"><span>Province</span><span id="rev-province">-</span></div>
                    <div class="sum-row"><span>City / Municipality</span><span id="rev-city">-</span></div>
                    <div class="sum-row"><span>Barangay</span><span id="rev-barangay">-</span></div>
                    <div class="sum-row"><span>Street / Purok</span><span id="rev-street">-</span></div>
                </div>

                <div class="summary-section">
                    <h6><i class="bi bi-people-fill me-2"></i>Guardian</h6>
                    <div class="sum-row"><span>Guardian Name</span><span id="rev-guardian">-</span></div>
                    <div class="sum-row"><span>Relationship</span><span id="rev-rel">-</span></div>
                    <div class="sum-row"><span>Guardian Phone</span><span id="rev-gphone">-</span></div>
                    <div class="sum-row"><span>Student Email</span><span id="rev-email">-</span></div>
                </div>

                <!-- Terms accepted badge (shown after modal acceptance) -->
                <div id="tc-accepted-badge" style="display:none;align-items:center;gap:10px;margin-bottom:20px;padding:12px 16px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;font-size:13px;color:#16a34a;font-weight:600;">
                    <i class="bi bi-check-circle-fill" style="font-size:18px;"></i>
                    Terms &amp; Conditions and Privacy Policy accepted.
                </div>
                <input type="checkbox" name="terms" id="appTerms" style="display:none;" required>

                <div class="step-btn-row">
                    <button type="button" class="btn-step-back" onclick="appStep(4)">
                        <i class="bi bi-arrow-left-short"></i> Back
                    </button>
                    {{-- Triggers OTP flow instead of direct submit --}}
                    <button type="button" class="btn-step-submit" id="otpTriggerBtn" onclick="startOtpFlow()">
                        <i class="bi bi-shield-check"></i> Verify Email & Submit
                    </button>
                </div>
                {{-- Hidden OTP token injected after verification --}}
                <input type="hidden" name="otp_token" id="otp_token_input">
            </div>

        </form>
    </div>
</div>

{{-- ── OTP VERIFICATION MODAL ── --}}
<div id="otpModal" style="display:none;position:fixed;inset:0;z-index:99999;align-items:center;justify-content:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);"></div>
    <div style="position:relative;width:100%;max-width:420px;margin:16px;border-radius:20px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,.3);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1a3a6c,#2471a3);padding:24px 28px;text-align:center;">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="bi bi-envelope-check-fill" style="font-size:26px;color:#fff;"></i>
            </div>
            <div style="font-size:18px;font-weight:800;color:#fff;">Verify Your Email</div>
            <div id="otpModalEmail" style="font-size:12px;color:rgba(255,255,255,.7);margin-top:4px;"></div>
        </div>

        {{-- Body --}}
        <div style="background:#fff;padding:28px;">
            <p style="font-size:13px;color:#374151;text-align:center;margin-bottom:20px;line-height:1.6;">
                We sent a <strong>6-digit verification code</strong> to your Gmail. Enter it below to complete your enrollment application.
            </p>

            {{-- OTP input boxes --}}
            <div style="display:flex;justify-content:center;gap:10px;margin-bottom:6px;" id="otpBoxes">
                @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    class="otp-box"
                    style="width:46px;height:56px;text-align:center;font-size:24px;font-weight:800;border:2px solid #e2e8f0;border-radius:10px;color:#1a3a6c;outline:none;transition:border-color .2s;"
                    oninput="otpBoxInput(this, {{ $i }})"
                    onkeydown="otpBoxKeydown(event, {{ $i }})"
                    onpaste="{{ $i === 0 ? 'otpPaste(event)' : 'return false;' }}"
                    onfocus="this.style.borderColor='#2471a3'"
                    onblur="this.style.borderColor='#e2e8f0'">
                @endfor
            </div>

            {{-- Error message --}}
            <div id="otpError" style="display:none;text-align:center;font-size:12px;color:#dc2626;margin-bottom:12px;padding:8px;background:#fef2f2;border-radius:8px;"></div>

            {{-- Success message --}}
            <div id="otpSuccess" style="display:none;text-align:center;font-size:12px;color:#16a34a;margin-bottom:12px;padding:8px;background:#f0fdf4;border-radius:8px;">
                <i class="bi bi-check-circle-fill me-1"></i> Email verified! Submitting your application...
            </div>

            {{-- Expiry countdown --}}
            <div style="text-align:center;font-size:12px;color:#94a3b8;margin-bottom:20px;">
                Code expires in <span id="otpCountdown" style="font-weight:700;color:#1a3a6c;">3:00</span>
            </div>

            {{-- Buttons --}}
            <button type="button" id="otpVerifyBtn" onclick="submitOtpCode()"
                style="width:100%;padding:13px;background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:8px;">
                <i class="bi bi-shield-check"></i> Verify Code
            </button>

            <div style="display:flex;gap:8px;">
                <button type="button" id="otpResendBtn" onclick="resendOtp()"
                    style="flex:1;padding:10px;background:#f8faff;color:#1a3a6c;border:1.5px solid #bfdbfe;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;">
                    <i class="bi bi-arrow-repeat"></i> Resend Code
                </button>
                <button type="button" onclick="closeOtpModal()"
                    style="flex:1;padding:10px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var _otpEmail    = '';
var _otpTimer    = null;
var _otpSeconds  = 180; // 3 minutes
var _otpVerified = false;
var csrfTokenEnroll = document.querySelector('meta[name="csrf-token"]') ?
    document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

// ── T&C / Privacy tab switcher ──
function tcTab(tab) {
    document.getElementById('tc-pane-terms').style.display   = tab === 'terms'   ? '' : 'none';
    document.getElementById('tc-pane-privacy').style.display = tab === 'privacy' ? '' : 'none';
    var tBtn = document.getElementById('tc-tab-terms');
    var pBtn = document.getElementById('tc-tab-privacy');
    tBtn.style.background = tab === 'terms'   ? 'var(--ilc-blue)' : '#f3f4f6';
    tBtn.style.color      = tab === 'terms'   ? '#fff' : '#6b7280';
    pBtn.style.background = tab === 'privacy' ? 'var(--ilc-blue)' : '#f3f4f6';
    pBtn.style.color      = tab === 'privacy' ? '#fff' : '#6b7280';
}

// ── Start OTP flow when submit is clicked ──
function startOtpFlow() {
    // Must agree to terms before proceeding
    var termsBox = document.getElementById('appTerms');
    if (!termsBox || !termsBox.checked) {
        termsBox && (termsBox.style.outline = '2px solid #dc2626');
        showNotification('Please agree to the Terms and Conditions and Privacy Policy before submitting.', 'error');
        return;
    }
    termsBox.style.outline = '';

    var emailInput = document.getElementById('student_email');
    if (!emailInput) return;
    var email = emailInput.value.trim().toLowerCase();

    // Basic Gmail check — navigate back to Step 4 (Guardian) where the email field lives
    if (!email || !/^[^\s@]+@gmail\.com$/i.test(email)) {
        appStep(4);
        emailInput.focus();
        emailInput.classList.add('input-error');
        showNotification('Please enter a valid Gmail address (@gmail.com) in Step 4 before submitting.', 'error');
        return;
    }
    emailInput.classList.remove('input-error');

    _otpEmail = email;
    document.getElementById('otpModalEmail').textContent = email;
    clearOtpBoxes();
    document.getElementById('otpError').style.display = 'none';
    document.getElementById('otpSuccess').style.display = 'none';

    // Show modal
    document.getElementById('otpModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Send OTP
    sendOtpRequest();
}

function sendOtpRequest() {
    var btn = document.getElementById('otpResendBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Sending...'; }

    fetch('{{ route("enrollment.send-otp") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfTokenEnroll, 'Accept': 'application/json' },
        body: JSON.stringify({ email: _otpEmail })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Resend Code'; }
        if (!data.success) {
            showOtpError(data.message);
        } else {
            startOtpCountdown();
            // Focus first box
            var boxes = document.querySelectorAll('.otp-box');
            if (boxes[0]) boxes[0].focus();
        }
    })
    .catch(function() {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Resend Code'; }
        showOtpError('Failed to send code. Please check your internet connection.');
    });
}

function submitOtpCode() {
    var boxes = document.querySelectorAll('.otp-box');
    var code  = Array.from(boxes).map(function(b){ return b.value; }).join('');

    if (code.length < 6) {
        showOtpError('Please enter all 6 digits of the code.');
        return;
    }

    var btn = document.getElementById('otpVerifyBtn');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;display:inline-block;animation:otpSpin .7s linear infinite;"></span> Verifying...';

    fetch('{{ route("enrollment.verify-otp") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfTokenEnroll, 'Accept': 'application/json' },
        body: JSON.stringify({ email: _otpEmail, code: code })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check"></i> Verify Code';

        if (!data.success) {
            showOtpError(data.message);
            clearOtpBoxes();
        } else {
            // ✅ Verified — inject token then fire the submit event
            // (dispatchEvent triggers the fetch-based addEventListener handler,
            //  unlike .submit() which bypasses it and does a native POST)
            document.getElementById('otp_token_input').value = data.token;
            document.getElementById('otpError').style.display = 'none';
            document.getElementById('otpSuccess').style.display = 'block';
            stopOtpCountdown();
            setTimeout(function() {
                closeOtpModal();
                document.getElementById('appForm').dispatchEvent(
                    new Event('submit', { bubbles: true, cancelable: true })
                );
            }, 1200);
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check"></i> Verify Code';
        showOtpError('Verification failed. Please try again.');
    });
}

function resendOtp() {
    clearOtpBoxes();
    document.getElementById('otpError').style.display = 'none';
    stopOtpCountdown();
    sendOtpRequest();
}

function closeOtpModal() {
    document.getElementById('otpModal').style.display = 'none';
    document.body.style.overflow = '';
    stopOtpCountdown();
}

// ── OTP box helpers ──
function clearOtpBoxes() {
    document.querySelectorAll('.otp-box').forEach(function(b){ b.value = ''; b.style.borderColor='#e2e8f0'; });
}

function otpBoxInput(el, idx) {
    el.value = el.value.replace(/[^0-9]/g, '');
    if (el.value && idx < 5) {
        var next = document.querySelectorAll('.otp-box')[idx + 1];
        if (next) next.focus();
    }
    // Auto-submit when all filled
    var code = Array.from(document.querySelectorAll('.otp-box')).map(function(b){ return b.value; }).join('');
    if (code.length === 6) submitOtpCode();
}

function otpBoxKeydown(e, idx) {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
        var prev = document.querySelectorAll('.otp-box')[idx - 1];
        if (prev) { prev.value = ''; prev.focus(); }
    }
}

function otpPaste(e) {
    e.preventDefault();
    var text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
    var boxes = document.querySelectorAll('.otp-box');
    text.split('').forEach(function(c, i) { if (boxes[i]) boxes[i].value = c; });
    if (text.length === 6) submitOtpCode();
    else if (boxes[text.length]) boxes[text.length].focus();
}

// ── Countdown timer ──
function startOtpCountdown() {
    _otpSeconds = 180; // 3 minutes — must match EnrollmentController::sendOtp()'s expires_at
    stopOtpCountdown();
    _otpTimer = setInterval(function() {
        _otpSeconds--;
        var m = Math.floor(_otpSeconds / 60);
        var s = _otpSeconds % 60;
        var el = document.getElementById('otpCountdown');
        if (el) el.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        if (_otpSeconds <= 0) {
            stopOtpCountdown();
            showOtpError('Code expired. Please request a new one.');
        }
    }, 1000);
}

function stopOtpCountdown() {
    if (_otpTimer) { clearInterval(_otpTimer); _otpTimer = null; }
}

function showOtpError(msg) {
    var el = document.getElementById('otpError');
    el.textContent = msg;
    el.style.display = 'block';
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeOtpModal();
});
</script>

<style>
@keyframes otpSpin { to { transform: rotate(360deg); } }
.otp-box:focus { border-color: #2471a3 !important; box-shadow: 0 0 0 3px rgba(36,113,163,.15); }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Input sanitization function
    function sanitizeInput(input) {
        return input.trim()
            .replace(/[<>]/g, '') // Remove HTML tags
            .replace(/javascript:/gi, '') // Remove javascript protocol
            .replace(/on\w+=/gi, ''); // Remove event handlers
    }

    // Capitalize first letter of each word
    function capitalizeFirst(input) {
        if (!input.value) return;
        
        const words = input.value.toLowerCase().split(' ');
        const capitalizedWords = words.map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        );
        
        input.value = capitalizedWords.join(' ');
    }

    // Format date to MM/DD/YYYY
    function formatDate(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        
        if (value.length >= 4) {
            // Format as MM/DD/YYYY
            const month = value.substring(0, 2);
            const day = value.substring(2, 4);
            const year = value.substring(4, 8);
            
            // Validate month and day
            if (parseInt(month) > 12) return;
            if (parseInt(day) > 31) return;
            if (parseInt(year) < 1900 || parseInt(year) > new Date().getFullYear()) return;
            
            input.value = `${month}/${day}/${year}`;
        }
    }

    // Phone validation — 10 digits after +63 prefix
    function validatePhone(input) {
        const value = input.value.replace(/[^0-9]/g, '');
        if (value && !/^9[0-9]{9}$/.test(value)) {
            showError(input, 'Enter 10 digits starting with 9 (e.g. 9123456789)');
            input.classList.add('input-error');
        } else {
            hideError(input);
            input.classList.remove('input-error');
        }
    }

    // Gmail-only validation with typo detection
    const _gmailTypos = {
        'gmial.com':'gmail.com','gmal.com':'gmail.com','gamil.com':'gmail.com',
        'gnail.com':'gmail.com','gmaill.com':'gmail.com','gmail.co':'gmail.com',
        'gmai.com':'gmail.com','gmali.com':'gmail.com','gmaio.com':'gmail.com',
    };
    const _rfcEmail = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

    function validateGmail(input) {
        const hint = document.getElementById('student_email_hint');
        const val  = input.value.trim();

        if (!val) {
            hideError(input);
            input.classList.remove('input-error');
            if (hint) { hint.textContent = 'Must be a Gmail address (@gmail.com)'; hint.style.color = '#888'; }
            return;
        }

        // 1. Basic format check
        if (!_rfcEmail.test(val)) {
            showError(input, 'Invalid email format. Please enter a valid email address.');
            input.classList.add('input-error');
            if (hint) hint.style.color = '#dc3545';
            return;
        }

        const domain = val.split('@')[1].toLowerCase();

        // 2. Typo detection
        if (_gmailTypos[domain]) {
            const fix = val.split('@')[0] + '@gmail.com';
            showError(input, 'Did you mean ' + fix + '? Click to fix.');
            input.classList.add('input-error');
            if (hint) hint.style.color = '#d97706';
            // Make the error message clickable to auto-fix
            const errEl = input.nextElementSibling;
            if (errEl && errEl.classList.contains('error-message')) {
                errEl.style.cursor = 'pointer';
                errEl.style.color  = '#d97706';
                errEl.onclick = function() { input.value = fix; validateGmail(input); };
            }
            return;
        }

        // 3. Gmail-only check
        if (domain !== 'gmail.com') {
            showError(input, 'Only Gmail addresses are accepted (e.g. yourname@gmail.com).');
            input.classList.add('input-error');
            if (hint) { hint.textContent = 'Must be a @gmail.com address.'; hint.style.color = '#dc3545'; }
            return;
        }

        // 4. All good
        hideError(input);
        input.classList.remove('input-error');
        if (hint) { hint.textContent = '✓ Valid Gmail address'; hint.style.color = '#28a745'; }
    }

    // Email validation (kept for other email fields)
    function validateEmail(input) {
        if (input.name === 'student_email' || input.id === 'student_email') {
            validateGmail(input); return;
        }
        if (input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
            showError(input, 'Please enter a valid email address');
            input.classList.add('input-error');
        } else {
            hideError(input);
            input.classList.remove('input-error');
        }
    }

    // Show error message
    function showError(input, message) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    // Hide error message
    function hideError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.style.display = 'none';
        }
    }

    // Validate current step
    function validateCurrentStep(step) {
        const currentStepDiv = document.getElementById('appStep' + step);
        const requiredFields = currentStepDiv.querySelectorAll('[data-required="true"], [required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            // Checkboxes must use .checked, not .value
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    field.style.outline = '2px solid #dc2626';
                    isValid = false;
                } else {
                    field.style.outline = '';
                }
                return;
            }

            const value = sanitizeInput(field.value);
            field.value = value; // Update with sanitized value

            if (!value) {
                showError(field, 'This field is required');
                field.classList.add('input-error');
                isValid = false;
            } else {
                hideError(field);
                field.classList.remove('input-error');

                // Additional validation based on field type
                if (field.type === 'email') {
                    validateEmail(field);
                    if (field.classList.contains('input-error')) isValid = false;
                } else if (field.name === 'contact' || field.name === 'guardian_phone') {
                    validatePhone(field);
                    if (field.classList.contains('input-error')) isValid = false;
                }
            }
        });
        
        return isValid;
    }

    // Multi-Step Form Navigation
    function appStep(step) {
        console.log('appStep called with step:', step);
        console.log('Current step element:', document.querySelector('.form-step:not([style*="display: none"])'));
        
        // Validate current step before moving
        const currentStepElement = document.querySelector('.form-step:not([style*="display: none"])');
        const currentStep = currentStepElement ? parseInt(currentStepElement.id.replace('appStep', '')) : 1;
        
        if (step > currentStep && !validateCurrentStep(currentStep)) {
            showNotification('Please fill in all required fields correctly before proceeding.', 'error');
            return;
        }
        
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show current step
        const targetStepElement = document.getElementById('appStep' + step);
        if (targetStepElement) {
            targetStepElement.style.display = 'block';
        }
        
        // Update step circles
        for (let i = 1; i <= 5; i++) {
            const circle = document.getElementById('sc' + i);
            const label = document.getElementById('slb' + i);
            const line = document.getElementById('sl' + i);
            
            if (i < step) {
                circle.className = 'step-circle done';
                circle.textContent = '';
                label.className = 'step-label done';
                if (line) line.className = 'step-line done';
            } else if (i === step) {
                circle.className = 'step-circle active';
                circle.textContent = i;
                label.className = 'step-label active';
            } else {
                circle.className = 'step-circle';
                circle.textContent = i;
                label.className = 'step-label';
            }
        }

        // Fill review summary on step 5
        if (step === 5) {
            updateReviewSummary();
        }

        // Scroll to top of form
        document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = type === 'error' ? 'alert alert-danger' : 'alert alert-success';
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Update Review Summary
    function updateReviewSummary() {
        const form = document.getElementById('appForm');
        
        // Get form values
        const getValue = (name) => {
            const element = form.querySelector(`[name="${name}"]`);
            return element ? element.value : '-';
        };
        const getSelectText = (name) => {
            const select = form.querySelector(`[name="${name}"]`);
            if (!select || !select.options || select.selectedIndex === undefined) {
                return '-';
            }
            return select.options[select.selectedIndex]?.text || '-';
        };
        
        // Update summary fields
        document.getElementById('rev-name').textContent = 
            `${getValue('first_name')} ${getValue('middle_name')} ${getValue('last_name')}`.trim();
        document.getElementById('rev-gender').textContent = 
            getValue('gender') ? getValue('gender').charAt(0).toUpperCase() + getValue('gender').slice(1) : '-';
        document.getElementById('rev-dob').textContent = getValue('birthdate');
        document.getElementById('rev-grade').textContent = getSelectText('grade_level');
        document.getElementById('rev-type').textContent = getSelectText('student_type');
        document.getElementById('rev-blood-type').textContent = getValue('blood_type') || 'Not specified';
        document.getElementById('rev-allergies').textContent = getValue('allergies') || 'None';
        document.getElementById('rev-medical').textContent = getValue('medical_conditions') || 'None';
        document.getElementById('rev-region').textContent = getValue('region');
        document.getElementById('rev-province').textContent = getValue('province');
        document.getElementById('rev-city').textContent = getValue('city');
        document.getElementById('rev-barangay').textContent = getValue('barangay');
        document.getElementById('rev-street').textContent = getValue('street_address');
        document.getElementById('rev-guardian').textContent = getValue('guardian_name');
        document.getElementById('rev-rel').textContent = getSelectText('relationship');
        const rawPhone = getValue('guardian_phone');
        document.getElementById('rev-gphone').textContent = rawPhone ? '+63 ' + rawPhone : '-';
        document.getElementById('rev-email').textContent = getValue('student_email');
        
        // Add payment option to summary
        const paymentOption = getValue('payment_option');
        const totalAmount = getValue('total_amount');
        if (paymentOption !== '-') {
            const paymentSummary = document.createElement('div');
            paymentSummary.className = 'summary-section';
            paymentSummary.innerHTML = `
                <h6><i class="bi bi-credit-card-fill me-2"></i>Payment Information</h6>
                <div class="sum-row"><span>Payment Option</span><span>Option ${paymentOption} - ${paymentOptions[paymentOption]?.name || ''}</span></div>
                <div class="sum-row"><span>Total Amount</span><span>₱${parseFloat(totalAmount).toLocaleString()}</span></div>
            `;
            
            // Insert before terms checkbox
            const termsDiv = document.querySelector('#appStep6 .form-step');
            if (termsDiv && paymentOption !== '-') {
                const existingPaymentSummary = termsDiv.querySelector('.payment-summary-section');
                if (existingPaymentSummary) {
                    existingPaymentSummary.remove();
                }
                paymentSummary.className = 'summary-section payment-summary-section';
                const termsCheckbox = termsDiv.querySelector('input[name="terms"]');
                if (termsCheckbox) {
                    termsCheckbox.closest('div').before(paymentSummary);
                }
            }
        }
    }

    // Form Submission
    document.getElementById('appForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submission

        // Validate all steps
        let allValid = true;
        for (let i = 1; i <= 5; i++) {
            if (!validateCurrentStep(i)) {
                allValid = false;
                break;
            }
        }

        if (!allValid) {
            showNotification('Please correct all errors before submitting the application.', 'error');
            return;
        }
        
        // Sanitize all form data
        const formData = new FormData(this);
        const sanitizedData = {};
        
        for (let [key, value] of formData.entries()) {
            if (typeof value === 'string') {
                sanitizedData[key] = sanitizeInput(value);
            } else {
                sanitizedData[key] = value;
            }
        }
        
        // Show loading state
        const submitBtn = document.querySelector('.btn-step-submit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
        }
        
        // Submit form with sanitized data
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('[name="_token"]')?.value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json().then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Application submitted successfully! Your reference number is: ' + (data.reference_number || ''), 'success');
                        this.reset();
                        setTimeout(function() {
                            window.location.href = '{{ route("home") }}#enrollment';
                        }, 2500);
                    } else {
                        showNotification(data.message || 'Submission failed. Please try again.', 'error');
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0];
                            if (firstError) showNotification(firstError[0], 'error');
                        }
                    }
                }).catch(() => {
                    showNotification('Application submitted successfully!', 'success');
                    this.reset();
                    setTimeout(function() {
                        window.location.href = '{{ route("home") }}#enrollment';
                    }, 2500);
                });
            } else if (response.status === 422) {
                return response.json().then(data => {
                    const firstError = Object.values(data.errors || {})[0];
                    showNotification(firstError ? firstError[0] : 'Validation failed. Please check your inputs.', 'error');
                });
            } else {
                showNotification('Submission failed. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Submission error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            // Restore button to OTP trigger state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-shield-check"></i> Verify Email & Submit';
            }
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced Enter and Arrow key navigation
        const form = document.getElementById('appForm');
        if (form) {
            // Get all form steps and build navigation map for each step
            const steps = ['appStep1', 'appStep2', 'appStep3', 'appStep4', 'appStep5'];

            steps.forEach(stepId => {
                const stepDiv = document.getElementById(stepId);
                if (!stepDiv) return;

                // Get all rows in this step that contain inputs
                const rows = stepDiv.querySelectorAll('.row');
                let navigationGrid = [];

                // Build a 2D grid of inputs (row -> [inputs in left-to-right order])
                rows.forEach(row => {
                    const inputs = Array.from(row.querySelectorAll('input, select')).filter(el => !el.disabled);
                    if (inputs.length > 0) {
                        navigationGrid.push(inputs);
                    }
                });

                // Attach key handlers to all inputs in this step
                navigationGrid.forEach((rowInputs, rowIndex) => {
                    rowInputs.forEach((input, colIndex) => {
                        input.addEventListener('keydown', function(e) {
                            let targetInput = null;

                            if (e.key === 'Enter') {
                                e.preventDefault();
                                // Move to next field in reading order (right, or next row left)
                                if (colIndex < rowInputs.length - 1) {
                                    // Move right in same row
                                    targetInput = rowInputs[colIndex + 1];
                                } else if (rowIndex < navigationGrid.length - 1) {
                                    // Move to first input of next row
                                    targetInput = navigationGrid[rowIndex + 1][0];
                                }
                                // If at last field of last row, move to next step
                                if (!targetInput && stepId !== 'appStep5') {
                                    const currentStep = parseInt(stepId.replace('appStep', ''));
                                    appStep(currentStep + 1);
                                    // Focus first input of next step after transition
                                    setTimeout(() => {
                                        const nextStepDiv = document.getElementById('appStep' + (currentStep + 1));
                                        if (nextStepDiv) {
                                            const firstInput = nextStepDiv.querySelector('input, select');
                                            if (firstInput) firstInput.focus();
                                        }
                                    }, 100);
                                    return;
                                }
                            } else if (e.key === 'ArrowRight' && !e.shiftKey) {
                                e.preventDefault();
                                if (colIndex < rowInputs.length - 1) {
                                    targetInput = rowInputs[colIndex + 1];
                                }
                            } else if (e.key === 'ArrowLeft' && !e.shiftKey) {
                                e.preventDefault();
                                if (colIndex > 0) {
                                    targetInput = rowInputs[colIndex - 1];
                                }
                            } else if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                if (rowIndex < navigationGrid.length - 1) {
                                    // Try to find matching column, or use last available
                                    const nextRow = navigationGrid[rowIndex + 1];
                                    targetInput = nextRow[Math.min(colIndex, nextRow.length - 1)];
                                }
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                if (rowIndex > 0) {
                                    // Try to find matching column, or use last available
                                    const prevRow = navigationGrid[rowIndex - 1];
                                    targetInput = prevRow[Math.min(colIndex, prevRow.length - 1)];
                                }
                            }

                            if (targetInput) {
                                targetInput.focus();
                                // For select elements, open dropdown on focus
                                if (targetInput.tagName === 'SELECT') {
                                    targetInput.click();
                                }
                            }
                        });
                    });
                });
            });
        }

        function getCurrentStep() {
            for (let i = 1; i <= 5; i++) {
                const stepDiv = document.getElementById('appStep' + i);
                if (stepDiv && stepDiv.style.display !== 'none') {
                    return i;
                }
            }
            return 1;
        }

        // Add smooth scrolling for all internal links (skip bare # hrefs)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (!href || href === '#') return;
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
</script>

<script src="/js/ph-address.js"></script>
<script>
    PHAddress.initCascade({
        region:   'enroll-region',
        province: 'enroll-province',
        city:     'enroll-city',
        barangay: 'enroll-barangay',
    });
</script>

<script>
// ── Enrollment Form Auto-Save ──────────────────────────────────────────
const DRAFT_KEY = 'ilc_enrollment_draft';

// Fields to save (skip hidden/security fields and file inputs)
const SAVE_FIELDS = [
    'first_name','middle_name','last_name','suffix','birthdate','gender',
    'place_of_birth','nationality','grade_level','student_type','last_school',
    'mother_name','mother_age','father_name','father_age','religious_affiliation',
    'blood_type','allergies','medical_conditions',
    'region','province','city','barangay','street_address','zip_code',
    'guardian_name','relationship','guardian_occupation','guardian_phone','student_email'
];

let _draftTimer = null;

function saveEnrollmentDraft() {
    const form = document.getElementById('appForm');
    if (!form) return;
    const data = {};
    SAVE_FIELDS.forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el) data[name] = el.value;
    });
    data._savedAt = new Date().toISOString();
    localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
    // Show subtle saved indicator
    const banner = document.getElementById('draft-saved-indicator');
    if (banner) { banner.style.opacity = '1'; clearTimeout(banner._t); banner._t = setTimeout(() => banner.style.opacity = '0', 2000); }
}

function debouncedSave() {
    clearTimeout(_draftTimer);
    _draftTimer = setTimeout(saveEnrollmentDraft, 1500);
}

function clearEnrollmentDraft() {
    localStorage.removeItem(DRAFT_KEY);
    const banner = document.getElementById('draft-banner');
    if (banner) banner.style.display = 'none';
}

async function restoreEnrollmentDraft() {
    const raw = localStorage.getItem(DRAFT_KEY);
    if (!raw) return;
    let data;
    try { data = JSON.parse(raw); } catch(e) { return; }
    if (!data || !data._savedAt) return;

    const form = document.getElementById('appForm');
    if (!form) return;

    // Restore simple fields first (address cascade handled separately below)
    const cascades = ['region','province','city','barangay'];
    SAVE_FIELDS.forEach(name => {
        if (cascades.includes(name)) return;
        const el = form.querySelector(`[name="${name}"]`);
        if (el && data[name]) el.value = data[name];
    });

    // Restore the region/province/city/barangay cascade. PHAddress.setValues()
    // awaits the JSON preload before touching the selects, avoiding a race
    // where a plain regionEl.value= assignment silently no-ops because the
    // region <select> still only has its "Loading regions..." placeholder.
    if (data.region || data.province || data.city || data.barangay) {
        await PHAddress.setValues(
            { region: 'enroll-region', province: 'enroll-province', city: 'enroll-city', barangay: 'enroll-barangay' },
            { region: data.region, province: data.province, city: data.city, barangay: data.barangay }
        );
    }

    // Show restored banner
    const banner = document.getElementById('draft-banner');
    if (banner) banner.style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('appForm');
    if (!form) return;

    // Attach save listeners
    SAVE_FIELDS.forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el) el.addEventListener('input', debouncedSave);
        if (el) el.addEventListener('change', debouncedSave);
    });

    // Clear draft on successful form submission (OTP verified)
    form.addEventListener('submit', clearEnrollmentDraft);

    // Restore draft on load
    restoreEnrollmentDraft();
});
</script>

</div>{{-- /real-enr-content --}}

<script>
(function () {
    function revealEnrollment() {
        var skel = document.getElementById('skel-enr-content');
        var real = document.getElementById('real-enr-content');
        if (skel) skel.style.display = 'none';
        if (real) {
            real.style.display = 'block';
            void real.offsetWidth;
            real.style.transition = 'opacity 0.4s ease';
            real.style.opacity = '1';
        }
        document.body.classList.remove('page-loading');
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(revealEnrollment, 220);
        });
    } else {
        setTimeout(revealEnrollment, 220);
    }
})();
</script>

<!-- ══ Terms & Conditions / Privacy Policy Modal ══ -->
<div id="tcModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.55);overflow-y:auto;padding:24px 16px;">
    <div style="background:#fff;border-radius:16px;max-width:640px;margin:0 auto;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;">

        {{-- Modal header --}}
        <div style="background:var(--ilc-blue);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;color:#fff;">
                <i class="bi bi-shield-check" style="font-size:24px;color:var(--ilc-gold);"></i>
                <div>
                    <div style="font-size:16px;font-weight:700;">Terms &amp; Conditions and Privacy Policy</div>
                    <div style="font-size:12px;opacity:0.8;">Please read carefully before proceeding to review</div>
                </div>
            </div>
            <button type="button" onclick="closeTcModal()" style="background:none;border:none;color:#fff;font-size:22px;cursor:pointer;opacity:0.7;line-height:1;" title="Cancel">&times;</button>
        </div>

        {{-- Tab switcher --}}
        <div style="display:flex;border-bottom:2px solid #e5e7eb;">
            <button type="button" id="modal-tc-tab-terms" onclick="modalTcTab('terms')"
                style="flex:1;padding:12px 0;font-size:13px;font-weight:700;border:none;background:#fff;color:var(--ilc-blue);border-bottom:3px solid var(--ilc-blue);cursor:pointer;transition:all 0.2s;">
                <i class="bi bi-file-text me-1"></i> Terms &amp; Conditions
            </button>
            <button type="button" id="modal-tc-tab-privacy" onclick="modalTcTab('privacy')"
                style="flex:1;padding:12px 0;font-size:13px;font-weight:700;border:none;background:#fff;color:#9ca3af;border-bottom:3px solid transparent;cursor:pointer;transition:all 0.2s;">
                <i class="bi bi-shield-lock me-1"></i> Privacy Policy
            </button>
        </div>

        {{-- Scrollable content --}}
        <div id="modal-tc-box" style="height:300px;overflow-y:auto;padding:20px 24px;font-size:12.5px;color:#444;line-height:1.8;">
            <div id="modal-tc-pane-terms">
                <p style="font-weight:700;color:#1a3a6c;margin:0 0 10px;">IEMELIF LEARNING CENTER — Terms and Conditions</p>
                <p style="color:#888;font-size:11px;">Online Enrollment System</p>

                <p><strong>1. Acceptance of Terms</strong><br>By accessing, registering, and using the ILC Online Enrollment System, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use this system.</p>

                <p><strong>2. Eligibility</strong><br>The ILC Online Enrollment System is available to:</p>
                <ul>
                    <li>Students seeking enrollment at Immaculate Learning Center</li>
                    <li>Parents or legal guardians of students</li>
                    <li>Authorized school staff and administrators</li>
                </ul>
                <p>You must be at least 18 years old or have parental consent to use this system on behalf of a minor.</p>

                <p><strong>3. User Account Responsibilities</strong></p>
                <p><strong>3.1 Account Creation</strong><br>Users are responsible for maintaining the confidentiality of their account credentials. You agree to:</p>
                <ul>
                    <li>Keep your password secure and not share it with anyone</li>
                    <li>Notify the school immediately if you suspect unauthorized access</li>
                    <li>Provide accurate and complete information during registration</li>
                    <li>Update your information when it changes</li>
                </ul>
                <p><strong>3.2 Account Security</strong><br>You are solely responsible for all activities that occur under your account. ILC is not liable for any loss or damage arising from your failure to secure your account.</p>

                <p><strong>4. Enrollment Policies</strong></p>
                <p><strong>4.1 Application Process</strong><br>Enrollment applications are subject to review and approval by ILC. Submission of an application does not guarantee enrollment. The school reserves the right to:</p>
                <ul>
                    <li>Accept or reject enrollment applications</li>
                    <li>Request additional documentation</li>
                    <li>Cancel enrollment for non-compliance with requirements</li>
                </ul>
                <p><strong>4.2 Required Documents</strong><br>Students must submit all required documents as specified in the enrollment checklist, including but not limited to:</p>
                <ul>
                    <li>Birth certificate (NSO/PSA copy)</li>
                    <li>Report cards or Form 138</li>
                    <li>Good moral certificate</li>
                    <li>Recent ID photos</li>
                    <li>Other documents as required by the school</li>
                </ul>
                <p><strong>4.3 Enrollment Payment</strong><br>Enrollment is considered complete upon:</p>
                <ul>
                    <li>Submission of all required documents</li>
                    <li>Payment of enrollment fees or down payment</li>
                    <li>Approval of the application by the school administration</li>
                </ul>
                <p>Payment plans are available as per the school's fee schedule. Failure to pay fees on time may result in enrollment cancellation.</p>

                <p><strong>5. Payment Terms</strong></p>
                <p><strong>5.1 Payment Methods</strong><br>ILC accepts payment through:</p>
                <ul>
                    <li>Cash (at the school's finance office)</li>
                    <li>GCash</li>
                    <li>Bank transfer (details provided upon request)</li>
                </ul>
                <p><strong>5.2 Payment Deadlines and Late Fees</strong><br>For students under an installment payment plan, each monthly installment is due on the date shown in the student's Payment Schedule, visible at all times in the Student Portal. If a payment is not received by the due date, the following applies automatically:</p>
                <ul>
                    <li><strong>1 week</strong> past the due date — a warning notice is sent by email.</li>
                    <li><strong>2 weeks</strong> past the due date — a follow-up reminder is sent (grace period; no fee yet).</li>
                    <li><strong>3 weeks</strong> past the due date — a late fee of <strong>₱500</strong> is added to that installment, and a confirmation notice is sent.</li>
                </ul>
                <p>Weekly reminders continue automatically for as long as an installment remains unpaid. A late fee alone does not affect a student's ability to attend class; see Sections 5.4 and 5.5 for what happens if payments fall further behind.</p>
                <p><strong>5.3 Refund Policy</strong><br>Refunds are subject to the school's refund policy:</p>
                <ul>
                    <li>Full refund if withdrawal is made before the start of classes</li>
                    <li>Partial refund if withdrawal is made within the first week of classes</li>
                    <li>No refund for withdrawals after the first week of classes</li>
                    <li>Special circumstances may be considered on a case-by-case basis</li>
                </ul>
                <p><strong>5.4 Promissory Note</strong><br>A <strong>Promissory Note</strong> is a written agreement between the parent/guardian and the school's Finance Office, stating a specific amount the family will pay by a specific promised date. It may be requested by the parent/guardian, or offered by the school, and becomes required once an account reaches the condition described in Section 5.5.</p>
                <ul>
                    <li>While a Promissory Note is active and its promised date has not yet passed, the account is considered in good standing.</li>
                    <li>If the promised date passes without full payment, the note is considered <strong>broken</strong>. This may result in the restriction described in Section 5.5 being applied or re-applied, and may be subject to additional terms set by the Finance Office.</li>
                    <li>Only Finance staff or the school Administrator may issue, extend, or resolve a Promissory Note — it cannot be self-certified by a parent/guardian.</li>
                    <li>A copy of every Promissory Note is available to the parent/guardian upon request.</li>
                </ul>
                <p><strong>5.5 Exam Permit</strong><br>An <strong>Exam Permit</strong> is the school's permission for a student to sit for periodic/quarterly exams.</p>
                <ul>
                    <li>The Exam Permit is withheld when payments are <strong>three (3) consecutive months</strong> behind schedule and there is no Promissory Note in good standing, or when a Promissory Note on the account has been broken (Section 5.4).</li>
                    <li>Withholding the Exam Permit is a restriction on taking the exam, separate from the late fee described in Section 5.2.</li>
                    <li>The Exam Permit is automatically restored as soon as the account balance is brought current, or a new Promissory Note is signed before its promised date, or the Finance Office confirms the balance has been settled — no separate request is needed.</li>
                    <li>This restriction applies specifically to periodic/quarterly exams and does not affect a student's ability to attend regular classes.</li>
                    <li>Parents/guardians may contact the Finance Office at any time to check their account status or arrange a Promissory Note before this restriction applies.</li>
                </ul>

                <p><strong>6. Academic Integrity and Conduct</strong></p>
                <p><strong>6.1 Student Conduct</strong><br>Students enrolled at ILC are expected to:</p>
                <ul>
                    <li>Adhere to the school's code of conduct and student handbook</li>
                    <li>Respect teachers, staff, and fellow students</li>
                    <li>Attend classes regularly and complete assignments on time</li>
                    <li>Maintain academic honesty in all school work</li>
                </ul>
                <p><strong>6.2 Academic Honesty</strong><br>Cheating, plagiarism, or any form of academic dishonesty is strictly prohibited and may result in disciplinary action, including suspension or expulsion.</p>

                <p><strong>7. Use of School Systems</strong></p>
                <p><strong>7.1 Acceptable Use</strong><br>The ILC Online Enrollment System and other school-provided technology must be used for educational purposes only. Prohibited activities include:</p>
                <ul>
                    <li>Attempting to hack, disrupt, or damage school systems</li>
                    <li>Uploading malicious software or viruses</li>
                    <li>Sharing copyrighted material without permission</li>
                    <li>Using the system for commercial purposes</li>
                    <li>Harassing or threatening other users</li>
                </ul>
                <p><strong>7.2 Monitoring</strong><br>ILC reserves the right to monitor system usage for security and compliance purposes. Users have no expectation of privacy when using school systems.</p>

                <p><strong>8. Privacy and Data Protection</strong><br>ILC is committed to protecting your personal information. Our Privacy Policy, shown in the tab above, details how we collect, use, store, and protect your data. By using this system, you consent to our data practices as outlined in our Privacy Policy.</p>

                <p><strong>9. Intellectual Property</strong><br>All content on the ILC Online Enrollment System, including text, graphics, logos, and software, is the property of Immaculate Learning Center and is protected by copyright laws. Users may not reproduce, distribute, or create derivative works without explicit permission.</p>

                <p><strong>10. Communication</strong><br>By enrolling, you agree to receive communications from ILC through:</p>
                <ul>
                    <li>Email (enrollment updates, announcements, reminders)</li>
                    <li>SMS (important alerts and notifications)</li>
                    <li>Phone calls (for urgent matters)</li>
                </ul>
                <p>You may opt out of non-essential communications by contacting the school administration.</p>

                <p><strong>11. Termination</strong><br>ILC reserves the right to suspend or terminate access to the enrollment system and cancel enrollment for:</p>
                <ul>
                    <li>Violation of these Terms and Conditions</li>
                    <li>Non-payment of fees</li>
                    <li>Providing false or misleading information</li>
                    <li>Behavior that disrupts the learning environment</li>
                    <li>Other violations of school policies</li>
                </ul>

                <p><strong>12. Limitation of Liability</strong><br>ILC shall not be liable for any indirect, incidental, special, or consequential damages arising from the use or inability to use the enrollment system, including but not limited to loss of data, loss of enrollment opportunities, or business interruption.</p>

                <p><strong>13. Modifications to Terms</strong><br>ILC reserves the right to modify these Terms and Conditions at any time. Changes will be posted on this page with an updated revision date. Continued use of the system after changes constitutes acceptance of the new terms.</p>

                <p><strong>14. Governing Law</strong><br>These Terms and Conditions are governed by the laws of the Republic of the Philippines. Any disputes arising from these terms shall be resolved in the appropriate courts of the Philippines.</p>

                <p><strong>15. Contact Information</strong><br>For questions about these Terms and Conditions or the enrollment process, please contact:</p>
                <p style="background:#e8f0fb;padding:10px 12px;border-left:3px solid #1a3a6c;border-radius:4px;">
                    <strong>Immaculate Learning Center</strong><br>
                    Address: Brgy Poblacion Central General Tinio Nueva Ecija<br>
                    Phone: 0951-989-9685<br>
                    Email: Iemelif_learningcenter@gmail.com<br>
                    Office Hours: Monday – Friday 7:30 AM – 5:00 PM
                </p>

                <p><strong>16. Agreement</strong><br>By clicking "I Agree" or using the ILC Online Enrollment System, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>

                <p style="color:#888;font-size:11px;">Full document: <a href="{{ route('terms') }}" target="_blank" style="color:var(--ilc-blue);">View complete Terms &amp; Conditions</a></p>
            </div>
            <div id="modal-tc-pane-privacy" style="display:none;">
                <p style="font-weight:700;color:#1a3a6c;margin:0 0 10px;">IEMELIF LEARNING CENTER — Privacy Policy</p>
                <p style="color:#888;font-size:11px;">Online Enrollment System</p>

                <p>IEMELIF Learning Center ("ILC", "we", "us", or "our") is committed to protecting the privacy and personal data of our students, parents, guardians, and all users of our Online Enrollment System. This Privacy Policy explains how we collect, use, store, share, and protect your personal information in compliance with <strong>Republic Act No. 10173</strong>, otherwise known as the <strong>Data Privacy Act of 2012</strong>, and its Implementing Rules and Regulations.</p>
                <p>By using the ILC Online Enrollment System and submitting an enrollment application, you acknowledge that you have read and understood this Privacy Policy and you consent to the collection, use, and processing of your personal data as described herein.</p>

                <p><strong>1. Data Controller Information</strong><br>The personal data you provide through this system is collected and controlled by:</p>
                <p style="background:#e8f0fb;padding:10px 12px;border-left:3px solid #1a3a6c;border-radius:4px;">
                    <strong>IEMELIF Learning Center</strong><br>
                    General Tinio, Nueva Ecija, Philippines<br>
                    Phone: 0951-989-9685<br>
                    Email: Iemelif_learningcenter@gmail.com<br>
                    Data Protection Officer: Mrs. Teofila Guillermo
                </p>

                <p><strong>2. Personal Data We Collect</strong><br>When you use our Online Enrollment System, we collect the following categories of personal data:</p>
                <p><strong>2.1 Student Information</strong></p>
                <ul>
                    <li>Full name (first, middle, last, suffix)</li>
                    <li>Date of birth, place of birth, and age</li>
                    <li>Gender and nationality</li>
                    <li>Learner's Reference Number (LRN)</li>
                    <li>Grade level and student type (new, transferee, or returning)</li>
                    <li>Previous school attended and last grade completed</li>
                </ul>
                <p><strong>2.2 Contact and Address Information</strong></p>
                <ul>
                    <li>Home address (province, city/municipality, barangay, street address, ZIP code)</li>
                    <li>Contact numbers</li>
                    <li>Email address</li>
                </ul>
                <p><strong>2.3 Parent and Guardian Information</strong></p>
                <ul>
                    <li>Full name of mother, father, and/or legal guardian</li>
                    <li>Relationship to the student</li>
                    <li>Occupation and age</li>
                    <li>Contact number and email address</li>
                </ul>
                <p><strong>2.4 Health Information</strong></p>
                <ul>
                    <li>Blood type</li>
                    <li>Known allergies</li>
                    <li>Existing medical conditions or special health needs</li>
                </ul>
                <p style="background:#fff8e1;padding:10px 12px;border-left:3px solid #f59e0b;border-radius:4px;"><strong>Note:</strong> Health information is considered sensitive personal data under the Data Privacy Act of 2012. It is collected solely for the safety and well-being of the student and is treated with the highest level of confidentiality.</p>
                <p><strong>2.5 Financial Information</strong></p>
                <ul>
                    <li>Selected payment option (A, B, C, or D)</li>
                    <li>Payment amounts and outstanding balances</li>
                    <li>Payment method (cash or GCash)</li>
                    <li>Payment reference numbers</li>
                    <li>Payment screenshots submitted for verification</li>
                </ul>
                <p><strong>2.6 Submitted Documents</strong></p>
                <ul>
                    <li>Birth Certificate (PSA/NSO copy)</li>
                    <li>Form 137 (Permanent Record)</li>
                    <li>Form 138 (Report Card)</li>
                    <li>Certificate of Good Moral Character</li>
                    <li>Barangay Clearance</li>
                    <li>Other documents required for enrollment</li>
                </ul>
                <p><strong>2.7 System and Account Data</strong></p>
                <ul>
                    <li>Student portal login credentials (email and encrypted password)</li>
                    <li>Account activity and login history</li>
                    <li>Enrollment application status and history</li>
                </ul>

                <p><strong>3. Purpose and Legal Basis for Processing</strong><br>We collect and process your personal data for the following purposes:</p>
                <ul>
                    <li>Processing and managing your enrollment application — fulfillment of a contract / legal obligation</li>
                    <li>Verifying student eligibility and reviewing documents — legitimate interest / legal obligation</li>
                    <li>Creating and managing student portal accounts — fulfillment of a contract</li>
                    <li>Processing payments and managing tuition accounts — fulfillment of a contract / legal obligation</li>
                    <li>Maintaining health records for student safety — protection of vital interests / consent</li>
                    <li>Sending enrollment updates, notifications, and reminders — legitimate interest / consent</li>
                    <li>Assigning students to grade sections and schedules — fulfillment of a contract</li>
                    <li>Compliance with DepEd and government reporting requirements — legal obligation</li>
                    <li>Improving our enrollment system and services — legitimate interest</li>
                </ul>

                <p><strong>4. How We Use Your Personal Data</strong><br>Your personal data is used exclusively for school-related purposes, including:</p>
                <ul>
                    <li>Reviewing, approving, or declining enrollment applications</li>
                    <li>Creating and managing student accounts in our portal system</li>
                    <li>Assigning students to appropriate grade levels and class sections</li>
                    <li>Generating enrollment records, clearances, and official school documents</li>
                    <li>Processing and tracking tuition payments and installment schedules</li>
                    <li>Communicating enrollment status, payment reminders, and school announcements</li>
                    <li>Monitoring student health needs to ensure a safe learning environment</li>
                    <li>Complying with reporting obligations to the Department of Education (DepEd) and other government agencies</li>
                    <li>Conducting academic performance tracking and promotion</li>
                </ul>

                <p><strong>5. Data Sharing and Disclosure</strong><br>We do <strong>not</strong> sell, rent, or trade your personal data to any third party. Your data may only be shared in the following circumstances:</p>
                <p><strong>5.1 Within the School</strong><br>Your data is accessible only to authorized school personnel whose roles require it, including school administrators, finance staff, class advisers, and the school principal. Access is role-based and limited to what is necessary.</p>
                <p><strong>5.2 Government Agencies</strong><br>We may be required by law to share certain student data with government agencies such as the Department of Education (DepEd), the Philippine Statistics Authority (PSA), or local government units, strictly for compliance and reporting purposes.</p>
                <p><strong>5.3 Emergency Situations</strong><br>In the event of a medical emergency or situation involving the safety of a student, relevant health and contact information may be disclosed to emergency responders, medical professionals, or authorized family members.</p>
                <p><strong>5.4 Legal Requirements</strong><br>We may disclose your information if required to do so by a court order, subpoena, or other valid legal process, or when disclosure is necessary to protect our legal rights or the rights of others.</p>

                <p><strong>6. Data Retention</strong><br>We retain your personal data for as long as is necessary to fulfill the purposes for which it was collected, and in accordance with applicable laws and regulations:</p>
                <ul>
                    <li><strong>Enrollment records</strong> — retained for the duration of the student's enrollment and for a minimum of <strong>10 years</strong> after the student's graduation or departure, in compliance with DepEd records management guidelines.</li>
                    <li><strong>Payment records</strong> — retained for a minimum of <strong>5 years</strong> for audit and financial compliance purposes.</li>
                    <li><strong>Health records</strong> — retained for the duration of enrollment and <strong>3 years</strong> thereafter.</li>
                    <li><strong>Uploaded documents</strong> — retained for the duration of the student's enrollment and may be deleted upon the student's request after graduation, subject to school policy.</li>
                    <li><strong>Account credentials</strong> — deactivated upon graduation or withdrawal and purged after the applicable retention period.</li>
                </ul>
                <p>Rejected or withdrawn applications are retained for a minimum of <strong>1 year</strong> for reference purposes, after which they are securely deleted or anonymized.</p>

                <p><strong>7. Data Security</strong><br>ILC implements appropriate technical and organizational security measures to protect your personal data against unauthorized access, disclosure, alteration, or destruction. These measures include:</p>
                <ul>
                    <li>Encrypted storage of passwords and sensitive data</li>
                    <li>Role-based access controls limiting data access to authorized personnel only</li>
                    <li>Secure file storage for uploaded documents and payment screenshots</li>
                    <li>Regular system monitoring and security reviews</li>
                    <li>Use of HTTPS/SSL encryption for data transmitted through the enrollment portal</li>
                </ul>
                <p>While we take every reasonable precaution, no system can guarantee absolute security. In the event of a data breach that poses a risk to your rights and freedoms, we will notify the National Privacy Commission (NPC) and affected individuals in accordance with the Data Privacy Act of 2012.</p>

                <p><strong>8. Your Rights as a Data Subject</strong><br>Under the Data Privacy Act of 2012 (RA 10173), you have the following rights regarding your personal data:</p>
                <ul>
                    <li><strong>Right to be Informed</strong> — You have the right to know what personal data we collect, how it is used, and to whom it may be disclosed.</li>
                    <li><strong>Right to Access</strong> — You may request a copy of the personal data we hold about you at any time.</li>
                    <li><strong>Right to Rectification</strong> — You may request correction of inaccurate or incomplete personal data. Students or parents should inform the school registrar of any changes.</li>
                    <li><strong>Right to Erasure or Blocking</strong> — You may request the deletion or blocking of your personal data under certain circumstances, subject to the school's legal retention obligations.</li>
                    <li><strong>Right to Object</strong> — You may object to the processing of your personal data for specific purposes, such as marketing or non-essential communications.</li>
                    <li><strong>Right to Data Portability</strong> — You may request that we provide your personal data in a structured, commonly used, and machine-readable format.</li>
                    <li><strong>Right to Lodge a Complaint</strong> — You have the right to file a complaint with the National Privacy Commission (NPC) if you believe your data privacy rights have been violated.</li>
                </ul>
                <p>To exercise any of these rights, please contact our Data Protection Officer using the contact details in Section 1. We will respond to your request within <strong>15 working days</strong> of receipt.</p>

                <p><strong>9. Children's Privacy</strong><br>The ILC Online Enrollment System is used to enroll minor students. The personal data of minors is collected with the full knowledge and consent of their parent or legal guardian, who is responsible for completing and submitting the enrollment application on the student's behalf.</p>
                <p>Parents and guardians have the right to access, correct, or request the deletion of their child's personal data at any time, subject to applicable laws and school policies.</p>

                <p><strong>10. Cookies and System Data</strong><br>Our enrollment system uses session cookies to maintain your login session and ensure secure access. These cookies are temporary and are deleted when you close your browser or log out. We do not use cookies for tracking or advertising purposes.</p>

                <p><strong>11. Third-Party Services</strong><br>Our enrollment system may use the following third-party services for technical functionality:</p>
                <ul>
                    <li><strong>GCash</strong> — for processing online payments. Payments made through GCash are subject to GCash's own privacy policy and terms of service.</li>
                    <li><strong>Email service providers</strong> — for sending enrollment notifications and updates. Only the necessary information (name, email address) is shared for delivery purposes.</li>
                </ul>
                <p>ILC is not responsible for the privacy practices of these third-party services. We encourage you to review their respective privacy policies.</p>

                <p><strong>12. Changes to This Privacy Policy</strong><br>ILC reserves the right to update or modify this Privacy Policy at any time to reflect changes in our practices, legal requirements, or system improvements. Any changes will be posted on this page with a revised effective date. Continued use of the enrollment system after any changes constitutes your acceptance of the updated policy.</p>
                <p>For significant changes, we will notify parents and guardians through the contact information provided during enrollment.</p>

                <p><strong>13. Contact and Complaints</strong><br>If you have questions, concerns, or requests regarding this Privacy Policy or how we handle your personal data, please contact us:</p>
                <p style="background:#e8f0fb;padding:10px 12px;border-left:3px solid #1a3a6c;border-radius:4px;">
                    <strong>IEMELIF Learning Center</strong><br>
                    General Tinio, Nueva Ecija, Philippines<br>
                    Phone: 0951-989-9685<br>
                    Email: Iemelif_learningcenter@gmail.com<br>
                    Data Protection Officer: Mrs. Teofila Guillermo<br>
                    Office Hours: Monday to Friday, 8:00 AM – 5:00 PM
                </p>
                <p>If you are not satisfied with our response, you have the right to file a complaint with the <strong>National Privacy Commission (NPC)</strong>:</p>
                <p style="background:#e8f0fb;padding:10px 12px;border-left:3px solid #1a3a6c;border-radius:4px;">
                    National Privacy Commission<br>
                    3/F, Core G, GSIS Complex, Roxas Boulevard, Pasay City, 1308 Philippines<br>
                    Website: <a href="https://www.privacy.gov.ph" target="_blank" style="color:var(--ilc-blue);">www.privacy.gov.ph</a><br>
                    Email: info@privacy.gov.ph
                </p>

                <p><strong>14. Consent</strong><br>By submitting an enrollment application through this system and checking the "I agree" checkbox, you confirm that:</p>
                <ul>
                    <li>You have read and understood this Privacy Policy in full.</li>
                    <li>You consent to the collection, use, and processing of the personal data described in this policy for the stated purposes.</li>
                    <li>You are at least 18 years old, or you are acting as the parent or legal guardian of the student being enrolled.</li>
                    <li>All information provided is true, accurate, and complete to the best of your knowledge.</li>
                </ul>

                <p style="color:#888;font-size:11px;">Full document: <a href="{{ route('privacy') }}" target="_blank" style="color:var(--ilc-blue);">View complete Privacy Policy</a></p>
            </div>
        </div>

        {{-- Scroll hint --}}
        <div id="modal-tc-scroll-hint" style="text-align:center;font-size:11px;color:#9ca3af;padding:6px 24px 0;display:flex;align-items:center;justify-content:center;gap:6px;">
            <i class="bi bi-arrow-down-circle"></i> Scroll down to read the full document
        </div>

        {{-- Checkbox --}}
        <div style="padding:16px 24px 0;">
            <label style="display:flex;align-items:flex-start;gap:10px;font-size:13px;color:#374151;cursor:not-allowed;padding:14px 16px;background:#f8fafc;border:1.5px solid #d1d5db;border-radius:10px;transition:border-color 0.2s;opacity:0.6;" id="modal-tc-label">
                <input type="checkbox" id="modal-tc-checkbox" disabled
                    onchange="onModalTcCheck()"
                    style="margin-top:2px;accent-color:var(--ilc-blue);width:16px;height:16px;flex-shrink:0;">
                <span>I have read and understood the <strong>Terms and Conditions</strong> and <strong>Privacy Policy</strong> of IEMELIF Learning Center, and I certify that all information provided in this application is true and correct.</span>
            </label>
        </div>

        {{-- Footer buttons --}}
        <div style="display:flex;gap:12px;padding:20px 24px;justify-content:flex-end;">
            <button type="button" onclick="closeTcModal()"
                style="padding:11px 24px;border-radius:8px;border:1.5px solid #d1d5db;background:#fff;color:#6b7280;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;">
                <i class="bi bi-x-lg me-1"></i> Cancel
            </button>
            <button type="button" id="modal-tc-accept-btn" onclick="acceptTcAndProceed()" disabled
                style="padding:11px 28px;border-radius:8px;border:none;background:#cbd5e1;color:#fff;font-size:13px;font-weight:700;cursor:not-allowed;transition:all 0.2s;">
                <i class="bi bi-check-lg me-1"></i> Accept &amp; Review Application
            </button>
        </div>
    </div>
</div>

<script>
// ── T&C Modal ──
var _tcRead = { terms: false, privacy: false };
var _tcActiveTab = 'terms';

function openTcModal() {
    var currentStepEl = document.querySelector('.form-step:not([style*="display: none"])');
    var currentStep = currentStepEl ? parseInt(currentStepEl.id.replace('appStep','')) : 1;
    if (currentStep === 4 && !validateCurrentStep(4)) {
        showNotification('Please fill in all required fields correctly before proceeding.', 'error');
        return;
    }
    // Reset modal state — both documents must be scrolled through again
    // each time the modal is opened, same as the checkbox itself resetting.
    _tcRead = { terms: false, privacy: false };
    var checkbox = document.getElementById('modal-tc-checkbox');
    checkbox.checked = false;
    checkbox.disabled = true;
    document.getElementById('modal-tc-label').style.opacity = '0.6';
    document.getElementById('modal-tc-label').style.cursor = 'not-allowed';
    document.getElementById('modal-tc-accept-btn').disabled = true;
    document.getElementById('modal-tc-accept-btn').style.background = '#cbd5e1';
    document.getElementById('modal-tc-accept-btn').style.cursor = 'not-allowed';
    document.getElementById('modal-tc-label').style.borderColor = '#d1d5db';
    modalTcTab('terms');
    document.getElementById('tcModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeTcModal() {
    document.getElementById('tcModal').style.display = 'none';
    document.body.style.overflow = '';
}

function modalTcTab(tab) {
    _tcActiveTab = tab;
    document.getElementById('modal-tc-pane-terms').style.display   = tab === 'terms'   ? '' : 'none';
    document.getElementById('modal-tc-pane-privacy').style.display = tab === 'privacy' ? '' : 'none';
    var tBtn = document.getElementById('modal-tc-tab-terms');
    var pBtn = document.getElementById('modal-tc-tab-privacy');
    tBtn.style.color       = tab === 'terms'   ? 'var(--ilc-blue)' : '#9ca3af';
    tBtn.style.borderBottom= tab === 'terms'   ? '3px solid var(--ilc-blue)' : '3px solid transparent';
    pBtn.style.color       = tab === 'privacy' ? 'var(--ilc-blue)' : '#9ca3af';
    pBtn.style.borderBottom= tab === 'privacy' ? '3px solid var(--ilc-blue)' : '3px solid transparent';
    var box = document.getElementById('modal-tc-box');
    box.scrollTop = 0;
    // Let the display:none/'' change above actually apply before measuring
    // scrollHeight, otherwise the hidden pane's old height can be read.
    setTimeout(function () {
        if (box.scrollHeight <= box.clientHeight + 4) {
            markTcTabRead(tab);
        } else {
            updateTcScrollHint();
        }
    }, 30);
}

function markTcTabRead(tab) {
    if (_tcRead[tab]) return;
    _tcRead[tab] = true;
    updateTcScrollHint();
    var checkbox = document.getElementById('modal-tc-checkbox');
    var label = document.getElementById('modal-tc-label');
    var bothRead = _tcRead.terms && _tcRead.privacy;
    checkbox.disabled = !bothRead;
    label.style.opacity = bothRead ? '1' : '0.6';
    label.style.cursor = bothRead ? 'pointer' : 'not-allowed';
}

function updateTcScrollHint() {
    var hint = document.getElementById('modal-tc-scroll-hint');
    if (_tcRead.terms && _tcRead.privacy) {
        hint.style.display = 'none';
        return;
    }
    hint.style.display = 'flex';
    if (!_tcRead[_tcActiveTab]) {
        hint.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Scroll down to finish reading this document';
    } else {
        var otherLabel = _tcActiveTab === 'terms' ? 'Privacy Policy' : 'Terms &amp; Conditions';
        hint.innerHTML = '<i class="bi bi-arrow-left-right"></i> Please also read the ' + otherLabel;
    }
}

document.getElementById('modal-tc-box').addEventListener('scroll', function () {
    var nearBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 8;
    if (nearBottom) markTcTabRead(_tcActiveTab);
});

function onModalTcCheck() {
    var checked = document.getElementById('modal-tc-checkbox').checked;
    var btn     = document.getElementById('modal-tc-accept-btn');
    var lbl     = document.getElementById('modal-tc-label');
    btn.disabled       = !checked;
    btn.style.background  = checked ? 'var(--ilc-blue)' : '#cbd5e1';
    btn.style.cursor      = checked ? 'pointer'         : 'not-allowed';
    lbl.style.borderColor = checked ? 'var(--ilc-blue)' : '#d1d5db';
}

function acceptTcAndProceed() {
    if (!document.getElementById('modal-tc-checkbox').checked) return;
    // Mark as accepted
    var appTerms = document.getElementById('appTerms');
    appTerms.checked = true;
    // Show accepted badge on step 5
    var badge = document.getElementById('tc-accepted-badge');
    if (badge) badge.style.display = 'flex';
    closeTcModal();
    appStep(5);
}

// Close modal when clicking backdrop
document.getElementById('tcModal').addEventListener('click', function(e) {
    if (e.target === this) closeTcModal();
});
</script>

</body>
</html>
