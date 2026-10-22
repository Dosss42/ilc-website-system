<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    table.sec-info { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.sec-info td { padding: 4px 8px; font-size: 10px; border: 1px solid #ddd; }
    table.sec-info td.lbl { width: 16%; background: #f0f4ff; color: #555; font-weight: bold; text-transform: uppercase; font-size: 8.5px; }
    .sub { font-size: 8.5px; color: #666; margin-top: 2px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    @php
        $sec = $summary['section'];
        $t   = $summary['totals'];
        $req = $summary['required_docs'];
        $peso = fn ($v) => '₱' . number_format((float) $v, 2);
        $typeLabels = ['new' => 'New', 'returning' => 'Returning', 'transferee' => 'Transferee'];
        $payLabels  = ['paid' => 'Fully Paid', 'partial' => 'Partially Paid', 'unpaid' => 'No Payment'];
    @endphp

    <table class="sec-info">
        <tr>
            <td class="lbl">Section</td><td>{{ $sec['name'] }}</td>
            <td class="lbl">Grade Level</td><td>{{ $sec['grade_label'] }}</td>
            <td class="lbl">School Year</td><td>{{ $sec['school_year'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Adviser</td><td>{{ $sec['adviser'] ?: 'Unassigned' }}</td>
            <td class="lbl">Room</td><td>{{ $sec['room'] ?: '—' }}</td>
            <td class="lbl">Capacity</td><td>{{ $t['students'] }} / {{ $sec['max_students'] }}</td>
        </tr>
    </table>

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $t['students'] }}</div>
            <div class="fin-stat-label">Total Students</div>
            <div class="sub">{{ $t['male'] }} boys · {{ $t['female'] }} girls{{ $t['gender_unknown'] ? ' · ' . $t['gender_unknown'] . ' not set' : '' }}</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $t['new'] }} / {{ $t['returning'] }} / {{ $t['transferee'] }}</div>
            <div class="fin-stat-label">New / Returning / Transferee</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $t['paid'] }} / {{ $t['partial'] }} / {{ $t['unpaid'] }}</div>
            <div class="fin-stat-label">Paid / Partial / No Payment</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $t['docs_complete'] }} / {{ $t['students'] }}</div>
            <div class="fin-stat-label">Documents Complete</div>
        </div>
    </div>

    <div class="fin-stats">
        <div class="fin-stat-cell" style="width:33%;">
            <div class="fin-stat-value">{{ $peso($t['total_fees']) }}</div>
            <div class="fin-stat-label">Total Tuition</div>
        </div>
        <div class="fin-stat-cell" style="width:33%;">
            <div class="fin-stat-value">{{ $peso($t['total_collected']) }}</div>
            <div class="fin-stat-label">Collected</div>
        </div>
        <div class="fin-stat-cell" style="width:34%;">
            <div class="fin-stat-value">{{ $peso($t['total_balance']) }}</div>
            <div class="fin-stat-label">Remaining Balance</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>LRN</th>
                <th>Student Name</th>
                <th class="text-center">Sex</th>
                <th>Type</th>
                <th>Payment</th>
                <th class="text-right">Balance</th>
                <th class="text-center">Documents</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary['students'] as $i => $s)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $s['lrn'] ?: '—' }}</td>
                    <td>{{ $s['name'] }}</td>
                    <td class="text-center">{{ $s['gender'] ? strtoupper($s['gender'][0]) : '—' }}</td>
                    <td>{{ $typeLabels[$s['student_type']] ?? '—' }}</td>
                    <td>{{ $payLabels[$s['payment']] }}{{ $s['payment_plan'] ? ' (Plan ' . $s['payment_plan'] . ')' : '' }}</td>
                    <td class="text-right">{{ $s['balance'] === null ? 'No fee set' : $peso($s['balance']) }}</td>
                    <td class="text-center">{{ $s['docs_approved'] }}/{{ $req }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No students in this section yet.</td></tr>
            @endforelse
        </tbody>
        @if(count($summary['students']))
            <tfoot>
                <tr>
                    <td colspan="6">Total: {{ $t['students'] }} student(s) — {{ $t['male'] }} boys, {{ $t['female'] }} girls</td>
                    <td class="text-right">{{ $peso($t['total_balance']) }}</td>
                    <td class="text-center">{{ $t['docs_complete'] }} complete</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
