<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/adminDashboard.css">
</head>
<body>
    @include('partials.admin-header')

    <div class="container-fluid">
        <div class="row">
            @include('partials.admin-sidebar')
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Enrollment Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download"></i> Export
                            </button>
                        </div>
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

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center" style="border-left: 4px solid #ffc107;">
                            <div class="card-body">
                                <h5 class="card-title text-warning">{{ $enrollments->where('status', 'pending')->count() }}</h5>
                                <p class="card-text">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center" style="border-left: 4px solid #28a745;">
                            <div class="card-body">
                                <h5 class="card-title text-success">{{ $enrollments->where('status', 'approved')->count() }}</h5>
                                <p class="card-text">Approved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center" style="border-left: 4px solid #dc3545;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">{{ $enrollments->where('status', 'declined')->count() }}</h5>
                                <p class="card-text">Declined</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center" style="border-left: 4px solid #17a2b8;">
                            <div class="card-body">
                                <h5 class="card-title text-info">{{ $enrollments->where('payment_status', 'paid')->count() }}</h5>
                                <p class="card-text">Paid</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="mb-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">All ({{ $enrollments->total() }})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Pending</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Approved</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Declined</a>
                        </li>
                    </ul>
                </div>

                <!-- Enrollment Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Reference #</th>
                                <th>Student Name</th>
                                <th>Grade Level</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td><code>{{ $enrollment->reference_number }}</code></td>
                                    <td>{{ $enrollment->full_name }}</td>
                                    <td>{{ $enrollment->grade_level_display }}</td>
                                    <td>{{ $enrollment->student_type_display }}</td>
                                    <td>
                                        @switch($enrollment->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
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
                                            @default
                                                <span class="badge bg-secondary">{{ $enrollment->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            @if($enrollment->status === 'pending')
                                                <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" 
                                                            onclick="return confirm('Approve this enrollment?')" title="Approve">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No enrollment applications found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }} 
                        of {{ $enrollments->total() }} entries
                    </div>
                    <div>
                        {{ $enrollments->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
