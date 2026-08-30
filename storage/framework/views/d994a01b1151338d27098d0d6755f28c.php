<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <link rel="stylesheet" href="<?php echo e(asset('css/about.css')); ?>?v=1780250000">  
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


<header class="top-header" >
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
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('about')); ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('academics')); ?>">Academics</a></li>
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
        <span class="skel-dark" style="height:24px;width:160px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:110px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:50px 0;background:#fff;">
        <div class="container"><div class="row g-5 align-items-center">
            <div class="col-lg-5"><span class="skel" style="height:280px;display:block;border-radius:10px;"></span></div>
            <div class="col-lg-7">
                <span class="skel" style="height:26px;width:180px;display:block;border-radius:4px;margin-bottom:10px;"></span>
                <span class="skel" style="height:13px;width:120px;display:block;border-radius:4px;margin-bottom:18px;"></span>
                <?php for($i=0;$i<4;$i++): ?><span class="skel" style="height:12px;width:100%;display:block;border-radius:4px;margin-bottom:7px;"></span><?php endfor; ?>
                <span class="skel" style="height:12px;width:68%;display:block;border-radius:4px;margin-bottom:22px;"></span>
                <span class="skel" style="height:36px;width:200px;display:block;border-radius:20px;"></span>
            </div>
        </div></div>
    </div>
    <div style="padding:48px 0;background:#f6f8fb;">
        <div class="container">
            <span class="skel" style="height:24px;width:240px;display:block;border-radius:4px;margin:0 auto 10px;"></span>
            <span class="skel" style="height:13px;width:170px;display:block;border-radius:4px;margin:0 auto 28px;"></span>
            <div class="row g-4"><?php for($i=0;$i<3;$i++): ?>
                <div class="col-md-4"><div style="background:#fff;border-radius:12px;padding:28px;text-align:center;">
                    <span class="skel" style="width:50px;height:50px;border-radius:50%;display:block;margin:0 auto 14px;"></span>
                    <span class="skel" style="height:18px;width:80px;display:block;border-radius:4px;margin:0 auto 12px;"></span>
                    <?php for($j=0;$j<3;$j++): ?><span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span><?php endfor; ?>
                </div></div><?php endfor; ?>
            </div>
        </div>
    </div>
    <div style="padding:50px 0;background:#fff;">
        <div class="container">
            <span class="skel" style="height:24px;width:210px;display:block;border-radius:4px;margin:0 auto 10px;"></span>
            <span class="skel" style="height:13px;width:160px;display:block;border-radius:4px;margin:0 auto 28px;"></span>
            <div class="row g-5">
                <div class="col-lg-3 col-md-4" style="text-align:center;">
                    <span class="skel" style="width:130px;height:130px;border-radius:50%;display:block;margin:0 auto 12px;"></span>
                    <span class="skel" style="height:13px;width:140px;display:block;border-radius:4px;margin:0 auto 8px;"></span>
                    <span class="skel" style="height:11px;width:100px;display:block;border-radius:4px;margin:0 auto;"></span>
                </div>
                <div class="col-lg-9 col-md-8">
                    <span class="skel" style="height:60px;width:50px;display:block;border-radius:4px;margin-bottom:14px;"></span>
                    <?php for($i=0;$i<5;$i++): ?><span class="skel" style="height:12px;width:100%;display:block;border-radius:4px;margin-bottom:7px;"></span><?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="real-section" id="real-content">


<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">About Us</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">About</span>
    </p>
</div>


