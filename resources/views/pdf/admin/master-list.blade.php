<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    @php $genderTotal = $male + $female; @endphp
    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $enrollments->count() }}</div>
            <div class="fin-stat-label">Enrolled Students</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $male }}</div>
            <div class="fin-stat-label">Male</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $female }}</div>
            <div class="fin-stat-label">Female</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $genderTotal }}</div>
            <div class="fin-stat-label">Total Enrolled</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Grade Level</th>
                <th class="text-center">Gender</th>
                <th class="text-center">Type</th>
                <th class="text-center">Status</th>
                <th class="text-center">Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $i => $e)
                @php
                    $sd = $e->student_data ?? [];
                    $fullName = trim(($sd['first_name'] ?? '') . ' ' . ($sd['last_name'] ?? '')) ?: ($e->full_name ?? '—');
                    $gl = $sd['grade_level'] ?? ($e->grade_level ?? '');
                    $glLabel = $gradeLevels[$gl] ?? $gl;
                    $stype = ucfirst($sd['student_type'] ?? '—');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $fullName }}</td>
                    <td>{{ $glLabel }}</td>
                    <td class="text-center">{{ ucfirst(strtolower($sd['gender'] ?? '—')) }}</td>
                    <td class="text-center">{{ $stype }}</td>
                    <td class="text-center">{{ ucfirst($e->status ?? 'pending') }}</td>
                    <td class="text-center">
                        {{ ($e->payment_status ?? '') === 'paid' ? 'Paid' : (($e->payment_status ?? '') === 'partial' ? 'Partial' : 'Unpaid') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No enrolled students found for this school year.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Total Enrolled</td>
                <td class="text-center">{{ $genderTotal }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
