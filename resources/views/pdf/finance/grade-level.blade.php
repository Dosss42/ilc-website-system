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
            <div class="fin-stat-value">{{ $grand->student_count }}</div>
            <div class="fin-stat-label">Total Students</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($grand->total_fee, 2) }}</div>
            <div class="fin-stat-label">Total Expected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($grand->total_paid, 2) }}</div>
            <div class="fin-stat-label">Total Collected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">₱{{ number_format($grand->total_balance, 2) }}</div>
            <div class="fin-stat-label">Total Outstanding</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th class="text-center">Students</th>
                <th class="text-right">Expected</th>
                <th class="text-right">Collected</th>
                <th class="text-right">Outstanding</th>
                <th class="text-center">% Collected</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r->grade_label }}</td>
                    <td class="text-center">{{ $r->student_count }}</td>
                    <td class="text-right">₱{{ number_format($r->total_fee, 2) }}</td>
                    <td class="text-right">₱{{ number_format($r->total_paid, 2) }}</td>
                    <td class="text-right">₱{{ number_format($r->total_balance, 2) }}</td>
                    <td class="text-center">{{ $r->total_fee > 0 ? round(max(0, min(100, ($r->total_fee - $r->total_balance) / $r->total_fee * 100))) : 0 }}%</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No enrollment records found for this school year.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $grand->student_count }}</td>
                <td class="text-right">₱{{ number_format($grand->total_fee, 2) }}</td>
                <td class="text-right">₱{{ number_format($grand->total_paid, 2) }}</td>
                <td class="text-right">₱{{ number_format($grand->total_balance, 2) }}</td>
                <td class="text-center">{{ $grand->total_fee > 0 ? round(max(0, min(100, ($grand->total_fee - $grand->total_balance) / $grand->total_fee * 100))) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
