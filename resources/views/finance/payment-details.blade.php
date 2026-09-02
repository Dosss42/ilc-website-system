<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Finance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1e3a5f;
            --gold: #c5a059;
            --blue-light: #2c5282;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        * { font-family: 'Poppins', sans-serif; }
        
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
            background: linear-gradient(180deg, var(--blue) 0%, var(--blue-light) 100%);
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
        
        .sidebar-menu {
            padding: 16px 0;
        }
        
        .menu-section {
            padding: 8px 20px;
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--gold);
        }
        
        .menu-item i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
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
        <div class="sidebar-header">
            <a href="{{ route('finance.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-wallet2"></i>
                <span>Finance Portal</span>
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
            <a href="{{ route('finance.profile') }}" class="menu-item">
                <i class="bi bi-person-circle"></i>
                My Profile
            </a>
            <a href="{{ route('finance.change-password') }}" class="menu-item">
                <i class="bi bi-shield-lock"></i>
                Change Password
            </a>
            <form method="POST" action="{{ route('finance.logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer;">
                    <i class="bi bi-box-arrow-left"></i>
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <a href="{{ route('finance.payments.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Back to Payments
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
                                <div class="info-value">{{ $document->processedBy->name ?? 'System' }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Reviewed On</div>
                                <div class="info-value">{{ $document->processed_at ? $document->processed_at->format('F d, Y h:i A') : $document->updated_at->format('F d, Y h:i A') }}</div>
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
                                <form method="POST" action="{{ route('admin.payments.approve', $document) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-approve" onclick="return confirm('Are you sure you want to APPROVE this payment?')">
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
                                        <div class="timeline-time">{{ $document->processed_at ? $document->processed_at->format('M d, Y h:i A') : $document->updated_at->format('M d, Y h:i A') }}</div>
                                        <div class="timeline-title">
                                            @if($document->status === 'approved')
                                                <span style="color: #28a745;"><i class="bi bi-check-circle"></i> Payment Approved</span>
                                            @else
                                                <span style="color: #dc3545;"><i class="bi bi-x-circle"></i> Payment Rejected</span>
                                            @endif
                                        </div>
                                        <div class="timeline-desc">
                                            Reviewed by {{ $document->processedBy->name ?? 'System' }}
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
</body>
</html>
