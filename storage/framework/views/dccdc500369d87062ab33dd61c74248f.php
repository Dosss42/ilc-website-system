<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academics &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
 

    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <link rel="stylesheet" href="<?php echo e(asset('css/academics.css')); ?>?v=1780250000">  
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
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780250000">
</head>
<body class="page-loading">


<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                
                <div class="school-logo">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">
                    <img src="/images/logo.png" alt="Logo 2"></a>
                </div>
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
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('about')); ?>">About</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('academics')); ?>">Academics</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admission')); ?>">Enrollment</a></li>
                <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('announcements*') ? 'active' : ''); ?>" href="<?php echo e(route('announcements')); ?>">Announcements</a>
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
        <span class="skel-dark" style="height:24px;width:150px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:110px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:40px 0 10px;background:#fff;">
        <div class="container">
            <?php for($i=0;$i<4;$i++): ?>
            <div style="margin-bottom:38px;">
                <span class="skel" style="height:26px;width:220px;display:block;border-radius:4px;margin-bottom:10px;"></span>
                <span class="skel" style="height:13px;width:170px;display:block;border-radius:4px;margin-bottom:18px;"></span>
                <div class="row g-3">
                    <?php for($j=0;$j<3;$j++): ?>
                    <div class="col-md-4">
                        <div style="background:#f8fafc;border-radius:10px;padding:18px;border:1px solid #eee;">
                            <span class="skel" style="height:14px;width:90px;display:block;border-radius:4px;margin-bottom:8px;"></span>
                            <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                            <span class="skel" style="height:11px;width:80%;display:block;border-radius:4px;"></span>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
    <div style="background:#1a3a6c;padding:38px 0 20px;">
        <div class="container"><div class="row g-4">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="col-md-3">
                <span class="skel-dark" style="height:14px;width:100px;display:block;border-radius:4px;margin-bottom:14px;"></span>
                <span class="skel-dark" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:7px;"></span>
                <span class="skel-dark" style="height:11px;width:88%;display:block;border-radius:4px;margin-bottom:7px;"></span>
                <span class="skel-dark" style="height:11px;width:76%;display:block;border-radius:4px;"></span>
            </div>
            <?php endfor; ?>
        </div></div>
    </div>
</div>

<div class="real-section" id="real-content">

<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Academics</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">Academics</span>
    </p>
</div>


<nav id="acad-quicknav">
    <div class="qnav-label">Quick Nav</div>

    <a href="#sec-overview" class="qnav-link" data-sec="sec-overview">
        <i class="bi bi-book-fill"></i>
        <span>Overview</span>
    </a>

    <a href="#sec-grades" class="qnav-link" data-sec="sec-grades">
        <i class="bi bi-mortarboard-fill"></i>
        <span>Grade Levels</span>
    </a>

    
    <a href="#sec-calendar" class="qnav-link" data-sec="sec-calendar" id="qnav-cal-btn" onclick="toggleCalSub(event)">
        <i class="bi bi-calendar3"></i>
        <span>Academic Calendar</span>
        <i class="bi bi-chevron-down qnav-chevron" id="qnav-chevron"></i>
    </a>
    <div id="qnav-cal-sub">
        <a href="#sec-term1" class="qnav-sub-link">
            <i class="bi bi-circle-fill" style="font-size:6px;color:#1d4ed8;"></i> Term 1
        </a>
        <a href="#sec-term2" class="qnav-sub-link">
            <i class="bi bi-circle-fill" style="font-size:6px;color:#2563eb;"></i> Term 2
        </a>
        <a href="#sec-term3" class="qnav-sub-link">
            <i class="bi bi-circle-fill" style="font-size:6px;color:#0ea5e9;"></i> Term 3
        </a>
    </div>

    <a href="#sec-grading" class="qnav-link" data-sec="sec-grading">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Grading System</span>
    </a>
</nav>

<style>
#acad-quicknav {
    position: fixed;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 998;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14), 0 0 0 1px rgba(0,0,0,0.06);
    padding: 10px 0 12px;
    width: 172px;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

