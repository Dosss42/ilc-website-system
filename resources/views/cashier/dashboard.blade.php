<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cashier Portal — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --blue:       #1a3a6c;
            --blue-mid:   #2471a3;
            --blue-pale:  #e8f0fb;
            --gold:       #c5a059;
            --gold-pale:  #fffbeb;
            --green:      #16a34a;
            --green-pale: #f0fdf4;
            --red:        #dc2626;
            --red-pale:   #fef2f2;
            --sidebar-w:  240px;
            --topbar-h:   62px;
            --radius:     14px;
        }
        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; min-height: 100vh; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1.5px solid #e2e8f0;
            display: flex; align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
        }
        .topbar-brand {
            width: 260px; height: 100%;
            background: linear-gradient(135deg, #162f5c, #1a3a6c);
            display: flex; align-items: center; gap: 11px;
            padding: 0 18px; flex-shrink: 0;
        }
        .topbar-brand .brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-brand .brand-icon i { font-size: 20px; color: #c5a059; }
        .topbar-brand .brand-text { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
        .topbar-brand .brand-sub  { font-size: 10px; color: rgba(255,255,255,.55); }

        .topbar-left {
            display: flex; align-items: center; gap: 10px;
            padding: 0 18px; flex: 1;
        }
        .sidebar-toggle {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1.5px solid #e2e8f0; background: #f8faff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #64748b; font-size: 18px;
            transition: all .15s;
        }
        .sidebar-toggle:hover { border-color: var(--blue-mid); color: var(--blue-mid); }

        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: #f8faff; border: 1.5px solid #e2e8f0;
            border-radius: 10px; padding: 8px 14px;
            flex: 1; max-width: 340px;
        }
        .topbar-search i { color: #94a3b8; font-size: 15px; }
        .topbar-search input {
            border: none; background: transparent; outline: none;
            font-size: 13px; color: #334155; width: 100%;
            font-family: 'Poppins', sans-serif;
        }
        .topbar-search input::placeholder { color: #94a3b8; }

        .topbar-right {
            display: flex; align-items: center; gap: 10px;
            padding-right: 20px; margin-left: auto;
        }
        .topbar-icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            border: 1.5px solid #e2e8f0; background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #64748b; font-size: 18px;
            position: relative; transition: all .15s;
        }
        .topbar-icon-btn:hover { border-color: var(--blue-mid); color: var(--blue-mid); background: var(--blue-pale); }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; border-radius: 50%;
            background: #dc2626; border: 2px solid #fff;
        }
        .user-chip {
            display: flex; align-items: center; gap: 9px;
            padding: 6px 12px 6px 6px;
            border: 1.5px solid #e2e8f0; border-radius: 40px;
            background: #fff; cursor: pointer; transition: all .2s;
        }
        .user-chip:hover { border-color: var(--blue-mid); background: var(--blue-pale); }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #1a3a6c, #2471a3);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
        }
        .user-name { font-size: 13px; font-weight: 600; color: #1e293b; }
        .user-role { font-size: 10px; color: #64748b; }

        /* ── SIDEBAR ── */
        :root { --sidebar-w: 260px; }

        .sidebar {
            position: fixed; top: var(--topbar-h); left: 0;
            width: var(--sidebar-w); bottom: 0;
            background: linear-gradient(180deg, #162f5c 0%, #1a3a6c 60%, #1c3f78 100%);
            overflow-y: auto; overflow-x: hidden;
            z-index: 900;
            display: flex; flex-direction: column;
            padding: 0 0 10px;
            box-shadow: 3px 0 18px rgba(0,0,0,0.18);
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        /* Cashier info card inside sidebar */
        .sidebar-cashier-card {
            margin: 16px 14px 6px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 13px 14px;
            display: flex; align-items: center; gap: 11px;
        }
        .sidebar-cashier-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, #c5a059, #e0b96a);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-cashier-name {
            font-size: 12.5px; font-weight: 700; color: #fff;
            line-height: 1.2; white-space: nowrap; overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-cashier-role {
            font-size: 10px; color: rgba(197,160,89,0.9);
            font-weight: 600; letter-spacing: .3px;
        }
        .sidebar-shift-pill {
            margin-left: auto; flex-shrink: 0;
            background: rgba(22,163,74,0.2);
            border: 1px solid rgba(22,163,74,0.35);
            color: #4ade80; font-size: 9px; font-weight: 700;
            padding: 3px 8px; border-radius: 20px;
            letter-spacing: .4px;
        }

        /* Quick action button */
        .sidebar-quick-btn {
            margin: 8px 14px 4px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #c5a059;
            border: none; border-radius: 10px;
            color: #fff; font-size: 13px; font-weight: 700;
            padding: 10px 14px; cursor: pointer; width: calc(100% - 28px);
            transition: background .15s, transform .1s;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar-quick-btn:hover { background: #d4b06a; transform: translateY(-1px); }
        .sidebar-quick-btn:active { transform: translateY(0); }
        .sidebar-quick-btn i { font-size: 15px; }

        .sidebar-section-lbl {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.6px;
            color: rgba(255,255,255,0.3);
            padding: 14px 18px 5px;
        }

        .sidebar-link {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 18px 10px 15px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 13px; font-weight: 400;
            font-family: 'Poppins', sans-serif;
            transition: all 0.18s;
            border-left: 3px solid transparent;
            cursor: pointer;
            background: none;
            border-right: none; border-top: none; border-bottom: none;
            width: 100%; text-align: left;
            border-radius: 0;
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
        }
        .sidebar-link .link-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.06);
            flex-shrink: 0; transition: background .18s;
        }
        .sidebar-link .link-icon i { font-size: 15px; width: auto; }
        .sidebar-link .link-label { flex: 1; }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.07); color: #fff;
        }
        .sidebar-link:hover .link-icon { background: rgba(255,255,255,0.12); }
        .sidebar-link.active {
            background: rgba(197,160,89,0.12);
            color: #fff;
            border-left-color: #c5a059;
        }
        .sidebar-link.active .link-icon {
            background: rgba(197,160,89,0.25);
            color: #c5a059;
        }

        .sidebar-badge {
            background: #dc2626; color: #fff;
            font-size: 9.5px; font-weight: 700;
            padding: 2px 7px; border-radius: 10px;
            min-width: 20px; text-align: center;
        }
        .sidebar-badge.gold {
            background: #c5a059;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 6px 16px;
        }

        .sidebar-bottom {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.09);
            padding-top: 8px;
        }

        /* Today's mini summary strip */
        .sidebar-today-strip {
            margin: 6px 14px 2px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex; flex-direction: column; gap: 6px;
        }
        .sidebar-today-row {
            display: flex; justify-content: space-between; align-items: center;
        }
        .sidebar-today-lbl { font-size: 10px; color: rgba(255,255,255,0.45); }
        .sidebar-today-val { font-size: 11px; font-weight: 700; color: #fff; }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 28px 32px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-title { font-size: 22px; font-weight: 700; color: var(--blue); }
        .page-sub   { font-size: 13px; color: #64748b; margin-top: 2px; }
        .page-date  {
            font-size: 12px; font-weight: 600; color: #64748b;
            background: #fff; border: 1.5px solid #e2e8f0;
            padding: 8px 14px; border-radius: 10px;
            display: flex; align-items: center; gap: 7px;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.blue::after  { background: linear-gradient(90deg,#1a3a6c,#2471a3); }
        .stat-card.green::after { background: linear-gradient(90deg,#16a34a,#22c55e); }
        .stat-card.gold::after  { background: linear-gradient(90deg,#b45309,#c5a059); }
        .stat-card.red::after   { background: linear-gradient(90deg,#dc2626,#ef4444); }

        .stat-icon-wrap {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 12px;
        }
        .stat-icon-wrap.blue  { background: #e8f0fb; color: #2471a3; }
        .stat-icon-wrap.green { background: #f0fdf4; color: #16a34a; }
        .stat-icon-wrap.gold  { background: #fffbeb; color: #b45309; }
        .stat-icon-wrap.red   { background: #fef2f2; color: #dc2626; }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--blue); line-height: 1; }
        .stat-label { font-size: 12px; font-weight: 600; color: #64748b; margin-top: 4px; }
        .stat-change {
            font-size: 11px; font-weight: 600; margin-top: 8px;
            display: flex; align-items: center; gap: 4px;
        }
        .stat-change.up   { color: #16a34a; }
        .stat-change.down { color: #dc2626; }

        /* ── CARDS ── */
        .card-box {
            background: #fff;
            border-radius: var(--radius);
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .card-box-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1.5px solid #f1f5f9;
        }
        .card-box-title {
            font-size: 14px; font-weight: 700; color: #1e293b;
            display: flex; align-items: center; gap: 8px;
        }
        .card-box-title i { font-size: 16px; }
        .card-box-body { padding: 20px; }

        /* ── TABLE ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            padding: 10px 14px;
            font-size: 11px; font-weight: 700;
            color: #64748b; text-transform: uppercase;
            letter-spacing: .5px; background: #f8faff;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
        }
        .data-table td {
            padding: 12px 14px;
            font-size: 13px; color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table tr:hover td { background: #f8faff; }
        .data-table tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-status.paid     { background: #f0fdf4; color: #16a34a; }
        .badge-status.pending  { background: #fffbeb; color: #b45309; }
        .badge-status.partial  { background: #e8f0fb; color: #2471a3; }
        .badge-status.rejected { background: #fef2f2; color: #dc2626; }

        .method-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .method-pill.cash   { background: #f0fdf4; color: #16a34a; }
        .method-pill.gcash  { background: #e8f0fb; color: #2471a3; }

        /* ── ACTION BUTTONS ── */
        .btn-primary-cash {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #1a3a6c, #2471a3);
            color: #fff; border: none; border-radius: 10px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: all .15s;
            box-shadow: 0 4px 12px rgba(26,58,108,.25);
        }
        .btn-primary-cash:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(26,58,108,.35); }
        .btn-outline-cash {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px;
            background: #fff; color: #2471a3;
            border: 1.5px solid #bfdbfe; border-radius: 10px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            font-family: 'Poppins', sans-serif; transition: all .15s;
        }
        .btn-outline-cash:hover { background: var(--blue-pale); }

        .action-btn-sm {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1.5px solid #e2e8f0; background: #f8faff;
            color: #64748b; cursor: pointer; font-size: 15px;
            transition: all .15s;
        }
        .action-btn-sm:hover { border-color: #2471a3; color: #2471a3; background: #e8f0fb; }

        /* ── QUICK ACTIONS ── */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .quick-card {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--radius);
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
        }
        .quick-card:hover {
            border-color: #2471a3;
            box-shadow: 0 4px 16px rgba(26,58,108,.12);
            transform: translateY(-2px);
        }
        .quick-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin: 0 auto 12px;
        }
        .quick-label { font-size: 13px; font-weight: 700; color: #1e293b; }
        .quick-desc  { font-size: 11px; color: #64748b; margin-top: 3px; }

        /* ── RECEIPT PREVIEW ── */
        .receipt-mini {
            background: #fff;
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            font-size: 12px;
        }

        /* ── MODAL ── */
        .modal-content { border: 0; border-radius: 20px; overflow: hidden; }
        .modal-header-grad {
            background: linear-gradient(135deg, #1a3a6c, #2471a3);
            padding: 20px 24px; border: 0;
        }
        .modal-header-grad .modal-title { color: #fff; font-weight: 700; font-size: 16px; }
        .btn-close-white { filter: brightness(0) invert(1); opacity: .8; }
        .form-lbl {
            font-size: 11px; font-weight: 700; color: #475569;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 6px; display: block;
        }
        .form-fld {
            width: 100%; border: 1.5px solid #e2e8f0;
            border-radius: 9px; padding: 10px 13px;
            font-size: 13px; color: #1e293b;
            font-family: 'Poppins', sans-serif;
            background: #f8faff; outline: none; transition: all .2s;
        }
        .form-fld:focus { border-color: #2471a3; background: #fff; box-shadow: 0 0 0 3px rgba(26,58,108,.08); }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .quick-grid  { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── Skeleton shimmer ── */
        @keyframes skelShimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        .skel, .db-skel {
            background: linear-gradient(90deg,#e8edf2 25%,#f5f7fa 50%,#e8edf2 75%);
            background-size: 600px 100%;
            animation: skelShimmer 1.4s ease-in-out infinite;
            border-radius: 8px;
            display: block;
        }
        .skel-wrap { background:#fff;border-radius:14px;padding:20px;border:1.5px solid #e2e8f0;margin-bottom:16px;overflow:hidden; }
        .skel-hdr  { display:flex;justify-content:space-between;align-items:center;margin-bottom:18px; }
        .skel-trow { display:flex;gap:10px;padding:12px 0;border-bottom:1px solid #f1f5f9;align-items:center; }
        .sec-content { opacity:0;transition:opacity .35s ease; }
    </style>
</head>
<body>

{{-- ══ TOPBAR ══ --}}
<div class="topbar">
    <div class="topbar-brand">
        <div class="brand-icon" style="background:rgba(255,255,255,.12);padding:4px;">
            <img src="/images/logo.png" alt="ILC Logo"
                 style="width:28px;height:28px;object-fit:contain;border-radius:4px;"
                 onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-cash-coin\' style=\'font-size:20px;color:#c5a059;\'></i>'">
        </div>
        <div>
            <div class="brand-text">ILC Cashier</div>
            <div class="brand-sub">Payment Portal</div>
        </div>
    </div>
    <div class="topbar-left">
        <div class="topbar-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search student name or reference…" id="globalSearch">
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-icon-btn" title="Print Receipt">
            <i class="bi bi-printer"></i>
        </div>
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">{{ strtoupper(substr(auth('cashier')->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth('cashier')->user()->name }}</div>
                    <div class="user-role">Cashier</div>
                </div>
                <i class="bi bi-chevron-down" style="font-size:11px;color:#94a3b8;margin-left:4px;"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end" style="min-width:220px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 8px 24px rgba(0,0,0,.12);padding:0;overflow:hidden;margin-top:6px!important;">
                <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:linear-gradient(135deg,#f0f5ff 0%,#fff 100%);border-bottom:1px solid #f0f0f0;">
                    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#1a3a6c,#2471a3);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr(auth('cashier')->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:13px;color:#1a3a6c;">{{ auth('cashier')->user()->name }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ auth('cashier')->user()->email }}</div>
                        <span style="display:inline-block;margin-top:4px;background:#e8f0fb;color:#1a3a6c;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">Cashier</span>
                    </div>
                </div>
                <div style="padding:6px 0;">
                    <form method="POST" action="{{ route('cashier.logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:#dc2626;text-decoration:none;cursor:pointer;transition:background .15s;border:none;background:none;width:100%;font-family:'Poppins',sans-serif;">
                            <i class="bi bi-box-arrow-left" style="font-size:15px;width:18px;"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SIDEBAR ══ --}}
<div class="sidebar" id="sidebar">

    {{-- Cashier info card --}}
    <div class="sidebar-cashier-card">
        <div class="sidebar-cashier-avatar" id="sb-avatar">C</div>
        <div style="flex:1;min-width:0;">
            <div class="sidebar-cashier-name" id="sb-name">{{ auth()->user()->name ?? 'Cashier' }}</div>
            <div class="sidebar-cashier-role">School Cashier</div>
        </div>
        <div class="sidebar-shift-pill">ON DUTY</div>
    </div>

    {{-- Quick Process Payment button --}}
    <button class="sidebar-quick-btn" onclick="showSection('process', document.querySelector('.sidebar-link[data-section=process]'))">
        <i class="bi bi-plus-circle-fill"></i> Process Payment
    </button>

    {{-- Today's mini stats --}}
    <div class="sidebar-today-strip">
        <div class="sidebar-today-row">
            <span class="sidebar-today-lbl">Today's Collection</span>
            <span class="sidebar-today-val" id="sb-today-amount">₱0.00</span>
        </div>
        <div class="sidebar-today-row">
            <span class="sidebar-today-lbl">Transactions</span>
            <span class="sidebar-today-val" id="sb-today-count">0</span>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    {{-- MAIN --}}
    <div class="sidebar-section-lbl">Overview</div>

    <button class="sidebar-link" data-section="dashboard" onclick="showSection('dashboard', this)">
        <span class="link-icon"><i class="bi bi-grid-fill"></i></span>
        <span class="link-label">Dashboard</span>
    </button>

    <div class="sidebar-divider"></div>

    {{-- PAYMENTS --}}
    <div class="sidebar-section-lbl">Payments</div>

    <button class="sidebar-link" data-section="process" onclick="showSection('process', this)">
        <span class="link-icon"><i class="bi bi-cash-coin"></i></span>
        <span class="link-label">Process Payment</span>
    </button>
    <button class="sidebar-link" data-section="history" onclick="showSection('history', this)">
        <span class="link-icon"><i class="bi bi-clock-history"></i></span>
        <span class="link-label">Payment History</span>
        <span class="sidebar-badge" id="pending-badge" style="display:none;">0</span>
    </button>
    <button class="sidebar-link" data-section="lookup" onclick="showSection('lookup', this)">
        <span class="link-icon"><i class="bi bi-person-badge-fill"></i></span>
        <span class="link-label">Student Lookup</span>
    </button>

    <div class="sidebar-divider"></div>

    {{-- REPORTS --}}
    <div class="sidebar-section-lbl">Reports</div>

    <button class="sidebar-link" data-section="daily" onclick="showSection('daily', this)">
        <span class="link-icon"><i class="bi bi-calendar-day-fill"></i></span>
        <span class="link-label">Daily Report</span>
    </button>
    <button class="sidebar-link" data-section="receipts" onclick="showSection('receipts', this)">
        <span class="link-icon"><i class="bi bi-receipt-cutoff"></i></span>
        <span class="link-label">Receipts</span>
    </button>
    <button class="sidebar-link" data-section="collection" onclick="showSection('collection', this)">
        <span class="link-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <span class="link-label">Collection Summary</span>
    </button>

    {{-- BOTTOM --}}
    <div class="sidebar-bottom">
        <button class="sidebar-link" data-section="settings" onclick="showSection('settings', this)">
            <span class="link-icon"><i class="bi bi-gear-fill"></i></span>
            <span class="link-label">Settings</span>
        </button>
        <form method="POST" action="{{ route('cashier.logout') }}" style="margin:0;"
              onsubmit="return confirm('Log out of the cashier portal?')">
            @csrf
            <button type="submit" class="sidebar-link" style="color:rgba(248,113,113,0.8);">
                <span class="link-icon" style="background:rgba(220,38,38,0.12);"><i class="bi bi-box-arrow-left" style="color:rgba(248,113,113,0.9);"></i></span>
                <span class="link-label">Log Out</span>
            </button>
        </form>
    </div>

</div>

{{-- ══ MAIN CONTENT ══ --}}
<div class="main" id="main-content">

{{-- ══ GLOBAL SKELETON — shown when switching any section ══ --}}
<div id="cs-global-skeleton" style="display:none;opacity:1;transition:opacity .2s ease;">

    {{-- Page header placeholder --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <span class="skel" style="height:26px;width:240px;margin-bottom:10px;border-radius:8px;"></span>
            <span class="skel" style="height:13px;width:360px;border-radius:6px;"></span>
        </div>
        <span class="skel" style="height:40px;width:130px;border-radius:10px;"></span>
    </div>

    {{-- Filter / action bar placeholder --}}
    <div style="background:#fff;border-radius:14px;padding:16px 20px;margin-bottom:18px;border:1.5px solid #e2e8f0;display:flex;gap:10px;flex-wrap:wrap;">
        <span class="skel" style="height:36px;flex:1;min-width:200px;border-radius:10px;"></span>
        <span class="skel" style="height:36px;width:130px;border-radius:10px;"></span>
        <span class="skel" style="height:36px;width:130px;border-radius:10px;"></span>
        <span class="skel" style="height:36px;width:100px;border-radius:10px;"></span>
    </div>

    {{-- Table card placeholder --}}
    <div style="background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;overflow:hidden;">
        {{-- Card header --}}
        <div style="padding:16px 20px;border-bottom:1.5px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <span class="skel" style="height:15px;width:180px;border-radius:6px;"></span>
            <span class="skel" style="height:15px;width:100px;border-radius:6px;"></span>
        </div>
        {{-- Table column headers --}}
        <div style="padding:10px 20px;background:#f8faff;border-bottom:1.5px solid #e2e8f0;display:flex;gap:16px;">
            @for($si=0;$si<7;$si++)
            <span class="skel" style="height:11px;width:{{ [70,120,80,60,90,60,40][$si] }}px;border-radius:4px;"></span>
            @endfor
        </div>
        {{-- Table rows --}}
        @for($si=0;$si<8;$si++)
        <div style="padding:14px 20px;border-bottom:1px solid #f8faff;display:flex;gap:16px;align-items:center;">
            <span class="skel" style="height:34px;width:34px;border-radius:8px;flex-shrink:0;"></span>
            <div style="flex:1.5;">
                <span class="skel" style="height:13px;width:{{ 100 + ($si * 10 % 60) }}px;margin-bottom:6px;border-radius:6px;"></span>
                <span class="skel" style="height:11px;width:80px;border-radius:5px;"></span>
            </div>
            <span class="skel" style="height:13px;width:60px;border-radius:6px;"></span>
            <span class="skel" style="height:13px;width:80px;border-radius:6px;"></span>
            <span class="skel" style="height:13px;width:70px;border-radius:6px;"></span>
            <span class="skel" style="height:24px;width:72px;border-radius:20px;"></span>
            <span class="skel" style="height:13px;width:80px;border-radius:6px;"></span>
            <span class="skel" style="height:30px;width:32px;border-radius:8px;"></span>
        </div>
        @endfor
    </div>

</div>
{{-- ══ END GLOBAL SKELETON ══ --}}

    {{-- ── DASHBOARD SECTION ── --}}
    <div id="section-dashboard" style="display:none;">

        {{-- ── Dashboard Skeleton ── --}}
        <div id="db-skeleton">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
                @for($i=0;$i<4;$i++)
                <div style="background:#fff;border-radius:14px;padding:20px;border:1.5px solid #e2e8f0;">
                    <span class="skel" style="width:46px;height:46px;border-radius:13px;margin-bottom:12px;"></span>
                    <span class="skel" style="height:28px;width:80px;margin-bottom:8px;"></span>
                    <span class="skel" style="height:12px;width:120px;"></span>
                </div>
                @endfor
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
                @for($i=0;$i<4;$i++)
                <div style="background:#fff;border-radius:14px;padding:20px;border:1.5px solid #e2e8f0;">
                    <span class="skel" style="width:52px;height:52px;border-radius:14px;margin:0 auto 12px;"></span>
                    <span class="skel" style="height:14px;width:90px;margin:0 auto 6px;border-radius:6px;"></span>
                    <span class="skel" style="height:11px;width:70px;margin:0 auto;border-radius:6px;"></span>
                </div>
                @endfor
            </div>
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px;">
                <div style="background:#fff;border-radius:14px;padding:20px;border:1.5px solid #e2e8f0;height:240px;">
                    <span class="skel" style="height:16px;width:200px;margin-bottom:18px;border-radius:6px;"></span>
                    <span class="skel" style="height:170px;border-radius:8px;"></span>
                </div>
                <div style="background:#fff;border-radius:14px;padding:20px;border:1.5px solid #e2e8f0;height:240px;">
                    <span class="skel" style="height:16px;width:130px;margin-bottom:18px;border-radius:6px;"></span>
                    <span class="skel" style="height:170px;border-radius:50%;width:170px;margin:0 auto;"></span>
                </div>
            </div>
        </div>

        {{-- ── Real Content ── --}}
        <div id="db-content" style="display:none;opacity:0;transition:opacity .35s ease;">

        <div class="page-header">
            <div>
                <div class="page-title">Cashier Dashboard</div>
                <div class="page-sub">Welcome back, {{ auth('cashier')->user()->name ?? 'Cashier' }}! Here's today's overview.</div>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <div class="page-date"><i class="bi bi-calendar3"></i><span id="live-date"></span></div>
                <button class="btn-primary-cash" onclick="showSection('process',document.querySelector('.sidebar-link[data-section=process]'))">
                    <i class="bi bi-plus-circle-fill"></i> Process Payment
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon-wrap blue"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value">₱{{ number_format($todayTotal,2) }}</div>
                <div class="stat-label">Today's Collection</div>
                <div class="stat-change {{ $todayTotal > 0 ? 'up' : '' }}" style="{{ $todayTotal == 0 ? 'color:#64748b;' : '' }}">
                    @if($todayTotal > 0)
                        <i class="bi bi-arrow-up-short"></i> {{ $todayCount }} transaction(s)
                    @else
                        No collections yet
                    @endif
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon-wrap green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value">{{ $todayCount }}</div>
                <div class="stat-label">Transactions Today</div>
                <div class="stat-change" style="color:#64748b;">
                    {{ $todayCount > 0 ? 'Processed today' : 'No transactions yet' }}
                </div>
            </div>
            <div class="stat-card gold">
                <div class="stat-icon-wrap gold"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pending Payments</div>
                <div class="stat-change" style="color:{{ $pendingCount > 0 ? '#b45309' : '#64748b' }};">
                    {{ $pendingCount > 0 ? $pendingCount . ' awaiting payment' : 'No pending items' }}
                </div>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#f0fdf4,#fff);border:1.5px solid #e2e8f0;border-radius:var(--radius);padding:20px;position:relative;overflow:hidden;">
                <div style="position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#16a34a,#22c55e);"></div>
                <div class="stat-icon-wrap green"><i class="bi bi-wallet2"></i></div>
                <div class="stat-value" style="color:#16a34a;">₱{{ number_format($todayCash,2) }}</div>
                <div class="stat-label">Cash Collected</div>
                <div class="stat-change" style="color:#64748b;">Today (cash only)</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="quick-grid">
            <div class="quick-card" onclick="showSection('process',document.querySelector('.sidebar-link[data-section=process]'))">
                <div class="quick-icon" style="background:#e8f0fb;"><i class="bi bi-cash-coin" style="color:#2471a3;font-size:24px;"></i></div>
                <div class="quick-label">Process Payment</div>
                <div class="quick-desc">Record a walk-in payment</div>
            </div>
            <div class="quick-card" onclick="showSection('lookup',document.querySelector('.sidebar-link[data-section=lookup]'))">
                <div class="quick-icon" style="background:#f0fdf4;"><i class="bi bi-person-badge-fill" style="color:#16a34a;font-size:24px;"></i></div>
                <div class="quick-label">Student Lookup</div>
                <div class="quick-desc">Check student balance</div>
            </div>
            <div class="quick-card" onclick="showSection('receipts',document.querySelector('.sidebar-link[data-section=receipts]'))">
                <div class="quick-icon" style="background:#fffbeb;"><i class="bi bi-receipt-cutoff" style="color:#b45309;font-size:24px;"></i></div>
                <div class="quick-label">Receipts</div>
                <div class="quick-desc">View &amp; reprint receipts</div>
            </div>
            <div class="quick-card" onclick="showSection('daily',document.querySelector('.sidebar-link[data-section=daily]'))">
                <div class="quick-icon" style="background:#fdf4ff;"><i class="bi bi-calendar-day-fill" style="color:#9333ea;font-size:24px;"></i></div>
                <div class="quick-label">Daily Report</div>
                <div class="quick-desc">View today's summary</div>
            </div>
        </div>

        {{-- Charts --}}
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px;">
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-graph-up" style="color:#2471a3;"></i> Daily Collections (Last 7 Days)</div>
                </div>
                <div class="card-box-body" style="height:200px;padding:16px;"><canvas id="csWeekBar"></canvas></div>
            </div>
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-pie-chart-fill" style="color:#2471a3;"></i> Today by Method</div>
                </div>
                <div class="card-box-body" style="height:200px;padding:16px;display:flex;justify-content:center;"><canvas id="csMethodDoughnut"></canvas></div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card-box" style="margin-bottom:20px;">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-clock-history" style="color:#2471a3;"></i> Today's Transactions</div>
                <button class="btn-outline-cash" onclick="showSection('history',document.querySelector('.sidebar-link[data-section=history]'))">
                    <i class="bi bi-arrow-right"></i> View All
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr><th>Reference</th><th>Student</th><th>Grade</th><th>Type</th><th>Method</th><th>Amount</th><th>Time</th><th>Print</th></tr>
                    </thead>
                    <tbody>
                        @forelse($todayTransactions as $tx)
                        <tr>
                            <td><span style="font-family:monospace;font-size:11.5px;background:#f8faff;padding:3px 8px;border-radius:6px;color:#475569;">{{ $tx->reference_number ?? '—' }}</span></td>
                            <td style="font-weight:700;">{{ $tx->user?->name ?? '—' }}</td>
                            <td>{{ $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—' }}</td>
                            <td style="font-size:12px;">{{ ucwords(str_replace('_',' ',$tx->payment_type??'—')) }}</td>
                            <td>
                                @if(strtolower($tx->payment_method??'')=='cash')
                                    <span class="method-pill cash"><i class="bi bi-cash"></i> Cash</span>
                                @else
                                    <span class="method-pill gcash"><i class="bi bi-phone"></i> {{ ucfirst($tx->payment_method??'—') }}</span>
                                @endif
                            </td>
                            <td style="font-weight:700;color:#16a34a;">₱{{ number_format($tx->amount,2) }}</td>
                            <td style="font-size:12px;color:#64748b;">{{ $tx->processed_at?->format('h:i A') }}</td>
                            <td>
                                <button onclick="reprintTx('{{ $tx->reference_number }}','{{ $tx->user?->name }}','{{ $tx->enrollment?->grade_level }}','{{ $tx->enrollment?->school_year }}','{{ $tx->payment_type }}','{{ $tx->payment_method }}','{{ $tx->amount }}','{{ $tx->processed_at?->format('M d, Y') }}','{{ $tx->processed_at?->format('h:i A') }}')"
                                    class="action-btn-sm" title="Print"><i class="bi bi-printer-fill"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            No transactions yet today
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bottom row --}}
        @php
            $dayTotal = $todayCash + $todayOnline;
            $cashPct  = $dayTotal > 0 ? round(($todayCash / $dayTotal) * 100) : 0;
            $onlPct   = $dayTotal > 0 ? round(($todayOnline / $dayTotal) * 100) : 0;
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            {{-- Payment Methods --}}
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-pie-chart-fill" style="color:#16a34a;"></i> Payment Methods Today</div>
                </div>
                <div class="card-box-body">
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:6px;">
                                <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:3px;background:#16a34a;display:inline-block;"></span>Cash</span>
                                <span style="color:#64748b;">₱{{ number_format($todayCash,2) }} ({{ $cashPct }}%)</span>
                            </div>
                            <div style="height:8px;border-radius:4px;background:#f1f5f9;overflow:hidden;">
                                <div style="height:100%;width:{{ $cashPct }}%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:4px;transition:width .6s ease;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:6px;">
                                <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:3px;background:#2471a3;display:inline-block;"></span>Online / E-Wallet</span>
                                <span style="color:#64748b;">₱{{ number_format($todayOnline,2) }} ({{ $onlPct }}%)</span>
                            </div>
                            <div style="height:8px;border-radius:4px;background:#f1f5f9;overflow:hidden;">
                                <div style="height:100%;width:{{ $onlPct }}%;background:linear-gradient(90deg,#1a3a6c,#2471a3);border-radius:4px;transition:width .6s ease;"></div>
                            </div>
                        </div>
                    </div>
                    @if($dayTotal == 0)
                    <div style="margin-top:20px;padding:14px;background:#f8faff;border-radius:10px;text-align:center;color:#94a3b8;font-size:12px;">
                        No payment data for today
                    </div>
                    @endif
                </div>
            </div>

            {{-- Shift Summary --}}
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-person-badge-fill" style="color:#b45309;"></i> Shift Summary</div>
                </div>
                <div class="card-box-body">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @php
                            $shiftRows = [
                                ['label'=>'Date',            'value'=>date('F d, Y'),                                          'icon'=>'bi-calendar3'],
                                ['label'=>'Cashier',         'value'=>auth('cashier')->user()->name ?? 'Cashier',              'icon'=>'bi-person-fill'],
                                ['label'=>'Total Processed', 'value'=>$todayCount . ' transaction(s)',                         'icon'=>'bi-receipt'],
                                ['label'=>'Total Amount',    'value'=>'₱' . number_format($todayTotal,2),                     'icon'=>'bi-cash-stack'],
                                ['label'=>'Cash Collected',  'value'=>'₱' . number_format($todayCash,2),                      'icon'=>'bi-wallet2'],
                                ['label'=>'Online Collected','value'=>'₱' . number_format($todayOnline,2),                    'icon'=>'bi-phone'],
                            ];
                        @endphp
                        @foreach($shiftRows as $row)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;background:#f8faff;border-radius:9px;border:1px solid #e2e8f0;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:7px;">
                                <i class="bi {{ $row['icon'] }}"></i>{{ $row['label'] }}
                            </span>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ $row['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        </div>{{-- /db-content --}}
    </div>

    {{-- ── PROCESS PAYMENT SECTION ── --}}
    <div id="section-process">

        {{-- Skeleton --}}
        <div id="process-skel" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div><span class="skel" style="height:26px;width:220px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:300px;"></span></div>
                <div style="display:flex;gap:10px;">
                    <span class="skel" style="height:54px;width:110px;border-radius:12px;"></span>
                    <span class="skel" style="height:54px;width:110px;border-radius:12px;"></span>
                    <span class="skel" style="height:54px;width:130px;border-radius:12px;"></span>
                </div>
            </div>
            <div class="skel-wrap">
                <div class="skel-hdr"><span class="skel" style="height:18px;width:180px;"></span><div style="display:flex;gap:8px;"><span class="skel" style="height:38px;width:280px;border-radius:10px;"></span><span class="skel" style="height:38px;width:90px;border-radius:10px;"></span></div></div>
                <div style="padding-top:4px;">
                    <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9;"><span class="skel" style="height:12px;flex:1;max-width:80px;"></span><span class="skel" style="height:12px;flex:2;"></span><span class="skel" style="height:12px;flex:1;"></span><span class="skel" style="height:12px;flex:1;"></span><span class="skel" style="height:12px;flex:1;"></span><span class="skel" style="height:12px;flex:1;max-width:80px;"></span><span class="skel" style="height:12px;flex:1;max-width:70px;"></span></div>
                    @for($i=0;$i<6;$i++)
                    <div class="skel-trow"><span class="skel" style="height:14px;flex:1;max-width:80px;"></span><span class="skel" style="height:28px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:22px;flex:1;border-radius:20px;max-width:70px;"></span><span class="skel" style="height:14px;flex:1;max-width:90px;"></span><span class="skel" style="height:30px;width:60px;border-radius:8px;flex-shrink:0;"></span></div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Real content --}}
        <div id="process-content" class="sec-content">

        {{-- ── Page header with live session stats ── --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:14px;">
            <div>
                <div class="page-title"><i class="bi bi-cash-coin me-2" style="color:#c5a059;"></i>Process Payment</div>
                <div class="page-sub">Search a student and collect payment at the counter.</div>
            </div>
            <div style="display:flex;gap:10px;">
                <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 16px;text-align:center;min-width:100px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Today</div>
                    <div style="font-size:18px;font-weight:900;color:#16a34a;" id="pp-today-amt">₱0.00</div>
                    <div style="font-size:10px;color:#64748b;">Collected</div>
                </div>
                <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 16px;text-align:center;min-width:100px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Transactions</div>
                    <div style="font-size:18px;font-weight:900;color:#1a3a6c;" id="pp-today-count">0</div>
                    <div style="font-size:10px;color:#64748b;">Today</div>
                </div>
                <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 16px;text-align:center;min-width:100px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Cashier</div>
                    <div style="font-size:13px;font-weight:800;color:#1e293b;margin:4px 0;">{{ auth('cashier')->user()->name ?? 'Staff' }}</div>
                    <div style="font-size:10px;color:#16a34a;font-weight:700;display:flex;align-items:center;justify-content:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block;"></span>On Duty</div>
                </div>
            </div>
        </div>

        {{-- ── Student List Panel ── --}}
        <div id="studentListPanel">
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title">
                        <i class="bi bi-people-fill" style="color:#2471a3;"></i>
                        Approved Students
                        <span id="studentListCount" style="background:#e8f0fb;color:#1a3a6c;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;margin-left:6px;">0</span>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <div style="display:flex;align-items:center;gap:8px;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 14px;min-width:260px;">
                            <i class="bi bi-search" style="color:#94a3b8;font-size:14px;flex-shrink:0;"></i>
                            <input type="text" id="studentListFilter" placeholder="Filter by name, grade, ref no…"
                                style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Poppins',sans-serif;"
                                oninput="filterStudentList(this.value)">
                        </div>
                        <button onclick="loadStudentList()" class="btn-outline-cash" style="white-space:nowrap;">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ref No.</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Section</th>
                                <th>School Year</th>
                                <th>Payment Status</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentListBody">
                            <tr>
                                <td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;">
                                    <i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>
                                    Loading students…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── POS Two-Column Layout ── --}}
        <div id="payPanel" style="display:none;grid-template-columns:5fr 6fr;gap:22px;align-items:start;">

            {{-- ══ LEFT: Student Info ══ --}}
            <div>

                {{-- Back to list --}}
                <button onclick="backToStudentList()" class="btn-outline-cash" style="margin-bottom:14px;width:100%;">
                    <i class="bi bi-arrow-left"></i> Back to Student List
                </button>

                {{-- Identity Card --}}
                <div style="background:linear-gradient(145deg,#0f2451 0%,#1a3a6c 40%,#2471a3 100%);border-radius:22px;padding:26px;margin-bottom:14px;position:relative;overflow:hidden;">
                    <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
                    <div style="position:absolute;bottom:-30px;left:-20px;width:100px;height:100px;border-radius:50%;background:rgba(197,160,89,.08);pointer-events:none;"></div>
                    <div style="position:absolute;top:20px;right:20px;width:60px;height:60px;border-radius:50%;background:rgba(197,160,89,.06);pointer-events:none;"></div>
                    {{-- Student header --}}
                    <div style="display:flex;align-items:center;gap:14px;position:relative;">
                        <div style="width:58px;height:58px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:900;flex-shrink:0;border:2.5px solid rgba(255,255,255,.3);box-shadow:0 4px 14px rgba(0,0,0,.2);" id="studentInitial">?</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:17px;font-weight:900;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:-.2px;" id="studentName">—</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.6);margin-top:3px;display:flex;align-items:center;gap:6px;" id="studentGrade">
                                <i class="bi bi-mortarboard-fill" style="font-size:10px;"></i> —
                            </div>
                        </div>
                        <button onclick="clearPaymentForm()" title="Change student"
                            style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);color:rgba(255,255,255,.75);border-radius:10px;padding:8px 12px;cursor:pointer;font-size:12px;transition:all .15s;line-height:1;flex-shrink:0;">
                            <i class="bi bi-arrow-left-circle me-1"></i>Change
                        </button>
                    </div>
                    {{-- Status + school year --}}
                    <div style="margin-top:14px;display:flex;align-items:center;gap:8px;">
                        <div id="studentPayStatusBadge" style="font-size:10px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;"></div>
                        <div style="font-size:11px;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:5px;"><i class="bi bi-calendar3"></i><span id="studentSchoolYear">—</span></div>
                    </div>
                    {{-- Balance + progress --}}
                    <div style="margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,.12);position:relative;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:10px;">
                            <div>
                                <div style="font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Outstanding Balance</div>
                                <div style="font-size:32px;font-weight:900;color:#fff;line-height:1;letter-spacing:-1px;" id="studentBalance">₱0.00</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Amount Paid</div>
                                <div style="font-size:16px;font-weight:700;color:rgba(255,255,255,.8);" id="cardAmountPaid">₱0.00</div>
                            </div>
                        </div>
                        {{-- Payment progress bar --}}
                        <div style="background:rgba(255,255,255,.12);border-radius:20px;height:6px;overflow:hidden;">
                            <div id="payProgressBar" style="height:100%;background:linear-gradient(90deg,#c5a059,#f5d08a);border-radius:20px;width:0%;transition:width .6s ease;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-top:5px;">
                            <div style="font-size:10px;color:rgba(255,255,255,.4);" id="payProgressLabel">0% paid</div>
                            <div style="font-size:10px;color:rgba(255,255,255,.4);" id="cardTotalFee">Total: ₱0.00</div>
                        </div>
                    </div>
                </div>

                {{-- Account Details --}}
                <div class="card-box" style="margin-bottom:14px;">
                    <div class="card-box-header">
                        <div class="card-box-title"><i class="bi bi-wallet2" style="color:#2471a3;"></i> Account Details</div>
                        <span id="acctPaymentPlan" style="font-size:11px;font-weight:700;background:#e8f0fb;color:#1a3a6c;padding:3px 10px;border-radius:20px;">—</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#f1f5f9;">
                        <div style="background:#fff;padding:14px 16px;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Total Fee</div>
                            <div id="acctTotalFee" style="font-size:16px;font-weight:800;color:#1e293b;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Amount Paid</div>
                            <div id="acctAmountPaid" style="font-size:16px;font-weight:800;color:#16a34a;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;border-top:1px solid #f1f5f9;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Remaining</div>
                            <div id="acctRemaining" style="font-size:16px;font-weight:800;color:#dc2626;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;border-top:1px solid #f1f5f9;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Monthly</div>
                            <div id="acctMonthly" style="font-size:16px;font-weight:800;color:#1a3a6c;">—</div>
                        </div>
                    </div>
                </div>

                {{-- Quick Guide --}}
                <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:14px 16px;">
                    <div style="font-size:11px;font-weight:700;color:#92400e;margin-bottom:10px;display:flex;align-items:center;gap:6px;"><i class="bi bi-info-circle-fill"></i> Payment Guide</div>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:#78350f;"><div style="width:20px;height:20px;border-radius:6px;background:#fcd34d;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">1</div>Search and select student</div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:#78350f;"><div style="width:20px;height:20px;border-radius:6px;background:#fcd34d;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">2</div>Choose a quick preset or enter amount</div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:#78350f;"><div style="width:20px;height:20px;border-radius:6px;background:#fcd34d;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">3</div>Select Cash or Online method</div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:#78350f;"><div style="width:20px;height:20px;border-radius:6px;background:#fcd34d;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">4</div>Click Process &amp; print receipt</div>
                    </div>
                </div>

            </div>{{-- /LEFT --}}

            {{-- ══ RIGHT: Payment Register ══ --}}
            <div>

                {{-- Payment Plan Selector (always visible; collapses after plan confirmed) --}}
                <div id="planSelectorCard" style="display:none;margin-bottom:14px;">
                    <div class="card-box" style="margin-bottom:0;" id="planSelectorBox">
                        {{-- Header: changes colour based on state --}}
                        <div class="card-box-header" id="planSelectorHeader" style="padding:14px 18px;">
                            <div class="card-box-title" id="planSelectorTitle">
                                <i class="bi bi-clipboard-check-fill"></i>
                                Payment Plan
                            </div>
                            <button id="changePlanBtn" onclick="expandPlanOptions()"
                                style="display:none;padding:5px 14px;border:1.5px solid #2471a3;background:#e8f0fb;color:#1a3a6c;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;">
                                <i class="bi bi-pencil-fill me-1"></i>Change Plan
                            </button>
                        </div>
                        {{-- Collapsed summary (shown after plan is confirmed) --}}
                        <div id="planSummaryStrip" style="display:none;padding:12px 18px;background:#f0fdf4;border-top:1px solid #bbf7d0;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:18px;"></i>
                                <div id="planSummaryText" style="font-size:13px;font-weight:700;color:#166534;flex:1;"></div>
                            </div>
                        </div>
                        {{-- Expandable options list --}}
                        <div id="planOptionsWrap">
                            <div style="padding:14px 16px 16px;" id="planOptionsList">
                                <div style="text-align:center;padding:24px;color:#94a3b8;">
                                    <i class="bi bi-arrow-repeat" style="font-size:22px;display:block;margin-bottom:8px;"></i>
                                    Loading plans…
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Presets --}}
                <div id="quickAmountRow" style="display:none;margin-bottom:14px;">
                    <div class="card-box" style="margin-bottom:0;">
                        <div class="card-box-header" style="padding:12px 18px;">
                            <div class="card-box-title"><i class="bi bi-lightning-fill" style="color:#c5a059;"></i> Quick Presets</div>
                            <span style="font-size:11px;color:#94a3b8;">Tap to fill amount instantly</span>
                        </div>
                        <div style="padding:14px 16px;display:flex;gap:10px;flex-wrap:wrap;" id="quickAmountBtns"></div>
                    </div>
                </div>

                {{-- Amount Input --}}
                <div class="card-box" style="margin-bottom:14px;">
                    <div class="card-box-header" style="padding:12px 18px;">
                        <div class="card-box-title"><i class="bi bi-cash-stack" style="color:#2471a3;"></i> Amount to Collect</div>
                        <div id="paymentTypeLabel" style="font-size:11px;font-weight:700;color:#1a3a6c;background:#e8f0fb;padding:4px 12px;border-radius:20px;">Select type</div>
                    </div>
                    <div style="padding:16px 20px 12px;">
                        <div style="display:flex;align-items:center;background:#f8faff;border:2.5px solid #e2e8f0;border-radius:16px;overflow:hidden;transition:all .2s;" id="amountInputWrap">
                            <span style="padding:18px 8px 18px 22px;font-size:30px;font-weight:900;color:#cbd5e1;user-select:none;">₱</span>
                            <input type="number" id="paymentAmount" placeholder="0.00" step="0.01" min="0"
                                style="flex:1;padding:18px 22px 18px 6px;font-size:32px;font-weight:900;color:#1e293b;border:none;outline:none;background:transparent;font-family:'Poppins',sans-serif;letter-spacing:-1px;"
                                oninput="updateTransactionSummary()"
                                onfocus="document.getElementById('amountInputWrap').style.borderColor='#2471a3';document.getElementById('amountInputWrap').style.background='#fff';document.getElementById('amountInputWrap').style.boxShadow='0 0 0 4px rgba(36,113,163,.08)';"
                                onblur="document.getElementById('amountInputWrap').style.borderColor='#e2e8f0';document.getElementById('amountInputWrap').style.background='#f8faff';document.getElementById('amountInputWrap').style.boxShadow='none';">
                        </div>
                        <div style="margin-top:10px;display:flex;gap:8px;">
                            <button type="button" onclick="clearAmount()" style="flex:1;padding:8px;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;font-size:12px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s;" onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background='#fff'"><i class="bi bi-x-circle me-1"></i>Clear</button>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="card-box" style="margin-bottom:14px;">
                    <div class="card-box-header" style="padding:12px 18px;">
                        <div class="card-box-title"><i class="bi bi-credit-card-2-front" style="color:#2471a3;"></i> Payment Method</div>
                    </div>
                    <div style="padding:14px 16px 16px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:0;">
                            <button type="button" id="methodCashBtn" onclick="selectPayMethod('cash')"
                                style="padding:20px 12px;border:2.5px solid #16a34a;border-radius:16px;background:#f0fdf4;cursor:pointer;text-align:center;transition:all .2s;box-shadow:0 4px 12px rgba(22,163,74,.15);">
                                <div style="width:48px;height:48px;border-radius:14px;background:#16a34a;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                                    <i class="bi bi-cash-stack" style="font-size:22px;color:#fff;"></i>
                                </div>
                                <div style="font-size:14px;font-weight:800;color:#166534;">Cash</div>
                                <div style="font-size:10px;color:#64748b;margin-top:3px;">Walk-in counter</div>
                            </button>
                            <button type="button" id="methodOnlineBtn" onclick="selectPayMethod('online')"
                                style="padding:20px 12px;border:2.5px solid #e2e8f0;border-radius:16px;background:#fff;cursor:pointer;text-align:center;transition:all .2s;">
                                <div style="width:48px;height:48px;border-radius:14px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;" id="onlineBtnIcon">
                                    <i class="bi bi-phone" style="font-size:22px;color:#94a3b8;"></i>
                                </div>
                                <div style="font-size:14px;font-weight:800;color:#64748b;" id="onlineBtnLabel">Online</div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:3px;">GCash · Maya · Bank</div>
                            </button>
                        </div>
                        <div id="onlineMethodRow" style="display:none;margin-top:14px;">
                            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Select e-Wallet / Bank</div>
                            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;">
                                @foreach([['gcash','bi-phone-fill','#0070ff','GCash'],['maya','bi-wallet2','#00b09b','Maya'],['grabpay','bi-bag-fill','#00b14f','Grab'],['bank','bi-bank2','#1a3a6c','Bank'],['otc','bi-shop','#e65100','OTC']] as [$m,$icon,$color,$label])
                                <button type="button" class="online-method-opt" data-method="{{ $m }}" onclick="selectOnlineMethod('{{ $m }}')"
                                    style="padding:10px 4px;border:2px solid #e2e8f0;border-radius:12px;background:#fff;cursor:pointer;text-align:center;transition:all .2s;">
                                    <div style="width:34px;height:34px;border-radius:10px;background:{{ $color }};margin:0 auto 5px;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi {{ $icon }}" style="color:#fff;font-size:14px;"></i>
                                    </div>
                                    <div style="font-size:10px;font-weight:700;color:#334155;">{{ $label }}</div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes field --}}
                <div class="card-box" style="margin-bottom:14px;">
                    <div class="card-box-header" style="padding:12px 18px;">
                        <div class="card-box-title"><i class="bi bi-pencil-square" style="color:#2471a3;"></i> Notes <span style="font-size:11px;font-weight:400;color:#94a3b8;">(optional)</span></div>
                    </div>
                    <div style="padding:12px 16px 16px;">
                        <textarea id="paymentNotesField" rows="2" class="form-fld" placeholder="e.g. Paid via manager, reference #, etc."
                            style="resize:none;font-size:13px;border-radius:10px;"
                            oninput="document.getElementById('paymentNotes').value=this.value;"></textarea>
                    </div>
                </div>

                {{-- Transaction Summary --}}
                <div id="txnSummary" style="display:none;background:linear-gradient(135deg,#f8faff,#eff6ff);border:1.5px solid #bfdbfe;border-radius:16px;padding:16px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:700;color:#1e40af;margin-bottom:12px;display:flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:.5px;"><i class="bi bi-receipt-cutoff"></i> Transaction Summary</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:#64748b;">Student</span><span style="font-weight:700;color:#1e293b;" id="txnStudent">—</span></div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:#64748b;">Payment Type</span><span style="font-weight:700;color:#1e293b;" id="txnType">—</span></div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:#64748b;">Method</span><span style="font-weight:700;color:#1e293b;" id="txnMethod">—</span></div>
                        <div style="height:1px;background:#bfdbfe;margin:4px 0;"></div>
                        <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:900;"><span style="color:#1e40af;">Total Amount</span><span style="color:#1a3a6c;" id="txnAmount">₱0.00</span></div>
                    </div>
                </div>

                {{-- Xendit link result --}}
                <div id="xenditLinkResult" style="display:none;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:16px;padding:18px;margin-bottom:14px;">
                    <div style="font-size:13px;font-weight:700;color:#166534;margin-bottom:12px;display:flex;align-items:center;gap:8px;"><i class="bi bi-check-circle-fill"></i> Payment link generated!</div>
                    <div style="display:flex;gap:8px;margin-bottom:10px;">
                        <input type="text" id="xenditLinkUrl" readonly style="flex:1;padding:10px 12px;border-radius:10px;border:1px solid #86efac;background:#fff;font-size:12px;font-family:monospace;outline:none;color:#166534;">
                        <button onclick="copyXenditLink()" style="padding:10px 16px;background:#16a34a;color:#fff;border:none;border-radius:10px;cursor:pointer;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px;"><i class="bi bi-clipboard" id="cashierCopyIcon"></i> Copy</button>
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-bottom:12px;" id="xenditLinkExpiry"></div>
                    <a id="xenditLinkOpenBtn" href="#" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#16a34a;color:#fff;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;"><i class="bi bi-box-arrow-up-right"></i> Open Payment Page</a>
                </div>

                {{-- Success Banner --}}
                <div id="paySuccessBanner" style="display:none;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid #86efac;border-radius:20px;padding:24px;margin-bottom:14px;text-align:center;">
                    <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 8px 20px rgba(22,163,74,.3);">
                        <i class="bi bi-check-lg" style="font-size:32px;color:#fff;"></i>
                    </div>
                    <div style="font-size:20px;font-weight:900;color:#166534;margin-bottom:4px;">Payment Recorded!</div>
                    <div id="paySuccessRef" style="font-size:13px;color:#64748b;margin-bottom:20px;line-height:1.6;"></div>
                    <div style="display:flex;gap:10px;justify-content:center;">
                        <button onclick="printLastReceipt()" style="padding:12px 22px;background:#fff;color:#16a34a;border:2px solid #86efac;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;box-shadow:0 2px 8px rgba(22,163,74,.1);">
                            <i class="bi bi-printer-fill"></i> Print Receipt
                        </button>
                        <button onclick="backToStudentList()" style="padding:12px 22px;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;box-shadow:0 4px 14px rgba(22,163,74,.35);">
                            <i class="bi bi-plus-circle-fill"></i> New Payment
                        </button>
                    </div>
                </div>

                {{-- Process Button --}}
                <button id="processBtn" onclick="handleProcessPayment()"
                    style="width:100%;padding:22px;background:linear-gradient(135deg,#166534,#16a34a);color:#fff;border:none;border-radius:18px;font-size:18px;font-weight:900;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:12px;transition:all .2s;letter-spacing:.3px;box-shadow:0 10px 28px rgba(22,163,74,.4);">
                    <i class="bi bi-check-circle-fill" style="font-size:22px;"></i>
                    <span id="processBtnLabel">Collect &amp; Record Payment</span>
                </button>

                {{-- Hidden fields --}}
                <input type="hidden" id="selectedEnrollmentId">
                <input type="hidden" id="selectedStudentEmail">
                <input type="hidden" id="selectedStudentNameHidden">
                <input type="hidden" id="paymentMethod" value="cash">
                <input type="hidden" id="paymentType" value="">
                <input type="hidden" id="paymentNotes" value="">

            </div>{{-- /RIGHT --}}

        </div>{{-- /POS layout --}}
        </div>{{-- /process-content --}}
    </div>

    {{-- ── PAYMENT HISTORY SECTION ── --}}
    <div id="section-history" style="display:none;">

        {{-- Skeleton --}}
        <div id="history-skel" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <div><span class="skel" style="height:26px;width:200px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:340px;"></span></div>
                <span class="skel" style="height:38px;width:110px;border-radius:10px;"></span>
            </div>
            <div class="skel-wrap">
                <div style="display:flex;gap:10px;margin-bottom:18px;"><span class="skel" style="height:38px;width:150px;border-radius:9px;"></span><span class="skel" style="height:38px;width:140px;border-radius:9px;"></span><span class="skel" style="height:38px;width:140px;border-radius:9px;"></span><span class="skel" style="height:38px;flex:1;max-width:240px;border-radius:10px;"></span></div>
                @for($i=0;$i<7;$i++)
                <div class="skel-trow"><span class="skel" style="height:12px;width:24px;"></span><span class="skel" style="height:22px;width:120px;border-radius:6px;"></span><span class="skel" style="height:32px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;"></span><span class="skel" style="height:14px;flex:1;max-width:80px;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:30px;width:36px;border-radius:8px;"></span></div>
                @endfor
            </div>
        </div>

        {{-- Real content --}}
        <div id="history-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-clock-history me-2" style="color:#2471a3;"></i>Payment History</div>
                <div class="page-sub">All transactions processed through this cashier terminal.</div>
            </div>
            <button class="btn-primary-cash"><i class="bi bi-download"></i> Export</button>
        </div>
        <div class="card-box">
            <div class="card-box-header">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%;">
                    <input type="date" class="form-fld" style="max-width:150px;" value="{{ date('Y-m-d') }}">
                    <select class="form-fld" style="max-width:140px;">
                        <option>All Methods</option>
                        <option>Cash</option>
                        <option>GCash</option>
                    </select>
                    <select class="form-fld" style="max-width:140px;">
                        <option>All Status</option>
                        <option>Paid</option>
                        <option>Pending</option>
                    </select>
                    <div class="topbar-search" style="flex:1;max-width:240px;">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search…">
                    </div>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reference</th>
                            <th>Student</th>
                            <th>Grade</th>
                            <th>Payment Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date &amp; Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTbody">
                        @forelse($recentTransactions as $i => $tx)
                        <tr>
                            <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                            <td><span style="font-family:monospace;font-size:11.5px;background:#f8faff;padding:3px 8px;border-radius:6px;color:#475569;">{{ $tx->reference_number ?? '—' }}</span></td>
                            <td>
                                <div style="font-weight:700;color:#1e293b;">{{ $tx->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—' }}</div>
                            </td>
                            <td>{{ $tx->enrollment?->grade_level ? ucfirst($tx->enrollment->grade_level) : '—' }}</td>
                            <td style="font-size:12px;">{{ ucwords(str_replace('_',' ',$tx->payment_type ?? '—')) }}</td>
                            <td>
                                @if(in_array($tx->payment_method,['cash','Cash']))
                                    <span class="method-pill cash"><i class="bi bi-cash"></i> Cash</span>
                                @else
                                    <span class="method-pill gcash"><i class="bi bi-phone"></i> {{ ucfirst($tx->payment_method ?? '—') }}</span>
                                @endif
                            </td>
                            <td style="font-weight:700;color:#16a34a;">₱{{ number_format($tx->amount,2) }}</td>
                            <td>
                                @if($tx->status === 'completed')
                                    <span class="badge-status paid"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                @elseif($tx->status === 'pending')
                                    <span class="badge-status pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                                @else
                                    <span class="badge-status rejected">{{ ucfirst($tx->status) }}</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#64748b;">{{ $tx->processed_at?->format('M d, Y') }}<br><span style="font-size:10px;">{{ $tx->processed_at?->format('h:i A') }}</span></td>
                            <td>
                                <button onclick="reprintTx('{{ $tx->reference_number }}','{{ $tx->user?->name }}','{{ $tx->enrollment?->grade_level }}','{{ $tx->enrollment?->school_year }}','{{ $tx->payment_type }}','{{ $tx->payment_method }}','{{ $tx->amount }}','{{ $tx->processed_at?->format('M d, Y') }}','{{ $tx->processed_at?->format('h:i A') }}')"
                                    class="action-btn-sm" title="Print Receipt">
                                    <i class="bi bi-printer-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" style="text-align:center;padding:48px;color:#94a3b8;">
                                <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:10px;"></i>
                                No completed transactions yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>{{-- /history-content --}}
    </div>

    {{-- ── STUDENT LOOKUP SECTION ── --}}
    <div id="section-lookup" style="display:none;">

        {{-- Skeleton --}}
        <div id="lookup-skel" style="display:none;">
            <div style="margin-bottom:24px;"><span class="skel" style="height:26px;width:210px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:380px;"></span></div>
            <div class="skel-wrap">
                <div class="skel-hdr"><span class="skel" style="height:18px;width:150px;"></span><div style="display:flex;gap:8px;"><span class="skel" style="height:38px;width:280px;border-radius:10px;"></span><span class="skel" style="height:38px;width:90px;border-radius:10px;"></span></div></div>
                @for($i=0;$i<6;$i++)
                <div class="skel-trow"><span class="skel" style="height:22px;width:90px;border-radius:6px;"></span><span class="skel" style="height:32px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:22px;width:80px;border-radius:20px;"></span><span class="skel" style="height:22px;width:70px;border-radius:20px;"></span><span class="skel" style="height:14px;flex:1;max-width:90px;"></span></div>
                @endfor
            </div>
        </div>

        {{-- Real content --}}
        <div id="lookup-content" class="sec-content">
        <div class="page-header" style="margin-bottom:20px;">
            <div>
                <div class="page-title"><i class="bi bi-person-badge-fill me-2" style="color:#16a34a;"></i>Student Lookup</div>
                <div class="page-sub">Check a student's balance and payment status — read only. Use Process Payment to collect money.</div>
            </div>
        </div>

        {{-- Auto-loaded student table --}}
        <div id="lookupListPanel">
            <div class="card-box" style="margin-bottom:16px;">
                <div class="card-box-header">
                    <div class="card-box-title">
                        <i class="bi bi-people-fill" style="color:#16a34a;"></i>
                        All Students
                        <span id="lookupListCount" style="background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;margin-left:6px;">0</span>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <div style="display:flex;align-items:center;gap:8px;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 14px;min-width:260px;">
                            <i class="bi bi-search" style="color:#94a3b8;font-size:14px;flex-shrink:0;"></i>
                            <input type="text" id="lookupFilterInput" placeholder="Filter by name, grade, ref no…"
                                style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Poppins',sans-serif;"
                                oninput="filterLookupList(this.value)">
                        </div>
                        <button onclick="loadLookupList()" class="btn-outline-cash" style="white-space:nowrap;">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ref No.</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Section</th>
                                <th>School Year</th>
                                <th>Payment Plan</th>
                                <th>Payment Status</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody id="lookupListBody">
                            <tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;">
                                <i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading…
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Student detail panel (shown on row click) --}}
        <div id="lookupPlaceholder" style="display:none;">
            <div style="padding:70px 24px;background:#fff;border-radius:20px;text-align:center;color:#94a3b8;border:2px dashed #e2e8f0;max-width:680px;">
                <div style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#e8f0fb,#f1f5f9);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;box-shadow:0 4px 16px rgba(26,58,108,.1);">
                    <i class="bi bi-person-lines-fill" style="font-size:36px;color:#2471a3;opacity:.7;"></i>
                </div>
                <div style="font-size:17px;font-weight:800;color:#334155;margin-bottom:8px;">Search a Student</div>
                <div style="font-size:13px;color:#94a3b8;max-width:340px;margin:0 auto;line-height:1.6;">Type a student's name, LRN, or email address above to view their full account and payment details.</div>
                <div style="margin-top:22px;display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;background:#f8faff;padding:8px 14px;border-radius:20px;border:1.5px solid #e2e8f0;"><i class="bi bi-person" style="color:#2471a3;"></i>Student Name</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;background:#f8faff;padding:8px 14px;border-radius:20px;border:1.5px solid #e2e8f0;"><i class="bi bi-upc" style="color:#2471a3;"></i>LRN Number</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;background:#f8faff;padding:8px 14px;border-radius:20px;border:1.5px solid #e2e8f0;"><i class="bi bi-envelope" style="color:#2471a3;"></i>Email Address</div>
                </div>
            </div>
        </div>

        {{-- Student Profile Result --}}
        <div id="lookupResult" style="display:none;max-width:900px;">
            <button onclick="backToLookupList()" class="btn-outline-cash" style="margin-bottom:16px;">
                <i class="bi bi-arrow-left"></i> Back to Student List
            </button>

            {{-- Identity Banner --}}
            <div style="background:linear-gradient(145deg,#0f2451 0%,#1a3a6c 45%,#2471a3 100%);border-radius:22px;padding:28px;margin-bottom:18px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-30px;left:-20px;width:120px;height:120px;border-radius:50%;background:rgba(197,160,89,.06);pointer-events:none;"></div>
                <div style="display:flex;align-items:center;gap:20px;position:relative;flex-wrap:wrap;">
                    <div style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:900;flex-shrink:0;border:3px solid rgba(255,255,255,.3);box-shadow:0 6px 20px rgba(0,0,0,.2);" id="lkInitial">?</div>
                    <div style="flex:1;min-width:180px;">
                        <div style="font-size:22px;font-weight:900;color:#fff;margin-bottom:4px;letter-spacing:-.3px;" id="lkName">—</div>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <span style="font-size:12px;color:rgba(255,255,255,.65);display:flex;align-items:center;gap:5px;"><i class="bi bi-mortarboard-fill" style="font-size:11px;"></i><span id="lkGrade">—</span></span>
                            <span style="font-size:12px;color:rgba(255,255,255,.65);display:flex;align-items:center;gap:5px;"><i class="bi bi-calendar3" style="font-size:11px;"></i><span id="lkSY">—</span></span>
                            <span style="font-size:12px;color:rgba(255,255,255,.65);display:flex;align-items:center;gap:5px;"><i class="bi bi-envelope" style="font-size:11px;"></i><span id="lkEmail">—</span></span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
                        <div id="lkStatusBadge" style="font-size:11px;font-weight:700;padding:5px 14px;border-radius:20px;"></div>
                        <button onclick="goToProcessPayment()" id="lkProcessBtn"
                            style="padding:10px 20px;background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.3);color:#fff;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;"
                            onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                            <i class="bi bi-cash-coin"></i> Process Payment
                        </button>
                    </div>
                </div>
                {{-- Balance + Progress --}}
                <div style="margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.12);position:relative;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:10px;flex-wrap:wrap;gap:10px;">
                        <div>
                            <div style="font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Outstanding Balance</div>
                            <div style="font-size:34px;font-weight:900;color:#fff;line-height:1;letter-spacing:-1px;" id="lkBalance">₱0.00</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Amount Paid</div>
                            <div style="font-size:20px;font-weight:800;color:rgba(255,255,255,.85);" id="lkAmountPaid">₱0.00</div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,.12);border-radius:20px;height:8px;overflow:hidden;">
                        <div id="lkProgressBar" style="height:100%;background:linear-gradient(90deg,#c5a059,#f5d08a);border-radius:20px;width:0%;transition:width .7s ease;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:6px;">
                        <span style="font-size:11px;color:rgba(255,255,255,.4);" id="lkProgressLabel">0% paid</span>
                        <span style="font-size:11px;color:rgba(255,255,255,.4);" id="lkTotalFee">Total: ₱0.00</span>
                    </div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">

                {{-- Account Summary --}}
                <div class="card-box">
                    <div class="card-box-header">
                        <div class="card-box-title"><i class="bi bi-wallet2" style="color:#2471a3;"></i> Account Summary</div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#f1f5f9;">
                        <div style="background:#fff;padding:14px 16px;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Payment Plan</div>
                            <div id="lkPlan" style="font-size:14px;font-weight:800;color:#1a3a6c;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Total Fee</div>
                            <div id="lkTotal" style="font-size:14px;font-weight:800;color:#1e293b;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;border-top:1px solid #f1f5f9;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Amount Paid</div>
                            <div id="lkPaid" style="font-size:14px;font-weight:800;color:#16a34a;">—</div>
                        </div>
                        <div style="background:#fff;padding:14px 16px;border-top:1px solid #f1f5f9;">
                            <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Monthly</div>
                            <div id="lkMonthly" style="font-size:14px;font-weight:800;color:#1a3a6c;">—</div>
                        </div>
                    </div>
                </div>

                {{-- Student Info --}}
                <div class="card-box">
                    <div class="card-box-header">
                        <div class="card-box-title"><i class="bi bi-person-badge" style="color:#2471a3;"></i> Student Info</div>
                    </div>
                    <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;"><i class="bi bi-upc" style="color:#2471a3;"></i>LRN</span>
                            <span id="lkLrn" style="font-size:13px;font-weight:700;color:#1e293b;font-family:monospace;">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;"><i class="bi bi-envelope" style="color:#2471a3;"></i>Email</span>
                            <span id="lkEmailVal" style="font-size:12px;font-weight:600;color:#1e293b;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;"><i class="bi bi-mortarboard" style="color:#2471a3;"></i>Grade</span>
                            <span id="lkGradeVal" style="font-size:13px;font-weight:700;color:#1e293b;">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;"><i class="bi bi-calendar3" style="color:#2471a3;"></i>School Year</span>
                            <span id="lkSYVal" style="font-size:13px;font-weight:700;color:#1e293b;">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;"><i class="bi bi-circle-fill" style="color:#2471a3;font-size:8px;"></i>Pay Status</span>
                            <span id="lkPayStatusBadge" style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;"></span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Balance breakdown bar --}}
            <div class="card-box" style="margin-bottom:18px;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-bar-chart-fill" style="color:#2471a3;"></i> Payment Overview</div>
                    <span id="lkOverviewPct" style="font-size:12px;font-weight:700;color:#16a34a;background:#f0fdf4;padding:3px 10px;border-radius:20px;">0% complete</span>
                </div>
                <div style="padding:16px 20px 20px;">
                    <div style="display:flex;height:20px;border-radius:10px;overflow:hidden;gap:2px;margin-bottom:12px;">
                        <div id="lkBarPaid"      style="background:linear-gradient(90deg,#16a34a,#22c55e);height:100%;border-radius:8px 0 0 8px;transition:width .7s ease;width:0%;min-width:0;"></div>
                        <div id="lkBarRemaining" style="background:#f1f5f9;height:100%;flex:1;border-radius:0 8px 8px 0;"></div>
                    </div>
                    <div style="display:flex;gap:20px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;"><span style="width:12px;height:12px;border-radius:3px;background:linear-gradient(135deg,#16a34a,#22c55e);display:inline-block;flex-shrink:0;"></span>Paid: <strong id="lkBarPaidAmt" style="color:#16a34a;">₱0.00</strong></div>
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;"><span style="width:12px;height:12px;border-radius:3px;background:#e2e8f0;display:inline-block;flex-shrink:0;"></span>Remaining: <strong id="lkBarRemAmt" style="color:#dc2626;">₱0.00</strong></div>
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#64748b;"><span style="width:12px;height:12px;border-radius:3px;background:#f0f4ff;border:1.5px solid #c7d2fe;display:inline-block;flex-shrink:0;"></span>Total: <strong id="lkBarTotalAmt" style="color:#1a3a6c;">₱0.00</strong></div>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <button onclick="goToProcessPayment()" style="padding:14px 28px;background:linear-gradient(135deg,#166534,#16a34a);color:#fff;border:none;border-radius:14px;font-size:14px;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:9px;box-shadow:0 8px 20px rgba(22,163,74,.35);transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="bi bi-cash-coin" style="font-size:17px;"></i> Process Payment Now
                </button>
                <button onclick="clearLookup()" style="padding:14px 22px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:14px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;" onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background='#fff'">
                    <i class="bi bi-arrow-counterclockwise"></i> New Search
                </button>
            </div>

        </div>{{-- /lookupResult --}}
        </div>{{-- /lookup-content --}}
    </div>

    {{-- ── DAILY REPORT SECTION ── --}}
    <div id="section-daily" style="display:none;">

        {{-- Skeleton --}}
        <div id="daily-skel" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <div><span class="skel" style="height:26px;width:190px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:260px;"></span></div>
                <div style="display:flex;gap:10px;"><span class="skel" style="height:38px;width:160px;border-radius:10px;"></span><span class="skel" style="height:38px;width:130px;border-radius:10px;"></span></div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
                @for($i=0;$i<4;$i++)
                <div class="skel-wrap" style="margin-bottom:0;padding:20px;display:flex;align-items:center;gap:14px;">
                    <span class="skel" style="width:46px;height:46px;border-radius:12px;flex-shrink:0;"></span>
                    <div style="flex:1;"><span class="skel" style="height:26px;width:80px;margin-bottom:8px;"></span><span class="skel" style="height:13px;width:110px;"></span></div>
                </div>
                @endfor
            </div>
            <div class="skel-wrap">
                <div class="skel-hdr"><span class="skel" style="height:18px;width:200px;"></span><span class="skel" style="height:14px;width:100px;"></span></div>
                @for($i=0;$i<6;$i++)
                <div class="skel-trow"><span class="skel" style="height:12px;width:20px;"></span><span class="skel" style="height:14px;flex:1;max-width:70px;"></span><span class="skel" style="height:28px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;"></span><span class="skel" style="height:14px;flex:1;max-width:90px;"></span><span class="skel" style="height:22px;width:90px;border-radius:6px;"></span><span class="skel" style="height:30px;width:36px;border-radius:8px;"></span></div>
                @endfor
            </div>
        </div>

        {{-- Real content --}}
        <div id="daily-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-calendar-day me-2" style="color:#9333ea;"></i>Daily Report</div>
                <div class="page-sub" id="dailyReportSub">Summary of payments collected today.</div>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <input type="date" class="form-fld" id="dailyReportDate" value="{{ date('Y-m-d') }}"
                    style="max-width:160px;" onchange="loadDailyReport(this.value)">
                <button onclick="printDailyReport()" class="btn-primary-cash">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;" id="dailyStatCards">
            <div class="stat-card blue">
                <div class="stat-icon-wrap blue"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value" id="drTotal">₱0.00</div>
                <div class="stat-label">Total Collected</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon-wrap green"><i class="bi bi-wallet2"></i></div>
                <div class="stat-value" id="drCash">₱0.00</div>
                <div class="stat-label">Cash Payments</div>
            </div>
            <div class="stat-card gold">
                <div class="stat-icon-wrap gold"><i class="bi bi-phone"></i></div>
                <div class="stat-value" id="drOnline">₱0.00</div>
                <div class="stat-label">Online / E-Wallet</div>
            </div>
            <div class="stat-card" style="background:#fff;">
                <div class="stat-icon-wrap" style="background:#f3e8ff;"><i class="bi bi-receipt" style="color:#9333ea;"></i></div>
                <div class="stat-value" id="drCount" style="color:#9333ea;">0</div>
                <div class="stat-label">Transactions</div>
                <div class="stat-card" style="display:none;"></div>
            </div>
        </div>

        <div class="card-box" id="dailyReportCard">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-table" style="color:#9333ea;"></i> Transaction Breakdown</div>
                <span id="dailyReportDateLabel" style="font-size:12px;color:#64748b;font-weight:600;"></span>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Student</th>
                            <th>Grade</th>
                            <th>Payment Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Receipt No.</th>
                            <th>Print</th>
                        </tr>
                    </thead>
                    <tbody id="dailyReportBody">
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-arrow-repeat" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>{{-- /daily-content --}}
    </div>

    {{-- ── RECEIPTS SECTION ── --}}
    <div id="section-receipts" style="display:none;">

        {{-- Skeleton --}}
        <div id="receipts-skel" style="display:none;">
            <div style="margin-bottom:24px;"><span class="skel" style="height:26px;width:160px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:310px;"></span></div>
            <div class="skel-wrap">
                <div class="skel-hdr"><div style="display:flex;align-items:center;gap:10px;"><span class="skel" style="height:18px;width:120px;"></span><span class="skel" style="height:22px;width:34px;border-radius:20px;"></span></div><div style="display:flex;gap:8px;"><span class="skel" style="height:38px;width:280px;border-radius:10px;"></span><span class="skel" style="height:38px;width:90px;border-radius:10px;"></span></div></div>
                @for($i=0;$i<7;$i++)
                <div class="skel-trow"><span class="skel" style="height:12px;width:24px;"></span><span class="skel" style="height:22px;width:110px;border-radius:6px;"></span><span class="skel" style="height:28px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:14px;flex:1;"></span><span class="skel" style="height:22px;width:60px;border-radius:20px;"></span><span class="skel" style="height:14px;flex:1;max-width:90px;"></span><span class="skel" style="height:28px;flex:1;border-radius:6px;"></span><span class="skel" style="height:30px;width:70px;border-radius:8px;"></span></div>
                @endfor
            </div>
        </div>

        {{-- Real content --}}
        <div id="receipts-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-receipt me-2" style="color:#b45309;"></i>Receipts</div>
                <div class="page-sub">All issued receipts. Click Print to reprint any receipt.</div>
            </div>
        </div>
        <div class="card-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <i class="bi bi-receipt-cutoff" style="color:#b45309;"></i> All Receipts
                    <span id="receiptsCount" style="background:#fffbeb;color:#b45309;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;margin-left:6px;">0</span>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <div style="display:flex;align-items:center;gap:8px;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 14px;min-width:240px;">
                        <i class="bi bi-search" style="color:#94a3b8;font-size:14px;flex-shrink:0;"></i>
                        <input type="text" id="receiptsFilter" placeholder="Search by name or reference…"
                            style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Poppins',sans-serif;"
                            oninput="filterReceipts(this.value)">
                    </div>
                    <button onclick="loadReceipts()" class="btn-outline-cash" style="white-space:nowrap;">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>OR / Ref No.</th>
                            <th>Student</th>
                            <th>Grade</th>
                            <th>Payment Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Print</th>
                        </tr>
                    </thead>
                    <tbody id="receiptsBody">
                        <tr><td colspan="9" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading receipts…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>{{-- /receipts-content --}}
    </div>

    {{-- ── COLLECTION SUMMARY SECTION ── --}}
    <div id="section-collection" style="display:none;">

        {{-- Skeleton --}}
        <div id="collection-skel" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <div><span class="skel" style="height:26px;width:230px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:270px;"></span></div>
                <span class="skel" style="height:38px;width:130px;border-radius:10px;"></span>
            </div>
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:20px;">
                <div class="skel-wrap" style="margin-bottom:0;">
                    <div class="skel-hdr"><span class="skel" style="height:17px;width:200px;"></span><span class="skel" style="height:14px;width:90px;"></span></div>
                    <span class="skel" style="height:210px;border-radius:10px;"></span>
                </div>
                <div class="skel-wrap" style="margin-bottom:0;">
                    <div class="skel-hdr"><span class="skel" style="height:17px;width:140px;"></span></div>
                    <span class="skel" style="height:170px;width:170px;border-radius:50%;margin:0 auto;display:block;"></span>
                    <div style="display:flex;justify-content:center;gap:12px;margin-top:12px;"><span class="skel" style="height:12px;width:50px;"></span><span class="skel" style="height:12px;width:50px;"></span><span class="skel" style="height:12px;width:50px;"></span></div>
                </div>
            </div>
            <div class="skel-wrap">
                <div class="skel-hdr"><span class="skel" style="height:17px;width:200px;"></span></div>
                <span class="skel" style="height:195px;border-radius:10px;"></span>
            </div>
        </div>

        {{-- Real content --}}
        <div id="collection-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-bar-chart-fill me-2" style="color:#2471a3;"></i>Collection Summary</div>
                <div class="page-sub">Monthly and annual collection overview.</div>
            </div>
            <button class="btn-primary-cash"><i class="bi bi-download"></i> Export Excel</button>
        </div>
        @php
            $csColMonths=[]; $csColTotals=[];
            for($i=5;$i>=0;$i--){
                $m=now()->subMonths($i);
                $csColMonths[]=$m->format('M Y');
                $csColTotals[]=(float)\App\Models\PaymentTransaction::where('status','completed')
                    ->whereYear('processed_at',$m->year)->whereMonth('processed_at',$m->month)->sum('amount');
            }
            $csColCash  = (float)\App\Models\PaymentTransaction::where('status','completed')->where('payment_method','cash')->sum('amount');
            $csColGcash = (float)\App\Models\PaymentTransaction::where('status','completed')->where('payment_method','gcash')->sum('amount');
            $csColOther = (float)\App\Models\PaymentTransaction::where('status','completed')->whereNotIn('payment_method',['cash','gcash'])->sum('amount');
        @endphp

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:20px;">
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-graph-up-arrow" style="color:#2471a3;"></i> Monthly Collection Trend</div>
                    <span style="font-size:11px;color:#94a3b8;">Last 6 months</span>
                </div>
                <div class="card-box-body" style="height:240px;"><canvas id="csColLine"></canvas></div>
            </div>
            <div class="card-box" style="margin-bottom:0;">
                <div class="card-box-header">
                    <div class="card-box-title"><i class="bi bi-pie-chart-fill" style="color:#2471a3;"></i> All-time by Method</div>
                </div>
                <div class="card-box-body" style="height:240px;display:flex;justify-content:center;"><canvas id="csColMethodDoughnut"></canvas></div>
            </div>
        </div>
        <div class="card-box">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-bar-chart-fill" style="color:#2471a3;"></i> Monthly Bar Comparison</div>
            </div>
            <div class="card-box-body" style="height:220px;"><canvas id="csColBar"></canvas></div>
        </div>
        </div>{{-- /collection-content --}}
    </div>

    {{-- ── SETTINGS SECTION ── --}}
    <div id="section-settings" style="display:none;">

        {{-- Skeleton --}}
        <div id="settings-skel" style="display:none;">
            <div style="margin-bottom:24px;"><span class="skel" style="height:26px;width:150px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:290px;"></span></div>
            <div class="skel-wrap" style="max-width:540px;">
                <div class="skel-hdr"><span class="skel" style="height:17px;width:120px;"></span></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    @for($i=0;$i<3;$i++)
                    <div><span class="skel" style="height:12px;width:80px;margin-bottom:6px;"></span><span class="skel" style="height:40px;border-radius:8px;"></span></div>
                    @endfor
                </div>
            </div>
            <div class="skel-wrap" style="max-width:540px;">
                <div class="skel-hdr"><span class="skel" style="height:17px;width:150px;"></span></div>
                @for($i=0;$i<3;$i++)
                <div style="margin-bottom:14px;"><span class="skel" style="height:12px;width:130px;margin-bottom:6px;"></span><span class="skel" style="height:42px;border-radius:8px;"></span></div>
                @endfor
                <span class="skel" style="height:42px;width:160px;border-radius:10px;margin-top:8px;"></span>
            </div>
        </div>

        {{-- Real content --}}
        <div id="settings-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-gear-fill me-2" style="color:#64748b;"></i>Settings</div>
                <div class="page-sub">Manage your account and security settings.</div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="card-box" style="max-width:540px;margin-bottom:20px;">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-person-circle" style="color:#2471a3;"></i> My Profile</div>
            </div>
            <div class="card-box-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-lbl">Full Name</label>
                        <input type="text" class="form-fld" value="{{ auth('cashier')->user()->name }}" readonly style="background:#f1f5f9;color:#64748b;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Email</label>
                        <input type="text" class="form-fld" value="{{ auth('cashier')->user()->email }}" readonly style="background:#f1f5f9;color:#64748b;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Role</label>
                        <input type="text" class="form-fld" value="Cashier" readonly style="background:#f1f5f9;color:#64748b;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card-box" style="max-width:540px;">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-shield-lock" style="color:#c5a059;"></i> Change Password</div>
            </div>
            <div class="card-box-body">

                @if(session('password_success'))
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#16a34a;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-check-circle-fill"></i> {{ session('password_success') }}
                    </div>
                @endif
                @error('current_password')
                    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#dc2626;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('cashier.change-password') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-lbl">Current Password <span style="color:#dc2626;">*</span></label>
                            <input type="password" name="current_password" class="form-fld" placeholder="Enter your current password" required>
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">New Password <span style="color:#dc2626;">*</span></label>
                            <input type="password" name="new_password" class="form-fld" placeholder="At least 8 characters" required minlength="8">
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">Confirm New Password <span style="color:#dc2626;">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-fld" placeholder="Re-enter new password" required>
                        </div>
                    </div>
                    <div style="margin-top:16px;padding:12px 14px;background:#f8f9fa;border-radius:8px;border-left:3px solid #c5a059;font-size:12px;color:#666;margin-bottom:18px;">
                        <i class="bi bi-shield-exclamation" style="color:#c5a059;"></i>
                        Use at least 8 characters. Never share your password with anyone.
                    </div>
                    <button type="submit" class="btn-primary-cash">
                        <i class="bi bi-lock-fill"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
        </div>{{-- /settings-content --}}
    </div>

</div>

{{-- Old process payment modal removed — using section-process full page instead --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Back-button guard ──────────────────────────────────────────────────────
    // Replaces the "previous" history entry so the browser back button always
    // lands on the cashier login page rather than an arbitrary 404 URL.
    (function () {
        if (window.history && window.history.pushState) {
            // Push a dummy "login" entry underneath the current dashboard entry.
            window.history.pushState({ cashierPage: 'login' }, '', '{{ route("cashier.login") }}');
            window.history.pushState({ cashierPage: 'dashboard' }, '', '{{ route("cashier.dashboard") }}');

            window.addEventListener('popstate', function (e) {
                // User pressed back — always send them to the login route.
                // If the session is still alive, showLogin() redirects them back
                // to the dashboard automatically. If expired, they see the form.
                window.location.replace('{{ route("cashier.login") }}');
            });
        }
    })();
    // ─────────────────────────────────────────────────────────────────────────

    var sections = ['dashboard','process','history','lookup','daily','receipts','collection','settings'];

    // Initialise on load — show dashboard with skeleton first
    document.addEventListener('DOMContentLoaded', function () {
        showSection('dashboard', document.querySelector('.sidebar-link[data-section="dashboard"]'));
    });

    // Skeleton delays per section (ms the shimmer plays before content appears)
    var _skelDelay = { dashboard: 600, collection: 500 };

    function showSection(name, btn) {
        var delay = _skelDelay[name] || 400;

        // 1. Hide all sections immediately
        sections.forEach(function(s) {
            var el = document.getElementById('section-' + s);
            if (el) { el.style.display = 'none'; el.style.opacity = ''; }
        });

        // 2. Update active nav link
        document.querySelectorAll('.sidebar-link').forEach(function(el) { el.classList.remove('active'); });
        if (btn) {
            btn.classList.add('active');
        } else {
            var m = document.querySelector('.sidebar-link[data-section="' + name + '"]');
            if (m) m.classList.add('active');
        }

        // 3. Start data fetches in parallel with the skeleton (so data is ready sooner)
        if (name === 'process') { clearPaymentForm(); loadStudentList(); }
        if (name === 'lookup') {
            document.getElementById('lookupListPanel').style.display  = 'block';
            document.getElementById('lookupResult').style.display     = 'none';
            document.getElementById('lookupPlaceholder').style.display = 'none';
            var lf = document.getElementById('lookupFilterInput');
            if (lf) lf.value = '';
            loadLookupList();
        }
        if (name === 'daily')    { var dr = document.getElementById('dailyReportDate'); loadDailyReport(dr ? dr.value : ''); }
        if (name === 'receipts') { loadReceipts(); }

        // 4. Show the section container
        var sec = document.getElementById('section-' + name);
        if (!sec) return;
        sec.style.display = 'block';

        // 5. Dashboard uses its own embedded skeleton (db-skeleton / db-content)
        if (name === 'dashboard') {
            var dbSkel = document.getElementById('db-skeleton');
            var dbCont = document.getElementById('db-content');
            if (dbSkel) { dbSkel.style.display = 'block'; }
            if (dbCont) { dbCont.style.display = 'none'; dbCont.style.opacity = '0'; }
            setTimeout(function() {
                if (dbSkel) dbSkel.style.display = 'none';
                if (dbCont) {
                    dbCont.style.display = 'block';
                    void dbCont.offsetWidth;
                    dbCont.style.opacity = '1';
                    setTimeout(initDashboardCharts, 80);
                }
            }, delay);
            return;
        }

        // 6. All other sections: per-section skeleton + content divs
        var skelEl = document.getElementById(name + '-skel');
        var contEl = document.getElementById(name + '-content');

        if (skelEl) { skelEl.style.display = 'block'; }
        if (contEl) { contEl.style.display = 'none'; contEl.style.opacity = '0'; }

        setTimeout(function() {
            if (skelEl) skelEl.style.display = 'none';
            if (contEl) {
                contEl.style.display = 'block';
                void contEl.offsetWidth;   // force reflow so transition fires
                contEl.style.opacity = '1';
                if (name === 'collection') setTimeout(initCollectionCharts, 80);
            }
        }, delay);
    }

    // Sidebar cashier avatar initial
    (function() {
        var nameEl = document.getElementById('sb-name');
        var avatarEl = document.getElementById('sb-avatar');
        if (nameEl && avatarEl) {
            var initials = (nameEl.textContent.trim().match(/\b\w/g) || []).slice(0,2).join('').toUpperCase();
            avatarEl.textContent = initials || 'C';
        }
    })();

    // Sync sidebar today stats from dashboard stat cards
    function syncSidebarStats() {
        var amountEl  = document.querySelector('.stat-card.blue .stat-value');
        var countCard = document.querySelector('.stat-card.green .stat-value');
        var sbAmt  = document.getElementById('sb-today-amount');
        var sbCnt  = document.getElementById('sb-today-count');
        if (sbAmt && amountEl) sbAmt.textContent = amountEl.textContent;
        if (sbCnt && countCard) sbCnt.textContent = countCard.textContent;
    }
    setTimeout(syncSidebarStats, 300);

    function openProcessModal() {
        showSection('process', document.querySelector('.sidebar-link[data-section="process"]'));
    }

    function openReceiptModal() {
        showSection('receipts');
    }

    function selectMethod(method) {
        ['cash','gcash'].forEach(function(m) {
            var btn = document.getElementById('pm-' + m);
            if (!btn) return;
            var isActive = m === method;
            btn.style.borderColor = isActive ? '#2471a3' : '#e2e8f0';
            btn.style.background  = isActive ? '#e8f0fb' : '#fff';
            btn.style.boxShadow   = isActive ? '0 0 0 3px rgba(26,58,108,.1)' : 'none';
        });
        document.getElementById('gcash-ref-row').style.display = method === 'gcash' ? 'block' : 'none';
    }

    // Live date
    function updateDate() {
        var el = document.getElementById('live-date');
        if (!el) return;
        var now = new Date();
        el.textContent = now.toLocaleDateString('en-PH', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    updateDate();
    setInterval(updateDate, 60000);

    /* ── Process Payment JS ── */
    var searchTimer = null;
    var selectedStudent = null;

    var _searchUrl      = '{{ route("cashier.students.search") }}';
    var _listUrl        = '{{ route("cashier.students.list") }}';
    var _loginUrl       = '{{ route("cashier.login") }}';
    var _optionsUrl     = '{{ route("cashier.payment.options") }}';
    var _setPlanUrl     = '{{ route("cashier.enrollment.set-plan") }}';
    var _csrfToken      = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
    var _logoUrl        = '{{ asset("images/logo.png") }}';
    var _dailyUrl       = '{{ route("cashier.daily.report") }}';
    var _receiptsUrl    = '{{ route("cashier.receipts.list") }}';
    var _cashierName    = '{{ auth("cashier")->user()->name ?? "Cashier" }}';
    var _allStudents = [];

    /* ── Student list ── */
    function loadStudentList() {
        var tbody = document.getElementById('studentListBody');
        var count = document.getElementById('studentListCount');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading students…</td></tr>';
        fetch(_listUrl, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            _allStudents = data;
            if (count) count.textContent = data.length;
            renderStudentList(data);
        })
        .catch(function() {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#dc2626;"><i class="bi bi-exclamation-circle" style="font-size:24px;display:block;margin-bottom:8px;"></i>Failed to load. <button onclick="loadStudentList()" style="margin-top:8px;padding:6px 16px;background:#1a3a6c;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:12px;">Retry</button></td></tr>';
        });
    }

    function renderStudentList(students) {
        var tbody = document.getElementById('studentListBody');
        var count = document.getElementById('studentListCount');
        if (count) count.textContent = students.length;
        if (!students.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>No approved students found.</td></tr>';
            return;
        }
        var payBadge = function(status) {
            if (status === 'paid')    return '<span class="badge-status paid"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>';
            if (status === 'partial') return '<span class="badge-status partial"><i class="bi bi-clock-fill me-1"></i>Partial</span>';
            return '<span class="badge-status pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
        };
        tbody.innerHTML = students.map(function(s) {
            var grade    = s.grade_level ? (s.grade_level.charAt(0).toUpperCase() + s.grade_level.slice(1)) : '—';
            var bal      = parseFloat(s.balance) || 0;
            var totalFee = parseFloat(s.total_fee) || 0;
            var enc      = JSON.stringify(s).replace(/"/g, '&quot;');
            /* Balance display: red if pending with fee set, green if paid, grey if no fee configured yet */
            var balColor = (s.payment_status === 'paid') ? '#16a34a'
                         : (totalFee > 0 && bal > 0)     ? '#dc2626'
                         : '#94a3b8';
            var balText  = totalFee === 0
                         ? '<span style="font-size:11px;color:#94a3b8;">Fee not set</span>'
                         : '₱' + bal.toLocaleString('en-PH', {minimumFractionDigits:2});
            return '<tr style="cursor:pointer;" onclick="selectStudentFromList(' + enc + ')">'
                + '<td><span style="font-family:monospace;font-size:11.5px;background:#f8faff;padding:3px 8px;border-radius:6px;color:#475569;">' + (s.reference_no || '—') + '</span></td>'
                + '<td><div style="font-weight:700;color:#1e293b;">' + s.name + '</div><div style="font-size:11px;color:#94a3b8;">' + s.email + '</div></td>'
                + '<td style="font-weight:600;">' + grade + '</td>'
                + '<td>' + (s.section && s.section !== '—' ? s.section : '<span style="color:#cbd5e1;">—</span>') + '</td>'
                + '<td style="font-size:12px;color:#64748b;">' + (s.school_year || '—') + '</td>'
                + '<td>' + payBadge(s.payment_status) + '</td>'
                + '<td style="font-weight:800;color:' + balColor + ';">' + balText + '</td>'
                + '<td><button onclick="event.stopPropagation();selectStudentFromList(' + enc + ')" class="btn-primary-cash" style="padding:7px 16px;font-size:12px;border-radius:8px;"><i class="bi bi-cash-coin me-1"></i>Pay</button></td>'
                + '</tr>';
        }).join('');
    }

    function filterStudentList(q) {
        if (!q) { renderStudentList(_allStudents); return; }
        var low = q.toLowerCase();
        renderStudentList(_allStudents.filter(function(s) {
            return s.name.toLowerCase().includes(low)
                || (s.reference_no || '').toLowerCase().includes(low)
                || (s.grade_level || '').toLowerCase().includes(low)
                || (s.section || '').toLowerCase().includes(low);
        }));
    }

    function selectStudentFromList(s) {
        document.getElementById('studentListPanel').style.display = 'none';
        selectStudent(s);
    }

    function backToStudentList() {
        clearPaymentForm();
        document.getElementById('studentListPanel').style.display = 'block';
        document.getElementById('payPanel').style.display = 'none';
        var f = document.getElementById('studentListFilter');
        if (f) f.value = '';
        loadStudentList(); /* always reload from server so statuses are fresh */
    }

    /* ── Student Lookup List ── */
    var _allLookupStudents = [];

    function loadLookupList() {
        var tbody = document.getElementById('lookupListBody');
        var count = document.getElementById('lookupListCount');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading…</td></tr>';
        fetch(_listUrl, { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(data){
            _allLookupStudents = data;
            if (count) count.textContent = data.length;
            renderLookupList(data);
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#dc2626;">Failed to load. <button onclick="loadLookupList()" style="margin-left:6px;padding:4px 12px;background:#1a3a6c;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:11px;">Retry</button></td></tr>';
        });
    }

    function renderLookupList(students) {
        var tbody = document.getElementById('lookupListBody');
        var count = document.getElementById('lookupListCount');
        if (count) count.textContent = students.length;
        if (!students.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>No students found.</td></tr>';
            return;
        }
        var planMap = {A:'Plan A – Cash',B:'Plan B – Monthly',C:'Plan C – Elem Monthly',D:'Plan D – Nur/K Monthly'};
        var payBadge = function(s) {
            if (s==='paid')    return '<span class="badge-status paid"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>';
            if (s==='partial') return '<span class="badge-status partial"><i class="bi bi-clock-fill me-1"></i>Partial</span>';
            return '<span class="badge-status pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
        };
        tbody.innerHTML = students.map(function(s) {
            var grade = s.grade_level ? (s.grade_level.charAt(0).toUpperCase()+s.grade_level.slice(1)) : '—';
            var bal   = parseFloat(s.balance)||0;
            var tf    = parseFloat(s.total_fee)||0;
            var balColor = s.payment_status==='paid' ? '#16a34a' : (tf>0&&bal>0 ? '#dc2626' : '#94a3b8');
            var balText  = tf===0 ? '<span style="font-size:11px;color:#94a3b8;">Fee not set</span>' : '₱'+bal.toLocaleString('en-PH',{minimumFractionDigits:2});
            var enc = JSON.stringify(s).replace(/"/g,'&quot;');
            return '<tr style="cursor:pointer;" onclick="openLookupDetail('+enc+')">'
                +'<td><span style="font-family:monospace;font-size:11.5px;background:#f8faff;padding:3px 8px;border-radius:6px;color:#475569;">'+(s.reference_no||'—')+'</span></td>'
                +'<td><div style="font-weight:700;color:#1e293b;">'+s.name+'</div><div style="font-size:11px;color:#94a3b8;">'+s.email+'</div></td>'
                +'<td style="font-weight:600;">'+grade+'</td>'
                +'<td>'+(s.section&&s.section!=='—'?s.section:'<span style="color:#cbd5e1;">—</span>')+'</td>'
                +'<td style="font-size:12px;color:#64748b;">'+(s.school_year||'—')+'</td>'
                +'<td style="font-size:12px;color:#475569;">'+(s.payment_option ? (planMap[s.payment_option]||s.payment_option) : '<span style="color:#94a3b8;">Not set</span>')+'</td>'
                +'<td>'+payBadge(s.payment_status)+'</td>'
                +'<td style="font-weight:800;color:'+balColor+';">'+balText+'</td>'
                +'</tr>';
        }).join('');
    }

    function filterLookupList(q) {
        if (!q) { renderLookupList(_allLookupStudents); return; }
        var low = q.toLowerCase();
        renderLookupList(_allLookupStudents.filter(function(s){
            return s.name.toLowerCase().includes(low)
                || (s.reference_no||'').toLowerCase().includes(low)
                || (s.grade_level||'').toLowerCase().includes(low)
                || (s.section||'').toLowerCase().includes(low);
        }));
    }

    function openLookupDetail(s) {
        document.getElementById('lookupListPanel').style.display = 'none';
        document.getElementById('lookupPlaceholder').style.display = 'none';
        selectLookupStudent(s);
    }

    function backToLookupList() {
        document.getElementById('lookupResult').style.display = 'none';
        document.getElementById('lookupPlaceholder').style.display = 'none';
        document.getElementById('lookupListPanel').style.display = 'block';
        document.getElementById('lookupFilterInput').value = '';
        renderLookupList(_allLookupStudents);
    }

    /* ── Daily Report ── */
    var _dailyRows = [];

    function loadDailyReport(date) {
        var tbody  = document.getElementById('dailyReportBody');
        var label  = document.getElementById('dailyReportDateLabel');
        if (!date) date = document.getElementById('dailyReportDate').value;
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading…</td></tr>';
        fetch(_dailyUrl+'?date='+encodeURIComponent(date), { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            _dailyRows = d.rows || [];
            document.getElementById('drTotal').textContent  = '₱'+d.total;
            document.getElementById('drCash').textContent   = '₱'+d.cash;
            document.getElementById('drOnline').textContent = '₱'+d.online;
            document.getElementById('drCount').textContent  = d.count;
            if (label) label.textContent = d.date;
            renderDailyRows(_dailyRows);
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:#dc2626;">Failed to load report. Try again.</td></tr>';
        });
    }

    function renderDailyRows(rows) {
        var tbody = document.getElementById('dailyReportBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;"></i>No transactions for this date.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(r,i){
            return '<tr>'
                +'<td style="color:#94a3b8;">'+(i+1)+'</td>'
                +'<td style="font-size:12px;color:#64748b;">'+r.time+'</td>'
                +'<td style="font-weight:700;color:#1e293b;">'+r.student+'</td>'
                +'<td>'+r.grade+'</td>'
                +'<td style="font-size:12px;">'+r.type+'</td>'
                +'<td><span class="method-pill '+(r.method.toLowerCase()==='cash'?'cash':'gcash')+'">'+r.method+'</span></td>'
                +'<td style="font-weight:700;color:#16a34a;">₱'+r.amount+'</td>'
                +'<td><span style="font-family:monospace;font-size:11px;background:#f8faff;padding:2px 7px;border-radius:5px;color:#475569;">'+r.reference+'</span></td>'
                +'<td><button onclick="reprintTx(\''+r.or_no+'\',\''+r.student+'\',\''+r.grade+'\',\''+r.school_year+'\',\''+r.type+'\',\''+r.method+'\',\''+r.amount+'\',\''+r.time+'\',\''+r.time+'\')" class="action-btn-sm" title="Print"><i class="bi bi-printer-fill"></i></button></td>'
                +'</tr>';
        }).join('');
    }

    function printDailyReport() {
        var date   = document.getElementById('dailyReportDate').value || 'Today';
        var total  = document.getElementById('drTotal').textContent;
        var cash   = document.getElementById('drCash').textContent;
        var online = document.getElementById('drOnline').textContent;
        var count  = document.getElementById('drCount').textContent;
        var w = window.open('','_blank','width=700,height=800,scrollbars=yes');
        if (!w) { alert('Pop-ups blocked. Please allow pop-ups to print.'); return; }
        var rows = _dailyRows.map(function(r,i){
            return '<tr><td>'+(i+1)+'</td><td>'+r.time+'</td><td>'+r.student+'</td><td>'+r.grade+'</td><td>'+r.type+'</td><td>'+r.method+'</td><td style="text-align:right;">₱'+r.amount+'</td><td>'+r.reference+'</td></tr>';
        }).join('') || '<tr><td colspan="8" style="text-align:center;padding:16px;color:#888;">No transactions</td></tr>';
        var html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Daily Report – '+date+'</title>'
            +'<style>body{font-family:Arial,sans-serif;font-size:12px;padding:20px;}h2{margin:0;font-size:16px;}h3{margin:4px 0;font-size:12px;font-weight:400;}'
            +'table{width:100%;border-collapse:collapse;margin-top:16px;}th,td{border:1px solid #ccc;padding:6px 8px;text-align:left;}'
            +'th{background:#1a3a6c;color:#fff;}.stats{display:flex;gap:20px;margin:16px 0;}.stat-box{border:1px solid #e2e8f0;border-radius:8px;padding:10px 16px;text-align:center;min-width:120px;}'
            +'</style></head>'
            +'<body onload="setTimeout(function(){window.print();},400);">'
            +'<div style="text-align:center;margin-bottom:16px;">'
            +'<img src="'+_logoUrl+'" style="width:55px;height:55px;object-fit:contain;border-radius:50%;" onerror="this.style.display=\'none\'"><br>'
            +'<h2>IEMELIF LEARNING CENTER</h2><h3>General Tinio, Nueva Ecija</h3>'
            +'<h3 style="margin-top:8px;font-weight:700;font-size:14px;">DAILY COLLECTION REPORT</h3>'
            +'<div style="font-size:12px;color:#555;">Date: <strong>'+date+'</strong></div></div>'
            +'<div class="stats"><div class="stat-box"><div style="font-size:10px;color:#888;text-transform:uppercase;">Total Collected</div><div style="font-size:18px;font-weight:700;color:#1a3a6c;">'+total+'</div></div>'
            +'<div class="stat-box"><div style="font-size:10px;color:#888;text-transform:uppercase;">Cash</div><div style="font-size:18px;font-weight:700;color:#16a34a;">'+cash+'</div></div>'
            +'<div class="stat-box"><div style="font-size:10px;color:#888;text-transform:uppercase;">Online</div><div style="font-size:18px;font-weight:700;color:#2471a3;">'+online+'</div></div>'
            +'<div class="stat-box"><div style="font-size:10px;color:#888;text-transform:uppercase;">Transactions</div><div style="font-size:18px;font-weight:700;color:#9333ea;">'+count+'</div></div></div>'
            +'<table><thead><tr><th>#</th><th>Time</th><th>Student</th><th>Grade</th><th>Type</th><th>Method</th><th>Amount</th><th>Reference</th></tr></thead><tbody>'+rows+'</tbody></table>'
            +'<div style="margin-top:30px;display:flex;justify-content:space-between;">'
            +'<div><div style="border-top:1px solid #000;padding-top:4px;min-width:180px;text-align:center;margin-top:36px;">Prepared by: '+_cashierName+'</div></div>'
            +'<div><div style="border-top:1px solid #000;padding-top:4px;min-width:180px;text-align:center;margin-top:36px;">Acknowledged by</div></div>'
            +'</div></body></html>';
        w.document.open(); w.document.write(html); w.document.close();
    }

    /* ── Receipts ── */
    var _allReceipts = [];

    function loadReceipts() {
        var tbody = document.getElementById('receiptsBody');
        var count = document.getElementById('receiptsCount');
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading receipts…</td></tr>';
        fetch(_receiptsUrl, { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(data){
            _allReceipts = data;
            if (count) count.textContent = data.length;
            renderReceipts(data);
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:#dc2626;">Failed to load receipts. <button onclick="loadReceipts()" style="margin-left:6px;padding:4px 12px;background:#1a3a6c;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:11px;">Retry</button></td></tr>';
        });
    }

    function renderReceipts(receipts) {
        var tbody = document.getElementById('receiptsBody');
        var count = document.getElementById('receiptsCount');
        if (count) count.textContent = receipts.length;
        if (!receipts.length) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>No receipts found.</td></tr>';
            return;
        }
        tbody.innerHTML = receipts.map(function(r,i){
            return '<tr>'
                +'<td style="color:#94a3b8;">'+(i+1)+'</td>'
                +'<td><span style="font-family:monospace;font-size:11.5px;background:#fffbeb;padding:3px 8px;border-radius:6px;color:#b45309;">'+(r.or_no||'—')+'</span></td>'
                +'<td><div style="font-weight:700;color:#1e293b;">'+r.student+'</div></td>'
                +'<td style="font-weight:600;">'+r.grade+'</td>'
                +'<td style="font-size:12px;">'+r.type+'</td>'
                +'<td><span class="method-pill '+(r.method.toLowerCase()==='cash'?'cash':'gcash')+'">'+r.method+'</span></td>'
                +'<td style="font-weight:700;color:#16a34a;">₱'+r.amount+'</td>'
                +'<td style="font-size:12px;color:#64748b;">'+r.date+'<br><span style="font-size:10px;">'+r.time+'</span></td>'
                +'<td><button onclick="reprintTx(\''+r.or_no+'\',\''+r.student+'\',\''+r.grade+'\',\''+r.school_year+'\',\''+r.type+'\',\''+r.method+'\',\''+r.amount+'\',\''+r.date+'\',\''+r.time+'\')" class="btn-primary-cash" style="padding:7px 14px;font-size:12px;border-radius:8px;" title="Print Receipt"><i class="bi bi-printer-fill me-1"></i>Print</button></td>'
                +'</tr>';
        }).join('');
    }

    function filterReceipts(q) {
        if (!q) { renderReceipts(_allReceipts); return; }
        var low = q.toLowerCase();
        renderReceipts(_allReceipts.filter(function(r){
            return r.student.toLowerCase().includes(low) || (r.or_no||'').toLowerCase().includes(low);
        }));
    }

    /* ── Payment Plan Selector ── */
    var _planGrade = null;

    function setPlanCardState(state) {
        /* state: 'loading' | 'options' | 'confirmed' */
        var box     = document.getElementById('planSelectorBox');
        var header  = document.getElementById('planSelectorHeader');
        var title   = document.getElementById('planSelectorTitle');
        var changeBtn = document.getElementById('changePlanBtn');
        var summaryStrip = document.getElementById('planSummaryStrip');
        var optionsWrap  = document.getElementById('planOptionsWrap');
        if (state === 'confirmed') {
            box.style.border   = '2px solid #16a34a';
            header.style.background = 'linear-gradient(135deg,#f0fdf4,#fff)';
            title.style.color  = '#166534';
            title.innerHTML    = '<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> Payment Plan';
            changeBtn.style.display = 'inline-flex';
            summaryStrip.style.display = 'block';
            optionsWrap.style.display  = 'none';
        } else {
            box.style.border   = '2px solid #f97316';
            header.style.background = 'linear-gradient(135deg,#fff7ed,#fff)';
            title.style.color  = '#c2410c';
            title.innerHTML    = '<i class="bi bi-clipboard-check-fill" style="color:#f97316;"></i> Select Payment Plan';
            changeBtn.style.display = 'none';
            summaryStrip.style.display = 'none';
            optionsWrap.style.display  = 'block';
        }
    }

    function expandPlanOptions() {
        setPlanCardState('select');
        window._selectedPlan = null;
        loadPaymentPlanOptions(_planGrade);
    }

    function loadPaymentPlanOptions(gradeLevel) {
        _planGrade = gradeLevel;
        var card = document.getElementById('planSelectorCard');
        var list = document.getElementById('planOptionsList');
        card.style.display = 'block';
        setPlanCardState('select');
        list.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:22px;display:block;margin-bottom:8px;"></i>Loading plans…</div>';

        fetch(_optionsUrl + '?grade=' + encodeURIComponent(gradeLevel), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken }
        })
        .then(function(r) { return r.json(); })
        .then(function(options) { renderPlanOptions(options, gradeLevel); })
        .catch(function() {
            list.innerHTML = '<div style="color:#dc2626;padding:12px;font-size:13px;">Failed to load plans. <button onclick="loadPaymentPlanOptions(\'' + gradeLevel + '\')" style="margin-left:6px;padding:4px 12px;background:#1a3a6c;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:11px;">Retry</button></div>';
        });
    }

    function renderPlanOptions(options, gradeLevel) {
        var list = document.getElementById('planOptionsList');
        var planMeta = {
            A: { label: 'Plan A — Cash Basis',      color: '#16a34a', icon: 'bi-cash-stack',       desc: 'Full payment in one transaction with ₱1,501 discount.' },
            B: { label: 'Plan B — Monthly (All)',   color: '#2471a3', icon: 'bi-calendar-month',   desc: 'Downpayment + 9 monthly payments of ₱1,056.10.' },
            C: { label: 'Plan C — Monthly (Elem)',  color: '#7c3aed', icon: 'bi-calendar2-week',   desc: 'For Grade 1–6 only. Downpayment + 9 monthly payments of ₱1,278.32.' },
            D: { label: 'Plan D — Monthly (Nur/K)', color: '#b45309', icon: 'bi-calendar2-heart',  desc: 'For Nursery/Kinder only. Downpayment + 9 monthly payments of ₱1,278.32.' }
        };
        var fmt = function(v) { return '₱' + Number(v||0).toLocaleString('en-PH', {minimumFractionDigits:2}); };
        var html = '';
        ['A','B','C','D'].forEach(function(opt) {
            if (!options[opt]) return;
            var bd   = options[opt];
            var meta = planMeta[opt];
            var total = bd.total_due || bd.base_total || 0;
            var summary = opt === 'A'
                ? 'Total: <b>' + fmt(total) + '</b> <span style="color:#16a34a;font-size:11px;">(saves ' + fmt(bd.discount||0) + ')</span>'
                : 'Down: <b>' + fmt(bd.downpayment) + '</b> + ' + bd.duration_months + '× ' + fmt(bd.monthly_amount) + '/mo = <b>' + fmt(total) + '</b>';

            html += '<div style="border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;margin-bottom:10px;cursor:pointer;transition:all .2s;background:#fff;" '
                  + 'id="planOpt_' + opt + '" '
                  + 'onclick="selectPlanOption(\'' + opt + '\',\'' + gradeLevel + '\')" '
                  + 'onmouseover="this.style.borderColor=\'' + meta.color + '\';this.style.background=\'#f8faff\'" '
                  + 'onmouseout="this.style.borderColor=(window._selectedPlan===\'' + opt + '\'?\''+meta.color+'\':\'#e2e8f0\');this.style.background=(window._selectedPlan===\'' + opt + '\'?\'#f0f7ff\':\'#fff\')">'
                  + '<div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">'
                  + '<div style="width:34px;height:34px;border-radius:8px;background:' + meta.color + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi ' + meta.icon + '" style="color:#fff;font-size:16px;"></i></div>'
                  + '<div><div style="font-size:13px;font-weight:700;color:#1e293b;">' + meta.label + '</div>'
                  + '<div style="font-size:11px;color:#64748b;">' + meta.desc + '</div></div>'
                  + '<div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;" id="planRadio_' + opt + '"></div>'
                  + '</div>'
                  + '<div style="font-size:12px;color:#64748b;padding-left:44px;">' + summary + '</div>'
                  + '</div>';
        });
        html += '<button id="confirmPlanBtn" onclick="confirmPaymentPlan()" '
              + 'style="display:none;width:100%;margin-top:4px;padding:12px;background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:\'Poppins\',sans-serif;">'
              + '<i class="bi bi-check-circle-fill me-2"></i>Confirm Plan &amp; Continue</button>';
        list.innerHTML = html;
    }

    window._selectedPlan = null;

    function selectPlanOption(opt, grade) {
        window._selectedPlan = opt;
        /* Visual feedback */
        ['A','B','C','D'].forEach(function(o) {
            var el = document.getElementById('planOpt_' + o);
            var rb = document.getElementById('planRadio_' + o);
            if (!el) return;
            var colors = {A:'#16a34a',B:'#2471a3',C:'#7c3aed',D:'#b45309'};
            if (o === opt) {
                el.style.borderColor  = colors[o];
                el.style.background   = '#f0f7ff';
                el.style.boxShadow    = '0 0 0 3px ' + colors[o] + '22';
                rb.style.background   = colors[o];
                rb.style.borderColor  = colors[o];
                rb.innerHTML          = '<i class="bi bi-check" style="color:#fff;font-size:10px;"></i>';
            } else {
                el.style.borderColor  = '#e2e8f0';
                el.style.background   = '#fff';
                el.style.boxShadow    = 'none';
                rb.style.background   = 'transparent';
                rb.style.borderColor  = '#e2e8f0';
                rb.innerHTML          = '';
            }
        });
        var btn = document.getElementById('confirmPlanBtn');
        if (btn) btn.style.display = 'block';
    }

    function confirmPaymentPlan() {
        if (!window._selectedPlan || !selectedStudent) return;
        var btn = document.getElementById('confirmPlanBtn');
        btn.disabled  = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Saving plan…';

        fetch(_setPlanUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
            body: JSON.stringify({ enrollment_id: selectedStudent.enrollment_id, payment_option: window._selectedPlan })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) { alert(data.error); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirm Plan & Continue'; return; }
            /* Update selectedStudent with new fee data */
            var bd = data.breakdown;
            selectedStudent.total_fee          = bd.total_due;
            selectedStudent.balance            = bd.total_due - (selectedStudent.payment_amount || 0);
            selectedStudent.payment_option     = window._selectedPlan;
            selectedStudent.payment_type       = bd.payment_type;
            selectedStudent.downpayment_amount = bd.downpayment || 0;
            selectedStudent.monthly_amount     = bd.monthly_amount || 0;
            /* Collapse plan card into confirmed summary */
            var planLabelsMap = {A:'Plan A — Cash Basis',B:'Plan B — Monthly',C:'Plan C — Monthly (Elem)',D:'Plan D — Monthly (Nur/K)'};
            var fmtS = function(v){ return '₱'+Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };
            var summaryText = (planLabelsMap[window._selectedPlan]||window._selectedPlan)
                + '&nbsp;&nbsp;|&nbsp;&nbsp;Total: ' + fmtS(bd.total_due);
            document.getElementById('planSummaryText').innerHTML = summaryText;
            setPlanCardState('confirmed');
            var fmt = function(v){ return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };
            document.getElementById('studentBalance').textContent  = fmt(selectedStudent.balance);
            document.getElementById('acctTotalFee').textContent    = fmt(selectedStudent.total_fee);
            document.getElementById('acctRemaining').textContent   = fmt(selectedStudent.balance);
            document.getElementById('cardTotalFee').textContent    = 'Total: ' + fmt(selectedStudent.total_fee);
            var total = Number(selectedStudent.total_fee||0);
            var paid  = Number(selectedStudent.payment_amount||0);
            var pct   = total > 0 ? Math.min(100,Math.round((paid/total)*100)) : 0;
            document.getElementById('payProgressBar').style.width  = pct + '%';
            document.getElementById('payProgressLabel').textContent = pct + '% paid';
            /* Rebuild quick presets with new plan */
            rebuildQuickPresets(selectedStudent);
            window._selectedPlan = null;
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirm Plan & Continue';
            alert('Failed to save plan. Please try again.');
        });
    }

    function searchStudent(q) {
        clearTimeout(searchTimer);
        var dd = document.getElementById('studentDropdown');
        if (q.length < 2) { dd.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            dd.innerHTML = '<div style="padding:14px;font-size:13px;color:#94a3b8;text-align:center;">Searching…</div>';
            dd.style.display = 'block';
            fetch(_searchUrl + '?q=' + encodeURIComponent(q), {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || ''
                }
            })
            .then(function(r) {
                if (r.status === 401 || r.status === 403) {
                    dd.innerHTML = '<div style="padding:14px;font-size:13px;color:#dc2626;text-align:center;">Session expired. <a href="' + _loginUrl + '">Log in again</a>.</div>';
                    dd.style.display = 'block';
                    return null;
                }
                return r.json();
            })
            .then(function(data) { if (data) renderStudentDropdown(data); })
            .catch(function() {
                dd.innerHTML = '<div style="padding:14px;font-size:13px;color:#dc2626;text-align:center;">Search failed — please refresh.</div>';
                dd.style.display = 'block';
            });
        }, 300);
    }

    function renderStudentDropdown(students) {
        var dd = document.getElementById('studentDropdown');
        if (!students.length) {
            dd.innerHTML = '<div style="padding:14px;font-size:13px;color:#94a3b8;text-align:center;">No students found</div>';
            dd.style.display = 'block';
            return;
        }
        dd.innerHTML = students.map(function(s) {
            return '<div onclick="selectStudent(' + JSON.stringify(s).replace(/"/g,'&quot;') + ')" ' +
                'style="display:flex;align-items:center;gap:12px;padding:10px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:background .1s;" ' +
                'onmouseover="this.style.background=\'#f0f6ff\'" onmouseout="this.style.background=\'#fff\'">' +
                '<div style="width:34px;height:34px;border-radius:50%;background:#1a3a6c;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">' + s.name.charAt(0).toUpperCase() + '</div>' +
                '<div><div style="font-size:13px;font-weight:600;color:#1e293b;">' + s.name + '</div>' +
                '<div style="font-size:11px;color:#64748b;">Grade ' + (s.grade_level||'—') + ' &nbsp;|&nbsp; Balance: ₱' + Number(s.balance||0).toLocaleString('en-PH',{minimumFractionDigits:2}) + '</div></div>' +
                '</div>';
        }).join('');
        dd.style.display = 'block';
    }

    function selectStudent(s) {
        selectedStudent = s;
        document.getElementById('selectedEnrollmentId').value       = s.enrollment_id || '';
        document.getElementById('selectedStudentEmail').value       = s.email || '';
        document.getElementById('selectedStudentNameHidden').value  = s.name;

        var fmt = function(v){ return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };
        var planLabels = { A:'Full Payment', B:'Installment B', C:'Installment C', D:'Installment D' };

        document.getElementById('studentInitial').textContent    = s.name.charAt(0).toUpperCase();
        document.getElementById('studentName').textContent       = s.name;
        document.getElementById('studentGrade').innerHTML        = '<i class="bi bi-mortarboard-fill" style="font-size:10px;"></i> Grade ' + (s.grade_level||'—');
        document.getElementById('studentSchoolYear').textContent = 'S.Y. ' + (s.school_year||'—');
        document.getElementById('studentBalance').textContent    = fmt(s.balance);

        // Card top: amount paid + progress bar
        document.getElementById('cardAmountPaid').textContent = fmt(s.payment_amount);
        var total = Number(s.total_fee||0);
        var paid  = Number(s.payment_amount||0);
        var pct   = total > 0 ? Math.min(100, Math.round((paid/total)*100)) : 0;
        document.getElementById('payProgressBar').style.width = pct + '%';
        document.getElementById('payProgressLabel').textContent = pct + '% paid';
        document.getElementById('cardTotalFee').textContent = 'Total: ' + fmt(total);

        // Account details grid
        document.getElementById('acctPaymentPlan').textContent = planLabels[s.payment_option] || 'Not set';
        document.getElementById('acctTotalFee').textContent    = total > 0 ? fmt(total) : '—';
        document.getElementById('acctAmountPaid').textContent  = fmt(s.payment_amount);
        document.getElementById('acctRemaining').textContent   = fmt(s.balance);
        document.getElementById('acctMonthly').textContent     = s.monthly_amount > 0 ? fmt(s.monthly_amount) : '—';

        // Update process section today stats
        syncProcessStats();

        var badge = document.getElementById('studentPayStatusBadge');
        var statusMap = { paid: ['Fully Paid','rgba(22,163,74,.25)','#fff'], partial: ['Partially Paid','rgba(180,83,9,.25)','#fff'], unpaid: ['Unpaid','rgba(220,38,38,.25)','#fff'], pending: ['Pending','rgba(100,116,139,.2)','rgba(255,255,255,.8)'] };
        var st = statusMap[s.payment_status] || statusMap.pending;
        badge.textContent = st[0];
        badge.style.background = st[1];
        badge.style.color = st[2];

        rebuildQuickPresets(s);

        var slp = document.getElementById('studentListPanel');
        if (slp) slp.style.display = 'none';
        document.getElementById('payPanel').style.display = 'grid';
        document.getElementById('paySuccessBanner').style.display = 'none';
        document.getElementById('processBtn').style.display = 'flex';
        selectPayMethod('cash');

        /* Always show plan card — expand if no plan yet, collapse to summary if plan exists */
        var hasPlan = s.payment_option && parseFloat(s.total_fee) > 0;
        if (hasPlan) {
            /* Show confirmed state with Change Plan button */
            _planGrade = s.grade_level || 'nursery';
            var planLabelsMap = {A:'Plan A — Cash Basis',B:'Plan B — Monthly',C:'Plan C — Monthly (Elem)',D:'Plan D — Monthly (Nur/K)'};
            var fmtP = function(v){ return '₱'+Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };
            var summaryText = (planLabelsMap[s.payment_option] || s.payment_option)
                + '&nbsp;&nbsp;|&nbsp;&nbsp;Total: ' + fmtP(s.total_fee);
            document.getElementById('planSummaryText').innerHTML = summaryText;
            document.getElementById('planSelectorCard').style.display = 'block';
            setPlanCardState('confirmed');
        } else {
            loadPaymentPlanOptions(s.grade_level || 'nursery');
        }
    }

    function rebuildQuickPresets(s) {
        var btns = [];
        if (s.downpayment_amount > 0 && (s.payment_amount||0) < s.downpayment_amount) {
            btns.push({ label: 'Downpayment', amount: s.downpayment_amount, type: 'Downpayment', color: '#1a3a6c' });
        }
        if (s.monthly_amount > 0) {
            btns.push({ label: 'Monthly Install.', amount: s.monthly_amount, type: 'Monthly Installment', color: '#2471a3' });
        }
        if (s.balance > 0) {
            btns.push({ label: 'Full Balance', amount: s.balance, type: 'Full Payment', color: '#16a34a' });
        }
        var qRow  = document.getElementById('quickAmountRow');
        var qBtns = document.getElementById('quickAmountBtns');
        if (btns.length > 0) {
            qBtns.innerHTML = btns.map(function(b) {
                return '<button type="button" onclick="quickFillAmount(' + b.amount + ',\'' + b.type + '\',this)" '
                    + 'style="padding:9px 16px;border-radius:10px;border:2px solid ' + b.color + ';background:#fff;color:' + b.color + ';'
                    + 'font-size:12px;font-weight:800;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:7px;box-shadow:0 2px 8px rgba(0,0,0,.06);">'
                    + '<i class="bi bi-lightning-fill"></i>' + b.label
                    + '<span style="background:' + b.color + ';color:#fff;padding:2px 8px;border-radius:6px;font-size:11px;">₱' + Number(b.amount).toLocaleString('en-PH',{minimumFractionDigits:0}) + '</span>'
                    + '</button>';
            }).join('');
            qRow.style.display = 'block';
        } else {
            qRow.style.display = 'none';
        }
    }

    function quickFillAmount(amount, type, btn) {
        document.getElementById('paymentAmount').value = Number(amount).toFixed(2);
        document.getElementById('paymentType').value = type;
        document.getElementById('paymentTypeLabel').textContent = type;
        document.querySelectorAll('#quickAmountBtns button').forEach(function(b) {
            b.style.opacity = '0.5'; b.style.transform = 'scale(.95)';
        });
        if (btn) { btn.style.opacity = '1'; btn.style.transform = 'scale(1.06)'; }
        var wrap = document.getElementById('amountInputWrap');
        wrap.style.borderColor = '#16a34a'; wrap.style.background = '#fff';
        setTimeout(function(){ wrap.style.borderColor = '#e2e8f0'; wrap.style.background = '#f8faff'; }, 800);
        updateTransactionSummary();
    }

    function selectPayMethod(method) {
        var cashBtn   = document.getElementById('methodCashBtn');
        var onlineBtn = document.getElementById('methodOnlineBtn');
        var isOnline  = method === 'online';

        // Cash button — icon is inside a div now
        cashBtn.style.borderColor = isOnline ? '#e2e8f0' : '#16a34a';
        cashBtn.style.background  = isOnline ? '#fff'    : '#f0fdf4';
        cashBtn.style.boxShadow   = isOnline ? 'none'    : '0 4px 14px rgba(22,163,74,.2)';

        // Online button
        onlineBtn.style.borderColor = isOnline ? '#2471a3' : '#e2e8f0';
        onlineBtn.style.background  = isOnline ? '#eff6ff' : '#fff';
        onlineBtn.style.boxShadow   = isOnline ? '0 4px 14px rgba(36,113,163,.2)' : 'none';

        document.getElementById('onlineMethodRow').style.display  = isOnline ? 'block' : 'none';
        document.getElementById('xenditLinkResult').style.display = 'none';

        if (!isOnline) {
            document.getElementById('paymentMethod').value = 'cash';
            document.getElementById('processBtnLabel').textContent = 'Collect & Record Payment';
            document.getElementById('processBtn').querySelector('i').className = 'bi bi-check-circle-fill';
            document.getElementById('processBtn').style.background = 'linear-gradient(135deg,#166534,#16a34a)';
            document.getElementById('processBtn').style.boxShadow  = '0 10px 28px rgba(22,163,74,.4)';
            // Reset online method selection
            document.querySelectorAll('.online-method-opt').forEach(function(b) {
                b.style.borderColor = '#e2e8f0'; b.style.background = '#fff'; b.style.transform = 'scale(1)';
            });
        } else {
            document.getElementById('paymentMethod').value = '';
            document.getElementById('processBtnLabel').textContent = 'Generate Payment Link';
            document.getElementById('processBtn').querySelector('i').className = 'bi bi-link-45deg';
            document.getElementById('processBtn').style.background = 'linear-gradient(135deg,#1a3a6c,#2471a3)';
            document.getElementById('processBtn').style.boxShadow  = '0 10px 28px rgba(26,58,108,.4)';
        }
        updateTransactionSummary();
    }

    function selectOnlineMethod(val) {
        document.getElementById('paymentMethod').value = val;
        // Highlight selected e-wallet button
        document.querySelectorAll('.online-method-opt').forEach(function(b) {
            var isActive = b.dataset.method === val;
            b.style.borderColor = isActive ? '#2471a3' : '#e2e8f0';
            b.style.background  = isActive ? '#eff6ff' : '#fff';
            b.style.transform   = isActive ? 'scale(1.06)' : 'scale(1)';
        });
        updateTransactionSummary();
    }

    function clearAmount() {
        document.getElementById('paymentAmount').value = '';
        document.getElementById('paymentType').value = '';
        document.getElementById('paymentTypeLabel').textContent = 'Select type';
        document.getElementById('txnSummary').style.display = 'none';
        document.querySelectorAll('#quickAmountBtns button').forEach(function(b) {
            b.style.opacity = '1'; b.style.transform = 'scale(1)';
        });
    }

    function updateTransactionSummary() {
        var amount = parseFloat(document.getElementById('paymentAmount').value);
        var method = document.getElementById('paymentMethod').value;
        var type   = document.getElementById('paymentType').value;
        var name   = document.getElementById('selectedStudentNameHidden').value;
        var box    = document.getElementById('txnSummary');
        if (!amount || amount <= 0 || !name) { box.style.display = 'none'; return; }
        var methodLabels = { cash:'Cash', gcash:'GCash', maya:'Maya', grabpay:'GrabPay', bank:'Bank Transfer', otc:'Over-the-Counter' };
        document.getElementById('txnStudent').textContent = name;
        document.getElementById('txnType').textContent    = type || 'Payment';
        document.getElementById('txnMethod').textContent  = methodLabels[method] || (method ? method : 'Not selected');
        document.getElementById('txnAmount').textContent  = '₱' + amount.toLocaleString('en-PH',{minimumFractionDigits:2});
        box.style.display = 'block';
    }

    function syncProcessStats() {
        fetch('/cashier/students/search?q=_stats_today', { headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
            .catch(function(){});
        // Sync from sidebar stats if available
        var sbAmt   = document.getElementById('sb-today-amount');
        var sbCount = document.getElementById('sb-today-count');
        if (sbAmt)   document.getElementById('pp-today-amt').textContent   = sbAmt.textContent;
        if (sbCount) document.getElementById('pp-today-count').textContent = sbCount.textContent;
    }

    function handleProcessPayment() {
        var enrollmentId = document.getElementById('selectedEnrollmentId').value;
        var amount       = document.getElementById('paymentAmount').value;
        var method       = document.getElementById('paymentMethod').value;
        var type         = document.getElementById('paymentType').value;

        if (!enrollmentId) { alert('Please select a student first.'); return; }
        if (!amount || amount <= 0) { alert('Please enter a valid amount.'); return; }
        if (!method) { alert('Please select a payment method.'); return; }
        if (!type)   { alert('Please select a payment type.'); return; }

        var isXendit = ['gcash','maya','grabpay','bank','otc'].includes(method);

        if (isXendit) {
            generateXenditLink(enrollmentId, amount, method, type);
        } else {
            processCash(enrollmentId, amount, type);
        }
    }

    function processCash(enrollmentId, amount, type) {
        var btn = document.getElementById('processBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing…';

        fetch('/cashier/payment/cash', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({
                enrollment_id: enrollmentId,
                amount: amount,
                payment_type: type,
                notes: document.getElementById('paymentNotes').value
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Build receipt data for reprint
                _lastReceiptData = {
                    or_no: data.reference || ('OR-' + String(Date.now()).slice(-6)),
                    date: new Date().toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}),
                    time: new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'}),
                    student_name: document.getElementById('selectedStudentNameHidden').value,
                    grade_level: selectedStudent ? (selectedStudent.grade_level || '—') : '—',
                    school_year: selectedStudent ? (selectedStudent.school_year || '—') : '—',
                    description: type,
                    amount: Number(amount).toLocaleString('en-PH',{minimumFractionDigits:2}),
                    method: 'Cash',
                    received_by: '{{ auth('cashier')->user()->name }}'
                };
                // Show success banner
                document.getElementById('paySuccessRef').textContent = 'Reference: ' + (data.reference || '—') + '  ·  Amount: ₱' + Number(amount).toLocaleString('en-PH',{minimumFractionDigits:2});
                document.getElementById('paySuccessBanner').style.display = 'block';
                btn.style.display = 'none';
                // Auto print
                setTimeout(function(){ if (_lastReceiptData) printOfficialReceipt(_lastReceiptData); }, 300);
            } else {
                alert('❌ Error: ' + (data.message || 'Something went wrong.'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:20px;"></i><span id="processBtnLabel">Collect & Record Payment</span>';
            }
        })
        .catch(function() {
            alert('❌ Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:20px;"></i><span id="processBtnLabel">Collect & Record Payment</span>';
        });
    }

    function generateXenditLink(enrollmentId, amount, method, type) {
        var btn = document.getElementById('processBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating…';

        fetch('/cashier/payment/xendit-link', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({
                enrollment_id: enrollmentId,
                amount: amount,
                payment_method: method,
                payment_type: type,
                student_name: document.getElementById('selectedStudentNameHidden').value,
                student_email: document.getElementById('selectedStudentEmail').value,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('xenditLinkUrl').value = data.invoice_url;
                document.getElementById('xenditLinkOpenBtn').href = data.invoice_url;
                if (data.expiry) {
                    document.getElementById('xenditLinkExpiry').textContent = 'Expires: ' + new Date(data.expiry).toLocaleString('en-PH');
                }
                document.getElementById('xenditLinkResult').style.display = 'block';
                window.open(data.invoice_url, '_blank');
            } else {
                alert('❌ ' + (data.message || 'Failed to generate link.'));
            }
        })
        .catch(() => alert('❌ Network error. Please try again.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-link-45deg" style="font-size:20px;"></i><span id="processBtnLabel">Generate Payment Link</span>';
        });
    }

    function copyXenditLink() {
        var url = document.getElementById('xenditLinkUrl').value;
        navigator.clipboard.writeText(url).then(function() {
            var icon = document.getElementById('cashierCopyIcon');
            if (icon) { icon.className = 'bi bi-clipboard-check'; setTimeout(function(){ icon.className = 'bi bi-clipboard'; }, 1500); }
        });
    }

    var _lastReceiptData = null;

    function clearPaymentForm() {
        selectedStudent = null;
        document.getElementById('selectedEnrollmentId').value = '';
        document.getElementById('selectedStudentEmail').value = '';
        document.getElementById('selectedStudentNameHidden').value = '';
        var slp = document.getElementById('studentListPanel');
        if (slp) slp.style.display = 'block';
        document.getElementById('payPanel').style.display = 'none';
        document.getElementById('paymentType').value = '';
        document.getElementById('paymentMethod').value = 'cash';
        document.getElementById('paymentAmount').value = '';
        document.getElementById('paymentNotes').value = '';
        document.getElementById('paymentTypeLabel').textContent = 'Select type';
        document.getElementById('xenditLinkResult').style.display = 'none';
        document.getElementById('onlineMethodRow').style.display = 'none';
        document.getElementById('txnSummary').style.display = 'none';
        document.getElementById('paySuccessBanner').style.display = 'none';
        document.getElementById('processBtn').style.display = 'flex';
        // Reset notes textarea
        var notesField = document.getElementById('paymentNotesField');
        if (notesField) notesField.value = '';
        // Reset online method buttons
        document.querySelectorAll('.online-method-opt').forEach(function(b) {
            b.style.borderColor = '#e2e8f0'; b.style.background = '#fff'; b.style.transform = 'scale(1)';
        });
        // Reset progress bar
        document.getElementById('payProgressBar').style.width = '0%';
        document.getElementById('payProgressLabel').textContent = '0% paid';
        var btn = document.getElementById('processBtn');
        btn.disabled = false;
        btn.style.background = 'linear-gradient(135deg,#166534,#16a34a)';
        btn.style.boxShadow  = '0 10px 28px rgba(22,163,74,.4)';
        btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:22px;"></i><span id="processBtnLabel">Collect &amp; Record Payment</span>';
    }

    function printLastReceipt() {
        if (!_lastReceiptData) return;
        printOfficialReceipt(_lastReceiptData);
    }

    function reprintTx(ref, name, grade, sy, type, method, amount, date, time) {
        printOfficialReceipt({
            or_no: ref, student_name: name, grade_level: grade,
            school_year: sy, description: type, method: method,
            amount: Number(amount).toLocaleString('en-PH',{minimumFractionDigits:2}),
            date: date, time: time,
            received_by: '{{ auth("cashier")->user()->name ?? "Cashier" }}'
        });
    }

    function printOfficialReceipt(d) {
        var w = window.open('', '_blank', 'width=420,height=640,scrollbars=yes');
        if (!w) { alert('Pop-ups are blocked. Please allow pop-ups for this site to print receipts.'); return; }
        var html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            + '<title>Official Receipt</title>'
            + '<style>'
            + 'body{font-family:"Courier New",monospace;font-size:12px;margin:0;padding:20px 24px;color:#000;}'
            + '.center{text-align:center;} .bold{font-weight:700;} .right{text-align:right;}'
            + 'h2{margin:0;font-size:15px;letter-spacing:.5px;} h3{margin:3px 0;font-size:11px;font-weight:400;}'
            + '.solid{border-top:2px solid #000;margin:10px 0;} .dash{border-top:1px dashed #000;margin:8px 0;}'
            + 'table{width:100%;border-collapse:collapse;}'
            + 'td{padding:3px 0;vertical-align:top;}'
            + 'td:last-child{text-align:right;}'
            + '.amt{font-size:16px;font-weight:900;}'
            + '.footer{margin-top:24px;font-size:10px;text-align:center;color:#444;}'
            + '.sig{margin-top:36px;border-top:1px solid #000;padding-top:4px;text-align:center;font-size:11px;}'
            + '@media print{body{padding:10px;}}'
            + '</style></head>'
            + '<body onload="setTimeout(function(){window.print();},400);">'
            + '<div class="center">'
            + '<img src="' + _logoUrl + '" alt="ILC Logo" style="width:70px;height:70px;object-fit:contain;border-radius:50%;margin-bottom:8px;" onerror="this.style.display=\'none\'">'
            + '<h2>IEMELIF LEARNING CENTER</h2>'
            + '<h3>General Tinio, Nueva Ecija</h3>'
            + '<h3>Tel: 0951-989-9685</h3></div>'
            + '<div class="solid"></div>'
            + '<div class="center bold" style="font-size:13px;letter-spacing:1px;">OFFICIAL RECEIPT</div>'
            + '<div class="solid"></div>'
            + '<table>'
            + '<tr><td>OR No.:</td><td class="bold">' + (d.or_no||'—') + '</td></tr>'
            + '<tr><td>Date:</td><td>' + (d.date||'—') + '</td></tr>'
            + '<tr><td>Time:</td><td>' + (d.time||'—') + '</td></tr>'
            + '</table>'
            + '<div class="dash"></div>'
            + '<table>'
            + '<tr><td>Student:</td><td class="bold">' + (d.student_name||'—') + '</td></tr>'
            + '<tr><td>Grade Level:</td><td>' + (d.grade_level ? (d.grade_level.charAt(0).toUpperCase()+d.grade_level.slice(1)) : '—') + '</td></tr>'
            + '<tr><td>School Year:</td><td>' + (d.school_year||'—') + '</td></tr>'
            + '</table>'
            + '<div class="dash"></div>'
            + '<table>'
            + '<tr><td>Description:</td><td>' + (d.description||'Payment') + '</td></tr>'
            + '<tr><td>Method:</td><td>' + (d.method||'Cash') + '</td></tr>'
            + '</table>'
            + '<div class="solid"></div>'
            + '<table><tr><td class="bold amt">AMOUNT PAID:</td><td class="bold amt right">&#x20B1;' + (d.amount||'0.00') + '</td></tr></table>'
            + '<div class="solid"></div>'
            + '<div style="margin-top:14px;font-size:11px;">Received by: <span class="bold">' + (d.received_by||'Cashier') + '</span></div>'
            + '<div class="sig">Cashier Signature</div>'
            + '<div class="footer">'
            + '<div>Thank you for your payment!</div>'
            + '<div style="margin-top:3px;">This is a computer-generated receipt.</div>'
            + '</div>'
            + '</body></html>';
        w.document.open();
        w.document.write(html);
        w.document.close();
    }

    /* ══ STUDENT LOOKUP ══ */
    var _lookupStudent = null;
    var _lookupTimer   = null;

    function lookupSearch(q) {
        clearTimeout(_lookupTimer);
        var dd = document.getElementById('lookupDropdown');
        if (q.length < 2) { dd.style.display = 'none'; return; }
        _lookupTimer = setTimeout(function() {
            fetch('/cashier/students/search?q=' + encodeURIComponent(q), {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) { renderLookupDropdown(data); })
            .catch(function() {});
        }, 280);
    }

    function renderLookupDropdown(students) {
        var dd = document.getElementById('lookupDropdown');
        if (!students.length) {
            dd.innerHTML = '<div style="padding:16px;font-size:13px;color:#94a3b8;text-align:center;"><i class="bi bi-search me-2"></i>No students found</div>';
            dd.style.display = 'block';
            return;
        }
        dd.innerHTML = students.map(function(s) {
            var statusColors = { paid:'#16a34a', partial:'#b45309', unpaid:'#dc2626', pending:'#64748b' };
            var statusLabels = { paid:'Fully Paid', partial:'Partial', unpaid:'Unpaid', pending:'Pending' };
            var color = statusColors[s.payment_status] || '#64748b';
            var label = statusLabels[s.payment_status] || 'Pending';
            return '<div onclick="selectLookupStudent(' + JSON.stringify(s).replace(/"/g,'&quot;') + ')" '
                + 'style="display:flex;align-items:center;gap:13px;padding:12px 16px;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:background .1s;" '
                + 'onmouseover="this.style.background=\'#f0f6ff\'" onmouseout="this.style.background=\'#fff\'">'
                + '<div style="width:40px;height:40px;border-radius:50%;background:#1a3a6c;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:15px;flex-shrink:0;">' + s.name.charAt(0).toUpperCase() + '</div>'
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-size:13px;font-weight:700;color:#1e293b;">' + s.name + '</div>'
                + '<div style="font-size:11px;color:#64748b;">Grade ' + (s.grade_level||'—') + ' · S.Y. ' + (s.school_year||'—') + '</div>'
                + '</div>'
                + '<div style="text-align:right;flex-shrink:0;">'
                + '<div style="font-size:12px;font-weight:700;color:#dc2626;">₱' + Number(s.balance||0).toLocaleString('en-PH',{minimumFractionDigits:2}) + '</div>'
                + '<div style="font-size:10px;font-weight:700;color:' + color + ';">' + label + '</div>'
                + '</div>'
                + '</div>';
        }).join('');
        dd.style.display = 'block';
    }

    function selectLookupStudent(s) {
        _lookupStudent = s;
        document.getElementById('lookupListPanel').style.display = 'none';
        document.getElementById('lookupPlaceholder').style.display = 'none';
        document.getElementById('lookupResult').style.display = 'block';

        var fmt = function(v) { return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };
        var planLabels = { A:'Full Payment', B:'Installment B', C:'Installment C', D:'Installment D' };

        // Banner
        document.getElementById('lkInitial').textContent   = s.name.charAt(0).toUpperCase();
        document.getElementById('lkName').textContent      = s.name;
        document.getElementById('lkGrade').textContent     = 'Grade ' + (s.grade_level||'—');
        document.getElementById('lkSY').textContent        = 'S.Y. ' + (s.school_year||'—');
        document.getElementById('lkEmail').textContent     = s.email || '—';
        document.getElementById('lkBalance').textContent   = fmt(s.balance);
        document.getElementById('lkAmountPaid').textContent = fmt(s.payment_amount);

        // Progress bar
        var total = Number(s.total_fee||0);
        var paid  = Number(s.payment_amount||0);
        var pct   = total > 0 ? Math.min(100, Math.round((paid/total)*100)) : 0;
        document.getElementById('lkProgressBar').style.width  = pct + '%';
        document.getElementById('lkProgressLabel').textContent = pct + '% paid';
        document.getElementById('lkTotalFee').textContent      = 'Total: ' + fmt(total);

        // Status badge
        var statusMap = { paid:['Fully Paid','#dcfce7','#16a34a'], partial:['Partially Paid','#fef3c7','#b45309'], unpaid:['Unpaid','#fee2e2','#dc2626'], pending:['No Enrollment','#f1f5f9','#64748b'] };
        var st = statusMap[s.payment_status] || statusMap.pending;
        var badge = document.getElementById('lkStatusBadge');
        badge.textContent = st[0]; badge.style.background = st[1]; badge.style.color = st[2];

        // Account summary
        document.getElementById('lkPlan').textContent    = planLabels[s.payment_option] || '—';
        document.getElementById('lkTotal').textContent   = total > 0 ? fmt(total) : '—';
        document.getElementById('lkPaid').textContent    = fmt(paid);
        document.getElementById('lkMonthly').textContent = s.monthly_amount > 0 ? fmt(s.monthly_amount) : '—';

        // Student info card
        document.getElementById('lkLrn').textContent      = s.lrn || '—';
        document.getElementById('lkEmailVal').textContent  = s.email || '—';
        document.getElementById('lkGradeVal').textContent  = 'Grade ' + (s.grade_level||'—');
        document.getElementById('lkSYVal').textContent     = 'S.Y. ' + (s.school_year||'—');
        var pb = document.getElementById('lkPayStatusBadge');
        pb.textContent = st[0]; pb.style.background = st[1]; pb.style.color = st[2];

        // Bar chart
        var barPct = total > 0 ? Math.min(100, (paid/total)*100) : 0;
        document.getElementById('lkBarPaid').style.width      = barPct + '%';
        document.getElementById('lkBarPaidAmt').textContent   = fmt(paid);
        document.getElementById('lkBarRemAmt').textContent    = fmt(s.balance);
        document.getElementById('lkBarTotalAmt').textContent  = fmt(total);
        document.getElementById('lkOverviewPct').textContent  = pct + '% complete';
    }

    function goToProcessPayment() {
        if (!_lookupStudent) return;
        showSection('process', document.querySelector('.sidebar-link[data-section="process"]'));
        setTimeout(function() { selectStudentFromList(_lookupStudent); }, 150);
    }

    function clearLookup() {
        _lookupStudent = null;
        document.getElementById('lookupListPanel').style.display = 'block';
        document.getElementById('lookupPlaceholder').style.display = 'none';
        document.getElementById('lookupResult').style.display = 'none';
        document.getElementById('lookupDropdown').style.display = 'none';
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#studentSearchInput') && !e.target.closest('#studentDropdown')) {
            document.getElementById('studentDropdown').style.display = 'none';
        }
        if (!e.target.closest('#lookupSearchInput') && !e.target.closest('#lookupDropdown')) {
            var ld = document.getElementById('lookupDropdown');
            if (ld) ld.style.display = 'none';
        }
    });

    // ══ Chart.js ══
    const _CsC = {blue:'#1a3a6c',mid:'#2471a3',gold:'#c5a059',green:'#16a34a',red:'#dc2626',gray:'#94a3b8'};
    Chart.defaults.font.family = "'Poppins',sans-serif";
    Chart.defaults.font.size   = 11;

    // Dashboard charts — lazy init (only after section is visible so Chart.js can measure canvas)
    var _dbChartsInit = false;
    function initDashboardCharts() {
        if (_dbChartsInit) return;
        _dbChartsInit = true;

        const barEl = document.getElementById('csWeekBar');
        if (barEl) new Chart(barEl,{type:'bar',
            data:{labels:@json($csChDays??[]),
                datasets:[{label:'Collections (₱)',data:@json($csChTotals??[]),
                    backgroundColor:_CsC.blue,borderRadius:5,borderSkipped:false}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                    ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
        });

        const doughEl = document.getElementById('csMethodDoughnut');
        if (doughEl) new Chart(doughEl,{type:'doughnut',
            data:{labels:['Cash','Online'],
                datasets:[{data:[{{ (float)$todayCash }},{{ (float)$todayOnline }}],
                    backgroundColor:[_CsC.green,_CsC.mid],borderWidth:0,hoverOffset:4}]},
            options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
                plugins:{legend:{position:'bottom',labels:{padding:10}}}}
        });
    }

    // Collection section charts (lazy)
    function initCollectionCharts() {
        if (window._csColInit) return;
        window._csColInit = true;

        const lineEl = document.getElementById('csColLine');
        if (lineEl) new Chart(lineEl,{type:'line',
            data:{labels:@json($csColMonths??[]),
                datasets:[{label:'Collections (₱)',data:@json($csColTotals??[]),
                    borderColor:_CsC.blue,backgroundColor:'rgba(26,58,108,.08)',
                    borderWidth:2,pointRadius:4,tension:0.4,fill:true}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                    ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
        });

        const methodEl = document.getElementById('csColMethodDoughnut');
        if (methodEl) new Chart(methodEl,{type:'doughnut',
            data:{labels:['Cash','GCash','E-Payment'],
                datasets:[{data:[{{$csColCash??0}},{{$csColGcash??0}},{{$csColOther??0}}],
                    backgroundColor:[_CsC.green,_CsC.mid,_CsC.gold],borderWidth:0,hoverOffset:4}]},
            options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
                plugins:{legend:{position:'bottom',labels:{padding:10}}}}
        });

        const barEl = document.getElementById('csColBar');
        if (barEl) new Chart(barEl,{type:'bar',
            data:{labels:@json($csColMonths??[]),
                datasets:[{label:'Collections (₱)',data:@json($csColTotals??[]),
                    backgroundColor:_CsC.mid,borderRadius:5,borderSkipped:false}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                    ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
        });
    }

</script>
</body>
</html>
