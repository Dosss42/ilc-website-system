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
                <th class="text-right">Assessed</th>
                <th class="text-right">Collected</th>
                <th class="text-right">Outstanding</th>
                <th class="text-center">Paid</th>
                <th class="text-center">Collection Rate</th>
            </tr>
        </thead>
        <tbody>
            @php $gf = 0; $gc = 0; $go = 0; $gp = 0; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $row = $finByGrade[$key] ?? ['total_fee' => 0, 'collected' => 0, 'outstanding' => 0, 'paid' => 0];
                    $gf += $row['total_fee'];
                    $gc += $row['collected'];
                    $go += $row['outstanding'];
                    $gp += $row['paid'];
                    $rate = $row['total_fee'] > 0 ? round($row['collected'] / $row['total_fee'] * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">₱{{ number_format($row['total_fee'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($row['collected'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($row['outstanding'], 2) }}</td>
                    <td class="text-center">{{ $row['paid'] }}</td>
                    <td class="text-center">{{ $rate }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right">₱{{ number_format($gf, 2) }}</td>
                <td class="text-right">₱{{ number_format($gc, 2) }}</td>
                <td class="text-right">₱{{ number_format($go, 2) }}</td>
                <td class="text-center">{{ $gp }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
