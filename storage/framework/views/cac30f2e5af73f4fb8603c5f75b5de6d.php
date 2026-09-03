<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780320000">
    <link rel="stylesheet" href="<?php echo e(asset('css/enrollment.css')); ?>?v=1780320000">
    <style>
        /* Custom Scrollbar Design */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #003d82;
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ffd700;
        }
        .main-nav { 
            background: #1a3a6c !important;
            display: block !important;
            position: relative !important;
        }
        .main-nav .navbar-nav .nav-link {
            color: #fff !important;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 10px 14px !important;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }
        .main-nav .navbar-nav .nav-link:hover,
        .main-nav .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
        }
    </style>
    <style>
        /* â”€â”€ ADMISSION PAGE SPECIFIC â”€â”€ */
        :root {
            --ilc-blue: #1a3a6c;
            --ilc-gold: #f5a623;
            --ilc-light-blue: #2980b9;
            --ilc-gray-bg: #f4f4f4;
            --ilc-border: #ddd;
        }

        /* â”€â”€ REQUIREMENTS SECTION â”€â”€ */
        .req-section { padding: 50px 0 40px; background: #fff; }

        .req-section-title {
            font-size: 24px; font-weight: 800;
            font-family: 'Open Sans', sans-serif;
            color: var(--ilc-blue); text-transform: none;
            letter-spacing: 0.3px; margin-bottom: 8px;
            line-height: 1.25;
        }

        .req-section-title::after {
            content: ''; display: block;
            width: 48px; height: 3px;
            background: #c5a059;
            margin-top: 10px; border-radius: 2px;
        }

        /* ── Grade-card style req cards ── */
        .req-card {
            background: #fff; border-radius: 14px;
            overflow: hidden; height: 100%;
            box-shadow: 0 2px 12px rgba(26,58,108,.07);
            border: 1.5px solid #e8edf5;
            transition: transform .2s, box-shadow .2s;
        }

        .req-card::before { display: none; }

        .req-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(26,58,108,.13); }

        .req-card-header {
            background: #1a3a6c; padding: 16px 20px;
            display: flex; align-items: center; gap: 12px;
        }

        .req-icon-box {
            width: 40px; height: 40px;
            background: rgba(197,160,89,.2);
            border-radius: 8px; display: flex;
            align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .req-icon-box i { font-size: 18px; color: #c5a059; }

        .req-card h5 {
            font-size: 14px; font-weight: 800; color: #fff;
            text-transform: uppercase; letter-spacing: 0.8px; margin: 0;
        }

        .req-card-body { padding: 16px 20px; }

        .req-card ul {
            list-style: none; padding: 0; margin: 0;
        }

        .req-card ul li {
            font-size: 13px; color: #475569;
            padding: 7px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: flex-start; gap: 8px;
        }

        .req-card ul li:last-child { border-bottom: none; }
        .req-card ul li i { color: #c5a059; font-size: 14px; flex-shrink: 0; margin-top: 1px; }

        /* â”€â”€ APPLY NOW BUTTON â”€â”€ */
        .btn-apply-now {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 10px; min-width: 220px;
            background: var(--ilc-gold); color: var(--ilc-blue);
            padding: 14px 36px; border-radius: 8px;
            font-size: 15px; font-weight: 700;
            text-decoration: none; border: none;
            cursor: pointer; transition: all 0.2s;
            font-family: 'Open Sans', sans-serif;
            box-shadow: 0 4px 12px rgba(245,166,35,0.35);
        }
        .btn-apply-now::after {
            content: '\2192';
            font-size: 18px;
            transition: transform 0.2s;
        }
        .btn-apply-now:hover::after {
            transform: translateX(4px);
        }

        .btn-apply-now:hover {
            background: var(--ilc-blue); color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26,58,108,0.2);
        }

        /* â”€â”€ APPLICATION FORM SECTION â”€â”€ */
        .application-section {
            padding: 50px 0 60px;
            background: var(--ilc-gray-bg);
            border-top: 3px solid var(--ilc-blue);
        }

        /* â”€â”€ STEP PROGRESS BAR â”€â”€ */
        .step-progress {
            display: flex; align-items: center;
            justify-content: center; gap: 0;
            margin-bottom: 8px;
        }

        .step-circle {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            border: 2px solid #ddd; background: #fff; color: #bbb;
            transition: all 0.3s; flex-shrink: 0; z-index: 1;
        }

        .step-circle.active { border-color: var(--ilc-blue); background: var(--ilc-blue); color: #fff; }
        .step-circle.done   { border-color: #27ae60; background: #27ae60; color: #fff; }

        .step-line {
            flex: 1; height: 2px; background: #ddd;
            max-width: 80px; transition: background 0.3s;
        }

        .step-line.done { background: #27ae60; }

        .step-labels {
            display: flex; justify-content: center;
            gap: 0; margin-bottom: 32px;
        }

        .step-labels span {
            font-size: 11px; color: #aaa; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            width: 110px; text-align: center;
            transition: color 0.3s;
        }

        .step-labels span.active { color: var(--ilc-blue); }
        .step-labels span.done   { color: #27ae60; }

        /* â”€â”€ FORM CARD â”€â”€ */
        .form-card {
            background: #fff; border: 1px solid var(--ilc-border);
            border-radius: 8px; padding: 32px 28px;
        }

        .form-section-label {
            font-size: 11px; font-weight: 700;
            color: var(--ilc-blue); text-transform: uppercase;
            letter-spacing: 1.5px; background: #e8f0fb;
            padding: 8px 14px; border-radius: 4px;
            margin-bottom: 16px; margin-top: 8px;
            display: flex; align-items: center; gap: 8px;
        }

        .form-section-label i { color: var(--ilc-gold); }

        .app-label {
            font-size: 12px; font-weight: 700; color: #444;
            text-transform: uppercase; letter-spacing: 0.4px;
            margin-bottom: 5px; display: block;
        }

        .app-input {
            width: 100%; border: 1.5px solid #e0e0e0;
            border-radius: 6px; padding: 10px 14px;
            font-size: 13px; font-family: 'Open Sans', sans-serif;
            background: #fafafa; color: #333;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .app-input:focus {
            border-color: var(--ilc-blue);
            box-shadow: 0 0 0 3px rgba(26,58,108,0.08);
            background: #fff; outline: none;
        }

        select.app-input { cursor: pointer; }
        textarea.app-input { resize: vertical; min-height: 80px; }

        .radio-group {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 14px; border: 1.5px solid #e0e0e0;
            border-radius: 6px; background: #fafafa;
        }

        .radio-group label {
            font-size: 13px; font-weight: 600; color: #555;
            margin: 0; cursor: pointer;
            display: flex; align-items: center; gap: 5px;
        }

        .radio-group input[type="radio"] {
            accent-color: var(--ilc-blue);
            width: 14px; height: 14px; margin: 0;
        }

        /* â”€â”€ FORM STEP BUTTONS â”€â”€ */
        .step-btn-row {
            display: flex; gap: 10px;
            margin-top: 24px; justify-content: flex-end;
        }

        .btn-step-next {
            background: var(--ilc-blue); color: #fff;
            border: none; border-radius: 8px;
            padding: 11px 28px; font-size: 13px;
            font-weight: 700; font-family: 'Open Sans', sans-serif;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; gap: 7px;
        }

        .btn-step-next:hover { background: var(--ilc-light-blue); }

        .btn-step-back {
            background: #fff; color: #666;
            border: 1.5px solid #ddd; border-radius: 8px;
            padding: 11px 20px; font-size: 13px;
            font-weight: 600; font-family: 'Open Sans', sans-serif;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 7px;
        }

        .btn-step-back:hover { border-color: var(--ilc-blue); color: var(--ilc-blue); }

        .btn-step-submit {
            background: #27ae60; color: #fff;
            border: none; border-radius: 8px;
            padding: 11px 28px; font-size: 13px;
            font-weight: 700; font-family: 'Open Sans', sans-serif;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; gap: 7px;
        }

        .btn-step-submit:hover { background: #219a52; }

        /* â”€â”€ SUMMARY BOX (step 4) â”€â”€ */
        .summary-section {
            background: #f8fafc; border: 1px solid var(--ilc-border);
            border-radius: 8px; padding: 20px; margin-bottom: 20px;
        }

        .summary-section h6 {
            font-size: 12px; font-weight: 700; color: var(--ilc-blue);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;
            border-bottom: 1px solid var(--ilc-border); padding-bottom: 6px;
        }

        .sum-row {
            display: flex; justify-content: space-between;
            font-size: 13px; margin-bottom: 6px;
        }

        .sum-row span:first-child { color: #888; }
        .sum-row span:last-child  { font-weight: 600; color: #333; }

        /* â”€â”€ SUCCESS STATE â”€â”€ */
        .success-box {
            text-align: center; padding: 40px 20px;
        }

        .success-box .success-icon {
            width: 80px; height: 80px;
            background: #e8f8f0; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        .success-box .success-icon i { font-size: 36px; color: #27ae60; }

        .success-box h3 { font-size: 20px; font-weight: 700; color: var(--ilc-blue); margin-bottom: 10px; }
        .success-box p  { font-size: 13px; color: #666; line-height: 1.7; margin-bottom: 6px; }
        .success-box .ref-code {
            display: inline-block; background: var(--ilc-blue);
            color: #fff; padding: 8px 20px; border-radius: 6px;
            font-size: 16px; font-weight: 700; letter-spacing: 2px;
            margin: 12px 0 20px;
        }

        /* Apply Notice Card */
        .apply-notice-card {
            background: var(--ilc-blue);
            color: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border: 4px solid var(--ilc-gold);
        }

        .apply-notice-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .apply-notice-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            backdrop-filter: blur(10px);
        }

        .apply-notice-card h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .apply-notice-card p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .apply-notice-steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 12px;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .step-item:hover {
            transform: translateX(10px);
            background: rgba(255,255,255,0.15);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }

        .step-content strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .step-content p {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        .btn-apply-login {
            background: var(--ilc-gold);
            color: var(--ilc-blue);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-apply-login:hover {
            background: var(--ilc-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--ilc-blue);
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--ilc-gold);
        }

        /* Loading Effects */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--ilc-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            margin-top: 20px;
            font-size: 16px;
            color: var(--ilc-blue);
            font-weight: 600;
        }

        /* Form Styles */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-top: 40px;
            border: 4px solid var(--ilc-gold);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h3 {
            color: var(--ilc-blue);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .step-progress {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
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
            width: 60px;
            height: 2px;
            background: #ddd;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .step-line.done {
            background: var(--ilc-gold);
        }

        .step-labels {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step-label {
            text-align: center;
            width: 80px;
            font-size: 12px;
            color: #666;
            transition: all 0.3s ease;
        }

        .step-label.active {
            color: var(--ilc-blue);
            font-weight: 600;
        }

        .step-label.done {
            color: var(--ilc-gold);
            font-weight: 600;
        }

        .form-section-label {
            background: var(--ilc-blue);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .app-label {
            display: block;
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
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        }

        .step-btn-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-step-next, .btn-step-submit {
            background: var(--ilc-blue);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-step-next:hover, .btn-step-submit:hover {
            background: var(--ilc-gold);
            color: var(--ilc-blue);
            transform: translateY(-2px);
        }

        .btn-step-back {
            background: #f5f5f5;
            color: #666;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-step-back:hover {
            background: #e0e0e0;
        }

        .summary-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-section h6 {
            color: var(--ilc-blue);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .sum-row:last-child {
            border-bottom: none;
        }

        .sum-row span:first-child {
            font-weight: 500;
            color: #666;
        }

        .sum-row span:last-child {
            font-weight: 600;
            color: #333;
        }
    </style>
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, div, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780320000">
</head>
<body class="page-loading">


<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                
                <div class="school-logo"><img src="/images/logo.png" alt="Logo 2"></div>
                <div class="school-title">
                    <h1>IEMELIF Learning Center</h1>
                    
                    <p>General Tinio, Nueva Ecija</p>
                </div>
            </div>
            <div class="d-none d-lg-flex flex-column align-items-end gap-1">
                <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:#111;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:4px;"><i class="bi bi-calendar3" style="color:var(--ilc-gold);font-size:11px;"></i><span id="topbar-date"></span></div>
                    <div style="width:1px;height:11px;background:#ccc;"></div>
                    <div style="display:flex;align-items:center;gap:4px;"><i class="bi bi-clock" style="color:var(--ilc-gold);font-size:11px;"></i><span id="topbar-time" style="font-variant-numeric:tabular-nums;min-width:70px;"></span></div>
                </div>
                <form class="search-form" action="<?php echo e(route('search')); ?>" method="GET" style="display:flex;flex-direction:row;align-items:center;">
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="<?php echo e(request('q')); ?>" style="flex:1;">
                    <button class="btn-search" type="submit" style="flex-shrink:0;"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>


<nav class="main-nav navbar navbar-expand-lg">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('about')); ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('academics')); ?>">Academics</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('admission')); ?>">Enrollment</a></li><li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('announcements*') ? 'active' : ''); ?>" href="<?php echo e(route('announcements')); ?>">Announcements</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('news')); ?>">News</a></li>
                <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('aims') ? 'active' : ''); ?>" href="<?php echo e(route('aims')); ?>">AIMS</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('contact')); ?>">Contact</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-light btn-sm">Log In</a>
            </div>
        </div>
    </div>
</nav>


<div class="skel-section" id="skel-content">
    <div style="background:#1a3a6c;padding:30px 0;text-align:center;border-bottom:4px solid #f5a623;">
        <span class="skel-dark" style="height:24px;width:160px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:110px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:40px 0;background:#f8fafc;">
        <div class="container">
            <span class="skel" style="height:26px;width:200px;display:block;border-radius:4px;margin:0 auto 10px;"></span>
            <span class="skel" style="height:12px;width:300px;display:block;border-radius:4px;margin:0 auto 24px;"></span>
            <div class="row g-3">
                <?php for($i=0;$i<3;$i++): ?>
                <div class="col-md-4">
                    <div style="background:#fff;border-radius:12px;padding:22px;border:1px solid #eee;">
                        <span class="skel" style="width:44px;height:44px;border-radius:10px;display:block;margin-bottom:13px;"></span>
                        <span class="skel" style="height:17px;width:130px;display:block;border-radius:4px;margin-bottom:11px;"></span>
                        <?php for($j=0;$j<4;$j++): ?><span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span><?php endfor; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div style="padding:40px 0;background:#fff;">
        <div class="container">
            <div style="background:#1a3a6c;border-radius:16px;padding:36px;text-align:center;">
                <span class="skel-dark" style="width:70px;height:70px;border-radius:50%;display:block;margin:0 auto 16px;"></span>
                <span class="skel-dark" style="height:26px;width:220px;display:block;border-radius:4px;margin:0 auto 10px;"></span>
                <span class="skel-dark" style="height:12px;width:300px;display:block;border-radius:4px;margin:0 auto 20px;"></span>
                <span class="skel-dark" style="height:44px;width:200px;display:block;border-radius:8px;margin:0 auto;"></span>
            </div>
        </div>
    </div>
</div>

<div class="real-section" id="real-content">


<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Enrollment</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">Enrollment</span>
    </p>
</div>


<section class="req-section">
    <div class="container">

        <h2 class="req-section-title">Enrollment Requirements</h2>
        <p style="font-size:13px; color:#888; margin-bottom:28px;">
            Please prepare the following documents before submitting your elementary enrollment application.
        </p>

        <div class="row g-3 mb-4">

            
            <div class="col-md-4">
                <div class="req-card">
                    <div class="req-card-header">
                        <div class="req-icon-box"><i class="bi bi-person-plus-fill"></i></div>
                        <h5>New Students</h5>
                    </div>
                    <div class="req-card-body">
                        <ul>
                            <li><i class="bi bi-check2-circle"></i> PSA Xerox Copy</li>
                            <li><i class="bi bi-check2-circle"></i> Form 137</li>
                            <li><i class="bi bi-check2-circle"></i> Grades</li>
                            <li><i class="bi bi-check2-circle"></i> 2x2 ID Photos (2 pieces)</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="req-card">
                    <div class="req-card-header">
                        <div class="req-icon-box"><i class="bi bi-arrow-left-right"></i></div>
                        <h5>Transferees</h5>
                    </div>
                    <div class="req-card-body">
                        <ul>
                            <li><i class="bi bi-check2-circle"></i> PSA Xerox Copy</li>
                            <li><i class="bi bi-check2-circle"></i> Form 137</li>
                            <li><i class="bi bi-check2-circle"></i> Grades</li>
                            <li><i class="bi bi-check2-circle"></i> 2x2 ID Photos (2 pieces)</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="req-card">
                    <div class="req-card-header">
                        <div class="req-icon-box"><i class="bi bi-list-check"></i></div>
                        <h5>Enrollment Process</h5>
                    </div>
                    <div class="req-card-body">
                        <ul>
                            <li><i class="bi bi-1-circle-fill"></i> Fill out the online application form</li>
                            <li><i class="bi bi-2-circle-fill"></i> Wait for admin approval via email</li>
                            <li><i class="bi bi-3-circle-fill"></i> Visit school to submit original documents</li>
                            <li><i class="bi bi-4-circle-fill"></i> Receive login credentials for student portal</li>
                            <li><i class="bi bi-5-circle-fill"></i> Pay enrollment fees at the online or cashier</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        
        <div style="background:#e8f0fb; border:1px solid #b8d0f0; border-radius:8px; padding:16px 20px; display:flex; align-items:flex-start; gap:14px; margin-bottom:32px;">
            <i class="bi bi-calendar-event-fill" style="color:var(--ilc-blue); font-size:20px; flex-shrink:0; margin-top:2px;"></i>
            <div>
                <div style="font-size:13px; font-weight:700; color:var(--ilc-blue); margin-bottom:4px;">
                    Enrollment Period: June 1 &ndash; June 5, 2026
                </div>
                <div style="font-size:12px; color:#555; line-height:1.6;">
                    
                    Office hours: Monday to Friday, 7:30 AM &ndash; 5:00 PM &nbsp;|&nbsp;
                    Saturday-Sunday: Closed 
                </div>
            </div>
        </div>

        
        <?php if(session('enrollment_closed') || (isset($enrollmentOpen) && !$enrollmentOpen)): ?>
        <div class="text-center mt-5 mb-4">
            <div style="display:inline-block;background:#fff3e0;border:1.5px solid #f5a623;border-radius:14px;padding:22px 36px;max-width:480px;">
                <div style="font-size:32px;margin-bottom:10px;"><i class="bi bi-lock-fill" style="font-size:32px;color:#1a3a6c;margin-bottom:10px;display:block;font-style:normal;"></i></div>
                <div style="font-weight:700;font-size:17px;color:#b45309;margin-bottom:6px;">Enrollment is Currently Closed</div>
                <div style="font-size:13px;color:#78350f;line-height:1.6;">
                    Online enrollment is not yet open for this school year.<br>
                    Please check back later or visit the school office for walk-in enrollment.
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center mt-5 mb-4">
            <a href="<?php echo e(route('enrollment.form')); ?>" class="btn-apply-now">
                <span>Enroll Now</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>


<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="loading-spinner"></div>
        <div class="loading-text">Loading...</div>
    </div>
</div>
</section>




<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scrolling for all internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Show success message if exists
        <?php if(session('success')): ?>
            setTimeout(() => {
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successDiv.style.zIndex = '9999';
                successDiv.innerHTML = `
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    successDiv.remove();
                }, 5000);
            }, 500);
        <?php endif; ?>
    });
</script>

<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
<script>(function(){var S=[['real-content','skel-content']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();</script>

</body>
</html><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\admission.blade.php ENDPATH**/ ?>