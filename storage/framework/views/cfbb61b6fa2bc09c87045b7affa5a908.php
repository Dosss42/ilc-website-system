<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMS — Academic Information & Management System | IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <style>
        body { font-family: 'Open Sans', sans-serif; -webkit-font-smoothing: antialiased; }

        .aims-hero {
            background: linear-gradient(135deg, #0f2549 0%, #1a3a6c 60%, #1e4d8c 100%);
            padding: 72px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .aims-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .aims-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(197,160,89,.18); border: 1px solid rgba(197,160,89,.4);
            color: #c5a059; padding: 6px 18px; border-radius: 20px;
            font-size: 11px; font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 18px;
        }
        .aims-title {
            font-size: 46px; font-weight: 800; color: #fff;
            line-height: 1.15; margin-bottom: 8px;
        }
        .aims-title span { color: #c5a059; }
        .aims-subtitle {
            font-size: 15px; color: rgba(255,255,255,.72); line-height: 1.75;
            max-width: 600px; margin-bottom: 32px;
        }
        .aims-hero-stat {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 12px; padding: 16px 22px;
            text-align: center; flex: 1; min-width: 110px;
        }
        .aims-hero-stat .val { font-size: 26px; font-weight: 800; color: #c5a059; line-height: 1; }
        .aims-hero-stat .lbl { font-size: 11px; color: rgba(255,255,255,.65); margin-top: 4px; }

        .aims-section-title {
            font-size: 24px; font-weight: 800; color: #1a3a6c;
            margin-bottom: 6px;
        }
        .aims-divider {
            width: 48px; height: 3px; background: #c5a059;
            border-radius: 2px; margin: 12px 0 32px;
        }

        .role-card {
            background: #fff; border-radius: 16px; padding: 28px 24px;
            height: 100%; border: 1.5px solid #e8edf5;
            box-shadow: 0 2px 14px rgba(26,58,108,.06);
            transition: transform .2s, box-shadow .2s;
        }
        .role-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(26,58,108,.14); }
        .role-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 16px; flex-shrink: 0;
        }
        .role-card h5 { font-size: 15px; font-weight: 800; color: #1a3a6c; margin-bottom: 8px; }
        .role-card p { font-size: 12.5px; color: #64748b; line-height: 1.7; margin-bottom: 14px; }
        .role-feature {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #475569; padding: 4px 0;
        }
        .role-feature i { color: #1a3a6c; font-size: 13px; flex-shrink: 0; }

        .how-step {
            display: flex; gap: 18px; align-items: flex-start;
            padding: 20px 0; border-bottom: 1px solid #f1f5f9;
        }
        .how-step:last-child { border-bottom: none; }
        .step-num {
            width: 40px; height: 40px; border-radius: 10px;
            background: #1a3a6c; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 800; flex-shrink: 0;
        }
        .step-num.gold { background: #c5a059; color: #1a3a6c; }

        .faq-item {
            border: 1.5px solid #e8edf5; border-radius: 12px;
            margin-bottom: 10px; overflow: hidden;
        }
        .faq-q {
            padding: 16px 20px; font-weight: 700; color: #1a3a6c;
            font-size: 13.5px; cursor: pointer;
            display: flex; justify-content: space-between; align-items: center;
            background: #fff; user-select: none;
        }
        .faq-q:hover { background: #f8faff; }
        .faq-a {
            padding: 0 20px; max-height: 0; overflow: hidden;
            transition: max-height .3s ease, padding .3s ease;
            font-size: 13px; color: #64748b; line-height: 1.7;
            background: #fafbff;
        }
        .faq-item.open .faq-a { max-height: 200px; padding: 12px 20px 16px; }
        .faq-item.open .faq-icon { transform: rotate(180deg); }
        .faq-icon { transition: transform .3s; font-size: 14px; color: #94a3b8; }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780250000">
</head>
<body class="page-loading">


<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="school-logo"><a href="<?php echo e(route('home')); ?>"><img src="/images/logo.png" alt="Logo"></a></div>
                <div class="school-title"><h1>IEMELIF Learning Center</h1><p>General Tinio, Nueva Ecija</p></div>
            </div>
            <div class="d-none d-lg-flex flex-column align-items-end gap-1">
                <form class="search-form" action="<?php echo e(route('search')); ?>" method="GET" style="display:flex;">
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="<?php echo e(request('q')); ?>">
                    <button class="btn-search" type="submit"><i class="bi bi-search"></i></button>
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
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admission')); ?>">Enrollment</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('announcements')); ?>">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('news')); ?>">News</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('aims')); ?>">AIMS</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('contact')); ?>">Contact</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-light btn-sm">Log In</a>
            </div>
        </div>
    </div>
</nav>


<div class="skel-section" id="skel-aims">
    
    <div style="background:#1a3a6c;padding:72px 0 60px;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="skel-dark" style="height:14px;width:130px;border-radius:20px;display:block;margin-bottom:20px;"></span>
                    <span class="skel-dark" style="height:44px;width:80%;display:block;border-radius:6px;margin-bottom:10px;"></span>
                    <span class="skel-dark" style="height:44px;width:60%;display:block;border-radius:6px;margin-bottom:24px;"></span>
                    <?php for($i=0;$i<3;$i++): ?><span class="skel-dark" style="height:13px;width:100%;display:block;border-radius:4px;margin-bottom:8px;"></span><?php endfor; ?>
                    <div style="display:flex;gap:12px;margin-top:28px;">
                        <span class="skel-dark" style="height:46px;width:160px;border-radius:10px;display:block;"></span>
                        <span class="skel-dark" style="height:46px;width:140px;border-radius:10px;display:block;"></span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <span class="skel-dark" style="height:220px;width:100%;border-radius:20px;display:block;"></span>
                </div>
            </div>
        </div>
    </div>
    
    <div style="background:#f6f8fb;padding:64px 0;">
        <div class="container">
            <span class="skel" style="height:12px;width:120px;display:block;border-radius:4px;margin:0 auto 12px;"></span>
            <span class="skel" style="height:28px;width:260px;display:block;border-radius:6px;margin:0 auto 32px;"></span>
            <div class="row g-4">
                <?php for($i=0;$i<4;$i++): ?>
                <div class="col-lg-3 col-md-6">
                    <div style="background:#fff;border-radius:16px;padding:28px 24px;">
                        <span class="skel" style="height:56px;width:56px;border-radius:14px;display:block;margin-bottom:16px;"></span>
                        <span class="skel" style="height:18px;width:70%;border-radius:4px;display:block;margin-bottom:10px;"></span>
                        <?php for($j=0;$j<4;$j++): ?><span class="skel" style="height:11px;width:100%;border-radius:4px;display:block;margin-bottom:7px;"></span><?php endfor; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <div style="background:#fff;padding:64px 0;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <span class="skel" style="height:12px;width:100px;display:block;border-radius:4px;margin-bottom:12px;"></span>
                    <span class="skel" style="height:26px;width:200px;display:block;border-radius:6px;margin-bottom:28px;"></span>
                    <?php for($i=0;$i<4;$i++): ?>
                    <div style="display:flex;gap:18px;padding:20px 0;border-bottom:1px solid #f1f5f9;">
                        <span class="skel" style="width:40px;height:40px;border-radius:10px;display:block;flex-shrink:0;"></span>
                        <div style="flex:1;">
                            <span class="skel" style="height:14px;width:60%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                            <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                            <span class="skel" style="height:11px;width:85%;display:block;border-radius:4px;"></span>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="col-lg-6">
                    <span class="skel" style="height:12px;width:140px;display:block;border-radius:4px;margin-bottom:12px;"></span>
                    <span class="skel" style="height:26px;width:240px;display:block;border-radius:6px;margin-bottom:28px;"></span>
                    <?php for($i=0;$i<5;$i++): ?><span class="skel" style="height:50px;width:100%;border-radius:12px;display:block;margin-bottom:10px;"></span><?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="real-section" id="real-aims">


<div class="aims-hero">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="aims-badge">
                    <i class="bi bi-grid-fill"></i> ILC Online Portal
                </div>
                <h1 class="aims-title">
                    <span>AIMS</span><br>
                    Academic Information<br>&amp; Management System
                </h1>
                <p class="aims-subtitle">
                    The official digital platform of IEMELIF Learning Center — connecting students, teachers, and administrators in one secure, easy-to-use system designed for the ILC community.
                </p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="<?php echo e(route('login')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:#c5a059;color:#1a3a6c;font-weight:800;font-size:13px;padding:13px 30px;border-radius:10px;text-decoration:none;transition:background .2s,transform .15s;"
                       onmouseover="this.style.background='#d4b06a';this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='#c5a059';this.style.transform='translateY(0)';">
                        <i class="bi bi-box-arrow-in-right" style="font-size:16px;"></i> Login to AIMS
                    </a>
                    <a href="<?php echo e(route('admission')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;font-weight:700;font-size:13px;padding:13px 28px;border-radius:10px;text-decoration:none;border:1.5px solid rgba(255,255,255,.4);transition:background .2s;"
                       onmouseover="this.style.background='rgba(255,255,255,.1)';"
                       onmouseout="this.style.background='transparent';">
                        <i class="bi bi-person-plus" style="font-size:15px;"></i> Enroll Now
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:28px;">
                    <p style="color:rgba(255,255,255,.5);font-size:10px;letter-spacing:2px;text-transform:uppercase;margin-bottom:16px;">System Overview</p>
                    <?php $__currentLoopData = [
                        ['bi-person-fill','#60a5fa','Student Portal','View grades, schedule & payments'],
                        ['bi-person-workspace','#4ade80','Teacher Portal','Grades, attendance & announcements'],
                        ['bi-shield-check','#fbbf24','Admin Dashboard','Enrollments, sections & reports'],
                        ['bi-cash-coin','#f9a8d4','Finance & Cashier','Payments, fees & collections'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$color,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.07);">
                        <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi <?php echo e($icon); ?>" style="font-size:16px;color:<?php echo e($color); ?>;"></i>
                        </div>
                        <div>
                            <p style="margin:0;color:#fff;font-size:13px;font-weight:700;"><?php echo e($title); ?></p>
                            <p style="margin:0;color:rgba(255,255,255,.5);font-size:11px;"><?php echo e($desc); ?></p>
                        </div>
                        <i class="bi bi-check-circle-fill ms-auto" style="color:#4ade80;font-size:14px;"></i>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <p style="margin:14px 0 0;text-align:center;color:rgba(255,255,255,.35);font-size:11px;">All portals run on the AIMS platform</p>
                </div>
            </div>
        </div>
    </div>
</div>


<section style="background:#f6f8fb;padding:64px 0;">
    <div class="container">
        <div style="text-align:center;margin-bottom:44px;">
            <p style="font-size:11px;font-weight:700;color:#c5a059;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px;">Who Can Use AIMS</p>
            <h2 class="aims-section-title">Built for Every ILC Role</h2>
            <div class="aims-divider mx-auto"></div>
            <p style="font-size:13.5px;color:#64748b;max-width:520px;margin:0 auto;">Each user type has a dedicated portal with tools and features designed specifically for their needs.</p>
        </div>
        <div class="row g-4">

            
            <div class="col-lg-3 col-md-6">
                <div class="role-card">
                    <div class="role-icon" style="background:#eff6ff;">
                        <i class="bi bi-person-fill" style="color:#1a3a6c;"></i>
                    </div>
                    <h5>Students</h5>
                    <p>Access everything about your school life — grades, schedule, payments, and enrollment status — anytime from any device.</p>
                    <?php $__currentLoopData = ['View grades per subject & term','Check class schedule','Track tuition balance & pay online','Upload required documents','View school announcements']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="role-feature"><i class="bi bi-check2"></i><?php echo e($f); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6">
                <div class="role-card">
                    <div class="role-icon" style="background:#f0fdf4;">
                        <i class="bi bi-person-workspace" style="color:#16a34a;"></i>
                    </div>
                    <h5>Teachers</h5>
                    <p>Manage your classes efficiently — enter grades, take attendance, post announcements, and schedule meetings with parents.</p>
                    <?php $__currentLoopData = ['Enter & submit grades by term','Record daily attendance','Post section announcements','Schedule parent-teacher meetings','View assigned sections & subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="role-feature"><i class="bi bi-check2"></i><?php echo e($f); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6">
                <div class="role-card">
                    <div class="role-icon" style="background:#fffbeb;">
                        <i class="bi bi-shield-check" style="color:#d97706;"></i>
                    </div>
                    <h5>Administrators</h5>
                    <p>Full control over school operations — enrollments, sections, teacher assignments, documents, and school-wide data.</p>
                    <?php $__currentLoopData = ['Approve & process enrollments','Manage sections & teachers','Review submitted documents','Oversee all student records','Generate school reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="role-feature"><i class="bi bi-check2"></i><?php echo e($f); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6">
                <div class="role-card">
                    <div class="role-icon" style="background:#fdf4ff;">
                        <i class="bi bi-cash-coin" style="color:#9333ea;"></i>
                    </div>
                    <h5>Finance &amp; Cashier</h5>
                    <p>Handle all tuition-related operations — from walk-in cash payments to online Xendit payment links and financial reports.</p>
                    <?php $__currentLoopData = ['Record walk-in payments','Generate Xendit payment links','Approve GCash screenshots','Manage installment schedules','View collection reports & charts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="role-feature"><i class="bi bi-check2"></i><?php echo e($f); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

        </div>
    </div>
</section>


<section style="background:#fff;padding:64px 0;">
    <div class="container">
        <div class="row g-5">

            
            <div class="col-lg-6">
                <p style="font-size:11px;font-weight:700;color:#c5a059;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px;">Getting Started</p>
                <h2 class="aims-section-title">How to Access AIMS</h2>
                <div class="aims-divider"></div>

                <div class="how-step">
                    <div class="step-num">1</div>
                    <div>
                        <p style="margin:0 0 4px;font-weight:700;color:#1a3a6c;font-size:13.5px;">Complete Your Enrollment</p>
                        <p style="margin:0;font-size:12.5px;color:#64748b;line-height:1.65;">Submit your enrollment application online. Once approved by the admin, your account is automatically created.</p>
                    </div>
                </div>
                <div class="how-step">
                    <div class="step-num">2</div>
                    <div>
                        <p style="margin:0 0 4px;font-weight:700;color:#1a3a6c;font-size:13.5px;">Receive Your Login Credentials</p>
                        <p style="margin:0;font-size:12.5px;color:#64748b;line-height:1.65;">Your registered email address and password are your AIMS credentials. Teachers and staff receive credentials from the administrator.</p>
                    </div>
                </div>
                <div class="how-step">
                    <div class="step-num">3</div>
                    <div>
                        <p style="margin:0 0 4px;font-weight:700;color:#1a3a6c;font-size:13.5px;">Log In to Your Portal</p>
                        <p style="margin:0;font-size:12.5px;color:#64748b;line-height:1.65;">Go to the Login page, enter your email and password. AIMS will automatically redirect you to the correct portal based on your role.</p>
                    </div>
                </div>
                <div class="how-step">
                    <div class="step-num gold">4</div>
                    <div>
                        <p style="margin:0 0 4px;font-weight:700;color:#1a3a6c;font-size:13.5px;">You're In!</p>
                        <p style="margin:0;font-size:12.5px;color:#64748b;line-height:1.65;">Access your grades, payments, schedules, and announcements from your personalized dashboard.</p>
                    </div>
                </div>

                <a href="<?php echo e(route('login')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:#1a3a6c;color:#fff;font-weight:700;font-size:13px;padding:12px 28px;border-radius:10px;text-decoration:none;margin-top:24px;transition:background .2s;"
                   onmouseover="this.style.background='#2471a3';"
                   onmouseout="this.style.background='#1a3a6c';">
                    <i class="bi bi-box-arrow-in-right"></i> Go to Login
                </a>
            </div>

            
            <div class="col-lg-6">
                <p style="font-size:11px;font-weight:700;color:#c5a059;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px;">Common Questions</p>
                <h2 class="aims-section-title">Frequently Asked Questions</h2>
                <div class="aims-divider"></div>

                <?php $__currentLoopData = [
                    ['What does AIMS stand for?', 'AIMS stands for Academic Information & Management System. It is the official online portal of IEMELIF Learning Center for managing academic and administrative processes.'],
                    ['Who can use AIMS?', 'AIMS is available to all enrolled students, teaching staff, school administrators, finance officers, and cashiers of IEMELIF Learning Center.'],
                    ['How do I get my AIMS account?', 'Student accounts are created automatically after a successful enrollment application. Teacher and staff accounts are created by the school administrator.'],
                    ['I forgot my password. What do I do?', 'Contact the school office or your administrator to request a password reset. You may also use the "Forgot Password" option on the login page if available.'],
                    ['Can I pay my tuition through AIMS?', 'Yes! Students can generate Xendit online payment links (GCash, Maya, bank transfer) directly from their AIMS payment portal.'],
                    ['Is AIMS accessible on mobile?', 'Yes. AIMS is fully responsive and works on smartphones, tablets, and desktop computers through any modern web browser.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$q, $a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="faq-item" onclick="this.classList.toggle('open')">
                    <div class="faq-q">
                        <span><?php echo e($q); ?></span>
                        <i class="bi bi-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-a"><?php echo e($a); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
</section>


<section style="background:linear-gradient(135deg,#0f2549,#1a3a6c);padding:56px 0;">
    <div class="container" style="text-align:center;">
        <div style="width:64px;height:64px;background:rgba(197,160,89,.15);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="bi bi-mortarboard-fill" style="font-size:28px;color:#c5a059;"></i>
        </div>
        <h2 style="color:#fff;font-size:26px;font-weight:800;margin-bottom:10px;">Ready to access <span style="color:#c5a059;">AIMS</span>?</h2>
        <p style="color:rgba(255,255,255,.7);font-size:14px;max-width:480px;margin:0 auto 30px;line-height:1.7;">Log in using your school email and password. First time? Complete your enrollment to get started.</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
            <a href="<?php echo e(route('login')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:#c5a059;color:#1a3a6c;font-weight:800;font-size:13px;padding:13px 32px;border-radius:10px;text-decoration:none;transition:background .2s,transform .15s;"
               onmouseover="this.style.background='#d4b06a';this.style.transform='translateY(-2px)';"
               onmouseout="this.style.background='#c5a059';this.style.transform='translateY(0)';">
                <i class="bi bi-box-arrow-in-right"></i> Login to AIMS
            </a>
            <a href="<?php echo e(route('admission')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;font-weight:700;font-size:13px;padding:13px 28px;border-radius:10px;text-decoration:none;border:1.5px solid rgba(255,255,255,.35);transition:background .2s;"
               onmouseover="this.style.background='rgba(255,255,255,.1)';"
               onmouseout="this.style.background='transparent';">
                <i class="bi bi-person-plus"></i> Start Enrollment
            </a>
        </div>
    </div>
</section>

</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    var S=[['real-aims','skel-aims']];
    function go(i){
        if(i>=S.length){document.body.classList.remove('page-loading');return;}
        var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);
        if(s)s.style.display='none';
        if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}
        setTimeout(function(){go(i+1);},160);
    }
    function start(){setTimeout(function(){go(0);},200);}
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}
})();
</script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/aims.blade.php ENDPATH**/ ?>