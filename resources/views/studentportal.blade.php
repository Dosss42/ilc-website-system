<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/global-scrollbar.css">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        /* ── Section skeleton loading ── */
        @keyframes pSkelShimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skel{background:linear-gradient(90deg,#e8edf2 25%,#f5f7fa 50%,#e8edf2 75%);background-size:600px 100%;animation:pSkelShimmer 1.4s ease-in-out infinite;border-radius:6px;display:block}
        .p-skel{position:absolute;top:0;left:0;right:0;bottom:0;padding:28px;z-index:50;background:var(--bg,#f4f7fb);pointer-events:none;min-height:100vh;transition:opacity .32s ease}
        [id^="section-"]{position:relative}

        /* ── Student Portal specific ── */
        .student-info-section { padding: 28px; }

        .info-section-title {
            font-size: 18px; font-weight: 700;
            color: var(--blue); text-align: center;
            text-transform: uppercase; letter-spacing: 2px;
            margin-bottom: 28px; padding-bottom: 14px;
            border-bottom: 2px solid var(--blue);
        }

        .info-group-label {
            font-size: 11px; font-weight: 700;
            color: var(--blue); text-transform: uppercase;
            letter-spacing: 1.5px; background: var(--blue-pale);
            padding: 8px 14px; border-radius: 4px;
            margin-bottom: 14px; margin-top: 10px;
            display: flex; align-items: center; gap: 8px;
        }

        .info-group-label i { color: var(--gold); }

        .form-field {
            position: relative;
        }

        .form-field input,
        .form-field select {
            width: 100%;
            border: 1.5px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Open Sans', sans-serif;
            background: #fafafa;
            color: var(--text);
            transition: border 0.2s, box-shadow 0.2s;
        }

        .form-field input:focus,
        .form-field select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(26,58,108,0.08);
            background: #fff; outline: none;
        }

        .form-field input::placeholder { color: #bbb; text-transform: uppercase; font-size: 12px; }

        .form-field label {
            font-size: 11px; font-weight: 700;
            color: #666; text-transform: uppercase;
            letter-spacing: 0.5px; display: block;
            margin-bottom: 5px;
        }

        .gender-row {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 6px; background: #fafafa;
        }

        .gender-row label {
            font-size: 12px; font-weight: 600;
            color: #555; margin: 0; cursor: pointer;
            display: flex; align-items: center; gap: 5px;
        }

        .gender-row input[type="radio"] {
            accent-color: var(--blue);
            width: 14px; height: 14px; margin: 0;
        }

        .gender-lbl {
            font-size: 10px; font-weight: 700;
            color: #666; text-transform: uppercase;
            letter-spacing: 0.5px; margin-right: 8px;
        }

        /* Smaller stat cards inside payment details modal */
        #paymentDetailsModal .stat-card { padding: 10px 12px; gap: 8px; }
        #paymentDetailsModal .stat-icon { width: 36px; height: 36px; border-radius: 8px; font-size: 16px; }
        #paymentDetailsModal .stat-value { font-size: 15px; line-height: 1.1; }
        #paymentDetailsModal .stat-label { font-size: 10px; margin-top: 2px; }

        .form-actions {
            display: flex; justify-content: flex-end;
            gap: 10px; padding: 20px 28px;
            border-top: 1px solid var(--border);
        }

        /* ── GRADES SECTION ── */
        .grade-quarter-tab {
            display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 18px;
        }

        .q-tab {
            padding: 7px 18px; border: 1.5px solid var(--border);
            border-radius: 20px; font-size: 12px; font-weight: 600;
            color: #555; background: #fff; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
        }

        .q-tab.active, .q-tab:hover {
            background: var(--blue); border-color: var(--blue); color: #fff;
        }

        /* ── SCHEDULE SECTION ── */
        .schedule-grid {
            display: grid;
            grid-template-columns: 80px repeat(5, 1fr);
            gap: 2px;
        }

        .sched-header {
            background: var(--blue); color: #fff;
            font-size: 11px; font-weight: 700;
            text-align: center; padding: 10px 6px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .sched-time {
            background: #f8fafc; color: var(--muted);
            font-size: 11px; font-weight: 600;
            padding: 10px 8px; text-align: right;
            border-bottom: 1px solid var(--border);
        }

        .sched-slot {
            background: #fff; min-height: 44px;
            border: 1px solid var(--border);
            border-radius: 3px; padding: 6px 8px;
            font-size: 11px;
        }

        .sched-slot.has-class {
            background: var(--blue-pale);
            border-color: var(--blue);
        }

        .sched-subject { font-weight: 700; color: var(--blue); display: block; }
        .sched-room    { color: var(--muted); display: block; }

        /* ── Payment Step Indicator ── */
        .pay-steps {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .pay-step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        .pay-step-num {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            font-size: 13px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .pay-step.active .pay-step-num {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 4px 12px rgba(26,58,108,0.35);
        }
        .pay-step.done .pay-step-num {
            background: #28a745;
            color: #fff;
        }
        .pay-step-label {
            font-size: 12px;
            font-weight: 600;
            color: #999;
        }
        .pay-step.active .pay-step-label { color: var(--blue); }
        .pay-step.done .pay-step-label  { color: #28a745; }
        .pay-step-line {
            flex: 1;
            height: 2px;
            background: #e0e0e0;
            margin: 0 8px;
            border-radius: 2px;
            transition: background 0.3s;
        }
        .pay-step-line.done { background: #28a745; }

        /* Payment Option Cards */
        .payment-option-card {
            border: 2px solid #e8ecf1;
            border-radius: 14px;
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            background: #fff;
            height: 100%;
            position: relative;
        }
        .payment-option-card:hover {
            border-color: var(--blue);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(26,58,108,0.13);
        }
        .payment-option-card.selected {
            border-color: var(--blue);
            background: linear-gradient(135deg, #eef4ff, #e4eeff);
            box-shadow: 0 4px 16px rgba(26,58,108,0.18);
        }
        .payment-option-card.selected::after {
            content: '\F26E';
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 10px; right: 12px;
            font-size: 16px;
            color: var(--blue);
        }
        .pay-opt-badge {
            position: absolute;
            top: -10px; left: 50%;
            transform: translateX(-50%);
            background: var(--gold);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .payment-option-header i {
            font-size: 28px;
            color: var(--blue);
            display: block;
            margin-bottom: 8px;
        }
        .payment-option-header h6 {
            font-weight: 700;
            color: var(--blue);
            margin-bottom: 4px;
            font-size: 14px;
        }
        .payment-option-card p {
            color: #777;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .payment-option-features {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            color: #666;
        }
        .payment-option-features span {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .payment-option-features i { color: #28a745; font-size: 13px; }

        /* Payment method cards (GCash / Cash) */
        .pay-method-card {
            border: 2px solid #e8ecf1;
            border-radius: 14px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.25s;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .pay-method-card:hover { border-color: var(--blue); transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.09); }
        .pay-method-card.selected { border-color: var(--blue); background: linear-gradient(135deg,#eef4ff,#e4eeff); }
        .pay-method-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }
        .pay-method-icon.gcash { background: linear-gradient(135deg,#007bff22,#007bff11); color: #007bff; }
        .pay-method-icon.cash  { background: linear-gradient(135deg,#28a74522,#28a74511); color: #28a745; }
        .pay-method-info { flex: 1; text-align: left; }
        .pay-method-info strong { display: block; font-size: 14px; font-weight: 700; color: var(--text); }
        .pay-method-info span   { font-size: 12px; color: #777; }

        /* Amount input with prefix */
        .amount-input-wrap {
            position: relative;
        }
        .amount-input-wrap .amount-prefix {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            font-weight: 700;
            color: var(--blue);
            pointer-events: none;
        }
        .amount-input-wrap input {
            padding-left: 36px !important;
        }

        /* ── Enhanced Upload Dropzone ── */
        .upload-dropzone {
            border: 2px dashed #c8d6e5;
            border-radius: 12px;
            padding: 28px 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        .upload-dropzone:hover {
            border-color: var(--blue);
            background: linear-gradient(135deg, #eef3ff 0%, #e8efff 100%);
            transform: translateY(-1px);
        }
        .upload-dropzone.dragover {
            border-color: var(--blue);
            background: rgba(26,58,108,0.06);
            box-shadow: 0 0 0 4px rgba(26,58,108,0.08);
        }
        .upload-dropzone-icon {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--blue), #2a6dd6);
            display: inline-flex;
            align-items: center; justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(26,58,108,0.25);
        }
        .upload-dropzone-icon i { font-size: 22px; color: #fff; }
        .upload-dropzone h6 {
            font-weight: 700; color: var(--text);
            margin-bottom: 4px; font-size: 14px;
        }
        .upload-dropzone p {
            font-size: 12px; color: #999; margin: 0;
        }
        .upload-dropzone .file-name-display {
            margin-top: 10px; font-size: 12px; font-weight: 600;
            color: var(--blue); display: none;
        }
        .upload-dropzone .file-name-display.show { display: block; }
        .upload-dropzone input[type="file"] {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }

        /* ── Payment Proof Cards ── */
        .proof-card {
            background: #fff;
            border: 1px solid #e8ecf1;
            border-radius: 10px;
            padding: 14px;
            display: flex; align-items: center; gap: 14px;
            transition: all 0.2s;
        }
        .proof-card:hover {
            border-color: var(--blue);
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }
        .proof-thumb {
            width: 48px; height: 48px;
            border-radius: 8px;
            background: linear-gradient(135deg, #e8efff, #d4e0ff);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .proof-thumb i { font-size: 20px; color: var(--blue); }
        .proof-info { flex: 1; min-width: 0; }
        .proof-info .proof-desc {
            font-size: 13px; font-weight: 600; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .proof-info .proof-date {
            font-size: 11px; color: #999; margin-top: 2px;
        }
        .proof-actions { display: flex; gap: 6px; align-items: center; }

        /* ── Document Checklist ── */
        .doc-checklist { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 20px; }
        .doc-check-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 14px;
            border: 1.5px solid #e8ecf1;
            border-radius: 10px;
            background: #fff;
            font-size: 13px; font-weight: 600;
            color: #555; transition: all 0.2s;
        }
        .doc-check-item.uploaded {
            border-color: #28a745;
            background: #f0fdf4;
            color: #166534;
        }
        .doc-check-item.uploaded .doc-check-icon {
            background: #28a745; color: #fff;
        }
        .doc-check-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #f0f2f5;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .doc-check-icon i { font-size: 13px; }

        /* ── Enhanced Status Pill ── */
        .status-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .status-pill.pending { background: #fff8e1; color: #e6a817; }
        .status-pill.approved { background: #e8f5e9; color: #2e7d32; }
        .status-pill.rejected { background: #fce4ec; color: #c62828; }
        .status-pill i { font-size: 10px; }

        /* ── Upload Submitted Card ── */
        .doc-uploaded-list { display: flex; flex-direction: column; gap: 8px; }
    </style>
</head>
<body>

{{-- TOPBAR --}}
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
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <div class="topbar-right">
        <a href="#" class="topbar-icon-btn">
            <i class="bi bi-bell"></i><span class="notif-dot">2</span>
        </a>
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">
                    @if($profile && $profile->photo)
                        <img src="{{ asset('storage/' . $profile->photo) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="user-chip-name">{{ Auth::user()->name }}</div>
                    <div class="user-chip-role">Student Portal</div>
                </div>
                <i class="bi bi-chevron-down user-chip-caret"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-end user-chip-dropdown">
                <div class="ucd-header">
                    <div class="ucd-avatar">
                        @if($profile && $profile->photo)
                            <img src="{{ asset('storage/' . $profile->photo) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="ucd-info">
                        <div class="ucd-name">{{ Auth::user()->name }}</div>
                        <div class="ucd-email">{{ Auth::user()->email }}</div>
                        <span class="ucd-badge">Student</span>
                    </div>
                </div>
                <div class="ucd-body">
                    <a class="ucd-item" href="#" onclick="showSection('info');return false;"><i class="bi bi-person-circle"></i> My Profile</a>
                    <a class="ucd-item" href="#" onclick="showSection('settings');return false;"><i class="bi bi-gear"></i> Settings</a>

                </div>
                <div class="ucd-divider"></div>
                <div class="ucd-footer">
                    <form method="POST" action="{{ route('logout') }}">
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

{{-- SIDEBAR --}}
<div class="dash-sidebar">
    <div class="sidebar-section-lbl">My Portal</div>
    <a href="#" class="sidebar-link active" id="nav-info" onclick="showSection('info');return false;">
        <i class="bi bi-person-fill"></i> Student Info
    </a>
    @php
        $isInstallment = $enrollment && ($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']));
        $downpaymentPaid = $isInstallment && $enrollment->downpayment_amount > 0 && $enrollment->payment_amount >= $enrollment->downpayment_amount;
        $canAccessGradesSchedule = $enrollment && $enrollment->status === 'enrolled' && ($enrollment->payment_status === 'paid' || $downpaymentPaid);
    @endphp
    @if($canAccessGradesSchedule)
    <a href="#" class="sidebar-link" id="nav-grades" onclick="showSection('grades');return false;">
        <i class="bi bi-file-earmark-text-fill"></i> Grades
    </a>
    <a href="#" class="sidebar-link" id="nav-schedule" onclick="showSection('schedule');return false;">
        <i class="bi bi-calendar3"></i> Schedule
    </a>
    @else
    <a href="#" class="sidebar-link" style="opacity:0.5;pointer-events:none;" title="Complete payment to unlock">
        <i class="bi bi-file-earmark-text-fill"></i> Grades <i class="bi bi-lock-fill ms-auto" style="font-size:11px;"></i>
    </a>
    <a href="#" class="sidebar-link" style="opacity:0.5;pointer-events:none;" title="Complete payment to unlock">
        <i class="bi bi-calendar3"></i> Schedule <i class="bi bi-lock-fill ms-auto" style="font-size:11px;"></i>
    </a>
    @endif
    <a href="#" class="sidebar-link" id="nav-enrollment" onclick="showSection('enrollment');return false;">
        <i class="bi bi-receipt-cutoff"></i> Enrollment
        @if(isset($needsReenrollment) && $needsReenrollment)
            <span class="sidebar-badge" style="background:#f59e0b;">!</span>
        @endif
    </a>
    <a href="#" class="sidebar-link" id="nav-payment" onclick="showSection('payment');return false;">
        <i class="bi bi-credit-card-fill"></i> Payment
    </a>
    {{--
    <a href="#" class="sidebar-link" id="nav-announcements" onclick="showSection('announcements');return false;">
        <i class="bi bi-megaphone-fill"></i> Announcements
        <span class="sidebar-badge">2</span>
    </a>--}}

    <div class="sidebar-divider"></div>
    <a href="#" class="sidebar-link" id="nav-settings" onclick="showSection('settings');return false;">
        <i class="bi bi-gear-fill"></i> Settings
    </a>

    <div class="sidebar-bottom">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%;background:none;text-align:left;cursor:pointer;border:none;">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="dash-main" style="padding:0;">

    @if($justPaidTransaction)
    {{-- ══════════════════════════════
         PAYMENT RECEIPT — shown after returning from Xendit checkout.
         Stays on screen until the parent dismisses it (no auto-redirect/auto-hide)
         so there's time to screenshot or print it.
    ══════════════════════════════ --}}
    @php
        $jptPaid   = $justPaidTransaction->status === 'completed';
        $jptWhen   = $justPaidTransaction->processed_at ?? $justPaidTransaction->created_at;
        $jptMethod = ['gcash'=>'GCash','maya'=>'Maya','grabpay'=>'GrabPay','bank'=>'Bank Transfer','otc'=>'Over-the-Counter'][$justPaidTransaction->payment_method] ?? ucfirst($justPaidTransaction->payment_method);
    @endphp
    <div id="xendit-receipt-card" style="max-width:560px;margin:0 auto 20px;background:#fff;border-radius:16px;box-shadow:0 8px 28px rgba(0,0,0,.12);overflow:hidden;border:1px solid #e5e7eb;">
        <div style="background:linear-gradient(135deg,{{ $jptPaid ? '#166534,#16a34a' : '#92400e,#c5a059' }});padding:22px 24px;color:#fff;position:relative;">
            <button type="button" onclick="document.getElementById('xendit-receipt-card').remove()"
                aria-label="Dismiss" style="position:absolute;top:14px;right:14px;background:rgba(255,255,255,.18);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;line-height:1;">&times;</button>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">
                    <i class="bi bi-{{ $jptPaid ? 'check-lg' : 'hourglass-split' }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:17px;">{{ $jptPaid ? 'Payment Received' : 'Payment Processing' }}</div>
                    <div style="font-size:12px;opacity:.9;">{{ $jptPaid ? 'Your payment was successful.' : "We're confirming your payment — this updates automatically." }}</div>
                </div>
            </div>
        </div>
        <div style="padding:22px 24px;">
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px dashed #e5e7eb;font-size:13px;">
                <span style="color:#666;">Amount</span>
                <span style="font-weight:700;color:#1a3a6c;font-size:16px;">₱{{ number_format($justPaidTransaction->amount, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px dashed #e5e7eb;font-size:13px;">
                <span style="color:#666;">Reference No.</span>
                <span style="font-weight:600;font-family:monospace;">{{ $justPaidTransaction->reference_number }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px dashed #e5e7eb;font-size:13px;">
                <span style="color:#666;">Method</span>
                <span style="font-weight:600;">{{ $jptMethod }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:9px 0;font-size:13px;">
                <span style="color:#666;">Date</span>
                <span style="font-weight:600;">{{ $jptWhen->format('M d, Y h:i A') }}</span>
            </div>
            <div style="display:flex;gap:10px;margin-top:18px;">
                <button type="button" onclick="window.print()" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #1a3a6c;background:#fff;color:#1a3a6c;font-weight:700;font-size:13px;cursor:pointer;">
                    <i class="bi bi-printer-fill me-1"></i> Print / Save
                </button>
                <button type="button" onclick="document.getElementById('xendit-receipt-card').remove()" style="flex:1;padding:10px;border-radius:9px;border:none;background:#1a3a6c;color:#fff;font-weight:700;font-size:13px;cursor:pointer;">
                    Done
                </button>
            </div>
        </div>
    </div>
    <style>
        @media print {
            .dash-topbar, .dash-sidebar, #section-info > *:not(#xendit-receipt-card),
            .student-info-section > *:not(#xendit-receipt-card) { display:none !important; }
            #xendit-receipt-card button { display:none !important; }
        }
    </style>
    @endif

    {{-- ══════════════════════════════
         WELCOME & PROFILE COMPLETION PROMPT
    ══════════════════════════════ --}}
    <div id="section-info" class="student-info-section">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Re-enrollment moved to Enrollment section --}}
        @if(isset($needsReenrollment) && $needsReenrollment)
        <div style="background:linear-gradient(135deg,#fff8e1,#fffbf0);border:1px solid #fcd34d;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="bi bi-mortarboard-fill" style="color:#d97706;font-size:20px;"></i>
                <div>
                    <div style="font-weight:700;font-size:13px;color:#92400e;">
                        {{ $reenrollmentOpen ?? false ? 'Enrollment for S.Y. '.($currentSchoolYear ?? '').' is now open!' : 'New school year enrollment is coming soon.' }}
                    </div>
                    <div style="font-size:11px;color:#92400e;opacity:0.8;">Go to Enrollment to see your checklist and re-enroll.</div>
                </div>
            </div>
            <button onclick="showSection('enrollment')" style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
                <i class="bi bi-receipt-cutoff me-1"></i> Go to Enrollment
            </button>
        </div>
        @endif

        {{-- ── Required Documents Notice ── --}}
        @if($enrollment)
        @php
            $_reqDocTypes = ['birth_certificate'=>'Birth Certificate (PSA)','form_137'=>'Form 137','report_card'=>'Report Card / Grades','two_by_two_picture'=>'2x2 ID Picture'];
            $_allDocs = \App\Models\StudentDocument::where(function($q) use ($enrollment) {
                $q->where('enrollment_id', $enrollment->id)->orWhere('user_id', Auth::id());
            })->whereIn('document_type', array_keys($_reqDocTypes))->get()->keyBy('document_type');
            $_approvedCount = $_allDocs->filter(fn($d) => $d->status === 'approved')->count();
            $_pendingCount  = $_allDocs->filter(fn($d) => $d->status === 'pending')->count();
            $_missingTypes  = array_keys(array_filter($_reqDocTypes, fn($label, $type) => !$_allDocs->has($type), ARRAY_FILTER_USE_BOTH));
        @endphp
        @if($_approvedCount < 4)
        <div style="background:{{ $_approvedCount === 0 ? 'linear-gradient(135deg,#fff3e0,#fef9f0)' : 'linear-gradient(135deg,#e8f4ff,#f0f8ff)' }};
                    border:1.5px solid {{ $_approvedCount === 0 ? '#fb923c' : '#60a5fa' }};
                    border-radius:12px;padding:16px 20px;margin-bottom:20px;">
            <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
                <div style="width:44px;height:44px;border-radius:50%;background:{{ $_approvedCount === 0 ? '#fb923c' : '#3b82f6' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-{{ $_approvedCount === 0 ? 'exclamation-triangle-fill' : 'hourglass-split' }}" style="color:#fff;font-size:20px;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:700;font-size:14px;color:{{ $_approvedCount === 0 ? '#9a3412' : '#1e40af' }};margin-bottom:4px;">
                        @if($_approvedCount === 0)
                            Required Documents Not Yet Submitted
                        @elseif($_pendingCount > 0)
                            Documents Under Review ({{ $_pendingCount }} pending)
                        @else
                            {{ 4 - $_approvedCount }} Document{{ (4 - $_approvedCount) !== 1 ? 's' : '' }} Still Needed
                        @endif
                    </div>
                    <div style="font-size:12px;color:#666;line-height:1.6;margin-bottom:10px;">
                        @if($_approvedCount === 0)
                            As a new enrollee, you must submit all required documents to complete your enrollment.
                            Please upload them as soon as possible.
                        @elseif($_pendingCount > 0)
                            Your uploaded documents are being reviewed by the registrar. You will be notified once approved.
                        @else
                            Please upload the remaining {{ 4 - $_approvedCount }} document(s) to fully complete your enrollment.
                        @endif
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
                        @foreach($_reqDocTypes as $type => $label)
                            @php
                                $doc = $_allDocs->get($type);
                                $status = $doc ? $doc->status : 'missing';
                            @endphp
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                {{ $status === 'approved' ? 'background:#dcfce7;color:#166534;' : ($status === 'pending' ? 'background:#dbeafe;color:#1e40af;' : 'background:#fee2e2;color:#991b1b;') }}">
                                <i class="bi bi-{{ $status === 'approved' ? 'check-circle-fill' : ($status === 'pending' ? 'clock-fill' : 'x-circle-fill') }}"></i>
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                    @if(count($_missingTypes) > 0 || $_pendingCount === 0)
                    <button onclick="showSection('enrollment'); setTimeout(()=>{ const el=document.getElementById('doc-upload-card'); if(el) el.scrollIntoView({behavior:'smooth'}); }, 200);"
                        style="background:{{ $_approvedCount === 0 ? '#fb923c' : '#3b82f6' }};color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:12px;font-weight:700;cursor:pointer;">
                        <i class="bi bi-upload me-1"></i>
                        {{ $_approvedCount === 0 ? 'Upload Documents Now' : 'Upload Remaining Documents' }}
                    </button>
                    @endif
                </div>
                <div style="text-align:center;flex-shrink:0;">
                    <div style="font-size:28px;font-weight:700;color:{{ $_approvedCount === 0 ? '#fb923c' : '#3b82f6' }};">{{ $_approvedCount }}/4</div>
                    <div style="font-size:10px;color:#999;text-transform:uppercase;letter-spacing:0.5px;">Approved</div>
                </div>
            </div>
        </div>
        @endif
        @endif

        @php
            $d = $enrollment ? ($enrollment->student_data ?? []) : [];
            $gradeMap  = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];

            // Prefer normalized model data, fall back to student_data JSON
            $pFirst    = $profile->first_name  ?? $d['first_name']  ?? '';
            $pMiddle   = $profile->middle_name ?? $d['middle_name'] ?? '';
            $pLast     = $profile->last_name   ?? $d['last_name']   ?? '';
            $pSuffix   = $profile->suffix      ?? $d['suffix']      ?? '';
            $pBirth    = $profile->birthdate   ?? $d['birthdate']   ?? '';
            $pGender   = $profile->gender      ?? $d['gender']      ?? '';
            $pPob      = $profile->place_of_birth        ?? $d['place_of_birth']        ?? '';
            $pNat      = $profile->nationality           ?? $d['nationality']           ?? '';
            $pRel      = $profile->religious_affiliation ?? $d['religious_affiliation'] ?? '';
            $pContact  = $profile->contact     ?? $d['contact']     ?? '';
            $pBlood    = $profile->blood_type       ?? $d['blood_type']        ?? '';
            $pAllergy  = $profile->allergies        ?? $d['allergies']         ?? '';
            $pMedical  = $profile->medical_conditions ?? $d['medical_conditions'] ?? '';

            $aProvince = $address->province      ?? $d['province']      ?? '';
            $aCity     = $address->city          ?? $d['city']          ?? '';
            $aBarangay = $address->barangay      ?? $d['barangay']      ?? '';
            $aStreet   = $address->street_address ?? $d['street_address'] ?? '';
            $aZip      = $address->zip_code      ?? $d['zip_code']      ?? '';
            $aRegion   = $address->region        ?? $d['region']        ?? '';

            $gName     = $guardian->name         ?? $d['guardian_name']  ?? '';
            $gRelation = $guardian->relationship ?? $d['relationship']   ?? '';
            $gPhone    = $guardian->contact      ?? $d['guardian_phone'] ?? '';
            $gEmail    = $guardian->email        ?? $d['guardian_email'] ?? '';
            $gOccup    = $guardian->occupation   ?? $d['guardian_occupation'] ?? '';

            $mName     = $mother->name ?? $d['mother_name'] ?? '';
            $mAge      = $mother->age  ?? $d['mother_age']  ?? '';
            $fName     = $father->name ?? $d['father_name'] ?? '';
            $fAge      = $father->age  ?? $d['father_age']  ?? '';

            $psName    = $previousSchool->school_name         ?? $d['last_school']          ?? '';
            $psAddr    = $previousSchool->school_address      ?? $d['last_school_address']  ?? '';
            $psGrade   = $previousSchool->last_grade_completed ?? $d['last_grade_completed'] ?? '';
            $psSY      = $previousSchool->school_year_graduated ?? $d['last_school_year']   ?? '';
            $psAvg     = $previousSchool->general_average     ?? '';

            $gradeRaw  = $d['grade_level'] ?? $enrollment->grade_level ?? '';
            $gradeDisp = $gradeMap[$gradeRaw] ?? ($gradeRaw ? ucfirst($gradeRaw) : 'N/A');
            $fullName  = trim("$pFirst $pMiddle $pLast") ?: Auth::user()->name;
            $initials  = collect(explode(' ', $fullName))->filter()->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
            $enrStatus = $enrollment->status ?? null;
            $enrStatusColor = match($enrStatus) { 'enrolled'=>'#28a745','approved'=>'#17a2b8','pending'=>'#e67e00',default=>'#dc3545' };
            $payStatus      = $enrollment->payment_status ?? 'pending';
            $payStatusColor = match($payStatus) { 'paid'=>'#28a745','partial'=>'#1976d2',default=>'#e67e00' };

            // CSS helper for read-only field value display
            $fv = 'font-size:14px;font-weight:600;color:var(--text);padding:6px 0;border-bottom:1px solid var(--border);min-height:32px;';
            $fl = 'font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;';
            // CSS helper for edit input
            $inp = 'width:100%;padding:8px 12px;border:1.5px solid #d0d5dd;border-radius:7px;font-size:13px;font-family:inherit;color:var(--text);background:#fff;';
        @endphp

        {{-- ── PROFILE HERO ── --}}
        <div style="background:linear-gradient(135deg,#1a3a6c,#2c5282);border-radius:14px;padding:24px 28px;margin-bottom:20px;color:#fff;">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                {{-- Avatar / Photo --}}
                <div style="position:relative;flex-shrink:0;">
                    @if($profile && $profile->photo)
                        <img src="{{ Storage::url($profile->photo) }}" alt="Photo"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.4);">
                    @else
                        <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;letter-spacing:1px;border:3px solid rgba(255,255,255,0.3);">
                            {{ $initials ?: 'ST' }}
                        </div>
                    @endif
                    {{-- Camera icon overlay to trigger upload --}}
                    <label for="heroPhotoInput" title="Change photo"
                           style="position:absolute;bottom:0;right:0;width:26px;height:26px;border-radius:50%;background:#f5a623;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;">
                        <i class="bi bi-camera-fill" style="font-size:12px;color:#1a3a6c;"></i>
                    </label>
                    <form id="heroPhotoForm" method="POST" action="{{ route('student.photo.upload') }}" enctype="multipart/form-data" style="display:none;">
                        @csrf
                        <input type="file" id="heroPhotoInput" name="photo" accept="image/jpeg,image/png,image/webp"
                               onchange="document.getElementById('heroPhotoForm').submit();">
                    </form>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:20px;font-weight:700;margin-bottom:6px;">{{ $fullName }}</div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        @if($gradeDisp !== 'N/A')
                            <span style="background:rgba(255,255,255,0.2);padding:3px 14px;border-radius:20px;font-size:12px;font-weight:700;"><i class="bi bi-mortarboard-fill me-1"></i>{{ $gradeDisp }}</span>
                        @endif
                        @if($enrollment)
                            <span style="background:rgba(255,255,255,0.12);padding:3px 12px;border-radius:20px;font-size:11px;">{{ $enrollment->reference_number }}</span>
                            @if($enrStatus)
                                <span style="padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $enrStatusColor }};">{{ ucfirst($enrStatus) }}</span>
                            @endif
                            <span style="padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $payStatusColor }};">Payment: {{ ucfirst($payStatus) }}</span>
                        @endif
                    </div>
                </div>
                @if($enrollment && $enrollment->status === 'approved' && $enrollment->payment_status !== 'paid')
                <div style="flex-shrink:0;">
                    <button onclick="showSection('payment')" style="display:inline-flex;align-items:center;gap:6px;background:#f5a623;color:#1a3a6c;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;border:none;cursor:pointer;">
                        <i class="bi bi-credit-card"></i> Pay Now
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- ── INFO TABS ── --}}
        <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:20px;background:#f0f2f5;padding:6px;border-radius:10px;">
            @foreach(['personal'=>['bi-person-fill','Personal Info'],'guardian'=>['bi-people-fill','Guardian'],'address'=>['bi-geo-alt-fill','Address'],'enrollment'=>['bi-mortarboard-fill','Enrollment'],'health'=>['bi-heart-pulse-fill','Health'],'payment'=>['bi-credit-card-fill','Payment'],'documents'=>['bi-file-earmark-check','Documents']] as $tk=>[$ti,$tl])
                <button type="button" id="infoTabBtn-{{ $tk }}" onclick="switchInfoTab('{{ $tk }}')"
                    style="display:flex;align-items:center;gap:5px;padding:8px 14px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;{{ $tk==='personal'?'background:var(--blue);color:#fff;':'background:transparent;color:#666;' }}">
                    <i class="bi {{ $ti }}"></i> {{ $tl }}
                </button>
            @endforeach
        </div>

        @php
        // Shared edit-button style
        $editBtnStyle = 'display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border:1.5px solid var(--blue);border-radius:7px;font-size:12px;font-weight:600;color:var(--blue);background:#fff;cursor:pointer;';
        $cancelBtnStyle = 'display:none;align-items:center;gap:5px;padding:6px 14px;border:1.5px solid #aaa;border-radius:7px;font-size:12px;font-weight:600;color:#666;background:#fff;cursor:pointer;';
        $saveBtnStyle = 'display:inline-flex;align-items:center;gap:5px;padding:8px 20px;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;';
        @endphp

        {{-- ═══════════════════════════════════════
             TAB: PERSONAL INFO (default, editable)
        ════════════════════════════════════════ --}}
        <div id="infoTab-personal" class="content-card">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-person-fill me-2" style="color:var(--gold);"></i>Personal Information</h6>
                <div style="display:flex;gap:6px;">
                    <button id="editBtn-personal" onclick="editInfoTab('personal')" style="{{ $editBtnStyle }}"><i class="bi bi-pencil"></i> Edit</button>
                    <button id="cancelBtn-personal" onclick="cancelEditInfoTab('personal')" style="{{ $cancelBtnStyle }}"><i class="bi bi-x"></i> Cancel</button>
                </div>
            </div>
            {{-- VIEW --}}
            <div id="view-personal" style="padding:20px;">
                <div class="row g-3">
                    @foreach([
                        ['First Name',$pFirst,'col-md-4'],['Middle Name',$pMiddle,'col-md-4'],['Last Name',$pLast,'col-md-4'],
                        ['Suffix',$pSuffix,'col-md-3'],['Gender',ucfirst($pGender),'col-md-3'],
                        ['Date of Birth',$pBirth ? \Carbon\Carbon::parse($pBirth)->format('F d, Y') : '—','col-md-3'],
                        ['Age',$pBirth ? \Carbon\Carbon::parse($pBirth)->age.' yrs old' : '—','col-md-3'],
                        ['Place of Birth',$pPob,'col-md-6'],['Nationality',$pNat,'col-md-6'],
                        ['Religion',$pRel,'col-md-6'],['Grade Level',$gradeDisp,'col-md-6'],
                        ['Email Address',Auth::user()->email,'col-md-6'],['Contact No.',$pContact,'col-md-6'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}">
                            <div style="{{ $fl }}">{{ $l }}</div>
                            <div style="{{ $fv }}{{ $l==='Grade Level'?'color:var(--blue);':'' }}">{{ $v ?: '—' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- EDIT FORM --}}
            <div id="form-personal" style="display:none;padding:20px;">
                <form method="POST" action="{{ route('profile.personal.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4"><label style="{{ $fl }}">First Name *</label><input name="first_name" value="{{ $pFirst }}" required style="{{ $inp }}"></div>
                        <div class="col-md-4"><label style="{{ $fl }}">Middle Name</label><input name="middle_name" value="{{ $pMiddle }}" style="{{ $inp }}"></div>
                        <div class="col-md-4"><label style="{{ $fl }}">Last Name *</label><input name="last_name" value="{{ $pLast }}" required style="{{ $inp }}"></div>
                        <div class="col-md-3"><label style="{{ $fl }}">Suffix</label>
                            <select name="suffix" style="{{ $inp }}">
                                @foreach([''=> 'None','Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III'] as $sv=>$sl)
                                    <option value="{{ $sv }}" {{ $pSuffix===$sv?'selected':''}}>{{ $sl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><label style="{{ $fl }}">Gender *</label>
                            <select name="gender" required style="{{ $inp }}">
                                <option value="male" {{ $pGender==='male'?'selected':''}}>Male</option>
                                <option value="female" {{ $pGender==='female'?'selected':''}}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-3"><label style="{{ $fl }}">Date of Birth *</label><input type="date" name="birthdate" id="edit-birthdate-input" value="{{ $pBirth }}" required style="{{ $inp }}" oninput="updateAgeDisplay()"></div>
                        <div class="col-md-3"><label style="{{ $fl }}">Age</label><div id="edit-age-display" style="padding:8px 12px;background:#f0f4ff;border:1.5px solid #c8d6f0;border-radius:6px;font-size:13px;color:#1d3557;font-weight:600;">{{ $pBirth ? \Carbon\Carbon::parse($pBirth)->age.' yrs old' : '—' }}</div></div>
                        <div class="col-md-3"><label style="{{ $fl }}">Contact No.</label><input name="contact" value="{{ $pContact }}" style="{{ $inp }}" placeholder="09XX-XXX-XXXX"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Place of Birth</label><input name="place_of_birth" value="{{ $pPob }}" style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Nationality</label><input name="nationality" value="{{ $pNat }}" style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Religious Affiliation</label><input name="religious_affiliation" value="{{ $pRel }}" style="{{ $inp }}"></div>
                        <div class="col-md-3"><label style="{{ $fl }}">Mother's Name</label><input name="mother_name" value="{{ $mName }}" style="{{ $inp }}"></div>
                        <div class="col-md-1"><label style="{{ $fl }}">Age</label><input type="number" name="mother_age" value="{{ $mAge }}" style="{{ $inp }}" min="1" max="120"></div>
                        <div class="col-md-3"><label style="{{ $fl }}">Father's Name</label><input name="father_name" value="{{ $fName }}" style="{{ $inp }}"></div>
                        <div class="col-md-1"><label style="{{ $fl }}">Age</label><input type="number" name="father_age" value="{{ $fAge }}" style="{{ $inp }}" min="1" max="120"></div>
                    </div>
                    <div style="margin-top:18px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border);padding-top:16px;">
                        <button type="button" onclick="cancelEditInfoTab('personal')" style="padding:8px 18px;border:1.5px solid #aaa;border-radius:8px;font-size:13px;font-weight:600;color:#666;background:#fff;cursor:pointer;">Cancel</button>
                        <button type="submit" style="{{ $saveBtnStyle }}"><i class="bi bi-check-lg"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: GUARDIAN (editable)
        ════════════════════════════════════════ --}}
        <div id="infoTab-guardian" class="content-card" style="display:none;">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-people-fill me-2" style="color:var(--gold);"></i>Guardian Information</h6>
                <div style="display:flex;gap:6px;">
                    <button id="editBtn-guardian" onclick="editInfoTab('guardian')" style="{{ $editBtnStyle }}"><i class="bi bi-pencil"></i> Edit</button>
                    <button id="cancelBtn-guardian" onclick="cancelEditInfoTab('guardian')" style="{{ $cancelBtnStyle }}"><i class="bi bi-x"></i> Cancel</button>
                </div>
            </div>
            {{-- VIEW --}}
            <div id="view-guardian" style="padding:20px;">
                <div class="row g-3">
                    @foreach([
                        ['Guardian Name',$gName,'col-md-6'],['Relationship',ucfirst($gRelation),'col-md-6'],
                        ['Contact Number',$gPhone,'col-md-6'],['Guardian Email',$gEmail,'col-md-6'],
                        ['Occupation',$gOccup,'col-md-6'],["Mother's Name",$mName,'col-md-4'],["Mother's Age",$mAge,'col-md-2'],
                        ["Father's Name",$fName,'col-md-4'],["Father's Age",$fAge,'col-md-2'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>
            </div>
            {{-- EDIT FORM --}}
            <div id="form-guardian" style="display:none;padding:20px;">
                <form method="POST" action="{{ route('profile.guardian.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><label style="{{ $fl }}">Guardian Name *</label><input name="guardian_name" value="{{ $gName }}" required style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Relationship *</label><input name="relationship" value="{{ $gRelation }}" required style="{{ $inp }}" placeholder="e.g. Father, Mother"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Contact Number *</label><input name="guardian_phone" value="{{ $gPhone }}" required style="{{ $inp }}" placeholder="09XX-XXX-XXXX"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Guardian Email</label><input type="email" name="student_email" value="{{ $gEmail }}" style="{{ $inp }}"></div>
                        <div class="col-md-12"><label style="{{ $fl }}">Occupation</label><input name="guardian_occupation" value="{{ $gOccup }}" style="{{ $inp }}"></div>
                    </div>
                    <div style="margin-top:18px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border);padding-top:16px;">
                        <button type="button" onclick="cancelEditInfoTab('guardian')" style="padding:8px 18px;border:1.5px solid #aaa;border-radius:8px;font-size:13px;font-weight:600;color:#666;background:#fff;cursor:pointer;">Cancel</button>
                        <button type="submit" style="{{ $saveBtnStyle }}"><i class="bi bi-check-lg"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: ADDRESS (editable)
        ════════════════════════════════════════ --}}
        <div id="infoTab-address" class="content-card" style="display:none;">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-geo-alt-fill me-2" style="color:var(--gold);"></i>Home Address</h6>
                <div style="display:flex;gap:6px;">
                    <button id="editBtn-address" onclick="editInfoTab('address')" style="{{ $editBtnStyle }}"><i class="bi bi-pencil"></i> Edit</button>
                    <button id="cancelBtn-address" onclick="cancelEditInfoTab('address')" style="{{ $cancelBtnStyle }}"><i class="bi bi-x"></i> Cancel</button>
                </div>
            </div>
            {{-- VIEW --}}
            <div id="view-address" style="padding:20px;">
                <div class="row g-3">
                    @foreach([
                        ['Region',$aRegion,'col-md-6'],['Province',$aProvince,'col-md-6'],
                        ['City / Municipality',$aCity,'col-md-6'],['Barangay',$aBarangay,'col-md-6'],
                        ['Street Address',$aStreet,'col-md-8'],['ZIP Code',$aZip,'col-md-4'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>
            </div>
            {{-- EDIT FORM --}}
            <div id="form-address" style="display:none;padding:20px;">
                <form method="POST" action="{{ route('profile.address.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><label style="{{ $fl }}">Province *</label><input name="province" value="{{ $aProvince }}" required style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">City / Municipality *</label><input name="city" value="{{ $aCity }}" required style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">Barangay *</label><input name="barangay" value="{{ $aBarangay }}" required style="{{ $inp }}"></div>
                        <div class="col-md-6"><label style="{{ $fl }}">ZIP Code</label><input name="zip_code" value="{{ $aZip }}" style="{{ $inp }}"></div>
                        <div class="col-md-12"><label style="{{ $fl }}">Street Address *</label><input name="street_address" value="{{ $aStreet }}" required style="{{ $inp }}"></div>
                    </div>
                    <div style="margin-top:18px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border);padding-top:16px;">
                        <button type="button" onclick="cancelEditInfoTab('address')" style="padding:8px 18px;border:1.5px solid #aaa;border-radius:8px;font-size:13px;font-weight:600;color:#666;background:#fff;cursor:pointer;">Cancel</button>
                        <button type="submit" style="{{ $saveBtnStyle }}"><i class="bi bi-check-lg"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: ENROLLMENT (read-only, includes previous school)
        ════════════════════════════════════════ --}}
        <div id="infoTab-enrollment" class="content-card" style="display:none;">
            <div class="content-card-header">
                <h6><i class="bi bi-mortarboard-fill me-2" style="color:var(--gold);"></i>Enrollment Information</h6>
            </div>
            <div style="padding:20px;">
                @if($enrollment)
                <div class="row g-3">
                    @foreach([
                        ['Reference No.',$enrollment->reference_number??'—','col-md-6'],
                        ['School Year',$enrollment->school_year??'—','col-md-6'],
                        ['Grade Level',$gradeDisp,'col-md-6'],
                        ['Section',$enrollment->section??'Not yet assigned','col-md-6'],
                        ['Student Type',ucfirst($d['student_type']??'—'),'col-md-6'],
                        ['Enrollment Status',ucfirst($enrollment->status??'—'),'col-md-6'],
                        ['Date Enrolled',$enrollment->created_at->format('F d, Y'),'col-md-6'],
                        ['LRN',$d['lrn']??'—','col-md-6'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>

                {{-- Previous School divider --}}
                <div style="margin:20px 0 16px;border-top:1px solid var(--border);padding-top:16px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-building" style="color:var(--gold);font-size:14px;"></i>
                    <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;">Previous School</span>
                </div>
                <div class="row g-3">
                    @foreach([
                        ['Last School Attended',$psName,'col-md-6'],
                        ['School Address',$psAddr,'col-md-6'],
                        ['Last Grade Completed',$psGrade,'col-md-4'],
                        ['School Year Completed',$psSY,'col-md-4'],
                        ['General Average',$psAvg,'col-md-4'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>
                @else
                    <div style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-clipboard-x" style="font-size:40px;display:block;margin-bottom:10px;opacity:.4;"></i>No enrollment record found.</div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: HEALTH (editable)
        ════════════════════════════════════════ --}}
        <div id="infoTab-health" class="content-card" style="display:none;">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-heart-pulse-fill me-2" style="color:var(--gold);"></i>Health Information</h6>
                <div style="display:flex;gap:6px;">
                    <button id="editBtn-health" onclick="editInfoTab('health')" style="{{ $editBtnStyle }}"><i class="bi bi-pencil"></i> Edit</button>
                    <button id="cancelBtn-health" onclick="cancelEditInfoTab('health')" style="{{ $cancelBtnStyle }}"><i class="bi bi-x"></i> Cancel</button>
                </div>
            </div>
            {{-- VIEW --}}
            <div id="view-health" style="padding:20px;">
                <div class="row g-3">
                    @foreach([
                        ['Blood Type',$pBlood,'col-md-4'],['Allergies',$pAllergy,'col-md-4'],['Medical Conditions',$pMedical,'col-md-4'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>
            </div>
            {{-- EDIT FORM --}}
            <div id="form-health" style="display:none;padding:20px;">
                <form method="POST" action="{{ route('profile.health.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4"><label style="{{ $fl }}">Blood Type</label>
                            <select name="blood_type" style="{{ $inp }}">
                                @foreach([''=> 'Select','A+'=>'A+','A-'=>'A-','B+'=>'B+','B-'=>'B-','O+'=>'O+','O-'=>'O-','AB+'=>'AB+','AB-'=>'AB-','unknown'=>'Unknown'] as $bv=>$bl)
                                    <option value="{{ $bv }}" {{ $pBlood===$bv?'selected':''}}>{{ $bl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label style="{{ $fl }}">Allergies</label><input name="allergies" value="{{ $pAllergy }}" style="{{ $inp }}" placeholder="List any allergies"></div>
                        <div class="col-md-4"><label style="{{ $fl }}">Medical Conditions</label><input name="medical_conditions" value="{{ $pMedical }}" style="{{ $inp }}" placeholder="Any chronic conditions"></div>
                    </div>
                    <div style="margin-top:18px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border);padding-top:16px;">
                        <button type="button" onclick="cancelEditInfoTab('health')" style="padding:8px 18px;border:1.5px solid #aaa;border-radius:8px;font-size:13px;font-weight:600;color:#666;background:#fff;cursor:pointer;">Cancel</button>
                        <button type="submit" style="{{ $saveBtnStyle }}"><i class="bi bi-check-lg"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: PAYMENT (read-only)
        ════════════════════════════════════════ --}}
        <div id="infoTab-payment" class="content-card" style="display:none;">
            <div class="content-card-header">
                <h6><i class="bi bi-credit-card-fill me-2" style="color:var(--gold);"></i>Payment Status</h6>
            </div>
            <div style="padding:20px;">
                @if($enrollment)
                @php
                    $optLabels = ['A'=>'Full Payment','B'=>'2-Term Installment','C'=>'3-Term Installment','D'=>'Monthly Installment'];
                    $amtPaid  = $enrollment->payment_amount ?? 0;
                    $totalFee = $enrollment->total_fee ?? 0;
                    $balance  = $enrollment->remaining_balance ?? max(0,$totalFee-$amtPaid);
                    $payPct   = $totalFee > 0 ? min(100,round($amtPaid/$totalFee*100)) : 0;
                @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:20px;">
                    <div style="background:#e3f2fd;border-radius:10px;padding:16px;text-align:center;"><div style="font-size:10px;color:#1565c0;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Total Fee</div><div style="font-size:18px;font-weight:700;color:#1565c0;">₱{{ number_format($totalFee,2) }}</div></div>
                    <div style="background:#e8f5e9;border-radius:10px;padding:16px;text-align:center;"><div style="font-size:10px;color:#2e7d32;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Amount Paid</div><div style="font-size:18px;font-weight:700;color:#2e7d32;">₱{{ number_format($amtPaid,2) }}</div></div>
                    <div style="background:{{ $balance>0?'#fff3e0':'#e8f5e9' }};border-radius:10px;padding:16px;text-align:center;"><div style="font-size:10px;color:{{ $balance>0?'#e65100':'#2e7d32' }};font-weight:700;text-transform:uppercase;margin-bottom:4px;">Balance</div><div style="font-size:18px;font-weight:700;color:{{ $balance>0?'#e65100':'#2e7d32' }};">₱{{ number_format($balance,2) }}</div></div>
                    <div style="background:#f3e5f5;border-radius:10px;padding:16px;text-align:center;"><div style="font-size:10px;color:#6a1b9a;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Progress</div><div style="font-size:18px;font-weight:700;color:#6a1b9a;">{{ $payPct }}%</div></div>
                </div>
                <div style="height:10px;background:#e0e0e0;border-radius:5px;margin-bottom:20px;overflow:hidden;">
                    <div style="height:100%;border-radius:5px;background:{{ $payPct>=100?'#28a745':'var(--blue)' }};width:{{ $payPct }}%;"></div>
                </div>
                <div class="row g-3">
                    @foreach([
                        ['Payment Status',ucfirst($payStatus),'col-md-4'],
                        ['Payment Type',ucfirst($enrollment->payment_type??'—'),'col-md-4'],
                        ['Payment Option',$optLabels[$enrollment->payment_option??'']??($enrollment->payment_option??'—'),'col-md-4'],
                        ['Payment Method',ucfirst($enrollment->payment_method??'—'),'col-md-4'],
                        ['Reference No.',$enrollment->payment_reference??'—','col-md-4'],
                        ['Next Due Date',$enrollment->next_installment_date?->format('M d, Y')??'—','col-md-4'],
                    ] as [$l,$v,$c])
                        <div class="{{ $c }}"><div style="{{ $fl }}">{{ $l }}</div><div style="{{ $fv }}">{{ $v ?: '—' }}</div></div>
                    @endforeach
                </div>
                @if($enrollment->status === 'approved' && $payStatus !== 'paid')
                    <div style="margin-top:20px;text-align:center;">
                        <button onclick="showSection('payment')" style="{{ $saveBtnStyle }}"><i class="bi bi-credit-card me-1"></i> Make a Payment</button>
                    </div>
                @endif
                @else
                    <div style="text-align:center;padding:40px;color:var(--muted);"><i class="bi bi-credit-card-2-front" style="font-size:40px;display:block;margin-bottom:10px;opacity:.4;"></i>No payment information available.</div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB: DOCUMENTS (read-only + upload link)
        ════════════════════════════════════════ --}}
        <div id="infoTab-documents" class="content-card" style="display:none;">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-file-earmark-check me-2" style="color:var(--gold);"></i>Required Documents</h6>
                <button onclick="showSection('enrollment')" style="{{ $saveBtnStyle }}"><i class="bi bi-upload me-1"></i> Upload Documents</button>
            </div>
            <div style="padding:20px;">
                @php
                    $docTypes = [
                        'birth_certificate'  => 'Birth Certificate (PSA)',
                        'form_137'           => 'Form 137',
                        'report_card'        => 'Report Card / Grades',
                        'two_by_two_picture' => '2x2 ID Picture',
                    ];
                    $uploadedDocs = isset($enrollment) ? \App\Models\StudentDocument::where(function($q) use ($enrollment) {
                        $q->where('enrollment_id',$enrollment->id)->orWhere('user_id',Auth::id());
                    })->get()->keyBy('document_type') : collect();
                @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                    @foreach($docTypes as $dk=>$dl)
                        @php $doc=$uploadedDocs->get($dk); $ok=$doc&&$doc->status==='approved'; @endphp
                        <div style="display:flex;align-items:center;gap:10px;padding:14px;border:1.5px solid {{ $ok?'#28a745':'#e0e0e0' }};border-radius:10px;background:{{ $ok?'#f0fdf4':'#fafafa' }};">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $ok?'#28a745':'#e0e0e0' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-{{ $ok?'check-lg':'file-earmark' }}" style="color:{{ $ok?'#fff':'#999' }};font-size:13px;"></i>
                            </div>
                            <div><div style="font-size:12px;font-weight:600;color:{{ $ok?'#166534':'#555' }};">{{ $dl }}</div><div style="font-size:10px;color:{{ $ok?'#22c55e':'#aaa' }};margin-top:1px;">{{ $ok?'Approved':($doc?ucfirst($doc->status):'Not submitted') }}</div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════
         SECTION: GRADES
    ══════════════════════════════ --}}
    <div id="section-grades" class="student-info-section" style="display:none;">

        <div class="info-section-title">My Grades</div>

        @php
            $glMap   = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
            $glRaw   = $enrollment ? ($enrollment->student_data['grade_level'] ?? $enrollment->grade_level) : '';
            $glLabel = $glMap[$glRaw] ?? ucfirst($glRaw) ?: 'N/A';

            // Build school year options:
            // - All years student has been enrolled
            // - Current year
            // - Next 3 future years
            $enrolledYears = auth()->user()->enrollments()
                ->whereNotNull('school_year')
                ->pluck('school_year')
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $currentSY = $enrollment->school_year ?? '';
            $baseY = $currentSY ? (int) explode('-', $currentSY)[0] : (now()->month >= 6 ? now()->year : now()->year - 1);

            $futureYears = [];
            for ($fy = $baseY + 1; $fy <= $baseY + 3; $fy++) {
                $futureYears[] = $fy . '-' . ($fy + 1);
            }

            $allSYOptions = collect(array_merge($enrolledYears, $futureYears))
                ->unique()->sort()->values()->toArray();
        @endphp

        {{-- Filter Bar: School Year + Term --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;">

            {{-- School Year --}}
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">School Year</label>
                <select id="gradesSYFilter"
                        onchange="loadGrades(currentGradeTerm, document.querySelector('.q-tab.active')); loadGWA();"
                        style="border:1.5px solid #d1d5db;border-radius:8px;padding:7px 12px;font-size:13px;font-weight:600;color:var(--text);background:#fff;cursor:pointer;min-width:130px;">
                    @foreach($allSYOptions as $syOpt)
                        <option value="{{ $syOpt }}" {{ $syOpt === $currentSY ? 'selected' : '' }}>
                            {{ $syOpt }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Divider --}}
            <div style="width:1px;height:36px;background:#e5e7eb;"></div>

            {{-- Term Tabs --}}
            <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">Term</label>
                <div style="display:flex;gap:6px;">
                    <button class="q-tab active" onclick="loadGrades(1, this); return false;"
                        style="border:1.5px solid var(--blue);background:var(--blue);color:#fff;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        Term 1
                    </button>
                    <button class="q-tab" onclick="loadGrades(2, this); return false;"
                        style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        Term 2
                    </button>
                    <button class="q-tab" onclick="loadGrades(3, this); return false;"
                        style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        Term 3
                    </button>
                </div>
            </div>
        </div>

        {{-- GWA Summary — Grade 1-6 only (not descriptive) --}}
        @if(!in_array($glRaw, ['nursery','kindergarten']))
        <div id="gwa-card" style="background:linear-gradient(135deg,#1a3a6c,#2563eb);border-radius:12px;padding:18px 24px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:20px;align-items:center;justify-content:space-between;color:#fff;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-bar-chart-fill" style="font-size:22px;"></i>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:0.8;margin-bottom:2px;">General Weighted Average</div>
                    <div id="gwa-value" style="font-size:32px;font-weight:800;line-height:1;">—</div>
                    <div id="gwa-remark" style="font-size:12px;opacity:0.8;margin-top:2px;"></div>
                </div>
            </div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;">
                <div style="text-align:center;background:rgba(255,255,255,0.12);border-radius:10px;padding:10px 16px;min-width:80px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:0.7;margin-bottom:4px;">Term 1</div>
                    <div id="gwa-t1" style="font-size:20px;font-weight:700;">—</div>
                </div>
                <div style="text-align:center;background:rgba(255,255,255,0.12);border-radius:10px;padding:10px 16px;min-width:80px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:0.7;margin-bottom:4px;">Term 2</div>
                    <div id="gwa-t2" style="font-size:20px;font-weight:700;">—</div>
                </div>
                <div style="text-align:center;background:rgba(255,255,255,0.12);border-radius:10px;padding:10px 16px;min-width:80px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:0.7;margin-bottom:4px;">Term 3</div>
                    <div id="gwa-t3" style="font-size:20px;font-weight:700;">—</div>
                </div>
            </div>
        </div>
        @endif

        <div class="content-card">
            <div class="content-card-header">
                <div>
                    <h6 id="gradesTitle" style="margin:0;">
                        1st Term Grades — {{ $glLabel }}
                    </h6>
                    <div style="font-size:11px;color:var(--muted);margin-top:3px;" id="gradesSYLabel">
                        S.Y. {{ $currentSY ?: '—' }}
                    </div>
                </div>
                <a href="#" onclick="window.print();return false;" style="font-size:13px;color:var(--blue);text-decoration:none;">
                    <i class="bi bi-printer-fill me-1"></i> Print
                </a>
            </div>

            {{-- Legend --}}
            <div style="display:flex;gap:12px;flex-wrap:wrap;padding:10px 16px;border-bottom:1px solid #f0f0f0;font-size:11px;color:#666;">
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:#e8f8f0;border:1px solid #27ae60;border-radius:2px;display:inline-block;"></span> Passed (≥75)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:#fff8ec;border:1px solid #f5a623;border-radius:2px;display:inline-block;"></span> Passed w/ Remedial (70–74)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:#fdecea;border:1px solid #e74c3c;border-radius:2px;display:inline-block;"></span> Failed (&lt;70)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;background:#f5f5f5;border:1px solid #bbb;border-radius:2px;display:inline-block;"></span> Not yet encoded</span>
            </div>

            {{-- Failing-subject notice — informational, points the student to admin rather than self-enrolling --}}
            <div id="gradesFailingNotice" style="display:none;margin:14px 16px 0;padding:12px 16px;background:#fff3e0;border:1px solid #ffcc80;border-radius:10px;font-size:12.5px;color:#7a4a00;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:16px;color:#e65100;margin-top:1px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:700;margin-bottom:2px;">Your grade is below passing in <span id="gradesFailingList"></span> this school year.</div>
                        <div>Please talk to your adviser or the registrar about summer class options.</div>
                    </div>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="dash-table" id="gradesTable">
                    <thead>
                        <tr>
                            <th style="width:80px;">Code</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th style="text-align:center;width:110px;">Final Grade</th>
                            <th style="text-align:center;width:160px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody">
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;">
                                <div style="color:var(--muted);">
                                    <i class="bi bi-journal-text" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                    Loading grades...
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════
         SECTION: SCHEDULE
    ══════════════════════════════ --}}
    <div id="section-schedule" class="student-info-section" style="display:none;">
        <div class="info-section-title">My Class Schedule</div>

        @if($section)
            @php
                $dayOrder  = ['Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7];
                $sortedSchedules = $schedules->sortBy([
                    fn($a,$b) => ($dayOrder[$a->day_of_week] ?? 9) <=> ($dayOrder[$b->day_of_week] ?? 9),
                    fn($a,$b) => $a->start_time <=> $b->start_time,
                ]);
                $dayColors = [
                    'Monday'    => ['bg'=>'#e3f2fd','border'=>'#1565c0','text'=>'#1565c0'],
                    'Tuesday'   => ['bg'=>'#f3e5f5','border'=>'#7b1fa2','text'=>'#7b1fa2'],
                    'Wednesday' => ['bg'=>'#e8f5e9','border'=>'#2e7d32','text'=>'#2e7d32'],
                    'Thursday'  => ['bg'=>'#fff8e1','border'=>'#f57f17','text'=>'#f57f17'],
                    'Friday'    => ['bg'=>'#fce4ec','border'=>'#c62828','text'=>'#c62828'],
                    'Saturday'  => ['bg'=>'#e0f7fa','border'=>'#00838f','text'=>'#00838f'],
                ];
                $studentName = auth()->user()->name;
                $gradeLabel  = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'][$section->grade_level] ?? ucfirst($section->grade_level);
                $schedSY     = $enrollment->school_year ?? '';
            @endphp

            {{-- Filter Bar: School Year + Term (same design as Grades) --}}
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;">

                {{-- School Year --}}
                <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">School Year</label>
                    <select id="schedSYFilter" onchange="loadSchedule()"
                        style="border:1.5px solid #d1d5db;border-radius:8px;padding:7px 12px;font-size:13px;font-weight:600;color:var(--text);background:#fff;cursor:pointer;min-width:130px;">
                        @foreach($allSYOptions as $syOpt)
                            <option value="{{ $syOpt }}" {{ $syOpt === $schedSY ? 'selected' : '' }}>{{ $syOpt }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:36px;background:#e5e7eb;"></div>

                {{-- Term Tabs --}}
                <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;">Term</label>
                    <div style="display:flex;gap:6px;">
                        <button class="sched-term-tab active" onclick="switchSchedTerm(1,this);return false;"
                            style="border:1.5px solid var(--blue);background:var(--blue);color:#fff;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                            Term 1
                        </button>
                        <button class="sched-term-tab" onclick="switchSchedTerm(2,this);return false;"
                            style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                            Term 2
                        </button>
                        <button class="sched-term-tab" onclick="switchSchedTerm(3,this);return false;"
                            style="border:1.5px solid #d1d5db;background:#fff;color:#555;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                            Term 3
                        </button>
                    </div>
                </div>

                {{-- Info pill --}}
                <div style="margin-left:auto;background:#f0f4ff;border:1px solid #c7d7ff;border-radius:20px;padding:5px 14px;font-size:11px;font-weight:600;color:var(--blue);">
                    <i class="bi bi-calendar3 me-1"></i>
                    <span id="schedTermLabel">Term 1</span> &nbsp;·&nbsp;
                    <span id="schedSYLabel">{{ $schedSY }}</span>
                </div>
            </div>

            {{-- Day Filter Bar --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-bottom:16px;">
                <span style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-right:4px;">Day:</span>
                @foreach(['All'=>'All','Monday'=>'Mon','Tuesday'=>'Tue','Wednesday'=>'Wed','Thursday'=>'Thu','Friday'=>'Fri'] as $dayVal=>$dayLabel)
                <button class="sched-day-filter"
                        data-day="{{ $dayVal }}"
                        onclick="filterScheduleByDay('{{ $dayVal }}', this)"
                        style="border:1.5px solid {{ $dayVal === 'All' ? 'var(--blue)' : '#d1d5db' }};background:{{ $dayVal === 'All' ? 'var(--blue)' : '#fff' }};color:{{ $dayVal === 'All' ? '#fff' : '#555' }};border-radius:20px;padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.15s;">
                    {{ $dayLabel }}
                </button>
                @endforeach
            </div>

            {{-- Header card --}}
            <div class="content-card mb-3">
                <div class="content-card-header">
                    <div>
                        <h6 style="margin:0;" id="schedMainTitle">{{ $gradeLabel }} — {{ $section->name }}</h6>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;" id="schedSubLabel">
                            S.Y. {{ $schedSY ?: '—' }}
                            @if($schedules->count() > 0)
                                &nbsp;·&nbsp; {{ $schedules->count() }} subject(s)
                            @else
                                &nbsp;·&nbsp; <span style="color:#f5a623;">Awaiting schedule</span>
                            @endif
                        </div>
                    </div>
                    @if($schedules->count() > 0)
                        <button onclick="downloadSchedulePDF()" style="display:inline-flex;align-items:center;gap:7px;background:var(--blue);color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                        </button>
                    @else
                        <button disabled style="display:inline-flex;align-items:center;gap:7px;background:#e5e7eb;color:#9ca3af;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:not-allowed;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                        </button>
                    @endif
                </div>
            </div>

            {{-- Schedule Table --}}
            <div class="content-card" id="scheduleTableCard">
                <div style="overflow-x:auto;">
                    <table class="dash-table" id="scheduleTable">
                        <thead>
                            <tr>
                                <th style="width:110px;">Day</th>
                                <th style="width:150px;">Time</th>
                                <th>Subject</th>
                                <th style="width:110px;">Room</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            <tr>
                                <td colspan="5" style="text-align:center;padding:40px;">
                                    <div style="color:var(--muted);">
                                        <i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                                        Loading schedule...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Hidden data for PDF --}}
            <script>
            var _scheduleData = {
                student:    '{{ addslashes($studentName) }}',
                grade:      '{{ addslashes($gradeLabel) }}',
                section:    '{{ addslashes($section->name) }}',
                schoolYear: '{{ $enrollment->school_year ?? "" }}',
                logoUrl:    '{{ asset("images/logo.png") }}',
                rows: [
                    @foreach($sortedSchedules as $sched)
                    {
                        day:     '{{ $sched->day_of_week }}',
                        start:   '{{ \Carbon\Carbon::parse($sched->start_time)->format("g:i A") }}',
                        end:     '{{ \Carbon\Carbon::parse($sched->end_time)->format("g:i A") }}',
                        subject: '{{ addslashes($sched->subject->name ?? "—") }}',
                        code:    '{{ addslashes($sched->subject->code ?? "") }}',
                        room:    '{{ addslashes($sched->room ?? "—") }}',
                        teacher: '{{ addslashes($sched->teacher->name ?? "—") }}',
                    },
                    @endforeach
                ]
            };

            // ── Schedule: load via API ──
            var _currentSchedTerm  = 1;
            var _currentDayFilter  = 'All';
            var _loadedScheduleData = null;

            function switchSchedTerm(term, btn) {
                _currentSchedTerm = term;
                loadSchedule(btn);
            }

            function loadSchedule(activeBtn) {
                // Reset day filter on every new load
                _currentDayFilter = 'All';
                document.querySelectorAll('.sched-day-filter').forEach(function(b) {
                    var isAll = b.dataset.day === 'All';
                    b.style.background  = isAll ? 'var(--blue)' : '#fff';
                    b.style.color       = isAll ? '#fff' : '#555';
                    b.style.borderColor = isAll ? 'var(--blue)' : '#d1d5db';
                    b.classList.toggle('active', isAll);
                });
                // Update term button styles
                document.querySelectorAll('.sched-term-tab').forEach(function(b) {
                    var isActive = activeBtn ? b === activeBtn : b.classList.contains('active');
                    b.style.background  = isActive ? 'var(--blue)' : '#fff';
                    b.style.color       = isActive ? '#fff' : '#555';
                    b.style.borderColor = isActive ? 'var(--blue)' : '#d1d5db';
                    b.classList.toggle('active', isActive);
                });
                if (activeBtn) activeBtn.classList.add('active');

                var sy       = (document.getElementById('schedSYFilter') || {}).value || '';
                var termLabel = 'Term ' + _currentSchedTerm;

                // Update pill labels
                var termEl = document.getElementById('schedTermLabel');
                var syEl   = document.getElementById('schedSYLabel');
                if (termEl) termEl.textContent = termLabel;
                if (syEl)   syEl.textContent   = sy;

                // Show loading
                var tbody = document.getElementById('scheduleTableBody');
                if (tbody) tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;"><div style="color:var(--muted);"><i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading schedule...</div></td></tr>';

                var url = '/api/student/schedule?term=' + _currentSchedTerm;
                if (sy) url += '&school_year=' + encodeURIComponent(sy);

                fetch(url, {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function(res) {
                    var d = res.data || {};
                    _loadedScheduleData = d;

                    // Update header card labels
                    var titleEl = document.getElementById('schedMainTitle');
                    var subEl   = document.getElementById('schedSubLabel');
                    if (titleEl) titleEl.textContent = (d.grade_label || '') + ' — ' + (d.section || '');
                    if (subEl)   subEl.innerHTML = 'S.Y. ' + (d.school_year || sy || '—')
                        + ' &nbsp;·&nbsp; ' + termLabel
                        + ' &nbsp;·&nbsp; ' + (d.total_subjects || 0) + ' subject(s)';

                    if (!tbody) return;

                    var schedules = d.schedule || [];
                    if (!schedules.length) {
                        var tLabel = 'Term ' + _currentSchedTerm;
                        var sLabel = d.school_year || sy || '';
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:48px 20px;">'
                            + '<i class="bi bi-calendar-x" style="font-size:40px;display:block;margin-bottom:12px;color:#c9d3e0;"></i>'
                            + '<div style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:6px;">No schedule for ' + tLabel + '</div>'
                            + '<div style="font-size:12px;color:#94a3b8;">No class schedule has been set up'
                            + (sLabel ? ' for S.Y. ' + sLabel : '') + ', ' + tLabel + ' yet.</div>'
                            + '</td></tr>';
                        return;
                    }

                    var dayColors = {
                        Monday:    {bg:'#e3f2fd',border:'#1565c0',text:'#1565c0'},
                        Tuesday:   {bg:'#f3e5f5',border:'#7b1fa2',text:'#7b1fa2'},
                        Wednesday: {bg:'#e8f5e9',border:'#2e7d32',text:'#2e7d32'},
                        Thursday:  {bg:'#fff8e1',border:'#f57f17',text:'#f57f17'},
                        Friday:    {bg:'#fce4ec',border:'#c62828',text:'#c62828'},
                    };

                    var html = '';
                    schedules.forEach(function(dayGroup) {
                        var day = dayGroup.day;
                        var dc  = dayColors[day] || {bg:'#f5f5f5',border:'#999',text:'#555'};
                        (dayGroup.classes || []).forEach(function(cls, i) {
                            html += '<tr style="' + (i===0 ? 'border-top:2px solid #e5e7eb;' : '') + '">';
                            // Day badge — only on first row of day
                            if (i === 0) {
                                html += '<td><span style="display:inline-block;background:' + dc.bg + ';color:' + dc.text + ';border:1px solid ' + dc.border + ';border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;white-space:nowrap;">' + day + '</span></td>';
                            } else {
                                html += '<td></td>';
                            }
                            html += '<td style="font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap;">' + fmtTime(cls.start_time) + ' – ' + fmtTime(cls.end_time) + '</td>';
                            html += '<td><div style="font-weight:600;">' + cls.subject_name + '</div>' + (cls.subject_code ? '<div style="font-size:11px;color:var(--muted);">' + cls.subject_code + '</div>' : '') + '</td>';
                            html += '<td style="font-size:13px;">' + (cls.room ? '<i class="bi bi-door-open-fill" style="color:var(--blue);font-size:11px;"></i> ' + cls.room : '<span style="color:#ccc;">—</span>') + '</td>';
                            html += '<td style="font-size:13px;"><i class="bi bi-person-fill" style="color:var(--blue);font-size:11px;"></i> ' + cls.teacher + '</td>';
                            html += '</tr>';
                        });
                    });
                    tbody.innerHTML = html;
                })
                .catch(function(err) {
                    console.error('Schedule load error:', err);
                    if (tbody) tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;"><div style="color:var(--muted);"><i class="bi bi-exclamation-triangle" style="font-size:24px;display:block;margin-bottom:8px;"></i>Failed to load schedule. Please try again.</div></td></tr>';
                });
            }

            function fmtTime(t) {
                if (!t) return '—';
                var p = String(t).split(':');
                var h = parseInt(p[0]), m = p[1] || '00';
                var ap = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                return h + ':' + m + ' ' + ap;
            }

            function filterScheduleByDay(day, btn) {
                _currentDayFilter = day;
                document.querySelectorAll('.sched-day-filter').forEach(function(b) {
                    var isActive = b === btn;
                    b.style.background  = isActive ? 'var(--blue)' : '#fff';
                    b.style.color       = isActive ? '#fff' : '#555';
                    b.style.borderColor = isActive ? 'var(--blue)' : '#d1d5db';
                    b.classList.toggle('active', isActive);
                });
                applyDayFilter();
            }

            function applyDayFilter() {
                if (!_loadedScheduleData) return;
                var tbody = document.getElementById('scheduleTableBody');
                if (!tbody) return;
                var dayColors = {
                    Monday:    {bg:'#e3f2fd',border:'#1565c0',text:'#1565c0'},
                    Tuesday:   {bg:'#f3e5f5',border:'#7b1fa2',text:'#7b1fa2'},
                    Wednesday: {bg:'#e8f5e9',border:'#2e7d32',text:'#2e7d32'},
                    Thursday:  {bg:'#fff8e1',border:'#f57f17',text:'#f57f17'},
                    Friday:    {bg:'#fce4ec',border:'#c62828',text:'#c62828'},
                };
                var allGroups = _loadedScheduleData.schedule || [];
                var filtered  = _currentDayFilter === 'All'
                    ? allGroups
                    : allGroups.filter(function(dg) { return dg.day === _currentDayFilter; });
                if (!filtered.length) {
                    var msg = _currentDayFilter === 'All' ? 'Schedule not yet assigned' : 'No classes on ' + _currentDayFilter;
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px 20px;">'
                        + '<i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:10px;color:#cbd5e1;"></i>'
                        + '<div style="font-weight:600;color:#64748b;margin-bottom:4px;">' + msg + '</div>'
                        + '</td></tr>';
                    return;
                }
                var html = '';
                filtered.forEach(function(dayGroup) {
                    var day = dayGroup.day;
                    var dc  = dayColors[day] || {bg:'#f5f5f5',border:'#999',text:'#555'};
                    (dayGroup.classes || []).forEach(function(cls, i) {
                        html += '<tr style="' + (i===0 ? 'border-top:2px solid #e5e7eb;' : '') + '">';
                        if (i === 0) {
                            html += '<td><span style="display:inline-block;background:' + dc.bg + ';color:' + dc.text + ';border:1px solid ' + dc.border + ';border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;white-space:nowrap;">' + day + '</span></td>';
                        } else {
                            html += '<td></td>';
                        }
                        html += '<td style="font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap;">' + fmtTime(cls.start_time) + ' – ' + fmtTime(cls.end_time) + '</td>';
                        html += '<td><div style="font-weight:600;">' + cls.subject_name + '</div>' + (cls.subject_code ? '<div style="font-size:11px;color:var(--muted);">' + cls.subject_code + '</div>' : '') + '</td>';
                        html += '<td style="font-size:13px;">' + (cls.room ? '<i class="bi bi-door-open-fill" style="color:var(--blue);font-size:11px;"></i> ' + cls.room : '<span style="color:#ccc;">—</span>') + '</td>';
                        html += '<td style="font-size:13px;"><i class="bi bi-person-fill" style="color:var(--blue);font-size:11px;"></i> ' + cls.teacher + '</td>';
                        html += '</tr>';
                    });
                });
                tbody.innerHTML = html;
            }

            function downloadSchedulePDF() {
                var logoUrl     = '{{ asset("images/logo.png") }}';
                var studentName = '{{ addslashes($studentName) }}';

                // Use AJAX-loaded data when available, fall back to Blade-rendered data
                var src = null;
                if (_loadedScheduleData && (_loadedScheduleData.schedule || []).length) {
                    src = _loadedScheduleData;
                } else if (_scheduleData && _scheduleData.rows && _scheduleData.rows.length) {
                    // Build compatible structure from Blade data
                    var byDay = {};
                    _scheduleData.rows.forEach(function(r) {
                        if (!byDay[r.day]) byDay[r.day] = [];
                        byDay[r.day].push({ start_time: r.start, end_time: r.end, subject_name: r.subject, subject_code: r.code, room: r.room, teacher: r.teacher });
                    });
                    src = {
                        grade_label: _scheduleData.grade,
                        section: _scheduleData.section,
                        school_year: _scheduleData.schoolYear,
                        schedule: Object.keys(byDay).map(function(d) { return { day: d, classes: byDay[d] }; })
                    };
                }
                if (!src) { alert('No schedule data to export.'); return; }

                // Flatten all classes
                var dayOrder  = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
                var allCls    = [];
                src.schedule.forEach(function(dg) {
                    (dg.classes || []).forEach(function(cls) {
                        allCls.push({ day: dg.day, start: cls.start_time, end: cls.end_time,
                            subject: cls.subject_name, code: cls.subject_code,
                            room: cls.room || '—', teacher: cls.teacher || '—' });
                    });
                });

                // Unique time slots sorted by start
                var seenSlots = {}, timeSlots = [];
                allCls.forEach(function(c) {
                    var k = c.start + '|' + c.end;
                    if (!seenSlots[k]) { seenSlots[k] = true; timeSlots.push({ start: c.start, end: c.end }); }
                });
                timeSlots.sort(function(a, b) { return a.start.localeCompare(b.start); });

                // Days present (in calendar order)
                var daysPresent = dayOrder.filter(function(day) {
                    return src.schedule.some(function(dg) { return dg.day === day && (dg.classes || []).length; });
                });

                // Grid lookup: timeKey → day → class
                var grid = {};
                allCls.forEach(function(c) {
                    var k = c.start + '|' + c.end;
                    if (!grid[k]) grid[k] = {};
                    grid[k][c.day] = c;
                });

                // Day header colors
                var dayBg = { Monday:'#1565c0', Tuesday:'#7b1fa2', Wednesday:'#2e7d32', Thursday:'#f57f17', Friday:'#c62828' };

                // Table header
                var colW   = Math.floor(72 / Math.max(daysPresent.length, 1));
                var hdrCols = '<th style="width:10%;padding:9px 10px;text-align:left;background:#1a3a6c;color:#fff;font-size:11px;border:1px solid #375a8c;">Time</th>';
                daysPresent.forEach(function(day) {
                    var bg = dayBg[day] || '#1a3a6c';
                    hdrCols += '<th style="width:' + colW + '%;padding:9px 10px;text-align:center;background:' + bg + ';color:#fff;font-size:11px;border:1px solid rgba(255,255,255,0.2);">' + day + '</th>';
                });

                // Table rows
                var rowsHtml = '';
                timeSlots.forEach(function(slot, idx) {
                    var k  = slot.start + '|' + slot.end;
                    var bg = idx % 2 === 0 ? '#ffffff' : '#f8fafc';
                    rowsHtml += '<tr style="background:' + bg + ';">';
                    rowsHtml += '<td style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;white-space:nowrap;border:1px solid #e5e7eb;border-right:2px solid #cbd5e1;text-align:center;">'
                        + fmtTime(slot.start) + '<br><span style="color:#9ca3af;font-weight:400;">–</span><br>' + fmtTime(slot.end) + '</td>';
                    daysPresent.forEach(function(day) {
                        var cls = (grid[k] || {})[day];
                        if (cls) {
                            var hdr = dayBg[day] || '#1a3a6c';
                            rowsHtml += '<td style="padding:7px 8px;text-align:center;border:1px solid #e5e7eb;vertical-align:middle;">'
                                + '<div style="font-weight:700;font-size:11px;color:#1a3a6c;">' + cls.subject + '</div>'
                                + (cls.code ? '<div style="font-size:9px;color:#6b7280;margin-top:1px;">' + cls.code + '</div>' : '')
                                + '<div style="font-size:9px;color:#6b7280;margin-top:3px;">📍 ' + cls.room + '</div>'
                                + '<div style="font-size:9px;color:#374151;margin-top:1px;">👤 ' + cls.teacher + '</div>'
                                + '</td>';
                        } else {
                            rowsHtml += '<td style="padding:8px;text-align:center;border:1px solid #e5e7eb;background:#fafafa;color:#d1d5db;font-size:11px;">—</td>';
                        }
                    });
                    rowsHtml += '</tr>';
                });

                var term = 'Term ' + _currentSchedTerm;
                var html = '<!DOCTYPE html><html><head>'
                    + '<meta charset="UTF-8">'
                    + '<title>Class Schedule – ' + studentName + '</title>'
                    + '<style>'
                    + 'body{font-family:Arial,sans-serif;margin:0;padding:24px;color:#222;font-size:13px;}'
                    + '.header{text-align:center;border-bottom:3px solid #1a3a6c;padding-bottom:14px;margin-bottom:16px;}'
                    + '.header img{width:68px;height:68px;object-fit:contain;display:block;margin:0 auto 8px;}'
                    + '.school-name{font-size:20px;font-weight:700;color:#1a3a6c;}'
                    + '.school-addr{font-size:11px;color:#555;margin-top:2px;}'
                    + '.doc-title{font-size:12px;font-weight:700;color:#2471a3;margin-top:5px;text-transform:uppercase;letter-spacing:2px;}'
                    + '.divider{width:50px;height:3px;background:#1a3a6c;margin:6px auto 0;border-radius:2px;}'
                    + '.meta{display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;justify-content:center;}'
                    + '.meta-item{background:#f0f4f8;border-radius:6px;padding:5px 12px;text-align:center;}'
                    + '.meta-label{color:#888;font-size:10px;text-transform:uppercase;letter-spacing:0.4px;}'
                    + '.meta-value{font-weight:700;color:#1a3a6c;font-size:12px;}'
                    + 'table{width:100%;border-collapse:collapse;table-layout:fixed;}'
                    + '.footer{margin-top:18px;border-top:1px solid #ddd;padding-top:8px;font-size:10px;color:#aaa;display:flex;justify-content:space-between;}'
                    + '@media print{body{margin:0;padding:16px;}}'
                    + '</style></head><body>'
                    + '<div class="header">'
                    + '<img src="' + logoUrl + '" alt="ILC Logo" onerror="this.style.display=\'none\'">'
                    + '<div class="school-name">IEMELIF LEARNING CENTER</div>'
                    + '<div class="school-addr">General Tinio, Nueva Ecija</div>'
                    + '<div class="doc-title">Class Schedule</div>'
                    + '<div class="divider"></div>'
                    + '</div>'
                    + '<div class="meta">'
                    + '<div class="meta-item"><div class="meta-label">Student</div><div class="meta-value">' + studentName + '</div></div>'
                    + '<div class="meta-item"><div class="meta-label">Grade Level</div><div class="meta-value">' + (src.grade_label || '') + '</div></div>'
                    + '<div class="meta-item"><div class="meta-label">Section</div><div class="meta-value">' + (src.section || '') + '</div></div>'
                    + '<div class="meta-item"><div class="meta-label">School Year</div><div class="meta-value">S.Y. ' + (src.school_year || '') + '</div></div>'
                    + '<div class="meta-item"><div class="meta-label">Term</div><div class="meta-value">' + term + '</div></div>'
                    + '</div>'
                    + '<table><thead><tr>' + hdrCols + '</tr></thead><tbody>' + rowsHtml + '</tbody></table>'
                    + '<div class="footer">'
                    + '<span>IEMELIF Learning Center — Official Class Schedule</span>'
                    + '<span>Printed: ' + new Date().toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'}) + '</span>'
                    + '</div>'
                    + '</body></html>';

                var win = window.open('', '_blank', 'width=960,height=720');
                win.document.write(html);
                win.document.close();
                win.onload = function() { win.focus(); win.print(); };
            }
            </script>

        @elseif($section)
            <div class="content-card" style="padding:40px; text-align:center;">
                <i class="bi bi-calendar-x" style="font-size:48px; color:var(--muted); opacity:0.3; display:block; margin-bottom:12px;"></i>
                <div style="color:var(--muted);">No schedules assigned to your section yet.</div>
            </div>
        @else
            <div class="content-card" style="padding:40px; text-align:center;">
                <i class="bi bi-person-exclamation" style="font-size:48px; color:var(--muted); opacity:0.3; display:block; margin-bottom:16px;"></i>
                <div style="color:var(--muted);">You are not assigned to a section yet. Please contact the registrar.</div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════
         SECTION: ENROLLMENT & PAYMENT
    ══════════════════════════════ --}}
    <div id="section-enrollment" class="student-info-section" style="display:none;">
        <div class="info-section-title">My Enrollment</div>

        {{-- ── Document upload flash messages ── --}}
        @if(session('doc_success'))
            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-check-circle-fill" style="font-size:18px;flex-shrink:0;"></i>
                <div><strong>Upload successful!</strong> {{ session('doc_success') }}</div>
            </div>
        @endif
        @if(session('doc_error'))
            <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-exclamation-circle-fill" style="font-size:18px;flex-shrink:0;"></i>
                <div><strong>Upload failed.</strong> {{ session('doc_error') }}</div>
            </div>
        @endif

        {{-- ── Enrollment Summary Banner ── --}}
        @if($enrollment)
        @php
            $envGradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6','graduated'=>'Graduated'];
            $envGrade    = $envGradeMap[$enrollment->student_data['grade_level'] ?? $enrollment->grade_level ?? ''] ?? ucfirst($enrollment->grade_level ?? '—');
            $envType     = ucfirst($enrollment->student_data['student_type'] ?? '—');
            $envStatus   = $enrollment->status ?? 'pending';
            $envStatusColors = ['enrolled'=>['#166534','#dcfce7','#86efac'],'approved'=>['#166534','#dcfce7','#86efac'],'pending'=>['#92400e','#fef3c7','#fcd34d'],'declined'=>['#991b1b','#fee2e2','#fca5a5']];
            [$envSC, $envSBg, $envSBorder] = $envStatusColors[$envStatus] ?? ['#374151','#f3f4f6','#d1d5db'];
            $envDate = $enrollment->created_at ? $enrollment->created_at->format('F d, Y') : '—';
        @endphp
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:0;margin-bottom:20px;overflow:hidden;">
            {{-- Header bar --}}
            <div style="background:linear-gradient(135deg,#1a3a6c,#2471a3);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-mortarboard-fill" style="color:#fff;font-size:17px;"></i>
                    </div>
                    <div>
                        <div style="font-size:15px;font-weight:800;color:#fff;line-height:1.1;">S.Y. {{ $enrollment->school_year ?? '—' }}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.7);margin-top:1px;">{{ $envGrade }}</div>
                    </div>
                </div>
                <span style="background:{{ $envSBg }};color:{{ $envSC }};border:1.5px solid {{ $envSBorder }};border-radius:20px;padding:4px 14px;font-size:11px;font-weight:700;">
                    {{ ucfirst($envStatus) }}
                </span>
            </div>
            {{-- Info row --}}
            <div style="display:flex;flex-wrap:wrap;gap:0;border-top:none;">
                @foreach([
                    ['bi-hash',           'Reference',    $enrollment->reference_number ?? '—'],
                    ['bi-person-badge',   'Student Type', $envType],
                    ['bi-calendar3',      'Date Enrolled',$envDate],
                    ['bi-people-fill',    'Section',      $enrollment->section ?? 'Not yet assigned'],
                ] as [$icon, $label, $value])
                <div style="flex:1;min-width:160px;padding:12px 16px;border-right:1px solid #f0f4f8;display:flex;align-items:center;gap:10px;">
                    <i class="bi {{ $icon }}" style="font-size:16px;color:#2471a3;flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</div>
                        <div style="font-size:13px;font-weight:600;color:#1e293b;margin-top:1px;">{{ $value }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Required Documents Upload Card ── --}}
        @php
            $enrollDocTypes = [
                'birth_certificate'  => [
                    'label'   => 'Birth Certificate (PSA)',
                    'icon'    => 'bi-file-earmark-person-fill',
                    'color'   => '#1565c0',
                    'bg'      => '#e3f0ff',
                    'hint'    => 'Original or certified true copy of PSA birth certificate.',
                ],
                'form_137'           => [
                    'label'   => 'Form 137',
                    'icon'    => 'bi-file-earmark-text-fill',
                    'color'   => '#6a1b9a',
                    'bg'      => '#f3e5ff',
                    'hint'    => 'Academic record / transcript from your previous school.',
                ],
                'report_card'        => [
                    'label'   => 'Report Card / Grades',
                    'icon'    => 'bi-file-earmark-bar-graph-fill',
                    'color'   => '#1b5e20',
                    'bg'      => '#e6f4ea',
                    'hint'    => 'Most recent report card or grade sheet from previous school year.',
                ],
                'two_by_two_picture' => [
                    'label'   => '2x2 ID Picture',
                    'icon'    => 'bi-person-bounding-box',
                    'color'   => '#e65100',
                    'bg'      => '#fff3e0',
                    'hint'    => 'Recent 2x2 colored photo with white background, taken within 3 months.',
                ],
            ];
            $enrollAllDocs = \App\Models\StudentDocument::where(function($q) use ($enrollment) {
                $q->where('enrollment_id', $enrollment->id)->orWhere('user_id', Auth::id());
            })->whereIn('document_type', array_keys($enrollDocTypes))->get()->keyBy('document_type');
            $enrollApproved = $enrollAllDocs->filter(fn($d) => $d->status === 'approved')->count();
        @endphp

        <div class="content-card mb-4" id="doc-upload-card">
            {{-- Header --}}
            <div class="content-card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <h6 style="display:flex;align-items:center;gap:8px;margin:0;">
                    <i class="bi bi-folder-fill" style="color:var(--gold);"></i>
                    Required Documents
                </h6>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="display:flex;gap:4px;">
                        @foreach(array_keys($enrollDocTypes) as $dt)
                            @php $dtDoc = $enrollAllDocs->get($dt); $dtSt = $dtDoc ? $dtDoc->status : 'missing'; @endphp
                            <div title="{{ $enrollDocTypes[$dt]['label'] }}"
                                 style="width:9px;height:9px;border-radius:50%;
                                 background:{{ $dtSt === 'approved' ? '#22c55e' : ($dtSt === 'pending' ? '#3b82f6' : '#d1d5db') }};"></div>
                        @endforeach
                    </div>
                    <span style="font-size:12px;font-weight:700;color:{{ $enrollApproved === 4 ? '#166534' : 'var(--muted)' }};">
                        {{ $enrollApproved }}/4 Approved
                    </span>
                </div>
            </div>

            {{-- Notice --}}
            <div style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:10px 18px;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-info-circle-fill" style="color:#d97706;font-size:15px;flex-shrink:0;"></i>
                <div style="font-size:11.5px;color:#92400e;line-height:1.5;">
                    <strong>New enrollees must submit all four documents.</strong>
                    Accepted: <strong>JPG, PNG, PDF</strong> (max 5 MB). Drag &amp; drop or click the box to browse.
                    @if($enrollment->status === 'pending')
                        &nbsp;<span style="color:#dc2626;font-weight:700;">⚠ Enrollment pending — submit docs to speed up approval.</span>
                    @endif
                </div>
            </div>

            {{-- Single bulk upload form --}}
            <form method="POST" action="{{ route('student.documents.upload.all') }}" enctype="multipart/form-data" id="bulk-doc-form">
                @csrf
                <div style="padding:18px;display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
                    @foreach($enrollDocTypes as $docType => $docInfo)
                        @php
                            $doc    = $enrollAllDocs->get($docType);
                            $status = $doc ? $doc->status : 'missing';
                            $canUpload = ($status === 'missing' || $status === 'rejected');
                        @endphp

                        <div id="doc-card-{{ $docType }}" style="border:1.5px solid {{ $status === 'approved' ? '#86efac' : ($status === 'pending' ? '#93c5fd' : ($status === 'rejected' ? '#fca5a5' : '#e2e8f0')) }};
                                    border-radius:12px;overflow:hidden;background:{{ $status === 'approved' ? '#f0fdf4' : '#fff' }};">

                            {{-- Card header --}}
                            <div style="padding:12px 14px 10px;display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:8px;background:{{ $docInfo['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi {{ $docInfo['icon'] }}" style="font-size:17px;color:{{ $docInfo['color'] }};"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:700;font-size:12.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $docInfo['label'] }}
                                    </div>
                                    <div style="font-size:10px;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $docInfo['hint'] }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status area --}}
                            @if($status === 'approved')
                                <div style="margin:0 12px 12px;background:#dcfce7;border-radius:8px;padding:12px;text-align:center;">
                                    <i class="bi bi-check-circle-fill" style="font-size:22px;color:#16a34a;display:block;margin-bottom:4px;"></i>
                                    <div style="font-size:11px;font-weight:700;color:#166534;">Approved</div>
                                    @if($doc->original_name ?? false)
                                        <div style="font-size:10px;color:#4ade80;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc->original_name }}</div>
                                    @endif
                                    <button type="button"
                                        onclick="openDocViewer('{{ asset('storage/' . $doc->file_path) }}','{{ addslashes($doc->original_name ?? $docInfo['label']) }}','{{ $docInfo['label'] }}')"
                                        style="margin-top:8px;display:inline-flex;align-items:center;gap:5px;background:#fff;color:#16a34a;border:1.5px solid #86efac;border-radius:20px;padding:4px 14px;font-size:11px;font-weight:700;cursor:pointer;">
                                        <i class="bi bi-eye-fill"></i> View
                                    </button>
                                </div>

                            @elseif($status === 'pending')
                                <div style="margin:0 12px 12px;background:#dbeafe;border:1.5px dashed #93c5fd;border-radius:8px;padding:12px;text-align:center;">
                                    <i class="bi bi-clock-fill" style="font-size:20px;color:#3b82f6;display:block;margin-bottom:4px;"></i>
                                    <div style="font-size:11px;font-weight:700;color:#1e40af;">Under Review</div>
                                    @if($doc->original_name ?? false)
                                        <div style="font-size:10px;color:#60a5fa;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc->original_name }}</div>
                                    @endif
                                    <div style="font-size:10px;color:#3b82f6;margin-top:4px;">Usually 1–3 business days</div>
                                    <button type="button"
                                        onclick="openDocViewer('{{ asset('storage/' . $doc->file_path) }}','{{ addslashes($doc->original_name ?? $docInfo['label']) }}','{{ $docInfo['label'] }}')"
                                        style="margin-top:8px;display:inline-flex;align-items:center;gap:5px;background:#fff;color:#1d4ed8;border:1.5px solid #93c5fd;border-radius:20px;padding:4px 14px;font-size:11px;font-weight:700;cursor:pointer;">
                                        <i class="bi bi-eye-fill"></i> View
                                    </button>
                                </div>

                            @else
                                {{-- Upload dropzone --}}
                                @if($status === 'rejected')
                                    <div style="margin:0 12px 6px;background:#fef2f2;border-radius:6px;padding:7px 10px;font-size:11px;color:#dc2626;display:flex;align-items:flex-start;gap:6px;">
                                        <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;margin-top:1px;"></i>
                                        <span>Rejected{{ ($doc->reject_reason ?? false) ? ': '.$doc->reject_reason : '. Please re-upload.' }}</span>
                                    </div>
                                @endif
                                <div onclick="document.getElementById('bulk-file-{{ $docType }}').click()"
                                     ondragover="event.preventDefault();this.style.borderColor='{{ $docInfo['color'] }}';this.style.background='{{ $docInfo['bg'] }}';"
                                     ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc';"
                                     ondrop="handleBulkDrop(event,'{{ $docType }}','{{ $docInfo['color'] }}','{{ $docInfo['bg'] }}')"
                                     style="margin:0 12px 12px;border:2px dashed #cbd5e1;border-radius:8px;padding:14px 10px;
                                            text-align:center;cursor:pointer;background:#f8fafc;transition:all 0.2s;">
                                    <div id="bulk-idle-{{ $docType }}">
                                        <i class="bi bi-cloud-arrow-up" style="font-size:22px;color:#94a3b8;display:block;margin-bottom:4px;"></i>
                                        <div style="font-size:11px;font-weight:600;color:#64748b;">Drop file here</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">or <span style="color:{{ $docInfo['color'] }};font-weight:700;">click to browse</span></div>
                                        <div style="font-size:9.5px;color:#cbd5e1;margin-top:4px;">PDF, JPG, PNG · Max 5 MB</div>
                                    </div>
                                    <div id="bulk-selected-{{ $docType }}" style="display:none;">
                                        <i class="bi bi-file-earmark-check-fill" style="font-size:22px;color:{{ $docInfo['color'] }};display:block;margin-bottom:4px;"></i>
                                        <div id="bulk-name-{{ $docType }}" style="font-size:11px;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"></div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Click to change</div>
                                    </div>
                                    <input type="file" id="bulk-file-{{ $docType }}" name="files[{{ $docType }}]"
                                           accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                                           onchange="selectBulkFile(this,'{{ $docType }}','{{ $docInfo['color'] }}','{{ $docInfo['bg'] }}')">
                                </div>
                            @endif

                        </div>{{-- /doc card --}}
                    @endforeach
                </div>{{-- /grid --}}

                {{-- Submit all button --}}
                @php $hasMissingOrRejected = collect($enrollDocTypes)->keys()->contains(fn($dt) => in_array($enrollAllDocs->get($dt)?->status ?? 'missing', ['missing','rejected'])); @endphp
                @if($hasMissingOrRejected)
                <div style="padding:0 18px 18px;">
                    <button type="submit" id="bulk-submit-btn" disabled
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px;
                               background:#94a3b8;color:#fff;border:none;border-radius:10px;
                               padding:13px 20px;font-size:14px;font-weight:700;cursor:not-allowed;transition:all .2s;">
                        <i class="bi bi-upload"></i>
                        <span id="bulk-submit-label">Select documents to submit</span>
                    </button>
                    <div style="text-align:center;font-size:11px;color:var(--muted);margin-top:8px;">
                        Already-approved and under-review documents are preserved automatically.
                    </div>
                </div>
                @endif
            </form>

            @if($enrollApproved === 4)
                <div style="margin:0 18px 18px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:12px;">
                    <i class="bi bi-patch-check-fill" style="font-size:22px;color:#22c55e;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:700;font-size:13px;color:#166534;">All documents approved!</div>
                        <div style="font-size:11px;color:#4ade80;margin-top:1px;">Your document requirements are fully complete.</div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        {{-- ── RE-ENROLLMENT CHECKLIST ── --}}
        @if(isset($needsReenrollment) && $needsReenrollment)
        @php
            $reenrollGlMap  = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6','graduated'=>'Graduated'];
            $suggestedLabel = $reenrollGlMap[$suggestedGrade ?? ''] ?? ucfirst(str_replace('_',' ',$suggestedGrade ?? ''));
            $currentGradeForCheck = $enrollment->student_data['grade_level'] ?? $enrollment->grade_level ?? '';
            $studentTypeForCheck  = $enrollment->student_data['student_type'] ?? 'returning';
            $isAutoAdvance  = in_array($currentGradeForCheck, ['nursery','kindergarten']) || $studentTypeForCheck === 'transferee';
            $isAssessed     = $promotionRecord !== null;
            $promoResult    = null;
            if ($isAssessed) {
                $promoResult = $promotionRecord->to_grade === 'graduated' ? 'Graduated'
                    : ($promotionRecord->from_grade === $promotionRecord->to_grade ? 'Retained' : 'Promoted');
            }
            $reenrollBalance = $enrollment->remaining_balance ?? max(0, ($enrollment->total_fee ?? 0) - ($enrollment->payment_amount ?? 0));
            $balanceCleared  = $reenrollBalance <= 0;
            $docsVerified    = $enrollApproved === 4;
            $canReenroll     = ($isAutoAdvance || $isAssessed) && $docsVerified && $balanceCleared;
        @endphp

        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:14px;margin-bottom:24px;overflow:hidden;">
            {{-- Header --}}
            <div style="background:linear-gradient(135deg,#1a3a6c,#2471a3);padding:18px 22px;color:#fff;display:flex;align-items:center;gap:14px;">
                <i class="bi bi-mortarboard-fill" style="font-size:24px;"></i>
                <div>
                    <div style="font-size:16px;font-weight:700;">
                        {{ ($reenrollmentOpen ?? false) ? 'Enrollment for S.Y. '.($currentSchoolYear ?? '').' is Open' : 'Preparing for S.Y. '.($currentSchoolYear ?? '') }}
                    </div>
                    <div style="font-size:12px;opacity:0.8;margin-top:2px;">
                        Complete the checklist below then click Re-enroll
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div style="padding:20px;display:flex;flex-direction:column;gap:12px;">

                {{-- Assessment (Grade 1-6 only, not auto-advance) --}}
                @if(!$isAutoAdvance)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;
                    background:{{ $isAssessed ? '#f0fdf4' : '#fff8f0' }};border:1px solid {{ $isAssessed ? '#86efac' : '#fed7aa' }};">
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $isAssessed ? '#22c55e' : '#fb923c' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-{{ $isAssessed ? 'check-lg' : 'hourglass-split' }}" style="color:#fff;font-size:17px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:14px;color:{{ $isAssessed ? '#166534' : '#9a3412' }};">
                            Admin Assessment
                        </div>
                        <div style="font-size:12px;color:#666;margin-top:3px;">
                            @if($isAssessed)
                                Result: <strong style="color:{{ $promoResult==='Promoted'?'#16a34a':($promoResult==='Graduated'?'#1a3a6c':'#d97706') }};">
                                    {{ $promoResult }} → {{ $suggestedLabel }}
                                </strong>
                            @else
                                Admin will review your grades and balance before marking this complete.
                            @endif
                        </div>
                    </div>
                    <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;
                        background:{{ $isAssessed ? '#dcfce7' : '#fef3c7' }};
                        color:{{ $isAssessed ? '#15803d' : '#b45309' }};">
                        {{ $isAssessed ? '✓ Done' : 'Pending' }}
                    </span>
                </div>
                @endif

                {{-- Documents --}}
                <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;
                    background:{{ $docsVerified ? '#f0fdf4' : '#fff8f0' }};border:1px solid {{ $docsVerified ? '#86efac' : '#fed7aa' }};">
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $docsVerified ? '#22c55e' : '#fb923c' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-{{ $docsVerified ? 'check-lg' : 'file-earmark-x-fill' }}" style="color:#fff;font-size:17px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:14px;color:{{ $docsVerified ? '#166534' : '#9a3412' }};">
                            Required Documents
                        </div>
                        <div style="font-size:12px;color:#666;margin-top:3px;">
                            @if($docsVerified)
                                All documents verified and approved by admin.
                            @else
                                {{ $enrollApproved }}/4 approved — upload missing documents below.
                            @endif
                        </div>
                    </div>
                    <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;
                        background:{{ $docsVerified ? '#dcfce7' : '#fef3c7' }};
                        color:{{ $docsVerified ? '#15803d' : '#b45309' }};">
                        {{ $docsVerified ? '✓ Verified' : $enrollApproved.'/4' }}
                    </span>
                </div>

                {{-- Balance --}}
                <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;
                    background:{{ $balanceCleared ? '#f0fdf4' : '#fff8f0' }};border:1px solid {{ $balanceCleared ? '#86efac' : '#fed7aa' }};">
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $balanceCleared ? '#22c55e' : '#fb923c' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-{{ $balanceCleared ? 'check-lg' : 'wallet2' }}" style="color:#fff;font-size:17px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:14px;color:{{ $balanceCleared ? '#166534' : '#9a3412' }};">
                            Previous Balance
                        </div>
                        <div style="font-size:12px;color:#666;margin-top:3px;">
                            @if($balanceCleared)
                                No outstanding balance — you're clear.
                            @else
                                Balance: <strong style="color:#dc2626;">₱{{ number_format($reenrollBalance,2) }}</strong>
                                — settle this before or upon re-enrollment.
                            @endif
                        </div>
                    </div>
                    <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;
                        background:{{ $balanceCleared ? '#dcfce7' : '#fef3c7' }};
                        color:{{ $balanceCleared ? '#15803d' : '#b45309' }};">
                        {{ $balanceCleared ? '✓ Cleared' : 'Has Balance' }}
                    </span>
                </div>

                {{-- Action --}}
                @if($reenrollmentOpen ?? false)
                    @if(!$isAutoAdvance && !$isAssessed)
                    {{-- Grade 1-6: waiting for admin assessment --}}
                    <div style="padding:14px 18px;background:#f0f7ff;border:1.5px dashed #93c5fd;border-radius:10px;text-align:center;color:#1e40af;font-size:13px;">
                        <i class="bi bi-clock-history me-1"></i>
                        Waiting for admin assessment — your re-enroll button will unlock once assessment is complete.
                    </div>
                    @elseif($canReenroll)
                    {{-- All conditions met — button active --}}
                    <form id="reenrollForm" method="POST" action="{{ route('student.reenroll') }}">
                        @csrf
                        <input type="hidden" name="target_school_year" value="{{ $currentSchoolYear ?? '' }}">
                    </form>
                    <button type="button"
                        onclick="openReenrollConfirm('{{ $currentSchoolYear ?? '' }}','{{ $suggestedLabel }}',true,0)"
                        style="width:100%;background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;border:none;border-radius:10px;padding:14px 20px;font-weight:700;font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;position:relative;">
                        <i class="bi bi-arrow-repeat" style="font-size:18px;"></i>
                        Re-enroll for S.Y. {{ $currentSchoolYear ?? '' }} &mdash; {{ $suggestedLabel }}
                        <span style="position:absolute;top:-10px;right:12px;background:#22c55e;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                            Ready
                        </span>
                    </button>
                    @else
                    {{-- Locked — documents or balance not cleared --}}
                    <div>
                        <button type="button" disabled
                            style="width:100%;background:#94a3b8;color:#fff;border:none;border-radius:10px;padding:14px 20px;font-weight:700;font-size:15px;cursor:not-allowed;display:flex;align-items:center;justify-content:center;gap:10px;">
                            <i class="bi bi-lock-fill" style="font-size:18px;"></i>
                            Re-enroll for S.Y. {{ $currentSchoolYear ?? '' }} — Locked
                        </button>
                        <div style="margin-top:10px;padding:12px 16px;background:#fff8f0;border:1px solid #fed7aa;border-radius:8px;font-size:12px;color:#92400e;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Complete the following to unlock:</strong>
                            <ul style="margin:6px 0 0 16px;padding:0;line-height:1.8;">
                                @if(!$docsVerified)
                                <li>Get all required documents approved ({{ $enrollApproved }}/4 verified)</li>
                                @endif
                                @if(!$balanceCleared)
                                <li>Settle outstanding balance of <strong>₱{{ number_format($reenrollBalance, 2) }}</strong></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    @endif
                @else
                <div style="padding:14px 18px;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:10px;text-align:center;color:#666;font-size:13px;">
                    <i class="bi bi-lock me-1"></i>
                    Enrollment is not yet open. Check back soon.
                </div>
                @endif

            </div>

            {{-- Divider before last year's info --}}
            <div style="padding:10px 22px;background:#f8f9fa;border-top:1px solid #e5e7eb;font-size:11px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.6px;">
                Previous Enrollment Record
            </div>
        </div>
        @endif

        @if($enrollment)
            {{-- Enrollment Information --}}
            <div class="content-card mb-4">
                <div class="content-card-header">
                    <h6><i class="bi bi-clipboard-check me-2" style="color:var(--gold);"></i>Enrollment Information</h6>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : ($enrollment->status === 'approved' ? 'info' : ($enrollment->status === 'pending' ? 'warning' : 'danger')) }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                        @if($enrollment->status === 'enrolled' && $availableSections->count() > 1)
                        <button type="button" class="btn-dash" style="padding:5px 14px; font-size:12px; background:#f0f4ff; color:var(--blue); border:1px solid #c8d6f0; border-radius:8px;" onclick="openChangeSectionModal()">
                            <i class="bi bi-arrow-left-right me-1"></i>Change Section
                        </button>
                        @endif
                        <button onclick="showSection('payment')" style="padding:5px 14px;font-size:12px;background:var(--blue);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
                            <i class="bi bi-credit-card me-1"></i>Go to Payment
                        </button>
                    </div>
                </div>
                <div style="padding:20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="dash-form-label">Reference Number</label>
                                <div class="form-field">
                                    <input type="text" value="{{ $enrollment->reference_number }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Grade Level</label>
                                <div class="form-field">
                                    <input type="text" value="{{ $enrollment->grade_level ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Section</label>
                                <div class="form-field" style="display:flex; align-items:center; gap:8px;">
                                    <input type="text" id="currentSectionDisplay" value="{{ $enrollment->section ?? 'Not yet assigned' }}" readonly style="flex:1;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Student Type</label>
                                <div class="form-field">
                                    <input type="text" value="{{ $enrollment->student_type ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="dash-form-label">Application Date</label>
                                <div class="form-field">
                                    <input type="text" value="{{ $enrollment->created_at->format('M d, Y') }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Payment Status</label>
                                <div class="form-field">
                                    <input type="text" value="{{ ucfirst($enrollment->payment_status ?? 'pending') }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Amount Paid</label>
                                <div class="form-field">
                                    <input type="text" value="PHP {{ number_format($enrollment->payment_amount ?? 0, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Payment Option</label>
                                <div class="form-field">
                                    @if($enrollment->payment_option)
                                        @php
                                            $optionLabels = [
                                                'A' => 'Option A — Full Payment (Cash Basis)',
                                                'B' => 'Option B — Monthly (Tuition + Electric)',
                                                'C' => 'Option C — Monthly (Tuition + Misc + Electric)',
                                                'D' => 'Option D — Monthly (Nursery/Kinder)',
                                            ];
                                        @endphp
                                        <input type="text" value="{{ $optionLabels[$enrollment->payment_option] ?? ('Option ' . $enrollment->payment_option) }}" readonly>
                                    @else
                                        <input type="text" value="Not yet selected" readonly style="color:#999;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div style="text-align:center;padding:60px;color:var(--muted);">
                <i class="bi bi-clipboard-x" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                <h5>No Enrollment Found</h5>
                <p>You don't have an active enrollment yet. Please contact the school registrar.</p>
            </div>
        @endif
    </div>{{-- /section-enrollment --}}

    {{-- ══════════════════════════════
         SECTION: PAYMENT
    ══════════════════════════════ --}}
    <div id="section-payment" class="student-info-section" style="display:none;">
        <div class="info-section-title">Payment</div>

        @if($enrollment)

            {{-- Payment Summary — shown for ALL payment types --}}
            @php
                $pmtTotal   = $enrollment->total_fee ?? 0;
                $pmtPaid    = $enrollment->payment_amount ?? 0;
                $pmtBalance = $enrollment->remaining_balance ?? max(0, $pmtTotal - $pmtPaid);
                $pmtPct     = $pmtTotal > 0 ? min(100, round($pmtPaid / $pmtTotal * 100)) : 0;
                $pmtStatus  = $enrollment->payment_status ?? 'pending';
                $optLabels  = ['A'=>'Full Payment','B'=>'Monthly (Option B)','C'=>'Monthly (Option C)','D'=>'Monthly (Option D)'];
            @endphp
            <div class="content-card mb-4">
                <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <h6><i class="bi bi-wallet2 me-2" style="color:var(--gold);"></i>Payment Summary</h6>
                    <span class="badge bg-{{ $pmtStatus==='paid'?'success':($pmtStatus==='partial'?'primary':'warning') }}">
                        {{ ucfirst($pmtStatus) }}
                    </span>
                </div>
                <div style="padding:20px;">
                    {{-- Fee stats --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
                        <div style="background:#e3f2fd;border-radius:10px;padding:14px;text-align:center;">
                            <div style="font-size:11px;color:#1565c0;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Total Fee</div>
                            <div style="font-size:18px;font-weight:700;color:#1565c0;">₱{{ number_format($pmtTotal,2) }}</div>
                        </div>
                        <div style="background:#e8f5e9;border-radius:10px;padding:14px;text-align:center;">
                            <div style="font-size:11px;color:#2e7d32;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Amount Paid</div>
                            <div style="font-size:18px;font-weight:700;color:#2e7d32;">₱{{ number_format($pmtPaid,2) }}</div>
                        </div>
                        <div style="background:{{ $pmtBalance>0?'#fff3e0':'#e8f5e9' }};border-radius:10px;padding:14px;text-align:center;">
                            <div style="font-size:11px;color:{{ $pmtBalance>0?'#e65100':'#2e7d32' }};font-weight:700;text-transform:uppercase;margin-bottom:4px;">Balance</div>
                            <div style="font-size:18px;font-weight:700;color:{{ $pmtBalance>0?'#e65100':'#2e7d32' }};">₱{{ number_format($pmtBalance,2) }}</div>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    <div style="height:10px;background:#e0e0e0;border-radius:5px;margin-bottom:14px;overflow:hidden;">
                        <div style="height:100%;border-radius:5px;background:{{ $pmtPct>=100?'#28a745':'var(--blue)' }};width:{{ $pmtPct }}%;transition:width .4s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);">
                        <span>{{ $optLabels[$enrollment->payment_option ?? ''] ?? ucfirst($enrollment->payment_type ?? 'Not set') }}</span>
                        <span>{{ $pmtPct }}% paid</span>
                    </div>
                </div>
            </div>

            {{-- Installment quick-row + View Schedule button --}}
            @if($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option, ['B', 'C', 'D']))
            @php
                $instList    = $paymentInstallments ?? collect([]);
                $instPaid    = $instList->where('status', 'paid')->count();
                $instTotal   = $instList->count();
                $instNext    = $instList->whereIn('status', ['pending','overdue'])->sortBy('due_date')->first();
                $dpAmount    = $enrollment->downpayment_amount ?? 0;
                $dpPaid      = $dpAmount > 0 && ($enrollment->payment_amount ?? 0) >= $dpAmount;
            @endphp
            <div class="content-card mb-4">
                <div style="padding:16px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                        {{-- Paid months counter --}}
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-calendar-check-fill" style="color:#43a047;font-size:16px;"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;color:var(--muted);line-height:1;">Months Paid</div>
                                <div style="font-size:16px;font-weight:700;color:#43a047;line-height:1.3;">{{ $instPaid }}<span style="color:#aaa;font-weight:400;">/{{ $instTotal }}</span></div>
                            </div>
                        </div>
                        {{-- Next due --}}
                        @if($instNext)
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:{{ ($instNext->status??'') === 'overdue' ? '#ffebee' : '#eff6ff' }};display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-alarm" style="color:{{ ($instNext->status??'') === 'overdue' ? '#e53935' : '#3b82f6' }};font-size:16px;"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;color:var(--muted);line-height:1;">Next Due</div>
                                <div style="font-size:13px;font-weight:700;color:{{ ($instNext->status??'') === 'overdue' ? '#e53935' : '#1d4ed8' }};line-height:1.3;">
                                    {{ $instNext->month_name ?? '—' }}
                                    &mdash; {{ optional($instNext->due_date)->format('M d, Y') ?? '—' }}
                                    @if(($instNext->status??'') === 'overdue')
                                        <span class="badge bg-danger ms-1" style="font-size:10px;">Overdue</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <button type="button" class="btn-dash btn-dash-primary" onclick="openInstallmentScheduleModal()" style="padding:9px 18px; font-size:13px; flex-shrink:0;">
                        <i class="bi bi-calendar-week me-2"></i>View Schedule
                    </button>
                </div>
            </div>
            @endif

            {{-- Make Payment Card --}}
            <div class="content-card mb-4">
                <div class="content-card-header">
                    <h6><i class="bi bi-credit-card me-2"></i>Make Payment</h6>
                </div>
                <div style="padding:28px;">
                    {{-- Step Indicator --}}
                    <div class="pay-steps" id="payStepIndicator">
                        <div class="pay-step active" id="payStepA">
                            <div class="pay-step-num">1</div>
                            <div class="pay-step-label">Select Plan</div>
                        </div>
                        <div class="pay-step-line" id="payLine1"></div>
                        <div class="pay-step" id="payStepB">
                            <div class="pay-step-num">2</div>
                            <div class="pay-step-label">Payment Method</div>
                        </div>
                        <div class="pay-step-line" id="payLine2"></div>
                        <div class="pay-step" id="payStepC">
                            <div class="pay-step-num">3</div>
                            <div class="pay-step-label">Submit</div>
                        </div>
                    </div>

                    {{-- STEP 1: Choose Payment Option --}}
                    <div id="payPanelStep1">
                        <p style="font-size:13px; color:#888; margin-bottom:16px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Choose your payment plan</p>
                        <div class="row mb-3 g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="payment-option-card" id="card-opt-a" onclick="selectPaymentOption('A')" style="padding-top:26px;">
                                    <span class="pay-opt-badge"><i class="bi bi-star-fill me-1"></i>Best Deal</span>
                                    <div class="payment-option-header"><i class="bi bi-cash-coin"></i><h6>Option A</h6></div>
                                    <p>Cash Basis — Pay in full &amp; save</p>
                                    <div class="payment-option-features">
                                        <span style="font-size:20px; font-weight:800; color:var(--gold);" id="opt-a-total">—</span>
                                        <span style="color:#28a745; font-weight:600;"><i class="bi bi-tag-fill"></i> 20% discount applied</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="payment-option-card" id="card-opt-b" onclick="selectPaymentOption('B')">
                                    <div class="payment-option-header"><i class="bi bi-calendar-month"></i><h6>Option B</h6></div>
                                    <p>Monthly — 9 months (all grades)</p>
                                    <div class="payment-option-features">
                                        <span style="font-size:18px; font-weight:800; color:var(--gold);" id="opt-b-monthly">—</span>
                                        <span><i class="bi bi-check-circle-fill"></i> /month for 9 months</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3" id="opt-c-container">
                                <div class="payment-option-card" id="card-opt-c" onclick="selectPaymentOption('C')">
                                    <div class="payment-option-header"><i class="bi bi-calendar2-week"></i><h6>Option C</h6></div>
                                    <p>Monthly — Grade 1–6</p>
                                    <div class="payment-option-features">
                                        <span style="font-size:18px; font-weight:800; color:var(--gold);" id="opt-c-monthly">—</span>
                                        <span><i class="bi bi-check-circle-fill"></i> /month for 9 months</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3" id="opt-d-container">
                                <div class="payment-option-card" id="card-opt-d" onclick="selectPaymentOption('D')">
                                    <div class="payment-option-header"><i class="bi bi-calendar2-week"></i><h6>Option D</h6></div>
                                    <p>Monthly — Nursery &amp; Kinder</p>
                                    <div class="payment-option-features">
                                        <span style="font-size:18px; font-weight:800; color:var(--gold);" id="opt-d-monthly">—</span>
                                        <span><i class="bi bi-check-circle-fill"></i> /month for 9 months</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="payment-breakdown-display" style="display:none; background:linear-gradient(135deg,#f4f8ff,#eef2ff); border:1px solid #c8d6f0; border-radius:12px; padding:20px; margin-bottom:20px;">
                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:14px; font-size:14px;"><i class="bi bi-receipt-cutoff me-2"></i>Payment Breakdown</h6>
                            <div id="breakdown-content"></div>
                        </div>
                        <input type="hidden" name="payment_option" id="selected-payment-option" value="">
                        <input type="hidden" name="downpayment_amount" id="downpayment-amount" value="">
                        <input type="hidden" name="monthly_amount" id="monthly-amount" value="">
                        <input type="hidden" name="total_amount" id="total-amount" value="">
                        <div id="pay-button-container" style="display:none; text-align:right;">
                            <button type="button" class="btn-dash btn-dash-primary" onclick="showPaymentMethodSelection()" style="padding:10px 28px;">
                                <i class="bi bi-arrow-right-circle me-2"></i>Continue to Payment Method
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Choose Payment Method --}}
                    <div id="payPanelStep2" style="display:none;">
                        <p style="font-size:13px; color:#888; margin-bottom:16px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">How would you like to pay?</p>
                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <div class="pay-method-card" id="card-cash" onclick="selectPaymentMethod('cash')">
                                    <div class="pay-method-icon cash"><i class="bi bi-cash-stack"></i></div>
                                    <div class="pay-method-info"><strong>Cash at School</strong><span>Visit the cashier — Mon–Fri, 8AM–4PM</span></div>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:14px;"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="pay-method-card" id="card-xendit" onclick="selectPaymentMethod('xendit')">
                                    <div class="pay-method-icon" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;">
                                        <i class="bi bi-link-45deg"></i>
                                    </div>
                                    <div class="pay-method-info">
                                        <strong>Pay Online</strong>
                                        <span>GCash, Maya, GrabPay, Bank, OTC via secure link</span>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted" style="font-size:14px;"></i>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-dash" onclick="backToStep1()" style="padding:8px 20px; background:#f0f4ff; color:var(--blue); border:1px solid #c8d6f0; border-radius:8px;">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </button>
                    </div>

                    {{-- STEP 3: Payment Details --}}
                    <div id="payment-forms" style="display:none;">
                        {{-- Step 3 Header Bar --}}
                        <div id="payment-step-title" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:10px;margin-bottom:20px;border:1px solid #bfdbfe;">
                            <div style="width:30px;height:30px;border-radius:50%;background:#1d4ed8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span style="font-size:13px;font-weight:800;color:#fff;">3</span>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e3a5f;" id="step3-method-label">Payment Details</div>
                                <div style="font-size:11px;color:#64748b;" id="step3-method-sub">Review your payment details below</div>
                            </div>
                        </div>

                        {{-- ── CASH INFO PANEL (shown when method = cash) ── --}}
                        <div id="cash-info-panel" style="display:none;">
                            <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:18px;padding:24px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                                    <div style="width:50px;height:50px;border-radius:14px;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-building" style="font-size:22px;color:#fff;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:15px;font-weight:800;color:#15803d;">Pay at the School Cashier</div>
                                        <div style="font-size:12px;color:#16a34a;margin-top:2px;">Bring your payment to the cashier window</div>
                                    </div>
                                </div>
                                <div style="background:#fff;border-radius:14px;padding:18px 20px;margin-bottom:16px;border:1px solid #bbf7d0;">
                                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;">Amount Due</div>
                                    <div style="font-size:30px;font-weight:900;color:#15803d;letter-spacing:-.5px;">₱<span id="cash-amount-display">0.00</span></div>
                                </div>
                                <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;color:#166534;">
                                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fff;border-radius:10px;border:1px solid #bbf7d0;">
                                        <i class="bi bi-clock-fill" style="color:#16a34a;font-size:16px;flex-shrink:0;"></i>
                                        <div><strong>Office Hours:</strong> Monday – Friday, 8:00 AM – 4:00 PM</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fff;border-radius:10px;border:1px solid #bbf7d0;">
                                        <i class="bi bi-geo-alt-fill" style="color:#16a34a;font-size:16px;flex-shrink:0;"></i>
                                        <div><strong>Location:</strong> Cashier Window, Main Building Ground Floor</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fff;border-radius:10px;border:1px solid #bbf7d0;">
                                        <i class="bi bi-person-badge-fill" style="color:#16a34a;font-size:16px;flex-shrink:0;"></i>
                                        <div><strong>Student Ref:</strong> {{ $enrollment->reference_number ?? 'Present your Student ID' }}</div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="backToStep2()"
                                style="padding:12px 20px;background:#f0f4ff;color:#1a3a6c;border:1.5px solid #c8d6f0;border-radius:12px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                        </div>

                        {{-- ── XENDIT PAYMENT PANEL (shown when method = xendit) ── --}}
                        <div id="xendit-payment-panel" style="display:none;">

                            {{-- Header card --}}
                            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2471a3 100%);border-radius:18px;padding:22px 24px;margin-bottom:16px;color:#fff;position:relative;overflow:hidden;">
                                <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
                                <div style="position:absolute;bottom:-30px;right:40px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
                                <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;position:relative;">
                                    <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;backdrop-filter:blur(4px);">
                                        <i class="bi bi-shield-lock-fill" style="font-size:22px;color:#fff;"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:15px;font-weight:800;letter-spacing:-.2px;">Secure Online Payment</div>
                                        <div style="font-size:11px;opacity:.75;margin-top:2px;">Powered by Xendit &mdash; 256-bit SSL encrypted</div>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.15);border-radius:20px;padding:5px 12px;font-size:10px;font-weight:700;display:flex;align-items:center;gap:5px;backdrop-filter:blur(4px);">
                                        <i class="bi bi-lock-fill"></i> SECURED
                                    </div>
                                </div>
                                {{-- Amount display --}}
                                <div style="background:rgba(255,255,255,0.12);border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;position:relative;">
                                    <div>
                                        <div style="font-size:10px;opacity:.65;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Amount Due</div>
                                        <div style="font-size:28px;font-weight:900;letter-spacing:-.5px;line-height:1;">₱<span id="xendit-amount-display">0.00</span></div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:10px;opacity:.65;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Link Valid</div>
                                        <div style="font-size:14px;font-weight:700;">24 hours</div>
                                    </div>
                                </div>
                                {{-- Hidden actual amount input (set programmatically) --}}
                                <input type="number" id="xendit-amount" style="display:none;" min="1" step="0.01">
                            </div>

                            {{-- Payment method cards --}}
                            <div style="margin-bottom:16px;">
                                <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-grid-3x3-gap-fill"></i> Select Payment Method
                                </div>
                                <input type="hidden" id="xendit-method" value="">
                                <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;" id="xendit-method-grid">
                                    <div class="xmethod-card" data-method="gcash" onclick="selectXenditMethod('gcash')"
                                        style="border:2px solid #e8ecf1;border-radius:14px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .2s;background:#fff;">
                                        <div style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#0070ff,#00aaff);margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-phone-fill" style="font-size:18px;color:#fff;"></i>
                                        </div>
                                        <div style="font-size:12px;font-weight:700;color:#1e293b;">GCash</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">e-Wallet</div>
                                    </div>
                                    <div class="xmethod-card" data-method="maya" onclick="selectXenditMethod('maya')"
                                        style="border:2px solid #e8ecf1;border-radius:14px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .2s;background:#fff;">
                                        <div style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#00b09b,#00d2a0);margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-wallet2" style="font-size:18px;color:#fff;"></i>
                                        </div>
                                        <div style="font-size:12px;font-weight:700;color:#1e293b;">Maya</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">e-Wallet</div>
                                    </div>
                                    <div class="xmethod-card" data-method="grabpay" onclick="selectXenditMethod('grabpay')"
                                        style="border:2px solid #e8ecf1;border-radius:14px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .2s;background:#fff;">
                                        <div style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#00b14f,#00d264);margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-bag-fill" style="font-size:18px;color:#fff;"></i>
                                        </div>
                                        <div style="font-size:12px;font-weight:700;color:#1e293b;">GrabPay</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">e-Wallet</div>
                                    </div>
                                    <div class="xmethod-card" data-method="bank" onclick="selectXenditMethod('bank')"
                                        style="border:2px solid #e8ecf1;border-radius:14px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .2s;background:#fff;">
                                        <div style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#1a3a6c,#2471a3);margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-bank2" style="font-size:18px;color:#fff;"></i>
                                        </div>
                                        <div style="font-size:12px;font-weight:700;color:#1e293b;">Bank</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">BPI · UBP</div>
                                    </div>
                                    <div class="xmethod-card" data-method="otc" onclick="selectXenditMethod('otc')"
                                        style="border:2px solid #e8ecf1;border-radius:14px;padding:14px 8px;text-align:center;cursor:pointer;transition:all .2s;background:#fff;">
                                        <div style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#e65100,#f57c00);margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-shop" style="font-size:18px;color:#fff;"></i>
                                        </div>
                                        <div style="font-size:12px;font-weight:700;color:#1e293b;">OTC</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">7-Eleven</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Error message --}}
                            <div id="xendit-error" style="display:none;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:14px;font-size:13px;color:#dc2626;align-items:center;gap:8px;">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span id="xendit-error-text"></span>
                            </div>

                            {{-- Generated link result --}}
                            <div id="xendit-link-result" style="display:none;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:14px;padding:18px 20px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-check-lg" style="font-size:18px;color:#fff;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:#166534;">Payment link generated!</div>
                                        <div style="font-size:11px;color:#16a34a;">Open the link below to complete your payment securely.</div>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <input type="text" id="xendit-link-url" readonly
                                        style="flex:1;padding:10px 14px;border-radius:10px;border:1.5px solid #86efac;background:#fff;font-size:12px;color:#374151;font-family:monospace;outline:none;">
                                    <button type="button" onclick="copyXenditStudentLink()"
                                        style="flex-shrink:0;padding:10px 16px;background:#1a3a6c;color:#fff;border:none;border-radius:10px;cursor:pointer;font-size:13px;transition:background .2s;" title="Copy link">
                                        <i class="bi bi-clipboard" id="xendit-copy-icon"></i>
                                    </button>
                                </div>
                                <div id="xendit-link-expiry" style="font-size:11px;color:#64748b;margin-bottom:12px;"></div>
                                <a id="xendit-link-open" href="#" target="_blank"
                                    style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#16a34a;color:#fff;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;transition:background .2s;">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Payment Page
                                </a>
                            </div>

                            {{-- Action buttons --}}
                            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                                <button type="button" id="xendit-generate-btn" onclick="studentGenerateXenditLink()" disabled
                                    style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;background:#cbd5e1;color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:not-allowed;font-family:inherit;transition:all .2s;min-width:200px;">
                                    <i class="bi bi-shield-lock"></i> Generate Secure Link
                                </button>
                                <button type="button" onclick="backToStep2()"
                                    style="padding:14px 20px;background:#f0f4ff;color:#1a3a6c;border:1.5px solid #c8d6f0;border-radius:12px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>{{-- /Make Payment --}}


        @else
            <div class="content-card">
                <div style="padding:40px; text-align:center;">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size:48px; display:block; margin-bottom:16px;"></i>
                    <h5 class="text-warning mb-3">No Active Enrollment Found</h5>
                    <p class="text-muted mb-4">Please complete the enrollment process first.</p>
                    <a href="{{ route('admission') }}" class="btn-dash btn-dash-primary">
                        <i class="bi bi-plus-circle me-2"></i>Start Enrollment
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL: Installment Schedule
    ══════════════════════════════════════════════════════ --}}
    @if($enrollment && ($enrollment->payment_type === 'installment' || in_array($enrollment->payment_option ?? '', ['B','C','D'])))
    @php
        $mInstList  = $paymentInstallments ?? collect([]);
        $mDpAmount  = $enrollment->downpayment_amount ?? 0;
        $mDpPaid    = $mDpAmount > 0 && ($enrollment->payment_amount ?? 0) >= $mDpAmount;
        $mOptLabels = ['A'=>'Full Payment','B'=>'Option B — Monthly','C'=>'Option C — Monthly','D'=>'Option D — Monthly'];
        $mPaidCnt   = $mInstList->where('status','paid')->count();
        $mTotalCnt  = $mInstList->count();
    @endphp
    <div class="modal fade" id="installmentScheduleModal" tabindex="-1" aria-labelledby="installmentScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,var(--blue),#1a56db); color:#fff; border:none; padding:20px 24px;">
                    <div>
                        <h5 class="modal-title mb-0" id="installmentScheduleModalLabel" style="font-weight:700;">
                            <i class="bi bi-calendar-week me-2"></i>Installment Schedule
                        </h5>
                        <div style="font-size:12px; opacity:.8; margin-top:3px;">
                            {{ $mOptLabels[$enrollment->payment_option ?? ''] ?? ucfirst($enrollment->payment_type ?? '') }}
                            &nbsp;&middot;&nbsp; S.Y. {{ $enrollment->school_year ?? '—' }}
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px; background:#f8faff;">

                    {{-- Summary stats --}}
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px;">
                        <div style="background:#fff; border-radius:10px; padding:14px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,.06);">
                            <div style="font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Total Fee</div>
                            <div style="font-size:17px; font-weight:700; color:var(--blue);">₱{{ number_format($enrollment->total_fee ?? 0, 2) }}</div>
                        </div>
                        <div style="background:#fff; border-radius:10px; padding:14px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,.06);">
                            <div style="font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Months Paid</div>
                            <div style="font-size:17px; font-weight:700; color:#43a047;">{{ $mPaidCnt }}<span style="color:#aaa; font-weight:400; font-size:14px;">/{{ $mTotalCnt }}</span></div>
                        </div>
                        <div style="background:#fff; border-radius:10px; padding:14px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,.06);">
                            <div style="font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Balance</div>
                            <div style="font-size:17px; font-weight:700; color:{{ ($enrollment->remaining_balance ?? 1) > 0 ? '#e65100' : '#43a047' }};">
                                ₱{{ number_format($enrollment->remaining_balance ?? max(0, ($enrollment->total_fee??0) - ($enrollment->payment_amount??0)), 2) }}
                            </div>
                        </div>
                    </div>

                    {{-- Downpayment row --}}
                    @if($mDpAmount > 0)
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:8px; letter-spacing:.5px;">Downpayment</div>
                        <div style="
                            border:1px solid {{ $mDpPaid ? '#c8e6c9' : '#ffe082' }};
                            border-left:4px solid {{ $mDpPaid ? '#43a047' : '#f59e0b' }};
                            border-radius:10px; padding:14px 16px;
                            background:{{ $mDpPaid ? '#f9fffe' : '#fffdf0' }};
                            display:flex; align-items:center; gap:14px;">
                            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;
                                background:{{ $mDpPaid ? '#e8f5e9' : '#fff8e1' }};
                                display:flex;align-items:center;justify-content:center;">
                                <i class="bi {{ $mDpPaid ? 'bi-check-lg' : 'bi-coin' }}" style="color:{{ $mDpPaid ? '#43a047' : '#f59e0b' }};font-size:17px;"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700; font-size:14px; color:var(--text);">Downpayment</div>
                                <div style="font-size:12px; color:var(--muted);">Required before monthly installments begin</div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <div style="font-size:15px; font-weight:700; color:{{ $mDpPaid ? '#43a047' : '#c77800' }};">₱{{ number_format($mDpAmount, 2) }}</div>
                                @if($mDpPaid)
                                    <div style="font-size:11px; color:#43a047;"><i class="bi bi-check-circle-fill me-1"></i>Paid</div>
                                @else
                                    <span style="font-size:11px; padding:2px 8px; border-radius:20px; background:#fff3e0; color:#c77800; font-weight:600;">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Monthly installments --}}
                    @if($mInstList->count() > 0)
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:8px; letter-spacing:.5px;">Monthly Installments</div>
                    <div style="display:flex; flex-direction:column; gap:9px;">
                        @foreach($mInstList->sortBy('due_date') as $inst)
                        @php
                            $iSt  = $inst->status ?? 'pending';
                            $iOvd = $iSt === 'overdue';
                            $iPd  = $iSt === 'paid';
                            $iPA  = $iSt === 'pending_approval';
                            $iLF  = $inst->late_fee ?? 0;
                            $iTD  = $inst->total_due ?? ($inst->amount ?? 0) + $iLF;
                        @endphp
                        <div style="
                            border:1px solid {{ $iOvd ? '#ffcdd2' : ($iPd ? '#c8e6c9' : '#e0e7ff') }};
                            border-left:4px solid {{ $iOvd ? '#e53935' : ($iPd ? '#43a047' : ($iPA ? '#f59e0b' : '#3b82f6')) }};
                            border-radius:10px; padding:13px 16px;
                            background:{{ $iOvd ? '#fff8f8' : ($iPd ? '#f9fffe' : '#fff') }};
                            display:flex; align-items:center; gap:13px;">
                            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                                background:{{ $iOvd?'#ffebee':($iPd?'#e8f5e9':($iPA?'#fff3e0':'#e8eeff')) }};
                                display:flex;align-items:center;justify-content:center;">
                                @if($iPd)
                                    <i class="bi bi-check-lg" style="color:#43a047;font-size:17px;"></i>
                                @elseif($iOvd)
                                    <i class="bi bi-exclamation-circle-fill" style="color:#e53935;font-size:15px;"></i>
                                @elseif($iPA)
                                    <i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:15px;"></i>
                                @else
                                    <i class="bi bi-clock" style="color:#3b82f6;font-size:15px;"></i>
                                @endif
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
                                    <span style="font-weight:700; font-size:13px; color:var(--text);">
                                        {{ $inst->month_name ?? ('Month ' . ($inst->installment_number ?? '')) }}
                                    </span>
                                    @if($iPd)
                                        <span class="status-pill approved" style="font-size:10px;padding:1px 7px;"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>
                                    @elseif($iPA)
                                        <span class="status-pill pending" style="font-size:10px;padding:1px 7px;"><i class="bi bi-hourglass me-1"></i>For Approval</span>
                                    @elseif($iOvd)
                                        <span class="status-pill rejected" style="font-size:10px;padding:1px 7px;"><i class="bi bi-exclamation-circle me-1"></i>Overdue</span>
                                    @else
                                        <span style="font-size:10px;padding:1px 7px;border-radius:20px;background:#e8eeff;color:#3b82f6;font-weight:600;">Upcoming</span>
                                    @endif
                                </div>
                                <div style="font-size:11px; color:var(--muted); margin-top:2px; display:flex; gap:12px; flex-wrap:wrap;">
                                    <span><i class="bi bi-calendar3 me-1"></i>Due: {{ optional($inst->due_date)->format('M d, Y') ?? '—' }}</span>
                                    @if($iPd && $inst->paid_at)
                                        <span><i class="bi bi-check2 me-1 text-success"></i>Paid: {{ \Carbon\Carbon::parse($inst->paid_at)->format('M d, Y') }}</span>
                                    @endif
                                    @if($iOvd && ($inst->weeks_overdue ?? 0) > 0)
                                        <span style="color:#e53935;"><i class="bi bi-exclamation-triangle me-1"></i>{{ $inst->weeks_overdue }} wk{{ $inst->weeks_overdue > 1 ? 's' : '' }} overdue</span>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <div style="font-size:14px; font-weight:700; color:{{ $iOvd?'#e53935':($iPd?'#43a047':'var(--blue)') }};">
                                    ₱{{ number_format($iTD, 2) }}
                                </div>
                                @if($iLF > 0)
                                <div style="font-size:10px; color:#e53935;">+₱{{ number_format($iLF, 2) }} late fee</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center; padding:32px; color:var(--muted);">
                        <i class="bi bi-calendar-x" style="font-size:38px; display:block; margin-bottom:10px; opacity:.4;"></i>
                        <p style="margin:0; font-size:13px;">No installment schedule found. Please contact the school office.</p>
                    </div>
                    @endif

                </div>
                <div class="modal-footer" style="border:none; padding:16px 24px; background:#f8faff;">
                    <button type="button" class="btn-dash" data-bs-dismiss="modal" style="padding:9px 20px; background:#f0f4ff; color:var(--blue); border:1px solid #c8d6f0; border-radius:8px;">
                        <i class="bi bi-x me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════
         SECTION: ANNOUNCEMENTS
    ══════════════════════════════ --}}
    <div id="section-announcements" class="student-info-section" style="display:none;">
        <div class="info-section-title">Announcements</div>

        {{-- CHANGE: Replace with @foreach($announcements as $ann) --}}
        <div class="content-card mb-3">
            <div style="padding:20px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <span class="status-badge active">General</span>
                    <span style="font-size:12px;color:var(--muted);">June 15, 2026</span>
                </div>
                <h6 style="font-weight:700;color:var(--blue);margin-bottom:8px;">School Opening Day — S.Y. 2026–2027</h6>
                <p style="font-size:13px;color:#555;line-height:1.7;margin:0;">
                    The IEMELIF Learning Center warmly welcomes all students to the first day of classes.
                    Please ensure all requirements are submitted. Attendance is required for all grade levels.
                </p>
            </div>
        </div>
        <div class="content-card mb-3">
            <div style="padding:20px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <span class="status-badge pending">Enrollment</span>
                    <span style="font-size:12px;color:var(--muted);">June 10, 2026</span>
                </div>
                <h6 style="font-weight:700;color:var(--blue);margin-bottom:8px;">Enrollment Period for S.Y. 2026–2027 is Now Open</h6>
                <p style="font-size:13px;color:#555;line-height:1.7;margin:0;">
                    Enrollment is officially open. Bring all required documents to the registrar's office during office hours.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════
         SECTION: SETTINGS
    ══════════════════════════════ --}}
    <div id="section-settings" class="student-info-section" style="display:none;">
        <div class="info-section-title">Account Settings</div>

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

                        {{-- Profile photo upload --}}
                        <form method="POST" action="{{ route('student.photo.upload') }}" enctype="multipart/form-data" id="student-photo-form">
                            @csrf
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                                <div style="position:relative;flex-shrink:0;width:72px;height:72px;">
                                    @if($profile && $profile->photo)
                                        <img id="student-avatar-img" src="{{ asset('storage/' . $profile->photo) }}" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
                                    @else
                                        <div id="student-avatar-placeholder" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-person-fill" style="font-size:30px;color:#fff;"></i>
                                        </div>
                                        <img id="student-avatar-img" src="" alt="Profile"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);display:none;">
                                    @endif
                                    <label for="student-photo-input" title="Change photo"
                                           style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:var(--blue);border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                        <i class="bi bi-camera-fill" style="font-size:11px;color:#fff;"></i>
                                    </label>
                                    <input type="file" id="student-photo-input" name="photo" accept="image/jpeg,image/png,image/webp"
                                           style="display:none;" onchange="previewStudentPhoto(this)">
                                </div>
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:var(--text);">{{ Auth::user()->name }}</div>
                                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ Auth::user()->email }}</div>
                                    <span style="display:inline-block;background:#e3f2fd;color:#1565c0;border:1px solid #90caf9;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:5px;">Student</span>
                                </div>
                            </div>
                            <button type="submit" id="student-photo-submit" style="display:none;"></button>
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
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Role</span>
                                <span style="font-size:12px;font-weight:600;">Student</span>
                            </div>
                            @if($enrollment)
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:12px;color:var(--muted);">Grade Level</span>
                                <span style="font-size:12px;font-weight:600;">{{ ucwords(str_replace('grade', 'Grade ', $enrollment->grade_level ?? '—')) }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;">
                                <span style="font-size:12px;color:var(--muted);">Section</span>
                                <span style="font-size:12px;font-weight:600;">{{ $enrollment->section ?? '—' }}</span>
                            </div>
                            @endif
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
                        @if($errors->has('otp_code'))
                            <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('otp_code') }}
                            </div>
                        @endif

                        {{-- Step 1: Enter passwords --}}
                        <div id="stu-pwd-step1" @if(session('otp_token')) style="display:none;" @endif>
                            <div style="background:#e8f0fb;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#1a3a6c;display:flex;align-items:center;gap:8px;">
                                <i class="bi bi-shield-lock-fill" style="font-size:16px;"></i>
                                For your security, a 6-digit OTP will be sent to your email to confirm the change.
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Current Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="stu-cur-pwd" class="dash-form-control" placeholder="Enter your current password">
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="stu-new-pwd" class="dash-form-control" placeholder="At least 8 characters" minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="dash-form-label">Confirm New Password <span style="color:var(--red);">*</span></label>
                                <input type="password" id="stu-confirm-pwd" class="dash-form-control" placeholder="Re-enter new password">
                            </div>
                            <div id="stu-pwd-error" style="display:none;background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#c0392b;"></div>
                            <button type="button" class="btn-dash btn-dash-primary" id="stu-send-otp-btn" onclick="studentSendOtp()">
                                <i class="bi bi-envelope-fill"></i> Send OTP to Email
                            </button>
                        </div>

                        {{-- Step 2: Enter OTP --}}
                        <div id="stu-pwd-step2" @if(!session('otp_token')) style="display:none;" @endif>
                            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#2e7d32;">
                                <i class="bi bi-envelope-check-fill"></i>
                                OTP sent to <strong id="stu-otp-email">your email</strong>. Enter the 6-digit code below. Expires in 10 minutes.
                            </div>
                            <form method="POST" action="{{ route('student.settings.password') }}" id="stu-otp-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="otp_token" id="stu-otp-token" value="{{ session('otp_token') }}">
                                <div class="mb-3">
                                    <label class="dash-form-label">6-Digit OTP Code <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="otp_code" class="dash-form-control" placeholder="e.g. 123456"
                                        maxlength="6" inputmode="numeric" pattern="\d{6}" required
                                        style="font-size:22px;letter-spacing:10px;font-weight:700;text-align:center;">
                                </div>
                                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                    <button type="submit" class="btn-dash btn-dash-primary">
                                        <i class="bi bi-lock-fill"></i> Confirm &amp; Change Password
                                    </button>
                                    <button type="button" class="btn-dash" style="background:#f1f5f9;color:#64748b;" onclick="studentResetOtpStep()">
                                        <i class="bi bi-arrow-left"></i> Start Over
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                    function studentSendOtp() {
                        var cur  = document.getElementById('stu-cur-pwd').value;
                        var npwd = document.getElementById('stu-new-pwd').value;
                        var conf = document.getElementById('stu-confirm-pwd').value;
                        var err  = document.getElementById('stu-pwd-error');

                        err.style.display = 'none';
                        if (!cur || !npwd || !conf) { err.textContent = 'Please fill in all fields.'; err.style.display='block'; return; }
                        if (npwd.length < 8)        { err.textContent = 'New password must be at least 8 characters.'; err.style.display='block'; return; }
                        if (npwd !== conf)           { err.textContent = 'Passwords do not match.'; err.style.display='block'; return; }

                        var btn = document.getElementById('stu-send-otp-btn');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending…';

                        fetch('{{ route('student.settings.password.otp') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ current_password: cur, password: npwd, password_confirmation: conf })
                        })
                        .then(r => r.json())
                        .then(d => {
                            if (d.success) {
                                document.getElementById('stu-otp-token').value = d.token;
                                document.getElementById('stu-otp-email').textContent = d.email;
                                document.getElementById('stu-pwd-step1').style.display = 'none';
                                document.getElementById('stu-pwd-step2').style.display = 'block';
                            } else {
                                err.textContent = d.message || 'Failed to send OTP.';
                                err.style.display = 'block';
                            }
                        })
                        .catch(() => { err.textContent = 'Network error. Please try again.'; err.style.display = 'block'; })
                        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-envelope-fill"></i> Send OTP to Email'; });
                    }

                    function studentResetOtpStep() {
                        document.getElementById('stu-pwd-step2').style.display = 'none';
                        document.getElementById('stu-pwd-step1').style.display = 'block';
                        document.getElementById('stu-cur-pwd').value = '';
                        document.getElementById('stu-new-pwd').value = '';
                        document.getElementById('stu-confirm-pwd').value = '';
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /dash-main --}}

{{-- Installment Payment Modal (for monthly payments after downpayment) --}}
<div class="modal fade" id="installmentPaymentModal" tabindex="-1" aria-labelledby="installmentPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border-radius:20px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:20px 24px;position:relative;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;top:16px;right:16px;"></button>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:46px;height:46px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-calendar-check-fill" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#fff;line-height:1.2;" id="installmentPaymentModalLabel">Pay Monthly Installment</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.75);margin-top:2px;">Option <span id="inst-modal-option">—</span> &nbsp;·&nbsp; S.Y. {{ $enrollment->school_year ?? '—' }}</div>
                    </div>
                </div>
                {{-- Amount bubble --}}
                <div style="margin-top:16px;background:rgba(255,255,255,0.15);border-radius:12px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.8);text-transform:uppercase;letter-spacing:.5px;">Monthly Amount Due</div>
                    <div style="font-size:22px;font-weight:800;color:#fff;">₱<span id="inst-modal-amount">0</span></div>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:20px 24px;background:#f8faff;max-height:72vh;overflow-y:auto;">

                    {{-- Payment options: Cash or Online --}}
                    <div style="margin-bottom:18px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
                            <i class="bi bi-1-circle-fill me-1" style="color:#1d4ed8;"></i> How will you pay?
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <button type="button" id="inst-card-cash" onclick="selectInstallmentMethod('cash')"
                                style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border:2px solid #e2e8f0;border-radius:12px;background:#fff;cursor:pointer;transition:all .2s;font-family:inherit;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-cash-stack" style="font-size:18px;color:#16a34a;"></i>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#1e3a5f;">Cash</span>
                                <span style="font-size:10px;color:#94a3b8;">Pay at cashier</span>
                            </button>
                            <button type="button" id="inst-card-xendit" onclick="selectInstallmentMethod('xendit')"
                                style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border:2px solid #e2e8f0;border-radius:12px;background:#fff;cursor:pointer;transition:all .2s;font-family:inherit;">
                                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#1a3a6c,#2471a3);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-link-45deg" style="font-size:18px;color:#fff;"></i>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#1e3a5f;">Pay Online</span>
                                <span style="font-size:10px;color:#94a3b8;">Secure link</span>
                            </button>
                        </div>
                    </div>

                    {{-- Cash Info Panel --}}
                    <div id="inst-cash-panel" style="display:none;margin-bottom:18px;">
                        <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:18px;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                                <div style="width:40px;height:40px;border-radius:11px;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-building" style="font-size:18px;color:#fff;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#15803d;">Pay at the School Cashier</div>
                                    <div style="font-size:11px;color:#16a34a;">Bring your payment to the cashier window</div>
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:#166534;">
                                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fff;border-radius:9px;border:1px solid #bbf7d0;">
                                    <i class="bi bi-cash-coin" style="color:#16a34a;font-size:14px;"></i>
                                    <div>Amount: <strong>₱<span id="inst-cash-display-amount">0.00</span></strong></div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fff;border-radius:9px;border:1px solid #bbf7d0;">
                                    <i class="bi bi-clock-fill" style="color:#16a34a;font-size:14px;"></i>
                                    <div>Mon–Fri &nbsp;·&nbsp; 8:00 AM – 4:00 PM</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fff;border-radius:9px;border:1px solid #bbf7d0;">
                                    <i class="bi bi-hash" style="color:#16a34a;font-size:14px;"></i>
                                    <div>Ref: <strong>{{ $enrollment->reference_number ?? 'Present Student ID' }}</strong></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary w-100 mt-3" data-bs-dismiss="modal"
                            style="border-radius:10px;font-weight:600;font-size:13px;">
                            <i class="bi bi-check-circle me-1"></i> Got it — I'll visit the cashier
                        </button>
                    </div>

                    {{-- Xendit Online Panel --}}
                    <div id="inst-xendit-panel" style="display:none;margin-bottom:18px;">
                        <div style="background:linear-gradient(135deg,#1a3a6c,#2471a3);border-radius:14px;padding:18px;color:#fff;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                                <div style="width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-shield-lock-fill" style="font-size:18px;color:#fff;"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:800;">Secure Online Payment</div>
                                    <div style="font-size:11px;opacity:.75;">Amount: ₱<span id="inst-xendit-amount-display">0.00</span></div>
                                </div>
                            </div>
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;opacity:.7;margin-bottom:8px;">Select Method</div>
                            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;" id="inst-xendit-method-grid">
                                @foreach([['gcash','bi-phone-fill','#0070ff','GCash'],['maya','bi-wallet2','#00b09b','Maya'],['grabpay','bi-bag-fill','#00b14f','Grab'],['bank','bi-bank2','#2471a3','Bank'],['otc','bi-shop','#e65100','OTC']] as [$m,$icon,$color,$label])
                                <div class="inst-xmethod-card" data-method="{{ $m }}" onclick="selectInstXenditMethod('{{ $m }}')"
                                    style="border:2px solid rgba(255,255,255,0.2);border-radius:10px;padding:10px 4px;text-align:center;cursor:pointer;transition:all .2s;background:rgba(255,255,255,0.1);">
                                    <div style="width:32px;height:32px;border-radius:8px;background:{{ $color }};margin:0 auto 5px;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi {{ $icon }}" style="font-size:15px;color:#fff;"></i>
                                    </div>
                                    <div style="font-size:10px;font-weight:700;color:#fff;">{{ $label }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div id="inst-xendit-error" style="display:none;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;margin-bottom:10px;font-size:12px;color:#dc2626;"></div>
                        <div id="inst-xendit-result" style="display:none;background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:14px;margin-bottom:10px;">
                            <div style="font-size:12px;font-weight:700;color:#16a34a;margin-bottom:8px;"><i class="bi bi-check-circle-fill me-1"></i> Payment link generated!</div>
                            <input type="text" id="inst-xendit-link-url" readonly style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #86efac;font-size:11px;font-family:monospace;margin-bottom:8px;">
                            <a id="inst-xendit-link-open" href="#" target="_blank"
                                style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#16a34a;color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                                <i class="bi bi-box-arrow-up-right"></i> Open Payment Page
                            </a>
                        </div>
                        <button type="button" id="inst-xendit-generate-btn" onclick="instGenerateXenditLink()" disabled
                            style="width:100%;padding:13px;background:#cbd5e1;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:not-allowed;font-family:inherit;transition:all .2s;">
                            <i class="bi bi-shield-lock me-1"></i> Generate Secure Link
                        </button>
                    </div>

            </div>{{-- /modal body --}}

        </div>
    </div>
</div>

{{-- Change Section Modal --}}
@if(isset($availableSections) && $availableSections->count() > 1 && $enrollment && $enrollment->status === 'enrolled')
<div class="modal fade" id="changeSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg, var(--blue), #1a3a6c); color:#fff; padding:16px 24px;">
                <h5 class="modal-title" style="font-weight:700;">
                    <i class="bi bi-arrow-left-right me-2"></i>Change Section
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="font-size:13px; color:#666; margin-bottom:16px;">
                    Select a section to transfer to. You can only move to a section that has available slots.
                </p>
                <div id="sectionCards" style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($availableSections as $sec)
                    @php $isFull = $sec->current_enrollment >= $sec->max_students; @endphp
                    @php $isCurrent = $enrollment->section === $sec->name; @endphp
                    <div class="section-pick-card {{ $isCurrent ? 'current' : '' }} {{ $isFull ? 'full' : '' }}"
                         onclick="{{ (!$isFull && !$isCurrent) ? 'confirmSectionChange(' . $sec->id . ', \'' . addslashes($sec->name) . '\')' : '' }}"
                         style="border:2px solid {{ $isCurrent ? 'var(--blue)' : ($isFull ? '#dee2e6' : '#e2e8f0') }}; border-radius:12px; padding:16px; cursor:{{ ($isFull || $isCurrent) ? 'default' : 'pointer' }}; background:{{ $isCurrent ? '#f0f4ff' : ($isFull ? '#f8f9fa' : '#fff') }}; transition:all .2s;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-weight:700; font-size:15px; color:{{ $isFull ? '#adb5bd' : 'var(--blue)' }};">
                                    {{ $sec->name }}
                                </div>
                                <div style="font-size:12px; color:#888; margin-top:3px;">
                                    {{ $sec->grade_level }}
                                    @if($sec->teacher) &nbsp;·&nbsp; Adviser: {{ $sec->teacher->name }} @endif
                                    @if($sec->room_number) &nbsp;·&nbsp; Room {{ $sec->room_number }} @endif
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0; margin-left:12px;">
                                @if($isCurrent)
                                    <span class="badge" style="background:var(--blue); color:#fff; font-size:11px;">Current</span>
                                @elseif($isFull)
                                    <span class="badge bg-secondary" style="font-size:11px;">Full</span>
                                @else
                                    <span style="font-size:12px; color:#28a745; font-weight:600;">
                                        {{ $sec->max_students - $sec->current_enrollment }} slot{{ ($sec->max_students - $sec->current_enrollment) !== 1 ? 's' : '' }} left
                                    </span>
                                @endif
                                <div style="font-size:11px; color:#aaa; margin-top:2px;">
                                    {{ $sec->current_enrollment }}/{{ $sec->max_students }} students
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div id="changeSectionMsg" style="display:none; margin-top:14px;"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Payment Details Modal --}}
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg, var(--blue), #1a3a6c); color:#fff; padding:16px 24px;">
                <h5 class="modal-title" id="paymentDetailsModalLabel" style="font-weight:700;">
                    <i class="bi bi-receipt-cutoff me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:0;">
                {{-- Summary Cards --}}
                <div style="padding:20px 24px 12px; background:#f8f9fa;">
                    <div class="row g-3" id="pd-summary-cards">
                        {{-- Filled by JS --}}
                    </div>
                </div>

                {{-- Fee Breakdown --}}
                <div style="padding:16px 24px; border-bottom:1px solid #eee;">
                    <h6 style="font-size:13px; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                        <i class="bi bi-list-check me-1"></i>Fee Breakdown
                    </h6>
                    <div id="pd-fee-breakdown">
                        {{-- Filled by JS --}}
                    </div>
                </div>

                {{-- Installment Schedule --}}
                <div id="pd-installment-section" style="padding:16px 24px; border-bottom:1px solid #eee; display:none;">
                    <h6 style="font-size:13px; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                        <i class="bi bi-calendar-event me-1"></i>Installment Schedule
                    </h6>
                    <div id="pd-installment-schedule">
                        {{-- Filled by JS --}}
                    </div>
                </div>

                {{-- Payment History --}}
                <div style="padding:16px 24px;">
                    <h6 style="font-size:13px; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                        <i class="bi bi-clock-history me-1"></i>Payment History
                    </h6>
                    <div id="pd-payment-history">
                        {{-- Filled by JS --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #eee; padding:12px 24px;">
                <button type="button" class="btn-dash btn-dash-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@php
    $gradeLevelForJS = $enrollment ? ($enrollment->grade_level ?? '') : '';
    $jsEnrollmentData = null;
    $jsPaymentHistory = [];
    $jsPaymentInstallments = [];
    $jsPaymentSummary = null;
    if ($enrollment) {
        $jsEnrollmentData = [
            'id' => $enrollment->id,
            'total_fee' => (float) ($enrollment->total_fee ?? 0),
            'payment_amount' => (float) ($enrollment->payment_amount ?? 0),
            'remaining_balance' => (float) ($enrollment->remaining_balance ?? max(0, ($enrollment->total_fee ?? 0) - ($enrollment->payment_amount ?? 0))),
            'payment_status' => $enrollment->payment_status ?? 'pending',
            'payment_type' => $enrollment->payment_type ?? 'full',
            'payment_option' => $enrollment->payment_option ?? '',
            'downpayment_amount' => (float) ($enrollment->downpayment_amount ?? 0),
            'monthly_amount' => (float) ($enrollment->monthly_amount ?? 0),
            'installment_number' => (int) ($enrollment->installment_number ?? 0),
            'installment_schedule' => (int) ($enrollment->installment_schedule ?? 30),
            'next_installment_date' => $enrollment->next_installment_date ? $enrollment->next_installment_date->format('Y-m-d') : null,
            'penalty_amount' => (float) ($enrollment->penalty_amount ?? 0),
            'payment_breakdown' => $enrollment->payment_breakdown ?? [],
            'reference_number' => $enrollment->reference_number ?? '',
            'school_year' => $enrollment->school_year ?? '',
            'account_blocked' => $enrollment->account_blocked ?? false,
        ];
        $jsPaymentHistory = \App\Models\StudentDocument::where('enrollment_id', $enrollment->id)
            ->where('document_type', 'payment_screenshot')
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'status', 'created_at', 'file_path'])
            ->toArray();
        
        // Payment installments for month-by-month tracking
        if ($paymentInstallments && $paymentInstallments->count() > 0) {
            $jsPaymentInstallments = $paymentInstallments->map(function($inst) {
                return [
                    'id' => $inst->id,
                    'month_name' => $inst->month_name,
                    'due_date' => $inst->due_date ? $inst->due_date->format('Y-m-d') : null,
                    'amount' => (float) $inst->amount,
                    'late_fee' => (float) $inst->late_fee,
                    'amount_paid' => (float) $inst->amount_paid,
                    'status' => $inst->status,
                    'weeks_overdue' => $inst->weeks_overdue,
                    'total_due' => (float) ($inst->amount + $inst->late_fee),
                ];
            })->toArray();
        }
        
        if ($paymentSummary) {
            $jsPaymentSummary = [
                'total_installments' => $paymentSummary['total_installments'],
                'paid' => $paymentSummary['paid'],
                'pending' => $paymentSummary['pending'],
                'overdue' => $paymentSummary['overdue'],
                'total_late_fees' => $paymentSummary['total_late_fees'],
                'total_paid' => $paymentSummary['total_paid'],
                'total_pending' => $paymentSummary['total_pending'],
            ];
        }
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const studentGradeLevel = '{{ $gradeLevelForJS }}';
    const enrollmentData = @json($jsEnrollmentData);
    const paymentHistory = @json($jsPaymentHistory);
    const paymentInstallments = @json($jsPaymentInstallments);
    const paymentSummary = @json($jsPaymentSummary);
</script>
<script>
    // Fee settings cache (cleared on each page load to always get fresh data)
    let feeSettingsCache = null;
    let feeDataCache = null; // Store calculated fee data for auto-fill

    // Load fee settings from API
    async function loadFeeSettings() {
        // Always fetch fresh data from the API
        try {
            const response = await fetch('/api/fees/settings?t=' + Date.now(), {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            const data = await response.json();
            if (data.success && data.settings) {
                // data.settings is a flat object { tuition: "7505.00", misc: "2800.00", ... }
                // Map DB column names to the old key format used by calculateFeeFallback
                const s = data.settings;
                feeSettingsCache = {
                    fee_tuition: s.tuition,
                    fee_misc: s.misc,
                    fee_insurance: s.insurance,
                    fee_electric: s.electric,
                    fee_books_nursery: s.books_nursery,
                    fee_books_grade1: s.books_grade1,
                    fee_books_grade3: s.books_grade3,
                    fee_books_grade4: s.books_grade4,
                    payment_option_a_discount: s.option_a_discount,
                    fee_optb_monthly_tuition: s.optb_monthly_tuition,
                    fee_optb_monthly_electric: s.optb_monthly_electric,
                    dp_b_nursery: s.optb_dp_nursery,
                    dp_b_kinder: s.optb_dp_kinder,
                    dp_b_grade1: s.optb_dp_grade1,
                    dp_b_grade3: s.optb_dp_grade3,
                    dp_b_grade4: s.optb_dp_grade4,
                    fee_optc_monthly_tuition: s.optc_monthly_tuition,
                    fee_optc_monthly_misc: s.optc_monthly_misc,
                    fee_optc_monthly_electric: s.optc_monthly_electric,
                    dp_c_grade1: s.optc_dp_grade1,
                    dp_c_grade3: s.optc_dp_grade3,
                    dp_c_grade4: s.optc_dp_grade4,
                    fee_optd_monthly_tuition: s.optd_monthly_tuition,
                    fee_optd_monthly_misc: s.optd_monthly_misc,
                    fee_optd_monthly_electric: s.optd_monthly_electric,
                    dp_d_nursery: s.optd_dp_nursery,
                    dp_d_kinder: s.optd_dp_kinder
                };
                return feeSettingsCache;
            }
        } catch (error) {
            console.error('Failed to load fee settings:', error);
        }
        return null;
    }

    // ── Switch sections via sidebar links ──
    const sections = ['info', 'grades', 'schedule', 'enrollment', 'payment', 'announcements', 'settings'];
    // ... rest of the code remains the same ...
    // ── Auto-capitalize: first letter of every word in text inputs ──
    (function() {
        var skipNames = ['email','password','search','lrn','reference'];
        function capWords(el) {
            if (!el || el.type !== 'text') return;
            var name = (el.name || el.id || '').toLowerCase();
            if (skipNames.some(function(s){ return name.includes(s); })) return;
            var pos = el.selectionStart;
            el.value = el.value.replace(/\b\w/g, function(c){ return c.toUpperCase(); });
            try { el.setSelectionRange(pos, pos); } catch(e){}
        }
        // Apply to student portal edit forms only
        var formIds = ['form-personal','form-guardian','form-address','form-health'];
        document.addEventListener('input', function(e) {
            var el = e.target;
            var inForm = formIds.some(function(id){
                var f = document.getElementById(id);
                return f && f.contains(el);
            });
            if (inForm) capWords(el);
        });
    })();

    // ── Student Info Tab Switcher ──
    function switchInfoTab(tab) {
        const tabs = ['personal','guardian','address','enrollment','health','payment','documents'];
        tabs.forEach(function(t) {
            var pane = document.getElementById('infoTab-' + t);
            var btn  = document.getElementById('infoTabBtn-' + t);
            if (!pane || !btn) return;
            var active = t === tab;
            pane.style.display   = active ? '' : 'none';
            btn.style.background = active ? 'var(--blue)' : 'transparent';
            btn.style.color      = active ? '#fff' : '#666';
        });
        // Cancel any open edit form when switching tabs
        ['personal','guardian','address','health'].forEach(function(t) {
            if (t !== tab) cancelEditInfoTab(t);
        });
    }

    // ── Inline Edit Toggle ──
    function editInfoTab(tab) {
        var view   = document.getElementById('view-' + tab);
        var form   = document.getElementById('form-' + tab);
        var editBtn   = document.getElementById('editBtn-' + tab);
        var cancelBtn = document.getElementById('cancelBtn-' + tab);
        if (!view || !form) return;
        view.style.display      = 'none';
        form.style.display      = '';
        editBtn.style.display   = 'none';
        cancelBtn.style.display = 'inline-flex';
    }

    function cancelEditInfoTab(tab) {
        var view   = document.getElementById('view-' + tab);
        var form   = document.getElementById('form-' + tab);
        var editBtn   = document.getElementById('editBtn-' + tab);
        var cancelBtn = document.getElementById('cancelBtn-' + tab);
        if (!view || !form) return;
        view.style.display      = '';
        form.style.display      = 'none';
        editBtn.style.display   = 'inline-flex';
        cancelBtn.style.display = 'none';
    }

    function updateAgeDisplay() {
        const input = document.getElementById('edit-birthdate-input');
        const display = document.getElementById('edit-age-display');
        if (!input || !display || !input.value) { if (display) display.textContent = '—'; return; }
        const today = new Date(), b = new Date(input.value);
        let age = today.getFullYear() - b.getFullYear();
        if (today.getMonth() < b.getMonth() || (today.getMonth() === b.getMonth() && today.getDate() < b.getDate())) age--;
        display.textContent = age >= 0 ? age + ' yrs old' : '—';
    }

    function showSection(name) {
        sections.forEach(s => {
            const el = document.getElementById('section-' + s);
            if (el) el.style.display = s === name ? '' : 'none';
            const nav = document.getElementById('nav-' + s);
            if (nav) {
                nav.classList.toggle('active', s === name);
            }
        });
        window.scrollTo(0, 0);
        applySectionSkeleton(name);
        // Load data when switching sections
        if (name === 'grades') {
            loadGrades(currentGradeTerm, document.querySelector('.q-tab.active'));
            loadGWA();
        }
        if (name === 'schedule') {
            loadSchedule();
        }
        return false;
    }

    // ── Load Grades from API ──
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

    var currentGradeTerm = 1;

    function loadGrades(quarter, tabEl) {
        currentGradeTerm = quarter || 1;

        // If no button passed (e.g. from SY dropdown), keep whichever tab is already active
        if (!tabEl) {
            tabEl = document.querySelector('.q-tab.active') || document.querySelectorAll('.q-tab')[currentGradeTerm - 1];
        }

        // Update term tab styles
        document.querySelectorAll('.q-tab').forEach(function(t) {
            var isActive = t === tabEl;
            t.style.background    = isActive ? 'var(--blue)' : '#fff';
            t.style.color         = isActive ? '#fff' : '#555';
            t.style.borderColor   = isActive ? 'var(--blue)' : '#d1d5db';
            t.classList.toggle('active', isActive);
        });

        var termLabels = {1: '1st Term', 2: '2nd Term', 3: '3rd Term'};
        var titleEl = document.getElementById('gradesTitle');
        if (titleEl) {
            var dashIdx = titleEl.textContent.indexOf('—');
            var gradePart = dashIdx !== -1 ? titleEl.textContent.slice(dashIdx).trim() : '';
            titleEl.textContent = (termLabels[quarter] || 'Term ' + quarter) + ' Grades ' + gradePart;
        }

        // Read school year from filter
        var syFilter = document.getElementById('gradesSYFilter');
        var schoolYear = syFilter ? syFilter.value : '';

        // Update subtitle
        var syLabel = document.getElementById('gradesSYLabel');
        if (syLabel && schoolYear) syLabel.textContent = 'S.Y. ' + schoolYear;

        var tbody = document.getElementById('gradesTableBody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;"><div style="color:var(--muted);"><i class="bi bi-hourglass-split" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading grades...</div></td></tr>';

        var gradeUrl = '/api/student/grades?quarter=' + quarter;
        if (schoolYear) gradeUrl += '&school_year=' + encodeURIComponent(schoolYear);

        fetch(gradeUrl, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function(r) { if (!r.ok) throw new Error('Failed'); return r.json(); })
        .then(function(res) {
            var subjects  = res.data.subjects  || [];
            var hasGrades = res.data.has_grades;
            var termLabel = { 1:'Term 1', 2:'Term 2', 3:'Term 3' }[quarter] || ('Term ' + quarter);
            var syLabel   = res.data.school_year || schoolYear || '';

            // Whole-year failing notice — shown regardless of which term is selected,
            // since it reflects the year average, not just this quarter.
            var failingEl  = document.getElementById('gradesFailingNotice');
            var failingList = res.data.failing_subjects || [];
            if (failingEl) {
                if (failingList.length > 0) {
                    document.getElementById('gradesFailingList').textContent =
                        failingList.map(function(f) { return f.subject_name + ' (' + f.average + ')'; }).join(', ');
                    failingEl.style.display = 'block';
                } else {
                    failingEl.style.display = 'none';
                }
            }

            if (subjects.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;"><div style="color:var(--muted);"><i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>No subjects found for your grade level.</div></td></tr>';
                return;
            }

            const pendingCount = res.pending_count || 0;

            // Show "pending approval" banner when teacher submitted but admin hasn't approved yet
            if (!hasGrades && pendingCount > 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:48px 20px;">'
                    + '<i class="bi bi-hourglass-split" style="font-size:40px;display:block;margin-bottom:12px;color:#f59e0b;opacity:0.8;"></i>'
                    + '<div style="font-weight:700;font-size:14px;color:#92400e;margin-bottom:6px;">Grades Pending Admin Approval</div>'
                    + '<div style="font-size:12px;color:#94a3b8;">Your teacher has submitted grades for <strong>' + termLabel + '</strong>'
                    + (syLabel ? ', S.Y. ' + syLabel : '') + '.<br>They are currently being reviewed by the administrator.</div>'
                    + '</td></tr>';
                return;
            }

            // Show "no grades yet" banner when teacher hasn't submitted for this term/year
            if (!hasGrades) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:48px 20px;">'
                    + '<i class="bi bi-clipboard-x" style="font-size:40px;display:block;margin-bottom:12px;color:#c9d3e0;"></i>'
                    + '<div style="font-weight:700;font-size:14px;color:#64748b;margin-bottom:6px;">No grades submitted yet</div>'
                    + '<div style="font-size:12px;color:#94a3b8;">Grades for <strong>' + termLabel + '</strong>'
                    + (syLabel ? ', S.Y. ' + syLabel : '') + ' have not been encoded by your teacher yet.</div>'
                    + '</td></tr>';
                return;
            }

            var isDesc = res.data.is_descriptive || false;
            var DESC_COLORS = { O:'#1a7a44', VS:'#1565c0', S:'#555', FS:'#b45309', DNME:'#c0392b' };
            var DESC_BG     = { O:'#e8f8f0', VS:'#e3f0ff', S:'#f5f5f5', FS:'#fff8ec', DNME:'#fdecea' };
            var DESC_BORDER = { O:'#27ae60', VS:'#1976d2', S:'#bbb', FS:'#f5a623', DNME:'#e74c3c' };

            var html = '';
            subjects.forEach(function(s) {
                var gradeDisplay, remarksDisplay, rowBg, hasGrade;

                if (isDesc) {
                    var dg = s.descriptive_grade;
                    hasGrade = dg && dg !== '';
                    rowBg = hasGrade
                        ? (dg === 'DNME' ? 'background:#fff8f8;' : 'background:#f6fef9;')
                        : 'background:#fafafa;';
                    gradeDisplay = hasGrade
                        ? '<span style="font-size:15px;font-weight:700;color:' + (DESC_COLORS[dg] || '#555') + ';">' + dg + '</span>'
                        : '<span style="color:#ccc;font-size:13px;">—</span>';
                    remarksDisplay = hasGrade
                        ? '<span style="background:' + (DESC_BG[dg]||'#f5f5f5') + ';color:' + (DESC_COLORS[dg]||'#555') + ';border:1px solid ' + (DESC_BORDER[dg]||'#ccc') + ';border-radius:12px;padding:3px 10px;font-size:11px;font-weight:600;">' + (s.remarks || dg) + '</span>'
                        : '<span style="color:#bbb;font-size:11px;font-style:italic;">Not yet encoded</span>';
                } else {
                    var fg = s.final_grade;
                    hasGrade = fg !== null && fg !== undefined && fg !== '';
                    rowBg = hasGrade
                        ? (fg >= 75 ? 'background:#f6fef9;' : (fg >= 70 ? 'background:#fffdf0;' : 'background:#fff8f8;'))
                        : 'background:#fafafa;';
                    gradeDisplay = hasGrade
                        ? '<span style="font-size:16px;font-weight:700;color:' + (fg >= 75 ? '#1a7a44' : (fg >= 70 ? '#b45309' : '#c0392b')) + ';">' + parseFloat(fg).toFixed(0) + '</span>'
                        : '<span style="color:#ccc;font-size:13px;">—</span>';
                    if (hasGrade) {
                        if (fg >= 75)      remarksDisplay = '<span style="background:#e8f8f0;color:#1a7a44;border:1px solid #27ae60;border-radius:12px;padding:3px 10px;font-size:11px;font-weight:600;">Passed</span>';
                        else if (fg >= 70) remarksDisplay = '<span style="background:#fff8ec;color:#b45309;border:1px solid #f5a623;border-radius:12px;padding:3px 10px;font-size:11px;font-weight:600;">Passed w/ Remedial</span>';
                        else               remarksDisplay = '<span style="background:#fdecea;color:#c0392b;border:1px solid #e74c3c;border-radius:12px;padding:3px 10px;font-size:11px;font-weight:600;">Failed</span>';
                    } else {
                        remarksDisplay = '<span style="color:#bbb;font-size:11px;font-style:italic;">Not yet encoded</span>';
                    }
                }

                html += '<tr style="' + rowBg + '">'
                    + '<td style="font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap;">' + (s.code || '—') + '</td>'
                    + '<td style="font-weight:600;">' + (s.name || '—') + '</td>'
                    + '<td style="font-size:12px;color:#555;"><i class="bi bi-person-fill" style="color:var(--blue);margin-right:4px;font-size:11px;"></i>' + (s.teacher || '—') + '</td>'
                    + '<td style="text-align:center;">' + gradeDisplay + '</td>'
                    + '<td style="text-align:center;">' + remarksDisplay + '</td>'
                    + '</tr>';
            });

            var total   = subjects.length;
            var encoded = subjects.filter(function(s) {
                return isDesc ? (s.descriptive_grade && s.descriptive_grade !== '')
                              : (s.final_grade !== null && s.final_grade !== '' && s.final_grade !== undefined);
            }).length;
            var passed  = isDesc
                ? subjects.filter(function(s) { return s.descriptive_grade && s.descriptive_grade !== 'DNME'; }).length
                : subjects.filter(function(s) { return s.final_grade >= 75; }).length;

            html += '<tr style="background:#f8f9fa;border-top:2px solid #e5e7eb;">'
                + '<td colspan="3" style="font-size:12px;color:var(--muted);padding:10px 12px;">'
                + total + ' subject(s) &nbsp;&middot;&nbsp; ' + encoded + ' grade(s) encoded'
                + '</td>'
                + '<td colspan="2" style="text-align:center;font-size:12px;font-weight:600;color:#1a7a44;padding:10px 12px;">'
                + (isDesc ? (encoded > 0 ? passed + ' / ' + encoded + ' satisfactory' : '')
                          : (encoded > 0 ? passed + ' / ' + encoded + ' passed' : ''))
                + '</td>'
                + '</tr>';

            tbody.innerHTML = html;
        })
        .catch(function(err) {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;"><div style="color:var(--muted);"><i class="bi bi-exclamation-triangle" style="font-size:24px;display:block;margin-bottom:8px;"></i>Failed to load grades. Please try again.</div></td></tr>';
        });
    }

    // ── GWA: (Term1 avg + Term2 avg + Term3 avg) / 3 ──
    function loadGWA() {
        var gwaCard = document.getElementById('gwa-card');
        if (!gwaCard) return; // Nursery/Kinder — no GWA card

        var syFilter   = document.getElementById('gradesSYFilter');
        var schoolYear = syFilter ? syFilter.value : '';

        function fetchTermAvg(term) {
            var url = '/api/student/grades?quarter=' + term;
            if (schoolYear) url += '&school_year=' + encodeURIComponent(schoolYear);
            return fetch(url, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                var subjects = (res.data && res.data.subjects) || [];
                var numeric  = subjects.filter(function(s) {
                    return s.final_grade !== null && s.final_grade !== undefined && s.final_grade !== '';
                });
                if (!numeric.length) return null;
                var sum = numeric.reduce(function(acc, s) { return acc + parseFloat(s.final_grade); }, 0);
                return Math.round((sum / numeric.length) * 100) / 100;
            })
            .catch(function() { return null; });
        }

        Promise.all([fetchTermAvg(1), fetchTermAvg(2), fetchTermAvg(3)])
        .then(function(avgs) {
            var labels = ['gwa-t1','gwa-t2','gwa-t3'];
            var validAvgs = [];

            avgs.forEach(function(avg, i) {
                var el = document.getElementById(labels[i]);
                if (el) {
                    if (avg !== null) {
                        el.textContent = avg.toFixed(2);
                        validAvgs.push(avg);
                    } else {
                        el.textContent = '—';
                    }
                }
            });

            var gwaEl     = document.getElementById('gwa-value');
            var remarkEl  = document.getElementById('gwa-remark');
            if (!gwaEl) return;

            if (!validAvgs.length) {
                gwaEl.textContent   = '—';
                if (remarkEl) remarkEl.textContent = 'No grades encoded yet';
                return;
            }

            var gwa = validAvgs.reduce(function(a, b) { return a + b; }, 0) / validAvgs.length;
            gwa = Math.round(gwa * 100) / 100;
            gwaEl.textContent = gwa.toFixed(2);

            if (remarkEl) {
                var remark = gwa >= 90 ? 'Outstanding' :
                             gwa >= 85 ? 'Very Satisfactory' :
                             gwa >= 80 ? 'Satisfactory' :
                             gwa >= 75 ? 'Fairly Satisfactory' : 'Did Not Meet Expectations';
                remarkEl.textContent = remark + (validAvgs.length < 3 ? ' · ' + validAvgs.length + '/3 terms encoded' : '');
            }
        });
    }

    // Update payment option card labels from loaded fee settings
    function updatePaymentCardLabels() {
        const settings = feeSettingsCache || {};
        const gradeLevel = studentGradeLevel.toLowerCase().replace(/\s/g, '');

        const tuition = parseFloat(settings.fee_tuition) || 0;
        const misc = parseFloat(settings.fee_misc) || 0;
        const insurance = parseFloat(settings.fee_insurance) || 0;
        const electric = parseFloat(settings.fee_electric) || 0;

        const booksMap = {
            'nursery': parseFloat(settings.fee_books_nursery) || 0,
            'kindergarten': parseFloat(settings.fee_books_nursery) || 0,
            'grade1': parseFloat(settings.fee_books_grade1) || 0,
            'grade2': parseFloat(settings.fee_books_grade1) || 0,
            'grade3': parseFloat(settings.fee_books_grade3) || 0,
            'grade4': parseFloat(settings.fee_books_grade4) || 0,
            'grade5': parseFloat(settings.fee_books_grade4) || 0,
            'grade6': parseFloat(settings.fee_books_grade4) || 0
        };
        const books = booksMap[gradeLevel] || 0;
        const total = tuition + misc + books + insurance + electric;

        if (total === 0) return; // Don't update if no data loaded yet

        // Option A: Cash total
        const discount = parseFloat(settings.payment_option_a_discount) || 0;
        const optATotal = total - discount;
        const optAEl = document.getElementById('opt-a-total');
        if (optAEl) optAEl.textContent = '₱' + optATotal.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 0});

        // Option B: Monthly (Tuition/Mo + Electric/Mo)
        const optBTuitionMo = parseFloat(settings.fee_optb_monthly_tuition) || 0;
        const optBElectricMo = parseFloat(settings.fee_optb_monthly_electric) || 0;
        const optBMonthly = optBTuitionMo + optBElectricMo;
        const optBEl = document.getElementById('opt-b-monthly');
        if (optBEl) optBEl.textContent = '₱' + optBMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';

        // Option C: Monthly (Tuition/Mo + Misc/Mo + Electric/Mo)
        const optCTuitionMo = parseFloat(settings.fee_optc_monthly_tuition) || 0;
        const optCMiscMo = parseFloat(settings.fee_optc_monthly_misc) || 0;
        const optCElectricMo = parseFloat(settings.fee_optc_monthly_electric) || 0;
        const optCMonthly = optCTuitionMo + optCMiscMo + optCElectricMo;
        const optCEl = document.getElementById('opt-c-monthly');
        if (optCEl) optCEl.textContent = '₱' + optCMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';

        // Option D: Monthly (Tuition/Mo + Misc/Mo + Electric/Mo)
        const optDTuitionMo = parseFloat(settings.fee_optd_monthly_tuition) || 0;
        const optDMiscMo = parseFloat(settings.fee_optd_monthly_misc) || 0;
        const optDElectricMo = parseFloat(settings.fee_optd_monthly_electric) || 0;
        const optDMonthly = optDTuitionMo + optDMiscMo + optDElectricMo;
        const optDEl = document.getElementById('opt-d-monthly');
        if (optDEl) optDEl.textContent = '₱' + optDMonthly.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/mo';
    }

    // Payment option selection
    let selectedPaymentOption = null;
    let cashSubmitTimeout = null;

    async function selectPaymentOption(option) {
        selectedPaymentOption = option;
        document.querySelectorAll('[id^="card-opt-"]').forEach(card => {
            card.classList.remove('selected');
        });
        document.getElementById('card-opt-' + option.toLowerCase()).classList.add('selected');
        document.getElementById('selected-payment-option').value = option;

        // Calculate and show breakdown (await to ensure hidden fields are populated)
        await calculatePaymentBreakdown(option);

        // Show Pay button
        document.getElementById('pay-button-container').style.display = 'block';
    }

    function setPayStep(step) {
        // Update step indicator and show/hide panels
        const panels = ['payPanelStep1', 'payPanelStep2', 'payment-forms'];
        panels.forEach((id, i) => {
            const el = document.getElementById(id);
            if (el) el.style.display = (i === step - 1) ? 'block' : 'none';
        });
        ['payStepA','payStepB','payStepC'].forEach((id, i) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('active','done');
            if (i + 1 < step) el.classList.add('done');
            else if (i + 1 === step) el.classList.add('active');
        });
        ['payLine1','payLine2'].forEach((id, i) => {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('done', step > i + 1);
        });
    }

    function showPaymentMethodSelection() {
        setPayStep(2);
        document.getElementById('payStepIndicator')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function backToStep1() {
        setPayStep(1);
    }

    function backToStep2() {
        if (cashSubmitTimeout) { clearTimeout(cashSubmitTimeout); cashSubmitTimeout = null; }
        setPayStep(2);
    }

    function switchGcashTab(tab) {
        var numPanel = document.getElementById('gcash-tab-number');
        var qrPanel  = document.getElementById('gcash-tab-qr');
        var numBtn   = document.getElementById('gcash-tab-btn-number');
        var qrBtn    = document.getElementById('gcash-tab-btn-qr');
        if (!numPanel) return;
        if (tab === 'number') {
            numPanel.style.display = '';
            qrPanel.style.display  = 'none';
            numBtn.style.cssText   = 'flex:1;padding:10px 0;background:#fff;border:none;border-bottom:2px solid #1d4ed8;font-size:12px;font-weight:700;color:#1d4ed8;cursor:pointer;transition:all .2s;';
            qrBtn.style.cssText    = 'flex:1;padding:10px 0;background:none;border:none;border-bottom:2px solid transparent;font-size:12px;font-weight:700;color:#94a3b8;cursor:pointer;transition:all .2s;';
        } else {
            numPanel.style.display = 'none';
            qrPanel.style.display  = '';
            numBtn.style.cssText   = 'flex:1;padding:10px 0;background:none;border:none;border-bottom:2px solid transparent;font-size:12px;font-weight:700;color:#94a3b8;cursor:pointer;transition:all .2s;';
            qrBtn.style.cssText    = 'flex:1;padding:10px 0;background:#fff;border:none;border-bottom:2px solid #1d4ed8;font-size:12px;font-weight:700;color:#1d4ed8;cursor:pointer;transition:all .2s;';
        }
    }

    function copyGcashNumber(num, iconId, labelId) {
        iconId  = iconId  || 'gcash-copy-icon';
        labelId = labelId || 'gcash-copy-label';
        var done = function() { _gcashCopied(iconId, labelId); };
        if (!navigator.clipboard) {
            var ta = document.createElement('textarea');
            ta.value = num; document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); document.body.removeChild(ta);
            done(); return;
        }
        navigator.clipboard.writeText(num).then(done);
    }
    function _gcashCopied(iconId, labelId) {
        var icon  = document.getElementById(iconId);
        var label = document.getElementById(labelId);
        if (!icon) return;
        icon.className = 'bi bi-check-lg';
        label.textContent = 'Copied!';
        setTimeout(function() {
            icon.className = 'bi bi-copy';
            label.textContent = 'Copy';
        }, 2000);
    }

    async function fetchFeeCalculation(option) {
        try {
            console.log(`Student portal fetching fees for ${studentGradeLevel}, option ${option}...`);
            const response = await fetch('/api/fees/calculate', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    grade_level: studentGradeLevel,
                    payment_option: option
                })
            });
            
            console.log('Student portal API status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Student portal API error:', errorText);
                throw new Error('Failed to fetch fee calculation');
            }
            
            const result = await response.json();
            console.log('Student portal API result:', result);
            return result.success ? result.data : null;
        } catch (error) {
            console.error('Fee API error:', error);
            return null;
        }
    }

    function calculateFeeFallback(option) {
        const gradeLevel = studentGradeLevel.toLowerCase().replace(/\s/g, '');
        const settings = feeSettingsCache || {};

        const tuition = parseFloat(settings.fee_tuition) || 0;
        const misc = parseFloat(settings.fee_misc) || 0;
        const insurance = parseFloat(settings.fee_insurance) || 0;
        const electric = parseFloat(settings.fee_electric) || 0;

        const booksMap = {
            'nursery': parseFloat(settings.fee_books_nursery) || 0,
            'kindergarten': parseFloat(settings.fee_books_nursery) || 0,
            'grade1': parseFloat(settings.fee_books_grade1) || 0,
            'grade2': parseFloat(settings.fee_books_grade1) || 0,
            'grade3': parseFloat(settings.fee_books_grade3) || 0,
            'grade4': parseFloat(settings.fee_books_grade4) || 0,
            'grade5': parseFloat(settings.fee_books_grade4) || 0,
            'grade6': parseFloat(settings.fee_books_grade4) || 0
        };

        const books = booksMap[gradeLevel] || 0;
        const total = tuition + misc + books + insurance + electric;

        if (total === 0) return null; // No fee data loaded

        const rates = { total, tuition, misc, books, insurance, electric };
        let downpayment = 0, monthly = 0, discount = 0;

        if (option === 'A') {
            discount = parseFloat(settings.payment_option_a_discount) || 0;
            const totalPayable = total - discount;
            return {
                components: rates,
                base_total: total,
                discount: discount,
                downpayment: 0,
                monthly_payment: 0,
                months: 0,
                total_payable: totalPayable,
                option: 'A'
            };
        } else if (option === 'B') {
            const downpayments = {
                'nursery': parseFloat(settings.dp_b_nursery) || 0,
                'kindergarten': parseFloat(settings.dp_b_kinder) || 0,
                'grade1': parseFloat(settings.dp_b_grade1) || 0,
                'grade2': parseFloat(settings.dp_b_grade1) || 0,
                'grade3': parseFloat(settings.dp_b_grade3) || 0,
                'grade4': parseFloat(settings.dp_b_grade4) || 0,
                'grade5': parseFloat(settings.dp_b_grade4) || 0,
                'grade6': parseFloat(settings.dp_b_grade4) || 0
            };
            downpayment = downpayments[gradeLevel] || 0;
            const tuitionMo = parseFloat(settings.fee_optb_monthly_tuition) || 0;
            const electricMo = parseFloat(settings.fee_optb_monthly_electric) || 0;
            monthly = tuitionMo + electricMo;
            const totalPayable = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: total,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: totalPayable,
                option: 'B'
            };
        } else if (option === 'C') {
            const downpayments = {
                'grade1': parseFloat(settings.dp_c_grade1) || 0,
                'grade2': parseFloat(settings.dp_c_grade1) || 0,
                'grade3': parseFloat(settings.dp_c_grade3) || 0,
                'grade4': parseFloat(settings.dp_c_grade4) || 0,
                'grade5': parseFloat(settings.dp_c_grade4) || 0,
                'grade6': parseFloat(settings.dp_c_grade4) || 0
            };
            downpayment = downpayments[gradeLevel] || 0;
            const tuitionMo = parseFloat(settings.fee_optc_monthly_tuition) || 0;
            const miscMo = parseFloat(settings.fee_optc_monthly_misc) || 0;
            const electricMo = parseFloat(settings.fee_optc_monthly_electric) || 0;
            monthly = tuitionMo + miscMo + electricMo;
            const totalPayable = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: total,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: totalPayable,
                option: 'C'
            };
        } else if (option === 'D') {
            const downpayments = {
                'nursery': parseFloat(settings.dp_d_nursery) || 0,
                'kindergarten': parseFloat(settings.dp_d_kinder) || 0
            };
            downpayment = downpayments[gradeLevel] || 0;
            const tuitionMo = parseFloat(settings.fee_optd_monthly_tuition) || 0;
            const miscMo = parseFloat(settings.fee_optd_monthly_misc) || 0;
            const electricMo = parseFloat(settings.fee_optd_monthly_electric) || 0;
            monthly = tuitionMo + miscMo + electricMo;
            const totalPayable = downpayment + (monthly * 9);
            return {
                components: rates,
                base_total: total,
                discount: 0,
                downpayment: downpayment,
                monthly_payment: parseFloat(monthly.toFixed(2)),
                months: 9,
                total_payable: totalPayable,
                option: 'D'
            };
        }
        return null;
    }

    async function calculatePaymentBreakdown(option) {
        let feeData = await fetchFeeCalculation(option);
        
        if (!feeData) {
            console.log('Using fallback fee calculation');
            feeData = calculateFeeFallback(option);
        }
        
        if (!feeData) {
            alert('Failed to calculate fees. Please try again.');
            return;
        }
        
        // Store for auto-fill in selectPaymentMethod
        feeDataCache = feeData;
        
        const c = feeData.components || feeData;
        let breakdownHTML = '';
        
        const feeComponentsHTML = `
            <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #e0e0e0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span>Tuition Fee:</span><span>₱${(c.tuition || 0).toLocaleString()}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span>Miscellaneous:</span><span>₱${(c.misc || 0).toLocaleString()}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span>Books:</span><span>₱${(c.books || 0).toLocaleString()}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span>Insurance:</span><span>₱${(c.insurance || 0).toLocaleString()}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span>Electric:</span><span>₱${(c.electric || 0).toLocaleString()}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-weight:700;">
                    <span>Base Total:</span><span>₱${(feeData.base_total || 0).toLocaleString()}</span>
                </div>
            </div>
        `;

        if (option === 'A') {
            breakdownHTML = feeComponentsHTML + `
                <div style="display:flex; justify-content:space-between; color:#dc3545; margin-bottom:12px;">
                    <span>Discount (20%):</span><span>-₱${(feeData.discount || 0).toLocaleString()}</span>
                </div>
                <div style="color:#28a745; font-weight:700; font-size:15px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span>Total Due:</span><span>₱${(feeData.total_payable || 0).toLocaleString()}</span>
                    </div>
                </div>
            `;
            document.getElementById('downpayment-amount').value = feeData.total_payable || 0;
            document.getElementById('monthly-amount').value = 0;
        } else {
            breakdownHTML = feeComponentsHTML + `
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
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span>Monthly (${feeData.months || 9} months, July-March):</span>
                        <span>₱${(feeData.monthly_payment || 0).toLocaleString()} × ${feeData.months || 9} = ₱${((feeData.monthly_payment || 0) * (feeData.months || 9)).toLocaleString()}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:#28a745; font-weight:700; font-size:15px;">
                        <span>Total:</span><span>₱${(feeData.total_payable || 0).toLocaleString()}</span>
                    </div>
                </div>
            `;
            document.getElementById('downpayment-amount').value = feeData.downpayment || 0;
            document.getElementById('monthly-amount').value = feeData.monthly_payment || 0;
        }

        document.getElementById('total-amount').value = feeData.total_payable || 0;
        document.getElementById('breakdown-content').innerHTML = breakdownHTML;
        document.getElementById('payment-breakdown-display').style.display = 'block';
    }

    function updatePaymentOptionsForGrade() {
        const gradeLevel = studentGradeLevel.toLowerCase().replace(/\s/g, '');
        const optCContainer = document.getElementById('opt-c-container');
        const optDContainer = document.getElementById('opt-d-container');

        // Option C is for Grade 1-6 only
        if (['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'].includes(gradeLevel)) {
            optCContainer.style.display = 'block';
        } else {
            optCContainer.style.display = 'none';
        }

        // Option D is for Nursery/Kinder only
        if (['nursery', 'kindergarten'].includes(gradeLevel)) {
            optDContainer.style.display = 'block';
        } else {
            optDContainer.style.display = 'none';
        }
    }

    // Payment method selection
    async function selectPaymentMethod(method) {
        if (cashSubmitTimeout) { clearTimeout(cashSubmitTimeout); cashSubmitTimeout = null; }
        if (!selectedPaymentOption) {
            alert('Please select a payment plan first (Step 1).');
            return;
        }
        document.querySelectorAll('#card-cash, #card-xendit').forEach(card => {
            card.classList.remove('selected');
        });
        document.getElementById('card-' + method)?.classList.add('selected');
        setPayStep(3);
        document.getElementById('payStepIndicator')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Use feeDataCache for amount calculation
        const feeData = feeDataCache || {};
        const dpAmount    = feeData.downpayment      || parseFloat(document.getElementById('downpayment-amount')?.value) || 0;
        const moAmount    = feeData.monthly_payment  || parseFloat(document.getElementById('monthly-amount')?.value)    || 0;
        const totalAmount = feeData.total_payable    || parseFloat(document.getElementById('total-amount')?.value)      || 0;

        // Auto-fill amount: downpayment first, then monthly installments
        let amount = 0;
        if (selectedPaymentOption === 'A') {
            amount = totalAmount;
        } else {
            // Check if enrollmentData exists to determine what student still owes
            const alreadyPaid = enrollmentData ? (enrollmentData.payment_amount || 0) : 0;
            const dpRequired = enrollmentData ? (enrollmentData.downpayment_amount || dpAmount) : dpAmount;
            if (alreadyPaid < dpRequired) {
                // Still owe downpayment
                amount = Math.max(0, dpRequired - alreadyPaid);
            } else {
                // Downpayment done — pay next monthly
                amount = moAmount;
            }
        }

        // Also pre-fill the xendit amount field and display
        const xenditAmountInput = document.getElementById('xendit-amount');
        if (xenditAmountInput && amount > 0) {
            xenditAmountInput.value = amount.toFixed(2);
            const xenditDisplay = document.getElementById('xendit-amount-display');
            if (xenditDisplay) xenditDisplay.textContent = parseFloat(amount).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
        }
        // Reset xendit method selection when re-entering panel
        document.getElementById('xendit-method').value = '';
        document.querySelectorAll('.xmethod-card').forEach(c => {
            c.style.border = '2px solid #e8ecf1';
            c.style.background = '#fff';
            c.style.transform = '';
            c.style.boxShadow = '';
        });
        const genBtn = document.getElementById('xendit-generate-btn');
        if (genBtn) { genBtn.disabled = true; genBtn.style.background = '#cbd5e1'; genBtn.style.cursor = 'not-allowed'; }
        document.getElementById('xendit-link-result').style.display = 'none';

        // Show the right panel
        const cashPanel   = document.getElementById('cash-info-panel');
        const xenditPanel = document.getElementById('xendit-payment-panel');
        const methodLabel = document.getElementById('step3-method-label');
        const methodSub   = document.getElementById('step3-method-sub');

        if (method === 'cash') {
            if (cashPanel)   cashPanel.style.display   = 'block';
            if (xenditPanel) xenditPanel.style.display = 'none';
            if (methodLabel) methodLabel.textContent = 'Cash Payment — Visit the Cashier';
            if (methodSub)   methodSub.textContent   = 'Bring your payment to the school cashier window';
            // Update cash amount display
            const cashDisplay = document.getElementById('cash-amount-display');
            if (cashDisplay) cashDisplay.textContent = parseFloat(amount).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
        } else {
            if (cashPanel)   cashPanel.style.display   = 'none';
            if (xenditPanel) xenditPanel.style.display = 'block';
            if (methodLabel) methodLabel.textContent = 'Generate a secure online payment link';
            if (methodSub)   methodSub.textContent   = 'Pay via GCash, Maya, GrabPay, Bank Transfer, or OTC';
        }
    }

    // ── Xendit method card selection ──
    function selectXenditMethod(method) {
        document.getElementById('xendit-method').value = method;
        document.querySelectorAll('.xmethod-card').forEach(c => {
            c.style.border      = '2px solid #e8ecf1';
            c.style.background  = '#fff';
            c.style.transform   = '';
            c.style.boxShadow   = '';
        });
        const sel = document.querySelector('.xmethod-card[data-method="' + method + '"]');
        if (sel) {
            sel.style.border     = '2px solid #1a3a6c';
            sel.style.background = 'linear-gradient(135deg,#eef4ff,#e4eeff)';
            sel.style.transform  = 'translateY(-3px)';
            sel.style.boxShadow  = '0 6px 18px rgba(26,58,108,.15)';
        }
        const btn = document.getElementById('xendit-generate-btn');
        if (btn) {
            btn.disabled        = false;
            btn.style.background= '#1a3a6c';
            btn.style.cursor    = 'pointer';
        }
    }

    // ── Xendit Payment Link Generation ──
    function studentGenerateXenditLink() {
        const amount   = document.getElementById('xendit-amount').value;
        const method   = document.getElementById('xendit-method').value;
        const errEl    = document.getElementById('xendit-error');
        const errText  = document.getElementById('xendit-error-text');
        const resultEl = document.getElementById('xendit-link-result');
        const btn      = document.getElementById('xendit-generate-btn');

        errEl.style.display = 'none';
        if (!amount || parseFloat(amount) < 1) {
            if (errText) errText.textContent = 'Please enter a valid amount.';
            errEl.style.display = 'flex'; return;
        }
        if (!method) {
            if (errText) errText.textContent = 'Please select a payment method above.';
            errEl.style.display = 'flex'; return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating secure link…';

        const feeInfo = feeDataCache || {};
        fetch('{{ route('student.payment.xendit-link') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                amount:             amount,
                payment_method:     method,
                payment_type:       selectedPaymentOption === 'A' ? 'Full Payment' : 'Installment',
                enrollment_id:      {{ $enrollment->id ?? 'null' }},
                payment_option:     selectedPaymentOption,
                total_fee:          feeInfo.total_payable  || 0,
                downpayment_amount: feeInfo.downpayment    || 0,
                monthly_amount:     feeInfo.monthly_payment || 0,
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('xendit-link-url').value = d.invoice_url;
                document.getElementById('xendit-link-open').href = d.invoice_url;
                if (d.expiry) {
                    document.getElementById('xendit-link-expiry').textContent =
                        'Link expires: ' + new Date(d.expiry).toLocaleString('en-PH');
                }
                resultEl.style.display = 'block';
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                window.open(d.invoice_url, '_blank');
            } else {
                if (errText) errText.textContent = d.message || 'Failed to generate payment link. Please try again.';
                errEl.style.display = 'flex';
            }
        })
        .catch(() => {
            if (errText) errText.textContent = 'Network error. Please check your connection and try again.';
            errEl.style.display = 'flex';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock me-2"></i> Generate Secure Link';
        });
    }

    function copyXenditStudentLink() {
        const url = document.getElementById('xendit-link-url').value;
        if (!url) return;
        navigator.clipboard.writeText(url).then(() => {
            const icon = document.getElementById('xendit-copy-icon');
            icon.className = 'bi bi-clipboard-check';
            setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 1800);
        });
    }

    // Open installment schedule modal
    function openInstallmentScheduleModal() {
        const modal = new bootstrap.Modal(document.getElementById('installmentScheduleModal'));
        modal.show();
    }

    // Show selected file name in dropzone
    function showFileName(input, displayId) {
        const display = document.getElementById(displayId);
        if (input.files && input.files[0]) {
            display.querySelector('span').textContent = input.files[0].name;
            display.classList.add('show');
        } else {
            display.classList.remove('show');
        }
    }


    // Dropzone drag-over effects
    document.querySelectorAll('.upload-dropzone').forEach(zone => {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', () => zone.classList.remove('dragover'));
    });

    // ── Document dropzone helpers ──
    function selectDocFile(input, docType, color, bg) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const idle     = document.getElementById('drop-idle-'     + docType);
        const selected = document.getElementById('drop-selected-' + docType);
        const nameEl   = document.getElementById('drop-name-'     + docType);
        const submitW  = document.getElementById('submit-wrap-'   + docType);
        const dropBox  = document.getElementById('drop-'          + docType);

        nameEl.textContent = file.name;
        idle.style.display     = 'none';
        selected.style.display = '';
        submitW.style.display  = '';
        dropBox.style.borderColor = color;
        dropBox.style.borderStyle = 'solid';
        dropBox.style.background  = bg;
    }

    function handleDocDrop(event, docType, color, bg) {
        event.preventDefault();
        const dropBox = document.getElementById('drop-' + docType);
        dropBox.style.borderColor = '#cbd5e1';
        dropBox.style.background  = '#f8fafc';

        const files = event.dataTransfer.files;
        if (!files || files.length === 0) return;

        const input = document.getElementById('file-' + docType);
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        selectDocFile(input, docType, color, bg);
    }

    // ── Bulk document upload functions ──
    function selectBulkFile(input, docType, color, bg) {
        if (!input.files || !input.files[0]) return;
        const file     = input.files[0];
        const idle     = document.getElementById('bulk-idle-'     + docType);
        const selected = document.getElementById('bulk-selected-' + docType);
        const nameEl   = document.getElementById('bulk-name-'     + docType);
        const dropBox  = input.closest('[ondragover]') || input.parentElement;

        nameEl.textContent     = file.name;
        idle.style.display     = 'none';
        selected.style.display = '';
        dropBox.style.borderColor = color;
        dropBox.style.borderStyle = 'solid';
        dropBox.style.background  = bg;

        updateBulkSubmitBtn();
    }

    function handleBulkDrop(event, docType, color, bg) {
        event.preventDefault();
        const files = event.dataTransfer.files;
        if (!files || files.length === 0) return;
        const input = document.getElementById('bulk-file-' + docType);
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        selectBulkFile(input, docType, color, bg);
    }

    function updateBulkSubmitBtn() {
        const btn   = document.getElementById('bulk-submit-btn');
        const label = document.getElementById('bulk-submit-label');
        if (!btn) return;

        const inputs = document.querySelectorAll('#bulk-doc-form input[type="file"]');
        let count = 0;
        inputs.forEach(inp => { if (inp.files && inp.files[0]) count++; });

        if (count > 0) {
            btn.disabled = false;
            btn.style.background   = 'linear-gradient(135deg,#1a3a6c,#2471a3)';
            btn.style.cursor       = 'pointer';
            label.textContent      = 'Submit ' + count + ' Document' + (count > 1 ? 's' : '');
        } else {
            btn.disabled = true;
            btn.style.background   = '#94a3b8';
            btn.style.cursor       = 'not-allowed';
            label.textContent      = 'Select documents to submit';
        }
    }

    // Load profile completion status on page load
    function previewStudentPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('student-avatar-img');
            const placeholder = document.getElementById('student-avatar-placeholder');
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';

            // Update topbar chip
            const chipAvatar = document.querySelector('.user-avatar');
            if (chipAvatar) {
                chipAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
                chipAvatar.style.background = 'none';
                chipAvatar.style.overflow = 'hidden';
            }

            document.getElementById('student-photo-form').submit();
        };
        reader.readAsDataURL(input.files[0]);
    }

    document.addEventListener('DOMContentLoaded', async function() {
        @if(session('settings_tab') || session('photo_success') || session('password_success') || $errors->has('current_password'))
        showSection('settings');
        @elseif(session('go_enrollment') || session('doc_success') || session('doc_error'))
        showSection('enrollment');
        setTimeout(function() {
            const docCard = document.getElementById('doc-upload-card');
            if (docCard) docCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
        @else
        // Default section is 'info' — trigger skeleton on initial page load
        applySectionSkeleton('info');
        @endif

        // Load fee settings and update card labels
        await loadFeeSettings();
        updatePaymentCardLabels();

        // Update payment options based on grade level
        updatePaymentOptionsForGrade();

        // Check if student already selected a payment option - hide payment option selection
        if (enrollmentData && enrollmentData.payment_option && enrollmentData.payment_status !== 'paid') {
            // Set selectedPaymentOption from enrollment data so Pay buttons work
            selectedPaymentOption = enrollmentData.payment_option;
            
            // Pre-calculate fee breakdown so feeDataCache is populated for auto-fill
            await calculatePaymentBreakdown(enrollmentData.payment_option);
            
            // Hide payment option selection cards (Step 1) - option is already locked in
            const paymentOptionCards = document.querySelectorAll('#card-opt-a, #card-opt-b, #card-opt-c, #card-opt-d');
            paymentOptionCards.forEach(card => {
                if (card) card.closest('.col-md-6, .col-lg-3').style.display = 'none';
            });
            
            // Hide the "Select your payment plan" text
            const step1Texts = document.querySelectorAll('#section-payment p');
            step1Texts.forEach(p => {
                if (p.textContent.includes('Select your payment plan')) {
                    p.style.display = 'none';
                }
            });
            
            // Hide the payment breakdown display and pay button container
            document.getElementById('payment-breakdown-display').style.display = 'none';
            document.getElementById('pay-button-container').style.display = 'none';
            
            const downpaymentPaid = enrollmentData.payment_amount >= enrollmentData.downpayment_amount;
            
            if (enrollmentData.payment_option === 'A') {
                // Full payment — pre-fill amount and go directly to step 2 (method selection)
                const amountInput = document.querySelector('#paymentForm input[name="amount"]');
                if (amountInput) amountInput.value = (enrollmentData.total_fee || 0).toFixed(2);
                document.getElementById('form-payment-option').value = 'A';
                document.getElementById('form-total-amount').value = enrollmentData.total_fee || 0;
                setPayStep(2);
            } else if (!downpaymentPaid) {
                // Installment plan - downpayment NOT yet paid
                // Show installment payment notice with Pay Downpayment button
                const makePaymentCard = document.querySelector('#section-payment .content-card');
                if (makePaymentCard && !document.getElementById('installment-notice')) {
                    const dpAmount = enrollmentData.downpayment_amount || 0;
                    const hasPendingPayment = paymentHistory.some(p => p.status === 'pending');
                    const dpBtnHTML = hasPendingPayment
                        ? '<div style="background:#fff3e0; border-radius:8px; padding:12px; text-align:center; color:#e65100; font-weight:600; font-size:13px;"><i class="bi bi-hourglass-split me-1"></i>Payment pending admin approval. Please wait for confirmation.</div>'
                        : `<button onclick="payDownpayment(${dpAmount})" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: #fff; border: none; font-weight: 700; padding: 14px;"><i class="bi bi-cash-coin me-2"></i>Pay Downpayment Now - ₱${dpAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</button>`;
                    const noticeHTML = `
                        <div id="installment-notice" class="alert alert-info mb-4" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); border: 1px solid #64b5f6; border-radius: 10px; padding: 20px;">
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <i class="bi bi-info-circle-fill" style="font-size: 32px; color: #1976d2; flex-shrink: 0;"></i>
                                <div style="flex: 1;">
                                    <h5 style="font-weight: 700; color: #1565c0; margin-bottom: 10px;">
                                        <i class="bi bi-calendar-check me-2"></i>Payment Plan: Option ${enrollmentData.payment_option}
                                    </h5>
                                    <div style="background: #fff; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: #666;">Downpayment Required:</span>
                                            <span style="font-weight: 700; color: #e65100; font-size: 18px;">₱${dpAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: #666;">Monthly Amount:</span>
                                            <span style="font-weight: 600;">₱${(enrollmentData.monthly_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888; margin-top: 8px;">
                                            <i class="bi bi-info-circle me-1"></i>Pay the downpayment first, then monthly installments will follow.
                                        </div>
                                    </div>
                                    ${dpBtnHTML}
                                </div>
                            </div>
                        </div>
                    `;
                    makePaymentCard.insertAdjacentHTML('afterbegin', noticeHTML);
                }
            } else {
                // Installment plan - downpayment already paid, show monthly payment
                const makePaymentCard = document.querySelector('#section-payment .content-card');
                if (makePaymentCard && !document.getElementById('installment-notice')) {
                    const hasPendingPayment = paymentHistory.some(p => p.status === 'pending');
                    const monthlyBtnHTML = hasPendingPayment
                        ? '<div style="background:#fff3e0; border-radius:8px; padding:12px; text-align:center; color:#e65100; font-weight:600; font-size:13px;"><i class="bi bi-hourglass-split me-1"></i>Payment pending admin approval. Please wait for confirmation.</div>'
                        : `<button onclick="payThisInstallment(${enrollmentData.monthly_amount}, 'Monthly Installment', '${enrollmentData.next_installment_date || ''}')" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #28a745, #218838); color: #fff; border: none; font-weight: 700; padding: 14px;"><i class="bi bi-credit-card me-2"></i>Pay Monthly Installment Now</button>`;
                    const noticeHTML = `
                        <div id="installment-notice" class="alert alert-info mb-4" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); border: 1px solid #64b5f6; border-radius: 10px; padding: 20px;">
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <i class="bi bi-info-circle-fill" style="font-size: 32px; color: #1976d2; flex-shrink: 0;"></i>
                                <div style="flex: 1;">
                                    <h5 style="font-weight: 700; color: #1565c0; margin-bottom: 10px;">
                                        <i class="bi bi-calendar-check me-2"></i>Installment Payment Active
                                    </h5>
                                    <div style="background: #fff; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: #666;">Payment Plan:</span>
                                            <span style="font-weight: 600;">Option ${enrollmentData.payment_option}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: #666;">Monthly Amount:</span>
                                            <span style="font-weight: 700; color: #1976d2;">₱${(enrollmentData.monthly_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #666;">Next Due Date:</span>
                                            <span style="font-weight: 600; color: ${enrollmentData.next_installment_date && new Date(enrollmentData.next_installment_date) < new Date() ? '#dc3545' : '#28a745'};">
                                                ${enrollmentData.next_installment_date ? new Date(enrollmentData.next_installment_date).toLocaleDateString('en-PH', {month: 'long', day: 'numeric', year: 'numeric'}) : 'Not set'}
                                            </span>
                                        </div>
                                    </div>
                                    ${monthlyBtnHTML}
                                </div>
                            </div>
                        </div>
                    `;
                    makePaymentCard.insertAdjacentHTML('afterbegin', noticeHTML);
                }
            }
        }

        fetch('{{ route("profile.status") }}')
            .then(response => response.json())
            .then(result => {
                // Handle API response structure (result.data contains the actual data)
                const data = result.data || result;
                
                // Update progress bar
                document.getElementById('completionProgress').style.width = data.percentage + '%';
                document.getElementById('completionPercentage').textContent = data.percentage + '%';
                
                // Update status icons
                const statusIcons = {
                    complete: '<i class="bi bi-check-circle-fill text-success"></i>',
                    incomplete: '<i class="bi bi-circle text-muted"></i>'
                };
                
                const completion = data.completion || {};
                document.getElementById('personalStatus').innerHTML = completion.personal ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('healthStatus').innerHTML = completion.health ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('addressStatus').innerHTML = completion.address ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('guardianStatus').innerHTML = completion.guardian ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('schoolStatus').innerHTML = completion.school ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('enrollmentStatus').innerHTML = completion.enrollment ? statusIcons.complete : statusIcons.incomplete;
                document.getElementById('paymentStatus').innerHTML = completion.payment ? statusIcons.complete : statusIcons.incomplete;
            })
            .catch(error => console.error('Error loading completion status:', error));
    });

    // ── Payment Details Modal ──
    function openChangeSectionModal() {
        const modal = document.getElementById('changeSectionModal');
        if (modal) new bootstrap.Modal(modal).show();
    }

    function confirmSectionChange(sectionId, sectionName) {
        const msgEl = document.getElementById('changeSectionMsg');
        if (!confirm(`Move to section "${sectionName}"?`)) return;

        msgEl.style.display = 'block';
        msgEl.innerHTML = '<div class="text-center text-muted" style="font-size:13px;"><i class="bi bi-hourglass-split me-1"></i>Updating section...</div>';

        fetch('{{ route("student.section.change") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ section_id: sectionId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                msgEl.innerHTML = `<div class="alert alert-success py-2" style="font-size:13px;"><i class="bi bi-check-circle-fill me-1"></i>${data.message}</div>`;
                const display = document.getElementById('currentSectionDisplay');
                if (display) display.value = data.section_name;
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('changeSectionModal'))?.hide();
                    location.reload();
                }, 1200);
            } else {
                msgEl.innerHTML = `<div class="alert alert-danger py-2" style="font-size:13px;"><i class="bi bi-exclamation-circle me-1"></i>${data.message}</div>`;
            }
        })
        .catch(() => {
            msgEl.innerHTML = '<div class="alert alert-danger py-2" style="font-size:13px;">An error occurred. Please try again.</div>';
        });
    }

    function openPaymentDetailsModal() {
        if (!enrollmentData) return;

        const fmt = (n) => '₱' + Number(n).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const e = enrollmentData;

        // 1. Summary Cards
        const statusColor = e.payment_status === 'paid' ? '#2e7d32' : (e.payment_status === 'partial' ? '#e65100' : '#616161');
        const statusIcon = e.payment_status === 'paid' ? 'bi-check-circle-fill' : (e.payment_status === 'partial' ? 'bi-hourglass-split' : 'bi-circle');
        const statusLabel = e.payment_status === 'paid' ? 'Fully Paid' : (e.payment_status === 'partial' ? 'Partial' : 'Pending');

        document.getElementById('pd-summary-cards').innerHTML = `
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="stat-value">${fmt(e.total_fee)}</div>
                        <div class="stat-label">Total Fee</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value">${fmt(e.payment_amount)}</div>
                        <div class="stat-label">Amount Paid</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <div class="stat-value">${fmt(e.remaining_balance)}</div>
                        <div class="stat-label">Balance</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon ${e.payment_status === 'paid' ? 'green' : (e.payment_status === 'partial' ? 'gold' : 'red')}"><i class="bi ${statusIcon}"></i></div>
                    <div>
                        <div class="stat-value" style="color:${statusColor};">${statusLabel}</div>
                        <div class="stat-label">Status</div>
                    </div>
                </div>
            </div>
        `;

        // 2. Fee Breakdown
        const bd = e.payment_breakdown || {};
        const breakdownLabels = {
            'tuition_fee': 'Tuition Fee',
            'misc_reg_pta': 'Misc / Reg / PTA',
            'books': 'Books',
            'insurance': 'Insurance',
            'electric_bill': 'Electric Bill',
            'base_total': 'Base Total',
            'discount': 'Discount',
            'total_due': 'Total Due',
            'downpayment': 'Downpayment',
            'monthly_amount': 'Monthly Amount',
            'duration_months': 'Duration (Months)',
            'payment_type': 'Payment Type',
        };
        let bdHTML = '';
        const bdKeys = ['tuition_fee','misc_reg_pta','books','insurance','electric_bill'];
        bdKeys.forEach(k => {
            if (bd[k] !== undefined) {
                bdHTML += `<div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f0f0f0; font-size:13px;">
                    <span style="color:#555;">${breakdownLabels[k] || k}</span>
                    <span style="font-weight:600;">${fmt(bd[k])}</span>
                </div>`;
            }
        });
        if (bd.base_total !== undefined) {
            bdHTML += `<div style="display:flex; justify-content:space-between; padding:8px 0; font-size:13px; font-weight:700; border-top:2px solid #ddd;">
                <span>Base Total</span><span>${fmt(bd.base_total)}</span>
            </div>`;
        }
        if (bd.discount !== undefined && bd.discount > 0) {
            bdHTML += `<div style="display:flex; justify-content:space-between; padding:6px 0; font-size:13px; color:#dc3545;">
                <span>Discount</span><span>-${fmt(bd.discount)}</span>
            </div>`;
        }
        if (bd.total_due !== undefined) {
            bdHTML += `<div style="display:flex; justify-content:space-between; padding:8px 0; font-size:14px; font-weight:700; color:#2e7d32; border-top:2px solid #ddd;">
                <span>Total Due</span><span>${fmt(bd.total_due)}</span>
            </div>`;
        }
        if (e.payment_option) {
            const optLabels = {A: 'Option A (Cash/Full)', B: 'Option B (Monthly)', C: 'Option C (Elementary Monthly)', D: 'Option D (Nursery Monthly)'};
            bdHTML += `<div style="margin-top:8px; padding:8px 12px; background:#e8eaf6; border-radius:6px; font-size:12px;">
                <i class="bi bi-tag-fill me-1" style="color:var(--blue);"></i>
                <strong>Plan:</strong> ${optLabels[e.payment_option] || 'Option ' + e.payment_option}
            </div>`;
        }
        document.getElementById('pd-fee-breakdown').innerHTML = bdHTML || '<div style="color:#999; text-align:center; padding:12px;">No breakdown available</div>';

        // 3. Installment Schedule (NEW - with month-by-month tracking)
        const isInstallment = e.payment_type === 'installment' || ['B','C','D'].includes(e.payment_option) || e.downpayment_amount > 0;
        const instSection = document.getElementById('pd-installment-section');
        const hasInstallmentData = paymentInstallments && paymentInstallments.length > 0;
        
        if ((isInstallment && e.monthly_amount > 0) || hasInstallmentData) {
            instSection.style.display = 'block';
            let instHTML = '';
            
            // Use new payment installments data
            if (hasInstallmentData) {
                // Downpayment row
                const dpPaid = e.payment_amount >= e.downpayment_amount;
                // A pending screenshot that isn't linked to a monthly installment means the downpayment is awaiting approval
                const hasMonthlyPending = paymentInstallments && paymentInstallments.some(i => i.status === 'pending_approval');
                const dpPendingApproval = !dpPaid && paymentHistory && paymentHistory.some(ph => ph.status === 'pending') && !hasMonthlyPending;
                const dpBg    = dpPaid ? '#e8f5e9' : (dpPendingApproval ? '#e3f2fd' : '#fff3e0');
                const dpBadgeBg = dpPaid ? '#c8e6c9' : (dpPendingApproval ? '#bbdefb' : '#ffe0b2');
                const dpBadgeColor = dpPaid ? '#2e7d32' : (dpPendingApproval ? '#1565c0' : '#e65100');
                const dpLabel = dpPaid ? 'Paid' : (dpPendingApproval ? '<i class="bi bi-hourglass-split"></i> Pending Approval' : 'Unpaid');
                instHTML += `<div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; margin-bottom:4px; border-radius:8px; background:${dpBg}; font-size:13px;">
                    <div>
                        <div style="font-weight:700;">Downpayment</div>
                        <div style="font-size:11px; color:#888;">One-time</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-weight:700;">${fmt(e.downpayment_amount)}</span>
                        <span style="font-size:11px; padding:3px 8px; border-radius:10px; background:${dpBadgeBg}; color:${dpBadgeColor}; font-weight:600;">${dpLabel}</span>
                    </div>
                </div>`;
                
                // Monthly rows from paymentInstallments
                paymentInstallments.forEach((inst) => {
                    const isPaid = inst.status === 'paid';
                    const isPendingApproval = inst.status === 'pending_approval';
                    const dueDate = new Date(inst.due_date);
                    const isPast = dueDate < new Date() && !isPaid && !isPendingApproval;
                    const isOverdue = inst.weeks_overdue > 0 && !isPendingApproval;
                    const hasLateFee = inst.late_fee > 0;
                    
                    let rowBg = '#fafafa';
                    let statusText = 'Upcoming';
                    let statusBg = '#e0e0e0';
                    let statusColor = '#616161';
                    
                    if (isPaid) {
                        rowBg = '#e8f5e9';
                        statusText = 'Paid';
                        statusBg = '#c8e6c9';
                        statusColor = '#2e7d32';
                    } else if (isPendingApproval) {
                        rowBg = '#e3f2fd';
                        statusText = 'Pending Approval';
                        statusBg = '#bbdefb';
                        statusColor = '#1565c0';
                    } else if (isOverdue) {
                        rowBg = '#ffebee';
                        statusText = 'Overdue (' + inst.weeks_overdue + 'w)';
                        statusBg = '#ffcdd2';
                        statusColor = '#c62828';
                    } else if (isPast) {
                        rowBg = '#fff3e0';
                        statusText = 'Past Due';
                        statusBg = '#ffe0b2';
                        statusColor = '#e65100';
                    }
                    
                    const totalAmount = inst.amount + inst.late_fee;
                    const amountDisplay = hasLateFee 
                        ? '<span style="font-weight:600;">' + fmt(totalAmount) + '</span> <span style="font-size:10px; color:#dc3545;">(+' + fmt(inst.late_fee) + ' late fee)</span>'
                        : '<span style="font-weight:600;">' + fmt(inst.amount) + '</span>';
                    
                    let payBtn = '';
                    if (isPendingApproval) {
                        payBtn = '<span style="font-size:10px; color:#1565c0; font-weight:600;"><i class="bi bi-hourglass-split"></i> Awaiting Approval</span>';
                    } else if (!isPaid) {
                        if (e.account_blocked) {
                            payBtn = '<span style="font-size:10px; color:#dc3545; font-weight:600;"><i class="bi bi-lock"></i> Portal Blocked</span>';
                        } else if (!dpPaid) {
                            payBtn = '<span style="font-size:10px; color:#e65100; font-weight:600;"><i class="bi bi-lock"></i> Pay downpayment first</span>';
                        } else {
                            payBtn = '<button onclick="paySpecificInstallment(' + inst.id + ', ' + totalAmount + ', \'' + inst.month_name + '\', \'' + inst.due_date + '\')" style="font-size:11px; padding:3px 10px; border-radius:6px; background:var(--blue); color:#fff; border:none; cursor:pointer;">Pay</button>';
                        }
                    }
                    
                    instHTML += '<div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; margin-bottom:3px; border-radius:8px; background:' + rowBg + '; font-size:13px;">' +
                        '<div>' +
                            '<div style="font-weight:600;">' + inst.month_name + '</div>' +
                            '<div style="font-size:11px; color:#888;">Due: ' + dueDate.toLocaleDateString('en-PH', {month:'short', day:'numeric', year:'numeric'}) + '</div>' +
                        '</div>' +
                        '<div style="display:flex; align-items:center; gap:10px;">' +
                            amountDisplay +
                            payBtn +
                            '<span style="font-size:10px; padding:2px 7px; border-radius:10px; background:' + statusBg + '; color:' + statusColor + '; font-weight:600;">' + statusText + '</span>' +
                        '</div>' +
                    '</div>';
                });
                
                // Summary info
                if (paymentSummary) {
                    instHTML += '<div style="margin-top:12px; padding:12px; background:#f5f5f5; border-radius:8px; font-size:12px;">' +
                        '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">' +
                            '<span>Paid:</span>' +
                            '<span style="font-weight:600; color:#2e7d32;">' + paymentSummary.paid + ' months</span>' +
                        '</div>' +
                        '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">' +
                            '<span>Pending:</span>' +
                            '<span style="font-weight:600;">' + paymentSummary.pending + ' months</span>' +
                        '</div>' +
                        (paymentSummary.overdue > 0 ? '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">' +
                            '<span>Overdue:</span>' +
                            '<span style="font-weight:600; color:#dc3545;">' + paymentSummary.overdue + ' months</span>' +
                        '</div>' : '') +
                        (paymentSummary.total_late_fees > 0 ? '<div style="display:flex; justify-content:space-between; border-top:1px solid #ddd; padding-top:8px; margin-top:8px;">' +
                            '<span>Total Late Fees:</span>' +
                            '<span style="font-weight:600; color:#dc3545;">' + fmt(paymentSummary.total_late_fees) + '</span>' +
                        '</div>' : '') +
                    '</div>';
                }
            } else {
                // Fallback message
                instHTML = '<div style="text-align:center; padding:20px; color:#999;"><i class="bi bi-info-circle" style="font-size:24px; display:block; margin-bottom:6px;"></i>Installment details will appear after enrollment is fully processed.</div>';
            }
            
            document.getElementById('pd-installment-schedule').innerHTML = instHTML;
        } else {
            instSection.style.display = 'none';
        }

        // 4. Payment History
        let histHTML = '';
        if (paymentHistory.length > 0) {
            paymentHistory.forEach(p => {
                const date = new Date(p.created_at);
                const statusPill = p.status === 'approved'
                    ? '<span style="font-size:10px; padding:2px 8px; border-radius:10px; background:#c8e6c9; color:#2e7d32; font-weight:600;">Approved</span>'
                    : (p.status === 'rejected'
                        ? '<span style="font-size:10px; padding:2px 8px; border-radius:10px; background:#ffcdd2; color:#c62828; font-weight:600;">Rejected</span>'
                        : '<span style="font-size:10px; padding:2px 8px; border-radius:10px; background:#ffe0b2; color:#e65100; font-weight:600;">Pending</span>');
                histHTML += `<div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f0f0f0; font-size:13px;">
                    <div>
                        <div style="font-weight:600;">${p.description || 'Payment'}</div>
                        <div style="font-size:11px; color:#888;">${date.toLocaleDateString('en-PH', {month:'short', day:'numeric', year:'numeric'})} at ${date.toLocaleTimeString('en-PH', {hour:'2-digit', minute:'2-digit'})}</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        ${statusPill}
                        <a href="/storage/${p.file_path}" target="_blank" style="width:28px; height:28px; border-radius:6px; background:#e3f2fd; display:flex; align-items:center; justify-content:center; color:var(--blue); text-decoration:none; font-size:12px;" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>`;
            });
        } else {
            histHTML = '<div style="text-align:center; padding:20px; color:#999;"><i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:6px;"></i>No payment records yet</div>';
        }
        document.getElementById('pd-payment-history').innerHTML = histHTML;

        // Next Due Date info
        if (e.next_installment_date) {
            const ndd = new Date(e.next_installment_date + 'T00:00:00');
            const daysLeft = Math.ceil((ndd - new Date()) / (1000*60*60*24));
            const daysText = daysLeft > 0 ? `${daysLeft} day${daysLeft !== 1 ? 's' : ''} left` : (daysLeft === 0 ? 'Due today' : 'Overdue');
            const daysColor = daysLeft > 7 ? '#2e7d32' : (daysLeft > 0 ? '#e65100' : '#c62828');
            document.getElementById('pd-summary-cards').innerHTML += `
                <div class="col-12" style="margin-top:4px;">
                    <div style="background:linear-gradient(135deg,#f3e5f5,#e1bee7); border-radius:10px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:11px; color:#6a1b9a;">Next Due Date</div>
                            <div style="font-size:16px; font-weight:700; color:#4a148c;">${ndd.toLocaleDateString('en-PH', {month:'long', day:'numeric', year:'numeric'})}</div>
                        </div>
                        <div style="text-align:right;">
                            <span style="font-size:13px; font-weight:700; color:${daysColor};">${daysText}</span>
                            ${e.monthly_amount > 0 ? `<div style="font-size:11px; color:#6a1b9a;">Amount: ${fmt(e.monthly_amount)}</div>` : ''}
                        </div>
                    </div>
                </div>`;
        }

        new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
    }

    // Pay downpayment - Opens the main payment form pre-filled with downpayment amount
    async function payDownpayment(amount) {
        // Close payment details modal if open
        bootstrap.Modal.getInstance(document.getElementById('paymentDetailsModal'))?.hide();

        selectedPaymentOption = enrollmentData.payment_option || 'C';
        await calculatePaymentBreakdown(selectedPaymentOption);

        // Pre-set hidden form fields so they're ready when the user reaches step 3
        document.getElementById('form-payment-option').value = enrollmentData.payment_option || 'C';
        document.getElementById('form-downpayment-amount').value = amount;
        document.getElementById('form-monthly-amount').value = enrollmentData.monthly_amount || 0;
        document.getElementById('form-total-amount').value = enrollmentData.total_fee || 0;

        // Pre-fill amount so it's ready in step 3
        const amountInput = document.querySelector('#paymentForm input[name="amount"]');
        if (amountInput) {
            amountInput.value = Number(amount).toFixed(2);
            amountInput.readOnly = false;
        }

        // Go to step 2 so the user MUST choose a payment method before submitting
        setPayStep(2);
        document.getElementById('payStepIndicator')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Pay a specific installment - Opens the Installment Payment Modal
    function payThisInstallment(amount, month, dueDate) {
        // Close payment details modal if open
        bootstrap.Modal.getInstance(document.getElementById('paymentDetailsModal'))?.hide();

        // Populate the installment payment modal
        if (enrollmentData) {
            document.getElementById('inst-modal-option').textContent = enrollmentData.payment_option || 'B';
            document.getElementById('inst-modal-amount').textContent = Number(enrollmentData.monthly_amount || amount).toLocaleString('en-PH', {minimumFractionDigits: 2});
            
            // Set hidden fields
            document.getElementById('inst-payment-option').value = enrollmentData.payment_option || 'B';
            document.getElementById('inst-downpayment-amount').value = enrollmentData.downpayment_amount || 0;
            document.getElementById('inst-monthly-amount').value = enrollmentData.monthly_amount || amount;
            document.getElementById('inst-total-amount').value = enrollmentData.total_fee || 0;
            
            // Clear any existing installment_id (pay next pending)
            const existingInstallmentInput = document.getElementById('inst-installment-id');
            if (existingInstallmentInput) {
                existingInstallmentInput.remove();
            }
            
            // Set amount input (readonly)
            document.getElementById('inst-amount-input').value = Number(enrollmentData.monthly_amount || amount).toFixed(2);
            
            // Update cash notice amount
            document.querySelectorAll('.inst-cash-amount').forEach(el => {
                el.textContent = Number(enrollmentData.monthly_amount || amount).toLocaleString('en-PH', {minimumFractionDigits: 2});
            });
        }

        // Reset modal panels
        document.getElementById('inst-cash-panel').style.display   = 'none';
        document.getElementById('inst-xendit-panel').style.display = 'none';
        document.getElementById('inst-xendit-result').style.display = 'none';
        document.getElementById('inst-xendit-error').style.display = 'none';
        ['cash','xendit'].forEach(m => {
            var c = document.getElementById('inst-card-' + m);
            if (c) { c.style.borderColor = '#e2e8f0'; c.style.background = '#fff'; c.style.boxShadow = 'none'; }
        });

        // Show the installment payment modal
        new bootstrap.Modal(document.getElementById('installmentPaymentModal')).show();
    }

    // Pay a SPECIFIC installment by ID (for month-by-month tracking)
    function paySpecificInstallment(installmentId, totalAmount, monthName, dueDate) {
        // Close payment details modal if open
        bootstrap.Modal.getInstance(document.getElementById('paymentDetailsModal'))?.hide();

        // Populate the installment payment modal
        if (enrollmentData) {
            document.getElementById('inst-modal-option').textContent = enrollmentData.payment_option || 'B';
            document.getElementById('inst-modal-amount').textContent = Number(totalAmount).toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('installmentPaymentModalLabel').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Pay ' + monthName + ' Installment';
        }
        // Store installment ID for Xendit link generation
        window._instInstallmentId = installmentId;

        // Reset modal panels
        document.getElementById('inst-cash-panel').style.display   = 'none';
        document.getElementById('inst-xendit-panel').style.display = 'none';
        document.getElementById('inst-xendit-result').style.display = 'none';
        document.getElementById('inst-xendit-error').style.display = 'none';
        ['cash','xendit'].forEach(m => {
            var c = document.getElementById('inst-card-' + m);
            if (c) { c.style.borderColor = '#e2e8f0'; c.style.background = '#fff'; c.style.boxShadow = 'none'; }
        });

        // Show the installment payment modal
        new bootstrap.Modal(document.getElementById('installmentPaymentModal')).show();
    }

    // Select method for installment modal (Cash or Xendit)
    function selectInstallmentMethod(method) {
        ['cash','xendit'].forEach(function(m) {
            var card = document.getElementById('inst-card-' + m);
            if (!card) return;
            var active = m === method;
            card.style.borderColor = active ? '#1a3a6c' : '#e2e8f0';
            card.style.background  = active ? '#eef4ff' : '#fff';
            card.style.boxShadow   = active ? '0 0 0 3px rgba(26,58,108,.12)' : 'none';
        });

        var cashPanel   = document.getElementById('inst-cash-panel');
        var xenditPanel = document.getElementById('inst-xendit-panel');

        if (method === 'cash') {
            cashPanel.style.display   = 'block';
            xenditPanel.style.display = 'none';
            // Update cash display amount
            var amt = document.getElementById('inst-modal-amount')?.textContent || '0';
            var cashAmt = document.getElementById('inst-cash-display-amount');
            if (cashAmt) cashAmt.textContent = amt;
        } else {
            cashPanel.style.display   = 'none';
            xenditPanel.style.display = 'block';
            // Pre-fill xendit amount display
            var amt2 = document.getElementById('inst-modal-amount')?.textContent || '0';
            var xAmt = document.getElementById('inst-xendit-amount-display');
            if (xAmt) xAmt.textContent = amt2;
            // Reset xendit method cards
            document.querySelectorAll('.inst-xmethod-card').forEach(c => {
                c.style.border     = '2px solid rgba(255,255,255,0.2)';
                c.style.background = 'rgba(255,255,255,0.1)';
            });
            var xBtn = document.getElementById('inst-xendit-generate-btn');
            if (xBtn) { xBtn.disabled = true; xBtn.style.background = '#cbd5e1'; xBtn.style.cursor = 'not-allowed'; }
            document.getElementById('inst-xendit-result').style.display = 'none';
        }
    }

    // Select method card inside installment Xendit panel
    function selectInstXenditMethod(method) {
        document.querySelectorAll('.inst-xmethod-card').forEach(c => {
            c.style.border     = '2px solid rgba(255,255,255,0.2)';
            c.style.background = 'rgba(255,255,255,0.1)';
        });
        var sel = document.querySelector('.inst-xmethod-card[data-method="' + method + '"]');
        if (sel) { sel.style.border = '2px solid #fff'; sel.style.background = 'rgba(255,255,255,0.3)'; }
        var btn = document.getElementById('inst-xendit-generate-btn');
        if (btn) { btn.disabled = false; btn.style.background = 'linear-gradient(135deg,#1a3a6c,#2471a3)'; btn.style.cursor = 'pointer'; }
        btn._selectedMethod = method;
    }

    // Generate Xendit link from installment modal
    function instGenerateXenditLink() {
        var btn    = document.getElementById('inst-xendit-generate-btn');
        var method = btn._selectedMethod;
        var amount = document.getElementById('inst-modal-amount')?.textContent?.replace(/,/g,'') || '0';
        var errEl  = document.getElementById('inst-xendit-error');
        var resultEl = document.getElementById('inst-xendit-result');
        var enrollmentId = {{ $enrollment->id ?? 'null' }};

        errEl.style.display = 'none';
        if (!method) { errEl.textContent = 'Please select a payment method.'; errEl.style.display = 'block'; return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating…';

        fetch('{{ route('student.payment.xendit-link') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                amount: parseFloat(amount),
                payment_method: method,
                payment_type: 'Installment',
                enrollment_id: enrollmentId,
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('inst-xendit-link-url').value = d.invoice_url;
                document.getElementById('inst-xendit-link-open').href  = d.invoice_url;
                resultEl.style.display = 'block';
                window.open(d.invoice_url, '_blank');
            } else {
                errEl.textContent = d.message || 'Failed. Please try again.';
                errEl.style.display = 'block';
            }
        })
        .catch(() => { errEl.textContent = 'Network error. Please try again.'; errEl.style.display = 'block'; })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Generate Secure Link';
        });
    }
