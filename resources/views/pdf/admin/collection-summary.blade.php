<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalFees, 2) }}</div>
            <div class="fin-stat-label">Total Assessed</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalCollected, 2) }}</div>
            <div class="fin-stat-label">Total Collected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $collectionRate }}%</div>
            <div class="fin-stat-label">Collection Rate</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $paid + $partial + $unpaid }}</div>
            <div class="fin-stat-label">Total Students</div>
        </div>
    </div>

    @php
        $rows = [
            ['label' => 'Fully Paid', 'amount' => $finAll->where('payment_status', 'paid')->sum('payment_amount'), 'count' => $paid],
            ['label' => 'Partial',    'amount' => $finAll->where('payment_status', 'partial')->sum('payment_amount'), 'count' => $partial],
            ['label' => 'Unpaid',     'amount' => 0, 'count' => $unpaid],
        ];
        $totalCountSafe = max(1, $paid + $partial + $unpaid);
    @endphp
    <table class="fin-table">
        <thead>
            <tr>
                <th>Payment Status</th>
                <th class="text-center">Students</th>
                <th class="text-right">Amount Collected</th>
                <th class="text-center">Share</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                <tr>
                    <td>{{ $r['label'] }}</td>
                    <td class="text-center">{{ $r['count'] }}</td>
                    <td class="text-right">{{ $r['amount'] > 0 ? '₱' . number_format($r['amount'], 2) : '—' }}</td>
                    <td class="text-center">{{ round($r['count'] / $totalCountSafe * 100) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total Assessed</td>
                <td class="text-center">{{ $paid + $partial + $unpaid }}</td>
                <td class="text-right">₱{{ number_format($totalFees, 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Total Collected</td>
                <td></td>
                <td class="text-right">₱{{ number_format($totalCollected, 2) }}</td>
                <td class="text-center">{{ $collectionRate }}%</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
