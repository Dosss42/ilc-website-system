<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    @php
        $statusRows = [
            ['label' => 'Enrolled',  'key' => 'enrolled'],
            ['label' => 'Approved',  'key' => 'approved'],
            ['label' => 'Completed', 'key' => 'completed'],
            ['label' => 'Pending',   'key' => 'pending'],
            ['label' => 'Declined',  'key' => 'declined'],
            ['label' => 'Dropped',   'key' => 'dropped'],
        ];
        $totalSafe = max(1, $enrollTotal);
    @endphp
    <table class="fin-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-center">Count</th>
                <th class="text-center">Share</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusRows as $sr)
                @php
                    $count = $enrollAll->where('status', $sr['key'])->count();
                    $pct   = round($count / $totalSafe * 100);
                @endphp
                <tr>
                    <td>{{ $sr['label'] }}</td>
                    <td class="text-center">{{ $count }}</td>
                    <td class="text-center">{{ $pct }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $enrollTotal }}</td>
                <td class="text-center">100%</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
