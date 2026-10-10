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
                <th class="text-center">Eligible</th>
                <th class="text-center">Promoted</th>
                <th class="text-center">Retained</th>
                <th class="text-center">Graduated</th>
                <th class="text-center">Pending</th>
            </tr>
        </thead>
        <tbody>
            @php $tot = ['eligible' => 0, 'promoted' => 0, 'retained' => 0, 'graduated' => 0, 'pending' => 0]; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $row = $promoByGrade[$key] ?? ['eligible' => 0, 'promoted' => 0, 'retained' => 0, 'graduated' => 0, 'pending' => 0];
                    foreach ($tot as $k => $v) { $tot[$k] += $row[$k] ?? 0; }
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $row['eligible'] }}</td>
                    <td class="text-center">{{ $row['promoted'] }}</td>
                    <td class="text-center">{{ $row['retained'] }}</td>
                    <td class="text-center">{{ $row['graduated'] }}</td>
                    <td class="text-center">{{ $row['pending'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $tot['eligible'] }}</td>
                <td class="text-center">{{ $tot['promoted'] }}</td>
                <td class="text-center">{{ $tot['retained'] }}</td>
                <td class="text-center">{{ $tot['graduated'] }}</td>
                <td class="text-center">{{ $tot['pending'] }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
