<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Finance Portal'); ?> - IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/global-scrollbar.css">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --blue: #1a3a6c;
            --gold: #c5a059;
            --blue-light: #2471a3;
            --gold-light: #d4b36a;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --sidebar-w: 240px;
            --topbar-h: 62px;
            --border: #e5e7eb;
            --muted: #888;
            --text: #1a3a6c;
            --green: #28a745;
            --red: #dc3545;
            --blue-pale: #e8f0fb;
        }

        * { font-family: 'Poppins', sans-serif; }

        body {
            background: #f5f6fa;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .topbar-brand {
            width: var(--sidebar-w); height: 100%;
            background: var(--blue);
            display: flex; align-items: center;
            gap: 10px; padding: 0 16px; flex-shrink: 0;
        }

        .topbar-brand i {
            font-size: 28px;
            color: var(--gold);
        }

        .topbar-brand span {
            font-weight: 600;
            font-size: 16px;
            color: #fff;
        }

        .topbar-center {
            flex: 1; display: flex;
            align-items: center; padding: 0 20px;
        }

        .topbar-right {
            display: flex; align-items: center;
            gap: 12px; padding-right: 20px; margin-left: auto;
        }

        .user-chip { display:flex;align-items:center;gap:10px;cursor:pointer;padding:6px 12px;border-radius:10px;border:1.5px solid #e2e8f0;transition:all 0.2s;background:#fff;-webkit-user-select:none;user-select:none; }
        .user-chip:hover { border-color:var(--blue);background:#f5f8ff; }
        .user-avatar { width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1a3a6c,#2471a3);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;font-weight:700;font-size:14px;color:#fff; }
        .user-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .user-chip-name { font-size:13px;font-weight:600;color:#2d3748;line-height:1.2; }
        .user-chip-role { font-size:10px;color:#718096; }
        .user-chip-caret { font-size:11px;color:#aaa;transition:transform 0.2s;margin-left:2px; }
        .dropdown.show .user-chip-caret { transform:rotate(180deg); }
        .user-chip-dropdown { min-width:270px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 8px 32px rgba(0,0,0,.12),0 2px 8px rgba(0,0,0,.06);padding:0;overflow:hidden;margin-top:6px!important; }
        .ucd-header { display:flex;align-items:center;gap:12px;padding:16px;background:linear-gradient(135deg,#f0f5ff 0%,#fff 100%);border-bottom:1px solid #f0f0f0; }
        .ucd-avatar { width:46px;height:46px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#1a3a6c,#2471a3);display:flex;align-items:center;justify-content:center;overflow:hidden;font-weight:700;font-size:18px;color:#fff;box-shadow:0 2px 8px rgba(26,58,108,.25); }
        .ucd-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .ucd-info { flex:1;min-width:0; }
        .ucd-name  { font-weight:700;font-size:13px;color:#1a3a6c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .ucd-email { font-size:11px;color:#64748b;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .ucd-badge { display:inline-block;margin-top:5px;background:#e8f0fb;color:#1a3a6c;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap; }
        .ucd-body  { padding:6px 0; }
        .ucd-item  { display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:#374151;text-decoration:none;cursor:pointer;transition:background 0.15s;border:none;background:none;width:100%; }
        .ucd-item:hover { background:#f5f8ff;color:#1a3a6c; }
        .ucd-item i { font-size:15px;width:18px;text-align:center;flex-shrink:0; }
        .ucd-divider { height:1px;background:#f0f0f0;margin:2px 0; }
        .ucd-footer { padding:6px 0; }
        .ucd-logout { color:#dc2626!important; }
        .ucd-logout:hover { background:#fff5f5!important;color:#b91c1c!important; }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: var(--topbar-h);
            width: var(--sidebar-w);
            height: calc(100vh - var(--topbar-h));
            background: #1a3a6c;
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar-menu { padding: 16px 0; }

        .menu-section {
            padding: 8px 20px;
            color: rgba(255,255,255,0.35);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-size: 13px;
            font-weight: 400;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--gold);
        }

        .menu-item i {
            font-size: 20px;
            width: 26px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 24px 32px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--blue);
        }

        /* ── Shared utility classes (match admin dashboard) ── */
        .stat-card{display:flex;align-items:center;gap:16px;padding:20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
        .stat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:24px;}
        .stat-icon.blue{background:#e3f2fd;color:#1976d2;}
        .stat-icon.green{background:#e8f5e9;color:#2e7d32;}
        .stat-icon.gold{background:#fff8e1;color:#f57c00;}
        .stat-icon.red{background:#ffebee;color:#c62828;}
        .stat-value{font-size:24px;font-weight:700;color:var(--blue);}
        .stat-label{font-size:12px;color:#666;margin-top:2px;}
        .content-card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:24px;}
        .content-card-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f0f0f0;}
        .content-card-header h6{margin:0;font-size:14px;font-weight:700;color:var(--blue);}
        .section-header{margin-bottom:24px;}
        .section-header h1{font-size:22px;font-weight:700;color:var(--blue);margin-bottom:4px;}
        .section-header p{color:#666;font-size:14px;margin:0;}
        .dash-table{width:100%;border-collapse:collapse;}
        .dash-table th{text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:0.5px;background:#f8f9fa;border-bottom:2px solid #e0e0e0;}
        .dash-table td{padding:12px 16px;font-size:13px;border-bottom:1px solid #f5f5f5;vertical-align:middle;}
        .dash-table tr:hover td{background:#fafbff;}
        .action-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s;font-size:15px;}
        .action-btn.view{background:#e3f2fd;color:#1976d2;}
        .action-btn.edit{background:#e8f5e9;color:#2e7d32;}
        .action-btn.delete{background:#ffebee;color:#c62828;}
        .action-btn:hover{opacity:0.8;}
        .grade-chip{display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#e8f0fe;color:#1565c0;border:1px solid #aac4f5;}
        .module-toolbar{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #f0f0f0;flex-wrap:wrap;}
        .toolbar-search{display:flex;align-items:center;gap:8px;flex:1;min-width:200px;background:#f5f5f5;border-radius:8px;padding:8px 12px;}
        .toolbar-search i{color:#888;font-size:14px;}
        .toolbar-search input{border:none;background:none;font-size:13px;width:100%;outline:none;}
        .toolbar-filter{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .toolbar-filter select{padding:8px 12px;border:1px solid #e0e0e0;border-radius:8px;font-size:13px;background:#fff;}
        .toolbar-count{font-size:12px;color:#666;}
        .btn-dash{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;}
        .btn-dash.btn-primary{background:var(--blue);color:#fff;}
        .btn-dash.btn-secondary{background:#f5f5f5;color:#555;border:1px solid #e0e0e0;}
        .form-fld{display:block;width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;transition:border 0.2s;}
        .form-fld:focus{border-color:var(--blue);outline:none;}
        .form-lbl{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:6px;}
        /* Pagination — matches admin dashboard style */
        .pagination{display:flex !important;justify-content:center;align-items:center;gap:4px;margin:0;padding:0;list-style:none;font-size:13px;}
        .pagination .page-item{display:block !important;}
        .pagination .page-item .page-link{display:inline-flex !important;align-items:center;justify-content:center;min-width:32px;height:32px;padding:6px 10px;margin:0 2px;border:1px solid var(--border);border-radius:6px;background:#fff;color:var(--text);font-weight:500;text-decoration:none;transition:all 0.2s;}
        .pagination .page-item .page-link:hover{background:var(--blue-pale);border-color:var(--blue);color:var(--blue);}
        .pagination .page-item.active .page-link{background:var(--blue);border-color:var(--blue);color:#fff;}
        .pagination .page-item.disabled .page-link{opacity:0.5;cursor:not-allowed;background:#f8f9fa;}
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link{font-weight:600;font-size:12px !important;padding:6px 14px !important;min-width:auto !important;height:auto !important;border-radius:6px !important;}
        .pagination .page-item:first-child .page-link::before,
        .pagination .page-item:last-child .page-link::before,
        .pagination .page-item:first-child .page-link::after,
        .pagination .page-item:last-child .page-link::after{display:none !important;content:none !important;}
        .pagination .page-item:first-child .page-link i,
        .pagination .page-item:last-child .page-link i{display:none !important;}
        .pagination-info{text-align:center;font-size:12px;color:var(--muted);margin-top:10px;}
        .user-row-name{display:flex;align-items:center;gap:10px;}
        .user-row-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;}
        .user-row-sub{font-size:11px;color:var(--muted);}
        .text-muted-alt{color:#aaa;font-size:13px;}
        .installment-progress-bar{height:100%;border-radius:4px;}

        /* ── Skeleton loading ── */
        .main-content{position:relative;}
        .fin-skeleton-overlay{position:absolute;inset:0;background:#f5f6fa;z-index:50;padding:24px 32px;overflow:hidden;transition:opacity .35s ease;}
        .fin-skeleton-overlay.fin-skel-hide{opacity:0;pointer-events:none;}
        .skel{position:relative;overflow:hidden;background:#e7eaf1;border-radius:8px;}
        .skel::after{content:'';position:absolute;top:0;left:-150%;width:150%;height:100%;
            background:linear-gradient(90deg,rgba(255,255,255,0) 0%,rgba(255,255,255,.65) 50%,rgba(255,255,255,0) 100%);
            animation:skel-shimmer 1.4s ease-in-out infinite;}
        @keyframes skel-shimmer{100%{left:150%;}}
        .skel-header-title{width:260px;height:26px;margin-bottom:10px;}
        .skel-header-sub{width:400px;height:14px;margin-bottom:24px;}
        .skel-row-gap{display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;}
        .skel-stat-card{flex:1;min-width:200px;height:76px;border-radius:12px;}
        .skel-card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:24px;padding:20px;}
        .skel-card-header{height:16px;width:190px;margin-bottom:18px;}
        .skel-line{height:12px;border-radius:4px;margin-bottom:10px;}
        .skel-table-row{height:44px;border-radius:6px;margin-bottom:8px;}
        .skel-form-fld{height:40px;border-radius:8px;margin-bottom:18px;}
        .skel-form-lbl{height:10px;width:110px;margin-bottom:8px;}
        .skel-chart{height:220px;border-radius:10px;}
        .skel-avatar-lg{width:100px;height:100px;border-radius:50%;margin:0 auto 20px;}
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-brand">
            <i class="bi bi-wallet2"></i>
            <span>Finance Portal</span>
        </div>
        <div class="topbar-center">
            <?php echo $__env->yieldContent('topbar-center'); ?>
        </div>
        <div class="topbar-right">
            <div class="dropdown">
                <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?php if(auth('finance')->user()->profile_photo): ?>
                            <img src="<?php echo e(asset('storage/' . auth('finance')->user()->profile_photo)); ?>" alt="Avatar">
                        <?php else: ?>
                            <?php echo e(strtoupper(substr(auth('finance')->user()->name, 0, 1))); ?>

                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="user-chip-name"><?php echo e(auth('finance')->user()->name); ?></div>
                        <div class="user-chip-role">Finance Officer</div>
                    </div>
                    <i class="bi bi-chevron-down user-chip-caret"></i>
                </div>
                <div class="dropdown-menu dropdown-menu-end user-chip-dropdown">
                    <div class="ucd-header">
                        <div class="ucd-avatar">
                            <?php if(auth('finance')->user()->profile_photo): ?>
                                <img src="<?php echo e(asset('storage/' . auth('finance')->user()->profile_photo)); ?>" alt="Avatar">
                            <?php else: ?>
                                <?php echo e(strtoupper(substr(auth('finance')->user()->name, 0, 2))); ?>

                            <?php endif; ?>
                        </div>
                        <div class="ucd-info">
                            <div class="ucd-name"><?php echo e(auth('finance')->user()->name); ?></div>
                            <div class="ucd-email"><?php echo e(auth('finance')->user()->email); ?></div>
                            <span class="ucd-badge">Finance Officer</span>
                        </div>
                    </div>
                    <div class="ucd-body">
                        <a class="ucd-item" href="<?php echo e(route('finance.profile')); ?>"><i class="bi bi-person-circle"></i> My Profile</a>
                        <a class="ucd-item" href="<?php echo e(route('finance.change-password')); ?>"><i class="bi bi-shield-lock"></i> Change Password</a>
                    </div>
                    <div class="ucd-divider"></div>
                    <div class="ucd-footer">
                        <form method="POST" action="<?php echo e(route('finance.logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="ucd-item ucd-logout">
                                <i class="bi bi-box-arrow-left"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <nav class="sidebar-menu">
            <div class="menu-section">Main</div>
            <a href="<?php echo e(route('finance.dashboard')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>

            <div class="menu-section">Students</div>
            <a href="<?php echo e(route('finance.students.index')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.students.*') ? 'active' : ''); ?>">
                <i class="bi bi-people-fill"></i>
                All Students
            </a>

            <div class="menu-section">Finance</div>
            <a href="<?php echo e(route('finance.installments.index')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.installments.*') ? 'active' : ''); ?>">
                <i class="bi bi-calendar-check-fill"></i>
                Installments
            </a>
            <a href="<?php echo e(route('finance.fees.index')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.fees.*') ? 'active' : ''); ?>">
                <i class="bi bi-cash-stack"></i>
                Fee Management
            </a>

            <div class="menu-section">Reports</div>
            <a href="<?php echo e(route('finance.reports.index')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.reports.*') ? 'active' : ''); ?>">
                <i class="bi bi-graph-up"></i>
                Financial Reports
            </a>

            <div class="menu-section">Account</div>
            <a href="<?php echo e(route('finance.profile')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.profile') ? 'active' : ''); ?>">
                <i class="bi bi-person-circle"></i>
                My Profile
            </a>
            <a href="<?php echo e(route('finance.change-password')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.change-password') ? 'active' : ''); ?>">
                <i class="bi bi-shield-lock"></i>
                Change Password
            </a>

            <div class="menu-section">System</div>
            <form method="POST" action="<?php echo e(route('finance.logout')); ?>" style="margin:0;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="menu-item w-100 text-start" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.8);border-left:3px solid transparent;">
                    <i class="bi bi-box-arrow-left"></i>
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div id="finSkeletonOverlay" class="fin-skeleton-overlay">
            <?php echo $__env->yieldContent('skeleton'); ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        (function(){
            var overlay = document.getElementById('finSkeletonOverlay');
            if (!overlay) return;
            var minTime = 450; // minimum ms the skeleton stays visible, so it doesn't just flash
            var start = Date.now();
            function reveal(){
                var wait = Math.max(0, minTime - (Date.now() - start));
                setTimeout(function(){
                    overlay.classList.add('fin-skel-hide');
                    setTimeout(function(){ overlay.remove(); }, 400);
                }, wait);
            }
            if (document.readyState === 'complete') reveal();
            else window.addEventListener('load', reveal);
        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/finance/layout.blade.php ENDPATH**/ ?>