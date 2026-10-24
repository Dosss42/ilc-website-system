<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF10-ES — {{ $lastName }}, {{ $firstName }}</title>
<style>
    /* Official SF10-ES (Learner Permanent Record for Elementary School, formerly Form 137), legal size.
       dompdf's own page margin is zeroed; .page's padding is the visible margin. No width on .page —
       width:100% tables inside a full-width box overflow the right edge under dompdf's table layout. */
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 7pt; color: #000; background: #fff; }
    .page { padding: 7mm 9mm; }
    .page-break { page-break-before: always; }

    /* Header */
    .form-code { font-size: 6.5pt; font-weight: bold; }
    table.hdr { width: 100%; border-collapse: collapse; }
    table.hdr td { vertical-align: middle; text-align: center; }
    table.hdr td.logo { width: 16%; }
    table.hdr td.logo img { width: 52px; height: 52px; }
    table.hdr td.logo img.deped { width: 84px; height: auto; } /* DepEd logo is wide, keep its proportions */
    .hdr-small { font-size: 8pt; line-height: 1.25; }
    .hdr-title { font-size: 12pt; font-weight: bold; margin-top: 4px; }
    .hdr-sub { font-size: 7.5pt; font-style: italic; }

    .bar { background: #d9d9d9; border: 1px solid #000; text-align: center; font-weight: bold; font-size: 7.5pt; padding: 1px 0; margin-top: 4px; }

    /* Label + underlined value rows */
    table.f { width: 100%; border-collapse: collapse; }
    table.f td { padding: 3px 2px 1px 0; white-space: nowrap; font-size: 7.5pt; vertical-align: bottom; }
    table.f td.v { border-bottom: 1px solid #000; font-weight: bold; white-space: normal; padding-left: 3px; }
    table.f td.gap { width: 8px; }
    .box { border: 1px solid #000; padding: 2px 4px; margin-top: 2px; }
    .chk { display: inline-block; width: 8px; height: 8px; border: 1px solid #000; text-align: center; font-size: 7px; line-height: 7px; margin-right: 3px; }

    /* Scholastic record blocks, two per row */
    table.blocks { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 3px; }
    table.blocks > tbody > tr > td { width: 50%; vertical-align: top; padding: 0; }
    table.blocks > tbody > tr > td.l { padding-right: 3px; }
    table.blocks > tbody > tr > td.r { padding-left: 3px; }
    .block { border: 1px solid #000; padding: 2px 3px 3px; }

    table.g { width: 100%; border-collapse: collapse; margin-top: 2px; table-layout: fixed; }
    table.g th, table.g td { border: 1px solid #000; font-size: 7pt; padding: 1px 2px; height: 15px; }
    table.g th { text-align: center; font-weight: bold; }
    table.g td.n { text-align: center; }
    table.g td.subj { font-weight: bold; font-size: 6.5pt; line-height: 1.1; }
    table.g td.ga { font-weight: bold; }
</style>
</head>
<body>
@php
    $img = fn ($p) => file_exists($p) ? 'data:image/png;base64,' . base64_encode(file_get_contents($p)) : null;
    // Left: the round DepEd seal (images/deped_seal.png), else the DepEd wordmark, else the IEMELIF seal. Right: the ILC logo.
    $sealFile = collect(['deped_seal.png', 'deped_logo.png', 'logo1.png'])->first(fn ($f) => file_exists(public_path('images/' . $f)));
    $sealIsDeped = $sealFile === 'deped_logo.png'; // the wide wordmark keeps its proportions
    $seal = $sealFile ? $img(public_path('images/' . $sealFile)) : null;
    $logo = $img(public_path('images/logo.png'));

    // Ratings print as whole numbers like on the form (90.00 → 90)
    $fmt = fn ($v) => $v === null || $v === '' ? '' : (is_numeric($v) ? rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.') : $v);

    // 8 Scholastic Record blocks (4 per page) like the printed form; more pages when there are more school years
    $blocks = $records;
    $total  = max(8, (int) ceil(count($blocks) / 4) * 4);
    for ($i = count($blocks); $i < $total; $i++) $blocks[] = null;
    $blockRows = array_chunk($blocks, 2);
    $subjectRows = 14; // learning-area lines per block (the form's list length)

    $division = 'Nueva Ecija';
    $region   = 'III';
    $district = \App\Models\Setting::get('school_district') ?: '';
@endphp

@php
/* One Scholastic Record block (empty when $r is null) */
$renderBlock = function ($r) use ($fmt, $schoolName, $schoolId, $division, $region, $district, $subjectRows) {
    // &nbsp; keeps an empty underline at its set width (dompdf shrinks empty cells)
    $v = fn ($x) => ($x ?? '') === '' ? '&nbsp;' : e($x);
    $isDesc = $r && in_array($r['gradeLevel'], ['nursery', 'kindergarten']);
    $html  = '<div class="block">';
    $html .= '<table class="f"><tr><td>School:</td><td class="v" style="width:62%;">' . ($r ? e(strtoupper($schoolName)) : '') . '</td><td class="gap"></td><td>School ID:</td><td class="v" style="width:18%;">' . ($r ? e($schoolId) : '') . '</td></tr></table>';
    $html .= '<table class="f"><tr><td>District:</td><td class="v" style="width:24%;">' . ($r ? e($district) : '') . '</td><td class="gap"></td><td>Division:</td><td class="v" style="width:26%;">' . ($r ? e($division) : '') . '</td><td class="gap"></td><td>Region:</td><td class="v" style="width:14%;">' . ($r ? e($region) : '') . '</td></tr></table>';
    $html .= '<table class="f"><tr><td>Classified as Grade:</td><td class="v" style="width:17%;">' . $v($r['grade'] ?? null) . '</td><td class="gap"></td><td>Section:</td><td class="v" style="width:20%;">' . $v($r['section'] ?? null) . '</td><td class="gap"></td><td>School Year:</td><td class="v" style="width:20%;white-space:nowrap;">' . $v($r['schoolYear'] ?? null) . '</td></tr></table>';
    $html .= '<table class="f"><tr><td>Name of Adviser/Teacher:</td><td class="v" style="width:44%;">' . $v($r['adviser'] ?? null) . '</td><td class="gap"></td><td>Signature:</td><td class="v" style="width:22%;"></td></tr></table>';

    $html .= '<table class="g"><thead>'
        . '<tr><th rowspan="2" style="width:40%;">LEARNING AREAS</th><th colspan="3" style="width:27%;">Term Rating</th><th rowspan="2" style="width:12%;">Final<br>Rating</th><th rowspan="2" style="width:21%;">Remarks</th></tr>'
        . '<tr><th>1</th><th>2</th><th>3</th></tr></thead><tbody>';
    $rows = $r['rows'] ?? [];
    for ($i = 0; $i < max($subjectRows, count($rows)); $i++) {
        $row = $rows[$i] ?? null;
        $html .= '<tr><td class="subj">' . $v($row['name'] ?? null) . '</td>';
        foreach ([0, 1, 2] as $t) $html .= '<td class="n">' . e($fmt($row['terms'][$t] ?? null)) . '</td>';
        $html .= '<td class="n">' . e($fmt($row['final'] ?? null)) . '</td><td class="n">' . $v($row['remarks'] ?? null) . '</td></tr>';
    }
    $html .= '<tr><td class="ga" colspan="4">General Average</td><td class="n" style="font-weight:bold;">' . ($isDesc ? '' : e($fmt($r['ga'] ?? null))) . '</td><td class="n">' . ($isDesc ? '' : $v($r['gaRemarks'] ?? null)) . '</td></tr>';
    $html .= '</tbody></table>';

    $html .= '<table class="g" style="margin-top:0;border-top:0;">'
        . '<tr><th style="width:26%;border-top:0;">Remedial Classes</th><th colspan="4" style="text-align:left;border-top:0;font-weight:normal;">Conducted from: ______________ to ______________</th></tr>'
        . '<tr><th style="width:26%;">Learning Areas</th><th style="width:17%;">Final Rating</th><th style="width:19%;">Remedial Class Mark</th><th style="width:19%;">Recomputed Final Grade</th><th style="width:19%;">Remarks</th></tr>'
        . '<tr><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td></tr></table>';
    return $html . '</div>';
};
@endphp

<div class="page">
    <div class="form-code">SF10-ES</div>
    <table class="hdr"><tr>
        <td class="logo">@if($seal)<img src="{{ $seal }}" alt="" class="{{ $sealIsDeped ? 'deped' : '' }}">@endif</td>
        <td>
            <div class="hdr-small">Republic of the Philippines<br>Department of Education</div>
            <div class="hdr-title">Learner Permanent Record for Elementary School (SF10-ES)</div>
            <div class="hdr-sub">(Formerly Form 137)</div>
        </td>
        <td class="logo">@if($logo)<img src="{{ $logo }}" alt="">@endif</td>
    </tr></table>

    {{-- ── LEARNER'S PERSONAL INFORMATION ── --}}
    <div class="bar">LEARNER'S PERSONAL INFORMATION</div>
    <table class="f">
        <tr>
            <td>LAST NAME:</td><td class="v" style="width:21%;">{{ strtoupper($lastName) }}</td><td class="gap"></td>
            <td>FIRST NAME:</td><td class="v" style="width:21%;">{{ strtoupper($firstName) }}</td><td class="gap"></td>
            <td>NAME EXTN. (Jr,I,II):</td><td class="v" style="width:6%;">{{ strtoupper($suffix) }}</td><td class="gap"></td>
            <td>MIDDLE NAME:</td><td class="v" style="width:17%;">{{ strtoupper($middleName) }}</td>
        </tr>
    </table>
    <table class="f">
        <tr>
            <td>Learner Reference Number (LRN):</td><td class="v" style="width:22%;">{{ $lrn }}</td><td class="gap"></td>
            <td>Birthdate (mm/dd/yyyy):</td><td class="v" style="width:18%;">{{ $birthdate !== '—' ? $birthdate : '' }}</td><td class="gap"></td>
            <td>Sex:</td><td class="v" style="width:14%;">{{ ucfirst($gender) }}</td>
        </tr>
    </table>

    {{-- ── ELIGIBILITY FOR ELEMENTARY SCHOOL ENROLMENT ── --}}
    <div class="bar">ELIGIBILITY FOR ELEMENTARY SCHOOL ENROLMENT</div>
    <div class="box" style="font-style:italic;">
        <table class="f"><tr>
            <td>Credential Presented for Grade 1:</td><td style="width:6%;"></td>
            <td><span class="chk">{{ $eligibility['kinderProgressReport'] ? 'X' : '' }}</span>Kinder Progress Report</td><td style="width:6%;"></td>
            <td><span class="chk"></span>ECCD Checklist</td><td style="width:6%;"></td>
            <td><span class="chk"></span>Kindergarten Certificate of Completion</td><td style="width:20%;"></td>
        </tr></table>
        <table class="f"><tr>
            <td>Name of School:</td><td class="v" style="width:30%;font-style:normal;">{{ $eligibility['schoolName'] }}</td><td class="gap"></td>
            <td>School ID:</td><td class="v" style="width:12%;font-style:normal;">{{ $eligibility['kinderProgressReport'] ? $schoolId : '' }}</td><td class="gap"></td>
            <td>Address of School:</td><td class="v" style="width:28%;font-style:normal;">{{ $eligibility['schoolAddress'] }}</td>
        </tr></table>
    </div>
    <div style="margin-top:2px;">Other Credential Presented</div>
    <table class="f"><tr>
        <td style="padding-left:12px;"><span class="chk"></span>PEPT Passer</td><td style="width:2%;"></td>
        <td>Rating:</td><td class="v" style="width:9%;"></td><td class="gap"></td>
        <td>Date of Examination/Assessment (mm/dd/yyyy):</td><td class="v" style="width:13%;"></td><td style="width:3%;"></td>
        <td><span class="chk"></span>Others (Pls. Specify):</td><td class="v" style="width:15%;"></td>
    </tr></table>
    <table class="f"><tr>
        <td style="padding-left:12px;">Name and Address of Testing Center:</td><td class="v" style="width:45%;"></td><td class="gap"></td>
        <td>Remark:</td><td class="v" style="width:18%;"></td>
    </tr></table>

    {{-- ── SCHOLASTIC RECORD ── --}}
    <div class="bar">SCHOLASTIC RECORD</div>
    @foreach($blockRows as $i => $pair)
        @if($i > 0 && $i % 2 === 0)
            </div><div class="page page-break">
        @endif
        <table class="blocks"><tr>
            <td class="l">{!! $renderBlock($pair[0]) !!}</td>
            <td class="r">{!! $renderBlock($pair[1] ?? null) !!}</td>
        </tr></table>
    @endforeach

    {{-- ── CERTIFICATION ── --}}
    <div class="bar" style="margin-top:6px;">CERTIFICATION</div>
    <div class="box" style="padding:5px 6px;">
        <table class="f"><tr>
            <td>I CERTIFY that this is a true record of</td><td class="v" style="width:30%;">{{ strtoupper(trim($firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix)) }}</td>
            <td>&nbsp;with LRN</td><td class="v" style="width:15%;">{{ $lrn }}</td>
            <td>&nbsp;and that he/she is eligible for admission to Grade</td><td class="v" style="width:7%;">{{ $eligibleForGrade }}</td><td>.</td>
        </tr></table>
        <table class="f" style="margin-top:2px;"><tr>
            <td>Name of School:</td><td class="v" style="width:28%;">{{ strtoupper($schoolName) }}</td><td class="gap"></td>
            <td>School ID:</td><td class="v" style="width:10%;">{{ $schoolId }}</td><td class="gap"></td>
            <td>Division:</td><td class="v" style="width:13%;">{{ $division }}</td><td class="gap"></td>
            <td>Last School Year Attended:</td><td class="v" style="width:13%;white-space:nowrap;">{{ $lastSchoolYear }}</td>
        </tr></table>
        <table style="width:100%;border-collapse:collapse;margin-top:16px;"><tr>
            <td style="width:24%;text-align:center;"><div style="border-top:1px solid #000;padding-top:1px;">Date</div></td>
            <td style="width:6%;"></td>
            <td style="width:40%;text-align:center;">
                <div style="font-weight:bold;">{{ strtoupper($principalName ?? '') }}</div>
                <div style="border-top:1px solid #000;padding-top:1px;">Signature of Principal/School Head over Printed Name</div>
            </td>
            <td style="width:6%;"></td>
            <td style="width:24%;text-align:center;font-style:italic;color:#555;">(Affix School Seal here)</td>
        </tr></table>
    </div>
</div>
</body>
</html>