</script>

{{-- ── RE-ENROLLMENT CONFIRM MODAL ── --}}
<div id="reenrollConfirmModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
    {{-- Backdrop --}}
    <div onclick="closeReenrollConfirm()" style="position:absolute;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"></div>

    {{-- Dialog --}}
    <div style="position:relative;width:100%;max-width:430px;margin:16px;border-radius:18px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.3);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1a3a6c,#2471a3);padding:28px 28px 22px;text-align:center;color:#fff;">
            <div style="width:60px;height:60px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="bi bi-mortarboard-fill" style="font-size:26px;"></i>
            </div>
            <div style="font-size:18px;font-weight:700;letter-spacing:0.3px;">Confirm Re-enrollment</div>
            <div id="reenrollModalSY" style="font-size:13px;opacity:0.8;margin-top:4px;"></div>
        </div>

        {{-- Body --}}
        <div style="background:#fff;padding:24px 28px;">

            {{-- Grade row --}}
            <div style="display:flex;align-items:center;gap:14px;background:#f0f7ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 16px;margin-bottom:14px;">
                <div style="width:40px;height:40px;background:#1a3a6c;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-arrow-up-circle-fill" style="color:#fff;font-size:20px;"></i>
                </div>
                <div>
                    <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Next Grade Level</div>
                    <div id="reenrollModalGrade" style="font-size:16px;font-weight:700;color:#1a3a6c;margin-top:2px;"></div>
                </div>
            </div>

            {{-- Balance notice (shown only if there's a balance) --}}
            <div id="reenrollBalanceNotice" style="display:none;align-items:center;gap:12px;background:#fff8f0;border:1px solid #fed7aa;border-radius:12px;padding:12px 16px;margin-bottom:14px;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#d97706;font-size:18px;flex-shrink:0;"></i>
                <div style="font-size:13px;color:#92400e;">
                    You have an outstanding balance of <strong id="reenrollBalanceAmt"></strong>. This will be carried over to the new school year.
                </div>
            </div>

            {{-- Info note --}}
            <div style="display:flex;align-items:flex-start;gap:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:22px;">
                <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:16px;flex-shrink:0;margin-top:1px;"></i>
                <div style="font-size:12px;color:#166534;line-height:1.5;">
                    Your personal information and records will be carried over. You can update your details after enrollment.
                </div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="closeReenrollConfirm()"
                    style="flex:1;padding:12px;border:1.5px solid #d1d5db;background:#fff;border-radius:10px;font-weight:600;font-size:14px;color:#374151;cursor:pointer;">
                    Cancel
                </button>
                <button type="button" id="reenrollConfirmBtn" onclick="submitReenrollForm()"
                    style="flex:2;padding:12px;background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;border:none;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <i class="bi bi-arrow-repeat"></i>
                    Yes, Re-enroll Now
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openReenrollConfirm(sy, grade, balanceCleared, balanceAmt) {
    document.getElementById('reenrollModalSY').textContent    = 'School Year ' + sy;
    document.getElementById('reenrollModalGrade').textContent = grade;

    var notice = document.getElementById('reenrollBalanceNotice');
    if (!balanceCleared && balanceAmt > 0) {
        document.getElementById('reenrollBalanceAmt').textContent = '₱' + balanceAmt.toLocaleString('en-PH', {minimumFractionDigits:2});
        notice.style.display = 'flex';
    } else {
        notice.style.display = 'none';
    }

    var modal = document.getElementById('reenrollConfirmModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReenrollConfirm() {
    document.getElementById('reenrollConfirmModal').style.display = 'none';
    document.body.style.overflow = '';
}

function submitReenrollForm() {
    var btn = document.getElementById('reenrollConfirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin 0.7s linear infinite;"></span> Processing…';
    document.getElementById('reenrollForm').submit();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeReenrollConfirm();
});
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

{{-- ── Document Viewer Modal ── --}}
<div class="modal fade" id="spDocViewerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);border:none;padding:14px 20px;">
                <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                    <div style="background:rgba(255,255,255,.2);border-radius:8px;padding:6px 10px;flex-shrink:0;">
                        <i class="bi bi-file-earmark-text" id="spDvIcon" style="color:#fff;font-size:16px;"></i>
                    </div>
                    <div style="min-width:0;">
                        <h5 class="modal-title" id="spDvTitle" style="color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:360px;margin:0;font-size:15px;font-weight:700;"></h5>
                        <div id="spDvSubtitle" style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:360px;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0;min-height:200px;">
                <div id="spDvImgWrap" style="text-align:center;padding:24px;">
                    <img id="spDvImg" src="" alt="Document" style="max-width:100%;max-height:65vh;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);">
                </div>
                <div id="spDvPdfWrap" style="display:none;">
                    <iframe id="spDvPdf" src="" style="width:100%;height:70vh;border:none;"></iframe>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e5e7eb;padding:14px 20px;justify-content:flex-end;gap:8px;">
                <a id="spDvDownload" href="" download style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#f3f4f6;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="bi bi-download"></i> Download
                </a>
                <a id="spDvOpen" href="" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#1a3a6c;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openDocViewer(url, filename, label) {
    document.getElementById('spDvTitle').textContent    = label    || 'Document';
    document.getElementById('spDvSubtitle').textContent = filename || '';
    document.getElementById('spDvDownload').href        = url;
    document.getElementById('spDvDownload').download    = filename || 'document';
    document.getElementById('spDvOpen').href            = url;

    var ext     = (url.split('?')[0].split('.').pop() || '').toLowerCase();
    var isImage = ['jpg','jpeg','png','gif','webp','bmp'].includes(ext);
    var isPdf   = ext === 'pdf';

    document.getElementById('spDvImgWrap').style.display = isImage ? 'block' : 'none';
    document.getElementById('spDvPdfWrap').style.display = isPdf   ? 'block' : 'none';

    if (isImage) { document.getElementById('spDvImg').src = url; }
    else if (isPdf) { document.getElementById('spDvPdf').src = url; }

    var modal = new bootstrap.Modal(document.getElementById('spDocViewerModal'));
    modal.show();
}

document.getElementById('spDocViewerModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('spDvImg').src = '';
    document.getElementById('spDvPdf').src = '';
});

// ── Auto-refresh when tab becomes visible again ──
// Handles: switching browser tabs, switching browser windows/apps,
// coming back from Edge → Chrome or vice-versa.
(function () {
    var _lastRefresh = Date.now();
    var REFRESH_COOLDOWN = 30000; // only re-fetch if tab was hidden for >30 seconds

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState !== 'visible') return;

        var hiddenFor = Date.now() - _lastRefresh;
        if (hiddenFor < REFRESH_COOLDOWN) return;
        _lastRefresh = Date.now();

        // Find which section is currently active
        var activeSection = null;
        ['info','grades','schedule','announcements','enrollment','payment','settings'].forEach(function(s) {
            var el = document.getElementById('section-' + s);
            if (el && el.style.display !== 'none') activeSection = s;
        });

        // Refresh only the active section's data
        if (activeSection === 'grades') {
            loadGrades(currentGradeTerm, document.querySelector('.q-tab.active'));
            loadGWA();
        } else if (activeSection === 'schedule') {
            if (typeof loadSchedule === 'function') loadSchedule();
        } else if (activeSection === 'announcements') {
            if (typeof loadAnnouncements === 'function') loadAnnouncements();
        }
        // info / enrollment / payment are server-rendered — no silent refresh needed
    });

    // Reset timer whenever user actively uses the page
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') _lastRefresh = Date.now();
    });
})();
</script>
</body>
</html>