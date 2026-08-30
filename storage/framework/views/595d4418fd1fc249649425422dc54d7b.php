<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Teacher Dashboard — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/global-scrollbar.css">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        /* ── Section skeleton loading ── */
        @keyframes pSkelShimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skel{background:linear-gradient(90deg,#e8edf2 25%,#f5f7fa 50%,#e8edf2 75%);background-size:600px 100%;animation:pSkelShimmer 1.4s ease-in-out infinite;border-radius:6px;display:block}
        .p-skel{position:absolute;top:0;left:0;right:0;bottom:0;padding:28px;z-index:50;background:var(--bg,#f0f4f8);pointer-events:none;min-height:100vh;transition:opacity .32s ease}
        [id^="section-"]{position:relative}

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

        /* Hide pagination arrows globally */
        .pagination .page-item:first-child .page-link::before,
        .pagination .page-item:last-child .page-link::before,
        .pagination .page-item:first-child .page-link::after,
        .pagination .page-item:last-child .page-link::after {
            display: none !important;
            content: none !important;
        }
        .pagination .page-item:first-child .page-link i,
        .pagination .page-item:last-child .page-link i {
            display: none !important;
        }
        /* Hide Bootstrap arrow entities */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            text-indent: 0 !important;
        }

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

        /* SIDEBAR */
        .dash-sidebar {
            position: fixed; top: var(--topbar-h); left: 0;
            width: var(--sidebar-w); height: calc(100vh - var(--topbar-h));
            background: var(--blue); display: flex; flex-direction: column;
            padding: 14px 0; z-index: 99; overflow-y: auto;
        }
        .sidebar-section-lbl {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255,255,255,0.35); padding: 12px 20px 5px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px; color: rgba(255,255,255,0.7);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all 0.2s; border-left: 3px solid transparent;
            cursor: pointer; background: none; border-right: none;
            border-top: none; border-bottom: none;
            width: 100%; text-align: left; font-family: 'Open Sans', sans-serif;
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
        .dash-section { padding: 28px; }

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

        /* TABLE */
        .dash-table { width: 100%; border-collapse: collapse; }
        .dash-table thead th {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: var(--muted); padding: 10px 20px;
            background: #f8fafc; border-bottom: 1px solid var(--border); white-space: nowrap;
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
        .status-badge.active    { background: var(--blue-pale); color: var(--blue); }
        .status-badge.enrolled  { background: #e8f8f0; color: var(--green); }
        .status-badge.pending   { background: #fff8ec; color: #d68910; }
        .status-badge.submitted { background: #e8f8f0; color: var(--green); }
        .status-badge.draft     { background: #fff8ec; color: #d68910; }
        .status-badge.locked    { background: #fdecea; color: var(--red); }

        /* USER ROW */
        .user-row-name { display: flex; align-items: center; gap: 10px; }
        .user-row-avatar {
            width: 34px; height: 34px; border-radius: 50%;
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
        .action-btn.gold   { background: #fff8ec; color: #d68910; }
        .action-btn:hover  { opacity: 0.75; }

        /* ANN ITEMS */
        .ann-dash-item {
            display: flex; gap: 14px; padding: 14px 20px;
            border-bottom: 1px solid var(--border); transition: background 0.15s;
        }
        .ann-dash-item:last-child { border-bottom: none; }
        .ann-dash-item:hover { background: #f8fafc; }
        .ann-date-badge {
            background: var(--blue); color: #fff; border-radius: 6px;
            text-align: center; padding: 6px 10px; min-width: 48px; flex-shrink: 0;
        }
        .ann-day { font-size: 18px; font-weight: 700; line-height: 1; display: block; }
        .ann-mon { font-size: 10px; text-transform: uppercase; opacity: 0.8; display: block; }
        .ann-body-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
        .ann-body-meta  { font-size: 11px; color: var(--muted); }

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
        textarea.form-fld { resize: vertical; min-height: 90px; }

        /* SCHEDULE CARD */
        .sched-card {
            border: 1px solid var(--border); border-radius: 10px;
            padding: 0; overflow: hidden; margin-bottom: 10px;
            transition: box-shadow 0.2s;
        }
        .sched-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .sched-card-bar {
            width: 5px; border-radius: 0; flex-shrink: 0;
            min-height: 100%;
        }
        .sched-card-inner { display: flex; gap: 0; }
        .sched-card-bar.blue   { background: var(--blue); }
        .sched-card-bar.green  { background: var(--green); }
        .sched-card-bar.gold   { background: var(--gold); }
        .sched-card-bar.red    { background: var(--red); }
        .sched-card-bar.purple { background: #7d3c98; }
        .sched-card-bar.orange { background: var(--orange); }
        .sched-card-content { padding: 14px 16px; flex: 1; }
        .sched-subject { font-size: 14px; font-weight: 700; color: var(--text); }
        .sched-meta    { font-size: 12px; color: var(--muted); margin-top: 3px; display: flex; gap: 12px; flex-wrap: wrap; }
        .sched-meta span { display: flex; align-items: center; gap: 4px; }
        .sched-time-badge {
            padding: 14px 16px; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: #f8fafc; border-left: 1px solid var(--border);
            min-width: 90px; text-align: center;
        }
        .sched-time-main { font-size: 13px; font-weight: 700; color: var(--text); }
        .sched-time-sub  { font-size: 10px; color: var(--muted); margin-top: 2px; }

        /* GRADEBOOK */
        .gradebook-input {
            width: 70px; border: 1.5px solid var(--border);
            border-radius: 6px; padding: 6px 8px;
            font-size: 13px; font-family: 'Open Sans', sans-serif;
            text-align: center; background: #fafafa; color: var(--text);
            transition: border 0.2s;
        }
        .gradebook-input:focus { border-color: var(--blue); outline: none; background: #fff; }
        .gradebook-input.passed { border-color: var(--green); background: #f0fdf4; }
        .gradebook-input.failed { border-color: var(--red); background: #fef2f2; }

        .grade-pill {
            display: inline-block; padding: 4px 12px;
            border-radius: 12px; font-size: 12px; font-weight: 700;
        }
        .grade-pill.outstanding  { background: #e8f8f0; color: var(--green); }
        .grade-pill.satisfactory { background: var(--blue-pale); color: var(--blue); }
        .grade-pill.fairly       { background: #fff8ec; color: #d68910; }
        .grade-pill.failed       { background: #fdecea; color: var(--red); }

        /* PROGRESS */
        .prog-bar-wrap { background: var(--bg); border-radius: 20px; height: 8px; overflow: hidden; margin-top: 6px; }
        .prog-bar { height: 100%; border-radius: 20px; background: var(--blue); transition: width 0.4s; }
        .prog-bar.gold  { background: var(--gold); }
        .prog-bar.green { background: var(--green); }
        .prog-bar.red   { background: var(--red); }

        /* ATTENDANCE */
        .att-day {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.15s;
            border: 2px solid transparent;
        }
        .att-day.present { background: #e8f8f0; color: var(--green); border-color: var(--green); }
        .att-day.absent  { background: #fdecea; color: var(--red); border-color: var(--red); }
        .att-day.late    { background: #fff8ec; color: #d68910; border-color: #d68910; }
        .att-day.none    { background: #f0f4f8; color: var(--muted); border-color: var(--border); }
        .att-day:hover   { opacity: 0.75; }

        /* SECTION LBL */
        .section-title-bar {
            font-size: 11px; font-weight: 700; color: var(--blue);
            text-transform: uppercase; letter-spacing: 1.5px;
            background: var(--blue-pale); padding: 8px 14px; border-radius: 4px;
            margin-bottom: 16px; margin-top: 10px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-title-bar i { color: var(--gold); }

        /* STUDENT PHOTO CARD */
        .student-card {
            border: 1px solid var(--border); border-radius: 10px;
            padding: 16px; text-align: center; background: var(--white);
            transition: box-shadow 0.2s, transform 0.2s; cursor: pointer;
        }
        .student-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .student-photo {
            width: 52px; height: 52px; border-radius: 50%;
            background: var(--blue-pale);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: var(--blue);
            margin: 0 auto 10px;
        }
        .student-card-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .student-card-sub  { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .student-card-avg  { font-size: 20px; font-weight: 700; color: var(--blue); margin-top: 8px; }

        /* WEIGHT INPUT */
        .weight-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid var(--border);
        }
        .weight-row:last-child { border-bottom: none; }
        .weight-label { font-size: 13px; font-weight: 600; color: var(--text); }
        .weight-input {
            width: 80px; border: 1.5px solid var(--border);
            border-radius: 8px; padding: 8px 12px;
            font-size: 13px; text-align: center; font-family: 'Open Sans', sans-serif;
            background: #fafafa; color: var(--text); transition: border 0.2s;
        }
        .weight-input:focus { border-color: var(--blue); outline: none; }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="dash-topbar">
    <div class="topbar-brand">
        
            <div class="brand-logo-circle">
                <img src="/images/logo.png" alt=""
                     onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-shield-fill\'></i>'">
            </div>
        <div class="brand-info">
            <h6>IEMELIF Learning Center</h6>
            <span>General Tinio, Nueva Ecija</span>
        </div>
    </div>
    <div class="topbar-center">
        <div class="dash-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search students, subjects...">
        </div>
    </div>
    <div class="topbar-right">
        <a href="#" class="topbar-icon-btn">
            <i class="bi bi-bell"></i>
            <span class="notif-dot">2</span>
        </a>
        <a href="#" class="topbar-icon-btn">
            <i class="bi bi-envelope"></i>
        </a>
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">
                    <?php if($teacher->profile_photo): ?>
                        <img src="<?php echo e(asset('storage/' . $teacher->profile_photo)); ?>" alt="Avatar">
                    <?php else: ?>
                        <?php echo e(strtoupper(substr($teacher->name, 0, 1))); ?>

                    <?php endif; ?>
                </div>
                <div>
                    <div class="user-chip-name"><?php echo e($teacher->name); ?></div>
                    <div class="user-chip-role">Teacher</div>
                </div>
                <i class="bi bi-chevron-down user-chip-caret"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end user-chip-dropdown">
                <div class="ucd-header">
                    <div class="ucd-avatar">
                        <?php if($teacher->profile_photo): ?>
                            <img src="<?php echo e(asset('storage/' . $teacher->profile_photo)); ?>" alt="Avatar">
                        <?php else: ?>
                            <?php echo e(strtoupper(substr($teacher->name, 0, 2))); ?>

                        <?php endif; ?>
                    </div>
                    <div class="ucd-info">
                        <div class="ucd-name"><?php echo e($teacher->name); ?></div>
                        <div class="ucd-email"><?php echo e($teacher->email); ?></div>
                        <span class="ucd-badge">Teacher</span>
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
    <button class="sidebar-link active" id="nav-dashboard" onclick="showSection('dashboard')">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </button>

    <div class="sidebar-section-lbl">Academic</div>
    <button class="sidebar-link" id="nav-students" onclick="showSection('students')">
        <i class="bi bi-people-fill"></i> My Students
        <?php if($draftCount > 0): ?><span class="sidebar-badge"><?php echo e($draftCount); ?></span><?php endif; ?>
    </button>
    <button class="sidebar-link" id="nav-schedule" onclick="showSection('schedule')">
        <i class="bi bi-calendar3"></i> My Schedule
    </button>
    <button class="sidebar-link" id="nav-reports" onclick="showSection('reports')">
        <i class="bi bi-printer-fill"></i> Grade Reports
    </button>
    

    <div class="sidebar-divider"></div>
    <button class="sidebar-link" id="nav-settings" onclick="showSection('settings')">
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
    <div id="section-dashboard" class="dash-section">
        <div class="section-header">
            <div>
                <h1>Teacher Dashboard</h1>
                <p><?php echo e(now()->format('l, F d, Y')); ?> — Welcome back, <?php echo e($teacher->name); ?>!</p>
            </div>
            <a href="#" onclick="showSection('students')" class="btn-dash btn-primary">
                <i class="bi bi-journal-check"></i> My Students
            </a>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($sections->sum(function($s) { return $s->students->count(); })); ?></div>
                        <div class="stat-label">Total Students</div>
                        <div class="stat-change up">Across all sections</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-pencil-square"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($draftCount); ?></div>
                        <div class="stat-label">Pending Drafts</div>
                        <div class="stat-change <?php echo e($draftCount > 0 ? 'down' : 'up'); ?>"><?php echo e($draftCount > 0 ? 'Awaiting review' : 'All submitted'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($schedules->count()); ?></div>
                        <div class="stat-label">Total Classes</div>
                        <div class="stat-change up">S.Y. 2026–2027</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-book-fill"></i></div>
                    <div>
                        <div class="stat-value"><?php echo e($subjects->count()); ?></div>
                        <div class="stat-label">Subjects Handled</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Today's Schedule -->
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>My Class Schedule Today</h6>
                        <a href="#" onclick="showSection('schedule')">Full Schedule</a>
                    </div>
                    <div class="p-3">
                        <?php
                            $todayDay = now()->format('l');
                            $todaySchedules = $schedules->where('day_of_week', $todayDay)->sortBy('start_time');
                        ?>
                        <?php if($todaySchedules->count() > 0): ?>
                            <?php $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sched): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="sched-card">
                                    <div class="sched-card-inner">
                                        <div class="sched-card-bar blue"></div>
                                        <div class="sched-card-content">
                                            <div class="sched-subject"><?php echo e($sched->subject->name ?? 'All Subjects'); ?></div>
                                            <div class="sched-meta">
                                                <span><i class="bi bi-people-fill"></i> <?php echo e($sched->section->grade_level); ?> – <?php echo e($sched->section->name); ?></span>
                                                <span><i class="bi bi-door-open-fill"></i> <?php echo e($sched->room ?? '—'); ?></span>
                                            </div>
                                        </div>
                                        <div class="sched-time-badge">
                                            <div class="sched-time-main"><?php echo e(\Carbon\Carbon::parse($sched->start_time)->format('g:i A')); ?></div>
                                            <div class="sched-time-sub"><?php echo e(\Carbon\Carbon::parse($sched->start_time)->diffInMinutes(\Carbon\Carbon::parse($sched->end_time))); ?> mins</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div style="text-align:center; padding:40px; color:var(--muted);">
                                <i class="bi bi-calendar-x" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>
                                No classes scheduled for today (<?php echo e($todayDay); ?>).
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Draft Grades -->
            <div class="col-lg-5">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6>Draft Grades</h6>
                        <a href="#" onclick="showSection('students')">My Students</a>
                    </div>
                    <div style="padding:20px 30px;text-align:center;color:var(--muted);font-size:13px;">
                        <?php if(($rejectedCount ?? 0) > 0): ?>
                            <div style="background:#ffebee;border:1px solid #ef9a9a;border-radius:8px;padding:12px 16px;margin-bottom:10px;text-align:left;">
                                <i class="bi bi-x-circle-fill" style="color:#c62828;"></i>
                                <strong style="color:#c62828;"> <?php echo e($rejectedCount); ?> grade submission(s) were returned by admin for revision.</strong>
                                <div style="font-size:11px;color:#b71c1c;margin-top:4px;">Please open the Class Record, correct the grades, and re-submit.</div>
                            </div>
                        <?php endif; ?>
                        <?php if($draftCount > 0): ?>
                            <i class="bi bi-exclamation-circle-fill" style="font-size:30px;display:block;margin-bottom:8px;color:var(--gold);opacity:0.85;"></i>
                            <strong style="color:var(--text);"><?php echo e($draftCount); ?> draft grade(s)</strong> awaiting review and submission.
                        <?php elseif(($rejectedCount ?? 0) == 0): ?>
                            <i class="bi bi-check-circle-fill" style="font-size:30px;display:block;margin-bottom:8px;color:var(--green);opacity:0.7;"></i>
                            No pending drafts. All grades are submitted.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-dashboard -->


    <!-- ═══════════════════════════
         SECTION: SCHEDULE
    ═══════════════════════════ -->
    <div id="section-schedule" class="dash-section" style="display:none;">
        <?php
            // Always show Mon–Fri; only add Saturday if there's an actual Saturday class
            $usedDays  = $schedules->pluck('day_of_week')->unique()->toArray();
            $showDays  = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            if (in_array('Saturday', $usedDays)) {
                $showDays[] = 'Saturday';
            }
            $timeSlots  = [];
            foreach ($schedules as $sched) {
                $timeKey = substr($sched->start_time,0,5).'-'.substr($sched->end_time,0,5);
                if (!isset($timeSlots[$timeKey])) {
                    $timeSlots[$timeKey] = ['start'=>$sched->start_time,'end'=>$sched->end_time,'days'=>[]];
                }
                $timeSlots[$timeKey]['days'][$sched->day_of_week] = $sched;
            }
            ksort($timeSlots);

            $dayColors = [
                'Monday'    => ['bg'=>'#1565c0','light'=>'#e3f0ff','text'=>'#1565c0','border'=>'#1565c0'],
                'Tuesday'   => ['bg'=>'#6a1b9a','light'=>'#f3e5ff','text'=>'#6a1b9a','border'=>'#6a1b9a'],
                'Wednesday' => ['bg'=>'#1b5e20','light'=>'#e6f4ea','text'=>'#1b5e20','border'=>'#1b5e20'],
                'Thursday'  => ['bg'=>'#e65100','light'=>'#fff3e0','text'=>'#e65100','border'=>'#e65100'],
                'Friday'    => ['bg'=>'#b71c1c','light'=>'#fdecea','text'=>'#b71c1c','border'=>'#b71c1c'],
                'Saturday'  => ['bg'=>'#37474f','light'=>'#eceff1','text'=>'#37474f','border'=>'#37474f'],
            ];
            $dayShort = ['Monday'=>'Mon','Tuesday'=>'Tue','Wednesday'=>'Wed','Thursday'=>'Thu','Friday'=>'Fri','Saturday'=>'Sat'];

            $totalPeriods  = $schedules->count();
            $uniqueSubjects = $schedules->pluck('subject_id')->filter()->unique()->count();
            $uniqueSections = $schedules->pluck('section_id')->filter()->unique()->count();
            $activeDays    = count(array_intersect($showDays, $usedDays));

            $primaryAdviser     = $adviserSections->first();
            $adviserGrade       = $primaryAdviser ? ($gradeLabels[$primaryAdviser->grade_level] ?? ucfirst(str_replace('_',' ',$primaryAdviser->grade_level))) : null;
            $adviserStudentCount = $adviserSections->sum(fn($s) => $s->students->count());

            $todayName = now()->format('l'); // e.g. 'Monday'
            $todaySchedules = $schedules->filter(fn($s) => $s->day_of_week === $todayName)->sortBy('start_time');
        ?>

        <!-- Header -->
        <div class="section-header">
            <div>
                <h1 style="display:flex;align-items:center;gap:10px;">
                    <span style="width:40px;height:40px;background:linear-gradient(135deg,var(--blue),var(--blue-light));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-calendar-week-fill" style="color:#fff;font-size:18px;"></i>
                    </span>
                    My Schedule
                </h1>
                <p>S.Y. <?php echo e($currentSchoolYear); ?> &nbsp;·&nbsp; Weekly teaching timetable</p>
            </div>
            <button onclick="window.print()" class="btn-dash btn-secondary" style="padding:9px 16px;">
                <i class="bi bi-printer-fill"></i> Print
            </button>
        </div>

        <!-- Stats Row -->
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
            <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;border-radius:10px;background:#e3f0ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-collection-fill" style="color:#1565c0;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:var(--text);line-height:1;"><?php echo e($totalPeriods); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">Total Periods</div>
                </div>
            </div>
            <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;border-radius:10px;background:#e6f4ea;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-book-fill" style="color:#1b5e20;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:var(--text);line-height:1;"><?php echo e($uniqueSubjects); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">Subjects</div>
                </div>
            </div>
            <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;border-radius:10px;background:#f3e5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-diagram-3-fill" style="color:#6a1b9a;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:var(--text);line-height:1;"><?php echo e($uniqueSections); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">Sections</div>
                </div>
            </div>
            <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;border-radius:10px;background:#fff3e0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-calendar3" style="color:#e65100;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:var(--text);line-height:1;"><?php echo e($activeDays); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">Teaching Days</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Timetable -->
            <div class="col-lg-8">
                <div class="content-card" style="overflow:hidden;">
                    <div class="content-card-header" style="background:linear-gradient(135deg,var(--blue),var(--blue-light));border-bottom:none;padding:14px 20px;">
                        <h6 style="color:#fff;margin:0;display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-grid-3x3-gap-fill"></i> Weekly Timetable
                        </h6>
                        <span style="font-size:11px;color:rgba(255,255,255,0.75);"><?php echo e($totalPeriods); ?> period<?php echo e($totalPeriods !== 1 ? 's' : ''); ?> this week</span>
                    </div>

                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;min-width:480px;">
                            <thead>
                                <tr>
                                    <th style="background:#f8fafc;padding:10px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid var(--border);white-space:nowrap;min-width:110px;">
                                        <i class="bi bi-clock" style="margin-right:4px;"></i> Time
                                    </th>
                                    <?php $__currentLoopData = $showDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th style="background:#f8fafc;padding:10px 12px;text-align:center;
                                                   border-bottom:<?php echo e($day === $todayName ? '2px solid var(--blue)' : '2px solid var(--border)'); ?>;
                                                   min-width:120px;">
                                            <div style="font-size:12px;font-weight:700;color:<?php echo e($day === $todayName ? 'var(--blue)' : 'var(--text)'); ?>;">
                                                <?php echo e($dayShort[$day] ?? $day); ?>

                                            </div>
                                            <div style="font-size:10px;color:var(--muted);margin-top:1px;"><?php echo e($day); ?></div>
                                            <?php if($day === $todayName): ?>
                                                <div style="margin-top:4px;display:inline-block;background:var(--blue-pale);border-radius:10px;padding:1px 8px;font-size:9px;font-weight:700;color:var(--blue);">TODAY</div>
                                            <?php endif; ?>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $timeSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timeKey => $timeSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="border-bottom:1px solid var(--border);">
                                        <td style="background:#f8fafc;padding:12px 14px;vertical-align:middle;border-right:2px solid var(--border);">
                                            <div style="font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;">
                                                <?php echo e(\Carbon\Carbon::parse($timeSlot['start'])->format('g:i')); ?>

                                            </div>
                                            <div style="font-size:10px;color:var(--muted);white-space:nowrap;">
                                                <?php echo e(\Carbon\Carbon::parse($timeSlot['start'])->format('A')); ?> – <?php echo e(\Carbon\Carbon::parse($timeSlot['end'])->format('g:i A')); ?>

                                            </div>
                                        </td>
                                        <?php $__currentLoopData = $showDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $cell    = $timeSlot['days'][$day] ?? null;
                                                $isToday = ($day === $todayName);
                                            ?>
                                            <td style="padding:8px 10px;vertical-align:middle;text-align:left;
                                                <?php echo e($isToday ? 'background:var(--blue-pale);' : ''); ?>">
                                                <?php if($cell): ?>
                                                    <div style="background:#fff;border:1px solid var(--border);border-left:3px solid var(--blue);border-radius:6px;padding:8px 10px;">
                                                        <div style="font-size:12px;font-weight:700;color:var(--blue);line-height:1.3;margin-bottom:3px;">
                                                            <?php echo e($cell->subject->name ?? 'All Subjects'); ?>

                                                        </div>
                                                        <?php if($cell->subject && $cell->subject->code): ?>
                                                            <div style="display:inline-block;background:var(--blue-pale);color:var(--blue);border-radius:4px;padding:1px 5px;font-size:9px;font-weight:700;margin-bottom:4px;">
                                                                <?php echo e($cell->subject->code); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                                            <span><i class="bi bi-diagram-2-fill" style="font-size:9px;"></i> <?php echo e($cell->section->name ?? '—'); ?></span>
                                                            <?php if($cell->room): ?>
                                                                <span style="color:#bbb;">·</span>
                                                                <span><i class="bi bi-geo-alt-fill" style="font-size:9px;"></i> <?php echo e($cell->room); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="height:52px;display:flex;align-items:center;justify-content:center;">
                                                        <span style="color:#e2e8f0;font-size:18px;font-weight:300;">—</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($timeSlots) === 0): ?>
                                    <tr>
                                        <td style="background:#f8fafc;padding:12px 14px;border-right:2px solid var(--border);"></td>
                                        <?php $__currentLoopData = $showDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td style="padding:40px 10px;text-align:center;color:#d1d5db;font-size:11px;">
                                                —
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(count($timeSlots) === 0): ?>
                        <div style="padding:18px 20px;background:#f8fafc;border-top:1px solid var(--border);text-align:center;font-size:12px;color:var(--muted);">
                            <i class="bi bi-info-circle me-1"></i> No schedules assigned yet. Contact admin to set up your teaching schedule.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar: Advisory + Today -->
            <div class="col-lg-4" style="display:flex;flex-direction:column;gap:16px;">

                <!-- Advisory Class Card -->
                <div class="content-card" style="overflow:hidden;">
                    <div style="background:linear-gradient(135deg,var(--blue) 0%,var(--blue-light) 100%);padding:20px;text-align:center;position:relative;">
                        <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
                        <div style="position:absolute;bottom:-30px;left:-10px;width:80px;height:80px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
                        <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,0.15);border:3px solid rgba(255,255,255,0.3);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;position:relative;z-index:1;">
                            <i class="bi bi-shield-fill-check" style="font-size:28px;color:#fff;"></i>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;position:relative;z-index:1;">Advisory Class</div>
                        <?php if($adviserSections->count() > 0): ?>
                            <?php $__currentLoopData = $adviserSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $advSec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="font-size:<?php echo e($adviserSections->count() > 1 ? '15px' : '20px'); ?>;font-weight:700;color:#fff;position:relative;z-index:1;margin-bottom:2px;">
                                    <?php echo e($advSec->name); ?>

                                </div>
                                <div style="font-size:12px;color:rgba(255,255,255,0.75);margin-bottom:<?php echo e(!$loop->last ? '6px' : '0'); ?>;position:relative;z-index:1;">
                                    <?php echo e($gradeLabels[$advSec->grade_level] ?? ucfirst($advSec->grade_level)); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div style="font-size:16px;font-weight:600;color:rgba(255,255,255,0.6);position:relative;z-index:1;">Not Assigned</div>
                        <?php endif; ?>
                    </div>
                    <div style="padding:16px;">
                        <?php if($adviserSections->count() > 0): ?>
                            <?php $__currentLoopData = $adviserSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $advSec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f0f0f0;">
                                    <span style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                                        <i class="bi bi-diagram-3-fill" style="color:var(--blue);"></i> <?php echo e($advSec->name); ?>

                                    </span>
                                    <span style="font-size:13px;font-weight:700;color:var(--text);"><?php echo e($advSec->students->count()); ?> students</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f0f0f0;margin-top:2px;">
                                <span style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-people-fill" style="color:var(--blue);"></i> Total Students
                                </span>
                                <span style="font-size:14px;font-weight:700;color:var(--text);"><?php echo e($adviserStudentCount); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;margin-bottom:12px;">
                                <span style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-calendar-check-fill" style="color:var(--blue);"></i> School Year
                                </span>
                                <span style="font-size:12px;font-weight:600;color:var(--text);"><?php echo e($currentSchoolYear); ?></span>
                            </div>
                            <button onclick="showSection('students')" class="btn-dash btn-primary" style="width:100%;justify-content:center;">
                                <i class="bi bi-people-fill"></i> View Class List
                            </button>
                        <?php else: ?>
                            <div style="text-align:center;padding:12px 0;">
                                <div style="font-size:12px;color:var(--muted);">No advisory class has been assigned to you yet.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Today's Classes Card -->
                <div class="content-card">
                    <div class="content-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                        <h6 style="display:flex;align-items:center;gap:7px;">
                            <i class="bi bi-sunrise-fill" style="color:var(--gold);"></i>
                            Today's Classes
                        </h6>
                        <span style="font-size:11px;color:var(--muted);font-weight:600;"><?php echo e(now()->format('l, M j')); ?></span>
                    </div>
                    <div style="padding:0 4px 4px;">
                        <?php if($todaySchedules->isEmpty()): ?>
                            <div style="text-align:center;padding:24px 16px;color:var(--muted);">
                                <i class="bi bi-check-circle" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.35;"></i>
                                <div style="font-size:12px;">No classes scheduled for today.</div>
                            </div>
                        <?php else: ?>
                            <?php $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $dc = $dayColors[$todayName] ?? ['bg'=>'#1a3a6c','light'=>'#e8f0fb','text'=>'#1a3a6c'];
                                    $startDt = \Carbon\Carbon::parse($ts->start_time);
                                    $endDt   = \Carbon\Carbon::parse($ts->end_time);
                                    $nowTime = now();
                                    $isActive = $nowTime->between($startDt, $endDt);
                                    $isPast   = $nowTime->gt($endDt);
                                ?>
                                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;margin:4px 0;border-radius:8px;
                                    <?php echo e($isActive ? 'background:'.$dc['light'].';border:1.5px solid '.$dc['border'].';' : ($isPast ? 'background:#fafafa;opacity:0.65;' : 'background:#f8fafc;')); ?>">
                                    <div style="width:36px;text-align:center;flex-shrink:0;padding-top:2px;">
                                        <div style="font-size:11px;font-weight:700;color:<?php echo e($isActive ? $dc['text'] : 'var(--muted)'); ?>;"><?php echo e($startDt->format('g:i')); ?></div>
                                        <div style="font-size:9px;color:var(--muted);"><?php echo e($startDt->format('A')); ?></div>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:12px;font-weight:700;color:<?php echo e($isActive ? $dc['text'] : 'var(--text)'); ?>;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            <?php echo e($ts->subject->name ?? 'All Subjects'); ?>

                                            <?php if($isActive): ?><span style="font-size:9px;background:<?php echo e($dc['bg']); ?>;color:#fff;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;">NOW</span><?php endif; ?>
                                        </div>
                                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">
                                            <?php echo e($ts->section->name ?? '—'); ?>

                                            <?php if($ts->room): ?> · <?php echo e($ts->room); ?><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($isPast): ?>
                                        <i class="bi bi-check-circle-fill" style="color:#28a745;font-size:14px;flex-shrink:0;margin-top:2px;"></i>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-schedule -->

    <!-- ═══════════════════════════
         SECTION: MY STUDENTS
    ═══════════════════════════ -->
    <div id="section-students" class="dash-section" style="display:none;">

        <!-- ── VIEW 1: Assignment list ── -->
        <div id="ms-list-view">
            <div class="section-header">
                <div>
                    <h1>My Students</h1>
                    <p>Select a subject to encode grades.</p>
                </div>
                <?php if($draftCount > 0): ?>
                    <div style="background:#fff8ec;border:1.5px solid #f5a623;border-radius:10px;padding:10px 18px;display:flex;align-items:center;gap:10px;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:var(--gold);font-size:18px;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--text);"><?php echo e($draftCount); ?> Draft(s) Pending</div>
                            <div style="font-size:11px;color:var(--muted);">Open a subject to review and submit.</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="content-card mb-3">
                <div class="p-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-lbl">School Year</label>
                            <select class="form-fld" id="ms-list-sy" onchange="syncFiltersToGradeView()">
                                <?php
                                    $baseSY = now()->month >= 6 ? now()->year : now()->year - 1;
                                    for ($y = $baseSY + 1; $y >= $baseSY - 2; $y--) {
                                        $sy = $y . '-' . ($y + 1);
                                        $selected = ($sy === $currentSchoolYear) ? 'selected' : '';
                                        echo "<option value=\"$sy\" $selected>$sy</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-lbl">Term</label>
                            <select class="form-fld" id="ms-list-term" onchange="syncFiltersToGradeView()">
                                <option value="1">Term 1</option>
                                <option value="2">Term 2</option>
                                <option value="3">Term 3</option>
                            </select>
                        </div>
                        <div class="col-md-4" style="display:flex;align-items:flex-end;">
                            <div style="background:#f0f4ff;border:1px solid #c7d7ff;border-radius:8px;padding:8px 12px;font-size:12px;color:var(--blue);font-weight:600;line-height:1.4;">
                                <i class="bi bi-arrow-right-circle-fill me-1"></i>
                                Clicking <strong>Enter Grades</strong> will open:<br>
                                <span id="ms-filter-preview" style="color:#1a3a6c;">Term 1 &nbsp;·&nbsp; S.Y. <?php echo e($currentSchoolYear); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="content-card-header"><h6>My Subject Assignments — S.Y. <?php echo e($currentSchoolYear); ?></h6></div>
                <div style="overflow-x:auto;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Grade Level &amp; Section</th>
                                <th>Subject</th>
                                <th>Students</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $teacherAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $sec          = $assignment->section;
                                    $sub          = $assignment->subject;
                                    $studentCount = $sec ? $sec->students->count() : 0;
                                    $glLabel      = $sec ? ($gradeLabels[$sec->grade_level] ?? ucfirst($sec->grade_level)) : '—';
                                ?>
                                <?php if($sec): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:700;color:var(--blue);font-size:13px;"><?php echo e($glLabel); ?></div>
                                        <div style="font-size:12px;color:var(--muted);"><?php echo e($sec->name); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;color:var(--text);"><?php echo e($sub?->name ?? 'All Subjects'); ?></div>
                                        <?php if($sub): ?><div class="user-row-sub"><?php echo e($sub->code); ?></div><?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight:700;color:var(--text);"><?php echo e($studentCount); ?></span>
                                        <span style="font-size:11px;color:var(--muted);"> students</span>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <button class="btn-dash btn-primary" style="padding:7px 12px;font-size:12px;margin-right:6px;"
                                            data-section-id="<?php echo e($sec->id); ?>"
                                            data-subject-id="<?php echo e($sub?->id ?? ''); ?>"
                                            data-subject-name="<?php echo e($sub?->name ?? 'All Subjects'); ?>"
                                            data-section-name="<?php echo e($glLabel); ?> — <?php echo e($sec->name); ?>"
                                            data-grade-level="<?php echo e($sec->grade_level ?? ''); ?>"
                                            onclick="openSubjectViewFromBtn(this)">
                                            <i class="bi bi-pencil-square"></i> Enter Grades
                                        </button>
                                        <button class="btn-dash btn-success" style="padding:7px 12px;font-size:12px;"
                                            data-section-id="<?php echo e($sec->id); ?>"
                                            data-subject-id="<?php echo e($sub?->id ?? ''); ?>"
                                            onclick="downloadTemplate(this)">
                                            <i class="bi bi-file-earmark-excel-fill"></i> Download Excel
                                        </button>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:40px;color:var(--muted);">
                                        <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                                        <div style="font-weight:600;margin-bottom:4px;">No schedule assigned yet.</div>
                                        <div style="font-size:12px;">Ask the admin to assign you a subject schedule. Advisory class alone does not give grade entry access.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /ms-list-view -->

        <!-- ── VIEW 2: Grade entry for a subject ── -->
        <div id="ms-grade-view" style="display:none;">
            <!-- Header: back + title, then filter row below -->
            <div style="margin-bottom:16px;">
                <!-- Row 1: Back button + Title -->
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px;">
                    <button class="btn-dash btn-secondary" style="padding:8px 14px;flex-shrink:0;" onclick="backToList()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <div>
                        <h1 id="ms-view-title" style="font-size:20px;margin:0;font-weight:700;color:var(--text);">—</h1>
                        <p id="ms-view-subtitle" style="margin:3px 0 0;font-size:12px;color:var(--muted);"></p>
                    </div>
                </div>
                <!-- Row 2: Year + Term tabs + Actions (same layout as student portal) -->
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:4px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;">

                    
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">School Year</label>
                        <select id="ms-sy" onchange="loadGradeView()"
                            style="border:1.5px solid #d1d5db;border-radius:8px;padding:7px 12px;font-size:13px;font-weight:600;color:var(--text);background:#fff;cursor:pointer;min-width:130px;">
                            <?php
                                $baseSY = now()->month >= 6 ? now()->year : now()->year - 1;
                                for ($y = $baseSY + 1; $y >= $baseSY - 2; $y--) {
                                    $sy = $y . '-' . ($y + 1);
                                    $selected = ($sy === $currentSchoolYear) ? 'selected' : '';
                                    echo "<option value=\"$sy\" $selected>$sy</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div style="width:1px;height:36px;background:#e5e7eb;"></div>

                    
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">Term</label>
                        <div style="display:flex;gap:6px;">
                            <button class="ms-term-tab active" onclick="selectMsTerm(1,this);return false;"
                                style="border:1.5px solid var(--blue);background:var(--blue);color:#fff;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                                Term 1
                            </button>
                            <button class="ms-term-tab" onclick="selectMsTerm(2,this);return false;"
                                style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                                Term 2
                            </button>
                            <button class="ms-term-tab" onclick="selectMsTerm(3,this);return false;"
                                style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                                Term 3
                            </button>
                        </div>
                    </div>

                    
                    <select id="ms-term" style="display:none;">
                        <option value="1">Term 1</option>
                        <option value="2">Term 2</option>
                        <option value="3">Term 3</option>
                    </select>

                    <div style="width:1px;height:36px;background:var(--border);"></div>

                    <button class="btn-dash btn-secondary" onclick="showDraftPreview()" title="View saved draft grades">
                        <i class="bi bi-eye"></i> Preview Drafts
                    </button>
                    <button class="btn-dash btn-secondary" onclick="downloadExportCSV()" title="Download grade template (.xlsx)">
                        <i class="bi bi-file-earmark-arrow-down"></i> Export Template
                    </button>
                    <button class="btn-dash btn-secondary" onclick="triggerImport()" title="Import grades from .xlsx or .csv">
                        <i class="bi bi-upload"></i> Import Grades
                    </button>
                    <input type="file" id="import-file" accept=".xlsx,.xls,.csv,.txt" style="display:none;" onchange="importGrades(this)">

                    <div style="width:1px;height:36px;background:var(--border);"></div>

                    <button class="btn-dash" style="background:#c0392b;color:#fff;" onclick="exportSF5()" title="Download SF5 — Report on Promotions (Excel)">
                        <i class="bi bi-file-earmark-spreadsheet"></i> SF5
                    </button>

                </div>
            </div>

            <!-- Draft notice -->
            <div id="ms-draft-notice" style="display:none;background:#fff8ec;border:1.5px solid #f5a623;border-radius:10px;padding:14px 18px;margin-bottom:16px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <i class="bi bi-pencil-square" style="color:var(--gold);font-size:20px;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--text);">Draft grades saved — review before submitting</div>
                            <div id="ms-draft-count-text" style="font-size:12px;color:var(--muted);margin-top:2px;"></div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        <button class="btn-dash btn-secondary" style="padding:8px 14px;" onclick="discardDraft()">
                            <i class="bi bi-x-circle"></i> Discard Draft
                        </button>
                        <button class="btn-dash btn-success" style="padding:8px 14px;" onclick="submitDraft()">
                            <i class="bi bi-check-circle-fill"></i> Submit Grades
                        </button>
                    </div>
                </div>
                <div style="margin-top:10px;padding:10px 14px;background:#fff;border-radius:8px;border:1px solid #f5a623;font-size:12px;color:#7f4f00;">
                    <strong>How to review:</strong>
                    Draft grades are shown in the <strong>Grade</strong> column with an <span style="background:#fff8ec;border:1px solid #f5a623;border-radius:4px;padding:1px 6px;font-weight:700;">amber highlight</span>.
                    You can still edit any grade in the input cells.
                    When you're satisfied, click <strong>Submit Grades</strong> to make them visible to students.
                </div>
            </div>

            <!-- Grade table -->
            <div class="content-card">
                <div class="content-card-header">
                    <h6 id="ms-table-title">Loading...</h6>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <span style="font-size:11px;color:var(--muted);">Enter grades below, then save as draft to review before submitting.</span>
                        <button id="btn-save-draft" class="btn-dash btn-secondary" style="padding:7px 16px;font-size:12px;" onclick="saveManualGrades()">
                            <i class="bi bi-floppy-fill"></i> Save as Draft
                        </button>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Student Name</th>
                                <th>LRN</th>
                                <th style="width:120px;">Grade (0–100)</th>
                                <th>Remarks</th>
                                <th>Draft</th>
                                <th style="width:60px;">SF9</th>
                            </tr>
                        </thead>
                        <tbody id="ms-grade-body">
                            <tr>
                                <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                                    <i class="bi bi-hourglass-split" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                                    Loading students...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Import errors -->
            <div id="ms-import-errors" style="display:none;margin-top:12px;background:#fdecea;border:1px solid var(--red);border-radius:8px;padding:14px 18px;">
                <div style="font-weight:700;color:var(--red);margin-bottom:6px;"><i class="bi bi-exclamation-triangle-fill"></i> Import Warnings</div>
                <ul id="ms-error-list" style="margin:0;padding-left:18px;font-size:12px;color:var(--text);"></ul>
            </div>
        </div><!-- /ms-grade-view -->

        <!-- ── Export Preview Modal ── -->
        <div id="ms-export-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1050;align-items:center;justify-content:center;" onclick="if(event.target===this)closeExportModal()">
            <div style="background:#fff;border-radius:16px;width:90%;max-width:780px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,0.18);overflow:hidden;">
                <!-- Modal header -->
                <div style="padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--text);" id="exp-modal-title">Grade Sheet Preview</div>
                        <div style="font-size:12px;color:var(--muted);" id="exp-modal-sub"></div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button class="btn-dash btn-primary" style="padding:8px 16px;" onclick="downloadExportCSV()">
                            <i class="bi bi-download"></i> Download CSV (Import-Ready)
                        </button>
                        <button onclick="closeExportModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:4px 8px;line-height:1;">&times;</button>
                    </div>
                </div>
                <!-- Modal body — scrollable table -->
                <div style="overflow-y:auto;padding:20px 24px;flex:1;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;" id="exp-preview-table">
                        <thead>
                            <tr style="background:#f0f4f8;">
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);width:36px;">#</th>
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);">Student Name</th>
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);">LRN</th>
                                <th style="padding:10px 12px;text-align:center;border-bottom:2px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);width:100px;">Grade</th>
                                <th style="padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="exp-preview-body">
                            <tr><td colspan="5" style="padding:40px;text-align:center;color:var(--muted);">Loading preview...</td></tr>
                        </tbody>
                    </table>
                </div>
                <!-- Modal footer -->
                <div style="padding:12px 24px;border-top:1px solid var(--border);background:#f8fafc;font-size:12px;color:var(--muted);flex-shrink:0;">
                    <i class="bi bi-info-circle"></i>
                    <strong>How to use:</strong> Click <em>Preview &amp; Export</em> → fill in the Grade column in Excel → save the file → click <em>Import Grades</em> → review the amber-highlighted grades → click <em>Submit Grades</em>.
                    Do <strong>not</strong> change student_id, LRN, or Name columns.
                </div>
            </div>
        </div>

    </div><!-- /section-students -->

    <!-- ═══════════════════════════
         SECTION: ATTENDANCE
    ═══════════════════════════ -->
    <div id="section-attendance" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Attendance</h1>
                <p>Mark and track student attendance per class.</p>
            </div>
            <button class="btn-dash btn-primary" onclick="saveAttendance()">
                <i class="bi bi-floppy-fill"></i> Save Attendance
            </button>
        </div>

        <div class="content-card mb-4">
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-lbl">Section</label>
                        <select class="form-fld" id="att-section" onchange="onAttSectionChange()">
                            <option value="">— Select Section —</option>
                            <?php $__currentLoopData = $sections ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sec->id); ?>"><?php echo e($sec->name); ?> (<?php echo e($gradeLabels[$sec->grade_level] ?? ucfirst($sec->grade_level)); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Subject</label>
                        <select class="form-fld" id="att-subject">
                            <option value="">— Select Subject —</option>
                            <?php $__currentLoopData = $subjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($teacherAssignments->whereNull('subject_id')->count() > 0): ?>
                                <option value="all-subjects">All Subjects (Nursery/Kindergarten)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-lbl">Date</label>
                        <input type="date" class="form-fld" id="att-date" value="<?php echo e(now()->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3" style="display:flex;align-items:flex-end;">
                        <button class="btn-dash btn-primary" style="width:100%;" onclick="loadAttendance()">
                            <i class="bi bi-search"></i> Load
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <h6 id="att-title">Select a section and date to load attendance</h6>
                <div style="display:flex;gap:10px;font-size:11px;align-items:center;">
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:var(--green);border-radius:3px;display:inline-block;"></span>Present</span>
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:var(--red);border-radius:3px;display:inline-block;"></span>Absent</span>
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:var(--gold);border-radius:3px;display:inline-block;"></span>Late</span>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="att-body">
                        <tr>
                            <td colspan="3" style="text-align:center; padding:40px; color:var(--muted);">
                                <i class="bi bi-clipboard-check" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>
                                Use the filters above to load attendance records.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="att-actions" style="padding:14px 20px;border-top:1px solid var(--border);display:none;gap:10px;justify-content:flex-end;">
                <button class="btn-dash btn-primary" onclick="saveAttendance()"><i class="bi bi-floppy-fill"></i> Save Attendance</button>
            </div>
        </div>
    </div><!-- /section-attendance -->

    <!-- ═══════════════════════════
         SECTION: ANNOUNCEMENTS
    ═══════════════════════════ -->
    <div id="section-announcements" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Announcements</h1>
                <p>Send announcements to students and parents.</p>
            </div>
        </div>

        <div class="content-card mb-4">
            <div class="content-card-header"><h6>Post New Announcement</h6></div>
            <div class="p-4">
                
                <form method="POST" action="#">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-lbl">Title *</label>
                            <input type="text" name="title" class="form-fld" placeholder="Announcement title">
                        </div>
                        <div class="col-md-3">
                            <label class="form-lbl">Target Audience</label>
                            <select name="audience" class="form-fld">
                                <option value="all">All My Students</option>
                                <option value="gr7">Grade 7 – Sampaguita</option>
                                <option value="gr8">Grade 8 – Rosal</option>
                                <option value="gr10">Grade 10 – Ilang-ilang</option>
                                <option value="parents">Parents Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-lbl">Category</label>
                            <select name="category" class="form-fld">
                                <option>Academic</option>
                                <option>Reminder</option>
                                <option>Activity</option>
                                <option>General</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">Content *</label>
                            <textarea name="content" class="form-fld" placeholder="Write your announcement here..."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-send-fill"></i> Post Announcement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header"><h6>My Posted Announcements</h6></div>
            <div style="padding:40px;text-align:center;color:var(--muted);">
                <i class="bi bi-megaphone" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                
                No announcements posted yet.
            </div>
        </div>
    </div><!-- /section-announcements -->

    <!-- ═══════════════════════════
         SECTION: PARENT-TEACHER CONF.
    ═══════════════════════════ -->
    <div id="section-ptc" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Parent-Teacher Conference</h1>
                <p>Schedule and manage parent-teacher meetings.</p>
            </div>
            <a href="#" class="btn-dash btn-primary">
                <i class="bi bi-plus-lg"></i> Schedule Meeting
            </a>
        </div>

        <div class="content-card mb-4">
            <div class="content-card-header"><h6>Schedule a Parent-Teacher Meeting</h6></div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-lbl">Student</label>
                        <select class="form-fld">
                            <option value="">Select Student</option>
                            <option>Juan Dela Cruz</option>
                            <option>Maria Santos</option>
                            <option>Ramon Lopez</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-lbl">Date</label>
                        <input type="date" class="form-fld">
                    </div>
                    <div class="col-md-4">
                        <label class="form-lbl">Time</label>
                        <input type="time" class="form-fld">
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Purpose / Concern</label>
                        <input type="text" class="form-fld" placeholder="e.g., Academic performance, Behavior concern">
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Venue</label>
                        <input type="text" class="form-fld" placeholder="e.g., Room 101, Principal's Office">
                    </div>
                    <div class="col-12">
                        <label class="form-lbl">Notes</label>
                        <textarea class="form-fld" placeholder="Additional notes for the parent..."></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn-dash btn-primary">
                            <i class="bi bi-calendar-plus-fill"></i> Schedule Meeting
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header"><h6>Scheduled Meetings</h6></div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Parent / Guardian</th>
                            <th>Date &amp; Time</th>
                            <th>Purpose</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">
                                <i class="bi bi-calendar-event" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                                No meetings scheduled yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /section-ptc -->

    <!-- ═══════════════════════════
         SECTION: GRADE REPORTS
    ═══════════════════════════ -->
    <div id="section-reports" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Grade Reports</h1>
                <p>Generate and print grade reports for submission.</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-file-earmark-text-fill" style="font-size:36px;color:var(--blue);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">Class Grade Report</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Full grade report for a section and subject.</p>
                        <a href="#" class="btn-dash btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-person-lines-fill" style="font-size:36px;color:var(--green);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">Individual Report Card</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Per-student grade report for parents/registrar.</p>
                        <a href="#" class="btn-dash btn-success" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card">
                    <div class="p-4 text-center">
                        <i class="bi bi-bar-chart-fill" style="font-size:36px;color:var(--gold);display:block;margin-bottom:12px;"></i>
                        <h6 style="font-weight:700;color:var(--text);margin-bottom:6px;">Class Performance Summary</h6>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Analytics and performance overview per quarter.</p>
                        <a href="#" class="btn-dash btn-gold" style="width:100%;justify-content:center;"><i class="bi bi-download"></i> Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /section-reports -->

    <!-- ═══════════════════════════
         SECTION: SETTINGS
    ═══════════════════════════ -->
    <div id="section-settings" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1>Settings</h1>
                <p>Manage your account and preferences.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Account Info -->
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

                        
                        <form method="POST" action="<?php echo e(route('teacher.settings.photo')); ?>" enctype="multipart/form-data" id="photo-upload-form">
                            <?php echo csrf_field(); ?>
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                                
                                <div id="settings-avatar-wrap" style="position:relative;flex-shrink:0;width:72px;height:72px;">
                                    <?php if($teacher->profile_photo): ?>
                                        <img id="settings-avatar-img" src="<?php echo e(asset('storage/' . $teacher->profile_photo)); ?>" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
                                    <?php else: ?>
                                        <div id="settings-avatar-placeholder" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-person-fill" style="font-size:30px;color:#fff;"></i>
                                        </div>
                                        <img id="settings-avatar-img" src="" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);display:none;">
                                    <?php endif; ?>
                                    
                                    <label for="photo-file-input" title="Change photo"
                                           style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:var(--blue);border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                        <i class="bi bi-camera-fill" style="font-size:11px;color:#fff;"></i>
                                    </label>
                                    <input type="file" id="photo-file-input" name="photo" accept="image/jpeg,image/png,image/webp"
                                           style="display:none;" onchange="previewAndUploadPhoto(this)">
                                </div>
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:var(--text);"><?php echo e($teacher->name); ?></div>
                                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e($teacher->email); ?></div>
                                    <span style="display:inline-block;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px;">Teacher</span>
                                </div>
                            </div>
                            
                            <button type="submit" id="photo-submit-btn" style="display:none;"></button>
                        </form>

                        <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;flex-direction:column;gap:0;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Full Name</span>
                                <span style="font-size:12px;font-weight:600;"><?php echo e($teacher->name); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Email</span>
                                <span style="font-size:12px;font-weight:600;"><?php echo e($teacher->email); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Role</span>
                                <span style="font-size:12px;font-weight:600;">Teacher</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Total Classes</span>
                                <span style="font-size:12px;font-weight:600;"><?php echo e($schedules->count()); ?></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;">
                                <span style="font-size:12px;color:var(--muted);">Advisory Class</span>
                                <span style="font-size:12px;font-weight:600;text-align:right;">
                                    <?php if($adviserSections->count() > 0): ?>
                                        <?php $__currentLoopData = $adviserSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $advSec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e($advSec->name); ?> <span style="font-size:11px;color:var(--muted);">(<?php echo e($gradeLabels[$advSec->grade_level] ?? ''); ?>)</span></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span style="color:var(--muted);font-style:italic;">Not assigned</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
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
                        <?php if($errors->has('otp_code')): ?>
                            <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-exclamation-circle-fill"></i> <?php echo e($errors->first('otp_code')); ?>

                            </div>
                        <?php endif; ?>

                        
                        <div id="tch-pwd-step1" <?php if(session('otp_token')): ?> style="display:none;" <?php endif; ?>>
                            <div style="background:#e8f0fb;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#1a3a6c;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-shield-lock-fill" style="font-size:16px;"></i>
                                For your security, a 6-digit OTP will be sent to your email to confirm the change.
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Current Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="tch-cur-pwd" class="form-fld" placeholder="Enter your current password">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="tch-new-pwd" class="form-fld" placeholder="At least 8 characters" minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Confirm New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="tch-confirm-pwd" class="form-fld" placeholder="Re-enter new password">
                            </div>
                            <div id="tch-pwd-error" style="display:none;background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#c0392b;"></div>
                            <button type="button" class="btn-dash btn-primary" id="tch-send-otp-btn" onclick="teacherSendOtp()">
                                <i class="bi bi-envelope-fill"></i> Send OTP to Email
                            </button>
                        </div>

                        
                        <div id="tch-pwd-step2" <?php if(!session('otp_token')): ?> style="display:none;" <?php endif; ?>>
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#2e7d32;">
                                <i class="bi bi-envelope-check-fill"></i>
                                OTP sent to <strong id="tch-otp-email">your email</strong>. Enter the 6-digit code below. Expires in 10 minutes.
                            </div>
                            <form method="POST" action="<?php echo e(route('teacher.settings.password')); ?>" id="tch-otp-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="otp_token" id="tch-otp-token" value="<?php echo e(session('otp_token')); ?>">
                                <div class="mb-3">
                                    <label class="form-lbl">6-Digit OTP Code <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="otp_code" class="form-fld" placeholder="e.g. 123456"
                                        maxlength="6" inputmode="numeric" pattern="\d{6}" required
                                        style="font-size:22px;letter-spacing:10px;font-weight:700;text-align:center;">
                                </div>
                                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                    <button type="submit" class="btn-dash btn-primary">
                                        <i class="bi bi-lock-fill"></i> Confirm &amp; Change Password
                                    </button>
                                    <button type="button" class="btn-dash" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;" onclick="teacherResetOtpStep()">
                                        <i class="bi bi-arrow-left"></i> Start Over
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                    function teacherSendOtp() {
                        var cur  = document.getElementById('tch-cur-pwd').value;
                        var npwd = document.getElementById('tch-new-pwd').value;
                        var conf = document.getElementById('tch-confirm-pwd').value;
                        var err  = document.getElementById('tch-pwd-error');

                        err.style.display = 'none';
                        if (!cur || !npwd || !conf) { err.textContent = 'Please fill in all fields.'; err.style.display='block'; return; }
                        if (npwd.length < 8)        { err.textContent = 'New password must be at least 8 characters.'; err.style.display='block'; return; }
                        if (npwd !== conf)           { err.textContent = 'Passwords do not match.'; err.style.display='block'; return; }

                        var btn = document.getElementById('tch-send-otp-btn');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending…';

                        fetch('<?php echo e(route('teacher.settings.password.otp')); ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                            body: JSON.stringify({ current_password: cur, password: npwd, password_confirmation: conf })
                        })
                        .then(r => r.json())
                        .then(d => {
                            if (d.success) {
                                document.getElementById('tch-otp-token').value = d.token;
                                document.getElementById('tch-otp-email').textContent = d.email;
                                document.getElementById('tch-pwd-step1').style.display = 'none';
                                document.getElementById('tch-pwd-step2').style.display = 'block';
                            } else {
                                err.textContent = d.message || 'Failed to send OTP.';
                                err.style.display = 'block';
                            }
                        })
                        .catch(() => { err.textContent = 'Network error. Please try again.'; err.style.display = 'block'; })
                        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-envelope-fill"></i> Send OTP to Email'; });
                    }

                    function teacherResetOtpStep() {
                        document.getElementById('tch-pwd-step2').style.display = 'none';
                        document.getElementById('tch-pwd-step1').style.display = 'block';
                        document.getElementById('tch-cur-pwd').value = '';
                        document.getElementById('tch-new-pwd').value = '';
                        document.getElementById('tch-confirm-pwd').value = '';
                    }
                    </script>
                </div>
            </div>
        </div>
    </div><!-- /section-settings -->

</div><!-- /dash-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sections = ['dashboard','schedule','students','attendance','announcements','ptc','reports','settings'];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';

    function showSection(name) {
        sections.forEach(s => {
            document.getElementById('section-' + s).style.display = s === name ? '' : 'none';
            const nav = document.getElementById('nav-' + s);
            if (nav) nav.classList.toggle('active', s === name);
        });
        window.scrollTo(0, 0);
        applySectionSkeleton(name);
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

    // Auto-select first option in a select if only one real option exists
    function autoSelectFirst(selectId) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        const options = sel.querySelectorAll('option[value]:not([value=""])');
        if (options.length === 1) sel.value = options[0].value;
    }

    function onAttSectionChange() { autoSelectFirst('att-subject'); }

    document.addEventListener('DOMContentLoaded', function() {
        autoSelectFirst('att-section');
        autoSelectFirst('att-subject');
        <?php if(session('settings_tab') || session('password_success') || session('photo_success') || $errors->has('current_password')): ?>
        showSection('settings');
        <?php else: ?>
        // Default section is 'dashboard' — trigger skeleton on initial page load
        applySectionSkeleton('dashboard');
        <?php endif; ?>
    });

    function previewAndUploadPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // Show live preview before submitting
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('settings-avatar-img');
            const placeholder = document.getElementById('settings-avatar-placeholder');
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';

            // Also update the topbar chip avatar
            const chipAvatar = document.querySelector('.user-avatar');
            if (chipAvatar) {
                chipAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
                chipAvatar.style.background = 'none';
                chipAvatar.style.overflow = 'hidden';
            }

            // Submit the form automatically
            document.getElementById('photo-upload-form').submit();
        };
        reader.readAsDataURL(file);
    }

    // ═══════════════════════════════
    // MY STUDENTS — GRADE ENTRY
    // ═══════════════════════════════
    let msCurrentSection     = null;
    let msCurrentSubject     = null;
    let msCurrentSubjectName = '';
    let msCurrentSectionLabel = '';
    let msCurrentGradeLevel  = '';
    let msIsDescriptive      = false;
    const DESCRIPTIVE_LEVELS = ['nursery', 'kindergarten'];

    function downloadTemplate(btn) {
        const sectionId  = btn.dataset.sectionId;
        const subjectId  = btn.dataset.subjectId;
        const schoolYear = document.getElementById('ms-list-sy').value;
        const term       = document.getElementById('ms-list-term').value;
        const params = new URLSearchParams({ section_id: sectionId, term: term, school_year: schoolYear });
        if (subjectId) params.set('subject_id', subjectId);
        window.location.href = '/teacher/grades/template?' + params.toString();
    }

    // Switch term tab in the grade view and immediately reload
    function selectMsTerm(term, btn) {
        document.getElementById('ms-term').value = term;
        document.querySelectorAll('.ms-term-tab').forEach(function(b) {
            var isActive = b === btn;
            b.style.background  = isActive ? 'var(--blue)' : '#fff';
            b.style.color       = isActive ? '#fff' : '#555';
            b.style.borderColor = isActive ? 'var(--blue)' : '#d1d5db';
            b.classList.toggle('active', isActive);
        });
        loadGradeView();
    }

    // Keep the grade-view selectors in sync with list-view selectors
    function syncFiltersToGradeView() {
        const sy   = document.getElementById('ms-list-sy').value;
        const term = document.getElementById('ms-list-term').value;
        document.getElementById('ms-sy').value   = sy;
        document.getElementById('ms-term').value = term;

        // Sync the term tab button active state
        document.querySelectorAll('.ms-term-tab').forEach(function(b, idx) {
            var isActive = (idx + 1) === parseInt(term);
            b.style.background  = isActive ? 'var(--blue)' : '#fff';
            b.style.color       = isActive ? '#fff' : '#555';
            b.style.borderColor = isActive ? 'var(--blue)' : '#d1d5db';
            b.classList.toggle('active', isActive);
        });

        // Update the live preview indicator in list view
        const preview = document.getElementById('ms-filter-preview');
        if (preview) {
            const termLabels = { '1': 'Term 1', '2': 'Term 2', '3': 'Term 3' };
            preview.textContent = (termLabels[term] || 'Term ' + term) + ' · S.Y. ' + sy;
        }

        // If the grade view is already open, reload it with the new filters
        const gradeView = document.getElementById('ms-grade-view');
        if (gradeView && gradeView.style.display !== 'none' && msCurrentSection) {
            loadGradeView();
        }
    }

    function openSubjectViewFromBtn(btn) {
        const sectionId   = parseInt(btn.dataset.sectionId);
        const subjectId   = btn.dataset.subjectId ? parseInt(btn.dataset.subjectId) : null;
        const subjectName = btn.dataset.subjectName;
        const sectionName = btn.dataset.sectionName;
        const gradeLevel  = btn.dataset.gradeLevel || '';
        openSubjectView(sectionId, subjectId, subjectName, sectionName, gradeLevel);
    }

    function openSubjectView(sectionId, subjectId, subjectName, sectionName, gradeLevel) {
        msCurrentSection     = sectionId;
        msCurrentSubject     = subjectId;
        msCurrentSubjectName = subjectName || 'All Subjects';
        msCurrentSectionLabel = sectionName;
        msCurrentGradeLevel  = gradeLevel || '';
        msIsDescriptive      = DESCRIPTIVE_LEVELS.includes(msCurrentGradeLevel);
        // Sync filters before opening
        syncFiltersToGradeView();
        const sy   = document.getElementById('ms-sy').value;
        const term = document.getElementById('ms-term').value;
        // Title = section context; subtitle = subject + SY + term
        document.getElementById('ms-view-title').textContent = sectionName;
        document.getElementById('ms-view-subtitle').textContent =
            msCurrentSubjectName + ' · S.Y. ' + sy + ' · Term ' + term;
        document.getElementById('ms-list-view').style.display = 'none';
        document.getElementById('ms-grade-view').style.display = '';
        loadGradeView();
    }

    function backToList() {
        closeExportModal();
        document.getElementById('ms-grade-view').style.display = 'none';
        document.getElementById('ms-list-view').style.display = '';
        // Sync list filters back
        document.getElementById('ms-list-sy').value   = document.getElementById('ms-sy').value;
        document.getElementById('ms-list-term').value = document.getElementById('ms-term').value;
        msCurrentSection     = null;
        msCurrentSubject     = null;
        msCurrentSubjectName = '';
        msCurrentSectionLabel = '';
    }

    function loadGradeView() {
        if (!msCurrentSection) return;
        const term = parseInt(document.getElementById('ms-term').value);
        const schoolYear = document.getElementById('ms-sy').value;
        // Keep subtitle in sync whenever term or SY changes
        document.getElementById('ms-view-subtitle').textContent =
            msCurrentSubjectName + ' · S.Y. ' + schoolYear + ' · Term ' + term;
        const tbody = document.getElementById('ms-grade-body');
        const title = document.getElementById('ms-table-title');
        title.textContent = 'Loading...';
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted);"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
        hideDraftNotice();

        fetch('/teacher/grades/load-class', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ section_id: msCurrentSection, subject_id: msCurrentSubject, term: term, school_year: schoolYear })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--red);">' + (data.message || 'Error loading students.') + '</td></tr>';
                return;
            }
            const termText = ['', 'Term 1', 'Term 2', 'Term 3'][term];
            const subjectLabel = msCurrentSubjectName || 'All Subjects';
            title.textContent = termText + ' — ' + data.section + ' · ' + subjectLabel;

            if (!data.data || !data.data.length) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>No students in this section.</td></tr>';
                return;
            }

            // Use is_descriptive flag from server (authoritative) or fall back to client-side check
            const isDesc = (data.is_descriptive !== undefined) ? data.is_descriptive : msIsDescriptive;
            const DESCRIPTIVE_OPTIONS = [
                { value:'O',    label:'O — Outstanding' },
                { value:'VS',   label:'VS — Very Satisfactory' },
                { value:'S',    label:'S — Satisfactory' },
                { value:'FS',   label:'FS — Fairly Satisfactory' },
                { value:'DNME', label:'DNME — Did Not Meet Expectations' },
            ];
            const DESCRIPTIVE_LABELS = { O:'Outstanding', VS:'Very Satisfactory', S:'Satisfactory', FS:'Fairly Satisfactory', DNME:'Did Not Meet Expectations' };

            let html = '';
            data.data.forEach(function(s, i) {
                const submittedG  = (s.grade !== '' && s.grade !== null && s.grade !== undefined) ? String(s.grade) : '';
                const hasDraft    = s.has_draft;
                const draftG      = (s.draft_grade !== null && s.draft_grade !== undefined && s.draft_grade !== '') ? String(s.draft_grade) : null;
                const inputVal    = submittedG !== '' ? submittedG : (draftG !== null ? draftG : '');
                const isDraftFill = submittedG === '' && draftG !== null;

                let inputHtml, remarks;

                if (isDesc) {
                    // ── Descriptive dropdown (Nursery / Kinder) ──
                    const opts = DESCRIPTIVE_OPTIONS.map(function(o) {
                        const sel = inputVal === o.value ? ' selected' : '';
                        return '<option value="' + o.value + '"' + sel + '>' + o.label + '</option>';
                    }).join('');
                    const selStyle = isDraftFill ? 'border-color:#f5a623;background:#fff8ec;font-weight:700;' : '';
                    inputHtml = '<select class="gradebook-input" style="width:100%;font-size:12px;' + selStyle + '" onchange="updateDescriptiveRemarks(this)">'
                        + '<option value="">— Select —</option>' + opts + '</select>';
                    remarks = inputVal ? (DESCRIPTIVE_LABELS[inputVal] || inputVal) : '';
                } else {
                    // ── Numeric input (Grade 1–6) ──
                    const gradeNum   = inputVal !== '' ? parseFloat(inputVal) : NaN;
                    const gradeClass = inputVal !== '' ? (gradeNum >= 75 ? 'passed' : 'failed') : '';
                    const selStyle   = isDraftFill ? 'background:#fff8ec;border-color:#f5a623;font-weight:700;' : '';
                    inputHtml = '<input type="number" class="gradebook-input ' + gradeClass + '" style="' + selStyle + '" value="' + inputVal + '" min="0" max="100" step="0.01" placeholder="—" oninput="updateRemarks(this)">';
                    remarks = submittedG !== '' ? (s.remarks || '') : (draftG !== null ? (parseFloat(draftG) >= 75 ? 'Passed' : 'Failed') : '');
                }

                // Draft badge
                let draftBadge = hasDraft && draftG
                    ? '<span class="status-badge draft"><i class="bi bi-pencil-fill"></i> ' + draftG + ' (draft)</span>'
                    : (hasDraft ? '<span class="status-badge draft"><i class="bi bi-pencil"></i> blank</span>' : '<span style="color:var(--muted);">—</span>');

                // Admin status badge
                let statusBadge = '';
                if (s.grade_status === 'approved')
                    statusBadge = '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;background:#e8f5e9;color:#2e7d32;"><i class="bi bi-patch-check-fill"></i> Approved</span>';
                else if (s.grade_status === 'submitted')
                    statusBadge = '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;background:#fff8e1;color:#f57c00;"><i class="bi bi-hourglass-split"></i> Pending Review</span>';
                else if (s.grade_status === 'rejected')
                    statusBadge = '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;background:#ffebee;color:#c62828;"><i class="bi bi-arrow-return-left"></i> Returned</span>';

                const remarksBadgeColor = isDesc
                    ? (inputVal && inputVal !== 'DNME' ? 'submitted' : 'locked')
                    : (inputVal !== '' && parseFloat(inputVal) >= 75 ? 'submitted' : 'locked');

                const sf9Url = '<?php echo e(route("teacher.sf9", ":sid")); ?>'.replace(':sid', s.student_id)
                    + '?school_year=' + encodeURIComponent(schoolYear);
                html += '<tr data-student-id="' + s.student_id + '" data-enrollment-id="' + (s.enrollment_id || '') + '">'
                    + '<td style="color:var(--muted);font-size:12px;">' + (i+1) + '</td>'
                    + '<td><div style="font-weight:600;font-size:13px;">' + s.name + '</div>'
                    +   (isDraftFill ? '<div style="font-size:10px;color:#d68910;margin-top:2px;"><i class="bi bi-upload"></i> Imported draft — verify before submitting</div>' : '')
                    + '</td>'
                    + '<td style="font-size:12px;color:var(--muted);">' + (s.lrn || '—') + '</td>'
                    + '<td>' + inputHtml + '</td>'
                    + '<td class="remarks-cell" style="font-size:12px;">' + (remarks ? '<span class="status-badge ' + remarksBadgeColor + '">' + remarks + '</span>' : '<span style="color:var(--muted);">—</span>') + '</td>'
                    + '<td style="font-size:12px;">' + (statusBadge || draftBadge) + '</td>'
                    + '<td><a href="' + sf9Url + '" target="_blank" title="Download SF9 Report Card" style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;background:#c0392b;color:#fff;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill"></i> SF9</a></td>'
                    + '</tr>';
            });
            tbody.innerHTML = html;

            if (data.draft_count > 0) {
                showDraftNotice(data.draft_count);
            }
        })
        .catch(function(err) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--red);">Error loading students.</td></tr>';
            console.error(err);
        });
    }

    function updateRemarks(input) {
        const v = parseFloat(input.value);
        input.classList.remove('passed', 'failed');
        const td = input.closest('tr').querySelector('.remarks-cell');
        if (!isNaN(v) && input.value !== '') {
            input.classList.add(v >= 75 ? 'passed' : 'failed');
            const label = v >= 75 ? 'Passed' : (v >= 70 ? 'Passed with Remedial' : 'Failed');
            const badgeClass = v >= 75 ? 'submitted' : 'locked';
            td.innerHTML = '<span class="status-badge ' + badgeClass + '">' + label + '</span>';
        } else {
            td.innerHTML = '<span style="color:var(--muted);">—</span>';
        }
    }

    const DESCRIPTIVE_LABELS_JS = { O:'Outstanding', VS:'Very Satisfactory', S:'Satisfactory', FS:'Fairly Satisfactory', DNME:'Did Not Meet Expectations' };
    function updateDescriptiveRemarks(sel) {
        const v  = sel.value;
        const td = sel.closest('tr').querySelector('.remarks-cell');
        if (v) {
            const passed = v !== 'DNME';
            td.innerHTML = '<span class="status-badge ' + (passed ? 'submitted' : 'locked') + '">' + (DESCRIPTIVE_LABELS_JS[v] || v) + '</span>';
        } else {
            td.innerHTML = '<span style="color:var(--muted);">—</span>';
        }
    }

    function saveManualGrades() {
        if (!msCurrentSection) return;
        const term       = parseInt(document.getElementById('ms-term').value);
        const schoolYear = document.getElementById('ms-sy').value;
        const rows       = document.querySelectorAll('#ms-grade-body tr[data-student-id]');
        if (!rows.length) { showToast('No students loaded.', 'error'); return; }

        const grades = [];
        let filledCount = 0;
        rows.forEach(function(row) {
            let gradeVal = null, descriptiveVal = null;
            if (msIsDescriptive) {
                const sel = row.querySelector('select.gradebook-input');
                descriptiveVal = sel && sel.value ? sel.value : null;
                if (descriptiveVal) filledCount++;
            } else {
                const input = row.querySelector('input[type="number"]');
                gradeVal = input && input.value !== '' ? parseFloat(input.value) : null;
                if (gradeVal !== null) filledCount++;
            }
            grades.push({
                student_id:        parseInt(row.dataset.studentId),
                enrollment_id:     row.dataset.enrollmentId || null,
                subject_id:        msCurrentSubject,
                term:              term,
                grade:             gradeVal,
                descriptive_grade: descriptiveVal,
                school_year:       schoolYear,
            });
        });

        if (filledCount === 0) {
            showToast('Please enter at least one grade before saving as draft.', 'error');
            return;
        }

        const btn = document.getElementById('btn-save-draft');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

        fetch('/teacher/grades/save', {
            method:      'POST',
            headers:     { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body:        JSON.stringify({ section_id: msCurrentSection, grades: grades, draft: true })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerHTML = origHtml;
            if (data.success) {
                showToast(data.message || 'Draft saved!', 'warning');
                loadGradeView(); // reloads table + shows draft notice if draft_count > 0
            } else {
                showToast('Error: ' + (data.message || 'Failed to save draft.'), 'error');
            }
        })
        .catch(function() {
            btn.disabled  = false;
            btn.innerHTML = origHtml;
            showToast('Network error. Please try again.', 'error');
        });
    }

    let msExportData = []; // holds rows for download

    function triggerImport() {
        if (!msCurrentSection) { showToast('Please select a section first.', 'error'); return; }
        document.getElementById('import-file').click();
    }

    function showDraftPreview() {
        if (!msCurrentSection) { showToast('Please select a section first.', 'error'); return; }
        const term       = document.getElementById('ms-term').value;
        const schoolYear = document.getElementById('ms-sy').value;
        const titleEl    = document.getElementById('ms-view-title').textContent;

        // Populate modal header
        document.getElementById('exp-modal-title').textContent = 'Draft Preview — ' + titleEl;
        document.getElementById('exp-modal-sub').textContent   = 'S.Y. ' + schoolYear + ' · Term ' + term;

        // Read current rows from the grade table
        const rows = document.querySelectorAll('#ms-grade-body tr[data-student-id]');
        const tbody = document.getElementById('exp-preview-body');
        msExportData = [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:30px;text-align:center;color:var(--muted);">No students loaded. Open a subject first.</td></tr>';
        } else {
            let html = '';
            rows.forEach(function(row, i) {
                const name    = row.cells[1].querySelector('div')?.textContent.trim() || row.cells[1].textContent.trim();
                const lrn     = row.cells[2].textContent.trim();
                const gradeIn = row.querySelector('input[type="number"]');
                const grade   = gradeIn ? (gradeIn.value !== '' ? parseFloat(gradeIn.value) : '') : '';
                const remarksEl = row.querySelector('.remarks-cell span');
                const remarks = remarksEl ? remarksEl.textContent.trim() : '—';
                const studentId = row.dataset.studentId;

                msExportData.push({ studentId, lrn, name, grade, remarks });

                const gradeColor = grade !== '' ? (grade >= 75 ? '#27ae60' : '#e74c3c') : '#718096';
                const bgColor = (i % 2 === 0) ? '#fff' : '#f8fafc';
                html += '<tr style="background:' + bgColor + ';">'
                    + '<td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;color:#718096;">' + (i + 1) + '</td>'
                    + '<td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;font-weight:600;color:#2d3748;">' + name + '</td>'
                    + '<td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;color:#718096;font-size:12px;">' + (lrn || '—') + '</td>'
                    + '<td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;text-align:center;font-weight:700;font-size:15px;color:' + gradeColor + ';">' + (grade !== '' ? grade : '—') + '</td>'
                    + '<td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">'
                    +   (grade !== '' ? '<span style="background:' + (grade >= 75 ? '#e8f8f0' : '#fdecea') + ';color:' + (grade >= 75 ? '#27ae60' : '#e74c3c') + ';padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">' + remarks + '</span>' : '<span style="color:#718096;">—</span>')
                    + '</td>'
                    + '</tr>';
            });
            tbody.innerHTML = html;
        }

        // Show modal
        const modal = document.getElementById('ms-export-modal');
        modal.style.display = 'flex';
    }

    function closeExportModal() {
        document.getElementById('ms-export-modal').style.display = 'none';
    }

    function downloadExportCSV() {
        if (!msCurrentSection) return;
        const term       = document.getElementById('ms-term').value;
        const schoolYear = document.getElementById('ms-sy').value;
        const params = new URLSearchParams({ section_id: msCurrentSection, term: term, school_year: schoolYear });
        if (msCurrentSubject) params.set('subject_id', msCurrentSubject);
        // Open in same tab — browser will trigger download for .xlsx content-disposition
        window.location.href = '/teacher/grades/export?' + params.toString();
    }

    function exportSF5() {
        if (!msCurrentSection) { showToast('Please select a section first.', 'error'); return; }
        const schoolYear = document.getElementById('ms-sy').value;
        const params = new URLSearchParams({ section_id: msCurrentSection, school_year: schoolYear });
        window.location.href = '/teacher/sf5?' + params.toString();
    }

    function importGrades(input) {
        if (!input.files.length) return;
        if (!msCurrentSection) { showToast('No section selected. Please select a section before importing.', 'error'); input.value = ''; return; }

        // Snapshot section/subject at time of file selection to avoid race conditions
        const importSectionId  = msCurrentSection;
        const importSubjectId  = msCurrentSubject;
        const term             = document.getElementById('ms-term').value;
        const schoolYear       = document.getElementById('ms-sy').value;
        const importSubjectName= msCurrentSubjectName || 'All Subjects';

        const form = new FormData();
        form.append('file', input.files[0]);
        form.append('section_id', importSectionId);
        form.append('term', term);
        form.append('school_year', schoolYear);
        if (importSubjectId) form.append('subject_id', importSubjectId);
        form.append('_token', csrfToken);
        input.value = '';

        showToast('Importing grades for ' + importSubjectName + ' (Term ' + term + ')…', 'warning');

        fetch('/teacher/grades/import', { method: 'POST', credentials: 'same-origin', body: form })
        .then(r => r.json())
        .then(data => {
            document.getElementById('ms-import-errors').style.display = 'none';
            if (!data.success) { showToast('Import failed: ' + (data.message || 'Unknown error.'), 'error'); return; }
            showToast(data.message || 'Draft saved!', 'warning');
            if (data.errors && data.errors.length) {
                const ul = document.getElementById('ms-error-list');
                ul.innerHTML = data.errors.map(e => '<li>' + e + '</li>').join('');
                document.getElementById('ms-import-errors').style.display = '';
            }
            // Only reload if still viewing the same section
            if (msCurrentSection === importSectionId) loadGradeView();
        })
        .catch(function() { showToast('Network error during import.', 'error'); });
    }

    function showDraftNotice(count) {
        const notice = document.getElementById('ms-draft-notice');
        document.getElementById('ms-draft-count-text').textContent = count + ' student grade(s) in draft. Review below, then submit.';
        notice.style.display = 'flex';
    }

    function hideDraftNotice() {
        document.getElementById('ms-draft-notice').style.display = 'none';
    }

    function submitDraft() {
        if (!msCurrentSection) return;
        const term       = document.getElementById('ms-term').value;
        const schoolYear = document.getElementById('ms-sy').value;
        fetch('/teacher/grades/submit-draft', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ section_id: msCurrentSection, subject_id: msCurrentSubject, term: term, school_year: schoolYear })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { showToast(data.message || 'Grades submitted!', 'success'); loadGradeView(); }
            else alert('Error: ' + (data.message || 'Submit failed.'));
        });
    }

    function discardDraft() {
        if (!confirm('Discard all draft grades for this subject and term?')) return;
        if (!msCurrentSection) return;
        const term       = document.getElementById('ms-term').value;
        const schoolYear = document.getElementById('ms-sy').value;
        fetch('/teacher/grades/discard-draft', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ section_id: msCurrentSection, subject_id: msCurrentSubject, term: term, school_year: schoolYear })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { showToast('Draft discarded.', 'warning'); loadGradeView(); }
            else alert('Error: ' + (data.message || 'Discard failed.'));
        });
    }

    function showToast(msg, type) {
        const colors = { success: 'var(--green)', warning: 'var(--gold)', error: 'var(--red)' };
        const t = document.createElement('div');
        t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:' + (colors[type]||colors.success) + ';color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,0.15);transition:opacity 0.3s;';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    // Attendance toggle
    function setAtt(el, type) {
        const row = el.parentElement;
        row.querySelectorAll('.att-day').forEach(d => { d.className = 'att-day none'; });
        el.className = 'att-day ' + type;
    }

    // ═══════════════════════════════
    // ATTENDANCE
    // ═══════════════════════════════
    function loadAttendance() {
        const sectionId = document.getElementById('att-section').value;
        const subjectVal = document.getElementById('att-subject').value;
        const date = document.getElementById('att-date').value;
        if (!sectionId || !date) { alert('Please select a section and date first.'); return; }

        const subjectId = (!subjectVal || subjectVal === 'all-subjects') ? null : subjectVal;

        const tbody = document.getElementById('att-body');
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:30px;color:var(--muted);"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';

        fetch('/teacher/attendance/load', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify({ section_id: sectionId, subject_id: subjectId, date: date })
        })
        .then(r => { if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'Server error'); }); return r.json(); })
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:40px;color:var(--red);"><i class="bi bi-exclamation-triangle" style="font-size:24px;display:block;margin-bottom:8px;"></i>' + (data.message || 'Error loading attendance.') + '</td></tr>';
                document.getElementById('att-actions').style.display = 'none';
                return;
            }
            if (!data.data || !data.data.length) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>No students found in this section.</td></tr>';
                document.getElementById('att-actions').style.display = 'none';
                return;
            }
            const sectionText = document.getElementById('att-section').selectedOptions[0].text;
            document.getElementById('att-title').textContent = 'Attendance — ' + sectionText + ' · ' + date;

            let html = '';
            data.data.forEach(function(student) {
                const initials = student.name.split(' ').map(function(n){return n[0];}).join('').substring(0,2).toUpperCase();
                const status = student.status || '';
                html += '<tr data-student-id="' + student.student_id + '">'
                    + '<td><div class="user-row-name"><div class="user-row-avatar">' + initials + '</div><div><div style="font-weight:600;font-size:13px;">' + student.name + '</div><div class="user-row-sub">' + (student.lrn || 'N/A') + '</div></div></div></td>'
                    + '<td><div style="display:flex;gap:6px;">'
                    + '<div class="att-day ' + (status === 'present' ? 'present' : 'none') + '" onclick="setAtt(this,\'present\')" title="Present">P</div>'
                    + '<div class="att-day ' + (status === 'absent' ? 'absent' : 'none') + '" onclick="setAtt(this,\'absent\')" title="Absent">A</div>'
                    + '<div class="att-day ' + (status === 'late' ? 'late' : 'none') + '" onclick="setAtt(this,\'late\')" title="Late">L</div>'
                    + '<div class="att-day ' + (status === 'excused' ? 'excused' : 'none') + '" onclick="setAtt(this,\'excused\')" title="Excused">E</div>'
                    + '</div></td>'
                    + '<td><input type="text" class="form-fld" style="padding:6px 10px;font-size:12px;" data-field="remarks" value="' + (student.remarks || '') + '" placeholder="Optional remarks..."></td>'
                    + '</tr>';
            });
            tbody.innerHTML = html;
            document.getElementById('att-actions').style.display = 'flex';
        })
        .catch(function(err) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:30px;color:var(--red);">Error loading attendance.</td></tr>';
            console.error(err);
        });
    }

    function saveAttendance() {
        const sectionId = document.getElementById('att-section').value;
        const subjectVal = document.getElementById('att-subject').value;
        const date = document.getElementById('att-date').value;
        if (!sectionId || !date) { alert('Please select a section and date first.'); return; }

        const subjectId = (!subjectVal || subjectVal === 'all-subjects') ? null : subjectVal;

        const rows = document.querySelectorAll('#att-body tr[data-student-id]');
        if (!rows.length) { alert('No attendance records to save. Load attendance first.'); return; }

        const records = [];
        rows.forEach(function(row) {
            const studentId = row.dataset.studentId;
            const activeDay = row.querySelector('.att-day.present, .att-day.absent, .att-day.late, .att-day.excused');
            const status = activeDay ? (activeDay.classList.contains('present') ? 'present' : activeDay.classList.contains('absent') ? 'absent' : activeDay.classList.contains('late') ? 'late' : 'excused') : '';
            const remarks = row.querySelector('[data-field="remarks"]')?.value || '';
            if (status) {
                records.push({ student_id: parseInt(studentId), status: status, remarks: remarks });
            }
        });

        if (!records.length) { alert('No attendance marked. Mark students as Present, Absent, Late, or Excused.'); return; }

        fetch('/teacher/attendance/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify({ section_id: sectionId, subject_id: subjectId, date: date, records: records })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) alert('Attendance saved successfully!');
            else alert('Error: ' + (data.message || 'Failed to save attendance.'));
        })
        .catch(function(err) { alert('Network error. Please try again.'); console.error(err); });
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
        if (el.tagName !== 'INPUT' || el.type !== 'text') return;
        var skip = ['email','search','password','reference'];
        var name = (el.name || '').toLowerCase();
        var id = (el.id || '').toLowerCase();
        var ph = (el.placeholder || '').toLowerCase();
        if (skip.some(function(s) { return name.indexOf(s) > -1 || id.indexOf(s) > -1; })) return;
        if (ph.indexOf('search') > -1 || ph.indexOf('09') === 0) return;
        capitalizeFirst(el);
    });

    // ── Auto-refresh grade view when tab becomes visible again ──
    (function () {
        var _hiddenAt = null;
        var THRESHOLD = 30000; // 30 seconds

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                _hiddenAt = Date.now();
            } else {
                if (_hiddenAt && (Date.now() - _hiddenAt) >= THRESHOLD) {
                    // Re-fetch grade drafts if a section is selected
                    if (typeof loadGradeView === 'function' && msCurrentSection) {
                        loadGradeView();
                    }
                }
                _hiddenAt = null;
            }
        });
    })();
</script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/teacherDashboard.blade.php ENDPATH**/ ?>