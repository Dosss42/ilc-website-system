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
                <th class="text-center">Enrolled</th>
                <th class="text-center">Sections</th>
                <th class="text-center">Capacity</th>
                <th class="text-center">Utilization</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $grandEnrolled = 0; $grandCap = 0; $grandSections = 0; @endphp
            @foreach($gradeLevels as $key => $label)
                @php
                    $cnt    = $studentsByGrade[$key] ?? 0;
                    $grandEnrolled += $cnt;
                    $secCnt = $allSections->where('grade_level', $key)->count();
                    $grandSections += $secCnt;
                    $cap    = $allSections->where('grade_level', $key)->sum('max_students') ?: ($secCnt * 40);
                    $grandCap += $cap;
                    $util   = $cap > 0 ? min(100, round($cnt / $cap * 100)) : 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $cnt }}</td>
                    <td class="text-center">{{ $secCnt ?: '—' }}</td>
                    <td class="text-center">{{ $cap ?: '—' }}</td>
                    <td class="text-center">{{ $cap > 0 ? $util . '%' : '—' }}</td>
                    <td class="text-center">{{ $cnt > 0 ? 'Active' : 'No data' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center">{{ $grandEnrolled }}</td>
                <td class="text-center">{{ $grandSections }}</td>
                <td class="text-center">{{ $grandCap ?: '—' }}</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
