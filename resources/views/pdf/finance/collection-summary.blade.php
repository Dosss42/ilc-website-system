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
        <div class="fin-stat-cell" style="width:50%;">
            <div class="fin-stat-value">{{ $grandCount }}</div>
            <div class="fin-stat-label">Total Payments Collected</div>
        </div>
        <div class="fin-stat-cell" style="width:50%;">
            <div class="fin-stat-value">₱{{ number_format($grandTotal, 2) }}</div>
            <div class="fin-stat-label">Total Amount Collected</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>By Payment Type</th>
                <th class="text-center">No. of Payments</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byType as $row)
                <tr>
                    <td>{{ ucfirst($row->payment_type) }}</td>
                    <td class="text-center">{{ $row->count }}</td>
                    <td class="text-right">₱{{ number_format($row->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No payments found for this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $grandCount }}</td>
                <td class="text-right">₱{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="fin-table">
        <thead>
            <tr>
                <th>By Payment Method</th>
                <th class="text-center">No. of Payments</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byMethod as $row)
                <tr>
                    <td>{{ strtoupper($row->payment_method) }}</td>
                    <td class="text-center">{{ $row->count }}</td>
                    <td class="text-right">₱{{ number_format($row->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No payments found for this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $grandCount }}</td>
                <td class="text-right">₱{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
