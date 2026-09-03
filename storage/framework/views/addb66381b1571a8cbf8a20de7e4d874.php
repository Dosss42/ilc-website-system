<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($news->title); ?> &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <link rel="stylesheet" href="<?php echo e(asset('css/news.css')); ?>?v=1780250000">
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780250000">
    <script>
        // Show real content immediately (no skeleton delay needed for detail page)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.skel-section').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.real-section').forEach(el => el.style.display = '');
        });
    </script>
    <style>
        .article-hero { width:100%;max-height:420px;object-fit:cover;border-radius:14px;margin-bottom:28px;box-shadow:0 4px 20px rgba(0,0,0,.12); }
        .article-hero-placeholder { width:100%;height:320px;border-radius:14px;background:linear-gradient(135deg,#e8f0fb,#f0f6ff);display:flex;align-items:center;justify-content:center;margin-bottom:28px; }
        .article-body { font-size:15px;line-height:1.9;color:#374151; }
        .article-body p { margin-bottom:18px; }
        .article-meta-bar { display:flex;flex-wrap:wrap;gap:16px;align-items:center;padding:14px 0;border-top:1px solid #f0f0f0;border-bottom:1px solid #f0f0f0;margin-bottom:28px; }
        .article-meta-bar span { font-size:13px;color:#64748b;display:flex;align-items:center;gap:5px; }
        .related-card { display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0; }
        .related-card:last-child { border-bottom:none; }
        .related-img { width:70px;height:54px;border-radius:8px;object-fit:cover;flex-shrink:0;background:#e8f0fb; }
        .related-body a { font-size:13px;font-weight:600;color:#1a3a6c;text-decoration:none;line-height:1.4;display:block; }
        .related-body a:hover { color:#2471a3; }
        .related-date { font-size:11px;color:#94a3b8;margin-top:3px; }
        .back-link { display:inline-flex;align-items:center;gap:7px;color:#1a3a6c;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:20px;transition:color .15s; }
        .back-link:hover { color:#2471a3; }
    </style>
</head>
<body>

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


<div style="background:var(--ilc-blue);padding:28px 0;text-align:center;border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff;font-size:22px;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin:0;">News &amp; Updates</h2>
    <p style="color:rgba(255,255,255,0.6);font-size:12px;margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5);text-decoration:none;">Home</a>
        <span style="margin:0 8px;color:rgba(255,255,255,0.4);">&mdash;</span>
        <a href="<?php echo e(route('news')); ?>" style="color:rgba(255,255,255,0.5);text-decoration:none;">News</a>
        <span style="margin:0 8px;color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;"><?php echo e(Str::limit($news->title, 40)); ?></span>
    </p>
</div>

<section class="news-page-section">
    <div class="container">
        <div class="row g-4">

            
            <div class="col-lg-8">
                <a href="<?php echo e(route('news')); ?>" class="back-link">
                    <i class="bi bi-arrow-left"></i> Back to News
                </a>

                
                <span class="news-category-badge <?php echo e($news->category); ?>" style="margin-bottom:12px;display:inline-block;"><?php echo e(ucfirst($news->category)); ?></span>
                <h1 style="font-size:26px;font-weight:800;color:#1a3a6c;line-height:1.3;margin-bottom:14px;"><?php echo e($news->title); ?></h1>

                
                <div class="article-meta-bar">
                    <span><i class="bi bi-calendar3"></i> <?php echo e($news->created_at->format('F d, Y')); ?></span>
                    <span><i class="bi bi-tag-fill"></i> <?php echo e(ucfirst($news->category)); ?></span>
                </div>

                
                <?php if($news->image): ?>
                    <img src="<?php echo e(asset('storage/'.$news->image)); ?>" alt="<?php echo e($news->title); ?>" class="article-hero">
                <?php else: ?>
                    <div class="article-hero-placeholder">
                        <i class="bi bi-newspaper" style="font-size:64px;color:#b8cfe8;"></i>
                    </div>
                <?php endif; ?>

                
                <div class="article-body">
                    <?php echo nl2br(e($news->body)); ?>

                </div>

                
                <div style="margin-top:32px;padding-top:20px;border-top:1px solid #f0f0f0;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:600;color:#64748b;">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(request()->fullUrl())); ?>" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1877f2;color:#fff;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;">
                       <i class="bi bi-facebook"></i> Facebook
                    </a>
                </div>

                
                <?php if($relatedNews->isNotEmpty()): ?>
                <div style="margin-top:36px;">
                    <h5 style="font-weight:700;color:#1a3a6c;margin-bottom:16px;"><i class="bi bi-newspaper me-2"></i>Related Articles</h5>
                    <?php $__currentLoopData = $relatedNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="related-card">
                        <?php if($r->image): ?>
                            <img src="<?php echo e(asset('storage/'.$r->image)); ?>" alt="<?php echo e($r->title); ?>" class="related-img">
                        <?php else: ?>
                            <div class="related-img" style="display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-image" style="color:#b8cfe8;font-size:20px;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="related-body">
                            <a href="<?php echo e(route('news.show', $r)); ?>"><?php echo e($r->title); ?></a>
                            <div class="related-date"><i class="bi bi-calendar3 me-1"></i><?php echo e($r->created_at->format('M d, Y')); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-4">
                <div class="news-sidebar">

                    
                    <?php if(isset($sidebarAnns) && $sidebarAnns->isNotEmpty()): ?>
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Recent Announcements</h6>
                        <?php $__currentLoopData = $sidebarAnns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('announcements.show', $ann)); ?>" style="text-decoration:none;">
                            <div class="ann-widget-item">
                                <div class="ann-widget-date">
                                    <span class="ann-day"><?php echo e($ann->created_at->format('d')); ?></span>
                                    <span class="ann-mon"><?php echo e($ann->created_at->format('M')); ?></span>
                                </div>
                                <div class="ann-widget-title"><?php echo e(Str::limit($ann->title, 55)); ?></div>
                            </div>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>

                    
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Categories</h6>
                        <a href="<?php echo e(route('news')); ?>" class="category-item <?php echo e(!request('category') ? 'active' : ''); ?>">
                            <span><i class="bi bi-grid me-2" style="color:var(--ilc-gold);"></i>All</span>
                        </a>
                        <?php $__currentLoopData = ['academic','events','activity','achievement','general']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('news', ['category' => $cat])); ?>" class="category-item <?php echo e(request('category') === $cat ? 'active' : ''); ?>">
                            <span><i class="bi bi-tag me-2" style="color:var(--ilc-gold);"></i><?php echo e(ucfirst($cat)); ?></span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\news-show.blade.php ENDPATH**/ ?>