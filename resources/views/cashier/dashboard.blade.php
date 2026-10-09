<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cashier Portal — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Open Sans, matching every other portal (Admin, Super Admin, Teacher, Student) --}}
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- Work Sans + IBM Plex Mono — used only inside the Process Payment
         "ticket" (see .pp-* rules below); everything else keeps Open Sans. --}}
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            --ink:        #1e293b;
            --muted:      #64748b;
            --border:     #e2e8f0;
            --sidebar-w:  240px;
            --topbar-h:   62px;
            --radius:     14px;
        }
        * { font-family: 'Open Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
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
        /* Brand block — same markup/classes as every other portal now
           (was its own bespoke .brand-icon/.brand-text/.brand-sub before). */
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
            font-family: 'Open Sans', sans-serif;
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

        /* ── Pagination — same look as Admin/Finance ── */
        .pagination { display: flex !important; justify-content: center; align-items: center; gap: 4px; margin: 0; padding: 0; list-style: none; font-size: 13px; }
        .pagination .page-item { display: block !important; }
        .pagination .page-item .page-link { display: inline-flex !important; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 6px 10px; margin: 0 2px; border: 1px solid var(--border); border-radius: 6px; background: #fff; color: var(--ink); font-weight: 500; text-decoration: none; transition: all 0.2s; }
        .pagination .page-item .page-link:hover { background: var(--blue-pale); border-color: var(--blue); color: var(--blue); }
        .pagination .page-item.active .page-link { background: var(--blue); border-color: var(--blue); color: #fff; }
        .pagination .page-item.disabled .page-link { opacity: 0.5; cursor: not-allowed; background: #f8f9fa; }
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link { font-weight: 600; font-size: 12px !important; padding: 6px 14px !important; min-width: auto !important; height: auto !important; border-radius: 6px !important; }
        .pagination-info { text-align: center; font-size: 12px; color: var(--muted); margin-top: 10px; }

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
            font-family: 'Open Sans', sans-serif;
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

        /* ── Installment Timeline — "which months are paid, what's next" ── */
        .pay-timeline { display: flex; align-items: flex-start; overflow-x: auto; padding: 10px 4px 4px; }
        .pt-step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex-shrink: 0; min-width: 46px; }
        .pt-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 2.5px solid #cbd5e1; background: #fff; color: #94a3b8; flex-shrink: 0; }
        .pt-step.paid .pt-dot { background: #16a34a; border-color: #16a34a; color: #fff; }
        .pt-step.pending .pt-dot { background: #2471a3; border-color: #2471a3; color: #fff; }
        .pt-step.overdue .pt-dot { background: #dc2626; border-color: #dc2626; color: #fff; }
        .pt-step.next .pt-dot { background: #fff; border-color: #d97706; color: #d97706; box-shadow: 0 0 0 4px rgba(217,119,6,.15); }
        .pt-label { font-size: 10px; font-weight: 700; color: #64748b; white-space: nowrap; }
        .pt-step.next .pt-label { color: #d97706; }
        .pt-sub { font-size: 9px; color: #b0b8c4; white-space: nowrap; }
        .pt-line { flex: 1; height: 3px; min-width: 14px; background: #e2e8f0; margin: 13px -2px 0; }
        .pt-line.paid { background: #16a34a; }

        /* ── ACTION BUTTONS ── */
        .btn-primary-cash {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #1a3a6c, #2471a3);
            color: #fff; border: none; border-radius: 10px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: 'Open Sans', sans-serif;
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
            font-family: 'Open Sans', sans-serif; transition: all .15s;
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
            font-family: 'Open Sans', sans-serif;
            background: #f8faff; outline: none; transition: all .2s;
        }
        .form-fld:focus { border-color: #2471a3; background: #fff; box-shadow: 0 0 0 3px rgba(26,58,108,.08); }

        /* ── Settings: profile banner + icon-prefixed inputs ── */
        .settings-banner {
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            border-radius: 16px;
            padding: 30px 26px;
            display: flex;
            align-items: center;
            gap: 20px;
            color: #fff;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .settings-banner::before {
            content: '';
            position: absolute;
            top: -50px; right: -30px;
            width: 170px; height: 170px;
            background: rgba(255,255,255,.07);
            border-radius: 50%;
        }
        .settings-banner::after {
            content: '';
            position: absolute;
            bottom: -55px; right: 75px;
            width: 110px; height: 110px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
        }
        .settings-banner-avatar {
            width: 74px; height: 74px; border-radius: 50%;
            background: rgba(255,255,255,.15);
            border: 3px solid rgba(255,255,255,.35);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 700; flex-shrink: 0;
            position: relative; z-index: 1;
        }
        .settings-banner-info { position: relative; z-index: 1; }
        .settings-banner-name { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
        .settings-banner-meta { font-size: 12.5px; opacity: .88; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .settings-banner-badge {
            background: rgba(255,255,255,.2);
            padding: 3px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .4px;
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap > i {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 14px; pointer-events: none;
        }
        .form-fld.has-icon { padding-left: 38px; }

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

        /* ══ PROCESS PAYMENT — receipt-ticket redesign ══
           Scoped entirely under #payPanel so nothing here reaches any other
           section (Dashboard, History, Lookup, etc. keep the original look). */
        #payPanel {
            --pp-paper:  #f8f6f1;
            --pp-surface:#ffffff;
            --pp-ink:    #1d2735;
            --pp-soft:   #5b6472;
            --pp-faint:  #8b93a0;
            --pp-line:   #e5e1d8;
            --pp-accent: #1a3a6c;
            --pp-accent-soft: #e8edf5;
            --pp-good:   #1f7a4d;
            --pp-good-soft: #e8f4ec;
            --pp-warn:   #a85a1a;
            --pp-warn-soft: #faeee0;
            font-family: 'Work Sans', 'Open Sans', sans-serif;
            color: var(--pp-ink);
        }
        #payPanel .pp-mono { font-family: 'IBM Plex Mono', ui-monospace, Menlo, Consolas, monospace; font-variant-numeric: tabular-nums; }

        #payPanel .pp-card { background:var(--pp-surface); border:1px solid var(--pp-line); border-radius:10px; overflow:hidden; }
        #payPanel .pp-sec { padding:14px 18px; border-bottom:1px solid var(--pp-line); }
        #payPanel .pp-sec:last-child { border-bottom:none; }
        #payPanel .pp-sec-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--pp-faint); margin-bottom:9px; }

        /* student strip */
        #payPanel .pp-avatar { width:38px;height:38px;border-radius:8px;background:var(--pp-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0; }
        #payPanel .pp-chip { font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:5px;background:var(--pp-warn-soft);color:var(--pp-warn);white-space:nowrap; }
        #payPanel .pp-chip.good { background:var(--pp-good-soft); color:var(--pp-good); }
        #payPanel .pp-dashed { border-top:1px dashed var(--pp-line); }
        #payPanel .pp-bar-track { height:4px;background:var(--pp-line);border-radius:3px;overflow:hidden; }
        #payPanel .pp-bar-fill  { height:100%;background:var(--pp-accent);border-radius:3px;transition:width .5s ease; }

        /* dotted-leader schedule rows */
        #payPanel .pp-item-row { display:flex; align-items:baseline; gap:8px; padding:4px 0; font-size:12.5px; }
        #payPanel .pp-item-row .pp-name { white-space:nowrap; color:var(--pp-soft); }
        #payPanel .pp-item-row .pp-leader { flex:1; border-bottom:1px dotted var(--pp-line); transform:translateY(-3px); }
        #payPanel .pp-item-row .pp-amt { white-space:nowrap; font-weight:600; }
        #payPanel .pp-item-row.next .pp-name, #payPanel .pp-item-row.next .pp-amt { color:var(--pp-warn); font-weight:700; }
        #payPanel .pp-item-row.overdue .pp-name, #payPanel .pp-item-row.overdue .pp-amt { color:#b91c1c; font-weight:700; }
        #payPanel .pp-item-row.paid .pp-amt { color:var(--pp-good); }
        #payPanel .pp-item-row .pp-tick { font-size:11px; color:var(--pp-good); margin-right:2px; }
        #payPanel .pp-item-row.faint .pp-name, #payPanel .pp-item-row.faint .pp-amt { color:var(--pp-faint); }

        #payPanel .pp-tmini { display:flex; align-items:center; margin-top:10px; }
        #payPanel .pp-tm-dot { width:7px;height:7px;border-radius:50%;background:var(--pp-line);flex-shrink:0; }
        #payPanel .pp-tm-dot.paid { background:var(--pp-good); }
        #payPanel .pp-tm-dot.next { background:var(--pp-warn); box-shadow:0 0 0 3px var(--pp-warn-soft); }
        #payPanel .pp-tm-seg { flex:1;height:1px;background:var(--pp-line);min-width:6px; }
        #payPanel .pp-tm-seg.paid { background:var(--pp-good); }

        /* presets */
        #payPanel .pp-preset { flex:1;min-width:130px;display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--pp-line);border-radius:8px;background:var(--pp-paper);cursor:pointer;font-size:12px;font-weight:600;color:var(--pp-soft);font-family:inherit; }
        #payPanel .pp-preset .pp-amt { font-weight:700;color:var(--pp-ink); }
        #payPanel .pp-preset.active { border-color:var(--pp-accent);background:var(--pp-accent-soft);color:var(--pp-accent); }
        #payPanel .pp-preset.active .pp-amt { color:var(--pp-accent); }

        /* amount */
        #payPanel .pp-amount-row { display:flex;align-items:baseline;gap:10px;padding:4px 0 2px; }
        #payPanel .pp-peso { font-size:22px;font-weight:600;color:var(--pp-faint); }
        #payPanel .pp-amount-input { font-size:32px;font-weight:700;letter-spacing:-1px;flex:1;min-width:0;border:none;outline:none;background:transparent;color:var(--pp-ink);font-family:'IBM Plex Mono',monospace; }
        #payPanel .pp-amount-underline { height:2px;background:var(--pp-accent);border-radius:2px;margin-top:6px; }
        #payPanel .pp-clear-btn { font-size:11px;color:var(--pp-faint);cursor:pointer;font-weight:600;white-space:nowrap;background:none;border:none;font-family:inherit; }

        /* method segmented toggle */
        #payPanel .pp-method-toggle { display:flex;background:var(--pp-paper);border:1px solid var(--pp-line);border-radius:9px;padding:3px;gap:3px; }
        #payPanel .pp-method-opt { flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 10px;border-radius:7px;font-size:12.5px;font-weight:600;color:var(--pp-soft);cursor:pointer;border:none;background:none;font-family:inherit;transition:all .12s; }
        #payPanel .pp-method-opt.active { background:var(--pp-surface);color:var(--pp-ink);box-shadow:0 1px 2px rgba(0,0,0,.08); }
        #payPanel .pp-method-opt.active.cash { color:var(--pp-good); }
        #payPanel .pp-method-opt.active.online { color:var(--pp-accent); }
        #payPanel .pp-wallet-chip { padding:6px 14px;border:1px solid var(--pp-line);border-radius:20px;font-size:11.5px;font-weight:600;color:var(--pp-soft);cursor:pointer;background:var(--pp-surface);font-family:inherit; }
        #payPanel .pp-wallet-chip.active { border-color:var(--pp-accent);color:var(--pp-accent);background:var(--pp-accent-soft); }

        #payPanel .pp-notes { width:100%;border:1px solid var(--pp-line);border-radius:8px;padding:9px 12px;font-size:12.5px;font-family:'Work Sans','Open Sans',sans-serif;background:var(--pp-paper);color:var(--pp-ink);resize:none; }

        /* totals / receipt footer */
        #payPanel .pp-total-line { display:flex;justify-content:space-between;font-size:12.5px;padding:3px 0;color:var(--pp-soft); }
        #payPanel .pp-total-line b { color:var(--pp-ink);font-weight:600; }
        #payPanel .pp-grand { display:flex;justify-content:space-between;align-items:baseline;margin-top:8px;padding-top:10px;border-top:1px dashed var(--pp-line); }
        #payPanel .pp-grand .pp-l { font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--pp-soft); }
        #payPanel .pp-grand .pp-v { font-size:22px;font-weight:700;color:var(--pp-accent); }
        #payPanel .pp-confirm-btn { width:100%;margin-top:14px;padding:13px;background:var(--pp-good);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;font-family:'Work Sans',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.2px;transition:background .15s; }

        /* recent payments log */
        #payPanel .pp-log-row { padding:9px 18px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--pp-line);font-size:12px; }
        #payPanel .pp-log-meta { font-size:10px;color:var(--pp-faint);margin-top:1px; }
        #payPanel .pp-log-ref { font-size:9.5px;color:var(--pp-faint); }
    </style>
</head>
<body>

{{-- ══ TOPBAR ══ --}}
<div class="topbar">
    <div class="topbar-brand">
        <div class="brand-logo-circle">
            <img src="/images/logo.png" alt=""
                 onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-cash-coin\'></i>'">
        </div>
        <div class="brand-info">
            <h6>IEMELIF Learning Center</h6>
            <span>General Tinio, Nueva Ecija</span>
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
                    <form method="POST" action="{{ route('cashier.logout') }}" style="margin:0;" onsubmit="return confirmLogout(this)">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:#dc2626;text-decoration:none;cursor:pointer;transition:background .15s;border:none;background:none;width:100%;font-family:'Open Sans',sans-serif;">
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
    <button class="sidebar-link" data-section="history" onclick="_historyStudentFilter=null;_historyPage=1;showSection('history', this)">
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
    <button class="sidebar-link" data-section="audit" onclick="showSection('audit', this)">
        <span class="link-icon"><i class="bi bi-journal-check"></i></span>
        <span class="link-label">Audit Trail</span>
    </button>

    {{-- BOTTOM --}}
    <div class="sidebar-bottom">
        <button class="sidebar-link" data-section="settings" onclick="showSection('settings', this)">
            <span class="link-icon"><i class="bi bi-gear-fill"></i></span>
            <span class="link-label">Settings</span>
        </button>
        <form method="POST" action="{{ route('cashier.logout') }}" style="margin:0;"
              onsubmit="return confirmLogout(this)">
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

<style>
    .ilc-breadcrumb{display:flex;align-items:center;gap:8px;padding:0 0 18px;font-size:13px;color:#64748b;flex-wrap:wrap;}
    .ilc-breadcrumb a{color:#1a3a6c;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
    .ilc-breadcrumb a:hover{text-decoration:underline;}
    .ilc-bc-sep{font-size:10px;color:#b6c0cc;}
    .ilc-bc-current{color:#334155;font-weight:700;}
</style>
<nav class="ilc-breadcrumb" aria-label="breadcrumb">
    <a href="#" onclick="showSection('dashboard');return false;"><i class="bi bi-house-door-fill"></i> Home</a>
    <i class="bi bi-chevron-right ilc-bc-sep"></i>
    <a href="#" id="bc-current" class="ilc-bc-current" style="text-decoration:none;" onclick="return bcMidClick();">Dashboard</a>
    <span id="bc-current-skel" class="skel" style="display:none;width:110px;height:13px;border-radius:4px;"></span>
    <span id="bc-extra-wrap" style="display:none;">
        <i class="bi bi-chevron-right ilc-bc-sep"></i>
        <span id="bc-extra" class="ilc-bc-current"></span>
    </span>
</nav>

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

        {{-- ── Page header ── --}}
        <div style="margin-bottom:20px;">
            <div class="page-title"><i class="bi bi-cash-coin me-2" style="color:#c5a059;"></i>Process Payment</div>
            <div class="page-sub">Search a student and collect payment at the counter.</div>
        </div>

        {{-- ── Payment Guide — always visible, so it's the first thing a new
             cashier sees, not something buried after a student is picked ── --}}
        <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
            <div style="font-size:11px;font-weight:800;color:#92400e;display:flex;align-items:center;gap:6px;white-space:nowrap;"><i class="bi bi-info-circle-fill"></i> How to process a payment:</div>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;flex:1;">
                <div style="display:flex;align-items:center;gap:7px;font-size:11.5px;color:#78350f;"><span style="width:19px;height:19px;border-radius:6px;background:#fcd34d;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">1</span>Search and select a student</div>
                <i class="bi bi-chevron-right" style="color:#fbbf24;font-size:10px;"></i>
                <div style="display:flex;align-items:center;gap:7px;font-size:11.5px;color:#78350f;"><span style="width:19px;height:19px;border-radius:6px;background:#fcd34d;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">2</span>Choose a quick preset or enter amount</div>
                <i class="bi bi-chevron-right" style="color:#fbbf24;font-size:10px;"></i>
                <div style="display:flex;align-items:center;gap:7px;font-size:11.5px;color:#78350f;"><span style="width:19px;height:19px;border-radius:6px;background:#fcd34d;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">3</span>Select Cash or Online method</div>
                <i class="bi bi-chevron-right" style="color:#fbbf24;font-size:10px;"></i>
                <div style="display:flex;align-items:center;gap:7px;font-size:11.5px;color:#78350f;"><span style="width:19px;height:19px;border-radius:6px;background:#fcd34d;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:10px;flex-shrink:0;">4</span>Click Process &amp; print receipt</div>
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
                                style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Open Sans',sans-serif;"
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
                <div id="studentListPagination"></div>
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

                {{-- Identity strip --}}
                <div class="pp-card" style="padding:16px;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:11px;">
                        <div class="pp-avatar" id="studentInitial">?</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14.5px;font-weight:700;letter-spacing:-.1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" id="studentName">—</div>
                            <div style="font-size:11px;color:var(--pp-faint);margin-top:1px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                <span id="studentGrade">—</span><span style="width:3px;height:3px;border-radius:50%;background:var(--pp-faint);"></span><span id="studentSchoolYear">—</span>
                                <span id="studentPayStatusBadge" class="pp-chip"></span>
                            </div>
                        </div>
                        <button onclick="clearPaymentForm()" title="Change student"
                            style="background:none;border:1px solid var(--pp-line);color:var(--pp-soft);border-radius:8px;padding:6px 10px;cursor:pointer;font-size:11px;line-height:1;flex-shrink:0;font-family:inherit;">
                            <i class="bi bi-arrow-left-circle me-1"></i>Change
                        </button>
                    </div>
                    <div class="pp-dashed" style="margin-top:12px;padding-top:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:baseline;">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--pp-faint);font-weight:600;">Outstanding</div>
                                <div class="pp-mono" style="font-size:22px;font-weight:700;letter-spacing:-.5px;" id="studentBalance">₱0.00</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--pp-faint);font-weight:600;">Paid</div>
                                <div class="pp-mono" style="font-size:13px;font-weight:600;color:var(--pp-soft);" id="cardAmountPaid">₱0.00</div>
                            </div>
                        </div>
                        <div class="pp-bar-track" style="margin-top:10px;"><div class="pp-bar-fill" id="payProgressBar" style="width:0%;"></div></div>
                        <div style="display:flex;justify-content:flex-end;margin-top:5px;">
                            <div style="font-size:10px;color:var(--pp-faint);" id="cardTotalFee">Total: ₱0.00</div>
                        </div>
                    </div>
                </div>

                {{-- Recent Payments — lets the cashier verify this student's payment
                     history before collecting a new one, instead of duplicating the
                     balance numbers already shown in the card above. --}}
                <div class="pp-card">
                    <div class="pp-sec" style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;">
                        <div class="pp-sec-label" style="margin-bottom:0;">Recent Payments</div>
                        <a href="#" onclick="loadHistoryForStudent(selectedStudent.id, selectedStudent.name);return false;" style="font-size:11px;font-weight:600;color:var(--pp-accent);text-decoration:none;">View all →</a>
                    </div>
                    <div id="recentPaymentsList">
                        <div style="text-align:center;padding:18px;color:var(--pp-faint);font-size:12px;">
                            <i class="bi bi-arrow-repeat" style="font-size:16px;display:block;margin-bottom:6px;"></i>Loading…
                        </div>
                    </div>
                </div>

            </div>{{-- /LEFT --}}

            {{-- ══ RIGHT: Payment Register ══ --}}
            <div>

                {{-- The ticket — one continuous card reading top to bottom like a
                     real receipt being built up: schedule, then amount, then
                     method, then notes, then the total and the confirm action. --}}
                <div class="pp-card">

                    {{-- Plan & Schedule --}}
                    <div id="planSelectorCard" style="display:none;">
                        <div id="planSelectorBox">
                            <div class="pp-sec" id="planSelectorHeader" style="display:flex;justify-content:space-between;align-items:center;padding-bottom:0;border-bottom:none;">
                                <div class="pp-sec-label" id="planSelectorTitle" style="margin-bottom:0;">Payment Plan</div>
                                <button id="changePlanBtn" onclick="expandPlanOptions()"
                                    style="display:none;padding:4px 10px;border:1px solid var(--pp-accent);background:var(--pp-accent-soft);color:var(--pp-accent);border-radius:6px;font-size:10.5px;font-weight:700;cursor:pointer;font-family:inherit;">
                                    <i class="bi bi-pencil-fill me-1"></i>Change
                                </button>
                            </div>
                            {{-- Collapsed summary (shown after plan is confirmed) --}}
                            <div id="planSummaryStrip" style="display:none;padding:0 18px 12px;">
                                <div style="display:flex;align-items:center;gap:7px;">
                                    <i class="bi bi-check-circle-fill" style="color:var(--pp-good);font-size:13px;"></i>
                                    <div id="planSummaryText" class="pp-mono" style="font-size:12px;font-weight:600;color:var(--pp-good);flex:1;"></div>
                                </div>
                            </div>
                            {{-- Expandable options list --}}
                            <div id="planOptionsWrap">
                                <div style="padding:0 18px 14px;" id="planOptionsList">
                                    <div style="text-align:center;padding:16px;color:var(--pp-faint);">
                                        <i class="bi bi-arrow-repeat" style="font-size:18px;display:block;margin-bottom:6px;"></i>
                                        Loading plans…
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Installment Timeline → rendered as dotted-leader schedule
                         rows by renderInstallmentTimeline(). Hidden for full-payment (A). --}}
                    <div id="timelineCard" class="pp-sec" style="display:none;">
                        <div class="pp-sec-label">Schedule</div>
                        <div id="timelineBody">
                            <div style="text-align:center;padding:14px;color:var(--pp-faint);font-size:12px;">
                                <i class="bi bi-arrow-repeat" style="font-size:16px;display:block;margin-bottom:6px;"></i>Loading…
                            </div>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="pp-sec">
                        <div class="pp-sec-label" style="display:flex;justify-content:space-between;">
                            <span>Amount</span>
                            <span id="paymentTypeLabel" style="text-transform:none;letter-spacing:0;color:var(--pp-accent);font-weight:700;">Select type</span>
                        </div>

                        {{-- Quick Presets --}}
                        <div id="quickAmountRow" style="display:none;margin-bottom:10px;">
                            <div style="display:flex;gap:8px;flex-wrap:wrap;" id="quickAmountBtns"></div>
                        </div>

                        {{-- Amount Input --}}
                        <div id="amountInputWrap" style="display:flex;align-items:baseline;gap:10px;">
                            <span class="pp-peso pp-mono">₱</span>
                            <input type="number" id="paymentAmount" placeholder="0.00" step="0.01" min="0" class="pp-amount-input"
                                oninput="updateTransactionSummary()">
                            <button type="button" onclick="clearAmount()" class="pp-clear-btn">Clear</button>
                        </div>
                        <div class="pp-amount-underline"></div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="pp-sec">
                        <div class="pp-sec-label">Method</div>
                        <div class="pp-method-toggle">
                            <button type="button" id="methodCashBtn" onclick="selectPayMethod('cash')" class="pp-method-opt cash active">💵 Cash</button>
                            <button type="button" id="methodOnlineBtn" onclick="selectPayMethod('online')" class="pp-method-opt online">📱 <span id="onlineBtnLabel">Online</span></button>
                        </div>
                        <div id="onlineMethodRow" style="display:none;margin-top:10px;">
                            <div style="display:flex;gap:8px;">
                                @foreach([['gcash','bi-phone-fill','GCash'],['maya','bi-wallet2','Maya']] as [$m,$icon,$label])
                                <button type="button" class="online-method-opt pp-wallet-chip" data-method="{{ $m }}" onclick="selectOnlineMethod('{{ $m }}')">
                                    <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Notes field --}}
                    <div class="pp-sec">
                        <div class="pp-sec-label">Notes <span style="text-transform:none;font-weight:400;">(optional)</span></div>
                        <textarea id="paymentNotesField" rows="1" class="pp-notes" placeholder="e.g. Paid via manager, reference #, etc."
                            oninput="document.getElementById('paymentNotes').value=this.value;"></textarea>
                    </div>

                    {{-- Totals + confirm — the receipt's own foot --}}
                    <div class="pp-sec">
                        {{-- Transaction Summary --}}
                        <div id="txnSummary" style="display:none;">
                            <div class="pp-total-line"><span>Student</span><b id="txnStudent">—</b></div>
                            <div class="pp-total-line"><span>Payment Type</span><b id="txnType">—</b></div>
                            <div class="pp-total-line"><span>Method</span><b id="txnMethod">—</b></div>
                            <div class="pp-grand"><span class="pp-l">Total</span><span class="pp-v pp-mono" id="txnAmount">₱0.00</span></div>
                        </div>

                        {{-- Xendit link result --}}
                        <div id="xenditLinkResult" style="display:none;background:var(--pp-good-soft);border:1px solid var(--pp-good);border-radius:8px;padding:14px;">
                            <div style="font-size:12px;font-weight:700;color:var(--pp-good);margin-bottom:10px;display:flex;align-items:center;gap:8px;"><i class="bi bi-check-circle-fill"></i> Payment link generated!</div>
                            <div style="display:flex;gap:8px;margin-bottom:10px;">
                                <input type="text" id="xenditLinkUrl" readonly class="pp-mono" style="flex:1;padding:8px 10px;border-radius:8px;border:1px solid var(--pp-good);background:#fff;font-size:11.5px;outline:none;color:var(--pp-good);">
                                <button onclick="copyXenditLink()" style="padding:8px 14px;background:var(--pp-good);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px;font-family:inherit;"><i class="bi bi-clipboard" id="cashierCopyIcon"></i> Copy</button>
                            </div>
                            <div style="font-size:10.5px;color:var(--pp-soft);margin-bottom:10px;" id="xenditLinkExpiry"></div>
                            <a id="xenditLinkOpenBtn" href="#" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--pp-good);color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;"><i class="bi bi-box-arrow-up-right"></i> Open Payment Page</a>
                            <div id="xenditPollStatus" style="display:none;margin-top:10px;padding:8px 12px;border-radius:8px;font-size:11.5px;font-weight:600;"></div>
                        </div>

                        {{-- Success Banner --}}
                        <div id="paySuccessBanner" style="display:none;text-align:center;">
                            <div style="width:48px;height:48px;border-radius:50%;background:var(--pp-good);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                                <i class="bi bi-check-lg" style="font-size:22px;color:#fff;"></i>
                            </div>
                            <div style="font-size:15px;font-weight:700;color:var(--pp-good);margin-bottom:3px;">Payment Recorded!</div>
                            <div id="paySuccessRef" style="font-size:12px;color:var(--pp-soft);margin-bottom:14px;line-height:1.5;"></div>
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <button onclick="printLastReceipt()" style="padding:9px 16px;background:#fff;color:var(--pp-good);border:1px solid var(--pp-good);border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:inherit;">
                                    <i class="bi bi-printer-fill"></i> Print Receipt
                                </button>
                                <button onclick="backToStudentList()" style="padding:9px 16px;background:var(--pp-good);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:inherit;">
                                    <i class="bi bi-plus-circle-fill"></i> New Payment
                                </button>
                            </div>
                        </div>

                        {{-- Process Button --}}
                        <button id="processBtn" onclick="handleProcessPayment()" class="pp-confirm-btn">
                            <i class="bi bi-check-circle-fill" style="font-size:17px;"></i>
                            <span id="processBtnLabel">Collect &amp; Record Payment</span>
                        </button>
                    </div>
                </div>

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
                <div class="page-sub" id="historyPageSub">All transactions processed through this cashier terminal.</div>
            </div>
        </div>
        {{-- Shown only when opened for one specific student (e.g. from Process Payment) --}}
        <div id="historyStudentChip" style="display:none;margin-bottom:14px;background:#e8f0fb;border:1.5px solid #bcd6f2;border-radius:12px;padding:10px 16px;align-items:center;gap:10px;">
            <i class="bi bi-person-check-fill" style="color:#1a3a6c;"></i>
            <span style="font-size:13px;color:#1a3a6c;">Showing payment history for <strong id="historyStudentChipName">—</strong></span>
            <button onclick="clearHistoryStudentFilter()" style="margin-left:auto;background:none;border:none;color:#2471a3;font-size:12px;font-weight:700;cursor:pointer;"><i class="bi bi-x-circle me-1"></i>Show All Students</button>
        </div>
        <div class="card-box">
            <div class="card-box-header">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%;">
                    <input type="date" class="form-fld" id="historyDateFilter" style="max-width:150px;" onchange="historyFilterChanged()">
                    <select class="form-fld" id="historyMethodFilter" style="max-width:140px;" onchange="historyFilterChanged()">
                        <option value="all">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online / GCash</option>
                    </select>
                    <select class="form-fld" id="historyStatusFilter" style="max-width:140px;" onchange="historyFilterChanged()">
                        <option value="all">All Status</option>
                        <option value="completed">Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                    <div class="topbar-search" style="flex:1;max-width:240px;">
                        <i class="bi bi-search"></i>
                        <input type="text" id="historySearchInput" placeholder="Search student or reference…" oninput="debouncedLoadHistory()">
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
                        <tr><td colspan="10" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading history…</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="historyPagination"></div>
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
                                style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Open Sans',sans-serif;"
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
                <div id="lookupListPagination"></div>
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
                            style="border:none;background:transparent;outline:none;font-size:13px;color:#334155;width:100%;font-family:'Open Sans',sans-serif;"
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
            <div id="receiptsPagination"></div>
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

    {{-- ── AUDIT TRAIL SECTION ── --}}
    <div id="section-audit" style="display:none;">

        {{-- Skeleton --}}
        <div id="audit-skel" style="display:none;">
            <div style="margin-bottom:24px;"><span class="skel" style="height:26px;width:160px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:310px;"></span></div>
            <div class="skel-wrap">
                <div class="skel-hdr"><div style="display:flex;align-items:center;gap:10px;"><span class="skel" style="height:18px;width:120px;"></span><span class="skel" style="height:22px;width:34px;border-radius:20px;"></span></div></div>
                @for($i=0;$i<7;$i++)
                <div class="skel-trow"><span class="skel" style="height:12px;width:24px;"></span><span class="skel" style="height:22px;width:110px;border-radius:6px;"></span><span class="skel" style="height:28px;flex:2;border-radius:6px;"></span><span class="skel" style="height:14px;flex:1;"></span></div>
                @endfor
            </div>
        </div>

        {{-- Real content --}}
        <div id="audit-content" class="sec-content">
        <div class="page-header">
            <div>
                <div class="page-title"><i class="bi bi-journal-check me-2" style="color:#2471a3;"></i>Audit Trail</div>
                <div class="page-sub">A record of payments and actions you've performed — for your own reference and accountability.</div>
            </div>
        </div>
        <div class="card-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <i class="bi bi-list-check" style="color:#2471a3;"></i> My Activity
                    <span id="auditCount" style="background:#eff6ff;color:#2471a3;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;margin-left:6px;">0</span>
                </div>
                <button onclick="loadAuditTrail()" class="btn-outline-cash" style="white-space:nowrap;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody id="auditBody">
                        <tr><td colspan="4" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading activity…
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div id="auditPagination"></div>
        </div>
        </div>{{-- /audit-content --}}
    </div>

    {{-- ── SETTINGS SECTION ── --}}
    <div id="section-settings" style="display:none;">

        {{-- Skeleton --}}
        <div id="settings-skel" style="display:none;">
            <div style="margin-bottom:24px;"><span class="skel" style="height:26px;width:150px;margin-bottom:8px;"></span><span class="skel" style="height:14px;width:290px;"></span></div>
            <span class="skel" style="display:block;height:110px;border-radius:16px;margin-bottom:20px;"></span>
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

        {{-- Profile banner --}}
        <div class="settings-banner">
            <div class="settings-banner-avatar">{{ strtoupper(substr(auth('cashier')->user()->name, 0, 1)) }}</div>
            <div class="settings-banner-info">
                <div class="settings-banner-name">{{ auth('cashier')->user()->name }}</div>
                <div class="settings-banner-meta">
                    <span class="settings-banner-badge">Cashier</span>
                    <span><i class="bi bi-envelope me-1"></i>{{ auth('cashier')->user()->email }}</span>
                </div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="card-box" style="max-width:540px;margin-bottom:20px;">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-person-lines-fill" style="color:#2471a3;"></i> Profile Information</div>
            </div>
            <div class="card-box-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-lbl">Full Name</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-fld has-icon" value="{{ auth('cashier')->user()->name }}" readonly style="background:#f1f5f9;color:#64748b;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Email</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="text" class="form-fld has-icon" value="{{ auth('cashier')->user()->email }}" readonly style="background:#f1f5f9;color:#64748b;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Role</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-shield-check"></i>
                            <input type="text" class="form-fld has-icon" value="Cashier" readonly style="background:#f1f5f9;color:#64748b;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card-box" style="max-width:540px;">
            <div class="card-box-header">
                <div class="card-box-title"><i class="bi bi-shield-lock" style="color:#c5a059;"></i> Security</div>
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
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="current_password" class="form-fld has-icon" placeholder="Enter your current password" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">New Password <span style="color:#dc2626;">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-key"></i>
                                <input type="password" name="new_password" class="form-fld has-icon" placeholder="At least 8 characters" required minlength="8">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">Confirm New Password <span style="color:#dc2626;">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-key-fill"></i>
                                <input type="password" name="new_password_confirmation" class="form-fld has-icon" placeholder="Re-enter new password" required>
                            </div>
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
    // ── Toast notifications ── replaces plain alert() popups everywhere in
    // this file with a styled, non-blocking toast (same look as the Admin/
    // Super Admin portals, for consistency across the whole app).
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
    // onConfirm runs only if the user clicks the confirm button; nothing
    // runs on cancel/backdrop-click/Escape.
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
    // in this file — blocks the native synchronous submit, shows the
    // styled confirm, and submits for real only if the user confirms.
    function confirmLogout(form) {
        showConfirm('Are you sure you want to log out?', function () { form.submit(); },
            { title: 'Log Out', confirmLabel: 'Log Out', danger: true });
        return false;
    }

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

    var sections = ['dashboard','process','history','lookup','daily','receipts','collection','audit','settings'];

    // Initialise on load — restore whichever tab was open last time, same
    // behavior as the Admin portal, so re-visiting doesn't always dump you
    // back on Dashboard.
    document.addEventListener('DOMContentLoaded', function () {
        var savedSection = localStorage.getItem('currentCashierSection');
        var sectionToShow = (savedSection && sections.includes(savedSection)) ? savedSection : 'dashboard';
        showSection(sectionToShow, document.querySelector('.sidebar-link[data-section="' + sectionToShow + '"]'));
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

    // Skeleton delays per section (ms the shimmer plays before content appears)
    var _skelDelay = { dashboard: 600, collection: 500 };

    // ── Breadcrumbs ──
    var BREADCRUMB_LABELS = {
        dashboard: 'Dashboard', process: 'Process Payment', history: 'Transaction History',
        lookup: 'Student Lookup', daily: 'Daily Report', receipts: 'Receipts',
        collection: 'Collection Summary', audit: 'Audit Log', settings: 'Settings',
    };
    function updateBreadcrumb(name) {
        var el = document.getElementById('bc-current');
        var skel = document.getElementById('bc-current-skel');
        // Switching sections always collapses the "student selected" crumb,
        // if it was open — it's only ever relevant while still on Process Payment.
        var extra = document.getElementById('bc-extra-wrap');
        if (extra) extra.style.display = 'none';
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
    function bcMidClick() {
        var extra = document.getElementById('bc-extra-wrap');
        if (extra && extra.style.display !== 'none') {
            backToStudentList();
        }
        return false;
    }

    function showSection(name, btn) {
        var delay = _skelDelay[name] || 400;
        updateBreadcrumb(name);
        localStorage.setItem('currentCashierSection', name);

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
        if (name === 'history')  { loadHistory(); }
        if (name === 'audit')    { loadAuditTrail(); }

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
                if (name === 'collection') setTimeout(loadCollectionSummary, 80);
            }
        }, delay);
    }

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
    var _timelineUrlBase = '{{ url("cashier/installments") }}';
    var _auditUrl       = '{{ route("cashier.audit-trail") }}';
    var _cashierName    = '{{ auth("cashier")->user()->name ?? "Cashier" }}';
    var _allStudents = [];

    /* ── Shared pagination control renderer ──────────────
       Matches the admin dashboard's own existing AJAX-pagination pattern
       (see loadSummerClasses() elsewhere in this codebase) — same
       pagination/page-item/page-link classes and full 1..lastPage number
       list, plus the "Showing X to Y of Z" caption used everywhere else
       (Student Management, Enrollment Management, Finance pages, etc.),
       styled via the .pagination-info rule added to this page's <style>.
       meta: the {current_page,last_page,total,from,to} shape Laravel's
       paginator serializes to. onPageChangeFnName: the name of a global
       function taking one page-number argument (kept as a name, not a
       closure, so it can go straight into the built onclick string).
       itemLabel: plural noun for the caption, e.g. "students". */
    function renderPaginationControls(containerId, meta, onPageChangeFnName, itemLabel) {
        var el = document.getElementById(containerId);
        if (!el) return;
        if (!meta || !meta.total) { el.innerHTML = ''; return; }
        var page = meta.current_page, last = meta.last_page;

        // Always render the page controls, even with just one page, so the
        // cashier always has a clear "what page am I on" indicator — unlike
        // Laravel's own ->links() (and the rest of Admin), which hides them
        // entirely when there's nothing to page through.
        var html = '<nav><ul class="pagination">';
        html += '<li class="page-item ' + (page <= 1 ? 'disabled' : '') + '"><a class="page-link" href="javascript:void(0)" onclick="' + onPageChangeFnName + '(' + (page - 1) + ')">Previous</a></li>';
        for (var i = 1; i <= last; i++) {
            html += '<li class="page-item ' + (i === page ? 'active' : '') + '"><a class="page-link" href="javascript:void(0)" onclick="' + onPageChangeFnName + '(' + i + ')">' + i + '</a></li>';
        }
        html += '<li class="page-item ' + (page >= last ? 'disabled' : '') + '"><a class="page-link" href="javascript:void(0)" onclick="' + onPageChangeFnName + '(' + (page + 1) + ')">Next</a></li>';
        html += '</ul></nav>';
        html += '<div class="pagination-info">Showing ' + meta.from + ' to ' + meta.to + ' of ' + meta.total + ' ' + (itemLabel || 'records') + '</div>';
        el.innerHTML = html;
    }

    /* ── Student list ── */
    var _studentListPage = 1;
    var _studentListSearch = '';
    var _studentListSearchTimer = null;

    function goToStudentListPage(p) { _studentListPage = p; loadStudentList(); }

    function loadStudentList() {
        var tbody = document.getElementById('studentListBody');
        var count = document.getElementById('studentListCount');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading students…</td></tr>';
        var url = _listUrl + '?page=' + _studentListPage + (_studentListSearch ? '&q=' + encodeURIComponent(_studentListSearch) : '');
        fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            _allStudents = data.data || [];
            if (count) count.textContent = data.total ?? _allStudents.length;
            renderStudentList(_allStudents);
            renderPaginationControls('studentListPagination', data, 'goToStudentListPage', 'students');
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
        // Now server-side (the list is paginated, so filtering only the
        // currently-loaded page client-side would miss matches on other
        // pages) — debounced so it doesn't fire a request per keystroke.
        clearTimeout(_studentListSearchTimer);
        _studentListSearchTimer = setTimeout(function () {
            _studentListSearch = q || '';
            _studentListPage = 1;
            loadStudentList();
        }, 400);
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

        var bcExtraWrap = document.getElementById('bc-extra-wrap');
        if (bcExtraWrap) bcExtraWrap.style.display = 'none';
    }

    /* ── Student Lookup List ── */
    var _allLookupStudents = [];

    var _lookupListPage = 1;
    var _lookupListSearch = '';
    var _lookupListSearchTimer = null;

    function goToLookupListPage(p) { _lookupListPage = p; loadLookupList(); }

    function loadLookupList() {
        var tbody = document.getElementById('lookupListBody');
        var count = document.getElementById('lookupListCount');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading…</td></tr>';
        var url = _listUrl + '?page=' + _lookupListPage + (_lookupListSearch ? '&q=' + encodeURIComponent(_lookupListSearch) : '');
        fetch(url, { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(data){
            _allLookupStudents = data.data || [];
            if (count) count.textContent = data.total ?? _allLookupStudents.length;
            renderLookupList(_allLookupStudents);
            renderPaginationControls('lookupListPagination', data, 'goToLookupListPage', 'students');
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
        // Server-side now, same reasoning as filterStudentList above.
        clearTimeout(_lookupListSearchTimer);
        _lookupListSearchTimer = setTimeout(function () {
            _lookupListSearch = q || '';
            _lookupListPage = 1;
            loadLookupList();
        }, 400);
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
        _lookupListSearch = '';
        _lookupListPage = 1;
        loadLookupList();
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
        if (!w) { showToast('Pop-ups blocked. Please allow pop-ups to print.', 'warning'); return; }
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
    var _receiptsPage = 1;
    var _receiptsSearch = '';
    var _receiptsSearchTimer = null;

    function goToReceiptsPage(p) { _receiptsPage = p; loadReceipts(); }

    function loadReceipts() {
        var tbody = document.getElementById('receiptsBody');
        var count = document.getElementById('receiptsCount');
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading receipts…</td></tr>';
        var url = _receiptsUrl + '?page=' + _receiptsPage + (_receiptsSearch ? '&q=' + encodeURIComponent(_receiptsSearch) : '');
        fetch(url, { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(data){
            _allReceipts = data.data || [];
            if (count) count.textContent = data.total ?? _allReceipts.length;
            renderReceipts(_allReceipts);
            renderPaginationControls('receiptsPagination', data, 'goToReceiptsPage', 'receipts');
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

    /* ── Recent Payments (Process Payment screen — one selected student) ── */
    function loadRecentPayments(userId) {
        var box = document.getElementById('recentPaymentsList');
        if (!box || !userId) return;
        box.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;font-size:12px;"><i class="bi bi-arrow-repeat" style="font-size:18px;display:block;margin-bottom:6px;"></i>Loading…</div>';
        fetch(_receiptsUrl + '?user_id=' + encodeURIComponent(userId) + '&per_page=5', {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken }
        })
        .then(function(r){ return r.json(); })
        .then(function(data){ renderRecentPayments(data.data || []); })
        .catch(function(){
            box.innerHTML = '<div style="text-align:center;padding:20px;color:#dc2626;font-size:12px;">Failed to load payment history.</div>';
        });
    }

    function renderRecentPayments(txs) {
        var box = document.getElementById('recentPaymentsList');
        if (!box) return;
        if (!txs.length) {
            box.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;font-size:12px;"><i class="bi bi-inbox" style="font-size:22px;display:block;margin-bottom:6px;"></i>No payments recorded yet.</div>';
            return;
        }
        box.innerHTML = txs.map(function(r){
            return '<div class="pp-log-row">'
                + '<div style="min-width:0;">'
                + '<div class="pp-mono" style="font-size:12.5px;font-weight:600;">₱' + r.amount + ' <span style="font-family:\'Work Sans\',sans-serif;font-weight:500;color:var(--pp-faint);">· ' + r.method + '</span></div>'
                + '<div class="pp-log-meta">' + r.type + ' &middot; ' + r.date + ' ' + r.time + '</div>'
                + '</div>'
                + '<span class="pp-mono pp-log-ref">' + (r.or_no||'—') + '</span>'
                + '</div>';
        }).join('');
    }

    /* ── Installment Timeline (Process Payment — one selected student) ── */
    function loadInstallmentTimeline(enrollmentId, paymentType) {
        var card = document.getElementById('timelineCard');
        if (!card) return;
        if (paymentType !== 'installment' || !enrollmentId) {
            card.style.display = 'none';
            return;
        }
        card.style.display = 'block';
        document.getElementById('timelineBody').innerHTML = '<div style="text-align:center;padding:16px;color:#94a3b8;font-size:12px;"><i class="bi bi-arrow-repeat" style="font-size:18px;display:block;margin-bottom:6px;"></i>Loading…</div>';
        fetch(_timelineUrlBase + '/' + enrollmentId, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken }
        })
        .then(function(r){ return r.json(); })
        .then(function(data){ renderInstallmentTimeline(data); })
        .catch(function(){
            document.getElementById('timelineBody').innerHTML = '<div style="text-align:center;padding:16px;color:#dc2626;font-size:12px;">Failed to load timeline.</div>';
        });
    }

    function renderInstallmentTimeline(data) {
        var body = document.getElementById('timelineBody');
        var installments = data.installments || [];
        if (!installments.length) {
            body.innerHTML = '<div style="text-align:center;padding:16px;color:#94a3b8;font-size:12px;">No monthly schedule found yet.</div>';
            return;
        }

        var dp = data.downpayment;
        var steps = [];
        if (dp && dp.amount > 0) {
            steps.push({ name: 'Downpayment', sub: '', amount: dp.amount, cls: dp.paid ? 'paid' : 'next' });
        }

        // "Next" is already claimed by the DP step itself only when there IS a
        // downpayment and it's still unpaid — otherwise no one has claimed it
        // yet, so the first unpaid month below should.
        var foundNext = !!(dp && dp.amount > 0 && !dp.paid);
        installments.forEach(function(inst) {
            var cls = 'faint';
            if (inst.status === 'paid') { cls = 'paid'; }
            else if (inst.status === 'pending_approval') { cls = ''; }
            else if (inst.weeks_overdue > 0) { cls = 'overdue'; }
            else if (!foundNext) { cls = 'next'; foundNext = true; }

            var d = inst.due_date ? new Date(inst.due_date + 'T00:00:00') : null;
            var sub = d ? d.toLocaleDateString('en-PH', { day: 'numeric', month: 'short' }) : '';
            var name = inst.month_name || '';
            if (cls === 'next') name += ' — due ' + sub;
            else if (cls === 'overdue') name += ' — overdue';
            steps.push({ name: name, amount: inst.amount + (inst.late_fee||0), cls: cls, monthShort: (inst.month_name||'').substring(0,3) });
        });

        // Dotted-leader rows, like a real receipt itemizing each month
        var html = steps.map(function(s) {
            var tick = s.cls === 'paid' ? '<span class="pp-tick">✓</span>' : '';
            return '<div class="pp-item-row ' + s.cls + '">' + tick
                + '<span class="pp-name">' + s.name + '</span>'
                + '<span class="pp-leader"></span>'
                + '<span class="pp-amt pp-mono">₱' + Number(s.amount).toLocaleString('en-PH',{minimumFractionDigits:2}) + '</span>'
                + '</div>';
        }).join('');

        // Mini dot-strip beneath, same idea as the full timeline but compact
        html += '<div class="pp-tmini">';
        steps.forEach(function(s, i) {
            if (i > 0) html += '<div class="pp-tm-seg ' + (steps[i-1].cls === 'paid' ? 'paid' : '') + '"></div>';
            html += '<div class="pp-tm-dot ' + (s.cls === 'paid' ? 'paid' : (s.cls === 'next' ? 'next' : '')) + '" title="' + s.name + '"></div>';
        });
        html += '</div>';

        body.innerHTML = html;
    }

    /* ── Payment History (full page — all students, or scoped to one) ── */
    var _historyStudentFilter = null; // { id, name } when opened from a specific student

    function loadHistoryForStudent(userId, studentName) {
        _historyStudentFilter = { id: userId, name: studentName };
        _historyPage = 1;
        var link = document.querySelector('.sidebar-link[data-section="history"]');
        showSection('history', link);
    }

    function clearHistoryStudentFilter() {
        _historyStudentFilter = null;
        document.getElementById('historyStudentChip').style.display = 'none';
        document.getElementById('historyPageSub').textContent = 'All transactions processed through this cashier terminal.';
        _historyPage = 1;
        loadHistory();
    }

    var _historyDebounceTimer = null;
    var _historyPage = 1;
    function debouncedLoadHistory() {
        clearTimeout(_historyDebounceTimer);
        _historyDebounceTimer = setTimeout(historyFilterChanged, 600);
    }

    function historyFilterChanged() {
        _historyPage = 1;
        loadHistory();
    }

    function goToHistoryPage(p) { _historyPage = p; loadHistory(); }

    function loadHistory() {
        var tbody = document.getElementById('historyTbody');
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading history…</td></tr>';

        var chip = document.getElementById('historyStudentChip');
        if (_historyStudentFilter) {
            document.getElementById('historyStudentChipName').textContent = _historyStudentFilter.name;
            chip.style.display = 'flex';
            document.getElementById('historyPageSub').textContent = 'Full payment history for ' + _historyStudentFilter.name + '.';
        } else {
            chip.style.display = 'none';
        }

        var params = new URLSearchParams();
        params.set('status', document.getElementById('historyStatusFilter').value || 'all');
        params.set('method', document.getElementById('historyMethodFilter').value || 'all');
        var date = document.getElementById('historyDateFilter').value;
        if (date) params.set('date', date);
        params.set('page', _historyPage);

        if (_historyStudentFilter) {
            params.set('user_id', _historyStudentFilter.id);
        } else {
            var q = document.getElementById('historySearchInput').value.trim();
            if (q) params.set('q', q);
        }

        fetch(_receiptsUrl + '?' + params.toString(), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrfToken }
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            renderHistory(data.data || []);
            renderPaginationControls('historyPagination', data, 'goToHistoryPage', 'transactions');
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:40px;color:#dc2626;">Failed to load history. <button onclick="loadHistory()" style="margin-left:6px;padding:4px 12px;background:#1a3a6c;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:11px;">Retry</button></td></tr>';
        });
    }

    function renderHistory(txs) {
        var tbody = document.getElementById('historyTbody');
        if (!txs.length) {
            tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:10px;"></i>No transactions found.</td></tr>';
            return;
        }
        var statusBadge = { completed: '<span class="badge-status paid"><i class="bi bi-check-circle-fill"></i> Paid</span>', pending: '<span class="badge-status pending"><i class="bi bi-hourglass-split"></i> Pending</span>' };
        tbody.innerHTML = txs.map(function(r, i){
            var badge = statusBadge[r.status] || ('<span class="badge-status rejected">' + (r.status.charAt(0).toUpperCase() + r.status.slice(1)) + '</span>');
            var methodPill = r.method.toLowerCase() === 'cash'
                ? '<span class="method-pill cash"><i class="bi bi-cash"></i> Cash</span>'
                : '<span class="method-pill gcash"><i class="bi bi-phone"></i> ' + r.method + '</span>';
            return '<tr>'
                + '<td style="color:#94a3b8;">' + (i + 1) + '</td>'
                + '<td><span style="font-family:monospace;font-size:11.5px;background:#f8faff;padding:3px 8px;border-radius:6px;color:#475569;">' + (r.or_no||'—') + '</span></td>'
                + '<td><div style="font-weight:700;color:#1e293b;">' + r.student + '</div><div style="font-size:11px;color:#94a3b8;">' + r.grade + '</div></td>'
                + '<td>' + r.grade + '</td>'
                + '<td style="font-size:12px;">' + r.type + '</td>'
                + '<td>' + methodPill + '</td>'
                + '<td style="font-weight:700;color:#16a34a;">₱' + r.amount + '</td>'
                + '<td>' + badge + '</td>'
                + '<td style="font-size:12px;color:#64748b;">' + r.date + '<br><span style="font-size:10px;">' + r.time + '</span></td>'
                + '<td><button onclick="reprintTx(\'' + r.or_no + '\',\'' + r.student + '\',\'' + r.grade + '\',\'' + r.school_year + '\',\'' + r.type + '\',\'' + r.method + '\',\'' + r.amount + '\',\'' + r.date + '\',\'' + r.time + '\')" class="action-btn-sm" title="Print Receipt"><i class="bi bi-printer-fill"></i></button></td>'
                + '</tr>';
        }).join('');
    }

    function filterReceipts(q) {
        // Server-side now, same reasoning as filterStudentList above.
        clearTimeout(_receiptsSearchTimer);
        _receiptsSearchTimer = setTimeout(function () {
            _receiptsSearch = q || '';
            _receiptsPage = 1;
            loadReceipts();
        }, 400);
    }

    /* ── Audit Trail (own activity only) ── */
    var _auditBadgeClass = {
        login: 'cash', logout: 'cash',
        cash_payment: 'cash', walkin_payment: 'cash',
        xendit_link_generated: 'gcash', xendit_payment_completed: 'gcash',
        password_change: 'gcash'
    };
    var _auditLabel = {
        login: 'Login', logout: 'Logout',
        cash_payment: 'Cash Payment', walkin_payment: 'Walk-in Payment',
        xendit_link_generated: 'Xendit Link', xendit_payment_completed: 'Xendit Confirmed',
        password_change: 'Password Change'
    };

    var _auditPage = 1;
    function goToAuditPage(p) { _auditPage = p; loadAuditTrail(); }

    function loadAuditTrail() {
        var tbody = document.getElementById('auditBody');
        var count = document.getElementById('auditCount');
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-arrow-repeat" style="font-size:28px;display:block;margin-bottom:10px;"></i>Loading activity…</td></tr>';
        fetch(_auditUrl + '?page=' + _auditPage, { credentials:'same-origin', headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrfToken} })
        .then(function(r){ return r.json(); })
        .then(function(data){
            var logs = data.data || [];
            if (count) count.textContent = data.total ?? logs.length;
            if (!logs.length) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:48px;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>No activity recorded yet.</td></tr>';
                renderPaginationControls('auditPagination', data, 'goToAuditPage', 'activity records');
                return;
            }
            tbody.innerHTML = logs.map(function(log,i){
                var cls   = _auditBadgeClass[log.event_type] || 'cash';
                var label = _auditLabel[log.event_type] || log.event_type;
                return '<tr>'
                    +'<td style="color:#94a3b8;">'+(i+1)+'</td>'
                    +'<td><span class="method-pill '+cls+'">'+label+'</span></td>'
                    +'<td style="font-size:12.5px;color:#334155;">'+log.description+'</td>'
                    +'<td style="font-size:12px;color:#64748b;white-space:nowrap;">'+log.date+'<br><span style="font-size:10px;">'+log.time+'</span></td>'
                    +'</tr>';
            }).join('');
            renderPaginationControls('auditPagination', data, 'goToAuditPage', 'activity records');
        })
        .catch(function(){
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:#dc2626;">Failed to load activity. <button onclick="loadAuditTrail()" style="margin-left:6px;padding:4px 12px;background:#1a3a6c;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:11px;">Retry</button></td></tr>';
        });
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
            title.innerHTML    = 'Payment Plan';
            changeBtn.style.display = 'inline-flex';
            summaryStrip.style.display = 'block';
            optionsWrap.style.display  = 'none';
        } else {
            title.innerHTML    = 'Select Payment Plan';
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
              + 'style="display:none;width:100%;margin-top:4px;padding:11px;background:#1a3a6c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:\'Work Sans\',sans-serif;">'
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
            if (data.error) { showToast(data.error, 'error'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirm Plan & Continue'; return; }
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
            document.getElementById('cardTotalFee').textContent    = 'Total: ' + fmt(selectedStudent.total_fee);
            var total = Number(selectedStudent.total_fee||0);
            var paid  = Number(selectedStudent.payment_amount||0);
            var pct   = total > 0 ? Math.min(100,Math.round((paid/total)*100)) : 0;
            document.getElementById('payProgressBar').style.width  = pct + '%';
            /* Rebuild quick presets with new plan */
            rebuildQuickPresets(selectedStudent);
            loadInstallmentTimeline(selectedStudent.enrollment_id, selectedStudent.payment_type);
            window._selectedPlan = null;
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirm Plan & Continue';
            showToast('Failed to save plan. Please try again.', 'error');
        });
    }

    function selectStudent(s) {
        selectedStudent = s;
        document.getElementById('selectedEnrollmentId').value       = s.enrollment_id || '';
        document.getElementById('selectedStudentEmail').value       = s.email || '';
        document.getElementById('selectedStudentNameHidden').value  = s.name;

        // Extend the breadcrumb with the selected student's name, e.g.
        // "Home > Process Payment > Putak Awa" — clicking the middle crumb
        // (bcMidClick) goes back to the student list.
        var bcExtra = document.getElementById('bc-extra');
        var bcExtraWrap = document.getElementById('bc-extra-wrap');
        if (bcExtra) bcExtra.textContent = s.name;
        if (bcExtraWrap) bcExtraWrap.style.display = '';

        var fmt = function(v){ return '₱' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2}); };

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
        document.getElementById('cardTotalFee').textContent = 'Total: ' + fmt(total);

        loadRecentPayments(s.id);
        loadInstallmentTimeline(s.enrollment_id, s.payment_type);

        var badge = document.getElementById('studentPayStatusBadge');
        var statusMap = { paid: ['Fully Paid','#e8f4ec','#1f7a4d'], partial: ['Partially Paid','#faeee0','#a85a1a'], unpaid: ['Unpaid','#fbe9e9','#b91c1c'], pending: ['Pending','#f1f0ec','#8b93a0'] };
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
                return '<button type="button" class="pp-preset" onclick="quickFillAmount(' + b.amount + ',\'' + b.type + '\',this)">'
                    + '<span>' + b.label + '</span>'
                    + '<span class="pp-amt pp-mono">₱' + Number(b.amount).toLocaleString('en-PH',{minimumFractionDigits:2}) + '</span>'
                    + '</button>';
            }).join('');
            qRow.style.display = 'block';
            // Auto-fill the highest-priority option (Downpayment, else Monthly,
            // else Full Balance — same order the buttons above are built in)
            // instead of making the cashier click it themselves. Typing an
            // amount by hand never set the payment *type*, which is what was
            // silently causing "please select a payment type" on Generate
            // Payment Link even though an amount and method were both set.
            // The buttons stay fully clickable to override this default.
            quickFillAmount(btns[0].amount, btns[0].type, qBtns.firstElementChild);
        } else {
            qRow.style.display = 'none';
        }
    }

    function quickFillAmount(amount, type, btn) {
        document.getElementById('paymentAmount').value = Number(amount).toFixed(2);
        document.getElementById('paymentType').value = type;
        document.getElementById('paymentTypeLabel').textContent = type;
        document.querySelectorAll('#quickAmountBtns button').forEach(function(b) {
            b.classList.remove('active');
        });
        if (btn) { btn.classList.add('active'); }
        var underline = document.querySelector('#amountInputWrap + .pp-amount-underline');
        if (underline) {
            underline.style.background = '#16a34a';
            setTimeout(function(){ underline.style.background = ''; }, 800);
        }
        updateTransactionSummary();
    }

    function selectPayMethod(method) {
        var cashBtn   = document.getElementById('methodCashBtn');
        var onlineBtn = document.getElementById('methodOnlineBtn');
        var isOnline  = method === 'online';

        cashBtn.classList.toggle('active', !isOnline);
        onlineBtn.classList.toggle('active', isOnline);

        document.getElementById('onlineMethodRow').style.display  = isOnline ? 'block' : 'none';
        document.getElementById('xenditLinkResult').style.display = 'none';

        if (!isOnline) {
            document.getElementById('paymentMethod').value = 'cash';
            document.getElementById('processBtnLabel').textContent = 'Collect & Record Payment';
            document.getElementById('processBtn').querySelector('i').className = 'bi bi-check-circle-fill';
            document.getElementById('processBtn').style.background = '#16a34a';
            // Reset online method selection
            document.querySelectorAll('.online-method-opt').forEach(function(b) {
                b.classList.remove('active');
            });
        } else {
            document.getElementById('paymentMethod').value = '';
            document.getElementById('processBtnLabel').textContent = 'Generate Payment Link';
            document.getElementById('processBtn').querySelector('i').className = 'bi bi-link-45deg';
            document.getElementById('processBtn').style.background = '#1a3a6c';
        }
        updateTransactionSummary();
    }

    function selectOnlineMethod(val) {
        document.getElementById('paymentMethod').value = val;
        // Highlight selected e-wallet chip
        document.querySelectorAll('.online-method-opt').forEach(function(b) {
            b.classList.toggle('active', b.dataset.method === val);
        });
        updateTransactionSummary();
    }

    function clearAmount() {
        document.getElementById('paymentAmount').value = '';
        document.getElementById('paymentType').value = '';
        document.getElementById('paymentTypeLabel').textContent = 'Select type';
        document.getElementById('txnSummary').style.display = 'none';
        document.querySelectorAll('#quickAmountBtns button').forEach(function(b) {
            b.classList.remove('active');
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

    function handleProcessPayment() {
        var enrollmentId = document.getElementById('selectedEnrollmentId').value;
        var amount       = document.getElementById('paymentAmount').value;
        var method       = document.getElementById('paymentMethod').value;
        var type         = document.getElementById('paymentType').value;

        if (!enrollmentId) { showToast('Please select a student first.', 'warning'); return; }
        if (!amount || amount <= 0) { showToast('Please enter a valid amount.', 'warning'); return; }
        if (!method) { showToast('Please select a payment method.', 'warning'); return; }
        if (!type)   { showToast('Please select a payment type.', 'warning'); return; }

        var isXendit = ['gcash','maya'].includes(method);

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
                showToast(data.message || 'Something went wrong.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:17px;"></i><span id="processBtnLabel">Collect & Record Payment</span>';
            }
        })
        .catch(function() {
            showToast('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:17px;"></i><span id="processBtnLabel">Collect & Record Payment</span>';
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
                pollCashierXenditStatus(data.invoice_id);
            } else {
                showToast(data.message || 'Failed to generate link.', 'error');
            }
        })
        .catch(() => showToast('Network error. Please try again.', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-link-45deg" style="font-size:20px;"></i><span id="processBtnLabel">Generate Payment Link</span>';
        });
    }

    // ── Poll for payment completion ──
    // The customer usually pays this link on their own phone, not the
    // cashier's screen — so without this, the cashier's page has no way to
    // know it went through except manually reloading, even though the
    // payment already succeeded on Xendit's side. Poll every 5s for up to
    // 15 minutes, then give up quietly.
    let _cashierXenditPollTimer = null;
    function pollCashierXenditStatus(invoiceId) {
        if (_cashierXenditPollTimer) clearInterval(_cashierXenditPollTimer);
        var statusEl = document.getElementById('xenditPollStatus');
        var attempts = 0;
        var maxAttempts = 180; // 180 * 5s = 15 minutes

        function tick() {
            attempts++;
            fetch('/cashier/payment/xendit-status?invoice_id=' + encodeURIComponent(invoiceId), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.status === 'completed') {
                    clearInterval(_cashierXenditPollTimer);
                    statusEl.style.display = 'block';
                    statusEl.style.background = '#dcfce7';
                    statusEl.style.color = '#166534';
                    statusEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Payment confirmed! Refreshing…';
                    setTimeout(function() { window.location.reload(); }, 1800);
                } else if (d.status === 'expired' || d.status === 'failed') {
                    clearInterval(_cashierXenditPollTimer);
                    statusEl.style.display = 'block';
                    statusEl.style.background = '#fee2e2';
                    statusEl.style.color = '#991b1b';
                    statusEl.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> This payment link expired or was not completed.';
                } else if (attempts >= maxAttempts) {
                    clearInterval(_cashierXenditPollTimer);
                    statusEl.style.display = 'block';
                    statusEl.style.background = '#fef3c7';
                    statusEl.style.color = '#92400e';
                    statusEl.innerHTML = '<i class="bi bi-clock-history me-1"></i> Still waiting on this payment — reload once the customer confirms.';
                } else {
                    statusEl.style.display = 'block';
                    statusEl.style.background = '#e0f2fe';
                    statusEl.style.color = '#075985';
                    statusEl.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Waiting for payment confirmation…';
                }
            })
            .catch(function() { /* transient network hiccup — just try again next tick */ });
        }

        tick();
        _cashierXenditPollTimer = setInterval(tick, 5000);
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
        var btn = document.getElementById('processBtn');
        btn.disabled = false;
        btn.style.background = '#16a34a';
        btn.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:17px;"></i><span id="processBtnLabel">Collect &amp; Record Payment</span>';
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
        if (!w) { showToast('Pop-ups are blocked. Please allow pop-ups for this site to print receipts.', 'warning'); return; }
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
    }

    // ══ Chart.js ══
    const _CsC = {blue:'#1a3a6c',mid:'#2471a3',gold:'#c5a059',green:'#16a34a',red:'#dc2626',gray:'#94a3b8'};
    Chart.defaults.font.family = "'Open Sans',sans-serif";
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

    // Collection section charts — data fetched on demand (was previously 9
    // queries baked into the Blade view and run on every dashboard load
    // regardless of which tab was open; now only runs when this tab opens).
    var _csColCharts = null;

    function loadCollectionSummary() {
        fetch('{{ route("cashier.collection-summary") }}', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) { renderCollectionCharts(d); })
            .catch(function () { showToast('Failed to load collection summary.', 'error'); });
    }

    function renderCollectionCharts(d) {
        if (_csColCharts) {
            _csColCharts.line.data.labels = d.months;
            _csColCharts.line.data.datasets[0].data = d.totals;
            _csColCharts.line.update();

            _csColCharts.method.data.datasets[0].data = [d.cash, d.gcash, d.other];
            _csColCharts.method.update();

            _csColCharts.bar.data.labels = d.months;
            _csColCharts.bar.data.datasets[0].data = d.totals;
            _csColCharts.bar.update();
            return;
        }

        var line = null, method = null, bar = null;

        const lineEl = document.getElementById('csColLine');
        if (lineEl) line = new Chart(lineEl,{type:'line',
            data:{labels:d.months,
                datasets:[{label:'Collections (₱)',data:d.totals,
                    borderColor:_CsC.blue,backgroundColor:'rgba(26,58,108,.08)',
                    borderWidth:2,pointRadius:4,tension:0.4,fill:true}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                    ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
        });

        const methodEl = document.getElementById('csColMethodDoughnut');
        if (methodEl) method = new Chart(methodEl,{type:'doughnut',
            data:{labels:['Cash','GCash','E-Payment'],
                datasets:[{data:[d.cash, d.gcash, d.other],
                    backgroundColor:[_CsC.green,_CsC.mid,_CsC.gold],borderWidth:0,hoverOffset:4}]},
            options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
                plugins:{legend:{position:'bottom',labels:{padding:10}}}}
        });

        const barEl = document.getElementById('csColBar');
        if (barEl) bar = new Chart(barEl,{type:'bar',
            data:{labels:d.months,
                datasets:[{label:'Collections (₱)',data:d.totals,
                    backgroundColor:_CsC.mid,borderRadius:5,borderSkipped:false}]},
            options:{responsive:true,maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},
                    ticks:{callback:v=>'₱'+Number(v).toLocaleString()}},x:{grid:{display:false}}}}
        });

        _csColCharts = { line: line, method: method, bar: bar };
    }

</script>
</body>
</html>
