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
                <th>#</th>
                <th>Student</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th class="text-center">Result</th>
                <th>To Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->grade_level }}</td>
                    <td>{{ $s->section }}</td>
                    <td class="text-center">{{ $s->result }}</td>
                    <td>{{ $s->to_grade ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No eligible students found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
