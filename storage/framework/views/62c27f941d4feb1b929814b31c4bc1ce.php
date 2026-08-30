<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
  
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <link rel="stylesheet" href="<?php echo e(asset('css/news.css')); ?>?v=1780250000">    
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
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('academics')); ?>">Academics</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admission')); ?>">Enrollment</a></li>
                <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('announcements*') ? 'active' : ''); ?>" href="<?php echo e(route('announcements')); ?>">Announcements</a>
                </li>
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('news')); ?>">News</a></li>
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
        <span class="skel-dark" style="height:24px;width:170px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:110px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:36px 0;background:#f8fafc;">
        <div class="container"><div class="row g-4">
            <div class="col-lg-8">
                <div style="display:flex;gap:8px;margin-bottom:20px;">
                    <?php for($i=0;$i<5;$i++): ?><span class="skel" style="height:32px;width:68px;border-radius:20px;display:block;"></span><?php endfor; ?>
                </div>
                <div style="background:#fff;border-radius:12px;overflow:hidden;margin-bottom:22px;">
                    <span class="skel" style="height:210px;display:block;border-radius:0;"></span>
                    <div style="padding:20px;">
                        <span class="skel" style="height:11px;width:55px;display:block;border-radius:20px;margin-bottom:10px;"></span>
                        <span class="skel" style="height:22px;width:88%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                        <span class="skel" style="height:11px;width:190px;display:block;border-radius:4px;margin-bottom:12px;"></span>
                        <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                        <span class="skel" style="height:11px;width:82%;display:block;border-radius:4px;"></span>
                    </div>
                </div>
                <div class="row g-3">
                    <?php for($i=0;$i<4;$i++): ?>
                    <div class="col-md-6">
                        <div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #eee;">
                            <span class="skel" style="height:130px;display:block;border-radius:0;"></span>
                            <div style="padding:14px;">
                                <span class="skel" style="height:11px;width:55px;display:block;border-radius:20px;margin-bottom:7px;"></span>
                                <span class="skel" style="height:14px;width:88%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                                <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;"></span>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <?php for($i=0;$i<3;$i++): ?>
                <div style="background:#fff;border-radius:10px;padding:18px;margin-bottom:18px;border:1px solid #eee;">
                    <span class="skel" style="height:15px;width:130px;display:block;border-radius:4px;margin-bottom:13px;"></span>
                    <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                    <span class="skel" style="height:11px;width:88%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                    <span class="skel" style="height:11px;width:72%;display:block;border-radius:4px;"></span>
                </div>
                <?php endfor; ?>
            </div>
        </div></div>
    </div>
</div>

<div class="real-section" id="real-content">


<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">News &amp; Updates</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">News</span>
    </p>
</div>


