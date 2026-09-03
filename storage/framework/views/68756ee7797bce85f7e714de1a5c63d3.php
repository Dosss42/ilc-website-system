<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Super Admin Dashboard — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/global-scrollbar.css">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    
    <style>
        :root {
            --blue:       #1a3a6c;
            --blue-light: #2471a3;
            --blue-pale:  #e8f0fb;
            --gold:       #f5a623;
            --red:        #e74c3c;
            --green:      #27ae60;
            --orange:     #e67e22;
            --white:      #ffffff;
            --bg:         #f0f4f8;
            --border:     #e2e8f0;
            --text:       #2d3748;
            --muted:      #718096;
            --sidebar-w:  240px;
            --topbar-h:   62px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Open Sans', sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }

        /* TOPBAR */
        .dash-topbar {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--topbar-h); background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .topbar-brand {
            width: var(--sidebar-w); height: 100%;
            background: var(--blue);
            display: flex; align-items: center;
            gap: 10px; padding: 0 16px; flex-shrink: 0;
        }
        .brand-logos { display: flex; gap: 6px; }
        .brand-logo-circle {
            width: 32px; height: 32px; border-radius: 50%;
            overflow: hidden; border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .brand-logo-circle img { width:100%; height:100%; object-fit:cover; }
        .brand-logo-circle i   { color:rgba(255,255,255,0.5); font-size:14px; }
        .brand-info h6 {
            font-size: 11px; font-weight: 700; color: #fff;
            margin: 0; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.3px;
        }
        .brand-info span { font-size: 9px; color: rgba(255,255,255,0.55); }
        .topbar-center { flex: 1; display: flex; align-items: center; padding: 0 20px; }
        .dash-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg); border: 1.5px solid var(--border);
            border-radius: 8px; padding: 7px 14px; width: 280px;
        }
        .dash-search i { color: var(--muted); font-size: 14px; }
        .dash-search input {
            border: none; background: transparent;
            font-size: 13px; font-family: 'Open Sans', sans-serif;
            color: var(--text); outline: none; width: 100%;
        }
        .dash-search input::placeholder { color: #bbb; }
        .topbar-right { display: flex; align-items: center; gap: 12px; padding-right: 20px; margin-left: auto; }
        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1.5px solid var(--border); background: var(--white);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--muted); position: relative;
            transition: all 0.2s; text-decoration: none;
        }
        .topbar-icon-btn:hover { border-color: var(--blue); color: var(--blue); }
        .notif-dot {
            position: absolute; top: -3px; right: -3px;
            width: 14px; height: 14px; background: var(--gold);
            border-radius: 50%; font-size: 8px; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }
        .user-chip { display:flex;align-items:center;gap:10px;cursor:pointer;padding:6px 12px;border-radius:10px;border:1.5px solid var(--border);transition:all 0.2s;background:#fff;-webkit-user-select:none;user-select:none; }
        .user-chip:hover { border-color:var(--blue);background:#f5f8ff; }
        .user-avatar { width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1a3a6c,#2a6dd6);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;font-weight:700;font-size:14px;color:#fff; }
        .user-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .user-avatar i { font-size:16px;color:#fff; }
        .user-chip-name { font-size:13px;font-weight:600;color:var(--text);line-height:1.2; }
        .user-chip-role { font-size:10px;color:var(--muted); }
        .user-chip-caret { font-size:11px;color:#aaa;transition:transform 0.2s;margin-left:2px; }
        .dropdown.show .user-chip-caret { transform:rotate(180deg); }
        .user-chip-dropdown { min-width:240px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 8px 32px rgba(0,0,0,.12),0 2px 8px rgba(0,0,0,.06);padding:0;overflow:hidden;margin-top:6px!important; }
        .ucd-header { display:flex;align-items:center;gap:12px;padding:16px;background:linear-gradient(135deg,#f0f5ff 0%,#fff 100%);border-bottom:1px solid #f0f0f0; }
        .ucd-avatar { width:46px;height:46px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#1a3a6c,#2a6dd6);display:flex;align-items:center;justify-content:center;overflow:hidden;font-weight:700;font-size:18px;color:#fff;box-shadow:0 2px 8px rgba(26,58,108,.25); }
        .ucd-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .ucd-info { flex:1;min-width:0; }
        .ucd-name { font-weight:700;font-size:13px;color:#1a3a6c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .ucd-email { font-size:11px;color:#64748b;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .ucd-badge { display:inline-block;margin-top:5px;background:#e8f0fb;color:var(--blue);font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px; }
        .ucd-body { padding:6px 0; }
        .ucd-item { display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;font-weight:500;color:#374151;text-decoration:none;transition:background 0.15s;cursor:pointer;width:100%;border:none;background:none;text-align:left; }
        .ucd-item:hover { background:#f5f8ff;color:var(--blue);text-decoration:none; }
        .ucd-item i { font-size:15px;width:18px;flex-shrink:0; }
        .ucd-divider { height:1px;background:#f0f0f0;margin:4px 0; }
        .ucd-footer { padding:6px 0; }
        .ucd-logout { color:#dc2626; }
        .ucd-logout:hover { background:#fff5f5!important;color:#dc2626!important; }

        /* Custom Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 13px;
        }
        .pagination .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--text);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .pagination .page-item .page-link:hover {
            background: var(--blue-pale);
            border-color: var(--blue);
            color: var(--blue);
        }
        .pagination .page-item.active .page-link {
            background: var(--blue);
            border-color: var(--blue);
            color: var(--white);
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--bg);
        }
        /* Text-based Previous/Next buttons */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            font-weight: 600;
            font-size: 12px !important;
            padding: 6px 14px !important;
            min-width: auto !important;
            height: auto !important;
            border-radius: 6px !important;
        }
        /* Remove all arrow icons from pagination */
        .pagination .page-item:first-child .page-link::before,
        .pagination .page-item:last-child .page-link::before,
        .pagination .page-item:first-child .page-link::after,
        .pagination .page-item:last-child .page-link::after {
            display: none !important;
            content: none !important;
        }
        /* Hide any chevron icons in pagination */
        .pagination .page-item:first-child .page-link i,
        .pagination .page-item:last-child .page-link i {
            display: none !important;
        }
        /* Force text content for Previous/Next */
        .pagination .page-item:first-child .page-link {
            text-indent: 0 !important;
        }
        .pagination .page-item:last-child .page-link {
            text-indent: 0 !important;
        }
        .pagination-info {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            color: var(--muted);
        }

        /* SIDEBAR */
        .dash-sidebar {
            position: fixed; top: var(--topbar-h); left: 0;
            width: var(--sidebar-w); height: calc(100vh - var(--topbar-h));
            background: var(--blue); display: flex; flex-direction: column;
            padding: 14px 0; z-index: 99; overflow-y: auto;
        }
        .sidebar-section-lbl {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35); padding: 12px 20px 5px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px; color: rgba(255,255,255,0.7);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all 0.2s; border-left: 3px solid transparent;
            cursor: pointer; background: none; border-right: none;
            border-top: none; border-bottom: none;
            width: 100%; text-align: left;
            font-family: 'Open Sans', sans-serif;
        }
        .sidebar-link i { font-size: 17px; width: 20px; flex-shrink: 0; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-link.active { background: rgba(255,255,255,0.12); color: #fff; border-left-color: var(--gold); }
        .sidebar-badge {
            margin-left: auto; background: var(--gold); color: #fff;
            font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px;
        }
        .sidebar-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 8px 20px; }
        .sidebar-bottom { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; }

        /* MAIN */
        .dash-main { margin-left: var(--sidebar-w); margin-top: var(--topbar-h); min-height: calc(100vh - var(--topbar-h)); }
        .dash-section { padding: 28px; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        .section-header {
            display: flex; align-items: flex-start;
            justify-content: space-between; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;
        }
        .section-header h1 { font-size: 22px; font-weight: 700; color: var(--text); margin: 0; }
        .section-header p  { font-size: 13px; color: var(--muted); margin: 3px 0 0; }

        /* BUTTONS */
        .btn-dash {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 8px;
            font-size: 13px; font-weight: 600; font-family: 'Open Sans', sans-serif;
            cursor: pointer; border: none; text-decoration: none; transition: all 0.2s;
        }
        .btn-primary   { background: var(--blue); color: #fff; }
        .btn-primary:hover   { background: var(--blue-light); color: #fff; }
        .btn-success   { background: var(--green); color: #fff; }
        .btn-gold      { background: var(--gold); color: var(--blue); font-weight: 700; }
        .btn-secondary { background: var(--white); color: var(--blue); border: 1.5px solid var(--border); }
        .btn-danger    { background: var(--red); color: #fff; }

        /* STAT CARDS */
        .stat-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 12px; padding: 20px;
            display: flex; align-items: center; gap: 16px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .stat-icon.blue   { background: var(--blue-pale); color: var(--blue); }
        .stat-icon.gold   { background: #fff8ec; color: var(--gold); }
        .stat-icon.green  { background: #e8f8f0; color: var(--green); }
        .stat-icon.red    { background: #fdecea; color: var(--red); }
        .stat-icon.purple { background: #f3e8fb; color: #7d3c98; }
        .stat-icon.orange { background: #fef5e7; color: var(--orange); }
        .stat-icon.teal   { background: #e0f5f1; color: #16a085; }
        .stat-value  { font-size: 26px; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-label  { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .stat-change { font-size: 11px; font-weight: 600; margin-top: 4px; }
        .stat-change.up   { color: var(--green); }
        .stat-change.down { color: var(--red); }

        /* CONTENT CARDS */
        .content-card { background: var(--white); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        .content-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; border-bottom: 1px solid var(--border);
        }
        .content-card-header h6 { font-size: 14px; font-weight: 700; color: var(--text); margin: 0; }
        .content-card-header a  { font-size: 12px; color: var(--blue-light); text-decoration: none; font-weight: 500; }
        .content-card-header a:hover { text-decoration: underline; }

        /* TABLE */
        .dash-table { width: 100%; border-collapse: collapse; }
        .dash-table thead th {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--muted); padding: 10px 20px; background: #f8fafc;
            border-bottom: 1px solid var(--border); white-space: nowrap;
        }
        .dash-table tbody td {
            font-size: 13px; padding: 13px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle; color: var(--text);
        }
        .dash-table tbody tr:last-child td { border-bottom: none; }
        .dash-table tbody tr:hover { background: #f8fafc; }

        /* BADGES */
        .status-badge {
            display: inline-block; padding: 4px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .status-badge.active   { background: var(--blue-pale); color: var(--blue); }
        .status-badge.inactive { background: #fdecea; color: var(--red); }
        .status-badge.enrolled { background: #e8f8f0; color: var(--green); }
        .status-badge.pending  { background: #fff8ec; color: #d68910; }
        .status-badge.suspended { background: #fdecea; color: var(--red); }
        .status-badge.primary  { background: var(--blue-pale); color: var(--blue); }

        /* SA TOAST */
        #sa-toast-wrap { position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none; }
        .sa-toast { display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:12px;background:#fff;border:1.5px solid #e2e8f0;box-shadow:0 8px 28px rgba(0,0,0,.14);min-width:280px;max-width:380px;pointer-events:all;animation:saSlideIn .25s ease; }
        @keyframes saSlideIn { from{opacity:0;transform:translateX(30px);}to{opacity:1;transform:translateX(0);} }
        .sa-toast.sa-success { border-color:#bbf7d0; }
        .sa-toast.sa-error   { border-color:#fecaca; }
        .sa-toast.sa-warning { border-color:#fde68a; }
        .sa-toast-icon { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0; }
        .sa-success .sa-toast-icon { background:#f0fdf4;color:#16a34a; }
        .sa-error   .sa-toast-icon { background:#fef2f2;color:#dc2626; }
        .sa-warning .sa-toast-icon { background:#fffbeb;color:#b45309; }
        .sa-toast-msg { font-size:13px;font-weight:600;color:#1e293b;flex:1; }
        .sa-toast-sub { font-size:11px;color:#64748b;margin-top:2px; }

        .role-badge {
            display: inline-block; padding: 4px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 700;
        }
        .role-badge.superadmin { background: #f3e8fb; color: #7d3c98; }
        .role-badge.admin      { background: var(--blue-pale); color: var(--blue); }
        .role-badge.teacher    { background: #e8f8f0; color: var(--green); }
        .role-badge.student    { background: #fff8ec; color: #d68910; }

        /* USER ROW */
        .user-row-name { display: flex; align-items: center; gap: 10px; }
        .user-row-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--blue-pale);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: var(--blue); flex-shrink: 0;
        }
        .user-row-sub { font-size: 11px; color: var(--muted); }

        /* ACTION BTNS */
        .action-btn {
            width: 30px; height: 30px; border-radius: 6px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 13px; cursor: pointer; border: none;
            transition: opacity 0.2s; text-decoration: none; margin-right: 3px;
        }
        .action-btn.edit   { background: var(--blue-pale); color: var(--blue); }
        .action-btn.delete { background: #fdecea; color: var(--red); }
        .action-btn.view   { background: #e8f8f0; color: var(--green); }
        .action-btn.lock   { background: #fff8ec; color: #d68910; }
        .action-btn:hover  { opacity: 0.75; }

        /* ANN ITEMS */
        .ann-dash-item {
            display: flex; gap: 14px; padding: 14px 20px;
            border-bottom: 1px solid var(--border); transition: background 0.15s;
        }
        .ann-dash-item:last-child { border-bottom: none; }
        .ann-dash-item:hover { background: #f8fafc; }
        .ann-date-badge {
            background: var(--blue); color: #fff; border-radius: 8px;
            text-align: center; padding: 8px 10px; min-width: 44px;
            flex-shrink: 0; display: flex; align-items: center; justify-content: center;
        }
        .ann-body-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
        .ann-body-meta  { font-size: 11px; color: var(--muted); }

        /* LOG ITEM */
        .log-item {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 12px 20px; border-bottom: 1px solid var(--border);
        }
        .log-item:last-child { border-bottom: none; }
        .log-icon {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .log-icon.success { background: #e8f8f0; color: var(--green); }
        .log-icon.danger  { background: #fdecea; color: var(--red); }
        .log-icon.warning { background: #fff8ec; color: #d68910; }
        .log-icon.info    { background: var(--blue-pale); color: var(--blue); }
        .log-icon.system  { background: #f3e8fb; color: #7d3c98; }
        .log-title { font-size: 13px; font-weight: 600; color: var(--text); }
        .log-meta  { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* FORM */
        .form-lbl {
            font-size: 12px; font-weight: 700; color: #444;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 5px; display: block;
        }
        .form-fld {
            width: 100%; border: 1.5px solid #e0e0e0;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; font-family: 'Open Sans', sans-serif;
            background: #fafafa; color: var(--text); transition: border 0.2s, box-shadow 0.2s;
        }
        .form-fld:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(26,58,108,0.08); background: #fff; outline: none; }
        select.form-fld { cursor: pointer; }
        textarea.form-fld { resize: vertical; min-height: 100px; }

        /* PERMISSION GRID */
        .perm-module {
            border: 1px solid var(--border); border-radius: 10px;
            overflow: hidden; margin-bottom: 12px;
        }
        .perm-module-header {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        .perm-module-header span { font-size: 13px; font-weight: 700; color: var(--text); }
        .perm-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 16px; border-bottom: 1px solid var(--border);
        }
        .perm-row:last-child { border-bottom: none; }
        .perm-row-name { font-size: 13px; color: var(--text); }
        .perm-checks { display: flex; gap: 20px; }
        .perm-check { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .perm-check label { font-size: 10px; color: var(--muted); font-weight: 600; text-transform: uppercase; }
        .perm-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--blue); }

        /* PROGRESS */
        .prog-bar-wrap { background: var(--bg); border-radius: 20px; height: 8px; overflow: hidden; margin-top: 6px; }
        .prog-bar { height: 100%; border-radius: 20px; background: var(--blue); transition: width 0.4s; }
        .prog-bar.gold   { background: var(--gold); }
        .prog-bar.green  { background: var(--green); }
        .prog-bar.red    { background: var(--red); }
        .prog-bar.purple { background: #7d3c98; }

        /* ── Section skeleton loading ── */
        @keyframes pSkelShimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skel{background:linear-gradient(90deg,#e8edf2 25%,#f5f7fa 50%,#e8edf2 75%);background-size:600px 100%;animation:pSkelShimmer 1.4s ease-in-out infinite;border-radius:6px;display:block}
        .p-skel{position:absolute;top:0;left:0;right:0;bottom:0;padding:28px;z-index:50;background:var(--bg,#f0f4f8);pointer-events:none;min-height:100vh;transition:opacity .32s ease}
        [id^="section-"]{position:relative}

        /* BACKUP CARD */
        .backup-card {
            border: 1px solid var(--border); border-radius: 10px;
            padding: 16px; display: flex; align-items: center;
            justify-content: space-between; gap: 12px;
            background: var(--white); margin-bottom: 10px;
            transition: box-shadow 0.2s;
        }
        .backup-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .backup-info-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .backup-info-meta { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .backup-size { font-size: 13px; font-weight: 700; color: var(--blue); }

        /* Content sections (announcements / news) */
        .alert-success-bar { background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px; }
        .form-lbl-sm { display:block;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px; }
        .dash-input { width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;outline:none;transition:border .2s; }
        .dash-input:focus { border-color:var(--blue); }
        select.dash-input { cursor:pointer; }
        input[type=file].dash-input { padding:8px 13px;background:#f9fafb; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- TOPBAR -->
<div class="dash-topbar">
    <div class="topbar-brand">
        <div class="brand-logos">
            
            <div class="brand-logo-circle">
                <img src="/images/logo.png" alt=""
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-shield-fill\'></i>'">
            </div>
        </div>
        <div class="brand-info">
            <h6>IEMELIF Learning Center</h6>
            <span>General Tinio, Nueva Ecija</span>
        </div>
    </div>
    <div class="topbar-center">
        <div class="dash-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search users, logs, settings...">
        </div>
    </div>
    <div class="topbar-right">
        <a href="#" class="topbar-icon-btn">
            <i class="bi bi-bell"></i>
            <span class="notif-dot">3</span>
        </a>
        <a href="#" class="topbar-icon-btn">
            <i class="bi bi-envelope"></i>
        </a>
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">
                    <?php if(Auth::user()->profile_photo): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Avatar">
                    <?php else: ?>
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    <?php endif; ?>
                </div>
                <div>
                    <div class="user-chip-name"><?php echo e(Auth::user()->name); ?></div>
                    <div class="user-chip-role">Super Admin</div>
                </div>
                <i class="bi bi-chevron-down user-chip-caret"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end user-chip-dropdown">
                <div class="ucd-header">
                    <div class="ucd-avatar">
                        <?php if(Auth::user()->profile_photo): ?>
                            <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Avatar">
                        <?php else: ?>
                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?>

                        <?php endif; ?>
                    </div>
                    <div class="ucd-info">
                        <div class="ucd-name"><?php echo e(Auth::user()->name); ?></div>
                        <div class="ucd-email"><?php echo e(Auth::user()->email); ?></div>
                        <span class="ucd-badge">Super Admin</span>
                    </div>
                </div>
                <div class="ucd-body">
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-person-circle"></i> My Profile</a>
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-gear"></i> Settings</a>
                </div>
                <div class="ucd-divider"></div>
                <div class="ucd-footer">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ucd-item ucd-logout">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SIDEBAR -->
<div class="dash-sidebar">
    <div class="sidebar-section-lbl">Main</div>
    <button class="sidebar-link <?php echo e($section === 'dashboard' ? 'active' : ''); ?>" id="nav-dashboard" onclick="showSection('dashboard')">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </button>

    <div class="sidebar-section-lbl">System Management</div>
    <button class="sidebar-link <?php echo e($section === 'users' ? 'active' : ''); ?>" id="nav-users" onclick="showSection('users')">
        <i class="bi bi-people-fill"></i> User Management
        <span class="sidebar-badge"><?php echo e($stats['total_users']); ?></span>
    </button>
    <button class="sidebar-link <?php echo e($section === 'roles' ? 'active' : ''); ?>" id="nav-roles" onclick="showSection('roles')">
        <i class="bi bi-shield-lock-fill"></i> Roles &amp; Permissions
    </button>
    <button class="sidebar-link <?php echo e($section === 'logs' ? 'active' : ''); ?>" id="nav-logs" onclick="showSection('logs')">
        <i class="bi bi-journal-text"></i> System Logs
    </button>
    <button class="sidebar-link <?php echo e($section === 'backup' ? 'active' : ''); ?>" id="nav-backup" onclick="showSection('backup')">
        <i class="bi bi-database-fill"></i> Backup &amp; Restore
    </button>

    <div class="sidebar-section-lbl">Academic Management</div>
    <button class="sidebar-link <?php echo e($section === 'enrollments' ? 'active' : ''); ?>" id="nav-enrollments" onclick="showSection('enrollments')">
        <i class="bi bi-clipboard-check-fill"></i> Enrollments
    </button>
    <button class="sidebar-link <?php echo e($section === 'teachers' ? 'active' : ''); ?>" id="nav-teachers" onclick="showSection('teachers')">
        <i class="bi bi-person-video3"></i> Teachers
    </button>
    <button class="sidebar-link <?php echo e($section === 'subjects' ? 'active' : ''); ?>" id="nav-subjects" onclick="showSection('subjects')">
        <i class="bi bi-book-fill"></i> Subjects
    </button>
    <button class="sidebar-link <?php echo e($section === 'sections-mgmt' ? 'active' : ''); ?>" id="nav-sections-mgmt" onclick="showSection('sections-mgmt')">
        <i class="bi bi-diagram-3-fill"></i> Sections
    </button>
    <button class="sidebar-link <?php echo e($section === 'schedules' ? 'active' : ''); ?>" id="nav-schedules" onclick="showSection('schedules')">
        <i class="bi bi-calendar3-week-fill"></i> Schedules
    </button>

    <div class="sidebar-section-lbl">Content</div>
    <button class="sidebar-link <?php echo e($section === 'announcements' ? 'active' : ''); ?>" id="nav-announcements" onclick="showSection('announcements')">
        <i class="bi bi-megaphone-fill"></i> Announcements
        <span class="sidebar-badge"><?php echo e($announcements->total()); ?></span>
    </button>
    <button class="sidebar-link <?php echo e($section === 'news' ? 'active' : ''); ?>" id="nav-news" onclick="showSection('news')">
        <i class="bi bi-newspaper"></i> News
        <span class="sidebar-badge"><?php echo e($newsArticles->total()); ?></span>
    </button>

    <div class="sidebar-section-lbl">System</div>
    <button class="sidebar-link <?php echo e($section === 'sys-settings' ? 'active' : ''); ?>" id="nav-sys-settings" onclick="showSection('sys-settings')">
        <i class="bi bi-sliders"></i> System Settings
    </button>

    <div class="sidebar-section-lbl">Overview</div>
    <button class="sidebar-link <?php echo e($section === 'reports' ? 'active' : ''); ?>" id="nav-reports" onclick="showSection('reports')">
        <i class="bi bi-bar-chart-fill"></i> Reports
    </button>

    <div class="sidebar-divider"></div>
    <button class="sidebar-link <?php echo e($section === 'settings' ? 'active' : ''); ?>" id="nav-settings" onclick="showSection('settings')">
        <i class="bi bi-gear-fill"></i> Settings
    </button>

    <div class="sidebar-bottom">
        
        <form method="POST" action="/logout">
            <?php echo csrf_field(); ?>
            <button type="submit" class="sidebar-link" style="color:rgba(255,255,255,0.7);">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="dash-main">

    <!-- ═══════════════════════════
         SECTION: DASHBOARD
    ═══════════════════════════ -->
    <div id="section-dashboard" class="dash-section" <?php echo e($section !== 'dashboard' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>Super Admin Dashboard</h1>
                
                <p>Thursday, March 26, 2026 — System Overview</p>
            </div>
            <a href="#" onclick="showSection('users')" class="btn-dash btn-primary">
                <i class="bi bi-person-plus-fill"></i> Add User
            </a>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['total_users']); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['admins'] + $stats['superadmins']); ?></div>
                        <div class="stat-label">Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-easel-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['total_teachers']); ?></div>
                        <div class="stat-label">Teachers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-mortarboard-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['total_students']); ?></div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['inactive_users']); ?></div>
                        <div class="stat-label">Inactive</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-database-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($stats['active_users']); ?></div>
                        <div class="stat-label">Active</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Charts -->
        <?php
            $saChMonths=[]; $saChEnroll=[];
            for($i=5;$i>=0;$i--){
                $m=now()->subMonths($i);
                $saChMonths[]=$m->format('M Y');
                $saChEnroll[]=\App\Models\Enrollment::whereYear('created_at',$m->year)->whereMonth('created_at',$m->month)->count();
            }
        ?>
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-graph-up me-2" style="color:var(--blue);"></i>Monthly Enrollment Trend</h6>
                        <span style="font-size:11px;color:var(--muted);">Last 6 months</span>
                    </div>
                    <div class="p-3" style="height:200px;"><canvas id="saEnrollTrend"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-people-fill me-2" style="color:var(--blue);"></i>Users by Role</h6>
                    </div>
                    <div class="p-3" style="height:200px;display:flex;justify-content:center;"><canvas id="saRoleDoughnut"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Users + Logs -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>Recent Users</h6>
                        <a href="#" onclick="showSection('users')">View All</a>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $dashboardUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $db_badge = match($user->role) {
                                        'superadmin' => ['cls'=>'superadmin','label'=>'Super Admin'],
                                        'admin'      => ['cls'=>'admin',     'label'=>'Admin'],
                                        'finance'    => ['cls'=>'teacher',   'label'=>'Finance'],
                                        'teacher'    => ['cls'=>'teacher',   'label'=>'Teacher'],
                                        default      => ['cls'=>'student',   'label'=>ucfirst($user->role)],
                                    };
                                ?>
                                <tr data-user-id="<?php echo e($user->id); ?>" data-user-name="<?php echo e($user->name); ?>" data-user-email="<?php echo e($user->email); ?>" data-user-role="<?php echo e($user->role); ?>" data-user-active="<?php echo e($user->is_active ? '1' : '0'); ?>">
                                    <td>
                                        <div class="user-row-name">
                                            <div class="user-row-avatar" style="background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;"><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></div>
                                            <div>
                                                <div style="font-weight:600;"><?php echo e($user->name); ?></div>
                                                <div class="user-row-sub"><?php echo e($user->email); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge <?php echo e($db_badge['cls']); ?>"><?php echo e($db_badge['label']); ?></span></td>
                                    <td>
                                        <span class="status-badge <?php echo e($user->is_active ? 'active' : 'inactive'); ?>">
                                            <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                                        </span>
                                    </td>
                                    <td style="font-size:12px;color:var(--muted);"><?php echo e($user->created_at?->format('M d, Y') ?? '—'); ?></td>
                                    <td>
                                        <div style="display:flex;gap:5px;">
                                            <button class="action-btn edit" title="Edit"
                                                onclick="openEditUserModal(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>','<?php echo e($user->email); ?>','<?php echo e($user->role); ?>',<?php echo e($user->is_active ? 'true' : 'false'); ?>)">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button class="action-btn lock" title="Reset Password"
                                                onclick="openResetPasswordModal(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')">
                                                <i class="bi bi-key-fill"></i>
                                            </button>
                                            <?php if($user->id !== auth()->id()): ?>
                                            <button class="action-btn <?php echo e($user->is_active ? 'delete' : 'view'); ?>" title="<?php echo e($user->is_active ? 'Deactivate' : 'Activate'); ?>"
                                                onclick="confirmToggleStatus(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>',<?php echo e($user->is_active ? 'true' : 'false'); ?>)">
                                                <i class="bi bi-person-<?php echo e($user->is_active ? 'x' : 'check'); ?>-fill"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete"
                                                onclick="confirmDeleteUser(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        
                        <div class="p-3 border-top" style="border-color:var(--border);">
                            <nav>
                                <?php echo e($dashboardUsers->appends(['sort' => $sort, 'role' => $roleFilter, 'section' => 'dashboard'])->links()); ?>

                            </nav>
                            <div class="pagination-info">
                                Showing <?php echo e($dashboardUsers->firstItem()); ?> to <?php echo e($dashboardUsers->lastItem()); ?> of <?php echo e($dashboardUsers->total()); ?> results
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>Recent System Logs</h6>
                        <a href="#" onclick="showSection('logs')">View All</a>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $logs->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $recentIcon = match($log->event_type) {
                            'login'        => ['icon' => 'bi-box-arrow-in-right', 'cls' => 'success'],
                            'logout'       => ['icon' => 'bi-box-arrow-right',    'cls' => 'info'],
                            'failed_login' => ['icon' => 'bi-shield-x',           'cls' => 'danger'],
                            'create'       => ['icon' => 'bi-plus-circle-fill',   'cls' => 'success'],
                            'update'       => ['icon' => 'bi-pencil-fill',        'cls' => 'info'],
                            'delete'       => ['icon' => 'bi-trash-fill',         'cls' => 'danger'],
                            default        => ['icon' => 'bi-info-circle-fill',   'cls' => 'info'],
                        };
                    ?>
                    <div class="log-item">
                        <div class="log-icon <?php echo e($recentIcon['cls']); ?>"><i class="bi <?php echo e($recentIcon['icon']); ?>"></i></div>
                        <div>
                            <div class="log-title"><?php echo e(Str::limit($log->description, 80)); ?></div>
                            <div class="log-meta">
                                <?php echo e($log->user_name ?? 'System'); ?>

                                <?php if($log->user_role): ?> · <?php echo e($log->user_role); ?> <?php endif; ?>
                                · <?php echo e($log->created_at?->diffForHumans()); ?>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div style="padding:24px;text-align:center;color:var(--muted);">
                        <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No activity logged yet
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!-- /section-dashboard -->

    <!-- ═══════════════════════════
         SECTION: USER MANAGEMENT
    ═══════════════════════════ -->
    <div id="section-users" class="dash-section" <?php echo e($section !== 'users' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>User Management</h1>
                <p>Create, edit, and manage all system users.</p>
            </div>
            <a href="#" class="btn-dash btn-primary" onclick="openCreateUserModal()">
                <i class="bi bi-person-plus-fill"></i> Add User
            </a>
        </div>

        <!-- Search & Filter -->
        
        <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
            <?php
                $roleTabs = [
                    'all'        => 'All',
                    'superadmin' => 'Super Admin',
                    'admin'      => 'Admin',
                    'finance'    => 'Finance',
                    'teacher'    => 'Teacher',
                    'student'    => 'Student',
                ];
                $roleCounts = [
                    'all'        => $userManagementUsers->total(),
                    'superadmin' => $stats['superadmins'],
                    'admin'      => $stats['admins'],
                    'finance'    => $stats['finance'],
                    'teacher'    => $stats['total_teachers'],
                    'student'    => $stats['total_students'],
                ];
            ?>
            <?php $__currentLoopData = $roleTabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="?role=<?php echo e($key); ?>&sort=<?php echo e($sort); ?>&section=users"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1.5px solid <?php echo e($roleFilter === $key ? 'var(--blue)' : 'var(--border)'); ?>;background:<?php echo e($roleFilter === $key ? 'var(--blue)' : '#fff'); ?>;color:<?php echo e($roleFilter === $key ? '#fff' : 'var(--text)'); ?>;transition:all .15s;">
                <?php echo e($label); ?>

                <span style="background:<?php echo e($roleFilter === $key ? 'rgba(255,255,255,.25)' : 'var(--blue-pale)'); ?>;color:<?php echo e($roleFilter === $key ? '#fff' : 'var(--blue)'); ?>;padding:1px 7px;border-radius:20px;font-size:10px;"><?php echo e($roleCounts[$key]); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
                <div class="dash-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="userSearchInput" placeholder="Search name or email…" oninput="filterUsersTable(this.value)">
                </div>
                <select class="form-fld" style="width:auto;padding:7px 10px;font-size:12px;" onchange="window.location.href='?sort='+this.value+'&role=<?php echo e($roleFilter); ?>&section=users'">
                    <option value="newest" <?php echo e($sort==='newest'?'selected':''); ?>>Newest</option>
                    <option value="oldest" <?php echo e($sort==='oldest'?'selected':''); ?>>Oldest</option>
                    <option value="name_asc" <?php echo e($sort==='name_asc'?'selected':''); ?>>A–Z</option>
                    <option value="name_desc" <?php echo e($sort==='name_desc'?'selected':''); ?>>Z–A</option>
                </select>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <h6>All User Accounts</h6>
                <span style="font-size:12px;color:var(--muted);" id="userCountLabel"><?php echo e($userManagementUsers->total()); ?> account(s)</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $roleBadge = match($user->role) {
                                'superadmin' => ['cls'=>'superadmin','label'=>'Super Admin'],
                                'admin'      => ['cls'=>'admin',      'label'=>'Admin'],
                                'finance'    => ['cls'=>'teacher',    'label'=>'Finance'],
                                default      => ['cls'=>'student',    'label'=>ucfirst($user->role)],
                            };
                        ?>
                        <tr data-user-id="<?php echo e($user->id); ?>" data-user-name="<?php echo e($user->name); ?>" data-user-email="<?php echo e($user->email); ?>" data-user-role="<?php echo e($user->role); ?>" data-user-active="<?php echo e($user->is_active ? '1' : '0'); ?>">
                            <td>
                                <div class="user-row-name">
                                    <div class="user-row-avatar" style="background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;"><?php echo e(strtoupper(substr($user->name,0,2))); ?></div>
                                    <div>
                                        <div style="font-weight:600;"><?php echo e($user->name); ?></div>
                                        <div class="user-row-sub"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="role-badge <?php echo e($roleBadge['cls']); ?>"><?php echo e($roleBadge['label']); ?></span></td>
                            <td>
                                <?php if($user->is_active): ?>
                                    <span class="status-badge active">Active</span>
                                <?php else: ?>
                                    <span class="status-badge inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($user->created_at?->format('M d, Y') ?? '—'); ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'); ?></td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button class="action-btn edit" title="Edit User"
                                        onclick="openEditUserModal(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>','<?php echo e($user->email); ?>','<?php echo e($user->role); ?>',<?php echo e($user->is_active ? 'true' : 'false'); ?>)">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="action-btn lock" title="Reset Password"
                                        onclick="openResetPasswordModal(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                    <button class="action-btn <?php echo e($user->is_active ? 'delete' : 'view'); ?>" title="<?php echo e($user->is_active ? 'Deactivate' : 'Activate'); ?> User"
                                        onclick="confirmToggleStatus(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>',<?php echo e($user->is_active ? 'true' : 'false'); ?>)">
                                        <i class="bi <?php echo e($user->is_active ? 'bi-person-x-fill' : 'bi-person-check-fill'); ?>"></i>
                                    </button>
                                    <?php if($user->id !== auth()->id()): ?>
                                    <button class="action-btn delete" title="Delete User"
                                        onclick="confirmDeleteUser(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                
                <?php if($userManagementUsers->hasPages()): ?>
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <nav>
                        <?php echo e($userManagementUsers->appends(['sort' => $sort, 'role' => $roleFilter, 'section' => 'users'])->links()); ?>

                    </nav>
                    <div class="pagination-info">
                        Showing <?php echo e($userManagementUsers->firstItem()); ?> to <?php echo e($userManagementUsers->lastItem()); ?> of <?php echo e($userManagementUsers->total()); ?> users
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- ═══════════════════════════ -->
    <div id="section-roles" class="dash-section" <?php echo e($section !== 'roles' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>Roles &amp; Permissions</h1>
                <p>Define access levels for each role in the system.</p>
            </div>
            <a href="#" class="btn-dash btn-primary">
                <i class="bi bi-plus-lg"></i> Add Role
            </a>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="content-card">
                    <div class="content-card-header"><h6>Roles</h6></div>
                    <div style="padding:8px;">
                        <div id="role-superadmin" class="perm-module" style="border-color:var(--blue);cursor:pointer;" onclick="selectRole('superadmin')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-fill-check" style="color:#7d3c98;"></i>
                                <span>Super Admin</span>
                                <span class="role-badge superadmin ms-auto">Full Access</span>
                            </div>
                        </div>
                        <div class="perm-module" style="cursor:pointer;" onclick="selectRole('admin')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-fill" style="color:var(--blue);"></i>
                                <span>Admin / Registrar</span>
                                <span class="role-badge admin ms-auto">Admin</span>
                            </div>
                        </div>
                        <div class="perm-module" style="cursor:pointer;" onclick="selectRole('teacher')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-half" style="color:var(--green);"></i>
                                <span>Teacher</span>
                                <span class="role-badge teacher ms-auto">Limited</span>
                            </div>
                        </div>
                        <div class="perm-module" style="cursor:pointer;" onclick="selectRole('student')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield" style="color:#d68910;"></i>
                                <span>Student</span>
                                <span class="role-badge student ms-auto">Read Only</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>Permissions — Super Admin</h6>
                        <button class="btn-dash btn-primary" style="padding:6px 14px;font-size:12px;">
                            <i class="bi bi-floppy-fill"></i> Save Changes
                        </button>
                    </div>
                    <div class="p-3">
                        <div class="perm-module">
                            <div class="perm-module-header">
                                <i class="bi bi-people-fill" style="color:var(--blue);"></i>
                                <span>Student Management</span>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">View Students</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">Create / Edit Students</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">Delete Students</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                        </div>
                        <div class="perm-module">
                            <div class="perm-module-header">
                                <i class="bi bi-journal-text" style="color:var(--gold);"></i>
                                <span>System Logs</span>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">View Logs</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">Delete Logs</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                        </div>
                        <div class="perm-module">
                            <div class="perm-module-header">
                                <i class="bi bi-database-fill" style="color:var(--orange);"></i>
                                <span>Backup &amp; Restore</span>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">Create Backup</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                            <div class="perm-row">
                                <span class="perm-row-name">Restore Backup</span>
                                <div class="perm-checks">
                                    <div class="perm-check"><label>Allow</label><input type="checkbox" checked></div>
                                    <div class="perm-check"><label>Deny</label><input type="checkbox"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-roles -->

    <!-- ═══════════════════════════
         SECTION: SYSTEM LOGS
    ═══════════════════════════ -->
    <div id="section-logs" class="dash-section" <?php echo e($section !== 'logs' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>System Logs</h1>
                <p>Full audit trail of all system activities.</p>
            </div>
            <a href="#" class="btn-dash btn-secondary">
                <i class="bi bi-download"></i> Export Logs
            </a>
        </div>

        <div class="content-card mb-4">
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="dash-search" style="width:100%;">
                            <i class="bi bi-search"></i>
                            <input type="text" id="logSearchInput" placeholder="Search logs…" oninput="filterLogsTable()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-fld" id="logTypeFilter" onchange="filterLogsTable()">
                            <option value="">All Event Types</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="failed_login">Failed Login</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-fld" id="logDateFilter" oninput="filterLogsTable()" value="">
                    </div>
                    <div class="col-md-2">
                        <button class="btn-dash btn-secondary" style="width:100%;" onclick="resetLogFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header"><h6>All Logs</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $badgeClass = match($log->event_type) {
                                'login'        => 'success',
                                'logout'       => 'active',
                                'failed_login' => 'warning',
                                'create'       => 'success',
                                'update'       => 'primary',
                                'delete'       => 'danger',
                                'error'        => 'danger',
                                default        => 'active',
                            };
                            $badgeLabel = match($log->event_type) {
                                'login'        => 'Login',
                                'logout'       => 'Logout',
                                'failed_login' => 'Failed Login',
                                'create'       => 'Create',
                                'update'       => 'Update',
                                'delete'       => 'Delete',
                                'error'        => 'Error',
                                default        => ucfirst($log->event_type),
                            };
                        ?>
                        <tr class="log-row"
                            data-type="<?php echo e($log->event_type); ?>"
                            data-desc="<?php echo e(strtolower($log->description . ' ' . $log->user_name)); ?>"
                            data-ts="<?php echo e($log->created_at?->toDateString()); ?>">
                            <td><span class="status-badge <?php echo e($badgeClass); ?>"><?php echo e($badgeLabel); ?></span></td>
                            <td style="max-width:420px;word-break:break-word;"><?php echo e($log->description); ?></td>
                            <td>
                                <?php if($log->user_name): ?>
                                <div style="font-size:13px;font-weight:600;"><?php echo e($log->user_name); ?></div>
                                <div style="font-size:11px;color:var(--muted);"><?php echo e($log->user_role ?? ''); ?></div>
                                <?php else: ?>
                                <span style="color:var(--muted);font-size:12px;">System</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($log->ip_address ?? '—'); ?></td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                                <?php echo e($log->created_at?->format('M d, Y h:i A')); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                                <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                                No activity logs yet. Events will appear here after users log in.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-logs -->

    <!-- ═══════════════════════════
         SECTION: BACKUP & RESTORE
    ═══════════════════════════ -->
    <div id="section-backup" class="dash-section" <?php echo e($section !== 'backup' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>Backup &amp; Restore</h1>
                <p>Manage database backups and system restoration.</p>
            </div>
            <a href="#" class="btn-dash btn-primary">
                <i class="bi bi-database-fill-up"></i> Create Backup Now
            </a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-database-fill"></i></div>
                    <div>
                        <div class="stat-value">0 MB</div>
                        <div class="stat-label">Total Backup Size</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value">0</div>
                        <div class="stat-label">Total Backups</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <div class="stat-value">—</div>
                        <div class="stat-label">Last Backup</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>Backup History (<?php echo e(count($backups)); ?> file<?php echo e(count($backups) !== 1 ? 's' : ''); ?>)</h6>
                        <button class="btn-dash btn-primary" style="padding:7px 16px;font-size:12px;" onclick="runBackup()" id="btnCreateBackup">
                            <i class="bi bi-database-add"></i> Create Backup Now
                        </button>
                    </div>
                    <div class="p-3">
                        <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="backup-card" id="bk-<?php echo e(Str::slug($bk['name'])); ?>">
                            <div style="flex:1;min-width:0;">
                                <div class="backup-info-name" style="font-size:13px;font-weight:700;color:var(--text);word-break:break-all;"><?php echo e($bk['name']); ?></div>
                                <div class="backup-info-meta" style="font-size:11px;color:var(--muted);margin-top:3px;">
                                    <i class="bi bi-clock me-1"></i><?php echo e($bk['created']); ?> &nbsp;·&nbsp;
                                    <i class="bi bi-hdd me-1"></i><?php echo e($bk['size']); ?>

                                </div>
                            </div>
                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                <a href="<?php echo e(route('superadmin.backup.download', $bk['name'])); ?>" class="action-btn view" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button class="action-btn edit" title="Restore Database" onclick="restoreBackup('<?php echo e($bk['name']); ?>')">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button class="action-btn delete" title="Delete Backup" onclick="deleteBackup('<?php echo e($bk['name']); ?>')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-database" style="font-size:36px;display:block;margin-bottom:10px;opacity:0.25;"></i>
                            <div style="font-weight:600;margin-bottom:6px;">No backups yet</div>
                            <div style="font-size:12px;">Click <strong>Create Backup Now</strong> to generate your first backup.</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card mb-3">
                    <div class="content-card-header"><h6><i class="bi bi-info-circle me-1" style="color:var(--blue);"></i>Backup Info</h6></div>
                    <div class="p-3" style="font-size:13px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Total Backups</span>
                            <strong><?php echo e(count($backups)); ?></strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Latest Backup</span>
                            <strong><?php echo e(count($backups) ? $backups[0]['created'] : '—'); ?></strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Storage Used</span>
                            <?php $totalSize = array_sum(array_column($backups,'size_raw')); ?>
                            <strong><?php echo e($totalSize >= 1048576 ? round($totalSize/1048576,2).' MB' : round($totalSize/1024,1).' KB'); ?></strong>
                        </div>
                        <div style="background:#fff8ec;border:1px solid #fde68a;border-radius:8px;padding:10px;font-size:12px;color:#92400e;margin-top:6px;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Restoring a backup will <strong>overwrite all current data</strong>. This cannot be undone.
                        </div>
                    </div>
                </div>
                <div class="content-card">
                    <div class="content-card-header"><h6><i class="bi bi-terminal me-1" style="color:var(--blue);"></i>Backup Log</h6></div>
                    <div class="p-3" id="backupLog" style="font-size:11px;font-family:monospace;max-height:160px;overflow-y:auto;color:var(--muted);">
                        Waiting for backup operation…
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-backup -->

    <!-- ═══════════════════════════
         SECTION: REPORTS
    ═══════════════════════════ -->
    <!-- ══════════════════════════
         SECTION: ANNOUNCEMENTS
    ══════════════════════════ -->
    <div id="section-announcements" class="dash-section" <?php echo e($section !== 'announcements' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1><i class="bi bi-megaphone-fill me-2" style="color:var(--blue);"></i>Announcements</h1>
                <p>Post and manage school-wide announcements visible on the public website.</p>
            </div>
        </div>

        <?php if(session('sa_success') && session('sa_section') === 'announcements'): ?>
            <div class="alert-success-bar"><i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('sa_success')); ?></div>
        <?php endif; ?>

        
        <div class="content-card mb-4">
            <div class="content-card-header"><h6><i class="bi bi-plus-circle me-2" style="color:var(--blue);"></i>Post New Announcement</h6></div>
            <div class="p-4">
                <form method="POST" action="<?php echo e(route('superadmin.announcements.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-lbl-sm">Title <span style="color:#e53935;">*</span></label>
                            <input type="text" name="title" class="dash-input" placeholder="Announcement title…" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl-sm">Category</label>
                            <select name="category" class="dash-input">
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="reminder">Reminder</option>
                                <option value="activity">Activity</option>
                                <option value="enrollment">Enrollment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl-sm">Audience</label>
                            <select name="audience" class="dash-input">
                                <option value="all">All (Public)</option>
                                <option value="parents">Parents</option>
                                <option value="teachers">Teachers</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl-sm">Cover Image (optional)</label>
                            <input type="file" name="image" class="dash-input" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-12">
                            <label class="form-lbl-sm">Content <span style="color:#e53935;">*</span></label>
                            <textarea name="content" class="dash-input" rows="4" placeholder="Write the announcement here…" required style="resize:vertical;"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-megaphone-fill"></i> Post Announcement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="content-card">
            <div class="content-card-header"><h6>All Announcements (<?php echo e($announcements->total()); ?>)</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Audience</th>
                            <th>Posted By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight:600;max-width:260px;"><?php echo e(Str::limit($ann->title, 60)); ?></td>
                            <td><span class="role-badge teacher"><?php echo e(ucfirst($ann->category)); ?></span></td>
                            <td>
                                <?php if($ann->image): ?>
                                    <img src="<?php echo e(asset('storage/'.$ann->image)); ?>" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span style="font-size:11px;color:var(--muted);">None</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(ucfirst($ann->audience)); ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($ann->teacher?->name ?? 'Superadmin'); ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($ann->created_at->format('M d, Y')); ?></td>
                            <td>
                                <span class="status-badge <?php echo e($ann->is_active ? 'active' : 'inactive'); ?>">
                                    <?php echo e($ann->is_active ? 'Active' : 'Hidden'); ?>

                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <form method="POST" action="<?php echo e(route('superadmin.announcements.toggle', $ann)); ?>" style="margin:0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="action-btn <?php echo e($ann->is_active ? 'edit' : 'view'); ?>" title="<?php echo e($ann->is_active ? 'Hide' : 'Show'); ?>">
                                            <i class="bi bi-<?php echo e($ann->is_active ? 'eye-slash' : 'eye'); ?>-fill"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('superadmin.announcements.destroy', $ann)); ?>" style="margin:0;"
                                          onsubmit="return confirm('Delete this announcement?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-btn delete" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-megaphone" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No announcements yet.
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($announcements->hasPages()): ?>
            <div style="padding:16px;display:flex;justify-content:center;"><?php echo e($announcements->links()); ?></div>
            <?php endif; ?>
        </div>
    </div><!-- /section-announcements -->

    <!-- ══════════════════════════
         SECTION: NEWS
    ══════════════════════════ -->
    <div id="section-news" class="dash-section" <?php echo e($section !== 'news' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1><i class="bi bi-newspaper me-2" style="color:var(--blue);"></i>News</h1>
                <p>Publish and manage news articles displayed on the public website.</p>
            </div>
        </div>

        <?php if(session('sa_success') && session('sa_section') === 'news'): ?>
            <div class="alert-success-bar"><i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('sa_success')); ?></div>
        <?php endif; ?>

        
        <div class="content-card mb-4">
            <div class="content-card-header"><h6><i class="bi bi-plus-circle me-2" style="color:var(--blue);"></i>Publish News Article</h6></div>
            <div class="p-4">
                <form method="POST" action="<?php echo e(route('superadmin.news.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-lbl-sm">Headline <span style="color:#e53935;">*</span></label>
                            <input type="text" name="title" class="dash-input" placeholder="News headline…" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl-sm">Category</label>
                            <select name="category" class="dash-input">
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="events">Events</option>
                                <option value="activity">Activity</option>
                                <option value="achievement">Achievement</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl-sm">Cover Photo (optional)</label>
                            <input type="file" name="image" class="dash-input" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-12">
                            <label class="form-lbl-sm">Body <span style="color:#e53935;">*</span></label>
                            <textarea name="body" class="dash-input" rows="5" placeholder="Write the news article here…" required style="resize:vertical;"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-newspaper"></i> Publish Article
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="content-card">
            <div class="content-card-header"><h6>All News Articles (<?php echo e($newsArticles->total()); ?>)</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Headline</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Posted By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $newsArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight:600;max-width:280px;"><?php echo e(Str::limit($article->title, 60)); ?></td>
                            <td><span class="role-badge student"><?php echo e(ucfirst($article->category)); ?></span></td>
                            <td>
                                <?php if($article->image): ?>
                                    <img src="<?php echo e(asset('storage/'.$article->image)); ?>" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span style="font-size:11px;color:var(--muted);">No image</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($article->author?->name ?? 'Superadmin'); ?></td>
                            <td style="font-size:12px;color:var(--muted);"><?php echo e($article->created_at->format('M d, Y')); ?></td>
                            <td>
                                <span class="status-badge <?php echo e($article->is_active ? 'active' : 'inactive'); ?>">
                                    <?php echo e($article->is_active ? 'Published' : 'Hidden'); ?>

                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <form method="POST" action="<?php echo e(route('superadmin.news.toggle', $article)); ?>" style="margin:0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="action-btn <?php echo e($article->is_active ? 'edit' : 'view'); ?>" title="<?php echo e($article->is_active ? 'Hide' : 'Show'); ?>">
                                            <i class="bi bi-<?php echo e($article->is_active ? 'eye-slash' : 'eye'); ?>-fill"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('superadmin.news.destroy', $article)); ?>" style="margin:0;"
                                          onsubmit="return confirm('Delete this news article?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="action-btn delete" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-newspaper" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No news articles yet.
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($newsArticles->hasPages()): ?>
            <div style="padding:16px;display:flex;justify-content:center;"><?php echo e($newsArticles->links()); ?></div>
            <?php endif; ?>
        </div>
    </div><!-- /section-news -->

    <div id="section-reports" class="dash-section" <?php echo e($section !== 'reports' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>Reports</h1>
                <p>System-wide reports and analytics.</p>
            </div>
        </div>
        <!-- Report Charts -->
        <?php
            $saRptMonths=[]; $saRptEnroll=[];
            for($i=5;$i>=0;$i--){
                $m=now()->subMonths($i);
                $saRptMonths[]=$m->format('M Y');
                $saRptEnroll[]=\App\Models\Enrollment::whereYear('created_at',$m->year)->whereMonth('created_at',$m->month)->count();
            }
        ?>
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-graph-up me-2" style="color:var(--blue);"></i>Enrollment Trend (Last 6 Months)</h6>
                    </div>
                    <div class="p-3" style="height:220px;"><canvas id="saRptEnrollLine"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-bar-chart-fill me-2" style="color:var(--blue);"></i>System User Distribution</h6>
                    </div>
                    <div class="p-3" style="height:220px;"><canvas id="saRptUserBar"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-people-fill" style="font-size:36px;color:var(--blue);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">User Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Complete list of all system users by role.</p>
                        <a href="#" class="btn-dash btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-journal-text" style="font-size:36px;color:var(--gold);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">Audit Log Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">All system activities and audit trail.</p>
                        <a href="#" class="btn-dash btn-gold" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-database-fill" style="font-size:36px;color:var(--orange);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">System Health Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Database size, backups, and performance.</p>
                        <a href="#" class="btn-dash btn-success" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-reports -->

    <!-- ═══════════════════════════
         SECTION: SETTINGS
    ═══════════════════════════ -->
    <div id="section-settings" class="dash-section" <?php echo e($section !== 'settings' ? 'style="display:none;"' : ''); ?>>
        <div class="section-header">
            <div>
                <h1>Settings</h1>
                <p>Manage your account and system preferences.</p>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-5">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-person-circle me-2" style="color:var(--blue);"></i>Account Information</h6>
                    </div>
                    <div class="p-4">

                        <?php if(session('photo_success')): ?>
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-check-circle-fill"></i> <?php echo e(session('photo_success')); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('superadmin.settings.photo')); ?>" enctype="multipart/form-data" id="sa-photo-form">
                            <?php echo csrf_field(); ?>
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                                <div style="position:relative;flex-shrink:0;width:72px;height:72px;">
                                    <?php if(Auth::user()->profile_photo): ?>
                                        <img id="sa-avatar-img" src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
                                    <?php else: ?>
                                        <div id="sa-avatar-placeholder" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-person-fill" style="font-size:30px;color:#fff;"></i>
                                        </div>
                                        <img id="sa-avatar-img" src="" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);display:none;">
                                    <?php endif; ?>
                                    <label for="sa-photo-input" title="Change photo"
                                           style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:var(--blue);border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                        <i class="bi bi-camera-fill" style="font-size:11px;color:#fff;"></i>
                                    </label>
                                    <input type="file" id="sa-photo-input" name="photo" accept="image/jpeg,image/png,image/webp"
                                           style="display:none;" onchange="previewSAPhoto(this)">
                                </div>
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:var(--text);"><?php echo e(Auth::user()->name); ?></div>
                                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e(Auth::user()->email); ?></div>
                                    <span style="display:inline-block;background:#fdecea;color:#c0392b;border:1px solid #f5c6cb;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px;">Super Admin</span>
                                </div>
                            </div>
                            <button type="submit" id="sa-photo-submit" style="display:none;"></button>
                        </form>

                        <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;flex-direction:column;gap:0;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Full Name</span>
                                <span style="font-size:12px;font-weight:600;"><?php echo e(Auth::user()->name); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Email</span>
                                <span style="font-size:12px;font-weight:600;"><?php echo e(Auth::user()->email); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;">
                                <span style="font-size:12px;color:var(--muted);">Role</span>
                                <span style="font-size:12px;font-weight:600;">Super Admin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-lock-fill me-2" style="color:var(--blue);"></i>Change Password</h6>
                    </div>
                    <div class="p-4">
                        <?php if(session('password_success')): ?>
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-check-circle-fill"></i> <?php echo e(session('password_success')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($errors->has('current_password')): ?>
                            <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-exclamation-circle-fill"></i> <?php echo e($errors->first('current_password')); ?>

                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('superadmin.settings.password')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="mb-3">
                                <label class="form-lbl">Current Password <span style="color:var(--red);">*</span></label>
                                <input type="password" name="current_password" class="form-fld" placeholder="Enter your current password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" name="password" class="form-fld" placeholder="At least 8 characters" required minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Confirm New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" name="password_confirmation" class="form-fld" placeholder="Re-enter new password" required>
                            </div>
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-lock-fill"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-settings -->

    <!-- ═══════════════════════════
         SECTION: ENROLLMENTS
    ═══════════════════════════ -->
    <div id="section-enrollments" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Enrollment Management</h1>
                <p>Review and manage student enrollment applications.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <select id="enr-status-filter" class="form-fld" style="width:160px;" onchange="loadEnrollments()">
                    <option value="all">All Status</option>
                    <option value="submitted">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="enrolled">Enrolled</option>
                    <option value="declined">Declined</option>
                </select>
                <button class="btn-dash btn-primary" onclick="loadEnrollments()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
        <div class="row g-3 mb-4" id="enr-stats">
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div><div><div class="stat-value" id="enr-cnt-submitted">—</div><div class="stat-label">Pending</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div><div><div class="stat-value" id="enr-cnt-approved">—</div><div class="stat-label">Approved</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div><div><div class="stat-value" id="enr-cnt-enrolled">—</div><div class="stat-label">Enrolled</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div><div><div class="stat-value" id="enr-cnt-declined">—</div><div class="stat-label">Declined</div></div></div></div>
        </div>
        <div class="content-card">
            <div class="content-card-header"><h6>Enrollment Applications</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead><tr>
                        <th>Ref #</th><th>Student Name</th><th>Grade Level</th>
                        <th>Date</th><th>Status</th><th>Payment</th><th>Actions</th>
                    </tr></thead>
                    <tbody id="enr-tbody">
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-enrollments -->

    <!-- ═══════════════════════════
         SECTION: TEACHERS
    ═══════════════════════════ -->
    <div id="section-teachers" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Teacher Management</h1>
                <p>Manage teacher accounts and assignments.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-dash btn-primary" onclick="openAddTeacherModal()">
                    <i class="bi bi-person-plus-fill"></i> Add Teacher
                </button>
                <button class="btn-dash btn-secondary" onclick="loadTeachers()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
        <div class="content-card">
            <div class="content-card-header">
                <h6>All Teachers</h6>
                <div class="dash-search" style="width:220px;">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search teachers…" oninput="filterTeachersTable(this.value)">
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead><tr>
                        <th>Name</th><th>Email</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody id="teachers-tbody">
                        <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-teachers -->

    <!-- ═══════════════════════════
         SECTION: SUBJECTS
    ═══════════════════════════ -->
    <div id="section-subjects" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Subject Management</h1>
                <p>Manage curriculum subjects and grade level assignments.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-dash btn-primary" onclick="openAddSubjectModal()">
                    <i class="bi bi-plus-lg"></i> Add Subject
                </button>
                <button class="btn-dash btn-secondary" onclick="loadSubjects()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
        <div class="content-card">
            <div class="content-card-header">
                <h6>All Subjects</h6>
                <select id="subj-grade-filter" class="form-fld" style="width:180px;" onchange="loadSubjects()">
                    <option value="">All Grade Levels</option>
                    <option value="nursery">Nursery</option>
                    <option value="kindergarten">Kindergarten</option>
                    <option value="grade1">Grade 1</option>
                    <option value="grade2">Grade 2</option>
                    <option value="grade3">Grade 3</option>
                    <option value="grade4">Grade 4</option>
                    <option value="grade5">Grade 5</option>
                    <option value="grade6">Grade 6</option>
                </select>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead><tr>
                        <th>Name</th><th>Code</th><th>Grade Level</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody id="subjects-tbody">
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-subjects -->

    <!-- ═══════════════════════════
         SECTION: SECTIONS MGMT
    ═══════════════════════════ -->
    <div id="section-sections-mgmt" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Section Management</h1>
                <p>Manage class sections, teacher assignments, and student lists.</p>
            </div>
            <button class="btn-dash btn-secondary" onclick="loadSectionsMgmt()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
        <div class="content-card">
            <div class="content-card-header">
                <h6>All Sections</h6>
                <select id="sec-grade-filter" class="form-fld" style="width:180px;" onchange="loadSectionsMgmt()">
                    <option value="">All Grade Levels</option>
                    <option value="nursery">Nursery</option>
                    <option value="kindergarten">Kindergarten</option>
                    <option value="grade1">Grade 1</option>
                    <option value="grade2">Grade 2</option>
                    <option value="grade3">Grade 3</option>
                    <option value="grade4">Grade 4</option>
                    <option value="grade5">Grade 5</option>
                    <option value="grade6">Grade 6</option>
                </select>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead><tr>
                        <th>Section Name</th><th>Grade Level</th><th>Adviser / Teacher</th><th>Students</th><th>Actions</th>
                    </tr></thead>
                    <tbody id="sections-tbody">
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-sections-mgmt -->

    <!-- ═══════════════════════════
         SECTION: SCHEDULES
    ═══════════════════════════ -->
    <div id="section-schedules" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Schedule Management</h1>
                <p>View and manage class schedules per section.</p>
            </div>
            <button class="btn-dash btn-secondary" onclick="loadSchedules()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <select id="sched-section-filter" class="form-fld" onchange="loadSchedules()">
                    <option value="">All Sections</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="sched-day-filter" class="form-fld" onchange="filterSchedulesTable()">
                    <option value="">All Days</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>
        </div>
        <div class="content-card">
            <div class="content-card-header"><h6>Class Schedules</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead><tr>
                        <th>Section</th><th>Subject</th><th>Teacher</th>
                        <th>Day</th><th>Time</th><th>Room</th>
                    </tr></thead>
                    <tbody id="schedules-tbody">
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-schedules -->

    <!-- ═══════════════════════════
         SECTION: SYSTEM SETTINGS
    ═══════════════════════════ -->
    <div id="section-sys-settings" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>System Settings</h1>
                <p>Control enrollment, maintenance mode, and system configuration.</p>
            </div>
            <button class="btn-dash btn-secondary" onclick="loadSysSettings()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="row g-4">
            <!-- Enrollment Toggle -->
            <div class="col-md-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-clipboard-check-fill me-2" style="color:var(--blue);"></i>Enrollment Status</h6>
                    </div>
                    <div class="p-4">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--text);">Online Enrollment</div>
                                <div style="font-size:12px;color:var(--muted);margin-top:3px;" id="enroll-toggle-desc">Loading current status…</div>
                            </div>
                            <div id="enroll-toggle-wrap" style="display:flex;align-items:center;gap:12px;">
                                <span id="enroll-toggle-label" style="font-size:12px;font-weight:700;"></span>
                                <button id="btn-toggle-enrollment" onclick="toggleEnrollment()"
                                    style="padding:9px 20px;border-radius:8px;border:none;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                                    Toggle
                                </button>
                            </div>
                        </div>
                        <div style="font-size:12px;color:var(--muted);background:#f8fafc;border-radius:8px;padding:10px 14px;line-height:1.6;">
                            When enrollment is <strong>Open</strong>, students can submit applications through the website.
                            When <strong>Closed</strong>, the enrollment form is hidden and applicants see a notice.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Toggle -->
            <div class="col-md-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-tools me-2" style="color:var(--orange);"></i>Maintenance Mode</h6>
                    </div>
                    <div class="p-4">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--text);">Maintenance Mode</div>
                                <div style="font-size:12px;color:var(--muted);margin-top:3px;" id="maint-toggle-desc">Loading current status…</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <span id="maint-toggle-label" style="font-size:12px;font-weight:700;"></span>
                                <button id="btn-toggle-maintenance" onclick="toggleMaintenance()"
                                    style="padding:9px 20px;border-radius:8px;border:none;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                                    Toggle
                                </button>
                            </div>
                        </div>
                        <div style="font-size:12px;color:var(--muted);background:#fef5e7;border-radius:8px;padding:10px 14px;line-height:1.6;">
                            <i class="bi bi-exclamation-triangle-fill me-1" style="color:var(--orange);"></i>
                            When maintenance mode is <strong>ON</strong>, the public website displays a maintenance notice.
                            Admin and portal access is not affected.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Quick View -->
            <div class="col-md-12">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-sliders me-2" style="color:var(--blue);"></i>System Settings Overview</h6>
                    </div>
                    <div id="sys-settings-overview" style="padding:16px;">
                        <div style="text-align:center;padding:20px;color:var(--muted);">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading settings…
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-sys-settings -->

</div><!-- /dash-main -->

<div id="sa-toast-wrap"></div>

<!-- ══════════════════════════════════════
     CREATE USER MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:22px 26px;position:relative;">
                <div style="display:flex;align-items:center;gap:13px;">
                    <div style="width:44px;height:44px;border-radius:13px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-plus-fill" style="font-size:21px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:17px;font-weight:800;color:#fff;">Create New User</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:2px;">Add a new account to the system</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:16px;right:18px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:22px 24px;background:#fff;">
                <form id="createUserForm">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-lbl">Full Name</label>
                        <input type="text" id="user-name" name="name" class="form-fld" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Email Address</label>
                        <input type="email" id="user-email" name="email" class="form-fld" placeholder="Enter email address" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-lbl">Password</label>
                            <div style="position:relative;">
                                <input type="password" id="user-password" name="password" class="form-fld" placeholder="Min. 8 characters" required minlength="8" style="padding-right:38px;">
                                <button type="button" onclick="togglePwdVis('user-password','cu-eye1')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:15px;padding:0;">
                                    <i class="bi bi-eye-fill" id="cu-eye1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-lbl">Confirm Password</label>
                            <div style="position:relative;">
                                <input type="password" id="user-password-confirm" name="password_confirmation" class="form-fld" placeholder="Repeat password" required minlength="8" style="padding-right:38px;">
                                <button type="button" onclick="togglePwdVis('user-password-confirm','cu-eye2')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:15px;padding:0;">
                                    <i class="bi bi-eye-fill" id="cu-eye2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Role</label>
                        <select id="user-role" name="role" class="form-fld" required>
                            <option value="">-- Select Role --</option>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin / Registrar</option>
                            <option value="finance">Finance / Cashier</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-check" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" id="user-active" name="is_active" value="1" class="form-check-input" checked>
                            <span class="form-check-label" style="font-size:13px;">Account Active</span>
                        </label>
                    </div>
                </form>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" onclick="createUser()" id="btnCreateUser"
                    style="padding:10px 22px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:7px;">
                    <i class="bi bi-person-plus-fill"></i>Create User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     EDIT USER MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;" id="edit-modal-avatar">U</div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#fff;">Edit Account</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:1px;" id="edit-modal-sub">Update user details</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:20px 24px;background:#fff;">
                <form id="editUserForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit-user-id" name="id">
                    <div class="mb-3">
                        <label class="form-lbl">Full Name</label>
                        <input type="text" id="edit-user-name" name="name" class="form-fld" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Email Address</label>
                        <input type="email" id="edit-user-email" name="email" class="form-fld" placeholder="Enter email address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Role</label>
                        <select id="edit-user-role" name="role" class="form-fld" required>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin / Registrar</option>
                            <option value="finance">Finance / Cashier</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-check" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" id="edit-user-active" name="is_active" value="1" class="form-check-input">
                            <span class="form-check-label" style="font-size:13px;">Account Active</span>
                        </label>
                    </div>
                </form>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" onclick="updateUser()" id="btnUpdateUser"
                    style="padding:10px 22px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:7px;">
                    <i class="bi bi-floppy-fill"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     RESET PASSWORD MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div style="background:linear-gradient(135deg,#92400e,#d97706);padding:20px 24px;position:relative;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:13px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-key-fill" style="font-size:20px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#fff;">Reset Password</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.7);margin-top:1px;" id="reset-modal-sub">Set a new password for the user</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:20px 24px;background:#fff;">
                <form id="resetPasswordForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="reset-user-id">
                    <div class="mb-3">
                        <label class="form-lbl">New Password</label>
                        <div style="position:relative;">
                            <input type="password" id="reset-password" class="form-fld" placeholder="Min. 8 characters" required minlength="8" style="padding-right:38px;">
                            <button type="button" onclick="togglePwdVis('reset-password','rp-eye1')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:15px;padding:0;">
                                <i class="bi bi-eye-fill" id="rp-eye1"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="form-lbl">Confirm New Password</label>
                        <div style="position:relative;">
                            <input type="password" id="reset-password-confirm" class="form-fld" placeholder="Repeat password" required minlength="8" style="padding-right:38px;">
                            <button type="button" onclick="togglePwdVis('reset-password-confirm','rp-eye2')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:15px;padding:0;">
                                <i class="bi bi-eye-fill" id="rp-eye2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div style="padding:14px 24px;background:#fffbeb;border-top:1.5px solid #fde68a;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" onclick="resetUserPassword()" id="btnResetPwd"
                    style="padding:10px 22px;background:linear-gradient(135deg,#92400e,#d97706);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:7px;">
                    <i class="bi bi-key-fill"></i>Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     CONFIRM ACTION MODAL (delete / toggle)
══════════════════════════════════════ -->
<div class="modal fade" id="saConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div id="saConfirmHeader" style="padding:20px 24px;position:relative;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div id="saConfirmIconWrap" style="width:42px;height:42px;border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:rgba(255,255,255,.15);">
                        <i id="saConfirmIcon" class="bi bi-question-circle-fill" style="font-size:21px;color:#fff;"></i>
                    </div>
                    <div>
                        <div id="saConfirmTitle" style="font-size:16px;font-weight:800;color:#fff;">Confirm Action</div>
                        <div id="saConfirmSub" style="font-size:11px;color:rgba(255,255,255,.7);margin-top:1px;">Please confirm to proceed</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:18px 24px;background:#fff;">
                <p id="saConfirmMsg" style="font-size:14px;color:#374151;margin:0;line-height:1.6;"></p>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
                <button type="button" id="saConfirmBtn"
                    style="padding:10px 22px;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;color:#fff;display:flex;align-items:center;gap:7px;">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sections = [
        'dashboard','users','roles','logs','backup','reports','settings',
        'enrollments','teachers','subjects','sections-mgmt','schedules','sys-settings',
        'announcements','news'
    ];

    // Track which sections have been loaded (avoid duplicate fetches)
    const _loaded = {};

    function showSection(name) {
        sections.forEach(s => {
            const el = document.getElementById('section-' + s);
            if (el) el.style.display = s === name ? '' : 'none';
            const nav = document.getElementById('nav-' + s);
            if (nav) nav.classList.toggle('active', s === name);
        });
        const url = new URL(window.location.href);
        url.searchParams.set('section', name);
        window.history.replaceState({}, '', url.toString());
        window.scrollTo({ top: 0, behavior: 'smooth' });
        applySectionSkeleton(name);

        // Lazy-load data panels on first open
        if (!_loaded[name]) {
            _loaded[name] = true;
            if (name === 'enrollments')   loadEnrollments();
            if (name === 'teachers')      loadTeachers();
            if (name === 'subjects')      loadSubjects();
            if (name === 'sections-mgmt') loadSectionsMgmt();
            if (name === 'schedules')     loadSchedules();
            if (name === 'sys-settings')  loadSysSettings();
        }
        if (name === 'reports') setTimeout(initSaReportsCharts, 80);
        return false;
    }

    function applySectionSkeleton(name) {
        var el = document.getElementById('section-' + name);
        if (!el) return;
        var old = el.querySelector('.p-skel'); if (old) old.remove();
        var s = document.createElement('div');
        s.className = 'p-skel';
        s.innerHTML = _buildSkelHTML();
        el.appendChild(s);
        setTimeout(function() {
            s.style.opacity = '0';
            setTimeout(function() { if (s.parentNode) s.remove(); }, 320);
        }, 350);
    }
    function _buildSkelHTML() {
        var c = '', r = '', i;
        for (i = 0; i < 4; i++) c += '<div style="background:#fff;border-radius:10px;padding:18px;display:flex;gap:12px;align-items:center;border:1px solid #e2e8f0;"><span class="skel" style="width:44px;height:44px;border-radius:10px;flex-shrink:0;"></span><div style="flex:1"><span class="skel" style="height:22px;width:55px;display:block;border-radius:4px;margin-bottom:6px;"></span><span class="skel" style="height:11px;width:75px;display:block;border-radius:4px;"></span></div></div>';
        for (i = 0; i < 5; i++) r += '<div style="padding:13px 18px;border-bottom:1px solid #f5f5f5;display:flex;gap:14px;align-items:center;"><span class="skel" style="width:30px;height:30px;border-radius:50%;flex-shrink:0;"></span><span class="skel" style="height:12px;flex:1;display:block;border-radius:4px;"></span><span class="skel" style="height:12px;width:90px;display:block;border-radius:4px;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;display:block;"></span></div>';
        return '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;"><div><span class="skel" style="height:24px;width:190px;display:block;border-radius:4px;margin-bottom:8px;"></span><span class="skel" style="height:12px;width:140px;display:block;border-radius:4px;"></span></div><span class="skel" style="height:38px;width:110px;border-radius:8px;display:block;"></span></div><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:22px;">' + c + '</div><div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;"><div style="padding:14px 18px;border-bottom:1px solid #f0f0f0;"><span class="skel" style="height:15px;width:130px;display:block;border-radius:4px;"></span></div>' + r + '</div>';
    }

    // On page load, show section from URL parameter (preserves section across pagination/filtering)
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('settings_tab') || session('photo_success') || session('password_success') || $errors->has('current_password')): ?>
        showSection('settings');
        <?php else: ?>
        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section') || '<?php echo e($section); ?>';
        if (section && sections.includes(section)) {
            showSection(section);
        }
        <?php endif; ?>
    });

    function previewSAPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('sa-avatar-img');
            const placeholder = document.getElementById('sa-avatar-placeholder');
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            const chipAvatar = document.querySelector('.user-avatar');
            if (chipAvatar) {
                chipAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
                chipAvatar.style.background = 'none';
                chipAvatar.style.overflow = 'hidden';
            }
            document.getElementById('sa-photo-form').submit();
        };
        reader.readAsDataURL(input.files[0]);
    }

    function selectRole(role) {
        document.querySelectorAll('[id^="role-"]').forEach(el => el.style.borderColor = '');
        const el = document.getElementById('role-' + role);
        if (el) el.style.borderColor = 'var(--blue)';
    }

    // Auto-capitalize first letter of each word on text inputs
    function capitalizeFirst(input) {
        if (!input.value) return;
        var pos = input.selectionStart;
        input.value = input.value.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
        input.setSelectionRange(pos, pos);
    }
    document.addEventListener('input', function(e) {
        var el = e.target;
        var isText = (el.tagName === 'INPUT' && el.type === 'text');
        var isTextarea = (el.tagName === 'TEXTAREA');
        if (!isText && !isTextarea) return;
        var skip = ['email','search','password','school_year','zip'];
        var name = (el.name || '').toLowerCase();
        var id = (el.id || '').toLowerCase();
        var ph = (el.placeholder || '').toLowerCase();
        if (skip.some(function(s) { return name.indexOf(s) > -1 || id.indexOf(s) > -1; })) return;
        if (ph.indexOf('search') > -1 || ph.indexOf('09') === 0 || ph.indexOf('20') === 0) return;
        capitalizeFirst(el);
    });

    // ── Toast helper ──────────────────────────────────────────
    function saToast(type, message, sub) {
        var wrap = document.getElementById('sa-toast-wrap');
        if (!wrap) return;
        var icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill' };
        var el = document.createElement('div');
        el.className = 'sa-toast sa-' + type;
        el.innerHTML =
            '<div class="sa-toast-icon"><i class="bi ' + (icons[type] || icons.success) + '"></i></div>' +
            '<div><div class="sa-toast-msg">' + message + '</div>' + (sub ? '<div class="sa-toast-sub">' + sub + '</div>' : '') + '</div>';
        wrap.appendChild(el);
        setTimeout(function() { el.style.opacity = '0'; el.style.transform = 'translateX(30px)'; el.style.transition = 'all .3s'; setTimeout(function() { el.remove(); }, 300); }, 3500);
    }

    // ── User Management ────────────────────────────────────────
    var createUserModal, editUserModal, resetPasswordModal, saConfirmModal;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        createUserModal    = new bootstrap.Modal(document.getElementById('createUserModal'));
        editUserModal      = new bootstrap.Modal(document.getElementById('editUserModal'));
        resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        saConfirmModal     = new bootstrap.Modal(document.getElementById('saConfirmModal'));
    });

    // Password visibility toggle
    function togglePwdVis(inputId, eyeId) {
        var input = document.getElementById(inputId);
        var eye   = document.getElementById(eyeId);
        if (!input || !eye) return;
        if (input.type === 'password') { input.type = 'text';     eye.className = 'bi bi-eye-slash-fill'; }
        else                           { input.type = 'password'; eye.className = 'bi bi-eye-fill'; }
    }

    // Generic confirm modal
    function saConfirm(opts) {
        // opts: { title, sub, msg, icon, gradient, btnLabel, btnColor, onConfirm }
        document.getElementById('saConfirmTitle').textContent   = opts.title   || 'Confirm';
        document.getElementById('saConfirmSub').textContent     = opts.sub     || '';
        document.getElementById('saConfirmMsg').textContent     = opts.msg     || 'Are you sure?';
        document.getElementById('saConfirmIcon').className      = 'bi ' + (opts.icon || 'bi-question-circle-fill');
        document.getElementById('saConfirmHeader').style.background = opts.gradient || 'linear-gradient(135deg,#1a3a6c,#2563eb)';
        var btn = document.getElementById('saConfirmBtn');
        btn.textContent   = '';
        btn.innerHTML     = '<i class="bi ' + (opts.icon || 'bi-check-circle-fill') + ' me-1"></i>' + (opts.btnLabel || 'Confirm');
        btn.style.background = opts.btnColor || 'linear-gradient(135deg,#1a3a6c,#2563eb)';
        btn.onclick = function() { saConfirmModal.hide(); if (opts.onConfirm) opts.onConfirm(); };
        saConfirmModal.show();
    }

    function openCreateUserModal() {
        document.getElementById('createUserForm').reset();
        document.getElementById('user-active').checked = true;
        // Reset eye icons
        ['cu-eye1','cu-eye2'].forEach(function(id) {
            var el = document.getElementById(id); if (el) el.className = 'bi bi-eye-fill';
        });
        createUserModal.show();
    }

    function openEditUserModal(userId, userName, userEmail, userRole, isActive) {
        document.getElementById('edit-user-id').value    = userId;
        document.getElementById('edit-user-name').value  = userName;
        document.getElementById('edit-user-email').value = userEmail;
        document.getElementById('edit-user-role').value  = userRole;
        document.getElementById('edit-user-active').checked = (isActive === true || isActive === 'true' || isActive === 1);
        // Update avatar & subtitle in header
        var avatar = document.getElementById('edit-modal-avatar');
        var sub    = document.getElementById('edit-modal-sub');
        if (avatar) avatar.textContent = (userName || 'U').substring(0, 2).toUpperCase();
        if (sub)    sub.textContent    = userEmail;
        editUserModal.show();
    }

    function openResetPasswordModal(userId, userName) {
        document.getElementById('reset-user-id').value          = userId;
        document.getElementById('reset-password').value         = '';
        document.getElementById('reset-password-confirm').value = '';
        // Reset eye icons
        ['rp-eye1','rp-eye2'].forEach(function(id) {
            var el = document.getElementById(id); if (el) el.className = 'bi bi-eye-fill';
        });
        var sub = document.getElementById('reset-modal-sub');
        if (sub && userName) sub.textContent = 'Resetting password for: ' + userName;
        resetPasswordModal.show();
    }

    function setBtnLoading(id, loading, originalHtml) {
        var btn = document.getElementById(id);
        if (!btn) return;
        btn.disabled = loading;
        if (loading) btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing…';
        else         btn.innerHTML = originalHtml;
    }

    async function createUser() {
        var name     = document.getElementById('user-name').value.trim();
        var email    = document.getElementById('user-email').value.trim();
        var password = document.getElementById('user-password').value;
        var confirm  = document.getElementById('user-password-confirm').value;
        var role     = document.getElementById('user-role').value;
        var isActive = document.getElementById('user-active').checked ? 1 : 0;

        if (!name || !email || !password || !role) { saToast('warning', 'All fields are required.'); return; }
        if (password !== confirm) { saToast('error', 'Passwords do not match.'); return; }
        if (password.length < 8)  { saToast('warning', 'Password must be at least 8 characters.'); return; }

        setBtnLoading('btnCreateUser', true);
        try {
            var r = await fetch('/superadmin/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, password, password_confirmation: confirm, role, is_active: isActive })
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', 'User created!', name + ' · ' + role);
                createUserModal.hide();
                setTimeout(() => location.reload(), 1200);
            } else {
                saToast('error', d.message || (d.errors ? Object.values(d.errors).flat().join(', ') : 'Error creating user.'));
            }
        } catch(e) { saToast('error', 'Network error. Please try again.'); }
        setBtnLoading('btnCreateUser', false, '<i class="bi bi-person-plus-fill"></i>Create User');
    }

    async function updateUser() {
        var userId   = document.getElementById('edit-user-id').value;
        var name     = document.getElementById('edit-user-name').value.trim();
        var email    = document.getElementById('edit-user-email').value.trim();
        var role     = document.getElementById('edit-user-role').value;
        var isActive = document.getElementById('edit-user-active').checked ? 1 : 0;

        if (!name || !email || !role) { saToast('warning', 'All fields are required.'); return; }

        setBtnLoading('btnUpdateUser', true);
        try {
            var r = await fetch('/superadmin/users/' + userId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, role, is_active: isActive })
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', 'User updated!', name);
                editUserModal.hide();
                setTimeout(() => location.reload(), 1200);
            } else {
                saToast('error', d.message || (d.errors ? Object.values(d.errors).flat().join(', ') : 'Error updating user.'));
            }
        } catch(e) { saToast('error', 'Network error. Please try again.'); }
        setBtnLoading('btnUpdateUser', false, '<i class="bi bi-floppy-fill"></i>Save Changes');
    }

    function confirmToggleStatus(userId, userName, isCurrentlyActive) {
        saConfirm({
            title    : isCurrentlyActive ? 'Deactivate Account' : 'Activate Account',
            sub      : userName,
            msg      : isCurrentlyActive
                ? 'This will prevent "' + userName + '" from logging in. You can reactivate at any time.'
                : 'This will allow "' + userName + '" to log in again.',
            icon     : isCurrentlyActive ? 'bi-person-x-fill' : 'bi-person-check-fill',
            gradient : isCurrentlyActive ? 'linear-gradient(135deg,#b91c1c,#dc2626)' : 'linear-gradient(135deg,#14532d,#16a34a)',
            btnLabel : isCurrentlyActive ? 'Deactivate' : 'Activate',
            btnColor : isCurrentlyActive ? '#dc2626' : '#16a34a',
            onConfirm: function() { toggleUserStatus(userId); }
        });
    }

    async function toggleUserStatus(userId) {
        try {
            var r = await fetch('/superadmin/users/' + userId + '/toggle-status', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', d.is_active ? 'Account activated.' : 'Account deactivated.');
                setTimeout(() => location.reload(), 1000);
            } else { saToast('error', d.message || 'Error updating status.'); }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    function confirmDeleteUser(userId, userName) {
        saConfirm({
            title    : 'Delete User',
            sub      : userName,
            msg      : 'Are you sure you want to permanently delete "' + userName + '"? This action cannot be undone.',
            icon     : 'bi-trash-fill',
            gradient : 'linear-gradient(135deg,#7f1d1d,#dc2626)',
            btnLabel : 'Delete User',
            btnColor : '#dc2626',
            onConfirm: function() { deleteUser(userId, userName); }
        });
    }

    async function deleteUser(userId, userName) {
        try {
            var r = await fetch('/superadmin/users/' + userId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', 'User deleted.', userName);
                setTimeout(() => location.reload(), 1200);
            } else { saToast('error', d.message || 'Error deleting user.'); }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    async function resetUserPassword() {
        var userId   = document.getElementById('reset-user-id').value;
        var password = document.getElementById('reset-password').value;
        var confirm  = document.getElementById('reset-password-confirm').value;

        if (!password || password.length < 8) { saToast('warning', 'Password must be at least 8 characters.'); return; }
        if (password !== confirm) { saToast('error', 'Passwords do not match.'); return; }

        setBtnLoading('btnResetPwd', true);
        try {
            var fd = new FormData();
            fd.append('password', password);
            fd.append('password_confirmation', confirm);
            var r = await fetch('/superadmin/users/' + userId + '/reset-password', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', 'Password reset successfully!');
                resetPasswordModal.hide();
            } else { saToast('error', d.message || 'Error resetting password.'); }
        } catch(e) { saToast('error', 'Network error.'); }
        setBtnLoading('btnResetPwd', false, '<i class="bi bi-key-fill"></i>Reset Password');
    }

    // ── Client-side table search (users) ──────────────────────
    function filterUsersTable(query) {
        var q = (query || '').toLowerCase();
        var rows = document.querySelectorAll('#section-users .dash-table tbody tr');
        var shown = 0;
        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase();
            var match = !q || text.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) shown++;
        });
        var label = document.getElementById('userCountLabel');
        if (label) label.textContent = shown + ' user(s)';
    }

    // ── Logs filter ────────────────────────────────────────────
    function filterLogsTable() {
        var search = (document.getElementById('logSearchInput').value || '').toLowerCase();
        var type   = (document.getElementById('logTypeFilter').value  || '').toLowerCase();
        var date   = (document.getElementById('logDateFilter').value  || '');
        var rows   = document.querySelectorAll('.log-row');
        var shown  = 0;
        rows.forEach(function(row) {
            var matchSearch = !search || row.dataset.desc.includes(search);
            var matchType   = !type   || row.dataset.type === type;
            var matchDate   = !date   || (row.dataset.ts || '').includes(date);
            var visible = matchSearch && matchType && matchDate;
            row.style.display = visible ? '' : 'none';
            if (visible) shown++;
        });
    }

    function resetLogFilters() {
        document.getElementById('logSearchInput').value = '';
        document.getElementById('logTypeFilter').value  = '';
        document.getElementById('logDateFilter').value  = '';
        filterLogsTable();
    }

    // ── Backup functions ──────────────────────────────────────
    function backupLog(msg) {
        var el = document.getElementById('backupLog');
        if (!el) return;
        var line = document.createElement('div');
        line.textContent = '[' + new Date().toLocaleTimeString() + '] ' + msg;
        el.appendChild(line);
        el.scrollTop = el.scrollHeight;
    }

    async function runBackup() {
        var btn = document.getElementById('btnCreateBackup');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating…';
        backupLog('Starting database backup…');
        try {
            var r = await fetch('<?php echo e(route("superadmin.backup.create")); ?>', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                backupLog('✓ ' + d.message);
                saToast('success', 'Backup created!', d.filename);
                setTimeout(() => location.reload(), 1500);
            } else {
                backupLog('✗ ' + d.message);
                saToast('error', 'Backup failed', d.message);
            }
        } catch(e) {
            backupLog('✗ Network error: ' + e.message);
            saToast('error', 'Network error.');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-database-add"></i> Create Backup Now';
    }

    async function deleteBackup(filename) {
        if (!confirm('Delete backup "' + filename + '"?\n\nThis cannot be undone.')) return;
        backupLog('Deleting ' + filename + '…');
        try {
            var r = await fetch('/superadmin/backup/' + encodeURIComponent(filename), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                backupLog('✓ Deleted: ' + filename);
                saToast('success', 'Backup deleted.');
                setTimeout(() => location.reload(), 1000);
            } else {
                backupLog('✗ ' + d.message);
                saToast('error', d.message);
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    async function restoreBackup(filename) {
        if (!confirm('⚠ RESTORE DATABASE from "' + filename + '"?\n\nThis will OVERWRITE all current data. This cannot be undone.\n\nAre you absolutely sure?')) return;
        backupLog('Restoring from ' + filename + '… this may take a moment.');
        try {
            var r = await fetch('/superadmin/backup/restore/' + encodeURIComponent(filename), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                backupLog('✓ Restore complete!');
                saToast('success', 'Database restored successfully!', filename);
            } else {
                backupLog('✗ Restore failed: ' + d.message);
                saToast('error', 'Restore failed', d.message);
            }
        } catch(e) {
            backupLog('✗ ' + e.message);
            saToast('error', 'Network error during restore.');
        }
    }

    // ══════════════════════════════════════════════════════════
    // ENROLLMENTS PANEL
    // ══════════════════════════════════════════════════════════
    var _enrData = [];

    async function loadEnrollments() {
        _loaded['enrollments'] = true;
        var status = document.getElementById('enr-status-filter')?.value || 'all';
        var tbody = document.getElementById('enr-tbody');
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var r = await fetch('/superadmin/enrollments-data?status=' + status, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            var d = await r.json();
            _enrData = d.enrollments || [];
            // Update stat badges
            var counts = d.counts || {};
            ['submitted','approved','enrolled','declined'].forEach(function(s) {
                var el = document.getElementById('enr-cnt-' + s);
                if (el) el.textContent = counts[s] || 0;
            });
            renderEnrollmentsTable(_enrData);
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--red);">Failed to load enrollments.</td></tr>';
        }
    }

    function renderEnrollmentsTable(rows) {
        var tbody = document.getElementById('enr-tbody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No enrollments found.</td></tr>';
            return;
        }
        var statusBadge = { submitted:'pending', approved:'active', enrolled:'enrolled', declined:'inactive' };
        var statusLabel = { submitted:'Pending', approved:'Approved', enrolled:'Enrolled', declined:'Declined' };
        tbody.innerHTML = rows.map(function(e) {
            var name = (e.last_name + ', ' + e.first_name).trim().replace(/^,\s*/, '') || '—';
            var grade = e.grade_level ? e.grade_level.replace('grade', 'Grade ').replace(/^(\w)/, function(m) { return m.toUpperCase(); }) : '—';
            var statusCls = statusBadge[e.status] || 'active';
            var payBadge = e.payment_status === 'paid'
                ? '<span class="status-badge enrolled">Paid</span>'
                : '<span class="status-badge pending">Unpaid</span>';
            var actions = '';
            if (e.status === 'submitted') {
                actions += '<button class="action-btn view" title="Approve" onclick="saEnrollAction(' + e.id + ',\'approve\')"><i class="bi bi-check-lg"></i></button>';
                actions += '<button class="action-btn delete" title="Decline" onclick="saEnrollAction(' + e.id + ',\'decline\')"><i class="bi bi-x-lg"></i></button>';
            }
            return '<tr>' +
                '<td style="font-size:12px;font-weight:700;color:var(--blue);">' + (e.reference_number || '—') + '</td>' +
                '<td>' + name + '</td>' +
                '<td>' + grade + '</td>' +
                '<td style="font-size:12px;color:var(--muted);">' + (e.created_at || '—') + '</td>' +
                '<td><span class="status-badge ' + statusCls + '">' + (statusLabel[e.status] || e.status) + '</span></td>' +
                '<td>' + payBadge + '</td>' +
                '<td>' + (actions || '<span style="font-size:11px;color:var(--muted);">—</span>') + '</td>' +
                '</tr>';
        }).join('');
    }

    async function saEnrollAction(enrollmentId, action) {
        if (!confirm((action === 'approve' ? 'Approve' : 'Decline') + ' this enrollment?')) return;
        try {
            var r = await fetch('/admin/enrollments/' + enrollmentId + '/' + action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', action === 'approve' ? 'Enrollment approved!' : 'Enrollment declined.');
                _loaded['enrollments'] = false;
                loadEnrollments();
            } else {
                saToast('error', d.message || 'Action failed.');
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ══════════════════════════════════════════════════════════
    // TEACHERS PANEL
    // ══════════════════════════════════════════════════════════
    var _teachersData = [];

    async function loadTeachers() {
        _loaded['teachers'] = true;
        var tbody = document.getElementById('teachers-tbody');
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var r = await fetch('/admin/teachers', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            _teachersData = d.teachers || [];
            renderTeachersTable(_teachersData);
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:30px;color:var(--red);">Failed to load teachers.</td></tr>';
        }
    }

    function renderTeachersTable(rows) {
        var tbody = document.getElementById('teachers-tbody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-person-x" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No teachers found.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(t) {
            var initials = (t.name || 'T').substring(0, 2).toUpperCase();
            var isActive = t.is_active || t.is_active === 1;
            return '<tr>' +
                '<td><div class="user-row-name"><div class="user-row-avatar">' + initials + '</div><div><div style="font-size:13px;font-weight:600;">' + (t.name || '—') + '</div></div></div></td>' +
                '<td style="font-size:12px;color:var(--muted);">' + (t.email || '—') + '</td>' +
                '<td><span class="status-badge ' + (isActive ? 'active' : 'inactive') + '">' + (isActive ? 'Active' : 'Inactive') + '</span></td>' +
                '<td>' +
                '<button class="action-btn lock" title="Reset Password" onclick="saResetTeacherPwd(' + t.id + ',\'' + (t.name || '').replace(/'/g, "\\'") + '\')"><i class="bi bi-key-fill"></i></button>' +
                '<button class="action-btn ' + (isActive ? 'delete' : 'view') + '" title="' + (isActive ? 'Deactivate' : 'Activate') + '" onclick="saToggleTeacher(' + t.id + ',' + (isActive ? 'true' : 'false') + ')"><i class="bi bi-' + (isActive ? 'person-dash-fill' : 'person-check-fill') + '"></i></button>' +
                '</td>' +
                '</tr>';
        }).join('');
    }

    function filterTeachersTable(q) {
        q = (q || '').toLowerCase();
        document.querySelectorAll('#teachers-tbody tr').forEach(function(row) {
            row.style.display = !q || row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    async function saToggleTeacher(userId, isActive) {
        if (!confirm((isActive ? 'Deactivate' : 'Activate') + ' this teacher account?')) return;
        try {
            var r = await fetch('/superadmin/users/' + userId + '/toggle-status', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) { saToast('success', isActive ? 'Teacher deactivated.' : 'Teacher activated.'); _loaded['teachers'] = false; loadTeachers(); }
            else saToast('error', d.message || 'Error.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    async function saResetTeacherPwd(userId, userName) {
        var pwd = prompt('Enter new password for ' + userName + ' (min. 8 characters):');
        if (!pwd) return;
        if (pwd.length < 8) { saToast('warning', 'Password must be at least 8 characters.'); return; }
        try {
            var fd = new FormData();
            fd.append('password', pwd); fd.append('password_confirmation', pwd);
            var r = await fetch('/superadmin/users/' + userId + '/reset-password', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: fd
            });
            var d = await r.json();
            if (d.success) saToast('success', 'Password reset for ' + userName + '.');
            else saToast('error', d.message || 'Error resetting password.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // Add Teacher Modal trigger (reuses existing createUserModal but pre-selects teacher role)
    function openAddTeacherModal() {
        if (document.getElementById('createUserModal')) {
            document.getElementById('createUserForm')?.reset();
            var roleEl = document.getElementById('user-role');
            if (roleEl) roleEl.value = 'teacher';
            createUserModal.show();
        }
    }

    // ══════════════════════════════════════════════════════════
    // SUBJECTS PANEL
    // ══════════════════════════════════════════════════════════
    var _subjectsData = [];
    var _editSubjectId = null;

    async function loadSubjects() {
        _loaded['subjects'] = true;
        var grade = document.getElementById('subj-grade-filter')?.value || '';
        var tbody = document.getElementById('subjects-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var url = '/admin/subjects' + (grade ? '?grade_level=' + grade : '');
            var r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            _subjectsData = d.subjects || [];
            renderSubjectsTable(_subjectsData);
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red);">Failed to load subjects.</td></tr>';
        }
    }

    function renderSubjectsTable(rows) {
        var tbody = document.getElementById('subjects-tbody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-book" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No subjects found.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(s) {
            var gl = (s.grade_level || '').replace('grade', 'Grade ').replace(/^(\w)/, function(m) { return m.toUpperCase(); });
            var isActive = s.is_active || s.is_active === 1;
            return '<tr>' +
                '<td style="font-weight:600;">' + (s.name || '—') + '</td>' +
                '<td style="font-size:12px;"><code style="background:#f0f4f8;padding:2px 7px;border-radius:4px;">' + (s.code || '—') + '</code></td>' +
                '<td>' + gl + '</td>' +
                '<td><span class="status-badge ' + (isActive ? 'active' : 'inactive') + '">' + (isActive ? 'Active' : 'Inactive') + '</span></td>' +
                '<td>' +
                '<button class="action-btn edit" title="Edit" onclick="openEditSubjectModal(' + s.id + ')"><i class="bi bi-pencil-fill"></i></button>' +
                '<button class="action-btn delete" title="Delete" onclick="saDeleteSubject(' + s.id + ',\'' + (s.name || '').replace(/'/g, "\\'") + '\')"><i class="bi bi-trash-fill"></i></button>' +
                '</td></tr>';
        }).join('');
    }

    // Subject Add Modal (inline modal)
    function openAddSubjectModal() {
        var modalHtml = '<div class="modal fade" id="subjectModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered" style="max-width:480px;"><div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,#1a3a6c,#2563eb);padding:20px 24px;"><div style="display:flex;align-items:center;gap:12px;"><div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;"><i class="bi bi-book-fill" style="color:#fff;font-size:18px;"></i></div><div style="font-size:16px;font-weight:800;color:#fff;" id="subjectModalTitle">Add Subject</div></div><button type="button" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button></div>' +
            '<div style="padding:20px 24px;background:#fff;">' +
            '<div class="mb-3"><label class="form-lbl">Subject Name *</label><input type="text" id="subj-name" class="form-fld" placeholder="e.g. Mathematics"></div>' +
            '<div class="mb-3"><label class="form-lbl">Subject Code *</label><input type="text" id="subj-code" class="form-fld" placeholder="e.g. MATH-G1"></div>' +
            '<div class="mb-3"><label class="form-lbl">Grade Level *</label><select id="subj-grade" class="form-fld"><option value="">Select…</option><option value="nursery">Nursery</option><option value="kindergarten">Kindergarten</option><option value="grade1">Grade 1</option><option value="grade2">Grade 2</option><option value="grade3">Grade 3</option><option value="grade4">Grade 4</option><option value="grade5">Grade 5</option><option value="grade6">Grade 6</option></select></div>' +
            '<div><label class="form-check" style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" id="subj-active" class="form-check-input" checked><span class="form-check-label" style="font-size:13px;">Active</span></label></div>' +
            '</div><div style="padding:12px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">' +
            '<button type="button" data-bs-dismiss="modal" style="padding:9px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>' +
            '<button type="button" id="btnSaveSubject" onclick="saveSubject()" style="padding:9px 20px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;"><i class="bi bi-floppy-fill me-1"></i>Save Subject</button>' +
            '</div></div></div></div>';
        var existing = document.getElementById('subjectModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        _editSubjectId = null;
        document.getElementById('subjectModalTitle').textContent = 'Add Subject';
        document.getElementById('subj-name').value = '';
        document.getElementById('subj-code').value = '';
        document.getElementById('subj-grade').value = '';
        document.getElementById('subj-active').checked = true;
        new bootstrap.Modal(document.getElementById('subjectModal')).show();
    }

    function openEditSubjectModal(subjectId) {
        var s = _subjectsData.find(function(x) { return x.id == subjectId; });
        if (!s) return;
        openAddSubjectModal();
        _editSubjectId = subjectId;
        document.getElementById('subjectModalTitle').textContent = 'Edit Subject';
        document.getElementById('subj-name').value  = s.name  || '';
        document.getElementById('subj-code').value  = s.code  || '';
        document.getElementById('subj-grade').value = s.grade_level || '';
        document.getElementById('subj-active').checked = !!(s.is_active || s.is_active === 1);
    }

    async function saveSubject() {
        var name    = document.getElementById('subj-name').value.trim();
        var code    = document.getElementById('subj-code').value.trim();
        var grade   = document.getElementById('subj-grade').value;
        var active  = document.getElementById('subj-active').checked;
        if (!name || !code || !grade) { saToast('warning', 'All fields are required.'); return; }

        var method = _editSubjectId ? 'PUT' : 'POST';
        var url    = '/admin/subjects' + (_editSubjectId ? '/' + _editSubjectId : '');
        try {
            var r = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name: name, code: code, grade_level: grade, is_active: active })
            });
            var d = await r.json();
            if (r.ok) {
                saToast('success', _editSubjectId ? 'Subject updated!' : 'Subject added!');
                bootstrap.Modal.getInstance(document.getElementById('subjectModal'))?.hide();
                _loaded['subjects'] = false; loadSubjects();
            } else {
                var msg = d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'Error saving subject.');
                saToast('error', msg);
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    async function saDeleteSubject(subjectId, subjectName) {
        if (!confirm('Delete subject "' + subjectName + '"? This cannot be undone.')) return;
        try {
            var r = await fetch('/admin/subjects/' + subjectId, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) { saToast('success', 'Subject deleted.'); _loaded['subjects'] = false; loadSubjects(); }
            else saToast('error', d.message || 'Error deleting subject.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ══════════════════════════════════════════════════════════
    // SECTIONS PANEL
    // ══════════════════════════════════════════════════════════
    async function loadSectionsMgmt() {
        _loaded['sections-mgmt'] = true;
        var grade = document.getElementById('sec-grade-filter')?.value || '';
        var tbody = document.getElementById('sections-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var url = '/admin/sections' + (grade ? '?grade_level=' + grade : '');
            var r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            var rows = d.sections || [];
            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-diagram-3" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No sections found.</td></tr>';
                return;
            }
            tbody.innerHTML = rows.map(function(s) {
                var gl = (s.grade_level || '').replace('grade', 'Grade ').replace(/^(\w)/, function(m) { return m.toUpperCase(); });
                var teacherName = s.teacher ? s.teacher.name : '<span style="color:var(--muted);font-size:12px;">Unassigned</span>';
                var studentCount = Array.isArray(s.students) ? s.students.length : (s.students_count || 0);
                return '<tr>' +
                    '<td style="font-weight:700;">' + (s.name || '—') + '</td>' +
                    '<td><span class="status-badge active">' + gl + '</span></td>' +
                    '<td>' + teacherName + '</td>' +
                    '<td><span style="font-size:13px;font-weight:700;color:var(--blue);">' + studentCount + '</span><span style="font-size:11px;color:var(--muted);"> students</span></td>' +
                    '<td><button class="action-btn view" title="View section in Admin" onclick="window.open(\'/admin/dashboard?section=sections\',\'_blank\')"><i class="bi bi-arrow-up-right-square"></i></button></td>' +
                    '</tr>';
            }).join('');
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red);">Failed to load sections.</td></tr>';
        }
    }

    // ══════════════════════════════════════════════════════════
    // SCHEDULES PANEL
    // ══════════════════════════════════════════════════════════
    var _schedulesData = [];

    async function loadSchedules() {
        _loaded['schedules'] = true;
        // Populate section dropdown if empty
        var secSel = document.getElementById('sched-section-filter');
        if (secSel && secSel.options.length <= 1) {
            try {
                var sr = await fetch('/admin/sections', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                var sd = await sr.json();
                (sd.sections || []).forEach(function(sec) {
                    var opt = document.createElement('option');
                    opt.value = sec.id; opt.textContent = sec.name + ' (' + (sec.grade_level || '') + ')';
                    secSel.appendChild(opt);
                });
            } catch(e) {}
        }

        var sectionId = document.getElementById('sched-section-filter')?.value || '';
        var tbody = document.getElementById('schedules-tbody');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var url = '/admin/schedules' + (sectionId ? '?section_id=' + sectionId : '');
            var r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            _schedulesData = d.schedules || [];
            filterSchedulesTable();
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--red);">Failed to load schedules.</td></tr>';
        }
    }

    function filterSchedulesTable() {
        var dayFilter = (document.getElementById('sched-day-filter')?.value || '').toLowerCase();
        var rows = _schedulesData.filter(function(s) {
            return !dayFilter || (s.day_of_week || '').toLowerCase() === dayFilter;
        });
        var tbody = document.getElementById('schedules-tbody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No schedules found.</td></tr>';
            return;
        }
        var dayColors = { Monday:'#e8f0fb', Tuesday:'#e8f8f0', Wednesday:'#fff8ec', Thursday:'#f3e8fb', Friday:'#fdecea' };
        tbody.innerHTML = rows.map(function(s) {
            var sectionName = s.section ? s.section.name : '—';
            var subjectName = s.subject ? s.subject.name : '—';
            var teacherName = s.teacher ? s.teacher.name : '<span style="color:var(--muted);">—</span>';
            var day = s.day_of_week || '—';
            var dayBg = dayColors[day] || '#f0f4f8';
            var timeStr = (s.start_time ? s.start_time.substring(0,5) : '—') + ' – ' + (s.end_time ? s.end_time.substring(0,5) : '—');
            return '<tr>' +
                '<td style="font-weight:600;">' + sectionName + '</td>' +
                '<td>' + subjectName + '</td>' +
                '<td>' + teacherName + '</td>' +
                '<td><span style="background:' + dayBg + ';padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">' + day + '</span></td>' +
                '<td style="font-size:12px;font-weight:600;white-space:nowrap;">' + timeStr + '</td>' +
                '<td style="font-size:12px;color:var(--muted);">' + (s.room || '—') + '</td>' +
                '</tr>';
        }).join('');
    }

    // ══════════════════════════════════════════════════════════
    // SYSTEM SETTINGS PANEL
    // ══════════════════════════════════════════════════════════
    var _sysEnrollmentOpen = null;
    var _sysMaintenanceOn  = null;

    async function loadSysSettings() {
        _loaded['sys-settings'] = true;
        try {
            var r = await fetch('/admin/settings', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            var settings = d.settings || {};
            // Flatten grouped settings into a key→value map
            var flat = {};
            Object.values(settings).forEach(function(group) {
                (Array.isArray(group) ? group : Object.values(group)).forEach(function(item) {
                    if (item && item.key) flat[item.key] = item.value;
                });
            });

            // Enrollment toggle
            _sysEnrollmentOpen = flat['enrollment_open'] === '1' || flat['enrollment_open'] === true || flat['enrollment_open'] === 1;
            updateEnrollmentToggleUI(_sysEnrollmentOpen);

            // Maintenance toggle
            _sysMaintenanceOn = flat['maintenance_mode'] === '1' || flat['maintenance_mode'] === true || flat['maintenance_mode'] === 1;
            updateMaintenanceToggleUI(_sysMaintenanceOn);

            // Render overview table
            var overviewEl = document.getElementById('sys-settings-overview');
            var rows = Object.values(settings);
            var allItems = [];
            rows.forEach(function(group) {
                (Array.isArray(group) ? group : Object.values(group)).forEach(function(item) {
                    if (item && item.key) allItems.push(item);
                });
            });
            if (!allItems.length) {
                overviewEl.innerHTML = '<div style="text-align:center;padding:20px;color:var(--muted);">No settings found.</div>';
                return;
            }
            overviewEl.innerHTML = '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr><th>Key</th><th>Label</th><th>Value</th><th>Group</th></tr></thead><tbody>' +
                allItems.map(function(item) {
                    var val = item.value !== null && item.value !== undefined ? String(item.value) : '—';
                    if (val === '1') val = '<span class="status-badge enrolled">ON</span>';
                    else if (val === '0') val = '<span class="status-badge inactive">OFF</span>';
                    return '<tr><td style="font-size:12px;font-family:monospace;color:var(--blue);">' + item.key + '</td>' +
                        '<td style="font-size:13px;">' + (item.label || item.key) + '</td>' +
                        '<td>' + val + '</td>' +
                        '<td style="font-size:11px;color:var(--muted);">' + (item.group || '—') + '</td></tr>';
                }).join('') +
                '</tbody></table></div>';

        } catch(e) {
            document.getElementById('enroll-toggle-desc').textContent = 'Failed to load settings.';
            document.getElementById('maint-toggle-desc').textContent  = 'Failed to load settings.';
        }
    }

    function updateEnrollmentToggleUI(isOpen) {
        var label = document.getElementById('enroll-toggle-label');
        var btn   = document.getElementById('btn-toggle-enrollment');
        var desc  = document.getElementById('enroll-toggle-desc');
        if (!label || !btn) return;
        label.textContent = isOpen ? 'OPEN' : 'CLOSED';
        label.style.color = isOpen ? 'var(--green)' : 'var(--red)';
        btn.textContent = isOpen ? 'Close Enrollment' : 'Open Enrollment';
        btn.style.background = isOpen ? 'linear-gradient(135deg,#b91c1c,#dc2626)' : 'linear-gradient(135deg,#14532d,#16a34a)';
        btn.style.color = '#fff';
        if (desc) desc.textContent = isOpen ? 'Students can submit online applications.' : 'Online applications are currently disabled.';
    }

    function updateMaintenanceToggleUI(isOn) {
        var label = document.getElementById('maint-toggle-label');
        var btn   = document.getElementById('btn-toggle-maintenance');
        var desc  = document.getElementById('maint-toggle-desc');
        if (!label || !btn) return;
        label.textContent = isOn ? 'ON' : 'OFF';
        label.style.color = isOn ? 'var(--red)' : 'var(--green)';
        btn.textContent = isOn ? 'Disable Maintenance' : 'Enable Maintenance';
        btn.style.background = isOn ? 'linear-gradient(135deg,#14532d,#16a34a)' : 'linear-gradient(135deg,#92400e,#d97706)';
        btn.style.color = '#fff';
        if (desc) desc.textContent = isOn ? 'Website is in maintenance mode.' : 'Website is publicly accessible.';
    }

    async function toggleEnrollment() {
        var action = _sysEnrollmentOpen ? 'close enrollment' : 'open enrollment';
        if (!confirm('Are you sure you want to ' + action + '?')) return;
        try {
            var r = await fetch('/admin/settings/toggle-enrollment', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success !== false) {
                _sysEnrollmentOpen = !_sysEnrollmentOpen;
                updateEnrollmentToggleUI(_sysEnrollmentOpen);
                saToast('success', _sysEnrollmentOpen ? 'Enrollment is now OPEN.' : 'Enrollment is now CLOSED.');
            } else { saToast('error', d.message || 'Toggle failed.'); }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    async function toggleMaintenance() {
        var action = _sysMaintenanceOn ? 'disable maintenance mode' : 'enable maintenance mode';
        if (!confirm('Are you sure you want to ' + action + '?')) return;
        try {
            var r = await fetch('/admin/settings/toggle-maintenance', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success !== false) {
                _sysMaintenanceOn = !_sysMaintenanceOn;
                updateMaintenanceToggleUI(_sysMaintenanceOn);
                saToast('success', _sysMaintenanceOn ? 'Maintenance mode ENABLED.' : 'Maintenance mode DISABLED.');
            } else { saToast('error', d.message || 'Toggle failed.'); }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ══ Chart.js ══
    const _SAC = {blue:'#1a3a6c',mid:'#2471a3',gold:'#c5a059',green:'#16a34a',red:'#dc2626',gray:'#94a3b8',purple:'#7c3aed'};
    Chart.defaults.font.family = "'Open Sans',sans-serif";
    Chart.defaults.font.size   = 11;

    // Dashboard: enrollment trend
    (function(){
        const el = document.getElementById('saEnrollTrend');
        if (!el) return;
        new Chart(el,{type:'line',
            data:{labels:<?php echo json_encode($saChMonths??[], 15, 512) ?>,
                datasets:[{label:'Enrollments',data:<?php echo json_encode($saChEnroll??[], 15, 512) ?>,
                    borderColor:_SAC.blue,backgroundColor:'rgba(26,58,108,.08)',
                    borderWidth:2,pointRadius:4,tension:0.4,fill:true}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{stepSize:1}},x:{grid:{display:false}}}}
        });
    })();

    // Dashboard: users by role doughnut
    (function(){
        const el = document.getElementById('saRoleDoughnut');
        if (!el) return;
        new Chart(el,{type:'doughnut',
            data:{labels:['Students','Teachers','Admins','Finance','Cashier'],
                datasets:[{data:[
                    <?php echo e($stats['total_students']??0); ?>,
                    <?php echo e($stats['total_teachers']??0); ?>,
                    <?php echo e(($stats['admins']??0)+($stats['superadmins']??0)); ?>,
                    <?php echo e(\App\Models\User::where('role','finance')->count()); ?>,
                    <?php echo e(\App\Models\User::where('role','cashier')->count()); ?>

                ],backgroundColor:[_SAC.blue,_SAC.gold,_SAC.mid,_SAC.green,_SAC.purple],borderWidth:0,hoverOffset:4}]},
            options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
                plugins:{legend:{position:'bottom',labels:{padding:10,font:{size:11}}}}}
        });
    })();

    function initSaReportsCharts() {
        if (window._saRptInit) return;
        window._saRptInit = true;

        const lineEl = document.getElementById('saRptEnrollLine');
        if (lineEl) new Chart(lineEl,{type:'line',
            data:{labels:<?php echo json_encode($saRptMonths??[], 15, 512) ?>,
                datasets:[{label:'Enrollments',data:<?php echo json_encode($saRptEnroll??[], 15, 512) ?>,
                    borderColor:_SAC.blue,backgroundColor:'rgba(26,58,108,.08)',
                    borderWidth:2,pointRadius:4,tension:0.4,fill:true}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{stepSize:1}},x:{grid:{display:false}}}}
        });

        const barEl = document.getElementById('saRptUserBar');
        if (barEl) new Chart(barEl,{type:'bar',
            data:{labels:['Students','Teachers','Admins','Finance','Cashier'],
                datasets:[{label:'Users',
                    data:[<?php echo e($stats['total_students']??0); ?>,<?php echo e($stats['total_teachers']??0); ?>,<?php echo e(($stats['admins']??0)+($stats['superadmins']??0)); ?>,<?php echo e(\App\Models\User::where('role','finance')->count()); ?>,<?php echo e(\App\Models\User::where('role','cashier')->count()); ?>],
                    backgroundColor:[_SAC.blue,_SAC.gold,_SAC.mid,_SAC.green,_SAC.purple],
                    borderRadius:5,borderSkipped:false}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'}},x:{grid:{display:false}}}}
        });
    }
</script>
</body>
</html><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\superadmin_dashboard.blade.php ENDPATH**/ ?>