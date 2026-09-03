<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEMELIF Learning Center - General Tinio Nueva Ecija</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780380004">
    <link rel="stylesheet" href="<?php echo e(asset('css/home.css')); ?>?v=1780380004">
    <style>
        /* Custom Scrollbar Design */
        ::-webkit-scrollbar { width: 12px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #1a3a6c; border-radius: 10px; border: 2px solid #f1f1f1; }
        ::-webkit-scrollbar-thumb:hover { background: #c5a059; }
        .main-nav { background: #1a3a6c !important; display: block !important; position: relative !important; }
        .main-nav .navbar-nav .nav-link { color: #fff !important; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 10px 14px !important; letter-spacing: 0.5px; transition: background 0.2s; }
        .main-nav .navbar-nav .nav-link:hover, .main-nav .navbar-nav .nav-link.active { background: rgba(255,255,255,0.15); }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(1.4); }
        }

        /* ── Skeleton shimmer ─────────────────────────────── */
        @keyframes skelShimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        .skel {
            background: linear-gradient(90deg, #e8edf2 25%, #f5f7fa 50%, #e8edf2 75%);
            background-size: 600px 100%;
            animation: skelShimmer 1.4s ease-in-out infinite;
            border-radius: 6px;
            display: block;
        }
        .skel-dark {
            background: linear-gradient(90deg, #d4d9e0 25%, #e8edf2 50%, #d4d9e0 75%);
            background-size: 600px 100%;
            animation: skelShimmer 1.4s ease-in-out infinite;
            border-radius: 6px;
            display: block;
        }

        /* All content sections always visible — no skeleton hide/show logic */
        .skel-section { display: none !important; }
        .real-section { display: block !important; opacity: 1 !important; visibility: visible !important; }
        .real-section-flex { display: flex !important; opacity: 1 !important; visibility: visible !important; }
    </style>

<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, div, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780380004">
</head>
<body>


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
                    <div style="display:flex;align-items:center;gap:4px;">
                        <i class="bi bi-calendar3" style="color:var(--ilc-gold);font-size:11px;"></i>
                        <span id="topbar-date"></span>
                    </div>
                    <div style="width:1px;height:11px;background:#ccc;"></div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <i class="bi bi-clock" style="color:var(--ilc-gold);font-size:11px;"></i>
                        <span id="topbar-time" style="font-variant-numeric:tabular-nums;min-width:70px;"></span>
                    </div>
                </div>

                
                <form class="search-form" action="<?php echo e(route('search')); ?>" method="GET" style="display:flex;flex-direction:row;align-items:center;">
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="<?php echo e(request('q')); ?>" style="flex:1;">
                    <button class="btn-search" type="submit" style="flex-shrink:0;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>

<script>
(function() {
    var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    function pad(n) { return n < 10 ? '0' + n : n; }
    function tick() {
        var now  = new Date();
        var d    = days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();
        var h    = now.getHours(), ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var t = h + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()) + ' ' + ampm;
        var de = document.getElementById('topbar-date');
        var te = document.getElementById('topbar-time');
        if (de) de.textContent = d;
        if (te) te.textContent = t;
    }
    tick();
    setInterval(tick, 1000);
})();
</script>


<nav class="main-nav navbar navbar-expand-lg">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('about*') ? 'active' : ''); ?>" href="<?php echo e(route('about')); ?>">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('academics*') ? 'active' : ''); ?>" href="<?php echo e(route('academics')); ?>">Academics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admission*') ? 'active' : ''); ?>" href="<?php echo e(route('admission')); ?>">Enrollment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('announcements*') ? 'active' : ''); ?>" href="<?php echo e(route('announcements')); ?>">Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('news*') ? 'active' : ''); ?>" href="<?php echo e(route('news')); ?>">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('aims') ? 'active' : ''); ?>" href="<?php echo e(route('aims')); ?>">AIMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('contact*') ? 'active' : ''); ?>" href="<?php echo e(route('contact')); ?>">Contact</a>
                </li>
            </ul>
        </div>

        
        <div class="d-flex align-items-center gap-2 nav-actions">
            
            <button type="button" class="nav-search-toggle d-lg-none" id="navSearchToggle" onclick="toggleNavSearch()" title="Search">
                <i class="bi bi-search"></i>
            </button>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-light btn-sm nav-login-btn">
                <i class="bi bi-person-fill me-1"></i>Log In
            </a>
        </div>
    </div>

    
    <div class="nav-search-bar d-lg-none" id="navSearchBar">
        <div class="container">
            <form class="nav-search-form" action="<?php echo e(route('search')); ?>" method="GET">
                <i class="bi bi-search nav-search-icon"></i>
                <input type="text" name="q" id="navSearchInput" class="nav-search-input"
                       placeholder="Search announcements, news, programs..."
                       value="<?php echo e(request('q')); ?>">
                <span class="nav-search-hint">Press Enter to search · Esc to close</span>
            </form>
        </div>
    </div>
</nav>

<script>
function toggleNavSearch() {
    var bar = document.getElementById('navSearchBar');
    var btn = document.getElementById('navSearchToggle');
    var icon = btn.querySelector('i');
    var isOpen = bar.classList.contains('open');
    if (isOpen) {
        bar.classList.remove('open');
        btn.classList.remove('active');
        icon.className = 'bi bi-search';
    } else {
        bar.classList.add('open');
        btn.classList.add('active');
        icon.className = 'bi bi-x-lg';
        setTimeout(function() { document.getElementById('navSearchInput').focus(); }, 150);
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var bar = document.getElementById('navSearchBar');
        if (bar && bar.classList.contains('open')) toggleNavSearch();
    }
});
</script>


<div class="skel-section" id="skel-hero" style="width:100%;overflow:hidden;">
    <span class="skel" style="width:100%;height:520px;border-radius:0;display:block;"></span>
</div>


<div class="real-section" id="real-hero">
<style>
/* ── Ken Burns Hero ── */
.hero-section {
    position: relative;
    width: 100%;
    height: 520px;
    overflow: hidden;
    background: #0f2451;
}

/* The image layer — Ken Burns slow zoom + drift */
.hero-bg {
    position: absolute;
    inset: -6%;           /* slightly oversized so zoom never reveals edges */
    width: 112%;
    height: 112%;
    background: url('<?php echo e(asset('images/background.png')); ?>') center center / cover no-repeat;
    animation: kenBurns 22s ease-in-out infinite;
    will-change: transform;
    pointer-events: none;
}

@keyframes kenBurns {
    0%   { transform: scale(1)    translate(0,   0);   }
    25%  { transform: scale(1.06) translate(-1%, -1%); }
    50%  { transform: scale(1.10) translate(-2%, 0.5%);}
    75%  { transform: scale(1.06) translate( 1%, -0.5%);}
    100% { transform: scale(1)    translate(0,   0);   }
}

/* Dark gradient overlay — bottom heavier so text pops */
.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(10, 22, 50, 0.30) 0%,
        rgba(10, 22, 50, 0.50) 50%,
        rgba(10, 22, 50, 0.78) 100%
    );
    z-index: 1;
    pointer-events: none;
}

