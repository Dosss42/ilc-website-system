@extends('finance.layout')

@section('title', 'Financial Reports')

@section('styles')
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
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-input {
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 7px;
        font-size: 12.5px;
    }

    .btn-filter {
        padding: 8px 14px;
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-filter:hover {
        background: var(--blue-light);
    }

    /* One row per report instead of a separate card each — the fix for
       the previous layout eating too much vertical space. */
    .rpt-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 12px 24px;
        border-bottom: 1px solid #f5f5f5;
    }
    .rpt-row:last-child { border-bottom: none; }
    .rpt-row-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--blue);
        min-width: 170px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .print-only { display: none; }
    @media print {
        .no-print, .sidebar, .topbar { display: none !important; }
        .print-only { display: block !important; margin-bottom: 20px; }
        .content-card { box-shadow: none !important; border: 1px solid #ddd; }
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
@endsection

@section('skeleton')
<div class="skel skel-header-title"></div>
<div class="skel-card">
    <div style="display:flex;gap:12px;">
        <div class="skel skel-form-fld" style="flex:1;"></div>
        <div class="skel skel-form-fld" style="width:160px;"></div>
        <div class="skel skel-form-fld" style="width:160px;"></div>
        <div class="skel skel-form-fld" style="width:150px;"></div>
    </div>
</div>
<div class="skel-row-gap">
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
    <div class="skel skel-stat-card"></div>
</div>
<div class="skel-card">
    <div class="skel skel-card-header"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
    <div class="skel skel-table-row"></div>
</div>
<div class="skel-row-gap">
    <div class="skel-card" style="flex:2;min-width:320px;">
        <div class="skel skel-card-header"></div>
        <div class="skel skel-chart"></div>
    </div>
    <div class="skel-card" style="flex:1;min-width:220px;">
        <div class="skel skel-card-header"></div>
        <div class="skel skel-chart" style="height:180px;"></div>
    </div>
</div>
@endsection

@section('content')
@php
    $reportTypeLabels = ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];
    $reportTypeLabel  = $reportTypeLabels[$reportType] ?? 'Daily';
@endphp
<div class="page-header no-print">
    <h1 class="page-title">Financial Reports</h1>
</div>

{{-- One compact card, one row per report, instead of four separate cards
     each with their own header/body padding and margin stacking up. Every
     report still has its own filter inputs (a date range for breakdown/
     summary, a school year for the two receivable-based ones — mixing them
     into one shared control would mean hiding/showing fields depending on
     what's picked, more confusing not less) and its own specifically-
     labeled download button. The plain browser Print button is gone —
     every report here already has a real, specifically-named PDF. --}}
<div class="content-card no-print">
    <div class="card-header">
        <h3 class="card-title"><i class="bi bi-file-earmark-pdf" style="color:var(--gold);"></i> Download Reports</h3>
    </div>

    <div class="rpt-row" style="background:#f8f9fa;">
        <div class="rpt-row-label"><i class="bi bi-people-fill"></i> Enrollment &amp; Payment Status</div>
        <form method="GET" action="{{ route('finance.reports.status-overview-pdf') }}" class="filters">
            <select name="school_year" class="filter-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter" style="background:#495057;">
                <i class="bi bi-file-earmark-pdf"></i> Download Enrollment &amp; Payment Status PDF
            </button>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-bar-chart-fill"></i> Collection Breakdown</div>
        <form method="GET" action="{{ route('finance.reports.index') }}" class="filters">
            <select name="type" class="filter-input">
                <option value="daily" {{ $reportType === 'daily' ? 'selected' : '' }}>Daily Report</option>
                <option value="weekly" {{ $reportType === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                <option value="yearly" {{ $reportType === 'yearly' ? 'selected' : '' }}>Yearly Report</option>
            </select>
            <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}" placeholder="From Date">
            <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}" placeholder="To Date">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Generate
            </button>
            <a href="{{ route('finance.reports.pdf', ['type' => $reportType, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                class="btn-filter" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-file-earmark-pdf"></i> Download {{ $reportTypeLabel }} Report PDF
            </a>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-clipboard2-data"></i> Collection Summary</div>
        <form method="GET" action="{{ route('finance.reports.collection-summary-pdf') }}" class="filters">
            <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}" placeholder="From Date">
            <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}" placeholder="To Date">
            <button type="submit" class="btn-filter" style="background:#2e7d32;">
                <i class="bi bi-file-earmark-pdf"></i> Download Collection Summary PDF
            </button>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-cash-coin"></i> Accounts Receivable</div>
        <form method="GET" action="{{ route('finance.installments.receivables-pdf') }}" class="filters">
            <select name="school_year" class="filter-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter" style="background:#7b1fa2;">
                <i class="bi bi-file-earmark-pdf"></i> Download Accounts Receivable Report PDF
            </button>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-hourglass-split"></i> Aging of Receivables</div>
        <form method="GET" action="{{ route('finance.reports.aging-pdf') }}" class="filters">
            <select name="school_year" class="filter-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter" style="background:#dc3545;">
                <i class="bi bi-file-earmark-pdf"></i> Download Aging of Receivables PDF
            </button>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-mortarboard-fill"></i> Collection by Grade Level</div>
        <form method="GET" action="{{ route('finance.reports.grade-level-pdf') }}" class="filters">
            <select name="school_year" class="filter-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter" style="background:#0d6efd;">
                <i class="bi bi-file-earmark-pdf"></i> Download Collection by Grade Level PDF
            </button>
        </form>
    </div>

    <div class="rpt-row">
        <div class="rpt-row-label"><i class="bi bi-pie-chart-fill"></i> Payment Plan Distribution</div>
        <form method="GET" action="{{ route('finance.reports.payment-plan-pdf') }}" class="filters">
            <select name="school_year" class="filter-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter" style="background:#e65100;">
                <i class="bi bi-file-earmark-pdf"></i> Download Payment Plan Distribution PDF
            </button>
        </form>
    </div>
