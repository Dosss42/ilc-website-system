<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Certificate of Good Moral Character — {{ $name }}</title>
<style>
    /* Certificate of Good Moral Character, letter size. School-issued (DepEd prescribes no template):
       the usual DepEd letterhead like the School Forms, the certification, Guidance Counselor + School Head. */
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #000; background: #fff; }
    .page { padding: 16mm 22mm 14mm; }

    table.hdr { width: 100%; border-collapse: collapse; }
    table.hdr td { vertical-align: middle; text-align: center; }
    table.hdr td.logo { width: 18%; }
    table.hdr td.logo img { width: 78px; height: 78px; }
    table.hdr td.logo img.deped { width: 110px; height: auto; }
    .hdr-small { font-size: 10pt; line-height: 1.3; }
    .hdr-school { font-size: 15pt; font-weight: bold; color: #1a3a6c; margin-top: 4px; letter-spacing: .3px; }
    .hdr-addr { font-size: 9.5pt; }
    .rule { border-top: 2.5px solid #1a3a6c; border-bottom: 1px solid #f5a623; height: 3px; margin: 10px 0 0; }

    .title { text-align: center; font-size: 17pt; font-weight: bold; letter-spacing: 1.5px; margin-top: 34px; }
    .title-line { width: 62%; margin: 6px auto 0; border-top: 1px solid #000; }

    .salute { margin-top: 34px; font-weight: bold; }
    .body p { text-indent: 48px; text-align: justify; line-height: 1.85; margin-top: 18px; }
    .u { font-weight: bold; text-decoration: underline; }

    table.sig { width: 100%; border-collapse: collapse; margin-top: 56px; }
    table.sig td { width: 50%; vertical-align: top; font-size: 11pt; }
    table.sig td.r { padding-left: 40px; }
    .sig-label { font-size: 10.5pt; margin-bottom: 38px; }
    .sig-name { font-weight: bold; text-transform: uppercase; border-top: 1px solid #000; padding-top: 3px; width: 240px; text-align: center; }
    .sig-role { font-size: 10pt; text-align: center; width: 240px; }

    .seal-note { margin-top: 48px; font-size: 9pt; font-style: italic; color: #333; }
</style>
</head>
<body>
@php
    $img = fn ($p) => file_exists($p) ? 'data:image/png;base64,' . base64_encode(file_get_contents($p)) : null;
    // Same letterhead as the School Forms: DepEd seal (or wordmark / IEMELIF seal) left, ILC logo right
    $sealFile = collect(['deped_seal.png', 'deped_logo.png', 'logo1.png'])->first(fn ($f) => file_exists(public_path('images/' . $f)));
    $seal = $sealFile ? $img(public_path('images/' . $sealFile)) : null;
    $logo = $img(public_path('images/logo.png'));
@endphp
<div class="page">
    <table class="hdr"><tr>
        <td class="logo">@if($seal)<img src="{{ $seal }}" class="{{ $sealFile === 'deped_logo.png' ? 'deped' : '' }}">@endif</td>
        <td>
            <div class="hdr-small">Republic of the Philippines<br><strong>Department of Education</strong><br>Region III &ndash; Central Luzon<br>Schools Division of Nueva Ecija{{ $district ? ' · District of ' . $district : '' }}</div>
            <div class="hdr-school">{{ strtoupper($schoolName) }}</div>
            <div class="hdr-addr">{{ $schoolAddress }}{{ $schoolId ? ' · School ID: ' . $schoolId : '' }}</div>
        </td>
        <td class="logo">@if($logo)<img src="{{ $logo }}">@endif</td>
    </tr></table>
    <div class="rule"></div>

    <div class="title">CERTIFICATE OF GOOD MORAL CHARACTER</div>
    <div class="title-line"></div>

    <div class="salute">TO WHOM IT MAY CONCERN:</div>

    <div class="body">
        <p>
            This is to certify that <span class="u">{{ $name }}</span>@if($lrn), with Learner Reference Number (LRN) <strong>{{ $lrn }}</strong>,@endif
            @if($current)
                is a bona fide pupil of {{ $schoolName }}, currently enrolled in <strong>{{ $grade }}{{ $section ? ' – ' . $section : '' }}</strong> for School Year <strong>{{ $schoolYear }}</strong>.
            @else
                was a bona fide pupil of {{ $schoolName }}, last enrolled in <strong>{{ $grade }}{{ $section ? ' – ' . $section : '' }}</strong> during School Year <strong>{{ $schoolYear }}</strong>.
            @endif
        </p>
        <p>
            This further certifies that during {{ $his }} stay in this school, {{ $he }} has shown good moral character,
            has been respectful of school authorities, teachers and fellow pupils, and has not been subjected to any
            disciplinary action as far as the records of this office are concerned.
        </p>
        <p>
            This certification is issued upon the request of {{ $requester ? 'the parent/guardian, ' . $requester . ',' : 'the parent/guardian' }}
            @if($purpose)
                for the purpose of <strong>{{ $purpose }}</strong>.
            @else
                for whatever legal purpose it may serve.
            @endif
        </p>
        <p>
            Issued this <strong>{{ $issuedDay }}</strong> day of <strong>{{ $issuedMonthYear }}</strong> at {{ $schoolName }}, {{ $schoolAddress }}.
        </p>
    </div>

    <table class="sig"><tr>
        <td>
            <div class="sig-label">Certified by:</div>
            <div class="sig-name">{{ $counselor ?: ' ' }}</div>
            <div class="sig-role">Guidance Counselor</div>
        </td>
        <td class="r">
            <div class="sig-label">Noted by:</div>
            <div class="sig-name">{{ $schoolHead ?: ' ' }}</div>
            <div class="sig-role">School Head</div>
        </td>
    </tr></table>

    <div class="seal-note">Not valid without the school's dry seal.</div>
</div>
</body>
</html>
