<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Admin Dashboard — ILC</title>

    

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    

    <link rel="stylesheet" href="/css/adminDashboard.css?v=<?php echo e(filemtime(public_path('css/adminDashboard.css'))); ?>">

    <link rel="stylesheet" href="/css/global-scrollbar.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <style>

        /* ── Section visibility — all dash-sections hidden by default ── */
        .dash-section { display: none !important; }
        .dash-section.section-active { display: block !important; }

        /* ── Section skeleton loading ── */
        @keyframes pSkelShimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skel{background:linear-gradient(90deg,#e8edf2 25%,#f5f7fa 50%,#e8edf2 75%);background-size:600px 100%;animation:pSkelShimmer 1.4s ease-in-out infinite;border-radius:6px;display:block}
        .p-skel{position:absolute;top:0;left:0;right:0;bottom:0;padding:28px;z-index:50;background:var(--bg,#f0f4f8);pointer-events:none;min-height:100vh;transition:opacity .32s ease}
        [id^="section-"]{position:relative}

        /* Custom Pagination */
        .pagination {
            display: flex !important;
            justify-content: center;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 13px;
        }
        .pagination .page-item {
            display: block !important;
        }
        .pagination .page-item .page-link {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 6px 10px;
            margin: 0 2px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--white);
            color: var(--text);
            font-weight: 500;
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

        /* Legacy .custom-alert class — kept for any stale DOM refs, visually hidden */
        .custom-alert { display: none !important; }

        .alert-info {

            background: #d1ecf1;

            border-left-color: #1a3a6c;

            color: #0c5460;

        }

        .alert-loading {

            background: #e8f0fe;

            border-left-color: #4285f4;

            color: #1a3a6c;

        }

        @keyframes slideIn {

            from {

                transform: translateX(100%);

                opacity: 0;

            }

            to {

                transform: translateX(0);

                opacity: 1;

            }

        }

        .payment-icon-paid,
        .payment-icon-partial,
        .payment-icon-pending,
        .payment-icon-overpaid,
        .payment-icon-partially_paid {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
        }

        .payment-icon-paid {
            background: #d4edda;
            color: #155724;
        }

        .payment-icon-partial,
        .payment-icon-partially_paid {
            background: #fff3cd;
            color: #856404;
        }

        .payment-icon-pending {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-icon-overpaid {
            background: #cce5ff;
            color: #004085;
        }

        .installment-progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s;
        }

        .installment-progress-bar[data-type="complete"] {
            background: linear-gradient(90deg, #28a745, #4caf50);
        }

        .installment-progress-bar[data-type="overdue"] {
            background: linear-gradient(90deg, #dc3545, #ef5350);
        }

        .installment-progress-bar[data-type="normal"] {
            background: linear-gradient(90deg, var(--blue), var(--blue-light));
        }

        .alert-close {

            position: absolute;

            top: 8px;

            right: 8px;

            background: none;

            border: none;

            font-size: 18px;

            cursor: pointer;

            opacity: 0.7;

        }

        .alert-close:hover {

            opacity: 1;

        }

        .alert-title {

            font-weight: 600;

            margin-bottom: 4px;

            font-size: 15px;

        }

        .alert-message {

            line-height: 1.4;

        }

        /* Schedule Tabs */

        .schedule-tabs {

            display: flex;

            gap: 8px;

            margin-bottom: 20px;

        }

        .schedule-tab {

            padding: 10px 20px;

            border: none;

            background: #f0f0f0;

            border-radius: 8px;

            cursor: pointer;

            font-size: 14px;

            font-weight: 600;

            color: var(--text);

            transition: all 0.2s;

            display: flex;

            align-items: center;

            gap: 8px;

        }

        .schedule-tab:hover {

            background: #e0e0e0;

        }

        .schedule-tab.active {

            background: var(--blue);

            color: white;

        }

        .schedule-tab i {

            font-size: 16px;

        }

        /* Schedule Grid Table */

        :root {
            --sch-blue: #2471a3;
            --sch-blue-tint: #e8f0fb;
        }

        .sched-filter-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px 20px;
            margin-bottom: 20px;
        }

        .sched-filter-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 12px;
        }

        .sched-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .sched-field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sched-field-group label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
        }

        .sched-toolbar .form-fld {
            min-width: 168px;
            width: auto;
        }

        .sched-toolbar .btn-dash { flex-shrink: 0; }

        /* ── Empty-state quick-pick grade chips ── */
        .sched-empty-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 56px 24px;
            text-align: center;
        }

        .sched-empty-icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: var(--sch-blue-tint, var(--blue-pale));
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
        }

        .sched-empty-icon i {
            font-size: 32px;
            color: var(--sch-blue, var(--blue));
        }

        .sched-quickpick {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            max-width: 560px;
            margin: 22px auto 0;
        }

        .sched-quickpick-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px;
            border-radius: 20px;
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--text);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .sched-quickpick-btn:hover {
            border-color: var(--sch-blue, var(--blue));
            background: var(--sch-blue-tint, var(--blue-pale));
            color: var(--sch-blue, var(--blue));
            transform: translateY(-1px);
        }

        .sched-quickpick-btn i { font-size: 11px; opacity: 0.7; }

        .schedule-grid-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15,23,42,0.04), 0 8px 24px rgba(15,23,42,0.06);
        }

        .schedule-grid-table th {
            padding: 14px 12px;
            text-align: center;
            font-weight: 700;
            border: none;
            color: #fff;
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        .schedule-grid-table th:first-child { background: #334155; }
        .schedule-grid-table th.day-mon,
        .schedule-grid-table th.day-tue,
        .schedule-grid-table th.day-wed,
        .schedule-grid-table th.day-thu,
        .schedule-grid-table th.day-fri { background: linear-gradient(135deg, #1a3a6c 0%, #2471a3 100%); }

        .schedule-grid-table td {
            padding: 8px;
            border: none;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            min-height: 76px;
            height: 76px;
            background: #fff;
            transition: background-color 0.15s ease;
        }

        .schedule-grid-table td:last-child { border-right: none; }
        .schedule-grid-table tr:last-child td { border-bottom: none; }

        .schedule-grid-table .time-cell {
            background: #f8fafc;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            font-size: 11.5px;
            color: #475569;
            border-right: 2px solid #e2e8f0;
        }

        .schedule-grid-table .break-cell {
            background: repeating-linear-gradient(135deg, #fbfaf7, #fbfaf7 10px, #f5f2ea 10px, #f5f2ea 20px);
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            color: #92722a;
            letter-spacing: 1.5px;
            font-size: 11px;
            border-bottom: 2px solid #ecd9a8;
        }

        .schedule-cell {
            cursor: pointer;
            position: relative;
        }

        .schedule-cell:hover { background: #f8fafc; }

        .schedule-cell.drag-over {
            background: var(--sch-blue-tint);
            outline: 2px dashed var(--sch-blue);
            outline-offset: -2px;
        }

        .schedule-cell-content.dragging {
            opacity: 0.4;
        }

        .schedule-cell-content[draggable="true"]:active {
            cursor: grabbing;
        }

        .sched-conflict-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 9.5px;
            font-weight: 800;
            color: #fff;
            background: #dc2626;
            padding: 2px 7px;
            border-radius: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .schedule-cell-content {
            padding: 8px 9px;
            border-radius: 8px;
            height: 100%;
            box-sizing: border-box;
            overflow: hidden;
            border-left: 3px solid var(--sch-blue);
            background: var(--sch-blue-tint);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .schedule-cell-content:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15,23,42,0.1);
        }

        .schedule-cell-content.has-conflict {
            background: #fef2f2;
            border-left: 3px solid #dc2626;
        }

        .schedule-cell-content .subj-row {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
        }

        .schedule-cell-content .subj-code-pill {
            flex-shrink: 0;
            font-size: 9px;
            font-weight: 800;
            color: #fff;
            background: var(--sch-blue);
            padding: 1px 6px;
            border-radius: 5px;
            letter-spacing: 0.2px;
        }

        .schedule-cell-content.has-conflict .subj-code-pill { background: #dc2626; }

        .schedule-cell-content .subject {
            font-weight: 700;
            color: #1e293b;
            font-size: 11.5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .schedule-cell-content .teacher {
            font-size: 10.5px;
            color: #475569;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .schedule-cell-content .teacher .teacher-icon {
            font-size: 10px;
            flex-shrink: 0;
            color: var(--sch-blue);
        }

        .schedule-cell-content .room {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .schedule-cell-content .room i { font-size: 9px; flex-shrink: 0; }

        .schedule-cell-empty {
            height: 100%;
            min-height: 60px;
            border: 1.5px dashed #dde3ec;
            border-radius: 8px;
            color: #b7c0cc;
            font-size: 11.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.15s ease;
        }

        .schedule-cell:hover .schedule-cell-empty {
            border-color: var(--sch-blue);
            color: var(--sch-blue);
            background: var(--sch-blue-tint);
        }

        /* â”€â”€ Report Tabs â”€â”€ */
        .rpt-tab-btn {
            padding: 8px 20px; border: none; background: transparent;
            border-radius: 7px; font-size: 13px; font-weight: 600;
            color: var(--muted); cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 7px;
            font-family: 'Open Sans', sans-serif;
        }
        .rpt-tab-btn:hover { background: var(--bg); color: var(--text); }
        .rpt-tab-btn.active { background: var(--blue); color: #fff; }
        .rpt-panel { animation: fadeIn 0.2s ease; }
        /* â”€â”€ Report Sub-navigation â”€â”€ */
        .rpt-subnav { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
        .rpt-sub-btn {
            padding:6px 16px; border:1.5px solid var(--border); background:#fff;
            border-radius:20px; font-size:12px; font-weight:600; color:var(--muted);
            cursor:pointer; transition:all 0.18s; display:inline-flex; align-items:center; gap:6px;
            font-family:'Open Sans',sans-serif;
        }
        .rpt-sub-btn:hover { border-color:var(--blue); color:var(--blue); background:#f0f4ff; }
        .rpt-sub-btn.active { border-color:var(--blue); color:var(--blue); background:#e8f0fe; font-weight:700; }
        /* â”€â”€ KPI Cards â”€â”€ */
        .kpi-card {
            background:#fff; border:1px solid var(--border); border-radius:12px;
            padding:18px 16px; position:relative; overflow:hidden; transition:box-shadow 0.2s;
        }
        .kpi-card:hover { box-shadow:0 4px 18px rgba(0,0,0,0.10); }
        .kpi-card .kpi-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:4px; }
        .kpi-card .kpi-value { font-size:28px; font-weight:800; line-height:1.1; }
        .kpi-card .kpi-target { font-size:11px; color:var(--muted); margin-top:2px; }
        .kpi-card .kpi-bar-wrap { height:5px; background:#e2e8f0; border-radius:3px; margin-top:10px; overflow:hidden; }
        .kpi-card .kpi-bar { height:100%; border-radius:3px; transition:width 0.5s; }
        .kpi-status-met { color:var(--green); }
        .kpi-status-warn { color:var(--gold); }
        .kpi-status-low { color:var(--red); }
        .kpi-status-dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:4px; vertical-align:middle; }
        .kpi-dot-met { background:var(--green); }
        .kpi-dot-warn { background:var(--gold); }
        .kpi-dot-low { background:var(--red); }
        .kpi-dot-na { background:#cbd5e1; }

    </style>

</head>

<body>



<?php
    // Ensure all variables are defined with safe defaults
    $teachers = $teachers ?? collect();
    $sections = $sections ?? collect();
    $studentDocuments = $studentDocuments ?? collect();
    $recentStudents = $recentStudents ?? collect();
    // $guidanceRecords / $announcements / $news / $archivedStudents /
    // $enrollments / $students / $allStudentsPayment / $subjects /
    // $financePayments / $installmentEnrollments intentionally NOT
    // defaulted here (unlike the others above). Each is only ever a real
    // paginator when actually passed — the first seven from their
    // sectionX() AJAX responses, the last two never at all anymore
    // (removed 2026-10-06, see docs/system-improvement-plan.md).
    // Defaulting any of these to collect() makes isset() true with the
    // WRONG type, and the partials' ->total()/->appends() calls
    // (paginator-only) then crash the entire dashboard with a 500, not
    // just that one tab — confirmed eight times now. (finance.blade.php/
    // fees.blade.php are permanently hidden, redirect-only UI, but still
    // PHP-execute on every page load since they're just display:none, not
    // excluded from rendering — so this bites even "dead" tabs.)
    $feeBreakdowns = $feeBreakdowns ?? [];
    $schoolYears = $schoolYears ?? [];
    
    $studentCount = $studentCount ?? 0;
    $enrollmentCount = $enrollmentCount ?? 0;
    $financeCount = $financeCount ?? 0;
    $paidCount = $paidCount ?? 0;
    $unpaidCount = $unpaidCount ?? 0;
    
    $sort = $sort ?? 'newest';
    $statusFilter = $statusFilter ?? 'all';
    $gradeFilter = $gradeFilter ?? 'all';
    $studentSearch = $studentSearch ?? '';
    $studentGradeFilter = $studentGradeFilter ?? '';
    $studentStatusFilter = $studentStatusFilter ?? '';
    $studentPaymentFilter = $studentPaymentFilter ?? '';
    $studentSchoolYearFilter = $studentSchoolYearFilter ?? '';
?>

<script>
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
// CRITICAL: Define essential functions FIRST (before HTML calls them)
// â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

const sections = [
    'dashboard','students','teachers','teacher-assignments','enrollment','finance','payments','installments','fees','subjects','sections','schedules',
    'guidance','announcements','news','reports','settings','summer','promotion','assessment','messages','grade-oversight','archives'
];

// ── Breadcrumbs ──
const BREADCRUMB_LABELS = {
    dashboard: 'Dashboard', students: 'Student Management', teachers: 'Teacher Management',
    'teacher-assignments': 'Teacher Assignments', enrollment: 'Enrollment Management',
    finance: 'Finance', payments: 'Payments', installments: 'Installments', fees: 'Fee Settings',
    subjects: 'Subject Management', sections: 'Section Management', schedules: 'Schedule Management',
    guidance: 'Guidance Records', announcements: 'Announcements', news: 'News Management',
    reports: 'Reports', settings: 'Settings', summer: 'Summer Classes',
    promotion: 'Assessment & Promotion', assessment: 'Assessment & Promotion',
    messages: 'Contact Messages', 'grade-oversight': 'Grade Oversight', archives: 'Archives',
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

function showSettingsTab(tab) {
    document.querySelectorAll('.settings-tab').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.stg-nav-item').forEach(btn => btn.classList.remove('active'));
    const tabEl = document.getElementById('tab-' + tab);
    if (tabEl) tabEl.style.display = '';
    const btnEl = document.getElementById('stab-btn-' + tab);
    if (btnEl) btnEl.classList.add('active');
}

function previewAdminPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('admin-avatar-img');
        const placeholder = document.getElementById('admin-avatar-placeholder');
        img.src = e.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        const chipAvatar = document.querySelector('.user-avatar');
        if (chipAvatar) {
            chipAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
            chipAvatar.style.background = 'none';
            chipAvatar.style.overflow = 'hidden';
        }
        document.getElementById('admin-photo-form').submit();
    };
    reader.readAsDataURL(input.files[0]);
}

// Finance/fee sections are now managed by dedicated portals
const _PORTAL_SECTIONS = ['finance','payments','installments','fees'];

function showSection(name) {
    // Redirect to dedicated portals instead of showing old admin finance sections
    if (_PORTAL_SECTIONS.includes(name)) {
        _showPortalRedirectModal(name);
        return false;
    }

    sections.forEach(s => {
        const el = document.getElementById('section-' + s);
        if (el) {
            el.classList.toggle('section-active', s === name);
            el.style.display = ''; // reset inline so CSS class controls it
        }
        const nav = document.getElementById('nav-' + s);
        if (nav) nav.classList.toggle('active', s === name);
    });
    localStorage.setItem('currentAdminSection', name);
    updateBreadcrumb(name);
    window.scrollTo(0, 0);
    applySectionSkeleton(name);
    if (name === 'guidance') {
        loadGuidanceSection('<?php echo e(route("admin.section.guidance")); ?>');
    }
    if (name === 'announcements') {
        loadAnnouncementsSection('<?php echo e(route("admin.section.announcements")); ?>');
    }
    if (name === 'news') {
        loadNewsSection('<?php echo e(route("admin.section.news")); ?>');
    }
    if (name === 'archives') {
        loadArchivesSection('<?php echo e(route("admin.section.archives")); ?>');
    }
    if (name === 'enrollment') {
        loadEnrollmentSection('<?php echo e(route("admin.section.enrollment")); ?>');
    }
    if (name === 'students') {
        loadStudentsSection('<?php echo e(route("admin.section.students")); ?>');
    }
    if (name === 'subjects') {
        loadSubjectsSection('<?php echo e(route("admin.section.subjects")); ?>');
    }
    if (name === 'settings' && typeof initSettings === 'function') {
        initSettings();
    }
    if (name === 'fees' && typeof loadFeeSettings === 'function') {
        loadFeeSettings();
    }
    if (name === 'reports') {
        // Forward the current querystring (if any) so a legacy deep link
        // like ?section=reports&rpt_tab=financial&rpt_subreport=outstanding
        // still opens on the right tab/sub-report/page — see
        // loadReportsSection() and the DOMContentLoaded restore block below
        // for the other half of this. In normal operation (clicking the nav
        // link) there's no querystring, so this is just the base URL.
        loadReportsSection('<?php echo e(route("admin.section.reports")); ?>' + window.location.search);
    }
    return false;
}

// ── On-demand section loading (Guidance Records) ──────────────────────
// adminIndex() used to compute this tab's data on every single dashboard
// page load regardless of whether the admin ever opened it. Now it's
// fetched only when the tab is actually opened, and the filter form /
// pagination inside it (which used to do a full page reload) also go
// through this instead — see docs/system-improvement-plan.md item #2.
function loadGuidanceSection(url) {
    const container = document.getElementById('section-guidance');
    if (!container) return;
    container.setAttribute('data-loading', '1');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => {
            swapSectionContent('guidance', html, () => container.removeAttribute('data-loading'));
        })
        .catch(() => {
            swapSectionContent('guidance', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Guidance Records. <button onclick="loadGuidanceSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>', () => container.removeAttribute('data-loading'));
        });
}

// Filter form + pagination links inside Guidance Records go through
// loadGuidanceSection() instead of a normal page navigation. Delegated on
// document (not bound to the form/links directly) because the whole
// subtree gets replaced on every load — direct bindings would be lost.
document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'guidance-filter-form') {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(e.target)).toString();
        loadGuidanceSection('<?php echo e(route("admin.section.guidance")); ?>?' + params);
    }
});
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-guidance a[href]');
    if (link && !link.closest('.pagination')?.classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadGuidanceSection('<?php echo e(route("admin.section.guidance")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Announcements) ── same pattern as
// Guidance Records above. The category filter chips and pagination links
// inside this tab point at admin.section.announcements (not
// admin.dashboard) specifically so that if JS is ever unavailable and one
// is clicked as a real navigation, it still lands somewhere sane (a
// standalone fragment) rather than an old full-page route — but in
// normal operation every click here is intercepted below and never
// actually navigates.
function loadAnnouncementsSection(url) {
    const container = document.getElementById('section-announcements');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('announcements', html); })
        .catch(() => {
            swapSectionContent('announcements', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Announcements. <button onclick="loadAnnouncementsSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-announcements a[href]');
    if (link && !link.closest('.pagination')?.classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadAnnouncementsSection('<?php echo e(route("admin.section.announcements")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (News) ── same pattern as Announcements.
function loadNewsSection(url) {
    const container = document.getElementById('section-news');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('news', html); })
        .catch(() => {
            swapSectionContent('news', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load News. <button onclick="loadNewsSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-news a[href]');
    if (link && !link.closest('.pagination')?.classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadNewsSection('<?php echo e(route("admin.section.news")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Archives) ── unlike Guidance/Announcements/
// News, this section has real <a href> action links (Restore, SF10
// download, Permanently Delete) alongside pagination, so the click
// delegation below is scoped to .pagination specifically — intercepting
// every <a> in the section like the others do would hijack those actions.
function loadArchivesSection(url) {
    const container = document.getElementById('section-archives');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('archives', html); })
        .catch(() => {
            swapSectionContent('archives', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Archives. <button onclick="loadArchivesSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-archives .pagination a[href]');
    if (link && !link.closest('.pagination').classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadArchivesSection('<?php echo e(route("admin.section.archives")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Enrollment Management) ── same
// .pagination-only scoping as Archives — this section also has real
// <a href="#"> action links (View) alongside pagination.
function loadEnrollmentSection(url) {
    const container = document.getElementById('section-enrollment');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('enrollment', html); })
        .catch(() => {
            swapSectionContent('enrollment', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Enrollment Management. <button onclick="loadEnrollmentSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-enrollment .pagination a[href]');
    if (link && !link.closest('.pagination').classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadEnrollmentSection('<?php echo e(route("admin.section.enrollment")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Student Management) ── same
// .pagination-only scoping as Archives/Enrollment — this section also has
// real <a href="#"> action links (View/Edit/Change Status/Archive,
// handled by the separate [data-id] click-delegation elsewhere and
// unaffected by this scoping). The filter form (#student-filter-form)
// used to do a real GET page reload on every change (including the
// debounced search box via debounceFormSubmit()) — now intercepted here
// and sent through loadStudentsSection() instead.
function loadStudentsSection(url) {
    const container = document.getElementById('section-students');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('students', html); })
        .catch(() => {
            swapSectionContent('students', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Student Management. <button onclick="loadStudentsSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'student-filter-form') {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(e.target)).toString();
        loadStudentsSection('<?php echo e(route("admin.section.students")); ?>?' + params);
    }
});
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-students .pagination a[href]');
    if (link && !link.closest('.pagination').classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadStudentsSection('<?php echo e(route("admin.section.students")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Subject Management) ── unlike Archives/
// Enrollment/Students, this section has no real <a href> action links at
// all (Edit/Delete/"Add to Schedule" are all <button> elements, already
// handled by the separate [data-id] delegation and inline onclick
// elsewhere) — the only <a> in the whole partial is pagination's own
// output, so the click delegation here is a blanket one, same as
// Guidance/Announcements/News. The search/grade/status filter inputs are
// unaffected — they already run entirely client-side via
// filterSubjectTable() (or a separate existing /admin/subjects AJAX
// endpoint when a grade is picked), neither of which touches $subjects
// or this fetch at all.
function loadSubjectsSection(url) {
    const container = document.getElementById('section-subjects');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => { swapSectionContent('subjects', html); })
        .catch(() => {
            swapSectionContent('subjects', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Subject Management. <button onclick="loadSubjectsSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-subjects a[href]');
    if (link && !link.closest('.pagination')?.classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadSubjectsSection('<?php echo e(route("admin.section.subjects")); ?>?' + url.searchParams.toString());
    }
});

// ── On-demand section loading (Reports) ── By far the largest/most complex
// conversion: Reports has THREE independent paginated sub-reports (Master
// List, Outstanding Balances, Promotion List), each carrying its own
// rpt_tab/rpt_subreport/page params via ->appends() so a "next page" click
// inside, say, Financial > Outstanding doesn't land back on the default
// Student > Master List view. Pagination used to be a full page reload
// (see the now-redundant-but-harmless restore block in the DOMContentLoaded
// handler above) — now every pagination link here is intercepted the same
// way as every other section (blanket a[href], same justification as
// Subjects: PDF export buttons are <button>, not <a>, confirmed via grep).
// The one thing unique to Reports: after swapping content, the correct
// tab/sub-report has to be re-applied (switchRptTab/switchRptSubReport)
// since a fresh content swap always starts back on the default panel, and
// initReportsCharts() has to re-run (its data no longer lives in this
// page's own Blade scope — see that function's own comment).
function loadReportsSection(url) {
    const container = document.getElementById('section-reports');
    if (!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(html => {
            swapSectionContent('reports', html, () => {
                const params = new URL(url, window.location.origin).searchParams;
                const rptTab = params.get('rpt_tab');
                const rptSubreport = params.get('rpt_subreport');
                if (rptTab) {
                    switchRptTab(rptTab);
                    if (rptSubreport) switchRptSubReport(rptTab, rptSubreport);
                }
                if (typeof initReportsCharts === 'function') initReportsCharts();
            });
        })
        .catch(() => {
            swapSectionContent('reports', '<div style="text-align:center;padding:60px 20px;color:#dc2626;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                'Failed to load Reports. <button onclick="loadReportsSection(\'' + url.replace(/'/g, "\\'") + '\')" style="margin-left:8px;padding:4px 12px;border:1px solid #dc2626;background:#fff;color:#dc2626;border-radius:6px;cursor:pointer;">Retry</button>' +
                '</div>');
        });
}
document.addEventListener('click', function (e) {
    const link = e.target.closest('#section-reports a[href]');
    if (link && !link.closest('.pagination')?.classList.contains('disabled')) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        loadReportsSection('<?php echo e(route("admin.section.reports")); ?>?' + url.searchParams.toString());
    }
});

function _showPortalRedirectModal(section) {
    const isFeeMgmt = section === 'fees';
    const modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;';
    modal.innerHTML = `
        <div style="background:#fff;border-radius:20px;padding:40px;max-width:460px;width:100%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,.2);">
            <div style="width:72px;height:72px;border-radius:18px;background:#e8f0fb;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <i class="bi bi-arrow-right-circle-fill" style="font-size:34px;color:#1a3a6c;"></i>
            </div>
            <h5 style="font-weight:800;color:#1a3a6c;margin-bottom:8px;">Moved to Dedicated Portals</h5>
            <p style="color:#64748b;font-size:14px;margin-bottom:24px;line-height:1.6;">
                ${isFeeMgmt ? 'Fee configuration' : 'Finance records, payments, and installments'} are now fully managed through the <strong>Finance Portal</strong>${section === 'payments' ? ' and <strong>Cashier Portal</strong>' : ''}.
            </p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="/finance/dashboard" target="_blank"
                   style="display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:#1a3a6c;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer;">
                   <i class="bi bi-wallet2"></i> Finance Portal
                </a>
                ${section !== 'fees' ? `<a href="/cashier/dashboard" target="_blank"
                   style="display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:#f1f5f9;color:#1a3a6c;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;">
                   <i class="bi bi-cash-coin"></i> Cashier Portal
                </a>` : ''}
                <button onclick="this.closest('[style*=\"position:fixed\"]").remove()"
                   style="padding:11px 20px;background:#f1f5f9;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;">
                   Close
                </button>
            </div>
        </div>`;
    document.body.appendChild(modal);
    modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });
}

// â”€â”€ Report Tabs â”€â”€
// ── HTML escaping helper (prevents XSS when inserting user data into innerHTML) ──
function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Sections whose content is fetched on demand (GET admin.section.*) rather
// than already being present in the page's initial HTML — their skeleton
// has to stay visible until that fetch actually resolves (see
// swapSectionContent, called from each load*Section() below), not
// disappear on a fixed timer regardless of whether the content has
// actually arrived yet.
const _ASYNC_SECTIONS = ['guidance', 'announcements', 'news', 'archives', 'enrollment', 'students', 'subjects', 'reports'];

// Same minimum-visible + fade timing used everywhere — both for async
// sections (where this is a real floor under how fast the fetch could
// resolve) and already-rendered ones (where it's just the fixed cosmetic
// transition), so every section's skeleton feels the same speed.
const _SKEL_MIN_MS = 350;
const _SKEL_FADE_MS = 320;

function applySectionSkeleton(name) {
    var el = document.getElementById('section-' + name);
    if (!el) return;
    var old = el.querySelector('.p-skel'); if (old) old.remove();
    var s = document.createElement('div');
    s.className = 'p-skel';
    s.dataset.shownAt = String(Date.now());
    s.innerHTML = _buildSkelHTML();
    el.appendChild(s);

    if (_ASYNC_SECTIONS.includes(name)) {
        // Left visible — swapSectionContent(name, ...) below replaces it
        // once the section's own fetch actually completes.
        return;
    }

    // Already-rendered sections: this skeleton is purely a brief visual
    // transition, so it's safe to clear on a fixed timer.
    setTimeout(function() {
        s.style.opacity = '0';
        setTimeout(function() { if (s.parentNode) s.remove(); }, _SKEL_FADE_MS);
    }, _SKEL_MIN_MS);
}

// Swaps an async section's content in once its fetch resolves — used
// instead of a plain `container.innerHTML = html` because that alone
// destroys the skeleton overlay as a side effect of replacing every
// child (it's appended inside the same container), with no transition:
// content would just snap in instantly the moment the fetch finishes.
// This waits out _SKEL_MIN_MS first, then re-attaches the (still-valid,
// just detached) skeleton node on top of the new content and fades IT
// out, producing an actual crossfade instead of an invisible one.
function swapSectionContent(name, html, afterSwap) {
    var container = document.getElementById('section-' + name);
    if (!container) return;
    var skel = container.querySelector('.p-skel');
    var shownAt = skel ? (parseInt(skel.dataset.shownAt, 10) || 0) : 0;
    var wait = skel ? Math.max(0, _SKEL_MIN_MS - (Date.now() - shownAt)) : 0;

    setTimeout(function() {
        if (skel && skel.parentNode) skel.remove(); // detach before the wipe, not as a side effect of it
        container.innerHTML = html;
        if (typeof afterSwap === 'function') afterSwap();
        if (skel) {
            container.appendChild(skel);
            skel.style.transition = 'opacity ' + (_SKEL_FADE_MS / 1000) + 's ease';
            skel.style.opacity = '1';
            requestAnimationFrame(function() { skel.style.opacity = '0'; });
            setTimeout(function() { if (skel.parentNode) skel.remove(); }, _SKEL_FADE_MS);
        }
    }, wait);
}
function _buildSkelHTML() {
    var c = '', r = '', i;
    for (i = 0; i < 4; i++) c += '<div style="background:#fff;border-radius:10px;padding:18px;display:flex;gap:12px;align-items:center;border:1px solid #e2e8f0;"><span class="skel" style="width:44px;height:44px;border-radius:10px;flex-shrink:0;"></span><div style="flex:1"><span class="skel" style="height:22px;width:55px;display:block;border-radius:4px;margin-bottom:6px;"></span><span class="skel" style="height:11px;width:75px;display:block;border-radius:4px;"></span></div></div>';
    for (i = 0; i < 5; i++) r += '<div style="padding:13px 18px;border-bottom:1px solid #f5f5f5;display:flex;gap:14px;align-items:center;"><span class="skel" style="width:30px;height:30px;border-radius:50%;flex-shrink:0;"></span><span class="skel" style="height:12px;flex:1;display:block;border-radius:4px;"></span><span class="skel" style="height:12px;width:90px;display:block;border-radius:4px;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;display:block;"></span></div>';
    return '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;"><div><span class="skel" style="height:24px;width:190px;display:block;border-radius:4px;margin-bottom:8px;"></span><span class="skel" style="height:12px;width:140px;display:block;border-radius:4px;"></span></div><span class="skel" style="height:38px;width:110px;border-radius:8px;display:block;"></span></div><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:22px;">' + c + '</div><div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;"><div style="padding:14px 18px;border-bottom:1px solid #f0f0f0;"><span class="skel" style="height:15px;width:130px;display:block;border-radius:4px;"></span></div>' + r + '</div>';
}

var _currentRptTab = 'students';
var _currentRptSubReport = { students: 'master', enrollment: 'status', financial: 'collection', promotion: 'overview', kpi: 'overview' };

function switchRptTab(tab) {
    _currentRptTab = tab;
    ['students','enrollment','financial','promotion','kpi'].forEach(function(t) {
        var panel = document.getElementById('rpt-panel-' + t);
        var btn   = document.getElementById('rpt-tab-' + t);
        if (panel) panel.style.display = t === tab ? '' : 'none';
        if (btn)   btn.classList.toggle('active', t === tab);
    });
}

function switchRptSubReport(tab, subreport) {
    _currentRptSubReport[tab] = subreport;
    // Hide all sub-panels for this tab
    var panels = document.querySelectorAll('#rpt-panel-' + tab + ' .rpt-sub-panel');
    panels.forEach(function(p) { p.style.display = 'none'; });
    // Show selected
    var target = document.getElementById('rpt-sub-' + tab + '-' + subreport);
    if (target) target.style.display = '';
    // Update sub-nav button states
    var btns = document.querySelectorAll('#rpt-panel-' + tab + ' .rpt-sub-btn');
    btns.forEach(function(b) {
        b.classList.toggle('active', b.dataset.subreport === subreport);
    });
}

function buildCurrentReportContent() {
    // Find active sub-report panel, fallback to whole tab print area
    var subreport = _currentRptSubReport[_currentRptTab];
    var panelId = subreport
        ? 'rpt-sub-' + _currentRptTab + '-' + subreport
        : 'rpt-print-' + _currentRptTab;
    var panel = document.getElementById(panelId);
    if (!panel) {
        panelId = 'rpt-print-' + _currentRptTab;
        panel   = document.getElementById(panelId);
    }
    if (!panel) return null;

    // Work on a clone so we never touch the live page, and strip out the
    // parts that only make sense on-screen (the panel's own header button
    // row, and pagination controls) — neither belongs on a printed page.
    var clone = panel.cloneNode(true);
    clone.querySelectorAll('.content-card-header, .p-3.border-top').forEach(function(el) {
        el.remove();
    });

    var syText = 'S.Y. <?php echo e($currentSchoolYear); ?>';

    var subTitles = {
        students: { master: 'Student Master List', grade: 'Students by Grade Level', newret: 'New vs Returning', docs: 'Document Compliance' },
        enrollment: { status: 'Enrollment Status Summary', grade: 'Enrollment by Grade Level', newret: 'New vs Returning Enrollees', trend: 'Daily Enrollment Trend' },
        financial: { collection: 'Collection Summary', grade: 'Financial by Grade Level', option: 'By Payment Option', outstanding: 'Outstanding Balances' },
        promotion: { overview: 'Promotion Overview', grade: 'Promotion by Grade Level', list: 'Promotion Student List' },
        kpi: { overview: 'KPI Dashboard' }
    };
    var tabTitles = { students: 'Students Report', enrollment: 'Enrollment Report', financial: 'Financial Report', promotion: 'Promotion Report', kpi: 'KPI Dashboard' };
    var sub = (subTitles[_currentRptTab] || {})[subreport] || '';
    var title = (tabTitles[_currentRptTab] || 'Report') + (sub ? ' — ' + sub : '');

    var style =
        'body{font-family:Arial,sans-serif;margin:0;padding:28px;font-size:13px;color:#222;}'
        + '.header{text-align:center;border-bottom:3px solid #1a3a6c;padding-bottom:14px;margin-bottom:20px;}'
        + '.header img{width:70px;height:70px;object-fit:contain;display:block;margin:0 auto 8px;}'
        + '.school-name{font-size:20px;font-weight:700;color:#1a3a6c;}'
        + '.school-addr{font-size:12px;color:#555;margin-top:2px;}'
        + '.report-title{font-size:13px;font-weight:700;color:#2471a3;text-transform:uppercase;letter-spacing:2px;margin-top:6px;}'
        + '.divider{width:50px;height:3px;background:#1a3a6c;margin:8px auto 0;border-radius:2px;}'
        + '.sy-label{font-size:12px;color:#666;margin:14px 0 16px;text-align:center;}'
        + 'table{width:100%;border-collapse:collapse;}'
        + 'thead tr{background:#1a3a6c;color:#fff;}'
        + 'th{padding:9px 12px;text-align:left;font-size:12px;font-weight:600;}'
        + 'td{padding:9px 12px;border-bottom:1px solid #eee;font-size:13px;}'
        + 'tr:nth-child(even){background:#f8fafc;}'
        + 'tr:last-child{font-weight:700;background:#f0f4f8;}'
        + '.footer{margin-top:26px;border-top:2px solid #1a3a6c;padding-top:14px;}'
        + '.footer-verse{text-align:center;font-style:italic;color:#2471a3;font-size:11.5px;line-height:1.5;padding:0 24px 12px;border-bottom:1px solid #e5e7eb;margin-bottom:12px;}'
        + '.footer-verse .ref{display:block;margin-top:5px;font-style:normal;font-weight:700;color:#1a3a6c;font-size:10px;text-transform:uppercase;letter-spacing:1px;}'
        + '.footer-vmg{display:flex;gap:24px;margin-bottom:12px;}'
        + '.footer-vmg-col{flex:1;}'
        + '.footer-vmg-col b{display:block;color:#1a3a6c;font-size:10px;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:4px;}'
        + '.footer-vmg-col p{margin:0;line-height:1.5;color:#666;font-size:10.5px;}'
        + '.footer-meta{display:flex;justify-content:space-between;color:#aaa;font-size:10.5px;padding-top:8px;border-top:1px solid #f0f0f0;}';

    var bodyInner =
        '<div class="header">'
        + '<img src="/images/logo.png" alt="ILC Logo" onerror="this.style.display=\'none\'">'
        + '<div class="school-name">IEMELIF LEARNING CENTER</div>'
        + '<div class="school-addr">General Tinio, Nueva Ecija</div>'
        + '<div class="report-title">' + title + '</div>'
        + '<div class="divider"></div>'
        + '</div>'
        + '<div class="sy-label">' + syText + '</div>'
        + clone.outerHTML
        + '<div class="footer">'
        + '<div class="footer-verse">'
        + '&ldquo;But Jesus said, Suffer little children, and forbid them not, to come unto me: for of such is the kingdom of heaven.&rdquo;'
        + '<span class="ref">Matthew 19:14 (KJV)</span>'
        + '</div>'
        + '<div class="footer-vmg">'
        + '<div class="footer-vmg-col">'
        + '<b>Vision</b>'
        + '<p>Creating and sustaining an integrated, wholesome, and appropriate environment for all phases of the learner\'s growth and development.</p>'
        + '</div>'
        + '<div class="footer-vmg-col">'
        + '<b>Mission</b>'
        + '<p>The IEMELIF Learning Center is a future-oriented school which gives opportunities to all children to discover their interests and God-given talents, which will be explored and developed as they grow up and become successful individuals. '
        + 'The ILC aims to train and lead these children to have a &ldquo;Desire to Learn&rdquo; and integrate everything they have acquired in their daily experiences. '
        + 'The ultimate goal of this school is to emulate and follow Jesus&rsquo; example, as He showed His love and care for the children.</p>'
        + '</div>'
        + '</div>'
        + '<div class="footer-meta"><span>IEMELIF Learning Center &mdash; Official Report</span>'
        + '<span>Printed: ' + new Date().toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'}) + '</span></div>'
        + '</div>';

    return {
        title: title,
        fullHtml: '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' + title + '</title><style>' + style + '</style></head><body>' + bodyInner + '</body></html>',
        style: style,
        bodyInner: bodyInner
    };
}

function printCurrentReport() {
    var built = buildCurrentReportContent();
    if (!built) return;
    var win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(built.fullHtml);
    win.document.close();
    win.onload = function() { win.focus(); win.print(); };
}

function exportReportToPdf(btn) {
    var built = buildCurrentReportContent();
    if (!built) { showAdminToast('Nothing to export yet.', 'error'); return; }

    var originalBtnHtml = btn ? btn.innerHTML : null;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...'; }

    // html2canvas only captures real pixels when the source element is on
    // screen (top:0/left:0) and fully opaque — opacity:0 and far off-screen
    // positioning (e.g. left:-9999px) both produced a blank capture in
    // testing. So the element renders on-screen for real, and a solid
    // full-page overlay is what actually keeps this invisible to the user.
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:#fff;z-index:999999;display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--muted);';
    overlay.innerHTML = '<div style="text-align:center;"><i class="bi bi-hourglass-split" style="font-size:28px;display:block;margin-bottom:10px;"></i>Generating PDF&hellip;</div>';
    document.body.appendChild(overlay);

    var container = document.createElement('div');
    container.style.position = 'absolute';
    container.style.top = '0';
    container.style.left = '0';
    container.style.width = '800px';
    container.style.background = '#fff';
    container.innerHTML = '<style>' + built.style + '</style>' + built.bodyInner;
    document.body.appendChild(container);

    var filename = built.title.replace(/[^a-z0-9]+/gi, '_') + '.pdf';

    function cleanup() {
        document.body.removeChild(container);
        document.body.removeChild(overlay);
        if (btn) { btn.disabled = false; btn.innerHTML = originalBtnHtml; }
    }

    html2pdf().set({
        margin: 10,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        // width/height explicitly passed — html2canvas otherwise auto-measures
        // the source element, which can come back as 0-height for elements
        // hidden via opacity/positioning tricks, producing a blank PDF.
        // Deliberately NOT passing windowWidth/windowHeight — those simulate
        // resizing the WHOLE page (sidebar included) to that width for layout
        // purposes, which shifted this dashboard's fixed-sidebar layout and
        // cropped the left edge of the capture.
        //
        // scrollX/scrollY: 0 — html2canvas otherwise defaults to the page's
        // CURRENT scroll position and offsets the capture by that amount.
        // Since the Print/PDF buttons live inside each report panel (often
        // below the fold), the page is usually scrolled when this runs,
        // which without this override left a blank gap at the top of the
        // PDF exactly the height of however far the page had been scrolled.
        html2canvas: { scale: 2, useCORS: true, width: container.scrollWidth, height: container.scrollHeight, scrollX: 0, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(container).save().then(function() {
        cleanup();
    }).catch(function(err) {
        cleanup();
        showAdminToast('PDF export failed: ' + (err.message || 'Unknown error'), 'error');
        console.error(err);
    });
}

function openWalkInEnrollmentModal() {
    const modal = document.getElementById('walkInEnrollmentModal');
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

</script>



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

            <input type="text" placeholder="Search students, records...">

        </div>

    </div>

    <div class="topbar-right">

        
        <?php $isMaintenance = $maintenanceMode ?? false; ?>
        <button id="maintenance-topbar-btn" onclick="toggleMaintenanceMode()"
            title="<?php echo e($isMaintenance ? 'Maintenance ON — click to turn off' : 'Turn on Maintenance Mode'); ?>"
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;border:1.5px solid <?php echo e($isMaintenance ? '#e74c3c' : '#ddd'); ?>;background:<?php echo e($isMaintenance ? '#fdecea' : '#f8f9fa'); ?>;color:<?php echo e($isMaintenance ? '#c0392b' : '#666'); ?>;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;">
            <i class="bi bi-<?php echo e($isMaintenance ? 'tools' : 'tools'); ?>" id="maintenance-topbar-icon"></i>
            <span id="maintenance-topbar-label"><?php echo e($isMaintenance ? 'Maintenance ON' : 'Maintenance'); ?></span>
        </button>

        <a href="#" class="topbar-icon-btn">

            <i class="bi bi-bell"></i>

            <span class="notif-dot">5</span>

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
                    <div class="user-chip-role">Admin / Registrar</div>
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
                        <span class="ucd-badge">Admin / Registrar</span>
                    </div>
                </div>
                <div class="ucd-body">
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-person-circle"></i> My Profile</a>
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-gear"></i> Settings</a>
                </div>
                <div class="ucd-divider"></div>
                <div class="ucd-footer">
                    <form method="POST" action="<?php echo e(route('logout')); ?>" onsubmit="return confirmLogout(this)">
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



<div class="dash-sidebar">

    <div class="sidebar-section-lbl">Main</div>

    <button class="sidebar-link active" id="nav-dashboard" onclick="showSection('dashboard')">

        <i class="bi bi-grid-1x2-fill"></i> Dashboard

    </button>

    <div class="sidebar-section-lbl">Management</div>

    <button class="sidebar-link" id="nav-students" onclick="showSection('students')">
        <i class="bi bi-people-fill"></i> Student Management
        <span class="sidebar-badge"><?php if(isset($studentCount)): ?><?php echo e($studentCount); ?><?php endif; ?></span>
    </button>

    <?php
        $sidebarAssessCount = ($assessStudents ?? collect())->filter(fn($s) => $s->promotions->isEmpty())->count();
    ?>
    <button class="sidebar-link" id="nav-assessment" onclick="showSection('assessment')">
        <i class="bi bi-mortarboard-fill"></i> Assessment &amp; Promotion
        <?php if($sidebarAssessCount > 0): ?>
            <span class="sidebar-badge"><?php echo e($sidebarAssessCount); ?></span>
        <?php endif; ?>
    </button>

    <button class="sidebar-link" id="nav-enrollment" onclick="showSection('enrollment')">

        <i class="bi bi-clipboard-check-fill"></i> Enrollment Management

        <?php if(isset($enrollmentCount)): ?>

        <span class="sidebar-badge"><?php echo e($enrollmentCount); ?></span>

        <?php endif; ?>

    </button>



    <button class="sidebar-link" id="nav-guidance" onclick="showSection('guidance')">

        <i class="bi bi-journal-medical"></i> Guidance Records

    </button>

    <button class="sidebar-link" id="nav-summer" onclick="showSection('summer')">

        <i class="bi bi-sun-fill"></i> Summer Classes

    </button>

    <div class="sidebar-section-lbl">Teacher</div>


    <button class="sidebar-link" id="nav-teachers" onclick="showSection('teachers')">

    <i class="bi bi-person-badge-fill"></i> Teacher Management

    </button>

    <button class="sidebar-link" id="nav-teacher-assignments" onclick="showSection('teacher-assignments')">

        <i class="bi bi-journal-bookmark-fill"></i> Advisory Teacher Assignments

    </button>


    
    
    

    <div class="sidebar-section-lbl">Academic</div>

    <button class="sidebar-link" id="nav-subjects" onclick="showSection('subjects')">

        <i class="bi bi-book-fill"></i> Subject Management

    </button>

    <button class="sidebar-link" id="nav-sections" onclick="showSection('sections')">

        <i class="bi bi-diagram-3-fill"></i> Section Management

    </button>

    <button class="sidebar-link" id="nav-schedules" onclick="showSection('schedules')">

        <i class="bi bi-calendar-week-fill"></i> Schedule Management

    </button>

    <div class="sidebar-section-lbl">Content</div>

    <button class="sidebar-link" id="nav-announcements" onclick="showSection('announcements')">

        <i class="bi bi-megaphone-fill"></i> Announcements

    </button>

    <button class="sidebar-link" id="nav-news" onclick="showSection('news')">

        <i class="bi bi-newspaper"></i> News

    </button>

    
    <div class="sidebar-section-lbl">settings</div>
    <div class="sidebar-divider"></div>

    <button class="sidebar-link" id="nav-archives" onclick="showSection('archives')">
        <i class="bi bi-archive-fill"></i> Archives
        <?php if(($archivedStudentsCount ?? 0) > 0): ?>
            <span class="sidebar-badge" style="background:#b45309;"><?php echo e($archivedStudentsCount); ?></span>
        <?php endif; ?>
    </button>

    <button class="sidebar-link" id="nav-reports" onclick="showSection('reports')">

        <i class="bi bi-bar-chart-fill"></i> Reports

    </button>

    <div class="sidebar-bottom">

        <button class="sidebar-link" id="nav-settings" onclick="showSection('settings')">

            <i class="bi bi-gear-fill"></i> Settings

        </button>

        

        <form method="POST" action="/logout" onsubmit="return confirmLogout(this)">

            <?php echo csrf_field(); ?>

            <button type="submit" class="sidebar-link"

                style="color:rgba(248,113,113,0.8);">

                <i class="bi bi-box-arrow-left" style="color:rgba(248,113,113,0.9);"></i> Logout

            </button>

        </form>

    </div>

</div>



<div class="dash-main">

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
        <span id="bc-current" class="ilc-bc-current">Dashboard</span>
        <span id="bc-current-skel" class="skel" style="display:none;width:110px;height:13px;border-radius:4px;"></span>
    </nav>

    

    <?php echo $__env->make('admin.sections.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.students', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('admin.sections.archives', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
    function filterArchiveRows() {
        var q     = (document.getElementById('archive-search-input')?.value || '').toLowerCase().trim();
        var grade = (document.getElementById('archive-grade-filter')?.value || '').toLowerCase().trim();
        var rows  = document.querySelectorAll('#archive-tbody tr');
        var vis   = 0;
        rows.forEach(function(row) {
            var searchMatch = !q    || (row.dataset.search || '').includes(q);
            var gradeMatch  = !grade || (row.dataset.grade  || '').toLowerCase() === grade;
            var show = searchMatch && gradeMatch;
            row.style.display = show ? '' : 'none';
            if (show) vis++;
        });
        var noRes = document.getElementById('archive-no-results');
        var label = document.getElementById('archive-count-label');
        if (noRes) noRes.style.display = (vis === 0) ? '' : 'none';
        if (label) label.textContent = 'Showing ' + vis + ' archived student(s)';
    }
    </script>

    
    <div class="modal fade" id="changeStatusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border:0;border-radius:14px;overflow:hidden;">
                <div class="modal-header" style="background:var(--blue);color:#fff;border:0;padding:16px 20px;">
                    <h6 class="modal-title mb-0"><i class="bi bi-arrow-left-right me-2"></i>Change Enrollment Status</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <p style="font-size:13px;color:#555;margin-bottom:14px;">Student: <strong id="cs-student-name"></strong></p>
                    <label class="form-lbl">New Status</label>
                    <select id="cs-new-status" class="form-fld mb-3">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="enrolled">Enrolled</option>
                        <option value="declined">Declined</option>
                        <option value="completed">Completed</option>
                        <option value="dropped">Dropped</option>
                        <option value="ghost">Ghost</option>
                        <option value="transferred">Transferred</option>
                    </select>
                    <p style="font-size:11px;color:#999;margin-top:4px;">This updates the enrollment record immediately.</p>
                </div>
                <div class="modal-footer" style="border:0;padding:12px 20px;">
                    <button class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-dash btn-primary" onclick="confirmChangeStatus()">Save Status</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="smAssessModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border:0;border-radius:14px;overflow:hidden;">
                <div class="modal-header" style="background:var(--blue);color:#fff;border:0;padding:16px 24px;">
                    <div>
                        <h6 class="modal-title mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Student Assessment</h6>
                        <div id="sm-assess-subtitle" style="font-size:12px;opacity:0.8;margin-top:2px;"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:0;">
                    
                    <div style="display:flex;border-bottom:1px solid #e5e7eb;">
                        <button class="sm-assess-tab active" onclick="smAssessTab('grades',this)" style="flex:1;padding:12px;border:0;background:#fff;font-size:13px;font-weight:600;color:var(--blue);border-bottom:2px solid var(--blue);cursor:pointer;">
                            <i class="bi bi-bar-chart-fill me-1"></i> Grades
                        </button>
                        <button class="sm-assess-tab" onclick="smAssessTab('balance',this)" style="flex:1;padding:12px;border:0;background:#f8f9fa;font-size:13px;font-weight:600;color:#888;cursor:pointer;">
                            <i class="bi bi-wallet2 me-1"></i> Balance
                        </button>
                        <button class="sm-assess-tab" id="sm-docs-tab-btn" onclick="smAssessTab('docs',this);loadSmDocuments();" style="flex:1;padding:12px;border:0;background:#f8f9fa;font-size:13px;font-weight:600;color:#888;cursor:pointer;">
                            <i class="bi bi-folder-check me-1"></i> Documents
                        </button>
                        <button class="sm-assess-tab" id="sm-guidance-tab-btn" onclick="smAssessTab('guidance',this);loadSmGuidance();" style="flex:1;padding:12px;border:0;background:#f8f9fa;font-size:13px;font-weight:600;color:#888;cursor:pointer;">
                            <i class="bi bi-flag me-1"></i> Guidance
                        </button>
                        <button class="sm-assess-tab" id="sm-summer-tab-btn" onclick="smAssessTab('summer',this);loadSmSummer();" style="flex:1;padding:12px;border:0;background:#f8f9fa;font-size:13px;font-weight:600;color:#888;cursor:pointer;">
                            <i class="bi bi-sun me-1"></i> Summer Class
                        </button>
                        <button class="sm-assess-tab" onclick="smAssessTab('decision',this)" style="flex:1;padding:12px;border:0;background:#f8f9fa;font-size:13px;font-weight:600;color:#888;cursor:pointer;">
                            <i class="bi bi-check2-square me-1"></i> Decision
                        </button>
                    </div>

                    
                    <div id="sm-tab-grades" class="sm-assess-panel" style="padding:20px;">
                        <div id="sm-grades-loading" style="text-align:center;padding:30px;color:#999;">
                            <div class="spinner-border spinner-border-sm me-2"></div> Loading grades...
                        </div>
                        <div id="sm-grades-content" style="display:none;"></div>
                    </div>

                    
                    <div id="sm-tab-balance" class="sm-assess-panel" style="display:none;padding:20px;">
                        <div id="sm-balance-content">
                            <div style="text-align:center;padding:30px;color:#999;">
                                <div class="spinner-border spinner-border-sm me-2"></div> Loading balance...
                            </div>
                        </div>
                    </div>

                    
                    <div id="sm-tab-docs" class="sm-assess-panel" style="display:none;padding:20px;">
                        <div id="sm-docs-loading" style="text-align:center;padding:30px;color:#999;">
                            <div class="spinner-border spinner-border-sm me-2"></div> Loading documents...
                        </div>
                        <div id="sm-docs-content" style="display:none;"></div>
                    </div>

                    
                    <div id="sm-tab-guidance" class="sm-assess-panel" style="display:none;padding:20px;">
                        <div id="sm-guidance-loading" style="text-align:center;padding:30px;color:#999;">
                            <div class="spinner-border spinner-border-sm me-2"></div> Loading guidance records...
                        </div>
                        <div id="sm-guidance-content" style="display:none;"></div>
                    </div>

                    
                    <div id="sm-tab-summer" class="sm-assess-panel" style="display:none;padding:20px;">
                        <div id="sm-summer-loading" style="text-align:center;padding:30px;color:#999;">
                            <div class="spinner-border spinner-border-sm me-2"></div> Loading summer class status...
                        </div>
                        <div id="sm-summer-content" style="display:none;"></div>
                    </div>

                    
                    <div id="sm-tab-decision" class="sm-assess-panel" style="display:none;padding:20px;">
                        <p style="font-size:13px;color:#555;margin-bottom:16px;">
                            Review the student's grades and balance above, then choose an action for the next school year.
                        </p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                            <label style="border:2px solid #ddd;border-radius:10px;padding:14px;cursor:pointer;transition:all 0.2s;" id="sm-opt-promote" onclick="smSelectDecision('promote')">
                                <input type="radio" name="sm-decision" value="promote" style="margin-right:8px;">
                                <strong style="color:#1a7a44;"><i class="bi bi-arrow-up-circle-fill me-1"></i> Promote</strong>
                                <p style="font-size:12px;color:#666;margin:6px 0 0 20px;">Move student to the next grade level.</p>
                            </label>
                            <label style="border:2px solid #ddd;border-radius:10px;padding:14px;cursor:pointer;transition:all 0.2s;" id="sm-opt-retain" onclick="smSelectDecision('retain')">
                                <input type="radio" name="sm-decision" value="retain" style="margin-right:8px;">
                                <strong style="color:#d68910;"><i class="bi bi-arrow-repeat me-1"></i> Retain</strong>
                                <p style="font-size:12px;color:#666;margin:6px 0 0 20px;">Keep student in the same grade.</p>
                            </label>
                        </div>
                        <div id="sm-next-grade-row" style="display:none;margin-bottom:12px;">
                            <label class="form-lbl">Next Grade Level (for promotion)</label>
                            <select id="sm-next-grade" class="form-fld"></select>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-lbl">Next School Year</label>
                            <select id="sm-assess-to-sy" class="form-fld">
                                <?php
                                    $syBase = $currentSchoolYear ?? '';
                                    $baseY  = $syBase ? (int) explode('-', $syBase)[0] : (now()->month >= 6 ? now()->year : now()->year - 1);
                                    for ($y = $baseY + 6; $y >= $baseY + 1; $y--) {
                                        $sy  = $y . '-' . ($y + 1);
                                        $sel = ($y === $baseY + 1) ? 'selected' : '';
                                        echo "<option value=\"$sy\" $sel>$sy</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-lbl">Remarks (optional)</label>
                            <textarea id="sm-assess-remarks" class="form-fld" rows="2" placeholder="e.g. Student passed all subjects..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb;padding:12px 20px;justify-content:space-between;">
                    <button class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-dash btn-primary" id="sm-assess-submit-btn" onclick="confirmSmAssessment()" style="display:none;">
                        <i class="bi bi-check2 me-1"></i> Confirm Assessment
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="bulkPromoteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-styled">
                <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #27ae60, #1e8449);">
                    <h5 class="modal-title" style="color:#fff;"><i class="bi bi-arrow-up-circle-fill me-2"></i>Promote All — <span id="bp-grade-label"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-styled">
                    <div id="bp-loading" style="text-align:center;padding:30px;color:var(--muted);">
                        <i class="bi bi-hourglass-split" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                        Checking eligible students...
                    </div>
                    <div id="bp-content" style="display:none;">
                        <div class="form-lbl-wrap mb-3">
                            <label class="form-lbl">Promote to School Year</label>
                            <input type="text" id="bp-to-sy" class="form-fld" readonly>
                        </div>

                        <div style="background:#e8f8f0;border:1px solid #b8e6c8;border-radius:10px;padding:12px 16px;margin-bottom:14px;">
                            <div style="font-weight:700;color:#1e8449;font-size:14px;">
                                <i class="bi bi-check-circle-fill me-1"></i><span id="bp-eligible-count">0</span> student(s) will be promoted
                            </div>
                            <div id="bp-eligible-names" style="font-size:12px;color:#2e7d32;margin-top:6px;max-height:100px;overflow-y:auto;"></div>
                        </div>

                        <div id="bp-excluded-box" style="background:#fff8e1;border:1px solid #f0dca0;border-radius:10px;padding:12px 16px;display:none;">
                            <div style="font-weight:700;color:#856404;font-size:14px;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i><span id="bp-excluded-count">0</span> student(s) skipped — unpaid balance or incomplete documents
                            </div>
                            <div id="bp-excluded-list" style="font-size:12px;color:#6b5400;margin-top:8px;max-height:150px;overflow-y:auto;"></div>
                        </div>

                        <div id="bp-none-eligible" style="display:none;text-align:center;padding:20px;color:var(--muted);">
                            No students in this grade can be bulk-promoted right now — everyone has an unpaid balance or incomplete documents.
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb;padding:12px 20px;justify-content:space-between;">
                    <button class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-dash btn-success" id="bp-confirm-btn" onclick="confirmBulkPromote()" style="display:none;">
                        <i class="bi bi-check2 me-1"></i> Confirm Promotion
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="autoAdvanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-styled">
                <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #27ae60, #1e8449);">
                    <h5 class="modal-title" style="color:#fff;"><i class="bi bi-arrow-up-circle-fill me-2"></i>Advance All — <span id="aa-grade-label"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-styled">
                    <div id="aa-loading" style="text-align:center;padding:30px;color:var(--muted);">
                        <i class="bi bi-hourglass-split" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                        Checking students...
                    </div>
                    <div id="aa-content" style="display:none;">
                        <div class="form-lbl-wrap mb-3">
                            <label class="form-lbl">Advance to School Year</label>
                            <input type="text" id="aa-to-sy" class="form-fld" readonly>
                        </div>
                        <div style="background:#e8f8f0;border:1px solid #b8e6c8;border-radius:10px;padding:12px 16px;">
                            <div style="font-weight:700;color:#1e8449;font-size:14px;">
                                <i class="bi bi-check-circle-fill me-1"></i><span id="aa-count">0</span> student(s) will be advanced
                            </div>
                            <div style="font-size:11.5px;color:#2e7d32;margin-top:4px;">No grade, guidance, payment, or document checks apply at this level.</div>
                            <div id="aa-names" style="font-size:12px;color:#2e7d32;margin-top:6px;max-height:120px;overflow-y:auto;"></div>
                        </div>
                        <div id="aa-none" style="display:none;text-align:center;padding:20px;color:var(--muted);">
                            No students found for this grade/school year.
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb;padding:12px 20px;justify-content:space-between;">
                    <button class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-dash btn-success" id="aa-confirm-btn" onclick="confirmAutoAdvance()" style="display:none;">
                        <i class="bi bi-check2 me-1"></i> Confirm Advancement
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <div class="modal fade" id="walkInEnrollmentModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-xl">

            <div class="modal-content modal-content-styled">

                <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #0056b3);">

                    <h5 class="modal-title" style="color:#fff;"><i class="bi bi-person-plus-fill me-2"></i>Walk-in Enrollment</h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body modal-body-styled">

                    <div style="background:#e8f4f8; border:1px solid #b8d4e8; border-radius:8px; padding:14px 16px; display:flex; align-items:center; gap:10px; margin-bottom:20px;">

                        <i class="bi bi-info-circle-fill" style="font-size:20px; color:#0056b3;"></i>

                        <div>

                            <div style="font-weight:700; font-size:13px; color:#004085;">Walk-in Enrollment</div>

                            <div style="font-size:12px; color:#004085;">Fill out this form for parents who enroll in person. Login credentials will be auto-generated and sent to the guardian's email after approval.</div>

                        </div>

                    </div>

                    <form id="walkInEnrollmentForm">

                        <input type="hidden" id="walkin-reenroll" name="reenroll_student_id" value="">

                        <!-- Personal Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-person me-2"></i>Personal Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="dash-form-label">First Name *</label>

                                    <input type="text" id="walkin-first-name" class="dash-form-control" placeholder="Enter first name" required>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Middle Name</label>

                                    <input type="text" id="walkin-middle-name" class="dash-form-control" placeholder="Enter middle name (optional)">

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Last Name *</label>

                                    <input type="text" id="walkin-last-name" class="dash-form-control" placeholder="Enter last name" required>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Suffix</label>

                                    <input type="text" id="walkin-suffix" class="dash-form-control" placeholder="Jr., Sr., III">

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Gender *</label>

                                    <select id="walkin-gender" class="dash-form-control" required>

                                        <option value="">Select</option>

                                        <option value="male">Male</option>

                                        <option value="female">Female</option>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Birthdate *</label>

                                    <input type="date" id="walkin-birthdate" class="dash-form-control" placeholder="Select birthdate" required>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Place of Birth *</label>

                                    <input type="text" id="walkin-place-of-birth" class="dash-form-control" required placeholder="e.g., Manila">

                                </div>

                                <div class="col-md-6">

                                    <label class="dash-form-label">Nationality</label>

                                    <input type="text" id="walkin-nationality" class="dash-form-control" placeholder="e.g., Filipino">

                                </div>

                            </div>

                        </div>

                        <!-- Parent Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-people me-2"></i>Parent Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="dash-form-label">Mother's Full Name *</label>

                                    <input type="text" id="walkin-mother-name" class="dash-form-control" placeholder="Mother's full name" required>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Mother's Age *</label>

                                    <input type="number" id="walkin-mother-age" class="dash-form-control" placeholder="Age" required min="1" max="120">

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Religious Affiliation *</label>

                                    <input type="text" id="walkin-religious-affiliation" class="dash-form-control" placeholder="e.g., Roman Catholic" required>

                                </div>

                                <div class="col-md-6">

                                    <label class="dash-form-label">Father's Full Name *</label>

                                    <input type="text" id="walkin-father-name" class="dash-form-control" placeholder="Father's full name" required>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Father's Age *</label>

                                    <input type="number" id="walkin-father-age" class="dash-form-control" placeholder="Age" required min="1" max="120">

                                </div>

                            </div>

                        </div>

                        <!-- Academic Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-book me-2"></i>Academic Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="dash-form-label">Grade Level *</label>

                                    <select id="walkin-grade-level" class="dash-form-control" required onchange="updateWalkinPaymentOptions()">

                                        <option value="">Select</option>

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

                                <div class="col-md-4">

                                    <label class="dash-form-label">Student Type *</label>

                                    <select id="walkin-student-type" class="dash-form-control" required>

                                        <option value="">Select</option>

                                        <option value="new">New</option>

                                        <option value="transferee">Transferee</option>

                                        <option value="returning">Returning</option>

                                    </select>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Last School Attended</label>

                                    <input type="text" id="walkin-last-school" class="dash-form-control" placeholder="Enter previous school name">

                                </div>

                            </div>

                        </div>

                        <!-- Address Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-geo-alt me-2"></i>Address Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-3">

                                    <label class="dash-form-label">Region *</label>

                                    <select id="walkin-region" class="dash-form-control" required>
                                        <option value="">Loading regions...</option>
                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Province *</label>

                                    <select id="walkin-province" class="dash-form-control" required disabled>
                                        <option value="">Select Province</option>
                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">City/Municipality *</label>

                                    <select id="walkin-city" class="dash-form-control" required disabled>
                                        <option value="">Select City/Municipality</option>
                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="dash-form-label">Barangay *</label>

                                    <select id="walkin-barangay" class="dash-form-control" required disabled>
                                        <option value="">Select Barangay</option>
                                    </select>

                                </div>

                                <div class="col-md-8">

                                    <label class="dash-form-label">Street / Purok / House No. *</label>

                                    <input type="text" id="walkin-street-address" class="dash-form-control" placeholder="Enter Street / Purok / House No." required>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Zip Code</label>

                                    <input type="text" id="walkin-zip-code" class="dash-form-control" placeholder="Enter zip code">

                                </div>

                            </div>

                        </div>

                        <!-- Guardian Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-shield-check me-2"></i>Guardian Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="dash-form-label">Full Name *</label>

                                    <input type="text" id="walkin-guardian-name" class="dash-form-control" placeholder="Enter guardian name" required>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Relationship *</label>

                                    <select id="walkin-relationship" class="dash-form-control" required>

                                        <option value="">Select</option>

                                        <option value="father">Father</option>

                                        <option value="mother">Mother</option>

                                        <option value="guardian">Guardian</option>

                                        <option value="sibling">Sibling</option>

                                        <option value="grandparent">Grandparent</option>

                                        <option value="relative">Relative</option>

                                    </select>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Guardian Occupation</label>

                                    <input type="text" id="walkin-guardian-occupation" class="dash-form-control" placeholder="Enter occupation (optional)">

                                </div>

                                <div class="col-md-6">

                                    <label class="dash-form-label">Guardian Phone *</label>
                                    <div style="display:flex;align-items:stretch;border:1.5px solid #c8d6f0;border-radius:6px;overflow:hidden;background:#fff;">
                                        <span style="padding:0 12px;background:#f0f4ff;color:#1d3557;font-weight:700;font-size:13px;display:flex;align-items:center;border-right:1.5px solid #c8d6f0;white-space:nowrap;">+63</span>
                                        <input type="tel" id="walkin-guardian-phone" class="dash-form-control"
                                            style="border:none;border-radius:0;flex:1;min-width:0;"
                                            placeholder="9XXXXXXXXX" maxlength="10"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            required>
                                    </div>
                                    <small class="text-muted-alt">10 digits after +63 (e.g. 9123456789)</small>

                                </div>

                                <div class="col-md-6">

                                    <label class="dash-form-label">Guardian Email (for credentials) *</label>

                                    <input type="email" id="walkin-guardian-email" class="dash-form-control"
                                        placeholder="example@gmail.com" required
                                        oninput="validateWalkinGmail(this)">

                                    <small class="text-muted-alt" id="walkin-email-hint">Must be a Gmail address — credentials will be sent here after approval.</small>

                                </div>

                            </div>

                        </div>

                        <!-- Health Information -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-heart-pulse me-2"></i>Health Information

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="dash-form-label">Blood Type</label>

                                    <select id="walkin-blood-type" class="dash-form-control">

                                        <option value="">Select</option>

                                        <option value="A+">A+</option>

                                        <option value="A-">A-</option>

                                        <option value="B+">B+</option>

                                        <option value="B-">B-</option>

                                        <option value="AB+">AB+</option>

                                        <option value="AB-">AB-</option>

                                        <option value="O+">O+</option>

                                        <option value="O-">O-</option>

                                    </select>

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Allergies</label>

                                    <input type="text" id="walkin-allergies" class="dash-form-control" placeholder="List any known allergies">

                                </div>

                                <div class="col-md-4">

                                    <label class="dash-form-label">Medical Conditions</label>

                                    <input type="text" id="walkin-medical-conditions" class="dash-form-control" placeholder="Any medical conditions">

                                </div>

                            </div>

                        </div>

                        <!-- Payment Options -->

                        <div class="mb-4">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                                <i class="bi bi-credit-card me-2"></i>Payment Option

                            </h6>

                            <div class="row g-3">

                                <div class="col-md-6 col-lg-3">

                                    <div class="payment-option-card" id="walkin-card-opt-a" onclick="selectWalkinPaymentOption('A')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                        <div class="payment-option-header">

                                            <h6>Option A</h6>

                                        </div>

                                        <p class="text-muted mb-0" style="font-size:12px;">Cash Basis (20% discount)</p>

                                        <div class="payment-option-features mt-2">

                                            <span style="font-size:18px; font-weight:700; color:var(--gold);" id="walkin-opt-a-total">₱14,504</span>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6 col-lg-3">

                                    <div class="payment-option-card" id="walkin-card-opt-b" onclick="selectWalkinPaymentOption('B')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                        <div class="payment-option-header">

                                            <h6>Option B</h6>

                                        </div>

                                        <p class="text-muted mb-0" style="font-size:12px;">Monthly Payment (9 months)</p>

                                        <div class="payment-option-features mt-2">

                                            <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-b-monthly">₱1,056.10/mo</span>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6 col-lg-3" id="walkin-opt-c-container">

                                    <div class="payment-option-card" id="walkin-card-opt-c" onclick="selectWalkinPaymentOption('C')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                        <div class="payment-option-header">

                                            <h6>Option C</h6>

                                        </div>

                                        <p class="text-muted mb-0" style="font-size:12px;">Elementary Monthly (Grade 1-6)</p>

                                        <div class="payment-option-features mt-2">

                                            <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-c-monthly">₱1,278.32/mo</span>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6 col-lg-3" id="walkin-opt-d-container">

                                    <div class="payment-option-card" id="walkin-card-opt-d" onclick="selectWalkinPaymentOption('D')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                        <div class="payment-option-header">

                                            <h6>Option D</h6>

                                        </div>

                                        <p class="text-muted mb-0" style="font-size:12px;">Nursery Monthly (Nursery/Kinder)</p>

                                        <div class="payment-option-features mt-2">

                                            <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-d-monthly">₱1,278.32/mo</span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div id="walkin-payment-breakdown" style="background:#f8f9fa; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-top:20px; display:none;">

                                <h6 style="color:var(--blue); font-weight:700; margin-bottom:16px;">

                                    <i class="bi bi-receipt-cutoff me-2"></i>Payment Breakdown

                                </h6>

                                <div id="walkin-breakdown-content"></div>

                            </div>

                            <input type="hidden" id="walkin-payment-option">

                            <input type="hidden" id="walkin-downpayment-amount">

                            <input type="hidden" id="walkin-monthly-amount">

                            <input type="hidden" id="walkin-total-amount">

                        </div>

                    </form>

                </div>

                <div class="modal-footer modal-footer-styled">

                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <button type="button" class="btn-dash btn-primary" onclick="submitWalkInEnrollment()"><i class="bi bi-check-lg me-1"></i>Submit Enrollment</button>

                </div>

            </div>

        </div>

    </div>

    

    <?php echo $__env->make('admin.sections.teachers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('admin.sections.teacher-assignments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="modal fade" id="assignmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-styled">
                <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #7b1fa2, #4a148c);">
                    <h5 class="modal-title" style="color:#fff;"><i class="bi bi-star-fill me-2"></i><span id="assignmentModalTitle">Add Advisory Assignment</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-styled">
                    <form id="assignmentForm" onsubmit="saveAssignment(event)">
                        <input type="hidden" id="assignment-id" value="">
                        <div class="mb-3">
                            <label class="dash-form-label">Teacher <span class="text-danger">*</span></label>
                            <select id="assignment-teacher" class="dash-form-control" required>
                                <option value="">Select teacher...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="dash-form-label">Section <span class="text-danger">*</span></label>
                            <select id="assignment-section" class="dash-form-control" required>
                                <option value="">Select section...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="dash-form-label">School Year <span class="text-danger">*</span></label>
                            <select id="assignment-school-year" class="dash-form-control" required>
                                <?php
                                    $amDefault = $currentSchoolYear ?? '';
                                ?>
                                <?php $__currentLoopData = \App\Models\Setting::schoolYearOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sy); ?>" <?php echo e($sy === $amDefault ? 'selected' : ''); ?>><?php echo e($sy); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size:12px; border-radius:8px;">
                            <i class="bi bi-info-circle me-1"></i> This teacher will be set as the <strong>advisory (homeroom) teacher</strong> for the selected section. To assign subjects and schedules, use <strong>Schedule Management</strong>.
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-check-lg me-1"></i> <span id="assignmentSaveBtn">Save Assignment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <?php echo $__env->make('admin.sections.enrollment', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.installments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="modal fade" id="adminPromissoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
            <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background:linear-gradient(135deg,#e65100,#f5a623);color:#fff;border:0;border-radius:14px 14px 0 0;padding:18px 24px;">
                    <div>
                        <h5 class="modal-title mb-0" style="font-weight:700;font-size:16px;">
                            <i class="bi bi-file-earmark-text me-2"></i>Promissory Note
                        </h5>
                        <div style="font-size:11px;opacity:0.85;margin-top:2px;">Record parent payment commitment</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div style="background:#f8f9fa;border-radius:8px;padding:12px 16px;margin-bottom:18px;">
                        <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;">Student</div>
                        <div style="font-size:15px;font-weight:700;color:var(--blue);" id="apn-student-display">—</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-lbl">Outstanding Balance</label>
                            <input type="number" id="apn-amount-overdue" step="0.01" readonly class="form-fld" style="background:#f8f9fa;">
                        </div>
                        <div class="col-6">
                            <label class="form-lbl">Amount Promised <span style="color:red;">*</span></label>
                            <input type="number" id="apn-amount-promised" step="0.01" min="1" required class="form-fld">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Promise Date <span style="color:red;">*</span></label>
                        <input type="date" id="apn-promise-date" required class="form-fld">
                    </div>
                    <div class="mb-3">
                        <label class="form-lbl">Parent / Guardian Name</label>
                        <input type="text" id="apn-guardian" placeholder="e.g. Juan dela Cruz" class="form-fld">
                    </div>
                    <div class="mb-2">
                        <label class="form-lbl">Remarks</label>
                        <textarea id="apn-remarks" rows="2" class="form-fld" placeholder="Optional notes..." style="resize:none;height:68px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 24px;gap:8px;">
                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-dash" id="apn-save-btn"
                        style="background:linear-gradient(135deg,#e65100,#f5a623);color:#fff;"
                        onclick="saveAdminPromissoryNote()">
                        <i class="bi bi-file-earmark-check"></i> Save Promissory Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <?php echo $__env->make('admin.sections.fees', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.subjects', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.sections', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.schedules', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.guidance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    
    <div class="modal fade" id="guidanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3); color:#fff; border:0; padding:20px 24px;">
                    <h5 class="modal-title" id="guidanceModalTitle"><i class="bi bi-journal-medical me-2"></i>Add Guidance Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <form id="guidanceForm">
                        <input type="hidden" id="guidance-id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Student *</label>
                                <div style="position:relative;">
                                    <input type="text" id="guidance-student-search" class="form-fld" placeholder="Type at least 2 letters of the student's name..." autocomplete="off" oninput="guidanceStudentSearch(this.value)">
                                    <input type="hidden" id="guidance-student">
                                    <div id="guidance-student-results" style="display:none;position:absolute;z-index:1000;top:100%;left:0;right:0;background:#fff;border:1px solid #dde3ec;border-radius:8px;max-height:220px;overflow-y:auto;box-shadow:0 6px 16px rgba(0,0,0,0.12);margin-top:2px;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Date *</label>
                                <input type="date" id="guidance-date" class="form-fld" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Concern Type *</label>
                                <select id="guidance-concern-type" class="form-fld" required>
                                    <option value="">Select Type</option>
                                    <option value="Behavioral">Behavioral</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Emotional">Emotional</option>
                                    <option value="Family">Family</option>
                                    <option value="Social">Social</option>
                                    <option value="Health">Health</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Counselor *</label>
                                <select id="guidance-counselor" class="form-fld" required>
                                    <option value="">Select Counselor</option>
                                    <?php $__currentLoopData = $guidanceCounselors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gc->id); ?>" <?php echo e(auth()->id() === $gc->id ? 'selected' : ''); ?>>
                                            <?php echo e($gc->name); ?> (<?php echo e(ucfirst($gc->role)); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-lbl">Concern Description *</label>
                                <textarea id="guidance-description" class="form-fld" rows="3" required placeholder="Describe the concern..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-lbl">Action Taken</label>
                                <textarea id="guidance-action" class="form-fld" rows="2" placeholder="What actions were taken..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-lbl">Recommendations</label>
                                <textarea id="guidance-recommendations" class="form-fld" rows="2" placeholder="Recommendations for student..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Follow-up Date</label>
                                <input type="date" id="guidance-follow-up" class="form-fld">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Status *</label>
                                <select id="guidance-status" class="form-fld" required>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-lbl">Additional Notes</label>
                                <textarea id="guidance-notes" class="form-fld" rows="2" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:16px 24px;">
                    <button type="button" id="guidance-delete-btn" class="btn-dash btn-secondary" onclick="deleteGuidanceRecordFromModal()" style="display:none; color:var(--red); margin-right:auto;">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-dash btn-primary" onclick="saveGuidanceRecord()">
                        <i class="bi bi-check-lg me-1"></i> Save Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <?php echo $__env->make('admin.sections.announcements', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.news', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);color:#fff;border:none;">
                    <h5 class="modal-title" style="font-weight:700;">Edit Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" id="editAnnId">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="editAnnTitle" class="form-fld form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="editAnnCategory" class="form-fld form-control">
                                <option value="academic">Academic</option>
                                <option value="reminder">Reminder</option>
                                <option value="activity">Activity</option>
                                <option value="general">General</option>
                                <option value="enrollment">Enrollment</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Audience</label>
                            <select id="editAnnAudience" class="form-fld form-control">
                                <option value="all">All</option>
                                <option value="parents">Parents</option>
                                <option value="teachers">Teachers</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Posted Date</label>
                        <input type="datetime-local" id="editAnnPostedAt" class="form-fld form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea id="editAnnContent" class="form-fld form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Replace Image (optional)</label>
                        <input type="file" id="editAnnImage" class="form-fld form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditAnnouncement()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editNewsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);color:#fff;border:none;">
                    <h5 class="modal-title" style="font-weight:700;">Edit News Article</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" id="editNewsId">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="editNewsTitle" class="form-fld form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="editNewsCategory" class="form-fld form-control">
                                <option value="academic">Academic</option>
                                <option value="events">Events</option>
                                <option value="activity">Activity</option>
                                <option value="achievement">Achievement</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Posted Date</label>
                            <input type="datetime-local" id="editNewsPostedAt" class="form-fld form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea id="editNewsBody" class="form-fld form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Replace Image (optional)</label>
                        <input type="file" id="editNewsImage" class="form-fld form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditNews()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openEditAnnouncementModal(id, title, content, category, audience, postedAt) {
        document.getElementById('editAnnId').value = id;
        document.getElementById('editAnnTitle').value = title;
        document.getElementById('editAnnContent').value = content;
        document.getElementById('editAnnCategory').value = category;
        document.getElementById('editAnnAudience').value = audience;
        document.getElementById('editAnnPostedAt').value = postedAt;
        document.getElementById('editAnnImage').value = '';
        new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
    }

    async function submitEditAnnouncement() {
        var id = document.getElementById('editAnnId').value;
        var fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('title', document.getElementById('editAnnTitle').value);
        fd.append('content', document.getElementById('editAnnContent').value);
        fd.append('category', document.getElementById('editAnnCategory').value);
        fd.append('audience', document.getElementById('editAnnAudience').value);
        fd.append('posted_at', document.getElementById('editAnnPostedAt').value);
        var imgFile = document.getElementById('editAnnImage').files[0];
        if (imgFile) fd.append('image', imgFile);

        try {
            var res = await fetch('/admin/announcements/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            });
            if (res.ok) {
                window.location.reload();
            } else {
                var data = await res.json().catch(function(){ return {}; });
                showToast(data.message || 'Failed to update announcement.', 'error');
            }
        } catch (e) {
            showToast('Failed to update announcement.', 'error');
        }
    }

    function openEditNewsModal(id, title, body, category, postedAt) {
        document.getElementById('editNewsId').value = id;
        document.getElementById('editNewsTitle').value = title;
        document.getElementById('editNewsBody').value = body;
        document.getElementById('editNewsCategory').value = category;
        document.getElementById('editNewsPostedAt').value = postedAt;
        document.getElementById('editNewsImage').value = '';
        new bootstrap.Modal(document.getElementById('editNewsModal')).show();
    }

    async function submitEditNews() {
        var id = document.getElementById('editNewsId').value;
        var fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('title', document.getElementById('editNewsTitle').value);
        fd.append('body', document.getElementById('editNewsBody').value);
        fd.append('category', document.getElementById('editNewsCategory').value);
        fd.append('posted_at', document.getElementById('editNewsPostedAt').value);
        var imgFile = document.getElementById('editNewsImage').files[0];
        if (imgFile) fd.append('image', imgFile);

        try {
            var res = await fetch('/admin/news/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            });
            if (res.ok) {
                window.location.reload();
            } else {
                var data = await res.json().catch(function(){ return {}; });
                showToast(data.message || 'Failed to update news article.', 'error');
            }
        } catch (e) {
            showToast('Failed to update news article.', 'error');
        }
    }
    </script>

    

    <?php echo $__env->make('admin.sections.reports', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('admin.sections.grade-oversight', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="modal fade" id="gradeRejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none;">
                <div style="background:linear-gradient(135deg,#c62828,#e53935); padding:20px 24px; display:flex; align-items:center; gap:12px;">
                    <i class="bi bi-x-circle-fill" style="font-size:22px; color:#fff;"></i>
                    <div style="color:#fff; font-size:16px; font-weight:700;">Return Grades for Revision</div>
                </div>
                <div style="padding:24px;">
                    <label style="font-size:13px; font-weight:600; color:var(--text); margin-bottom:6px; display:block;">Reason / Feedback for Teacher</label>
                    <textarea id="gradeRejectReason" rows="3" style="width:100%; border:1.5px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; resize:vertical;" placeholder="e.g. Please double-check grades for Section A Term 2..."></textarea>
                </div>
                <div style="padding:0 24px 20px; display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="gradeRejectConfirmBtn" onclick="confirmRejectGrades()">
                        <i class="bi bi-arrow-return-left me-1"></i> Return to Teacher
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <?php echo $__env->make('admin.sections.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="modal fade" id="msgViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);border:none;padding:16px 22px;">
                    <div style="flex:1;min-width:0;">
                        <h5 id="msgModalSubject" class="modal-title" style="color:#fff;font-weight:700;font-size:15px;margin:0;"></h5>
                        <div id="msgModalMeta" style="font-size:11px;color:rgba(255,255,255,.65);margin-top:3px;"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                        <div style="background:#f8faff;border-radius:10px;padding:12px 14px;">
                            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">From</div>
                            <div id="msgModalName" style="font-size:13px;font-weight:700;color:#1e293b;"></div>
                            <div id="msgModalEmail" style="font-size:12px;color:#64748b;margin-top:2px;"></div>
                        </div>
                        <div style="background:#f8faff;border-radius:10px;padding:12px 14px;">
                            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Contact & Date</div>
                            <div id="msgModalPhone" style="font-size:13px;font-weight:600;color:#1e293b;"></div>
                            <div id="msgModalDate"  style="font-size:12px;color:#64748b;margin-top:2px;"></div>
                        </div>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:16px 18px;">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Message</div>
                        <div id="msgModalBody" style="font-size:13px;color:#374151;line-height:1.7;white-space:pre-wrap;"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e8edf5;padding:14px 22px;gap:8px;justify-content:space-between;">
                    <div id="msgModalStatusBtns" style="display:flex;gap:8px;"></div>
                    <div style="display:flex;gap:8px;">
                        <a id="msgModalReplyLink" href="#"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1a3a6c;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                            <i class="bi bi-envelope-arrow-up-fill"></i> Reply via Email
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
    var _currentMsgId = null;

    function openMsgModal(id, name, email, phone, subject, message, date, status) {
        _currentMsgId = id;
        document.getElementById('msgModalSubject').textContent = subject;
        document.getElementById('msgModalMeta').textContent    = date;
        document.getElementById('msgModalName').textContent    = name;
        document.getElementById('msgModalEmail').textContent   = email;
        document.getElementById('msgModalPhone').textContent   = phone || '—';
        document.getElementById('msgModalDate').textContent    = date;
        document.getElementById('msgModalBody').textContent    = message;
        document.getElementById('msgModalReplyLink').href      = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);

        // Status action buttons inside modal
        var btns = '';
        if (status !== 'read')    btns += '<button onclick="updateMsgStatus(' + id + ',\'read\',true)"    style="padding:8px 14px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-eye"></i> Mark as Read</button>';
        if (status !== 'replied') btns += '<button onclick="updateMsgStatus(' + id + ',\'replied\',true)" style="padding:8px 14px;background:#f0fdf4;color:#15803d;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-reply-fill"></i> Mark as Replied</button>';
        document.getElementById('msgModalStatusBtns').innerHTML = btns;

        // Auto-mark as read when opened
        if (status === 'unread') updateMsgStatus(id, 'read', false);

        new bootstrap.Modal(document.getElementById('msgViewModal')).show();
    }

    function updateMsgStatus(id, status, closeModal) {
        fetch('/admin/messages/' + id + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ status: status })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) return;
            // Update row background + badge
            var row = document.getElementById('msg-row-' + id);
            if (row) {
                row.style.background = '#fff';
                var badge = row.querySelector('span[style*="border-radius:20px"]');
                var cfg = { unread:['#fef3c7','#92400e','Unread'], read:['#eff6ff','#1d4ed8','Read'], replied:['#f0fdf4','#15803d','Replied'] }[status];
                if (badge && cfg) {
                    badge.style.background = cfg[0];
                    badge.style.color      = cfg[1];
                    badge.textContent      = cfg[2];
                }
            }
            // Update sidebar badge count
            var sidebarBadge = document.querySelector('#nav-messages .sidebar-badge');
            if (sidebarBadge) {
                var count = parseInt(sidebarBadge.textContent) || 0;
                if (status !== 'unread' && count > 0) {
                    count--;
                    if (count === 0) sidebarBadge.style.display = 'none';
                    else sidebarBadge.textContent = count;
                }
            }
            if (closeModal) {
                var m = bootstrap.Modal.getInstance(document.getElementById('msgViewModal'));
                if (m) m.hide();
            }
        });
    }

    function deleteMsg(id) {
        showDeleteConfirm('Delete this message? This cannot be undone.', function () {
            fetch('/admin/messages/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var row = document.getElementById('msg-row-' + id);
                    if (row) row.remove();
                    showAdminToast('Message deleted.', 'success');
                }
            });
        });
    }
    </script>

    <script>
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // GRADE OVERSIGHT
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    let _pendingRejectPayload = null;

    function loadGradeSubmissions() {
        const list = document.getElementById('gradeSubmissionsList');
        const empty = document.getElementById('gradeOversightEmpty');
        list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-hourglass-split" style="font-size:32px;display:block;margin-bottom:8px;"></i>Loading...</div>';
        empty.style.display = 'none';

        fetch('/admin/grades/pending', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(res => {
                const data = res.data || [];
                // Update sidebar badge
                const badge = document.getElementById('gradeOversightBadge');
                if (data.length > 0) {
                    badge.textContent = data.length;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }

                if (data.length === 0) {
                    list.innerHTML = '';
                    empty.style.display = '';
                    return;
                }

                const termLabel = t => t == 1 ? '1st Term' : t == 2 ? '2nd Term' : '3rd Term';

                list.innerHTML = data.map((group, i) => `
                <div style="background:#fff;border-radius:12px;border:1px solid var(--border);margin-bottom:16px;overflow:hidden;">
                    <div style="background:#f8f9fa;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:14px;font-weight:700;color:var(--text);">
                                <i class="bi bi-person-badge me-2" style="color:var(--blue);"></i>${group.teacher}
                            </div>
                            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                                ${group.subject} &nbsp;·&nbsp; ${termLabel(group.term)} &nbsp;·&nbsp; S.Y. ${group.school_year}
                                &nbsp;·&nbsp; <strong>${group.count}</strong> student(s)
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button class="action-btn edit" title="Approve All" style="background:#e8f5e9;color:#2e7d32;padding:6px 14px;font-size:12px;font-weight:600;border-radius:8px;display:flex;align-items:center;gap:5px;"
                                onclick="approveGradeGroup(${group.teacher_id}, ${group.subject_id ?? 'null'}, ${group.term}, '${group.school_year}', this)">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="action-btn delete" title="Return for Revision" style="background:#ffebee;color:#c62828;padding:6px 14px;font-size:12px;font-weight:600;border-radius:8px;display:flex;align-items:center;gap:5px;"
                                onclick="openGradeReject(${group.teacher_id}, ${group.subject_id ?? 'null'}, ${group.term}, '${group.school_year}')">
                                <i class="bi bi-arrow-return-left"></i> Return
                            </button>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="dash-table" style="margin:0;">
                            <thead><tr><th>#</th><th>Student Name</th><th>Grade</th><th>Remarks</th></tr></thead>
                            <tbody>
                                ${group.grades.map((g, j) => {
                                    const isDesc = g.descriptive_grade && g.descriptive_grade !== '';
                                    const gradeDisplay = isDesc
                                        ? `<strong style="color:#1565c0;">${g.descriptive_grade}</strong>`
                                        : (g.grade !== null && g.grade !== undefined
                                            ? `<strong style="color:${parseFloat(g.grade) >= 75 ? 'var(--green)' : 'var(--red)'};">${g.grade}</strong>`
                                            : '<span style="color:#ccc;">—</span>');
                                    const isPassed = isDesc
                                        ? g.descriptive_grade !== 'DNME'
                                        : parseFloat(g.grade) >= 75;
                                    const remarksBg = isPassed ? '#e8f5e9' : '#ffebee';
                                    const remarksColor = isPassed ? '#2e7d32' : '#c62828';
                                    return `<tr>
                                        <td style="color:var(--muted);">${j+1}</td>
                                        <td style="font-weight:600;">${g.student}</td>
                                        <td>${gradeDisplay}</td>
                                        <td><span style="font-size:11px;padding:3px 8px;border-radius:4px;background:${remarksBg};color:${remarksColor};">${g.remarks || '—'}</span></td>
                                    </tr>`;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>`).join('');
            })
            .catch(() => {
                list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--red);">Failed to load grade submissions.</div>';
            });
    }

    function approveGradeGroup(teacherId, subjectId, term, schoolYear, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Approving...';
        fetch('/admin/grades/approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ teacher_id: teacherId, subject_id: subjectId === 'null' ? null : subjectId, term, school_year: schoolYear })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showCustomAlert('success', 'Approved', res.message);
                loadGradeSubmissions();
            } else {
                showCustomAlert('error', 'Error', res.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg"></i> Approve';
            }
        });
    }

    function openGradeReject(teacherId, subjectId, term, schoolYear) {
        _pendingRejectPayload = { teacher_id: teacherId, subject_id: subjectId === 'null' ? null : subjectId, term, school_year: schoolYear };
        document.getElementById('gradeRejectReason').value = '';
        new bootstrap.Modal(document.getElementById('gradeRejectModal')).show();
    }

    function confirmRejectGrades() {
        if (!_pendingRejectPayload) return;
        const reason = document.getElementById('gradeRejectReason').value.trim();
        const btn = document.getElementById('gradeRejectConfirmBtn');
        btn.disabled = true;
        fetch('/admin/grades/reject', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ ..._pendingRejectPayload, reason })
        })
        .then(r => r.json())
        .then(res => {
            bootstrap.Modal.getInstance(document.getElementById('gradeRejectModal')).hide();
            btn.disabled = false;
            if (res.success) {
                showCustomAlert('success', 'Returned', res.message);
                loadGradeSubmissions();
            } else {
                showCustomAlert('error', 'Error', res.message);
            }
        });
    }

    // Auto-load when navigating to grade-oversight section
    const _origShowSection = typeof showSection === 'function' ? showSection : null;
    document.addEventListener('DOMContentLoaded', () => {
        const _realShow = window.showSection;
        window.showSection = function(name) {
            _realShow(name);
            if (name === 'grade-oversight') loadGradeSubmissions();
        };
    });
    </script>

    

    <?php echo $__env->make('admin.sections.settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('admin.sections.summer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('admin.sections.assessment', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


</div>

<!-- Include Enrollment View Modal -->

<?php echo $__env->make('admin.enrollments.view-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Student View Modal -->

<div class="modal fade" id="studentViewModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2a6dd6); color:#fff; border:0; padding:20px 24px;">

                <div>

                    <h5 class="modal-title" style="font-weight:700; margin:0;" id="sv-name">Student Name</h5>

                    <small id="sv-ref" style="opacity:0.8;"></small>

                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body" style="padding:24px;">

                <div class="row g-3">

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-person me-2"></i>Personal Info</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>LRN:</strong> <span id="sv-lrn"></span></div>

                                <div><strong>Email:</strong> <span id="sv-email"></span></div>

                                <div><strong>Gender:</strong> <span id="sv-gender"></span></div>

                                <div><strong>Birthdate:</strong> <span id="sv-birthdate"></span></div>

                                <div><strong>Age:</strong> <span id="sv-age"></span></div>

                                <div><strong>Place of Birth:</strong> <span id="sv-pob"></span></div>

                                <div><strong>Student Type:</strong> <span id="sv-type"></span></div>

                                <div><strong>Joined:</strong> <span id="sv-joined"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-mortarboard me-2"></i>Enrollment</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Grade:</strong> <span id="sv-grade"></span></div>

                                <div><strong>Section:</strong> <span id="sv-section"></span></div>

                                <div><strong>Status:</strong> <span id="sv-status"></span></div>

                                <div><strong>Payment:</strong> <span id="sv-payment"></span></div>

                                <div><strong>Amount Paid:</strong> <span id="sv-amount"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-people me-2"></i>Guardian</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Name:</strong> <span id="sv-guardian"></span></div>

                                <div><strong>Relationship:</strong> <span id="sv-relation"></span></div>

                                <div><strong>Phone:</strong> <span id="sv-gphone"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-geo-alt me-2"></i>Address</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Street:</strong> <span id="sv-street"></span></div>

                                <div><strong>Barangay:</strong> <span id="sv-brgy"></span></div>

                                <div><strong>City:</strong> <span id="sv-city"></span></div>

                                <div><strong>Province:</strong> <span id="sv-province"></span></div>

                            </div>

                        </div>

                    </div>

                </div>

                
                <div class="row g-3 mt-1">

                    <div class="col-md-12">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-folder-check me-2"></i>Submitted Documents</h6>

                            <div id="sv-docs-loading" style="text-align:center;padding:20px;color:#999;">
                                <div class="spinner-border spinner-border-sm me-2"></div> Loading documents...
                            </div>

                            <div id="sv-docs-content" style="display:none;"></div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer" style="border:0; padding:16px 24px;">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <a id="sv-sf10-btn" href="#" target="_blank"
                   class="btn btn-success"
                   style="display:none; background:#1a6b2d; border-color:#1a6b2d;">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download SF10
                </a>

                <button type="button" class="btn btn-primary" id="sv-reenroll-btn" onclick="reEnrollStudent()" style="display:none;"><i class="bi bi-arrow-repeat me-1"></i> Re-Enroll</button>

            </div>

        </div>

    </div>

</div>

<!-- Student Edit Modal -->
<div class="modal fade" id="studentEditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">

            
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:22px 26px;position:relative;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:46px;height:46px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-fill-gear" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;color:#fff;line-height:1.1;">Edit Student</div>
                        <div id="se-header-name" style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">Loading student detailsâ€¦</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:16px;right:18px;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
                <input type="hidden" id="se-id">
            </div>

            
            <div style="background:#f8faff;border-bottom:1.5px solid #e2e8f0;padding:0 24px;">
                <div style="display:flex;gap:2px;overflow-x:auto;" id="se-tab-nav">
                    <button type="button" onclick="seTab('personal')" id="se-tab-btn-personal"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid #2563eb;background:transparent;color:#2563eb;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-person-fill"></i>Personal Info
                    </button>
                    <button type="button" onclick="seTab('guardian')" id="se-tab-btn-guardian"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-people-fill"></i>Guardian
                    </button>
                    <button type="button" onclick="seTab('enrollment')" id="se-tab-btn-enrollment"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-journal-check"></i>Enrollment
                    </button>
                    <button type="button" onclick="seTab('address')" id="se-tab-btn-address"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-geo-alt-fill"></i>Address
                    </button>
                </div>
            </div>

            
            <div style="padding:24px 26px;background:#fff;max-height:60vh;overflow-y:auto;">

                
                <div id="se-pane-personal">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-person-badge-fill me-1"></i> Basic Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Full Name</label>
                                <input type="text" id="se-name" class="form-fld" placeholder="Student full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Email Address</label>
                                <input type="email" id="se-email" class="form-fld" placeholder="Email address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">LRN (Learner Reference No.)</label>
                                <input type="text" id="se-lrn" class="form-fld" placeholder="Enter LRN" style="font-family:monospace;letter-spacing:.5px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Gender</label>
                                <select id="se-gender" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Birthdate</label>
                                <input type="date" id="se-birthdate" class="form-fld">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Place of Birth</label>
                                <input type="text" id="se-pob" class="form-fld" placeholder="City/Municipality">
                            </div>
                        </div>
                    </div>
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-shield-check me-1"></i> Account Settings
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Student Type</label>
                                <select id="se-type" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="new">New Student</option>
                                    <option value="transferee">Transferee</option>
                                    <option value="returning">Returning Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Account Status</label>
                                <select id="se-active" class="form-fld">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div id="se-pane-guardian" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-people-fill me-1"></i> Guardian / Parent Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Guardian Name</label>
                                <input type="text" id="se-guardian-name" class="form-fld" placeholder="Full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Relationship</label>
                                <select id="se-guardian-rel" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="guardian">Guardian</option>
                                    <option value="sibling">Sibling</option>
                                    <option value="grandparent">Grandparent</option>
                                    <option value="relative">Relative</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Contact Number</label>
                                <input type="text" id="se-guardian-phone" class="form-fld" placeholder="e.g. 09xxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Occupation</label>
                                <input type="text" id="se-guardian-occ" class="form-fld" placeholder="Occupation">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div id="se-pane-enrollment" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-journal-check me-1"></i> Academic Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Grade Level</label>
                                <select id="se-grade" class="form-fld">
                                    <option value="">-- Select Grade --</option>
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
                            <div class="col-md-6">
                                <label class="form-lbl">Section</label>
                                <input type="text" id="se-section" class="form-fld" placeholder="Section name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Enrollment Status</label>
                                <select id="se-enroll-status" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-cash-stack me-1"></i> Payment Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Payment Status</label>
                                <select id="se-pay-status" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Amount Paid (₱)</label>
                                <input type="number" id="se-pay-amount" class="form-fld" placeholder="0.00" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div id="se-pane-address" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-geo-alt-fill me-1"></i> Home Address
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-lbl">Street Address</label>
                                <input type="text" id="se-street" class="form-fld" placeholder="House/Block/Lot, Street">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Region</label>
                                <select id="se-region" class="form-fld">
                                    <option value="">Select Region</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Province</label>
                                <select id="se-province" class="form-fld" disabled>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">City/Municipality</label>
                                <select id="se-city" class="form-fld" disabled>
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Barangay</label>
                                <select id="se-brgy" class="form-fld" disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Zip Code</label>
                                <input type="text" id="se-zip" class="form-fld" placeholder="Zip code">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div style="padding:16px 26px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <button type="button" data-bs-dismiss="modal"
                    style="display:flex;align-items:center;gap:6px;padding:11px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg"></i>Cancel
                </button>
                <button type="button" onclick="saveStudent()"
                    style="display:flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(37,99,235,.25);">
                    <i class="bi bi-floppy-fill"></i>Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Summer Class Create/Edit Modal -->

<div class="modal fade" id="summerClassModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">

            <div class="modal-header" style="background:linear-gradient(135deg,#f5a623,#e67e22); color:#fff; border:0; padding:20px 24px;">

                <h5 class="modal-title" style="font-weight:700; margin:0;" id="summerModalTitle"><i class="bi bi-sun-fill me-2"></i>Create Summer Class</h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body" style="padding:24px;">

                <input type="hidden" id="sc-id">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-lbl">School Year *</label>

                        <select id="sc-sy" class="form-fld">
                            <?php
                                $currentSY = now()->month >= 6 ? now()->year : now()->year - 1;
                                for ($y = $currentSY + 1; $y >= 1994; $y--) {
                                    $sy = $y . '-' . ($y + 1);
                                    $selected = $y === $currentSY + 1 ? 'selected' : '';
                                    echo "<option value=\"$sy\" $selected>$sy</option>";
                                }
                            ?>
                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Grade Level *</label>

                        <select id="sc-grade" class="form-fld" onchange="filterSummerSubjectsByGrade()">

                            <option value="">— Select —</option>

                            <option value="grade1">Grade 1</option>

                            <option value="grade2">Grade 2</option>

                            <option value="grade3">Grade 3</option>

                            <option value="grade4">Grade 4</option>

                            <option value="grade5">Grade 5</option>

                            <option value="grade6">Grade 6</option>



                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Subject *</label>

                        <select id="sc-subject" class="form-fld">

                            <option value="">— Select Grade First —</option>

                            <?php $__currentLoopData = $allActiveSubjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php if($sub): ?>

                                <option value="<?php echo e($sub->id); ?>" data-grade="<?php echo e($sub->grade_level); ?>" hidden><?php echo e($sub->code ?? ''); ?> — <?php echo e($sub->name); ?></option>

                                <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Teacher</label>

                        <select id="sc-teacher" class="form-fld">

                            <option value="">— Assign Later —</option>

                            <?php $__currentLoopData = $allActiveTeachers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php if($t !== null): ?>

                                <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>

                                <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Start Date *</label>

                        <input type="date" id="sc-start" class="form-fld">

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">End Date *</label>

                        <input type="date" id="sc-end" class="form-fld">

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Room</label>

                        <input type="text" id="sc-room" class="form-fld" placeholder="e.g., Room 101">

                    </div>

                    <div class="col-md-6">

                        <label class="form-lbl">Max Slots</label>

                        <input type="number" id="sc-slots" class="form-fld" value="40" min="1" max="100">

                    </div>

                    <div class="col-12">

                        <label class="form-lbl">Schedule Description</label>

                        <input type="text" id="sc-sched" class="form-fld" placeholder="e.g., MWF 8:00 AM “ 10:00 AM">

                    </div>

                    <div class="col-12">

                        <label class="form-lbl">Remarks</label>

                        <textarea id="sc-remarks" class="form-fld" placeholder="Optional notes..."></textarea>

                    </div>

                </div>

            </div>

            <div class="modal-footer" style="border:0; padding:16px 24px;">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn btn-warning" onclick="saveSummerClass()" style="font-weight:600;">

                    <i class="bi bi-check-lg me-1"></i>Save Summer Class

                </button>

            </div>

        </div>

    </div>

</div>

<!-- Summer Class — Manage Students Modal -->
<div class="modal fade" id="summerStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f5a623,#e67e22); color:#fff; border:0; padding:20px 24px;">
                <div>
                    <h5 class="modal-title" style="font-weight:700; margin:0;"><i class="bi bi-people-fill me-2"></i>Manage Students</h5>
                    <div id="ss-subtitle" style="font-size:12px; opacity:0.85; margin-top:2px;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <input type="hidden" id="ss-class-id">
                <input type="hidden" id="ss-subject-id">
                <input type="hidden" id="ss-school-year">

                <h6 style="font-weight:700; font-size:13px; color:var(--blue); margin-bottom:10px;"><i class="bi bi-person-plus-fill me-1"></i>Add a Failing Student</h6>
                <div style="font-size:11.5px; color:var(--muted); margin-bottom:10px;">Students who have a failing average (below 75) in this subject this school year.</div>
                <div id="ss-eligible-loading" style="text-align:center; padding:16px; color:#999; font-size:12px;">
                    <div class="spinner-border spinner-border-sm me-2"></div> Loading eligible students...
                </div>
                <div id="ss-eligible-list" style="display:none; margin-bottom:22px;"></div>

                <h6 style="font-weight:700; font-size:13px; color:var(--blue); margin-bottom:10px;"><i class="bi bi-clipboard-check me-1"></i>Enrolled Students</h6>
                <div id="ss-enrolled-loading" style="text-align:center; padding:20px; color:#999;">
                    <div class="spinner-border spinner-border-sm me-2"></div> Loading...
                </div>
                <div id="ss-enrolled-list" style="display:none;"></div>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Payment Flow Modal -->
<?php
    $adminGcashNumber = \App\Models\Setting::get('gcash_number', null);
    $adminGcashName   = \App\Models\Setting::get('gcash_account_name', null);
    $adminGcashQr     = \App\Models\Setting::get('gcash_qr_path', null);
    $adminGcashQrUrl  = $adminGcashQr
        ? (str_starts_with($adminGcashQr, '/') || str_starts_with($adminGcashQr, 'http')
            ? asset($adminGcashQr) : asset("storage/{$adminGcashQr}"))
        : null;
?>
<div class="modal fade" id="paymentFlowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">

            
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;top:16px;right:16px;"></button>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:46px;height:46px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-credit-card-fill" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#fff;">Process Payment</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.75);margin-top:2px;">Student: <span id="pay-flow-student-name" style="font-weight:600;">—</span></div>
                    </div>
                </div>
                
                <div id="pf-amount-bubble" style="display:none;margin-top:14px;background:rgba(255,255,255,0.15);border-radius:12px;padding:12px 16px;display:none;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.5px;">Amount to Pay</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:1px;" id="pf-plan-label">—</div>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:#fff;" id="pf-amount-display">₱—</div>
                </div>
                
                <div style="display:flex;align-items:center;gap:8px;margin-top:14px;">
                    <div id="pf-pill-1" style="display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;background:rgba(255,255,255,0.9);font-size:11px;font-weight:700;color:#1a3a6c;">
                        <span style="width:18px;height:18px;border-radius:50%;background:#1a3a6c;color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;">1</span> Select Plan
                    </div>
                    <i class="bi bi-chevron-right" style="color:rgba(255,255,255,0.5);font-size:11px;"></i>
                    <div id="pf-pill-2" style="display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;background:rgba(255,255,255,0.2);font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);">
                        <span style="width:18px;height:18px;border-radius:50%;background:rgba(255,255,255,0.3);color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;">2</span> Payment
                    </div>
                </div>
            </div>

            
            <div style="padding:22px 24px;background:#f8faff;max-height:72vh;overflow-y:auto;">
                <input type="hidden" id="pay-flow-enrollment-id">
                <input type="hidden" id="pay-flow-grade">
                <input type="hidden" id="admin-selected-payment-option" value="">
                <input type="hidden" id="admin-selected-method" value="">
                <input type="hidden" id="admin-downpayment-amount" value="0">
                <input type="hidden" id="admin-monthly-amount" value="0">
                <input type="hidden" id="admin-total-amount" value="0">

                
                <div id="pay-flow-step-1">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;">
                        <i class="bi bi-list-check me-1" style="color:#1d4ed8;"></i> Choose Payment Plan
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div id="opt-a-container">
                            <button type="button" id="card-opt-a" onclick="selectAdminPaymentOption('A')"
                                style="width:100%;padding:14px 12px;border:2px solid #e2e8f0;border-radius:13px;background:#fff;cursor:pointer;text-align:left;transition:all .2s;font-family:inherit;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                    <div style="width:30px;height:30px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-star-fill" style="font-size:13px;color:#d97706;"></i>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:#1e3a5f;">Option A</span>
                                    <span style="font-size:9px;padding:1px 6px;border-radius:10px;background:#fef3c7;color:#d97706;font-weight:700;">20% OFF</span>
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">Full Payment</div>
                                <div style="font-size:15px;font-weight:800;color:#1d4ed8;"><span id="opt-a-total">—</span></div>
                            </button>
                        </div>
                        <div id="opt-b-container">
                            <button type="button" id="card-opt-b" onclick="selectAdminPaymentOption('B')"
                                style="width:100%;padding:14px 12px;border:2px solid #e2e8f0;border-radius:13px;background:#fff;cursor:pointer;text-align:left;transition:all .2s;font-family:inherit;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                    <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-calendar-month" style="font-size:13px;color:#1d4ed8;"></i>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:#1e3a5f;">Option B</span>
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">Tuition + Electric</div>
                                <div style="font-size:14px;font-weight:800;color:#1d4ed8;"><span id="opt-b-monthly">—</span><span style="font-size:10px;color:#94a3b8;font-weight:500;">/mo</span></div>
                            </button>
                        </div>
                        <div id="opt-c-container">
                            <button type="button" id="card-opt-c" onclick="selectAdminPaymentOption('C')"
                                style="width:100%;padding:14px 12px;border:2px solid #e2e8f0;border-radius:13px;background:#fff;cursor:pointer;text-align:left;transition:all .2s;font-family:inherit;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                    <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-calendar2-week" style="font-size:13px;color:#1d4ed8;"></i>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:#1e3a5f;">Option C</span>
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">Tuition + Misc + Electric</div>
                                <div style="font-size:14px;font-weight:800;color:#1d4ed8;"><span id="opt-c-monthly">—</span><span style="font-size:10px;color:#94a3b8;font-weight:500;">/mo</span></div>
                            </button>
                        </div>
                        <div id="opt-d-container">
                            <button type="button" id="card-opt-d" onclick="selectAdminPaymentOption('D')"
                                style="width:100%;padding:14px 12px;border:2px solid #e2e8f0;border-radius:13px;background:#fff;cursor:pointer;text-align:left;transition:all .2s;font-family:inherit;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                    <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-calendar2-week" style="font-size:13px;color:#1d4ed8;"></i>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:#1e3a5f;">Option D</span>
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">Full Monthly</div>
                                <div style="font-size:14px;font-weight:800;color:#1d4ed8;"><span id="opt-d-monthly">—</span><span style="font-size:10px;color:#94a3b8;font-weight:500;">/mo</span></div>
                            </button>
                        </div>
                    </div>
                    <div id="admin-payment-breakdown-display" style="display:none;margin-top:14px;padding:14px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;font-size:13px;">
                        <div id="admin-breakdown-content"></div>
                    </div>
                    <div id="admin-pay-button-container" style="display:none;margin-top:16px;text-align:right;">
                        <button type="button" onclick="showAdminPaymentMethodSelection()"
                            style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                            <i class="bi bi-arrow-right-circle-fill"></i> Continue to Payment
                        </button>
                    </div>
                </div>

                
                <div id="pay-flow-step-2" style="display:none;">

                    
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
                        <i class="bi bi-1-circle-fill me-1" style="color:#1d4ed8;font-size:13px;"></i> Payment Method
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                        <button type="button" id="card-cash" onclick="selectAdminPaymentMethod('cash')"
                            style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border:2px solid #e2e8f0;border-radius:12px;background:#fff;cursor:pointer;transition:all .2s;font-family:inherit;">
                            <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-cash-stack" style="font-size:18px;color:#16a34a;"></i>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#1e3a5f;">Cash</span>
                            <span style="font-size:10px;color:#94a3b8;">Walk-in cashier</span>
                        </button>
                        <button type="button" id="card-gcash" onclick="selectAdminPaymentMethod('gcash')"
                            style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border:2px solid #e2e8f0;border-radius:12px;background:#fff;cursor:pointer;transition:all .2s;font-family:inherit;">
                            <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-phone-fill" style="font-size:18px;color:#1d4ed8;"></i>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#1e3a5f;">GCash</span>
                            <span style="font-size:10px;color:#94a3b8;">Online transfer</span>
                        </button>
                    </div>

                    
                    <div id="admin-gcash-info" style="display:none;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                            <i class="bi bi-2-circle-fill me-1" style="color:#1d4ed8;font-size:13px;"></i> GCash Details
                        </div>
                        <div style="background:#fff;border:1.5px solid #bfdbfe;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(29,78,216,.06);">
                            <div style="display:flex;background:#f0f7ff;">
                                <button type="button" id="admin-gcash-tab-btn-number" onclick="switchAdminGcashTab('number')"
                                    style="flex:1;padding:10px 0;background:#fff;border:none;border-bottom:2px solid #1d4ed8;font-size:12px;font-weight:700;color:#1d4ed8;cursor:pointer;transition:all .2s;">
                                    <i class="bi bi-phone me-1"></i>Number
                                </button>
                                <button type="button" id="admin-gcash-tab-btn-qr" onclick="switchAdminGcashTab('qr')"
                                    style="flex:1;padding:10px 0;background:none;border:none;border-bottom:2px solid transparent;font-size:12px;font-weight:700;color:#94a3b8;cursor:pointer;transition:all .2s;">
                                    <i class="bi bi-qr-code me-1"></i>QR Code
                                </button>
                            </div>
                            <div id="admin-gcash-tab-number" style="padding:18px;text-align:center;">
                                <?php if($adminGcashNumber): ?>
                                    <div style="font-size:22px;font-weight:800;color:#1e3a5f;letter-spacing:4px;margin-bottom:3px;"><?php echo e($adminGcashNumber); ?></div>
                                    <?php if($adminGcashName): ?>
                                        <div style="font-size:12px;color:#64748b;margin-bottom:14px;"><?php echo e($adminGcashName); ?></div>
                                    <?php endif; ?>
                                    <button type="button" onclick="copyAdminGcashNumber('<?php echo e($adminGcashNumber); ?>')"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:7px 18px;background:#1d4ed8;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-copy" id="admin-gcash-copy-icon"></i><span id="admin-gcash-copy-label">Copy Number</span>
                                    </button>
                                <?php else: ?>
                                    <i class="bi bi-telephone-x" style="font-size:28px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                    <div style="font-size:12px;color:#94a3b8;">GCash number not configured in Settings.</div>
                                <?php endif; ?>
                            </div>
                            <div id="admin-gcash-tab-qr" style="display:none;padding:18px;text-align:center;">
                                <?php if($adminGcashQrUrl): ?>
                                    <img src="<?php echo e($adminGcashQrUrl); ?>" alt="GCash QR"
                                        style="max-width:170px;width:100%;border-radius:12px;box-shadow:0 4px 14px rgba(29,78,216,.15);">
                                    <div style="font-size:11px;color:#64748b;margin-top:10px;"><i class="bi bi-phone me-1"></i>Scan with GCash app</div>
                                <?php else: ?>
                                    <i class="bi bi-qr-code" style="font-size:34px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                    <div style="font-size:12px;color:#94a3b8;">QR code not configured in Settings.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div id="admin-payment-amount-section" style="display:none;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;" id="admin-fields-label">
                            <i class="bi bi-2-circle-fill me-1" style="color:#1d4ed8;font-size:13px;"></i> Payment Details
                        </div>
                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-lbl">Amount (PHP) <span style="color:#e53935;">*</span></label>
                                    <div style="position:relative;">
                                        <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:16px;font-weight:700;color:#64748b;">₱</span>
                                        <input type="number" id="admin-payment-amount" class="form-fld"
                                            placeholder="0.00" step="0.01" min="0"
                                            style="font-size:17px;font-weight:700;padding:11px 14px 11px 28px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-lbl">Reference No. <span style="font-weight:400;color:#94a3b8;">(optional)</span></label>
                                    <div style="position:relative;">
                                        <i class="bi bi-hash" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px;"></i>
                                        <input type="text" id="admin-payment-reference" class="form-fld"
                                            placeholder="Receipt or ref number"
                                            style="padding:11px 14px 11px 28px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div style="display:flex;justify-content:space-between;gap:10px;">
                            <button type="button"
                                onclick="document.getElementById('pay-flow-step-1').style.display='block';document.getElementById('pay-flow-step-2').style.display='none';document.getElementById('pf-pill-1').style.background='rgba(255,255,255,0.9)';document.getElementById('pf-pill-1').style.color='#1a3a6c';document.getElementById('pf-pill-2').style.background='rgba(255,255,255,0.2)';document.getElementById('pf-pill-2').style.color='rgba(255,255,255,0.6)';"
                                style="display:flex;align-items:center;gap:6px;padding:11px 18px;background:#f0f4ff;color:#1d4ed8;border:1.5px solid #bfdbfe;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                                <i class="bi bi-arrow-left"></i>Back
                            </button>
                            <button type="button" onclick="submitAdminPayment()"
                                style="flex:1;max-width:200px;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
                                <i class="bi bi-check-circle-fill"></i>Submit Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    #paymentFlowModal [id^="card-opt-"].pf-selected,
    #paymentFlowModal #card-cash.pf-selected,
    #paymentFlowModal #card-gcash.pf-selected {
        border-color:#1d4ed8 !important;
        background:#eff6ff !important;
        box-shadow:0 0 0 3px rgba(29,78,216,.12) !important;
    }
    .card.selected { border-color: var(--blue) !important; background: #e8f0fe !important; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<div class="modal fade" id="genericConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" id="genericConfirmHeader" style="color:#fff; border:0; padding:20px 24px;">
                <h5 class="modal-title" id="genericConfirmTitle" style="font-weight:700; margin:0;"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p id="genericConfirmMessage" style="font-size:15px; margin:0; color:#444;"></p>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px; gap:8px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="genericConfirmBtn"></button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" style="z-index:1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333); color:#fff; border:0; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:700; margin:0;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p id="deleteConfirmMessage" style="font-size:16px; margin:0;">Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash-fill me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Password-Confirmation Modal (for destructive student actions: archive / permanent delete) -->
<div class="modal fade" id="pwConfirmModal" tabindex="-1" style="z-index:1075;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333); color:#fff; border:0; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:700; margin:0;"><i class="bi bi-shield-lock-fill me-2"></i>Confirm Your Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p id="pwConfirmMessage" style="font-size:14px; color:#475569; margin:0 0 16px;">This action cannot be undone. Enter your account password to confirm.</p>
                <label class="form-lbl" for="pwConfirmInput">Your Password</label>
                <div style="position:relative;">
                    <input type="password" id="pwConfirmInput" class="form-fld" placeholder="Enter your password" style="width:100%;padding-right:42px;" autocomplete="current-password">
                    <button type="button" onclick="_togglePwConfirmVisibility()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:none;background:none;color:#94a3b8;cursor:pointer;padding:4px;">
                        <i class="bi bi-eye-fill" id="pwConfirmToggleIcon"></i>
                    </button>
                </div>
                <div id="pwConfirmError" style="display:none;color:#dc2626;font-size:12.5px;font-weight:600;margin-top:8px;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i><span id="pwConfirmErrorText">Incorrect password.</span>
                </div>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="pwConfirmBtn">
                    <i class="bi bi-check-lg me-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Conflict Confirmation Modal -->
<div class="modal fade" id="scheduleConflictConfirmModal" tabindex="-1" style="z-index:1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:0; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:700; margin:0;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Schedule Conflict Detected</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="font-size:14px;color:#64748b;margin:0 0 10px;">This time slot overlaps with an existing schedule:</p>
                <div id="scheduleConflictConfirmMessage" style="font-size:13px;background:#fdecea;border:1px solid #f5c6cb;border-radius:10px;padding:12px 14px;color:#c0392b;font-weight:600;line-height:1.6;"></div>
                <p style="font-size:13px;color:#64748b;margin:14px 0 0;">You can still save it — it will show a red warning on the schedule grid.</p>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmScheduleConflictBtn">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Save Anyway
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Promotion Confirmation Modal -->
<div class="modal fade" id="promoteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#198754,#157347); color:#fff; border:0; padding:20px 24px;">
                <h5 class="modal-title" style="font-weight:700; margin:0;"><i class="bi bi-arrow-up-circle-fill me-2"></i>Confirm Promotion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p id="promoteConfirmMessage" style="font-size:16px; margin:0;">Are you sure you want to promote these students?</p>
            </div>
            <div class="modal-footer" style="border:0; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmPromoteBtn">
                    <i class="bi bi-arrow-up-circle-fill me-1"></i>Promote
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Subjects Modal (section subject assignment) -->
<div class="modal fade" id="manualSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--ilc-blue),#0056b3);color:white;border:none;">
                <h5 class="modal-title"><i class="bi bi-book-fill me-2"></i>Manage Subjects — <span id="manual-section-display"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <input type="hidden" id="manual-section-id">
                <input type="hidden" id="manual-section-name">
                <input type="hidden" id="manual-section-grade">

                <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i>Check subjects to assign them to this section. Uncheck to remove.</p>

                <div class="table-responsive" style="max-height:320px;overflow-y:auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:40px;text-align:center;"></th>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody id="manage-subjects-list"></tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" id="toggle-add-subject-form" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-down me-1"></i>Add New Subject
                    </button>
                </div>
                <div id="add-subject-form-body" style="display:none;" class="mt-2 p-3 border rounded bg-light">
                    <h6 class="mb-2" style="font-size:13px;font-weight:600;">Add New Subject to Library</h6>
                    <div class="row g-2 align-items-center">
                        <div class="col-3">
                            <input type="text" id="add-subject-code" class="form-control form-control-sm" placeholder="Code (e.g. MATH1)">
                        </div>
                        <div class="col-5">
                            <input type="text" id="add-subject-name" class="form-control form-control-sm" placeholder="Subject Name">
                        </div>
                        <div class="col-4">
                            <input type="hidden" id="add-subject-grade">
                            <button type="button" id="add-subject-btn" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i>Add Subject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x me-1"></i>Cancel</button>
                <button type="button" id="manualSubjectSaveBtn" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<?php
    // Non-paginated flat data for schedule modal dropdowns
    $modalSections = \App\Models\Section::with('subjects:id,name,code')
        ->select('id','name','grade_level','school_year','is_active','room_number')
        ->where('is_active', true)
        ->orderBy('grade_level')->orderBy('name')
        ->get()
        ->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'grade_level' => $s->grade_level,
            'room_number' => $s->room_number,
            'subjects'    => $s->subjects->map(fn($subj) => ['id'=>$subj->id,'name'=>$subj->name,'code'=>$subj->code])->values()->toArray()
        ]);
    $modalTeachers = \App\Models\User::where('role','teacher')->where('is_active',true)
        ->select('id','name')->orderBy('name')->get();
    $modalAssignments = \App\Models\TeacherAssignment::select('teacher_id','section_id','subject_id','is_advisory')->get();

    // $allInstallmentData intentionally left empty — this used to loop over
    // every enrollment AND every student's payment record on every single
    // dashboard load (two nested loops building a full installment history
    // for each), but it only ever fed showInstallmentModal() via
    // .js-view-installments buttons — and those buttons exist ONLY inside
    // the Finance/Installments sections, which are permanently
    // redirect-only dead UI (see _PORTAL_SECTIONS in showSection()) and can
    // never actually be displayed. Confirmed via grep: no .js-view-installments
    // button exists anywhere reachable. See docs/system-improvement-plan.md.
    $allInstallmentData = [];
?>

<script>

    const timeSlots = [

        { label: '7:00-8:00 AM', start: '07:00', end: '08:00', isBreak: false },

        { label: '8:00-9:00 AM', start: '08:00', end: '09:00', isBreak: false },

        { label: '9:00-9:20 AM', start: '09:00', end: '09:20', isBreak: true, breakName: 'Health Break' },

        { label: '9:20-10:20 AM', start: '09:20', end: '10:20', isBreak: false },

        { label: '10:20-11:20 AM', start: '10:20', end: '11:20', isBreak: false },

        { label: '11:20-12:00 PM', start: '11:20', end: '12:00', isBreak: true, breakName: 'Lunch Break' },

        { label: '12:00-1:00 PM', start: '12:00', end: '13:00', isBreak: false },

        { label: '1:00-2:00 PM', start: '13:00', end: '14:00', isBreak: false, breakName: 'Aral Program' }

    ];

    // â”€â”€ Grade-specific Time Slots (Nursery/Kindergarten have fewer hours) â”€â”€

    const gradeTimeSlots = {

        'nursery': timeSlots.slice(0, 4), // 4 hours only

        'kindergarten': timeSlots.slice(0, 4), // 4 hours only

        'default': timeSlots // Full day for Grade 1-6

    };

    // â”€â”€ Term Information â”€â”€

    const termInfo = {

        '1': { name: 'Term 1', start: 'Jun 8, 2026', end: 'Sep 15, 2026', exam: 'Aug 28 - Sep 1' },

        '2': { name: 'Term 2', start: 'Sep 16, 2026', end: 'Dec 18, 2026', exam: 'Dec 3-4' },

        '3': { name: 'Term 3', start: 'Jan 4, 2027', end: 'Apr 8, 2027', exam: 'Mar 22-23' }

    };

    // â”€â”€ Cached schedule data for current grid â”€â”€
    let _scheduleCache = [];
    let _scheduleCacheKey = '';
    // â”€â”€ Entry currently being drag-and-dropped on the schedule grid â”€â”€
    let _draggedScheduleEntry = null;

    // â”€â”€ Quick-pick a grade from the empty-state chips â”€â”€ sets the dropdown,
    // dispatches a real 'change' event (the existing section-repopulation
    // listener is bound via addEventListener, which setting .value alone
    // does not trigger), then loads the grid immediately.
    function quickSelectScheduleGrade(grade) {
        const gradeSelect = document.getElementById('scheduleGradeFilter');
        gradeSelect.value = grade;
        gradeSelect.dispatchEvent(new Event('change'));
        loadScheduleGrid();
    }

    // â”€â”€ Load Schedule Grid â”€â”€

    function loadScheduleGrid() {

        const grade = document.getElementById('scheduleGradeFilter').value;

        const term = document.getElementById('scheduleTermFilter').value;

        const sectionId = document.getElementById('scheduleSectionFilter').value;

        if (!grade) {

            document.getElementById('scheduleGridContainer').style.display = 'none';

            document.getElementById('scheduleEmptyState').style.display = 'block';

            return;

        }

        // Show grid, hide empty state

        document.getElementById('scheduleGridContainer').style.display = 'block';

        document.getElementById('scheduleEmptyState').style.display = 'none';

        // Update info text

        const gradeLabel = grade.charAt(0).toUpperCase() + grade.slice(1).replace('grade', 'Grade ');

        const sectionLabel = sectionId ? document.getElementById('scheduleSectionFilter').selectedOptions[0].textContent : 'All Sections';

        const termLabel = termInfo[term] ? termInfo[term].name : 'Term ' + term;

        document.getElementById('scheduleInfoText').textContent = `${gradeLabel} - ${sectionLabel} (${termLabel})`;

        // Build grid with loading state
        const tbody = document.getElementById('scheduleGridBody');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;"><i class="bi bi-hourglass-split" style="font-size:24px;color:var(--muted);"></i><div style="font-size:12px;color:var(--muted);margin-top:8px;">Loading schedules...</div></td></tr>';

        // Fetch schedules from API
        const cacheKey = `${grade}-${sectionId}-${term}`;
        let fetchUrl = '/admin/schedules?grade_level=' + grade + '&term=' + term;
        if (sectionId) fetchUrl += '&section_id=' + sectionId;

        fetch(fetchUrl, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const schedules = Array.isArray(data) ? data : (data.schedules || data.data || []);
            _scheduleCache = schedules;
            _scheduleCacheKey = cacheKey;
            buildScheduleGrid(grade, term, sectionId, schedules);
        })
        .catch(err => {
            console.error('Error loading schedules:', err);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--red);">Failed to load schedules.</td></tr>';
        });
    }

    // â”€â”€ Build Schedule Grid with data â”€â”€

    function buildScheduleGrid(grade, term, sectionId, schedules) {
        const slots = gradeTimeSlots[grade] || gradeTimeSlots['default'];
        const tbody = document.getElementById('scheduleGridBody');
        tbody.innerHTML = '';
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        slots.forEach(slot => {
            const row = document.createElement('tr');
            const timeCell = document.createElement('td');
            timeCell.className = slot.isBreak ? 'break-cell' : 'time-cell';
            timeCell.textContent = slot.isBreak ? slot.breakName : slot.label;
            if (slot.isBreak) {
                timeCell.colSpan = 6;
                row.appendChild(timeCell);
                tbody.appendChild(row);
                return;
            }
            row.appendChild(timeCell);

            days.forEach(day => {
                const cell = document.createElement('td');
                cell.className = 'schedule-cell';
                cell.dataset.day = day;
                cell.dataset.start = slot.start;
                cell.dataset.end = slot.end;
                cell.onclick = () => openScheduleModalForCell(day, slot, grade, sectionId, term);

                // Find matching schedule
                const entry = schedules.find(s => {
                    const sDay = s.day_of_week;
                    const sStart = (s.start_time || '').substring(0, 5);
                    const sEnd = (s.end_time || '').substring(0, 5);
                    return sDay === day && sStart === slot.start && sEnd === slot.end;
                });

                // Drop target — every cell can be dropped onto (dragover must
                // preventDefault or the browser rejects the drop outright).
                cell.ondragover = (e) => {
                    e.preventDefault();
                    cell.classList.add('drag-over');
                };
                cell.ondragleave = () => cell.classList.remove('drag-over');
                cell.ondrop = (e) => {
                    e.preventDefault();
                    cell.classList.remove('drag-over');
                    if (!_draggedScheduleEntry) return;
                    _handleScheduleDrop(_draggedScheduleEntry, day, slot.start, slot.end, grade, sectionId, term);
                };

                if (entry) {
                    const subjName = entry.subject ? entry.subject.name : (entry.subject_name || 'N/A');
                    const subjCode = entry.subject ? entry.subject.code : '';
                    const teacherName = entry.teacher ? entry.teacher.name : (entry.teacher_name || '');
                    const roomVal = entry.room || '';
                    const hasConflict = !!entry.has_conflict;
                    const reasons = entry.conflict_reasons || [];
                    const warnBadge = hasConflict
                        ? `<div class="sched-conflict-badge" title="${reasons.map(r => r.replace(/"/g, '&quot;')).join(' | ')}"><i class="bi bi-exclamation-triangle-fill"></i> Conflict</div>`
                        : '';
                    cell.innerHTML = `<div class="schedule-cell-content${hasConflict ? ' has-conflict' : ''}" draggable="true" style="cursor:grab;" data-schedule-id="${entry.id}" data-subject-id="${entry.subject_id || ''}" data-section-id="${entry.section_id || ''}" data-room="${roomVal}" data-active="${entry.is_active ? 1 : 0}" data-term="${entry.term || term}">
                        ${warnBadge}
                        <div class="subj-row">
                            ${subjCode ? `<span class="subj-code-pill">${subjCode}</span>` : ''}
                            <div class="subject">${subjName}</div>
                        </div>
                        <div class="teacher"><i class="bi bi-person-fill teacher-icon"></i> ${teacherName || '—'}</div>
                        ${roomVal ? '<div class="room"><i class="bi bi-geo-alt-fill"></i>' + roomVal + '</div>' : ''}
                    </div>`;
                    cell.title = hasConflict ? 'Schedule conflict:\n' + reasons.join('\n') : '';
                    const contentEl = cell.querySelector('.schedule-cell-content');
                    // Click on content to edit
                    contentEl.onclick = (e) => {
                        e.stopPropagation();
                        editScheduleFromCell(entry, day, slot, grade, sectionId, term);
                    };
                    // Drag this entry to another cell to move it
                    contentEl.ondragstart = (e) => {
                        _draggedScheduleEntry = entry;
                        contentEl.classList.add('dragging');
                        e.dataTransfer.effectAllowed = 'move';
                    };
                    contentEl.ondragend = () => {
                        contentEl.classList.remove('dragging');
                        _draggedScheduleEntry = null;
                    };
                } else {
                    cell.innerHTML = `<div class="schedule-cell-empty"><i class="bi bi-plus-lg me-1"></i>Add</div>`;
                }
                row.appendChild(cell);
            });
            tbody.appendChild(row);
        });
    }

    // â”€â”€ Edit schedule from cell click â”€â”€
    function editScheduleFromCell(entry, day, slot, grade, sectionId, term) {
        document.getElementById('scheduleModalTitle').innerHTML = '<i class="bi bi-pencil-fill me-2"></i>Edit Schedule';
        document.getElementById('sched-id').value = entry.id;

        setSchedDay(entry.day_of_week || day);
        document.getElementById('sched-term').value = entry.term || term;
        document.getElementById('sched-start').value = (entry.start_time || '').substring(0, 5) || slot.start;
        document.getElementById('sched-end').value = (entry.end_time || '').substring(0, 5) || slot.end;
        document.getElementById('sched-active').value = entry.is_active ? '1' : '0';
        document.getElementById('sched-delete-btn').style.display = '';

        // Grade → sections → room → subjects (async) → restore saved values
        document.getElementById('sched-grade-level').value = grade || '';
        _loadScheduleModalForEdit(grade || '', entry.section_id || sectionId || '', entry.subject_id || '', entry.teacher_id || '', entry.room || '');
        new bootstrap.Modal(document.getElementById('scheduleModal')).show();
    }

    // â”€â”€ Open Schedule Modal for Cell (add new) â”€â”€

    function openScheduleModalForCell(day, slot, grade, sectionId, term) {
        document.getElementById('scheduleModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Schedule';
        document.getElementById('sched-id').value = '';

        setSchedDay(day);
        document.getElementById('sched-start').value = slot.start;
        document.getElementById('sched-end').value = slot.end;
        document.getElementById('sched-term').value = term || '1';
        document.getElementById('sched-active').value = '1';
        document.getElementById('sched-delete-btn').style.display = 'none';

        // Populate sections + subjects for grade; pre-select section if known
        document.getElementById('sched-grade-level').value = grade || '';
        onScheduleGradeLevelChange(grade || '');
        // After grade change populates sections, select the current section
        if (sectionId) {
            setTimeout(() => {
                document.getElementById('sched-section').value = sectionId;
                populateSchedTeachers(sectionId);
            }, 50);
        }

        new bootstrap.Modal(document.getElementById('scheduleModal')).show();
    }

    // â”€â”€ Filter subjects dropdown by grade level â”€â”€
    function filterSubjectsByGrade(gradeLevel) {
        const subjectSelect = document.getElementById('sched-subject');
        const allOptions = subjectSelect.querySelectorAll('option');
        allOptions.forEach(opt => {
            if (!opt.value) return; // keep placeholder
            const optGrade = opt.dataset.grade || '';
            opt.style.display = (!optGrade || optGrade === gradeLevel) ? '' : 'none';
        });
        subjectSelect.value = '';
    }

    // â”€â”€ Download Schedule as PDF â”€â”€
    function downloadScheduleAdminPDF() {
        var grade      = document.getElementById('scheduleGradeFilter').value;
        var sectionSel = document.getElementById('scheduleSectionFilter');
        var termSel    = document.getElementById('scheduleTermFilter');
        var sectionLabel = sectionSel && sectionSel.selectedIndex > 0 ? sectionSel.options[sectionSel.selectedIndex].text : 'All Sections';
        var termLabels = { '1': '1st Term', '2': '2nd Term', '3': '3rd Term' };
        var termLabel  = termLabels[termSel ? termSel.value : '1'] || 'Term';
        var gradeLabels = { 'nursery':'Nursery','kindergarten':'Kindergarten','grade1':'Grade 1','grade2':'Grade 2','grade3':'Grade 3','grade4':'Grade 4','grade5':'Grade 5','grade6':'Grade 6' };
        var gradeLabel = gradeLabels[grade] || grade || 'All Grades';

        if (!grade) {
            showCustomAlert('warning', 'Select Grade', 'Please select a grade level first.');
            return;
        }

        var slots    = gradeTimeSlots[grade] || gradeTimeSlots['default'];
        var days     = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        var blueLine = '#2471a3', blueTint = '#e8f0fb';
        var dayColors = {
            Monday: { line: blueLine, tint: blueTint }, Tuesday: { line: blueLine, tint: blueTint },
            Wednesday: { line: blueLine, tint: blueTint }, Thursday: { line: blueLine, tint: blueTint },
            Friday: { line: blueLine, tint: blueTint }
        };
        var schedules = _scheduleCache || [];
        var logoUrl  = '<?php echo e(asset("images/logo.png")); ?>';
        var printDate = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });

        // Every cell — filled or empty — gets the SAME fixed height so the
        // printed grid reads as a neat, evenly-boxed timetable regardless of
        // how much text is in any one slot.
        var CELL_HEIGHT = '1in';

        // Build table rows
        var theadHtml = '<tr>'
            + '<th style="padding:9px 10px;text-align:center;border:1px solid #334155;width:80px;font-size:11px;background:#334155;color:#fff;">TIME</th>';
        days.forEach(function(d) {
            var c = dayColors[d];
            theadHtml += '<th style="padding:9px 10px;text-align:center;border:1px solid ' + c.line + ';font-size:11px;background:' + c.line + ';color:#fff;">' + d.toUpperCase() + '</th>';
        });
        theadHtml += '</tr>';

        var tbodyHtml = '';
        slots.forEach(function(slot, idx) {
            if (slot.isBreak) {
                tbodyHtml += '<tr>'
                    + '<td colspan="6" style="padding:6px 10px;background:#f5f2ea;text-align:center;font-size:10px;font-weight:700;color:#92722a;border:1px solid #ecd9a8;letter-spacing:1.5px;">'
                    + slot.breakName.toUpperCase()
                    + '</td></tr>';
                return;
            }

            tbodyHtml += '<tr>';
            tbodyHtml += '<td style="height:' + CELL_HEIGHT + ';box-sizing:border-box;padding:6px 8px;border:1px solid #e2e8f0;font-size:10px;font-weight:700;color:#475569;text-align:center;white-space:nowrap;background:#f8fafc;">' + slot.label + '</td>';

            days.forEach(function(day) {
                var c = dayColors[day];
                var entry = schedules.find(function(s) {
                    return s.day_of_week === day
                        && (s.start_time || '').substring(0, 5) === slot.start
                        && (s.end_time || '').substring(0, 5) === slot.end;
                });

                var tdOpen = '<td style="height:' + CELL_HEIGHT + ';box-sizing:border-box;padding:5px;border:1px solid #e2e8f0;vertical-align:top;overflow:hidden;">';

                if (entry) {
                    var hasConflict = !!entry.has_conflict;
                    var lineColor = hasConflict ? '#dc2626' : c.line;
                    var tintColor = hasConflict ? '#fef2f2' : c.tint;
                    var subjName = entry.subject ? entry.subject.name : (entry.subject_name || '—');
                    var subjCode = entry.subject ? (entry.subject.code || '') : '';
                    var teacher  = entry.teacher ? entry.teacher.name : (entry.teacher_name || '—');
                    var room     = entry.room || '';
                    tbodyHtml += tdOpen
                        + '<div style="background:' + tintColor + ';border-left:3px solid ' + lineColor + ';border-radius:4px;padding:6px 7px;height:100%;box-sizing:border-box;overflow:hidden;">'
                        + (hasConflict ? '<div style="display:inline-block;font-size:8px;font-weight:800;color:#fff;background:#dc2626;padding:1px 5px;border-radius:8px;margin-bottom:3px;letter-spacing:0.3px;">CONFLICT</div>' : '')
                        + '<div style="font-size:10.5px;font-weight:700;color:#1e293b;">' + (subjCode ? '<span style="color:' + lineColor + ';">' + subjCode + '</span> — ' : '') + subjName + '</div>'
                        + '<div style="font-size:9.5px;color:#475569;margin-top:2px;">' + teacher + '</div>'
                        + (room ? '<div style="font-size:9px;color:#64748b;margin-top:2px;">' + room + '</div>' : '')
                        + '</div></td>';
                } else {
                    tbodyHtml += tdOpen + '</td>';
                }
            });
            tbodyHtml += '</tr>';
        });

        var html = '<!DOCTYPE html><html><head>'
            + '<meta charset="UTF-8">'
            + '<title>Class Schedule — ' + gradeLabel + ' ' + sectionLabel + '</title>'
            + '<style>'
            + 'body{font-family:Arial,sans-serif;margin:0;padding:24px;color:#222;font-size:13px;}'
            + 'table{border-collapse:collapse;width:100%;table-layout:fixed;}'
            + '.header{text-align:center;border-bottom:3px solid #1a3a6c;padding-bottom:16px;margin-bottom:18px;}'
            + '.header img{width:80px;height:80px;object-fit:contain;display:block;margin:0 auto 10px;}'
            + '.school-name{font-size:22px;font-weight:700;color:#1a3a6c;letter-spacing:0.5px;}'
            + '.school-addr{font-size:12px;color:#555;margin-top:3px;}'
            + '.doc-title{font-size:13px;font-weight:700;color:#2471a3;margin-top:6px;text-transform:uppercase;letter-spacing:2px;}'
            + '.divider{width:60px;height:3px;background:#1a3a6c;margin:8px auto 0;border-radius:2px;}'
            + '.meta{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:16px;justify-content:center;}'
            + '.meta-item{background:#f0f4f8;border-radius:6px;padding:5px 14px;text-align:center;}'
            + '.meta-label{color:#888;font-size:10px;text-transform:uppercase;}'
            + '.meta-value{font-weight:700;color:#1a3a6c;font-size:12px;}'
            + '.footer{margin-top:20px;border-top:1px solid #ddd;padding-top:10px;display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;}'
            + '@media print{body{padding:16px;}}'
            + '</style></head><body>'
            + '<div class="header">'
            + '<img src="' + logoUrl + '" alt="ILC" onerror="this.style.display=\'none\'">'
            + '<div class="school-name">IEMELIF LEARNING CENTER</div>'
            + '<div class="school-addr">General Tinio, Nueva Ecija</div>'
            + '<div class="doc-title">Class Schedule</div>'
            + '<div class="divider"></div>'
            + '</div>'
            + '<div class="meta">'
            + '<div class="meta-item"><div class="meta-label">Grade Level</div><div class="meta-value">' + gradeLabel + '</div></div>'
            + '<div class="meta-item"><div class="meta-label">Section</div><div class="meta-value">' + sectionLabel + '</div></div>'
            + '<div class="meta-item"><div class="meta-label">Term</div><div class="meta-value">' + termLabel + '</div></div>'
            + '</div>'
            + '<table><thead>' + theadHtml + '</thead><tbody>' + tbodyHtml + '</tbody></table>'
            + '<div class="footer">'
            + '<span>IEMELIF Learning Center &mdash; General Tinio, Nueva Ecija</span>'
            + '<span>Printed: ' + printDate + '</span>'
            + '</div>'
            + '</body></html>';

        var win = window.open('', '_blank', 'width=1000,height=750');
        win.document.write(html);
        win.document.close();
        win.onload = function() { win.focus(); win.print(); };
    }

    // â”€â”€ Export Schedule â”€â”€

    function csvEscape(value) {
        const s = value === null || value === undefined ? '' : String(value);
        return /[",\r\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
    }

    function exportSchedule() {

        const grade = document.getElementById('scheduleGradeFilter').value;

        if (!grade) {

            showCustomAlert('warning', 'Select Grade', 'Please select a grade level first.');

            return;

        }

        const rows = _scheduleCache || [];

        if (!rows.length) {
            showCustomAlert('warning', 'Nothing to Export', 'Load a schedule first, then export it.');
            return;
        }

        const dayOrder = { 'Monday':1, 'Tuesday':2, 'Wednesday':3, 'Thursday':4, 'Friday':5, 'Saturday':6, 'Sunday':7 };
        const sorted = [...rows].sort((a, b) => {
            const d = (dayOrder[a.day_of_week] || 99) - (dayOrder[b.day_of_week] || 99);
            return d !== 0 ? d : (a.start_time || '').localeCompare(b.start_time || '');
        });

        const header = ['Day', 'Start Time', 'End Time', 'Section', 'Subject Code', 'Subject Name', 'Teacher', 'Room'];
        const lines = [header.map(csvEscape).join(',')];

        sorted.forEach(s => {
            lines.push([
                s.day_of_week || '',
                (s.start_time || '').substring(0, 5),
                (s.end_time || '').substring(0, 5),
                s.section ? s.section.name : '',
                s.subject ? (s.subject.code || '') : '',
                s.subject ? s.subject.name : '',
                s.teacher ? s.teacher.name : '',
                s.room || '',
            ].map(csvEscape).join(','));
        });

        const sectionSel = document.getElementById('scheduleSectionFilter');
        const sectionLabel = sectionSel && sectionSel.selectedIndex > 0 ? sectionSel.options[sectionSel.selectedIndex].text : 'all-sections';
        const filename = `schedule-${grade}-${sectionLabel}`.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') + '.csv';

        const blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // â”€â”€ Populate Section Filter based on Grade â”€â”€

    const scheduleGradeFilter = document.getElementById('scheduleGradeFilter');
    if (scheduleGradeFilter) {
        scheduleGradeFilter.addEventListener('change', function() {

        const grade = this.value;

        const sectionSelect = document.getElementById('scheduleSectionFilter');

        sectionSelect.innerHTML = '<option value="">All Sections</option>';

        if (grade) {
            fetch('/admin/sections?grade_level=' + grade, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const sections = Array.isArray(data) ? data : (data.sections || data.data || []);
                sections.forEach(sec => {
                    const option = document.createElement('option');
                    option.value = sec.id;
                    option.textContent = sec.name;
                    sectionSelect.appendChild(option);
                });
            })
            .catch(err => console.error('Error loading sections:', err));
        }
    });
    }

    // â”€â”€ Schedule Templates per Grade Level â”€â”€

    const scheduleTemplates = {

        'nursery': [

            { day: 'Monday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Values Development' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'Aesthetic/Creative Development' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'Cognitive Development' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Values Development' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Aesthetic/Creative Development' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Cognitive Development' },

            { day: 'Friday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Friday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' }

        ],

        'kindergarten': [

            { day: 'Monday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Values Development' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'Aesthetic/Creative Development' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'Cognitive Development' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Values Development' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Aesthetic/Creative Development' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Cognitive Development' },

            { day: 'Friday', time: '07:30-08:30', subject: 'Literacy, Language, and Communication' },

            { day: 'Friday', time: '08:30-09:30', subject: 'Socio-Emotional Development' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Physical Health and Motor Development' }

        ],

        'grade1': [

            { day: 'Monday', time: '07:30-08:30', subject: 'Math' },

            { day: 'Monday', time: '08:30-09:30', subject: 'GMRC' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Language' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Reading and Literacy' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'Makabansa' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'GMRC' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Language' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'Reading and Literacy' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Makabansa' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Math' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'GMRC' },

            { day: 'Friday', time: '07:30-08:30', subject: 'Language' },

            { day: 'Friday', time: '08:30-09:30', subject: 'Reading and Literacy' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Makabansa' }

        ],

        'grade2': [

            { day: 'Monday', time: '07:30-08:30', subject: 'English' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Makabansa' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'GMRC' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'English' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'Filipino' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Math' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'Makabansa' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'GMRC' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'English' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Filipino' },

            { day: 'Friday', time: '07:30-08:30', subject: 'Math' },

            { day: 'Friday', time: '08:30-09:30', subject: 'Makabansa' },

            { day: 'Friday', time: '09:30-10:30', subject: 'GMRC' }

        ],

        'grade3': [

            { day: 'Monday', time: '07:30-08:30', subject: 'English' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Science' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'Makabansa' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'GMRC' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'English' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Science' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Makabansa' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'GMRC' },

            { day: 'Friday', time: '07:30-08:30', subject: 'English' },

            { day: 'Friday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Math' }

        ],

        'grade4': [

            { day: 'Monday', time: '07:30-08:30', subject: 'English' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Science' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'EPP' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'AP' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'Mapeh' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'GMRC' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'English' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Filipino' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Math' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Science' },

            { day: 'Friday', time: '07:30-08:30', subject: 'EPP' },

            { day: 'Friday', time: '08:30-09:30', subject: 'AP' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Mapeh' }

        ],

        'grade5': [

            { day: 'Monday', time: '07:30-08:30', subject: 'English' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Science' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'EPP' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'AP' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'Mapeh' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'GMRC' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'English' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Filipino' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Math' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Science' },

            { day: 'Friday', time: '07:30-08:30', subject: 'EPP' },

            { day: 'Friday', time: '08:30-09:30', subject: 'AP' },

            { day: 'Friday', time: '09:30-10:30', subject: 'Mapeh' }

        ],

        'grade6': [

            { day: 'Monday', time: '07:30-08:30', subject: 'English' },

            { day: 'Monday', time: '08:30-09:30', subject: 'Filipino' },

            { day: 'Monday', time: '09:30-10:30', subject: 'Math' },

            { day: 'Tuesday', time: '07:30-08:30', subject: 'Science' },

            { day: 'Tuesday', time: '08:30-09:30', subject: 'AP' },

            { day: 'Tuesday', time: '09:30-10:30', subject: 'ESP' },

            { day: 'Wednesday', time: '07:30-08:30', subject: 'TLE' },

            { day: 'Wednesday', time: '08:30-09:30', subject: 'Mapeh' },

            { day: 'Wednesday', time: '09:30-10:30', subject: 'English' },

            { day: 'Thursday', time: '07:30-08:30', subject: 'Filipino' },

            { day: 'Thursday', time: '08:30-09:30', subject: 'Math' },

            { day: 'Thursday', time: '09:30-10:30', subject: 'Science' },

            { day: 'Friday', time: '07:30-08:30', subject: 'AP' },

            { day: 'Friday', time: '08:30-09:30', subject: 'ESP' },

            { day: 'Friday', time: '09:30-10:30', subject: 'TLE' }

        ]

    };

    // â”€â”€ Standard Subjects per Grade Level (based on DepEd K-6 curriculum) â”€â”€

    const standardSubjects = {

        'nursery': [

            { name: 'Literacy, Language, and Communication', code: 'LLC' },

            { name: 'Socio-Emotional Development', code: 'SED' },

            { name: 'Values Development', code: 'VD' },

            { name: 'Physical Health and Motor Development', code: 'PHMD' },

            { name: 'Aesthetic/Creative Development', code: 'ACD' },

            { name: 'Cognitive Development', code: 'CD' }

        ],

        'kindergarten': [

            { name: 'Literacy, Language, and Communication', code: 'LLC' },

            { name: 'Socio-Emotional Development', code: 'SED' },

            { name: 'Values Development', code: 'VD' },

            { name: 'Physical Health and Motor Development', code: 'PHMD' },

            { name: 'Aesthetic/Creative Development', code: 'ACD' },

            { name: 'Cognitive Development', code: 'CD' }

        ],

        'grade1': [

            { name: 'Math', code: 'MATH' },

            { name: 'GMRC', code: 'GMRC' },

            { name: 'Language', code: 'LANG' },

            { name: 'Reading and Literacy', code: 'RL' },

            { name: 'Makabansa', code: 'MAK' }

        ],

        'grade2': [

            { name: 'English', code: 'ENG' },

            { name: 'Filipino', code: 'FIL' },

            { name: 'Math', code: 'MATH' },

            { name: 'Makabansa', code: 'MAK' },

            { name: 'GMRC', code: 'GMRC' }

        ],

        'grade3': [

            { name: 'English', code: 'ENG' },

            { name: 'Filipino', code: 'FIL' },

            { name: 'Math', code: 'MATH' },

            { name: 'Science', code: 'SCI' },

            { name: 'Makabansa', code: 'MAK' },

            { name: 'GMRC', code: 'GMRC' }

        ],

        'grade4': [

            { name: 'English', code: 'ENG' },

            { name: 'Filipino', code: 'FIL' },

            { name: 'Math', code: 'MATH' },

            { name: 'Science', code: 'SCI' },

            { name: 'EPP', code: 'EPP' },

            { name: 'AP', code: 'AP' },

            { name: 'Mapeh', code: 'MAPEH' },

            { name: 'GMRC', code: 'GMRC' }

        ],

        'grade5': [

            { name: 'English', code: 'ENG' },

            { name: 'Filipino', code: 'FIL' },

            { name: 'Math', code: 'MATH' },

            { name: 'Science', code: 'SCI' },

            { name: 'EPP', code: 'EPP' },

            { name: 'AP', code: 'AP' },

            { name: 'Mapeh', code: 'MAPEH' },

            { name: 'GMRC', code: 'GMRC' }

        ],

        'grade6': [

            { name: 'English', code: 'ENG' },

            { name: 'Filipino', code: 'FIL' },

            { name: 'Math', code: 'MATH' },

            { name: 'Science', code: 'SCI' },

            { name: 'AP', code: 'AP' },

            { name: 'ESP', code: 'ESP' },

            { name: 'TLE', code: 'TLE' },

            { name: 'Mapeh', code: 'MAPEH' }

        ]

    };

    // â”€â”€ Custom Alert System â”€â”€

    function showCustomAlert(type, title, message) {
        var combined = (title && message)
            ? '<strong>' + title + '</strong><br><span style="font-weight:400;opacity:0.85;">' + message + '</span>'
            : (title || message);
        showToast(combined, type === 'loading' ? 'info' : type);
    }

    // â”€â”€ Lightweight confirmation modal â”€â”€ for simple yes/no prompts like
    // "log out?" that don't need the full delete-confirm modal's dedicated
    // HTML/Bootstrap machinery below. Same look as every other portal now.
    // Named differently from the Bootstrap-modal showConfirm(title, message,
    // onConfirm, options) defined further down this file -- two same-named
    // function declarations in one scope silently collide (the later one
    // wins), which previously broke every caller of this lightweight version.
    function showConfirmSimple(message, onConfirm, opts) {
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
    // in this file — blocks the native synchronous submit, shows the
    // styled confirm, and submits for real only if the user confirms.
    function confirmLogout(form) {
        showConfirmSimple('Are you sure you want to log out?', function () { form.submit(); },
            { title: 'Log Out', confirmLabel: 'Log Out', danger: true });
        return false;
    }

    // â”€â”€ Delete Confirmation System â”€â”€
    let deleteConfirmCallback = null;

    function showDeleteConfirm(message, onConfirm) {
        if (typeof message === 'function') {
            onConfirm = message;
            message = 'Are you sure you want to delete this item? This action cannot be undone.';
        }
        document.getElementById('deleteConfirmMessage').textContent = message;
        deleteConfirmCallback = onConfirm;
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    }

    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            const modalEl = document.getElementById('deleteConfirmModal');
            // Run the callback only after the modal has actually finished closing
            // (its hide transition + backdrop teardown) rather than immediately —
            // when this confirm modal is opened on top of another already-open
            // modal (e.g. an edit form), firing the callback synchronously can
            // leave a stray backdrop behind that silently blocks every click on
            // the page underneath, including that other modal's own close button.
            modalEl.addEventListener('hidden.bs.modal', function() {
                if (typeof deleteConfirmCallback === 'function') {
                    deleteConfirmCallback();
                    deleteConfirmCallback = null;
                }
            }, { once: true });
            bootstrap.Modal.getInstance(modalEl).hide();
        });
    }

    // â”€â”€ Password-Confirmation ("type your password to confirm") â”€â”€
    // Used for destructive student actions (archive / permanent delete) —
    // unlike the plain delete-confirm modal, this one stays open on a wrong
    // password (shows an inline error, lets the admin retry) and only closes
    // once the server actually accepts the password.
    let _pwConfirmCallback = null;

    function showPasswordConfirm(message, onConfirm) {
        document.getElementById('pwConfirmMessage').textContent = message;
        const input = document.getElementById('pwConfirmInput');
        input.value = '';
        input.type = 'password';
        document.getElementById('pwConfirmToggleIcon').className = 'bi bi-eye-fill';
        document.getElementById('pwConfirmError').style.display = 'none';
        _pwConfirmCallback = onConfirm;
        new bootstrap.Modal(document.getElementById('pwConfirmModal')).show();
        setTimeout(() => input.focus(), 300);
    }

    function _togglePwConfirmVisibility() {
        const input = document.getElementById('pwConfirmInput');
        const icon = document.getElementById('pwConfirmToggleIcon');
        if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash-fill'; }
        else { input.type = 'password'; icon.className = 'bi bi-eye-fill'; }
    }

    function _pwConfirmShowError(msg) {
        document.getElementById('pwConfirmErrorText').textContent = msg || 'Incorrect password.';
        document.getElementById('pwConfirmError').style.display = '';
    }

    function _pwConfirmClose() {
        const modalEl = document.getElementById('pwConfirmModal');
        const inst = bootstrap.Modal.getInstance(modalEl);
        if (inst) inst.hide();
    }

    const pwConfirmBtn = document.getElementById('pwConfirmBtn');
    if (pwConfirmBtn) {
        pwConfirmBtn.addEventListener('click', function() {
            const pw = document.getElementById('pwConfirmInput').value;
            document.getElementById('pwConfirmError').style.display = 'none';
            if (!pw) {
                _pwConfirmShowError('Please enter your password.');
                return;
            }
            if (typeof _pwConfirmCallback === 'function') {
                _pwConfirmCallback(pw);
            }
        });
    }
    const pwConfirmInput = document.getElementById('pwConfirmInput');
    if (pwConfirmInput) {
        pwConfirmInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pwConfirmBtn').click(); }
        });
    }

    // â”€â”€ Schedule Conflict Confirmation ("Save Anyway") â”€â”€
    let scheduleConflictConfirmCallback = null;

    const confirmScheduleConflictBtn = document.getElementById('confirmScheduleConflictBtn');
    if (confirmScheduleConflictBtn) {
        confirmScheduleConflictBtn.addEventListener('click', function() {
            const modalEl = document.getElementById('scheduleConflictConfirmModal');
            // Same nested-modal-safe pattern as confirmDeleteBtn — this dialog
            // opens on top of the already-open Add/Edit Schedule modal.
            modalEl.addEventListener('hidden.bs.modal', function() {
                if (typeof scheduleConflictConfirmCallback === 'function') {
                    scheduleConflictConfirmCallback();
                    scheduleConflictConfirmCallback = null;
                }
            }, { once: true });
            bootstrap.Modal.getInstance(modalEl).hide();
        });
    }

    // â”€â”€ Promotion Confirmation System â”€â”€
    let promoteConfirmCallback = null;

    function showPromoteConfirm(message, onConfirm) {
        if (typeof message === 'function') {
            onConfirm = message;
            message = 'Are you sure you want to promote these students?';
        }
        document.getElementById('promoteConfirmMessage').textContent = message;
        promoteConfirmCallback = onConfirm;
        new bootstrap.Modal(document.getElementById('promoteConfirmModal')).show();
    }

    const confirmPromoteBtn = document.getElementById('confirmPromoteBtn');
    if (confirmPromoteBtn) {
        confirmPromoteBtn.addEventListener('click', function() {
            const modalEl = document.getElementById('promoteConfirmModal');
            modalEl.addEventListener('hidden.bs.modal', function() {
                if (typeof promoteConfirmCallback === 'function') {
                    promoteConfirmCallback();
                    promoteConfirmCallback = null;
                }
            }, { once: true });
            bootstrap.Modal.getInstance(modalEl).hide();
        });
    }

    function showLoading(message) {
        message = message || 'Processing...';
        hideLoading();

        let container = document.getElementById('ilc-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'ilc-toast-container';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;pointer-events:none;';
            document.body.appendChild(container);
        }

        const div = document.createElement('div');
        div.id = 'loadingToast';
        div.style.cssText = 'min-width:290px;max-width:420px;background:#e3f2fd;border:1.5px solid #2471a3;border-left:4px solid #2471a3;border-radius:10px;padding:13px 16px;display:flex;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,0.12);font-size:13px;pointer-events:all;';
        div.innerHTML = '<div class="spinner-border spinner-border-sm" style="width:16px;height:16px;border-width:2px;color:#1565c0;flex-shrink:0;"></div><span style="font-weight:500;color:#1565c0;">' + message + '</span>';
        container.appendChild(div);
    }

    function hideLoading() {
        const el = document.getElementById('loadingToast');
        if (el) el.remove();
    }

    let genericConfirmCallback = null;

    function showConfirm(title, message, onConfirm, options) {
        options = options || {};
        const btnText    = options.btnText    || 'Confirm';
        const btnClass   = options.btnClass   || 'btn btn-primary';
        const btnIcon    = options.btnIcon    || 'bi-check-circle-fill';
        const headerBg   = options.headerBg   || 'linear-gradient(135deg,#0056b3,#003d82)';
        const headerIcon = options.headerIcon || 'bi-question-circle-fill';

        document.getElementById('genericConfirmTitle').innerHTML   = '<i class="bi ' + headerIcon + ' me-2"></i>' + title;
        document.getElementById('genericConfirmMessage').textContent = message;
        document.getElementById('genericConfirmHeader').style.background = headerBg;

        const btn = document.getElementById('genericConfirmBtn');
        btn.className = btnClass;
        btn.innerHTML = '<i class="bi ' + btnIcon + ' me-1"></i>' + btnText;

        genericConfirmCallback = onConfirm;
        new bootstrap.Modal(document.getElementById('genericConfirmModal')).show();
    }

    const genericConfirmBtnEl = document.getElementById('genericConfirmBtn');
    if (genericConfirmBtnEl) {
        genericConfirmBtnEl.addEventListener('click', function() {
            const modalEl = document.getElementById('genericConfirmModal');
            modalEl.addEventListener('hidden.bs.modal', function() {
                if (typeof genericConfirmCallback === 'function') {
                    genericConfirmCallback();
                    genericConfirmCallback = null;
                }
            }, { once: true });
            bootstrap.Modal.getInstance(modalEl).hide();
        });
    }

    function confirmAndSubmit(event, form, message, options) {
        event.preventDefault();
        options = options || {};
        showConfirm(
            options.title || 'Confirm Action',
            message,
            function() { form.submit(); },
            options
        );
    }

    // â”€â”€ Switch sections (function defined above at line 14650) â”€â”€

    // ── Auto-refresh when tab becomes visible again ──
    // Handles returning from another browser, tab, or window after ≥30 s away.
    (function () {
        var _hiddenAt = null;
        var THRESHOLD = 30000; // 30 seconds

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                _hiddenAt = Date.now();
            } else {
                if (_hiddenAt && (Date.now() - _hiddenAt) >= THRESHOLD) {
                    reloadWithSection();
                }
                _hiddenAt = null;
            }
        });
    })();

    // Restore section on page load
    function reloadWithSection() {
        // Save current section before reload so it's restored properly
        const currentSection = localStorage.getItem('currentAdminSection') || 'dashboard';
        localStorage.setItem('currentAdminSection', currentSection);
        location.reload();
    }

    // Restore section on page load

    (function() {

        // URL ?section=X takes priority (set by sort/filter redirects) so the
        // correct section is always shown after a filter-triggered page reload.
        const urlSection = new URLSearchParams(window.location.search).get('section');
        const savedSection = (urlSection && sections.includes(urlSection))
            ? urlSection
            : localStorage.getItem('currentAdminSection');

        // Always show a section - default to dashboard if nothing is saved
        const sectionToShow = (savedSection && sections.includes(savedSection))
            ? savedSection
            : 'dashboard';

        // Show immediately (CSS hides all sections by default — no flash)
        showSection(sectionToShow);

        // Reports has a second level of nesting (tab -> sub-report) that a
        // plain ?section=reports can't express — pagination links inside
        // Reports reload the whole page, so without this every "next page"
        // click would silently drop back to the default Students/Master List
        // tab instead of staying on whichever tab/sub-report was open.
        if (sectionToShow === 'reports') {
            const rptParams = new URLSearchParams(window.location.search);
            const rptTab = rptParams.get('rpt_tab');
            const rptSubreport = rptParams.get('rpt_subreport');
            if (rptTab) {
                switchRptTab(rptTab);
                if (rptSubreport) {
                    switchRptSubReport(rptTab, rptSubreport);
                }
            }
        }

    })();

    // Reject document modal

    function rejectDoc(docId) {

        document.getElementById('rejectDocForm').action = '/admin/documents/' + docId + '/reject';

        document.getElementById('rejectDocModal').style.display = 'flex';

    }

    // Approve Enrollment

    function approveEnrollment(enrollmentId) {
        console.log('approveEnrollment called with ID:', enrollmentId);

        // Hide the enrollment view modal if it's actually open right now, to avoid
        // Bootstrap nested-modal conflicts — wait for it to actually finish closing
        // before opening the next one. Checking the 'show' class (not just whether a
        // bootstrap.Modal instance exists) matters: this button is also reachable
        // directly from the table row, so once the view modal has been opened and
        // closed even once earlier in the session, getInstance() keeps returning
        // that same (now-hidden) instance. Calling .hide() on an already-hidden
        // Bootstrap modal is a silent no-op — 'hidden.bs.modal' never fires — which
        // left the confirm dialog never appearing on the very next click.
        const viewModalEl = document.getElementById('enrollmentViewModal');
        if (viewModalEl && viewModalEl.classList.contains('show')) {
            viewModalEl.addEventListener('hidden.bs.modal', () => openApproveConfirm(enrollmentId), { once: true });
            bootstrap.Modal.getInstance(viewModalEl).hide();
        } else {
            openApproveConfirm(enrollmentId);
        }
    }

    function openApproveConfirm(enrollmentId) {
        // Enhanced confirmation dialog

        const confirmDialog = document.createElement('div');

        confirmDialog.className = 'modal fade';

        confirmDialog.innerHTML = `

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header bg-success text-white">

                        <h5 class="modal-title">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            Confirm Enrollment Approval

                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="alert alert-info d-flex align-items-center">

                            <i class="bi bi-info-circle-fill me-2"></i>

                            <div>

                                <strong>Important:</strong> Approving this enrollment will:

                                <ul class="mb-0 mt-2">

                                    <li>Create a student account with login credentials</li>

                                    <li>Send approval notification to the student's email</li>

                                    <li>Mark this application as approved</li>

                                </ul>

                            </div>

                        </div>

                        <div class="mt-3">

                            <label class="form-label">Additional Notes (Optional)</label>

                            <textarea id="approvalNotes" class="form-control" rows="2" placeholder="Add any notes for this approval..."></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            <i class="bi bi-x-circle me-1"></i> Cancel

                        </button>

                        <button type="button" class="btn btn-success" id="confirmApproveBtn">

                            <i class="bi bi-check-circle-fill me-1"></i> Approve Enrollment

                        </button>

                    </div>

                </div>

            </div>

        `;

        

        document.body.appendChild(confirmDialog);

        const modal = new bootstrap.Modal(confirmDialog);

        modal.show();

        

        // Handle approval confirmation

        document.getElementById('confirmApproveBtn').onclick = function() {

            const notes = document.getElementById('approvalNotes').value;



            // Show loading state — both on the button and as a page-level indicator,
            // since approval also creates the student account and sends an email,
            // which can take a moment longer than the button alone makes obvious.
            showLoading('Approving enrollment…');

            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';

            this.disabled = true;

            

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            

            fetch(`/admin/enrollments/${enrollmentId}/approve`, {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': token,

                    'Accept': 'application/json'

                },

                body: JSON.stringify({ notes: notes })

            })

            .then(response => {

                if (!response.ok) {

                    throw new Error(`HTTP error! status: ${response.status}`);

                }

                return response.json();

            })

            .then(data => {
                hideLoading();

                if (data.success) {

                    // Close confirmation modal

                    modal.hide();

                    

                    // Update main modal status

                    updateStatusBanner('approved');

                    document.getElementById('modal-processing-status').textContent = 'Approved - Student Account Created';

                    

                    // Update action buttons

                    const actionButtons = document.getElementById('modal-action-buttons');

                    actionButtons.innerHTML = `

                        <button class="btn btn-success success-animation" disabled>

                            <i class="bi bi-check-circle-fill"></i> Approved Successfully

                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">

                            <i class="bi bi-arrow-left"></i> Close

                        </button>

                    `;

                    

                    // Show success message

                    showCustomAlert('success', 'Enrollment Approved!', 

                        'Student account has been created successfully. Login credentials have been sent to the student\'s email.');

                    

                    // Add success animation to modal

                    const modalElement = document.getElementById('enrollmentViewModal');

                    modalElement.classList.add('success-animation');

                    

                    // Close modal after delay and reload

                    setTimeout(() => {

                        const mainModal = bootstrap.Modal.getInstance(modalElement);

                        if (mainModal) {

                            mainModal.hide();

                        }

                        setTimeout(() => {

                            reloadWithSection();

                        }, 500);

                    }, 3000);

                    

                } else {

                    // Reset button and show error

                    this.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Approve Enrollment';

                    this.disabled = false;

                    showCustomAlert('error', 'Approval Failed', 

                        data.message || 'Failed to approve enrollment. Please try again.');

                }

            })

            .catch(error => {
                hideLoading();

                // Reset button and show error

                this.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Approve Enrollment';

                this.disabled = false;

                console.error('Approval error:', error);

                showCustomAlert('error', 'Network Error', 

                    'An error occurred while processing the approval. Please check your connection and try again.');

            });

        };

        

        // Clean up modal when hidden

        confirmDialog.addEventListener('hidden.bs.modal', () => {

            document.body.removeChild(confirmDialog);

        });

    }

    // Decline Enrollment

    function declineEnrollment(enrollmentId) {

        // See the identical fix (and the reason it's needed) in approveEnrollment
        // just above — must check the 'show' class, not just whether a
        // bootstrap.Modal instance exists, since this button is also reachable
        // directly from the table row.
        const viewModalEl = document.getElementById('enrollmentViewModal');
        if (viewModalEl && viewModalEl.classList.contains('show')) {
            viewModalEl.addEventListener('hidden.bs.modal', () => openDeclineModal(enrollmentId), { once: true });
            bootstrap.Modal.getInstance(viewModalEl).hide();
        } else {
            openDeclineModal(enrollmentId);
        }
    }

    function openDeclineModal(enrollmentId) {
        // Create custom modal for decline reason

        const declineModal = document.createElement('div');

        declineModal.className = 'modal fade';

        declineModal.innerHTML = `

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">Decline Enrollment</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-lbl">Reason for Declining *</label>

                            <textarea id="declineReason" class="form-fld" rows="3" placeholder="Enter reason for declining this enrollment..."></textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-lbl">Storage Location</label>

                            <select id="declineStorage" class="form-fld">

                                <option value="pending">Pending Review</option>

                                <option value="declined_temporarily">Declined Temporarily</option>

                                <option value="declined_permanently">Declined Permanently</option>

                                <option value="requires_documents">Requires Additional Documents</option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-danger" onclick="confirmDecline('${enrollmentId}', this)">

                            <i class="bi bi-x"></i> Decline Enrollment

                        </button>

                    </div>

                </div>

            </div>

        `;

        

        document.body.appendChild(declineModal);

        const modal = new bootstrap.Modal(declineModal);

        modal.show();

        

        // Clean up modal when hidden

        declineModal.addEventListener('hidden.bs.modal', () => {

            document.body.removeChild(declineModal);

        });

    }

    // Confirm Decline

    function confirmDecline(enrollmentId, btn) {

        const reason = document.getElementById('declineReason').value.trim();

        const storage = document.getElementById('declineStorage').value;



        if (reason === '') {

            showCustomAlert('warning', 'Warning!', 'Please provide a reason for declining.');

            return;

        }

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        

        console.log('Declining enrollment:', enrollmentId, 'Reason:', reason, 'Storage:', storage);

        showLoading('Declining enrollment…');
        if (btn) {
            btn.disabled = true;
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Declining...';
        }

        fetch(`/admin/enrollments/${enrollmentId}/decline`, {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': token,

                'Accept': 'application/json'

            },

            body: JSON.stringify({ reason: reason, storage: storage })

        })

        .then(response => {

            console.log('Response status:', response.status);

            if (!response.ok) {

                throw new Error(`HTTP error! status: ${response.status}`);

            }

            return response.json();

        })

        .then(data => {
            hideLoading();
            console.log('Response data:', data);

            if (data.success) {

                const storageText = storage.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                showCustomAlert('success', 'Success!', `Enrollment declined successfully and stored as: ${storageText}.`);

                reloadWithSection();

            } else {

                showCustomAlert('error', 'Error!', 'Error: ' + (data.message || 'Unknown error occurred'));
                if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.originalHtml; }

            }

        })

        .catch(error => {
            hideLoading();
            if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.originalHtml; }

            console.error('Error:', error);

            showCustomAlert('error', 'Error!', 'An error occurred: ' + error.message + '. Please try again.');

        });

    }

    // View Enrollment Modal
    function viewEnrollmentModal(enrollmentId) {
        const modalElement = document.getElementById('enrollmentViewModal');
        modalElement.classList.add('loading');

        // Reset to Details tab
        evmTab('details');

        fetch(`/admin/enrollments/${enrollmentId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(data => {
            modalElement.classList.remove('loading');
            const sd = data.student_data || {};
            const fmt = (d) => d ? new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' }) : 'N/A';

            // Header
            document.getElementById('modal-reference-number').textContent = data.reference_number || 'N/A';
            document.getElementById('modal-school-year').textContent       = data.school_year || 'N/A';

            // Student type badge in header
            const typeBadge = document.getElementById('modal-type-badge');
            const typeMap = { returning:{ label:'Re-Enroll', color:'#fbbf24' }, transferee:{ label:'Transferee', color:'#6ee7b7' }, new:{ label:'New Student', color:'#a5f3fc' } };
            const typeInfo = typeMap[sd.student_type] || null;
            if (typeInfo) {
                typeBadge.textContent = typeInfo.label;
                typeBadge.style.background = 'rgba(255,255,255,.15)';
                typeBadge.style.color = typeInfo.color;
                typeBadge.style.borderColor = typeInfo.color;
                typeBadge.style.display = 'inline-block';
            } else { typeBadge.style.display = 'none'; }

            // Status pill
            const statusColors = { enrolled:'#16a34a', approved:'#16a34a', pending:'#d97706', declined:'#dc2626', completed:'#6d28d9' };
            const statusBg     = { enrolled:'#dcfce7', approved:'#dcfce7', pending:'#fef3c7', declined:'#fee2e2', completed:'#ede9fe' };
            const sc = statusColors[data.status] || '#6b7280';
            const sb = statusBg[data.status] || '#f3f4f6';
            document.getElementById('modal-status-pill').innerHTML = `<span style="background:${sb};color:${sc};border:1.5px solid ${sc};border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;">${(data.status||'').replace(/^\w/, c=>c.toUpperCase())}</span>`;

            // Student info
            document.getElementById('modal-student-name').textContent      = [`${sd.first_name||''}`, sd.middle_name||'', sd.last_name||''].filter(Boolean).join(' ') || 'N/A';
            document.getElementById('modal-student-email').textContent     = sd.student_email || 'N/A';
            document.getElementById('modal-student-phone').textContent     = sd.guardian_phone || 'N/A';
            document.getElementById('modal-grade-level').textContent       = formatGradeLevel(sd.grade_level) || 'N/A';
            document.getElementById('modal-student-type').textContent      = formatStudentType(sd.student_type) || 'N/A';
            document.getElementById('modal-student-birthdate').textContent = sd.birthdate ? new Date(sd.birthdate).toLocaleDateString('en-PH') : 'N/A';

            // Guardian
            document.getElementById('modal-guardian-name').textContent       = sd.guardian_name || 'N/A';
            document.getElementById('modal-relationship').textContent        = formatRelationship(sd.relationship) || 'N/A';
            document.getElementById('modal-guardian-occupation').textContent = sd.guardian_occupation || 'N/A';
            document.getElementById('modal-guardian-email').textContent      = sd.student_email || 'N/A';
            document.getElementById('modal-guardian-phone').textContent      = sd.guardian_phone || 'N/A';

            // Address
            document.getElementById('modal-street-address').textContent = sd.street_address || 'N/A';
            document.getElementById('modal-barangay').textContent        = sd.barangay || 'N/A';
            document.getElementById('modal-city').textContent            = sd.city || 'N/A';
            document.getElementById('modal-province').textContent        = sd.province || 'N/A';
            document.getElementById('modal-zip-code').textContent        = sd.zip_code || 'N/A';

            // Timeline
            document.getElementById('modal-reference-number').textContent  = data.reference_number || 'N/A';
            document.getElementById('modal-application-date').textContent  = fmt(data.created_at);
            document.getElementById('modal-updated-at').textContent        = fmt(data.updated_at);
            document.getElementById('modal-processing-status').textContent = getProcessingStatus(data.status);

            // Section card
            const secCard = document.getElementById('section-assignment-card');
            if (['enrolled','approved','completed'].includes(data.status)) {
                secCard.style.display = 'block';
                document.getElementById('modal-current-section').textContent    = data.section || 'Not assigned';
                document.getElementById('modal-section-grade-level').textContent = formatGradeLevel(sd.grade_level);
                const csBtn = document.getElementById('change-section-enrollment-id');
                if (csBtn) csBtn.value = data.id;
            } else { secCard.style.display = 'none'; }

            // Action buttons
            const actionButtons = document.getElementById('modal-action-buttons');
            if (data.status === 'pending') {
                actionButtons.innerHTML = `
                    <button class="btn btn-success" onclick="approveEnrollment('${data.id}')"><i class="bi bi-check-circle-fill"></i> Approve</button>
                    <button class="btn btn-danger"  onclick="declineEnrollment('${data.id}')"><i class="bi bi-x-circle-fill"></i> Decline</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i> Close</button>`;
            } else {
                actionButtons.innerHTML = `<button class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i> Close</button>`;
            }

            // Documents tab — load via existing _fetchDocs using user_id
            if (data.user_id) {
                _fetchDocs(data.user_id, 'evm-docs-loading', 'evm-docs-content', 'reloadEvmDocs');
                // Show doc count badge after load
                var _evmPoll = setInterval(function() {
                    var cont = document.getElementById('evm-docs-content');
                    if (cont && cont.style.display !== 'none') {
                        clearInterval(_evmPoll);
                        var cards = cont.querySelectorAll('[style*="border-radius:12px"]');
                        var uploaded = Array.from(cards).filter(function(c) { return !c.textContent.includes('Not yet uploaded'); });
                        var badge = document.getElementById('evm-doc-count');
                        if (badge) { badge.textContent = uploaded.length + '/4'; badge.style.display = 'inline-block'; }
                    }
                }, 300);
            }

            // Show modal
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })
        .catch(error => {
            modalElement.classList.remove('loading');
            console.error('Error loading enrollment data:', error);
            showCustomAlert('error', 'Error!', 'Failed to load enrollment details.');
        });
    }

    // Reload helper for documents inside enrollment view modal
    function reloadEvmDocs() {
        var loadEl = document.getElementById('evm-docs-loading');
        var contEl = document.getElementById('evm-docs-content');
        if (loadEl) loadEl.style.display = 'block';
        if (contEl) { contEl.style.display = 'none'; contEl.innerHTML = ''; }
    }

    // Tab switcher for enrollment view modal
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

    

    // Helper functions for formatting data

    function formatGradeLevel(grade) {

        const gradeMap = {

            'nursery': 'Nursery',

            'kindergarten': 'Kindergarten',

            'grade1': 'Grade 1',

            'grade2': 'Grade 2',

            'grade3': 'Grade 3',

            'grade4': 'Grade 4',

            'grade5': 'Grade 5',

            'grade6': 'Grade 6'

        };

        return gradeMap[grade] || grade || 'N/A';

    }

    

    function formatStudentType(type) {

        const typeMap = {

            'new': 'New Student',

            'transferee': 'Transferee',

            'returning': 'Returning Student'

        };

        return typeMap[type] || type || 'N/A';

    }

    

    function formatRelationship(rel) {

        const relMap = {

            'father': 'Father',

            'mother': 'Mother',

            'guardian': 'Guardian',

            'sibling': 'Sibling',

            'grandparent': 'Grandparent',

            'relative': 'Relative'

        };

        return relMap[rel] || rel || 'N/A';

    }

    

    function updateStatusBanner(status) {
        // Status is now shown as a pill in the modal header — no legacy banner elements needed
        var pill = document.getElementById('modal-status-pill');
        if (!pill) return;
        var statusColors = { enrolled:'#16a34a', approved:'#16a34a', pending:'#d97706', declined:'#dc2626', completed:'#6d28d9' };
        var statusBg     = { enrolled:'#dcfce7', approved:'#dcfce7', pending:'#fef3c7', declined:'#fee2e2', completed:'#ede9fe' };
        var sc = statusColors[status] || '#6b7280';
        var sb = statusBg[status] || '#f3f4f6';
        pill.innerHTML = '<span style="background:' + sb + ';color:' + sc + ';border:1.5px solid ' + sc + ';border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;">' + (status||'').replace(/^\w/, function(c){ return c.toUpperCase(); }) + '</span>';
    }

    

    function getProcessingStatus(status) {

        const statusMap = {

            'pending': 'Under Review',

            'approved': 'Approved - Student Account Created',

            'declined': 'Declined - See Reason in Notes'

        };

        return statusMap[status] || status || 'Unknown';

    }

    // View Student Details

    function viewStudentDetails(studentId) {

        fetch(`/admin/students/${studentId}`, {

            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }

        })

        .then(r => { if (!r.ok) throw new Error('Failed to load'); return r.json(); })

        .then(d => {

            const sd = d.student_data || {};

            const prof = d.profile || {};

            const grd = d.guardian || {};

            const addr = d.address || {};

            const enr = d.enrollment || {};

            

            document.getElementById('sv-name').textContent = d.name;

            document.getElementById('sv-ref').textContent = enr.reference_number || 'No enrollment';

            document.getElementById('sv-email').textContent = d.email;

            document.getElementById('sv-lrn').textContent = d.lrn || 'N/A';

            document.getElementById('sv-gender').textContent = (prof.gender || sd.gender || 'N/A').replace(/^\w/, c => c.toUpperCase());

            const _bdate = prof.birthdate || sd.birthdate || '';
            document.getElementById('sv-birthdate').textContent = _bdate
                ? new Date(_bdate).toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'})
                : 'N/A';
            if (_bdate) {
                const _today = new Date(), _b = new Date(_bdate);
                let _age = _today.getFullYear() - _b.getFullYear();
                if (_today.getMonth() < _b.getMonth() || (_today.getMonth() === _b.getMonth() && _today.getDate() < _b.getDate())) _age--;
                document.getElementById('sv-age').textContent = _age + ' yrs old';
            } else {
                document.getElementById('sv-age').textContent = 'N/A';
            }

            document.getElementById('sv-pob').textContent = sd.place_of_birth || 'N/A';

            document.getElementById('sv-type').textContent = formatStudentType(sd.student_type);

            document.getElementById('sv-joined').textContent = d.created_at;

            document.getElementById('sv-grade').textContent = enr.grade_level || 'N/A';

            document.getElementById('sv-section').textContent = (enr.section && enr.section !== 'Unassigned') ? enr.section : '—';

            document.getElementById('sv-status').textContent = (enr.status || 'N/A').replace(/^\w/, c => c.toUpperCase());

            document.getElementById('sv-payment').textContent = (enr.payment_status || 'unpaid').replace(/^\w/, c => c.toUpperCase());

            document.getElementById('sv-amount').textContent = '₱' + parseFloat(enr.payment_amount || 0).toFixed(2);

            document.getElementById('sv-guardian').textContent = grd.name || sd.guardian_name || 'N/A';

            document.getElementById('sv-relation').textContent = grd.relationship || sd.guardian_relationship || 'N/A';

            document.getElementById('sv-gphone').textContent = grd.contact || sd.guardian_phone || 'N/A';

            document.getElementById('sv-street').textContent = addr.street || sd.street_address || 'N/A';

            document.getElementById('sv-brgy').textContent = addr.barangay || sd.barangay || 'N/A';

            document.getElementById('sv-city').textContent = addr.municipality || sd.city || 'N/A';

            document.getElementById('sv-province').textContent = addr.province || sd.province || 'N/A';

            const modal = new bootstrap.Modal(document.getElementById('studentViewModal'));

            // SF10 download button — always visible for any student with an ID
            const sf10Btn = document.getElementById('sv-sf10-btn');
            if (sf10Btn) {
                sf10Btn.href = '<?php echo e(url("admin/students")); ?>/' + studentId + '/sf10';
                sf10Btn.style.display = 'inline-block';
            }

            // Show re-enroll button if student is not currently enrolled
            const reEnrollBtn = document.getElementById('sv-reenroll-btn');
            if (reEnrollBtn) {
                const isEnrolled = enr.status && ['approved','enrolled'].includes(enr.status);
                reEnrollBtn.style.display = isEnrolled ? 'none' : 'inline-block';
                reEnrollBtn.dataset.studentId = studentId;
            }

            // Load documents for this student
            _svStudentId = studentId;
            loadSvDocuments(studentId);

            modal.show();

        })

        .catch(err => {

            console.error('Error loading student details:', err);

            showCustomAlert('error', 'Error', 'Failed to load student details. Please try again.');

        });

    }

    function reEnrollStudent() {
        const btn = document.getElementById('sv-reenroll-btn');
        const studentId = btn ? btn.dataset.studentId : null;
        if (!studentId) return;

        // Close the view modal
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('studentViewModal'));
        if (viewModal) viewModal.hide();

        // Open walk-in enrollment modal and pre-fill with student data
        fetch(`/admin/students/${studentId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })
        .then(d => {
            const sd = d.student_data || {};
            openWalkInEnrollmentModal();
            setTimeout(() => {
                // Pre-fill name parts
                const nameParts = (d.name || '').trim().split(/\s+/);
                const firstNameEl = document.getElementById('walkin-first-name');
                const lastNameEl = document.getElementById('walkin-last-name');
                const middleNameEl = document.getElementById('walkin-middle-name');
                const suffixEl = document.getElementById('walkin-suffix');
                if (lastNameEl && nameParts.length > 0) lastNameEl.value = nameParts.pop() || '';
                if (firstNameEl && nameParts.length > 0) firstNameEl.value = nameParts.shift() || '';
                if (middleNameEl && nameParts.length > 0) middleNameEl.value = nameParts.join(' ');
                // Set re-enroll flag
                const reEnrollFlag = document.getElementById('walkin-reenroll');
                if (reEnrollFlag) reEnrollFlag.value = studentId;
                // Show re-enrollment notice
                const notice = document.querySelector('#walkInEnrollmentModal .modal-header-styled h5');
                if (notice) notice.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Re-Enrollment';
            }, 300);
        })
        .catch(err => {
            console.error('Error loading student for re-enrollment:', err);
            showCustomAlert('error', 'Error', 'Failed to load student data for re-enrollment');
        });
    }

    // Student Edit Modal — tab switcher
    function seTab(name) {
        var tabs = ['personal','guardian','enrollment','address'];
        tabs.forEach(function(t) {
            var pane = document.getElementById('se-pane-' + t);
            var btn  = document.getElementById('se-tab-btn-' + t);
            if (!pane || !btn) return;
            var active = t === name;
            pane.style.display = active ? 'block' : 'none';
            btn.style.color       = active ? '#2563eb' : '#64748b';
            btn.style.fontWeight  = active ? '700' : '600';
            btn.style.borderBottomColor = active ? '#2563eb' : 'transparent';
        });
    }

    // Edit Student

    function editStudent(studentId) {

        fetch(`/admin/students/${studentId}`, {

            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }

        })

        .then(r => { if (!r.ok) throw new Error('Failed to load'); return r.json(); })

        .then(d => {

            const sd = d.student_data || {};

            const prof = d.profile || {};

            const grd = d.guardian || {};

            const addr = d.address || {};

            const enr = d.enrollment || {};

            

            // Reset to first tab and update header name
            seTab('personal');
            var headerName = document.getElementById('se-header-name');
            if (headerName) headerName.textContent = d.name || 'Student';

            // Personal Info

            document.getElementById('se-id').value = d.id;

            document.getElementById('se-name').value = d.name;

            document.getElementById('se-email').value = d.email;

            document.getElementById('se-lrn').value = d.lrn || '';

            document.getElementById('se-gender').value = prof.gender || sd.gender || '';

            document.getElementById('se-birthdate').value = prof.birthdate || sd.birthdate || '';

            document.getElementById('se-pob').value = sd.place_of_birth || '';

            document.getElementById('se-type').value = sd.student_type || '';

            document.getElementById('se-active').value = d.is_active ? '1' : '0';

            

            // Guardian

            document.getElementById('se-guardian-name').value = grd.name || sd.guardian_name || '';

            document.getElementById('se-guardian-rel').value = grd.relationship || sd.guardian_relationship || '';

            document.getElementById('se-guardian-phone').value = grd.contact || sd.guardian_phone || '';

            document.getElementById('se-guardian-occ').value = grd.occupation || sd.guardian_occupation || '';

            

            // Enrollment

            if (enr.grade_level) {

                document.getElementById('se-grade').value = enr.grade_level.toLowerCase().replace(/\s/g, '');

            }

            document.getElementById('se-section').value = enr.section || '';

            document.getElementById('se-enroll-status').value = enr.status || '';

            document.getElementById('se-pay-status').value = enr.payment_status || '';

            document.getElementById('se-pay-amount').value = enr.payment_amount || '';

            

            // Address

            document.getElementById('se-street').value = addr.street || sd.street_address || '';

            document.getElementById('se-zip').value = addr.zip_code || sd.zip_code || '';

            PHAddress.setValues(
                { region: 'se-region', province: 'se-province', city: 'se-city', barangay: 'se-brgy' },
                {
                    region:   sd.region   || addr.region   || '',
                    province: addr.province || sd.province || '',
                    city:     addr.municipality || sd.city || '',
                    barangay: addr.barangay || sd.barangay || '',
                }
            );

            

            new bootstrap.Modal(document.getElementById('studentEditModal')).show();

        })

        .catch(err => {

            console.error(err);

            showCustomAlert('error', 'Error', 'Failed to load student data.');

        });

    }

    // Save Student (from edit modal)

    function saveStudent(confirmed) {

        if (!confirmed) {
            showConfirm('Save Student', 'Are you sure you want to save these changes?', function() { saveStudent(true); }, {
                btnText: 'Save Changes', btnClass: 'btn btn-primary', btnIcon: 'bi-floppy-fill',
                headerBg: 'linear-gradient(135deg,#1a3a6c,#2563eb)', headerIcon: 'bi-floppy-fill'
            });
            return;
        }

        const id = document.getElementById('se-id').value;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const btn = document.querySelector('#studentEditModal [onclick*="saveStudent"]');

        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...'; }

        fetch(`/admin/students/${id}`, {

            method: 'PUT',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': token,

                'Accept': 'application/json'

            },

            body: JSON.stringify({

                name: document.getElementById('se-name').value,

                email: document.getElementById('se-email').value,

                lrn: document.getElementById('se-lrn').value,

                is_active: document.getElementById('se-active').value === '1',

                gender: document.getElementById('se-gender').value,

                birthdate: document.getElementById('se-birthdate').value,

                place_of_birth: document.getElementById('se-pob').value,

                student_type: document.getElementById('se-type').value,

                guardian_name: document.getElementById('se-guardian-name').value,

                guardian_relationship: document.getElementById('se-guardian-rel').value,

                guardian_phone: document.getElementById('se-guardian-phone').value,

                guardian_occupation: document.getElementById('se-guardian-occ').value,

                grade_level: document.getElementById('se-grade').value || null,

                section: document.getElementById('se-section').value,

                enrollment_status: document.getElementById('se-enroll-status').value,

                payment_status: document.getElementById('se-pay-status').value,

                payment_amount: document.getElementById('se-pay-amount').value,

                street: document.getElementById('se-street').value,

                region: document.getElementById('se-region').value,

                barangay: document.getElementById('se-brgy').value,

                city: document.getElementById('se-city').value,

                province: document.getElementById('se-province').value,

                zip_code: document.getElementById('se-zip').value,

            })

        })

        .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })

        .then(d => {

            if (d.success) {

                bootstrap.Modal.getInstance(document.getElementById('studentEditModal')).hide();

                showCustomAlert('success', 'Updated!', d.message);

                setTimeout(() => reloadWithSection(), 1200);

            } else {

                showCustomAlert('error', 'Error', d.message || 'Update failed.');

            }

        })

        .catch(err => {

            console.error(err);

            showCustomAlert('error', 'Error', 'Failed to update student.');

        })

        .finally(() => {

            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-floppy-fill me-1"></i>Save Changes'; }

        });

    }

    // Archive Student (soft delete)
    function deleteStudent(studentId, studentName) {
        showPasswordConfirm(
            'Archive "' + (studentName || 'this student') + '"? Their records will be preserved and can be restored from the Archives tab. Enter your password to confirm.',
            function(password) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch(`/admin/students/${studentId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password })
                })
                .then(async r => {
                    const d = await r.json();
                    if (!r.ok) {
                        _pwConfirmShowError(d.message || 'Incorrect password.');
                        return;
                    }
                    _pwConfirmClose();
                    if (d.success) {
                        showCustomAlert('success', 'Archived!', d.message);
                        setTimeout(() => reloadWithSection(), 1200);
                    } else {
                        showCustomAlert('error', 'Error', d.message || 'Archive failed.');
                    }
                })
                .catch(() => _pwConfirmShowError('Network error — failed to reach the server. Please try again.'));
            }
        );
    }

    // Restore archived student
    function restoreStudent(studentId, studentName) {
        showDeleteConfirm(
            'Restore "' + (studentName || 'this student') + '" back to active students?',
            function() {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch(`/admin/students/${studentId}/restore`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                })
                .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })
                .then(d => {
                    if (d.success) {
                        showCustomAlert('success', 'Restored!', d.message);
                        setTimeout(() => reloadWithSection(), 1200);
                    } else {
                        showCustomAlert('error', 'Error', d.message || 'Restore failed.');
                    }
                })
                .catch(() => showCustomAlert('error', 'Error', 'Failed to restore student.'));
            }
        );
    }

    // Toggle the archives panel at the bottom of Student Management
    function toggleArchivesPanel() {
        const panel   = document.getElementById('archives-panel');
        const chevron = document.getElementById('archives-chevron');
        if (!panel) return;
        const open = panel.style.display === 'none';
        panel.style.display = open ? '' : 'none';
        if (chevron) chevron.style.transform = open ? 'rotate(180deg)' : '';
    }

    // Permanently delete an archived student — cannot be undone
    function forceDeleteStudent(studentId, studentName) {
        showPasswordConfirm(
            '⚠️ PERMANENTLY DELETE "' + (studentName || 'this student') + '"? All records, documents, and files will be removed forever. This cannot be undone. Enter your password to confirm.',
            function(password) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch(`/admin/students/${studentId}/force`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password })
                })
                .then(async r => {
                    const d = await r.json();
                    if (!r.ok) {
                        _pwConfirmShowError(d.message || 'Incorrect password.');
                        return;
                    }
                    _pwConfirmClose();
                    if (d.success) {
                        showCustomAlert('success', 'Deleted!', d.message);
                        setTimeout(() => reloadWithSection(), 1200);
                    } else {
                        showCustomAlert('error', 'Error', d.message || 'Delete failed.');
                    }
                })
                .catch(() => _pwConfirmShowError('Network error — failed to reach the server. Please try again.'));
            }
        );
    }


    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // â”€â”€ Data for schedule modal dropdowns â”€â”€
    // Js::from() HTML-encodes the output for safe embedding in a script tag,
    // unlike a raw ->toJson() call would — section/subject/teacher names are
    // free-text fields admins type in, and a name containing a closing
    // script tag followed by another script tag would otherwise execute as
    // real page JavaScript for every admin who opens this dashboard.
    let sectionsList       = <?php echo \Illuminate\Support\Js::from($modalSections); ?>;
    let teachersList       = <?php echo \Illuminate\Support\Js::from($modalTeachers); ?>;
    let teacherAssignments = <?php echo \Illuminate\Support\Js::from($modalAssignments); ?>;

    // Filter sections by grade level in schedule modal
    function onScheduleGradeLevelChange(gradeLevel) {
        const sectionSel = document.getElementById('sched-section');
        const subjectSel = document.getElementById('sched-subject');
        const teacherSel = document.getElementById('sched-teacher');
        const roomSel    = document.getElementById('sched-room');

        sectionSel.innerHTML = '<option value="">— Select Section —</option>';
        subjectSel.innerHTML = '<option value="">— Loading subjects... —</option>';
        subjectSel.disabled = true;
        teacherSel.innerHTML = '<option value="">— None / TBA —</option>';

        if (!gradeLevel) {
            sectionSel.innerHTML = '<option value="">— Select Grade First —</option>';
            subjectSel.innerHTML = '<option value="">— Select Grade First —</option>';
            subjectSel.disabled = false;
            roomSel.innerHTML = '<option value="">— Select Grade First —</option>';
            return;
        }

        // Populate sections filtered by grade
        const filteredSections = sectionsList.filter(s => s.grade_level === gradeLevel);
        if (filteredSections.length === 0) {
            const o = document.createElement('option');
            o.value = '';
            o.textContent = '— No sections for this grade —';
            o.disabled = true;
            sectionSel.appendChild(o);
        } else {
            filteredSections.forEach(s => {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                sectionSel.appendChild(o);
            });
        }

        // Build room dropdown from unique room_numbers of this grade's sections
        const rooms = [...new Set(
            filteredSections.map(s => s.room_number).filter(r => r && r.trim() !== '')
        )].sort();
        roomSel.innerHTML = '<option value="">— No Room —</option>';
        rooms.forEach(r => {
            const o = document.createElement('option');
            o.value = r;
            o.textContent = r;
            roomSel.appendChild(o);
        });
        // Auto-select if all sections in this grade share the same room
        if (rooms.length === 1) {
            roomSel.value = rooms[0];
        }

        // Fetch subjects for this grade level from the API
        fetch('/admin/subjects?grade_level=' + encodeURIComponent(gradeLevel) + '&is_active=1', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const subjects = data.subjects || [];
            subjectSel.innerHTML = '<option value="">— Select Subject —</option>';
            if (subjects.length === 0) {
                const o = document.createElement('option');
                o.value = '';
                o.textContent = '— No subjects for this grade —';
                o.disabled = true;
                subjectSel.appendChild(o);
            } else {
                subjects.forEach(subj => {
                    const o = document.createElement('option');
                    o.value = subj.id;
                    o.textContent = subj.name + ' (' + subj.code + ')';
                    subjectSel.appendChild(o);
                });
            }
            subjectSel.disabled = false;
        })
        .catch(() => {
            subjectSel.innerHTML = '<option value="">— Failed to load subjects —</option>';
            subjectSel.disabled = false;
        });

        // Populate all teachers
        populateSchedTeachers('');
    }

    function onScheduleSectionChange(sectionId) {
        // When a specific section is selected, refine room to that section's room_number
        const roomSel = document.getElementById('sched-room');
        if (sectionId) {
            const sec = sectionsList.find(s => String(s.id) === String(sectionId));
            if (sec && sec.room_number) {
                // Ensure the option exists, add it if missing
                if (!Array.from(roomSel.options).some(o => o.value === sec.room_number)) {
                    const o = document.createElement('option');
                    o.value = sec.room_number;
                    o.textContent = sec.room_number;
                    roomSel.appendChild(o);
                }
                roomSel.value = sec.room_number;
            }
        }
        // Subjects are already loaded by grade level — just refresh teacher dropdown
        populateSchedTeachers(sectionId);
    }

    function populateSchedTeachers(sectionId) {
        const teacherSel = document.getElementById('sched-teacher');
        const currentVal = teacherSel.value;
        teacherSel.innerHTML = '<option value="">— None / TBA —</option>';

        // Collect advisory teacher IDs for this section (for informational ✓ mark only)
        const advisoryIds = new Set(
            teacherAssignments
                .filter(ta => ta.is_advisory && String(ta.section_id) === String(sectionId))
                .map(ta => String(ta.teacher_id))
        );

        teachersList.forEach(t => {
            const o = document.createElement('option');
            o.value = t.id;
            o.textContent = advisoryIds.has(String(t.id)) ? '✓ ' + t.name + ' (Advisory)' : t.name;
            teacherSel.appendChild(o);
        });

        if (currentVal) teacherSel.value = currentVal;
    }

    // â”€â”€ Helper: populate schedule modal dropdowns for edit (handles async subject load) â”€â”€
    function _loadScheduleModalForEdit(grade, sectionId, subjectId, teacherId, savedRoom) {
        const sectionSel = document.getElementById('sched-section');
        const subjectSel = document.getElementById('sched-subject');
        const roomSel    = document.getElementById('sched-room');

        // Populate sections for this grade
        sectionSel.innerHTML = '<option value=””>— Select Section —</option>';
        const gradeSections = sectionsList.filter(s => s.grade_level === grade);
        gradeSections.forEach(s => {
            const o = document.createElement('option');
            o.value = s.id;
            o.textContent = s.name;
            if (String(s.id) === String(sectionId)) o.selected = true;
            sectionSel.appendChild(o);
        });

        // Populate room dropdown from unique room_numbers of this grade's sections
        const rooms = [...new Set(
            gradeSections.map(s => s.room_number).filter(r => r && r.trim() !== '')
        )].sort();
        roomSel.innerHTML = '<option value=””>— No Room —</option>';
        rooms.forEach(r => {
            const o = document.createElement('option');
            o.value = r;
            o.textContent = r;
            roomSel.appendChild(o);
        });
        // Restore saved room — add as option if not in list (data created before rooms were set)
        if (savedRoom) {
            if (!Array.from(roomSel.options).some(o => o.value === savedRoom)) {
                const o = document.createElement('option');
                o.value = savedRoom;
                o.textContent = savedRoom;
                roomSel.appendChild(o);
            }
            roomSel.value = savedRoom;
        }

        // Fetch subjects for this grade, then restore selection
        subjectSel.innerHTML = '<option value="">Loadingâ€¦</option>';
        subjectSel.disabled = true;
        fetch('/admin/subjects?grade_level=' + encodeURIComponent(grade) + '&is_active=1', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const subjects = data.subjects || [];
            subjectSel.innerHTML = '<option value="">— Select Subject —</option>';
            subjects.forEach(subj => {
                const o = document.createElement('option');
                o.value = subj.id;
                o.textContent = subj.name + ' (' + subj.code + ')';
                if (String(subj.id) === String(subjectId)) o.selected = true;
                subjectSel.appendChild(o);
            });
            subjectSel.disabled = false;
            // Restore teacher after subjects are loaded
            populateSchedTeachers(sectionId);
            if (teacherId) document.getElementById('sched-teacher').value = teacherId;
        })
        .catch(() => {
            subjectSel.innerHTML = '<option value="">— Failed to load subjects —</option>';
            subjectSel.disabled = false;
        });
    }

    // â”€â”€ Installment data for modal â”€â”€
    window.installmentData = <?php echo json_encode($allInstallmentData ?? [], 15, 512) ?>;

    // Subject modal: wire status radio buttons
    document.querySelectorAll('[name="subj-active-radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('subj-active').value = this.value;
            document.getElementById('subj-active-label-1').style.borderColor = this.value === '1' ? 'var(--green)' : '#e2e8f0';
            document.getElementById('subj-active-label-0').style.borderColor = this.value === '0' ? 'var(--red)' : '#e2e8f0';
        });
    });

    // Schedule modal: day picker
    document.querySelectorAll('.sched-day-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.sched-day-btn').forEach(function(b) {
                b.style.borderColor = '#e2e8f0'; b.style.background = '#fff'; b.style.color = 'var(--muted)';
            });
            this.style.borderColor = '#6f42c1'; this.style.background = '#f3eeff'; this.style.color = '#6f42c1';
            document.getElementById('sched-day').value = this.dataset.day;
        });
    });

    function setSchedDay(day) {
        document.querySelectorAll('.sched-day-btn').forEach(function(b) {
            const active = b.dataset.day === day;
            b.style.borderColor = active ? '#6f42c1' : '#e2e8f0';
            b.style.background = active ? '#f3eeff' : '#fff';
            b.style.color = active ? '#6f42c1' : 'var(--muted)';
        });
        document.getElementById('sched-day').value = day;
    }

    function openSubjectModal() {

        document.getElementById('subjectModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Subject';

        document.getElementById('subj-id').value = '';

        document.getElementById('subj-code').value = '';

        document.getElementById('subj-name').value = '';

        document.getElementById('subj-desc').value = '';

        document.getElementById('subj-grade').value = '';

        document.getElementById('subj-active').value = '1';
        document.getElementById('subj-active-1').checked = true;
        document.getElementById('subj-active-label-1').style.borderColor = 'var(--green)';
        document.getElementById('subj-active-label-0').style.borderColor = '#e2e8f0';

        new bootstrap.Modal(document.getElementById('subjectModal')).show();

    }

    function editSubject(id, name, code, desc, grade, active) {

        document.getElementById('subjectModalTitle').innerHTML = '<i class="bi bi-pencil-fill me-2"></i>Edit Subject';

        document.getElementById('subj-id').value = id;

        document.getElementById('subj-code').value = code;

        document.getElementById('subj-name').value = name;

        document.getElementById('subj-desc').value = desc || '';

        document.getElementById('subj-grade').value = grade || '';

        const activeVal = active ? '1' : '0';
        document.getElementById('subj-active').value = activeVal;
        document.getElementById('subj-active-1').checked = activeVal === '1';
        document.getElementById('subj-active-0').checked = activeVal === '0';
        document.getElementById('subj-active-label-1').style.borderColor = activeVal === '1' ? 'var(--green)' : '#e2e8f0';
        document.getElementById('subj-active-label-0').style.borderColor = activeVal === '0' ? 'var(--red)' : '#e2e8f0';

        new bootstrap.Modal(document.getElementById('subjectModal')).show();

    }

    function saveSubject(confirmed) {

        if (!confirmed) {
            showConfirm('Save Subject', 'Are you sure you want to save these changes?', function() { saveSubject(true); }, {
                btnText: 'Save', btnClass: 'btn btn-primary', btnIcon: 'bi-floppy-fill',
                headerBg: 'linear-gradient(135deg,#0056b3,#003d82)', headerIcon: 'bi-floppy-fill'
            });
            return;
        }

        const id = document.getElementById('subj-id').value;

        const url = id ? `/admin/subjects/${id}` : '/admin/subjects';

        const method = id ? 'PUT' : 'POST';

        const body = {

            code: document.getElementById('subj-code').value,

            name: document.getElementById('subj-name').value,

            description: document.getElementById('subj-desc').value,

            grade_level: document.getElementById('subj-grade').value,

            is_active: document.getElementById('subj-active').value === '1'

        };

        fetch(url, {

            method: method,

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },

            body: JSON.stringify(body)

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();

            showCustomAlert('success', 'Saved!', id ? 'Subject updated.' : 'Subject created.');

            setTimeout(() => reloadWithSection(), 1000);

        })

        .catch(async err => {

            let msg = 'Failed to save subject.';

            try { const e = await err.json(); msg = e.message || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

        });

    }

    function deleteSubject(id) {

        showDeleteConfirm('Delete this subject?', function() {

            fetch(`/admin/subjects/${id}`, {

                method: 'DELETE',

                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }

            })

            .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })

            .then(d => {

                showCustomAlert('success', 'Deleted!', 'Subject deleted.');

                setTimeout(() => reloadWithSection(), 1000);

            })

            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete subject.'));

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // TEACHER MANAGEMENT

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // TEACHER ASSIGNMENTS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    let currentPageAssignments = [];

    async function loadTeacherAssignments() {
        console.log('Loading teacher assignments...');
        const schoolYear = document.getElementById('ta-school-year-filter').value;
        const sort = document.getElementById('ta-sort-filter').value;
        
        // Show loading state
        const tbody = document.getElementById('ta-table-body');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text);"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
        }
        
        try {
            const response = await fetch('/admin/teacher-assignments?school_year=' + schoolYear + '&sort=' + sort, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const data = await response.json();
            if (data.success) {
                renderAssignmentTable(data.assignments.data || data.assignments);
                updateAssignmentSummary(data.assignments.data || data.assignments);
                
                // Always render pagination
                renderPagination(data.assignments);
            }
        } catch (error) {
            console.error('Failed to load teacher assignments:', error);
        }
    }

    function renderAssignmentTable(assignments) {
        currentPageAssignments = assignments;
        const tbody = document.getElementById('ta-table-body');
        if (!assignments.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text);"><i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:8px;"></i>No advisory assignments found for this school year.</td></tr>';
            return;
        }
        const gradeLevelDisplay = {
            'nursery': 'Nursery', 'kindergarten': 'Kindergarten',
            'grade1': 'Grade 1', 'grade2': 'Grade 2', 'grade3': 'Grade 3',
            'grade4': 'Grade 4', 'grade5': 'Grade 5', 'grade6': 'Grade 6'
        };
        tbody.innerHTML = assignments.map(a => `
            <tr>
                <td style="font-weight:600;">${a.teacher_name} <span class="badge bg-primary ms-1" style="font-size:10px;"><i class="bi bi-star-fill me-1"></i>Advisory</span></td>
                <td>${a.section_name}</td>
                <td>${gradeLevelDisplay[a.grade_level] || a.grade_level}</td>
                <td>${a.school_year || '—'}</td>
                <td>
                    <button class="action-btn edit" onclick="editAssignment(${a.id})" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                    <button class="action-btn delete" onclick="deleteAssignment(${a.id})" title="Delete"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
        `).join('');
    }

    function updateAssignmentSummary(assignments) {
        document.getElementById('ta-advisory-count').textContent = assignments.length;
        const uniqueTeachers = new Set(assignments.map(a => a.teacher_id));
        document.getElementById('ta-teacher-count').textContent = uniqueTeachers.size;
        const uniqueSections = new Set(assignments.map(a => a.section_id));
        document.getElementById('ta-section-count').textContent = uniqueSections.size;
    }

    async function openAddAssignmentModal() {
        document.getElementById('assignment-id').value = '';
        document.getElementById('assignmentModalTitle').textContent = 'Add Advisory Assignment';
        document.getElementById('assignmentSaveBtn').textContent = 'Save Assignment';
        document.getElementById('assignment-teacher').value = '';
        document.getElementById('assignment-section').value = '';
        document.getElementById('assignment-school-year').value = document.getElementById('ta-school-year-filter').value;

        await loadAssignmentDropdowns();
        new bootstrap.Modal(document.getElementById('assignmentModal')).show();
    }

    async function openAssignAdvisoryForSection(sectionId, sectionName) {
        document.getElementById('assignment-id').value = '';
        document.getElementById('assignmentModalTitle').textContent = 'Assign Advisory Teacher — ' + sectionName;
        document.getElementById('assignmentSaveBtn').textContent = 'Save Assignment';
        document.getElementById('assignment-teacher').value = '';
        document.getElementById('assignment-school-year').value = document.getElementById('ta-school-year-filter') ?
            document.getElementById('ta-school-year-filter').value : '';

        await loadAssignmentDropdowns();
        // Pre-select section
        document.getElementById('assignment-section').value = sectionId;
        new bootstrap.Modal(document.getElementById('assignmentModal')).show();
    }

    async function openAddAssignmentModalForTeacher(teacherId, teacherName) {
        document.getElementById('assignment-id').value = '';
        document.getElementById('assignmentModalTitle').textContent = 'Add Advisory Assignment — ' + teacherName;
        document.getElementById('assignmentSaveBtn').textContent = 'Save Assignment';
        document.getElementById('assignment-section').value = '';
        document.getElementById('assignment-school-year').value = document.getElementById('ta-school-year-filter') ?
            document.getElementById('ta-school-year-filter').value : '';

        await loadAssignmentDropdowns();
        // Pre-select teacher
        document.getElementById('assignment-teacher').value = teacherId;
        new bootstrap.Modal(document.getElementById('assignmentModal')).show();
    }

    async function editAssignment(id) {
        try {
            const assignment = currentPageAssignments.find(a => a.id === id);
            if (!assignment) {
                console.error('Assignment not found in current page. ID:', id);
                return;
            }

            await loadAssignmentDropdowns();

            document.getElementById('assignment-id').value = id;
            document.getElementById('assignmentModalTitle').textContent = 'Edit Advisory Assignment';
            document.getElementById('assignmentSaveBtn').textContent = 'Update Assignment';
            document.getElementById('assignment-teacher').value = assignment.teacher_id;
            document.getElementById('assignment-section').value = assignment.section_id;
            document.getElementById('assignment-school-year').value = assignment.school_year;

            new bootstrap.Modal(document.getElementById('assignmentModal')).show();
        } catch (error) {
            console.error('Failed to open edit assignment modal:', error);
        }
    }

    async function loadAssignmentDropdowns() {
        const schoolYear = document.getElementById('ta-school-year-filter').value;

        // Load teachers
        try {
            const tRes = await fetch('/admin/teachers', { headers: { 'Accept': 'application/json' } });
            const tData = await tRes.json();
            const teacherSelect = document.getElementById('assignment-teacher');
            teacherSelect.innerHTML = '<option value="">Select teacher...</option>';
            if (tData.teachers) {
                tData.teachers.forEach(t => {
                    teacherSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
            }
        } catch (e) { console.error('Failed to load teachers:', e); }

        // Load sections for current school year
        try {
            const secRes = await fetch('/admin/sections', { headers: { 'Accept': 'application/json' } });
            const secData = await secRes.json();
            const sectionSelect = document.getElementById('assignment-section');
            sectionSelect.innerHTML = '<option value="">Select section...</option>';
            const gradeLabels = {
                'nursery': 'Nursery', 'kindergarten': 'Kindergarten',
                'grade1': 'Grade 1', 'grade2': 'Grade 2', 'grade3': 'Grade 3',
                'grade4': 'Grade 4', 'grade5': 'Grade 5', 'grade6': 'Grade 6'
            };
            if (secData.sections) {
                const filtered = secData.sections.filter(s => s.school_year === schoolYear);
                // If no sections for selected SY, show all sections
                const sectionsToShow = filtered.length > 0 ? filtered : secData.sections;
                // Sort by grade level order
                const gradeOrder = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
                sectionsToShow.sort((a, b) => gradeOrder.indexOf(a.grade_level) - gradeOrder.indexOf(b.grade_level));
                sectionsToShow.forEach(s => {
                    const gl = gradeLabels[s.grade_level] || s.grade_level;
                    sectionSelect.innerHTML += `<option value="${s.id}">${s.name} - ${gl}</option>`;
                });
                if (sectionsToShow.length === 0) {
                    sectionSelect.innerHTML = '<option value="">No sections found</option>';
                }
            }
        } catch (e) { console.error('Failed to load sections:', e); }
    }

    async function saveAssignment(event) {
        event.preventDefault();
        const id = document.getElementById('assignment-id').value;
        const teacherId = document.getElementById('assignment-teacher').value;
        const sectionId = document.getElementById('assignment-section').value;
        const schoolYear = document.getElementById('assignment-school-year').value;

        const payload = {
            teacher_id: teacherId,
            subject_id: null,
            section_id: sectionId,
            school_year: schoolYear,
            is_advisory: 1,
        };

        try {
            const url = id ? `/admin/teacher-assignments/${id}` : '/admin/teacher-assignments';
            const method = id ? 'PUT' : 'POST';
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('assignmentModal')).hide();
                loadTeacherAssignments();
                showToast(id ? 'Advisory assignment updated.' : 'Advisory assignment saved.', 'success');
            } else {
                showCustomAlert('error', 'Error', data.message || 'Failed to save assignment.');
            }
        } catch (error) {
            console.error('Failed to save assignment:', error);
            showCustomAlert('error', 'Error', 'An error occurred while saving the assignment.');
        }
    }

    async function deleteAssignment(id) {
        showDeleteConfirm('Are you sure you want to delete this assignment?', async function() {
            try {
                const response = await fetch(`/admin/teacher-assignments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    loadTeacherAssignments();
                    showToast('Assignment deleted.', 'success');
                }
            } catch (error) {
                console.error('Failed to delete assignment:', error);
            }
        });
    }

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

    // Load teacher assignments when switching to that section
    const originalShowSection = showSection;
    showSection = function(name) {
        // 'promotion' was merged into 'assessment' — redirect transparently
        if (name === 'promotion') name = 'assessment';
        originalShowSection(name);
        if (name === 'teacher-assignments') {
            loadTeacherAssignments();
        }
        if (name === 'summer') {
            loadSummerClasses();
        }
    };

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // END TEACHER ASSIGNMENTS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function renderPagination(paginator) {
        const paginationDiv = document.getElementById('ta-pagination');
        if (!paginationDiv) {
            console.error('ta-pagination container not found');
            return;
        }
        
        // Always show pagination, even for single page or no data
        if (!paginator || !paginator.lastPage) {
            // Show empty pagination if no data
            paginationDiv.innerHTML = '<div class="p-3 border-top"><nav><ul class="pagination"></ul></nav><div class="pagination-info">Showing 0 to 0 of 0 assignments</div></div>';
            return;
        }
        
        let html = '<div class="p-3 border-top">';
        html += '<nav><ul class="pagination">';

        // Previous button
        if (paginator.currentPage > 1) {
            html += '<li class="page-item"><a href="#" class="page-link" onclick="goToPage(' + (paginator.currentPage - 1) + '); return false;">Previous</a></li>';
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }

        // Page numbers
        for (let i = 1; i <= paginator.lastPage; i++) {
            if (i === paginator.currentPage) {
                html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
            } else {
                html += '<li class="page-item"><a href="#" class="page-link" onclick="goToPage(' + i + '); return false;">' + i + '</a></li>';
            }
        }

        // Next button
        if (paginator.currentPage < paginator.lastPage) {
            html += '<li class="page-item"><a href="#" class="page-link" onclick="goToPage(' + (paginator.currentPage + 1) + '); return false;">Next</a></li>';
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }

        html += '</ul></nav>';
        html += '<div class="pagination-info">Showing ' + (paginator.from || 0) + ' to ' + (paginator.to || 0) + ' of ' + (paginator.total || 0) + ' assignments</div>';
        html += '</div>';
        paginationDiv.innerHTML = html;
    }

    function goToPage(page) {
        const schoolYear = document.getElementById('ta-school-year-filter').value;
        const sort = document.getElementById('ta-sort-filter').value;
        fetch('/admin/teacher-assignments?school_year=' + schoolYear + '&sort=' + sort + '&page=' + page, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderAssignmentTable(data.assignments.data || data.assignments);
                updateAssignmentSummary(data.assignments.data || data.assignments);
                renderPagination(data.assignments);
            }
        });
    }

    // â”€â”€ Teacher email real-time validation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const _emailRfc = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    const _commonTypos = {
        'gmial.com':'gmail.com','gmal.com':'gmail.com','gmail.co':'gmail.com',
        'gamil.com':'gmail.com','gmaill.com':'gmail.com','gnail.com':'gmail.com',
        'yahooo.com':'yahoo.com','yaho.com':'yahoo.com','yahoomail.com':'yahoo.com',
        'hotmial.com':'hotmail.com','hotmal.com':'hotmail.com','outlok.com':'outlook.com',
    };

    let _emailDebounce = null;
    function validateTeacherEmailField(input) {
        clearTimeout(_emailDebounce);
        const icon = document.getElementById('teacher-email-icon');
        const msg  = document.getElementById('teacher-email-msg');
        const val  = input.value.trim();

        if (!val) {
            icon.style.display = 'none';
            msg.style.display  = 'none';
            input.style.borderColor = '';
            return;
        }

        // Show spinner while debouncing
        icon.style.display = '';
        icon.innerHTML = '<i class="bi bi-arrow-repeat" style="color:#999;animation:spin 1s linear infinite;"></i>';
        msg.style.display = 'none';

        _emailDebounce = setTimeout(function() {
            // 1. Basic format check
            if (!_emailRfc.test(val)) {
                setEmailState(input, 'error', 'Invalid email format. Please enter a valid email address.');
                return;
            }

            // 2. Typo suggestion
            const domain = val.split('@')[1].toLowerCase();
            if (_commonTypos[domain]) {
                const suggested = val.split('@')[0] + '@' + _commonTypos[domain];
                setEmailState(input, 'warn',
                    'Did you mean <strong>' + suggested + '</strong>? '
                    + '<a href="#" onclick="document.getElementById(\'teacher-email\').value=\'' + suggested + '\';validateTeacherEmailField(document.getElementById(\'teacher-email\'));return false;" '
                    + 'style="color:#d97706;font-weight:700;">Use this</a>');
                return;
            }

            // 3. Disposable/blocked domains
            const blocked = ['mailinator.com','guerrillamail.com','tempmail.com','throwam.com','yopmail.com','maildrop.cc'];
            if (blocked.includes(domain)) {
                setEmailState(input, 'error', 'Disposable email addresses are not allowed.');
                return;
            }

            // 4. All checks passed
            setEmailState(input, 'ok', 'Email format looks good.');
        }, 600);
    }

    function setEmailState(input, state, message) {
        const icon = document.getElementById('teacher-email-icon');
        const msg  = document.getElementById('teacher-email-msg');
        const map  = {
            ok:    { color:'#16a34a', border:'#86efac', ico:'<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>' },
            warn:  { color:'#d97706', border:'#fcd34d', ico:'<i class="bi bi-exclamation-triangle-fill" style="color:#d97706;"></i>' },
            error: { color:'#dc2626', border:'#fca5a5', ico:'<i class="bi bi-x-circle-fill" style="color:#dc2626;"></i>' },
        };
        const s = map[state] || map.error;
        icon.style.display  = '';
        icon.innerHTML      = s.ico;
        msg.style.display   = '';
        msg.innerHTML       = '<span style="color:' + s.color + ';">' + message + '</span>';
        input.style.borderColor = s.border;
    }

    function _resetTeacherEmailUI() {
        const emailEl = document.getElementById('teacher-email');
        const icon    = document.getElementById('teacher-email-icon');
        const msg     = document.getElementById('teacher-email-msg');
        if (emailEl) emailEl.style.borderColor = '';
        if (icon)    { icon.style.display = 'none'; icon.innerHTML = ''; }
        if (msg)     { msg.style.display  = 'none'; msg.innerHTML  = ''; }
    }

    function openTeacherModal() {

        document.getElementById('teacherModalTitle').innerHTML = '<i class="bi bi-person-badge-fill me-2"></i>Add Teacher';

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

    function editTeacher(id, name, email, active) {

        document.getElementById('teacherModalTitle').innerHTML = '<i class="bi bi-person-badge-fill me-2"></i>Edit Teacher';

        document.getElementById('teacher-id').value = id;
        document.getElementById('teacher-name').value = name;
        document.getElementById('teacher-email').value = email;
        document.getElementById('teacher-password').value = '';
        document.getElementById('teacher-autogen-info').style.display = 'none';
        document.getElementById('teacher-password-group').style.display = 'block';
        document.getElementById('teacher-active').value = active ? '1' : '0';
        _resetTeacherEmailUI();

        new bootstrap.Modal(document.getElementById('teacherModal')).show();

    }

    function saveTeacher(confirmed) {

        if (!confirmed) {
            // â”€â”€ Client-side validation before showing confirm dialog â”€â”€
            const nameVal  = document.getElementById('teacher-name').value.trim();
            const emailVal = document.getElementById('teacher-email').value.trim();

            if (!nameVal || nameVal.length < 2) {
                showCustomAlert('error', 'Missing Name', 'Please enter the teacher\'s full name (at least 2 characters).');
                document.getElementById('teacher-name').focus();
                return;
            }

            if (!emailVal) {
                showCustomAlert('error', 'Missing Email', 'Please enter an email address.');
                document.getElementById('teacher-email').focus();
                return;
            }

            if (!_emailRfc.test(emailVal)) {
                showCustomAlert('error', 'Invalid Email', 'The email address format is not valid. Please check and try again.');
                document.getElementById('teacher-email').focus();
                return;
            }

            const emailDomain = emailVal.split('@')[1].toLowerCase();
            const blocked = ['mailinator.com','guerrillamail.com','tempmail.com','throwam.com','yopmail.com','maildrop.cc'];
            if (blocked.includes(emailDomain)) {
                showCustomAlert('error', 'Invalid Email', 'Disposable email addresses are not allowed.');
                document.getElementById('teacher-email').focus();
                return;
            }

            showConfirm('Save Teacher', 'Are you sure you want to save these changes?', function() { saveTeacher(true); }, {
                btnText: 'Save', btnClass: 'btn btn-primary', btnIcon: 'bi-floppy-fill',
                headerBg: 'linear-gradient(135deg,#0056b3,#003d82)', headerIcon: 'bi-floppy-fill'
            });
            return;
        }

        const id = document.getElementById('teacher-id').value;

        const url = id ? `/admin/teachers/${id}` : '/admin/teachers';

        const method = id ? 'PUT' : 'POST';

        const body = {

            name: document.getElementById('teacher-name').value,

            email: document.getElementById('teacher-email').value,

            is_active: document.getElementById('teacher-active').value === '1'

        };

        if (id) {

            const pw = document.getElementById('teacher-password').value;

            if (pw) body.password = pw;

        }

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify(body)
        })
        .then(r => {
            if (!r.ok) {
                return r.text().then(t => {
                    try { const e = JSON.parse(t); throw new Error(e.message || Object.values(e.errors || {}).flat().join(', ')); }
                    catch(parseErr) { if (parseErr.message !== 'Failed to save teacher.') throw parseErr; throw new Error('HTTP ' + r.status + ': ' + t.substring(0,200)); }
                });
            }
            return r.json();
        })
        .then(d => {
            bootstrap.Modal.getInstance(document.getElementById('teacherModal')).hide();
            var msg = id ? 'Teacher updated.' : 'Teacher created! Login credentials sent to their email.';
            showCustomAlert('success', 'Saved!', msg);
            setTimeout(() => { localStorage.setItem('currentAdminSection', 'teachers'); location.reload(); }, 1500);
        })
        .catch(err => {
            console.error('saveTeacher error:', err);
            showCustomAlert('error', 'Error', err.message || 'Failed to save teacher.');
        });

    }

    function deleteTeacher(id) {

        showDeleteConfirm('Delete this teacher? This cannot be undone.', function() {

            fetch(`/admin/teachers/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })

            .then(d => {

                showCustomAlert('success', 'Deleted!', 'Teacher deleted.');

                setTimeout(() => reloadWithSection(), 1000);

            })

            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete teacher.'));

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // WALK-IN ENROLLMENT

    // â”€â”€ Auto-capitalize walk-in enrollment text inputs â”€â”€
    (function() {
        var skipNames = ['email','password','reference'];
        function capWords(el) {
            if (!el || el.type !== 'text') return;
            var name = (el.id || el.name || '').toLowerCase();
            if (skipNames.some(function(s){ return name.includes(s); })) return;
            var pos = el.selectionStart;
            el.value = el.value.replace(/\b\w/g, function(c){ return c.toUpperCase(); });
            try { el.setSelectionRange(pos, pos); } catch(e){}
        }
        document.addEventListener('input', function(e) {
            var el = e.target;
            var modal = document.getElementById('walkInEnrollmentModal');
            if (modal && modal.contains(el)) capWords(el);
        });
    })();

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    async function openWalkInEnrollmentModal() {

        // Reset form
        document.getElementById('walkInEnrollmentForm').reset();
        // Re-disable cascaded address selects (form.reset() restores values but not disabled state)
        const addrReset = { 'walkin-province': 'Select Province', 'walkin-city': 'Select City/Municipality', 'walkin-barangay': 'Select Barangay' };
        Object.entries(addrReset).forEach(([id, label]) => {
            const el = document.getElementById(id);
            el.innerHTML = `<option value="">${label}</option>`;
            el.disabled = true;
        });
        document.getElementById('walkin-payment-breakdown').style.display = 'none';

        // Reset re-enroll flag and title
        const reEnrollFlag = document.getElementById('walkin-reenroll');
        if (reEnrollFlag) reEnrollFlag.value = '';
        const notice = document.querySelector('#walkInEnrollmentModal .modal-header-styled h5');
        if (notice) notice.innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Walk-in Enrollment';

        // Load fee settings if not already loaded
        if (!window._feeTotals) {
            await loadFeeSettings();
        }

        updateWalkinPaymentOptions();

        new bootstrap.Modal(document.getElementById('walkInEnrollmentModal')).show();

        // Initialize keyboard navigation for walk-in form
        setTimeout(() => initWalkInFormNavigation(), 100);

    }

    // Keyboard navigation for Walk-in Enrollment Form
    function initWalkInFormNavigation() {
        const form = document.getElementById('walkInEnrollmentForm');
        if (!form) return;

        // Get all form rows that contain inputs
        const rows = form.querySelectorAll('.row');
        let navigationGrid = [];

        // Build a 2D grid of inputs (row -> [inputs in left-to-right order])
        rows.forEach(row => {
            const inputs = Array.from(row.querySelectorAll('input, select')).filter(el => !el.disabled && el.type !== 'hidden');
            if (inputs.length > 0) {
                navigationGrid.push(inputs);
            }
        });

        // Attach key handlers to all inputs
        navigationGrid.forEach((rowInputs, rowIndex) => {
            rowInputs.forEach((input, colIndex) => {
                // Remove any existing keydown handlers to avoid duplicates
                input.removeEventListener('keydown', handleWalkInKeydown);
                input.addEventListener('keydown', handleWalkInKeydown);

                function handleWalkInKeydown(e) {
                    let targetInput = null;

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        // Move to next field in reading order (right, or next row left)
                        if (colIndex < rowInputs.length - 1) {
                            // Move right in same row
                            targetInput = rowInputs[colIndex + 1];
                        } else if (rowIndex < navigationGrid.length - 1) {
                            // Move to first input of next row
                            targetInput = navigationGrid[rowIndex + 1][0];
                        } else {
                            // At last field - focus submit button
                            const submitBtn = document.querySelector('#walkInEnrollmentModal .btn-primary');
                            if (submitBtn) {
                                submitBtn.focus();
                                return;
                            }
                        }
                    } else if (e.key === 'ArrowRight' && !e.shiftKey) {
                        e.preventDefault();
                        if (colIndex < rowInputs.length - 1) {
                            targetInput = rowInputs[colIndex + 1];
                        }
                    } else if (e.key === 'ArrowLeft' && !e.shiftKey) {
                        e.preventDefault();
                        if (colIndex > 0) {
                            targetInput = rowInputs[colIndex - 1];
                        }
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (rowIndex < navigationGrid.length - 1) {
                            // Try to find matching column, or use last available
                            const nextRow = navigationGrid[rowIndex + 1];
                            targetInput = nextRow[Math.min(colIndex, nextRow.length - 1)];
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (rowIndex > 0) {
                            // Try to find matching column, or use last available
                            const prevRow = navigationGrid[rowIndex - 1];
                            targetInput = prevRow[Math.min(colIndex, prevRow.length - 1)];
                        }
                    }

                    if (targetInput) {
                        targetInput.focus();
                        // For select elements, try to open dropdown
                        if (targetInput.tagName === 'SELECT') {
                            setTimeout(() => targetInput.click(), 50);
                        }
                    }
                }
            });
        });
    }

    // Shared API function for Finance Management fee calculations
    async function fetchAdminFeeCalculation(gradeLevel, paymentOption) {
        try {
            console.log(`Fetching fees for ${gradeLevel}, option ${paymentOption}...`);
            const response = await fetch('/api/fees/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    grade_level: gradeLevel,
                    payment_option: paymentOption
                })
            });
            
            console.log('API response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('API error response:', errorText);
                throw new Error(`Failed to fetch fee calculation: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('API result:', result);
            return result.success ? result.data : null;
        } catch (error) {
            console.error('Fee API error:', error);
            return null;
        }
    }

    // Fallback fee calculation for Finance Management — uses loaded fee settings
    function calculateAdminFeeFallback(gradeLevel, paymentOption) {
        const t = window._feeTotals || {};
        const tuition = t.tuition || 0;
        const misc = t.misc || 0;
        const insurance = t.insurance || 0;
        const electric = t.electric || 0;

        const booksMap = {
            'nursery': t.booksNursery || 0, 'kindergarten': t.booksNursery || 0,
            'grade1': t.booksGrade1 || 0, 'grade2': t.booksGrade1 || 0,
            'grade3': t.booksGrade3 || 0, 'grade4': t.booksGrade4 || 0,
            'grade5': t.booksGrade4 || 0, 'grade6': t.booksGrade4 || 0
        };
        const books = booksMap[gradeLevel] || 0;
        const baseTotal = tuition + misc + books + insurance + electric;

        if (baseTotal === 0) return null;

        const rates = { total: baseTotal, tuition, misc, books, insurance, electric };
        let total = 0, downpayment = 0, monthly = 0, discount = 0;

        // Read current input values for downpayments and monthly components
        const dpBMap = {
            'nursery': parseFloat(document.getElementById('fee-optb-dp-nursery')?.value) || 0,
            'kindergarten': parseFloat(document.getElementById('fee-optb-dp-kinder')?.value) || 0,
            'grade1': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
            'grade2': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
            'grade3': parseFloat(document.getElementById('fee-optb-dp-grade3')?.value) || 0,
            'grade4': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
            'grade5': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
            'grade6': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0
        };
        const dpCMap = {
            'grade1': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
            'grade2': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
            'grade3': parseFloat(document.getElementById('fee-optc-dp-grade3')?.value) || 0,
            'grade4': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
            'grade5': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
            'grade6': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0
        };
        const dpDMap = {
            'nursery': parseFloat(document.getElementById('fee-optd-dp-nursery')?.value) || 0,
            'kindergarten': parseFloat(document.getElementById('fee-optd-dp-kinder')?.value) || 0
        };

        if (paymentOption === 'A') {
            discount = parseFloat(document.getElementById('fee-opta-discount')?.value) || 0;
            total = baseTotal - discount;
            return {
                components: rates,
                base_total: baseTotal,
                discount: discount,
                downpayment: 0,
                monthly_payment: 0,
                months: 0,
                total_payable: total,
                option: 'A'
            };
        } else if (paymentOption === 'B') {
            downpayment = dpBMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optb-monthly-tuition')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optb-monthly-electric')?.value) || 0;
            monthly = tuitionMo + electricMo;
            total = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: baseTotal,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: total,
                option: 'B'
            };
        } else if (paymentOption === 'C') {
            downpayment = dpCMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optc-monthly-tuition')?.value) || 0;
            const miscMo = parseFloat(document.getElementById('fee-optc-monthly-misc')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optc-monthly-electric')?.value) || 0;
            monthly = tuitionMo + miscMo + electricMo;
            total = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: baseTotal,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: total,
                option: 'C'
            };
        } else if (paymentOption === 'D') {
            downpayment = dpDMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optd-monthly-tuition')?.value) || 0;
            const miscMo = parseFloat(document.getElementById('fee-optd-monthly-misc')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optd-monthly-electric')?.value) || 0;
            monthly = tuitionMo + miscMo + electricMo;
            total = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: baseTotal,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: total,
                option: 'D'
            };
        }
        return null;
    }

    async function updateWalkinPaymentOptions() {
        const gradeLevel = document.getElementById('walkin-grade-level').value;
        if (!gradeLevel) return;

        // Fetch fee data from API for each option
        const [optA, optB, optC, optD] = await Promise.all([
            fetchAdminFeeCalculation(gradeLevel, 'A').catch(() => null),
            fetchAdminFeeCalculation(gradeLevel, 'B').catch(() => null),
            fetchAdminFeeCalculation(gradeLevel, 'C').catch(() => null),
            fetchAdminFeeCalculation(gradeLevel, 'D').catch(() => null)
        ]);

        // Use API data or fallback
        const feeA = optA || calculateAdminFeeFallback(gradeLevel, 'A');
        const feeB = optB || calculateAdminFeeFallback(gradeLevel, 'B');
        const feeC = optC || calculateAdminFeeFallback(gradeLevel, 'C');
        const feeD = optD || calculateAdminFeeFallback(gradeLevel, 'D');

        // Update Option A (Cash Basis with discount)
        const discountedTotal = feeA.total_payable || (feeA.base_total - feeA.discount);
        document.getElementById('walkin-opt-a-total').textContent = '₱' + discountedTotal.toLocaleString();

        // Update Option B (Monthly)
        document.getElementById('walkin-opt-b-monthly').textContent = '₱' + (feeB.monthly_payment || 0).toLocaleString() + '/mo';

        // Update Option C (Elem Monthly)
        document.getElementById('walkin-opt-c-monthly').textContent = '₱' + (feeC.monthly_payment || 0).toLocaleString() + '/mo';

        // Update Option D (Pre-Elem Monthly)
        document.getElementById('walkin-opt-d-monthly').textContent = '₱' + (feeD.monthly_payment || 0).toLocaleString() + '/mo';

        // Show/hide options based on grade level
        const walkinGrade = document.getElementById('walkin-grade-level')?.value?.toLowerCase().replace(/\s/g, '') || '';
        const isWalkinElementary = ['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'].includes(walkinGrade);
        const isWalkinPreElem = ['nursery', 'kindergarten'].includes(walkinGrade);

        document.getElementById('walkin-opt-c-container').style.display = isWalkinElementary ? 'block' : 'none';
        document.getElementById('walkin-opt-d-container').style.display = isWalkinPreElem ? 'block' : 'none';
    }

    function selectWalkinPaymentOption(option) {

        document.querySelectorAll('[id^="walkin-card-opt-"]').forEach(card => card.classList.remove('selected'));

        document.getElementById('walkin-card-opt-' + option.toLowerCase()).classList.add('selected');

        document.getElementById('walkin-payment-option').value = option;

        updateWalkinPaymentCalculation();

    }

    async function updateWalkinPaymentCalculation() {
        const gradeLevel = document.getElementById('walkin-grade-level').value;
        const paymentOption = document.getElementById('walkin-payment-option').value;

        if (!gradeLevel || !paymentOption) return;

        // Fetch fee data from API
        let feeData = await fetchAdminFeeCalculation(gradeLevel, paymentOption);
        
        // Use fallback if API fails
        if (!feeData) {
            feeData = calculateAdminFeeFallback(gradeLevel, paymentOption);
        }

        if (!feeData) {
            showCustomAlert('error', 'Error', 'Failed to calculate fees. Please try again.');
            return;
        }

        const c = feeData.components || feeData;
        let breakdownHTML = '';

        if (paymentOption === 'A') {
            const total = feeData.total_payable || (feeData.base_total - feeData.discount);
            breakdownHTML = `
                <div><strong>Base Total:</strong> ₱${(feeData.base_total || 0).toLocaleString()}</div>
                <div><strong>Discount:</strong> -₱${(feeData.discount || 0).toLocaleString()}</div>
                <div style="color:var(--green); font-weight:700; margin-top:4px;"><strong>Total Due:</strong> ₱${total.toLocaleString()}</div>
            `;
            document.getElementById('walkin-downpayment-amount').value = total;
            document.getElementById('walkin-monthly-amount').value = 0;
        } else {
            // Options B, C, D have similar structure with downpayment
            breakdownHTML = `
                <div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">
                    <div style="font-weight:600; margin-bottom:4px;">Downpayment Breakdown:</div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Books:</span><span>₱${(c.books || 0).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Insurance:</span><span>₱${(c.insurance || 0).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Misc/Reg/PTA:</span><span>₱${(c.misc || 0).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:4px;">
                        <span>Total Downpayment:</span><span>₱${(feeData.downpayment || 0).toLocaleString()}</span>
                    </div>
                </div>
                <div><strong>Monthly:</strong> ₱${(feeData.monthly_payment || 0).toLocaleString()} (${feeData.months || 9} months, July-March)</div>
                <div style="color:var(--green); font-weight:700; margin-top:4px;"><strong>Total:</strong> ₱${(feeData.total_payable || 0).toLocaleString()}</div>
            `;
            document.getElementById('walkin-downpayment-amount').value = feeData.downpayment || 0;
            document.getElementById('walkin-monthly-amount').value = feeData.monthly_payment || 0;
        }

        document.getElementById('walkin-total-amount').value = feeData.total_payable || 0;
        document.getElementById('walkin-breakdown-content').innerHTML = breakdownHTML;
        document.getElementById('walkin-payment-breakdown').style.display = 'block';
    }

    function submitWalkInEnrollment(confirmed) {

        if (!confirmed) {
            showConfirm('Submit Enrollment', 'Are you sure you want to submit this enrollment?', function() { submitWalkInEnrollment(true); }, {
                btnText: 'Submit', btnClass: 'btn btn-success', btnIcon: 'bi-send-fill',
                headerBg: 'linear-gradient(135deg,#27ae60,#1e8449)', headerIcon: 'bi-person-plus-fill'
            });
            return;
        }

        const form = document.getElementById('walkInEnrollmentForm');

        const emailInput = document.getElementById('walkin-guardian-email');
        if (!emailInput.value || !/^[^\s@]+@gmail\.com$/i.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            emailInput.focus();
            const hint = document.getElementById('walkin-email-hint');
            if (hint) { hint.textContent = 'Only Gmail addresses accepted (@gmail.com)'; hint.style.color = '#dc3545'; }
            showToast('Please enter a valid Gmail address for the guardian email.', 'error');
            return;
        }

        const phoneInput = document.getElementById('walkin-guardian-phone');
        if (!phoneInput.value || !/^9[0-9]{9}$/.test(phoneInput.value)) {
            phoneInput.classList.add('is-invalid');
            phoneInput.focus();
            showToast('Please enter a valid 10-digit phone number starting with 9 (after +63).', 'error');
            return;
        }

        const formData = {

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

        // Validate required fields

        const required = ['first_name', 'last_name', 'gender', 'birthdate', 'place_of_birth', 'grade_level', 'student_type', 'region', 'province', 'city', 'barangay', 'street_address', 'mother_name', 'mother_age', 'father_name', 'father_age', 'religious_affiliation', 'guardian_name', 'relationship', 'guardian_phone', 'guardian_email', 'payment_option'];

        for (let field of required) {

            if (!formData[field]) {

                showCustomAlert('warning', 'Warning', 'Please fill in all required fields.');

                return;

            }

        }

        const btn = document.querySelector('#walkInEnrollmentModal .btn-primary');

        btn.disabled = true;

        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Submitting...';

        fetch('/admin/enrollments/walk-in', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': csrfToken,

                'Accept': 'application/json'

            },

            body: JSON.stringify(formData)

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('walkInEnrollmentModal')).hide();

            const isReEnroll = document.getElementById('walkin-reenroll') && document.getElementById('walkin-reenroll').value;
            const msg = isReEnroll
                ? 'Re-enrollment submitted successfully. Reference number: ' + d.reference_number
                : 'Walk-in enrollment submitted. Reference number: ' + d.reference_number;
            showCustomAlert('success', 'Success!', msg);

            setTimeout(() => reloadWithSection(), 1500);

        })

        .catch(async err => {

            let msg = 'Failed to submit enrollment.';

            try { const e = await err.json(); msg = e.message || e.error || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

            btn.disabled = false;

            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Submit Enrollment';

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // PAYMENT UPDATE (Finance)

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    async function loadFeeSettings() {
        if (window._feeTotals) {
            return; // Already loaded
        }

        // Read fee values from the form fields
        const getVal = (id) => parseFloat(document.getElementById(id)?.value) || 0;

        window._feeTotals = {
            tuition: getVal('fee-tuition'),
            misc: getVal('fee-misc'),
            insurance: getVal('fee-insurance'),
            electric: getVal('fee-electric'),
            booksNursery: getVal('fee-books-nursery'),
            booksKindergarten: getVal('fee-books-kindergarten'),
            booksGrade1: getVal('fee-books-grade1'),
            booksGrade3: getVal('fee-books-grade3'),
            booksGrade4: getVal('fee-books-grade4')
        };

        console.log('Fee settings loaded:', window._feeTotals);
    }

    function updatePaymentStatus(enrollmentId, studentName, status, amount, method, ref, paymentOption, downpayment, monthly, totalFee, remaining) {
        const info = {
            option: paymentOption || '',
            totalFee: totalFee || 0,
            amountPaid: amount || 0,
            remaining: remaining || 0,
            downpayment: downpayment || 0,
            monthly: monthly || 0
        };

        openPaymentModal(
            enrollmentId,
            studentName,
            status,
            amount || '',
            method || '',
            ref || '',
            info
        );
    }

    function openPaymentModal(enrollmentId, studentName, status, amount, method, ref, info) {
        document.getElementById('pay-enrollment-id').value = enrollmentId;
        document.getElementById('pay-student-name').value = studentName;
        document.getElementById('pay-amount').value = amount || '';
        document.getElementById('pay-reference').value = ref || '';

        // Header labels
        var stuLabel = document.getElementById('pu-student-label');
        if (stuLabel) stuLabel.textContent = studentName || '—';

        const fmt = n => Number(n || 0).toLocaleString('en-PH', {minimumFractionDigits:2});
        const opt = info.option || '';
        const optLabels = { A:'Option A — Full Payment', B:'Option B — Monthly', C:'Option C — Elementary Monthly', D:'Option D — Pre-Elementary Monthly' };
        const optLabel = optLabels[opt] || (opt ? 'Option ' + opt : 'Not set');

        var planLbl = document.getElementById('pu-plan-label');
        if (planLbl) planLbl.textContent = optLabel;

        var balDisplay = document.getElementById('pu-balance-display');
        if (balDisplay) {
            balDisplay.textContent = '₱' + fmt(info.remaining);
            balDisplay.style.color = info.remaining > 0 ? '#fbbf24' : '#86efac';
        }

        // Summary grid cards
        const numCard = (label, val, color) =>
            `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
                <div style="font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">${label}</div>
                <div style="font-size:15px;font-weight:700;color:${color||'#1e3a5f'};">₱${val}</div>
            </div>`;

        let gridHTML =
            `<div style="background:#fff;border:1px solid #bfdbfe;border-radius:10px;padding:12px;grid-column:span 2;">
                <div style="font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Payment Plan</div>
                <div style="font-size:12px;font-weight:700;color:#1d4ed8;">${optLabel}</div>
            </div>` +
            numCard('Total Fee', fmt(info.totalFee), '#1e3a5f') +
            numCard('Amount Paid', fmt(info.amountPaid), '#16a34a') +
            numCard('Balance', fmt(info.remaining), info.remaining > 0 ? '#d97706' : '#16a34a');

        if (opt && opt !== 'A') {
            gridHTML += numCard('Downpayment', fmt(info.downpayment), '#1e3a5f') +
                        numCard('Monthly', fmt(info.monthly), '#1e3a5f');
        }

        document.getElementById('payment-breakdown-content').innerHTML = gridHTML;

        // Pre-select current plan type
        var currentPlan = (info.option === 'A') ? 'full' : 'installment';
        puSetPlan(currentPlan);

        new bootstrap.Modal(document.getElementById('paymentUpdateModal')).show();
    }

    function puSetPlan(plan) {
        var fullBtn  = document.getElementById('pu-plan-full');
        var instBtn  = document.getElementById('pu-plan-installment');
        var statusEl = document.getElementById('pay-status');

        if (!fullBtn || !instBtn) return;

        var isFull = plan === 'full';

        fullBtn.style.borderColor = isFull ? '#d97706' : '#e2e8f0';
        fullBtn.style.background  = isFull ? '#fffbeb' : '#fff';
        fullBtn.style.boxShadow   = isFull ? '0 0 0 3px rgba(217,119,6,.15)' : 'none';

        instBtn.style.borderColor = !isFull ? '#1d4ed8' : '#e2e8f0';
        instBtn.style.background  = !isFull ? '#eff6ff' : '#fff';
        instBtn.style.boxShadow   = !isFull ? '0 0 0 3px rgba(29,78,216,.12)' : 'none';

        // Set the hidden status for the controller
        statusEl.value = isFull ? 'paid' : 'partial';

        // Store plan type for savePaymentUpdate to send as payment_option hint
        document.getElementById('paymentUpdateModal').dataset.planType = plan;
    }

    function savePaymentUpdate(confirmed) {

        if (!confirmed) {
            showConfirm(
                'Save Plan',
                'Are you sure you want to save this payment plan?',
                function() { savePaymentUpdate(true); },
                {
                    btnText: 'Save Plan',
                    btnClass: 'btn btn-primary',
                    btnIcon: 'bi-floppy-fill',
                    headerBg: 'linear-gradient(135deg,#1a3a6c,#2563eb)',
                    headerIcon: 'bi-floppy-fill'
                }
            );
            return;
        }

        const enrollmentId = document.getElementById('pay-enrollment-id').value;
        if (!enrollmentId) return;

        const planType = document.getElementById('paymentUpdateModal').dataset.planType || 'installment';

        if (!planType) {
            showCustomAlert('warning', 'Select a Plan', 'Please choose Full Payment or Installment.');
            return;
        }

        // Send plan type — amount/method are NOT changed here (use the Pay button for that)
        const body = {
            payment_status:  document.getElementById('pay-status').value,
            payment_option:  planType === 'full' ? 'A' : null,
            payment_type:    planType,
            payment_action:  'plan-only'    // tells controller not to touch payment_amount
        };

        fetch(`/admin/enrollments/${enrollmentId}/payment`, {

            method: 'POST',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },

            body: JSON.stringify(body)

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('paymentUpdateModal')).hide();

            showCustomAlert('success', 'Updated!', 'Payment status updated successfully.');

            setTimeout(() => reloadWithSection(), 1000);

        })

        .catch(async err => {

            let msg = 'Failed to update payment.';

            try { const e = await err.json(); msg = e.message || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // ADMIN PAYMENT FLOW

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    async function openAdminPaymentFlow(enrollmentId, studentName, grade, amountPaid, downpayment, monthly, paymentType, totalFee, paymentOption) {

        document.getElementById('pay-flow-enrollment-id').value = enrollmentId;

        document.getElementById('pay-flow-student-name').textContent = studentName;

        document.getElementById('pay-flow-grade').value = grade;

        // Store current payment data for amount auto-fill
        document.getElementById('pay-flow-enrollment-id').dataset.amountPaid = amountPaid || 0;

        document.getElementById('pay-flow-enrollment-id').dataset.downpayment = downpayment || 0;

        document.getElementById('pay-flow-enrollment-id').dataset.monthly = monthly || 0;



        // Reset modal state

        document.getElementById('pay-flow-step-1').style.display = 'block';

        document.getElementById('pay-flow-step-2').style.display = 'none';

        document.getElementById('admin-payment-breakdown-display').style.display = 'none';

        document.getElementById('admin-pay-button-container').style.display = 'none';

        document.getElementById('admin-payment-amount-section').style.display = 'none';

        document.querySelectorAll('[id^="card-opt-"]').forEach(card => card.classList.remove('selected'));

        // Reset selected method — clear hidden input AND visual state
        document.getElementById('admin-selected-method').value = '';
        ['cash','gcash'].forEach(function(m) {
            var c = document.getElementById('card-' + m);
            if (!c) return;
            c.classList.remove('selected'); c.classList.remove('pf-selected');
            c.style.borderColor = '#e2e8f0'; c.style.background = '#fff'; c.style.boxShadow = 'none';
        });



        // Load fee settings if not already loaded
        if (!window._feeTotals) {
            await loadFeeSettings();
        }

        // Update payment option prices based on grade

        updateAdminPaymentPrices(grade);



        // Walk-in students or existing students with payment type: skip Step 1, go directly to Step 2
        if (paymentType && paymentType !== '') {
            let option = 'A';
            if (paymentType === 'installment') {
                // Use the student's actual payment option if available
                if (paymentOption && ['B', 'C', 'D'].includes(paymentOption)) {
                    option = paymentOption;
                } else {
                    // Fall back to grade-level mapping for walk-in students without explicit option
                    const gradeLevel = (grade || '').toLowerCase().replace(/\s/g, '');
                    if (['nursery', 'kindergarten'].includes(gradeLevel)) {
                        option = 'D';
                    } else if (['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'].includes(gradeLevel)) {
                        option = 'C';
                    } else {
                        option = 'B';
                    }
                }
            }
            selectAdminPaymentOption(option);
            document.getElementById('pay-flow-step-1').style.display = 'none';
            document.getElementById('pay-flow-step-2').style.display = 'block';
            
            // Auto-fill amount based on the calculated breakdown (after selectAdminPaymentOption runs)
            if (option === 'A') {
                const totalAmt = parseFloat(document.getElementById('admin-total-amount').value) || 0;
                const alreadyPaid = parseFloat(amountPaid) || 0;
                const remaining = Math.max(0, totalAmt - alreadyPaid);
                if (remaining > 0) {
                    document.getElementById('admin-payment-amount').value = remaining.toFixed(2);
                }
            } else {
                // For installment options, check if downpayment is still owed
                const alreadyPaid = parseFloat(amountPaid) || 0;
                const dpRequired = parseFloat(document.getElementById('admin-downpayment-amount').value) || 0;
                const monthlyAmt = parseFloat(document.getElementById('admin-monthly-amount').value) || 0;
                if (alreadyPaid < dpRequired) {
                    document.getElementById('admin-payment-amount').value = Math.max(0, dpRequired - alreadyPaid).toFixed(2);
                } else if (monthlyAmt > 0) {
                    document.getElementById('admin-payment-amount').value = monthlyAmt.toFixed(2);
                }
            }
        }



        new bootstrap.Modal(document.getElementById('paymentFlowModal')).show();

    }

    function updateAdminPaymentPrices(grade) {

        const gradeLevel = grade.toLowerCase().replace(/\s/g, '');

        const t = window._feeTotals || {};
        const tuition = t.tuition || 0;
        const misc = t.misc || 0;
        const insurance = t.insurance || 0;
        const electric = t.electric || 0;

        const booksMap = {
            'nursery': t.booksNursery || 0, 'kindergarten': t.booksNursery || 0,
            'grade1': t.booksGrade1 || 0, 'grade2': t.booksGrade1 || 0,
            'grade3': t.booksGrade3 || 0, 'grade4': t.booksGrade4 || 0,
            'grade5': t.booksGrade4 || 0, 'grade6': t.booksGrade4 || 0
        };
        const books = booksMap[gradeLevel] || 0;
        const baseTotal = tuition + misc + books + insurance + electric;

        // Update Option A (Cash with discount)
        const discount = parseFloat(document.getElementById('fee-opta-discount')?.value) || 0;
        const discountedTotal = baseTotal - discount;
        document.getElementById('opt-a-total').textContent = '₱' + discountedTotal.toLocaleString();

        // Update Option B (Monthly)
        const optBTuitionMo = parseFloat(document.getElementById('fee-optb-monthly-tuition')?.value) || 0;
        const optBElectricMo = parseFloat(document.getElementById('fee-optb-monthly-electric')?.value) || 0;
        const optBMonthly = optBTuitionMo + optBElectricMo;
        document.getElementById('opt-b-monthly').textContent = '₱' + optBMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';

        // Update Option C (Monthly)
        const optCTuitionMo = parseFloat(document.getElementById('fee-optc-monthly-tuition')?.value) || 0;
        const optCMiscMo = parseFloat(document.getElementById('fee-optc-monthly-misc')?.value) || 0;
        const optCElectricMo = parseFloat(document.getElementById('fee-optc-monthly-electric')?.value) || 0;
        const optCMonthly = optCTuitionMo + optCMiscMo + optCElectricMo;
        document.getElementById('opt-c-monthly').textContent = '₱' + optCMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';

        // Update Option D (Monthly)
        const optDTuitionMo = parseFloat(document.getElementById('fee-optd-monthly-tuition')?.value) || 0;
        const optDMiscMo = parseFloat(document.getElementById('fee-optd-monthly-misc')?.value) || 0;
        const optDElectricMo = parseFloat(document.getElementById('fee-optd-monthly-electric')?.value) || 0;
        const optDMonthly = optDTuitionMo + optDMiscMo + optDElectricMo;
        document.getElementById('opt-d-monthly').textContent = '₱' + optDMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';

        // Show/hide options based on grade level
        const isElementary = ['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'].includes(gradeLevel);
        const isPreElem = ['nursery', 'kindergarten'].includes(gradeLevel);

        document.getElementById('opt-c-container').style.display = isElementary ? 'block' : 'none';

        document.getElementById('opt-d-container').style.display = isPreElem ? 'block' : 'none';

    }

    function selectAdminPaymentOption(option) {
        document.querySelectorAll('[id^="card-opt-"]').forEach(card => {
            card.classList.remove('selected');
            card.classList.remove('pf-selected');
        });
        const card = document.getElementById('card-opt-' + option.toLowerCase());
        if (card) { card.classList.add('selected'); card.classList.add('pf-selected'); }
        document.getElementById('admin-selected-payment-option').value = option;
        calculateAdminPaymentBreakdown(option);
        document.getElementById('admin-pay-button-container').style.display = 'block';
    }

    function _pfUpdateAmountBubble(amount, planLabel) {
        var bubble = document.getElementById('pf-amount-bubble');
        var display = document.getElementById('pf-amount-display');
        var lbl = document.getElementById('pf-plan-label');
        if (!bubble || !display) return;
        if (amount > 0) {
            display.textContent = '₱' + parseFloat(amount).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
            if (lbl && planLabel) lbl.textContent = planLabel;
            bubble.style.display = 'flex';
        } else {
            bubble.style.display = 'none';
        }
    }

    function calculateAdminPaymentBreakdown(option) {

        const gradeLevel = document.getElementById('pay-flow-grade').value.toLowerCase().replace(/\s/g, '');

        const t = window._feeTotals || {};
        const tuition = t.tuition || 0;
        const misc = t.misc || 0;
        const insurance = t.insurance || 0;
        const electric = t.electric || 0;

        const booksMap = {
            'nursery': t.booksNursery || 0, 'kindergarten': t.booksNursery || 0,
            'grade1': t.booksGrade1 || 0, 'grade2': t.booksGrade1 || 0,
            'grade3': t.booksGrade3 || 0, 'grade4': t.booksGrade4 || 0,
            'grade5': t.booksGrade4 || 0, 'grade6': t.booksGrade4 || 0
        };
        const books = booksMap[gradeLevel] || 0;
        const baseTotal = tuition + misc + books + insurance + electric;

        const rates = { total: baseTotal, tuition, misc, books, insurance, electric };

        let breakdownHTML = '';

        let total = 0;

        let downpayment = 0;

        let monthly = 0;

        if (option === 'A') {

            const discount = parseFloat(document.getElementById('fee-opta-discount')?.value) || 0;
            total = baseTotal - discount;

            breakdownHTML = `

                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e0e0e0;">

                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">

                        <span>Tuition Fee:</span><span>₱${rates.tuition.toLocaleString()}</span>

                    </div>

                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">

                        <span>Miscellaneous:</span><span>₱${rates.misc.toLocaleString()}</span>

                    </div>

                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">

                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>

                    </div>

                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">

                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>

                    </div>

                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">

                        <span>Electric:</span><span>₱${rates.electric.toLocaleString()}</span>

                    </div>

                </div>

                <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--green);">

                    <span>Total (with discount):</span><span>₱${total.toLocaleString()}</span>

                </div>

            `;

            document.getElementById('admin-total-amount').value = total;
            document.getElementById('admin-downpayment-amount').value = 0;
            document.getElementById('admin-monthly-amount').value = 0;

        } else if (option === 'B') {

            const dpBMap = {
                'nursery': parseFloat(document.getElementById('fee-optb-dp-nursery')?.value) || 0,
                'kindergarten': parseFloat(document.getElementById('fee-optb-dp-kinder')?.value) || 0,
                'grade1': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
                'grade2': parseFloat(document.getElementById('fee-optb-dp-grade1')?.value) || 0,
                'grade3': parseFloat(document.getElementById('fee-optb-dp-grade3')?.value) || 0,
                'grade4': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
                'grade5': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0,
                'grade6': parseFloat(document.getElementById('fee-optb-dp-grade4')?.value) || 0
            };
            downpayment = dpBMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optb-monthly-tuition')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optb-monthly-electric')?.value) || 0;
            monthly = tuitionMo + electricMo;

            total = downpayment + (monthly * 9);

            breakdownHTML = `
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e0e0e0;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Tuition Fee:</span><span>₱${rates.tuition.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Miscellaneous:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Electric:</span><span>₱${rates.electric.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700;">
                        <span>Base Total:</span><span>₱${baseTotal.toLocaleString()}</span>
                    </div>
                </div>
                <div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">
                    <div style="font-weight:600; margin-bottom:4px;">Downpayment Breakdown:</div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Misc/Reg/PTA:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:4px;">
                        <span>Total Downpayment:</span><span>₱${downpayment.toLocaleString()}</span>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Monthly (9 months, July-March):</span>
                        <span>₱${monthly.toLocaleString()} Ã— 9 = ₱${(monthly * 9).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--green);">
                        <span>Total:</span><span>₱${total.toLocaleString()}</span>
                    </div>
                </div>
            `;

            document.getElementById('admin-downpayment-amount').value = downpayment;

            document.getElementById('admin-monthly-amount').value = monthly;

            document.getElementById('admin-total-amount').value = total;

        } else if (option === 'C') {

            const dpCMap = {
                'grade1': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
                'grade2': parseFloat(document.getElementById('fee-optc-dp-grade1')?.value) || 0,
                'grade3': parseFloat(document.getElementById('fee-optc-dp-grade3')?.value) || 0,
                'grade4': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
                'grade5': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0,
                'grade6': parseFloat(document.getElementById('fee-optc-dp-grade4')?.value) || 0
            };
            downpayment = dpCMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optc-monthly-tuition')?.value) || 0;
            const miscMo = parseFloat(document.getElementById('fee-optc-monthly-misc')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optc-monthly-electric')?.value) || 0;
            monthly = tuitionMo + miscMo + electricMo;

            total = downpayment + (monthly * 9);

            breakdownHTML = `
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e0e0e0;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Tuition Fee:</span><span>₱${rates.tuition.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Miscellaneous:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Electric:</span><span>₱${rates.electric.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700;">
                        <span>Base Total:</span><span>₱${baseTotal.toLocaleString()}</span>
                    </div>
                </div>
                <div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">
                    <div style="font-weight:600; margin-bottom:4px;">Downpayment Breakdown:</div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Misc/Reg/PTA:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:4px;">
                        <span>Total Downpayment:</span><span>₱${downpayment.toLocaleString()}</span>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Monthly (9 months, July-March):</span>
                        <span>₱${monthly.toLocaleString()} Ã— 9 = ₱${(monthly * 9).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--green);">
                        <span>Total:</span><span>₱${total.toLocaleString()}</span>
                    </div>
                </div>
            `;

            document.getElementById('admin-downpayment-amount').value = downpayment;

            document.getElementById('admin-monthly-amount').value = monthly;

            document.getElementById('admin-total-amount').value = total;

        } else if (option === 'D') {

            const dpDMap = {
                'nursery': parseFloat(document.getElementById('fee-optd-dp-nursery')?.value) || 0,
                'kindergarten': parseFloat(document.getElementById('fee-optd-dp-kinder')?.value) || 0
            };
            downpayment = dpDMap[gradeLevel] || 0;
            const tuitionMo = parseFloat(document.getElementById('fee-optd-monthly-tuition')?.value) || 0;
            const miscMo = parseFloat(document.getElementById('fee-optd-monthly-misc')?.value) || 0;
            const electricMo = parseFloat(document.getElementById('fee-optd-monthly-electric')?.value) || 0;
            monthly = tuitionMo + miscMo + electricMo;

            total = downpayment + (monthly * 9);

            breakdownHTML = `
                <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e0e0e0;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Tuition Fee:</span><span>₱${rates.tuition.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Miscellaneous:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Electric:</span><span>₱${rates.electric.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700;">
                        <span>Base Total:</span><span>₱${baseTotal.toLocaleString()}</span>
                    </div>
                </div>
                <div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">
                    <div style="font-weight:600; margin-bottom:4px;">Downpayment Breakdown:</div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Books:</span><span>₱${rates.books.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Insurance:</span><span>₱${rates.insurance.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span>Misc/Reg/PTA:</span><span>₱${rates.misc.toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:4px;">
                        <span>Total Downpayment:</span><span>₱${downpayment.toLocaleString()}</span>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Monthly (9 months, July-March):</span>
                        <span>₱${monthly.toLocaleString()} Ã— 9 = ₱${(monthly * 9).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--green);">
                        <span>Total:</span><span>₱${total.toLocaleString()}</span>
                    </div>
                </div>
            `;

            document.getElementById('admin-downpayment-amount').value = downpayment;

            document.getElementById('admin-monthly-amount').value = monthly;

            document.getElementById('admin-total-amount').value = total;

        }

        document.getElementById('admin-breakdown-content').innerHTML = breakdownHTML;

        document.getElementById('admin-payment-breakdown-display').style.display = 'block';

    }

    function showAdminPaymentMethodSelection() {
        document.getElementById('pay-flow-step-1').style.display = 'none';
        document.getElementById('pay-flow-step-2').style.display = 'block';
        // Update step pills
        var p1 = document.getElementById('pf-pill-1'), p2 = document.getElementById('pf-pill-2');
        if (p1) { p1.style.background = 'rgba(255,255,255,0.2)'; p1.style.color = 'rgba(255,255,255,0.6)'; }
        if (p2) { p2.style.background = 'rgba(255,255,255,0.9)'; p2.style.color = '#1a3a6c'; }
    }

    function switchAdminGcashTab(tab) {
        var numPanel = document.getElementById('admin-gcash-tab-number');
        var qrPanel  = document.getElementById('admin-gcash-tab-qr');
        var numBtn   = document.getElementById('admin-gcash-tab-btn-number');
        var qrBtn    = document.getElementById('admin-gcash-tab-btn-qr');
        if (!numPanel) return;
        if (tab === 'number') {
            numPanel.style.display = ''; qrPanel.style.display = 'none';
            numBtn.style.borderBottomColor = '#1d4ed8'; numBtn.style.color = '#1d4ed8'; numBtn.style.background = '#fff';
            qrBtn.style.borderBottomColor = 'transparent'; qrBtn.style.color = '#94a3b8'; qrBtn.style.background = 'none';
        } else {
            numPanel.style.display = 'none'; qrPanel.style.display = '';
            numBtn.style.borderBottomColor = 'transparent'; numBtn.style.color = '#94a3b8'; numBtn.style.background = 'none';
            qrBtn.style.borderBottomColor = '#1d4ed8'; qrBtn.style.color = '#1d4ed8'; qrBtn.style.background = '#fff';
        }
    }

    function copyAdminGcashNumber(num) {
        var done = function() {
            var icon = document.getElementById('admin-gcash-copy-icon');
            var lbl  = document.getElementById('admin-gcash-copy-label');
            if (!icon) return;
            icon.className = 'bi bi-check-lg'; lbl.textContent = 'Copied!';
            setTimeout(function() { icon.className = 'bi bi-copy'; lbl.textContent = 'Copy Number'; }, 2000);
        };
        if (!navigator.clipboard) {
            var ta = document.createElement('textarea'); ta.value = num;
            document.body.appendChild(ta); ta.select(); document.execCommand('copy');
            document.body.removeChild(ta); done(); return;
        }
        navigator.clipboard.writeText(num).then(done);
    }

    function selectAdminPaymentMethod(method) {
        // Store the selected method in a hidden input — the authoritative source of truth
        document.getElementById('admin-selected-method').value = method;

        ['cash','gcash'].forEach(function(m) {
            var card = document.getElementById('card-' + m);
            if (!card) return;
            var isActive = m === method;
            card.style.borderColor  = isActive ? '#1d4ed8' : '#e2e8f0';
            card.style.background   = isActive ? '#eff6ff' : '#fff';
            card.style.boxShadow    = isActive ? '0 0 0 3px rgba(29,78,216,.12)' : 'none';
            card.classList[isActive ? 'add' : 'remove']('selected');
            card.classList[isActive ? 'add' : 'remove']('pf-selected');
        });

        var gcashInfo   = document.getElementById('admin-gcash-info');
        var fieldsLabel = document.getElementById('admin-fields-label');

        if (method === 'gcash') {
            if (gcashInfo) gcashInfo.style.display = 'block';
            if (fieldsLabel) fieldsLabel.innerHTML = '<i class="bi bi-3-circle-fill me-1" style="color:#1d4ed8;font-size:13px;"></i> Payment Details';
        } else {
            if (gcashInfo) gcashInfo.style.display = 'none';
            if (fieldsLabel) fieldsLabel.innerHTML = '<i class="bi bi-2-circle-fill me-1" style="color:#1d4ed8;font-size:13px;"></i> Payment Details';
        }

        document.getElementById('admin-payment-amount-section').style.display = 'block';

        

        // Auto-fill payment amount based on selected option and what's already paid
        const totalAmount = parseFloat(document.getElementById('admin-total-amount').value) || 0;
        const downpayment = parseFloat(document.getElementById('admin-downpayment-amount').value) || 0;
        const monthly = parseFloat(document.getElementById('admin-monthly-amount').value) || 0;
        const paymentOption = document.getElementById('admin-selected-payment-option').value;

        const el = document.getElementById('pay-flow-enrollment-id');
        const alreadyPaid = parseFloat(el?.dataset?.amountPaid) || 0;
        const dpRequired  = parseFloat(el?.dataset?.downpayment) || downpayment;
        let autoAmount = 0;

        if (paymentOption === 'A') {
            autoAmount = Math.max(0, totalAmount - alreadyPaid);
        } else {
            autoAmount = alreadyPaid < dpRequired
                ? Math.max(0, dpRequired - alreadyPaid)
                : monthly;
        }

        document.getElementById('admin-payment-amount').value = autoAmount.toFixed(2);

        // Update header amount bubble
        const optLabels = { A:'Full Payment (Option A)', B:'Monthly Installment (Option B)',
                            C:'Monthly Installment (Option C)', D:'Monthly Installment (Option D)' };
        _pfUpdateAmountBubble(autoAmount, optLabels[paymentOption] || 'Payment');

        // Keep bubble in sync when admin edits the amount field
        const amtInput = document.getElementById('admin-payment-amount');
        amtInput.oninput = function() {
            _pfUpdateAmountBubble(parseFloat(this.value) || 0, optLabels[paymentOption] || 'Payment');
        };
    }

    function submitAdminPayment(confirmed) {
        if (!confirmed) {
            showConfirm('Submit Payment', 'Are you sure you want to submit this payment?', function() { submitAdminPayment(true); }, {
                btnText: 'Submit Payment', btnClass: 'btn btn-success', btnIcon: 'bi-cash-coin',
                headerBg: 'linear-gradient(135deg,#27ae60,#1e8449)', headerIcon: 'bi-cash-coin'
            });
            return;
        }

        const enrollmentId = document.getElementById('pay-flow-enrollment-id').value;
        const paymentOption = document.getElementById('admin-selected-payment-option').value;
        const paymentMethod = document.getElementById('admin-selected-method').value;
        const paymentAmount = document.getElementById('admin-payment-amount').value;
        const paymentReference = document.getElementById('admin-payment-reference').value;

        if (!paymentOption) {
            showCustomAlert('warning', 'Payment Option Required', 'Please select a payment option (A, B, C, or D).');
            return;
        }

        if (!paymentMethod || !['gcash', 'cash'].includes(paymentMethod)) {
            showCustomAlert('warning', 'Payment Method Required', 'Please select a payment method (GCash or Cash).');
            return;
        }

        if (!paymentAmount || parseFloat(paymentAmount) <= 0) {
            showCustomAlert('error', 'Error', 'Please enter a valid payment amount.');
            return;
        }

        const body = {
            payment_option: paymentOption,
            payment_method: paymentMethod,
            payment_amount: paymentAmount,
            payment_reference: paymentReference,
            payment_breakdown: {
                downpayment: document.getElementById('admin-downpayment-amount').value,
                monthly: document.getElementById('admin-monthly-amount').value,
                total: document.getElementById('admin-total-amount').value
            },
            payment_status: paymentOption === 'A' ? 'paid' : 'partial',
            payment_action: 'increment'

        };

        

        fetch(`/admin/enrollments/${enrollmentId}/payment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify(body)
        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('paymentFlowModal')).hide();

            showCustomAlert('success', 'Success!', 'Payment submitted successfully.');

            reloadWithSection();

        })

        .catch(async err => {

            let msg = 'Failed to submit payment.';

            try { const e = await err.json(); msg = e.message || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

        });

    }

    function printOfficialReceipt(data) {
        const w = window.open('', '_blank', 'width=680,height=820');
        const fmt = v => parseFloat(String(v).replace(/,/g, '')).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const typeLabel = data.type === 'walkin' ? 'Walk-in / Cashier' : 'Online Payment';
        w.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Official Receipt ${data.or_no}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #1a1a1a; }
  .page { max-width: 620px; margin: 0 auto; padding: 32px 36px; }
  /* Header */
  .hdr { text-align: center; border-bottom: 3px double #1a3a6c; padding-bottom: 18px; margin-bottom: 20px; }
  .hdr .school { font-size: 20px; font-weight: 800; color: #1a3a6c; letter-spacing: .3px; }
  .hdr .addr  { font-size: 12px; color: #555; margin-top: 3px; }
  .hdr .doc-title { font-size: 15px; font-weight: 700; color: #fff; background: #1a3a6c; display: inline-block;
                     padding: 5px 28px; border-radius: 4px; margin-top: 14px; letter-spacing: 2px; }
  /* OR Meta */
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; }
  .meta .or-no { font-size: 15px; font-weight: 700; color: #1a3a6c; }
  .meta .dt    { text-align: right; color: #555; line-height: 1.7; }
  /* Table */
  .tbl { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px; }
  .tbl th { background: #eef3fc; color: #1a3a6c; font-size: 10px; font-weight: 700;
             text-transform: uppercase; letter-spacing: 1px; padding: 8px 12px; border: 1px solid #d0d9ef; }
  .tbl td { padding: 10px 12px; border: 1px solid #e4e9f4; vertical-align: top; }
  .tbl tr:nth-child(even) td { background: #f8f9ff; }
  .label { color: #555; font-size: 11px; }
  /* Total */
  .total-row { background: #1a3a6c !important; }
  .total-row td { color: #fff !important; font-size: 15px; font-weight: 700; border-color: #1a3a6c !important; }
  /* Footer */
  .sigs { display: flex; justify-content: space-between; margin-top: 36px; gap: 24px; }
  .sig-box { flex: 1; text-align: center; }
  .sig-line { border-top: 1px solid #1a3a6c; margin: 0 8px; padding-top: 6px; font-size: 11px; color: #333; font-weight: 600; }
  .sig-role { font-size: 10px; color: #777; margin-top: 2px; }
  .note { text-align: center; font-size: 10px; color: #888; margin-top: 28px; border-top: 1px solid #e0e0e0; padding-top: 12px; }
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .page { padding: 12px 18px; }
    .no-print { display: none; }
  }
</style>
</head>
<body>
<div class="page">
  <!-- Header -->
  <div class="hdr">
    <div class="school">IEMELIF Learning Center</div>
    <div class="addr">General Tinio, Nueva Ecija &nbsp;|&nbsp; Tel: (044) 000-0000</div>
    <div class="doc-title">OFFICIAL RECEIPT</div>
  </div>
  <!-- OR # and Date -->
  <div class="meta">
    <div>
      <div class="label">OR Number</div>
      <div class="or-no">${data.or_no}</div>
    </div>
    <div class="dt">
      <div><strong>${data.date}</strong></div>
      <div>${data.time}</div>
      <div style="margin-top:4px;padding:2px 8px;border-radius:4px;background:#e3f2fd;color:#1565c0;font-size:10px;font-weight:600;display:inline-block;">${typeLabel}</div>
    </div>
  </div>
  <!-- Details Table -->
  <table class="tbl">
    <thead>
      <tr><th colspan="2">Payment Details</th></tr>
    </thead>
    <tbody>
      <tr>
        <td style="width:38%"><span class="label">Student Name</span></td>
        <td><strong>${data.student_name}</strong></td>
      </tr>
      <tr>
        <td><span class="label">Grade Level</span></td>
        <td>${data.grade_level}</td>
      </tr>
      <tr>
        <td><span class="label">School Year</span></td>
        <td>${data.school_year}</td>
      </tr>
      <tr>
        <td><span class="label">Description</span></td>
        <td>${data.description}</td>
      </tr>
      <tr>
        <td><span class="label">Payment Method</span></td>
        <td>${data.method}</td>
      </tr>
      <tr>
        <td><span class="label">Received By</span></td>
        <td>${data.received_by}</td>
      </tr>
      <tr class="total-row">
        <td>Amount Paid</td>
        <td>₱ ${fmt(data.amount)}</td>
      </tr>
    </tbody>
  </table>
  <!-- Signatures -->
  <div class="sigs">
    <div class="sig-box">
      <div style="height:44px;"></div>
      <div class="sig-line">${data.received_by}</div>
      <div class="sig-role">Cashier / Authorized Staff</div>
    </div>
    <div class="sig-box">
      <div style="height:44px;"></div>
      <div class="sig-line">${data.student_name}</div>
      <div class="sig-role">Student / Guardian</div>
    </div>
  </div>
  <!-- Note -->
  <div class="note">
    This is a computer-generated official receipt.<br>
    Please keep this receipt for your records. &copy; ${new Date().getFullYear()} IEMELIF Learning Center
  </div>
</div>
<scr` + `ipt>window.onload=function(){window.print();};</scr` + `ipt>
</body>
</html>`);
        w.document.close();
    }

    function printPaymentReceipt(enrollmentId, studentName, refNumber, amount, method, date) {
        const receiptWindow = window.open('', '_blank', 'width=600,height=700');
        const amountFormatted = parseFloat(amount).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
        
        receiptWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; margin: 0; }
                    .receipt { max-width: 500px; margin: 0 auto; border: 2px solid #333; padding: 20px; }
                    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                    .header h1 { margin: 0; font-size: 24px; }
                    .header p { margin: 5px 0; color: #666; }
                    .receipt-details { margin-bottom: 20px; }
                    .row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed #ccc; padding-bottom: 5px; }
                    .row label { font-weight: bold; }
                    .row span { color: #333; }
                    .total { font-size: 18px; font-weight: bold; color: #2a6dd6; border-top: 2px solid #333; padding-top: 10px; margin-top: 15px; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
                    @media print { body { padding: 0; } .receipt { border: none; } }
                </style>
            </head>
            <body>
                <div class="receipt">
                    <div class="header">
                        <h1>OFFICIAL RECEIPT</h1>
                        <p>ILC School System</p>
                        <p>Payment Confirmation</p>
                    </div>
                    <div class="receipt-details">
                        <div class="row">
                            <label>Receipt #:</label>
                            <span>${refNumber}</span>
                        </div>
                        <div class="row">
                            <label>Date:</label>
                            <span>${date}</span>
                        </div>
                        <div class="row">
                            <label>Student Name:</label>
                            <span>${studentName}</span>
                        </div>
                        <div class="row">
                            <label>Payment Method:</label>
                            <span>${method.toUpperCase()}</span>
                        </div>
                        <div class="row total">
                            <label>Amount Paid:</label>
                            <span>${amountFormatted}</span>
                        </div>
                    </div>
                    <div class="footer">
                        <p>This is an official receipt of payment.</p>
                        <p>Thank you for your payment!</p>
                        <p>Generated on ${new Date().toLocaleString()}</p>
                    </div>
                </div>
                <scr` + `ipt>
                    window.onload = function() {
                        window.print();
                        window.close();
                    };
                </scr` + `ipt>
            </body>
            </html>
        `);
        receiptWindow.document.close();
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // SECTION MANAGEMENT

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function openSectionModal() {

        document.getElementById('sectionModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Section';

        document.getElementById('sec-id').value = '';

        document.getElementById('sec-name').value = '';

        document.getElementById('sec-grade').value = '';

        document.getElementById('sec-room').value = '';

        document.getElementById('sec-max').value = '30';

        document.getElementById('sec-sy').value = document.getElementById('sec-sy').options[0].value;

        document.getElementById('sec-active').value = '1';

        new bootstrap.Modal(document.getElementById('sectionModal')).show();

    }

    function editSection(id, name, grade, room, sy, active, maxStudents) {

        document.getElementById('sectionModalTitle').innerHTML = '<i class="bi bi-pencil-fill me-2"></i>Edit Section';

        document.getElementById('sec-id').value = id;

        document.getElementById('sec-name').value = name;

        document.getElementById('sec-grade').value = grade || '';

        document.getElementById('sec-room').value = room || '';

        document.getElementById('sec-sy').value = sy || document.getElementById('sec-sy').options[0].value;

        document.getElementById('sec-max').value = maxStudents || 30;

        document.getElementById('sec-active').value = active ? '1' : '0';

        new bootstrap.Modal(document.getElementById('sectionModal')).show();

    }

    function saveSectionRecord(confirmed) {

        if (!confirmed) {
            showConfirm('Save Section', 'Are you sure you want to save these changes?', function() { saveSectionRecord(true); }, {
                btnText: 'Save', btnClass: 'btn btn-primary', btnIcon: 'bi-floppy-fill',
                headerBg: 'linear-gradient(135deg,#0056b3,#003d82)', headerIcon: 'bi-floppy-fill'
            });
            return;
        }

        const id = document.getElementById('sec-id').value;

        const url = id ? `/admin/sections/${id}` : '/admin/sections';

        const method = id ? 'PUT' : 'POST';

        const body = {

            name: document.getElementById('sec-name').value,

            grade_level: document.getElementById('sec-grade').value,

            room_number: document.getElementById('sec-room').value,

            school_year: document.getElementById('sec-sy').value,

            max_students: parseInt(document.getElementById('sec-max').value) || 30,

            is_active: document.getElementById('sec-active').value === '1'

        };

        fetch(url, {

            method: method,

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },

            body: JSON.stringify(body)

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('sectionModal')).hide();

            

            // If creating a new section, auto-create and assign standard subjects

            if (!id && body.grade_level && standardSubjects[body.grade_level]) {

                const gradeSubjects = standardSubjects[body.grade_level];

                const sectionId = d.data?.id || d.id;

                

                // Create subjects and assign them to the section

                let createdCount = 0;

                const createPromises = gradeSubjects.map(subject => {

                    return fetch('/admin/subjects', {

                        method: 'POST',

                        headers: { 

                            'Content-Type': 'application/json', 

                            'X-CSRF-TOKEN': csrfToken, 

                            'Accept': 'application/json' 

                        },

                        body: JSON.stringify({

                            name: subject.name,

                            code: subject.code,

                            grade_level: body.grade_level,

                            description: null,

                            is_active: true

                        })

                    })

                    .then(r => { if (!r.ok) throw r; return r.json(); })

                    .then(subjData => {

                        createdCount++;

                        return subjData.data?.id || subjData.id;

                    })

                    .catch(err => {

                        console.error('Failed to create subject:', subject.name, err);

                        return null;

                    });

                });

                

                Promise.all(createPromises).then(subjectIds => {

                    const validIds = subjectIds.filter(id => id !== null);

                    if (validIds.length > 0 && sectionId) {

                        // Assign created subjects to the section

                        const subjectsToAssign = validIds.map(id => ({ id: id }));

                        fetch(`/admin/sections/${sectionId}/subjects`, {

                            method: 'POST',

                            headers: { 

                                'Content-Type': 'application/json', 

                                'X-CSRF-TOKEN': csrfToken, 

                                'Accept': 'application/json' 

                            },

                            body: JSON.stringify({ subjects: subjectsToAssign })

                        })

                        .then(() => {

                            showCustomAlert('success', 'Saved!', `Section created with ${createdCount} standard subjects assigned.`);

                            setTimeout(() => reloadWithSection(), 1000);

                        })

                        .catch(() => {

                            showCustomAlert('success', 'Saved!', 'Section created. Some subjects may not have been assigned.');

                            setTimeout(() => reloadWithSection(), 1000);

                        });

                    } else {

                        showCustomAlert('success', 'Saved!', 'Section created.');

                        setTimeout(() => reloadWithSection(), 1000);

                    }

                });

            } else {

                showCustomAlert('success', 'Saved!', id ? 'Section updated.' : 'Section created.');

                setTimeout(() => reloadWithSection(), 1000);

            }

        })

        .catch(async err => {

            let msg = 'Failed to save section.';

            try { const e = await err.json(); msg = e.message || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

        });

    }

    function deleteSectionRecord(id) {

        showDeleteConfirm('Delete this section? This will also remove all associated schedules and student enrollments.', function() {

            fetch(`/admin/sections/${id}`, {

                method: 'DELETE',

                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }

            })

            .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })

            .then(d => {

                showCustomAlert('success', 'Deleted!', 'Section deleted.');

                setTimeout(() => reloadWithSection(), 1000);

            })

            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete section.'));

        });

    }

    function openAssignSubjectsModal(sectionId, sectionName, gradeLevel) {

        document.getElementById('assign-section-id').value = sectionId;

        document.getElementById('assign-section-name').value = sectionName;

        document.getElementById('assign-section-display').textContent = sectionName;

        // Auto-check subjects matching the section's grade level or "All Grades"

        document.querySelectorAll('.subject-checkbox').forEach(cb => {

            const subjectGrade = cb.closest('tr')?.querySelector('td:nth-child(3)')?.textContent || '';

            if (!subjectGrade || subjectGrade === 'All Grades' || subjectGrade.toLowerCase().includes(gradeLevel?.toLowerCase() || '')) {

                cb.checked = true;

            }

        });

        

        // Load currently assigned subjects for this section (this will override auto-selection)

        fetch(`/admin/sections/${sectionId}/subjects`, {

            headers: { 'Accept': 'application/json' }

        })

        .then(r => r.json())

        .then(data => {

            // Handle both array response and object with data property

            const subjects = Array.isArray(data) ? data : (data.data || []);

            if (subjects.length > 0) {

                document.querySelectorAll('.subject-checkbox').forEach(cb => cb.checked = false);

                subjects.forEach(subject => {

                    const checkbox = document.querySelector(`.subject-checkbox[value="${subject.id}"]`);

                    if (checkbox) checkbox.checked = true;

                });

            }

        })

        .catch(err => console.error('Error loading section subjects:', err));

        

        new bootstrap.Modal(document.getElementById('assignSubjectsModal')).show();

    }

    function saveSectionSubjects(confirmed) {

        if (!confirmed) {
            showConfirm('Save Subjects', 'Are you sure you want to save these changes?', function() { saveSectionSubjects(true); }, {
                btnText: 'Save', btnClass: 'btn btn-primary', btnIcon: 'bi-floppy-fill',
                headerBg: 'linear-gradient(135deg,#0056b3,#003d82)', headerIcon: 'bi-floppy-fill'
            });
            return;
        }

        const sectionId = document.getElementById('assign-section-id').value;

        const selectedSubjects = [];

        

        document.querySelectorAll('.subject-checkbox:checked').forEach(cb => {

            selectedSubjects.push({

                id: cb.value,

                name: cb.dataset.name,

                code: cb.dataset.code

            });

        });

        

        fetch(`/admin/sections/${sectionId}/subjects`, {

            method: 'POST',

            headers: { 

                'Content-Type': 'application/json', 

                'X-CSRF-TOKEN': csrfToken, 

                'Accept': 'application/json' 

            },

            body: JSON.stringify({ subjects: selectedSubjects })

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            bootstrap.Modal.getInstance(document.getElementById('assignSubjectsModal')).hide();

            showCustomAlert('success', 'Saved!', `Subjects assigned to section "${document.getElementById('assign-section-name').value}".`);

        })

        .catch(async err => {

            let msg = 'Failed to assign subjects.';

            try { const e = await err.json(); msg = e.message || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

        });

    }

    function showScreenshotModal(screenshotUrl) {
        if (!screenshotUrl) {
            showCustomAlert('warning', 'Warning', 'No screenshot available.');
            return;
        }
        document.getElementById('screenshotImage').src = screenshotUrl;
        document.getElementById('screenshotDownload').href = screenshotUrl;
        document.getElementById('screenshotOpen').href = screenshotUrl;
        new bootstrap.Modal(document.getElementById('screenshotModal')).show();
    }

    function viewScreenshot(screenshotUrl) {
        showScreenshotModal(screenshotUrl);
    }

    function openAddStudentModal(sectionId, sectionName, enrolled, max) {
        max = max || 30;

        document.getElementById('addStudentSectionId').value = sectionId;

        document.getElementById('addStudentSectionName').value = sectionName;

        document.getElementById('addStudentSectionDisplay').textContent = sectionName + ' (' + enrolled + '/' + max + ' enrolled)';

        if (parseInt(enrolled) >= parseInt(max)) {
            showCustomAlert('warning', 'Section Full', 'Section <strong>' + sectionName + '</strong> is already at full capacity (' + max + ' students).');
            return;
        }

        document.getElementById('studentSearchInput').value = '';

        document.getElementById('addStudentBtn').disabled = true;

        selectedStudentId = null;

        document.getElementById('studentListTableBody').innerHTML =

            '<tr><td colspan="7" style="text-align:center; padding:60px 20px;"><div style="background:#e8f8f0; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-hourglass-split" style="font-size:32px; color:var(--green);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">Loading eligible students...</div></td></tr>';

        new bootstrap.Modal(document.getElementById('addStudentModal')).show();

        // Fetch section details first to get current students

        fetch(`/admin/sections/${sectionId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(r => {
            if (!r.ok) {
                return r.text().then(t => { throw new Error('HTTP ' + r.status + ': ' + t.substring(0,200)); });
            }
            return r.json();
        })

        .then(sectionData => {

            const currentStudentIds = (sectionData.students || []).map(s => s.id);

            // Fetch all eligible students

            return fetch('/admin/students?payment_status=paid,partial,approved', {

                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }

            })

            .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })

            .then(d => {

                const allStudents = d.data || d.students || d || [];

                // Filter out students already in this section OR already assigned to any section
                eligibleStudents = allStudents.filter(s => {
                    if (currentStudentIds.includes(s.id)) return false;
                    // Exclude students already assigned to a section (has a section that is not empty/unassigned)
                    if (s.section && s.section !== 'Unassigned' && s.section !== '') return false;
                    return true;
                });

                renderStudentList(eligibleStudents);

            });

        })

        .catch(err => {

            console.error(err);

            document.getElementById('studentListTableBody').innerHTML =

                '<tr><td colspan="7" style="text-align:center; padding:60px 20px;"><div style="background:#ffe8e8; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-exclamation-triangle" style="font-size:32px; color:var(--red);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">Failed to load students</div><div style="font-size:13px; color:var(--muted);">Please try again</div></td></tr>';

        });

    }

    function renderStudentList(students) {

        const tbody = document.getElementById('studentListTableBody');

        if (!students || students.length === 0) {

            tbody.innerHTML =

                '<tr><td colspan="7" style="text-align:center; padding:60px 20px;"><div style="background:#e8f8f0; width:70px; height:70px; border-radius:50%; display:flex;align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-people" style="font-size:32px; color:var(--green);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">No eligible students found</div><div style="font-size:13px; color:var(--muted);">Students must be approved or enrolled with payment recorded</div></td></tr>';

            return;

        }

        const gradeLabels = {

            'nursery': 'Nursery', 'kindergarten': 'Kinder',

            'grade1': 'G1', 'grade2': 'G2', 'grade3': 'G3',

            'grade4': 'G4', 'grade5': 'G5', 'grade6': 'G6'

        };

        let html = '';

        students.forEach(function(s) {

            const isSelected = s.id === selectedStudentId;

            const gradeLabel = gradeLabels[s.grade_level] || s.grade_level || '—';

            const sectionDisplay = (s.section && s.section !== 'Unassigned') ? s.section : '<span style="color:var(--muted);">—</span>';

            const paymentClass = s.payment_status === 'paid' ? 'active' : (s.payment_status === 'partial' ? 'enrolled' : 'pending');

            const paymentLabel = s.payment_status === 'paid' ? 'Paid' : (s.payment_status === 'partial' ? 'Installment' : s.payment_status || '—');

            html += '<tr class="' + (isSelected ? 'table-row-selected' : '') + '" onclick="selectStudent(' + s.id + ')" data-id="' + s.id + '" data-lrn="' + escHtml((s.lrn || '').toLowerCase()) + '" data-name="' + escHtml((s.name || '').toLowerCase()) + '" data-email="' + escHtml((s.email || '').toLowerCase()) + '">' +

                    '<td style="font-family:monospace; font-size:12px;">' + escHtml(s.lrn || '—') + '</td>' +

                    '<td style="font-weight:600;">' + escHtml(s.name || '—') + '</td>' +

                    '<td style="font-size:12px; color:var(--muted);">' + escHtml(s.email || '—') + '</td>' +

                    '<td><span class="grade-chip" style="font-size:11px;">' + gradeLabel + '</span></td>' +

                    '<td><span class="status-badge ' + paymentClass + '" style="font-size:11px;">' + paymentLabel + '</span></td>' +

                    '<td>' + sectionDisplay + '</td>' +

                    '<td style="text-align:center;">' +

                        (isSelected ? '<i class="bi bi-check-circle-fill" style="color:var(--green); font-size:16px;"></i>' : '<i class="bi bi-circle" style="color:var(--border); font-size:16px;"></i>') +

                    '</td>' +

                '</tr>';

        });

        tbody.innerHTML = html;

    }

    function selectStudent(studentId) {

        selectedStudentId = studentId;

        document.getElementById('addStudentBtn').disabled = false;

        renderStudentList(filterStudents(document.getElementById('studentSearchInput').value));

    }

    function filterStudents(searchTerm) {

        searchTerm = searchTerm.toLowerCase();

        return eligibleStudents.filter(function(s) {

            return (s.lrn || '').toLowerCase().includes(searchTerm) ||

                   (s.name || '').toLowerCase().includes(searchTerm) ||

                   (s.email || '').toLowerCase().includes(searchTerm);

        });

    }

    function filterStudentList() {

        const searchTerm = document.getElementById('studentSearchInput').value;

        const filtered = filterStudents(searchTerm);

        renderStudentList(filtered);

    }

    function addStudentToSection() {

        if (!selectedStudentId) {

            showCustomAlert('warning', 'Warning', 'Please select a student.');

            return;

        }

        const sectionId = document.getElementById('addStudentSectionId').value;

        const sectionName = document.getElementById('addStudentSectionName').value;

        const btn = document.getElementById('addStudentBtn');

        btn.disabled = true;

        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Adding...';

        fetch(`/admin/sections/${sectionId}/add-student`, {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': csrfToken,

                'Accept': 'application/json'

            },

            body: JSON.stringify({ student_id: selectedStudentId })

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(d => {

            // Remove the added student from eligibleStudents array

            eligibleStudents = eligibleStudents.filter(s => s.id !== selectedStudentId);

            // Re-render the table

            renderStudentList(eligibleStudents);

            // Reset selection

            selectedStudentId = null;

            document.getElementById('addStudentBtn').disabled = true;

            // Clear search

            document.getElementById('studentSearchInput').value = '';

            showCustomAlert('success', 'Success!', 'Student added to ' + sectionName + '.');

            // Reload page to update enrollment count

            setTimeout(() => reloadWithSection(), 1000);

        })

        .catch(async err => {

            let msg = 'Failed to add student.';

            try { const e = await err.json(); msg = e.message || e.error || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}

            showCustomAlert('error', 'Error', msg);

            btn.disabled = false;

            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Add Student';

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // MANAGE SUBJECTS (Unified Add/Remove)
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function openManageSubjectsModal(sectionId, sectionName, gradeLevel) {
        document.getElementById('manual-section-id').value = sectionId;
        document.getElementById('manual-section-name').value = sectionName;
        document.getElementById('manual-section-display').textContent = sectionName;
        document.getElementById('manual-section-grade').value = gradeLevel || '';
        document.getElementById('add-subject-grade').value = gradeLevel || '';
        document.getElementById('add-subject-code').value = '';
        document.getElementById('add-subject-name').value = '';
        document.getElementById('add-subject-form-body').style.display = 'none';
        
        const subjectsList = document.getElementById('manage-subjects-list');
        subjectsList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:40px;"><i class="bi bi-hourglass-split" style="font-size:24px; color:var(--muted);"></i><div style="font-size:12px; color:var(--muted); margin-top:8px;">Loading subjects...</div></td></tr>';
        
        new bootstrap.Modal(document.getElementById('manualSubjectModal')).show();
        
        Promise.all([
            fetch('/admin/subjects', { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(`/admin/sections/${sectionId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(r => r.json())
        ])
        .then(([subjectsData, sectionData]) => {
            const allSubjects = Array.isArray(subjectsData) ? subjectsData : (subjectsData.subjects || []);
            const assignedSubjects = sectionData.subjects || [];
            const assignedIds = new Set(assignedSubjects.map(s => s.id));
            
            // Filter subjects to show only those matching the section's grade level
            const gradeSubjects = gradeLevel
                ? allSubjects.filter(s => !s.grade_level || s.grade_level === gradeLevel)
                : allSubjects;
            
            if (gradeSubjects.length === 0) {
                subjectsList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:40px;"><div style="font-size:13px; color:var(--muted);">No subjects found for ' + (gradeLevel || 'this grade level') + '. Create subjects first or check subject grade levels.</div></td></tr>';
                return;
            }
            
            subjectsList.innerHTML = gradeSubjects.map(sub => {
                const gradeDisplay = sub.grade_level 
                    ? sub.grade_level.replace(/_/g, ' ').replace(/\bgrade\b/gi, '').trim() || 'All Grades'
                    : 'All Grades';
                return `
                <tr>
                    <td style="text-align:center;">
                        <input type="checkbox" class="manage-subject-checkbox" value="${sub.id}" data-name="${sub.name}" data-code="${sub.code || ''}" ${assignedIds.has(sub.id) ? 'checked' : ''}>
                    </td>
                    <td><span style="font-weight:600;">${sub.code || ''}</span></td>
                    <td>${sub.name}</td>
                    <td><span class="grade-chip" style="font-size:11px;">${gradeDisplay}</span></td>
                </tr>
            `}).join('');
        })
        .catch(err => {
            console.error('Error loading subjects:', err);
            subjectsList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:40px;"><div style="font-size:13px; color:var(--red);">Failed to load subjects.</div></td></tr>';
        });
    }

    document.getElementById('manualSubjectSaveBtn').addEventListener('click', function() {
        const sectionId = document.getElementById('manual-section-id').value;
        const sectionName = document.getElementById('manual-section-name').value;
        
        const checkboxes = document.querySelectorAll('.manage-subject-checkbox:checked');
        const subjects = Array.from(checkboxes).map(cb => ({
            id: cb.value,
            name: cb.dataset.name || '',
            code: cb.dataset.code || ''
        }));
        
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';
        
        fetch(`/admin/sections/${sectionId}/subjects`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ subjects: subjects })
        })
        .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(d => {
            bootstrap.Modal.getInstance(document.getElementById('manualSubjectModal')).hide();
            showCustomAlert('success', 'Success!', `Subjects updated for section "${sectionName}".`);
            setTimeout(() => reloadWithSection(), 1000);
        })
        .catch(async err => {
            let msg = 'Failed to update subjects.';
            try { const e = await err.json(); msg = e.message || e.error || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}
            showCustomAlert('error', 'Error', msg);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save Changes';
        });
    });

    // Toggle Add Subject Form
    document.getElementById('toggle-add-subject-form').addEventListener('click', function() {
        const body = document.getElementById('add-subject-form-body');
        const isHidden = body.style.display === 'none';
        body.style.display = isHidden ? 'block' : 'none';
        this.innerHTML = isHidden
            ? '<i class="bi bi-chevron-up me-1"></i>Collapse'
            : '<i class="bi bi-chevron-down me-1"></i>Expand';
    });

    // Add New Subject (from Manage Subjects modal)
    document.getElementById('add-subject-btn').addEventListener('click', function() {
        const code = document.getElementById('add-subject-code').value.trim();
        const name = document.getElementById('add-subject-name').value.trim();
        const gradeLevel = document.getElementById('add-subject-grade').value.trim();

        if (!code || !name) {
            showCustomAlert('error', 'Validation Error', 'Subject code and name are required.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch('/admin/subjects', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                code: code,
                grade_level: gradeLevel || 'grade1',
                is_active: true
            })
        })
        .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(newSubject => {
            // Add new row to the subjects table with checkbox checked
            const subjectsList = document.getElementById('manage-subjects-list');
            const gradeDisplay = gradeLevel
                ? gradeLevel.replace(/_/g, ' ').replace(/\bgrade\b/gi, '').trim() || 'All Grades'
                : 'All Grades';

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td style="text-align:center;">
                    <input type="checkbox" class="manage-subject-checkbox" value="${newSubject.id}" data-name="${newSubject.name}" data-code="${newSubject.code || ''}" checked>
                </td>
                <td><span style="font-weight:600;">${newSubject.code || ''}</span></td>
                <td>${newSubject.name}</td>
                <td><span class="grade-chip" style="font-size:11px;">${gradeDisplay}</span></td>
            `;
            subjectsList.appendChild(newRow);

            // Clear form
            document.getElementById('add-subject-code').value = '';
            document.getElementById('add-subject-name').value = '';

            showCustomAlert('success', 'Subject Added', `"${name}" (${code}) has been created and checked.`);
        })
        .catch(async err => {
            let msg = 'Failed to add subject.';
            try { const e = await err.json(); msg = e.message || e.error || Object.values(e.errors || {}).flat().join(', '); } catch(x) {}
            showCustomAlert('error', 'Error', msg);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Add';
        });
    });

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // VIEW SECTION SUBJECTS

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function viewSectionSubjects(sectionId, sectionName) {
        document.getElementById('viewSubjectsSectionId').value = sectionId;
        document.getElementById('viewSubjectsSectionName').textContent = sectionName;
        document.getElementById('viewSubjectsGradeLevel').textContent = 'Loading...';
        document.getElementById('viewSubjectsList').innerHTML = '<span style="color:var(--muted); font-size:12px;">Loading...</span>';
        new bootstrap.Modal(document.getElementById('viewSubjectsModal')).show();
        fetch(`/admin/sections/${sectionId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(r => {
            if (!r.ok) {
                return r.text().then(t => { throw new Error('HTTP ' + r.status + ': ' + t.substring(0,200)); });
            }
            return r.json();
        })
        .then(d => {
            if (d.error) {
                throw new Error(d.error);
            }
            document.getElementById('viewSubjectsGradeLevel').textContent = d.grade_level || '—';
            const subjects = d.subjects || [];
            const subjectsList = document.getElementById('viewSubjectsList');

            if (subjects.length === 0) {
                subjectsList.innerHTML = '<span style="color:var(--muted); font-size:12px;">No subjects assigned</span>';
            } else {
                subjectsList.innerHTML = subjects.map(sub =>
                    `<span class="grade-chip" style="font-size:12px; background:var(--blue); color:white;">${sub.name}</span>`
                ).join('');
            }
        })
        .catch(err => {
            console.error('viewSectionSubjects error:', err);
            document.getElementById('viewSubjectsList').innerHTML =
                '<span style="color:var(--red); font-size:12px;">Failed to load: ' + (err.message || 'Unknown error') + '</span>';
        });
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // VIEW SECTION STUDENTS

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    var _viewSectionGradeLevel = null;

    function viewSectionStudents(sectionId, sectionName) {
        _viewSectionGradeLevel = null;
        document.getElementById('viewSectionId').value = sectionId;
        document.getElementById('viewSectionName').value = sectionName;
        document.getElementById('viewSectionDisplay').textContent = sectionName;
        document.getElementById('viewSectionCapacityText').textContent = '—';
        document.getElementById('viewSectionCapacityBar').style.width = '0%';
        document.getElementById('viewSectionCapacityBar').style.background = 'var(--green)';
        document.getElementById('viewStudentsTableBody').innerHTML =
            '<tr><td colspan="5" style="text-align:center; padding:60px 20px;"><div style="background:#e8f8f0; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-hourglass-split" style="font-size:32px; color:var(--green);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">Loading students...</div></td></tr>';
        new bootstrap.Modal(document.getElementById('viewSectionStudentsModal')).show();
        fetch(`/admin/sections/${sectionId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(r => {
            if (!r.ok) return r.text().then(t => { throw new Error('HTTP ' + r.status + ': ' + t.substring(0,200)); });
            return r.json();
        })
        .then(d => {
            if (d.error) throw new Error(d.error);
            _viewSectionGradeLevel = d.grade_level || null;
            const enrolled = d.current_enrollment ?? 0;
            const max = d.max_students ?? 30;
            const pct = max > 0 ? Math.min(100, Math.round(enrolled / max * 100)) : 0;
            const barColor = pct >= 90 ? 'var(--red)' : (pct >= 70 ? 'var(--gold, #f59e0b)' : 'var(--green)');
            document.getElementById('viewSectionCapacityText').textContent = enrolled + '/' + max + ' enrolled';
            document.getElementById('viewSectionCapacityBar').style.width = pct + '%';
            document.getElementById('viewSectionCapacityBar').style.background = barColor;
            renderSectionStudents(d.students || []);
        })
        .catch(err => {
            console.error('viewSectionStudents error:', err);
            document.getElementById('viewStudentsTableBody').innerHTML =
                '<tr><td colspan="5" style="text-align:center; padding:60px 20px;"><div style="background:#ffe8e8; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-exclamation-triangle" style="font-size:32px; color:var(--red);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">Failed to load students</div><div style="font-size:13px; color:var(--muted);">' + (err.message || 'Unknown error') + '</div></td></tr>';
        });
    }

    function renderSectionStudents(students) {
        const tbody = document.getElementById('viewStudentsTableBody');
        const sectionId = document.getElementById('viewSectionId').value;

        if (!students || students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:60px 20px;"><div style="background:#e8f8f0; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;"><i class="bi bi-people" style="font-size:32px; color:var(--green);"></i></div><div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">No students assigned</div><div style="font-size:13px; color:var(--muted);">This section has no students yet</div></td></tr>';
            return;
        }

        const gradeLabels = {
            'nursery': 'Nursery', 'kindergarten': 'Kinder',
            'grade1': 'G1', 'grade2': 'G2', 'grade3': 'G3',
            'grade4': 'G4', 'grade5': 'G5', 'grade6': 'G6'
        };

        let html = '';
        students.forEach(function(s) {
            const enrollment = s.latest_enrollment || s.latestEnrollment || null;
            const gradeLevel = enrollment ? enrollment.grade_level : null;
            const gradeLabel = gradeLabels[gradeLevel] || gradeLevel || '—';
            const payStatus = enrollment ? enrollment.payment_status : null;
            const payClass = payStatus === 'paid' ? 'active' : (payStatus === 'partial' ? 'enrolled' : 'pending');
            const payLabel = payStatus === 'paid' ? 'Paid' : (payStatus === 'partial' ? 'Installment' : (payStatus || '—'));
            const safeName = escHtml(s.name || '');
            const safeNameAttr = encodeURIComponent(s.name || '');

            html += `<tr>
                <td style="font-family:monospace; font-size:12px;">${escHtml(s.lrn || '—')}</td>
                <td style="font-weight:600;">${safeName || '—'}</td>
                <td><span class="grade-chip" style="font-size:11px;">${escHtml(gradeLabel)}</span></td>
                <td><span class="status-badge ${payClass}" style="font-size:11px;">${escHtml(payLabel)}</span></td>
                <td style="text-align:center; white-space:nowrap;">
                    <button class="action-btn edit" title="Transfer to Another Section" onclick="openTransferModal(${s.id}, decodeURIComponent('${safeNameAttr}'), ${sectionId})">
                        <i class="bi bi-arrow-left-right"></i>
                    </button>
                    <button class="action-btn delete" title="Remove from Section" onclick="removeSectionStudent(${s.id}, ${sectionId}, decodeURIComponent('${safeNameAttr}'))">
                        <i class="bi bi-person-dash-fill"></i>
                    </button>
                </td>
            </tr>`;
        });

        tbody.innerHTML = html;
    }

    function openTransferModal(studentId, studentName, fromSectionId) {
        document.getElementById('transferStudentId').value = studentId;
        document.getElementById('transferFromSectionId').value = fromSectionId;
        document.getElementById('transferStudentDisplay').textContent = studentName;
        document.getElementById('transferTargetSection').innerHTML = '<option value="">Loading sections...</option>';
        document.getElementById('transferBtn').disabled = true;

        new bootstrap.Modal(document.getElementById('transferSectionModal')).show();

        fetch('/admin/sections', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(d => {
            const all = d.sections || d || [];
            const grade = _viewSectionGradeLevel;
            const eligible = all.filter(s =>
                parseInt(s.id) !== parseInt(fromSectionId) &&
                s.is_active &&
                (!grade || s.grade_level === grade)
            );
            if (eligible.length === 0) {
                document.getElementById('transferTargetSection').innerHTML = '<option value="">No other sections available for this grade</option>';
                return;
            }
            let opts = '<option value="">Select target section...</option>';
            eligible.forEach(s => {
                const enrolled = s.current_enrollment ?? 0;
                const max = s.max_students ?? 30;
                const full = enrolled >= max;
                opts += `<option value="${s.id}" ${full ? 'disabled' : ''}>${s.name} — ${enrolled}/${max}${full ? ' (FULL)' : ''}</option>`;
            });
            document.getElementById('transferTargetSection').innerHTML = opts;
        })
        .catch(() => {
            document.getElementById('transferTargetSection').innerHTML = '<option value="">Failed to load sections</option>';
        });
    }

    function confirmTransfer() {
        const studentId      = document.getElementById('transferStudentId').value;
        const fromSectionId  = document.getElementById('transferFromSectionId').value;
        const targetSectionId = document.getElementById('transferTargetSection').value;
        if (!targetSectionId) { showCustomAlert('warning', 'Warning', 'Please select a target section.'); return; }

        const btn = document.getElementById('transferBtn');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Transferring...';

        fetch(`/admin/sections/${fromSectionId}/transfer-student`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ student_id: studentId, target_section_id: targetSectionId })
        })
        .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(d => {
            bootstrap.Modal.getInstance(document.getElementById('transferSectionModal')).hide();
            showCustomAlert('success', 'Transferred!', 'Student moved to section <strong>' + d.new_section + '</strong>.');
            const sectionName = document.getElementById('viewSectionName').value;
            setTimeout(() => viewSectionStudents(fromSectionId, sectionName), 500);
            setTimeout(() => reloadWithSection(), 1500);
        })
        .catch(async err => {
            let msg = 'Failed to transfer student.';
            try { const e = await err.json(); msg = e.message || e.error || msg; } catch(x) {}
            showCustomAlert('error', 'Error', msg);
            btn.disabled = false;
            btn.innerHTML = origHtml;
        });
    }

    function removeSectionStudent(studentId, sectionId, studentName) {
        showConfirm('Remove from Section',
            'Remove <strong>' + studentName + '</strong> from this section? They can be re-assigned later.',
            function() {
                fetch(`/admin/sections/${sectionId}/remove-student`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ student_id: studentId })
                })
                .then(r => { if (!r.ok) throw r; return r.json(); })
                .then(() => {
                    showCustomAlert('success', 'Removed!', studentName + ' removed from section.');
                    const sectionName = document.getElementById('viewSectionName').value;
                    viewSectionStudents(sectionId, sectionName);
                    setTimeout(() => reloadWithSection(), 1500);
                })
                .catch(async err => {
                    let msg = 'Failed to remove student.';
                    try { const e = await err.json(); msg = e.message || e.error || msg; } catch(x) {}
                    showCustomAlert('error', 'Error', msg);
                });
            },
            { btnText: 'Remove', btnClass: 'btn btn-danger', btnIcon: 'bi-person-dash-fill',
              headerBg: 'linear-gradient(135deg,#c62828,#b71c1c)', headerIcon: 'bi-person-dash-fill' }
        );
    }

    function autoAssignStudents() {
        showConfirm('Auto-Assign Students',
            'Automatically assign all <strong>unassigned enrolled students</strong> to available sections based on their grade level and section capacity.<br><br>Students who are already in a section will not be affected.',
            function() {
                fetch('/admin/sections/auto-assign', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({})
                })
                .then(r => { if (!r.ok) throw r; return r.json(); })
                .then(d => {
                    showCustomAlert('success', 'Auto-Assign Complete', d.message || 'Students assigned successfully.');
                    setTimeout(() => reloadWithSection(), 1500);
                })
                .catch(async err => {
                    let msg = 'Auto-assign failed.';
                    try { const e = await err.json(); msg = e.message || e.error || msg; } catch(x) {}
                    showCustomAlert('error', 'Error', msg);
                });
            },
            { btnText: 'Auto-Assign', btnClass: 'btn btn-primary', btnIcon: 'bi-magic',
              headerBg: 'linear-gradient(135deg,var(--blue),#1565c0)', headerIcon: 'bi-magic' }
        );
    }

    function openScheduleModal() {

        document.getElementById('scheduleModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Schedule';

        document.getElementById('sched-id').value = '';

        document.getElementById('sched-grade-level').value = '';
        document.getElementById('sched-section').innerHTML = '<option value="">— Select Grade First —</option>';
        document.getElementById('sched-subject').innerHTML = '<option value="">— Select Grade First —</option>';
        document.getElementById('sched-teacher').innerHTML = '<option value="">— None / TBA —</option>';

        setSchedDay('');

        document.getElementById('sched-term').value = '';

        document.getElementById('sched-start').value = '07:30';

        document.getElementById('sched-end').value = '08:30';

        document.getElementById('sched-room').value = '';

        document.getElementById('sched-active').value = '1';

        document.getElementById('sched-delete-btn').style.display = 'none';

        new bootstrap.Modal(document.getElementById('scheduleModal')).show();

    }

    // ── Copy Term Schedule ──

    function openCopyTermModal() {
        document.getElementById('copyterm-source').value = '1';
        document.getElementById('copyterm-target').value = '2';
        document.getElementById('copyterm-replace').checked = false;
        document.getElementById('copyterm-result').style.display = 'none';
        document.getElementById('copyterm-result').innerHTML = '';
        new bootstrap.Modal(document.getElementById('copyTermModal')).show();
    }

    function submitCopyTerm() {
        const sourceTerm = document.getElementById('copyterm-source').value;
        const targetTerm = document.getElementById('copyterm-target').value;
        const replace    = document.getElementById('copyterm-replace').checked;
        const resultEl   = document.getElementById('copyterm-result');
        const btn        = document.getElementById('copyterm-submit-btn');

        if (sourceTerm === targetTerm) {
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div style="background:#fff3e0;color:#e65100;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-exclamation-triangle me-1"></i>Source and target term must be different.</div>';
            return;
        }

        if (replace) {
            showConfirmSimple('This will delete the existing schedule for the target term before copying. Continue?',
                function () { doSubmitCopyTerm(sourceTerm, targetTerm, replace, resultEl, btn); },
                { title: 'Overwrite Target Term', confirmLabel: 'Continue', danger: true });
            return;
        }

        doSubmitCopyTerm(sourceTerm, targetTerm, replace, resultEl, btn);
    }

    function doSubmitCopyTerm(sourceTerm, targetTerm, replace, resultEl, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Copying…';
        resultEl.style.display = 'none';

        fetch('<?php echo e(route("admin.schedules.copy-term")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                source_term: sourceTerm,
                target_term: targetTerm,
                replace_target: replace,
            }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy Schedule';

            if (!ok || !data.success) {
                resultEl.style.display = 'block';
                resultEl.innerHTML = `<div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-x-circle me-1"></i>${data.message || 'Failed to copy schedule.'}</div>`;
                return;
            }

            let html = `<div style="background:#e8f5e9;color:#1b5e20;padding:10px 14px;border-radius:8px;font-size:12.5px;margin-bottom:${data.skipped.length ? '10px' : '0'};">
                <i class="bi bi-check-circle me-1"></i>Copied ${data.copied} of ${data.total} schedule block(s).
            </div>`;

            if (data.skipped.length) {
                html += `<div style="background:#fff3e0;color:#e65100;padding:10px 14px;border-radius:8px;font-size:12px;">
                    <div style="font-weight:700;margin-bottom:6px;"><i class="bi bi-exclamation-triangle me-1"></i>${data.skipped.length} skipped due to conflicts:</div>
                    <ul style="margin:0;padding-left:18px;">` +
                    data.skipped.map(s => `<li>${s.section} — ${s.subject} (${s.day} ${s.time}): ${s.reasons.join('; ')}</li>`).join('') +
                    `</ul></div>`;
            }

            resultEl.style.display = 'block';
            resultEl.innerHTML = html;

            // Refresh the visible grid if it's showing the target term
            if (document.getElementById('scheduleTermFilter').value === targetTerm) {
                loadScheduleGrid();
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy Schedule';
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;font-size:12.5px;"><i class="bi bi-x-circle me-1"></i>Network error. Please try again.</div>';
        });
    }

    // ── Schedule Template Generator Functions ──

    function openScheduleTemplateModal() {

        document.getElementById('template-section').value = '';

        document.getElementById('template-term').value = '';

        document.getElementById('template-preview').style.display = 'none';

        document.getElementById('template-preview-body').innerHTML = '';

        new bootstrap.Modal(document.getElementById('scheduleTemplateModal')).show();

        document.getElementById('template-section').addEventListener('change', loadSectionSubjects);

        document.getElementById('template-term').addEventListener('change', loadSectionSubjects);

    }

    function loadSectionSubjects() {

        const sectionSelect = document.getElementById('template-section');

        const termSelect = document.getElementById('template-term');

        const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];

        let gradeLevel = selectedOption ? selectedOption.dataset.grade : null;

        const term = termSelect.value;

        if (!gradeLevel || !term) {

            document.getElementById('template-preview').style.display = 'none';

            return;

        }

        // Normalize grade level to match template keys

        gradeLevel = gradeLevel.toLowerCase().replace(/[^a-z0-9]/g, '');

        // Handle variations like "grade1", "grade_1", "grade 1", "Grade 1"

        if (gradeLevel.includes('grade')) {

            gradeLevel = gradeLevel.replace(/[^0-9]/g, '');

            gradeLevel = 'grade' + gradeLevel;

        }

        const template = scheduleTemplates[gradeLevel];

        if (!template) {

            console.error('No template found for grade level:', gradeLevel);

            document.getElementById('template-preview').style.display = 'none';

            return;

        }

        const sectionId = sectionSelect.value;

        fetch(`/admin/sections/${sectionId}`, {

            headers: { 'Accept': 'application/json' }

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(data => {

            const sectionSubjects = data.subjects || [];

            renderTemplatePreview(template, sectionSubjects);

        })

        .catch(err => {

            console.error('Error loading section subjects:', err);

            renderTemplatePreview(template, []);

        });

    }

    function renderTemplatePreview(template, sectionSubjects) {

        const tbody = document.getElementById('template-preview-body');

        if (!template || template.length === 0) {

            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px;">No template available for this grade level.</td></tr>';

            document.getElementById('template-preview').style.display = 'block';

            return;

        }

        let html = '';

        template.forEach(item => {

            html += `

                <tr>

                    <td>${item.day}</td>

                    <td>${item.time}</td>

                    <td>${item.subject}</td>

                    <td><span style="color:var(--muted); font-size:11px;">Room TBD</span></td>

                </tr>

            `;

        });

        tbody.innerHTML = html;

        document.getElementById('template-preview').style.display = 'block';

    }

    function generateScheduleFromTemplate() {

        const sectionId = document.getElementById('template-section').value;

        const term = document.getElementById('template-term').value;

        const sectionSelect = document.getElementById('template-section');

        const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];

        let gradeLevel = selectedOption ? selectedOption.dataset.grade : null;

        if (!sectionId || !term || !gradeLevel) {

            showCustomAlert('warning', 'Missing Information', 'Please select a section and term.');

            return;

        }

        // Normalize grade level to match template keys

        gradeLevel = gradeLevel.toLowerCase().replace(/[^a-z0-9]/g, '');

        if (gradeLevel.includes('grade')) {

            gradeLevel = gradeLevel.replace(/[^0-9]/g, '');

            gradeLevel = 'grade' + gradeLevel;

        }

        const template = scheduleTemplates[gradeLevel];

        if (!template || template.length === 0) {

            showCustomAlert('error', 'Error', 'No template available for this grade level.');

            return;

        }

        fetch(`/admin/sections/${sectionId}`, {

            headers: { 'Accept': 'application/json' }

        })

        .then(r => { if (!r.ok) throw r; return r.json(); })

        .then(data => {

            const sectionSubjects = data.subjects || [];

            const subjectMap = {};

            sectionSubjects.forEach(sub => {

                subjectMap[sub.name] = sub.id;

            });

            let createdCount = 0;

            let failedCount = 0;

            const createPromises = template.map(item => {

                const subjectId = subjectMap[item.subject];

                if (!subjectId) {

                    failedCount++;

                    return Promise.resolve();

                }

                const timeParts = item.time.split('-');

                const body = {

                    section_id: sectionId,

                    subject_id: subjectId,

                    teacher_id: null,

                    day_of_week: item.day,

                    term: parseInt(term),

                    start_time: timeParts[0] || '07:30',

                    end_time: timeParts[1] || '08:30',

                    room: '',

                    is_active: true

                };

                return fetch('/admin/schedules', {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': csrfToken,

                        'Accept': 'application/json'

                    },

                    body: JSON.stringify(body)

                })

                .then(r => { if (!r.ok) throw r; return r.json(); })

                .then(() => { createdCount++; })

                .catch(() => { failedCount++; });

            });

            return Promise.all(createPromises).then(() => {

                bootstrap.Modal.getInstance(document.getElementById('scheduleTemplateModal')).hide();

                if (createdCount > 0) {

                    showCustomAlert('success', 'Success!', `Generated ${createdCount} schedules${failedCount > 0 ? ` (${failedCount} failed - subjects not assigned)` : ''}.`);

                    setTimeout(() => reloadWithSection(), 1000);

                } else {

                    showCustomAlert('error', 'Error', 'No schedules generated. Make sure subjects are assigned to this section.');

                }

            });

        })

        .catch(err => {

            console.error(err);

            showCustomAlert('error', 'Error', 'Failed to generate schedules. Please try again.');

        });

    }

    function editSchedule(id, sectionId, subjectId, teacherId, day, start, end, room, active, term) {

        document.getElementById('scheduleModalTitle').innerHTML = '<i class="bi bi-pencil-fill me-2"></i>Edit Schedule';
        document.getElementById('sched-id').value = id;

        setSchedDay(day || '');
        document.getElementById('sched-term').value = term || '';
        document.getElementById('sched-start').value = (start || '07:30').substring(0, 5);
        document.getElementById('sched-end').value = (end || '08:30').substring(0, 5);
        document.getElementById('sched-room').value = room || '';
        document.getElementById('sched-active').value = active ? '1' : '0';
        document.getElementById('sched-delete-btn').style.display = '';

        // Derive grade from sectionsList
        const sec = sectionsList.find(s => String(s.id) === String(sectionId));
        const grade = sec ? sec.grade_level : '';
        document.getElementById('sched-grade-level').value = grade;
        _loadScheduleModalForEdit(grade, sectionId, subjectId, teacherId);

        new bootstrap.Modal(document.getElementById('scheduleModal')).show();

    }

    function _timesOverlap(s1, e1, s2, e2) {
        return s1 < e2 && e1 > s2;
    }

    // Shared by saveSchedule() (modal form) and the drag-and-drop handler —
    // checks the in-memory schedule cache for teacher/room/section
    // double-booking against the given day/time, excluding excludeId (the
    // entry being edited/moved, so it doesn't conflict with itself).
    function _findScheduleConflicts(sectionId, teacherId, roomVal, dayOfWeek, startTime, endTime, excludeId) {
        const conflicts = [];
        if (!_scheduleCache || !_scheduleCache.length || !startTime || !endTime) return conflicts;

        _scheduleCache.forEach(s => {
            if (excludeId && String(s.id) === String(excludeId)) return;
            if (s.day_of_week !== dayOfWeek) return;
            const sStart = (s.start_time || '').substring(0, 5);
            const sEnd   = (s.end_time   || '').substring(0, 5);
            if (!sStart || !sEnd) return;
            if (!_timesOverlap(startTime, endTime, sStart, sEnd)) return;

            if (teacherId && s.teacher_id && String(s.teacher_id) === String(teacherId)) {
                const tName   = (s.teacher   && s.teacher.name)  ? s.teacher.name   : 'this teacher';
                const subName = (s.subject   && s.subject.name)  ? s.subject.name   : 'a subject';
                const secName = (s.section   && s.section.name)  ? s.section.name   : 'a section';
                conflicts.push(`Teacher conflict: ${tName} is already teaching ${subName} (${secName}) on ${dayOfWeek} ${sStart}–${sEnd}.`);
            }

            if (roomVal && s.room && s.room.trim() === roomVal.trim()) {
                const subName = (s.subject && s.subject.name) ? s.subject.name : 'a subject';
                const secName = (s.section && s.section.name) ? s.section.name : 'a section';
                conflicts.push(`Room conflict: ${roomVal} is already used for ${subName} (${secName}) on ${dayOfWeek} ${sStart}–${sEnd}.`);
            }

            if (String(s.section_id) === String(sectionId)) {
                const subName = (s.subject && s.subject.name) ? s.subject.name : 'a subject';
                const secName = (s.section && s.section.name) ? s.section.name : 'this section';
                conflicts.push(`Section conflict: ${secName} already has ${subName} scheduled on ${dayOfWeek} ${sStart}–${sEnd}.`);
            }
        });

        return conflicts;
    }

    // Drag-and-drop: move an existing schedule entry to a different
    // day/time slot on the grid. Builds the update request directly from
    // the entry's own cached data (section, subject, teacher, room, status)
    // rather than the edit modal's fields, since the modal's room dropdown
    // is populated dynamically per grade/section and isn't guaranteed to
    // already contain the right options when the modal itself isn't open.
    function _handleScheduleDrop(entry, newDay, newStart, newEnd, grade, sectionId, term) {
        const sameSlot = entry.day_of_week === newDay
            && (entry.start_time || '').substring(0, 5) === newStart
            && (entry.end_time || '').substring(0, 5) === newEnd;
        if (sameSlot) return;

        // Scoped to the same section only — when viewing "All Sections",
        // _scheduleCache holds every section's entries, and a different
        // section already having a class at this day/time isn't actually
        // a conflict for *this* one (that's what _findScheduleConflicts'
        // room/teacher checks are for; section-double-booking there also
        // already checks same-section only).
        const occupied = _scheduleCache.find(s => {
            if (String(s.id) === String(entry.id)) return false;
            if (String(s.section_id) !== String(entry.section_id)) return false;
            const sStart = (s.start_time || '').substring(0, 5);
            const sEnd   = (s.end_time   || '').substring(0, 5);
            return s.day_of_week === newDay && sStart === newStart && sEnd === newEnd;
        });
        if (occupied) {
            showCustomAlert('warning', 'Slot Occupied', 'That time slot already has a class scheduled. Drag it onto an empty slot, or edit/delete the existing one first.');
            return;
        }

        const conflicts = _findScheduleConflicts(
            entry.section_id, entry.teacher_id, entry.room || '', newDay, newStart, newEnd, entry.id
        );

        const doMove = () => _submitScheduleDrop(entry, newDay, newStart, newEnd, conflicts);

        if (conflicts.length > 0) {
            document.getElementById('scheduleConflictConfirmMessage').innerHTML =
                conflicts.map(c => c.replace(/</g, '&lt;')).join('<br>');
            scheduleConflictConfirmCallback = doMove;
            new bootstrap.Modal(document.getElementById('scheduleConflictConfirmModal')).show();
            return;
        }

        doMove();
    }

    function _submitScheduleDrop(entry, newDay, newStart, newEnd, preSaveConflicts) {
        const body = {
            section_id: entry.section_id,
            subject_id: entry.subject_id,
            teacher_id: entry.teacher_id || null,
            day_of_week: newDay,
            term: entry.term,
            start_time: newStart,
            end_time: newEnd,
            room: entry.room || '',
            is_active: !!entry.is_active,
        };

        fetch(`/admin/schedules/${entry.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        })
        .then(async r => {
            const data = await r.json();
            if (!r.ok) {
                const msg = data.message || Object.values(data.errors || {}).flat().join('\n') || 'Failed to move schedule.';
                showCustomAlert('error', 'Error', msg);
                loadScheduleGrid();
                return;
            }
            const serverReasons = data.conflict_reasons || [];
            const allReasons = serverReasons.length ? serverReasons : preSaveConflicts;
            if (data.has_conflict || allReasons.length) {
                showCustomAlert('warning', 'Moved with a Schedule Conflict', 'Schedule moved, but it overlaps with another entry:\n' + allReasons.join('\n'));
            } else {
                showCustomAlert('success', 'Moved!', 'Schedule moved to the new slot.');
            }
            loadScheduleGrid();
        })
        .catch(err => {
            console.error('_submitScheduleDrop() failed:', err);
            showCustomAlert('error', 'Error', 'Failed to move schedule: ' + (err && err.message ? err.message : 'network or server error.'));
            loadScheduleGrid();
        });
    }

    function saveSchedule() {
      try {

        const id = document.getElementById('sched-id').value;

        const sectionId = document.getElementById('sched-section').value;
        const subjectId = document.getElementById('sched-subject').value;
        const dayOfWeek = document.getElementById('sched-day').value;
        const termVal   = document.getElementById('sched-term').value;
        const startTime = document.getElementById('sched-start').value;
        const endTime   = document.getElementById('sched-end').value;
        const teacherId = document.getElementById('sched-teacher').value || null;
        const roomVal   = document.getElementById('sched-room').value;

        if (!sectionId || !subjectId || !dayOfWeek || !termVal) {
            showCustomAlert('error', 'Validation Error', 'Section, Subject, Day, and Term are required.');
            return;
        }

        // Client-side conflict detection against loaded schedule cache.
        // Conflicts no longer silently block saving — if any are found, ask
        // for confirmation first ("Save Anyway"); the actual save request
        // only fires once the user confirms (or immediately, if clean).
        const conflicts = _findScheduleConflicts(sectionId, teacherId, roomVal, dayOfWeek, startTime, endTime, id);

        if (conflicts.length > 0) {
            document.getElementById('scheduleConflictConfirmMessage').innerHTML =
                conflicts.map(c => c.replace(/</g, '&lt;')).join('<br>');
            scheduleConflictConfirmCallback = function() { _submitScheduleSave(conflicts); };
            new bootstrap.Modal(document.getElementById('scheduleConflictConfirmModal')).show();
            return;
        }

        _submitScheduleSave([]);
      } catch (err) {
          // Safety net: a silent failure here used to look like "nothing
          // happened" with no feedback at all. Surface whatever actually
          // broke instead of swallowing it.
          console.error('saveSchedule() failed:', err);
          showCustomAlert('error', 'Unexpected Error', 'Something went wrong before the save request could be sent: ' + (err && err.message ? err.message : err));
      }
    }

    // Performs the actual create/update request. `preSaveConflicts` is only
    // used as a fallback warning message if the server doesn't echo back its
    // own conflict_reasons for some reason.
    function _submitScheduleSave(preSaveConflicts) {
      try {

        const id = document.getElementById('sched-id').value;

        const url = id ? `/admin/schedules/${id}` : '/admin/schedules';

        const method = id ? 'PUT' : 'POST';

        const body = {

            section_id: document.getElementById('sched-section').value,

            subject_id: document.getElementById('sched-subject').value,

            teacher_id: document.getElementById('sched-teacher').value || null,

            day_of_week: document.getElementById('sched-day').value,

            term: document.getElementById('sched-term').value,

            start_time: document.getElementById('sched-start').value,

            end_time: document.getElementById('sched-end').value,

            room: document.getElementById('sched-room').value,

            is_active: document.getElementById('sched-active').value === '1'

        };

        fetch(url, {

            method: method,

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },

            body: JSON.stringify(body)

        })

        .then(async r => {
            const data = await r.json();
            if (!r.ok) {
                const msg = data.message || Object.values(data.errors || {}).flat().join('\n') || 'Failed to save schedule.';
                showCustomAlert('error', 'Error', msg);
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();

            // Conflicts no longer block saving — the schedule is saved either way.
            // Show a warning toast (instead of the plain success one) when the
            // server flagged it, or when our own pre-check found one.
            const serverReasons = data.conflict_reasons || [];
            const allReasons = serverReasons.length ? serverReasons : preSaveConflicts;
            if (data.has_conflict || allReasons.length) {
                showCustomAlert('warning', 'Saved with a Schedule Conflict',
                    (id ? 'Schedule updated' : 'Schedule created') + ', but it overlaps with another entry:\n' + allReasons.join('\n'));
            } else {
                showCustomAlert('success', 'Saved!', id ? 'Schedule updated.' : 'Schedule created.');
            }
            loadScheduleGrid();
        })

        .catch(err => {
            console.error('_submitScheduleSave() request failed:', err);
            showCustomAlert('error', 'Error', 'Failed to save schedule: ' + (err && err.message ? err.message : 'network or server error.'));
        });

      } catch (err) {
          console.error('_submitScheduleSave() failed before request:', err);
          showCustomAlert('error', 'Unexpected Error', 'Something went wrong while preparing the save request: ' + (err && err.message ? err.message : err));
      }

    }

    function deleteSchedule() {

        const id = document.getElementById('sched-id').value;
        if (!id) return;

        showDeleteConfirm('Delete this schedule entry?', function() {

            fetch(`/admin/schedules/${id}`, {

                method: 'DELETE',

                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }

            })

            .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })

            .then(d => {

                bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
                showCustomAlert('success', 'Deleted!', 'Schedule entry deleted.');

                loadScheduleGrid(); // Reload grid instead of full page

            })

            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete schedule.'));

        });

    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // MASS PROMOTION FUNCTIONS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    const gradePromotionMap = {
        'nursery':      'kindergarten',
        'kindergarten': 'grade1',
        'grade1':       'grade2',
        'grade2':       'grade3',
        'grade3':       'grade4',
        'grade4':       'grade5',
        'grade5':       'grade6',
        'grade6':       'graduated'
    };

    function gradeLabel(g) {
        if (!g) return '—';
        const map = {
            'nursery': 'Nursery',
            'kindergarten': 'Kindergarten',
            'grade1': 'Grade 1',
            'grade2': 'Grade 2',
            'grade3': 'Grade 3',
            'grade4': 'Grade 4',
            'grade5': 'Grade 5',
            'grade6': 'Grade 6',
            'grade7': 'Grade 7',
            'grade8': 'Grade 8',
            'grade9': 'Grade 9',
            'grade10': 'Grade 10',
            'grade11': 'Grade 11',
            'grade12': 'Grade 12',
            'graduated': 'Graduated'
        };
        return map[g] || g;
    }

    function showAdminToast(msg, type) {
        showToast(msg, type);
    }


    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // GUIDANCE RECORD CRUD FUNCTIONS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function openGuidanceModal() {
        document.getElementById('guidance-id').value = '';
        document.getElementById('guidanceForm').reset();
        document.getElementById('guidance-student').value = '';
        document.getElementById('guidance-student-search').value = '';
        document.getElementById('guidance-student-results').style.display = 'none';
        document.getElementById('guidanceModalTitle').innerHTML = '<i class="bi bi-journal-medical me-2"></i>Add Guidance Record';
        document.getElementById('guidance-delete-btn').style.display = 'none';
        document.getElementById('guidance-date').value = new Date().toISOString().split('T')[0];
        new bootstrap.Modal(document.getElementById('guidanceModal')).show();
    }

    // Type-ahead student picker for the Guidance modal — queries the full
    // students table (not the Student Management tab's paginated 15-at-a-time
    // list) so it works correctly past 250+ students.
    let _guidanceStudentSearchTimer = null;
    function guidanceStudentSearch(query) {
        document.getElementById('guidance-student').value = '';
        clearTimeout(_guidanceStudentSearchTimer);
        const resultsEl = document.getElementById('guidance-student-results');
        if (query.trim().length < 2) {
            resultsEl.style.display = 'none';
            resultsEl.innerHTML = '';
            return;
        }
        _guidanceStudentSearchTimer = setTimeout(() => {
            fetch('/admin/students/search?q=' + encodeURIComponent(query.trim()))
                .then(r => r.json())
                .then(students => {
                    if (!students.length) {
                        resultsEl.innerHTML = '<div style="padding:10px;font-size:13px;color:#999;">No students found.</div>';
                        resultsEl.style.display = 'block';
                        return;
                    }
                    resultsEl.innerHTML = students.map(s =>
                        `<div class="guidance-student-option" data-id="${s.id}" data-name="${s.name.replace(/"/g, '&quot;')}" style="padding:9px 12px;font-size:13px;cursor:pointer;border-bottom:1px solid #f0f2f5;">${s.name} <span style="color:#999;font-size:11.5px;">(${s.email})</span></div>`
                    ).join('');
                    resultsEl.style.display = 'block';
                })
                .catch(() => { resultsEl.style.display = 'none'; });
        }, 300);
    }

    document.addEventListener('click', function(e) {
        const option = e.target.closest('.guidance-student-option');
        if (option) {
            document.getElementById('guidance-student').value = option.dataset.id;
            document.getElementById('guidance-student-search').value = option.dataset.name;
            document.getElementById('guidance-student-results').style.display = 'none';
            return;
        }
        if (!e.target.closest('#guidance-student-search')) {
            const resultsEl = document.getElementById('guidance-student-results');
            if (resultsEl) resultsEl.style.display = 'none';
        }
    });

    function editGuidanceRecord(id) {
        fetch(`/admin/guidance/${id}`)
            .then(r => r.json())
            .then(d => {
                const record = d.record;
                document.getElementById('guidance-id').value = record.id;
                document.getElementById('guidance-student').value = record.student_id;
                document.getElementById('guidance-student-search').value = record.student ? record.student.name : '';
                document.getElementById('guidance-student-results').style.display = 'none';
                document.getElementById('guidance-counselor').value = record.counselor_id;
                document.getElementById('guidance-date').value = record.date;
                document.getElementById('guidance-concern-type').value = record.concern_type;
                document.getElementById('guidance-description').value = record.concern_description;
                document.getElementById('guidance-action').value = record.action_taken || '';
                document.getElementById('guidance-recommendations').value = record.recommendations || '';
                document.getElementById('guidance-follow-up').value = record.follow_up_date || '';
                document.getElementById('guidance-status').value = record.status;
                document.getElementById('guidance-notes').value = record.notes || '';
                document.getElementById('guidanceModalTitle').innerHTML = '<i class="bi bi-journal-medical me-2"></i>Edit Guidance Record';
                document.getElementById('guidance-delete-btn').style.display = 'inline-block';
                new bootstrap.Modal(document.getElementById('guidanceModal')).show();
            })
            .catch(() => showCustomAlert('error', 'Error', 'Failed to load record'));
    }

    function viewGuidanceRecord(id) {
        editGuidanceRecord(id); // Same as edit but user can just view
    }

    function saveGuidanceRecord() {
        const id = document.getElementById('guidance-id').value;
        const data = {
            student_id: document.getElementById('guidance-student').value,
            counselor_id: document.getElementById('guidance-counselor').value,
            date: document.getElementById('guidance-date').value,
            concern_type: document.getElementById('guidance-concern-type').value,
            concern_description: document.getElementById('guidance-description').value,
            action_taken: document.getElementById('guidance-action').value,
            recommendations: document.getElementById('guidance-recommendations').value,
            follow_up_date: document.getElementById('guidance-follow-up').value,
            status: document.getElementById('guidance-status').value,
            notes: document.getElementById('guidance-notes').value,
        };

        if (!data.student_id || !data.counselor_id || !data.date || !data.concern_type || !data.concern_description) {
            showCustomAlert('error', 'Error', 'Please fill in all required fields');
            return;
        }

        const url = id ? `/admin/guidance/${id}` : '/admin/guidance';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                bootstrap.Modal.getInstance(document.getElementById('guidanceModal')).hide();
                showCustomAlert('success', 'Success', d.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showCustomAlert('error', 'Error', d.message);
            }
        })
        .catch(() => showCustomAlert('error', 'Error', 'Failed to save record'));
    }

    function deleteGuidanceRecord(id) {
        showDeleteConfirm('Are you sure you want to delete this guidance record?', () => {
            fetch(`/admin/guidance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    showCustomAlert('success', 'Deleted!', d.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showCustomAlert('error', 'Error', d.message);
                }
            })
            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete record'));
        });
    }

    function deleteGuidanceRecordFromModal() {
        const id = document.getElementById('guidance-id').value;
        if (!id) return;
        deleteGuidanceRecord(id);
    }

    // ── Summer Class Management ──

    // Escapes a value for safe embedding inside a single-quoted JS string
    // literal built into an inline onclick="" attribute.
    function escJs(s) {
        return String(s == null ? '' : s).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }

    // Show only subjects belonging to the selected grade level. Options for
    // every other grade are marked `hidden` (set server-side), rather than
    // removed, so no extra request is needed to re-populate them.
    function filterSummerSubjectsByGrade() {
        const grade = document.getElementById('sc-grade').value;
        const subjectSelect = document.getElementById('sc-subject');
        const placeholder = subjectSelect.querySelector('option[value=""]');
        let anyVisible = false;

        subjectSelect.querySelectorAll('option[data-grade]').forEach(function(opt) {
            const match = opt.dataset.grade === grade;
            opt.hidden = !match;
            if (match) anyVisible = true;
        });

        const selectedOpt = subjectSelect.options[subjectSelect.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.grade && selectedOpt.dataset.grade !== grade) {
            subjectSelect.value = '';
        }

        placeholder.textContent = !grade
            ? '— Select Grade First —'
            : (anyVisible ? '— Select Subject —' : '— No subjects for this grade —');
    }

    function openCreateSummerModal() {
        document.getElementById('sc-id').value = '';
        document.getElementById('summerModalTitle').innerHTML = '<i class="bi bi-sun-fill me-2"></i>Create Summer Class';
        document.getElementById('sc-grade').value = '';
        document.getElementById('sc-subject').value = '';
        document.getElementById('sc-teacher').value = '';
        document.getElementById('sc-start').value = '';
        document.getElementById('sc-end').value = '';
        document.getElementById('sc-room').value = '';
        document.getElementById('sc-slots').value = 40;
        document.getElementById('sc-sched').value = '';
        document.getElementById('sc-remarks').value = '';
        filterSummerSubjectsByGrade();
        new bootstrap.Modal(document.getElementById('summerClassModal')).show();
    }

    function editSummerClass(id) {
        fetch('/admin/summer-classes/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                const sc = d.data;
                document.getElementById('sc-id').value = sc.id;
                document.getElementById('summerModalTitle').innerHTML = '<i class="bi bi-sun-fill me-2"></i>Edit Summer Class';
                document.getElementById('sc-sy').value = sc.school_year;
                document.getElementById('sc-grade').value = sc.grade_level;
                filterSummerSubjectsByGrade();
                document.getElementById('sc-subject').value = sc.subject_id;
                document.getElementById('sc-teacher').value = sc.teacher_id || '';
                document.getElementById('sc-start').value = (sc.start_date || '').slice(0,10);
                document.getElementById('sc-end').value = (sc.end_date || '').slice(0,10);
                document.getElementById('sc-room').value = sc.room || '';
                document.getElementById('sc-slots').value = sc.max_slots;
                document.getElementById('sc-sched').value = sc.schedule_description || '';
                document.getElementById('sc-remarks').value = sc.remarks || '';
                new bootstrap.Modal(document.getElementById('summerClassModal')).show();
            })
            .catch(() => showCustomAlert('error', 'Error', 'Failed to load summer class'));
    }

    function saveSummerClass() {
        const id = document.getElementById('sc-id').value;
        const data = {
            school_year: document.getElementById('sc-sy').value,
            grade_level: document.getElementById('sc-grade').value,
            subject_id: document.getElementById('sc-subject').value,
            teacher_id: document.getElementById('sc-teacher').value || null,
            start_date: document.getElementById('sc-start').value,
            end_date: document.getElementById('sc-end').value,
            room: document.getElementById('sc-room').value,
            max_slots: document.getElementById('sc-slots').value,
            schedule_description: document.getElementById('sc-sched').value,
            remarks: document.getElementById('sc-remarks').value,
        };

        if (!data.grade_level || !data.subject_id || !data.start_date || !data.end_date) {
            showCustomAlert('error', 'Error', 'Please fill in all required fields (Grade Level, Subject, Start Date, End Date).');
            return;
        }

        const url    = id ? `/admin/summer-classes/${id}` : '/admin/summer-classes';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json().then(body => ({ ok: r.ok, body })))
        .then(({ ok, body }) => {
            if (ok && body.success) {
                bootstrap.Modal.getInstance(document.getElementById('summerClassModal')).hide();
                showCustomAlert('success', 'Success', body.message);
                loadSummerClasses();
            } else {
                const firstError = body.errors ? Object.values(body.errors)[0][0] : (body.message || 'Failed to save summer class.');
                showCustomAlert('error', 'Error', firstError);
            }
        })
        .catch(() => showCustomAlert('error', 'Error', 'Network error. Please try again.'));
    }

    function deleteSummerClass(id) {
        showDeleteConfirm('Delete this summer class? Enrolled students will also be removed from it.', function() {
            fetch('/admin/summer-classes/' + id, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            })
            .then(r => r.json())
            .then(d => {
                showCustomAlert(d.success ? 'success' : 'error', d.success ? 'Deleted' : 'Error', d.message);
                loadSummerClasses();
            })
            .catch(() => showCustomAlert('error', 'Error', 'Failed to delete summer class.'));
        });
    }

    // ── Summer Class — Manage Students (enroll / grade / remove) ──
    const SS_STATUS_STYLE = {
        enrolled: { bg: '#e3f2fd', color: '#1565c0', label: 'Enrolled' },
        passed:   { bg: '#e8f5e9', color: '#2e7d32', label: 'Passed' },
        failed:   { bg: '#ffebee', color: '#c62828', label: 'Failed' },
        dropped:  { bg: '#f5f5f5', color: '#666',    label: 'Dropped' },
    };

    function openSummerStudentsModal(classId, subjectId, schoolYear, subjectLabel, gradeLabel) {
        document.getElementById('ss-class-id').value = classId;
        document.getElementById('ss-subject-id').value = subjectId;
        document.getElementById('ss-school-year').value = schoolYear;
        document.getElementById('ss-subtitle').textContent = subjectLabel + ' — ' + gradeLabel + ' (' + schoolYear + ')';
        new bootstrap.Modal(document.getElementById('summerStudentsModal')).show();
        loadSummerClassStudents();
    }

    function loadSummerClassStudents() {
        const classId = document.getElementById('ss-class-id').value;
        const loadEl  = document.getElementById('ss-enrolled-loading');
        const listEl  = document.getElementById('ss-enrolled-list');
        loadEl.style.display = 'block';
        listEl.style.display = 'none';

        fetch('/admin/summer-classes/' + classId, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                const enrollments = d.data.enrollments || [];
                loadEl.style.display = 'none';
                listEl.style.display = 'block';
                listEl.innerHTML = buildEnrolledStudentsTable(enrollments);
                loadEligibleForSummerEnroll(enrollments.map(e => e.student_id));
            })
            .catch(() => { loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load students.</span>'; });
    }

    function buildEnrolledStudentsTable(enrollments) {
        if (!enrollments.length) {
            return '<div style="text-align:center;padding:20px;color:#999;font-size:12px;border:1px dashed #ddd;border-radius:8px;">No students enrolled yet — add one above.</div>';
        }
        let html = '<div style="overflow-x:auto;"><table class="dash-table"><thead><tr>' +
            '<th>Student</th><th>Original Grade</th><th>Summer Grade</th><th>Status</th><th>Actions</th>' +
            '</tr></thead><tbody>';
        enrollments.forEach(function(e) {
            const st = SS_STATUS_STYLE[e.status] || SS_STATUS_STYLE.enrolled;
            html += '<tr>' +
                '<td><div style="font-weight:600;">' + (e.student ? e.student.name : '—') + '</div>' +
                    '<div style="font-size:11px;color:var(--muted);">' + (e.student ? e.student.email : '') + '</div></td>' +
                '<td>' + (e.original_grade ?? '—') + '</td>' +
                '<td><input type="number" min="0" max="100" step="0.01" value="' + (e.summer_grade ?? '') + '" id="ss-grade-' + e.student_id + '" style="width:80px;padding:4px 8px;border:1px solid #ddd;border-radius:6px;font-size:12px;"></td>' +
                '<td><span style="display:inline-flex;align-items:center;padding:3px 9px;border-radius:12px;font-size:10px;font-weight:700;background:' + st.bg + ';color:' + st.color + ';">' + st.label + '</span></td>' +
                '<td style="white-space:nowrap;">' +
                    '<button class="action-btn edit" title="Save Grade" onclick="saveSummerStudentGrade(' + e.student_id + ')"><i class="bi bi-check-lg"></i></button> ' +
                    '<button class="action-btn delete" title="Remove" onclick="removeSummerStudent(' + e.student_id + ')"><i class="bi bi-trash-fill"></i></button>' +
                '</td>' +
            '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    function loadEligibleForSummerEnroll(alreadyEnrolledIds) {
        const subjectId  = document.getElementById('ss-subject-id').value;
        const schoolYear = document.getElementById('ss-school-year').value;
        const loadEl = document.getElementById('ss-eligible-loading');
        const listEl = document.getElementById('ss-eligible-list');
        loadEl.style.display = 'block';
        listEl.style.display = 'none';

        fetch('/admin/summer-classes/eligible-students?subject_id=' + subjectId + '&school_year=' + encodeURIComponent(schoolYear), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                loadEl.style.display = 'none';
                listEl.style.display = 'block';
                const eligible = (d.data || []).filter(function(s) { return alreadyEnrolledIds.indexOf(s.id) === -1; });
                if (!eligible.length) {
                    listEl.innerHTML = '<div style="font-size:12px;color:#999;">No other failing students found for this subject, or all are already enrolled.</div>';
                    return;
                }
                listEl.innerHTML = eligible.map(function(s) {
                    return '<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:6px;">' +
                        '<div><span style="font-weight:600;font-size:13px;">' + s.name + '</span>' +
                        '<span style="font-size:11px;color:#c62828;margin-left:8px;">Grade: ' + s.grade + '</span></div>' +
                        '<button class="btn-dash btn-primary" style="padding:5px 12px;font-size:11px;" onclick="enrollSummerStudent(' + s.id + ', ' + s.grade + ')"><i class="bi bi-plus-lg"></i> Add</button>' +
                    '</div>';
                }).join('');
            })
            .catch(() => { loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load eligible students.</span>'; });
    }

    function enrollSummerStudent(studentId, originalGrade) {
        const classId = document.getElementById('ss-class-id').value;
        fetch('/admin/summer-classes/' + classId + '/enroll', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ student_id: studentId, original_grade: originalGrade }),
        })
        .then(r => r.json().then(body => ({ ok: r.ok, body })))
        .then(({ ok, body }) => {
            if (ok && body.success) {
                showCustomAlert('success', 'Enrolled', body.message);
                loadSummerClassStudents();
                loadSummerClasses();
            } else {
                showCustomAlert('error', 'Error', body.message || 'Failed to enroll student.');
            }
        })
        .catch(() => showCustomAlert('error', 'Error', 'Network error. Please try again.'));
    }

    function saveSummerStudentGrade(studentId) {
        const classId = document.getElementById('ss-class-id').value;
        const gradeInput = document.getElementById('ss-grade-' + studentId);
        const grade = gradeInput.value;
        if (grade === '') { showCustomAlert('error', 'Error', 'Enter a grade first.'); return; }

        fetch('/admin/summer-classes/' + classId + '/grade/' + studentId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ summer_grade: grade }),
        })
        .then(r => r.json().then(body => ({ ok: r.ok, body })))
        .then(({ ok, body }) => {
            if (ok && body.success) {
                showCustomAlert('success', 'Saved', body.message);
                loadSummerClassStudents();
            } else {
                showCustomAlert('error', 'Error', body.message || 'Failed to save grade.');
            }
        })
        .catch(() => showCustomAlert('error', 'Error', 'Network error. Please try again.'));
    }

    function removeSummerStudent(studentId) {
        showDeleteConfirm('Remove this student from the summer class?', function() {
            const classId = document.getElementById('ss-class-id').value;
            fetch('/admin/summer-classes/' + classId + '/remove/' + studentId, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            })
            .then(r => r.json())
            .then(d => {
                showCustomAlert(d.success ? 'success' : 'error', d.success ? 'Removed' : 'Error', d.message);
                loadSummerClassStudents();
                loadSummerClasses();
            })
            .catch(() => showCustomAlert('error', 'Error', 'Failed to remove student.'));
        });
    }

    function loadSummerClasses(page) {
        const tbody = document.getElementById('summer-classes-body');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading...</td></tr>';

        const sy     = document.getElementById('summer-sy-filter').value;
        const status = document.getElementById('summer-status-filter').value;
        const params = new URLSearchParams();
        if (sy) params.set('school_year', sy);
        if (status) params.set('status', status);
        if (page) params.set('page', page);

        fetch('/admin/summer-classes?' + params.toString(), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                const rows = d.data || [];
                if (!rows.length) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text);padding:40px;">' +
                        '<i class="bi bi-sun" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>' +
                        'No summer classes found. Click "Create Summer Class" to get started.</td></tr>';
                    document.getElementById('summer-pagination').innerHTML = '';
                    return;
                }

                const statusStyle = {
                    upcoming:  { badge: 'pending',  label: 'Upcoming' },
                    ongoing:   { badge: 'active',   label: 'Ongoing' },
                    completed: { badge: 'enrolled', label: 'Completed' },
                    cancelled: { badge: 'declined', label: 'Cancelled' },
                };
                const summerGradeLabels = { nursery:'Nursery', kindergarten:'Kindergarten', grade1:'Grade 1', grade2:'Grade 2', grade3:'Grade 3', grade4:'Grade 4', grade5:'Grade 5', grade6:'Grade 6' };

                tbody.innerHTML = rows.map(sc => {
                    const st = statusStyle[sc.status] || statusStyle.upcoming;
                    return `<tr>
                        <td>${sc.subject ? (sc.subject.code ? sc.subject.code + ' — ' : '') + sc.subject.name : '—'}</td>
                        <td><span class="grade-chip">${summerGradeLabels[sc.grade_level] || sc.grade_level}</span></td>
                        <td>${sc.teacher ? sc.teacher.name : '<span style="color:var(--muted);">Unassigned</span>'}</td>
                        <td style="font-size:12px;">${sc.schedule_description || '—'}<br><span style="color:var(--muted);">${sc.room || ''}</span></td>
                        <td style="font-size:12px;white-space:nowrap;">${(sc.start_date || '').slice(0,10)} to ${(sc.end_date || '').slice(0,10)}</td>
                        <td>${sc.enrollments_count ?? 0} / ${sc.max_slots}</td>
                        <td><span class="status-badge ${st.badge}">${st.label}</span></td>
                        <td style="white-space:nowrap;">
                            <button class="action-btn view" title="Manage Students" onclick="openSummerStudentsModal(${sc.id}, ${sc.subject_id}, '${escJs(sc.school_year)}', '${escJs(sc.subject ? sc.subject.name : 'Subject')}', '${escJs(summerGradeLabels[sc.grade_level] || sc.grade_level)}')"><i class="bi bi-people-fill"></i></button>
                            <button class="action-btn edit" title="Edit" onclick="editSummerClass(${sc.id})"><i class="bi bi-pencil-fill"></i></button>
                            <button class="action-btn delete" title="Delete" onclick="deleteSummerClass(${sc.id})"><i class="bi bi-trash-fill"></i></button>
                        </td>
                    </tr>`;
                }).join('');

                const p = d.pagination;
                if (p && p.hasPages) {
                    let pagHtml = '<nav><ul class="pagination justify-content-center mb-0">';
                    pagHtml += `<li class="page-item ${p.onFirstPage ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadSummerClasses(${p.currentPage - 1});return false;">Prev</a></li>`;
                    for (let i = 1; i <= p.lastPage; i++) {
                        pagHtml += `<li class="page-item ${i === p.currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="loadSummerClasses(${i});return false;">${i}</a></li>`;
                    }
                    pagHtml += `<li class="page-item ${!p.hasMorePages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadSummerClasses(${p.currentPage + 1});return false;">Next</a></li>`;
                    pagHtml += '</ul></nav>';
                    document.getElementById('summer-pagination').innerHTML = pagHtml;
                } else {
                    document.getElementById('summer-pagination').innerHTML = '';
                }
            })
            .catch(() => {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--red);">Error loading summer classes.</td></tr>';
            });
    }

    let _subjectFilterTimer = null;
    function filterSubjectTable() {
        clearTimeout(_subjectFilterTimer);
        _subjectFilterTimer = setTimeout(_doSubjectFilter, 250);
    }

    function _doSubjectFilter() {
        const search = (document.getElementById('subjectSearchInput').value || '').toLowerCase();
        const grade  = document.getElementById('subjectGradeFilter').value;
        const status = document.getElementById('subjectStatusFilter').value;

        // If a grade is selected, fetch all subjects for that grade via AJAX
        // so pagination doesn't hide results on other pages
        if (grade) {
            const tbody = document.querySelector('#subjectTable tbody');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading...</td></tr>';

            let url = '/admin/subjects?grade_level=' + encodeURIComponent(grade);
            if (status === 'active')   url += '&is_active=1';
            if (status === 'inactive') url += '&is_active=0';

            fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const subjects = (data.subjects || []).filter(s =>
                    !search || (s.name + ' ' + s.code).toLowerCase().includes(search)
                );
                document.getElementById('subjectVisibleCount').textContent = subjects.length;
                if (!subjects.length) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">No subjects found for this grade.</td></tr>';
                    return;
                }
                const glMap = {nursery:'Nursery',kindergarten:'Kindergarten',grade1:'Grade 1',grade2:'Grade 2',grade3:'Grade 3',grade4:'Grade 4',grade5:'Grade 5',grade6:'Grade 6'};
                tbody.innerHTML = subjects.map(s => `
                    <tr data-grade="${s.grade_level}" data-status="${s.is_active ? 'active' : 'inactive'}" data-search="${(s.code + ' ' + s.name).toLowerCase()}">
                        <td><span class="code-chip">${s.code}</span></td>
                        <td><div style="font-weight:600;">${s.name}</div>${s.description ? '<div style="font-size:11px;color:var(--muted);margin-top:2px;">' + s.description.substring(0,50) + '</div>' : ''}</td>
                        <td><span class="grade-chip">${glMap[s.grade_level] || s.grade_level}</span></td>
                        <td>${s.is_active ? '<span class="status-badge active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>' : '<span class="status-badge inactive"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>'}</td>
                        <td style="text-align:center;">
                            <button class="action-btn edit js-subject-edit" title="Edit" data-id="${s.id}" data-name="${s.name}" data-code="${s.code}" data-desc="${s.description || ''}" data-grade="${s.grade_level}" data-active="${s.is_active ? '1' : '0'}"><i class="bi bi-pencil-fill"></i></button>
                            <button class="action-btn delete js-subject-delete" title="Delete" data-id="${s.id}"><i class="bi bi-trash-fill"></i></button>
                        </td>
                    </tr>`).join('');
                // Re-wire edit/delete buttons rendered by AJAX
                document.querySelectorAll('#subjectTable .js-subject-edit').forEach(btn => btn.addEventListener('click', function() {
                    editSubject(this.dataset.id, this.dataset.name, this.dataset.code, this.dataset.desc, this.dataset.grade, this.dataset.active);
                }));
                document.querySelectorAll('#subjectTable .js-subject-delete').forEach(btn => btn.addEventListener('click', function() {
                    deleteSubject(this.dataset.id);
                }));
            })
            .catch(() => {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--red);">Failed to load subjects.</td></tr>';
            });
            return;
        }

        // No grade selected — client-side filter on current paginated rows
        const rows = document.querySelectorAll('#subjectTable tbody tr[data-search]');
        let visible = 0;
        rows.forEach(row => {
            const matchSearch = !search || row.dataset.search.includes(search);
            const matchStatus = !status || row.dataset.status === status;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
            if (matchSearch && matchStatus) visible++;
        });
        document.getElementById('subjectVisibleCount').textContent = visible;
    }

    let _enrollmentSearchTimer = null;
    function debouncedEnrollmentSearch() {
        clearTimeout(_enrollmentSearchTimer);
        _enrollmentSearchTimer = setTimeout(filterEnrollments, 600);
    }

    function filterEnrollments() {
        // Was a full-page navigation (window.location.href = ...) — now an
        // AJAX re-fetch of just this tab, same pattern as every other
        // converted section. See docs/system-improvement-plan.md item #2.
        const statusValue = document.getElementById('statusFilter').value;
        const gradeValue = document.getElementById('gradeFilter').value;
        const sortValue = document.getElementById('enrollmentSortFilter').value;
        const searchInput = document.getElementById('enrollmentSearchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const params = new URLSearchParams();
        params.set('status', statusValue);
        params.set('grade', gradeValue);
        params.set('sort', sortValue);
        if (searchValue) params.set('enrollment_search', searchValue);
        loadEnrollmentSection('<?php echo e(route("admin.section.enrollment")); ?>?' + params.toString());
    }

    function filterEnrollmentsGrade() {
        filterEnrollments();
    }

    function clearEnrollmentSearch() {
        document.getElementById('enrollmentSearchInput').value = '';
        filterEnrollments();
    }

    function filterSectionTable() {

        const search = (document.getElementById('sectionSearchInput').value || '').toLowerCase();

        const grade = document.getElementById('sectionGradeFilterQuick').value;

        const status = document.getElementById('sectionStatusFilter').value;

        const rows = document.querySelectorAll('#sectionTable tbody tr[data-search]');

        let visible = 0;

        rows.forEach(row => {

            const matchSearch = !search || row.dataset.search.includes(search);

            const matchGrade = !grade || row.dataset.grade === grade;

            const matchStatus = !status || row.dataset.status === status;

            const show = matchSearch && matchGrade && matchStatus;

            row.style.display = show ? '' : 'none';

            if (show) visible++;

        });

        const counter = document.getElementById('sectionVisibleCount');

        if (counter) counter.textContent = visible;

    }

    function filterTeacherTable() {

        const search = (document.getElementById('teacherSearchInput').value || '').toLowerCase();

        const status = document.getElementById('teacherStatusFilter').value;

        const rows = document.querySelectorAll('#teacherTable tbody tr[data-search]');

        let visible = 0;

        rows.forEach(row => {

            const matchSearch = !search || row.dataset.search.includes(search);

            const matchStatus = !status || row.dataset.status === status;

            const show = matchSearch && matchStatus;

            row.style.display = show ? '' : 'none';

            if (show) visible++;

        });

        const counter = document.getElementById('teacherVisibleCount');

        if (counter) counter.textContent = visible;

    }

    // â”€â”€ Assessment & Promotion filters â”€â”€
    // Grade/status/search filtering + pagination is now server-side (GET params
    // assess_grade / assess_status / assess_search / assess_page, handled in
    // EnrollmentController::adminIndex) — the chips and search box are plain
    // links/a form now, no client-side filtering needed here.

    function filterFinanceTable() {

        const search = (document.getElementById('financeSearchInput').value || '').toLowerCase();

        const pay = document.getElementById('financePayFilter').value;

        const section = document.getElementById('financeSectionFilter').value;

        const rows = document.querySelectorAll('#financeStudentTable tbody tr[data-search]');

        let visible = 0;

        rows.forEach(row => {

            const matchSearch = !search || row.dataset.search.includes(search);

            const rowPay = row.dataset.pay || '';

            let matchPay = true;
            if (pay) {
                if (pay === 'unpaid') {
                    // Unpaid includes: empty, null, undefined, 'pending', or 'unpaid'
                    matchPay = !rowPay || rowPay === 'pending' || rowPay === 'unpaid';
                } else {
                    matchPay = rowPay === pay;
                }
            }

            const matchSection = !section || row.dataset.section === section;

            const show = matchSearch && matchPay && matchSection;

            row.style.display = show ? '' : 'none';

            if (show) visible++;

        });

        const counter = document.getElementById('financeVisibleCount');

        if (counter) counter.textContent = visible;

    }

    function filterPaymentTable() {

        const search = (document.getElementById('payFilterSearch').value || '').toLowerCase();

        const status = document.getElementById('payFilterStatus').value;

        const method = document.getElementById('payFilterMethod').value;

        const year = document.getElementById('payFilterYear').value;

        // Covers both the Online Payments (#paymentsTable) and Cash
        // Transactions (#walkInPaymentsTable) tabs — rows use data-student,
        // not data-search (which doesn't exist on any row).
        const rows = document.querySelectorAll('#paymentsTable tbody tr[data-student], #walkInPaymentsTable tbody tr[data-student]');

        let visible = 0;

        rows.forEach(row => {

            const matchSearch = !search || (row.dataset.student || '').includes(search);

            const matchStatus = status === 'all' || row.dataset.status === status;

            const matchMethod = method === 'all' || row.dataset.method === method;

            const matchYear = year === 'all' || row.dataset.year === year;

            const show = matchSearch && matchStatus && matchMethod && matchYear;

            row.style.display = show ? '' : 'none';

            if (show) visible++;

        });

        const counter = document.getElementById('paymentVisibleCount');

        if (counter) counter.textContent = visible;

    }

    // Mirrors the working filterInstallments()/resetInstallmentFilters() in
    // resources/views/finance/installments.blade.php — this dashboard's
    // #installmentsTable rows use the identical data-year/data-status/
    // data-overdue/data-student attributes, so the same logic applies as-is.
    function filterInstallments() {
        const year    = document.getElementById('instFilterYear').value.toLowerCase();
        const status  = document.getElementById('instFilterStatus').value.toLowerCase();
        const overdue = document.getElementById('instFilterOverdue').value.toLowerCase();
        const search  = document.getElementById('instFilterSearch').value.toLowerCase();
        const rows    = document.querySelectorAll('#installmentsTable tbody tr[data-student]');
        let visible   = 0;

        rows.forEach(row => {
            const rowYear    = (row.dataset.year    || '').toLowerCase();
            const rowStatus  = (row.dataset.status  || '').toLowerCase();
            const rowOverdue = (row.dataset.overdue || '').toLowerCase();
            const rowStudent = (row.dataset.student || '').toLowerCase();

            const matchYear    = year    === 'all' || rowYear    === year;
            const matchStatus  = status  === 'all' || rowStatus  === status;
            const matchOverdue = overdue === 'all' || rowOverdue === overdue;
            const matchSearch  = !search || rowStudent.includes(search);

            if (matchYear && matchStatus && matchOverdue && matchSearch) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        const countEl = document.getElementById('instRowCount');
        if (countEl) countEl.textContent = visible + ' student(s) found';

        const emptyRow = document.getElementById('instEmptyRow');
        if (emptyRow) emptyRow.style.display = visible === 0 ? '' : 'none';
    }

    function resetInstallmentFilters() {
        document.getElementById('instFilterYear').value    = 'all';
        document.getElementById('instFilterStatus').value  = 'all';
        document.getElementById('instFilterOverdue').value = 'all';
        document.getElementById('instFilterSearch').value  = '';
        filterInstallments();
    }

    // Shared helper for server-side (GET form reload) search boxes — e.g.
    // Student Management and Guidance search — so typing auto-searches
    // ~600ms after the last keystroke instead of requiring blur/Enter.
    function debounceFormSubmit(el, ms) {
        clearTimeout(el._debounceTimer);
        // requestSubmit(), not submit() — submit() does not dispatch a
        // 'submit' event at all (DOM spec), so the delegated AJAX
        // interception below would never fire and this would silently
        // fall back to a full page reload. Confirmed via direct test.
        el._debounceTimer = setTimeout(() => el.form.requestSubmit(), ms || 600);
    }

    function validateWalkinGmail(input) {
        const hint = document.getElementById('walkin-email-hint');
        const val  = input.value.trim();

        const walkinTypos = {
            'gmial.com':'gmail.com','gmal.com':'gmail.com','gamil.com':'gmail.com',
            'gnail.com':'gmail.com','gmaill.com':'gmail.com','gmail.co':'gmail.com',
            'gmai.com':'gmail.com','gmali.com':'gmail.com',
        };
        const rfcRe = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

        if (!val) {
            input.classList.remove('is-invalid', 'is-valid');
            if (hint) { hint.textContent = 'Must be a Gmail address — credentials will be sent here after approval.'; hint.style.color = ''; }
            return;
        }

        // 1. Format
        if (!rfcRe.test(val)) {
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) { hint.textContent = 'Invalid email format.'; hint.style.color = '#dc3545'; }
            return;
        }

        const domain = val.split('@')[1].toLowerCase();

        // 2. Typo
        if (walkinTypos[domain]) {
            const fix = val.split('@')[0] + '@gmail.com';
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) {
                hint.innerHTML = 'Did you mean <strong>' + fix + '</strong>? '
                    + '<a href="#" style="color:#d97706;font-weight:700;" onclick="document.getElementById(\'walkin-guardian-email\').value=\''
                    + fix + '\';validateWalkinGmail(document.getElementById(\'walkin-guardian-email\'));return false;">Use this</a>';
                hint.style.color = '#d97706';
            }
            return;
        }

        // 3. Gmail-only
        if (domain !== 'gmail.com') {
            input.classList.add('is-invalid'); input.classList.remove('is-valid');
            if (hint) { hint.textContent = 'Only Gmail addresses accepted (e.g. yourname@gmail.com).'; hint.style.color = '#dc3545'; }
            return;
        }

        // 4. Valid
        input.classList.remove('is-invalid'); input.classList.add('is-valid');
        if (hint) { hint.textContent = '✓ Valid Gmail address'; hint.style.color = '#28a745'; }
    }

    function switchAdminPmtTab(tab) {
        ['online', 'walkin'].forEach(function(t) {
            const btn   = document.getElementById('adminPmtTab-' + t);
            const panel = document.getElementById('adminPmtPanel-' + t);
            if (!btn || !panel) return;
            const isActive = (t === tab);
            panel.style.display = isActive ? '' : 'none';
            if (isActive) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function resetPaymentFilters() {

        document.getElementById('payFilterSearch').value = '';

        document.getElementById('payFilterStatus').value = 'all';

        document.getElementById('payFilterMethod').value = 'all';

        document.getElementById('payFilterYear').value = 'all';

        filterPaymentTable();

    }

    // Auto-redirect to settings Account tab after photo/password update
    <?php if(session('settings_tab') || session('photo_success') || session('password_success') || $errors->has('current_password')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showSection('settings');
        showSettingsTab('account');
    });
    <?php endif; ?>

    // Initialize payment filters when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Reset payment filters to ensure all records show
        setTimeout(() => {
            if (document.getElementById('payFilterSearch')) {
                document.getElementById('payFilterSearch').value = '';
                document.getElementById('payFilterStatus').value = 'all';
                document.getElementById('payFilterMethod').value = 'all';
                document.getElementById('payFilterYear').value = 'all';
                
                if (typeof filterPaymentTable === 'function') {
                    filterPaymentTable();
                }
            }
        }, 100);
    });

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // EVENT DELEGATION (replaces inline onclick)

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    document.addEventListener('click', function(e) {

        var btn = e.target.closest('[data-id]');

        if (!btn) return;

        var id = btn.dataset.id;

        // Student actions

        if (btn.classList.contains('js-student-view')) { 
            e.preventDefault(); 
            viewStudentDetails(id); 
        }

        else if (btn.classList.contains('js-student-edit')) { 
            e.preventDefault(); 
            editStudent(id); 
        }

        else if (btn.classList.contains('js-student-archive')) {
            e.preventDefault();
            deleteStudent(id, btn.dataset.name);
        }

        else if (btn.classList.contains('js-student-restore')) {
            e.preventDefault();
            restoreStudent(id, btn.dataset.name);
        }

        else if (btn.classList.contains('js-student-force-delete')) {
            e.preventDefault();
            forceDeleteStudent(id, btn.dataset.name);
        }

        // Enrollment actions

        else if (btn.classList.contains('js-enrollment-view')) { 
            e.preventDefault(); 
            viewEnrollmentModal(id); 
        }

        else if (btn.classList.contains('js-enrollment-approve')) {
            e.preventDefault();
            console.log('Approve button clicked, ID:', id);
            approveEnrollment(id);
        }

        else if (btn.classList.contains('js-enrollment-decline')) {
            e.preventDefault();
            declineEnrollment(id);
        }

        // Document reject

        else if (btn.classList.contains('js-doc-reject')) {
            e.preventDefault();
            rejectDoc(id);
        }

        // Guidance actions

        else if (btn.classList.contains('js-guidance-view')) {
            e.preventDefault();
            viewGuidanceRecord(id);
        }

        else if (btn.classList.contains('js-guidance-edit')) {
            e.preventDefault();
            editGuidanceRecord(id);
        }

        else if (btn.classList.contains('js-guidance-delete')) {
            e.preventDefault();
            deleteGuidanceRecord(id);
        }

        // Section actions

        else if (btn.classList.contains('js-section-view-students')) {
            e.preventDefault();
            viewSectionStudents(id, btn.dataset.name);
        }

        else if (btn.classList.contains('js-section-view-subjects')) {
            e.preventDefault();
            viewSectionSubjects(id, btn.dataset.name);
        }

        else if (btn.classList.contains('js-section-manage-subjects')) {
            e.preventDefault();
            openManageSubjectsModal(id, btn.dataset.name, btn.dataset.grade);
        }

        else if (btn.classList.contains('js-section-edit')) {
            e.preventDefault();
            const sectionData = {
                id: id,
                name: btn.dataset.name,
                grade: btn.dataset.grade,
                room: btn.dataset.room,
                sy: btn.dataset.sy,
                active: btn.dataset.active === '1',
                maxStudents: btn.dataset.max || '30'
            };
            editSection(sectionData.id, sectionData.name, sectionData.grade, sectionData.room, sectionData.sy, sectionData.active, sectionData.maxStudents);
        }

        else if (btn.classList.contains('js-section-delete')) {
            e.preventDefault();
            deleteSectionRecord(id);
        }

        // Teacher actions

        else if (btn.classList.contains('js-teacher-view')) {
            e.preventDefault();
            viewTeacherDetails(id);
        }

        else if (btn.classList.contains('js-teacher-edit')) {
            e.preventDefault();
            editTeacher(id, btn.dataset.name, btn.dataset.email, btn.dataset.active === '1');
        }

        else if (btn.classList.contains('js-teacher-delete')) {
            e.preventDefault();
            deleteTeacher(id);
        }

        // Subject actions

        else if (btn.classList.contains('js-subject-edit')) {
            e.preventDefault();
            editSubject(id, btn.dataset.name, btn.dataset.code, btn.dataset.desc, btn.dataset.grade, btn.dataset.active === '1');
        }

        else if (btn.classList.contains('js-subject-delete')) {
            e.preventDefault();
            deleteSubject(id);
        }

        // Schedule actions

        else if (btn.classList.contains('js-schedule-edit')) {
            e.preventDefault();
            editSchedule(id);
        }

        else if (btn.classList.contains('js-schedule-delete')) {
            e.preventDefault();
            deleteSchedule(id);
        }

        // Fee actions

        else if (btn.classList.contains('js-fee-edit')) {
            e.preventDefault();
            editFee(id);
        }

        else if (btn.classList.contains('js-fee-delete')) {
            e.preventDefault();
            deleteFee(id);
        }

        // Finance payment actions

        else if (btn.classList.contains('js-payment-update')) {
            e.preventDefault();
            updatePaymentStatus(
                id,
                btn.dataset.name,
                btn.dataset.status,
                btn.dataset.amount,
                btn.dataset.method,
                btn.dataset.ref,
                btn.dataset.paymentOption,
                btn.dataset.downpayment,
                btn.dataset.monthly,
                btn.dataset.totalFee,
                btn.dataset.remaining
            );
        }

        else if (btn.classList.contains('js-payment-pay')) {
            e.preventDefault();
            openAdminPaymentFlow(
                btn.dataset.id,
                btn.dataset.name,
                btn.dataset.grade,
                btn.dataset.amountPaid,
                btn.dataset.downpayment,
                btn.dataset.monthly,
                btn.dataset.paymentType,
                btn.dataset.totalFee,
                btn.dataset.paymentOption
            );
        }

        // Payment screenshot actions

        else if (btn.classList.contains('js-view-screenshot')) {
            e.preventDefault();
            viewScreenshot(btn.dataset.screenshotUrl);
        }

        // Payments section actions

        else if (btn.classList.contains('js-view-payment')) {
            e.preventDefault();
            viewPaymentDetails(btn.dataset.paymentId, btn.dataset.studentName, btn.dataset.email);
        }

        else if (btn.classList.contains('js-approve-payment')) {
            e.preventDefault();
            approvePayment(id);
        }

        else if (btn.classList.contains('js-reject-payment')) {
            e.preventDefault();
            rejectPayment(id);
        }

        // Section add student action

        else if (btn.classList.contains('add-student')) {
            e.preventDefault();
            openAddStudentModal(btn.dataset.id, btn.dataset.name, btn.dataset.enrolled, btn.dataset.max);
        }

        // Assignment actions

        else if (btn.classList.contains('js-assignment-edit')) {
            e.preventDefault();
            editAssignment(id);
        }

        else if (btn.classList.contains('js-assignment-delete')) {
            e.preventDefault();
            deleteAssignment(id);
        }

        // Installment view action
        else if (btn.classList.contains('js-view-installments')) {
            e.preventDefault();
            showInstallmentModal(
                btn.dataset.id,
                btn.dataset.name,
                btn.dataset.grade,
                btn.dataset.option,
                btn.dataset.monthly,
                btn.dataset.downpayment,
                btn.dataset.totalFee,
                btn.dataset.totalPaid
            );
        }

});

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // PAYMENT SECTION FUNCTIONS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function viewPaymentDetails(paymentId, studentName, email) {
        const btn = document.querySelector(`.js-view-payment[data-payment-id="${paymentId}"]`);
        if (!btn) {
            showCustomAlert('error', 'Error', 'Payment button not found.');
            return;
        }

        // Get data from button attributes
        const description = btn.dataset.description || 'Payment';
        const submitted = btn.dataset.submitted || 'N/A';
        const status = btn.dataset.status || 'N/A';
        const totalAmount = btn.dataset.totalAmount || 0;
        const method = btn.dataset.method || 'N/A';
        const installmentData = btn.dataset.installment || '';
        const screenshotUrl = btn.dataset.screenshotUrl || '';
        const grade = btn.dataset.grade || 'N/A';
        const paymentOption = btn.dataset.paymentOption || 'N/A';
        const totalFee = btn.dataset.totalFee || 0;
        const amountPaid = btn.dataset.amountPaid || 0;
        const balance = btn.dataset.balance || 0;
        const reviewedBy = btn.dataset.reviewedBy || 'System';
        const reviewedAt = btn.dataset.reviewedAt || 'N/A';

        let installment = null;
        try { installment = installmentData ? JSON.parse(installmentData) : null; } catch(e) { installment = null; }

        // Populate Student Information
        document.getElementById('adminDetailStudentName').textContent = studentName || 'N/A';
        document.getElementById('adminDetailStudentEmail').textContent = email || 'N/A';
        document.getElementById('adminDetailGradeLevel').textContent = grade;

        // Populate Payment Information
        document.getElementById('adminDetailPaymentId').textContent = '#' + paymentId;
        document.getElementById('adminDetailDescription').textContent = description;
        document.getElementById('adminDetailMethod').textContent = method === 'gcash' ? 'GCash' : 'Cash';
        document.getElementById('adminDetailSubmitted').textContent = submitted;
        document.getElementById('adminDetailStatus').innerHTML = status === 'approved' || status === 'completed'
            ? '<span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; background:#e8f5e9; color:#2e7d32;"><i class="bi bi-check-circle"></i> Approved</span>'
            : (status === 'pending'
                ? '<span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; background:#fff3e0; color:#e65100;"><i class="bi bi-hourglass-split"></i> Pending</span>'
                : '<span style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; background:#ffebee; color:#c62828;"><i class="bi bi-x-circle"></i> Rejected</span>');

        // Show Installment Details if available
        if (installment) {
            document.getElementById('adminDetailMonthName').textContent = installment.month_name || 'N/A';
            document.getElementById('adminDetailBaseAmount').textContent = '₱' + parseFloat(installment.amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
            const lateFee = parseFloat(installment.late_fee || 0);
            const totalDue = parseFloat(installment.amount || 0) + lateFee;
            document.getElementById('adminDetailTotalAmount').textContent = '₱' + totalDue.toLocaleString('en-PH', {minimumFractionDigits: 2});
            if (lateFee > 0) {
                document.getElementById('adminDetailLateFee').textContent = '₱' + lateFee.toLocaleString('en-PH', {minimumFractionDigits: 2});
                document.getElementById('adminLateFeeRow').style.display = 'block';
            } else {
                document.getElementById('adminLateFeeRow').style.display = 'none';
            }
            document.getElementById('adminInstallmentDetailsSection').style.display = 'block';
        } else {
            document.getElementById('adminInstallmentDetailsSection').style.display = 'none';
        }

        // Show Enrollment Summary
        const fmt = (n) => '₱' + parseFloat(n || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
        document.getElementById('adminDetailPaymentOption').textContent = paymentOption;
        document.getElementById('adminDetailTotalFee').textContent = fmt(totalFee);
        document.getElementById('adminDetailAmountPaid').textContent = fmt(amountPaid);
        document.getElementById('adminDetailBalance').textContent = fmt(balance);
        document.getElementById('adminEnrollmentSummarySection').style.display = 'block';

        // Show Review Information
        document.getElementById('adminDetailReviewedBy').textContent = reviewedBy;
        document.getElementById('adminDetailReviewedAt').textContent = reviewedAt;
        document.getElementById('adminReviewInfoSection').style.display = 'block';

        // Show screenshot if available
        if (screenshotUrl) {
            document.getElementById('adminDetailScreenshot').src = screenshotUrl;
            document.getElementById('adminScreenshotPreviewSection').style.display = 'block';
        } else {
            document.getElementById('adminScreenshotPreviewSection').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
    }

    function approvePayment(paymentId) {
        showConfirm('Approve Payment', 'Are you sure you want to approve this payment?', function() {
            fetch(`/payments/${paymentId}/approve`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(async r => {
                const text = await r.text();
                let data;
                try { data = JSON.parse(text); } catch(e) { data = { message: text.substring(0, 200) }; }
                if (!r.ok) {
                    console.error('Approve error:', r.status, data);
                    throw new Error(data.message || `HTTP ${r.status}`);
                }
                return data;
            })
            .then(d => {
                showCustomAlert('success', 'Approved!', d.message || 'Payment has been approved successfully.');
                setTimeout(() => reloadWithSection(), 1000);
            })
            .catch(err => {
                console.error('Approve catch:', err);
                showCustomAlert('error', 'Error', err.message || 'Failed to approve payment.');
            });
        }, {
            btnText: 'Approve', btnClass: 'btn btn-success', btnIcon: 'bi-check-lg',
            headerBg: 'linear-gradient(135deg,#27ae60,#1e8449)', headerIcon: 'bi-check-lg'
        });
    }

    function rejectPayment(paymentId) {
        showConfirm('Reject Payment', 'Are you sure you want to reject this payment?', function() {
            fetch(`/payments/${paymentId}/reject`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(r => {
                if (!r.ok) throw r;
                return r.json();
            })
            .then(d => {
                showCustomAlert('success', 'Rejected!', 'Payment has been rejected.');
                setTimeout(() => reloadWithSection(), 1000);
            })
            .catch(err => {
                let msg = 'Failed to reject payment.';
                try { const e = err.json ? err.json() : Promise.resolve({}); e.then(data => { msg = data.message || Object.values(data.errors || {}).flat().join(', '); }); } catch(x) {}
                showCustomAlert('error', 'Error', msg);
            });
        }, {
            btnText: 'Reject', btnClass: 'btn btn-danger', btnIcon: 'bi-x-lg',
            headerBg: 'linear-gradient(135deg,#dc3545,#c82333)', headerIcon: 'bi-x-lg'
        });
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // INSTALLMENT MODAL FUNCTIONS
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function showInstallmentModal(enrollmentId, name, grade, option, monthly, downpayment, totalFee, totalPaid) {
        // Get installment data from global scope
        let installments = window.installmentData[enrollmentId] || [];
        
        // Format currency
        const fmt = (num) => '₱' + (num || 0).toLocaleString('en-PH', {minimumFractionDigits: 2});
        
        // Build modal content
        let content = `
            <div style="background:linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius:12px; padding:20px; margin-bottom:20px;">
                <h6 style="color:#2c3e50; margin-bottom:12px; font-weight:600;"><i class="bi bi-person-circle me-2"></i>Student Information</h6>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div><strong>Name:</strong> ${name}</div>
                    <div><strong>Grade:</strong> ${grade}</div>
                    <div><strong>Payment Option:</strong> ${option}</div>
                    <div><strong>Monthly:</strong> ${fmt(monthly)}</div>
                    <div><strong>Downpayment:</strong> ${fmt(downpayment)}</div>
                    <div><strong>Total Fee:</strong> ${fmt(totalFee)}</div>
                </div>
            </div>
            
            <div style="background:#f8f9fa; border-radius:12px; padding:20px;">
                <h6 style="color:#2c3e50; margin-bottom:16px; font-weight:600;"><i class="bi bi-calendar-check me-2"></i>Installment Schedule</h6>
                <div style="max-height:400px; overflow-y:auto;">
        `;
        
        if (installments.length === 0) {
            content += `
                <div style="text-align:center; padding:40px; color:#6c757d;">
                    <i class="bi bi-calendar-x" style="font-size:48px; margin-bottom:16px;"></i>
                    <div>No installment data available</div>
                </div>
            `;
        } else {
            installments.forEach(inst => {
                const statusClass = inst.status === 'paid' ? 'paid' : (inst.status === 'pending_approval' ? 'pending-approval' : (inst.status === 'pending' ? 'pending' : 'overdue'));
                const statusIcon  = inst.status === 'paid' ? 'bi-check-circle-fill' : (inst.status === 'pending_approval' ? 'bi-hourglass' : (inst.status === 'pending' ? 'bi-clock' : 'bi-exclamation-circle-fill'));
                const statusText  = inst.status === 'paid' ? 'Paid' : (inst.status === 'pending_approval' ? 'For Approval' : (inst.status === 'pending' ? 'Pending' : 'Overdue'));
                const dueDate = inst.due_date ? new Date(inst.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'No due date';
                const paidAt = inst.paid_at || '—';
                
                content += `
                    <div style="border:1px solid #e0e0e0; border-radius:8px; padding:16px; margin-bottom:12px; background:#fff;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <div style="font-weight:600; color:#2c3e50; font-size:16px;">
                                ${inst.month_name || inst.month || 'Unknown Month'}
                            </div>
                            <span class="status-badge ${statusClass}"><i class="bi ${statusIcon}"></i> ${statusText}</span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:14px;">
                            <div><strong>Due:</strong> ${dueDate}</div>
                            <div><strong>Paid:</strong> ${paidAt}</div>
                            <div><strong>Amount:</strong> ${fmt(inst.amount)}</div>
                            <div><strong>Late Fee:</strong> ${fmt(inst.late_fee || 0)}</div>
                            <div><strong>Total Due:</strong> ${fmt(inst.total_due || (inst.amount + (inst.late_fee || 0)))}</div>
                            <div><strong>Weeks Overdue:</strong> ${inst.weeks_overdue || 0}</div>
                        </div>
                    </div>
                `;
            });
        }
        
        content += `
                </div>
            </div>
        `;
        
        // Update modal content and show
        document.getElementById('installmentModalContent').innerHTML = content;
        const modal = new bootstrap.Modal(document.getElementById('installmentModal'));
        modal.show();
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // MAINTENANCE MODE TOGGLE
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    let _maintenanceOn = <?php echo e(($maintenanceMode ?? false) ? 'true' : 'false'); ?>;

    function toggleMaintenanceMode() {
        const btn = document.getElementById('maintenance-topbar-btn');
        btn.disabled = true;

        fetch('/admin/settings/toggle-maintenance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showAdminToast(data.message || 'Failed.', 'error'); btn.disabled = false; return; }

            _maintenanceOn = data.maintenance_mode;
            const label = document.getElementById('maintenance-topbar-label');

            if (data.maintenance_mode) {
                btn.style.borderColor = '#e74c3c';
                btn.style.background  = '#fdecea';
                btn.style.color       = '#c0392b';
                label.textContent     = 'Maintenance ON';
                btn.title             = 'Maintenance ON — click to turn off';
            } else {
                btn.style.borderColor = '#ddd';
                btn.style.background  = '#f8f9fa';
                btn.style.color       = '#666';
                label.textContent     = 'Maintenance';
                btn.title             = 'Turn on Maintenance Mode';
            }

            btn.disabled = false;
            showAdminToast(data.message, data.maintenance_mode ? 'warning' : 'success');

            // Sync the Settings tab toggle if visible
            const settingsSel = document.getElementById('set-maintenance_mode');
            if (settingsSel) settingsSel.value = data.maintenance_mode ? '1' : '0';
        })
        .catch(() => { showAdminToast('Network error.', 'error'); btn.disabled = false; });
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // ENROLLMENT WINDOW TOGGLE
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    let _enrollmentCurrentlyOpen = <?php echo e(($enrollmentOpen ?? true) ? 'true' : 'false'); ?>;

    function toggleEnrollmentWindow() {
        const btn = document.getElementById('enrollment-toggle-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('/admin/settings/toggle-enrollment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showAdminToast(data.message || 'Failed to toggle enrollment.', 'error'); btn.disabled = false; return; }

            _enrollmentCurrentlyOpen = data.enrollment_open;
            const badge = document.getElementById('enrollment-window-badge');
            const label = document.getElementById('enrollment-window-label');
            const togLabel = document.getElementById('enrollment-toggle-label');

            if (data.enrollment_open) {
                badge.style.background = '#e8f8f0'; badge.style.color = '#1a7a44'; badge.style.borderColor = '#27ae60';
                badge.querySelector('span').style.background = '#27ae60';
                label.textContent = 'Enrollment OPEN';
                btn.className = 'btn-dash btn-secondary';
                btn.innerHTML = '<i class="bi bi-lock-fill me-1"></i><span id="enrollment-toggle-label">Close Enrollment</span>';
            } else {
                badge.style.background = '#fdecea'; badge.style.color = '#c0392b'; badge.style.borderColor = '#e74c3c';
                badge.querySelector('span').style.background = '#e74c3c';
                label.textContent = 'Enrollment CLOSED';
                btn.className = 'btn-dash btn-primary';
                btn.innerHTML = '<i class="bi bi-unlock-fill me-1"></i><span id="enrollment-toggle-label">Open Enrollment</span>';
            }

            btn.disabled = false;

            // Warn if opening enrollment with unassessed students
            if (data.enrollment_open && data.unassessed_count > 0) {
                showAdminToast(`âš  ${data.unassessed_count} Grade 1“6 student(s) have not been assessed yet. Consider completing assessments first.`, 'warning');
            } else {
                showAdminToast(data.message, data.enrollment_open ? 'success' : 'error');
            }
        })
        .catch(() => { showAdminToast('Network error.', 'error'); btn.disabled = false; });
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // SETTINGS SAVE / LOAD
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    function saveSettings(group) {
        var settings = [];
        document.querySelectorAll('#tab-' + group + ' [id^="set-"]').forEach(function(el) {
            var key = el.id.replace('set-', '');
            settings.push({ key: key, value: el.value });
        });
        if (!settings.length) { showAdminToast('No settings found.', 'error'); return; }

        fetch('/admin/settings', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ settings: settings })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) showAdminToast('Settings saved successfully.', 'success');
            else showAdminToast(d.message || 'Failed to save settings.', 'error');
        })
        .catch(() => showAdminToast('Network error saving settings.', 'error'));
    }

    function loadSettingsGroup(group) {
        fetch('/admin/settings/' + group, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            (d.settings || []).forEach(function(s) {
                var el = document.getElementById('set-' + s.key);
                if (el) el.value = s.value ?? '';
            });
        });
    }

    // Auto-load all settings groups when Settings section is opened
    var _settingsLoaded = false;
    (function() {
        var origShowSection = window.showSection;
        window.showSection = function(section) {
            if (origShowSection) origShowSection(section);
            if (section === 'settings' && !_settingsLoaded) {
                _settingsLoaded = true;
                ['school','academic','security'].forEach(loadSettingsGroup);
            }
        };
    })();

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // CHANGE STATUS MODAL
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    let _csEnrollmentId = null;

    // â”€â”€ Admin Promissory Note â”€â”€
    var _apnEnrollmentId = null;
    function openAdminPromissoryModal(enrollmentId, studentName, balance) {
        _apnEnrollmentId = enrollmentId;
        document.getElementById('apn-student-display').textContent = studentName;
        document.getElementById('apn-amount-overdue').value  = balance;
        document.getElementById('apn-amount-promised').value = balance;
        document.getElementById('apn-promise-date').value    = '';
        document.getElementById('apn-guardian').value        = '';
        document.getElementById('apn-remarks').value         = '';
        new bootstrap.Modal(document.getElementById('adminPromissoryModal')).show();
    }

    function saveAdminPromissoryNote() {
        var btn = document.getElementById('apn-save-btn');
        var promiseDate = document.getElementById('apn-promise-date').value;
        var amountPromised = document.getElementById('apn-amount-promised').value;
        if (!promiseDate) { showToast('Please select a promise date.', 'warning'); return; }
        if (!amountPromised || parseFloat(amountPromised) <= 0) { showToast('Please enter a valid promised amount.', 'warning'); return; }
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
        fetch('/promissory-notes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({
                enrollment_id:   _apnEnrollmentId,
                amount_overdue:  document.getElementById('apn-amount-overdue').value,
                amount_promised: amountPromised,
                promise_date:    promiseDate,
                parent_guardian: document.getElementById('apn-guardian').value,
                remarks:         document.getElementById('apn-remarks').value,
            })
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-file-earmark-check"></i> Save Promissory Note';
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('adminPromissoryModal')).hide();
                showToast('Promissory note ' + data.reference + ' created. You can print it from the Finance → Installments section.', 'success');
            } else {
                showToast(data.message || 'Failed to create promissory note.', 'error');
            }
        })
        .catch(function(){ btn.disabled = false; btn.innerHTML = '<i class="bi bi-file-earmark-check"></i> Save Promissory Note'; showToast('Network error. Please try again.', 'error'); });
    }

    function openChangeStatusModal(userId, enrollmentId, currentStatus, studentName) {
        _csEnrollmentId = enrollmentId;
        document.getElementById('cs-student-name').textContent = studentName;
        document.getElementById('cs-new-status').value = currentStatus || 'pending';
        new bootstrap.Modal(document.getElementById('changeStatusModal')).show();
    }

    function confirmChangeStatus() {
        if (!_csEnrollmentId) { showAdminToast('No enrollment selected.', 'error'); return; }
        const newStatus = document.getElementById('cs-new-status').value;
        fetch(`/admin/enrollment/${_csEnrollmentId}/change-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ status: newStatus })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAdminToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('changeStatusModal')).hide();
                setTimeout(() => location.reload(), 800);
            } else {
                showAdminToast(data.message || 'Failed to update status.', 'error');
            }
        })
        .catch(() => showAdminToast('Network error.', 'error'));
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // SM ASSESS MODAL (Student Management Assessment)
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    let _smAssessStudentId = null, _smAssessEnrollmentId = null, _smAssessGradeRaw = null;
    let _svStudentId = null;
    let _docReloadFn  = null;

    const gradeNextMap = {
        'nursery':'kindergarten','kindergarten':'grade1',
        'grade1':'grade2','grade2':'grade3','grade3':'grade4',
        'grade4':'grade5','grade5':'grade6','grade6':'graduated'
    };
    const gradeLabels = {
        'nursery':'Nursery','kindergarten':'Kindergarten',
        'grade1':'Grade 1','grade2':'Grade 2','grade3':'Grade 3',
        'grade4':'Grade 4','grade5':'Grade 5','grade6':'Grade 6','graduated':'Graduated'
    };

    let _smAssessFromSY = '';

    function openSmAssessModal(userId, studentName, gradeRaw, section, totalFee, amtPaid, balance, payStatus, fromSY) {
        _smAssessStudentId = userId;
        _smAssessGradeRaw  = gradeRaw;
        _smAssessFromSY    = fromSY || '';

        const gradeDisplay = gradeLabels[gradeRaw] || gradeRaw;
        document.getElementById('sm-assess-subtitle').textContent = studentName + ' — ' + gradeDisplay + (section && section !== '—' ? ' (' + section + ')' : '');
        document.getElementById('sm-assess-submit-btn').style.display = 'none';

        smAssessTab('grades', document.querySelector('.sm-assess-tab'));

        const nextSel = document.getElementById('sm-next-grade');
        nextSel.innerHTML = '';
        const nextGrade = gradeNextMap[gradeRaw];
        if (nextGrade) {
            const opt = document.createElement('option');
            opt.value = nextGrade;
            opt.textContent = gradeLabels[nextGrade] || nextGrade;
            nextSel.appendChild(opt);
        }

        document.querySelectorAll('input[name="sm-decision"]').forEach(r => r.checked = false);
        document.getElementById('sm-opt-promote').style.borderColor = '#ddd';
        document.getElementById('sm-opt-retain').style.borderColor  = '#ddd';
        document.getElementById('sm-next-grade-row').style.display  = 'none';
        document.getElementById('sm-assess-remarks').value = '';

        // Render balance tab
        const fmt = n => '₱' + Number(n).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
        const isPaid    = payStatus === 'paid';
        const isPartial = payStatus === 'partial';
        const balColor  = balance <= 0 ? '#2e7d32' : (balance > 0 && isPartial ? '#1976d2' : '#c62828');
        const pct       = totalFee > 0 ? Math.min(100, Math.round(amtPaid / totalFee * 100)) : 0;
        document.getElementById('sm-balance-content').innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
                <div style="background:#e3f2fd;border-radius:10px;padding:16px;text-align:center;">
                    <div style="font-size:11px;color:#1565c0;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Total Fee</div>
                    <div style="font-size:20px;font-weight:700;color:#1565c0;">${fmt(totalFee)}</div>
                </div>
                <div style="background:#e8f5e9;border-radius:10px;padding:16px;text-align:center;">
                    <div style="font-size:11px;color:#2e7d32;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Amount Paid</div>
                    <div style="font-size:20px;font-weight:700;color:#2e7d32;">${fmt(amtPaid)}</div>
                </div>
                <div style="background:${balance<=0?'#e8f5e9':'#fff3e0'};border-radius:10px;padding:16px;text-align:center;">
                    <div style="font-size:11px;color:${balColor};font-weight:700;text-transform:uppercase;margin-bottom:4px;">Balance</div>
                    <div style="font-size:20px;font-weight:700;color:${balColor};">${fmt(balance)}</div>
                </div>
            </div>
            <div style="height:10px;background:#e0e0e0;border-radius:5px;margin-bottom:16px;overflow:hidden;">
                <div style="height:100%;border-radius:5px;background:${pct>=100?'#28a745':'#1976d2'};width:${pct}%;transition:width .4s;"></div>
            </div>
            <div style="background:${balance>0?'#fff8f0':'#f0fdf4'};border:1px solid ${balance>0?'#ffc107':'#86efac'};border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-${balance<=0?'check-circle-fill':'exclamation-triangle-fill'}" style="font-size:20px;color:${balance<=0?'#2e7d32':'#e67e00'};"></i>
                <div>
                    <div style="font-weight:700;font-size:13px;color:${balance<=0?'#166534':'#854d0e'};">
                        ${balance<=0 ? 'Fully Paid — No outstanding balance' : 'Outstanding balance of ' + fmt(balance)}
                    </div>
                    <div style="font-size:11px;color:#666;margin-top:2px;">
                        Payment Status: <strong>${payStatus.charAt(0).toUpperCase()+payStatus.slice(1)}</strong> &nbsp;·&nbsp; ${pct}% of tuition settled
                    </div>
                </div>
            </div>
            ${balance > 0 ? '<div style="margin-top:12px;padding:10px 14px;background:#fff3e0;border-radius:8px;font-size:12px;color:#854d0e;"><i class="bi bi-info-circle me-1"></i>Student has an outstanding balance. You may still promote them — admin discretion applies. The balance will carry over or must be settled before the new school year begins.</div>' : ''}
        `;

        new bootstrap.Modal(document.getElementById('smAssessModal')).show();
        loadSmGrades();
    }

    function smAssessTab(tab, btn) {
        document.querySelectorAll('.sm-assess-panel').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.sm-assess-tab').forEach(b => {
            b.style.background   = '#f8f9fa';
            b.style.color        = '#888';
            b.style.borderBottom = 'none';
        });
        document.getElementById('sm-tab-' + tab).style.display = 'block';
        if (btn) {
            btn.style.background   = '#fff';
            btn.style.color        = 'var(--blue)';
            btn.style.borderBottom = '2px solid var(--blue)';
        }
        document.getElementById('sm-assess-submit-btn').style.display = (tab === 'decision') ? '' : 'none';
    }

    function loadSmGrades() {
        document.getElementById('sm-grades-loading').style.display = 'block';
        document.getElementById('sm-grades-content').style.display = 'none';
        fetch('/admin/student/' + _smAssessStudentId + '/grades-for-assess', {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('sm-grades-loading').style.display = 'none';
            var el = document.getElementById('sm-grades-content');
            var rows = data.grades || [];

            if (rows.length === 0) {
                el.innerHTML = '<div style="text-align:center;padding:30px;color:#999;">'
                    + '<i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>'
                    + 'No subjects found for this student\'s grade level.</div>';
                el.style.display = 'block';
                return;
            }

            var statusBadge = {
                approved:    '<span style="font-size:10px;padding:2px 7px;border-radius:10px;background:#dcfce7;color:#166534;font-weight:700;">Approved</span>',
                submitted:   '<span style="font-size:10px;padding:2px 7px;border-radius:10px;background:#dbeafe;color:#1e40af;font-weight:700;">Submitted</span>',
                draft:       '<span style="font-size:10px;padding:2px 7px;border-radius:10px;background:#fef9c3;color:#854d0e;font-weight:700;">Draft</span>',
                not_entered: '<span style="font-size:10px;padding:2px 7px;border-radius:10px;background:#f3f4f6;color:#9ca3af;font-weight:700;">Not yet</span>',
            };

            var isDesc = rows.length > 0 && rows[0].is_descriptive;

            var fmtGrade = function(v) {
                if (v === null || v === undefined || v === '') return '<span style="color:#ccc;">—</span>';
                if (isDesc) {
                    var c = v === 'DNME' ? '#dc2626' : '#1565c0';
                    return '<span style="font-weight:700;color:' + c + ';">' + v + '</span>';
                }
                var n = parseFloat(v);
                var c = n >= 75 ? '#166534' : (n >= 70 ? '#854d0e' : '#dc2626');
                return '<span style="font-weight:700;color:' + c + ';">' + n.toFixed(0) + '</span>';
            };

            var html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">';
            html += '<thead><tr style="background:#f8f9fa;">'
                + '<th style="padding:9px 12px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;">Subject</th>'
                + '<th style="padding:9px 8px;text-align:center;border-bottom:2px solid #e5e7eb;font-size:12px;">T1</th>'
                + '<th style="padding:9px 8px;text-align:center;border-bottom:2px solid #e5e7eb;font-size:12px;">T2</th>'
                + '<th style="padding:9px 8px;text-align:center;border-bottom:2px solid #e5e7eb;font-size:12px;">T3</th>'
                + (isDesc ? '' : '<th style="padding:9px 8px;text-align:center;border-bottom:2px solid #e5e7eb;font-size:12px;">Avg</th>')
                + '<th style="padding:9px 12px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;">Remarks</th>'
                + '<th style="padding:9px 8px;text-align:center;border-bottom:2px solid #e5e7eb;font-size:12px;">Status</th>'
                + '</tr></thead><tbody>';

            rows.forEach(function(row, i) {
                var avg = (!isDesc && row.average !== null && row.average !== undefined)
                    ? parseFloat(row.average).toFixed(1) : null;
                var avgHtml = avg !== null
                    ? '<span style="font-weight:700;color:' + (parseFloat(avg)>=75?'#166534':(parseFloat(avg)>=70?'#854d0e':'#dc2626')) + ';">' + avg + '</span>'
                    : '<span style="color:#ccc;">—</span>';

                var remarks = row.remarks || '';
                if (!remarks && !isDesc && avg !== null) {
                    var a = parseFloat(avg);
                    remarks = a >= 75 ? 'Passed' : (a >= 70 ? 'Passed w/ Remedial' : 'Failed');
                }

                var rowBg = i % 2 === 0 ? '#fff' : '#fafafa';
                html += '<tr style="border-top:1px solid #f0f0f0;background:' + rowBg + ';">'
                    + '<td style="padding:9px 12px;font-weight:600;">' + row.subject + '</td>'
                    + '<td style="padding:9px 8px;text-align:center;">' + fmtGrade(row.term1) + '</td>'
                    + '<td style="padding:9px 8px;text-align:center;">' + fmtGrade(row.term2) + '</td>'
                    + '<td style="padding:9px 8px;text-align:center;">' + fmtGrade(row.term3) + '</td>'
                    + (isDesc ? '' : '<td style="padding:9px 8px;text-align:center;">' + avgHtml + '</td>')
                    + '<td style="padding:9px 12px;font-size:12px;color:#555;">' + (remarks || '<span style="color:#ccc;">—</span>') + '</td>'
                    + '<td style="padding:9px 8px;text-align:center;">' + (statusBadge[row.status] || '') + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table>';

            if (!isDesc) {
                // Summary: numeric average
                var entered = rows.filter(function(r) { return r.average !== null && r.average !== undefined; });
                var overallAvg = entered.length ? (entered.reduce(function(s,r){ return s + parseFloat(r.average); }, 0) / entered.length).toFixed(1) : null;
                if (overallAvg !== null) {
                    var oa = parseFloat(overallAvg);
                    var oaColor = oa >= 75 ? '#166534' : (oa >= 70 ? '#854d0e' : '#dc2626');
                    var oaLabel = oa >= 75 ? 'General Average — Passed' : (oa >= 70 ? 'General Average — Passed w/ Remedial' : 'General Average — Failed');
                    html += '<div style="margin-top:12px;padding:10px 16px;background:#f0f4ff;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">'
                        + '<span style="font-size:12px;font-weight:600;color:#555;">' + oaLabel + '</span>'
                        + '<span style="font-size:20px;font-weight:700;color:' + oaColor + ';">' + overallAvg + '</span>'
                        + '</div>';
                }
            } else {
                html += '<div style="margin-top:12px;padding:10px 16px;background:#e8f4ff;border-radius:8px;font-size:11px;color:#1565c0;">'
                    + '<strong>Descriptive Rating:</strong> O=Outstanding · VS=Very Satisfactory · S=Satisfactory · FS=Fairly Satisfactory · DNME=Did Not Meet Expectations'
                    + '</div>';
            }

            if (data.school_year) {
                html += '<div style="margin-top:6px;font-size:11px;color:#9ca3af;text-align:right;">S.Y. ' + data.school_year + '</div>';
            }

            el.innerHTML = html;
            el.style.display = 'block';
        })
        .catch(function() {
            document.getElementById('sm-grades-loading').innerHTML = '<span style="color:#c0392b;"><i class="bi bi-exclamation-triangle me-1"></i>Failed to load grades.</span>';
        });
    }

    // â”€â”€ Document card helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    const DOC_TYPES = [
        { key: 'birth_certificate',  label: 'Birth Certificate',   icon: 'bi-file-earmark-person-fill' },
        { key: 'form_137',           label: 'Form 137',            icon: 'bi-file-earmark-text-fill'   },
        { key: 'report_card',        label: 'Grades / Report Card',icon: 'bi-file-earmark-check-fill'  },
        { key: 'two_by_two_picture', label: '2x2 ID Picture',      icon: 'bi-person-bounding-box'      },
    ];

    function buildDocCards(documents, reloadFnName) {
        // Index uploaded docs by type (latest wins)
        var byType = {};
        documents.forEach(function(d) { if (!byType[d.document_type]) byType[d.document_type] = d; });

        var html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;">';

        DOC_TYPES.forEach(function(type) {
            var doc = byType[type.key] || null;
            var border, bg, iconBg, iconColor, badge, actions = '', viewBtn = '';

            if (!doc) {
                border  = '1px dashed #d0d7de'; bg = '#fafafa';
                iconBg  = '#f0f0f0'; iconColor = '#bbb';
                badge   = '<span style="font-size:11px;font-weight:700;background:#f0f0f0;color:#999;padding:3px 10px;border-radius:20px;">â—‹ Missing</span>';
            } else if (doc.status === 'approved') {
                border  = '1px solid #bbf7d0'; bg = '#f0fdf4';
                iconBg  = '#dcfce7'; iconColor = '#16a34a';
                badge   = '<span style="font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;">✓ Approved</span>';
            } else if (doc.status === 'rejected') {
                border  = '1px solid #fecaca'; bg = '#fff5f5';
                iconBg  = '#fee2e2'; iconColor = '#dc2626';
                badge   = '<span style="font-size:11px;font-weight:700;background:#fee2e2;color:#dc2626;padding:3px 10px;border-radius:20px;">✗ Rejected</span>';
                if (doc.reject_reason) badge += '<div style="font-size:10px;color:#dc2626;margin-top:4px;opacity:0.8;">' + doc.reject_reason + '</div>';
            } else {
                border  = '1px solid #bfdbfe'; bg = '#eff6ff';
                iconBg  = '#dbeafe'; iconColor = '#1d4ed8';
                badge   = '<span style="font-size:11px;font-weight:700;background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;">â— Pending Review</span>';
            }

            if (doc && doc.file_path) {
                var _url  = '/storage/' + doc.file_path;
                var _name = (doc.original_name || 'document').replace(/\\/g,'\\\\').replace(/'/g,"\\'");
                var _lbl  = type.label.replace(/'/g,"\\'");
                viewBtn = '<button onclick="viewDocFile(\'' + _url + '\',\'' + _name + '\',\'' + _lbl + '\')"'
                    + ' style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#1d4ed8;background:none;border:none;padding:0;cursor:pointer;">'
                    + '<i class="bi bi-eye-fill"></i> View File</button>';
            }

            if (doc && doc.status === 'pending') {
                actions = '<div style="display:flex;gap:6px;">'
                    + '<button onclick="approveDocCard(' + doc.id + ',\'' + reloadFnName + '\')"'
                    + ' style="flex:1;padding:5px 0;background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">'
                    + '<i class="bi bi-check-lg"></i> Approve</button>'
                    + '<button onclick="rejectDocCard(' + doc.id + ',\'' + reloadFnName + '\')"'
                    + ' style="flex:1;padding:5px 0;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">'
                    + '<i class="bi bi-x-lg"></i> Reject</button>'
                    + '</div>';
            }

            var filename = doc ? doc.original_name : null;
            var dateStr  = (doc && doc.created_at)
                ? new Date(doc.created_at).toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' })
                : null;

            html += '<div style="border:' + border + ';border-radius:12px;padding:16px;background:' + bg + ';display:flex;flex-direction:column;gap:10px;">';

            // Icon + label
            html += '<div style="display:flex;align-items:center;gap:10px;">'
                + '<div style="width:42px;height:42px;border-radius:10px;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
                + '<i class="bi ' + type.icon + '" style="font-size:21px;color:' + iconColor + ';"></i></div>'
                + '<div style="font-weight:700;font-size:13px;color:#1a3a6c;line-height:1.3;">' + type.label + '</div>'
                + '</div>';

            // File info
            html += '<div style="font-size:12px;color:#555;border-top:1px solid rgba(0,0,0,0.06);padding-top:8px;">';
            if (filename) {
                html += '<div style="display:flex;align-items:center;gap:5px;overflow:hidden;">'
                    + '<i class="bi bi-paperclip" style="color:#999;flex-shrink:0;font-size:13px;"></i>'
                    + '<span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' + filename + '">' + filename + '</span></div>';
                if (dateStr) html += '<div style="font-size:11px;color:#aaa;margin-top:3px;">' + dateStr + '</div>';
            } else {
                html += '<span style="color:#bbb;font-style:italic;">Not yet uploaded</span>';
            }
            html += '</div>';

            // Badge + actions + view
            html += '<div style="display:flex;flex-direction:column;gap:8px;">'
                + badge
                + (actions  ? actions  : '')
                + (viewBtn  ? viewBtn  : '')
                + '</div>';

            html += '</div>';
        });

        html += '</div>';
        return html;
    }

    function approveDocCard(docId, reloadFnName) {
        fetch('/admin/documents/' + docId + '/approve', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                showAdminToast('Document approved.', 'success');
                window[reloadFnName]();
            } else {
                showAdminToast(d.message || 'Failed to approve.', 'error');
            }
        })
        .catch(function() { showAdminToast('Failed to approve document.', 'error'); });
    }

    function rejectDocCard(docId, reloadFnName) {
        _docReloadFn = window[reloadFnName];
        document.getElementById('rejectDocForm').action = '/admin/documents/' + docId + '/reject';
        new bootstrap.Modal(document.getElementById('rejectDocModal')).show();
    }

    function submitRejectDoc() {
        var form    = document.getElementById('rejectDocForm');
        var reason  = form.querySelector('[name="reject_reason"]').value.trim();
        if (!reason) { showAdminToast('Please enter a rejection reason.', 'error'); return; }
        var btn = document.querySelector('#rejectDocModal .btn-danger');
        btn.disabled = true;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ reject_reason: reason })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            btn.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('rejectDocModal')).hide();
            form.querySelector('[name="reject_reason"]').value = '';
            if (d.success) {
                showAdminToast('Document rejected.', 'success');
                if (_docReloadFn) { _docReloadFn(); _docReloadFn = null; }
            } else {
                showAdminToast(d.message || 'Failed to reject.', 'error');
            }
        })
        .catch(function() {
            btn.disabled = false;
            showAdminToast('Failed to reject document.', 'error');
        });
    }

    function reloadSmDocs() { loadSmDocuments(); }
    function reloadSvDocs() { if (_svStudentId) loadSvDocuments(_svStudentId); }

    function viewDocFile(url, name, docTypeLabel) {
        var ext = url.split('?')[0].split('.').pop().toLowerCase();
        var isImg = ['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1;
        var isPdf = ext === 'pdf';

        document.getElementById('docViewerTitle').textContent = name || 'Document';
        document.getElementById('docViewerSubtitle').textContent = docTypeLabel || '';

        var icon = document.getElementById('docViewerIcon');
        icon.className = 'bi ' + (isImg ? 'bi-file-earmark-image' : isPdf ? 'bi-file-earmark-pdf' : 'bi-file-earmark-text');

        var imgWrap = document.getElementById('docViewerImgWrap');
        var pdfWrap = document.getElementById('docViewerPdfWrap');

        if (isImg) {
            document.getElementById('docViewerImg').src = url;
            imgWrap.style.display = '';
            pdfWrap.style.display = 'none';
        } else if (isPdf) {
            document.getElementById('docViewerPdf').src = url;
            imgWrap.style.display = 'none';
            pdfWrap.style.display = '';
        } else {
            imgWrap.innerHTML = '<div style="padding:60px 20px;text-align:center;">'
                + '<i class="bi bi-file-earmark" style="font-size:64px;color:#ccc;"></i>'
                + '<p style="margin-top:16px;color:#666;font-size:13px;">Preview not available for this file type.</p>'
                + '<a href="' + url + '" download class="btn-dash btn-primary" style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">'
                + '<i class="bi bi-download"></i> Download File</a></div>';
            imgWrap.style.display = '';
            pdfWrap.style.display = 'none';
        }

        document.getElementById('docViewerDownload').href = url;
        document.getElementById('docViewerDownload').download = name || 'document';
        document.getElementById('docViewerOpen').href = url;
        document.getElementById('docViewerActions').style.display = 'none';

        new bootstrap.Modal(document.getElementById('docViewerModal')).show();
    }

    function _fetchDocs(userId, loadingId, contentId, reloadFnName) {
        var loadEl = document.getElementById(loadingId);
        var contEl = document.getElementById(contentId);
        loadEl.style.display = 'block';
        contEl.style.display = 'none';
        contEl.innerHTML = '';
        fetch('/admin/student/' + userId + '/documents-for-assess', {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loadEl.style.display = 'none';
            contEl.innerHTML = buildDocCards(data.documents || [], reloadFnName);
            contEl.style.display = 'block';
        })
        .catch(function() {
            loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load documents.</span>';
        });
    }

    function loadSmDocuments() { _fetchDocs(_smAssessStudentId, 'sm-docs-loading', 'sm-docs-content', 'reloadSmDocs'); }
    function loadSvDocuments(userId) { _fetchDocs(userId, 'sv-docs-loading', 'sv-docs-content', 'reloadSvDocs'); }

    // ── Guidance tab (assessment modal) — informational only, does not block anything ──
    function loadSmGuidance() {
        var loadEl = document.getElementById('sm-guidance-loading');
        var contEl = document.getElementById('sm-guidance-content');
        loadEl.style.display = 'block';
        contEl.style.display = 'none';
        contEl.innerHTML = '';
        fetch('/admin/student/' + _smAssessStudentId + '/guidance-for-assess', {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loadEl.style.display = 'none';
            contEl.innerHTML = buildGuidanceCards(data.records || []);
            contEl.style.display = 'block';
        })
        .catch(function() {
            loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load guidance records.</span>';
        });
    }

    function buildGuidanceCards(records) {
        var openCount = records.filter(function(r) { return r.status === 'open' || r.status === 'in_progress'; }).length;

        var html = '<div style="background:#e8f4ff;border:1px solid #b8d4f0;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#1a3a6c;display:flex;gap:8px;align-items:flex-start;">' +
            '<i class="bi bi-info-circle-fill" style="margin-top:1px;flex-shrink:0;"></i>' +
            '<span>Guidance history is shown for context only — it does not block promotion or retention. The decision remains yours.</span></div>';

        if (!records.length) {
            html += '<div style="text-align:center;padding:30px;color:#999;">' +
                '<i class="bi bi-flag" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>No guidance records for this student.</div>';
            return html;
        }

        html += '<div style="font-size:11px;font-weight:700;color:' + (openCount > 0 ? '#e65100' : '#2e7d32') + ';margin-bottom:10px;text-transform:uppercase;">' +
            (openCount > 0 ? openCount + ' open/in-progress concern(s)' : 'No open concerns') + ' — ' + records.length + ' total record(s)</div>';

        var statusStyle = {
            open:        { bg: '#fff3e0', color: '#e65100', label: 'Open' },
            in_progress: { bg: '#e3f2fd', color: '#1565c0', label: 'In Progress' },
            resolved:    { bg: '#e8f5e9', color: '#2e7d32', label: 'Resolved' },
            closed:      { bg: '#f5f5f5', color: '#666',    label: 'Closed' },
        };

        html += '<div style="display:flex;flex-direction:column;gap:10px;">';
        records.forEach(function(r) {
            var st = statusStyle[r.status] || statusStyle.closed;
            html += '<div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;">' +
                '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:6px;">' +
                '<div>' +
                '<span style="font-weight:700;font-size:13px;">' + (r.concern_type || 'Concern') + '</span>' +
                '<span style="font-size:11px;color:#999;margin-left:8px;">' + (r.date || '') + '</span>' +
                '</div>' +
                '<span style="font-size:10px;font-weight:700;background:' + st.bg + ';color:' + st.color + ';padding:2px 9px;border-radius:20px;white-space:nowrap;">' + st.label + '</span>' +
                '</div>' +
                '<div style="font-size:12px;color:#444;">' + (r.concern_description || '') + '</div>' +
                (r.counselor ? '<div style="font-size:11px;color:#999;margin-top:6px;">Counselor: ' + r.counselor.name + '</div>' : '') +
                '</div>';
        });
        html += '</div>';
        return html;
    }

    // ── Summer Class tab (assessment modal) — informational only, does not block anything ──
    function loadSmSummer() {
        var loadEl = document.getElementById('sm-summer-loading');
        var contEl = document.getElementById('sm-summer-content');
        loadEl.style.display = 'block';
        contEl.style.display = 'none';
        contEl.innerHTML = '';
        fetch('/admin/student/' + _smAssessStudentId + '/summer-for-assess', {
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loadEl.style.display = 'none';
            contEl.innerHTML = buildSummerCards(data.subjects || []);
            contEl.style.display = 'block';
        })
        .catch(function() {
            loadEl.innerHTML = '<span style="color:#c0392b;font-size:12px;">Failed to load summer class status.</span>';
        });
    }

    function buildSummerCards(subjects) {
        var html = '<div style="background:#e8f4ff;border:1px solid #b8d4f0;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#1a3a6c;display:flex;gap:8px;align-items:flex-start;">' +
            '<i class="bi bi-info-circle-fill" style="margin-top:1px;flex-shrink:0;"></i>' +
            '<span>Summer class status is shown for context only — it does not block promotion or retention. The decision remains yours.</span></div>';

        if (!subjects.length) {
            html += '<div style="text-align:center;padding:30px;color:#999;">' +
                '<i class="bi bi-sun" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>No failing subjects this school year — nothing to remediate.</div>';
            return html;
        }

        var statusStyle = {
            passed:       { bg: '#e8f5e9', color: '#2e7d32', label: 'Passed' },
            failed:       { bg: '#ffebee', color: '#c62828', label: 'Failed Summer Class' },
            enrolled:     { bg: '#e3f2fd', color: '#1565c0', label: 'Enrolled — In Progress' },
            dropped:      { bg: '#f5f5f5', color: '#666',    label: 'Dropped' },
            not_enrolled: { bg: '#fff3e0', color: '#e65100', label: 'Not Enrolled' },
        };

        var clearedCount = subjects.filter(function(s) { return s.summer_status === 'passed'; }).length;
        html += '<div style="font-size:11px;font-weight:700;color:' + (clearedCount === subjects.length ? '#2e7d32' : '#e65100') + ';margin-bottom:10px;text-transform:uppercase;">' +
            clearedCount + ' of ' + subjects.length + ' failing subject(s) cleared</div>';

        html += '<div style="display:flex;flex-direction:column;gap:10px;">';
        subjects.forEach(function(s) {
            var st = statusStyle[s.summer_status] || statusStyle.not_enrolled;
            html += '<div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;">' +
                '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">' +
                '<div>' +
                '<span style="font-weight:700;font-size:13px;">' + s.subject_name + '</span>' +
                '<span style="font-size:11px;color:#c62828;margin-left:8px;">Failing grade: ' + s.failing_grade + '</span>' +
                '</div>' +
                '<span style="font-size:10px;font-weight:700;background:' + st.bg + ';color:' + st.color + ';padding:2px 9px;border-radius:20px;white-space:nowrap;">' + st.label + '</span>' +
                '</div>' +
                (s.summer_grade !== null ? '<div style="font-size:11px;color:#666;margin-top:6px;">Summer class grade: <strong>' + s.summer_grade + '</strong></div>' : '') +
                (s.summer_status === 'not_enrolled' ? buildSummerEnrollAction(s) : '') +
                '</div>';
        });
        html += '</div>';
        return html;
    }

    // Inline enroll shortcut on the assessment modal — same admin-only
    // enrollment as the Summer Class Management "Manage Students" flow, just
    // reachable without leaving the assessment view.
    function buildSummerEnrollAction(s) {
        if (s.available_class_id) {
            return '<button type="button" class="btn-dash btn-primary" style="margin-top:8px;padding:6px 14px;font-size:11.5px;" ' +
                'onclick="enrollFromAssessSummerTab(' + s.available_class_id + ', ' + s.subject_id + ', ' + s.failing_grade + ', this)">' +
                '<i class="bi bi-plus-lg me-1"></i>Enroll in Summer Class</button>';
        }
        return '<div style="margin-top:8px;font-size:11px;color:#999;"><i class="bi bi-info-circle me-1"></i>No summer class created for this subject yet — set one up in Summer Class Management.</div>';
    }

    function enrollFromAssessSummerTab(summerClassId, subjectId, originalGrade, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Enrolling…';

        fetch('/admin/summer-classes/' + summerClassId + '/enroll', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ student_id: _smAssessStudentId, original_grade: originalGrade }),
        })
        .then(r => r.json().then(body => ({ ok: r.ok, body })))
        .then(({ ok, body }) => {
            if (ok && body.success) {
                showAdminToast('Enrolled in summer class.', 'success');
                loadSmSummer(); // refresh this tab so status flips to "Enrolled"
            } else {
                showAdminToast(body.message || 'Failed to enroll.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Enroll in Summer Class';
            }
        })
        .catch(() => {
            showAdminToast('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Enroll in Summer Class';
        });
    }

    function smSelectDecision(val) {
        document.querySelectorAll('input[name="sm-decision"]').forEach(function(r) { r.checked = false; });
        document.querySelector('input[name="sm-decision"][value="' + val + '"]').checked = true;
        document.getElementById('sm-opt-promote').style.borderColor = val === 'promote' ? '#1a7a44' : '#ddd';
        document.getElementById('sm-opt-retain').style.borderColor  = val === 'retain'  ? '#d68910' : '#ddd';
        document.getElementById('sm-next-grade-row').style.display  = val === 'promote' ? 'block' : 'none';
    }

    function confirmSmAssessment() {
        const action = document.querySelector('input[name="sm-decision"]:checked');
        if (!action) { showAdminToast('Please choose Promote or Retain.', 'error'); return; }
        const toSY      = document.getElementById('sm-assess-to-sy').value;
        const remarks   = document.getElementById('sm-assess-remarks').value;
        const nextGrade = action.value === 'promote' ? document.getElementById('sm-next-grade').value : null;

        if (!_smAssessFromSY) {
            showAdminToast('Cannot determine current school year. Please refresh the page.', 'error');
            return;
        }

        fetch('/admin/assessment/' + _smAssessStudentId + '/promote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                action:            action.value,
                from_school_year:  _smAssessFromSY,
                to_school_year:    toSY,
                next_grade:        nextGrade,
                remarks:           remarks
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAdminToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('smAssessModal')).hide();
                setTimeout(() => location.reload(), 800);
            } else {
                showAdminToast(data.message || 'Assessment failed.', 'error');
            }
        })
        .catch(err => showAdminToast('Network error: ' + (err.message || 'Unknown'), 'error'));
    }

    // ── Bulk Promote (per-grade) ──
    let _bpGrade = null, _bpFromSY = null, _bpToSY = null;

    function bpEscapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function openBulkPromoteModal(grade, gradeLabel) {
        document.getElementById('bp-grade-label').textContent = gradeLabel;
        document.getElementById('bp-loading').style.display = '';
        document.getElementById('bp-loading').innerHTML = '<i class="bi bi-hourglass-split" style="font-size:28px;display:block;margin-bottom:8px;"></i>Checking eligible students...';
        document.getElementById('bp-content').style.display = 'none';
        document.getElementById('bp-confirm-btn').style.display = 'none';
        document.getElementById('bp-excluded-box').style.display = 'none';
        document.getElementById('bp-none-eligible').style.display = 'none';

        _bpGrade  = grade;
        _bpFromSY = '<?php echo e($currentSchoolYear); ?>';
        _bpToSY   = '<?php echo e($assessNextSchoolYear); ?>';
        document.getElementById('bp-to-sy').value = _bpToSY;

        const params = new URLSearchParams({ grade: _bpGrade, from_school_year: _bpFromSY });
        fetch('/admin/assessment/bulk-preview?' + params.toString(), {
            headers: { 'Accept': 'application/json' }, credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('bp-loading').style.display = 'none';
            document.getElementById('bp-content').style.display = '';

            document.getElementById('bp-eligible-count').textContent = data.eligible_count;
            document.getElementById('bp-eligible-names').innerHTML = data.eligible.length
                ? data.eligible.map(function(s) {
                    const warn = (s.warnings && s.warnings.length)
                        ? ' <span style="color:#a16207;">⚠ ' + bpEscapeHtml(s.warnings.join(', ')) + '</span>'
                        : '';
                    return '<div>' + bpEscapeHtml(s.name) + warn + '</div>';
                }).join('')
                : '—';

            if (data.excluded_count > 0) {
                document.getElementById('bp-excluded-box').style.display = '';
                document.getElementById('bp-excluded-count').textContent = data.excluded_count;
                document.getElementById('bp-excluded-list').innerHTML = data.excluded.map(function(e) {
                    return '<div style="margin-bottom:4px;"><strong>' + bpEscapeHtml(e.name) + '</strong>: ' + bpEscapeHtml(e.reasons.join(', ')) + '</div>';
                }).join('');
            }

            if (data.eligible_count > 0) {
                document.getElementById('bp-confirm-btn').style.display = '';
            } else {
                document.getElementById('bp-none-eligible').style.display = '';
            }
        })
        .catch(err => {
            document.getElementById('bp-loading').innerHTML = '<span style="color:var(--red);">Error loading preview. Please try again.</span>';
            console.error(err);
        });

        new bootstrap.Modal(document.getElementById('bulkPromoteModal')).show();
    }

    function confirmBulkPromote() {
        const btn = document.getElementById('bp-confirm-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        fetch('/admin/assessment/bulk-promote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                grade: _bpGrade,
                from_school_year: _bpFromSY,
                to_school_year: _bpToSY
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAdminToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('bulkPromoteModal')).hide();
                setTimeout(() => location.reload(), 900);
            } else {
                showAdminToast(data.message || 'Bulk promotion failed.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Confirm Promotion';
            }
        })
        .catch(err => {
            showAdminToast('Network error: ' + (err.message || 'Unknown'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Confirm Promotion';
        });
    }

    // ── Auto-Advance (Nursery/Kindergarten — no checks) ──
    let _aaGrade = null, _aaFromSY = null, _aaToSY = null;

    function openAutoAdvanceModal(grade, gradeLabel) {
        document.getElementById('aa-grade-label').textContent = gradeLabel;
        document.getElementById('aa-loading').style.display = '';
        document.getElementById('aa-content').style.display = 'none';
        document.getElementById('aa-confirm-btn').style.display = 'none';
        document.getElementById('aa-none').style.display = 'none';

        _aaGrade  = grade;
        _aaFromSY = '<?php echo e($currentSchoolYear); ?>';
        _aaToSY   = '<?php echo e($assessNextSchoolYear); ?>';
        document.getElementById('aa-to-sy').value = _aaToSY;

        const params = new URLSearchParams({ grade: _aaGrade, from_school_year: _aaFromSY, to_school_year: _aaToSY });
        fetch('/admin/assessment/auto-advance-preview?' + params.toString(), {
            headers: { 'Accept': 'application/json' }, credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('aa-loading').style.display = 'none';
            document.getElementById('aa-content').style.display = '';
            document.getElementById('aa-count').textContent = data.count;
            document.getElementById('aa-names').textContent = data.students.map(s => s.name).join(', ') || '—';

            if (data.count > 0) {
                document.getElementById('aa-confirm-btn').style.display = '';
            } else {
                document.getElementById('aa-none').style.display = '';
            }
        })
        .catch(err => {
            document.getElementById('aa-loading').innerHTML = '<span style="color:var(--red);">Error loading preview. Please try again.</span>';
            console.error(err);
        });

        new bootstrap.Modal(document.getElementById('autoAdvanceModal')).show();
    }

    function confirmAutoAdvance() {
        const btn = document.getElementById('aa-confirm-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        fetch('/admin/assessment/auto-advance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                grade: _aaGrade,
                from_school_year: _aaFromSY,
                to_school_year: _aaToSY
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAdminToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('autoAdvanceModal')).hide();
                setTimeout(() => location.reload(), 900);
            } else {
                showAdminToast(data.message || 'Auto-advance failed.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Confirm Advancement';
            }
        })
        .catch(err => {
            showAdminToast('Network error: ' + (err.message || 'Unknown'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Confirm Advancement';
        });
    }

</script>

<!-- Installment Details Modal -->
<div class="modal fade" id="installmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #2c5282);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-calendar-check me-2"></i>Installment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled">
                <div id="installmentModalContent">
                    <!-- Content populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Document Modal -->
<div class="modal fade" id="rejectDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #e74c3c, #c0392b);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-x-circle-fill me-2"></i>Reject Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled">
                <form id="rejectDocForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_method" value="PUT">
                    <div class="mb-3">
                        <label class="dash-form-label"><i class="bi bi-chat-left-text me-1"></i>Rejection Reason</label>
                        <textarea name="reject_reason" class="dash-form-control" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-dash btn-danger" onclick="submitRejectDoc()"><i class="bi bi-x-lg me-1"></i>Reject Document</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Update Modal -->
<div class="modal fade" id="paymentUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">

            
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;top:16px;right:16px;"></button>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:46px;height:46px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-pencil-square" style="font-size:20px;color:#fff;"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:16px;font-weight:800;color:#fff;">Plan Payment</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.8);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" id="pu-student-label">—</div>
                    </div>
                </div>
                
                <div style="margin-top:14px;background:rgba(255,255,255,0.15);border-radius:12px;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.5px;">Remaining Balance</div>
                        <div style="font-size:10px;color:rgba(255,255,255,0.6);margin-top:1px;" id="pu-plan-label">—</div>
                    </div>
                    <div style="font-size:22px;font-weight:800;color:#fff;" id="pu-balance-display">₱—</div>
                </div>
            </div>

            
            <div style="padding:20px 24px;background:#f8faff;">
                <input type="hidden" id="pay-enrollment-id">
                <input type="hidden" id="pay-student-name">

                
                <div id="payment-breakdown-section" style="margin-bottom:18px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                        <i class="bi bi-receipt-cutoff me-1" style="color:#1d4ed8;"></i> Payment Plan Summary
                    </div>
                    <div id="payment-breakdown-content" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;"></div>
                </div>

                
                <input type="hidden" id="pay-amount" value="">
                <input type="hidden" id="pay-reference" value="">
                <input type="hidden" id="pay-method" value="">
                <select id="pay-status" style="display:none;">
                    <option value="pending">Pending</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>

                
                <div style="margin-bottom:6px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
                        <i class="bi bi-ui-checks me-1" style="color:#1d4ed8;"></i> Payment Plan Type
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <button type="button" id="pu-plan-full" onclick="puSetPlan('full')"
                            style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 12px;border:2px solid #e2e8f0;border-radius:14px;background:#fff;cursor:pointer;font-family:inherit;transition:all .2s;text-align:center;">
                            <div style="width:44px;height:44px;border-radius:12px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-cash-coin" style="font-size:20px;color:#d97706;"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e3a5f;">Full Payment</div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Pay all at once (Option A)</div>
                            </div>
                        </button>
                        <button type="button" id="pu-plan-installment" onclick="puSetPlan('installment')"
                            style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 12px;border:2px solid #e2e8f0;border-radius:14px;background:#fff;cursor:pointer;font-family:inherit;transition:all .2s;text-align:center;">
                            <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-calendar-week-fill" style="font-size:20px;color:#1d4ed8;"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e3a5f;">Installment</div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Monthly plan (B / C / D)</div>
                            </div>
                        </button>
                    </div>
                </div>

                
                <div style="display:flex;justify-content:space-between;gap:10px;">
                    <button type="button" data-bs-dismiss="modal"
                        style="display:flex;align-items:center;gap:6px;padding:11px 18px;background:#f0f4ff;color:#1d4ed8;border:1.5px solid #bfdbfe;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                        <i class="bi bi-x-lg"></i>Cancel
                    </button>
                    <button type="button" onclick="savePaymentUpdate()"
                        style="flex:1;max-width:180px;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
                        <i class="bi bi-check-circle-fill"></i>Save Payment
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Screenshot Preview Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #2c5282);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-image me-2"></i>Payment Screenshot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="text-align:center; padding:24px;">
                <img id="screenshotImage" src="" alt="Payment Screenshot" style="max-width:100%; max-height:70vh; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                <div style="margin-top:16px;">
                    <a id="screenshotDownload" href="" download class="btn-dash btn-secondary" style="text-decoration:none;">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                    <a id="screenshotOpen" href="" target="_blank" class="btn-dash btn-primary" style="text-decoration:none; margin-left:8px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div class="modal fade" id="docViewerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), var(--blue-light));">
                <div style="display:flex; align-items:center; gap:10px; flex:1; min-width:0;">
                    <div style="background:rgba(255,255,255,0.2); border-radius:8px; padding:6px 10px; flex-shrink:0;">
                        <i class="bi bi-file-earmark-text" id="docViewerIcon" style="color:#fff; font-size:16px;"></i>
                    </div>
                    <div style="min-width:0;">
                        <h5 class="modal-title" id="docViewerTitle" style="color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:360px;">Document</h5>
                        <div id="docViewerSubtitle" style="font-size:12px; color:rgba(255,255,255,0.8);"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:0; min-height:200px;">
                <div id="docViewerImgWrap" style="text-align:center; padding:24px;">
                    <img id="docViewerImg" src="" alt="Document" style="max-width:100%; max-height:65vh; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                </div>
                <div id="docViewerPdfWrap" style="display:none;">
                    <iframe id="docViewerPdf" src="" style="width:100%; height:70vh; border:none; border-radius:0 0 8px 8px;"></iframe>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border); padding:14px 20px; gap:8px; flex-wrap:wrap; justify-content:space-between;">
                <div id="docViewerActions" style="display:flex; gap:8px; flex-wrap:wrap;">
                    <form id="docViewerApproveForm" method="POST" action="" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-dash btn-success" style="display:flex; align-items:center; gap:6px;" onclick="confirmAndSubmit(event, this.closest('form'), 'Approve this document?', {title:'Approve Document', btnText:'Approve', btnClass:'btn btn-success', btnIcon:'bi-check-circle-fill', headerBg:'linear-gradient(135deg,#27ae60,#1e8449)', headerIcon:'bi-check-circle-fill'})">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    </form>
                    <button type="button" id="docViewerRejectBtn" class="btn-dash btn-danger js-doc-reject" style="display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-x-lg"></i> Reject
                    </button>
                </div>
                <div style="display:flex; gap:8px;">
                    <a id="docViewerDownload" href="" download class="btn-dash btn-secondary" style="text-decoration:none; display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <a id="docViewerOpen" href="" target="_blank" class="btn-dash btn-primary" style="text-decoration:none; display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #2c5282);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-credit-card me-2"></i>Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:24px;">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-person me-2"></i>Student Information</h6>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Student Name</div>
                            <div id="adminDetailStudentName" style="font-weight:600;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Email</div>
                            <div id="adminDetailStudentEmail" style="font-weight:500;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Grade Level</div>
                            <div id="adminDetailGradeLevel" style="font-weight:500;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-receipt me-2"></i>Payment Information</h6>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Payment ID</div>
                            <div id="adminDetailPaymentId" style="font-weight:600;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Description</div>
                            <div id="adminDetailDescription" style="font-weight:500;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Payment Method</div>
                            <div id="adminDetailMethod" style="font-weight:500;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Submitted On</div>
                            <div id="adminDetailSubmitted" style="font-weight:500;"></div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:var(--muted);">Status</div>
                            <div id="adminDetailStatus"></div>
                        </div>
                    </div>
                </div>

                <hr style="margin:20px 0; border-color:var(--border);">

                <!-- Installment Details -->
                <div id="adminInstallmentDetailsSection" style="display:none;">
                    <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-calendar-check me-2"></i>Installment Details</h6>
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px;">
                        <div style="background:#e3f2fd; padding:16px; border-radius:8px; text-align:center;">
                            <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Month</div>
                            <div id="adminDetailMonthName" style="font-weight:700; color:var(--blue); font-size:16px;"></div>
                        </div>
                        <div style="background:#e8f5e9; padding:16px; border-radius:8px; text-align:center;">
                            <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Base Amount</div>
                            <div id="adminDetailBaseAmount" style="font-weight:700; color:var(--green); font-size:16px;"></div>
                        </div>
                        <div style="background:#fff3e0; padding:16px; border-radius:8px; text-align:center;">
                            <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Total with Fees</div>
                            <div id="adminDetailTotalAmount" style="font-weight:700; color:#f57c00; font-size:16px;"></div>
                        </div>
                    </div>
                    <div id="adminLateFeeRow" style="margin-top:12px; padding:12px; background:#ffebee; border-radius:8px; display:none;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:#c62828;"><i class="bi bi-exclamation-triangle me-2"></i>Late Fee Applied</span>
                            <span id="adminDetailLateFee" style="font-weight:700; color:#c62828;"></span>
                        </div>
                    </div>
                </div>

                <!-- Enrollment Summary -->
                <div id="adminEnrollmentSummarySection" style="display:none; margin-top:20px;">
                    <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-person-badge me-2"></i>Enrollment Summary</h6>
                    <div style="background:#f8f9fa; padding:16px; border-radius:8px;">
                        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; text-align:center;">
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Payment Plan</div>
                                <div id="adminDetailPaymentOption" style="font-weight:700; color:var(--blue);"></div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Total Fee</div>
                                <div id="adminDetailTotalFee" style="font-weight:700;"></div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Amount Paid</div>
                                <div id="adminDetailAmountPaid" style="font-weight:700; color:var(--green);"></div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Balance</div>
                                <div id="adminDetailBalance" style="font-weight:700; color:#e65100;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Information -->
                <div id="adminReviewInfoSection" style="display:none; margin-top:20px;">
                    <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-check2-square me-2"></i>Review Information</h6>
                    <div style="background:#f8f9fa; padding:16px; border-radius:8px;">
                        <div style="margin-bottom:8px;">
                            <span style="color:var(--muted);">Reviewed By:</span>
                            <span id="adminDetailReviewedBy" style="font-weight:600; margin-left:8px;"></span>
                        </div>
                        <div>
                            <span style="color:var(--muted);">Reviewed On:</span>
                            <span id="adminDetailReviewedAt" style="font-weight:600; margin-left:8px;"></span>
                        </div>
                    </div>
                </div>

                <!-- Screenshot Preview in Modal -->
                <div id="adminScreenshotPreviewSection" style="display:none; margin-top:20px;">
                    <h6 style="color:var(--blue); font-weight:600; margin-bottom:16px;"><i class="bi bi-image me-2"></i>Payment Screenshot</h6>
                    <div style="text-align:center; padding:16px; background:#f8f9fa; border-radius:8px;">
                        <img id="adminDetailScreenshot" src="" alt="Payment Screenshot" style="max-width:100%; max-height:200px; border-radius:8px; cursor:pointer;" onclick="showScreenshotModal(this.src)">
                        <div style="margin-top:12px;">
                            <button type="button" class="btn-dash btn-secondary" onclick="showScreenshotModal(document.getElementById('adminDetailScreenshot').src)">
                                <i class="bi bi-eye me-1"></i>View Full Size
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="adminDetailViewFullPage" href="" class="btn-dash btn-primary" style="text-decoration:none;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>View Full Page
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Subject Modal (Add / Edit) -->
<!-- Subject Modal (Add / Edit) -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled" style="border-radius:16px; overflow:hidden; border:none;">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #1a3a6c, #1565c0); padding:18px 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-book-fill" style="font-size:18px; color:#fff;"></i>
                    </div>
                    <h5 class="modal-title" id="subjectModalTitle" style="color:#fff; font-weight:700; margin:0; font-size:16px;"><i class="bi bi-plus-circle me-2"></i>Add Subject</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:24px;">
                <input type="hidden" id="subj-id">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="dash-form-label"><i class="bi bi-upc me-1" style="color:var(--blue-light);"></i>Subject Code <span class="text-danger">*</span></label>
                        <input type="text" id="subj-code" class="dash-form-control" placeholder="e.g. MATH101" style="font-family:monospace; font-size:13px; letter-spacing:0.5px;">
                    </div>
                    <div class="col-md-7">
                        <label class="dash-form-label"><i class="bi bi-mortarboard me-1" style="color:var(--blue-light);"></i>Grade Level <span class="text-danger">*</span></label>
                        <select id="subj-grade" class="dash-form-control">
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
                </div>
                <div class="mb-3">
                    <label class="dash-form-label"><i class="bi bi-book me-1" style="color:var(--blue-light);"></i>Subject Name <span class="text-danger">*</span></label>
                    <input type="text" id="subj-name" class="dash-form-control" placeholder="e.g. Mathematics">
                </div>
                <div class="mb-3">
                    <label class="dash-form-label"><i class="bi bi-card-text me-1" style="color:var(--blue-light);"></i>Description</label>
                    <input type="text" id="subj-desc" class="dash-form-control" placeholder="Optional short description">
                </div>
                <div class="mb-0">
                    <label class="dash-form-label"><i class="bi bi-toggle-on me-1" style="color:var(--blue-light);"></i>Status</label>
                    <div style="display:flex; gap:10px;">
                        <label style="display:flex; align-items:center; gap:8px; padding:10px 16px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer; flex:1; transition:all .2s;" id="subj-active-label-1">
                            <input type="radio" name="subj-active-radio" id="subj-active-1" value="1" checked style="display:none;">
                            <span style="width:14px; height:14px; border-radius:50%; background:var(--green); display:inline-block; box-shadow:0 0 0 3px rgba(39,174,96,0.2);"></span>
                            <span style="font-size:13px; font-weight:600; color:var(--green);">Active</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; padding:10px 16px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer; flex:1; transition:all .2s;" id="subj-active-label-0">
                            <input type="radio" name="subj-active-radio" id="subj-active-0" value="0" style="display:none;">
                            <span style="width:14px; height:14px; border-radius:50%; background:#adb5bd; display:inline-block;"></span>
                            <span style="font-size:13px; font-weight:600; color:#6c757d;">Inactive</span>
                        </label>
                    </div>
                    <input type="hidden" id="subj-active" value="1">
                </div>
            </div>
            <div class="modal-footer modal-footer-styled" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal" style="padding:9px 20px;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn-dash btn-primary" id="subjectSaveBtn" onclick="saveSubject()" style="padding:9px 24px; font-weight:700;">
                    <i class="bi bi-check-lg me-1"></i>Save Subject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Section Modal (Add / Edit) -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled" style="border-radius:16px; overflow:hidden; border:none;">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #1b7a3e, #2e7d32); padding:18px 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-diagram-3-fill" style="font-size:18px; color:#fff;"></i>
                    </div>
                    <h5 class="modal-title" id="sectionModalTitle" style="color:#fff; font-weight:700; margin:0; font-size:16px;"><i class="bi bi-plus-circle me-2"></i>Add Section</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:24px;">
                <input type="hidden" id="sec-id">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-tag me-1" style="color:var(--green);"></i>Section Name <span class="text-danger">*</span></label>
                        <input type="text" id="sec-name" class="dash-form-control" placeholder="e.g. Section A">
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-mortarboard me-1" style="color:var(--green);"></i>Grade Level <span class="text-danger">*</span></label>
                        <select id="sec-grade" class="dash-form-control">
                            <option value="">— Select grade —</option>
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
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-door-open me-1" style="color:var(--green);"></i>Room Number</label>
                        <input type="text" id="sec-room" class="dash-form-control" placeholder="e.g. Room 101">
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-people me-1" style="color:var(--green);"></i>Max Students <span class="text-danger">*</span></label>
                        <div style="position:relative;">
                            <input type="number" id="sec-max" class="dash-form-control" min="1" max="200" value="30" placeholder="30">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; color:var(--muted);">students</span>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-calendar me-1" style="color:var(--green);"></i>School Year <span class="text-danger">*</span></label>
                        <select id="sec-sy" class="dash-form-control">
                            <?php
                                $cSY = now()->month >= 6 ? now()->year : now()->year - 1;
                                for ($y = 2020; $y <= $cSY + 1; $y++) {
                                    $syOpt = $y . '-' . ($y + 1);
                                    $selOpt = $y === $cSY + 1 ? 'selected' : '';
                                    echo "<option value=\"$syOpt\" $selOpt>$syOpt</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-toggle-on me-1" style="color:var(--green);"></i>Status</label>
                        <select id="sec-active" class="dash-form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal" style="padding:9px 20px;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn-dash btn-primary" onclick="saveSectionRecord()" style="padding:9px 24px; font-weight:700;">
                    <i class="bi bi-check-lg me-1"></i>Save Section
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal (Add / Edit) -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled" style="border-radius:16px; overflow:hidden; border:none;">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #4a1d8a, #6f42c1); padding:18px 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-calendar3-week-fill" style="font-size:18px; color:#fff;"></i>
                    </div>
                    <h5 class="modal-title" id="scheduleModalTitle" style="color:#fff; font-weight:700; margin:0; font-size:16px;"><i class="bi bi-plus-circle me-2"></i>Add Schedule</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:24px;">
                <input type="hidden" id="sched-id">

                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-layers me-1" style="color:#6f42c1;"></i>Grade Level <span class="text-danger">*</span></label>
                        <select id="sched-grade-level" class="dash-form-control" onchange="onScheduleGradeLevelChange(this.value)">
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
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-diagram-3 me-1" style="color:#6f42c1;"></i>Section <span class="text-danger">*</span></label>
                        <select id="sched-section" class="dash-form-control" onchange="onScheduleSectionChange(this.value)">
                            <option value="">— Select Grade First —</option>
                        </select>
                    </div>
                </div>

                
                <div class="mb-3">
                    <label class="dash-form-label"><i class="bi bi-book me-1" style="color:#6f42c1;"></i>Subject <span class="text-danger">*</span></label>
                    <select id="sched-subject" class="dash-form-control" onchange="onScheduleSubjectChange(document.getElementById('sched-section').value, this.value)">
                        <option value="">— Select Section First —</option>
                    </select>
                </div>

                
                <div class="mb-3">
                    <label class="dash-form-label"><i class="bi bi-person-badge me-1" style="color:#6f42c1;"></i>Teacher <span style="color:var(--muted); font-size:11px;">(optional)</span></label>
                    <select id="sched-teacher" class="dash-form-control">
                        <option value="">— None / TBA —</option>
                    </select>
                    <div style="font-size:11px; color:var(--muted); margin-top:4px;"><i class="bi bi-info-circle me-1"></i>All active teachers shown. Advisory/homeroom teachers for this section are marked with ✓.</div>
                </div>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="dash-form-label"><i class="bi bi-calendar-week me-1" style="color:#6f42c1;"></i>Day of Week <span class="text-danger">*</span></label>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;" id="sched-day-btns">
                            <?php $__currentLoopData = ['Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $short=>$full): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="sched-day-btn" data-day="<?php echo e($full); ?>"
                                style="padding:7px 12px; border:2px solid #e2e8f0; border-radius:8px; background:#fff; font-size:12px; font-weight:700; cursor:pointer; transition:all .15s; color:var(--muted);"><?php echo e($short); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <input type="hidden" id="sched-day" value="">
                    </div>
                    <div class="col-md-5">
                        <label class="dash-form-label"><i class="bi bi-layers me-1" style="color:#6f42c1;"></i>Term <span class="text-danger">*</span></label>
                        <select id="sched-term" class="dash-form-control">
                            <option value="">— Select Term —</option>
                            <option value="1">1st Term</option>
                            <option value="2">2nd Term</option>
                            <option value="3">3rd Term</option>
                        </select>
                    </div>
                </div>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="dash-form-label"><i class="bi bi-clock me-1" style="color:#6f42c1;"></i>Start Time <span class="text-danger">*</span></label>
                        <input type="time" id="sched-start" class="dash-form-control" value="07:30">
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label"><i class="bi bi-clock-fill me-1" style="color:#6f42c1;"></i>End Time <span class="text-danger">*</span></label>
                        <input type="time" id="sched-end" class="dash-form-control" value="08:30">
                    </div>
                    <div class="col-md-4">
                        <label class="dash-form-label"><i class="bi bi-geo-alt me-1" style="color:#6f42c1;"></i>Room</label>
                        <select id="sched-room" class="dash-form-control">
                            <option value="">— Select Grade First —</option>
                        </select>
                    </div>
                </div>

                
                <div class="mb-0">
                    <label class="dash-form-label"><i class="bi bi-toggle-on me-1" style="color:#6f42c1;"></i>Status</label>
                    <select id="sched-active" class="dash-form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <button type="button" id="sched-delete-btn" class="btn-dash" style="display:none; padding:9px 18px; background:#fff0f0; color:var(--red); border:1px solid #fbc8c8; border-radius:8px; font-size:13px;" onclick="deleteSchedule()">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
                <div style="display:flex; gap:10px; margin-left:auto;">
                    <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal" style="padding:9px 20px;">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn-dash btn-primary" onclick="saveSchedule()" style="padding:9px 24px; font-weight:700; background:linear-gradient(135deg,#4a1d8a,#6f42c1); border:none;">
                        <i class="bi bi-check-lg me-1"></i>Save Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Term Schedule Modal -->
<div class="modal fade" id="copyTermModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-styled" style="border-radius:16px; overflow:hidden; border:none;">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #4a1d8a, #6f42c1); padding:18px 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-copy" style="font-size:16px; color:#fff;"></i>
                    </div>
                    <h5 class="modal-title" style="color:#fff; font-weight:700; margin:0; font-size:16px;">Copy Term Schedule</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:24px;">
                <div style="font-size:12.5px; color:var(--muted); background:#f8f9fa; border-radius:8px; padding:12px 14px; margin-bottom:18px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Duplicates every schedule block from the source term into the target term —
                    same sections, subjects, teachers, and rooms. Blocks that would create a
                    conflict in the target term are skipped and listed, not silently overwritten.
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-calendar-minus me-1" style="color:#6f42c1;"></i>Copy From <span class="text-danger">*</span></label>
                        <select id="copyterm-source" class="dash-form-control">
                            <option value="1">1st Term</option>
                            <option value="2">2nd Term</option>
                            <option value="3">3rd Term</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="dash-form-label"><i class="bi bi-calendar-plus me-1" style="color:#6f42c1;"></i>Copy To <span class="text-danger">*</span></label>
                        <select id="copyterm-target" class="dash-form-control">
                            <option value="1">1st Term</option>
                            <option value="2" selected>2nd Term</option>
                            <option value="3">3rd Term</option>
                        </select>
                    </div>
                </div>
                <div class="form-check" style="margin-bottom:4px;">
                    <input class="form-check-input" type="checkbox" id="copyterm-replace">
                    <label class="form-check-label" for="copyterm-replace" style="font-size:13px;">
                        Clear the target term's existing schedule first
                        <div style="font-size:11px; color:var(--muted);">Leave unchecked to add alongside whatever's already in the target term.</div>
                    </label>
                </div>
                <div id="copyterm-result" style="display:none; margin-top:16px;"></div>
            </div>
            <div class="modal-footer modal-footer-styled" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal" style="padding:9px 20px;">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" id="copyterm-submit-btn" class="btn-dash btn-primary" onclick="submitCopyTerm()" style="padding:9px 24px; font-weight:700; background:linear-gradient(135deg,#4a1d8a,#6f42c1); border:none;">
                    <i class="bi bi-copy me-1"></i>Copy Schedule
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Section Subjects Modal -->
<div class="modal fade" id="viewSubjectsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #1565c0);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-book-fill me-2"></i>Subjects — <span id="viewSubjectsSectionName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled">
                <input type="hidden" id="viewSubjectsSectionId">
                <div class="mb-2" style="font-size:13px; color:var(--muted);">
                    Grade Level: <strong id="viewSubjectsGradeLevel"></strong>
                </div>
                <div id="viewSubjectsList" style="display:flex; flex-wrap:wrap; gap:6px; padding:14px; background:#f8f9fa; border-radius:8px; min-height:60px;">
                    <span style="color:var(--muted); font-size:12px;">Loading...</span>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Section Students Modal -->
<div class="modal fade" id="viewSectionStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--green), #2e7d32);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-people-fill me-2"></i>Students — <span id="viewSectionDisplay"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:0;">
                <input type="hidden" id="viewSectionId">
                <input type="hidden" id="viewSectionName">
                
                <div id="viewSectionCapacityRow" style="padding:12px 16px; background:var(--pale-bg,#f8fafc); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; color:var(--muted); font-weight:600;">CAPACITY</span>
                    <div style="flex:1; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                        <div id="viewSectionCapacityBar" style="height:100%; background:var(--green); border-radius:4px; width:0%; transition:width .4s;"></div>
                    </div>
                    <span id="viewSectionCapacityText" style="font-size:12px; font-weight:700; color:var(--text); white-space:nowrap;">—</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>LRN</th>
                                <th>Name</th>
                                <th>Grade</th>
                                <th>Payment</th>
                                <th style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="viewStudentsTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--muted);">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Section Modal -->
<div class="modal fade" id="transferSectionModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #1565c0);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-arrow-left-right me-2"></i>Transfer Student to Another Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled">
                <input type="hidden" id="transferStudentId">
                <input type="hidden" id="transferFromSectionId">
                <div class="mb-3">
                    <label class="dash-form-label">Student</label>
                    <div style="font-weight:700; font-size:14px; padding:10px 12px; background:var(--pale-bg,#f8fafc); border-radius:8px; border:1px solid var(--border);" id="transferStudentDisplay"></div>
                </div>
                <div class="mb-1">
                    <label class="dash-form-label">Transfer to Section <span style="color:var(--red);">*</span></label>
                    <select id="transferTargetSection" class="dash-form-control" onchange="document.getElementById('transferBtn').disabled = !this.value;">
                        <option value="">Select target section...</option>
                    </select>
                </div>
                <div style="font-size:11px; color:var(--muted); margin-top:6px;"><i class="bi bi-info-circle me-1"></i>Only sections of the same grade level with available space are shown.</div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="transferBtn" class="btn-dash btn-primary" disabled onclick="confirmTransfer()">
                    <i class="bi bi-arrow-left-right me-1"></i> Transfer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Subjects to Section Modal -->
<div class="modal fade" id="assignSubjectsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #1565c0);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-book-half me-2"></i>Assign Subjects — <span id="assign-section-display"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled" style="padding:0;">
                <input type="hidden" id="assign-section-id">
                <input type="hidden" id="assign-section-name">
                <div style="overflow-x:auto; max-height:420px;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th style="width:44px; text-align:center;">
                                    <input type="checkbox" id="assign-check-all" title="Select all" onchange="document.querySelectorAll('.subject-checkbox').forEach(function(cb){ cb.checked = this.checked; }.bind(this))">
                                </th>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Grade Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $allSubjectsForAssign = \App\Models\Subject::where('is_active', true)->orderBy('grade_level')->orderBy('name')->get(); ?>
                            <?php $__empty_1 = true; $__currentLoopData = $allSubjectsForAssign; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="subject-checkbox" value="<?php echo e($ms->id); ?>" data-name="<?php echo e($ms->name); ?>" data-code="<?php echo e($ms->code); ?>">
                                </td>
                                <td style="font-size:12px; font-family:monospace;"><?php echo e($ms->code); ?></td>
                                <td style="font-weight:600;"><?php echo e($ms->name); ?></td>
                                <td><span class="grade-chip" style="font-size:11px;"><?php echo e(ucfirst(str_replace(['grade','_'],[' Grade ',''], $ms->grade_level ?? 'All'))); ?></span></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--muted);">No subjects found. Add subjects first in Subject Management.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-dash btn-primary" onclick="saveSectionSubjects()"><i class="bi bi-check-lg me-1"></i>Save Assignments</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Student to Section Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-styled">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--green), #2e7d32);">
                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-person-plus-fill me-2"></i>Add Student — <span id="addStudentSectionDisplay"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-styled">
                <input type="hidden" id="addStudentSectionId">
                <input type="hidden" id="addStudentSectionName">
                <div class="mb-3">
                    <input type="text" id="studentSearchInput" class="dash-form-control" placeholder="Search by name, LRN, or email..." oninput="filterStudentList()">
                </div>
                <div style="overflow-x:auto; max-height:380px;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>LRN</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Grade</th>
                                <th>Payment</th>
                                <th>Section</th>
                                <th style="text-align:center;">Select</th>
                            </tr>
                        </thead>
                        <tbody id="studentListTableBody">
                            <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--muted);">Loading students...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="addStudentBtn" class="btn-dash btn-primary" disabled onclick="addStudentToSection()">
                    <i class="bi bi-plus-lg me-1"></i>Add Selected Student
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/ph-address.js"></script>
<script>
    // Walk-in enrollment address cascade
    PHAddress.initCascade({
        region:   'walkin-region',
        province: 'walkin-province',
        city:     'walkin-city',
        barangay: 'walkin-barangay',
    });

    // ══ Chart.js Initialization ══
    const _CC = {
        blue:'#1a3a6c',mid:'#2471a3',gold:'#c5a059',green:'#16a34a',
        red:'#dc2626',gray:'#94a3b8',
        bpale:'rgba(26,58,108,.08)',mpale:'rgba(36,113,163,.12)'
    };
    Chart.defaults.font.family = "'Open Sans',sans-serif";
    Chart.defaults.font.size   = 11;

    // Dashboard: enrollment trend
    (function(){
        const el = document.getElementById('adminEnrollTrend');
        if (!el) return;
        new Chart(el, {
            type:'line',
            data:{
                labels: <?php echo json_encode($chMonths ?? [], 15, 512) ?>,
                datasets:[{label:'Enrollments',data:<?php echo json_encode($chEnroll ?? [], 15, 512) ?>,
                    borderColor:_CC.blue,backgroundColor:_CC.bpale,
                    borderWidth:2,pointRadius:4,tension:0.4,fill:true}]
            },
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{stepSize:1}},x:{grid:{display:false}}}}
        });
    })();

    // Dashboard: payment status doughnut
    (function(){
        const el = document.getElementById('adminPayDoughnut');
        if (!el) return;
        new Chart(el, {
            type:'doughnut',
            data:{labels:['Paid','Partial','Unpaid'],
                datasets:[{data:[<?php echo e($chPaid??0); ?>,<?php echo e($chPartial??0); ?>,<?php echo e($chUnpaid??0); ?>],
                    backgroundColor:[_CC.green,_CC.gold,_CC.red],borderWidth:0,hoverOffset:4}]},
            options:{responsive:true,maintainAspectRatio:false,cutout:'68%',
                plugins:{legend:{position:'bottom',labels:{padding:12,font:{size:11}}}}}
        });
    })();

    // Reports section charts — called after every Reports (re)load, not
    // just once. Reports' data used to be baked into this function's own
    // Blade-templated source at initial page compile time (reading
    // controller variables directly via Blade output syntax); now that
    // Reports loads on demand via AJAX, those variables don't exist on the main page at all
    // — the data instead comes from a JSON island reports-content.blade.php
    // renders alongside itself (#rpt-chart-data), read here instead. This
    // also fixes a latent staleness bug: the old single-shot
    // window._rptChartsInit guard meant these charts never updated after
    // the very first render even when the underlying data changed —
    // destroying and recreating per canvas on every call fixes that too.
    function initReportsCharts() {
        const dataEl = document.getElementById('rpt-chart-data');
        if (!dataEl) return;
        let d;
        try { d = JSON.parse(dataEl.textContent); } catch (e) { return; }

        const gradeEl = document.getElementById('rptGradeBar');
        if (gradeEl) {
            Chart.getChart(gradeEl)?.destroy();
            new Chart(gradeEl,{
                type:'bar',
                data:{labels:d.gradeLabels || [],
                    datasets:[{label:'Students',data:d.gradeData || [],
                        backgroundColor:_CC.blue,borderRadius:5,borderSkipped:false}]},
                options:{responsive:true,maintainAspectRatio:false,
                    plugins:{legend:{display:false}},
                    scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'}},x:{grid:{display:false}}}}
            });
        }

        const enrollEl = document.getElementById('rptEnrollStatusDoughnut');
        if (enrollEl) {
            Chart.getChart(enrollEl)?.destroy();
            new Chart(enrollEl,{
                type:'doughnut',
                data:{labels:['Approved','Pending','Declined','Dropped'],
                    datasets:[{data:[d.enrollApproved||0, d.enrollPending||0, d.enrollDeclined||0, d.enrollDropped||0],
                        backgroundColor:[_CC.green,_CC.gold,_CC.red,_CC.gray],borderWidth:0,hoverOffset:4}]},
                options:{responsive:true,maintainAspectRatio:false,cutout:'68%',
                    plugins:{legend:{position:'bottom',labels:{padding:10,font:{size:11}}}}}
            });
        }

        const dailyEl = document.getElementById('rptDailyLine');
        if (dailyEl) {
            Chart.getChart(dailyEl)?.destroy();
            new Chart(dailyEl,{
                type:'line',
                data:{labels:d.dailyLabels || [],
                    datasets:[{label:'Enrollments',data:d.dailyData || [],
                        borderColor:_CC.mid,backgroundColor:_CC.mpale,
                        borderWidth:2,pointRadius:4,tension:0.3,fill:true}]},
                options:{responsive:true,maintainAspectRatio:false,
                    plugins:{legend:{display:false}},
                    scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{stepSize:1}},x:{grid:{display:false}}}}
            });
        }
    }

    // Student edit modal address cascade
    PHAddress.initCascade({
        region:   'se-region',
        province: 'se-province',
        city:     'se-city',
        barangay: 'se-brgy',
    });
</script>

</body>

</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/adminDashboard.blade.php ENDPATH**/ ?>