/* Subtle shine sweep across the top */
.hero-shine {
    position: absolute;
    top: 0; left: -100%; right: auto;
    width: 60%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.04) 50%, transparent 60%);
    animation: shineSweep 8s ease-in-out infinite;
    z-index: 2;
    pointer-events: none;
}

@keyframes shineSweep {
    0%   { left: -100%; }
    60%  { left: 140%;  }
    100% { left: 140%;  }
}

/* Content layer */
.hero-content {
    position: absolute;
    inset: 0;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 20px;
}

.hero-school-name {
    font-size: clamp(22px, 4vw, 40px);
    font-weight: 900;
    color: #fff;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-shadow: 0 3px 20px rgba(0,0,0,.5);
    margin-bottom: 10px;
    animation: heroFadeUp .9s ease both;
}

.hero-tagline {
    font-size: clamp(13px, 1.8vw, 17px);
    color: rgba(255,255,255,.82);
    font-weight: 500;
    letter-spacing: .5px;
    margin-bottom: 28px;
    text-shadow: 0 2px 10px rgba(0,0,0,.4);
    animation: heroFadeUp .9s .18s ease both;
}

.hero-divider {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #c5a059, #f5d08a, #c5a059);
    border-radius: 2px;
    margin: 0 auto 24px;
    animation: heroFadeUp .9s .1s ease both;
}

.hero-actions {
    display: flex;
    gap: 14px;
    justify-content: center;
    flex-wrap: wrap;
    animation: heroFadeUp .9s .3s ease both;
}

.hero-btn-primary {
    padding: 13px 32px;
    background: linear-gradient(135deg, #c5a059, #e8c97a);
    color: #0f2451;
    border: none;
    border-radius: 40px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    letter-spacing: .4px;
    transition: all .25s;
    box-shadow: 0 6px 22px rgba(197,160,89,.45);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.hero-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(197,160,89,.6);
    color: #0f2451;
}

.hero-btn-secondary {
    padding: 13px 28px;
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 40px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    letter-spacing: .4px;
    transition: all .25s;
    backdrop-filter: blur(4px);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.hero-btn-secondary:hover {
    background: rgba(255,255,255,.22);
    transform: translateY(-3px);
    color: #fff;
}

/* Scroll indicator */
.hero-scroll {
    position: absolute;
    bottom: 22px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 4;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,.5);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    animation: heroFadeUp 1s .6s ease both;
    cursor: pointer;
}
.hero-scroll-dot {
    width: 24px;
    height: 38px;
    border: 2px solid rgba(255,255,255,.35);
    border-radius: 12px;
    display: flex;
    justify-content: center;
    padding-top: 6px;
}
.hero-scroll-dot::after {
    content: '';
    width: 4px;
    height: 8px;
    border-radius: 2px;
    background: rgba(255,255,255,.6);
    animation: scrollBob 1.6s ease-in-out infinite;
}
@keyframes scrollBob {
    0%, 100% { transform: translateY(0); opacity: 1; }
    50%       { transform: translateY(8px); opacity: .3; }
}

@keyframes heroFadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 576px) {
    .hero-section { height: 380px; }
    .hero-btn-primary, .hero-btn-secondary { padding: 11px 22px; font-size: 13px; }
}
</style>

<div class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-shine"></div>

    <div class="hero-content">
        <div class="hero-school-name">IEMELIF Learning Center</div>
        <div class="hero-divider"></div>
        <div class="hero-tagline">Nurturing Faith, Excellence, and Learning — General Tinio, Nueva Ecija</div>
        <div class="hero-actions">
            <?php $enrollmentOpen = \App\Models\Setting::get('enrollment_open', true); ?>
            <?php if($enrollmentOpen): ?>
            <a href="<?php echo e(route('enrollment.form')); ?>" class="hero-btn-primary">
                <i class="bi bi-pencil-fill"></i> Enroll Now
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('about')); ?>" class="hero-btn-secondary">
                <i class="bi bi-info-circle"></i> Learn More
            </a>
        </div>
    </div>

    <div class="hero-scroll" onclick="document.querySelector('.real-section:nth-of-type(2)').scrollIntoView({behavior:'smooth'})">
        <div class="hero-scroll-dot"></div>
        <span>Scroll</span>
    </div>
</div>
</div>



<div class="skel-section" id="skel-thumbs">
    
    <div style="background:#1a3a6c;padding:20px 0;">
        <div class="container"><div class="row g-3">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="col-md-3 text-center">
                <span class="skel-dark" style="height:28px;width:50px;display:block;border-radius:4px;margin:0 auto 6px;"></span>
                <span class="skel-dark" style="height:11px;width:80px;display:block;border-radius:4px;margin:0 auto;"></span>
            </div>
            <?php endfor; ?>
        </div></div>
    </div>
    
    <div style="background:#f6f8fb;padding:36px 0;">
        <div class="container"><div class="row g-4">
            <?php for($i=0;$i<3;$i++): ?>
            <div class="col-md-4">
                <span class="skel" style="height:200px;display:block;border-radius:18px 18px 0 0;margin-bottom:0;"></span>
                <div style="background:#fff;border-radius:0 0 18px 18px;padding:18px;border:1.5px solid #e8edf5;border-top:none;">
                    <span class="skel" style="height:16px;width:70%;display:block;border-radius:4px;margin-bottom:10px;"></span>
                    <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                    <span class="skel" style="height:11px;width:85%;display:block;border-radius:4px;"></span>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
