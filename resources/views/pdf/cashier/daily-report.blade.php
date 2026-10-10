<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    table.fin-table td.ref { font-family: 'Courier New', monospace; font-size: 8.5px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($total, 2) }}</div>
            <div class="fin-stat-label">Total Collected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($cash, 2) }}</div>
            <div class="fin-stat-label">Cash Payments</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($online, 2) }}</div>
            <div class="fin-stat-label">Online / E-Wallet</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $count }}</div>
            <div class="fin-stat-label">Transactions</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Time</th>
                <th>Student</th>
                <th>Grade</th>
                <th>Payment Type</th>
                <th>Method</th>
                <th class="text-right">Amount</th>
                <th>Reference No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r['time'] }}</td>
                    <td>{{ $r['student'] }}</td>
                    <td>{{ $r['grade'] }}</td>
                    <td>{{ $r['type'] }}</td>
                    <td>{{ $r['method'] }}</td>
                    <td class="text-right">₱{{ number_format($r['amount'], 2) }}</td>
                    <td class="ref">{{ $r['reference'] }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No transactions recorded for this date.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total</td>
                <td class="text-right">₱{{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
