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
                <th>Date</th>
                <th class="text-center">Applications</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyDays as $day)
                <tr>
                    <td>{{ $day['label'] }}</td>
                    <td class="text-center">{{ $day['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>7-Day Total</td>
                <td class="text-center">{{ collect($dailyDays)->sum('count') }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