<section class="history-section">
    <div class="container">
        <div class="row align-items-center g-5">

            
            <div class="col-lg-5">
                <div class="history-img-box">
                   
                         <img src="/images/bg.png" alt="School">
                </div>
            </div>

            
            <div class="col-lg-7">
                <div class="history-text">
                    <h2 class="section-title">School History</h2>
                    <p class="section-subtitle">How it all began</p>

                    
                    <p>
                        The IEMELIF Learning Center &mdash; General Trias National ILC was established
                        with a vision to provide quality Christian education to the youth of
                        General Trias, Cavite. Founded under the guidance of the Iglesia
                        Evangelica Metodista En Las Islas Filipinas (IEMELIF), the school
                        has been a beacon of faith and academic excellence since its inception.
                    </p>
                    <p>
                        Over the decades, the institution has grown from a small community school
                        to a nationally recognized learning center, nurturing generations of
                        students to become upright citizens and servant leaders in their
                        communities and in the church.
                    </p>
                    <p>
                        Today, the school continues to uphold its founding principles &mdash;
                        combining rigorous academic instruction with strong spiritual formation,
                        guided always by the Word of God.
                    </p>

                    <div class="history-badge">
                        <i class="bi bi-award-fill"></i>
                        
                        Established Since Feb 1995
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="vmg-about-section">
    <div class="container">

        <h2 class="section-title">Vision, Mission &amp; Philosophy</h2>
        <p class="section-subtitle">The foundation of everything we do</p>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="vmg-card">
                    <div class="vmg-icon"><i class="bi bi-eye-fill"></i></div>
                    <h4>Vision</h4>
                    
                    <p>
                        Creating and sustaining as integrated wholesome and appropriate enironment for
                        all phases of the learne's growth and development.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="vmg-card">
                    <div class="vmg-icon"><i class="bi bi-bullseye"></i></div>
                    <h4>Mission</h4>
                    
                    <p>
                    The IEMELIF Learning Center is a future-oriented school which gives opportunities 
                    to all children to discover their interest and GOD-GIVEN talents that will be explored and developed as they grow up and becomme successful individuals.
                    The ILC aims to train and lead these children to have a " Desire to LEarn" and integrate everything they have acquired/obtained in their daily experiences.
                    The ultimate goal of this school is to emulate and follow Juses example as He showed His love and care for the children.

                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="vmg-card">
                    <div class="vmg-icon"><i class="bi bi-flag-fill"></i></div>
                    <h4>Philosophy</h4>
                    
                    <p>
                    The IEMELIF Learning Center (Gen.Tinio IEMELIF Church)Inc recognizes the love of Jesus for the children. As He said," Let the children come to me." (Mattnew 19:14)
                    As Jesus took the children in His arms, in the same way, the school is committed to serve, teach and care for the children.
                    This school aims to provide sufficient learning experiences to guide them to grow as a total person-spiritually, intellectually, emotionally,socially and physically. 
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="principal-section">
    <div class="container">

        <h2 class="section-title">Principal's Message</h2>
        <p class="section-subtitle">A word from our school head</p>

        <div class="row g-5 align-items-start">

            
            <div class="col-lg-3 col-md-4">
                <div class="principal-photo-box">

                    <div class="principal-photo">
                    
                             <img src="/images/oic.jpg" alt="Principal">
                     
                    </div>

                    
                    <div class="principal-name">MRS. Teofila Guillermo</div>
                    
                    
                    
                    
                    <div class="principal-position">School Principal / Head</div>
                    <div class="principal-signature">&mdash;MRS. Teofila Guillermo</div>

                </div>
            </div>

            
            <div class="col-lg-9 col-md-8">
                <div class="principal-message-box">
                    <div class="quote-icon">"</div>

                    
                    <p>
                        Welcome to the IEMELIF Learning Center &mdash; 
                        It is with great joy and deep gratitude that I extend my warmest
                        greetings to all our students, parents, faculty, staff, and
                        stakeholders who form the heart of our beloved school community.
                    </p>
                    <p>
                        Our school stands as a testament to the power of faith, perseverance,
                        and the unwavering belief that every child deserves a quality education
                        anchored in Christian values. We are committed to nurturing not just
                        the minds of our learners, but also their hearts and spirits.
                    </p>
                    <p>
                        As we continue this journey together, let us keep our eyes fixed on
                        our shared vision &mdash; to raise a generation of young men and women who
                        are academically excellent, morally upright, and deeply rooted in
                        the Word of God.
                    </p>
                    <p>Together, we can make a difference. God bless us all.</p>
                </div>
            </div>

        </div>
    </div>
</section>

</div>

<div class="skel-section" id="skel-footer" style="background:#1a3a6c;padding:38px 0 20px;">
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
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 1.5s ease-in-out infinite;"></span>
                        Live
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom mt-4">
        &copy; <?php echo e(date('Y')); ?> IEMELIF Learning Center &mdash; General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();
</script>
<script>
(function(){var S=[['real-content','skel-content'],['real-footer','skel-footer']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();
</script>
</body>
</html><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/about.blade.php ENDPATH**/ ?>