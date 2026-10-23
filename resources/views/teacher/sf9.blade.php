<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF9 — Learner's Performance Report — {{ $student->name }}</title>
<style>
    /* Official SF9 (Learner's Performance Report) layout: one letter-size landscape sheet,
       the report on the left half and attendance / comments / signatures on the right half.
       dompdf's own page margin is zeroed; .page's padding is the visible margin. No width on
       .page — width:100% tables inside a full-width box overflow under dompdf's table layout. */
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; background: #fff; }
    .page { padding: 7mm 9mm; }

    table.halves { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.halves > tbody > tr > td { width: 50%; vertical-align: top; }
    td.left-half  { padding-right: 7mm; }
    td.right-half { padding-left: 7mm; border-left: 1px solid #000; }

    /* Header */
    table.hdr { width: 100%; border-collapse: collapse; }
    table.hdr td { vertical-align: middle; text-align: center; }
    table.hdr td.logo { width: 14%; }
    table.hdr td.logo img { width: 58px; height: 58px; }
    .hdr-text { font-size: 10.5pt; line-height: 1.25; white-space: nowrap; }

    .fill { display: inline-block; border-bottom: 1px solid #000; text-align: center; font-weight: bold; }
    .center { text-align: center; }
    .title { font-size: 12pt; font-weight: bold; text-align: center; margin-top: 7px; }
    .heading { font-size: 10pt; font-weight: bold; text-align: center; margin: 7px 0 4px; }

    table.info { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.info td { padding: 2px 3px 2px 0; white-space: nowrap; font-size: 9.5pt; }
    table.info td.val { border-bottom: 1px solid #000; font-weight: bold; text-align: center; white-space: normal; }

    .letter { margin-top: 7px; font-size: 9.5pt; line-height: 1.3; text-align: justify; }
    .letter .indent { text-indent: 26px; }

    /* Learning progress table */
    table.grid { width: 100%; border-collapse: collapse; }
    table.grid th, table.grid td { border: 1px solid #000; padding: 2px 4px; font-size: 9.5pt; }
    table.grid th { font-weight: bold; text-align: center; }
    table.grid td.num { text-align: center; width: 7%; }
    table.grid td.final { text-align: center; width: 13%; font-weight: bold; }
    table.grid td.rem { text-align: center; width: 15%; }
    table.grid tr.blank td { height: 16px; }
    td.ga-label { text-align: right; font-weight: bold; border-left: 1px solid #000; }

    table.desc { width: 92%; margin: 0 auto; border-collapse: collapse; }
    table.desc th, table.desc td { text-align: center; font-size: 9pt; padding: 0 2px; }
    table.desc th { font-weight: bold; }

    /* Right half */
    table.att { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.att th, table.att td { border: 1px solid #000; text-align: center; font-size: 8pt; padding: 2px 1px; }
    table.att th { font-weight: normal; }
    table.att th.month, table.att td.lbl { width: 15%; }
    table.att th.month { font-weight: bold; }
    table.att td.lbl { font-size: 7.5pt; line-height: 1.1; }
    table.att td { height: 24px; }

    table.comments { width: 100%; border-collapse: collapse; }
    table.comments td { border: 1px solid #000; height: 60px; vertical-align: top; padding: 3px 7px; }

    table.sig { width: 76%; margin: 0 auto; border-collapse: collapse; }
    table.sig td { padding: 3px 0; font-size: 9.5pt; }
    table.sig td.line { border-bottom: 1px solid #000; width: 82%; }

    .para { font-size: 9.5pt; line-height: 1.3; margin-top: 3px; }
    table.lines { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.lines td { padding: 2px 0; font-size: 9.5pt; white-space: nowrap; }
    table.lines td.line { border-bottom: 1px solid #000; width: 100%; font-weight: bold; text-align: center; white-space: normal; }

    table.approve { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.approve td { vertical-align: bottom; font-size: 9.5pt; text-align: center; }
    .sign-line { border-top: 1px solid #000; padding-top: 1px; }
    .adviser-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; }
</style>
</head>
<body>
@php
    // Left: DepEd seal when public/images/deped_logo.png is added, else the IEMELIF seal. Right: the school logo.
    $sealPath   = file_exists(public_path('images/deped_logo.png')) ? public_path('images/deped_logo.png') : public_path('images/logo1.png');
    $schoolLogo = public_path('images/logo.png');
    $img = fn ($p) => file_exists($p) ? 'data:image/png;base64,' . base64_encode(file_get_contents($p)) : null;

    $sfSchoolName    = \App\Models\Setting::get('school_name') ?: 'IEMELIF Learning Center';
    $sfSchoolAddress = \App\Models\Setting::get('school_address') ?: 'General Tinio, Nueva Ecija';

    $learnerName = $profile
        ? strtoupper(trim("{$profile->last_name}, {$profile->first_name} " . ($profile->middle_name ?? '') . ' ' . ($profile->suffix ?? '')))
        : strtoupper($student->name);
    $sex = $profile?->gender ? ucfirst($profile->gender) : '';

    // At least this many learning-area rows, so the table keeps the form's height like the printed SF9
    $minRows = 10;
    // Ratings print as whole numbers like on the form (90.00 → 90, 87.5 stays 87.5)
    $fmt = fn ($v) => $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
@endphp

<div class="page">
<table class="halves"><tr>

{{-- ════════════ LEFT HALF ════════════ --}}
<td class="left-half">

    <table class="hdr"><tr>
        <td class="logo">@if($img($sealPath))<img src="{{ $img($sealPath) }}" alt="">@endif</td>
        <td class="hdr-text">
            Republic of the Philippines<br>
            Department of Education<br>
            Region III<br>
            <b>SCHOOLS DIVISION OFFICE OF</b> <span class="fill" style="width:105px;">NUEVA ECIJA</span><br>
            District <span class="fill" style="min-width:60px;">&nbsp;</span><br>
            {{ $sfSchoolAddress }}
        </td>
        <td class="logo">@if($img($schoolLogo))<img src="{{ $img($schoolLogo) }}" alt="">@endif</td>
    </tr></table>

    <div class="center" style="margin-top:7px;">School: <span class="fill" style="width:62%;">{{ strtoupper($sfSchoolName) }}</span></div>

    <div class="title">LEARNER'S PERFORMANCE REPORT</div>
    <div class="center" style="font-size:10pt;">School Year {{ $schoolYear }}</div>

    <table class="info">
        <tr>
            <td style="width:9%;">Name:</td><td class="val" style="width:40%;">{{ $learnerName }}</td>
            <td style="width:3%;"></td>
            <td style="width:8%;">Age:</td><td class="val" style="width:12%;">{{ $age ?? '' }}</td>
            <td style="width:3%;"></td>
            <td style="width:10%;">Sex:</td><td class="val" style="width:15%;">{{ $sex }}</td>
        </tr>
        <tr>
            <td>LRN:</td><td class="val">{{ $student->lrn ?? '' }}</td>
            <td></td>
            <td>Grade:</td><td class="val">{{ $gradeLabel }}</td>
            <td></td>
            <td>Section:</td><td class="val">{{ $sectionName }}</td>
        </tr>
        <tr>
            <td colspan="2">Track (SHS only) <span class="fill" style="width:58%;">&nbsp;</span></td>
            <td colspan="6"></td>
        </tr>
    </table>

    <div class="letter">
        Dear Parents,
        <div class="indent">This Performance Report shows the ability and progress your child has made in the different learning areas as well as his/her core values.</div>
        <div class="indent">The school welcomes you should you desire to know more about your child's progress.</div>
    </div>

    <div class="heading">LEARNING PROGRESS AND ACHIEVEMENT</div>
    <table class="grid">
        <thead>
            <tr>
                <th rowspan="2" style="width:44%;">Learning Areas</th>
                <th colspan="3">TERM</th>
                <th rowspan="2">Final<br>Grade</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr><th>1</th><th>2</th><th>3</th></tr>
        </thead>
        <tbody>
            @foreach($subjects as $sub)
            <tr>
                <td>{{ $sub['name'] }}</td>
                @if($isDescriptive)
                    <td class="num">{{ $sub['term1_desc'] ?? '' }}</td>
                    <td class="num">{{ $sub['term2_desc'] ?? '' }}</td>
                    <td class="num">{{ $sub['term3_desc'] ?? '' }}</td>
                    <td class="final"></td>
                    <td class="rem"></td>
                @else
                    <td class="num">{{ $fmt($sub['term1']) }}</td>
                    <td class="num">{{ $fmt($sub['term2']) }}</td>
                    <td class="num">{{ $fmt($sub['term3']) }}</td>
                    <td class="final">{{ $sub['final'] ?? '' }}</td>
                    <td class="rem">{{ $sub['remarks'] ?? '' }}</td>
                @endif
            </tr>
            @endforeach
            @for($i = count($subjects); $i < $minRows; $i++)
            <tr class="blank"><td></td><td class="num"></td><td class="num"></td><td class="num"></td><td class="final"></td><td class="rem"></td></tr>
            @endfor
            <tr>
                <td colspan="4" class="ga-label">General Average</td>
                <td class="final">{{ $isDescriptive ? '' : ($gwa ?? '') }}</td>
                <td class="rem">{{ (!$isDescriptive && $gwa !== null) ? ($gwa >= 75 ? 'Passed' : 'Failed') : '' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="heading">PERFORMANCE DESCRIPTORS</div>
    @if($isDescriptive)
    <table class="desc">
        <tr><th>Rating</th><th>Description</th></tr>
        <tr><td>O</td><td>Outstanding</td></tr>
        <tr><td>VS</td><td>Very Satisfactory</td></tr>
        <tr><td>S</td><td>Satisfactory</td></tr>
        <tr><td>FS</td><td>Fairly Satisfactory</td></tr>
        <tr><td>DNME</td><td>Did Not Meet Expectations</td></tr>
    </table>
    @else
    <table class="desc">
        <tr><th>Grading Scale</th><th>Description</th><th>Remarks</th></tr>
        <tr><td>90-100</td><td>Advancing</td><td>Passed</td></tr>
        <tr><td>80-89</td><td>Benchmarking</td><td>Passed</td></tr>
        <tr><td>75-79</td><td>Connecting</td><td>Passed</td></tr>
        <tr><td>65-74</td><td>Developing</td><td>Failed</td></tr>
        <tr><td>0-64</td><td>Emerging</td><td>Failed</td></tr>
    </table>
    @endif
</td>

{{-- ════════════ RIGHT HALF ════════════ --}}
<td class="right-half">

    <div class="heading" style="margin-top:2px;">ATTENDANCE RECORD</div>
    <table class="att">
        <tr>
            <th class="month">Month</th>
            @foreach(array_keys($attendance) as $m)<th>{{ $m }}</th>@endforeach
            <th>Total</th>
        </tr>
        <tr>
            <td class="lbl">No. of Class Days</td>
            @foreach($attendance as $a)<td>{{ $a['days'] ?? '' }}</td>@endforeach
            <td>{{ $attendanceTotal['days'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">No. of Days Present</td>
            @foreach($attendance as $a)<td>{{ $a['present'] ?? '' }}</td>@endforeach
            <td>{{ $attendanceTotal['present'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">No. of Days Absent</td>
            @foreach($attendance as $a)<td>{{ $a['absent'] ?? '' }}</td>@endforeach
            <td>{{ $attendanceTotal['absent'] ?? '' }}</td>
        </tr>
    </table>

    <div class="heading" style="margin-top:12px;">TEACHER'S COMMENTS/REMARKS</div>
    <table class="comments">
        <tr><td><b>Term 1</b></td></tr>
        <tr><td><b>Term 2</b></td></tr>
        <tr><td><b>Term 3</b></td></tr>
    </table>

    <div class="heading" style="margin-top:12px;">PARENTS/GUARDIAN'S SIGNATURE</div>
    <table class="sig">
        <tr><td>Term 1</td><td class="line">&nbsp;</td></tr>
        <tr><td>Term 2</td><td class="line">&nbsp;</td></tr>
        <tr><td>Term 3</td><td class="line">&nbsp;</td></tr>
    </table>

    <div class="heading" style="margin-top:12px;">CERTIFICATE OF TRANSFER</div>
    <div class="para">This is to certify that the above-named learner has satisfactorily completed the requirements for the grade level indicated.</div>
    <table class="lines">
        <tr><td style="width:1%;">Admitted to Grade:&nbsp;</td><td class="line" style="width:45%;">{{ $gradeLabel }}</td><td style="width:40%;"></td></tr>
    </table>
    <table class="lines" style="margin-top:2px;">
        <tr><td style="width:1%;">Eligible for Admission to Grade:&nbsp;</td><td class="line" style="width:35%;">{{ in_array($actionTaken, ['Promoted']) ? $nextGradeLabel : '' }}</td><td style="width:35%;"></td></tr>
    </table>

    <div style="margin-top:8px;font-weight:bold;">Approved:</div>
    <table class="approve">
        <tr>
            <td style="width:42%;">
                <div style="height:16px;font-weight:bold;">{{ $schoolHead }}</div>
                <div class="sign-line">School Head</div>
            </td>
            <td style="width:16%;"></td>
            <td style="width:42%;">
                <div class="adviser-name">{{ $teacherName ?: ' ' }}</div>
                <div>Adviser</div>
            </td>
        </tr>
    </table>

    <div class="heading" style="margin-top:12px;">CANCELLATION OF ELIGIBILITY TO TRANSFER</div>
    <table class="lines">
        <tr>
            <td style="width:1%;">Admitted in:&nbsp;</td><td class="line" style="width:40%;">&nbsp;</td>
            <td style="width:14%;"></td>
            <td style="width:1%;">Date:&nbsp;</td><td class="line" style="width:30%;">&nbsp;</td>
        </tr>
    </table>
    <table class="approve" style="margin-top:14px;">
        <tr><td style="width:42%;"><div style="height:12px;"></div><div class="sign-line">School Head</div></td><td></td></tr>
    </table>
</td>

</tr></table>
</div>
</body>
</html>
