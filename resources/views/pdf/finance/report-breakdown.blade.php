<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    @php
        $cash  = $reportData['payments_by_method']['cash']  ?? 0;
        $gcash = $reportData['payments_by_method']['gcash'] ?? 0;
    @endphp
    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $reportData['total_payments'] ?? 0 }}</div>
            <div class="fin-stat-label">Total Payments</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $cash }}</div>
            <div class="fin-stat-label">Cash Payments</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $gcash }}</div>
            <div class="fin-stat-label">GCash Payments</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalAmount, 2) }}</div>
            <div class="fin-stat-label">Total Collected</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Period</th>
                <th class="text-center">No. of Payments</th>
                <th class="text-right">Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['daily_breakdown'] ?? [] as $period)
                <tr>
                    <td>{{ $period->period_label }}</td>
                    <td class="text-center">{{ $period->count }}</td>
                    <td class="text-right">₱{{ number_format($period->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No payment records found for this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $reportData['total_payments'] ?? 0 }}</td>
                <td class="text-right">₱{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
