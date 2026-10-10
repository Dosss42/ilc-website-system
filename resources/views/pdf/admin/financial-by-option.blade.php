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
        $optionLabels = [
            'A' => 'Option A — Full Payment',
            'B' => 'Option B — 2 Installments',
            'C' => 'Option C — 3 Installments',
            'D' => 'Option D — Monthly',
        ];
    @endphp
    <table class="fin-table">
        <thead>
            <tr>
                <th>Payment Option</th>
                <th class="text-center">Students</th>
                <th class="text-right">Assessed</th>
                <th class="text-right">Collected</th>
                <th class="text-right">Outstanding</th>
                <th class="text-center">Fully Paid</th>
            </tr>
        </thead>
        <tbody>
            @forelse($finByOption as $opt => $row)
                <tr>
                    <td>{{ $optionLabels[$opt] ?? 'Option ' . $opt }}</td>
                    <td class="text-center">{{ $row['count'] }}</td>
                    <td class="text-right">₱{{ number_format($row['total_fee'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($row['collected'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($row['outstanding'], 2) }}</td>
                    <td class="text-center">{{ $row['paid'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No payment option data available.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
