<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Finance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1e3a5f;
            --gold: #c5a059;
            --blue-light: #2c5282;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        * { font-family: 'Open Sans', sans-serif; }
        
        body {
            background: #f5f6fa;
            min-height: 100vh;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #162f5c 0%, #1a3a6c 60%, #1c3f78 100%);
            box-shadow: 3px 0 18px rgba(0,0,0,0.18);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-brand i {
            font-size: 28px;
            color: var(--gold);
        }

        /* Brand block — same markup/classes as every other portal now
           (was a bare icon + single-line "Finance Portal"/"Payment Review"
           label before). */
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

        .sidebar-menu {
            padding: 16px 0;
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
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--gold);
        }
        .menu-item.active { background: rgba(197,160,89,0.12); }

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
        
        .main-content {
            margin-left: 260px;
            padding: 24px 32px;
        }
        
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
        
        .content-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--blue);
        }
        
        .card-body {
            padding: 24px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-badge.pending { background: #fff3e0; color: #e65100; }
        .status-badge.approved { background: #e8f5e9; color: #2e7d32; }
        .status-badge.rejected { background: #ffebee; color: #c62828; }
        
        .info-row {
            display: flex;
            margin-bottom: 16px;
        }
        
        .info-label {
            width: 180px;
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            flex: 1;
            font-size: 14px;
            color: #333;
            font-weight: 600;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #666;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-approve {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-approve:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .btn-reject {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-reject:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .payment-screenshot {
            max-width: 100%;
            max-height: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .timeline {
            position: relative;
            padding-left: 24px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .timeline-dot {
            position: absolute;
            left: -20px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--blue);
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .timeline-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
        }
        
        .timeline-time {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .timeline-title {
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 4px;
        }
        
        .timeline-desc {
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        @if(($viewerContext ?? 'finance') === 'finance')
        <div class="sidebar-header">
            <a href="{{ route('finance.dashboard') }}" class="sidebar-brand">
                <div class="brand-logo-circle">
                    <img src="/images/logo.png" alt=""
                         onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-wallet2\'></i>'">
                </div>
                <div class="brand-info">
                    <h6>IEMELIF Learning Center</h6>
                    <span>General Tinio, Nueva Ecija</span>
                </div>
            </a>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Main</div>
            <a href="{{ route('finance.dashboard') }}" class="menu-item">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>
            <div class="menu-section">Finance</div>
            <a href="{{ route('finance.payments.index') }}" class="menu-item active">
                <i class="bi bi-credit-card-fill"></i>
                Payments
            </a>
            <a href="{{ route('finance.installments.index') }}" class="menu-item">
                <i class="bi bi-calendar-check-fill"></i>
                Installments
            </a>
            <a href="{{ route('finance.fees.index') }}" class="menu-item">
                <i class="bi bi-cash-stack"></i>
                Fee Management
            </a>
            <div class="menu-section">Reports</div>
            <a href="{{ route('finance.reports.index') }}" class="menu-item">
                <i class="bi bi-graph-up"></i>
                Financial Reports
            </a>
            <div class="menu-section">Account</div>
            <a href="{{ route('finance.settings') }}" class="menu-item">
                <i class="bi bi-gear-fill"></i>
                Settings
            </a>
            <form method="POST" action="{{ route('finance.logout') }}" style="margin: 0;" onsubmit="return confirmLogout(this)">
                @csrf
                <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer;">
                    <i class="bi bi-box-arrow-left"></i>
                    Logout
                </button>
            </form>
        </nav>
        @else
        {{-- Viewed by Admin/Super Admin (via /admin/payments/{id}) — that
             session has no finance-guard login, so the sidebar above would
             bounce every click to /finance/login. Point back at the Admin
             dashboard instead. --}}
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <div class="brand-logo-circle">
                    <img src="/images/logo.png" alt=""
                         onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-wallet2\'></i>'">
                </div>
                <div class="brand-info">
                    <h6>IEMELIF Learning Center</h6>
                    <span>General Tinio, Nueva Ecija</span>
                </div>
            </a>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="menu-item active">
                <i class="bi bi-grid-fill"></i>
                Admin Dashboard
            </a>
            <div class="menu-section">Account</div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer;">
                    <i class="bi bi-box-arrow-left"></i>
                    Logout
                </button>
            </form>
        </nav>
        @endif
    </aside>

    <main class="main-content">
        <style>
            .ilc-breadcrumb{display:flex;align-items:center;gap:8px;padding:0 0 16px;font-size:13px;color:#64748b;flex-wrap:wrap;}
            .ilc-breadcrumb a{color:var(--blue);text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;}
            .ilc-breadcrumb a:hover{text-decoration:underline;}
            .ilc-bc-sep{font-size:10px;color:#b6c0cc;}
            .ilc-bc-current{color:#334155;font-weight:700;}
        </style>
        @php $bcHome = ($viewerContext ?? 'finance') === 'finance' ? route('finance.dashboard') : route('admin.dashboard'); @endphp
        <nav class="ilc-breadcrumb" aria-label="breadcrumb">
            <a href="{{ $bcHome }}"><i class="bi bi-house-door-fill"></i> Home</a>
            <i class="bi bi-chevron-right ilc-bc-sep"></i>
            @if(($viewerContext ?? 'finance') === 'finance')
            <a href="{{ route('finance.payments.index') }}">Payments</a>
            <i class="bi bi-chevron-right ilc-bc-sep"></i>
            @endif
            <span class="ilc-bc-current">Payment Details</span>
        </nav>
        <div class="page-header">
            <h1 class="page-title">
                <a href="{{ $bcHome }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> {{ ($viewerContext ?? 'finance') === 'finance' ? 'Back to Payments' : 'Back to Dashboard' }}
                </a>
                Payment Details
            </h1>
        </div>

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

        <div class="row">
            <!-- Payment Information -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-credit-card" style="color: var(--gold);"></i>
                            Payment Information
                        </h3>
                        @if($document->status === 'pending')
                            <span class="status-badge pending">
                                <i class="bi bi-hourglass-split"></i> Pending Verification
                            </span>
                        @elseif($document->status === 'approved')
                            <span class="status-badge approved">
                                <i class="bi bi-check-circle"></i> Approved
                            </span>
                        @else
                            <span class="status-badge rejected">
                                <i class="bi bi-x-circle"></i> Rejected
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Payment ID</div>
                            <div class="info-value">#{{ $document->id }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Student Name</div>
                            <div class="info-value">{{ $document->user->name ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $document->user->email ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Grade Level</div>
                            <div class="info-value">{{ $document->enrollment->grade_level ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Description</div>
                            <div class="info-value">{{ $document->description ?? 'Payment' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Submitted On</div>
                            <div class="info-value">{{ $document->created_at->format('F d, Y h:i A') }}</div>
                        </div>
                        
                        @if($document->status !== 'pending')
                            <div class="info-row">
                                <div class="info-label">Reviewed By</div>
                                <div class="info-value">{{ $reviewerName }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Reviewed On</div>
                                <div class="info-value">{{ $reviewedAt ? $reviewedAt->format('F d, Y h:i A') : $document->updated_at->format('F d, Y h:i A') }}</div>
                            </div>
                        @endif
                        
                        @if($document->status === 'rejected' && $document->reject_reason)
                            <div class="info-row">
                                <div class="info-label">Rejection Reason</div>
                                <div class="info-value" style="color: #dc3545;">{{ $document->reject_reason }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Enrollment Info -->
                @if($document->enrollment)
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-person-badge" style="color: var(--gold);"></i>
                                Enrollment Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-label">Payment Plan</div>
                                <div class="info-value">Option {{ $document->enrollment->payment_option ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Total Fee</div>
                                <div class="info-value">₱{{ number_format($document->enrollment->total_fee ?? 0, 2) }}</div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Amount Paid</div>
                                <div class="info-value">₱{{ number_format($document->enrollment->payment_amount ?? 0, 2) }}</div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Remaining Balance</div>
                                <div class="info-value" style="color: #e65100;">₱{{ number_format($document->enrollment->remaining_balance ?? 0, 2) }}</div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Payment Status</div>
                                <div class="info-value">
                                    @if($document->enrollment->payment_status === 'paid')
                                        <span style="color: #28a745;">Fully Paid</span>
                                    @elseif($document->enrollment->payment_status === 'partial')
                                        <span style="color: #ffc107;">Partially Paid</span>
                                    @else
                                        <span style="color: #6c757d;">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Screenshot & Actions -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-image" style="color: var(--gold);"></i>
                            Payment Screenshot
                        </h3>
                    </div>
                    <div class="card-body" style="text-align: center;">
                        @if($document->file_path)
                            <a href="{{ route('documents.view', $document) }}" target="_blank">
                                <img src="{{ route('documents.view', $document) }}" alt="Payment Screenshot" class="payment-screenshot">
                            </a>
                            <div style="margin-top: 16px;">
                                <a href="{{ route('documents.view', $document) }}" target="_blank" class="btn-back">
                                    <i class="bi bi-eye"></i> View Full Size
                                </a>
                                <a href="{{ route('documents.view', $document) }}" download class="btn-back" style="margin-left: 8px;">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        @else
                            <div style="padding: 40px; color: #666;">
                                <i class="bi bi-image" style="font-size: 64px; color: #ddd; display: block; margin-bottom: 16px;"></i>
                                No screenshot available
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                @if($document->status === 'pending')
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-check2-square" style="color: var(--gold);"></i>
                                Verification Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                <form method="POST" action="{{ route('admin.payments.approve', $document) }}" style="display: inline;" onsubmit="return confirmApprovePayment(this)">
                                    @csrf
                                    <button type="submit" class="btn-approve">
                                        <i class="bi bi-check-lg"></i> Approve Payment
                                    </button>
                                </form>
                                
                                <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-lg"></i> Reject Payment
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-clock-history" style="color: var(--gold);"></i>
                            Payment Timeline
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background: var(--blue);"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">{{ $document->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="timeline-title">Payment Submitted</div>
                                    <div class="timeline-desc">Student submitted payment for verification</div>
                                </div>
                            </div>
                            
                            @if($document->status !== 'pending')
                                <div class="timeline-item">
                                    <div class="timeline-dot" style="background: {{ $document->status === 'approved' ? '#28a745' : '#dc3545' }};"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-time">{{ $reviewedAt ? $reviewedAt->format('M d, Y h:i A') : $document->updated_at->format('M d, Y h:i A') }}</div>
                                        <div class="timeline-title">
                                            @if($document->status === 'approved')
                                                <span style="color: #28a745;"><i class="bi bi-check-circle"></i> Payment Approved</span>
                                            @else
                                                <span style="color: #dc3545;"><i class="bi bi-x-circle"></i> Payment Rejected</span>
                                            @endif
                                        </div>
                                        <div class="timeline-desc">
                                            Reviewed by {{ $reviewerName }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Reject Modal -->
    @if($document->status === 'pending')
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header" style="background: #ffebee; border-bottom: none;">
                        <h5 class="modal-title" style="color: #c62828;">
                            <i class="bi bi-x-circle"></i> Reject Payment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.payments.reject', $document) }}">
                        @csrf
                        <div class="modal-body">
                            <p style="color: #666; margin-bottom: 16px;">
                                You are about to reject payment <strong>#{{ $document->id }}</strong> from <strong>{{ $document->user->name ?? 'N/A' }}</strong>.
                            </p>
                            <div class="form-group">
                                <label class="form-label">Rejection Reason <span style="color: #dc3545;">*</span></label>
                                <textarea name="reject_reason" class="form-control" rows="3" placeholder="Enter a clear reason for rejection..." required style="border-radius: 8px;"></textarea>
                                <div class="form-hint">This reason will be visible to the student.</div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: none;">
                            <button type="button" class="btn-back" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-reject">Reject Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            // title/message/confirmLabel are set via textContent below, not
            // interpolated into this HTML string — all three can carry
            // server/user-controlled text.
            box.innerHTML =
                '<div style="width:52px;height:52px;border-radius:50%;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">' +
                    '<i class="bi ' + (danger ? 'bi-exclamation-triangle-fill' : 'bi-question-circle-fill') + '" style="font-size:24px;color:' + iconColor + ';"></i>' +
                '</div>' +
                '<div id="ilc-confirm-title" style="font-size:15px;font-weight:700;color:#1a3a6c;margin-bottom:6px;"></div>' +
                '<div id="ilc-confirm-message" style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:20px;"></div>' +
                '<div style="display:flex;gap:10px;">' +
                    '<button type="button" id="ilc-confirm-cancel" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;">Cancel</button>' +
                    '<button type="button" id="ilc-confirm-ok" style="flex:1;padding:10px;border-radius:9px;border:none;background:' + okBg + ';color:#fff;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;"></button>' +
                '</div>';
            box.querySelector('#ilc-confirm-title').textContent = title;
            box.querySelector('#ilc-confirm-message').textContent = message;
            box.querySelector('#ilc-confirm-ok').textContent = confirmLabel;

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

        function confirmLogout(form) {
            showConfirm('Are you sure you want to log out?', function () { form.submit(); },
                { title: 'Log Out', confirmLabel: 'Log Out', danger: true });
            return false;
        }

        function confirmApprovePayment(form) {
            showConfirm('Are you sure you want to APPROVE this payment?', function () { form.submit(); },
                { title: 'Approve Payment', confirmLabel: 'Approve' });
            return false;
        }
    </script>
</body>
</html>
