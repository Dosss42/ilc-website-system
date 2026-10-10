<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }} — {{ $enrollment->user->name ?? '' }}</title>
@include('pdf.shared._styles')
<style>
    .stmt-info { display: table; width: 100%; margin-bottom: 16px; }
    .stmt-info-col { display: table-cell; width: 50%; vertical-align: top; }
    .stmt-info-row { font-size: 10.5px; margin-bottom: 4px; }
    .stmt-info-label { color: #666; display: inline-block; width: 110px; }
    .stmt-info-value { font-weight: bold; color: #1a1a1a; }
    .stmt-section-title { font-size: 11px; font-weight: bold; color: #1a3a6c; text-transform: uppercase; letter-spacing: 0.5px; margin: 16px 0 6px; padding-bottom: 4px; border-bottom: 1.5px solid #1a3a6c; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="stmt-info">
        <div class="stmt-info-col">
            <div class="stmt-info-row"><span class="stmt-info-label">Student:</span> <span class="stmt-info-value">{{ $enrollment->user->name ?? '—' }}</span></div>
            <div class="stmt-info-row"><span class="stmt-info-label">Grade Level:</span> <span class="stmt-info-value">{{ ucfirst($enrollment->grade_level ?? '—') }}</span></div>
            <div class="stmt-info-row"><span class="stmt-info-label">School Year:</span> <span class="stmt-info-value">{{ $enrollment->school_year ?? '—' }}</span></div>
        </div>
        <div class="stmt-info-col">
            <div class="stmt-info-row"><span class="stmt-info-label">Payment Plan:</span> <span class="stmt-info-value">{{ $enrollment->payment_option ? 'Plan ' . $enrollment->payment_option : ucfirst($enrollment->payment_type ?? '—') }}</span></div>
            <div class="stmt-info-row"><span class="stmt-info-label">Enrollment Status:</span> <span class="stmt-info-value">{{ ucfirst($enrollment->status ?? '—') }}</span></div>
            <div class="stmt-info-row"><span class="stmt-info-label">Payment Status:</span> <span class="stmt-info-value">{{ ucfirst($enrollment->payment_status ?? '—') }}</span></div>
        </div>
    </div>

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalFee, 2) }}</div>
            <div class="fin-stat-label">Total Fee</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalPaid, 2) }}</div>
            <div class="fin-stat-label">Total Paid</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($balance, 2) }}</div>
            <div class="fin-stat-label">Remaining Balance</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $totalFee > 0 ? round(min(100, $totalPaid / $totalFee * 100)) : 0 }}%</div>
            <div class="fin-stat-label">Paid</div>
        </div>
    </div>

    <div class="stmt-section-title">Payment History</div>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference No.</th>
                <th>For</th>
                <th>Method</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('M d, Y') }}</td>
                    <td>{{ $t->reference_number ?? '—' }}</td>
                    <td>{{ $t->installment_month ?? ucfirst($t->payment_type) }}</td>
                    <td>{{ strtoupper($t->payment_method) }}</td>
                    <td class="text-right">₱{{ number_format($t->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Paid</td>
                <td class="text-right">₱{{ number_format($totalPaid, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($enrollment->paymentInstallments->count() > 0)
        <div class="stmt-section-title">Installment Schedule</div>
        <table class="fin-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Due Date</th>
                    <th class="text-right">Amount Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollment->paymentInstallments as $inst)
                    <tr>
                        <td>{{ $inst->month_name }}</td>
                        <td>{{ $inst->due_date ? $inst->due_date->format('M d, Y') : '—' }}</td>
                        <td class="text-right">₱{{ number_format($inst->amount + $inst->late_fee, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $inst->status)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
