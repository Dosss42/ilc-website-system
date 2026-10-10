<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    .adm-note { font-size: 9.5px; color: #666; margin-top: 10px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $promoted }}</div>
            <div class="fin-stat-label">Promoted</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $retained }}</div>
            <div class="fin-stat-label">Retained</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $graduated }}</div>
            <div class="fin-stat-label">Graduated</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $pending }}</div>
            <div class="fin-stat-label">Pending Assessment</div>
        </div>
    </div>

    <div class="adm-note">
        Out of {{ $eligibleTotal }} eligible student(s) (Nursery&ndash;Grade 6, excluding first-year transferees).
    </div>

    @include('pdf.shared._bottom')
</body>
</html>
