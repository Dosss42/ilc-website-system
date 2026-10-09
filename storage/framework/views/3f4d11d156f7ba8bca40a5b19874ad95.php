<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script>
        // Remember which Finance page was last open and, when landing back on
        // the bare Dashboard fresh (e.g. right after logging back in, or by
        // opening the portal from a bookmark/new tab), jump straight back to
        // it — same "pick up where I left off" behavior as the Admin
        // portal's tabs. Runs before the rest of the page loads so there's
        // no dashboard flash before the redirect.
        //
        // "Fresh" is judged from document.referrer rather than a one-time
        // flag: if the browser navigated here FROM another page already
        // inside /finance/ (e.g. clicking the "Dashboard" link on purpose),
        // that's treated as deliberate and never redirected. A stored
        // per-tab flag would only catch the very first load of a tab and
        // then incorrectly let every later legitimate click through — this
        // referrer check gets it right on every single load, new tab or not.
        (function () {
            var isDashboard = <?php echo json_encode(\Illuminate\Support\Facades\Route::currentRouteName() === 'finance.dashboard', 15, 512) ?>;
            var currentPath = window.location.pathname;
            if (isDashboard) {
                var ref = document.referrer || '';
                var cameFromWithinFinance = ref.indexOf('/finance/') !== -1 && ref.indexOf('/finance/login') === -1;
                if (!cameFromWithinFinance) {
                    var savedPath = localStorage.getItem('financeLastPage');
                    if (savedPath && savedPath !== currentPath && savedPath.indexOf('/finance/') === 0) {
                        window.location.replace(savedPath);
                        return;
                    }
                }
            }
            localStorage.setItem('financeLastPage', currentPath);
        })();
    </script>
    <title><?php echo $__env->yieldContent('title', 'Finance Portal'); ?> - IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        * { font-family: 'Open Sans', sans-serif; }

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
            background: linear-gradient(135deg, #162f5c, #1a3a6c);
            display: flex; align-items: center;
            gap: 10px; padding: 0 16px; flex-shrink: 0;
        }

        /* Brand block — same markup/classes as every other portal now
           (was a bare icon + single-line "Finance Portal" label before). */
        .brand-logo-circle {
            width: 32px; height: 32px; border-radius: 50%;
            overflow: hidden; border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-logo-circle img { width: 100%; height: 100%; object-fit: cover; }
        .brand-logo-circle i   { color: rgba(255,255,255,0.5); font-size: 14px; }
        .brand-info h6 {
            font-size: 11px; font-weight: 700; color: #fff;
            margin: 0; line-height: 1.2;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .brand-info span { font-size: 9px; color: rgba(255,255,255,0.55); }

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
            background: linear-gradient(180deg, #162f5c 0%, #1a3a6c 60%, #1c3f78 100%);
            box-shadow: 3px 0 18px rgba(0,0,0,0.18);
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar-menu {
            padding: 16px 0;
            display: flex; flex-direction: column;
            min-height: calc(100% - 16px);
        }

        /* Settings/Logout pinned to the bottom with a divider above them,
           matching the Cashier portal's sidebar (the shared reference
           layout across every portal now) — was just two more sections
           in the normal flow before, not actually pinned. */
        .sidebar-bottom {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.09);
            padding-top: 8px;
        }

        .menu-section {
            padding: 14px 20px 5px;
            color: rgba(255,255,255,0.3);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.6px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px 10px 17px;
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
        .menu-item.active {
            background: rgba(197,160,89,0.12);
        }

        /* Icon badge — each nav icon sits in its own rounded square,
           matching the Cashier portal's sidebar (the shared reference
           look across every portal now). */
        .menu-item i {
            font-size: 15px;
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.06);
            transition: background 0.18s;
            flex-shrink: 0;
        }
        .menu-item:hover i { background: rgba(255,255,255,0.12); }
        .menu-item.active i { background: rgba(197,160,89,0.25); color: var(--gold); }

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
            <div class="brand-logo-circle">
                <img src="/images/logo.png" alt=""
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-wallet2\'></i>'">
            </div>
            <div class="brand-info">
                <h6>IEMELIF Learning Center</h6>
                <span>General Tinio, Nueva Ecija</span>
            </div>
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
                        <a class="ucd-item" href="<?php echo e(route('finance.settings')); ?>"><i class="bi bi-gear-fill"></i> Settings</a>
                    </div>
                    <div class="ucd-divider"></div>
                    <div class="ucd-footer">
                        <form method="POST" action="<?php echo e(route('finance.logout')); ?>" onsubmit="return confirmLogout(this)">
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
            <a href="<?php echo e(route('finance.audit-trail')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.audit-trail') ? 'active' : ''); ?>">
                <i class="bi bi-journal-check"></i>
                Audit Trail
            </a>

            <div class="sidebar-bottom">
                <a href="<?php echo e(route('finance.settings')); ?>" class="menu-item <?php echo e(request()->routeIs('finance.settings') ? 'active' : ''); ?>">
                    <i class="bi bi-gear-fill"></i>
                    Settings
                </a>
                <form method="POST" action="<?php echo e(route('finance.logout')); ?>" style="margin:0;" onsubmit="return confirmLogout(this)">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="menu-item w-100 text-start" style="background:none;border:none;cursor:pointer;color:rgba(248,113,113,0.8);border-left:3px solid transparent;">
                        <i class="bi bi-box-arrow-left" style="color:rgba(248,113,113,0.9);"></i>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div id="finSkeletonOverlay" class="fin-skeleton-overlay">
            <?php echo $__env->yieldContent('skeleton'); ?>
        </div>

        <?php
            $__bcMap = [
                'finance.dashboard'          => 'Dashboard',
                'finance.payments.index'     => 'Payments',
                'finance.students.index'     => 'All Students',
                'finance.installments.index' => 'Installments',
                'finance.fees.index'         => 'Fee Management',
                'finance.reports.index'      => 'Reports',
                'finance.settings'           => 'Settings',
            ];
            $__bcRoute = \Illuminate\Support\Facades\Route::currentRouteName();
            $__bcCurrent = $__bcMap[$__bcRoute] ?? ucwords(str_replace(['-', '.'], [' ', ' '], \Illuminate\Support\Str::afterLast($__bcRoute ?? '', '.')));
        ?>
        <style>
            .ilc-breadcrumb{display:flex;align-items:center;gap:8px;padding:0 0 18px;font-size:13px;color:#64748b;flex-wrap:wrap;}
            .ilc-breadcrumb a{color:var(--blue);text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
            .ilc-breadcrumb a:hover{text-decoration:underline;}
            .ilc-bc-sep{font-size:10px;color:#b6c0cc;}
            .ilc-bc-current{color:#334155;font-weight:700;}
        </style>
        <nav class="ilc-breadcrumb" aria-label="breadcrumb">
            <a href="<?php echo e(route('finance.dashboard')); ?>"><i class="bi bi-house-door-fill"></i> Home</a>
            <?php if($__bcRoute !== 'finance.dashboard'): ?>
                <i class="bi bi-chevron-right ilc-bc-sep"></i>
                <span class="ilc-bc-current"><?php echo e($__bcCurrent); ?></span>
            <?php endif; ?>
        </nav>

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
    <script>
        // ── Toast notifications ── shared by every Finance page (fees,
        // installments, students, etc. each push their own scripts into
        // this layout and run after it, so this is defined before any of
        // them need it). Replaces plain alert() popups — same look as the
        // Admin/Super Admin/Cashier portals, for consistency across the
        // whole app.
        function showToast(message, type) {
            if (!type) type = 'success';
            var icons   = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
            var colors  = { success:'#1a7a44', error:'#c0392b', warning:'#b45309', info:'#1565c0' };
            var borders = { success:'#27ae60', error:'#e74c3c', warning:'#f5a623', info:'#2471a3' };
            var bgs     = { success:'#e8f8f0', error:'#fdecea', warning:'#fff8ec', info:'#e3f2fd' };
            var icon    = icons[type]   || icons.success;
            var color   = colors[type]  || colors.success;
            var border  = borders[type] || borders.success;
            var bg      = bgs[type]     || bgs.success;

            var container = document.getElementById('ilc-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'ilc-toast-container';
                container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;pointer-events:none;';
                document.body.appendChild(container);
            }

            var t = document.createElement('div');
            t.style.cssText = 'min-width:290px;max-width:420px;background:' + bg + ';border:1.5px solid ' + border + ';border-left:4px solid ' + border + ';border-radius:10px;padding:13px 16px;display:flex;align-items:flex-start;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,0.12);font-size:13px;pointer-events:all;transform:translateX(120%);transition:transform 0.28s ease,opacity 0.28s;';

            var closeBtn = document.createElement('button');
            closeBtn.innerHTML = '&times;';
            closeBtn.style.cssText = 'background:none;border:none;color:#aaa;cursor:pointer;font-size:18px;line-height:1;padding:0 0 0 6px;flex-shrink:0;';
            closeBtn.onclick = function() { t.remove(); };

            var iconEl = document.createElement('i');
            iconEl.className = 'bi ' + icon;
            iconEl.style.cssText = 'color:' + border + ';font-size:16px;flex-shrink:0;margin-top:1px;';

            var msgEl = document.createElement('span');
            msgEl.style.cssText = 'flex:1;font-weight:500;color:' + color + ';line-height:1.5;';
            msgEl.innerHTML = message;

            t.appendChild(iconEl);
            t.appendChild(msgEl);
            t.appendChild(closeBtn);
            container.appendChild(t);

            requestAnimationFrame(function() { t.style.transform = 'translateX(0)'; });

            setTimeout(function() {
                t.style.opacity = '0';
                t.style.transform = 'translateX(120%)';
                setTimeout(function() { t.remove(); }, 300);
            }, 4500);
        }

        // ── Confirmation modal ── replaces native confirm() popups with a
        // styled, on-brand dialog (same look across every portal now).
        // onConfirm runs only if the user clicks the confirm button;
        // nothing runs on cancel/backdrop-click/Escape.
        function showConfirm(message, onConfirm, opts) {
            opts = opts || {};
            var title = opts.title || 'Please Confirm';
            var confirmLabel = opts.confirmLabel || 'Yes, Continue';
            var danger = !!opts.danger;
            var iconBg = danger ? '#fdecea' : '#fff8ec';
            var iconColor = danger ? '#c0392b' : '#b45309';
            var okBg = danger ? '#dc2626' : '#1a3a6c';

            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';

            var box = document.createElement('div');
            box.style.cssText = 'background:#fff;border-radius:16px;padding:28px 26px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);text-align:center;font-family:\'Open Sans\',sans-serif;';
            box.innerHTML =
                '<div style="width:52px;height:52px;border-radius:50%;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">' +
                    '<i class="bi ' + (danger ? 'bi-exclamation-triangle-fill' : 'bi-question-circle-fill') + '" style="font-size:24px;color:' + iconColor + ';"></i>' +
                '</div>' +
                '<div style="font-size:15px;font-weight:700;color:#1a3a6c;margin-bottom:6px;">' + title + '</div>' +
                '<div style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:20px;">' + message + '</div>' +
                '<div style="display:flex;gap:10px;">' +
                    '<button type="button" id="ilc-confirm-cancel" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">Cancel</button>' +
                    '<button type="button" id="ilc-confirm-ok" style="flex:1;padding:10px;border-radius:9px;border:none;background:' + okBg + ';color:#fff;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">' + confirmLabel + '</button>' +
                '</div>';

            overlay.appendChild(box);
            document.body.appendChild(overlay);

            function close() {
                overlay.remove();
                document.removeEventListener('keydown', onKey);
            }
            function onKey(e) { if (e.key === 'Escape') close(); }
            document.addEventListener('keydown', onKey);

            box.querySelector('#ilc-confirm-cancel').onclick = close;
            box.querySelector('#ilc-confirm-ok').onclick = function () { close(); onConfirm(); };
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
        }

        // Shared helper for every logout <form onsubmit="return confirmLogout(this)">
        // across the Finance portal — blocks the native synchronous submit,
        // shows the styled confirm, and submits for real only if confirmed.
        function confirmLogout(form) {
            showConfirm('Are you sure you want to log out?', function () { form.submit(); },
                { title: 'Log Out', confirmLabel: 'Log Out', danger: true });
            return false;
        }

        // Styled replacement for native prompt() — single text input, same
        // look as showConfirm. onSubmit receives the entered value (never
        // called if the user cancels or submits an empty value).
        function showPrompt(message, onSubmit, opts) {
            opts = opts || {};
            var title = opts.title || 'Please Enter a Value';
            var confirmLabel = opts.confirmLabel || 'Submit';
            var placeholder = opts.placeholder || '';
            var inputType = opts.inputType || 'text';

            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';

            var box = document.createElement('div');
            box.style.cssText = 'background:#fff;border-radius:16px;padding:28px 26px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);text-align:center;font-family:\'Open Sans\',sans-serif;';
            box.innerHTML =
                '<div style="width:52px;height:52px;border-radius:50%;background:#fff8ec;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">' +
                    '<i class="bi bi-pencil-fill" style="font-size:22px;color:#b45309;"></i>' +
                '</div>' +
                '<div style="font-size:15px;font-weight:700;color:#1a3a6c;margin-bottom:6px;">' + title + '</div>' +
                '<div style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:14px;">' + message + '</div>' +
                '<input type="' + inputType + '" id="ilc-prompt-input" placeholder="' + placeholder + '" style="width:100%;padding:10px 12px;border-radius:9px;border:1.5px solid #e2e8f0;font-size:13px;margin-bottom:18px;font-family:inherit;box-sizing:border-box;">' +
                '<div style="display:flex;gap:10px;">' +
                    '<button type="button" id="ilc-prompt-cancel" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">Cancel</button>' +
                    '<button type="button" id="ilc-prompt-ok" style="flex:1;padding:10px;border-radius:9px;border:none;background:#1a3a6c;color:#fff;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">' + confirmLabel + '</button>' +
                '</div>';

            overlay.appendChild(box);
            document.body.appendChild(overlay);

            var input = box.querySelector('#ilc-prompt-input');
            setTimeout(function () { input.focus(); }, 50);

            function close() {
                overlay.remove();
                document.removeEventListener('keydown', onKey);
            }
            function submit() {
                var val = input.value.trim();
                if (!val) { input.focus(); return; }
                close();
                onSubmit(val);
            }
            function onKey(e) {
                if (e.key === 'Escape') close();
                if (e.key === 'Enter') submit();
            }
            document.addEventListener('keydown', onKey);

            box.querySelector('#ilc-prompt-cancel').onclick = close;
            box.querySelector('#ilc-prompt-ok').onclick = submit;
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
        }
    </script>
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

    <script>
        // ── Auto-refresh when tab becomes visible again ──
        // Handles returning from another browser tab/window after >=30s away,
        // same behavior as the Admin/Super Admin/Cashier portals, so figures
        // (totals, pending counts, etc.) don't go stale silently while the
        // Finance user is looking at something else.
        (function () {
            var _hiddenAt = null;
            var THRESHOLD = 30000; // 30 seconds
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    _hiddenAt = Date.now();
                } else {
                    if (_hiddenAt && (Date.now() - _hiddenAt) >= THRESHOLD) {
                        location.reload();
                    }
                    _hiddenAt = null;
                }
            });
        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/finance/layout.blade.php ENDPATH**/ ?>