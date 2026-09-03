<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($announcement->title); ?> &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>?v=1780250000">
    <link rel="stylesheet" href="<?php echo e(asset('css/announcements.css')); ?>?v=1780250000">
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="<?php echo e(asset('css/ilc-typography.css')); ?>?v=1780250000">
    <style>
        .ann-article-hero { width:100%;max-height:380px;object-fit:cover;border-radius:14px;margin-bottom:28px;box-shadow:0 4px 20px rgba(0,0,0,.1); }
        .ann-body { font-size:15px;line-height:1.9;color:#374151; }
        .ann-body p { margin-bottom:18px; }
        .ann-meta-bar { display:flex;flex-wrap:wrap;gap:16px;align-items:center;padding:14px 0;border-top:1px solid #f0f0f0;border-bottom:1px solid #f0f0f0;margin-bottom:28px; }
        .ann-meta-bar span { font-size:13px;color:#64748b;display:flex;align-items:center;gap:5px; }
        .related-ann-card { display:flex;gap:14px;padding:14px;background:#f8faff;border-radius:10px;margin-bottom:10px;text-decoration:none;transition:background .15s; }
        .related-ann-card:hover { background:#e8f0fb; }
        .related-ann-date { min-width:50px;text-align:center;background:#1a3a6c;border-radius:8px;padding:8px 6px;color:#fff;flex-shrink:0; }
        .related-ann-date .r-day { display:block;font-size:20px;font-weight:800;line-height:1; }
        .related-ann-date .r-mon { display:block;font-size:10px;text-transform:uppercase;letter-spacing:.5px;opacity:.8; }
        .related-ann-title { font-size:13px;font-weight:600;color:#1a3a6c;line-height:1.4;margin:0; }
        .related-ann-cat { font-size:11px;color:#94a3b8;margin-top:4px; }
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
                <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('announcements')); ?>">Announcements</a></li>
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


<div style="background:var(--ilc-blue);padding:28px 0;text-align:center;border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff;font-size:22px;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin:0;">Announcements</h2>
    <p style="color:rgba(255,255,255,0.6);font-size:12px;margin:6px 0 0;">
        <a href="<?php echo e(route('home')); ?>" style="color:rgba(255,255,255,0.5);text-decoration:none;">Home</a>
        <span style="margin:0 8px;color:rgba(255,255,255,0.4);">&mdash;</span>
        <a href="<?php echo e(route('announcements')); ?>" style="color:rgba(255,255,255,0.5);text-decoration:none;">Announcements</a>
        <span style="margin:0 8px;color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;"><?php echo e(Str::limit($announcement->title, 40)); ?></span>
    </p>
</div>

<section class="ann-page-section">
    <div class="container">
        <div class="row g-4">

            
            <div class="col-lg-8">
                <a href="<?php echo e(route('announcements')); ?>" class="back-link">
                    <i class="bi bi-arrow-left"></i> Back to Announcements
                </a>

                
                <span class="ann-type-pill <?php echo e($announcement->category); ?>" style="margin-bottom:12px;display:inline-block;">
                    <i class="bi bi-pin-fill me-1" style="color:#c5a059;font-style:normal;"></i> <?php echo e(ucfirst($announcement->category)); ?>

                </span>

                
                <h1 style="font-size:26px;font-weight:800;color:#1a3a6c;line-height:1.3;margin-bottom:14px;">
                    <?php echo e($announcement->title); ?>

                </h1>

                
                <div class="ann-meta-bar">
                    <span><i class="bi bi-calendar3"></i> <?php echo e($announcement->created_at->format('F d, Y')); ?></span>
                    <span><i class="bi bi-people-fill"></i> <?php echo e(ucfirst($announcement->audience)); ?></span>
                </div>

                
                <?php if($announcement->image): ?>
                    <img src="<?php echo e(asset('storage/'.$announcement->image)); ?>" alt="<?php echo e($announcement->title); ?>" class="ann-article-hero">
                <?php endif; ?>

                
                <div class="ann-body">
                    <?php echo nl2br(e($announcement->content)); ?>

                </div>

                
                <div style="margin-top:32px;padding-top:20px;border-top:1px solid #f0f0f0;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:600;color:#64748b;">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(request()->fullUrl())); ?>" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1877f2;color:#fff;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;">
                       <i class="bi bi-facebook"></i> Facebook
                    </a>
                </div>

                
                <?php if($relatedAnns->isNotEmpty()): ?>
                <div style="margin-top:36px;">
                    <h5 style="font-weight:700;color:#1a3a6c;margin-bottom:16px;"><i class="bi bi-megaphone me-2"></i>Related Announcements</h5>
                    <?php $__currentLoopData = $relatedAnns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('announcements.show', $r)); ?>" class="related-ann-card">
                        <div class="related-ann-date">
                            <span class="r-day"><?php echo e($r->created_at->format('d')); ?></span>
                            <span class="r-mon"><?php echo e($r->created_at->format('M')); ?></span>
                        </div>
                        <div>
                            <p class="related-ann-title"><?php echo e($r->title); ?></p>
                            <p class="related-ann-cat"><?php echo e(ucfirst($r->category)); ?></p>
                        </div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-4">
                <div class="news-sidebar">
                    <div class="sidebar-widget">
                        <h6 class="sidebar-widget-title">Filter by Category</h6>
                        <a href="<?php echo e(route('announcements')); ?>" class="category-item <?php echo e(!request('category') ? 'active' : ''); ?>">
                            <span><i class="bi bi-grid me-2" style="color:var(--ilc-gold);"></i>All Announcements</span>
                        </a>
                        <?php $__currentLoopData = ['general','academic','reminder','activity','enrollment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('announcements', ['category' => $cat])); ?>" class="category-item">
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
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\announcements-show.blade.php ENDPATH**/ ?>