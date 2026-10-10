<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    <table class="fin-table">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th class="text-center">Total</th>
                <th class="text-center">Approved</th>
                <th class="text-center">Pending</th>
                <th class="text-center">Dropped</th>
                <th class="text-center">Completion</th>
            </tr>
        </thead>
        <tbody>
            @php $gt = 0; $ga = 0; $gp = 0; $gd = 0; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $row = $enrollByGrade[$key] ?? ['total' => 0, 'approved' => 0, 'pending' => 0, 'dropped' => 0];
                    $gt += $row['total'];
                    $ga += $row['approved'];
                    $gp += $row['pending'];
                    $gd += $row['dropped'] ?? 0;
                    $pct = $row['total'] > 0 ? round($row['approved'] / $row['total'] * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $row['total'] }}</td>
                    <td class="text-center">{{ $row['approved'] }}</td>
                    <td class="text-center">{{ $row['pending'] }}</td>
                    <td class="text-center">{{ $row['dropped'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['total'] > 0 ? $pct . '%' : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $gt }}</td>
                <td class="text-center">{{ $ga }}</td>
                <td class="text-center">{{ $gp }}</td>
                <td class="text-center">{{ $gd }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
