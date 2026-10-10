<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    .aging-bucket-title { font-size: 11px; font-weight: bold; color: #fff; padding: 6px 10px; margin: 14px 0 0; border-radius: 4px 4px 0 0; }
    .aging-bucket-title.bcurrent { background: #2e7d32; }
    .aging-bucket-title.b1_30 { background: #f5a623; }
    .aging-bucket-title.b31_60 { background: #e67e22; }
    .aging-bucket-title.b61_90 { background: #dc3545; }
    .aging-bucket-title.b90_plus { background: #7b1fa2; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $grandCount }}</div>
            <div class="fin-stat-label">Students With Balance</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($grandTotal, 2) }}</div>
            <div class="fin-stat-label">Total Outstanding</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $bucketTotals['61_90']['count'] + $bucketTotals['90_plus']['count'] }}</div>
            <div class="fin-stat-label">61+ Days Overdue</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($bucketTotals['61_90']['amount'] + $bucketTotals['90_plus']['amount'], 2) }}</div>
            <div class="fin-stat-label">61+ Days Amount</div>
        </div>
    </div>

    {{-- Bucket summary table --}}
    <table class="fin-table">
        <thead>
            <tr>
                <th>Aging Bucket</th>
                <th class="text-center">Students</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bucketLabels as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $bucketTotals[$key]['count'] }}</td>
                    <td class="text-right">₱{{ number_format($bucketTotals[$key]['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $grandCount }}</td>
                <td class="text-right">₱{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Per-bucket detail — worst (most overdue) bucket first --}}
    @foreach(array_reverse($bucketLabels, true) as $key => $label)
        @if(count($buckets[$key]) > 0)
            <div class="aging-bucket-title b{{ $key }}">{{ $label }} ({{ count($buckets[$key]) }} student{{ count($buckets[$key]) === 1 ? '' : 's' }})</div>
            <table class="fin-table" style="margin-top:0;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Grade</th>
                        <th class="text-center">Days Overdue</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buckets[$key] as $e)
                        <tr>
                            <td>{{ $e->user->name ?? '—' }}</td>
                            <td>{{ ucfirst($e->grade_level ?? '—') }}</td>
                            <td class="text-center">{{ $e->days_overdue }}</td>
                            <td class="text-right">₱{{ number_format($e->remaining_balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
