<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF5 — {{ $section->name }} — {{ $schoolYear }}</title>
<style>
    /* Official SF5 (Report on Promotion and Level of Progress & Achievement, DepEd Order 8 s. 2015), legal landscape.
       dompdf's own page margin is zeroed; .page's padding is the visible margin. No width on .page —
       width:100% tables inside a full-width box overflow the right edge under dompdf's table layout. */
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 7pt; color: #000; background: #fff; }
    .page { padding: 7mm 9mm 5mm; }
    .page-break { page-break-before: always; }

    /* Header */
    table.hdr { width: 100%; border-collapse: collapse; }
    table.hdr td { vertical-align: middle; }
    table.hdr td.logo { width: 11%; text-align: center; }
    table.hdr td.logo img { width: 56px; height: 56px; }
    table.hdr td.logo img.deped { width: 92px; height: auto; }
    .title { font-size: 13pt; font-weight: bold; text-align: center; }
    .subtitle { font-size: 7pt; font-style: italic; text-align: center; margin-bottom: 4px; }
    table.fields { width: 100%; border-collapse: separate; border-spacing: 4px 3px; }
    table.fields td { font-size: 7.5pt; white-space: nowrap; }
    table.fields td.lbl { text-align: right; padding-right: 2px; }
    table.fields td.box { border: 1px solid #000; height: 15px; font-weight: bold; padding: 1px 4px; }

    /* Layout: learners on the left, summary / signatures on the right */
    table.layout { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 4px; }
    table.layout > tbody > tr > td { vertical-align: top; }
    td.left-col { width: 76%; }
    td.right-col { width: 24%; padding-left: 10px; }

    table.lr { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.lr th, table.lr td { border: 1px solid #000; font-size: 7pt; padding: 1px 3px; }
    table.lr th { font-weight: bold; text-align: center; vertical-align: middle; font-size: 6.5pt; }
    table.lr th .note { font-weight: normal; }
    table.lr td { height: 18px; }
    table.lr td.c { text-align: center; }
    table.lr td.shade { background: #d9d9d9; }

    table.sum { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.sum th, table.sum td { border: 1px solid #000; font-size: 6.5pt; text-align: center; padding: 2px; }
    table.sum th.cap { font-size: 7pt; padding: 3px; }
    table.sum td.lbl { font-weight: bold; font-size: 6pt; line-height: 1.15; height: 26px; }

    .sig { margin-top: 18px; font-size: 7pt; }
    .sig .line { border-bottom: 1px solid #000; height: 16px; text-align: center; font-weight: bold; margin-top: 6px; }
    .sig .role { text-align: center; margin-top: 2px; }
    .guide { font-size: 6pt; line-height: 1.25; margin-top: 10px; }
    .guide p { margin-top: 4px; text-align: justify; }
    .pageno { text-align: right; font-size: 7pt; margin-top: 4px; }
</style>
</head>
<body>
@php
    $img = fn ($p) => file_exists($p) ? 'data:image/png;base64,' . base64_encode(file_get_contents($p)) : null;
    // Left: the round DepEd seal (images/deped_seal.png), else the DepEd wordmark, else the IEMELIF seal. Right: the ILC logo.
    $sealFile = collect(['deped_seal.png', 'deped_logo.png', 'logo1.png'])->first(fn ($f) => file_exists(public_path('images/' . $f)));
    $sealIsDeped = $sealFile === 'deped_logo.png'; // the wide wordmark keeps its proportions
    $seal = $sealFile ? $img(public_path('images/' . $sealFile)) : null;
    $district = \App\Models\Setting::get('school_district') ?: '';
    $logo = $img(public_path('images/logo.png'));
    $sfSchoolName = \App\Models\Setting::get('school_name') ?: 'IEMELIF Learning Center';

    // Learner rows per page (page 1 also has the header). Males first, then females, like the printed form;
    // empty lines pad each part so the form keeps its shape, and the totals sit at the end of each part.
    $firstCap = 27;
    $nextCap  = 32;
    $paginate = function (array $rows, array $tail, int $firstRoom, int $room) {
        $pages = []; $i = 0; $n = count($rows); $cap = $firstRoom;
        while (true) {
            $left = $n - $i;
            if ($left + count($tail) <= $cap) {
                $page = array_slice($rows, $i);
                for ($k = count($page); $k < $cap - count($tail); $k++) $page[] = null;
                $pages[] = array_merge($page, $tail);
                return $pages;
            }
            $take = min($left, $cap);
            $pages[] = array_slice($rows, $i, $take);
            $i += $take;
            $cap = $room;
        }
    };
    $total = fn ($label, $n) => ['total' => $label, 'n' => $n];
    $females = array_merge($females, $others); // learners with no sex recorded are listed after the females
    $pages = array_merge(
        $paginate($males, [$total('TOTAL MALE', count($males))], $firstCap, $nextCap),
        $paginate($females, [$total('TOTAL FEMALE', count($females) - count($others)), $total('COMBINED', count($males) + count($females))], $nextCap, $nextCap)
    );
    $pageCount = count($pages);
    $fmt = fn ($v) => $v === null ? '' : (string) $v;
@endphp

@foreach($pages as $p => $pageRows)
<div class="page {{ $p > 0 ? 'page-break' : '' }}">

    @if($p === 0)
    <table class="hdr"><tr>
        <td class="logo">@if($seal)<img src="{{ $seal }}" alt="" class="{{ $sealIsDeped ? 'deped' : '' }}">@endif</td>
        <td>
            <div class="title">School Form 5 (SF 5) Report on Promotion and Level of Progress &amp; Achievement</div>
            <div class="subtitle">Revised to conform with the instructions of DepEd Order 8, s. 2015</div>
            <table class="fields">
                <tr>
                    <td class="lbl" style="width:9%;">Region</td><td class="box" style="width:12%;">III</td>
                    <td class="lbl" style="width:9%;">Division</td><td class="box" style="width:24%;">NUEVA ECIJA</td>
                    <td class="lbl" style="width:9%;">District</td><td class="box" style="width:17%;">{{ $district ?: ' ' }}</td>
                    <td style="width:7%;"></td><td style="width:13%;"></td>
                </tr>
                <tr>
                    <td class="lbl">School ID</td><td class="box">{{ $schoolId ?: ' ' }}</td>
                    <td class="lbl">School Year</td><td class="box">{{ $schoolYear }}</td>
                    <td class="lbl">Curriculum</td><td class="box">MATATAG Curriculum</td>
                    <td></td><td></td>
                </tr>
                <tr>
                    <td class="lbl">School Name</td><td class="box" colspan="3">{{ strtoupper($sfSchoolName) }}</td>
                    <td class="lbl">Grade Level</td><td class="box">{{ $gradeLabel }}</td>
                    <td class="lbl">Section</td><td class="box">{{ $section->name }}</td>
                </tr>
            </table>
        </td>
        <td class="logo">@if($logo)<img src="{{ $logo }}" alt="">@endif</td>
    </tr></table>
    @endif

    <table class="layout"><tr>
        {{-- ── Learners ── --}}
        <td class="left-col">
            <table class="lr">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:12%;">LRN</th>
                        <th rowspan="2" style="width:30%;">LEARNER'S NAME<br><span class="note">(Last Name, First Name, Middle Name)</span></th>
                        <th rowspan="2" style="width:11%;">GENERAL AVERAGE <span class="note">(Whole numbers for non-honor as per DepEd Order 8, s. 2015)</span></th>
                        <th rowspan="2" style="width:13%;">ACTION TAKEN: PROMOTED, *CONDITIONALLY PROMOTED or RETAINED</th>
                        <th colspan="2">INCOMPLETE LEARNING AREA/S</th>
                    </tr>
                    <tr>
                        <th style="width:17%;"><span class="note">Incomplete Learning Area/s from previous school year/s that <b><u>had been completed</u></b> as of end of current School Year</span></th>
                        <th style="width:17%;"><span class="note">Incomplete Learning Area/s as of end of current School Year</span></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pageRows as $r)
                    @if($r === null)
                        <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    @elseif(isset($r['total']))
                        <tr>
                            <td class="c" style="font-weight:bold;">{{ $r['n'] }}</td>
                            <td style="font-weight:bold;">{{ $r['total'] }}</td>
                            <td class="shade"></td><td class="shade"></td><td class="shade"></td><td class="shade"></td>
                        </tr>
                    @else
                        <tr>
                            <td class="c">{{ $r['lrn'] }}</td>
                            <td>{{ $r['name'] }}</td>
                            <td class="c">{{ $fmt($r['avg']) }}</td>
                            <td class="c" style="font-size:6pt;">{{ $r['action'] }}</td>
                            <td></td>
                            <td style="font-size:6pt;">{{ $r['incomplete'] }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </td>

        {{-- ── Right column ── --}}
        <td class="right-col">
            @if($p === 0)
                <div style="height:{{ 52 }}px;"></div>
                <table class="sum">
                    <tr><th colspan="4" class="cap">SUMMARY TABLE</th></tr>
                    <tr><th>STATUS</th><th>MALE</th><th>FEMALE</th><th>TOTAL</th></tr>
                    @foreach($summary as $s)
                    <tr><td class="lbl">{{ strtoupper($s['label']) }}</td><td>{{ $s['m'] }}</td><td>{{ $s['f'] }}</td><td>{{ $s['t'] }}</td></tr>
                    @endforeach
                </table>
                <table class="sum">
                    <tr><th colspan="4" class="cap">LEVEL OF PROGRESS AND ACHIEVEMENT</th></tr>
                    <tr><th></th><th>MALE</th><th>FEMALE</th><th>TOTAL</th></tr>
                    @foreach($levels as $l)
                    <tr><td class="lbl">{{ $l['label'] }}<br><span style="font-weight:normal;">{{ $l['range'] }}</span></td><td>{{ $l['m'] }}</td><td>{{ $l['f'] }}</td><td>{{ $l['t'] }}</td></tr>
                    @endforeach
                </table>
            @else
                <div style="height:40px;"></div>
                <div class="sig">PREPARED BY:
                    <div class="line">{{ strtoupper($adviserName) }}</div>
                    <div class="role">Class Adviser</div><div class="role">(Name and Signature)</div>
                </div>
                <div class="sig">CERTIFIED CORRECT &amp; SUBMITTED:
                    <div class="line">{{ strtoupper($schoolHead) }}</div>
                    <div class="role">School Head</div><div class="role">(Name and Signature)</div>
                </div>
                <div class="sig">REVIEWED BY:
                    <div class="line"></div>
                    <div class="role">Division Representative</div><div class="role">(Name and Signature)</div>
                </div>
                @if($p === $pageCount - 1)
                <div class="guide">
                    <b><i>GUIDELINES:</i></b>
                    <p><b><i>1. Do not include Dropouts and Transferred Out (D.O.4, 2014)</i></b></p>
                    <p>2. To be prepared by the Adviser. Final rating per learning area should be taken from the record of subject teachers. The class adviser should compute for the General Average. (leave it blank for *conditionally promoted).</p>
                    <p>3. On the summary table, reflect the total number of learners PROMOTED (Final Grade of at least <b>75% in ALL learning areas</b>), RETAINED (Did not meet expectations in <b>three (3) or more learning areas</b>) and *CONDITIONALLY PROMOTED (*did not meet expectations in <b>not more than two (2) learning areas</b>) and the Level of Progress and Achievement according to the individual General Average. All provisions on classroom assessment and the grading system in the said Order shall be in effect for all grade levels - Deped Order 29, s. 2015.</p>
                    <p>4. Incomplete Learning Areas. The 1st sub-column refers to learning area/s that failed from previous SY but had been completed in the current SY. The 2nd sub-column presented the list of learning areas that did not meet expectation during the current SY.</p>
                    <p>5. Protocols of validation &amp; submission is under the discretion of the Schools Division Superintendent.</p>
                </div>
                @endif
            @endif
            <div class="pageno">School Form 5: Page {{ $p + 1 }} of {{ $pageCount }}</div>
        </td>
    </tr></table>
</div>
@endforeach
</body>
</html>
