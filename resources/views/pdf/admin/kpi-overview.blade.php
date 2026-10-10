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
                <th>KPI Metric</th>
                <th class="text-center">Current Value</th>
                <th class="text-center">Target</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kpiCards as $kpi)
                <tr>
                    <td>{{ $kpi['label'] }}</td>
                    <td class="text-center">{{ $kpi['value'] }}</td>
                    <td class="text-center">{{ $kpi['target'] }}</td>
                    <td class="text-center">
                        {{ $kpi['met'] ? 'Target Met' : ($kpi['warn'] ? 'Near Target' : 'Below Target') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
