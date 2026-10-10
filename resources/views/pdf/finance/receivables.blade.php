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
        $overdueCount = $enrollments->where('overdue_months', '>', 0)->count();
        $notedCount   = $enrollments->where('has_active_note', true)->count();
    @endphp
    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $enrollments->count() }}</div>
            <div class="fin-stat-label">Students With Balance</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $overdueCount }}</div>
            <div class="fin-stat-label">Overdue</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $notedCount }}</div>
            <div class="fin-stat-label">With Promissory Note</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($totalReceivable, 2) }}</div>
            <div class="fin-stat-label">Total Receivable</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Grade</th>
                <th class="text-right">Balance</th>
                <th class="text-center">Months Overdue</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $e)
                <tr>
                    <td>{{ $e->user->name ?? '—' }}</td>
                    <td>{{ ucfirst($e->grade_level ?? '—') }}</td>
                    <td class="text-right">₱{{ number_format($e->remaining_balance, 2) }}</td>
                    <td class="text-center">{{ $e->overdue_months }}</td>
                    <td>
                        @if($e->has_active_note)
                            Promissory Note Active
                        @elseif($e->overdue_months > 0)
                            Overdue
                        @else
                            Current
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No outstanding balances found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td class="text-right">₱{{ number_format($totalReceivable, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
