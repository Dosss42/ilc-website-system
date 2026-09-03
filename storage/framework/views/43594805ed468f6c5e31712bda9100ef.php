<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Details - <?php echo e($enrollment->reference_number); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/adminDashboard.css">
</head>
<body>
    <?php echo $__env->make('partials.admin-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container-fluid">
        <div class="row">
            <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.enrollments.index')); ?>">Enrollments</a></li>
                        <li class="breadcrumb-item active"><?php echo e($enrollment->reference_number); ?></li>
                    </ol>
                </nav>

                <!-- Success/Error Messages -->
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Status Badge -->
                <div class="mb-3">
                    <?php switch($enrollment->status):
                        case ('pending'): ?>
                            <span class="badge bg-warning fs-6">Pending Review</span>
                            <?php break; ?>
                        <?php case ('approved'): ?>
                            <span class="badge bg-success fs-6">Approved</span>
                            <?php break; ?>
                        <?php case ('declined'): ?>
                            <span class="badge bg-danger fs-6">Declined</span>
                            <?php break; ?>
                        <?php case ('enrolled'): ?>
                            <span class="badge bg-primary fs-6">Enrolled</span>
                            <?php break; ?>
                    <?php endswitch; ?>
                </div>

                <!-- Student Information -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Student Information</h5>
                                <small class="text-muted">Ref: <?php echo e($enrollment->reference_number); ?></small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Full Name:</strong> <?php echo e($enrollment->full_name); ?></p>
                                        <p><strong>Gender:</strong> <?php echo e(ucfirst($enrollment->student_data['gender'])); ?></p>
                                        <p><strong>Date of Birth:</strong> <?php echo e(\Carbon\Carbon::parse($enrollment->student_data['birthdate'])->format('F d, Y')); ?></p>
                                        <p><strong>Place of Birth:</strong> <?php echo e($enrollment->student_data['place_of_birth']); ?></p>
                                        <p><strong>Religion:</strong> <?php echo e($enrollment->student_data['religion'] ?: 'N/A'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Grade Level:</strong> <?php echo e($enrollment->grade_level_display); ?></p>
                                        <p><strong>Student Type:</strong> <?php echo e($enrollment->student_type_display); ?></p>
                                        <p><strong>Last School:</strong> <?php echo e($enrollment->student_data['last_school'] ?: 'N/A'); ?></p>
                                        <p><strong>Contact:</strong> <?php echo e($enrollment->student_data['contact']); ?></p>
                                        <p><strong>Email:</strong> <?php echo e($enrollment->student_data['student_email']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Address Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Complete Address:</strong></p>
                                <p class="ms-3">
                                    <?php echo e($enrollment->student_data['street_address']); ?>, <?php echo e($enrollment->student_data['barangay']); ?><br>
                                    <?php echo e($enrollment->student_data['city']); ?>, <?php echo e($enrollment->student_data['province']); ?><br>
                                    <?php echo e($enrollment->student_data['region']); ?> <?php echo e($enrollment->student_data['zip_code'] ? $enrollment->student_data['zip_code'] : ''); ?>

                                </p>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Parent/Guardian Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Guardian Name:</strong> <?php echo e($enrollment->student_data['guardian_name']); ?></p>
                                        <p><strong>Relationship:</strong> <?php echo e(ucfirst($enrollment->student_data['relationship'])); ?></p>
                                        <p><strong>Occupation:</strong> <?php echo e($enrollment->student_data['guardian_occupation'] ?: 'N/A'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Mobile:</strong> <?php echo e($enrollment->student_data['guardian_phone']); ?></p>
                                        <p><strong>Email:</strong> <?php echo e($enrollment->student_data['guardian_email'] ?: 'N/A'); ?></p>
                                        <p><strong>Facebook:</strong> <?php echo e($enrollment->student_data['guardian_facebook'] ?: 'N/A'); ?></p>
                                    </div>
                                </div>
                                <?php if($enrollment->student_data['guardian_address']): ?>
                                    <p><strong>Guardian Address:</strong> <?php echo e($enrollment->student_data['guardian_address']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Sidebar -->
                    <div class="col-md-4">
                        <!-- Application Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Application Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <?php switch($enrollment->status):
                                        case ('pending'): ?>
                                            <i class="bi bi-clock-history text-warning fs-1"></i>
                                            <p class="text-warning mt-2">Pending Review</p>
                                            <?php break; ?>
                                        <?php case ('approved'): ?>
                                            <i class="bi bi-check-circle text-success fs-1"></i>
                                            <p class="text-success mt-2">Approved</p>
                                            <?php break; ?>
                                        <?php case ('declined'): ?>
                                            <i class="bi bi-x-circle text-danger fs-1"></i>
                                            <p class="text-danger mt-2">Declined</p>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </div>

                                <?php if($enrollment->status === 'pending'): ?>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="bi bi-check-circle me-2"></i>Approve Enrollment
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#declineModal">
                                            <i class="bi bi-x-circle me-2"></i>Decline Enrollment
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if($enrollment->status === 'declined'): ?>
                                    <div class="alert alert-danger">
                                        <strong>Decline Reason:</strong><br>
                                        <?php echo e($enrollment->decline_reason); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Status:</strong> 
                                    <?php switch($enrollment->payment_status):
                                        case ('pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                            <?php break; ?>
                                        <?php case ('paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                            <?php break; ?>
                                        <?php case ('partial'): ?>
                                            <span class="badge bg-info">Partial</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                    <?php endswitch; ?>
                                </p>
                                <?php if($enrollment->payment_amount): ?>
                                    <p><strong>Amount:</strong> PHP <?php echo e(number_format($enrollment->payment_amount, 2)); ?></p>
                                <?php endif; ?>
                                <?php if($enrollment->payment_method): ?>
                                    <p><strong>Method:</strong> <?php echo e(ucfirst($enrollment->payment_method)); ?></p>
                                <?php endif; ?>
                                <?php if($enrollment->payment_reference): ?>
                                    <p><strong>Reference:</strong> <?php echo e($enrollment->payment_reference); ?></p>
                                <?php endif; ?>

                                <?php if($enrollment->status === 'approved'): ?>
                                    <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                        <i class="bi bi-cash me-2"></i>Update Payment
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Application Timeline -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <small class="text-muted">Applied</small><br>
                                        <strong><?php echo e($enrollment->created_at->format('M d, Y h:i A')); ?></strong>
                                    </div>
                                    <?php if($enrollment->approved_at): ?>
                                        <div class="timeline-item">
                                            <small class="text-muted">Approved</small><br>
                                            <strong><?php echo e($enrollment->approved_at->format('M d, Y h:i A')); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($enrollment->declined_at): ?>
                                        <div class="timeline-item">
                                            <small class="text-muted">Declined</small><br>
                                            <strong><?php echo e($enrollment->declined_at->format('M d, Y h:i A')); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.enrollments.approve', $enrollment)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve this enrollment?</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            A student account will be created and login credentials will be sent to <?php echo e($enrollment->student_data['student_email']); ?>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Approve Enrollment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Decline Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.enrollments.decline', $enrollment)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Decline Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for declining *</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required
                                      placeholder="Please provide a reason for declining this enrollment application..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            The student will be notified of this decision via email.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i>Decline Enrollment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.enrollments.payment', $enrollment)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Update Payment Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="payment_status" class="form-label">Payment Status *</label>
                            <select name="payment_status" id="payment_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="partial">Partial Payment</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_amount" class="form-label">Amount Paid *</label>
                            <input type="number" name="payment_amount" id="payment_amount" class="form-control" 
                                   step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">Select Method</option>
                                <option value="gcash">GCash</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_reference" class="form-label">Reference Number</label>
                            <input type="text" name="payment_reference" id="payment_reference" class="form-control" 
                                   placeholder="Transaction reference or receipt number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\admin\enrollments\show.blade.php ENDPATH**/ ?>