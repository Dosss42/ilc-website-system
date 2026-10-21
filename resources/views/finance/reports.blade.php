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

    /* ── Download Reports: grid of report cards ── */
    .rpt-head { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; padding:20px 24px; border-bottom:1px solid #f0f0f0; }
    .rpt-head h3 { font-size:16px; font-weight:700; color:var(--blue); margin:0; display:flex; align-items:center; gap:8px; }
    .rpt-head p { font-size:12.5px; color:#6b7280; margin:3px 0 0; }
    .rpt-group-title { font-size:11px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#94a3b8; padding:18px 24px 0; display:flex; align-items:center; gap:6px; }
    .rpt-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(290px, 1fr)); gap:16px; padding:12px 24px 4px; }
    .rpt-tile { background:#fff; border:1px solid #e9edf2; border-radius:12px; padding:16px; display:flex; flex-direction:column; gap:14px; transition:box-shadow .2s, border-color .2s, transform .2s; }
    .rpt-tile:hover { border-color:#d3dcea; box-shadow:0 6px 18px rgba(26,58,108,.08); transform:translateY(-1px); }
    .rpt-tile-head { display:flex; gap:12px; align-items:flex-start; }
    .rpt-ico { width:40px; height:40px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:18px; }
    .rpt-ico.blue   { background:#e8effa; color:#1a3a6c; }
    .rpt-ico.green  { background:#e8f5e9; color:#2e7d32; }
    .rpt-ico.teal   { background:#e0f2f1; color:#00796b; }
    .rpt-ico.purple { background:#f3e8fb; color:#7b1fa2; }
    .rpt-ico.red    { background:#fdecec; color:#c62828; }
    .rpt-ico.orange { background:#fff1e3; color:#e65100; }
    .rpt-ico.slate  { background:#eef1f5; color:#475569; }
    .rpt-tile h4 { font-size:14px; font-weight:700; color:var(--blue); margin:0; line-height:1.3; }
    .rpt-tile-head p { font-size:12px; color:#6b7280; margin:3px 0 0; line-height:1.45; }
    .rpt-form { display:flex; flex-direction:column; gap:10px; margin-top:auto; }
    .rpt-fields { display:flex; gap:8px; }
    .rpt-field { flex:1; min-width:0; }
    .rpt-field label { display:block; font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
    .rpt-input { width:100%; padding:8px 10px; border:1px solid #e0e4ea; border-radius:8px; font-size:12.5px; background:#fafbfc; color:#1f2937; transition:border-color .2s, box-shadow .2s; }
    .rpt-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,58,108,.08); background:#fff; outline:none; }
    .rpt-actions { display:flex; gap:8px; }
    .rpt-btn { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 12px; border-radius:8px; font-size:12.5px; font-weight:600; border:1.5px solid var(--blue); background:var(--blue); color:#fff; cursor:pointer; text-decoration:none; transition:background .2s, border-color .2s; white-space:nowrap; }
    .rpt-btn:hover { background:var(--blue-light); border-color:var(--blue-light); color:#fff; }
    .rpt-btn.outline { background:#fff; color:var(--blue); border-color:#d6deea; }
    .rpt-btn.outline:hover { background:#f3f6fb; border-color:var(--blue); color:var(--blue); }
    .rpt-foot { margin-top:16px; padding:12px 24px; border-top:1px solid #f0f0f0; background:#fafbfc; border-radius:0 0 12px 12px; }
    .rpt-foot a { font-size:12.5px; color:#6b7280; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .rpt-foot a:hover span { text-decoration:underline; }
    @media (max-width: 576px) {
        .rpt-head, .rpt-group-title, .rpt-grid, .rpt-foot { padding-left:16px; padding-right:16px; }
        .rpt-grid { grid-template-columns:1fr; }
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

@php
    // School-year reports share the same card layout; only these details differ.
    $yearReports = [
        ['title' => 'Enrollment & Payment Status', 'desc' => 'Enrolled students and their payment status for the school year.', 'icon' => 'bi-people-fill', 'tint' => 'slate', 'route' => 'finance.reports.status-overview-pdf', 'group' => 'receivables'],
        ['title' => 'Collection by Grade Level',   'desc' => 'Collections grouped by grade level for the school year.',          'icon' => 'bi-mortarboard-fill', 'tint' => 'blue', 'route' => 'finance.reports.grade-level-pdf', 'group' => 'collections'],
        ['title' => 'Accounts Receivable',         'desc' => 'Outstanding balances still owed by students.',                     'icon' => 'bi-cash-coin', 'tint' => 'purple', 'route' => 'finance.installments.receivables-pdf', 'group' => 'receivables'],
        ['title' => 'Aging of Receivables',        'desc' => 'Unpaid balances grouped by how long they have been overdue.',      'icon' => 'bi-hourglass-split', 'tint' => 'red', 'route' => 'finance.reports.aging-pdf', 'group' => 'receivables'],
        ['title' => 'Payment Plan Distribution',   'desc' => 'How students are spread across the payment plans.',                'icon' => 'bi-pie-chart-fill', 'tint' => 'orange', 'route' => 'finance.reports.payment-plan-pdf', 'group' => 'receivables'],
    ];
@endphp

<div class="content-card no-print">
    <div class="rpt-head">
        <div>
            <h3><i class="bi bi-file-earmark-pdf" style="color:var(--gold);"></i> Download Reports</h3>
            <p>Choose the filters on a report, then download it as a PDF.</p>
        </div>
    </div>

    {{-- ── Collections ── --}}
    <div class="rpt-group-title"><i class="bi bi-wallet2"></i> Collections</div>
    <div class="rpt-grid">
        <div class="rpt-tile">
            <div class="rpt-tile-head">
                <div class="rpt-ico blue"><i class="bi bi-bar-chart-fill"></i></div>
                <div>
                    <h4>Collection Breakdown</h4>
                    <p>Payments collected per day, week, month or year. Generate shows it on this page.</p>
                </div>
            </div>
            <form method="GET" action="{{ route('finance.reports.index') }}" class="rpt-form">
                <div class="rpt-field">
                    <label>Group by</label>
                    <select name="type" class="rpt-input">
                        <option value="daily" {{ $reportType === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $reportType === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $reportType === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="rpt-fields">
                    <div class="rpt-field"><label>From</label><input type="date" name="date_from" class="rpt-input" value="{{ $dateFrom }}"></div>
                    <div class="rpt-field"><label>To</label><input type="date" name="date_to" class="rpt-input" value="{{ $dateTo }}"></div>
                </div>
                <div class="rpt-actions">
                    <button type="submit" class="rpt-btn outline"><i class="bi bi-search"></i> Generate</button>
                    {{-- formaction: downloads using the filters as currently typed, not the last generated ones --}}
                    <button type="submit" formaction="{{ route('finance.reports.pdf') }}" class="rpt-btn"><i class="bi bi-download"></i> Download PDF</button>
                </div>
            </form>
        </div>

        <div class="rpt-tile">
            <div class="rpt-tile-head">
                <div class="rpt-ico green"><i class="bi bi-clipboard2-data"></i></div>
                <div>
                    <h4>Collection Summary</h4>
                    <p>Total amount collected within a date range.</p>
                </div>
            </div>
            <form method="GET" action="{{ route('finance.reports.collection-summary-pdf') }}" class="rpt-form">
                <div class="rpt-fields">
                    <div class="rpt-field"><label>From</label><input type="date" name="date_from" class="rpt-input" value="{{ $dateFrom }}"></div>
                    <div class="rpt-field"><label>To</label><input type="date" name="date_to" class="rpt-input" value="{{ $dateTo }}"></div>
                </div>
                <button type="submit" class="rpt-btn"><i class="bi bi-download"></i> Download PDF</button>
            </form>
        </div>

        @foreach(array_filter($yearReports, fn ($r) => $r['group'] === 'collections') as $r)
            @include('finance.partials.report-year-tile', ['r' => $r])
        @endforeach
    </div>

    {{-- ── Receivables & Enrollment ── --}}
    <div class="rpt-group-title"><i class="bi bi-journal-text"></i> Receivables &amp; Enrollment</div>
    <div class="rpt-grid">
        @foreach(array_filter($yearReports, fn ($r) => $r['group'] === 'receivables') as $r)
            @include('finance.partials.report-year-tile', ['r' => $r])
        @endforeach
    </div>

    <div class="rpt-foot">
        <a href="{{ route('finance.students.index') }}">
            <i class="bi bi-info-circle"></i> Need one student's <span style="color:var(--blue);font-weight:600;">Statement of Account</span>? Download it from the All Students page.
        </a>
    </div>
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
