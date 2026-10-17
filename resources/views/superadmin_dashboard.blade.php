<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: linear-gradient(135deg, #162f5c, #1a3a6c);
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
            background: linear-gradient(180deg, #162f5c 0%, #1a3a6c 60%, #1c3f78 100%);
            box-shadow: 3px 0 18px rgba(0,0,0,0.18);
            display: flex; flex-direction: column;
            padding: 14px 0; z-index: 99; overflow-y: auto;
        }
        .sidebar-section-lbl {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.6px;
            color: rgba(255,255,255,0.3); padding: 14px 20px 5px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 20px 10px 17px; color: rgba(255,255,255,0.7);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all 0.2s; border-left: 3px solid transparent;
            cursor: pointer; background: none; border-right: none;
            border-top: none; border-bottom: none;
            width: 100%; text-align: left;
            font-family: 'Open Sans', sans-serif;
        }
        /* Icon badge — each nav icon sits in its own rounded square,
           matching the Cashier portal's sidebar (the shared reference
           look across every portal now). */
        .sidebar-link i {
            font-size: 15px; width: 30px; height: 30px; flex-shrink: 0;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.06);
            transition: background 0.18s;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-link:hover i { background: rgba(255,255,255,0.12); }
        .sidebar-link.active { background: rgba(197,160,89,0.12); color: #fff; border-left-color: var(--gold); }
        .sidebar-link.active i { background: rgba(197,160,89,0.25); color: var(--gold); }
        .sidebar-badge {
            margin-left: auto; background: var(--gold); color: #fff;
            font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 10px;
            min-width: 20px; text-align: center;
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
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* Change Section button inside the ported enrollment-detail modal
           has no backing JS function in Admin either — hidden here rather
           than shipping a dead button into a new context. */
        #btn-change-section { display: none !important; }

        /* ── Walk-in Enrollment form controls (ported from public/css/adminDashboard.css,
             which the shared admin.enrollments.walk-in-modal partial depends on but
             is never linked on this page) ── */
        .dash-form-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #4a5568 !important;
            margin-bottom: 6px !important;
            display: block !important;
            letter-spacing: 0.3px;
        }
        .dash-form-control,
        input.dash-form-control,
        select.dash-form-control,
        textarea.dash-form-control {
            width: 100% !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
            font-family: 'Open Sans', sans-serif !important;
            background-color: #f8fafc !important;
            color: #2d3748 !important;
            transition: all 0.25s ease !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none !important;
        }
        .dash-form-control:hover,
        input.dash-form-control:hover,
        select.dash-form-control:hover {
            border-color: #cbd5e0 !important;
            background-color: #fff !important;
        }
        .dash-form-control:focus,
        input.dash-form-control:focus,
        select.dash-form-control:focus,
        textarea.dash-form-control:focus {
            border-color: #1a3a6c !important;
            box-shadow: 0 0 0 4px rgba(26,58,108,0.1) !important;
            background-color: #fff !important;
            outline: none !important;
        }
        select.dash-form-control {
            cursor: pointer !important;
            padding-right: 32px !important;
        }
        textarea.dash-form-control {
            resize: vertical !important;
            min-height: 70px !important;
        }
        .modal-content.modal-content-styled,
        .modal-content-styled {
            border: 0 !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 25px 65px rgba(0,0,0,0.18) !important;
        }
        .modal-header.modal-header-styled,
        .modal-header-styled {
            background: linear-gradient(135deg, #1a3a6c 0%, #2a6dd6 100%) !important;
            color: #fff !important;
            border: 0 !important;
            border-bottom: none !important;
            padding: 22px 28px !important;
            position: relative;
        }
        .modal-header-styled::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, rgba(245,166,35,0.6), transparent);
        }
        .modal-header-styled .modal-title {
            font-weight: 700 !important; font-size: 17px !important;
            display: flex !important; align-items: center !important; gap: 10px !important;
            color: #fff !important;
        }
        .modal-header-styled .modal-title i { font-size: 20px; opacity: 0.9; }
        .modal-header-styled .btn-close {
            filter: brightness(0) invert(1) !important; opacity: 0.8;
        }
        .modal-header-styled .btn-close:hover { opacity: 1; }
        .modal-body.modal-body-styled,
        .modal-body-styled {
            padding: 28px !important;
            background: #fff !important;
        }
        .modal-footer.modal-footer-styled,
        .modal-footer-styled {
            border-top: 1px solid #e8ecf1 !important;
            padding: 16px 28px !important;
            background: #f8fafc !important;
        }
        .modal-footer-styled .btn-dash {
            border-radius: 10px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
        }
        .text-muted-alt { color: #999; }

        /* ── Schedule grid (ported from adminDashboard.blade.php's own
             schedule CSS, same classes/markup so the grid looks and
             behaves identically) ── */
        .sched-filter-card { background: var(--white); border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px 20px; margin-bottom: 20px; }
        .sched-filter-title { font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 12px; }
        .sched-toolbar { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
        .sched-field-group { display: flex; flex-direction: column; gap: 6px; }
        .sched-field-group label { font-size: 11.5px; font-weight: 600; color: var(--muted); }
        .sched-toolbar .form-fld { min-width: 168px; width: auto; }
        .sched-toolbar .btn-dash { flex-shrink: 0; }
        .sched-empty-card { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 56px 24px; text-align: center; }
        .sched-empty-icon { width: 72px; height: 72px; border-radius: 50%; background: var(--blue-pale); display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; }
        .sched-empty-icon i { font-size: 32px; color: var(--blue); }
        .sched-quickpick { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; max-width: 560px; margin: 22px auto 0; }
        .sched-quickpick-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 20px; border: 1.5px solid var(--border); background: var(--white); color: var(--text); font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
        .sched-quickpick-btn:hover { border-color: var(--blue); background: var(--blue-pale); color: var(--blue); transform: translateY(-1px); }
        .sched-quickpick-btn i { font-size: 11px; opacity: 0.7; }
        .schedule-grid-table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0; font-size: 13px; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 2px rgba(15,23,42,0.04), 0 8px 24px rgba(15,23,42,0.06); }
        .schedule-grid-table th { padding: 14px 12px; text-align: center; font-weight: 700; border: none; color: #fff; font-size: 13px; letter-spacing: 0.3px; }
        .schedule-grid-table th:first-child { background: #334155; }
        .schedule-grid-table th.day-mon, .schedule-grid-table th.day-tue, .schedule-grid-table th.day-wed, .schedule-grid-table th.day-thu, .schedule-grid-table th.day-fri { background: linear-gradient(135deg, #1a3a6c 0%, #2471a3 100%); }
        .schedule-grid-table td { padding: 8px; border: none; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: top; min-height: 76px; height: 76px; background: #fff; transition: background-color 0.15s ease; }
        .schedule-grid-table td:last-child { border-right: none; }
        .schedule-grid-table tr:last-child td { border-bottom: none; }
        .schedule-grid-table .time-cell { background: #f8fafc; font-weight: 700; text-align: center; vertical-align: middle; font-size: 11.5px; color: #475569; border-right: 2px solid #e2e8f0; }
        .schedule-grid-table .break-cell { background: repeating-linear-gradient(135deg, #fbfaf7, #fbfaf7 10px, #f5f2ea 10px, #f5f2ea 20px); text-align: center; vertical-align: middle; font-weight: 700; color: #92722a; letter-spacing: 1.5px; font-size: 11px; border-bottom: 2px solid #ecd9a8; }
        .schedule-cell { cursor: pointer; position: relative; }
        .schedule-cell:hover { background: #f8fafc; }
        .sched-conflict-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 9.5px; font-weight: 800; color: #fff; background: #dc2626; padding: 2px 7px; border-radius: 20px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px; }
        .schedule-cell-content { padding: 8px 9px; border-radius: 8px; height: 100%; box-sizing: border-box; overflow: hidden; border-left: 3px solid var(--blue); background: var(--blue-pale); transition: transform 0.12s ease, box-shadow 0.12s ease; }
        .schedule-cell-content:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,23,42,0.1); }
        .schedule-cell-content.has-conflict { background: #fef2f2; border-left: 3px solid #dc2626; }
        .schedule-cell-content .subj-row { display: flex; align-items: center; gap: 5px; margin-bottom: 5px; }
        .schedule-cell-content .subj-code-pill { flex-shrink: 0; font-size: 9px; font-weight: 800; color: #fff; background: var(--blue); padding: 1px 6px; border-radius: 5px; letter-spacing: 0.2px; }
        .schedule-cell-content.has-conflict .subj-code-pill { background: #dc2626; }
        .schedule-cell-content .subject { font-weight: 700; color: #1e293b; font-size: 11.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .schedule-cell-content .teacher { font-size: 10.5px; color: #475569; margin-top: 3px; display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .schedule-cell-content .teacher .teacher-icon { font-size: 10px; flex-shrink: 0; color: var(--blue); }
        .schedule-cell-content .room { font-size: 10px; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .schedule-cell-content .room i { font-size: 9px; flex-shrink: 0; }
        .schedule-cell-empty { height: 100%; min-height: 60px; border: 1.5px dashed #dde3ec; border-radius: 8px; color: #b7c0cc; font-size: 11.5px; display: flex; align-items: center; justify-content: center; font-weight: 600; letter-spacing: 0.3px; transition: all 0.15s ease; }
        .schedule-cell:hover .schedule-cell-empty { border-color: var(--blue); color: var(--blue); background: var(--blue-pale); }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- TOPBAR -->
<div class="dash-topbar">
    <div class="topbar-brand">
        <div class="brand-logos">
            {{--  
            <div class="brand-logo-circle">
                <img src="/images/logo1.png" alt=""
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-building\'></i>'">
            </div>--}}
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
                    @if(Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="user-chip-name">{{ Auth::user()->name }}</div>
                    <div class="user-chip-role">Super Admin</div>
                </div>
                <i class="bi bi-chevron-down user-chip-caret"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end user-chip-dropdown">
                <div class="ucd-header">
                    <div class="ucd-avatar">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="ucd-info">
                        <div class="ucd-name">{{ Auth::user()->name }}</div>
                        <div class="ucd-email">{{ Auth::user()->email }}</div>
                        <span class="ucd-badge">Super Admin</span>
                    </div>
                </div>
                <div class="ucd-body">
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-person-circle"></i> My Profile</a>
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-gear"></i> Settings</a>
                </div>
                <div class="ucd-divider"></div>
                <div class="ucd-footer">
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout(this)">
                        @csrf
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
    <button class="sidebar-link {{ $section === 'dashboard' ? 'active' : '' }}" id="nav-dashboard" onclick="showSection('dashboard')">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </button>

    <div class="sidebar-section-lbl">System Management</div>
    <button class="sidebar-link {{ $section === 'users' ? 'active' : '' }}" id="nav-users" onclick="showSection('users')">
        <i class="bi bi-people-fill"></i> User Management
        <span class="sidebar-badge">{{ $stats['total_users'] }}</span>
    </button>
    <button class="sidebar-link {{ $section === 'roles' ? 'active' : '' }}" id="nav-roles" onclick="showSection('roles')">
        <i class="bi bi-shield-lock-fill"></i> Roles &amp; Permissions
    </button>
    <button class="sidebar-link {{ $section === 'logs' ? 'active' : '' }}" id="nav-logs" onclick="showSection('logs')">
        <i class="bi bi-journal-text"></i> System Logs
    </button>
    <button class="sidebar-link {{ $section === 'backup' ? 'active' : '' }}" id="nav-backup" onclick="showSection('backup')">
        <i class="bi bi-database-fill"></i> Backup &amp; Restore
    </button>

    <div class="sidebar-section-lbl">Academic Management</div>
    <button class="sidebar-link {{ $section === 'enrollments' ? 'active' : '' }}" id="nav-enrollments" onclick="showSection('enrollments')">
        <i class="bi bi-clipboard-check-fill"></i> Enrollments
    </button>
    <button class="sidebar-link {{ $section === 'teachers' ? 'active' : '' }}" id="nav-teachers" onclick="showSection('teachers')">
        <i class="bi bi-person-video3"></i> Teachers
    </button>
    <button class="sidebar-link {{ $section === 'subjects' ? 'active' : '' }}" id="nav-subjects" onclick="showSection('subjects')">
        <i class="bi bi-book-fill"></i> Subjects
    </button>
    <button class="sidebar-link {{ $section === 'sections-mgmt' ? 'active' : '' }}" id="nav-sections-mgmt" onclick="showSection('sections-mgmt')">
        <i class="bi bi-diagram-3-fill"></i> Sections
    </button>
    <button class="sidebar-link {{ $section === 'schedules' ? 'active' : '' }}" id="nav-schedules" onclick="showSection('schedules')">
        <i class="bi bi-calendar3-week-fill"></i> Schedules
    </button>

    <div class="sidebar-section-lbl">Content</div>
    <button class="sidebar-link {{ $section === 'announcements' ? 'active' : '' }}" id="nav-announcements" onclick="showSection('announcements')">
        <i class="bi bi-megaphone-fill"></i> Announcements
        <span class="sidebar-badge">{{ $announcements->total() }}</span>
    </button>
    <button class="sidebar-link {{ $section === 'news' ? 'active' : '' }}" id="nav-news" onclick="showSection('news')">
        <i class="bi bi-newspaper"></i> News
        <span class="sidebar-badge">{{ $newsArticles->total() }}</span>
    </button>

    <div class="sidebar-section-lbl">System</div>
    <button class="sidebar-link {{ $section === 'sys-settings' ? 'active' : '' }}" id="nav-sys-settings" onclick="showSection('sys-settings')">
        <i class="bi bi-sliders"></i> System Settings
    </button>

    <div class="sidebar-section-lbl">Overview</div>
    <button class="sidebar-link {{ $section === 'reports' ? 'active' : '' }}" id="nav-reports" onclick="showSection('reports')">
        <i class="bi bi-bar-chart-fill"></i> Reports
    </button>

    <div class="sidebar-bottom">
        <button class="sidebar-link {{ $section === 'settings' ? 'active' : '' }}" id="nav-settings" onclick="showSection('settings')">
            <i class="bi bi-gear-fill"></i> Settings
        </button>
        {{-- CHANGE: action="{{ route('logout') }}" --}}
        <form method="POST" action="/logout" onsubmit="return confirmLogout(this)">
            @csrf
            <button type="submit" class="sidebar-link" style="color:rgba(248,113,113,0.8);">
                <i class="bi bi-box-arrow-left" style="color:rgba(248,113,113,0.9);"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="dash-main">

    @php
        $__bcLabels = [
            'dashboard' => 'Dashboard', 'users' => 'User Management', 'roles' => 'Roles & Permissions',
            'logs' => 'Activity Logs', 'backup' => 'Backup & Restore', 'reports' => 'Reports', 'settings' => 'Settings',
            'enrollments' => 'Enrollment Oversight', 'teachers' => 'Teacher Management', 'subjects' => 'Subject Management',
            'sections-mgmt' => 'Section Management', 'schedules' => 'Schedule Management', 'sys-settings' => 'System Settings',
            'announcements' => 'Announcements', 'news' => 'News Management',
        ];
    @endphp
    <style>
        .ilc-breadcrumb{display:flex;align-items:center;gap:8px;padding:18px 2px 16px 28px;font-size:13px;color:#64748b;flex-wrap:wrap;}
        .ilc-breadcrumb a{color:#1a3a6c;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
        .ilc-breadcrumb a:hover{text-decoration:underline;}
        .ilc-bc-sep{font-size:10px;color:#b6c0cc;}
        .ilc-bc-current{color:#334155;font-weight:700;}
    </style>
    <nav class="ilc-breadcrumb" aria-label="breadcrumb">
        <a href="#" onclick="showSection('dashboard');return false;"><i class="bi bi-house-door-fill"></i> Home</a>
        <i class="bi bi-chevron-right ilc-bc-sep"></i>
        <span id="bc-current" class="ilc-bc-current">{{ $__bcLabels[$section] ?? 'Dashboard' }}</span>
        <span id="bc-current-skel" class="skel" style="display:none;width:110px;height:13px;border-radius:4px;"></span>
    </nav>

    <!-- ═══════════════════════════
         SECTION: DASHBOARD
    ═══════════════════════════ -->
    <div id="section-dashboard" class="dash-section" {{ $section !== 'dashboard' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1>Super Admin Dashboard</h1>
                {{-- CHANGE: {{ now()->format('l, F d, Y') }} --}}
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
                        <div class="stat-value">{{ $stats['total_users'] }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['admins'] + $stats['superadmins'] }}</div>
                        <div class="stat-label">Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-easel-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['total_teachers'] }}</div>
                        <div class="stat-label">Teachers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-mortarboard-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['total_students'] }}</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['inactive_users'] }}</div>
                        <div class="stat-label">Inactive</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-database-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['active_users'] }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Charts -->
        @php
            $saChMonths=[]; $saChEnroll=[];
            for($i=5;$i>=0;$i--){
                $m=now()->subMonths($i);
                $saChMonths[]=$m->format('M Y');
                $saChEnroll[]=\App\Models\Enrollment::whereYear('created_at',$m->year)->whereMonth('created_at',$m->month)->count();
            }
        @endphp
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
                                @foreach($dashboardUsers as $user)
                                @php
                                    $db_badge = match($user->role) {
                                        'superadmin' => ['cls'=>'superadmin','label'=>'Super Admin'],
                                        'admin'      => ['cls'=>'admin',     'label'=>'Admin'],
                                        'finance'    => ['cls'=>'teacher',   'label'=>'Finance'],
                                        'cashier'    => ['cls'=>'teacher',   'label'=>'Cashier'],
                                        'teacher'    => ['cls'=>'teacher',   'label'=>'Teacher'],
                                        default      => ['cls'=>'student',   'label'=>ucfirst($user->role)],
                                    };
                                @endphp
                                <tr data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}" data-user-role="{{ $user->role }}" data-user-active="{{ $user->is_active ? '1' : '0' }}">
                                    <td>
                                        <div class="user-row-name">
                                            <div class="user-row-avatar" style="background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                            <div>
                                                <div style="font-weight:600;">{{ $user->name }}</div>
                                                <div class="user-row-sub">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge {{ $db_badge['cls'] }}">{{ $db_badge['label'] }}</span></td>
                                    <td>
                                        <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td style="font-size:12px;color:var(--muted);">{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                                    <td>
                                        <div style="display:flex;gap:5px;">
                                            <button class="action-btn edit" title="Edit"
                                                onclick="openEditUserModal({{ $user->id }},'{{ addslashes($user->name) }}','{{ $user->email }}','{{ $user->role }}',{{ $user->is_active ? 'true' : 'false' }})">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button class="action-btn lock" title="Reset Password"
                                                onclick="openResetPasswordModal({{ $user->id }},'{{ addslashes($user->name) }}')">
                                                <i class="bi bi-key-fill"></i>
                                            </button>
                                            @if($user->id !== auth()->id())
                                            <button class="action-btn {{ $user->is_active ? 'delete' : 'view' }}" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                                onclick="confirmToggleStatus({{ $user->id }},'{{ addslashes($user->name) }}',{{ $user->is_active ? 'true' : 'false' }})">
                                                <i class="bi bi-person-{{ $user->is_active ? 'x' : 'check' }}-fill"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete"
                                                onclick="confirmDeleteUser({{ $user->id }},'{{ addslashes($user->name) }}')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Dashboard Pagination --}}
                        <div class="p-3 border-top" style="border-color:var(--border);">
                            <nav>
                                {{ $dashboardUsers->appends(['sort' => $sort, 'role' => $roleFilter, 'section' => 'dashboard'])->links() }}
                            </nav>
                            <div class="pagination-info">
                                Showing {{ $dashboardUsers->firstItem() }} to {{ $dashboardUsers->lastItem() }} of {{ $dashboardUsers->total() }} results
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
                    @forelse($logs->take(5) as $log)
                    @php
                        $recentIcon = match($log->event_type) {
                            'login'        => ['icon' => 'bi-box-arrow-in-right', 'cls' => 'success'],
                            'logout'       => ['icon' => 'bi-box-arrow-right',    'cls' => 'info'],
                            'failed_login' => ['icon' => 'bi-shield-x',           'cls' => 'danger'],
                            'create'       => ['icon' => 'bi-plus-circle-fill',   'cls' => 'success'],
                            'update'       => ['icon' => 'bi-pencil-fill',        'cls' => 'info'],
                            'delete'       => ['icon' => 'bi-trash-fill',         'cls' => 'danger'],
                            default        => ['icon' => 'bi-info-circle-fill',   'cls' => 'info'],
                        };
                    @endphp
                    <div class="log-item">
                        <div class="log-icon {{ $recentIcon['cls'] }}"><i class="bi {{ $recentIcon['icon'] }}"></i></div>
                        <div>
                            <div class="log-title">{{ Str::limit($log->description, 80) }}</div>
                            <div class="log-meta">
                                {{ $log->user_name ?? 'System' }}
                                @if($log->user_role) · {{ $log->user_role }} @endif
                                · {{ $log->created_at?->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding:24px;text-align:center;color:var(--muted);">
                        <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No activity logged yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div><!-- /section-dashboard -->

    <!-- ═══════════════════════════
         SECTION: USER MANAGEMENT
    ═══════════════════════════ -->
    <div id="section-users" class="dash-section" {{ $section !== 'users' ? 'style="display:none;"' : '' }}>
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
        {{-- Role filter tabs --}}
        <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
            @php
                $roleTabs = [
                    'all'        => 'All',
                    'superadmin' => 'Super Admin',
                    'admin'      => 'Admin',
                    'finance'    => 'Finance',
                    'cashier'    => 'Cashier',
                    'teacher'    => 'Teacher',
                    'student'    => 'Student',
                ];
                $roleCounts = [
                    'all'        => $userManagementUsers->total(),
                    'superadmin' => $stats['superadmins'],
                    'admin'      => $stats['admins'],
                    'finance'    => $stats['finance'],
                    'cashier'    => $stats['cashiers'],
                    'teacher'    => $stats['total_teachers'],
                    'student'    => $stats['total_students'],
                ];
            @endphp
            @foreach($roleTabs as $key => $label)
            <a href="?role={{ $key }}&sort={{ $sort }}&section=users"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1.5px solid {{ $roleFilter === $key ? 'var(--blue)' : 'var(--border)' }};background:{{ $roleFilter === $key ? 'var(--blue)' : '#fff' }};color:{{ $roleFilter === $key ? '#fff' : 'var(--text)' }};transition:all .15s;">
                {{ $label }}
                <span style="background:{{ $roleFilter === $key ? 'rgba(255,255,255,.25)' : 'var(--blue-pale)' }};color:{{ $roleFilter === $key ? '#fff' : 'var(--blue)' }};padding:1px 7px;border-radius:20px;font-size:10px;">{{ $roleCounts[$key] }}</span>
            </a>
            @endforeach
            <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
                <div class="dash-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="userSearchInput" placeholder="Search name or email…" oninput="filterUsersTable(this.value)">
                </div>
                <select class="form-fld" style="width:auto;padding:7px 10px;font-size:12px;" onchange="window.location.href='?sort='+this.value+'&role={{ $roleFilter }}&section=users'">
                    <option value="newest" {{ $sort==='newest'?'selected':'' }}>Newest</option>
                    <option value="oldest" {{ $sort==='oldest'?'selected':'' }}>Oldest</option>
                    <option value="name_asc" {{ $sort==='name_asc'?'selected':'' }}>A–Z</option>
                    <option value="name_desc" {{ $sort==='name_desc'?'selected':'' }}>Z–A</option>
                </select>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <h6>All User Accounts</h6>
                <span style="font-size:12px;color:var(--muted);" id="userCountLabel">{{ $userManagementUsers->total() }} account(s)</span>
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
                        @foreach($users as $user)
                        @php
                            $roleBadge = match($user->role) {
                                'superadmin' => ['cls'=>'superadmin','label'=>'Super Admin'],
                                'admin'      => ['cls'=>'admin',      'label'=>'Admin'],
                                'finance'    => ['cls'=>'teacher',    'label'=>'Finance'],
                                'cashier'    => ['cls'=>'teacher',    'label'=>'Cashier'],
                                default      => ['cls'=>'student',    'label'=>ucfirst($user->role)],
                            };
                        @endphp
                        <tr data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}" data-user-role="{{ $user->role }}" data-user-active="{{ $user->is_active ? '1' : '0' }}">
                            <td>
                                <div class="user-row-name">
                                    <div class="user-row-avatar" style="background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;">{{ strtoupper(substr($user->name,0,2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;">{{ $user->name }}</div>
                                        <div class="user-row-sub">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="role-badge {{ $roleBadge['cls'] }}">{{ $roleBadge['label'] }}</span></td>
                            <td>
                                @if($user->is_active)
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button class="action-btn edit" title="Edit User"
                                        onclick="openEditUserModal({{ $user->id }},'{{ addslashes($user->name) }}','{{ $user->email }}','{{ $user->role }}',{{ $user->is_active ? 'true' : 'false' }})">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="action-btn lock" title="Reset Password"
                                        onclick="openResetPasswordModal({{ $user->id }},'{{ addslashes($user->name) }}')">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                    <button class="action-btn {{ $user->is_active ? 'delete' : 'view' }}" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User"
                                        onclick="confirmToggleStatus({{ $user->id }},'{{ addslashes($user->name) }}',{{ $user->is_active ? 'true' : 'false' }})">
                                        <i class="bi {{ $user->is_active ? 'bi-person-x-fill' : 'bi-person-check-fill' }}"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button class="action-btn delete" title="Delete User"
                                        onclick="confirmDeleteUser({{ $user->id }},'{{ addslashes($user->name) }}')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- User Management Pagination --}}
                @if($userManagementUsers->hasPages())
                <div class="p-3 border-top" style="border-color:var(--border);">
                    <nav>
                        {{ $userManagementUsers->appends(['sort' => $sort, 'role' => $roleFilter, 'section' => 'users'])->links() }}
                    </nav>
                    <div class="pagination-info">
                        Showing {{ $userManagementUsers->firstItem() }} to {{ $userManagementUsers->lastItem() }} of {{ $userManagementUsers->total() }} users
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- ═══════════════════════════ -->
    <div id="section-roles" class="dash-section" {{ $section !== 'roles' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1>Roles &amp; Permissions</h1>
                <p>What each role can actually access — a reference, not a configuration screen.</p>
            </div>
        </div>

        <div class="content-card mb-4" style="border-left:4px solid var(--blue);">
            <div class="p-3" style="font-size:12.5px;color:var(--muted);display:flex;gap:10px;align-items:flex-start;">
                <i class="bi bi-info-circle-fill" style="color:var(--blue);font-size:16px;flex-shrink:0;margin-top:1px;"></i>
                <span>This page used to show editable Allow/Deny checkboxes that didn't actually save anywhere. Access levels in this system are enforced directly in code (route middleware and controller checks) for reliability — this page now shows what's <strong>actually</strong> true for each role, so it can't say something that isn't real.</span>
            </div>
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
                        <div id="role-admin" class="perm-module" style="cursor:pointer;" onclick="selectRole('admin')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-fill" style="color:var(--blue);"></i>
                                <span>Admin / Registrar</span>
                                <span class="role-badge admin ms-auto">Admin</span>
                            </div>
                        </div>
                        <div id="role-finance" class="perm-module" style="cursor:pointer;" onclick="selectRole('finance')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-lock-fill" style="color:#2471a3;"></i>
                                <span>Finance</span>
                                <span class="role-badge teacher ms-auto">Limited</span>
                            </div>
                        </div>
                        <div id="role-cashier" class="perm-module" style="cursor:pointer;" onclick="selectRole('cashier')">
                            <div class="perm-module-header">
                                <i class="bi bi-cash-coin" style="color:#b45309;"></i>
                                <span>Cashier</span>
                                <span class="role-badge teacher ms-auto">Limited</span>
                            </div>
                        </div>
                        <div id="role-teacher" class="perm-module" style="cursor:pointer;" onclick="selectRole('teacher')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield-half" style="color:var(--green);"></i>
                                <span>Teacher</span>
                                <span class="role-badge teacher ms-auto">Limited</span>
                            </div>
                        </div>
                        <div id="role-student" class="perm-module" style="cursor:pointer;" onclick="selectRole('student')">
                            <div class="perm-module-header">
                                <i class="bi bi-shield" style="color:#d68910;"></i>
                                <span>Student</span>
                                <span class="role-badge student ms-auto">Own Data Only</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6 id="permPanelTitle">Access — Super Admin</h6>
                    </div>
                    <div class="p-3">
                        @php
                            // Sourced directly from the real, enforced route middleware and
                            // controller checks in this codebase — not a separate config that
                            // could drift from what's actually true.
                            $roleCapabilities = [
                                'superadmin' => [
                                    'Everything Admin has, plus:' => [
                                        'Create, edit, deactivate, and delete any user account (including other Super Admins)',
                                        'Create and restore full database backups',
                                        'Toggle enrollment open/closed and site-wide maintenance mode',
                                        'View system-wide activity logs across every role',
                                    ],
                                ],
                                'admin' => [
                                    'Enrollment & Students' => [
                                        'Approve, decline, and process payment for enrollment applications',
                                        'Full student record management, including archive/restore/permanent delete',
                                        'Section changes and walk-in enrollment',
                                    ],
                                    'Academics' => [
                                        'Manage subjects, sections, schedules, and teacher assignments',
                                        'Approve or reject submitted grades; run promotion and summer class flows',
                                        'Guidance records',
                                    ],
                                    'Finance (view + act)' => [
                                        'Approve or reject online (Xendit) and payment-screenshot transactions',
                                        'Configure fee amounts and school-year settings',
                                    ],
                                    'Content & Reports' => [
                                        'Announcements, news, contact message inbox',
                                        'Academic, financial, enrollment, and guidance reports',
                                    ],
                                ],
                                'finance' => [
                                    'Payments (view only)' => [
                                        'View all payment transactions and installment plans — cannot approve or reject (that stays with Admin, by design)',
                                        'Record a walk-in cash/GCash payment directly',
                                        'View fee settings and financial reports',
                                    ],
                                    'Own account' => [
                                        'Own profile, password, and personal Audit Trail (own actions only — not other staff\'s)',
                                    ],
                                ],
                                'cashier' => [
                                    'Payment Processing' => [
                                        'Process a cash payment or generate an online (Xendit) payment link for any student',
                                        'Search/list students, set an enrollment payment plan',
                                        'Daily collection report and receipts list',
                                    ],
                                    'Own account' => [
                                        'Own profile, password, and personal Audit Trail (own actions only — not other staff\'s)',
                                    ],
                                ],
                                'teacher' => [
                                    'Own Sections Only' => [
                                        'Enter and manage grades, attendance, for sections/subjects they\'re assigned to — not other teachers\' sections',
                                        'Print DepEd forms (SF9/SF5) and class report cards for their own students',
                                        'Schedule Parent-Teacher Conferences',
                                    ],
                                    'Announcements' => [
                                        'Post announcements visible to all students (targeting is not otherwise configurable)',
                                    ],
                                    'Own account' => [
                                        'Own profile and password (OTP-verified change)',
                                    ],
                                ],
                                'student' => [
                                    'Own Records Only' => [
                                        'View own profile, grades, schedule, and payment history',
                                        'Upload/manage own documents — cannot see other students\' documents',
                                        'Submit enrollment/re-enrollment and section-change requests',
                                        'Make payments (cash at cashier, or online via Xendit)',
                                    ],
                                    'Own account' => [
                                        'Own profile and password (OTP-verified change)',
                                    ],
                                ],
                            ];
                            $roleTitles = [
                                'superadmin' => 'Super Admin', 'admin' => 'Admin / Registrar',
                                'finance' => 'Finance', 'cashier' => 'Cashier',
                                'teacher' => 'Teacher', 'student' => 'Student',
                            ];
                        @endphp
                        @foreach($roleCapabilities as $roleKey => $groups)
                        <div class="role-perm-panel" id="permPanel-{{ $roleKey }}" data-title="Access — {{ $roleTitles[$roleKey] }}" style="{{ $roleKey === 'superadmin' ? '' : 'display:none;' }}">
                            @foreach($groups as $groupName => $items)
                            <div class="perm-module">
                                <div class="perm-module-header">
                                    <i class="bi bi-check-circle-fill" style="color:var(--green);"></i>
                                    <span>{{ $groupName }}</span>
                                </div>
                                @foreach($items as $item)
                                <div class="perm-row">
                                    <span class="perm-row-name" style="font-weight:400;">{{ $item }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-roles -->

    <!-- ═══════════════════════════
         SECTION: SYSTEM LOGS
    ═══════════════════════════ -->
    <div id="section-logs" class="dash-section" {{ $section !== 'logs' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1>System Logs</h1>
                <p>Full audit trail of all system activities.</p>
            </div>
            <a href="#" id="exportLogsBtn" class="btn-dash btn-secondary" onclick="exportLogsCsv(event)">
                <i class="bi bi-download"></i> Export Logs
            </a>
        </div>

        <div class="content-card mb-4">
            <div class="p-3">
                <form method="GET" action="{{ url()->current() }}" id="logs-filter-form" class="row g-3">
                    <input type="hidden" name="section" value="logs">
                    <div class="col-md-3">
                        <div class="dash-search" style="width:100%;">
                            <i class="bi bi-search"></i>
                            <input type="text" name="log_search" id="logSearchInput" placeholder="Search logs…" value="{{ request('log_search') }}" oninput="debounceFormSubmit(this)">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-fld" name="log_type" id="logTypeFilter" onchange="this.form.submit()">
                            <option value="">All Event Types</option>
                            <option value="login" {{ request('log_type') === 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ request('log_type') === 'logout' ? 'selected' : '' }}>Logout</option>
                            <option value="failed_login" {{ request('log_type') === 'failed_login' ? 'selected' : '' }}>Failed Login</option>
                            <option value="denied" {{ request('log_type') === 'denied' ? 'selected' : '' }}>Denied Attempt</option>
                            <option value="create" {{ request('log_type') === 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('log_type') === 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ request('log_type') === 'delete' ? 'selected' : '' }}>Delete</option>
                            <option value="error" {{ request('log_type') === 'error' ? 'selected' : '' }}>Error</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-fld" name="log_role" id="logRoleFilter" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            <option value="superadmin" {{ request('log_role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ request('log_role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="finance" {{ request('log_role') === 'finance' ? 'selected' : '' }}>Finance</option>
                            <option value="cashier" {{ request('log_role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="teacher" {{ request('log_role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ request('log_role') === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-fld" name="log_date" id="logDateFilter" onchange="this.form.submit()" value="{{ request('log_date') }}">
                    </div>
                    <div class="col-md-2">
                        <a href="{{ url()->current() }}?section=logs" class="btn-dash btn-secondary" style="width:100%;justify-content:center;">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
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
                    <tbody id="logsTableBody">
                        @forelse($logs as $log)
                        @php
                            $badgeClass = match($log->event_type) {
                                'login'        => 'success',
                                'logout'       => 'active',
                                'failed_login' => 'warning',
                                'denied'       => 'danger',
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
                                'denied'       => 'Denied Attempt',
                                'create'       => 'Create',
                                'update'       => 'Update',
                                'delete'       => 'Delete',
                                'error'        => 'Error',
                                default        => ucfirst($log->event_type),
                            };
                        @endphp
                        <tr class="log-row"
                            data-type="{{ $log->event_type }}"
                            data-desc="{{ strtolower($log->description . ' ' . $log->user_name) }}"
                            data-ts="{{ $log->created_at?->toDateString() }}"
                            data-role="{{ $log->user_role }}">
                            <td><span class="status-badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                            <td style="max-width:420px;word-break:break-word;">{{ $log->description }}</td>
                            <td>
                                @if($log->user_name)
                                <div style="font-size:13px;font-weight:600;">{{ $log->user_name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $log->user_role ?? '' }}</div>
                                @else
                                <span style="color:var(--muted);font-size:12px;">System</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">{{ $log->ip_address ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                                {{ $log->created_at?->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                                <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                                No activity logs yet. Events will appear here after users log in.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->total() > 0)
            <div class="p-3 border-top" style="border-color:var(--border);">
                <nav>
                    {{ $logs->appends(['log_type' => request('log_type'), 'log_search' => request('log_search'), 'log_date' => request('log_date'), 'section' => 'logs'])->links() }}
                </nav>
                <div class="pagination-info">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} log(s)
                </div>
            </div>
            @endif
        </div>
    </div><!-- /section-logs -->

    <!-- ═══════════════════════════
         SECTION: BACKUP & RESTORE
    ═══════════════════════════ -->
    <div id="section-backup" class="dash-section" {{ $section !== 'backup' ? 'style="display:none;"' : '' }}>
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
                        <h6>Backup History ({{ count($backups) }} file{{ count($backups) !== 1 ? 's' : '' }})</h6>
                        <button class="btn-dash btn-primary" style="padding:7px 16px;font-size:12px;" onclick="runBackup()" id="btnCreateBackup">
                            <i class="bi bi-database-add"></i> Create Backup Now
                        </button>
                    </div>
                    <div class="p-3">
                        @forelse($backups as $bk)
                        <div class="backup-card" id="bk-{{ Str::slug($bk['name']) }}">
                            <div style="flex:1;min-width:0;">
                                <div class="backup-info-name" style="font-size:13px;font-weight:700;color:var(--text);word-break:break-all;">{{ $bk['name'] }}</div>
                                <div class="backup-info-meta" style="font-size:11px;color:var(--muted);margin-top:3px;">
                                    <i class="bi bi-clock me-1"></i>{{ $bk['created'] }} &nbsp;·&nbsp;
                                    <i class="bi bi-hdd me-1"></i>{{ $bk['size'] }}
                                </div>
                            </div>
                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                <a href="{{ route('superadmin.backup.download', $bk['name']) }}" class="action-btn view" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button class="action-btn edit" title="Restore Database" onclick="restoreBackup('{{ $bk['name'] }}')">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button class="action-btn delete" title="Delete Backup" onclick="deleteBackup('{{ $bk['name'] }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-database" style="font-size:36px;display:block;margin-bottom:10px;opacity:0.25;"></i>
                            <div style="font-weight:600;margin-bottom:6px;">No backups yet</div>
                            <div style="font-size:12px;">Click <strong>Create Backup Now</strong> to generate your first backup.</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card mb-3">
                    <div class="content-card-header"><h6><i class="bi bi-info-circle me-1" style="color:var(--blue);"></i>Backup Info</h6></div>
                    <div class="p-3" style="font-size:13px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Total Backups</span>
                            <strong>{{ count($backups) }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Latest Backup</span>
                            <strong>{{ count($backups) ? $backups[0]['created'] : '—' }}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                            <span style="color:var(--muted);">Storage Used</span>
                            @php $totalSize = array_sum(array_column($backups,'size_raw')); @endphp
                            <strong>{{ $totalSize >= 1048576 ? round($totalSize/1048576,2).' MB' : round($totalSize/1024,1).' KB' }}</strong>
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
    <div id="section-announcements" class="dash-section" {{ $section !== 'announcements' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1><i class="bi bi-megaphone-fill me-2" style="color:var(--blue);"></i>Announcements</h1>
                <p>Post and manage school-wide announcements visible on the public website.</p>
            </div>
        </div>

        @if(session('sa_success') && session('sa_section') === 'announcements')
            <div class="alert-success-bar"><i class="bi bi-check-circle-fill me-2"></i>{{ session('sa_success') }}</div>
        @endif

        {{-- Post New Announcement --}}
        <div class="content-card mb-4">
            <div class="content-card-header"><h6><i class="bi bi-plus-circle me-2" style="color:var(--blue);"></i>Post New Announcement</h6></div>
            <div class="p-4">
                <form method="POST" action="{{ route('superadmin.announcements.store') }}" enctype="multipart/form-data">
                    @csrf
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

        {{-- Announcements List --}}
        <div class="content-card">
            <div class="content-card-header"><h6>All Announcements ({{ $announcements->total() }})</h6></div>
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
                        @forelse($announcements as $ann)
                        <tr>
                            <td style="font-weight:600;max-width:260px;">{{ Str::limit($ann->title, 60) }}</td>
                            <td><span class="role-badge teacher">{{ ucfirst($ann->category) }}</span></td>
                            <td>
                                @if($ann->image)
                                    <img src="{{ asset('storage/'.$ann->image) }}" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">
                                @else
                                    <span style="font-size:11px;color:var(--muted);">None</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($ann->audience) }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $ann->teacher?->name ?? 'Superadmin' }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $ann->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge {{ $ann->is_active ? 'active' : 'inactive' }}">
                                    {{ $ann->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button type="button" class="action-btn view" title="Edit"
                                        onclick="openEditAnnouncementModal({{ $ann->id }}, {{ \Illuminate\Support\Js::from($ann->title) }}, {{ \Illuminate\Support\Js::from($ann->content) }}, {{ \Illuminate\Support\Js::from($ann->category) }}, {{ \Illuminate\Support\Js::from($ann->audience) }}, {{ \Illuminate\Support\Js::from($ann->created_at->format('Y-m-d\TH:i')) }})">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.announcements.toggle', $ann) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="action-btn {{ $ann->is_active ? 'edit' : 'view' }}" title="{{ $ann->is_active ? 'Hide' : 'Show' }}">
                                            <i class="bi bi-{{ $ann->is_active ? 'eye-slash' : 'eye' }}-fill"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('superadmin.announcements.destroy', $ann) }}" style="margin:0;"
                                          onsubmit="return confirmFormSubmit(this, 'Delete this announcement?', 'Delete Announcement')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-megaphone" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No announcements yet.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
            <div style="padding:16px;display:flex;justify-content:center;">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div><!-- /section-announcements -->

    <!-- ══════════════════════════
         SECTION: NEWS
    ══════════════════════════ -->
    <div id="section-news" class="dash-section" {{ $section !== 'news' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1><i class="bi bi-newspaper me-2" style="color:var(--blue);"></i>News</h1>
                <p>Publish and manage news articles displayed on the public website.</p>
            </div>
        </div>

        @if(session('sa_success') && session('sa_section') === 'news')
            <div class="alert-success-bar"><i class="bi bi-check-circle-fill me-2"></i>{{ session('sa_success') }}</div>
        @endif

        {{-- Post New Article --}}
        <div class="content-card mb-4">
            <div class="content-card-header"><h6><i class="bi bi-plus-circle me-2" style="color:var(--blue);"></i>Publish News Article</h6></div>
            <div class="p-4">
                <form method="POST" action="{{ route('superadmin.news.store') }}" enctype="multipart/form-data">
                    @csrf
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

        {{-- News List --}}
        <div class="content-card">
            <div class="content-card-header"><h6>All News Articles ({{ $newsArticles->total() }})</h6></div>
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
                        @forelse($newsArticles as $article)
                        <tr>
                            <td style="font-weight:600;max-width:280px;">{{ Str::limit($article->title, 60) }}</td>
                            <td><span class="role-badge student">{{ ucfirst($article->category) }}</span></td>
                            <td>
                                @if($article->image)
                                    <img src="{{ asset('storage/'.$article->image) }}" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">
                                @else
                                    <span style="font-size:11px;color:var(--muted);">No image</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">{{ $article->author?->name ?? 'Superadmin' }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $article->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge {{ $article->is_active ? 'active' : 'inactive' }}">
                                    {{ $article->is_active ? 'Published' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button type="button" class="action-btn view" title="Edit"
                                        onclick="openEditNewsModal({{ $article->id }}, {{ \Illuminate\Support\Js::from($article->title) }}, {{ \Illuminate\Support\Js::from($article->body) }}, {{ \Illuminate\Support\Js::from($article->category) }}, {{ \Illuminate\Support\Js::from($article->created_at->format('Y-m-d\TH:i')) }})">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.news.toggle', $article) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="action-btn {{ $article->is_active ? 'edit' : 'view' }}" title="{{ $article->is_active ? 'Hide' : 'Show' }}">
                                            <i class="bi bi-{{ $article->is_active ? 'eye-slash' : 'eye' }}-fill"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('superadmin.news.destroy', $article) }}" style="margin:0;"
                                          onsubmit="return confirmFormSubmit(this, 'Delete this news article?', 'Delete News Article')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-newspaper" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No news articles yet.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($newsArticles->hasPages())
            <div style="padding:16px;display:flex;justify-content:center;">{{ $newsArticles->links() }}</div>
            @endif
        </div>
    </div><!-- /section-news -->

    <div id="section-reports" class="dash-section" {{ $section !== 'reports' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1>Reports</h1>
                <p>System-wide reports and analytics.</p>
            </div>
        </div>
        <!-- Report Charts -->
        @php
            $saRptMonths=[]; $saRptEnroll=[];
            for($i=5;$i>=0;$i--){
                $m=now()->subMonths($i);
                $saRptMonths[]=$m->format('M Y');
                $saRptEnroll[]=\App\Models\Enrollment::whereYear('created_at',$m->year)->whereMonth('created_at',$m->month)->count();
            }
        @endphp
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
                        <a href="{{ route('superadmin.reports.user-report-pdf') }}" class="btn-dash btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-journal-text" style="font-size:36px;color:var(--gold);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">Audit Log Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">All system activities and audit trail.</p>
                        <a href="{{ route('superadmin.reports.audit-log-report-pdf') }}" class="btn-dash btn-gold" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-database-fill" style="font-size:36px;color:var(--orange);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">System Health Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Database size, backups, and performance.</p>
                        <a href="{{ route('superadmin.reports.system-health-report-pdf') }}" class="btn-dash btn-success" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-reports -->

    <!-- ═══════════════════════════
         SECTION: SETTINGS
    ═══════════════════════════ -->
    <div id="section-settings" class="dash-section" {{ $section !== 'settings' ? 'style="display:none;"' : '' }}>
        <div class="section-header">
            <div>
                <h1>Settings</h1>
                <p>Manage your account and system preferences.</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Account Info --}}
            <div class="col-md-5">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-person-circle me-2" style="color:var(--blue);"></i>Account Information</h6>
                    </div>
                    <div class="p-4">

                        @if(session('photo_success'))
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-check-circle-fill"></i> {{ session('photo_success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('superadmin.settings.photo') }}" enctype="multipart/form-data" id="sa-photo-form">
                            @csrf
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                                <div style="position:relative;flex-shrink:0;width:72px;height:72px;">
                                    @if(Auth::user()->profile_photo)
                                        <img id="sa-avatar-img" src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
                                    @else
                                        <div id="sa-avatar-placeholder" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-person-fill" style="font-size:30px;color:#fff;"></i>
                                        </div>
                                        <img id="sa-avatar-img" src="" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);display:none;">
                                    @endif
                                    <label for="sa-photo-input" title="Change photo"
                                           style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:var(--blue);border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                        <i class="bi bi-camera-fill" style="font-size:11px;color:#fff;"></i>
                                    </label>
                                    <input type="file" id="sa-photo-input" name="photo" accept="image/jpeg,image/png,image/webp"
                                           style="display:none;" onchange="previewSAPhoto(this)">
                                </div>
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:var(--text);">{{ Auth::user()->name }}</div>
                                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ Auth::user()->email }}</div>
                                    <span style="display:inline-block;background:#fdecea;color:#c0392b;border:1px solid #f5c6cb;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px;">Super Admin</span>
                                </div>
                            </div>
                            <button type="submit" id="sa-photo-submit" style="display:none;"></button>
                        </form>

                        <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;flex-direction:column;gap:0;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Full Name</span>
                                <span style="font-size:12px;font-weight:600;">{{ Auth::user()->name }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Email</span>
                                <span style="font-size:12px;font-weight:600;">{{ Auth::user()->email }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;">
                                <span style="font-size:12px;color:var(--muted);">Role</span>
                                <span style="font-size:12px;font-weight:600;">Super Admin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="col-md-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-lock-fill me-2" style="color:var(--blue);"></i>Change Password</h6>
                    </div>
                    <div class="p-4">
                        @if(session('password_success'))
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-check-circle-fill"></i> {{ session('password_success') }}
                            </div>
                        @endif
                        @if($errors->has('current_password'))
                            <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('current_password') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('superadmin.settings.password') }}">
                            @csrf
                            @method('PUT')
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
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <div id="sa-enr-window-badge"
                     style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;
                            background:#e8f8f0;color:#1a7a44;border:1.5px solid #27ae60;">
                    <span style="width:8px;height:8px;border-radius:50%;display:inline-block;background:#27ae60;box-shadow:0 0 0 2px #a8d5b5;"></span>
                    <span id="sa-enr-window-label">Enrollment OPEN</span>
                </div>
                <button class="btn-dash btn-secondary" id="sa-enr-toggle-btn" onclick="saToggleEnrollmentWindow()">
                    <i class="bi bi-lock-fill me-1"></i><span id="sa-enr-toggle-label">Close Enrollment</span>
                </button>
                <button class="btn-dash btn-primary" onclick="_loaded['enrollments']=false; loadEnrollments();">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
                <button class="btn-dash btn-primary" onclick="openWalkInEnrollmentModal()">
                    <i class="bi bi-person-plus-fill"></i> New Enrollment
                </button>
            </div>
        </div>
        <div class="row g-3 mb-4" id="enr-stats">
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div><div><div class="stat-value" id="enr-cnt-pending">—</div><div class="stat-label">Pending</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div><div><div class="stat-value" id="enr-cnt-approved">—</div><div class="stat-label">Approved</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div><div><div class="stat-value" id="enr-cnt-enrolled">—</div><div class="stat-label">Enrolled</div></div></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div><div><div class="stat-value" id="enr-cnt-declined">—</div><div class="stat-label">Declined</div></div></div></div>
        </div>
        <div class="content-card mb-3">
            <div class="p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="enr-search" class="form-fld" placeholder="Search by name, email, or reference number..."
                                   oninput="clearTimeout(window._enrSearchTimer); window._enrSearchTimer=setTimeout(function(){ _loaded['enrollments']=false; loadEnrollments(1); }, 400);">
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <select id="enr-status-filter" class="form-fld" onchange="_loaded['enrollments']=false; loadEnrollments(1);">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="enrolled">Enrolled</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="enr-grade-filter" class="form-fld" onchange="_loaded['enrollments']=false; loadEnrollments(1);">
                            <option value="all">All Grades</option>
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
                    <div class="col-md-3">
                        <select id="enr-sort-filter" class="form-fld" onchange="_loaded['enrollments']=false; loadEnrollments(1);">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                        </select>
                    </div>
                </div>
            </div>
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
            <div id="enr-pagination" class="p-3 border-top" style="border-color:var(--border);"></div>
        </div>
    </div><!-- /section-enrollments -->

    @include('admin.enrollments.view-modal')
    @include('admin.enrollments.walk-in-modal')

    <script src="/js/ph-address.js"></script>
    <script>
        // Walk-in enrollment address cascade (same shared library + field IDs as Admin)
        PHAddress.initCascade({
            region:   'walkin-region',
            province: 'walkin-province',
            city:     'walkin-city',
            barangay: 'walkin-barangay',
        });
    </script>

    <!-- Reject Document Modal (enrollment documents review) -->
    <div class="modal fade" id="saRejectDocModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#e74c3c,#c0392b);color:#fff;border:none;">
                    <h5 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>Reject Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-lbl">Rejection Reason</label>
                    <textarea id="sa-reject-doc-reason" class="form-fld" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-dash btn-danger" onclick="saSubmitRejectDoc()"><i class="bi bi-x-lg"></i> Reject Document</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Viewer Modal (enrollment documents review) -->
    <div class="modal fade" id="saDocViewerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),#2c5282);color:#fff;border:none;">
                    <h5 class="modal-title" id="sa-docv-title">Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="min-height:200px;">
                    <div id="sa-docv-img-wrap" style="text-align:center;padding:24px;">
                        <img id="sa-docv-img" src="" alt="Document" style="max-width:100%;max-height:65vh;border-radius:8px;">
                    </div>
                    <div id="sa-docv-pdf-wrap" style="display:none;">
                        <iframe id="sa-docv-pdf" src="" style="width:100%;height:70vh;border:none;"></iframe>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="gap:8px;">
                    <a id="sa-docv-download" href="" download class="btn-dash btn-secondary" style="text-decoration:none;"><i class="bi bi-download"></i> Download</a>
                    <a id="sa-docv-open" href="" target="_blank" class="btn-dash btn-primary" style="text-decoration:none;"><i class="bi bi-box-arrow-up-right"></i> Open in New Tab</a>
                </div>
            </div>
        </div>
    </div>

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
            <div id="teachers-pagination" class="p-3 border-top" style="border-color:var(--border);"></div>
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
            <div id="subjects-pagination" class="p-3 border-top" style="border-color:var(--border);"></div>
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
            <div style="display:flex;gap:10px;">
                <button class="btn-dash btn-primary" onclick="openSectionModal()">
                    <i class="bi bi-plus-lg"></i> Add Section
                </button>
                <button class="btn-dash btn-secondary" onclick="loadSectionsMgmt()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
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
            <div id="sections-pagination" class="p-3 border-top" style="border-color:var(--border);"></div>
        </div>
    </div><!-- /section-sections-mgmt -->

    <!-- ═══════════════════════════
         SECTION: SCHEDULES
    ═══════════════════════════ -->
    <div id="section-schedules" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Schedule Management</h1>
                <p>Manage class schedules by grade level and section.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-dash btn-secondary" onclick="openCopyTermModal()">
                    <i class="bi bi-copy"></i> Copy Term
                </button>
                <button class="btn-dash btn-primary" onclick="openScheduleModal()">
                    <i class="bi bi-plus-lg"></i> Add Schedule
                </button>
            </div>
        </div>

        {{-- Schedule Filters --}}
        <div class="sched-filter-card">
            <div class="sched-filter-title">Filters</div>
            <div class="sched-toolbar">
                <div class="sched-field-group">
                    <label for="scheduleGradeFilter">Grade Level</label>
                    <select class="form-fld" id="scheduleGradeFilter">
                        <option value="">— Select Grade —</option>
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
                <div class="sched-field-group">
                    <label for="scheduleSectionFilter">Section</label>
                    <select class="form-fld" id="scheduleSectionFilter" onchange="loadScheduleGrid()">
                        <option value="">All Sections</option>
                    </select>
                </div>
                <div class="sched-field-group">
                    <label for="scheduleTermFilter">Term</label>
                    <select class="form-fld" id="scheduleTermFilter" onchange="loadScheduleGrid()">
                        <option value="1">1st Term</option>
                        <option value="2">2nd Term</option>
                        <option value="3">3rd Term</option>
                    </select>
                </div>
                <button class="btn-dash btn-primary" onclick="loadScheduleGrid()">
                    <i class="bi bi-search"></i> Load
                </button>
            </div>
        </div>

        {{-- Empty State --}}
        <div id="scheduleEmptyState" class="sched-empty-card">
            <div class="sched-empty-icon"><i class="bi bi-calendar-week"></i></div>
            <div style="font-size:16px; font-weight:700; color:var(--text); margin-bottom:4px;">Select a grade level to view schedules</div>
            <div style="font-size:13px; color:var(--muted);">Choose a grade above, or jump straight to one below.</div>
            <div class="sched-quickpick">
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('nursery')"><i class="bi bi-lightning-fill"></i> Nursery</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('kindergarten')"><i class="bi bi-lightning-fill"></i> Kindergarten</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade1')"><i class="bi bi-lightning-fill"></i> Grade 1</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade2')"><i class="bi bi-lightning-fill"></i> Grade 2</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade3')"><i class="bi bi-lightning-fill"></i> Grade 3</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade4')"><i class="bi bi-lightning-fill"></i> Grade 4</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade5')"><i class="bi bi-lightning-fill"></i> Grade 5</button>
                <button type="button" class="sched-quickpick-btn" onclick="quickSelectScheduleGrade('grade6')"><i class="bi bi-lightning-fill"></i> Grade 6</button>
            </div>
        </div>

        {{-- Schedule Grid --}}
        <div id="scheduleGridContainer" class="content-card" style="display:none;">
            <div class="content-card-header">
                <h6 id="scheduleInfoText">Schedule</h6>
                <a href="#" id="saSchedulePdfLink" target="_blank" class="btn-dash btn-primary" style="display:inline-flex;align-items:center;gap:7px;text-decoration:none;">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                </a>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:0 16px 12px;font-size:11.5px;color:#64748b;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:11px;"></i>Red = schedule conflict
            </div>
            <div style="overflow-x:auto;">
                <table class="schedule-grid-table">
                    <thead>
                        <tr>
                            <th style="width:100px;min-width:100px;">Time</th>
                            <th class="day-mon" style="width:calc((100% - 100px) / 5);min-width:120px;">Monday</th>
                            <th class="day-tue" style="width:calc((100% - 100px) / 5);min-width:120px;">Tuesday</th>
                            <th class="day-wed" style="width:calc((100% - 100px) / 5);min-width:120px;">Wednesday</th>
                            <th class="day-thu" style="width:calc((100% - 100px) / 5);min-width:120px;">Thursday</th>
                            <th class="day-fri" style="width:calc((100% - 100px) / 5);min-width:120px;">Friday</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleGridBody"></tbody>
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

            <!-- Website Maintenance Toggle -->
            <div class="col-md-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-globe2 me-2" style="color:var(--orange);"></i>Website Maintenance Mode</h6>
                    </div>
                    <div class="p-4">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--text);">Public Website</div>
                                <div style="font-size:12px;color:var(--muted);margin-top:3px;" id="site-maint-toggle-desc">Loading current status…</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <span id="site-maint-toggle-label" style="font-size:12px;font-weight:700;"></span>
                                <button id="btn-toggle-site-maintenance" onclick="toggleSiteMaintenance()"
                                    style="padding:9px 20px;border-radius:8px;border:none;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                                    Toggle
                                </button>
                            </div>
                        </div>
                        <div style="font-size:12px;color:var(--muted);background:#fef5e7;border-radius:8px;padding:10px 14px;line-height:1.6;">
                            <i class="bi bi-exclamation-triangle-fill me-1" style="color:var(--orange);"></i>
                            When ON, the public website (home, about, admission, news, etc.) displays a maintenance notice.
                            Student/Teacher portals and admin access are not affected — see Portal Maintenance below.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portal Maintenance Toggle -->
            <div class="col-md-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-tools me-2" style="color:var(--orange);"></i>Portal Maintenance Mode</h6>
                    </div>
                    <div class="p-4">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--text);">Student &amp; Teacher Portals</div>
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
                            When ON, the Student and Teacher portals show a maintenance notice.
                            The public website, Cashier, and Finance portals are not affected.
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
                    @csrf
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
                            <option value="finance">Finance</option>
                            <option value="cashier">Cashier</option>
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
                    @csrf
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
                            <option value="finance">Finance</option>
                            <option value="cashier">Cashier</option>
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
     EDIT ANNOUNCEMENT MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <div style="font-size:16px;font-weight:800;color:#fff;">Edit Announcement</div>
                <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:1px;">Category, audience, content, and post date can all be changed here</div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:20px 24px;background:#fff;">
                <form id="editAnnouncementForm">
                    @csrf
                    <input type="hidden" id="edit-ann-id" name="id">
                    <div class="mb-3">
                        <label class="form-lbl">Title</label>
                        <input type="text" id="edit-ann-title" name="title" class="form-fld" required maxlength="255">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-lbl">Category</label>
                            <select id="edit-ann-category" name="category" class="form-fld" required>
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="reminder">Reminder</option>
                                <option value="activity">Activity</option>
                                <option value="enrollment">Enrollment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-lbl">Audience</label>
                            <select id="edit-ann-audience" name="audience" class="form-fld" required>
                                <option value="all">All (Public)</option>
                                <option value="parents">Parents</option>
                                <option value="teachers">Teachers</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-lbl">Posted Date</label>
                            <input type="datetime-local" id="edit-ann-posted-at" name="posted_at" class="form-fld">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Content</label>
                        <textarea id="edit-ann-content" name="content" class="form-fld" rows="5" required style="resize:vertical;"></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-lbl">Replace Cover Image (optional)</label>
                        <input type="file" id="edit-ann-image" name="image" class="form-fld" accept="image/jpeg,image/png,image/webp">
                    </div>
                </form>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" onclick="submitEditAnnouncement()" id="btnUpdateAnnouncement"
                    style="padding:10px 22px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:7px;">
                    <i class="bi bi-floppy-fill"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     EDIT NEWS MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="editNewsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <div style="font-size:16px;font-weight:800;color:#fff;">Edit News Article</div>
                <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:1px;">Category, body, and post date can all be changed here</div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:20px 24px;background:#fff;">
                <form id="editNewsForm">
                    @csrf
                    <input type="hidden" id="edit-news-id" name="id">
                    <div class="mb-3">
                        <label class="form-lbl">Headline</label>
                        <input type="text" id="edit-news-title" name="title" class="form-fld" required maxlength="255">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-lbl">Category</label>
                            <select id="edit-news-category" name="category" class="form-fld" required>
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="events">Events</option>
                                <option value="activity">Activity</option>
                                <option value="achievement">Achievement</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl">Posted Date</label>
                            <input type="datetime-local" id="edit-news-posted-at" name="posted_at" class="form-fld">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Body</label>
                        <textarea id="edit-news-body" name="body" class="form-fld" rows="5" required style="resize:vertical;"></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-lbl">Replace Cover Photo (optional)</label>
                        <input type="file" id="edit-news-image" name="image" class="form-fld" accept="image/jpeg,image/png,image/webp">
                    </div>
                </form>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" data-bs-dismiss="modal"
                    style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" onclick="submitEditNews()" id="btnUpdateNews"
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
                    @csrf
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

    // ── Breadcrumbs ──
    const BREADCRUMB_LABELS = {
        dashboard: 'Dashboard', users: 'User Management', roles: 'Roles & Permissions',
        logs: 'Activity Logs', backup: 'Backup & Restore', reports: 'Reports', settings: 'Settings',
        enrollments: 'Enrollment Oversight', teachers: 'Teacher Management', subjects: 'Subject Management',
        'sections-mgmt': 'Section Management', schedules: 'Schedule Management', 'sys-settings': 'System Settings',
        announcements: 'Announcements', news: 'News Management',
    };
    function updateBreadcrumb(name) {
        const el = document.getElementById('bc-current');
        const skel = document.getElementById('bc-current-skel');
        if (!el) return;
        if (!skel) { el.textContent = BREADCRUMB_LABELS[name] || name; return; }
        el.style.display = 'none';
        skel.style.display = 'inline-block';
        setTimeout(function() {
            el.textContent = BREADCRUMB_LABELS[name] || name;
            el.style.display = '';
            skel.style.display = 'none';
        }, 280);
    }

    function showSection(name) {
        sections.forEach(s => {
            const el = document.getElementById('section-' + s);
            if (el) el.style.display = s === name ? '' : 'none';
            const nav = document.getElementById('nav-' + s);
            if (nav) nav.classList.toggle('active', s === name);
        });
        updateBreadcrumb(name);
        localStorage.setItem('currentSuperAdminSection', name);
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
            if (name === 'schedules')     loadScheduleGrid();
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
        @if(session('settings_tab') || session('photo_success') || session('password_success') || $errors->has('current_password'))
        showSection('settings');
        @else
        // Priority: explicit ?section= in the URL (e.g. a filter/sort reload),
        // then whatever tab was open last time (same "remember where I left
        // off" behavior as the Admin portal), then the server-picked default.
        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section') || localStorage.getItem('currentSuperAdminSection') || '{{ $section }}';
        if (section && sections.includes(section)) {
            showSection(section);
        }
        @endif
    });

    // ── Auto-refresh when tab becomes visible again ──
    // Handles returning from another browser tab/window after >=30s away,
    // same behavior as the Admin portal, so data doesn't go stale silently.
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

        document.querySelectorAll('.role-perm-panel').forEach(function(p) { p.style.display = 'none'; });
        var panel = document.getElementById('permPanel-' + role);
        if (panel) {
            panel.style.display = '';
            var title = document.getElementById('permPanelTitle');
            if (title) title.textContent = panel.dataset.title;
        }
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
        // message/sub are set via textContent below, not interpolated into
        // this HTML string — several callers pass a server response's
        // `message` field straight through here.
        el.innerHTML =
            '<div class="sa-toast-icon"><i class="bi ' + (icons[type] || icons.success) + '"></i></div>' +
            '<div><div class="sa-toast-msg"></div>' + (sub ? '<div class="sa-toast-sub"></div>' : '') + '</div>';
        el.querySelector('.sa-toast-msg').textContent = message;
        if (sub) el.querySelector('.sa-toast-sub').textContent = sub;
        wrap.appendChild(el);
        setTimeout(function() { el.style.opacity = '0'; el.style.transform = 'translateX(30px)'; el.style.transition = 'all .3s'; setTimeout(function() { el.remove(); }, 300); }, 3500);
    }

    // ── User Management ────────────────────────────────────────
    var createUserModal, editUserModal, resetPasswordModal, saConfirmModal;
    var editAnnouncementModal, editNewsModal;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        createUserModal    = new bootstrap.Modal(document.getElementById('createUserModal'));
        editUserModal      = new bootstrap.Modal(document.getElementById('editUserModal'));
        resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        saConfirmModal      = new bootstrap.Modal(document.getElementById('saConfirmModal'));
        var annModalEl = document.getElementById('editAnnouncementModal');
        var newsModalEl = document.getElementById('editNewsModal');
        if (annModalEl)  editAnnouncementModal = new bootstrap.Modal(annModalEl);
        if (newsModalEl) editNewsModal         = new bootstrap.Modal(newsModalEl);
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

    // Shared helper for every logout <form onsubmit="return confirmLogout(this)">
    // in this file — reuses the same confirm modal as the other super admin
    // actions instead of a plain confirm() popup.
    function confirmLogout(form) {
        saConfirm({
            title: 'Log Out',
            msg: 'Are you sure you want to log out?',
            icon: 'bi-box-arrow-left',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Log Out',
            btnColor: '#dc2626',
            onConfirm: function() { form.submit(); }
        });
        return false;
    }

    // Generic helper for any <form onsubmit="return confirmFormSubmit(this, 'message')">
    // — same confirm modal, styled as a destructive (red) action by default
    // since every current use of this is a delete.
    function confirmFormSubmit(form, message, title) {
        saConfirm({
            title: title || 'Please Confirm',
            msg: message,
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: function() { form.submit(); }
        });
        return false;
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

    // ── Edit Announcement / Edit News ──────────────────
    // File uploads + a spoofed PUT don't mix well in plain PHP (multipart
    // bodies only get parsed on a real POST) — so these submit as an actual
    // POST with a `_method=PUT` field, which Laravel still routes to the
    // PUT route, while the wire-level request stays POST so the optional
    // image upload is received correctly either way.
    function openEditAnnouncementModal(id, title, content, category, audience, postedAt) {
        document.getElementById('edit-ann-id').value        = id;
        document.getElementById('edit-ann-title').value     = title;
        document.getElementById('edit-ann-content').value   = content;
        document.getElementById('edit-ann-category').value  = category;
        document.getElementById('edit-ann-audience').value  = audience;
        document.getElementById('edit-ann-posted-at').value = postedAt;
        document.getElementById('edit-ann-image').value     = '';
        editAnnouncementModal.show();
    }

    async function submitEditAnnouncement() {
        var id = document.getElementById('edit-ann-id').value;
        var title = document.getElementById('edit-ann-title').value.trim();
        var content = document.getElementById('edit-ann-content').value.trim();
        if (!title || !content) { saToast('warning', 'Title and content are required.'); return; }

        var fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('_token', csrfToken);
        fd.append('title', title);
        fd.append('content', content);
        fd.append('category', document.getElementById('edit-ann-category').value);
        fd.append('audience', document.getElementById('edit-ann-audience').value);
        fd.append('posted_at', document.getElementById('edit-ann-posted-at').value);
        var imgFile = document.getElementById('edit-ann-image').files[0];
        if (imgFile) fd.append('image', imgFile);

        setBtnLoading('btnUpdateAnnouncement', true);
        try {
            var r = await fetch('/superadmin/announcements/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });
            if (r.ok || r.redirected) {
                saToast('success', 'Announcement updated!');
                editAnnouncementModal.hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                var d = await r.json().catch(() => null);
                saToast('error', (d && d.message) || (d && d.errors ? Object.values(d.errors).flat().join(', ') : 'Error updating announcement.'));
            }
        } catch(e) { saToast('error', 'Network error. Please try again.'); }
        setBtnLoading('btnUpdateAnnouncement', false, '<i class="bi bi-floppy-fill"></i>Save Changes');
    }

    function openEditNewsModal(id, title, body, category, postedAt) {
        document.getElementById('edit-news-id').value        = id;
        document.getElementById('edit-news-title').value     = title;
        document.getElementById('edit-news-body').value      = body;
        document.getElementById('edit-news-category').value  = category;
        document.getElementById('edit-news-posted-at').value = postedAt;
        document.getElementById('edit-news-image').value     = '';
        editNewsModal.show();
    }

    async function submitEditNews() {
        var id = document.getElementById('edit-news-id').value;
        var title = document.getElementById('edit-news-title').value.trim();
        var body = document.getElementById('edit-news-body').value.trim();
        if (!title || !body) { saToast('warning', 'Headline and body are required.'); return; }

        var fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('_token', csrfToken);
        fd.append('title', title);
        fd.append('body', body);
        fd.append('category', document.getElementById('edit-news-category').value);
        fd.append('posted_at', document.getElementById('edit-news-posted-at').value);
        var imgFile = document.getElementById('edit-news-image').files[0];
        if (imgFile) fd.append('image', imgFile);

        setBtnLoading('btnUpdateNews', true);
        try {
            var r = await fetch('/superadmin/news/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });
            if (r.ok || r.redirected) {
                saToast('success', 'News article updated!');
                editNewsModal.hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                var d = await r.json().catch(() => null);
                saToast('error', (d && d.message) || (d && d.errors ? Object.values(d.errors).flat().join(', ') : 'Error updating news article.'));
            }
        } catch(e) { saToast('error', 'Network error. Please try again.'); }
        setBtnLoading('btnUpdateNews', false, '<i class="bi bi-floppy-fill"></i>Save Changes');
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
    // Logs filters are real GET params now (server-paginated); the search
    // box debounces so it doesn't reload on every keystroke.
    function debounceFormSubmit(el, ms) {
        clearTimeout(el._debounceTimer);
        el._debounceTimer = setTimeout(() => el.form.submit(), ms || 600);
    }

    // Shared pagination-control renderer for the AJAX-driven tables below
    // (Enrollments, Teachers, Subjects, Sections, Schedules) — same visual
    // language (.pagination / .pagination-info) as the server-rendered
    // tables elsewhere on this page, just built client-side since these
    // tables are populated via fetch(), not Blade.
    // Same page-number-window logic as Laravel's own paginator (resources/views/partials/pagination.blade.php)
    // — first/last pages plus a window around the current page, with "…" gaps —
    // so these AJAX-driven tables look and behave exactly like every
    // server-rendered paginated table elsewhere in this app.
    function pageElements(current, last) {
        var delta = 2, range = [], withDots = [], l;
        for (var i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) range.push(i);
        }
        range.forEach(function(i) {
            if (l) {
                if (i - l === 2) withDots.push(l + 1);
                else if (i - l !== 1) withDots.push('...');
            }
            withDots.push(i);
            l = i;
        });
        return withDots;
    }

    function renderPagerControls(containerId, meta, itemLabel, fnName) {
        var el = document.getElementById(containerId);
        if (!el) return;
        if (!meta || !meta.total) { el.innerHTML = ''; return; }
        var html = '';
        html += '<nav><ul class="pagination">';
        html += meta.current_page <= 1
            ? '<li class="page-item disabled" aria-disabled="true" aria-label="Previous"><span class="page-link" aria-hidden="true">Previous</span></li>'
            : '<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault();' + fnName + '(' + (meta.current_page - 1) + ')" rel="prev" aria-label="Previous">Previous</a></li>';

        pageElements(meta.current_page, meta.last_page).forEach(function(p) {
            if (p === '...') {
                html += '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>';
            } else if (p === meta.current_page) {
                html += '<li class="page-item active" aria-current="page"><span class="page-link">' + p + '</span></li>';
            } else {
                html += '<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault();' + fnName + '(' + p + ')">' + p + '</a></li>';
            }
        });

        html += meta.current_page >= meta.last_page
            ? '<li class="page-item disabled" aria-disabled="true" aria-label="Next"><span class="page-link" aria-hidden="true">Next</span></li>'
            : '<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault();' + fnName + '(' + (meta.current_page + 1) + ')" rel="next" aria-label="Next">Next</a></li>';
        html += '</ul></nav>';
        html += '<div class="pagination-info">Showing ' + meta.from + ' to ' + meta.to + ' of ' + meta.total + ' ' + itemLabel + '</div>';
        el.innerHTML = html;
    }

    // ── Export (System Logs) ──────────────────
    function currentLogFilterParams() {
        var params = new URLSearchParams();
        var type = document.getElementById('logTypeFilter').value;
        var date = document.getElementById('logDateFilter').value;
        var search = document.getElementById('logSearchInput').value;
        var roleEl = document.getElementById('logRoleFilter');
        if (type) params.set('log_type', type);
        if (date) params.set('log_date', date);
        if (search) params.set('log_search', search);
        if (roleEl && roleEl.value) params.set('log_role', roleEl.value);
        return params;
    }

    function exportLogsCsv(e) {
        e.preventDefault();
        var params = currentLogFilterParams();
        window.location.href = '{{ route("superadmin.logs.export") }}?' + params.toString();
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
            var r = await fetch('{{ route("superadmin.backup.create") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) {
                backupLog('✓ ' + d.message);
                backupLog(d.secondary ? '✓ Copied to secondary drive' : '— Secondary drive not configured, skipped');
                backupLog(d.drive ? '✓ Uploaded to Google Drive' : '— Google Drive not configured, skipped');
                saToast('success', 'Backup created!', d.filename + (d.secondary || d.drive ? ' (+ off-machine copy)' : ' — consider setting up a secondary/cloud copy, see BACKUP_SETUP.md'));
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

    function deleteBackup(filename) {
        saConfirm({
            title: 'Delete Backup',
            msg: 'Delete backup "' + filename + '"? This cannot be undone.',
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: function() { doDeleteBackup(filename); }
        });
    }

    async function doDeleteBackup(filename) {
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

    function restoreBackup(filename) {
        saConfirm({
            title: 'Restore Database',
            msg: 'RESTORE DATABASE from "' + filename + '"? This will OVERWRITE all current data. This cannot be undone — are you absolutely sure?',
            icon: 'bi-exclamation-triangle-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Restore & Overwrite',
            btnColor: '#dc2626',
            onConfirm: function() { doRestoreBackup(filename); }
        });
    }

    async function doRestoreBackup(filename) {
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

    var _enrPage = 1;

    async function loadEnrollments(page) {
        _loaded['enrollments'] = true;
        _enrPage = page || 1;
        var status = document.getElementById('enr-status-filter')?.value || 'all';
        var grade  = document.getElementById('enr-grade-filter')?.value || 'all';
        var sort   = document.getElementById('enr-sort-filter')?.value || 'newest';
        var search = document.getElementById('enr-search')?.value || '';
        var tbody = document.getElementById('enr-tbody');
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var qs = new URLSearchParams({ status: status, grade: grade, sort: sort, search: search, page: _enrPage });
            var r = await fetch('/superadmin/enrollments-data?' + qs.toString(), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            var d = await r.json();
            _enrData = d.enrollments || [];
            // Update stat badges
            var counts = d.counts || {};
            ['pending','approved','enrolled','declined'].forEach(function(s) {
                var el = document.getElementById('enr-cnt-' + s);
                if (el) el.textContent = counts[s] || 0;
            });
            // Enrollment open/closed badge
            _saEnrollmentOpen = !!d.enrollment_open;
            saRenderEnrollmentWindowBadge();
            renderEnrollmentsTable(_enrData);
            renderPagerControls('enr-pagination', d, 'enrollment(s)', 'goToEnrollmentsPage');
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--red);">Failed to load enrollments.</td></tr>';
        }
    }

    function goToEnrollmentsPage(p) { loadEnrollments(p); }

    function renderEnrollmentsTable(rows) {
        var tbody = document.getElementById('enr-tbody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No enrollments found.</td></tr>';
            return;
        }
        var statusBadge = { pending:'pending', approved:'active', enrolled:'enrolled', declined:'inactive' };
        var statusLabel = { pending:'Pending', approved:'Approved', enrolled:'Enrolled', declined:'Declined' };
        tbody.innerHTML = rows.map(function(e) {
            var name = (e.last_name + ', ' + e.first_name).trim().replace(/^,\s*/, '') || '—';
            var grade = e.grade_level ? e.grade_level.replace('grade', 'Grade ').replace(/^(\w)/, function(m) { return m.toUpperCase(); }) : '—';
            var statusCls = statusBadge[e.status] || 'active';
            var payBadge = e.payment_status === 'paid'
                ? '<span class="status-badge enrolled">Paid</span>'
                : '<span class="status-badge pending">Unpaid</span>';
            var actions = '<button class="action-btn view" title="View Application Details" onclick="saViewEnrollment(' + e.id + ')"><i class="bi bi-eye"></i></button>';
            if (e.status === 'pending') {
                actions += '<button class="action-btn edit" title="Approve" onclick="saApproveEnrollment(' + e.id + ')"><i class="bi bi-check-lg"></i></button>';
                actions += '<button class="action-btn delete" title="Decline" onclick="saDeclineEnrollment(' + e.id + ')"><i class="bi bi-x-lg"></i></button>';
            }
            return '<tr>' +
                '<td style="font-size:12px;font-weight:700;color:var(--blue);">' + (e.reference_number || '—') + '</td>' +
                '<td>' + name + '</td>' +
                '<td>' + grade + '</td>' +
                '<td style="font-size:12px;color:var(--muted);">' + (e.created_at || '—') + '</td>' +
                '<td><span class="status-badge ' + statusCls + '">' + (statusLabel[e.status] || e.status) + '</span></td>' +
                '<td>' + payBadge + '</td>' +
                '<td>' + actions + '</td>' +
                '</tr>';
        }).join('');
    }

    function saApproveEnrollment(enrollmentId) {
        saConfirm({
            title: 'Approve Enrollment',
            msg: 'Approving this enrollment will create a student account and send login credentials to the student\'s email. Continue?',
            icon: 'bi-check-circle-fill',
            gradient: 'linear-gradient(135deg,#14532d,#16a34a)',
            btnLabel: 'Approve',
            btnColor: '#16a34a',
            onConfirm: function() { doSaEnrollAction(enrollmentId, 'approve'); }
        });
    }

    async function doSaEnrollAction(enrollmentId, action, extraBody) {
        try {
            var r = await fetch('/admin/enrollments/' + enrollmentId + '/' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(extraBody || {})
            });
            var d = await r.json();
            if (d.success) {
                saToast('success', action === 'approve' ? 'Enrollment approved!' : 'Enrollment declined.');
                var evm = document.getElementById('enrollmentViewModal');
                var evmInst = evm && bootstrap.Modal.getInstance(evm);
                if (evmInst) evmInst.hide();
                _loaded['enrollments'] = false;
                loadEnrollments();
            } else {
                saToast('error', d.message || 'Action failed.');
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ── Decline reason modal (dynamic, same pattern as other SA modals) ──
    function saDeclineEnrollment(enrollmentId) {
        document.getElementById('saDeclineEnrollModal')?.remove();
        var html = '<div class="modal fade" id="saDeclineEnrollModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered">' +
            '<div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div class="modal-header" style="background:linear-gradient(135deg,#b91c1c,#dc2626);color:#fff;border:none;"><h5 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>Decline Enrollment</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>' +
            '<div class="modal-body p-4">' +
            '<label class="form-lbl">Reason for Declining</label>' +
            '<textarea id="sa-decline-reason" class="form-fld" rows="3" placeholder="Enter reason for declining this enrollment..."></textarea>' +
            '<label class="form-lbl mt-3">Storage Location</label>' +
            '<select id="sa-decline-storage" class="form-fld">' +
            '<option value="pending">Pending Review</option>' +
            '<option value="declined_temporarily">Declined Temporarily</option>' +
            '<option value="declined_permanently">Declined Permanently</option>' +
            '<option value="requires_documents">Requires Additional Documents</option>' +
            '</select>' +
            '</div>' +
            '<div class="modal-footer bg-light"><button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn-dash btn-danger" onclick="doSaDeclineEnrollment(' + enrollmentId + ')"><i class="bi bi-x-lg"></i> Decline Enrollment</button></div>' +
            '</div></div></div>';
        document.body.insertAdjacentHTML('beforeend', html);
        new bootstrap.Modal(document.getElementById('saDeclineEnrollModal')).show();
    }

    function doSaDeclineEnrollment(enrollmentId) {
        var reason  = document.getElementById('sa-decline-reason').value.trim();
        var storage = document.getElementById('sa-decline-storage').value;
        if (!reason) { saToast('error', 'Please provide a reason for declining.'); return; }
        var modalEl = document.getElementById('saDeclineEnrollModal');
        bootstrap.Modal.getInstance(modalEl)?.hide();
        doSaEnrollAction(enrollmentId, 'decline', { reason: reason, storage: storage });
    }

    // ── Enrollment open/closed toggle (reuses /admin/settings/toggle-enrollment) ──
    var _saEnrollmentOpen = true;

    function saRenderEnrollmentWindowBadge() {
        var badge = document.getElementById('sa-enr-window-badge');
        var label = document.getElementById('sa-enr-window-label');
        var btn   = document.getElementById('sa-enr-toggle-btn');
        var btnLabel = document.getElementById('sa-enr-toggle-label');
        if (!badge) return;
        if (_saEnrollmentOpen) {
            badge.style.background = '#e8f8f0'; badge.style.color = '#1a7a44'; badge.style.borderColor = '#27ae60';
            badge.querySelector('span').style.background = '#27ae60';
            label.textContent = 'Enrollment OPEN';
            btn.className = 'btn-dash btn-secondary';
            btn.innerHTML = '<i class="bi bi-lock-fill me-1"></i><span id="sa-enr-toggle-label">Close Enrollment</span>';
        } else {
            badge.style.background = '#fdecea'; badge.style.color = '#c0392b'; badge.style.borderColor = '#e74c3c';
            badge.querySelector('span').style.background = '#e74c3c';
            label.textContent = 'Enrollment CLOSED';
            btn.className = 'btn-dash btn-primary';
            btn.innerHTML = '<i class="bi bi-unlock-fill me-1"></i><span id="sa-enr-toggle-label">Open Enrollment</span>';
        }
    }

    function saToggleEnrollmentWindow() {
        var btn = document.getElementById('sa-enr-toggle-btn');
        btn.disabled = true;
        fetch('/admin/settings/toggle-enrollment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({})
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            btn.disabled = false;
            if (!d.success) { saToast('error', d.message || 'Failed to toggle enrollment.'); return; }
            _saEnrollmentOpen = d.enrollment_open;
            saRenderEnrollmentWindowBadge();
            if (d.enrollment_open && d.unassessed_count > 0) {
                saToast('warning', d.unassessed_count + ' Grade 1–6 student(s) have not been assessed yet.', 'Consider completing assessments first.');
            } else {
                saToast(d.enrollment_open ? 'success' : 'error', d.message);
            }
        })
        .catch(function() { btn.disabled = false; saToast('error', 'Network error.'); });
    }

    // ── Enrollment detail modal (ported, reuses admin.enrollments.view-modal markup) ──
    function evmTab(tab) {
        ['details','documents'].forEach(function(t) {
            var pane = document.getElementById('evm-pane-' + t);
            var btn  = document.getElementById('evm-tab-' + t);
            if (pane) pane.style.display = t === tab ? 'block' : 'none';
            if (btn) {
                btn.style.borderBottomColor = t === tab ? '#1a3a6c' : 'transparent';
                btn.style.color             = t === tab ? '#1a3a6c' : '#64748b';
                btn.style.fontWeight        = t === tab ? '700' : '600';
            }
        });
    }

    function reloadEvmDocs() {
        var loadEl = document.getElementById('evm-docs-loading');
        var contEl = document.getElementById('evm-docs-content');
        if (loadEl) loadEl.style.display = 'block';
        if (contEl) { contEl.style.display = 'none'; contEl.innerHTML = ''; }
    }

    function saFormatGradeLevel(grade) {
        var gradeMap = { nursery:'Nursery', kindergarten:'Kindergarten', grade1:'Grade 1', grade2:'Grade 2', grade3:'Grade 3', grade4:'Grade 4', grade5:'Grade 5', grade6:'Grade 6' };
        return gradeMap[grade] || grade || 'N/A';
    }
    function saFormatStudentType(type) {
        var typeMap = { new:'New Student', transferee:'Transferee', returning:'Returning Student' };
        return typeMap[type] || type || 'N/A';
    }
    function saFormatRelationship(rel) {
        var relMap = { father:'Father', mother:'Mother', guardian:'Guardian', sibling:'Sibling', grandparent:'Grandparent', relative:'Relative' };
        return relMap[rel] || rel || 'N/A';
    }

    function saViewEnrollment(enrollmentId) {
        var modalEl = document.getElementById('enrollmentViewModal');
        evmTab('details');
        fetch('/admin/enrollments/' + enrollmentId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function(data) {
            var sd = data.student_data || {};
            var fmt = function(d) { return d ? new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' }) : 'N/A'; };

            document.getElementById('modal-reference-number').textContent = data.reference_number || 'N/A';
            document.getElementById('modal-school-year').textContent       = data.school_year || 'N/A';

            var typeBadge = document.getElementById('modal-type-badge');
            var typeMap = { returning:{ label:'Re-Enroll', color:'#fbbf24' }, transferee:{ label:'Transferee', color:'#6ee7b7' }, new:{ label:'New Student', color:'#a5f3fc' } };
            var typeInfo = typeMap[sd.student_type] || null;
            if (typeInfo) {
                typeBadge.textContent = typeInfo.label;
                typeBadge.style.background = 'rgba(255,255,255,.15)';
                typeBadge.style.color = typeInfo.color;
                typeBadge.style.borderColor = typeInfo.color;
                typeBadge.style.display = 'inline-block';
            } else { typeBadge.style.display = 'none'; }

            var statusColors = { enrolled:'#16a34a', approved:'#16a34a', pending:'#d97706', declined:'#dc2626', completed:'#6d28d9' };
            var statusBg     = { enrolled:'#dcfce7', approved:'#dcfce7', pending:'#fef3c7', declined:'#fee2e2', completed:'#ede9fe' };
            var sc = statusColors[data.status] || '#6b7280';
            var sb = statusBg[data.status] || '#f3f4f6';
            document.getElementById('modal-status-pill').innerHTML = '<span style="background:' + sb + ';color:' + sc + ';border:1.5px solid ' + sc + ';border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;">' + (data.status||'').replace(/^\w/, function(c){ return c.toUpperCase(); }) + '</span>';

            document.getElementById('modal-student-name').textContent      = [sd.first_name||'', sd.middle_name||'', sd.last_name||''].filter(Boolean).join(' ') || 'N/A';
            document.getElementById('modal-student-email').textContent     = sd.student_email || 'N/A';
            document.getElementById('modal-student-phone').textContent     = sd.guardian_phone || 'N/A';
            document.getElementById('modal-grade-level').textContent       = saFormatGradeLevel(sd.grade_level);
            document.getElementById('modal-student-type').textContent      = saFormatStudentType(sd.student_type);
            document.getElementById('modal-student-birthdate').textContent = sd.birthdate ? new Date(sd.birthdate).toLocaleDateString('en-PH') : 'N/A';

            document.getElementById('modal-guardian-name').textContent       = sd.guardian_name || 'N/A';
            document.getElementById('modal-relationship').textContent        = saFormatRelationship(sd.relationship);
            document.getElementById('modal-guardian-occupation').textContent = sd.guardian_occupation || 'N/A';
            document.getElementById('modal-guardian-email').textContent      = sd.student_email || 'N/A';
            document.getElementById('modal-guardian-phone').textContent      = sd.guardian_phone || 'N/A';

            document.getElementById('modal-street-address').textContent = sd.street_address || 'N/A';
            document.getElementById('modal-barangay').textContent        = sd.barangay || 'N/A';
            document.getElementById('modal-city').textContent            = sd.city || 'N/A';
            document.getElementById('modal-province').textContent        = sd.province || 'N/A';
            document.getElementById('modal-zip-code').textContent        = sd.zip_code || 'N/A';

            document.getElementById('modal-application-date').textContent  = fmt(data.created_at);
            document.getElementById('modal-updated-at').textContent        = fmt(data.updated_at);
            var procMap = { pending:'Under Review', approved:'Approved - Student Account Created', declined:'Declined - See Reason in Notes' };
            document.getElementById('modal-processing-status').textContent = procMap[data.status] || data.status || 'Unknown';

            var secCard = document.getElementById('section-assignment-card');
            if (['enrolled','approved','completed'].includes(data.status)) {
                secCard.style.display = 'block';
                document.getElementById('modal-current-section').textContent     = data.section || 'Not assigned';
                document.getElementById('modal-section-grade-level').textContent = saFormatGradeLevel(sd.grade_level);
            } else { secCard.style.display = 'none'; }

            var actionButtons = document.getElementById('modal-action-buttons');
            if (data.status === 'pending') {
                actionButtons.innerHTML = '<button class="btn btn-success" onclick="saApproveEnrollment(' + data.id + ')"><i class="bi bi-check-circle-fill"></i> Approve</button>' +
                    '<button class="btn btn-danger" onclick="saDeclineEnrollment(' + data.id + ')"><i class="bi bi-x-circle-fill"></i> Decline</button>' +
                    '<button class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i> Close</button>';
            } else {
                actionButtons.innerHTML = '<button class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i> Close</button>';
            }

            if (data.user_id) {
                saFetchDocs(data.user_id);
            }

            new bootstrap.Modal(modalEl).show();
        })
        .catch(function(error) {
            console.error('Error loading enrollment data:', error);
            saToast('error', 'Failed to load enrollment details.');
        });
    }

    // ── Document review (approve/reject uploaded enrollment documents) ──
    var SA_DOC_TYPES = [
        { key: 'birth_certificate',  label: 'Birth Certificate',    icon: 'bi-file-earmark-person-fill' },
        { key: 'form_137',           label: 'Form 137',             icon: 'bi-file-earmark-text-fill'   },
        { key: 'report_card',        label: 'Grades / Report Card', icon: 'bi-file-earmark-check-fill'  },
        { key: 'two_by_two_picture', label: '2x2 ID Picture',       icon: 'bi-person-bounding-box'      },
    ];
    var _saRejectDocId = null;

    function saFetchDocs(userId) {
        var loadEl = document.getElementById('evm-docs-loading');
        var contEl = document.getElementById('evm-docs-content');
        loadEl.style.display = 'block';
        contEl.style.display = 'none';
        contEl.innerHTML = '';
        fetch('/admin/student/' + userId + '/documents-for-assess', { headers: { 'X-CSRF-TOKEN': csrfToken } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loadEl.style.display = 'none';
            contEl.innerHTML = saBuildDocCards(data.documents || [], userId);
            contEl.style.display = 'block';
            var badge = document.getElementById('evm-doc-count');
            if (badge) {
                var uploaded = (data.documents || []).filter(function(d) { return SA_DOC_TYPES.some(function(t) { return t.key === d.document_type; }); });
                badge.textContent = uploaded.length + '/4';
                badge.style.display = 'inline-block';
            }
        })
        .catch(function() { loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load documents.</span>'; });
    }

    function saBuildDocCards(documents, userId) {
        var byType = {};
        documents.forEach(function(d) { if (!byType[d.document_type]) byType[d.document_type] = d; });

        var html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;">';

        SA_DOC_TYPES.forEach(function(type) {
            var doc = byType[type.key] || null;
            var border, bg, iconBg, iconColor, badge, actions = '', viewBtn = '';

            if (!doc) {
                border = '1px dashed #d0d7de'; bg = '#fafafa'; iconBg = '#f0f0f0'; iconColor = '#bbb';
                badge  = '<span style="font-size:11px;font-weight:700;background:#f0f0f0;color:#999;padding:3px 10px;border-radius:20px;">○ Missing</span>';
            } else if (doc.status === 'approved') {
                border = '1px solid #bbf7d0'; bg = '#f0fdf4'; iconBg = '#dcfce7'; iconColor = '#16a34a';
                badge  = '<span style="font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;">✓ Approved</span>';
            } else if (doc.status === 'rejected') {
                border = '1px solid #fecaca'; bg = '#fff5f5'; iconBg = '#fee2e2'; iconColor = '#dc2626';
                badge  = '<span style="font-size:11px;font-weight:700;background:#fee2e2;color:#dc2626;padding:3px 10px;border-radius:20px;">✗ Rejected</span>';
                if (doc.reject_reason) badge += '<div style="font-size:10px;color:#dc2626;margin-top:4px;opacity:0.8;">' + doc.reject_reason + '</div>';
            } else {
                border = '1px solid #bfdbfe'; bg = '#eff6ff'; iconBg = '#dbeafe'; iconColor = '#1d4ed8';
                badge  = '<span style="font-size:11px;font-weight:700;background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;">● Pending Review</span>';
            }

            if (doc && doc.file_path) {
                var _url  = '/storage/' + doc.file_path;
                var _name = (doc.original_name || 'document').replace(/\\/g,'\\\\').replace(/'/g,"\\'");
                var _lbl  = type.label.replace(/'/g,"\\'");
                viewBtn = '<button onclick="saViewDocFile(\'' + _url + '\',\'' + _name + '\',\'' + _lbl + '\')" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#1d4ed8;background:none;border:none;padding:0;cursor:pointer;"><i class="bi bi-eye-fill"></i> View File</button>';
            }

            if (doc && doc.status === 'pending') {
                actions = '<div style="display:flex;gap:6px;">' +
                    '<button onclick="saApproveDocCard(' + doc.id + ',' + userId + ')" style="flex:1;padding:5px 0;background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;"><i class="bi bi-check-lg"></i> Approve</button>' +
                    '<button onclick="saRejectDocCard(' + doc.id + ',' + userId + ')" style="flex:1;padding:5px 0;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;"><i class="bi bi-x-lg"></i> Reject</button>' +
                    '</div>';
            }

            var filename = doc ? doc.original_name : null;
            var dateStr  = (doc && doc.created_at) ? new Date(doc.created_at).toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' }) : null;

            html += '<div style="border:' + border + ';border-radius:12px;padding:16px;background:' + bg + ';display:flex;flex-direction:column;gap:10px;">';
            html += '<div style="display:flex;align-items:center;gap:10px;"><div style="width:42px;height:42px;border-radius:10px;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi ' + type.icon + '" style="font-size:21px;color:' + iconColor + ';"></i></div><div style="font-weight:700;font-size:13px;color:#1a3a6c;line-height:1.3;">' + type.label + '</div></div>';
            html += '<div style="font-size:12px;color:#555;border-top:1px solid rgba(0,0,0,0.06);padding-top:8px;">';
            if (filename) {
                html += '<div style="display:flex;align-items:center;gap:5px;overflow:hidden;"><i class="bi bi-paperclip" style="color:#999;flex-shrink:0;font-size:13px;"></i><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' + filename + '">' + filename + '</span></div>';
                if (dateStr) html += '<div style="font-size:11px;color:#aaa;margin-top:3px;">' + dateStr + '</div>';
            } else {
                html += '<span style="color:#bbb;font-style:italic;">Not yet uploaded</span>';
            }
            html += '</div>';
            html += '<div style="display:flex;flex-direction:column;gap:8px;">' + badge + actions + viewBtn + '</div>';
            html += '</div>';
        });

        html += '</div>';
        return html;
    }

    function saApproveDocCard(docId, userId) {
        fetch('/admin/documents/' + docId + '/approve', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) { saToast('success', 'Document approved.'); saFetchDocs(userId); }
            else { saToast('error', d.message || 'Failed to approve.'); }
        })
        .catch(function() { saToast('error', 'Failed to approve document.'); });
    }

    function saRejectDocCard(docId, userId) {
        _saRejectDocId = docId;
        document.getElementById('sa-reject-doc-reason').value = '';
        var modal = new bootstrap.Modal(document.getElementById('saRejectDocModal'));
        document.getElementById('saRejectDocModal').dataset.userId = userId;
        modal.show();
    }

    function saSubmitRejectDoc() {
        var reason = document.getElementById('sa-reject-doc-reason').value.trim();
        if (!reason) { saToast('error', 'Please enter a rejection reason.'); return; }
        var userId = document.getElementById('saRejectDocModal').dataset.userId;
        fetch('/admin/documents/' + _saRejectDocId + '/reject', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ reject_reason: reason })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            bootstrap.Modal.getInstance(document.getElementById('saRejectDocModal')).hide();
            if (d.success) { saToast('success', 'Document rejected.'); saFetchDocs(userId); }
            else { saToast('error', d.message || 'Failed to reject.'); }
        })
        .catch(function() {
            bootstrap.Modal.getInstance(document.getElementById('saRejectDocModal')).hide();
            saToast('error', 'Failed to reject document.');
        });
    }

    function saViewDocFile(url, name, docTypeLabel) {
        var ext = url.split('?')[0].split('.').pop().toLowerCase();
        var isImg = ['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1;
        var isPdf = ext === 'pdf';

        document.getElementById('sa-docv-title').textContent = (name || 'Document') + (docTypeLabel ? ' — ' + docTypeLabel : '');

        var imgWrap = document.getElementById('sa-docv-img-wrap');
        var pdfWrap = document.getElementById('sa-docv-pdf-wrap');

        if (isImg) {
            document.getElementById('sa-docv-img').src = url;
            imgWrap.style.display = ''; pdfWrap.style.display = 'none';
        } else if (isPdf) {
            document.getElementById('sa-docv-pdf').src = url;
            imgWrap.style.display = 'none'; pdfWrap.style.display = '';
        } else {
            imgWrap.innerHTML = '<div style="padding:60px 20px;text-align:center;"><i class="bi bi-file-earmark" style="font-size:64px;color:#ccc;"></i><p style="margin-top:16px;color:#666;font-size:13px;">Preview not available for this file type.</p></div>';
            imgWrap.style.display = ''; pdfWrap.style.display = 'none';
        }

        document.getElementById('sa-docv-download').href = url;
        document.getElementById('sa-docv-download').download = name || 'document';
        document.getElementById('sa-docv-open').href = url;

        new bootstrap.Modal(document.getElementById('saDocViewerModal')).show();
    }

    // ── Walk-in Enrollment (ported, reuses admin.enrollments.walk-in-modal markup) ──
    async function openWalkInEnrollmentModal() {
        document.getElementById('walkInEnrollmentForm').reset();
        var addrReset = { 'walkin-province': 'Select Province', 'walkin-city': 'Select City/Municipality', 'walkin-barangay': 'Select Barangay' };
        Object.entries(addrReset).forEach(function([id, label]) {
            var el = document.getElementById(id);
            el.innerHTML = '<option value="">' + label + '</option>';
            el.disabled = true;
        });
        document.getElementById('walkin-payment-breakdown').style.display = 'none';

        var reEnrollFlag = document.getElementById('walkin-reenroll');
        if (reEnrollFlag) reEnrollFlag.value = '';

        if (!window._feeTotals) {
            await loadFeeSettings();
        }

        updateWalkinPaymentOptions();

        new bootstrap.Modal(document.getElementById('walkInEnrollmentModal')).show();

        setTimeout(function() { initWalkInFormNavigation(); }, 100);
    }

    function initWalkInFormNavigation() {
        var form = document.getElementById('walkInEnrollmentForm');
        if (!form) return;

        var rows = form.querySelectorAll('.row');
        var navigationGrid = [];

        rows.forEach(function(row) {
            var inputs = Array.from(row.querySelectorAll('input, select')).filter(function(el) { return !el.disabled && el.type !== 'hidden'; });
            if (inputs.length > 0) navigationGrid.push(inputs);
        });

        navigationGrid.forEach(function(rowInputs, rowIndex) {
            rowInputs.forEach(function(input, colIndex) {
                input.removeEventListener('keydown', handleWalkInKeydown);
                input.addEventListener('keydown', handleWalkInKeydown);

                function handleWalkInKeydown(e) {
                    var targetInput = null;

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (colIndex < rowInputs.length - 1) {
                            targetInput = rowInputs[colIndex + 1];
                        } else if (rowIndex < navigationGrid.length - 1) {
                            targetInput = navigationGrid[rowIndex + 1][0];
                        } else {
                            var submitBtn = document.querySelector('#walkInEnrollmentModal .btn-primary');
                            if (submitBtn) { submitBtn.focus(); return; }
                        }
                    } else if (e.key === 'ArrowRight' && !e.shiftKey) {
                        e.preventDefault();
                        if (colIndex < rowInputs.length - 1) targetInput = rowInputs[colIndex + 1];
                    } else if (e.key === 'ArrowLeft' && !e.shiftKey) {
                        e.preventDefault();
                        if (colIndex > 0) targetInput = rowInputs[colIndex - 1];
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (rowIndex < navigationGrid.length - 1) {
                            var nextRow = navigationGrid[rowIndex + 1];
                            targetInput = nextRow[Math.min(colIndex, nextRow.length - 1)];
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (rowIndex > 0) {
                            var prevRow = navigationGrid[rowIndex - 1];
                            targetInput = prevRow[Math.min(colIndex, prevRow.length - 1)];
                        }
                    }

                    if (targetInput) {
                        targetInput.focus();
                        if (targetInput.tagName === 'SELECT') setTimeout(function() { targetInput.click(); }, 50);
                    }
                }
            });
        });
    }

    async function fetchAdminFeeCalculation(gradeLevel, paymentOption) {
        try {
            var response = await fetch('/api/fees/calculate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ grade_level: gradeLevel, payment_option: paymentOption })
            });
            if (!response.ok) throw new Error('Failed to fetch fee calculation: ' + response.status);
            var result = await response.json();
            return result.success ? result.data : null;
        } catch (error) {
            console.error('Fee API error:', error);
            return null;
        }
    }

    function calculateAdminFeeFallback(gradeLevel, paymentOption) {
        var t = window._feeTotals || {};
        var tuition = t.tuition || 0;
        var misc = t.misc || 0;
        var insurance = t.insurance || 0;
        var electric = t.electric || 0;

        var booksMap = {
            'nursery': t.booksNursery || 0, 'kindergarten': t.booksNursery || 0,
            'grade1': t.booksGrade1 || 0, 'grade2': t.booksGrade1 || 0,
            'grade3': t.booksGrade3 || 0, 'grade4': t.booksGrade4 || 0,
            'grade5': t.booksGrade4 || 0, 'grade6': t.booksGrade4 || 0
        };
        var books = booksMap[gradeLevel] || 0;
        var baseTotal = tuition + misc + books + insurance + electric;

        if (baseTotal === 0) return null;

        var rates = { total: baseTotal, tuition: tuition, misc: misc, books: books, insurance: insurance, electric: electric };
        var total = 0, downpayment = 0, monthly = 0, discount = 0;

        var dpBMap = {
            'nursery': parseFloat(document.getElementById('fee-optb-dp-nursery')?.value) || 0,
            'kindergarten': parseFloat(document.getElementById('fee-optb-dp-kinder')?.value) || 0,
            'grade1': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
            'grade2': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
            'grade3': parseFloat(document.getElementById('fee-optb-dp-grade3')?.value) || 0,
            'grade4': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
            'grade5': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
            'grade6': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0
        };
        var dpCMap = {
            'grade1': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
            'grade2': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
            'grade3': parseFloat(document.getElementById('fee-optc-dp-grade3')?.value) || 0,
            'grade4': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
            'grade5': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
            'grade6': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0
        };
        var dpDMap = {
            'nursery': parseFloat(document.getElementById('fee-optd-dp-nursery')?.value) || 0,
            'kindergarten': parseFloat(document.getElementById('fee-optd-dp-kinder')?.value) || 0
        };

        if (paymentOption === 'A') {
            discount = parseFloat(document.getElementById('fee-opta-discount')?.value) || 0;
            total = baseTotal - discount;
            return { components: rates, base_total: baseTotal, discount: discount, downpayment: 0, monthly_payment: 0, months: 0, total_payable: total, option: 'A' };
        } else if (paymentOption === 'B') {
            downpayment = dpBMap[gradeLevel] || 0;
            var tuitionMoB = parseFloat(document.getElementById('fee-optb-monthly-tuition')?.value) || 0;
            var electricMoB = parseFloat(document.getElementById('fee-optb-monthly-electric')?.value) || 0;
            monthly = tuitionMoB + electricMoB;
            total = downpayment + (monthly * 9);
            return { components: rates, base_total: baseTotal, discount: 0, downpayment: downpayment, monthly_payment: parseFloat(monthly.toFixed(2)), months: 9, total_payable: total, option: 'B' };
        } else if (paymentOption === 'C') {
            downpayment = dpCMap[gradeLevel] || 0;
            var tuitionMoC = parseFloat(document.getElementById('fee-optc-monthly-tuition')?.value) || 0;
            var miscMoC = parseFloat(document.getElementById('fee-optc-monthly-misc')?.value) || 0;
            var electricMoC = parseFloat(document.getElementById('fee-optc-monthly-electric')?.value) || 0;
            monthly = tuitionMoC + miscMoC + electricMoC;
            total = downpayment + (monthly * 9);
            return { components: rates, base_total: baseTotal, discount: 0, downpayment: downpayment, monthly_payment: parseFloat(monthly.toFixed(2)), months: 9, total_payable: total, option: 'C' };
        } else if (paymentOption === 'D') {
            downpayment = dpDMap[gradeLevel] || 0;
            var tuitionMoD = parseFloat(document.getElementById('fee-optd-monthly-tuition')?.value) || 0;
            var miscMoD = parseFloat(document.getElementById('fee-optd-monthly-misc')?.value) || 0;
            var electricMoD = parseFloat(document.getElementById('fee-optd-monthly-electric')?.value) || 0;
            monthly = tuitionMoD + miscMoD + electricMoD;
            total = downpayment + (monthly * 9);
            return { components: rates, base_total: baseTotal, discount: 0, downpayment: downpayment, monthly_payment: parseFloat(monthly.toFixed(2)), months: 9, total_payable: total, option: 'D' };
        }
        return null;
    }

    async function updateWalkinPaymentOptions() {
        var gradeLevel = document.getElementById('walkin-grade-level').value;
        if (!gradeLevel) return;

        var results = await Promise.all([
            fetchAdminFeeCalculation(gradeLevel, 'A').catch(function() { return null; }),
            fetchAdminFeeCalculation(gradeLevel, 'B').catch(function() { return null; }),
            fetchAdminFeeCalculation(gradeLevel, 'C').catch(function() { return null; }),
            fetchAdminFeeCalculation(gradeLevel, 'D').catch(function() { return null; })
        ]);
        var optA = results[0], optB = results[1], optC = results[2], optD = results[3];

        var feeA = optA || calculateAdminFeeFallback(gradeLevel, 'A') || {};
        var feeB = optB || calculateAdminFeeFallback(gradeLevel, 'B') || {};
        var feeC = optC || calculateAdminFeeFallback(gradeLevel, 'C') || {};
        var feeD = optD || calculateAdminFeeFallback(gradeLevel, 'D') || {};

        var discountedTotal = feeA.total_payable || ((feeA.base_total || 0) - (feeA.discount || 0));
        document.getElementById('walkin-opt-a-total').textContent = '₱' + discountedTotal.toLocaleString();
        document.getElementById('walkin-opt-b-monthly').textContent = '₱' + (feeB.monthly_payment || 0).toLocaleString() + '/mo';
        document.getElementById('walkin-opt-c-monthly').textContent = '₱' + (feeC.monthly_payment || 0).toLocaleString() + '/mo';
        document.getElementById('walkin-opt-d-monthly').textContent = '₱' + (feeD.monthly_payment || 0).toLocaleString() + '/mo';

        var walkinGrade = (document.getElementById('walkin-grade-level')?.value || '').toLowerCase().replace(/\s/g, '');
        var isWalkinElementary = ['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'].includes(walkinGrade);
        var isWalkinPreElem = ['nursery', 'kindergarten'].includes(walkinGrade);

        document.getElementById('walkin-opt-c-container').style.display = isWalkinElementary ? 'block' : 'none';
        document.getElementById('walkin-opt-d-container').style.display = isWalkinPreElem ? 'block' : 'none';
    }

    function selectWalkinPaymentOption(option) {
        document.querySelectorAll('[id^="walkin-card-opt-"]').forEach(function(card) { card.classList.remove('selected'); });
        document.getElementById('walkin-card-opt-' + option.toLowerCase()).classList.add('selected');
        document.getElementById('walkin-payment-option').value = option;
        updateWalkinPaymentCalculation();
    }

    async function updateWalkinPaymentCalculation() {
        var gradeLevel = document.getElementById('walkin-grade-level').value;
        var paymentOption = document.getElementById('walkin-payment-option').value;
        if (!gradeLevel || !paymentOption) return;

        var feeData = await fetchAdminFeeCalculation(gradeLevel, paymentOption);
        if (!feeData) feeData = calculateAdminFeeFallback(gradeLevel, paymentOption);

        if (!feeData) {
            saToast('error', 'Failed to calculate fees. Please try again.');
            return;
        }

        var c = feeData.components || feeData;
        var breakdownHTML = '';

        if (paymentOption === 'A') {
            var total = feeData.total_payable || ((feeData.base_total || 0) - (feeData.discount || 0));
            breakdownHTML = '<div><strong>Base Total:</strong> ₱' + (feeData.base_total || 0).toLocaleString() + '</div>' +
                '<div><strong>Discount:</strong> -₱' + (feeData.discount || 0).toLocaleString() + '</div>' +
                '<div style="color:var(--green); font-weight:700; margin-top:4px;"><strong>Total Due:</strong> ₱' + total.toLocaleString() + '</div>';
            document.getElementById('walkin-downpayment-amount').value = total;
            document.getElementById('walkin-monthly-amount').value = 0;
        } else {
            breakdownHTML = '<div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">' +
                '<div style="font-weight:600; margin-bottom:4px;">Downpayment Breakdown:</div>' +
                '<div style="display:flex; justify-content:space-between; margin-bottom:2px;"><span>Books:</span><span>₱' + (c.books || 0).toLocaleString() + '</span></div>' +
                '<div style="display:flex; justify-content:space-between; margin-bottom:2px;"><span>Insurance:</span><span>₱' + (c.insurance || 0).toLocaleString() + '</span></div>' +
                '<div style="display:flex; justify-content:space-between; margin-bottom:2px;"><span>Misc/Reg/PTA:</span><span>₱' + (c.misc || 0).toLocaleString() + '</span></div>' +
                '<div style="display:flex; justify-content:space-between; font-weight:700; margin-top:4px;"><span>Total Downpayment:</span><span>₱' + (feeData.downpayment || 0).toLocaleString() + '</span></div>' +
                '</div>' +
                '<div><strong>Monthly:</strong> ₱' + (feeData.monthly_payment || 0).toLocaleString() + ' (' + (feeData.months || 9) + ' months, July-March)</div>' +
                '<div style="color:var(--green); font-weight:700; margin-top:4px;"><strong>Total:</strong> ₱' + (feeData.total_payable || 0).toLocaleString() + '</div>';
            document.getElementById('walkin-downpayment-amount').value = feeData.downpayment || 0;
            document.getElementById('walkin-monthly-amount').value = feeData.monthly_payment || 0;
        }

        document.getElementById('walkin-total-amount').value = feeData.total_payable || 0;
        document.getElementById('walkin-breakdown-content').innerHTML = breakdownHTML;
        document.getElementById('walkin-payment-breakdown').style.display = 'block';
    }

    function validateWalkinGmail(input) {
        var hint = document.getElementById('walkin-email-hint');
        var val  = input.value.trim();

        var walkinTypos = {
            'gmial.com':'gmail.com','gmal.com':'gmail.com','gamil.com':'gmail.com',
            'gnail.com':'gmail.com','gmaill.com':'gmail.com','gmail.co':'gmail.com',
            'gmai.com':'gmail.com','gmali.com':'gmail.com',
        };
        var rfcRe = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

        if (!val) {
            input.classList.remove('is-invalid', 'is-valid');
            if (hint) { hint.textContent = 'Must be a Gmail address — credentials will be sent here after approval.'; hint.style.color = ''; }
            return;
        }

        if (!rfcRe.test(val)) {
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) { hint.textContent = 'Invalid email format.'; hint.style.color = '#dc3545'; }
            return;
        }

        var domain = val.split('@')[1].toLowerCase();

        if (walkinTypos[domain]) {
            var fix = val.split('@')[0] + '@gmail.com';
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) {
                hint.innerHTML = 'Did you mean <strong>' + fix + '</strong>? ' +
                    '<a href="#" style="color:#d97706;font-weight:700;" onclick="document.getElementById(\'walkin-guardian-email\').value=\'' +
                    fix + '\';validateWalkinGmail(document.getElementById(\'walkin-guardian-email\'));return false;">Use this</a>';
                hint.style.color = '#d97706';
            }
            return;
        }

        if (domain !== 'gmail.com') {
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) { hint.textContent = 'Only Gmail addresses accepted (e.g. yourname@gmail.com).'; hint.style.color = '#dc3545'; }
            return;
        }

        input.classList.remove('is-invalid'); input.classList.add('is-valid');
        if (hint) { hint.textContent = '✓ Valid Gmail address'; hint.style.color = '#28a745'; }
    }

    async function loadFeeSettings() {
        if (window._feeTotals) return;
        var getVal = function(id) { return parseFloat(document.getElementById(id)?.value) || 0; };
        window._feeTotals = {
            tuition: getVal('fee-tuition'), misc: getVal('fee-misc'), insurance: getVal('fee-insurance'),
            electric: getVal('fee-electric'), booksNursery: getVal('fee-books-nursery'),
            booksKindergarten: getVal('fee-books-kindergarten'), booksGrade1: getVal('fee-books-grade1'),
            booksGrade3: getVal('fee-books-grade3'), booksGrade4: getVal('fee-books-grade4')
        };
    }

    function submitWalkInEnrollment(confirmed) {
        if (!confirmed) {
            saConfirm({
                title: 'Submit Enrollment',
                msg: 'Are you sure you want to submit this enrollment?',
                icon: 'bi-person-plus-fill',
                gradient: 'linear-gradient(135deg,#27ae60,#1e8449)',
                btnLabel: 'Submit',
                btnColor: '#16a34a',
                onConfirm: function() { submitWalkInEnrollment(true); }
            });
            return;
        }

        var emailInput = document.getElementById('walkin-guardian-email');
        if (!emailInput.value || !/^[^\s@]+@gmail\.com$/i.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            emailInput.focus();
            var hint = document.getElementById('walkin-email-hint');
            if (hint) { hint.textContent = 'Only Gmail addresses accepted (@gmail.com)'; hint.style.color = '#dc3545'; }
            saToast('error', 'Please enter a valid Gmail address for the guardian email.');
            return;
        }

        var phoneInput = document.getElementById('walkin-guardian-phone');
        if (!phoneInput.value || !/^9[0-9]{9}$/.test(phoneInput.value)) {
            phoneInput.classList.add('is-invalid');
            phoneInput.focus();
            saToast('error', 'Please enter a valid 10-digit phone number starting with 9 (after +63).');
            return;
        }

        var formData = {
            first_name: document.getElementById('walkin-first-name').value,
            last_name: document.getElementById('walkin-last-name').value,
            middle_name: document.getElementById('walkin-middle-name').value,
            suffix: document.getElementById('walkin-suffix').value,
            gender: document.getElementById('walkin-gender').value,
            birthdate: document.getElementById('walkin-birthdate').value,
            place_of_birth: document.getElementById('walkin-place-of-birth').value,
            nationality: document.getElementById('walkin-nationality').value,
            grade_level: document.getElementById('walkin-grade-level').value,
            student_type: document.getElementById('walkin-student-type').value,
            last_school: document.getElementById('walkin-last-school').value,
            region: document.getElementById('walkin-region').value,
            province: document.getElementById('walkin-province').value,
            city: document.getElementById('walkin-city').value,
            barangay: document.getElementById('walkin-barangay').value,
            street_address: document.getElementById('walkin-street-address').value,
            zip_code: document.getElementById('walkin-zip-code').value,
            mother_name: document.getElementById('walkin-mother-name').value,
            mother_age: document.getElementById('walkin-mother-age').value,
            father_name: document.getElementById('walkin-father-name').value,
            father_age: document.getElementById('walkin-father-age').value,
            religious_affiliation: document.getElementById('walkin-religious-affiliation').value,
            guardian_name: document.getElementById('walkin-guardian-name').value,
            relationship: document.getElementById('walkin-relationship').value,
            guardian_occupation: document.getElementById('walkin-guardian-occupation').value,
            guardian_phone: '+63' + document.getElementById('walkin-guardian-phone').value,
            guardian_email: document.getElementById('walkin-guardian-email').value,
            blood_type: document.getElementById('walkin-blood-type').value,
            allergies: document.getElementById('walkin-allergies').value,
            medical_conditions: document.getElementById('walkin-medical-conditions').value,
            payment_option: document.getElementById('walkin-payment-option').value,
            downpayment_amount: document.getElementById('walkin-downpayment-amount').value,
            monthly_amount: document.getElementById('walkin-monthly-amount').value,
            total_amount: document.getElementById('walkin-total-amount').value,
            reenroll_student_id: document.getElementById('walkin-reenroll').value
        };

        var required = ['first_name', 'last_name', 'gender', 'birthdate', 'place_of_birth', 'grade_level', 'student_type', 'region', 'province', 'city', 'barangay', 'street_address', 'mother_name', 'mother_age', 'father_name', 'father_age', 'religious_affiliation', 'guardian_name', 'relationship', 'guardian_phone', 'guardian_email', 'payment_option'];
        for (var i = 0; i < required.length; i++) {
            if (!formData[required[i]]) {
                saToast('error', 'Please fill in all required fields.');
                return;
            }
        }

        var btn = document.querySelector('#walkInEnrollmentModal .btn-primary');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Submitting...';

        fetch('/admin/enrollments/walk-in', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(function(r) { if (!r.ok) throw r; return r.json(); })
        .then(function(d) {
            bootstrap.Modal.getInstance(document.getElementById('walkInEnrollmentModal')).hide();
            var isReEnroll = document.getElementById('walkin-reenroll') && document.getElementById('walkin-reenroll').value;
            var msg = isReEnroll
                ? 'Re-enrollment submitted successfully. Reference number: ' + d.reference_number
                : 'Walk-in enrollment submitted. Reference number: ' + d.reference_number;
            saToast('success', msg);
            _loaded['enrollments'] = false;
            loadEnrollments();
        })
        .catch(async function(err) {
            var msg = 'Failed to submit enrollment.';
            try { var e = await err.json(); msg = e.message || e.error || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}
            saToast('error', msg);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Submit Enrollment';
        });
    }

    // ══════════════════════════════════════════════════════════
    // TEACHERS PANEL
    // ══════════════════════════════════════════════════════════
    var _teachersData = [];

    var _teachersPage = 1;

    async function loadTeachers(page) {
        _loaded['teachers'] = true;
        _teachersPage = page || 1;
        var tbody = document.getElementById('teachers-tbody');
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var r = await fetch('/admin/teachers?paginated=1&page=' + _teachersPage, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            _teachersData = d.teachers || [];
            renderTeachersTable(_teachersData);
            renderPagerControls('teachers-pagination', d, 'teacher(s)', 'goToTeachersPage');
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:30px;color:var(--red);">Failed to load teachers.</td></tr>';
        }
    }

    function goToTeachersPage(p) { loadTeachers(p); }

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
                '<button class="action-btn edit" title="Edit" onclick="openEditTeacherModal(' + t.id + ')"><i class="bi bi-pencil-fill"></i></button>' +
                '<button class="action-btn lock" title="Reset Password" onclick="saResetTeacherPwd(' + t.id + ',\'' + (t.name || '').replace(/'/g, "\\'") + '\')"><i class="bi bi-key-fill"></i></button>' +
                '<button class="action-btn ' + (isActive ? 'delete' : 'view') + '" title="' + (isActive ? 'Deactivate' : 'Activate') + '" onclick="saToggleTeacher(' + t.id + ',' + (isActive ? 'true' : 'false') + ')"><i class="bi bi-' + (isActive ? 'person-dash-fill' : 'person-check-fill') + '"></i></button>' +
                '<button class="action-btn delete" title="Delete" onclick="saDeleteTeacher(' + t.id + ',\'' + (t.name || '').replace(/'/g, "\\'") + '\')"><i class="bi bi-trash3-fill"></i></button>' +
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

    function saToggleTeacher(userId, isActive) {
        saConfirm({
            title: isActive ? 'Deactivate Teacher' : 'Activate Teacher',
            msg: (isActive ? 'Deactivate' : 'Activate') + ' this teacher account?',
            icon: isActive ? 'bi-person-x-fill' : 'bi-person-check-fill',
            gradient: isActive ? 'linear-gradient(135deg,#b91c1c,#dc2626)' : 'linear-gradient(135deg,#14532d,#16a34a)',
            btnLabel: isActive ? 'Deactivate' : 'Activate',
            btnColor: isActive ? '#dc2626' : '#16a34a',
            onConfirm: function() { doSaToggleTeacher(userId, isActive); }
        });
    }

    async function doSaToggleTeacher(userId, isActive) {
        try {
            var r = await fetch('/superadmin/users/' + userId + '/toggle-status', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success) { saToast('success', isActive ? 'Teacher deactivated.' : 'Teacher activated.'); _loaded['teachers'] = false; loadTeachers(); }
            else saToast('error', d.message || 'Error.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // Styled replacement for native prompt() — single text input modal, no
    // pre-existing HTML required. onSubmit receives the entered value (never
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
        // title/message/placeholder/confirmLabel are set via
        // textContent/properties below, not interpolated into this HTML
        // string — all can carry server/user-controlled text (e.g. a
        // teacher's own name).
        box.innerHTML =
            '<div style="width:52px;height:52px;border-radius:50%;background:#fff8ec;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">' +
                '<i class="bi bi-pencil-fill" style="font-size:22px;color:#b45309;"></i>' +
            '</div>' +
            '<div id="ilc-prompt-title" style="font-size:15px;font-weight:700;color:#1a3a6c;margin-bottom:6px;"></div>' +
            '<div id="ilc-prompt-message" style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:14px;"></div>' +
            '<input type="' + inputType + '" id="ilc-prompt-input" style="width:100%;padding:10px 12px;border-radius:9px;border:1.5px solid #e2e8f0;font-size:13px;margin-bottom:18px;font-family:inherit;box-sizing:border-box;">' +
            '<div style="display:flex;gap:10px;">' +
                '<button type="button" id="ilc-prompt-cancel" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">Cancel</button>' +
                '<button type="button" id="ilc-prompt-ok" style="flex:1;padding:10px;border-radius:9px;border:none;background:#1a3a6c;color:#fff;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;"></button>' +
            '</div>';
        box.querySelector('#ilc-prompt-title').textContent = title;
        box.querySelector('#ilc-prompt-message').textContent = message;
        box.querySelector('#ilc-prompt-ok').textContent = confirmLabel;

        overlay.appendChild(box);
        document.body.appendChild(overlay);

        var input = box.querySelector('#ilc-prompt-input');
        input.placeholder = placeholder;
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

    function saResetTeacherPwd(userId, userName) {
        showPrompt('Enter a new password for ' + userName + ' (min. 8 characters).', function (pwd) {
            doSaResetTeacherPwd(userId, userName, pwd);
        }, { title: 'Reset Password', placeholder: 'New password', inputType: 'password', confirmLabel: 'Reset Password' });
    }

    async function doSaResetTeacherPwd(userId, userName, pwd) {
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
    // Dedicated Add/Edit Teacher modal — was reusing the generic "create
    // user" form (role preset to teacher) which posts to
    // /superadmin/users (SuperAdminController::storeUser), a different
    // path than Admin's own Add Teacher flow. That meant a teacher created
    // here needed a manually-typed password and got no welcome email,
    // unlike TeacherController::store()'s auto-generated password + emailed
    // credentials (App\Mail\TeacherAccountCreated). Posting to the same
    // /admin/teachers endpoint Admin already uses fixes both — same
    // validation, same auto-password+email behavior, no new backend needed
    // (AdminMiddleware already allows superadmin on every /admin/* route).
    var _emailRfc = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    var _teacherCommonTypos = {
        'gmial.com':'gmail.com','gmal.com':'gmail.com','gmail.co':'gmail.com',
        'gamil.com':'gmail.com','gmaill.com':'gmail.com','gnail.com':'gmail.com',
        'yahooo.com':'yahoo.com','yaho.com':'yahoo.com','yahoomail.com':'yahoo.com',
        'hotmial.com':'hotmail.com','hotmal.com':'hotmail.com','outlok.com':'outlook.com',
    };
    var _teacherEmailDebounce = null;

    function _teacherModalHtml() {
        return '<div class="modal fade" id="teacherModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,#6a1b9a,#7b1fa2);padding:20px 24px;"><div style="display:flex;align-items:center;gap:12px;"><div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;"><i class="bi bi-person-badge-fill" style="color:#fff;font-size:18px;"></i></div><div style="font-size:16px;font-weight:800;color:#fff;" id="teacherModalTitle">Add Teacher</div></div><button type="button" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button></div>' +
            '<div style="padding:20px 24px;background:#fff;">' +
            '<input type="hidden" id="teacher-id">' +
            '<div class="mb-3"><label class="form-lbl">Full Name *</label><input type="text" id="teacher-name" class="form-fld" placeholder="e.g. Juan Dela Cruz"></div>' +
            '<div class="mb-3"><label class="form-lbl">Email Address *</label><div style="position:relative;"><input type="email" id="teacher-email" class="form-fld" placeholder="e.g. teacher@gmail.com" oninput="validateTeacherEmailField(this)" style="padding-right:38px;"><span id="teacher-email-icon" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:16px;display:none;"></span></div><div id="teacher-email-msg" style="font-size:11px;margin-top:5px;display:none;"></div></div>' +
            '<div class="mb-3" id="teacher-autogen-info" style="display:none;"><div style="background:#e8f8f0;border:1px solid #b8e6c8;border-radius:8px;padding:14px 16px;display:flex;align-items:center;gap:10px;"><i class="bi bi-shield-lock-fill" style="font-size:20px;color:#28a745;"></i><div><div style="font-weight:700;font-size:13px;color:#155724;">Password Auto-Generated</div><div style="font-size:12px;color:#3c763d;">A secure password will be generated and sent to the teacher\'s email automatically.</div></div></div></div>' +
            '<div class="mb-3" id="teacher-password-group" style="display:none;"><label class="form-lbl">New Password</label><input type="password" id="teacher-password" class="form-fld" placeholder="Leave blank to keep current"><small style="color:#64748b;font-size:11px;">Leave blank to keep current password.</small></div>' +
            '<div><label class="form-lbl">Status</label><select id="teacher-active" class="form-fld"><option value="1">Active</option><option value="0">Inactive</option></select></div>' +
            '</div><div style="padding:12px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">' +
            '<button type="button" data-bs-dismiss="modal" style="padding:9px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>' +
            '<button type="button" onclick="saveTeacher()" style="padding:9px 20px;background:linear-gradient(135deg,#6a1b9a,#7b1fa2);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;"><i class="bi bi-floppy-fill me-1"></i>Save Teacher</button>' +
            '</div></div></div></div>';
    }

    function _resetTeacherEmailUI() {
        var emailEl = document.getElementById('teacher-email');
        var icon = document.getElementById('teacher-email-icon');
        var msg = document.getElementById('teacher-email-msg');
        if (emailEl) emailEl.style.borderColor = '';
        if (icon) { icon.style.display = 'none'; icon.innerHTML = ''; }
        if (msg) { msg.style.display = 'none'; msg.innerHTML = ''; }
    }

    function setTeacherEmailState(input, state, message) {
        var icon = document.getElementById('teacher-email-icon');
        var msg = document.getElementById('teacher-email-msg');
        var map = {
            ok:    { color:'#16a34a', border:'#86efac', ico:'<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>' },
            warn:  { color:'#d97706', border:'#fcd34d', ico:'<i class="bi bi-exclamation-triangle-fill" style="color:#d97706;"></i>' },
            error: { color:'#dc2626', border:'#fca5a5', ico:'<i class="bi bi-x-circle-fill" style="color:#dc2626;"></i>' },
        };
        var s = map[state] || map.error;
        icon.style.display = ''; icon.innerHTML = s.ico;
        msg.style.display = ''; msg.innerHTML = '<span style="color:' + s.color + ';">' + message + '</span>';
        input.style.borderColor = s.border;
    }

    function validateTeacherEmailField(input) {
        clearTimeout(_teacherEmailDebounce);
        var icon = document.getElementById('teacher-email-icon');
        var msg = document.getElementById('teacher-email-msg');
        var val = input.value.trim();
        if (!val) { icon.style.display = 'none'; msg.style.display = 'none'; input.style.borderColor = ''; return; }
        icon.style.display = '';
        icon.innerHTML = '<i class="bi bi-arrow-repeat" style="color:#999;animation:spin 1s linear infinite;"></i>';
        msg.style.display = 'none';
        _teacherEmailDebounce = setTimeout(function() {
            if (!_emailRfc.test(val)) { setTeacherEmailState(input, 'error', 'Invalid email format. Please enter a valid email address.'); return; }
            var domain = val.split('@')[1].toLowerCase();
            if (_teacherCommonTypos[domain]) {
                var suggested = val.split('@')[0] + '@' + _teacherCommonTypos[domain];
                setTeacherEmailState(input, 'warn', 'Did you mean <strong>' + suggested + '</strong>? '
                    + '<a href="#" onclick="document.getElementById(\'teacher-email\').value=\'' + suggested + '\';validateTeacherEmailField(document.getElementById(\'teacher-email\'));return false;" style="color:#d97706;font-weight:700;">Use this</a>');
                return;
            }
            var blocked = ['mailinator.com','guerrillamail.com','tempmail.com','throwam.com','yopmail.com','maildrop.cc'];
            if (blocked.includes(domain)) { setTeacherEmailState(input, 'error', 'Disposable email addresses are not allowed.'); return; }
            setTeacherEmailState(input, 'ok', 'Email format looks good.');
        }, 600);
    }

    function openAddTeacherModal() {
        var existing = document.getElementById('teacherModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _teacherModalHtml());
        document.getElementById('teacherModalTitle').textContent = 'Add Teacher';
        document.getElementById('teacher-id').value = '';
        document.getElementById('teacher-name').value = '';
        document.getElementById('teacher-email').value = '';
        document.getElementById('teacher-password').value = '';
        document.getElementById('teacher-autogen-info').style.display = 'block';
        document.getElementById('teacher-password-group').style.display = 'none';
        document.getElementById('teacher-active').value = '1';
        _resetTeacherEmailUI();
        new bootstrap.Modal(document.getElementById('teacherModal')).show();
    }

    function openEditTeacherModal(teacherId) {
        var t = _teachersData.find(function(x) { return x.id == teacherId; });
        if (!t) return;
        var existing = document.getElementById('teacherModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _teacherModalHtml());
        document.getElementById('teacherModalTitle').textContent = 'Edit Teacher';
        document.getElementById('teacher-id').value = t.id;
        document.getElementById('teacher-name').value = t.name || '';
        document.getElementById('teacher-email').value = t.email || '';
        document.getElementById('teacher-password').value = '';
        document.getElementById('teacher-autogen-info').style.display = 'none';
        document.getElementById('teacher-password-group').style.display = 'block';
        document.getElementById('teacher-active').value = (t.is_active || t.is_active === 1) ? '1' : '0';
        _resetTeacherEmailUI();
        new bootstrap.Modal(document.getElementById('teacherModal')).show();
    }

    async function saveTeacher() {
        var id     = document.getElementById('teacher-id').value;
        var name   = document.getElementById('teacher-name').value.trim();
        var email  = document.getElementById('teacher-email').value.trim();
        var active = document.getElementById('teacher-active').value === '1';
        if (!name || name.length < 2) { saToast('warning', "Please enter the teacher's full name."); return; }
        if (!email || !_emailRfc.test(email)) { saToast('warning', 'Please enter a valid email address.'); return; }

        var body = { name: name, email: email, is_active: active };
        if (id) {
            var pw = document.getElementById('teacher-password').value;
            if (pw) body.password = pw;
        }

        var method = id ? 'PUT' : 'POST';
        var url    = '/admin/teachers' + (id ? '/' + id : '');
        try {
            var r = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });
            var d = await r.json();
            if (r.ok) {
                saToast('success', id ? 'Teacher updated!' : 'Teacher created! Login credentials sent to their email.');
                bootstrap.Modal.getInstance(document.getElementById('teacherModal'))?.hide();
                _loaded['teachers'] = false; loadTeachers();
            } else {
                var msg = d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'Error saving teacher.');
                saToast('error', msg);
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    function saDeleteTeacher(teacherId, teacherName) {
        saConfirm({
            title: 'Delete Teacher',
            msg: 'Delete teacher "' + teacherName + '"? This cannot be undone.',
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: function() { doSaDeleteTeacher(teacherId); }
        });
    }

    async function doSaDeleteTeacher(teacherId) {
        try {
            var r = await fetch('/admin/teachers/' + teacherId, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (r.ok) { saToast('success', 'Teacher deleted.'); _loaded['teachers'] = false; loadTeachers(); }
            else saToast('error', d.message || 'Error deleting teacher.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ══════════════════════════════════════════════════════════
    // SUBJECTS PANEL
    // ══════════════════════════════════════════════════════════
    var _subjectsData = [];
    var _editSubjectId = null;

    var _subjectsPage = 1;

    async function loadSubjects(page) {
        _loaded['subjects'] = true;
        _subjectsPage = page || 1;
        var grade = document.getElementById('subj-grade-filter')?.value || '';
        var tbody = document.getElementById('subjects-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var url = '/admin/subjects?paginated=1&page=' + _subjectsPage + (grade ? '&grade_level=' + grade : '');
            var r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            _subjectsData = d.subjects || [];
            renderSubjectsTable(_subjectsData);
            renderPagerControls('subjects-pagination', d, 'subject(s)', 'goToSubjectsPage');
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red);">Failed to load subjects.</td></tr>';
        }
    }

    function goToSubjectsPage(p) { loadSubjects(p); }

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
            '<div class="mb-3"><label class="form-lbl">Description</label><textarea id="subj-description" class="form-fld" rows="2" placeholder="Optional notes about this subject"></textarea></div>' +
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
        document.getElementById('subj-description').value = '';
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
        document.getElementById('subj-description').value = s.description || '';
        document.getElementById('subj-active').checked = !!(s.is_active || s.is_active === 1);
    }

    async function saveSubject() {
        var name        = document.getElementById('subj-name').value.trim();
        var code        = document.getElementById('subj-code').value.trim();
        var grade       = document.getElementById('subj-grade').value;
        var description = document.getElementById('subj-description').value.trim();
        var active      = document.getElementById('subj-active').checked;
        if (!name || !code || !grade) { saToast('warning', 'All fields are required.'); return; }

        var method = _editSubjectId ? 'PUT' : 'POST';
        var url    = '/admin/subjects' + (_editSubjectId ? '/' + _editSubjectId : '');
        try {
            var r = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name: name, code: code, grade_level: grade, description: description, is_active: active })
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

    function saDeleteSubject(subjectId, subjectName) {
        saConfirm({
            title: 'Delete Subject',
            msg: 'Delete subject "' + subjectName + '"? This cannot be undone.',
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: function() { doSaDeleteSubject(subjectId); }
        });
    }

    async function doSaDeleteSubject(subjectId) {
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
    var _sectionsPage = 1;
    var _sectionsData = [];

    async function loadSectionsMgmt(page) {
        _loaded['sections-mgmt'] = true;
        _sectionsPage = page || 1;
        var grade = document.getElementById('sec-grade-filter')?.value || '';
        var tbody = document.getElementById('sections-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
        try {
            var url = '/admin/sections?paginated=1&page=' + _sectionsPage + (grade ? '&grade_level=' + grade : '');
            var r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            var rows = d.sections || [];
            _sectionsData = rows;
            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-diagram-3" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>No sections found.</td></tr>';
                document.getElementById('sections-pagination').innerHTML = '';
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
                    '<td>' +
                    '<button class="action-btn edit" title="Edit" onclick="editSaSection(' + s.id + ')"><i class="bi bi-pencil-fill"></i></button>' +
                    '<button class="action-btn delete" title="Delete" onclick="saDeleteSection(' + s.id + ',\'' + (s.name || '').replace(/'/g, "\\'") + '\')"><i class="bi bi-trash3-fill"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');
            renderPagerControls('sections-pagination', d, 'section(s)', 'goToSectionsPage');
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red);">Failed to load sections.</td></tr>';
        }
    }

    function goToSectionsPage(p) { loadSectionsMgmt(p); }

    // Add/Edit Section — ported from Admin's own sectionModal
    // (adminDashboard.blade.php openSectionModal/editSection/
    // saveSectionRecord) onto the same /admin/sections endpoints
    // (SectionController), which already accept a superadmin session.
    // Deliberately NOT porting Admin's "auto-create standard subjects for
    // this grade" convenience step on new-section creation — that depends
    // on a large shared lookup table (adminDashboard.blade.php's
    // `standardSubjects`) and is a nice-to-have, not core parity; can be
    // added later if wanted.
    function _sectionSchoolYearOptions() {
        var cSY = {!! json_encode((int) substr($currentSchoolYear ?? '', 0, 4)) !!} || (new Date().getMonth() >= 5 ? new Date().getFullYear() : new Date().getFullYear() - 1);
        var html = '';
        for (var y = cSY + 4; y >= 2020; y--) {
            var sy = y + '-' + (y + 1);
            html += '<option value="' + sy + '"' + (y === cSY ? ' selected' : '') + '>' + sy + '</option>';
        }
        return html;
    }

    function _sectionModalHtml() {
        return '<div class="modal fade" id="saSectionModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered" style="max-width:560px;"><div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,#1b7a3e,#2e7d32);padding:20px 24px;"><div style="display:flex;align-items:center;gap:12px;"><div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;"><i class="bi bi-diagram-3-fill" style="color:#fff;font-size:18px;"></i></div><div style="font-size:16px;font-weight:800;color:#fff;" id="saSectionModalTitle">Add Section</div></div><button type="button" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button></div>' +
            '<div style="padding:20px 24px;background:#fff;">' +
            '<input type="hidden" id="sa-sec-id">' +
            '<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-lbl">Section Name *</label><input type="text" id="sa-sec-name" class="form-fld" placeholder="e.g. Section A"></div>' +
            '<div class="col-md-6"><label class="form-lbl">Grade Level *</label><select id="sa-sec-grade" class="form-fld"><option value="">— Select grade —</option><option value="nursery">Nursery</option><option value="kindergarten">Kindergarten</option><option value="grade1">Grade 1</option><option value="grade2">Grade 2</option><option value="grade3">Grade 3</option><option value="grade4">Grade 4</option><option value="grade5">Grade 5</option><option value="grade6">Grade 6</option></select></div></div>' +
            '<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-lbl">Room Number</label><input type="text" id="sa-sec-room" class="form-fld" placeholder="e.g. Room 101"></div>' +
            '<div class="col-md-6"><label class="form-lbl">Max Students *</label><input type="number" id="sa-sec-max" class="form-fld" min="1" max="200" value="30"></div></div>' +
            '<div class="row g-3"><div class="col-md-6"><label class="form-lbl">School Year *</label><select id="sa-sec-sy" class="form-fld">' + _sectionSchoolYearOptions() + '</select></div>' +
            '<div class="col-md-6"><label class="form-lbl">Status</label><select id="sa-sec-active" class="form-fld"><option value="1">Active</option><option value="0">Inactive</option></select></div></div>' +
            '</div><div style="padding:12px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">' +
            '<button type="button" data-bs-dismiss="modal" style="padding:9px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>' +
            '<button type="button" onclick="saveSaSection()" style="padding:9px 20px;background:linear-gradient(135deg,#1b7a3e,#2e7d32);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;"><i class="bi bi-floppy-fill me-1"></i>Save Section</button>' +
            '</div></div></div></div>';
    }

    function openSectionModal() {
        var existing = document.getElementById('saSectionModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _sectionModalHtml());
        document.getElementById('saSectionModalTitle').textContent = 'Add Section';
        document.getElementById('sa-sec-id').value = '';
        document.getElementById('sa-sec-name').value = '';
        document.getElementById('sa-sec-grade').value = '';
        document.getElementById('sa-sec-room').value = '';
        document.getElementById('sa-sec-max').value = '30';
        document.getElementById('sa-sec-active').value = '1';
        new bootstrap.Modal(document.getElementById('saSectionModal')).show();
    }

    function editSaSection(sectionId) {
        var s = _sectionsData.find(function(x) { return x.id == sectionId; });
        if (!s) return;
        var existing = document.getElementById('saSectionModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _sectionModalHtml());
        document.getElementById('saSectionModalTitle').textContent = 'Edit Section';
        document.getElementById('sa-sec-id').value = s.id;
        document.getElementById('sa-sec-name').value = s.name || '';
        document.getElementById('sa-sec-grade').value = s.grade_level || '';
        document.getElementById('sa-sec-room').value = s.room_number || '';
        document.getElementById('sa-sec-max').value = s.max_students || 30;
        if (s.school_year) document.getElementById('sa-sec-sy').value = s.school_year;
        document.getElementById('sa-sec-active').value = (s.is_active || s.is_active === 1) ? '1' : '0';
        new bootstrap.Modal(document.getElementById('saSectionModal')).show();
    }

    async function saveSaSection() {
        var id = document.getElementById('sa-sec-id').value;
        var name = document.getElementById('sa-sec-name').value.trim();
        var grade = document.getElementById('sa-sec-grade').value;
        if (!name || !grade) { saToast('warning', 'Section name and grade level are required.'); return; }

        var body = {
            name: name,
            grade_level: grade,
            room_number: document.getElementById('sa-sec-room').value,
            school_year: document.getElementById('sa-sec-sy').value,
            max_students: parseInt(document.getElementById('sa-sec-max').value) || 30,
            is_active: document.getElementById('sa-sec-active').value === '1',
        };

        var method = id ? 'PUT' : 'POST';
        var url = '/admin/sections' + (id ? '/' + id : '');
        try {
            var r = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });
            var d = await r.json();
            if (r.ok) {
                saToast('success', id ? 'Section updated!' : 'Section created!');
                bootstrap.Modal.getInstance(document.getElementById('saSectionModal'))?.hide();
                _loaded['sections-mgmt'] = false; loadSectionsMgmt();
            } else {
                var msg = d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'Error saving section.');
                saToast('error', msg);
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    function saDeleteSection(sectionId, sectionName) {
        saConfirm({
            title: 'Delete Section',
            msg: 'Delete section "' + sectionName + '"? This will also remove all associated schedules and student enrollments.',
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: function() { doSaDeleteSection(sectionId); }
        });
    }

    async function doSaDeleteSection(sectionId) {
        try {
            var r = await fetch('/admin/sections/' + sectionId, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (r.ok) { saToast('success', 'Section deleted.'); _loaded['sections-mgmt'] = false; loadSectionsMgmt(); }
            else saToast('error', d.message || 'Error deleting section.');
        } catch(e) { saToast('error', 'Network error.'); }
    }

    // ══════════════════════════════════════════════════════════
    // SCHEDULES PANEL — full weekly grid, ported from Admin's own
    // schedule management (adminDashboard.blade.php: loadScheduleGrid/
    // buildScheduleGrid/openScheduleModal/saveSchedule/deleteSchedule/
    // Copy Term), hitting the exact same /admin/schedules* endpoints
    // (ScheduleController), which already accept a superadmin session.
    //
    // Deliberately simplified vs. Admin's version, to keep this portable
    // without duplicating several large page-wide datasets Admin preloads
    // (sectionsList/teachersList/teacherAssignments): the Add/Edit modal's
    // Section/Subject/Teacher dropdowns are populated with on-demand
    // fetches instead, hitting the same endpoints with the same filters.
    // Also NOT ported: drag-and-drop rescheduling, the Schedule Template
    // Generator, CSV export, and the pre-save conflict-confirmation modal
    // (conflicts are still fully detected and shown — just as a warning
    // toast after saving, using the same has_conflict/conflict_reasons the
    // server already returns, rather than a separate pre-save dialog).
    // All of these are optional follow-ups, not core CRUD parity.
    const schedTimeSlots = [
        { label: '7:00-8:00 AM', start: '07:00', end: '08:00', isBreak: false },
        { label: '8:00-9:00 AM', start: '08:00', end: '09:00', isBreak: false },
        { label: '9:00-9:20 AM', start: '09:00', end: '09:20', isBreak: true, breakName: 'Health Break' },
        { label: '9:20-10:20 AM', start: '09:20', end: '10:20', isBreak: false },
        { label: '10:20-11:20 AM', start: '10:20', end: '11:20', isBreak: false },
        { label: '11:20-12:00 PM', start: '11:20', end: '12:00', isBreak: true, breakName: 'Lunch Break' },
        { label: '12:00-1:00 PM', start: '12:00', end: '13:00', isBreak: false },
        { label: '1:00-2:00 PM', start: '13:00', end: '14:00', isBreak: false, breakName: 'Aral Program' },
    ];
    const schedGradeTimeSlots = {
        'nursery': schedTimeSlots.slice(0, 4),
        'kindergarten': schedTimeSlots.slice(0, 4),
        'default': schedTimeSlots,
    };
    const schedTermInfo = {
        '1': { name: 'Term 1' }, '2': { name: 'Term 2' }, '3': { name: 'Term 3' },
    };
    var _saScheduleCache = [];

    function quickSelectScheduleGrade(grade) {
        document.getElementById('scheduleGradeFilter').value = grade;
        loadScheduleGrid();
    }

    async function loadScheduleGrid() {
        var grade = document.getElementById('scheduleGradeFilter').value;
        var term = document.getElementById('scheduleTermFilter').value;
        var sectionId = document.getElementById('scheduleSectionFilter').value;

        // Populate the section filter for this grade, if not already done for it.
        var secSel = document.getElementById('scheduleSectionFilter');
        if (grade && secSel.dataset.loadedGrade !== grade) {
            secSel.innerHTML = '<option value="">All Sections</option>';
            try {
                var sr = await fetch('/admin/sections?grade_level=' + encodeURIComponent(grade), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                var sd = await sr.json();
                (sd.sections || []).forEach(function(sec) {
                    var opt = document.createElement('option');
                    opt.value = sec.id; opt.textContent = sec.name;
                    secSel.appendChild(opt);
                });
                secSel.dataset.loadedGrade = grade;
            } catch(e) {}
        }

        if (!grade) {
            document.getElementById('scheduleGridContainer').style.display = 'none';
            document.getElementById('scheduleEmptyState').style.display = 'block';
            return;
        }
        document.getElementById('scheduleGridContainer').style.display = 'block';
        document.getElementById('scheduleEmptyState').style.display = 'none';

        var gradeLabel = grade.charAt(0).toUpperCase() + grade.slice(1).replace('grade', 'Grade ');
        var sectionLabel = sectionId ? secSel.selectedOptions[0].textContent : 'All Sections';
        var termLabel = (schedTermInfo[term] || {}).name || ('Term ' + term);
        document.getElementById('scheduleInfoText').textContent = gradeLabel + ' - ' + sectionLabel + ' (' + termLabel + ')';

        var pdfUrl = '{{ route("admin.schedules.pdf") }}?grade_level=' + encodeURIComponent(grade) + '&term=' + encodeURIComponent(term);
        if (sectionId) pdfUrl += '&section_id=' + encodeURIComponent(sectionId);
        document.getElementById('saSchedulePdfLink').href = pdfUrl;

        var tbody = document.getElementById('scheduleGridBody');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;"><i class="bi bi-hourglass-split" style="font-size:24px;color:var(--muted);"></i><div style="font-size:12px;color:var(--muted);margin-top:8px;">Loading schedules...</div></td></tr>';

        var fetchUrl = '/admin/schedules?grade_level=' + grade + '&term=' + term + (sectionId ? '&section_id=' + sectionId : '');
        try {
            var r = await fetch(fetchUrl, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var data = await r.json();
            var schedules = Array.isArray(data) ? data : (data.schedules || data.data || []);
            _saScheduleCache = schedules;
            buildSaScheduleGrid(grade, term, sectionId, schedules);
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--red);">Failed to load schedules.</td></tr>';
        }
    }

    function buildSaScheduleGrid(grade, term, sectionId, schedules) {
        var slots = schedGradeTimeSlots[grade] || schedGradeTimeSlots['default'];
        var tbody = document.getElementById('scheduleGridBody');
        tbody.innerHTML = '';
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        slots.forEach(function(slot) {
            var row = document.createElement('tr');
            var timeCell = document.createElement('td');
            timeCell.className = slot.isBreak ? 'break-cell' : 'time-cell';
            timeCell.textContent = slot.isBreak ? slot.breakName : slot.label;
            if (slot.isBreak) {
                timeCell.colSpan = 6;
                row.appendChild(timeCell);
                tbody.appendChild(row);
                return;
            }
            row.appendChild(timeCell);

            days.forEach(function(day) {
                var cell = document.createElement('td');
                cell.className = 'schedule-cell';
                var entry = schedules.find(function(s) {
                    return s.day_of_week === day && (s.start_time || '').substring(0,5) === slot.start && (s.end_time || '').substring(0,5) === slot.end;
                });

                if (entry) {
                    var subjName = entry.subject ? entry.subject.name : (entry.subject_name || 'N/A');
                    var subjCode = entry.subject ? entry.subject.code : '';
                    var teacherName = entry.teacher ? entry.teacher.name : (entry.teacher_name || '');
                    var roomVal = entry.room || '';
                    var hasConflict = !!entry.has_conflict;
                    var reasons = entry.conflict_reasons || [];
                    var warnBadge = hasConflict ? '<div class="sched-conflict-badge" title="' + reasons.map(function(r){return r.replace(/"/g,'&quot;');}).join(' | ') + '"><i class="bi bi-exclamation-triangle-fill"></i> Conflict</div>' : '';
                    cell.innerHTML = '<div class="schedule-cell-content' + (hasConflict ? ' has-conflict' : '') + '">' + warnBadge +
                        '<div class="subj-row">' + (subjCode ? '<span class="subj-code-pill">' + subjCode + '</span>' : '') + '<div class="subject">' + subjName + '</div></div>' +
                        '<div class="teacher"><i class="bi bi-person-fill teacher-icon"></i> ' + (teacherName || '—') + '</div>' +
                        (roomVal ? '<div class="room"><i class="bi bi-geo-alt-fill"></i>' + roomVal + '</div>' : '') + '</div>';
                    cell.querySelector('.schedule-cell-content').onclick = function() { openEditScheduleModal(entry, day, slot, grade, sectionId, term); };
                } else {
                    cell.innerHTML = '<div class="schedule-cell-empty"><i class="bi bi-plus-lg me-1"></i>Add</div>';
                    cell.onclick = function() { openScheduleModalForCell(day, slot, grade, sectionId, term); };
                }
                row.appendChild(cell);
            });
            tbody.appendChild(row);
        });
    }

    function _scheduleModalHtml() {
        var dayBtns = ['Mon','Tue','Wed','Thu','Fri'].map(function(short, i) {
            var full = ['Monday','Tuesday','Wednesday','Thursday','Friday'][i];
            return '<button type="button" class="sa-sched-day-btn" data-day="' + full + '" onclick="setSaSchedDay(\'' + full + '\')" style="padding:7px 12px;border:2px solid #e2e8f0;border-radius:8px;background:#fff;font-size:12px;font-weight:700;cursor:pointer;color:var(--muted);">' + short + '</button>';
        }).join('');
        return '<div class="modal fade" id="saScheduleModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,#4a1d8a,#6f42c1);padding:18px 24px;"><div style="display:flex;align-items:center;gap:12px;"><div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;"><i class="bi bi-calendar3-week-fill" style="color:#fff;font-size:18px;"></i></div><div style="font-size:16px;font-weight:800;color:#fff;" id="saScheduleModalTitle">Add Schedule</div></div><button type="button" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button></div>' +
            '<div style="padding:20px 24px;background:#fff;">' +
            '<input type="hidden" id="sa-sched-id">' +
            '<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-lbl">Grade Level *</label><select id="sa-sched-grade" class="form-fld" onchange="onSaScheduleGradeChange(this.value)"><option value="">— Select Grade —</option><option value="nursery">Nursery</option><option value="kindergarten">Kindergarten</option><option value="grade1">Grade 1</option><option value="grade2">Grade 2</option><option value="grade3">Grade 3</option><option value="grade4">Grade 4</option><option value="grade5">Grade 5</option><option value="grade6">Grade 6</option></select></div>' +
            '<div class="col-md-6"><label class="form-lbl">Section *</label><select id="sa-sched-section" class="form-fld" onchange="onSaScheduleSectionChange(this.value)"><option value="">— Select Grade First —</option></select></div></div>' +
            '<div class="mb-3"><label class="form-lbl">Subject *</label><select id="sa-sched-subject" class="form-fld"><option value="">— Select Grade First —</option></select></div>' +
            '<div class="mb-3"><label class="form-lbl">Teacher (optional)</label><select id="sa-sched-teacher" class="form-fld"><option value="">— None / TBA —</option></select></div>' +
            '<div class="row g-3 mb-3"><div class="col-md-7"><label class="form-lbl">Day of Week *</label><div style="display:flex;gap:6px;flex-wrap:wrap;" id="sa-sched-day-btns">' + dayBtns + '</div><input type="hidden" id="sa-sched-day"></div>' +
            '<div class="col-md-5"><label class="form-lbl">Term *</label><select id="sa-sched-term" class="form-fld"><option value="">— Select Term —</option><option value="1">1st Term</option><option value="2">2nd Term</option><option value="3">3rd Term</option></select></div></div>' +
            '<div class="row g-3 mb-3"><div class="col-md-4"><label class="form-lbl">Start Time *</label><input type="time" id="sa-sched-start" class="form-fld" value="07:30"></div>' +
            '<div class="col-md-4"><label class="form-lbl">End Time *</label><input type="time" id="sa-sched-end" class="form-fld" value="08:30"></div>' +
            '<div class="col-md-4"><label class="form-lbl">Room</label><input type="text" id="sa-sched-room" class="form-fld" placeholder="e.g. Room 101"></div></div>' +
            '<div><label class="form-lbl">Status</label><select id="sa-sched-active" class="form-fld"><option value="1">Active</option><option value="0">Inactive</option></select></div>' +
            '</div><div style="padding:12px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">' +
            '<button type="button" id="sa-sched-delete-btn" style="display:none;padding:9px 16px;background:#fff0f0;color:#dc2626;border:1px solid #fbc8c8;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;" onclick="deleteSaSchedule()"><i class="bi bi-trash me-1"></i>Delete</button>' +
            '<div style="display:flex;gap:10px;margin-left:auto;"><button type="button" data-bs-dismiss="modal" style="padding:9px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>' +
            '<button type="button" onclick="saveSaSchedule()" style="padding:9px 20px;background:linear-gradient(135deg,#4a1d8a,#6f42c1);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;"><i class="bi bi-floppy-fill me-1"></i>Save Schedule</button></div>' +
            '</div></div></div></div>';
    }

    function setSaSchedDay(day) {
        document.querySelectorAll('.sa-sched-day-btn').forEach(function(b) {
            var active = b.dataset.day === day;
            b.style.borderColor = active ? '#6f42c1' : '#e2e8f0';
            b.style.background = active ? '#f3eeff' : '#fff';
            b.style.color = active ? '#6f42c1' : 'var(--muted)';
        });
        document.getElementById('sa-sched-day').value = day;
    }

    async function _populateSaScheduleSections(grade, selectedSectionId) {
        var secSel = document.getElementById('sa-sched-section');
        secSel.innerHTML = '<option value="">Loading…</option>';
        try {
            var r = await fetch('/admin/sections?grade_level=' + encodeURIComponent(grade), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            var sections = d.sections || [];
            secSel.innerHTML = '<option value="">— Select Section —</option>';
            sections.forEach(function(s) {
                var o = document.createElement('option');
                o.value = s.id; o.textContent = s.name;
                if (String(s.id) === String(selectedSectionId)) o.selected = true;
                secSel.appendChild(o);
            });
        } catch(e) { secSel.innerHTML = '<option value="">— Failed to load —</option>'; }
    }

    async function _populateSaScheduleSubjects(grade, selectedSubjectId) {
        var subjSel = document.getElementById('sa-sched-subject');
        subjSel.innerHTML = '<option value="">Loading…</option>';
        try {
            var r = await fetch('/admin/subjects?grade_level=' + encodeURIComponent(grade) + '&is_active=1', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            var subjects = d.subjects || [];
            subjSel.innerHTML = '<option value="">— Select Subject —</option>';
            subjects.forEach(function(s) {
                var o = document.createElement('option');
                o.value = s.id; o.textContent = s.name + ' (' + s.code + ')';
                if (String(s.id) === String(selectedSubjectId)) o.selected = true;
                subjSel.appendChild(o);
            });
        } catch(e) { subjSel.innerHTML = '<option value="">— Failed to load —</option>'; }
    }

    async function _populateSaScheduleTeachers(selectedTeacherId) {
        var teacherSel = document.getElementById('sa-sched-teacher');
        teacherSel.innerHTML = '<option value="">— None / TBA —</option>';
        try {
            var r = await fetch('/admin/teachers', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            var d = await r.json();
            (d.teachers || []).forEach(function(t) {
                var o = document.createElement('option');
                o.value = t.id; o.textContent = t.name;
                if (String(t.id) === String(selectedTeacherId)) o.selected = true;
                teacherSel.appendChild(o);
            });
        } catch(e) {}
    }

    function onSaScheduleGradeChange(grade) {
        if (!grade) return;
        document.getElementById('sa-sched-section').innerHTML = '<option value="">— Select Grade First —</option>';
        document.getElementById('sa-sched-subject').innerHTML = '<option value="">— Select Grade First —</option>';
        _populateSaScheduleSections(grade, '');
        _populateSaScheduleSubjects(grade, '');
    }

    function onSaScheduleSectionChange(sectionId) { /* room/teacher stay as-is — section is just for filtering here */ }

    function openScheduleModal() {
        var existing = document.getElementById('saScheduleModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _scheduleModalHtml());
        document.getElementById('saScheduleModalTitle').textContent = 'Add Schedule';
        document.getElementById('sa-sched-id').value = '';
        setSaSchedDay('');
        document.getElementById('sa-sched-term').value = '';
        document.getElementById('sa-sched-start').value = '07:30';
        document.getElementById('sa-sched-end').value = '08:30';
        document.getElementById('sa-sched-room').value = '';
        document.getElementById('sa-sched-active').value = '1';
        document.getElementById('sa-sched-delete-btn').style.display = 'none';
        _populateSaScheduleTeachers('');
        new bootstrap.Modal(document.getElementById('saScheduleModal')).show();
    }

    function openScheduleModalForCell(day, slot, grade, sectionId, term) {
        openScheduleModal();
        document.getElementById('sa-sched-grade').value = grade || '';
        if (grade) { _populateSaScheduleSections(grade, sectionId || ''); _populateSaScheduleSubjects(grade, ''); }
        setSaSchedDay(day);
        document.getElementById('sa-sched-term').value = term;
        document.getElementById('sa-sched-start').value = slot.start;
        document.getElementById('sa-sched-end').value = slot.end;
    }

    function openEditScheduleModal(entry, day, slot, grade, sectionId, term) {
        var existing = document.getElementById('saScheduleModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', _scheduleModalHtml());
        document.getElementById('saScheduleModalTitle').textContent = 'Edit Schedule';
        document.getElementById('sa-sched-id').value = entry.id;
        document.getElementById('sa-sched-grade').value = grade || '';
        setSaSchedDay(entry.day_of_week || day);
        document.getElementById('sa-sched-term').value = entry.term || term;
        document.getElementById('sa-sched-start').value = (entry.start_time || '').substring(0,5) || slot.start;
        document.getElementById('sa-sched-end').value = (entry.end_time || '').substring(0,5) || slot.end;
        document.getElementById('sa-sched-room').value = entry.room || '';
        document.getElementById('sa-sched-active').value = entry.is_active ? '1' : '0';
        document.getElementById('sa-sched-delete-btn').style.display = '';
        _populateSaScheduleSections(grade || '', entry.section_id || sectionId || '');
        _populateSaScheduleSubjects(grade || '', entry.subject_id || '');
        _populateSaScheduleTeachers(entry.teacher_id || '');
        new bootstrap.Modal(document.getElementById('saScheduleModal')).show();
    }

    async function saveSaSchedule() {
        var id = document.getElementById('sa-sched-id').value;
        var sectionId = document.getElementById('sa-sched-section').value;
        var subjectId = document.getElementById('sa-sched-subject').value;
        var dayOfWeek = document.getElementById('sa-sched-day').value;
        var termVal = document.getElementById('sa-sched-term').value;
        if (!sectionId || !subjectId || !dayOfWeek || !termVal) { saToast('warning', 'Section, Subject, Day, and Term are required.'); return; }

        var body = {
            section_id: sectionId, subject_id: subjectId,
            teacher_id: document.getElementById('sa-sched-teacher').value || null,
            day_of_week: dayOfWeek, term: termVal,
            start_time: document.getElementById('sa-sched-start').value,
            end_time: document.getElementById('sa-sched-end').value,
            room: document.getElementById('sa-sched-room').value,
            is_active: document.getElementById('sa-sched-active').value === '1',
        };

        var method = id ? 'PUT' : 'POST';
        var url = '/admin/schedules' + (id ? '/' + id : '');
        try {
            var r = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });
            var d = await r.json();
            if (r.ok) {
                bootstrap.Modal.getInstance(document.getElementById('saScheduleModal'))?.hide();
                var reasons = d.conflict_reasons || [];
                if (d.has_conflict || reasons.length) {
                    saToast('warning', (id ? 'Schedule updated' : 'Schedule created') + ', but it overlaps with another entry: ' + reasons.join('; '));
                } else {
                    saToast('success', id ? 'Schedule updated!' : 'Schedule created!');
                }
                loadScheduleGrid();
            } else {
                var msg = d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'Error saving schedule.');
                saToast('error', msg);
            }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    function deleteSaSchedule() {
        var id = document.getElementById('sa-sched-id').value;
        if (!id) return;
        saConfirm({
            title: 'Delete Schedule',
            msg: 'Delete this schedule entry?',
            icon: 'bi-trash3-fill',
            gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel: 'Delete',
            btnColor: '#dc2626',
            onConfirm: async function() {
                try {
                    var r = await fetch('/admin/schedules/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                    if (r.ok) {
                        bootstrap.Modal.getInstance(document.getElementById('saScheduleModal'))?.hide();
                        saToast('success', 'Schedule entry deleted.');
                        loadScheduleGrid();
                    } else { saToast('error', 'Failed to delete schedule.'); }
                } catch(e) { saToast('error', 'Network error.'); }
            }
        });
    }

    // ── Copy Term Schedule ──
    function openCopyTermModal() {
        var html = '<div class="modal fade" id="saCopyTermModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,#4a1d8a,#6f42c1);padding:18px 24px;"><div style="display:flex;align-items:center;gap:12px;"><div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;"><i class="bi bi-copy" style="color:#fff;font-size:16px;"></i></div><div style="font-size:16px;font-weight:800;color:#fff;">Copy Term Schedule</div></div><button type="button" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button></div>' +
            '<div style="padding:20px 24px;background:#fff;">' +
            '<div style="font-size:12.5px;color:var(--muted);background:#f8f9fa;border-radius:8px;padding:12px 14px;margin-bottom:18px;"><i class="bi bi-info-circle me-1"></i>Duplicates every schedule block from the source term into the target term. Blocks that would create a conflict are skipped and listed, not silently overwritten.</div>' +
            '<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-lbl">Copy From *</label><select id="sa-copyterm-source" class="form-fld"><option value="1">1st Term</option><option value="2">2nd Term</option><option value="3">3rd Term</option></select></div>' +
            '<div class="col-md-6"><label class="form-lbl">Copy To *</label><select id="sa-copyterm-target" class="form-fld"><option value="1">1st Term</option><option value="2" selected>2nd Term</option><option value="3">3rd Term</option></select></div></div>' +
            '<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:4px;"><input type="checkbox" id="sa-copyterm-replace" style="margin-top:3px;"><label for="sa-copyterm-replace" style="font-size:13px;">Clear the target term\'s existing schedule first<div style="font-size:11px;color:var(--muted);">Leave unchecked to add alongside whatever\'s already in the target term.</div></label></div>' +
            '<div id="sa-copyterm-result" style="display:none;margin-top:16px;"></div>' +
            '</div><div style="padding:12px 24px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;gap:10px;justify-content:flex-end;">' +
            '<button type="button" data-bs-dismiss="modal" style="padding:9px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>' +
            '<button type="button" id="sa-copyterm-submit-btn" onclick="submitSaCopyTerm()" style="padding:9px 20px;background:linear-gradient(135deg,#4a1d8a,#6f42c1);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;"><i class="bi bi-copy me-1"></i>Copy Schedule</button>' +
            '</div></div></div></div>';
        var existing = document.getElementById('saCopyTermModal');
        if (existing) existing.remove();
        document.body.insertAdjacentHTML('beforeend', html);
        new bootstrap.Modal(document.getElementById('saCopyTermModal')).show();
    }

    function submitSaCopyTerm() {
        var sourceTerm = document.getElementById('sa-copyterm-source').value;
        var targetTerm = document.getElementById('sa-copyterm-target').value;
        var replace = document.getElementById('sa-copyterm-replace').checked;
        var resultEl = document.getElementById('sa-copyterm-result');

        if (sourceTerm === targetTerm) {
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div style="background:#fff3e0;color:#e65100;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-exclamation-triangle me-1"></i>Source and target term must be different.</div>';
            return;
        }
        if (replace) {
            saConfirm({
                title: 'Overwrite Target Term', msg: 'This will delete the existing schedule for the target term before copying. Continue?',
                icon: 'bi-exclamation-triangle-fill', gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)',
                btnLabel: 'Continue', btnColor: '#dc2626',
                onConfirm: function() { _doSaCopyTerm(sourceTerm, targetTerm, replace, resultEl); }
            });
            return;
        }
        _doSaCopyTerm(sourceTerm, targetTerm, replace, resultEl);
    }

    async function _doSaCopyTerm(sourceTerm, targetTerm, replace, resultEl) {
        var btn = document.getElementById('sa-copyterm-submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Copying…';
        resultEl.style.display = 'none';
        try {
            var r = await fetch('{{ route("admin.schedules.copy-term") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ source_term: sourceTerm, target_term: targetTerm, replace_target: replace }),
            });
            var data = await r.json();
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy Schedule';
            if (!r.ok || !data.success) {
                resultEl.style.display = 'block';
                resultEl.innerHTML = '<div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-x-circle me-1"></i>' + (data.message || 'Failed to copy schedule.') + '</div>';
                return;
            }
            var html = '<div style="background:#e8f5e9;color:#1b5e20;padding:10px 14px;border-radius:8px;font-size:12.5px;margin-bottom:' + (data.skipped.length ? '10px' : '0') + ';"><i class="bi bi-check-circle me-1"></i>Copied ' + data.copied + ' of ' + data.total + ' schedule block(s).</div>';
            if (data.skipped.length) {
                html += '<div style="background:#fff3e0;color:#e65100;padding:10px 14px;border-radius:8px;font-size:12px;"><div style="font-weight:700;margin-bottom:6px;"><i class="bi bi-exclamation-triangle me-1"></i>' + data.skipped.length + ' skipped due to conflicts:</div><ul style="margin:0;padding-left:18px;">' +
                    data.skipped.map(function(s) { return '<li>' + s.section + ' — ' + s.subject + ' (' + s.day + ' ' + s.time + '): ' + s.reasons.join('; ') + '</li>'; }).join('') + '</ul></div>';
            }
            resultEl.style.display = 'block';
            resultEl.innerHTML = html;
            if (document.getElementById('scheduleTermFilter').value === targetTerm) loadScheduleGrid();
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy Schedule';
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-x-circle me-1"></i>Network error. Please try again.</div>';
        }
    }

    // ══════════════════════════════════════════════════════════
    // SYSTEM SETTINGS PANEL
    // ══════════════════════════════════════════════════════════
    var _sysEnrollmentOpen = null;
    var _sysMaintenanceOn  = null;
    var _sysSiteMaintenanceOn = null;

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

            // Portal maintenance toggle
            _sysMaintenanceOn = flat['maintenance_mode'] === '1' || flat['maintenance_mode'] === true || flat['maintenance_mode'] === 1;
            updateMaintenanceToggleUI(_sysMaintenanceOn);

            // Website maintenance toggle
            _sysSiteMaintenanceOn = flat['site_maintenance_mode'] === '1' || flat['site_maintenance_mode'] === true || flat['site_maintenance_mode'] === 1;
            updateSiteMaintenanceToggleUI(_sysSiteMaintenanceOn);

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
            document.getElementById('site-maint-toggle-desc').textContent = 'Failed to load settings.';
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
        if (desc) desc.textContent = isOn ? 'Student/Teacher portals are in maintenance mode.' : 'Student/Teacher portals are accessible.';
    }

    function updateSiteMaintenanceToggleUI(isOn) {
        var label = document.getElementById('site-maint-toggle-label');
        var btn   = document.getElementById('btn-toggle-site-maintenance');
        var desc  = document.getElementById('site-maint-toggle-desc');
        if (!label || !btn) return;
        label.textContent = isOn ? 'ON' : 'OFF';
        label.style.color = isOn ? 'var(--red)' : 'var(--green)';
        btn.textContent = isOn ? 'Disable Maintenance' : 'Enable Maintenance';
        btn.style.background = isOn ? 'linear-gradient(135deg,#14532d,#16a34a)' : 'linear-gradient(135deg,#92400e,#d97706)';
        btn.style.color = '#fff';
        if (desc) desc.textContent = isOn ? 'Public website is in maintenance mode.' : 'Public website is accessible.';
    }

    function toggleEnrollment() {
        var opening = !_sysEnrollmentOpen;
        saConfirm({
            title    : opening ? 'Open Enrollment' : 'Close Enrollment',
            sub      : 'Online Enrollment',
            msg      : opening
                ? 'Students will be able to submit online applications again.'
                : 'The enrollment form will be hidden and applicants will see a closed notice.',
            icon     : opening ? 'bi-clipboard-check-fill' : 'bi-clipboard-x-fill',
            gradient : opening ? 'linear-gradient(135deg,#14532d,#16a34a)' : 'linear-gradient(135deg,#b91c1c,#dc2626)',
            btnLabel : opening ? 'Open Enrollment' : 'Close Enrollment',
            btnColor : opening ? '#16a34a' : '#dc2626',
            onConfirm: doToggleEnrollment
        });
    }

    async function doToggleEnrollment() {
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

    function toggleMaintenance() {
        var enabling = !_sysMaintenanceOn;
        saConfirm({
            title    : enabling ? 'Enable Portal Maintenance' : 'Disable Portal Maintenance',
            sub      : 'Student & Teacher Portals',
            msg      : enabling
                ? 'Students and teachers will be signed out of their portals and shown a maintenance notice until this is turned off.'
                : 'Student and Teacher portals will become accessible again.',
            icon     : 'bi-tools',
            gradient : enabling ? 'linear-gradient(135deg,#92400e,#d97706)' : 'linear-gradient(135deg,#14532d,#16a34a)',
            btnLabel : enabling ? 'Enable Maintenance' : 'Disable Maintenance',
            btnColor : enabling ? '#d97706' : '#16a34a',
            onConfirm: doToggleMaintenance
        });
    }

    async function doToggleMaintenance() {
        try {
            var r = await fetch('/admin/settings/toggle-maintenance', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success !== false) {
                _sysMaintenanceOn = !_sysMaintenanceOn;
                updateMaintenanceToggleUI(_sysMaintenanceOn);
                saToast('success', _sysMaintenanceOn ? 'Portal maintenance mode ENABLED.' : 'Portal maintenance mode DISABLED.');
            } else { saToast('error', d.message || 'Toggle failed.'); }
        } catch(e) { saToast('error', 'Network error.'); }
    }

    function toggleSiteMaintenance() {
        var enabling = !_sysSiteMaintenanceOn;
        saConfirm({
            title    : enabling ? 'Enable Website Maintenance' : 'Disable Website Maintenance',
            sub      : 'Public Website',
            msg      : enabling
                ? 'Visitors to the public website (home, about, admission, news, etc.) will see a maintenance notice until this is turned off. Student/Teacher portals are not affected.'
                : 'The public website will become accessible again.',
            icon     : 'bi-globe2',
            gradient : enabling ? 'linear-gradient(135deg,#92400e,#d97706)' : 'linear-gradient(135deg,#14532d,#16a34a)',
            btnLabel : enabling ? 'Enable Maintenance' : 'Disable Maintenance',
            btnColor : enabling ? '#d97706' : '#16a34a',
            onConfirm: doToggleSiteMaintenance
        });
    }

    async function doToggleSiteMaintenance() {
        try {
            var r = await fetch('/admin/settings/toggle-site-maintenance', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            var d = await r.json();
            if (d.success !== false) {
                _sysSiteMaintenanceOn = !_sysSiteMaintenanceOn;
                updateSiteMaintenanceToggleUI(_sysSiteMaintenanceOn);
                saToast('success', _sysSiteMaintenanceOn ? 'Website maintenance mode ENABLED.' : 'Website maintenance mode DISABLED.');
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
            data:{labels:@json($saChMonths??[]),
                datasets:[{label:'Enrollments',data:@json($saChEnroll??[]),
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
                    {{$stats['total_students']??0}},
                    {{$stats['total_teachers']??0}},
                    {{($stats['admins']??0)+($stats['superadmins']??0)}},
                    {{\App\Models\User::where('role','finance')->count()}},
                    {{\App\Models\User::where('role','cashier')->count()}}
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
            data:{labels:@json($saRptMonths??[]),
                datasets:[{label:'Enrollments',data:@json($saRptEnroll??[]),
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
                    data:[{{$stats['total_students']??0}},{{$stats['total_teachers']??0}},{{($stats['admins']??0)+($stats['superadmins']??0)}},{{\App\Models\User::where('role','finance')->count()}},{{\App\Models\User::where('role','cashier')->count()}}],
                    backgroundColor:[_SAC.blue,_SAC.gold,_SAC.mid,_SAC.green,_SAC.purple],
                    borderRadius:5,borderSkipped:false}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'}},x:{grid:{display:false}}}}
        });
    }
</script>
</body>
</html>