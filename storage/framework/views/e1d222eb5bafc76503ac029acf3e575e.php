<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Users Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1e3a5f;
            --gold: #c5a059;
            --blue-light: #2c5282;
        }
        
        * { font-family: 'Poppins', sans-serif; }
        
        body {
            background: #f5f6fa;
            min-height: 100vh;
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
            font-size: 18px;
            font-weight: 600;
            color: var(--blue);
        }
        
        .card-body {
            padding: 24px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--blue-light));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .data-table td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-badge.inactive {
            background: #ffebee;
            color: #c62828;
        }
        
        .btn-toggle {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        
        .btn-toggle.deactivate {
            background: #ffebee;
            color: #c62828;
        }
        
        .btn-toggle.activate {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--blue);">
                <i class="bi bi-people" style="color: var(--gold);"></i>
                Finance Users Management
            </h1>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Admin
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Create New Finance User -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-person-plus" style="color: var(--gold);"></i>
                    Create New Finance User
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.finance.create')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 13px; font-weight: 600;">Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 13px; font-weight: 600;">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 13px; font-weight: 600;">Password</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Create Finance User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Finance Users List -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-list" style="color: var(--gold);"></i>
                    Finance Users
                </h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $financeUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($user->id); ?></td>
                                <td style="font-weight: 600;"><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php if($user->is_active): ?>
                                        <span class="status-badge active">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="bi bi-x-circle"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('admin.finance.toggle-status', $user)); ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to change this user status?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-toggle <?php echo e($user->is_active ? 'deactivate' : 'activate'); ?>">
                                            <?php if($user->is_active): ?>
                                                <i class="bi bi-person-x"></i> Deactivate
                                            <?php else: ?>
                                                <i class="bi bi-person-check"></i> Activate
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px;">
                                    <i class="bi bi-inbox" style="font-size: 48px; color: #ddd;"></i>
                                    <p style="margin-top: 16px; color: #666;">No finance users found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if($financeUsers->hasPages()): ?>
                    <div style="padding: 16px;">
                        <?php echo e($financeUsers->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\finance\users.blade.php ENDPATH**/ ?>