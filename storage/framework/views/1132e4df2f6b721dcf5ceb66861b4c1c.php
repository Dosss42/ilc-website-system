<?php $__env->startSection('title', 'Financial Reports'); ?>

<?php $__env->startSection('styles'); ?>
<style>
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

    .filters {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-input {
        padding: 10px 16px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 13px;
    }

    .btn-filter {
        padding: 10px 20px;
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-filter:hover {
        background: var(--blue-light);
    }

    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--blue);
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
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
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
    }

    .chart-container {
        height: 300px;
        margin-top: 20px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Financial Reports</h1>
</div>

<!-- Report Filters -->
<div class="content-card">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('finance.reports.index')); ?>" class="filters">
            <select name="type" class="filter-input">
                <option value="daily" <?php echo e($reportType === 'daily' ? 'selected' : ''); ?>>Daily Report</option>
                <option value="weekly" <?php echo e($reportType === 'weekly' ? 'selected' : ''); ?>>Weekly Report</option>
                <option value="monthly" <?php echo e($reportType === 'monthly' ? 'selected' : ''); ?>>Monthly Report</option>
                <option value="yearly" <?php echo e($reportType === 'yearly' ? 'selected' : ''); ?>>Yearly Report</option>
            </select>
            <input type="date" name="date_from" class="filter-input" value="<?php echo e($dateFrom); ?>" placeholder="From Date">
            <input type="date" name="date_to" class="filter-input" value="<?php echo e($dateTo); ?>" placeholder="To Date">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Generate Report
            </button>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd; color: #1976d2;">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="stat-value"><?php echo e($reportData['total_payments'] ?? 0); ?></div>
            <div class="stat-label">Total Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9; color: #2e7d32;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value"><?php echo e($reportData['payments_by_method']['cash'] ?? 0); ?></div>
            <div class="stat-label">Cash Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0; color: #e65100;">
                <i class="bi bi-phone"></i>
            </div>
            <div class="stat-value"><?php echo e($reportData['payments_by_method']['gcash'] ?? 0); ?></div>
            <div class="stat-label">GCash Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f3e5f5; color: #6a1b9a;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo e(count($reportData['daily_breakdown'] ?? [])); ?></div>
            <div class="stat-label">Active Days</div>
        </div>
    </div>
</div>

<!-- Daily Breakdown Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-table" style="color: var(--gold);"></i>
            Daily Payment Breakdown
        </h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Number of Payments</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['daily_breakdown'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($day->date)->format('F d, Y')); ?></td>
                        <td style="font-weight: 600; color: var(--blue);">
                            <?php echo e($day->count); ?> payments
                        </td>
                        <td>
                            <?php if($day->count > 10): ?>
                                <span style="color: #28a745; font-weight: 600;">
                                    <i class="bi bi-graph-up-arrow"></i> High Activity
                                </span>
                            <?php elseif($day->count > 5): ?>
                                <span style="color: #ffc107; font-weight: 600;">
                                    <i class="bi bi-graph-up"></i> Moderate
                                </span>
                            <?php else: ?>
                                <span style="color: #6c757d;">
                                    <i class="bi bi-dash-circle"></i> Low
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 60px; color: #666;">
                            <i class="bi bi-inbox" style="font-size: 64px; color: #ddd; display: block; margin-bottom: 16px;"></i>
                            <h5>No data available</h5>
                            <p>No payment records found for the selected date range.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Charts Row -->
<?php
    $cashCount  = $reportData['payments_by_method']['cash']  ?? 0;
    $gcashCount = $reportData['payments_by_method']['gcash'] ?? 0;
    $methodTotal = $cashCount + $gcashCount;
    $dailyLabels  = collect($reportData['daily_breakdown'] ?? [])->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->values();
    $dailyCounts  = collect($reportData['daily_breakdown'] ?? [])->pluck('count')->values();
?>

<div class="row mb-4">
    
    <div class="col-lg-8 mb-4">
        <div class="content-card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-bar-chart-fill" style="color: var(--gold);"></i>
                    Daily Payment Activity
                </h3>
            </div>
            <div class="card-body">
                <?php if(count($reportData['daily_breakdown'] ?? []) > 0): ?>
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:60px 20px; color:#aaa;">
                        <i class="bi bi-bar-chart" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                        No activity data for selected period
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4 mb-4">
        <div class="content-card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pie-chart-fill" style="color: var(--gold);"></i>
                    Payment Methods
                </h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <?php if($methodTotal > 0): ?>
                    <div style="max-width:220px; width:100%;">
                        <canvas id="methodChart"></canvas>
                    </div>
                    <div class="d-flex gap-4 mt-3" style="font-size:13px;">
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#2e7d32;margin-right:5px;"></span>Cash (<?php echo e($cashCount); ?>)</span>
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#1976d2;margin-right:5px;"></span>GCash (<?php echo e($gcashCount); ?>)</span>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:40px 20px; color:#aaa;">
                        <i class="bi bi-pie-chart" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                        No payment data
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Daily Breakdown Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-table" style="color: var(--gold);"></i>
            Daily Payment Breakdown
        </h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Number of Payments</th>
                    <th>Activity Level</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['daily_breakdown'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($day->date)->format('F d, Y')); ?></td>
                        <td style="font-weight: 600; color: var(--blue);"><?php echo e($day->count); ?> payments</td>
                        <td>
                            <?php if($day->count > 10): ?>
                                <span style="color:#28a745; font-weight:600;"><i class="bi bi-graph-up-arrow"></i> High Activity</span>
                            <?php elseif($day->count > 5): ?>
                                <span style="color:#ffc107; font-weight:600;"><i class="bi bi-graph-up"></i> Moderate</span>
                            <?php else: ?>
                                <span style="color:#6c757d;"><i class="bi bi-dash-circle"></i> Low</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding:60px; color:#666;">
                            <i class="bi bi-inbox" style="font-size:64px; color:#ddd; display:block; margin-bottom:16px;"></i>
                            <h5>No data available</h5>
                            <p>No payment records found for the selected date range.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if(count($reportData['daily_breakdown'] ?? []) > 0): ?>
(function() {
    var labels = <?php echo json_encode($dailyLabels, 15, 512) ?>;
    var counts = <?php echo json_encode($dailyCounts, 15, 512) ?>;
    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Payments',
                data: counts,
                backgroundColor: 'rgba(30,58,95,0.75)',
                borderColor: '#1e3a5f',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxRotation: 45 } }
            }
        }
    });
})();
<?php endif; ?>

<?php if($methodTotal > 0): ?>
(function() {
    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'GCash'],
            datasets: [{
                data: [<?php echo e($cashCount); ?>, <?php echo e($gcashCount); ?>],
                backgroundColor: ['#2e7d32', '#1976d2'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var pct = Math.round(ctx.parsed / <?php echo e($methodTotal); ?> * 100);
                            return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            },
            cutout: '65%',
        }
    });
})();
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('finance.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/finance/reports.blade.php ENDPATH**/ ?>