<section class="news-page-section">
    <div class="container">
        <div class="row g-4">

            
            <div class="col-lg-8">

                <h2 class="news-section-title">Latest News</h2>
                <p class="news-section-subtitle">Stay updated with the latest happenings at IEMELIF Learning Center.</p>

                
                
                <div class="news-filter-tabs">
                    <a href="<?php echo e(route('news')); ?>" class="filter-tab <?php echo e(!request('category') ? 'active' : ''); ?>">All</a>
                    <a href="<?php echo e(route('news', ['category' => 'events'])); ?>" class="filter-tab <?php echo e(request('category') === 'events' ? 'active' : ''); ?>">Events</a>
                    <a href="<?php echo e(route('news', ['category' => 'academic'])); ?>" class="filter-tab <?php echo e(request('category') === 'academic' ? 'active' : ''); ?>">Academic</a>
                    <a href="<?php echo e(route('news', ['category' => 'activity'])); ?>" class="filter-tab <?php echo e(request('category') === 'activity' ? 'active' : ''); ?>">Activity</a>
                    <a href="<?php echo e(route('news', ['category' => 'achievement'])); ?>" class="filter-tab <?php echo e(request('category') === 'achievement' ? 'active' : ''); ?>">Achievement</a>
                    <a href="<?php echo e(route('news', ['category' => 'general'])); ?>" class="filter-tab <?php echo e(request('category') === 'general' ? 'active' : ''); ?>">General</a>
                </div>

                
                
                <?php if(isset($featuredNews) && $featuredNews): ?>
                <a href="<?php echo e(route('news.show', $featuredNews)); ?>" style="text-decoration:none;color:inherit;display:block;">
                <div class="featured-news-card" style="cursor:pointer;">
                    <div class="featured-img">
                        <div class="img-placeholder">
                            <?php if($featuredNews->image): ?>
                                <img src="<?php echo e(asset('storage/'.$featuredNews->image)); ?>" alt="<?php echo e($featuredNews->title); ?>">
                            <?php else: ?>
                                <img src="<?php echo e(asset('images/bg4.jpg')); ?>" alt="<?php echo e($featuredNews->title); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="featured-body">
                        <span class="news-category-badge <?php echo e($featuredNews->category); ?>"><?php echo e(ucfirst($featuredNews->category)); ?></span>
                        <h3><?php echo e($featuredNews->title); ?></h3>
                        <div class="news-meta">
                            <span><i class="bi bi-calendar3"></i> <?php echo e($featuredNews->created_at->format('F d, Y')); ?></span>
                        </div>
                        <p><?php echo e(Str::limit($featuredNews->body, 300)); ?></p>
                        <span class="btn-read-more" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;">Read More <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
                </a>
                <?php else: ?>
                
                <div class="featured-news-card">
                    <div class="featured-img">
                        <div class="img-placeholder">
                            <img src="/images/bg4.jpg" alt="">
                        </div>
                    </div>
                    <div class="featured-body">
                        <span class="news-category-badge events">Events</span>
                        <h3>IEMELIF Learning Center Officially Opens S.Y. 2026&ndash;2027 Under MATATAG Curriculum</h3>
                        <div class="news-meta">
                            <span><i class="bi bi-calendar3"></i> June 8, 2026</span>
                        </div>
                        <p>
                            IEMELIF Learning Center proudly opened Term 1 of School Year 2026&ndash;2027
                            on June 8, 2026 under the new MATATAG Curriculum framework. The Opening
                            Block kicked off with the Mandatory Learners' Health Assessment, Learners
                            and Parents' Orientation, and the start of BOSY Assessments.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if(isset($news) && $news->isNotEmpty()): ?>
                <div class="row g-3">
                    <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6">
                        <a href="<?php echo e(route('news.show', $article)); ?>" style="text-decoration:none;color:inherit;display:block;height:100%;">
                        <div class="news-grid-card" style="cursor:pointer;height:100%;">
                            <div class="grid-img">
                                <?php if($article->image): ?>
                                    <img src="<?php echo e(asset('storage/'.$article->image)); ?>" alt="<?php echo e($article->title); ?>">
                                <?php else: ?>
                                    <div class="img-placeholder"><img src="<?php echo e(asset('images/bg'.((($loop->index % 3)+1)).'.jpg')); ?>" alt="<?php echo e($article->title); ?>"></div>
                                <?php endif; ?>
                            </div>
                            <div class="grid-body">
                                <span class="news-category-badge <?php echo e($article->category); ?>"><?php echo e(ucfirst($article->category)); ?></span>
                                <h5><?php echo e($article->title); ?></h5>
                                <p><?php echo e(Str::limit($article->body, 120)); ?></p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> <?php echo e($article->created_at->format('M d, Y')); ?></span>
                                <span class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></span>
                            </div>
                        </div>
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="news-pagination"><?php echo e($news->links()); ?></div>
                <?php else: ?>
                
                <div class="row g-3">

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg2.jpg" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge academic">Academic</span>
                                <h5>ILC Conducts Brigada Eskwela 2026 &mdash; Community Unites for School Readiness</h5>
                                <p>Students, parents, teachers, and community volunteers joined hands during Brigada Eskwela (June 1&ndash;5) to prepare the school for the opening of S.Y. 2026&ndash;2027.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Jun 5, 2026</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg3.jpg" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge events">Events</span>
                                <h5>National Teachers' Month Culmination & World Teachers' Day Celebration</h5>
                                <p>ILC honored its dedicated teachers on October 5, 2026 as part of the culmination of National Teachers' Month and World Teachers' Day celebration.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Oct 5, 2026</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg1.jpg" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge academic">Academic</span>
                                <h5>Term 1 PTA Meeting & Report Card Distribution &mdash; September 9, 2026</h5>
                                <p>Parents and guardians gathered for the Term 1 PTA meeting and report card distribution on September 9, 2026 as part of the End-of-Term 1 Block.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Sep 9, 2026</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg2.jpg" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge events">Events</span>
                                <h5>ILC Year-End Activity & Term 2 Wellness Break &mdash; December 16&ndash;18, 2026</h5>
                                <p>The school held its Year-End Activity on December 16 followed by INSET and the Wellness Break for learners and teachers on December 17&ndash;18, 2026.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Dec 16, 2026</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg1.jpg" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge academic">Academic</span>
                                <h5>EOSY Rites 2027 &mdash; Celebrating Moving Up & Graduating Learners</h5>
                                <p>ILC held its End-of-School Year Rites on April 6&ndash;7, 2027, recognizing moving up and graduating learners for their dedication and achievements throughout S.Y. 2026&ndash;2027.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Apr 7, 2027</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="news-grid-card">
                            <div class="grid-img"><div class="img-placeholder"><img src="/images/bg.png" alt=""></div></div>
                            <div class="grid-body">
                                <span class="news-category-badge general">General</span>
                                <h5>Term 3 PTA Meeting & End-of-Year Report Card Distribution &mdash; April 8, 2027</h5>
                                <p>The final PTA meeting of S.Y. 2026&ndash;2027 and distribution of Term 3 report cards was held on April 8, 2027, marking the official close of the school year.</p>
                            </div>
                            <div class="grid-footer">
                                <span class="news-date"><i class="bi bi-calendar3"></i> Apr 8, 2027</span>
                                <a href="#" class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                </div>

                
                
                <div class="news-pagination">
                    <a href="#" class="page-btn active">1</a>
                    <a href="#" class="page-btn">2</a>
                    <a href="#" class="page-btn">3</a>
                    <a href="#" class="page-btn"><i class="bi bi-chevron-right"></i></a>
                </div>
                <?php endif; ?>

            </div>

            
            <div class="col-lg-4">
                <div class="news-sidebar">

                    
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Recent Posts</h6>
                        <?php if(isset($recentNews) && $recentNews->isNotEmpty()): ?>
                            <?php $__currentLoopData = $recentNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('news.show', $rn)); ?>" style="text-decoration:none;">
                                <div class="recent-post-item">
                                    <div class="recent-post-img">
                                        <?php if($rn->image): ?>
                                            <img src="<?php echo e(asset('storage/'.$rn->image)); ?>" alt="<?php echo e($rn->title); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
                                        <?php else: ?>
                                            <div class="img-placeholder"><i class="bi bi-image"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="recent-post-body">
                                        <span class="post-title"><?php echo e(Str::limit($rn->title, 50)); ?></span>
                                        <div class="post-date"><i class="bi bi-calendar3" style="color:var(--ilc-gold);margin-right:3px;"></i> <?php echo e($rn->created_at->format('M d, Y')); ?></div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        
                        <?php $__currentLoopData = [
                            ['title' => '35 Years of Excellence in Education and Faith', 'date' => 'Mar 15, 2026', 'img' => 'bg4.jpg'],
                            ['title' => 'Science Education Affair 2025', 'date' => 'Feb 28, 2026', 'img' => 'bg2.jpg'],
                            ['title' => 'Welcome Back to School, ILCians!', 'date' => 'Jan 10, 2026', 'img' => 'bg3.jpg'],
                            ['title' => 'Parent-Teacher Conference 3rd Quarter', 'date' => 'Jan 23, 2026', 'img' => 'bg.png'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staticPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="recent-post-item">
                            <div class="recent-post-img">
                                <div class="img-placeholder"><img src="<?php echo e(asset('images/'.$staticPost['img'])); ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"></div>
                            </div>
                            <div class="recent-post-body">
                                <span class="post-title"><?php echo e($staticPost['title']); ?></span>
                                <div class="post-date"><i class="bi bi-calendar3" style="color:var(--ilc-gold);margin-right:3px;"></i> <?php echo e($staticPost['date']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Categories</h6>
                        <a href="<?php echo e(route('news')); ?>" class="category-item <?php echo e(!request('category') ? 'active' : ''); ?>">
                            <span><i class="bi bi-grid me-2" style="color:var(--ilc-gold);"></i>All</span>
                            <span class="cat-count"><?php echo e(isset($categories) ? $categories->sum('total') : 0); ?></span>
                        </a>
                        <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('news', ['category' => $cat->category])); ?>" class="category-item <?php echo e(request('category') === $cat->category ? 'active' : ''); ?>">
                                <span><i class="bi bi-tag me-2" style="color:var(--ilc-gold);"></i><?php echo e(ucfirst($cat->category)); ?></span>
                                <span class="cat-count"><?php echo e($cat->total); ?></span>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        
                        <?php $__currentLoopData = ['events' => 4, 'academic' => 5, 'activity' => 3, 'achievement' => 2, 'general' => 6]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('news', ['category' => $cat])); ?>" class="category-item <?php echo e(request('category') === $cat ? 'active' : ''); ?>">
                            <span><i class="bi bi-tag me-2" style="color:var(--ilc-gold);"></i><?php echo e(ucfirst($cat)); ?></span>
                            <span class="cat-count"><?php echo e($count); ?></span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Announcements</h6>
                        <?php if(isset($sidebarAnns) && $sidebarAnns->isNotEmpty()): ?>
                            <?php $__currentLoopData = $sidebarAnns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('announcements.show', $ann)); ?>" style="text-decoration:none;">
                                <div class="ann-widget-item">
                                    <div class="ann-widget-date">
                                        <span class="ann-day"><?php echo e($ann->created_at->format('d')); ?></span>
                                        <span class="ann-mon"><?php echo e($ann->created_at->format('M')); ?></span>
                                    </div>
                                    <div class="ann-widget-title"><?php echo e(Str::limit($ann->title, 50)); ?></div>
                                </div>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        
                        <?php $__currentLoopData = [
                            ['day'=>'15','mon'=>'Jun','title'=>'School Opening Day &mdash; S.Y. 2026&ndash;2027'],
                            ['day'=>'10','mon'=>'Jun','title'=>'Enrollment Period is Now Open'],
                            ['day'=>'05','mon'=>'Jun','title'=>'General Assembly for All Parents'],
                            ['day'=>'01','mon'=>'Jun','title'=>'Card Signing &mdash; 4th Quarter'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="ann-widget-item">
                            <div class="ann-widget-date">
                                <span class="ann-day"><?php echo e($sa['day']); ?></span>
                                <span class="ann-mon"><?php echo e($sa['mon']); ?></span>
                            </div>
                            <div class="ann-widget-title"><?php echo e($sa['title']); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

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

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
<script>(function(){var S=[['real-content','skel-content'],['real-footer','skel-footer']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();</script>
</body>
</html><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/news.blade.php ENDPATH**/ ?>