</div>


<div class="real-section" id="real-thumbs">
<style>
/* ── Stats Bar ── */
.stats-bar {
    background: linear-gradient(135deg, #0f2451 0%, #1a3a6c 60%, #2471a3 100%);
    padding: 0;
    border-bottom: 3px solid #c5a059;
}
.stats-bar-inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    divide-x: 1px solid rgba(255,255,255,.1);
}
.stat-item {
    padding: 20px 24px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,.1);
    position: relative;
    overflow: hidden;
    transition: background .25s;
}
.stat-item:last-child { border-right: none; }
.stat-item:hover { background: rgba(255,255,255,.06); }
.stat-item::before {
    content: '';
    position: absolute;
    bottom: 0; left: 50%; transform: translateX(-50%);
    width: 0; height: 3px;
    background: #c5a059;
    transition: width .3s;
}
.stat-item:hover::before { width: 60%; }
.stat-icon {
    font-size: 22px;
    color: #c5a059;
    margin-bottom: 6px;
    display: block;
}
.stat-number {
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    letter-spacing: -1px;
    margin-bottom: 4px;
}
.stat-label {
    font-size: 11px;
    color: rgba(255,255,255,.55);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .8px;
}

/* ── Feature Cards ── */
.feature-cards-row {
    background: #f6f8fb;
    padding: 40px 0 44px;
}
.feature-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(26,58,108,.08);
    border: 1.5px solid #e8edf5;
    transition: transform .25s, box-shadow .25s;
    height: 100%;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}
.feature-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(26,58,108,.16);
    color: inherit;
    text-decoration: none;
}
.feature-card-img {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
    background: #e8f0fb;
    flex-shrink: 0;
}
.feature-card-img img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform .5s ease;
}
.feature-card:hover .feature-card-img img {
    transform: scale(1.06);
}
.feature-card-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 50%, rgba(10,22,50,.55) 100%);
    z-index: 1;
}
.feature-card-badge {
    position: absolute;
    top: 12px;
    left: 14px;
    z-index: 2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    backdrop-filter: blur(6px);
}
.feature-card-body {
    padding: 20px 22px 22px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.feature-card-title {
    font-size: 16px;
    font-weight: 800;
    color: #1a3a6c;
    margin-bottom: 8px;
    line-height: 1.3;
}
.feature-card-text {
    font-size: 13px;
    color: #64748b;
    line-height: 1.65;
    flex: 1;
    margin-bottom: 16px;
}
.feature-card-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #1a3a6c;
    text-decoration: none;
    margin-top: auto;
    transition: gap .2s;
}
.feature-card:hover .feature-card-link { gap: 10px; }
.feature-card-link i { font-size: 14px; }

@media (max-width: 768px) {
    .stats-bar-inner { grid-template-columns: repeat(2, 1fr); }
    .stat-item { border-bottom: 1px solid rgba(255,255,255,.1); }
}
</style>


<div class="stats-bar">
    <div class="container-fluid px-0">
        <div class="stats-bar-inner">
            <div class="stat-item">
                <i class="bi bi-building stat-icon"></i>
                <div class="stat-number" data-count="35" data-suffix="+">35+</div>
                <div class="stat-label">Years of Service</div>
            </div>
            <div class="stat-item">
                <i class="bi bi-people-fill stat-icon"></i>
                <?php $studentCount = \App\Models\User::where('role','student')->count(); ?>
                <div class="stat-number" data-count="<?php echo e($studentCount); ?>" data-suffix=""><?php echo e($studentCount); ?></div>
                <div class="stat-label">Enrolled Students</div>
            </div>
            <div class="stat-item">
                <i class="bi bi-mortarboard-fill stat-icon"></i>
                <div class="stat-number" data-count="8" data-suffix="">8</div>
                <div class="stat-label">Grade Levels</div>
            </div>
            <div class="stat-item">
                <i class="bi bi-geo-alt-fill stat-icon"></i>
                <div class="stat-number">GTN</div>
                <div class="stat-label">General Tinio, N.E.</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function animateCount(el, target, suffix, duration) {
        var startTime = null;
        function step(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); /* ease-out cubic */
            el.textContent = Math.floor(eased * target) + suffix;
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target + suffix;
        }
        requestAnimationFrame(step);
    }

    function initStats() {
        var els = document.querySelectorAll('.stat-number[data-count]');
        if (!els.length) return;
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el     = entry.target;
                var target = parseInt(el.getAttribute('data-count'), 10);
                var suffix = el.getAttribute('data-suffix') || '';
                animateCount(el, target, suffix, 1800);
                observer.unobserve(el);
            });
        }, { threshold: 0.4 });
        els.forEach(function (el) { observer.observe(el); });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initStats);
    else initStats();
})();
</script>


