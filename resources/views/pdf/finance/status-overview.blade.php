<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    .so-note { font-size: 9.5px; color: #666; margin: 10px 0 16px; padding: 8px 12px; background: #fff8ec; border: 1px solid #f5a623; border-radius: 4px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $totalStudentAccounts }}</div>
            <div class="fin-stat-label">Total Student Accounts</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $enrolledCount }}</div>
            <div class="fin-stat-label">Enrolled This Year</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $cashCount }}</div>
            <div class="fin-stat-label">On Cash Plan</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $installmentCount }}</div>
            <div class="fin-stat-label">On Installment Plan</div>
        </div>
    </div>

    <div class="so-note">
        <i>This is the only report that accounts for every student account in the system, not just active
        enrollments — the Grade Level, Payment Plan, Accounts Receivable, and Aging reports are all scoped to
        enrolled students only, since a declined applicant isn't on a payment plan and owes the school nothing.</i>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-center">Students</th>
                <th class="text-right">Total Fee</th>
                <th class="text-right">Total Paid</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusRows as $row)
                <tr>
                    <td>{{ $row->label }}</td>
                    <td class="text-center">{{ $row->count }}</td>
                    <td class="text-right">{{ $row->total_fee === null ? '—' : '₱' . number_format($row->total_fee, 2) }}</td>
                    <td class="text-right">{{ $row->total_paid === null ? '—' : '₱' . number_format($row->total_paid, 2) }}</td>
                    <td class="text-right">{{ $row->total_balance === null ? '—' : '₱' . number_format($row->total_balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total Student Accounts</td>
                <td class="text-center">{{ $totalStudentAccounts }}</td>
                <td class="text-right">—</td>
                <td class="text-right">—</td>
                <td class="text-right">—</td>
            </tr>
        </tfoot>
    </table>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Payment Plan (Enrolled Students Only)</th>
                <th class="text-center">Students</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash (Plan A)</td>
                <td class="text-center">{{ $cashCount }}</td>
            </tr>
            <tr>
                <td>Installment (Plan B/C/D)</td>
                <td class="text-center">{{ $installmentCount }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>Total Enrolled</td>
                <td class="text-center">{{ $enrolledCount }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom', ['preparedByName' => $generatedBy])
</body>
</html>
