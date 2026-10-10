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
        $sixMonthTotal = array_sum($totals);
        $allTimeTotal  = $cash + $gcash + $other;
    @endphp
    <div class="fin-stats">
        <div class="fin-stat-cell" style="width:50%;">
            <div class="fin-stat-value">₱{{ number_format($sixMonthTotal, 2) }}</div>
            <div class="fin-stat-label">Collected (Last 6 Months)</div>
        </div>
        <div class="fin-stat-cell" style="width:50%;">
            <div class="fin-stat-value">₱{{ number_format($allTimeTotal, 2) }}</div>
            <div class="fin-stat-label">Collected (All Time)</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            @foreach($months as $i => $month)
                <tr>
                    <td>{{ $month }}</td>
                    <td class="text-right">₱{{ number_format($totals[$i], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right">₱{{ number_format($sixMonthTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Payment Method (All Time)</th>
                <th class="text-right">Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash</td>
                <td class="text-right">₱{{ number_format($cash, 2) }}</td>
            </tr>
            <tr>
                <td>GCash</td>
                <td class="text-right">₱{{ number_format($gcash, 2) }}</td>
            </tr>
            <tr>
                <td>Other</td>
                <td class="text-right">₱{{ number_format($other, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right">₱{{ number_format($allTimeTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