<div class="feature-cards-row">
    <div class="container">
        <div class="row g-4">

            
            <div class="col-md-4">
                <a href="<?php echo e(route('about')); ?>" class="feature-card">
                    <div class="feature-card-img">
                        <img src="<?php echo e(asset('images/oic.jpg')); ?>" alt="School Head">
                        <div class="feature-card-img-overlay"></div>
                        <span class="feature-card-badge" style="background:rgba(26,58,108,.7);color:#fff;">
                            <i class="bi bi-person-badge me-1"></i>Leadership
                        </span>
                    </div>
                    <div class="feature-card-body">
                        <div class="feature-card-title">Meet Our School Head</div>
                        <div class="feature-card-text">Learn about the vision and leadership guiding IEMELIF Learning Center towards excellence in faith-based education.</div>
                        <span class="feature-card-link">Learn More <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

            
            <div class="col-md-4">
                <?php $enrollOpen = \App\Models\Setting::get('enrollment_open', true); ?>
                <a href="<?php echo e($enrollOpen ? route('enrollment.form') : route('admission')); ?>" class="feature-card">
                    <div class="feature-card-img">
                        <img src="<?php echo e(asset('images/bg7.jpg')); ?>" alt="Enrollment">
                        <div class="feature-card-img-overlay"></div>
                        <span class="feature-card-badge" style="background:<?php echo e($enrollOpen ? 'rgba(22,163,74,.8)' : 'rgba(100,116,139,.7)'); ?>;color:#fff;">
                            <i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>
                            <?php echo e($enrollOpen ? 'Enrollment Open' : 'Enrollment Closed'); ?>

                        </span>
                    </div>
                    <div class="feature-card-body">
                        <div class="feature-card-title"><?php echo e($enrollOpen ? 'Enroll Now for S.Y. 2026–2027' : 'Enrollment Information'); ?></div>
                        <div class="feature-card-text"><?php echo e($enrollOpen ? 'Secure your child\'s spot today. Online enrollment is open — complete the requirements and register in minutes.' : 'Enrollment for the current school year is now closed. Check back soon or contact the school for updates.'); ?></div>
                        <span class="feature-card-link"><?php echo e($enrollOpen ? 'Start Enrollment' : 'View Details'); ?> <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

            
            <div class="col-md-4">
                <a href="<?php echo e(route('academics')); ?>" class="feature-card">
                    <div class="feature-card-img">
                        <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="Academics" style="object-fit:contain;padding:20px;background:#e8f0fb;">
                        <div class="feature-card-img-overlay" style="background:linear-gradient(to bottom, transparent 30%, rgba(10,22,50,.4) 100%);"></div>
                        <span class="feature-card-badge" style="background:rgba(197,160,89,.85);color:#0f2451;">
                            <i class="bi bi-book-fill me-1"></i>Academics
                        </span>
                    </div>
                    <div class="feature-card-body">
                        <div class="feature-card-title">Our Academic Programs</div>
                        <div class="feature-card-text">From Nursery to Grade 6, IEMELIF offers a holistic MATATAG-based curriculum that develops mind, character, and faith.</div>
                        <span class="feature-card-link">Explore Programs <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

</div>


<div class="skel-section" id="skel-news-hdr" style="padding:12px 0;background:#e8edf2;">
    <div class="container" style="text-align:center;">
        <span class="skel" style="height:22px;width:300px;display:inline-block;border-radius:4px;"></span>
    </div>
</div>


<div class="real-section" id="real-news-hdr">
<div style="background: var(--ilc-blue); padding: 10px 0; text-align:center; border-bottom: 4px solid var(--ilc-gold);">
    <span style="color:#fff; font-size:18px; font-weight:700; text-transform:uppercase; letter-spacing:2px;">
        ILC NEWS &amp; ANNOUCEMENTS
    </span>
</div>
</div>


<div class="skel-section" id="skel-news" style="padding:40px 0;background:#fff;">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-7">
                <span class="skel" style="height:20px;width:120px;display:block;border-radius:4px;margin-bottom:18px;"></span>
                <?php for($i=0;$i<3;$i++): ?>
                <div style="display:flex;gap:14px;margin-bottom:22px;align-items:flex-start;">
                    <span class="skel" style="width:110px;height:80px;flex-shrink:0;border-radius:8px;display:block;"></span>
                    <div style="flex:1;">
                        <span class="skel" style="height:16px;width:90%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                        <span class="skel" style="height:13px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                        <span class="skel" style="height:13px;width:75%;display:block;border-radius:4px;"></span>
                    </div>
                </div>
                <?php endfor; ?>
                <span class="skel" style="height:34px;width:110px;border-radius:20px;display:block;"></span>
            </div>
            
            <div class="col-lg-5">
                <span class="skel" style="height:20px;width:150px;display:block;border-radius:4px;margin-bottom:18px;"></span>
                <?php for($i=0;$i<3;$i++): ?>
                <div style="display:flex;gap:12px;margin-bottom:16px;align-items:center;">
                    <span class="skel" style="width:52px;height:64px;flex-shrink:0;border-radius:8px;display:block;"></span>
                    <div style="flex:1;">
                        <span class="skel" style="height:15px;width:85%;display:block;border-radius:4px;margin-bottom:7px;"></span>
                        <span class="skel" style="height:13px;width:60%;display:block;border-radius:4px;"></span>
                    </div>
                </div>
                <?php endfor; ?>
                <span class="skel" style="height:34px;width:160px;border-radius:20px;display:block;"></span>
            </div>
        </div>
    </div>
</div>


<div class="real-section" id="real-news">
<style>
/* ── Enhanced News & Announcements ── */
.news-section {
    background: #f8fafd;
    padding: 52px 0 60px;
    position: relative;
}
.news-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1a3a6c 0%, #c5a059 50%, #1a3a6c 100%);
}

/* Column headers */
.news-col-hdr {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 2px solid #e8edf5;
}
.news-col-hdr-badge {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #fff;
    background: #1a3a6c;
    padding: 3px 10px;
    border-radius: 4px;
}
.news-col-hdr-title {
    font-size: 20px;
    font-weight: 800;
    color: #1a3a6c;
    line-height: 1;
}

