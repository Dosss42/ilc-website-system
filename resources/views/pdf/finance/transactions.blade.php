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

    @php
        $completed = $transactions->whereIn('status', ['completed', 'approved'])->count();
        $pending   = $transactions->where('status', 'pending')->count();
        $rejected  = $transactions->where('status', 'rejected')->count();
    @endphp
    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $transactions->count() }}</div>
            <div class="fin-stat-label">Total Transactions</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $completed }}</div>
            <div class="fin-stat-label">Completed</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $pending }}</div>
            <div class="fin-stat-label">Pending</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalAmount, 2) }}</div>
            <div class="fin-stat-label">Total Amount</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference No.</th>
                <th>Student</th>
                <th>Type</th>
                <th>Method</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('M d, Y') }}</td>
                    <td class="ref">{{ $t->reference_number ?? '—' }}</td>
                    <td>{{ $t->user->name ?? '—' }}</td>
                    <td>{{ ucfirst($t->payment_type) }}</td>
                    <td>{{ strtoupper($t->payment_method) }}</td>
                    <td class="text-right">₱{{ number_format($t->amount, 2) }}</td>
                    <td>{{ ucfirst($t->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No transactions found for this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total (excl. rejected)</td>
                <td class="text-right">₱{{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