</div>

<div style="margin-bottom:12px;" class="no-print">
    <a href="{{ route('finance.students.index') }}" style="font-size:12.5px;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-info-circle"></i> Need one student's <span style="color:var(--blue);font-weight:600;">Statement of Account</span>? Download it from the All Students page.
    </a>
</div>

<div class="print-only" style="display:none;">
    <h2>{{ $reportTypeLabel }} Financial Report</h2>
    <p>{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd; color: #1976d2;">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="stat-value">{{ $reportData['total_payments'] ?? 0 }}</div>
            <div class="stat-label">Total Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9; color: #2e7d32;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value">{{ $reportData['payments_by_method']['cash'] ?? 0 }}</div>
            <div class="stat-label">Cash Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0; color: #e65100;">
                <i class="bi bi-phone"></i>
            </div>
            <div class="stat-value">{{ $reportData['payments_by_method']['gcash'] ?? 0 }}</div>
            <div class="stat-label">GCash Payments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f3e5f5; color: #6a1b9a;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-value">{{ count($reportData['daily_breakdown'] ?? []) }}</div>
            <div class="stat-label">Active {{ $reportTypeLabel }} Periods</div>
        </div>
    </div>
</div>

<!-- Breakdown Table -->
<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-table" style="color: var(--gold);"></i>
            {{ $reportTypeLabel }} Payment Breakdown
        </h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Number of Payments</th>
                    <th>Amount Collected</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['daily_breakdown'] ?? [] as $period)
                    <tr>
                        <td>{{ $period->period_label }}</td>
                        <td style="font-weight: 600; color: var(--blue);">
                            {{ $period->count }} payments
                        </td>
                        <td style="font-weight: 600; color: #2e7d32;">
                            ₱{{ number_format($period->total_amount, 2) }}
                        </td>
                        <td>
                            @if($period->count > 10)
                                <span style="color: #28a745; font-weight: 600;">
                                    <i class="bi bi-graph-up-arrow"></i> High Activity
                                </span>
                            @elseif($period->count > 5)
                                <span style="color: #ffc107; font-weight: 600;">
                                    <i class="bi bi-graph-up"></i> Moderate
                                </span>
                            @else
                                <span style="color: #6c757d;">
                                    <i class="bi bi-dash-circle"></i> Low
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #666;">
                            <i class="bi bi-inbox" style="font-size: 64px; color: #ddd; display: block; margin-bottom: 16px;"></i>
                            <h5>No data available</h5>
                            <p>No payment records found for the selected date range.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Charts Row -->
@php
    $cashCount  = $reportData['payments_by_method']['cash']  ?? 0;
    $gcashCount = $reportData['payments_by_method']['gcash'] ?? 0;
    $methodTotal = $cashCount + $gcashCount;
    $dailyLabels  = collect($reportData['daily_breakdown'] ?? [])->pluck('period_label')->reverse()->values();
    $dailyCounts  = collect($reportData['daily_breakdown'] ?? [])->pluck('count')->reverse()->values();
@endphp

<div class="row mb-4">
    {{-- Daily Activity Bar Chart --}}
    <div class="col-lg-8 mb-4">
        <div class="content-card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-bar-chart-fill" style="color: var(--gold);"></i>
                    {{ $reportTypeLabel }} Payment Activity
                </h3>
            </div>
            <div class="card-body">
                @if(count($reportData['daily_breakdown'] ?? []) > 0)
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#aaa;">
                        <i class="bi bi-bar-chart" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                        No activity data for selected period
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Method Doughnut Chart --}}
    <div class="col-lg-4 mb-4">
        <div class="content-card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pie-chart-fill" style="color: var(--gold);"></i>
                    Payment Methods
                </h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                @if($methodTotal > 0)
                    <div style="max-width:220px; width:100%;">
                        <canvas id="methodChart"></canvas>
                    </div>
                    <div class="d-flex gap-4 mt-3" style="font-size:13px;">
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#2e7d32;margin-right:5px;"></span>Cash ({{ $cashCount }})</span>
                        <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#1976d2;margin-right:5px;"></span>GCash ({{ $gcashCount }})</span>
                    </div>
                @else
                    <div style="text-align:center; padding:40px 20px; color:#aaa;">
                        <i class="bi bi-pie-chart" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                        No payment data
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(count($reportData['daily_breakdown'] ?? []) > 0)
(function() {
    var labels = @json($dailyLabels);
    var counts = @json($dailyCounts);
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
@endif

@if($methodTotal > 0)
(function() {
    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'GCash'],
            datasets: [{
                data: [{{ $cashCount }}, {{ $gcashCount }}],
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
                            var pct = Math.round(ctx.parsed / {{ $methodTotal }} * 100);
                            return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            },
            cutout: '65%',
        }
    });
})();
@endif
</script>
@endsection
