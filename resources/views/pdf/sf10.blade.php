<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF10 - {{ $student->name }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; background: #fff; }
    .page { width: 100%; padding: 1px; }

    /* ── Header ── */
    .sf-header { display: table; width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 6px; }
    .sf-header-logo { display: table-cell; width: 65px; vertical-align: middle; text-align: center; }
    .sf-header-logo img { width: 55px; height: 55px; }
    .sf-header-center { display: table-cell; vertical-align: middle; text-align: center; }
    .sf-header-center .republic { font-size: 9px; }
    .sf-header-center .deped { font-size: 10px; font-weight: bold; }
    .sf-header-center .region { font-size: 9px; }
    .sf-header-center .school-name { font-size: 13px; font-weight: bold; margin-top: 3px; text-transform: uppercase; }
    .sf-header-center .school-addr { font-size: 9px; }
    .sf-header-deped { display: table-cell; width: 65px; vertical-align: middle; text-align: center; }
    .sf-header-deped img { width: 55px; height: 55px; }

    /* ── Form Title ── */
    .form-title-bar { text-align: center; margin: 4px 0; }
    .form-title-bar .form-code { font-size: 11px; font-weight: bold; }
    .form-title-bar .form-name { font-size: 13px; font-weight: bold; text-transform: uppercase; }
    .form-title-bar .form-sub { font-size: 9px; font-style: italic; }

    /* ── Section headings ── */
    .part-label {
        background: #003087;
        color: #fff;
        font-size: 9px;
        font-weight: bold;
        padding: 3px 8px;
        margin: 6px 0 3px;
        text-transform: uppercase;
    }

    /* ── Field rows ── */
    .field-row { display: table; width: 100%; border-collapse: collapse; }
    .field-cell { display: table-cell; padding: 2px 4px; border: 1px solid #999; vertical-align: bottom; }
    .field-label { font-size: 8px; color: #444; display: block; }
    .field-value { font-size: 10px; font-weight: bold; border-bottom: 1px solid #000; min-height: 16px; display: block; padding: 1px 2px; }
    .field-empty { border-bottom: 1px solid #000; min-height: 16px; display: block; }

    /* ── Inline table rows ── */
    table.info-tbl { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
    table.info-tbl td { border: 1px solid #aaa; padding: 2px 4px; font-size: 10px; vertical-align: top; }
    table.info-tbl .lbl { font-size: 7.5px; color: #555; display: block; margin-bottom: 1px; }
    table.info-tbl .val { font-weight: bold; }

    /* ── Academic Record Table ── */
    table.grade-tbl { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.grade-tbl th { background: #003087; color: #fff; font-size: 8.5px; padding: 4px 3px; text-align: center; border: 1px solid #002060; }
    table.grade-tbl td { border: 1px solid #aaa; padding: 3px 4px; font-size: 9px; text-align: center; vertical-align: middle; }
    table.grade-tbl td.subj { text-align: left; font-size: 9px; }
    table.grade-tbl tr:nth-child(even) td { background: #f5f8ff; }
    table.grade-tbl .avg-row td { background: #e8edff; font-weight: bold; font-size: 9.5px; }
    table.grade-tbl .section-hdr td { background: #d0d9f0; font-weight: bold; font-size: 8.5px; text-align: left; padding: 2px 4px; }

    /* ── Signature block ── */
    .sig-block { margin-top: 14px; display: table; width: 100%; }
    .sig-cell { display: table-cell; width: 33%; text-align: center; padding: 0 8px; vertical-align: bottom; }
    .sig-line { border-top: 1px solid #000; margin-top: 30px; padding-top: 3px; font-size: 9px; font-weight: bold; }
    .sig-sub { font-size: 8px; color: #555; }

    /* ── Footer note ── */
    .footer-note { margin-top: 6px; font-size: 7.5px; color: #555; border-top: 1px solid #ccc; padding-top: 4px; }

    /* ── Pass/Fail color ── */
    .grade-pass { color: #1a6b2d; }
    .grade-fail { color: #c0392b; }
    .grade-desc { color: #1a3a6c; }

    .page-break { page-break-after: always; }

    .two-col { display: table; width: 100%; }
    .two-col-left { display: table-cell; width: 50%; padding-right: 4px; }
    .two-col-right { display: table-cell; width: 50%; padding-left: 4px; }
</style>
</head>
<body>
<div class="page">

    {{-- ═══════════════════════ HEADER ═══════════════════════ --}}
    <div class="sf-header">
        <div class="sf-header-logo">
            @if($schoolLogoBase64)
                <img src="{{ $schoolLogoBase64 }}" alt="School Logo" style="width:55px;height:55px;object-fit:contain;">
            @else
                <div style="width:55px;height:55px;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:7px;color:#666;text-align:center;">School<br>Logo</div>
            @endif
        </div>
        <div class="sf-header-center">
            <div class="republic">Republic of the Philippines</div>
            <div class="deped">Department of Education</div>
            <div class="region">Region III – Central Luzon · Schools Division of Nueva Ecija</div>
            <div class="school-name">{{ $schoolName }}</div>
            <div class="school-addr">{{ $schoolAddress }}</div>
        </div>
        <div class="sf-header-deped">
            @if($depedLogoBase64)
                <img src="{{ $depedLogoBase64 }}" alt="DepEd Logo" style="width:55px;height:55px;object-fit:contain;">
            @else
                <div style="width:55px;height:55px;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:7px;color:#666;text-align:center;">DepEd<br>Logo</div>
            @endif
        </div>
    </div>

    {{-- Form Title --}}
    <div class="form-title-bar">
        <div class="form-code">School Form 10 (SF10)</div>
        <div class="form-name">Learner's Permanent Academic Record</div>
        <div class="form-sub">(For Kinder to Grade 6)</div>
    </div>

    {{-- ═══════════ PART I — SCHOOL INFORMATION ═══════════ --}}
    <div class="part-label">Part I &nbsp;–&nbsp; School Information</div>
    <table class="info-tbl">
        <tr>
            <td style="width:22%"><span class="lbl">School ID</span><span class="val">—</span></td>
            <td style="width:55%"><span class="lbl">School Name</span><span class="val">{{ $schoolName }}</span></td>
            <td style="width:23%"><span class="lbl">School Year</span><span class="val">{{ $schoolYear }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="lbl">School Address</span><span class="val">{{ $schoolAddress }}</span></td>
            <td><span class="lbl">Division</span><span class="val">Nueva Ecija</span></td>
        </tr>
    </table>

    {{-- ═══════════ PART II — LEARNER'S BASIC INFORMATION ═══════════ --}}
    <div class="part-label">Part II &nbsp;–&nbsp; Learner's Basic Information</div>

    <table class="info-tbl">
        <tr>
            <td style="width:30%"><span class="lbl">Learner Reference Number (LRN)</span><span class="val">{{ $student->lrn ?: '—' }}</span></td>
            <td style="width:70%" colspan="3">
                <span class="lbl">Name (Last Name, First Name, Middle Name)</span>
                <span class="val">{{ strtoupper($lastName) }}, {{ strtoupper($firstName) }} {{ strtoupper($middleName) }}{{ $suffix ? ' '.$suffix : '' }}</span>
            </td>
        </tr>
        <tr>
            <td><span class="lbl">Date of Birth (mm/dd/yyyy)</span><span class="val">{{ $birthdate }}</span></td>
            <td><span class="lbl">Place of Birth</span><span class="val">{{ $placeOfBirth ?: '—' }}</span></td>
            <td><span class="lbl">Sex</span><span class="val">{{ ucfirst($gender) }}</span></td>
            <td><span class="lbl">Mother Tongue</span><span class="val">{{ $motherTongue }}</span></td>
        </tr>
    </table>

    {{-- Address --}}
    <table class="info-tbl" style="margin-top:2px;">
        <tr>
            <td colspan="4"><span class="lbl">Home Address</span></td>
        </tr>
        <tr>
            <td style="width:30%"><span class="lbl">House No. / Street</span><span class="val">{{ $streetAddress ?: '—' }}</span></td>
            <td style="width:25%"><span class="lbl">Barangay</span><span class="val">{{ $barangay ?: '—' }}</span></td>
            <td style="width:25%"><span class="lbl">Municipality / City</span><span class="val">{{ $city ?: '—' }}</span></td>
            <td style="width:20%"><span class="lbl">Province</span><span class="val">{{ $province ?: '—' }}</span></td>
        </tr>
    </table>

    {{-- Parents / Guardian --}}
    <table class="info-tbl" style="margin-top:2px;">
        <tr>
            <td style="width:33%"><span class="lbl">Name of Mother (Last, First, Middle)</span><span class="val">{{ $motherName ?: '—' }}</span></td>
            <td style="width:33%"><span class="lbl">Name of Father (Last, First, Middle)</span><span class="val">{{ $fatherName ?: '—' }}</span></td>
            <td style="width:34%"><span class="lbl">Guardian's Name (if not parent)</span><span class="val">{{ $guardianName ?: '—' }}</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
                <span class="lbl">Relationship to Learner</span><span class="val">{{ $guardianRelation ?: '—' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="lbl">Contact Number (of Mother / Father / Guardian)</span>
                <span class="val">{{ $guardianPhone ?: '—' }}</span>
            </td>
            <td>
                <span class="lbl">Religious Affiliation</span>
                <span class="val">{{ $religiousAffiliation ?: '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- Health Info --}}
    <table class="info-tbl" style="margin-top:2px;">
        <tr>
            <td style="width:20%"><span class="lbl">Blood Type</span><span class="val">{{ $bloodType ?: '—' }}</span></td>
            <td style="width:40%"><span class="lbl">Allergies / Special Medical Conditions</span><span class="val">{{ $allergies ?: 'None' }}</span></td>
            <td style="width:40%"><span class="lbl">Last School Attended (for transferees)</span><span class="val">{{ $lastSchool ?: '—' }}</span></td>
        </tr>
    </table>

    {{-- ═══════════ PART III — ACADEMIC RECORD ═══════════ --}}
    <div class="part-label">Part III &nbsp;–&nbsp; Academic Record (Current Enrollment)</div>

    @if($enrollments->isEmpty())
        <p style="font-size:10px; color:#666; padding:8px;">No enrollment record found for this student.</p>
    @else
        @foreach($enrollments as $enr)
            @php
                $sd = $enr->student_data ?? [];
                $grLevel = $enr->grade_level ?? ($sd['grade_level'] ?? '');
                $grMap = ['nursery'=>'Nursery','kindergarten'=>'Kindergarten','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
                $grDisplay = $grMap[$grLevel] ?? ucfirst($grLevel);
                $isDescriptive = in_array($grLevel, ['nursery','kindergarten']);
                $termCount = (int)$totalTerms;

                // Group grades for this enrollment by subject
                $enrGrades = $grades->where('enrollment_id', $enr->id)->groupBy('subject_id');
            @endphp

            <table class="grade-tbl">
                <thead>
                    <tr>
                        <th colspan="{{ 3 + $termCount }}" style="text-align:left; font-size:10px; background:#001f5e;">
                            {{ $grDisplay }} &nbsp;|&nbsp; Section: {{ $enr->section ?? '—' }} &nbsp;|&nbsp; S.Y. {{ $enr->school_year }} &nbsp;|&nbsp; Status: {{ ucfirst($enr->status) }}
                        </th>
                    </tr>
                    <tr>
                        <th style="width:5%; text-align:center;">#</th>
                        <th style="text-align:left; width:45%;">Learning Area / Subject</th>
                        @for($t = 1; $t <= $termCount; $t++)
                            <th>{{ $t }}{{ $t==1?'st':($t==2?'nd':'rd') }} Term</th>
                        @endfor
                        <th>Final Rating</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNum = 0; $passCount = 0; $failCount = 0; $totalFinal = 0; $gradeCount = 0; @endphp
                    @forelse($enr->subjects ?? [] as $subj)
                        @php
                            $rowNum++;
                            $subjGrades = $enrGrades->get($subj->id, collect());
                            $termGrades = [];
                            for($t = 1; $t <= $termCount; $t++) {
                                $g = $subjGrades->firstWhere('term', $t);
                                $termGrades[$t] = $g;
                            }
                            // Compute final rating
                            $numericVals = [];
                            foreach($termGrades as $tg) {
                                if($tg) {
                                    if($isDescriptive && $tg->descriptive_grade) {
                                        // skip numeric avg for descriptive
                                    } elseif($tg->grade !== null) {
                                        $numericVals[] = (float)$tg->grade;
                                    }
                                }
                            }
                            $finalRating = count($numericVals) ? round(array_sum($numericVals)/count($numericVals), 2) : null;
                            $remarks = '';
                            if($finalRating !== null) {
                                $remarks = $finalRating >= 75 ? 'Passed' : 'Failed';
                                if($finalRating >= 75) $passCount++; else $failCount++;
                                $totalFinal += $finalRating;
                                $gradeCount++;
                            }
                        @endphp
                        <tr>
                            <td>{{ $rowNum }}</td>
                            <td class="subj">{{ $subj->name }}</td>
                            @for($t = 1; $t <= $termCount; $t++)
                                @php $tg = $termGrades[$t]; @endphp
                                <td>
                                    @if($tg)
                                        @if($isDescriptive && $tg->descriptive_grade)
                                            <span class="grade-desc">{{ $tg->descriptive_grade }}</span>
                                        @elseif($tg->grade !== null)
                                            <span class="{{ $tg->grade >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ number_format($tg->grade, 0) }}</span>
                                        @else
                                            <span style="color:#999;">—</span>
                                        @endif
                                    @else
                                        <span style="color:#ccc;">—</span>
                                    @endif
                                </td>
                            @endfor
                            <td>
                                @if($finalRating !== null)
                                    <strong class="{{ $finalRating >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ number_format($finalRating, 0) }}</strong>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($remarks)
                                    <span class="{{ $remarks === 'Passed' ? 'grade-pass' : 'grade-fail' }}">{{ $remarks }}</span>
                                @else —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ 3 + $termCount }}" style="text-align:center; color:#999; padding:6px;">No subjects on record.</td></tr>
                    @endforelse

                    @if($gradeCount > 0)
                    <tr class="avg-row">
                        <td colspan="2" style="text-align:right;">General Average</td>
                        @for($t = 1; $t <= $termCount; $t++)<td>—</td>@endfor
                        <td><strong>{{ number_format($totalFinal / $gradeCount, 2) }}</strong></td>
                        <td><span class="{{ ($totalFinal/$gradeCount) >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ ($totalFinal/$gradeCount) >= 75 ? 'Promoted' : 'Retained' }}</span></td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div style="height:6px;"></div>
        @endforeach
    @endif

    {{-- ═══════════ SIGNATURE BLOCK ═══════════ --}}
    <div class="sig-block">
        <div class="sig-cell">
            <div class="sig-line">{{ $student->name }}</div>
            <div class="sig-sub">Learner / Parent / Guardian Signature over Printed Name</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">{{ $principalName ?: '______________________________' }}</div>
            <div class="sig-sub">School Principal / Head Teacher</div>
        </div>
        <div class="sig-cell">
            <div style="font-size:9px; text-align:right; color:#555;">Date Printed: {{ now()->format('F d, Y') }}</div>
            <div style="font-size:9px; text-align:right; color:#555; margin-top:2px;">Printed by: Admin</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-note">
        This document is an official record of {{ $schoolName }}. Unauthorized alteration of this form is punishable by law.
        &nbsp;|&nbsp; SF10 – Learner's Permanent Academic Record &nbsp;|&nbsp; {{ $schoolYear }}
    </div>

</div>
</body>
</html>