/* Hide on small screens */
@media (max-width: 1100px) { #acad-quicknav { display: none; } }

.qnav-label {
    font-size: 9.5px;
    font-weight: 800;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 2px 16px 8px;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 4px;
}

.qnav-link {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 16px;
    font-size: 12px;
    font-weight: 600;
    color: #4b5563;
    text-decoration: none;
    border-left: 3px solid transparent;
    border-radius: 0 8px 8px 0;
    transition: color 0.2s, border-color 0.2s, background 0.2s;
    font-family: 'Open Sans', sans-serif;
    cursor: pointer;
    position: relative;
}
.qnav-link i:first-child { font-size: 13px; flex-shrink: 0; }
.qnav-link span { flex: 1; line-height: 1.3; }
.qnav-chevron {
    font-size: 10px;
    transition: transform 0.25s;
    flex-shrink: 0;
}
.qnav-link:hover { color: #1a3a6c; background: #f0f4f8; }
.qnav-link.active {
    color: #1a3a6c;
    background: #eef2fb;
    border-left-color: #1a3a6c;
    font-weight: 700;
}

/* Calendar sub-links */
#qnav-cal-sub {
    display: none;
    flex-direction: column;
    padding: 0 0 4px 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
#qnav-cal-sub.open { display: flex; }
.qnav-sub-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px 8px 38px;
    font-size: 11.5px;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: color 0.15s, background 0.15s;
    font-family: 'Open Sans', sans-serif;
}
.qnav-sub-link:hover { color: #1a3a6c; background: #f0f4f8; }
</style>


<section class="acad-overview-section" id="sec-overview">
    <div class="container">
        <div class="row align-items-center g-5">

            
            <div class="col-lg-7">
                <h2 class="acad-section-title">Academic Overview</h2>
                <p class="acad-section-subtitle">Quality Christian education from Nursery, kinder, Grade 1 to Grade 6</p>

                <div class="overview-text">
                    
                    <p>
                        The IEMELIF Learning Center offers a comprehensive Junior High School
                        curriculum aligned with the Department of Education's K&ndash;6 Basic Education
                        Program. Our academic program is designed to develop well-rounded learners
                        who are academically competent, spiritually grounded, and socially responsible.
                    </p>
                    <p>
                        We offer Grade 1 through Grade 6, covering all core subjects required by
                        DepEd while integrating Christian values and character formation in every
                        aspect of school life. Our dedicated faculty ensures that each learner
                        receives personalized attention and quality instruction.
                    </p>
                    <p>
                        Beyond academics, students are encouraged to participate in co-curricular
                        activities, spiritual formation programs, and community service initiatives
                        that help shape them into responsible and God-fearing citizens.
                    </p>
                </div>

                
                
                <div class="acad-stat-badges">
                    <div class="acad-stat-badge">
                        <i class="bi bi-mortarboard-fill"></i> Nursery, Kinder to Grade 1 &ndash; 6
                    </div>
                    <div class="acad-stat-badge">
                        <i class="bi bi-book-fill"></i> K-6 Aligned
                    </div>
                    <div class="acad-stat-badge">
                        <i class="bi bi-people-fill"></i> Small Class Size
                    </div>
                    <div class="acad-stat-badge">
                        <i class="bi bi-award-fill"></i> DepEd Accredited
                    </div>
                </div>
            </div>

            
            <div class="col-lg-5">
                <div class="overview-img-box">
                    
                    <div class="img-placeholder">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="grade-levels-section" id="sec-grades">
    <div class="container">

        <h2 class="acad-section-title center">Grade Levels Offered</h2>
        <p class="acad-section-subtitle center">Each grade level follows the DepEd K-6 curriculum with Christian values integration.</p>

        <div class="row g-3">

            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">Nursery</span>
                        <span class="grade-label">Nursery</span>
                    </div>
                    <div class="grade-card-body">
                        
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> Literacy, Language, and Communication</li>
                            <li><i class="bi bi-check-circle-fill"></i> Socio-Emotional Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Values Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Physical Health and Motor Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Aethetic/Creative Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Cognitive Development</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">Kinder</span>
                        <span class="grade-label">Kinder</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> Literacy, Language, and Communication</li>
                            <li><i class="bi bi-check-circle-fill"></i> Socio-Emotional Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Values Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Physical Health and Motor Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Aethetic/Creative Development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Cognitive Development</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">1</span>
                        <span class="grade-label">Grade 1</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> GMRC</li>
                            <li><i class="bi bi-check-circle-fill"></i> Language</li>
                            <li><i class="bi bi-check-circle-fill"></i> Reading and Literacy</li>
                            <li><i class="bi bi-check-circle-fill"></i> Makabansa</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">2</span>
                        <span class="grade-label">Grade 2</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> English</li>
                            <li><i class="bi bi-check-circle-fill"></i> Filipino</li>
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> Makabansa</li>
                            <li><i class="bi bi-check-circle-fill"></i> GMRC</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">3</span>
                        <span class="grade-label">Grade 3</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> English</li>
                            <li><i class="bi bi-check-circle-fill"></i> Filipino</li>
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> Science</li>
                            <li><i class="bi bi-check-circle-fill"></i> Makabansa</li>
                            <li><i class="bi bi-check-circle-fill"></i> GMRC</li>
                        </ul>
                    </div>
                </div>
            </div>


            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">4</span>
                        <span class="grade-label">Grade 4</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> English</li>
                            <li><i class="bi bi-check-circle-fill"></i> Filipino</li>
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> Science</li>
                            <li><i class="bi bi-check-circle-fill"></i> EPP</li>
                            <li><i class="bi bi-check-circle-fill"></i> AP</li>
                            <li><i class="bi bi-check-circle-fill"></i> Mapeh</li>
                            <li><i class="bi bi-check-circle-fill"></i> GMRC</li>
                        </ul>
                    </div>
                </div>
            </div>


            
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">5</span>
                        <span class="grade-label">Grade 5</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> English</li>
                            <li><i class="bi bi-check-circle-fill"></i> Filipino</li>
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> Science</li>
                            <li><i class="bi bi-check-circle-fill"></i> EPP</li>
                            <li><i class="bi bi-check-circle-fill"></i> AP</li>
                            <li><i class="bi bi-check-circle-fill"></i> Mapeh</li>
                            <li><i class="bi bi-check-circle-fill"></i> GMRC</li>
                        </ul>
                    </div>
                </div>
            </div>


           
            <div class="col-md-6 col-lg-3">
                <div class="grade-card">
                    <div class="grade-card-header">
                        <span class="grade-num">6</span>
                        <span class="grade-label">Grade 6</span>
                    </div>
                    <div class="grade-card-body">
                        <ul class="subject-list">
                            <li><i class="bi bi-check-circle-fill"></i> English</li>
                            <li><i class="bi bi-check-circle-fill"></i> Filipino</li>
                            <li><i class="bi bi-check-circle-fill"></i> Math</li>
                            <li><i class="bi bi-check-circle-fill"></i> Science</li>
                            <li><i class="bi bi-check-circle-fill"></i> AP</li>
                            <li><i class="bi bi-check-circle-fill"></i> ESP</li>
                            <li><i class="bi bi-check-circle-fill"></i> TLE</li>
                            <li><i class="bi bi-check-circle-fill"></i> Mapeh</li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>


  


<section class="calendar-section" id="sec-calendar">
    <div class="container">

        <h2 class="acad-section-title">Academic Calendar S.Y. 2026&ndash;2027</h2>
        <p class="acad-section-subtitle">Important dates and events for the school year.</p>

        
        <div style="overflow-x:auto;">
            
        </div>

        
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:24px;">
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;"><span style="width:12px;height:12px;border-radius:3px;background:#dc2626;display:inline-block;"></span>Holiday</span>
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;"><span style="width:12px;height:12px;border-radius:3px;background:#1d4ed8;display:inline-block;"></span>Examination</span>
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;"><span style="width:12px;height:12px;border-radius:3px;background:#0369a1;display:inline-block;"></span>School Activity</span>
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;"><span style="width:12px;height:12px;border-radius:3px;background:#1e40af;display:inline-block;"></span>Break / EOSY</span>
        </div>

        <?php
        $typeMap = [
            'holiday'  => ['#fee2e2','#dc2626','Holiday'],
            'exam'     => ['#bfdbfe','#1e3a8a','Exam'],
            'activity' => ['#e0f2fe','#0369a1','Activity'],
            'break'    => ['#dbeafe','#1e40af','Break'],
        ];
        ?>

        
        <div style="border:1px solid #93c5fd;border-radius:12px;margin-bottom:28px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#0c4a6e,#0369a1);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <span style="font-size:13px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:1px;">End-of-School Year (EOSY) 2025&ndash;2026</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead><tr style="background:#e0f2fe;">
                        <th style="padding:10px 14px;text-align:left;width:120px;font-weight:700;color:#0c4a6e;border-bottom:2px solid #7dd3fc;">Date</th>
                        <th style="padding:10px 14px;text-align:left;font-weight:700;color:#0c4a6e;border-bottom:2px solid #7dd3fc;">Activity / Event</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#0c4a6e;border-bottom:2px solid #7dd3fc;">Type</th>
                    </tr></thead>
                    <tbody>
                    <?php $eoRows = [
                        ['Apr 1',    "Start of 30-day Teachers' EOSY Break",'break'],
                        ['Apr 2',    'Maundy Thursday (Regular Holiday)','holiday'],
                        ['Apr 3',    'Good Friday (Regular Holiday)','holiday'],
                        ['Apr 4',    'Black Saturday (Additional Special Non-Working Holiday)','holiday'],
                        ['Apr 9',    'The Day of Valor (Regular Holiday)','holiday'],
                        ['Apr 13&ndash;17','2026 National Schools Press Conference (NSPC)','activity'],
                        ['Apr 18&ndash;22','2026 National Festival of Talents (NFOT)','activity'],
                        ['Apr 25&ndash;30','2026 Palarong Pambansa','activity'],
                        ['May 1',    "Labor Day / End of 30-day Teachers' EOSY Break",'holiday'],
                        ['May 4&ndash;22', 'EOSY Intervention Program','activity'],
                        ['May 4&ndash;29', 'Training Window for Teachers, School Heads & Teaching-Related Personnel','activity'],
                        ['May 4&ndash;29', 'Oplan Balik Eskwela','activity'],
                    ]; ?>
                    <?php $__currentLoopData = $eoRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php [$bg,$tc,$label] = $typeMap[$row[2]]; ?>
                    <tr style="background:<?php echo e($i%2==0?'#fff':'#f0f9ff'); ?>;border-bottom:1px solid #e0f2fe;">
                        <td style="padding:9px 14px;font-weight:600;color:#374151;white-space:nowrap;"><?php echo e($row[0]); ?></td>
                        <td style="padding:9px 14px;color:#374151;"><?php echo e($row[1]); ?></td>
                        <td style="padding:9px 14px;"><span style="background:<?php echo e($bg); ?>;color:<?php echo e($tc); ?>;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;"><?php echo e($label); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="sec-term1" style="border:1px solid #bfdbfe;border-radius:12px;margin-bottom:28px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <span style="font-size:13px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:1px;">Term 1</span>
                <span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">June 8 &ndash; September 15, 2026</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead><tr style="background:#dbeafe;">
                        <th style="padding:10px 14px;text-align:left;width:90px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #bfdbfe;">Month</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #bfdbfe;">Date</th>
                        <th style="padding:10px 14px;text-align:left;font-weight:700;color:#1e3a8a;border-bottom:2px solid #bfdbfe;">Activity / Event</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #bfdbfe;">Type</th>
                    </tr></thead>
                    <tbody>
                    <?php $t1Rows = [
                        ['June','1&ndash;5',   'Brigada Eskwela / Enrollment Period','activity'],
                        ['',   '8&ndash;11',   "Opening Block: Start of Term 1 &mdash; Learners' Health Assessment, Orientation, Testing Window for BOSY",'activity'],
                        ['',   '12',     'Independence Day (Regular Holiday)','holiday'],
                        ['',   '22&ndash;26',  'Phil ECD Checklist for BOSY','activity'],
                        ['',   '23',     "DepEd Founding Anniversary: Flag Raising & Opening of Activity (Nationwide)",'activity'],
                        ['',   '24',     'DepEd Founding Anniversary: Anniversary Proper','activity'],
                        ['July','3',     "End of Mandatory Learners' Health Assessment",'activity'],
                        ['',   '6',      'Term 1: First Teacher-made Summative Test','exam'],
                        ['',   '10',     'End of Testing Window for BOSY Assessments (CRLA, RMA, Phil-IRI, etc.)','activity'],
                        ['',   '13&ndash;17',  'MFAT','activity'],
                        ['',   '20&ndash;24',  'National Federation SELG and SSLG Election','activity'],
                        ['',   '28',     'Term 1: Second Teacher-made Summative Test','exam'],
                        ['Aug','21',     'Ninoy Aquino Day (Non-Working Holiday)','holiday'],
                        ['',   '28',     'Term 1 Examination','exam'],
                        ['',   '31',     'National Heroes Day (Regular Holiday)','holiday'],
                        ['Sept','1',     'Term 1 Examination (continuation)','exam'],
                        ['',   '5',      "Start of National Teachers' Month",'activity'],
                        ['',   '2&ndash;8',    'ARAL Program, Computation of Grades, Accomplishment of School Forms & Co/Extra-Curricular Activities','activity'],
                        ['',   '9',      'PTA Meeting & Distribution of Report Cards','activity'],
                        ['',   '10&ndash;11',  'INSET','activity'],
                        ['',   '10&ndash;15',  'Wellness Break of Learners (Guided asynchronous learning)','break'],
                        ['',   '14&ndash;15',  'Wellness Break of Teachers','break'],
                        ['',   '15',     'End of Term 1','activity'],
                    ]; ?>
                    <?php $__currentLoopData = $t1Rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php [$bg,$tc,$label] = $typeMap[$row[3]]; ?>
                    <tr style="background:<?php echo e($i%2==0?'#fff':'#f8faff'); ?>;border-bottom:1px solid #f0f4f8;">
                        <td style="padding:9px 14px;font-weight:700;color:#1e3a8a;white-space:nowrap;"><?php echo e($row[0]); ?></td>
                        <td style="padding:9px 14px;font-weight:600;color:#374151;white-space:nowrap;"><?php echo e($row[1]); ?></td>
                        <td style="padding:9px 14px;color:#374151;"><?php echo e($row[2]); ?></td>
                        <td style="padding:9px 14px;"><span style="background:<?php echo e($bg); ?>;color:<?php echo e($tc); ?>;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;"><?php echo e($label); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="sec-term2" style="border:1px solid #7dd3fc;border-radius:12px;margin-bottom:28px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <span style="font-size:13px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:1px;">Term 2</span>
                <span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">September 16 &ndash; December 18, 2026</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead><tr style="background:#dbeafe;">
                        <th style="padding:10px 14px;text-align:left;width:90px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #93c5fd;">Month</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #93c5fd;">Date</th>
                        <th style="padding:10px 14px;text-align:left;font-weight:700;color:#1e3a8a;border-bottom:2px solid #93c5fd;">Activity / Event</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#1e3a8a;border-bottom:2px solid #93c5fd;">Type</th>
                    </tr></thead>
                    <tbody>
                    <?php $t2Rows = [
                        ['Sept','16',    'Start of Term 2 / Start of Testing Window for NCAE (Grade 10 only)','activity'],
                        ['',   '21&ndash;25',  'Start of Testing Window for MOSY Assessments (CRLA, RMA, Phil-IRI)','activity'],
                        ['Oct','5',      "Culmination of National Teachers' Month / World Teachers' Day",'activity'],
                        ['',   '5&ndash;9',    'NAT for Grade 10','exam'],
                        ['',   '7',      'Term 2: First Teacher-made Summative Test','exam'],
                        ['',   '19&ndash;23',  'End of Testing Window for MOSY Assessments','activity'],
                        ['',   '29',     'Term 2: Second Teacher-made Summative Test','exam'],
                        ['Nov','1',      "All Saints' Day (Special Non-Working Holiday)",'holiday'],
                        ['',   '2',      "All Souls' Day (Additional Special Non-Working Holiday)",'holiday'],
                        ['',   '15',     'PEPT (Luzon & VisMin Clusters)','exam'],
                        ['',   '27',     'Araw ng Pagbasa','activity'],
                        ['',   '30',     'Bonifacio Day (Regular Holiday)','holiday'],
                        ['Dec','3&ndash;4',    'Term 2 Examination','exam'],
                        ['',   '4',      'End of Testing Window for NCAE (Grade 10 only)','activity'],
                        ['',   '7&ndash;18',   'End-of-Term Block','activity'],
                        ['',   '7&ndash;14',   'ARAL Program, Computation of Grades, Accomplishment of School Forms & Co/Extra-Curricular Activities','activity'],
                        ['',   '8',      'Feast of the Immaculate Conception of Mary (Special Non-Working Holiday)','holiday'],
                        ['',   '15',     'PTA Meeting & Distribution of Progress/Performance Report','activity'],
                        ['',   '16',     'Year-End Activity','activity'],
                        ['',   '17&ndash;18',  'INSET / Wellness Break of Learners & Teachers','break'],
                        ['',   '18',     'End of Term 2','activity'],
                        ['',   '19&ndash;31',  'Year-End Break (Wellness Break of Learners & Teachers)','break'],
                        ['',   '24',     'Christmas Eve (Special Non-Working Holiday)','holiday'],
                        ['',   '25',     'Christmas Day (Regular Holiday)','holiday'],
                        ['',   '30',     'Rizal Day','holiday'],
                        ['',   '31',     'Last Day of the Year (Special Non-Working Holiday)','holiday'],
                    ]; ?>
                    <?php $__currentLoopData = $t2Rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php [$bg,$tc,$label] = $typeMap[$row[3]]; ?>
                    <tr style="background:<?php echo e($i%2==0?'#fff':'#f0f7ff'); ?>;border-bottom:1px solid #e8f0fe;">
                        <td style="padding:9px 14px;font-weight:700;color:#1e3a8a;white-space:nowrap;"><?php echo e($row[0]); ?></td>
                        <td style="padding:9px 14px;font-weight:600;color:#374151;white-space:nowrap;"><?php echo e($row[1]); ?></td>
                        <td style="padding:9px 14px;color:#374151;"><?php echo e($row[2]); ?></td>
                        <td style="padding:9px 14px;"><span style="background:<?php echo e($bg); ?>;color:<?php echo e($tc); ?>;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;"><?php echo e($label); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="sec-term3" style="border:1px solid #93c5fd;border-radius:12px;margin-bottom:28px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#0369a1,#0ea5e9);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <span style="font-size:13px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:1px;">Term 3</span>
                <span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">January 4 &ndash; April 8, 2027</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead><tr style="background:#e0f2fe;">
                        <th style="padding:10px 14px;text-align:left;width:90px;font-weight:700;color:#0369a1;border-bottom:2px solid #7dd3fc;">Month</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#0369a1;border-bottom:2px solid #7dd3fc;">Date</th>
                        <th style="padding:10px 14px;text-align:left;font-weight:700;color:#0369a1;border-bottom:2px solid #7dd3fc;">Activity / Event</th>
                        <th style="padding:10px 14px;text-align:left;width:100px;font-weight:700;color:#0369a1;border-bottom:2px solid #7dd3fc;">Type</th>
                    </tr></thead>
                    <tbody>
                    <?php $t3Rows = [
                        ['Jan','1',       "New Year's Day (Regular Holiday)",'holiday'],
                        ['',  '4',        'Start of Term 3','activity'],
                        ['',  '25',       'Term 3: First Teacher-made Summative Test','exam'],
                        ['',  '30',       'Start of Early Registration for Incoming Kinder, Grades 1, 7, 11, OSCYA & Transferees','activity'],
                        ['Feb','1&ndash;5',     'Start of Testing Window for EOSY Assessments (CRLA, RMA, Phil-IRI)','activity'],
                        ['',  '6',        'Chinese New Year (Additional Special Non-Working Holiday)','holiday'],
                        ['',  '15&ndash;19',    'NAT for Grade 12','exam'],
                        ['',  '16',       'Term 3: Second Teacher-made Summative Test','exam'],
                        ['',  '26',       'End of Early Registration for Incoming Kinder, Grades 1, 7, 11, OSCYA & Transferees','activity'],
                        ['Mar','1&ndash;5',     'ELLNA for Grade 3','exam'],
                        ['',  '8&ndash;12',     'NAT for Grade 6','exam'],
                        ['',  '8&ndash;12',     'End of Testing Window for EOSY Assessments (CRLA, RMA, Phil-IRI)','activity'],
                        ['',  '15&ndash;16',    'Phil ECD Checklist for EOSY / Moving up / Graduating Learners','activity'],
                        ['',  '17&ndash;23',    'Computation of Grades, Accomplishment of School Forms & Academic Deliberation (Moving Up/Graduating Learners)','activity'],
                        ['',  '22&ndash;23',    'Term 3 Examination (Moving up/Graduating Learners)','exam'],
                        ['',  '24, 29&ndash;31','End-of-Term Block / Computation of Grades (Other Grade Levels)','activity'],
                        ['',  '24',       'Announcement of Academic Excellence Awardees (Moving up/Graduating Learners)','activity'],
                        ['',  '25',       'Maundy Thursday (Regular Holiday)','holiday'],
                        ['',  '26',       'Good Friday (Regular Holiday)','holiday'],
                        ['',  '27',       'Black Saturday (Additional Special Non-Working Holiday)','holiday'],
                        ['Apr','1&ndash;8',     'End-of-Term Block &mdash; Accomplishment of School Forms & Co/Extra-Curricular Activities','activity'],
                        ['',  '2 & 5',    'INSET','activity'],
                        ['',  '6&ndash;7',      'EOSY Rites','activity'],
                        ['',  '8',        'PTA Meeting & Distribution of Report Cards / End of Term 3','activity'],
                        ['',  '9',        "The Day of Valor / Start of 30-day Teachers' EOSY Break",'holiday'],
                        ['',  '19&ndash;23',    '2027 NSPC','activity'],
                        ['',  '26&ndash;30',    '2027 NFOT','activity'],
                        ['May','1',       'Labor Day (Regular Holiday)','holiday'],
                        ['',  '3&ndash;7',      '2027 Palarong Pambansa','activity'],
                        ['',  '9',        "End of 30-day Teachers' EOSY Break",'break'],
                        ['',  '10&ndash;14',    'Start of EOSY Intervention Program & Training Window for Teachers','activity'],
                        ['',  '17',       'Start of Oplan Balik Eskwela','activity'],
                        ['Jun','1&ndash;4',     'End of Training Window for Teachers & Oplan Balik Eskwela','activity'],
                        ['',  '7&ndash;11',     'Brigada Eskwela / Enrollment Period','activity'],
                    ]; ?>
                    <?php $__currentLoopData = $t3Rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php [$bg,$tc,$label] = $typeMap[$row[3]]; ?>
                    <tr style="background:<?php echo e($i%2==0?'#fff':'#f0f9ff'); ?>;border-bottom:1px solid #e0f2fe;">
                        <td style="padding:9px 14px;font-weight:700;color:#0369a1;white-space:nowrap;"><?php echo e($row[0]); ?></td>
                        <td style="padding:9px 14px;font-weight:600;color:#374151;white-space:nowrap;"><?php echo e($row[1]); ?></td>
                        <td style="padding:9px 14px;color:#374151;"><?php echo e($row[2]); ?></td>
                        <td style="padding:9px 14px;"><span style="background:<?php echo e($bg); ?>;color:<?php echo e($tc); ?>;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;"><?php echo e($label); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>


<section class="grading-section" id="sec-grading">
    <div class="container">

        <h2 class="acad-section-title">Grading System</h2>
        <p class="acad-section-subtitle">MATATAG Curriculum &mdash; DepEd K&ndash;6 Assessment Guidelines, S.Y. 2026&ndash;2027</p>

        
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 18px;margin-bottom:28px;display:flex;align-items:flex-start;gap:12px;">
            <i class="bi bi-info-circle-fill" style="color:#1d4ed8;font-size:18px;flex-shrink:0;margin-top:1px;"></i>
            <div style="font-size:13px;color:#1e3a8a;line-height:1.6;">
                Under the <strong>MATATAG Curriculum</strong>, assessment is aligned with the <strong>Three-Term school year</strong>.
                Grading is now computed <strong>per term</strong> instead of quarterly.
                Kindergarten uses a <strong>descriptive rating system</strong> &mdash; no numerical grades.
                Grades 1&ndash;6 continue to use the numerical grading scale with the same grade composition weights.
            </div>
        </div>

        
        <div style="margin-bottom:28px;">
            <h5 style="font-size:15px;font-weight:700;color:#1e3a8a;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-stars" style="color:#f59e0b;"></i> Nursery/Kindergarten &mdash; Descriptive Rating System
            </h5>
            <div style="overflow-x:auto;">
                <table class="grading-table">
                    <thead>
                        <tr>
                            <th>Rating Symbol</th>
                            <th>Descriptor</th>
                            <th>Meaning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span style="font-size:16px;font-weight:800;color:#16a34a;">A</span></td>
                            <td><span class="grade-pill outstanding">Advanced</span></td>
                            <td>Child consistently exceeds developmental expectations</td>
                        </tr>
                        <tr>
                            <td><span style="font-size:16px;font-weight:800;color:#2563eb;">P</span></td>
                            <td><span class="grade-pill satisfactory">Proficient</span></td>
                            <td>Child meets developmental expectations independently</td>
                        </tr>
                        <tr>
                            <td><span style="font-size:16px;font-weight:800;color:#0369a1;">AP</span></td>
                            <td><span class="grade-pill fairly">Approaching Proficiency</span></td>
                            <td>Child is progressing toward developmental expectations</td>
                        </tr>
                        <tr>
                            <td><span style="font-size:16px;font-weight:800;color:#d97706;">D</span></td>
                            <td><span class="grade-pill fairly" style="background:#fef3c7;color:#92400e;">Developing</span></td>
                            <td>Child needs support to meet developmental expectations</td>
                        </tr>
                        <tr>
                            <td><span style="font-size:16px;font-weight:800;color:#dc2626;">B</span></td>
                            <td><span class="grade-pill did-not-meet">Beginning</span></td>
                            <td>Child requires intensive support and intervention</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p style="font-size:12px;color:#6b7280;margin-top:8px;"><i class="bi bi-info-circle me-1"></i>Kindergarten assessment is done through observation, portfolio, and developmental checklists. No numerical grades are given.</p>
        </div>

        
        <div>
            <h5 style="font-size:15px;font-weight:700;color:#1e3a8a;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-mortarboard-fill" style="color:#1d4ed8;"></i> Grades 1&ndash;6 &mdash; Numerical Grading Scale
            </h5>
            <div class="row g-4">

                
                <div class="col-lg-7">
                    <table class="grading-table">
                        <thead>
                            <tr>
                                <th>Grade Range</th>
                                <th>Descriptor</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>90 &ndash; 100</td>
                                <td><span class="grade-pill outstanding">Outstanding</span></td>
                                <td>Passed</td>
                            </tr>
                            <tr>
                                <td>85 &ndash; 89</td>
                                <td><span class="grade-pill satisfactory">Very Satisfactory</span></td>
                                <td>Passed</td>
                            </tr>
                            <tr>
                                <td>80 &ndash; 84</td>
                                <td><span class="grade-pill satisfactory">Satisfactory</span></td>
                                <td>Passed</td>
                            </tr>
                            <tr>
                                <td>75 &ndash; 79</td>
                                <td><span class="grade-pill fairly">Fairly Satisfactory</span></td>
                                <td>Passed</td>
                            </tr>
                            <tr>
                                <td>Below 75</td>
                                <td><span class="grade-pill did-not-meet">Did Not Meet Expectations</span></td>
                                <td>Failed</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                
                <div class="col-lg-5">
                    <div style="background:#fff;border:1px solid var(--ilc-border);border-radius:10px;padding:20px;">
                        <h6 style="font-size:13px;font-weight:700;color:var(--ilc-blue);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;padding-bottom:10px;border-bottom:1px solid var(--ilc-border);">
                            Grade Composition <span style="font-weight:400;font-size:11px;color:#94a3b8;">(per term)</span>
                        </h6>
                        <div style="display:flex;flex-direction:column;gap:14px;margin-top:14px;">
                            <div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                                    <span style="color:#555;">Written Works</span>
                                    <span style="font-weight:700;color:var(--ilc-blue);">25%</span>
                                </div>
                                <div style="background:#e8f0fb;border-radius:4px;height:8px;">
                                    <div style="background:var(--ilc-blue);height:100%;width:25%;border-radius:4px;"></div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Quizzes, long tests, written outputs</div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                                    <span style="color:#555;">Performance Tasks</span>
                                    <span style="font-weight:700;color:var(--ilc-blue);">50%</span>
                                </div>
                                <div style="background:#e8f0fb;border-radius:4px;height:8px;">
                                    <div style="background:var(--ilc-blue);height:100%;width:50%;border-radius:4px;"></div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:3px;">Projects, demonstrations, presentations</div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                                    <span style="color:#555;">Term Summative Assessment</span>
                                    <span style="font-weight:700;color:var(--ilc-blue);">25%</span>
                                </div>
                                <div style="background:#e8f0fb;border-radius:4px;height:8px;">
                                    <div style="background:var(--ilc-blue);height:100%;width:25%;border-radius:4px;"></div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:3px;">End-of-term summative exam (replaces quarterly exam)</div>
                            </div>
                        </div>
                    </div>

                    <div class="grading-note" style="margin-top:14px;">
                        <i class="bi bi-info-circle-fill"></i>
                        The passing grade is <strong>75</strong>. Students who do not meet this mark are required to undergo remedial classes or intervention programs per DepEd MATATAG guidelines.
                        Formative assessments are used for monitoring progress only and <strong>do not affect the term grade</strong>.
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>


<footer class="site-footer">
    <div class="container">
        <div class="row g-4">

            
            <div class="col-md-3">
                <h6>Contact Details</h6>
                <div class="footer-contact">
                    <p>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Brgy Poblacion Central, General Tinio, Nueva Ecija, Philippines</span>
                    </p>
                    <p>
                        <i class="bi bi-telephone-fill"></i>
                        <span>0951-989-9685</span>
                    </p>
                    <p>
                        <i class="bi bi-envelope-fill"></i>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="274e494148674e424a424b4e410a4e4b440942435209574f">Iemelif_learningcenter@gmail.com</a></span>
                    </p>
                </div>
                
                <div class="footer-logos mt-3">
                    <div class="footer-logo-img">
                        <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Logo"> 
                    </div>
                    <div class="footer-logo-img">
                        <img src="<?php echo e(asset('images/logoo.jpg')); ?>" alt="Logo"> 
                    </div>
                </div>
            </div>

            
            <div class="col-md-2">
                <h6>Quick Links</h6>
                                <a href="<?php echo e(route('home')); ?>">Home</a>
                <a href="<?php echo e(route('about')); ?>">About Us</a>
                <a href="<?php echo e(route('academics')); ?>">Academics</a>
                <a href="<?php echo e(route('admission')); ?>">Enrollment</a>
                <a href="<?php echo e(route('announcements')); ?>">Announcements</a>
                <a href="<?php echo e(route('news')); ?>">News</a>
                <a href="<?php echo e(route('aims')); ?>">AIMS</a>
                <a href="<?php echo e(route('contact')); ?>">Contact Us</a>
                <a href="<?php echo e(route('terms')); ?>">Terms &amp; Conditions</a>
                <a href="<?php echo e(route('privacy')); ?>">Privacy Policy</a>
                <a href="#" onclick="ckShowAgain(event)">Cookie Settings</a>
            </div>

            
            <div class="col-md-3">
                <h6>Latest Articles</h6>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="<?php echo e(asset('images/bg4.jpg')); ?>" alt="News 1"> 
                    </div>
                    <div class="footer-news-text">
                        Celebrating 32 Years of Excellence in Education
                    </div>
                </div>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="<?php echo e(asset('images/bg2.jpg')); ?>" alt="News 1"> 
                    </div>
                    <div class="footer-news-text">
                        Science Education Affair 2025 &mdash; A Successful Event
                    </div>
                </div>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="<?php echo e(asset('images/bg3.jpg')); ?>" alt="News 1"> 
                    </div>
                    <div class="footer-news-text">
                        Welcome Back to School, ILCians!
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <h6>Office Hours</h6>
                <div class="office-hours-row"><span>Monday &ndash; Friday</span><span>7:30 AM &ndash; 5:00 PM</span></div>
                <div class="office-hours-row"><span>Saturday - Sunday</span><span style="color:var(--ilc-gold);">Closed</span></div>
                <h6 class="mt-3">Visitor Counter</h6>
                <div style="display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px 14px;margin-top:8px;">
                    <i class="bi bi-people-fill" style="font-size:20px;color:var(--ilc-gold);flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.8px;">Total Visitors</div>
                        <div style="font-size:20px;font-weight:800;color:#fff;line-height:1.2;"><?php echo e(number_format($visitorCount ?? 0)); ?></div>
                    </div>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:5px;font-size:10px;color:rgba(255,255,255,.4);">
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 1.5s ease-in-out infinite;"></span>Live
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom mt-4">
        &copy; <?php echo e(date('Y')); ?> IEMELIF Learning Center &mdash; General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>


<section class="react-academics-section">
    <div class="container">
        
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var sections = ['sec-overview','sec-grades','sec-calendar','sec-grading'];
    var links    = document.querySelectorAll('.qnav-link[data-sec]');

    // â”€â”€ Active highlight on scroll â”€â”€
    function setActive() {
        var scrollY = window.scrollY + 120;
        var current = sections[0];
        sections.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.offsetTop <= scrollY) current = id;
        });
        links.forEach(function(a) {
            a.classList.toggle('active', a.dataset.sec === current);
        });
    }
    window.addEventListener('scroll', setActive, { passive: true });
    setActive();

    // â”€â”€ Smooth scroll (no offset needed &mdash; sidebar doesn't block content) â”€â”€
    document.querySelectorAll('a[href^="#sec-"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            var target = document.getElementById(this.getAttribute('href').slice(1));
            if (!target) return;
            e.preventDefault();
            var top = target.getBoundingClientRect().top + window.scrollY - 24;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });
})();

// â”€â”€ Calendar sub-links click toggle â”€â”€
function toggleCalSub(e) {
    // Only toggle sub &mdash; still allow the calendar anchor scroll
    var sub      = document.getElementById('qnav-cal-sub');
    var chevron  = document.getElementById('qnav-chevron');
    var isOpen   = sub.classList.contains('open');

    if (isOpen) {
        sub.classList.remove('open');
        chevron.style.transform = 'rotate(0deg)';
        // Let the href navigate
    } else {
        e.preventDefault(); // open sub first, don't scroll yet
        sub.classList.add('open');
        chevron.style.transform = 'rotate(180deg)';
    }
}
</script>
<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
</div>
<script>(function(){var S=[['real-content','skel-content']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();</script>
</body>
</html><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/academics.blade.php ENDPATH**/ ?>