<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $documentTitle }}</title>
@include('pdf.manual._styles')
</head>
<body>
    <div class="cover">
        @if($schoolLogoPath)
            <img src="{{ $schoolLogoPath }}">
        @endif
        <div class="school-name">{{ strtoupper($schoolName) }}</div>
        <div class="school-addr">General Tinio IEMELIF Church<br>Poblacion Central, General Tinio, Nueva Ecija.</div>
        <div class="manual-title">System User Manual</div>
        <div class="manual-sub">A complete guide to every portal — Public Enrollment, Student, Teacher,<br>Finance, Cashier, Admin/Registrar, and Super Admin</div>
        <div class="manual-meta">Version 1.0 &mdash; {{ $generatedAt->format('F Y') }}</div>
    </div>

    <div class="toc">
        <div class="toc-title">Table of Contents</div>
        @foreach($tocEntries as $entry)
            <div class="toc-entry"><span class="toc-num">{{ $entry['num'] }}</span>{{ $entry['title'] }}</div>
        @endforeach
    </div>

    @foreach($chaptersHtml as $html)
        <div class="chapter">{!! $html !!}</div>
    @endforeach
</body>
</html>
