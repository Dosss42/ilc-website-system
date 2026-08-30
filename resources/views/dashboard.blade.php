<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — IEMELIF Learning Center</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-width: 240px;
            --topbar-height: 62px;
            --blue:       #1a3a6c;
            --blue-light: #2471a3;
            --blue-pale:  #e8f0fb;
            --gold:       #f5a623;
            --text:       #2d3748;
            --muted:      #718096;
            --bg:         #f0f4f8;
            --white:      #ffffff;
            --border:     #e2e8f0;
            --sidebar-bg: #1a3a6c;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ══════════════════════════════
           TOP BAR
        ══════════════════════════════ */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-height);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 20px 0 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .topbar-brand {
            width: var(--sidebar-width);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            background: var(--sidebar-bg);
            height: 100%;
            flex-shrink: 0;
        }

        .topbar-brand .brand-logos {
            display: flex;
            gap: 6px;
        }

        .brand-logo-img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-logo-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-text h6 {
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .brand-text span {
            font-size: 9px;
            color: rgba(255,255,255,0.6);
        }

        .topbar-center {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 7px 14px;
            gap: 8px;
            width: 280px;
        }

        .search-box i { color: var(--muted); font-size: 14px; }

        .search-box input {
            border: none;
            background: transparent;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            outline: none;
            width: 100%;
        }

        .search-box input::placeholder { color: #aaa; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }

        .topbar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--muted);
            position: relative;
            transition: border-color 0.2s;
        }

        .topbar-icon-btn:hover { border-color: var(--blue); color: var(--blue); }

        .notif-badge {
            position: absolute;
            top: -4px; right: -4px;
            width: 16px; height: 16px;
            background: var(--gold);
            border-radius: 50%;
            font-size: 9px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            transition: border-color 0.2s;
        }

        .user-chip:hover { border-color: var(--blue); }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--blue-pale);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-avatar i { color: var(--blue); font-size: 18px; }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.2;
        }

        .user-info .user-role {
            font-size: 10px;
            color: var(--muted);
        }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 16px 0;
            z-index: 99;
            overflow-y: auto;
        }

        .sidebar-section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35);
            padding: 14px 20px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
            border-left: 3px solid transparent;
        }

        .sidebar-link i {
            font-size: 17px;
            width: 20px;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .sidebar-link.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-left-color: var(--gold);
        }

        .sidebar-link .badge-count {
            margin-left: auto;
            background: var(--gold);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 10px 20px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* ══════════════════════════════
           MAIN CONTENT
        ══════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .page-header p {
            font-size: 13px;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .btn-primary-custom {
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            text-decoration: none;
        }

        .btn-primary-custom:hover { background: var(--blue-light); color: #fff; }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.blue   { background: #e8f0fb; color: var(--blue); }
        .stat-icon.gold   { background: #fff8ec; color: var(--gold); }
        .stat-icon.green  { background: #e8f8f0; color: #27ae60; }
        .stat-icon.red    { background: #fdecea; color: #e74c3c; }

        .stat-info .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-info .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .stat-info .stat-change {
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
        }

        .stat-change.up   { color: #27ae60; }
        .stat-change.down { color: #e74c3c; }

        /* ── Content Cards ── */
        .content-card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .content-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .content-card-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .content-card-header a {
            font-size: 12px;
            color: var(--blue-light);
            text-decoration: none;
            font-weight: 500;
        }

        .content-card-header a:hover { text-decoration: underline; }

        /* ── Table ── */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom thead th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            padding: 10px 20px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }

        .table-custom tbody td {
            font-size: 13px;
            padding: 13px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text);
        }

        .table-custom tbody tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background: #f8fafc; }

        .student-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--blue-pale);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--blue);
            flex-shrink: 0;
        }

        /* ── Status Badges ── */
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-status.enrolled  { background: #e8f8f0; color: #27ae60; }
        .badge-status.pending   { background: #fff8ec; color: #f39c12; }
        .badge-status.inactive  { background: #fdecea; color: #e74c3c; }

        /* ── Announcement List ── */
        .ann-item {
            display: flex;
            gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .ann-item:last-child { border-bottom: none; }
        .ann-item:hover { background: #f8fafc; }

        .ann-date {
            background: var(--blue);
            color: #fff;
            border-radius: 8px;
            text-align: center;
            padding: 6px 10px;
            min-width: 48px;
            flex-shrink: 0;
        }

        .ann-date .day  { font-size: 18px; font-weight: 700; line-height: 1; }
        .ann-date .mon  { font-size: 10px; text-transform: uppercase; opacity: 0.8; }

        .ann-body .ann-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }

        .ann-body .ann-meta {
            font-size: 11px;
            color: var(--muted);
        }

        /* ── Quick Actions ── */
        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 18px 10px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: var(--white);
            cursor: pointer;
            text-decoration: none;
            color: var(--text);
            transition: all 0.2s;
            text-align: center;
        }

        .quick-action:hover {
            border-color: var(--blue);
            background: var(--blue-pale);
            color: var(--blue);
            transform: translateY(-2px);
        }

        .quick-action i { font-size: 22px; }
        .quick-action span { font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════
     TOP BAR
══════════════════════════════════════ --}}
<div class="topbar">

    {{-- Brand / Logo --}}
    <div class="topbar-brand">
        {{-- =============================================
             TO ADD LOGOS: Replace <i> with your <img>
             ============================================= --}}
        <div class="brand-logos">
            <div class="brand-logo-img">
                <img src="/images/logo1.png" alt="Logo"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-building text-white\'></i>'">
            </div>
            <div class="brand-logo-img">
                <img src="/images/logo.png" alt="Logo"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-shield-fill text-white\'></i>'">
            </div>
        </div>
        <div class="brand-text">
            <h6>IEMELIF Learning Center</h6>
            <span>General Trias National ILC</span>
        </div>
    </div>

    {{-- Search --}}
    <div class="topbar-center">
        <div class="search-box">
            <i class="bi bi-search"></i>
            {{-- =============================================
                 SEARCH: Connect to your search controller
                 ============================================= --}}
            <input type="text" placeholder="Search students, records...">
        </div>
    </div>

    {{-- Right Controls --}}
    <div class="topbar-right">

        {{-- Notifications --}}
        {{-- =============================================
             NOTIFICATIONS: Connect to your notifications
             Replace the '3' badge with dynamic count
             ============================================= --}}
        <div class="topbar-icon-btn">
            <i class="bi bi-bell"></i>
            <span class="notif-badge">3</span>
        </div>

        {{-- Messages --}}
        <div class="topbar-icon-btn">
            <i class="bi bi-chat-dots"></i>
        </div>

        {{-- User Chip --}}
        {{-- =============================================
             USER INFO: Replace with Auth::user()->name
             Example: {{ Auth::user()->name }}
             Example role: {{ Auth::user()->role }}
             ============================================= --}}
        <div class="user-chip">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
                {{-- Replace with: <img src="{{ Auth::user()->avatar }}" alt=""> --}}
            </div>
            <div class="user-info">
                <div class="user-name">Admin User</div>
                {{-- Replace with: {{ Auth::user()->name }} --}}
                <div class="user-role">Administrator</div>
                {{-- Replace with: {{ Auth::user()->role }} --}}
            </div>
            <i class="bi bi-chevron-down" style="font-size:11px; color:#aaa; margin-left:4px;"></i>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ --}}
<div class="sidebar">

    <div class="sidebar-section-label">Main Menu</div>

    {{-- =============================================
         SIDEBAR LINKS:
         - Change href to your actual routes
         - Add 'active' class to current page link
         - Change badge numbers to dynamic counts
         ============================================= --}}

    <a href="#" class="sidebar-link active">
        <i class="bi bi-grid-1x2-fill"></i>
        Dashboard
    </a>

    <div class="sidebar-section-label">Management</div>

    <a href="#" class="sidebar-link">
        <i class="bi bi-people-fill"></i>
        Student Management
        {{-- Replace 0 with: {{ $studentCount }} --}}
        <span class="badge-count">0</span>
    </a>

    <a href="#" class="sidebar-link">
        <i class="bi bi-person-badge-fill"></i>
        Teacher Management
    </a>

    <a href="#" class="sidebar-link">
        <i class="bi bi-clipboard-check-fill"></i>
        Enrollment / Finance
        <span class="badge-count">0</span>
    </a>

    <a href="#" class="sidebar-link">
        <i class="bi bi-journal-medical"></i>
        Guidance Records
    </a>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section-label">Reports</div>

    <a href="#" class="sidebar-link">
        <i class="bi bi-bar-chart-fill"></i>
        Reports
    </a>

    <a href="#" class="sidebar-link">
        <i class="bi bi-megaphone-fill"></i>
        Announcements
    </a>

    <div class="sidebar-bottom">
        <a href="#" class="sidebar-link">
            <i class="bi bi-gear-fill"></i>
            Settings
        </a>
        {{-- =============================================
             LOGOUT: Change href to your logout route
             Example: href="{{ route('logout') }}"
             Use POST method for Laravel logout
             ============================================= --}}
        <a href="/logout" class="sidebar-link"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-left"></i>
            Logout
        </a>
        <form id="logout-form" action="/logout" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</div>

{{-- ══════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════ --}}
<div class="main-content">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            {{-- Replace with dynamic date: {{ now()->format('l, F d, Y') }} --}}
            <p>{{ now()->format('l, F d, Y') }} — Welcome back, Admin!</p>
        </div>
        {{-- =============================================
             ADD STUDENT BUTTON: Change href to your route
             ============================================= --}}
        <a href="#" class="btn-primary-custom">
            <i class="bi bi-plus-lg"></i> Add Student
        </a>
    </div>

    {{-- ── STAT CARDS ── --}}
    {{-- =============================================
         STAT CARDS: Replace hardcoded numbers with
         dynamic values from your controller
         Example: $totalStudents, $totalTeachers, etc.
         ============================================= --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-info">
                    {{-- Replace with: {{ $totalStudents }} --}}
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> 0 this month</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon gold">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-info">
                    {{-- Replace with: {{ $totalTeachers }} --}}
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Teachers</div>
                    <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> 0 active</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-clipboard-check-fill"></i>
                </div>
                <div class="stat-info">
                    {{-- Replace with: {{ $enrolledCount }} --}}
                    <div class="stat-value">0</div>
                    <div class="stat-label">Enrolled</div>
                    <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> S.Y. 2026-2027</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    {{-- Replace with: {{ $pendingCount }} --}}
                    <div class="stat-value">0</div>
                    <div class="stat-label">Pending Enrollment</div>
                    <div class="stat-change down"><i class="bi bi-clock"></i> Needs action</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="content-card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="p-3">
                    <div class="row g-2">
                        {{-- =============================================
                             QUICK ACTIONS: Change href to your routes
                             ============================================= --}}
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-person-plus-fill"></i>
                                <span>Enroll Student</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-file-earmark-text-fill"></i>
                                <span>View Records</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-printer-fill"></i>
                                <span>Print Report</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-megaphone-fill"></i>
                                <span>Post Announcement</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-cash-stack"></i>
                                <span>Finance</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="#" class="quick-action">
                                <i class="bi bi-calendar-event-fill"></i>
                                <span>Schedule</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STUDENTS TABLE + ANNOUNCEMENTS ── --}}
    <div class="row g-3">

        {{-- Recent Students Table --}}
        <div class="col-lg-7">
            <div class="content-card">
                <div class="content-card-header">
                    <h6>Recent Students</h6>
                    {{-- Change href to your students list route --}}
                    <a href="#">View All</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Grade</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- =============================================
                                 STUDENTS TABLE:
                                 Replace this with a @foreach loop
                                 Example:
                                 @foreach($students as $student)
                                 <tr>
                                   <td>{{ $student->name }}</td>
                                   <td>{{ $student->grade }}</td>
                                   <td>{{ $student->section }}</td>
                                   <td>{{ $student->status }}</td>
                                 </tr>
                                 @endforeach
                                 ============================================= --}}
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <div class="student-avatar">JD</div>
                                        <div>
                                            <div style="font-weight:600;">Juan Dela Cruz</div>
                                            <div style="font-size:11px;color:#aaa;">ID: 2026-001</div>
                                        </div>
                                    </div>
                                </td>
                                <td>Grade 7</td>
                                <td>Sampaguita</td>
                                <td><span class="badge-status enrolled">Enrolled</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <div class="student-avatar">MS</div>
                                        <div>
                                            <div style="font-weight:600;">Maria Santos</div>
                                            <div style="font-size:11px;color:#aaa;">ID: 2026-002</div>
                                        </div>
                                    </div>
                                </td>
                                <td>Grade 8</td>
                                <td>Rosal</td>
                                <td><span class="badge-status pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <div class="student-avatar">RL</div>
                                        <div>
                                            <div style="font-weight:600;">Ramon Lopez</div>
                                            <div style="font-size:11px;color:#aaa;">ID: 2026-003</div>
                                        </div>
                                    </div>
                                </td>
                                <td>Grade 10</td>
                                <td>Ilang-ilang</td>
                                <td><span class="badge-status enrolled">Enrolled</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <div class="student-avatar">AC</div>
                                        <div>
                                            <div style="font-weight:600;">Ana Cruz</div>
                                            <div style="font-size:11px;color:#aaa;">ID: 2026-004</div>
                                        </div>
                                    </div>
                                </td>
                                <td>Grade 9</td>
                                <td>Jasmin</td>
                                <td><span class="badge-status inactive">Inactive</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Announcements --}}
        <div class="col-lg-5">
            <div class="content-card">
                <div class="content-card-header">
                    <h6>Recent Announcements</h6>
                    {{-- Change href to your announcements route --}}
                    <a href="#">View All</a>
                </div>
                {{-- =============================================
                     ANNOUNCEMENTS:
                     Replace with @foreach from your controller
                     ============================================= --}}
                <div class="ann-item">
                    <div class="ann-date">
                        <div class="day">15</div>
                        <div class="mon">Jun</div>
                    </div>
                    <div class="ann-body">
                        <div class="ann-title">School Opening Day</div>
                        <div class="ann-meta">Posted by Admin · S.Y. 2026-2027</div>
                    </div>
                </div>
                <div class="ann-item">
                    <div class="ann-date">
                        <div class="day">10</div>
                        <div class="mon">Jun</div>
                    </div>
                    <div class="ann-body">
                        <div class="ann-title">Enrollment Period Now Open</div>
                        <div class="ann-meta">Posted by Registrar · All Levels</div>
                    </div>
                </div>
                <div class="ann-item">
                    <div class="ann-date">
                        <div class="day">05</div>
                        <div class="mon">Jun</div>
                    </div>
                    <div class="ann-body">
                        <div class="ann-title">Parent-Teacher Conference</div>
                        <div class="ann-meta">Posted by Admin · 3rd Quarter</div>
                    </div>
                </div>
                <div class="ann-item">
                    <div class="ann-date">
                        <div class="day">01</div>
                        <div class="mon">Jun</div>
                    </div>
                    <div class="ann-body">
                        <div class="ann-title">General Assembly — All Parents</div>
                        <div class="ann-meta">Posted by Principal · Auditorium</div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
</div>{{-- /main-content --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Highlight active sidebar link based on current URL ──
    // You can remove this if you're using Laravel's request()->routeIs()
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
</body>
</html>