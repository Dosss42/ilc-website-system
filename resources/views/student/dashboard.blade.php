@include('partials.student-header')

<style>
    .progress-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
    }
    .progress-percentage {
        font-size: 48px;
        font-weight: 700;
        line-height: 1;
    }
    .progress-label {
        font-size: 14px;
        opacity: 0.9;
    }
    .requirement-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s;
    }
    .requirement-item.completed {
        border-color: #28a745;
        background: #f8fff9;
    }
    .requirement-item i {
        font-size: 20px;
        width: 30px;
        text-align: center;
    }
    .requirement-item.completed i {
        color: #28a745;
    }
    .status-badge {
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .status-completed { background: #d4edda; color: #155724; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-missing { background: #f8d7da; color: #721c24; }
    .quick-action-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .quick-action-card i {
        font-size: 40px;
        color: #667eea;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
        <div class="row">
            @include('partials.student-sidebar')
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">Welcome back, {{ Auth::user()->name }}!</span>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($enrollment)
                    <!-- Progress Overview -->
                    <div class="progress-card">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="progress-percentage">{{ $progress['percentage'] }}%</div>
                                <div class="progress-label">Profile Completion</div>
                            </div>
                            <div class="col-md-8">
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $progress['percentage'] }}%; font-weight: 600;"
                                         aria-valuenow="{{ $progress['percentage'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $progress['completed'] }} of {{ $progress['total'] }} Complete
                                    </div>
                                </div>
                                <div class="text-white">
                                    <small>
                                        @switch($progress['status'])
                                            @case('completed')
                                                <i class="bi bi-check-circle-fill me-1"></i> Excellent! Your profile is complete.
                                                @break
                                            @case('almost_complete')
                                                <i class="bi bi-exclamation-circle-fill me-1"></i> Almost there! Just a few more items.
                                                @break
                                            @case('in_progress')
                                                <i class="bi bi-hourglass-split me-1"></i> Good progress! Keep going.
                                                @break
                                            @default
                                                <i class="bi bi-info-circle-fill me-1"></i> Let's get started! Complete your profile.
                                        @endswitch
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Requirements Checklist -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Enrollment Requirements</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($progress['sections'] as $section => $completed)
                                        <div class="requirement-item {{ $completed ? 'completed' : '' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $completed ? 'bi-check-circle-fill' : 'bi-circle' }} me-3"></i>
                                                    <div>
                                                        <strong>{{ $this->getRequirementTitle($section) }}</strong>
                                                        <div class="text-muted small">{{ $this->getRequirementDescription($section) }}</div>
                                                    </div>
                                                </div>
                                                <span class="status-badge {{ $completed ? 'status-completed' : 'status-missing' }}">
                                                    {{ $completed ? 'Completed' : 'Missing' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('student.info') }}" class="quick-action-card text-decoration-none">
                                        <i class="bi bi-person-fill"></i>
                                        <h6>Update Information</h6>
                                        <small class="text-muted">Complete your profile</small>
                                    </a>
                                    
                                    <a href="{{ route('student.documents') }}" class="quick-action-card text-decoration-none">
                                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                        <h6>Upload Documents</h6>
                                        <small class="text-muted">Submit requirements</small>
                                    </a>
                                    
                                    <div class="quick-action-card text-decoration-none">
                                        <i class="bi bi-credit-card-fill"></i>
                                        <h6>Payment Status</h6>
                                        <small class="text-muted">View and pay fees</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Status -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Enrollment Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Reference Number:</strong> <code>{{ $enrollment->reference_number }}</code></p>
                                    <p><strong>Grade Level:</strong> {{ $enrollment->grade_level_display }}</p>
                                    <p><strong>Student Type:</strong> {{ $enrollment->student_type_display }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Application Date:</strong> {{ $enrollment->created_at->format('M d, Y') }}</p>
                                    <p><strong>Status:</strong> 
                                        @switch($enrollment->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending Review</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success">Approved</span>
                                                @break
                                            @case('declined')
                                                <span class="badge bg-danger">Declined</span>
                                                @break
                                            @case('enrolled')
                                                <span class="badge bg-primary">Enrolled</span>
                                                @break
                                        @endswitch
                                    </p>
                                    <p><strong>Payment Status:</strong>
                                        @switch($enrollment->payment_status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('paid')
                                                <span class="badge bg-success">Paid</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info">Partial</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">N/A</span>
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Enrollment Message -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            No Active Enrollment
                        </h5>
                        <p class="mb-3">You don't have any active enrollment records. Please complete the enrollment process to access your full dashboard features.</p>
                        <hr>
                        <p class="mb-0">
                            <a href="{{ route('admission') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Start New Enrollment
                            </a>
                        </p>
                    </div>
                @endif

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Helper function for view
function getRequirementTitle($section) {
    $titles = [
        'personal_info' => 'Personal Information',
        'address_info' => 'Address Information', 
        'guardian_info' => 'Guardian Information',
        'birth_certificate' => 'Birth Certificate',
        'form_137' => 'Form 137 (Permanent Record)',
        'form_138' => 'Form 138 (Report Card)',
        'good_moral' => 'Certificate of Good Moral Character',
        'barangay_clearance' => 'Barangay Clearance',
    ];
    return $titles[$section] ?? $section;
}

function getRequirementDescription($section) {
    $descriptions = [
        'personal_info' => 'Complete your personal details',
        'address_info' => 'Provide your complete address',
        'guardian_info' => 'Add parent/guardian information',
        'birth_certificate' => 'Upload PSA Birth Certificate',
        'form_137' => 'Upload Permanent Record (Form 137)',
        'form_138' => 'Upload Report Card (Form 138)',
        'good_moral' => 'Upload Certificate of Good Moral Character',
        'barangay_clearance' => 'Upload Barangay Clearance Certificate',
    ];
    return $descriptions[$section] ?? 'Complete this requirement';
}
?>
