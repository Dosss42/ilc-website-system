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
                <th class="text-center">New</th>
                <th class="text-center">Returning</th>
                <th class="text-center">Transferee</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $gn = 0; $gr = 0; $gtr = 0; $gt = 0; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $row = $enrollByGrade[$key] ?? ['new' => 0, 'returning' => 0, 'transferee' => 0, 'total' => 0];
                    $gn  += $row['new'] ?? 0;
                    $gr  += $row['returning'] ?? 0;
                    $gtr += $row['transferee'] ?? 0;
                    $gt  += $row['total'] ?? 0;
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
                <td class="text-center">{{ $gn }}</td>
                <td class="text-center">{{ $gr }}</td>
                <td class="text-center">{{ $gtr }}</td>
                <td class="text-center">{{ $gt }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
