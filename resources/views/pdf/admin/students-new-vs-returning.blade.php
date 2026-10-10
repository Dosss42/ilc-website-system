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
            <div class="fin-stat-value">{{ $newCount }}</div>
            <div class="fin-stat-label">New Students</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $returningCount }}</div>
            <div class="fin-stat-label">Returning</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $transfereeCount }}</div>
            <div class="fin-stat-label">Transferee</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $newCount + $returningCount + $transfereeCount }}</div>
            <div class="fin-stat-label">Total</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th class="text-center">New</th>
                <th class="text-center">Returning</th>
                <th class="text-center">Transferee</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $gNew = 0; $gRet = 0; $gTrans = 0; $gTot = 0; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $row = $enrollByGrade[$key] ?? ['new' => 0, 'returning' => 0, 'transferee' => 0, 'total' => 0];
                    $gNew   += $row['new'] ?? 0;
                    $gRet   += $row['returning'] ?? 0;
                    $gTrans += $row['transferee'] ?? 0;
                    $gTot   += $row['total'] ?? 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $row['new'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['returning'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['transferee'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['total'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $gNew }}</td>
                <td class="text-center">{{ $gRet }}</td>
                <td class="text-center">{{ $gTrans }}</td>
                <td class="text-center">{{ $gTot }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