/* News cards — enhanced */
.news-card {
    background: #fff;
    border-radius: 12px;
    display: flex;
    margin-bottom: 14px;
    overflow: hidden;
    transition: box-shadow .25s, transform .2s;
    box-shadow: 0 2px 12px rgba(26,58,108,.06);
    border: 1.5px solid #e8edf5;
    text-decoration: none !important;
    color: inherit !important;
    position: relative;
}
.news-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, #1a3a6c, #c5a059);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .3s ease;
}
.news-card:hover { box-shadow: 0 10px 30px rgba(26,58,108,.14); transform: translateY(-2px); }
.news-card:hover::after { transform: scaleX(1); }
.news-card .news-img {
    width: 130px;
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
    background: #e8f0fb;
    min-height: 100px;
}
.news-card .news-img img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
    display: block;
}
.news-card .news-img .news-img-placeholder {
    position: absolute; inset: 0;
}
.news-card .news-img .news-img-placeholder img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.news-card:hover .news-img img { transform: scale(1.07); }
.news-card .news-body {
    padding: 14px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.news-card .news-body h6 {
    font-size: 13.5px;
    font-weight: 700;
    color: #1a3a6c;
    margin: 0;
    line-height: 1.4;
}
.news-card .news-body p {
    font-size: 12px;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
    flex: 1;
}
.news-read-more {
    font-size: 11px;
    font-weight: 700;
    color: #1a3a6c;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    transition: color .2s, gap .2s;
}
.news-card:hover .news-read-more { color: #c5a059; gap: 8px; }

/* Announcements panel */
.ann-panel {
    background: linear-gradient(160deg, #f0f4fb 0%, #e8eef8 100%);
    border-radius: 16px;
    padding: 22px 20px 20px;
    border: 1.5px solid #dde4f0;
}

/* Announcement cards on white bg */
.announcement-card {
    background: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    margin-bottom: 10px;
    border: 1.5px solid #e4eaf5;
    box-shadow: 0 2px 8px rgba(26,58,108,.05);
    transition: box-shadow .2s, transform .15s, border-color .2s;
}
.announcement-card:hover {
    box-shadow: 0 6px 20px rgba(26,58,108,.12);
    transform: translateY(-1px);
    border-color: #c5a059;
}
.announcement-card.alt { background: #f8fafd; }
.ann-date-box {
    background: linear-gradient(135deg, #1a3a6c 0%, #2471a3 100%);
    border-radius: 8px;
    text-align: center;
    padding: 7px 10px;
    min-width: 52px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(26,58,108,.2);
}
.ann-date-box .ann-month,
.ann-date-box .ann-day,
.ann-date-box .ann-year { color: #fff !important; }
.ann-date-box .ann-month { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; line-height: 1; display: block; }
.ann-date-box .ann-day   { font-size: 22px; font-weight: 800; line-height: 1.1; display: block; }
.ann-date-box .ann-year  { font-size: 10px; opacity: .75; display: block; }
.ann-title { font-size: 13px; font-weight: 600; line-height: 1.35; flex: 1; color: #1a3a6c; }
.announcement-card .ann-title { color: #1a3a6c !important; }

/* More buttons */
.btn-more {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #1a3a6c;
    color: #fff !important;
    border-radius: 8px;
    padding: 9px 22px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
    letter-spacing: .3px;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 2px 10px rgba(26,58,108,.2);
    margin-top: 14px;
}
.btn-more:hover {
    background: #c5a059;
    color: #1a3a6c !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(197,160,89,.35);
}
</style>

<section class="news-section">
    <div class="container">
        <div class="row g-4 g-xl-5">

            
            <div class="col-lg-7">
                <div class="news-col-hdr">
                    <span class="news-col-hdr-badge">Latest</span>
                    <span class="news-col-hdr-title">ILC News</span>
                </div>

                <?php if(isset($latestNews) && $latestNews->isNotEmpty()): ?>
                    <?php $__currentLoopData = $latestNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('news.show', $article)); ?>" class="news-card">
                        <div class="news-img">
                            <?php if($article->image): ?>
                                <img src="<?php echo e(asset('storage/'.$article->image)); ?>" alt="<?php echo e($article->title); ?>">
                            <?php else: ?>
                                <img src="<?php echo e(asset('images/bg'.(($loop->index % 3) + 1).'.jpg')); ?>" alt="<?php echo e($article->title); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="news-body">
                            <h6><?php echo e($article->title); ?></h6>
                            <p><?php echo e(Str::limit($article->body, 110)); ?></p>
                            <span class="news-read-more">Read More <i class="bi bi-arrow-right-short"></i></span>
                        </div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <a href="<?php echo e(route('news')); ?>" class="news-card">
                    <div class="news-img"><div class="news-img-placeholder"><img src="<?php echo e(asset('images/bg4.jpg')); ?>" alt="News"></div></div>
                    <div class="news-body">
                        <h6>Celebrating 35 Years of Excellence in Education and Faith</h6>
                        <p>The IEMELIF Learning Center celebrated its 35th founding anniversary with inspiring programs.</p>
                        <span class="news-read-more">Read More <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </a>
                <a href="<?php echo e(route('news')); ?>" class="news-card">
                    <div class="news-img"><div class="news-img-placeholder"><img src="<?php echo e(asset('images/bg2.jpg')); ?>" alt="News"></div></div>
                    <div class="news-body">
                        <h6>Science Education Affair 2025</h6>
                        <p>Students and faculty participated actively showcasing projects and innovations that highlight STEM learning.</p>
                        <span class="news-read-more">Read More <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </a>
                <a href="<?php echo e(route('news')); ?>" class="news-card">
                    <div class="news-img"><div class="news-img-placeholder"><img src="<?php echo e(asset('images/bg3.jpg')); ?>" alt="News"></div></div>
                    <div class="news-body">
                        <h6>Welcome Back to School, ILCians!</h6>
                        <p>The school warmly welcomed all students and staff for a new school year filled with hope and growth.</p>
                        <span class="news-read-more">Read More <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </a>
                <?php endif; ?>

                <a href="<?php echo e(route('news')); ?>" class="btn-more"><i class="bi bi-newspaper"></i> More News</a>
            </div>

            
            <div class="col-lg-5">
                <div class="news-col-hdr">
                    <span class="news-col-hdr-badge">Posted</span>
                    <span class="news-col-hdr-title">Announcements</span>
                </div>

                <div class="ann-panel">
                    
                    <div class="enrollment-feature-card">
                        <div class="enroll-icon-box">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="enroll-body">
                            <span class="enroll-sy">S.Y. <?php echo e(now()->year); ?>–<?php echo e(now()->year + 1); ?></span>
                            <?php if(isset($enrollmentOpen) && $enrollmentOpen): ?>
                            <span class="ann-title" style="color:#fff !important;">Enrollment Period is now open.</span>
                            <a href="<?php echo e(route('admission')); ?>" class="enroll-btn-apply">
                                <i class="bi bi-pencil-square"></i> Apply Now
                            </a>
                            <?php else: ?>
                            <span class="ann-title" style="color:#fff !important;">Enrollment is not yet open.</span>
                            <span class="enroll-btn-closed"><i class="bi bi-lock-fill"></i> Opening Soon</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if(isset($latestAnnouncements) && $latestAnnouncements->isNotEmpty()): ?>
                        <?php $__currentLoopData = $latestAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="announcement-card <?php echo e($loop->even ? 'alt' : ''); ?>">
                            <div class="ann-date-box">
                                <span class="ann-month"><?php echo e($ann->created_at->format('M')); ?></span>
                                <span class="ann-day"><?php echo e($ann->created_at->format('d')); ?></span>
                                <span class="ann-year"><?php echo e($ann->created_at->format('Y')); ?></span>
                            </div>
                            <div class="ann-title"><?php echo e($ann->title); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="announcement-card">
                        <div class="ann-date-box">
                            <span class="ann-month">Jun</span>
                            <span class="ann-day">15</span>
                            <span class="ann-year"><?php echo e(now()->year); ?></span>
                        </div>
                        <div class="ann-title">School Opening Day — Welcome back, ILCians!</div>
                    </div>
                    <div class="announcement-card alt">
                        <div class="ann-date-box">
                            <span class="ann-month">Jun</span>
                            <span class="ann-day">15</span>
                            <span class="ann-year"><?php echo e(now()->year); ?></span>
                        </div>
                        <div class="ann-title">General Assembly for Parents and Guardians — Auditorium.</div>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo e(route('announcements')); ?>" class="btn-more"><i class="bi bi-megaphone-fill"></i> More Announcements</a>
                </div>
            </div>

        </div>
    </div>
</section>
</div>


<div class="skel-section" id="skel-vmg" style="padding:50px 0;background:#f6f8fb;">
    <div class="container">
        <div class="row justify-content-center gy-4">
            <?php for($i=0;$i<3;$i++): ?>
            <div class="col-md-4">
                <span class="skel" style="height:22px;width:90px;display:block;border-radius:4px;margin-bottom:14px;"></span>
                <span class="skel" style="height:13px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                <span class="skel" style="height:13px;width:95%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                <span class="skel" style="height:13px;width:80%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                <span class="skel" style="height:13px;width:88%;display:block;border-radius:4px;"></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>


<div class="real-section" id="real-vmg">
<style>
/* ── Enhanced VMG Section ── */
.vmg-section {
    background: linear-gradient(135deg, #0a1628 0%, #1a3a6c 55%, #0f2451 100%);
    padding: 68px 0 76px;
    position: relative;
    overflow: hidden;
    border-top: none;
}
.vmg-section::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(circle at 15% 50%, rgba(197,160,89,.07) 0%, transparent 55%),
        radial-gradient(circle at 85% 50%, rgba(197,160,89,.05) 0%, transparent 55%);
    pointer-events: none;
}
.vmg-section-header { text-align: center; margin-bottom: 48px; position: relative; }
.vmg-section-title {
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 900;
    color: #fff !important;
    letter-spacing: .4px;
    margin-bottom: 0;
    line-height: 1.2;
}
.vmg-section-title::after {
    content: '';
    display: block;
    width: 56px; height: 3px;
    background: #c5a059;
    border-radius: 2px;
    margin: 14px auto 0;
}
.vmg-section-sub {
    font-size: 14px;
    color: rgba(255,255,255,.58) !important;
    margin-top: 16px;
    line-height: 1.6;
}

/* Glass cards on dark background */
.vmg-block {
    background: rgba(255,255,255,.07);
    border: 1.5px solid rgba(255,255,255,.13);
    border-radius: 18px;
    overflow: hidden;
    height: 100%;
    transition: transform .25s, box-shadow .25s, background .25s;
    box-shadow: 0 4px 28px rgba(0,0,0,.25);
}
.vmg-block:hover {
    transform: translateY(-7px);
    background: rgba(255,255,255,.12);
    box-shadow: 0 16px 44px rgba(0,0,0,.35);
}
.vmg-card-header {
    background: rgba(197,160,89,.14);
    border-bottom: 1px solid rgba(197,160,89,.22);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.vmg-icon-box {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, #c5a059 0%, #e8c97a 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(197,160,89,.45);
}
.vmg-icon-box i { font-size: 20px; color: #1a3a6c !important; }
.vmg-card-header h4 {
    color: #fff !important;
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.4px;
    margin: 0; padding: 0;
    background: transparent;
}
.vmg-card-header h4::after { display: none !important; }
.vmg-block h4::after { display: none !important; }
.vmg-card-body { padding: 22px 24px; }
.vmg-block p {
    font-size: 13.5px;
    color: rgba(255,255,255,.78) !important;
    line-height: 1.85;
    margin: 0;
}
.vmg-block em { color: #c5a059; font-style: italic; }

/* Bible verse */
.vmg-verse {
    text-align: center;
    margin-top: 52px;
    position: relative;
    padding: 0 24px;
}
.vmg-verse::before {
    content: '\201C';
    font-size: 80px;
    line-height: 1;
    color: rgba(197,160,89,.18);
    font-family: Georgia, serif;
    position: absolute;
    top: -20px; left: 50%;
    transform: translateX(-50%);
}
.vmg-verse blockquote {
    font-size: 15px;
    font-style: italic;
    color: rgba(255,255,255,.62);
    margin: 0; padding: 0;
    border: none;
    line-height: 1.75;
    position: relative;
}
.vmg-verse cite {
    display: block;
    margin-top: 10px;
    font-size: 11px;
    font-weight: 800;
    color: #c5a059;
    letter-spacing: 1.5px;
    font-style: normal;
    text-transform: uppercase;
}
</style>

<section class="vmg-section">
    <div class="container">
        <div class="vmg-section-header">
            <h2 class="vmg-section-title">Our Vision, Mission &amp; Philosophy</h2>
            <p class="vmg-section-sub">The guiding principles that shape every ILCian's growth and development</p>
        </div>

        <div class="row justify-content-center gy-4">

            <div class="col-md-4">
                <div class="vmg-block">
                    <div class="vmg-card-header">
                        <div class="vmg-icon-box"><i class="bi bi-eye-fill"></i></div>
                        <h4>Vision</h4>
                    </div>
                    <div class="vmg-card-body">
                        <p>Creating and sustaining an integrated, wholesome and appropriate environment for all phases of the learner's growth and development.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="vmg-block">
                    <div class="vmg-card-header">
                        <div class="vmg-icon-box"><i class="bi bi-bullseye"></i></div>
                        <h4>Mission</h4>
                    </div>
                    <div class="vmg-card-body">
                        <p>The IEMELIF Learning Center is a future-oriented school which gives opportunities to all children to discover their interest and GOD-GIVEN talents. The ILC aims to train and lead these children to have a "Desire to Learn" — following Jesus' example of love and care for children.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="vmg-block">
                    <div class="vmg-card-header">
                        <div class="vmg-icon-box"><i class="bi bi-book-fill"></i></div>
                        <h4>Philosophy</h4>
                    </div>
                    <div class="vmg-card-body">
                        <p>The IEMELIF Learning Center recognizes the love of Jesus for the children. The school is committed to serve, teach and care for the children, providing sufficient learning experiences to guide them to grow as total persons — spiritually, intellectually, emotionally, socially and physically.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="vmg-verse">
            <blockquote>"Let the little children come to me, and do not hinder them, for the kingdom of heaven belongs to such as these."</blockquote>
            <cite>— Matthew 19:14</cite>
        </div>
    </div>
</section>
</div>


<div class="skel-section" id="skel-footer" style="background:#1a3a6c;padding:40px 0 20px;">
    <div class="container">
        <div class="row g-4">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="col-md-3">
                <span class="skel-dark" style="height:16px;width:100px;display:block;border-radius:4px;margin-bottom:16px;"></span>
                <span class="skel-dark" style="height:12px;width:100%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                <span class="skel-dark" style="height:12px;width:88%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                <span class="skel-dark" style="height:12px;width:76%;display:block;border-radius:4px;"></span>
            </div>
            <?php endfor; ?>
        </div>
        <div style="margin-top:32px;text-align:center;">
            <span class="skel-dark" style="height:12px;width:300px;display:inline-block;border-radius:4px;"></span>
        </div>
    </div>
</div>


<div class="real-section" id="real-footer">
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
                        Science Education Affair 2025 — A Successful Event
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
                <div class="office-hours-row"><span>Monday – Friday</span><span>7:30 AM – 5:00 PM</span></div>
                <div class="office-hours-row"><span>Saturday - Sunday</span><span style="color:var(--ilc-gold);">Closed</span></div>
                <h6 class="mt-3">Visitor Counter</h6>
                <div style="display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px 14px;margin-top:8px;">
                    <i class="bi bi-people-fill" style="font-size:20px;color:var(--ilc-gold);flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.8px;">Total Visitors</div>
                        <div style="font-size:20px;font-weight:800;color:#fff;line-height:1.2;"><?php echo e(number_format($visitorCount ?? 0)); ?></div>
                    </div>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:5px;font-size:10px;color:rgba(255,255,255,.4);">
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 1.5s ease-in-out infinite;"></span>
                        Live
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom mt-4">
        &copy; <?php echo e(date('Y')); ?> IEMELIF Learning Center — General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<style>
/* Overlay — pure dark dim, no blur */
#ckOverlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.65);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}
#ckOverlay.ck-on {
    opacity: 1;
    visibility: visible;
}

/* Banner — always in DOM, starts off-screen below */
#ckBanner {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 99991;
    background: #1a3a6c;
    border-top: 4px solid #c5a059;
    padding: 26px 32px 22px;
    box-shadow: 0 -6px 40px rgba(0,0,0,.4);
    font-family: 'Open Sans', sans-serif;
    transform: translateY(100%);
    visibility: hidden;
    transition: transform 0.6s cubic-bezier(.22,.68,0,1.05), visibility 0.6s;
}
#ckBanner.ck-on {
    transform: translateY(0);
    visibility: visible;
}

/* Manage modal */
#ckModal {
    position: fixed; inset: 0;
    z-index: 99992;
    display: flex;
    align-items: center; justify-content: center;
    opacity: 0; visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s;
}
#ckModal.ck-on { opacity: 1; visibility: visible; }
#ckModalBox {
    background: #fff;
    border-radius: 16px;
    width: 100%; max-width: 520px;
    margin: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
    overflow: hidden;
    font-family: 'Open Sans', sans-serif;
    transform: translateY(16px);
    transition: transform 0.3s ease;
}
#ckModal.ck-on #ckModalBox { transform: translateY(0); }

.ck-toggle { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
.ck-toggle input { opacity:0; width:0; height:0; }
.ck-slider { position:absolute; inset:0; background:#cbd5e1; border-radius:24px; cursor:pointer; transition:.3s; }
.ck-slider:before { content:''; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.3s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.ck-toggle input:checked + .ck-slider { background:#1a3a6c; }
.ck-toggle input:checked + .ck-slider:before { transform:translateX(20px); }
.ck-toggle input:disabled + .ck-slider { background:#1a3a6c; opacity:.6; cursor:not-allowed; }

/* Cookie button hovers */
#ckBtnManage { transition: background .2s, border-color .2s, color .2s; }
#ckBtnManage:hover { background: rgba(255,255,255,.15) !important; border-color: rgba(255,255,255,.7) !important; color: #fff !important; }
#ckBtnReject { transition: background .2s, border-color .2s; }
#ckBtnReject:hover { background: rgba(255,255,255,.22) !important; border-color: rgba(255,255,255,.5) !important; }
#ckBtnAccept { transition: background .2s, transform .15s, box-shadow .2s; }
#ckBtnAccept:hover { background: #c5a059 !important; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(197,160,89,.5); }
#ckBtnAccept:active { transform: translateY(0); }
</style>


<div id="ckOverlay"></div>


<div id="ckBanner">
    <div style="max-width:1140px;margin:0 auto;">
        <div style="display:flex;align-items:flex-start;gap:18px;margin-bottom:18px;">
            <div style="background:rgba(197,160,89,.18);border-radius:12px;padding:11px;flex-shrink:0;">
                <i class="bi bi-cookie" style="font-size:30px;color:#c5a059;display:block;line-height:1;"></i>
            </div>
            <div style="flex:1;">
                <p style="margin:0 0 6px;color:#fff;font-size:16px;font-weight:800;letter-spacing:.3px;">This website uses cookies</p>
                <p style="margin:0;color:rgba(255,255,255,.82);font-size:13px;line-height:1.75;">
                    We use cookies to enhance your browsing experience, remember your preferences, analyze site traffic, and deliver personalized content.
                    Cookies help us understand how visitors interact with our site so we can continuously improve our services for ILC families.
                    You can choose which types of cookies to allow below. Read our
                    <a href="<?php echo e(route('privacy')); ?>" style="color:#c5a059;font-weight:600;text-decoration:underline;" target="_blank">Privacy Policy</a>
                    and <a href="<?php echo e(route('terms')); ?>" style="color:#c5a059;font-weight:600;text-decoration:underline;" target="_blank">Terms of Service</a> for details.
                </p>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;">
            <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-size:11px;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.2);"><i class="bi bi-shield-check me-1" style="color:#4ade80;"></i>Essential</span>
            <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-size:11px;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.2);"><i class="bi bi-bar-chart me-1" style="color:#60a5fa;"></i>Analytics</span>
            <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-size:11px;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.2);"><i class="bi bi-megaphone me-1" style="color:#f9a8d4;"></i>Marketing</span>
            <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-size:11px;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.2);"><i class="bi bi-person-check me-1" style="color:#c5a059;"></i>Preferences</span>
        </div>
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;justify-content:flex-end;">
            <button id="ckBtnManage" onclick="ckManage()" style="padding:11px 20px;border-radius:9px;font-size:13px;font-weight:600;background:transparent;border:1.5px solid rgba(255,255,255,.35);color:rgba(255,255,255,.9);cursor:pointer;font-family:'Open Sans',sans-serif;display:inline-flex;align-items:center;gap:7px;">
                <i class="bi bi-sliders"></i>Manage Cookies
            </button>
            <button id="ckBtnReject" onclick="ckReject()" style="padding:11px 22px;border-radius:9px;font-size:13px;font-weight:600;background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.25);color:#fff;cursor:pointer;font-family:'Open Sans',sans-serif;display:inline-flex;align-items:center;gap:7px;">
                <i class="bi bi-x-circle"></i>Reject All
            </button>
            <button id="ckBtnAccept" onclick="ckAccept()" style="padding:11px 28px;border-radius:9px;font-size:13px;font-weight:800;background:#c5a059;border:none;color:#1a3a6c;cursor:pointer;font-family:'Open Sans',sans-serif;display:inline-flex;align-items:center;gap:7px;">
                <i class="bi bi-check-circle-fill"></i>Accept All
            </button>
        </div>
    </div>
</div>


<div id="ckModal">
    <div id="ckModalBox">
        <div style="background:#1a3a6c;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="bi bi-sliders" style="color:#c5a059;font-size:20px;"></i>
                <span style="color:#fff;font-weight:700;font-size:15px;">Manage Cookie Preferences</span>
            </div>
            <button onclick="ckModalClose()" style="background:none;border:none;color:rgba(255,255,255,.65);font-size:22px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>
        </div>
        <div style="padding:22px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;border-bottom:1px solid #f1f5f9;">
                <div style="flex:1;margin-right:16px;"><p style="margin:0 0 3px;font-weight:700;color:#1a3a6c;font-size:13px;">Essential Cookies</p><p style="margin:0;font-size:12px;color:#64748b;line-height:1.5;">Required for the site to work. Cannot be turned off. Includes session and security cookies.</p></div>
                <label class="ck-toggle"><input type="checkbox" checked disabled><span class="ck-slider"></span></label>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;border-bottom:1px solid #f1f5f9;">
                <div style="flex:1;margin-right:16px;"><p style="margin:0 0 3px;font-weight:700;color:#1a3a6c;font-size:13px;">Analytics Cookies</p><p style="margin:0;font-size:12px;color:#64748b;line-height:1.5;">Help us understand how visitors use our website. All data is anonymous and aggregated.</p></div>
                <label class="ck-toggle"><input type="checkbox" id="ckAnalytics" checked><span class="ck-slider"></span></label>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;border-bottom:1px solid #f1f5f9;">
                <div style="flex:1;margin-right:16px;"><p style="margin:0 0 3px;font-weight:700;color:#1a3a6c;font-size:13px;">Marketing Cookies</p><p style="margin:0;font-size:12px;color:#64748b;line-height:1.5;">Used to show relevant content and school announcements. We never sell your data to third parties.</p></div>
                <label class="ck-toggle"><input type="checkbox" id="ckMarketing"><span class="ck-slider"></span></label>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;">
                <div style="flex:1;margin-right:16px;"><p style="margin:0 0 3px;font-weight:700;color:#1a3a6c;font-size:13px;">Preference Cookies</p><p style="margin:0;font-size:12px;color:#64748b;line-height:1.5;">Remember your settings like language and display options across visits.</p></div>
                <label class="ck-toggle"><input type="checkbox" id="ckPreferences" checked><span class="ck-slider"></span></label>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <button onclick="ckReject()" style="padding:10px 18px;border-radius:8px;font-size:12px;font-weight:600;background:#f1f5f9;border:none;color:#475569;cursor:pointer;font-family:'Open Sans',sans-serif;">Reject All</button>
                <button onclick="ckSave()" style="padding:10px 22px;border-radius:8px;font-size:12px;font-weight:700;background:#1a3a6c;border:none;color:#fff;cursor:pointer;font-family:'Open Sans',sans-serif;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-check-lg"></i>Save My Preferences</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var KEY = 'ilc_ck';

    function hideBanner() {
        document.getElementById('ckBanner').classList.remove('ck-on');
        document.getElementById('ckOverlay').classList.remove('ck-on');
        document.getElementById('ckModal').classList.remove('ck-on');
        sessionStorage.setItem(KEY, '1');
    }

    window.ckAccept  = function () { hideBanner(); };
    window.ckReject  = function () { hideBanner(); };
    window.ckSave    = function () { hideBanner(); };
    window.ckManage  = function () { document.getElementById('ckModal').classList.add('ck-on'); };
    window.ckModalClose = function () { document.getElementById('ckModal').classList.remove('ck-on'); };

    if (!sessionStorage.getItem(KEY)) {
        setTimeout(function () {
            document.getElementById('ckOverlay').classList.add('ck-on');
            document.getElementById('ckBanner').classList.add('ck-on');
        }, 1800);
    }
})();
</script>
</body>
</html>
</script><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\home.blade.php ENDPATH**/ ?>