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
            <div class="fin-stat-value">{{ $outstandingList->count() }}</div>
            <div class="fin-stat-label">Accounts With Balance</div>
        </div>
        <div class="fin-stat-cell" style="width:50%;">
            <div class="fin-stat-value">₱{{ number_format($outstandingTotal, 2) }}</div>
            <div class="fin-stat-label">Total Outstanding</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Grade Level</th>
                <th>Option</th>
                <th class="text-right">Total Fee</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outstandingList as $i => $e)
                @php
                    $sd = $e->student_data ?? [];
                    $name = trim(($sd['first_name'] ?? '') . ' ' . ($sd['last_name'] ?? '')) ?: '—';
                    $gl   = $gradeLevels[$sd['grade_level'] ?? $e->grade_level ?? ''] ?? '—';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $name }}</td>
                    <td>{{ $gl }}</td>
                    <td class="text-center">{{ $e->payment_option ? 'Option ' . $e->payment_option : '—' }}</td>
                    <td class="text-right">₱{{ number_format($e->total_fee ?? 0, 2) }}</td>
                    <td class="text-right">₱{{ number_format($e->payment_amount ?? 0, 2) }}</td>
                    <td class="text-right">₱{{ number_format($e->remaining_balance ?? 0, 2) }}</td>
                    <td class="text-center">Outstanding</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No outstanding balances.</td></tr>
            @endforelse
        </tbody>
        @if($outstandingList->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6">Total Outstanding (all accounts)</td>
                    <td class="text-right">₱{{ number_format($outstandingTotal, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
