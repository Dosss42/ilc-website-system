    <div id="section-assessment" class="dash-section" style="display:none;">

        <div class="section-header">
            <div>
                <h1><i class=”bi bi-mortarboard-fill” style=”color:var(--gold);”></i> Assessment &amp; Promotion</h1>
                <p>Review grades and promote or retain existing Nursery, Kinder, and Grade 1–6 students. Transferee students are excluded.</p>
            </div>
        </div>

        @php
            $glMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
            $nextGlMap = ['nursery'=>'kindergarten','kindergarten'=>'grade1','grade1'=>'grade2','grade2'=>'grade3','grade3'=>'grade4','grade4'=>'grade5','grade5'=>'grade6','grade6'=>'graduated'];

            // $assessStudents, $assessTotal/$assessPending/$assessDone/$assessByGrade,
            // and $assessGradeFilter/$assessStatusFilter/$assessSearchTerm all come
            // pre-computed + paginated from the controller now.
            $assessBaseQuery = [
                'section'       => 'assessment',
                'assess_grade'  => $assessGradeFilter ?? 'all',
                'assess_status' => $assessStatusFilter ?? 'all',
                'assess_search' => $assessSearchTerm ?? '',
            ];
            $assessUrl = fn($overrides) => url()->current() . '?' . http_build_query(array_merge($assessBaseQuery, $overrides));
        @endphp

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                <div><div class="stat-value">{{ $assessTotal }}</div><div class="stat-label">Eligible Students</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div><div class="stat-value">{{ $assessPending }}</div><div class="stat-label">Pending Assessment</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div><div class="stat-value">{{ $assessDone }}</div><div class="stat-label">Already Assessed</div></div>
            </div>
        </div>

        {{-- Info banner --}}
        <div style="background:#e8f4ff;border:1px solid #b8d4f0;border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:12px;color:#1a3a6c;display:flex;gap:10px;align-items:flex-start;">
            <i class="bi bi-info-circle-fill" style="font-size:16px;margin-top:1px;flex-shrink:0;"></i>
            <div>
                <strong>Who is assessed:</strong> Nursery, Kinder, and Grade 1–6 students who completed a school year at ILC.<br>
                <strong>Not assessed:</strong> Transferees during their first year at ILC — they are assessed next cycle.<br>
                <strong>Flow:</strong> End of year → Assess → Promoted / Retained / Graduated → Open next S.Y. enrollment → Students re-enroll at new grade level.
            </div>
        </div>

        {{-- Grade filter chips --}}
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;align-items:center;">
            <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-right:4px;">Grade:</span>
            @php $assessGradeActive = ($assessGradeFilter ?? 'all') === 'all'; @endphp
            <a href="{{ $assessUrl(['assess_grade' => 'all', 'assess_page' => 1]) }}"
                style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;display:inline-block;
                    border:1.5px solid {{ $assessGradeActive ? 'var(--blue)' : '#ddd' }};
                    background:{{ $assessGradeActive ? 'var(--blue)' : '#f8f9fa' }};
                    color:{{ $assessGradeActive ? '#fff' : '#555' }};">
                All ({{ $assessTotal }})
            </a>
            @foreach($glMap as $gk => $gl)
                @if(($assessByGrade[$gk] ?? 0) > 0)
                @php $assessGradeActive = ($assessGradeFilter ?? '') === $gk; @endphp
                <a href="{{ $assessUrl(['assess_grade' => $gk, 'assess_page' => 1]) }}"
                    style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;display:inline-block;
                        border:1.5px solid {{ $assessGradeActive ? 'var(--blue)' : '#ddd' }};
                        background:{{ $assessGradeActive ? 'var(--blue)' : '#f8f9fa' }};
                        color:{{ $assessGradeActive ? '#fff' : '#555' }};">
                    {{ $gl }} ({{ $assessByGrade[$gk] }})
                </a>
                @endif
            @endforeach

            {{-- Status filter --}}
            <div style="margin-left:auto;display:flex;gap:6px;">
                @foreach(['all' => 'All', 'pending' => 'Pending', 'done' => 'Assessed'] as $sk => $sl)
                    @php $assessStatusActive = ($assessStatusFilter ?? 'all') === $sk; @endphp
                    <a href="{{ $assessUrl(['assess_status' => $sk, 'assess_page' => 1]) }}"
                        style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;display:inline-block;
                            border:1.5px solid {{ $assessStatusActive ? 'var(--blue)' : '#ddd' }};
                            background:{{ $assessStatusActive ? 'var(--blue)' : '#f8f9fa' }};
                            color:{{ $assessStatusActive ? '#fff' : '#555' }};">
                        {{ $sl }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Bulk promote / auto-advance — only offered once a single grade is selected --}}
        @if(($assessGradeFilter ?? 'all') !== 'all')
        <div style="margin-bottom:16px;">
            @if(in_array($assessGradeFilter, ['nursery', 'kindergarten']))
            <button type="button" class="btn-dash btn-success" style="padding:8px 16px;font-size:12.5px;"
                onclick="openAutoAdvanceModal('{{ $assessGradeFilter }}', '{{ $glMap[$assessGradeFilter] ?? ucfirst($assessGradeFilter) }}')">
                <i class="bi bi-arrow-up-circle me-1"></i> Advance All {{ $glMap[$assessGradeFilter] ?? ucfirst($assessGradeFilter) }} Students
            </button>
            <span style="font-size:11.5px;color:var(--muted);margin-left:8px;">
                {{ $glMap[$assessGradeFilter] ?? ucfirst($assessGradeFilter) }} is developmental, not academic — every student here advances automatically, no review needed.
            </span>
            @else
            <button type="button" class="btn-dash btn-success" style="padding:8px 16px;font-size:12.5px;"
                onclick="openBulkPromoteModal('{{ $assessGradeFilter }}', '{{ $glMap[$assessGradeFilter] ?? ucfirst($assessGradeFilter) }}')">
                <i class="bi bi-arrow-up-circle me-1"></i> Promote All {{ $glMap[$assessGradeFilter] ?? ucfirst($assessGradeFilter) }} Students
            </button>
            <span style="font-size:11.5px;color:var(--muted);margin-left:8px;">
                Only students with a settled balance and complete documents will be included. Failing/incomplete grades and guidance concerns are shown as warnings but don't block promotion.
            </span>
            @endif
        </div>
        @endif

        {{-- Search --}}
        <div class="content-card mb-3">
            <div style="padding:14px 18px;">
                <form method="GET" action="{{ url()->current() }}" id="assess-search-form">
                    <input type="hidden" name="section" value="assessment">
                    <input type="hidden" name="assess_grade" value="{{ $assessGradeFilter ?? 'all' }}">
                    <input type="hidden" name="assess_status" value="{{ $assessStatusFilter ?? 'all' }}">
                    <input type="text" name="assess_search" id="assessSearch" placeholder="Search student name..."
                        value="{{ $assessSearchTerm ?? '' }}"
                        oninput="debounceFormSubmit(this)"
                        style="width:100%;padding:9px 14px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;">
                </form>
            </div>
        </div>

        {{-- Assessment Table --}}
        <div class="content-card">
            <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6><i class="bi bi-table me-2" style="color:var(--gold);"></i>Students for Assessment</h6>
                <span style="font-size:12px;color:var(--muted);" id="assess-count-label">{{ $assessStudents->total() }} student(s)</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table" id="assessTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Student Type</th>
                            <th>Status</th>
                            <th>Assessment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessStudents as $as)
                        @php
                            $asEnr    = $as->latestEnrollment;
                            $asGl     = $asEnr ? ($asEnr->student_data['grade_level'] ?? ($asEnr->grade_level ?? '')) : '';
                            $asGlLbl  = $glMap[$asGl] ?? ucfirst($asGl);
                            $asType   = $asEnr ? ucfirst($asEnr->student_data['student_type'] ?? '—') : '—';
                            $asSection= $asEnr->section ?? '—';
                            $asPromo  = $as->promotions->sortByDesc('id')->first();
                            $isDone   = $asPromo !== null;
                            $promoResult = $isDone
                                ? ($asPromo->to_grade === 'graduated' ? 'Graduated' : (($asPromo->from_grade === $asPromo->to_grade) ? 'Retained' : 'Promoted'))
                                : null;
                            $resultColor = match($promoResult) {
                                'Promoted'  => '#28a745',
                                'Retained'  => '#e67e00',
                                'Graduated' => '#1a3a6c',
                                default     => null
                            };
                        @endphp
                        <tr style="{{ $isDone ? 'background:#f6fef9;' : 'background:#fffbf0;' }}">
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-weight:600;">{{ $as->name }}</span>
                                    @php $asGuidanceCount = $assessGuidanceCounts[$as->id] ?? 0; @endphp
                                    @if($asGuidanceCount > 0)
                                        <span title="{{ $asGuidanceCount }} open guidance concern(s) — informational only, does not block assessment"
                                            style="display:inline-flex;align-items:center;gap:3px;background:#fff3e0;color:#e65100;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;">
                                            <i class="bi bi-flag-fill"></i> {{ $asGuidanceCount }}
                                        </span>
                                    @endif
                                    @php $asSummer = $assessSummerStatus[$as->id] ?? null; @endphp
                                    @if($asSummer && $asSummer['failed'] > 0)
                                        @php $asSummerDone = $asSummer['cleared'] >= $asSummer['failed']; @endphp
                                        <span title="Summer class remediation: {{ $asSummer['cleared'] }} of {{ $asSummer['failed'] }} failing subject(s) cleared — informational only, does not block assessment"
                                            style="display:inline-flex;align-items:center;gap:3px;background:{{ $asSummerDone ? '#e8f5e9' : '#fff3e0' }};color:{{ $asSummerDone ? '#2e7d32' : '#e65100' }};font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;">
                                            <i class="bi bi-sun-fill"></i> {{ $asSummer['cleared'] }}/{{ $asSummer['failed'] }}
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:11px;color:var(--muted);">{{ $as->email }}</div>
                            </td>
                            <td><span class="grade-chip">{{ $asGlLbl }}</span></td>
                            <td style="font-size:13px;">{{ $asSection }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $asType }}</td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;
                                    background:{{ $isDone ? '#e8f5e9' : '#fff8e1' }};color:{{ $isDone ? '#2e7d32' : '#856404' }};">
                                    <i class="bi bi-{{ $isDone ? 'check-circle-fill' : 'hourglass-split' }}"></i>
                                    {{ $isDone ? 'Assessed' : 'Pending' }}
                                </span>
                            </td>
                            <td>
                                @if($isDone && $asPromo)
                                    <div style="font-size:12px;font-weight:700;color:{{ $resultColor }};">
                                        <i class="bi bi-{{ $promoResult==='Promoted'?'arrow-up-circle-fill':($promoResult==='Graduated'?'star-fill':'arrow-repeat') }} me-1"></i>
                                        {{ $promoResult }}
                                    </div>
                                    @if($promoResult !== 'Retained' && $promoResult !== 'Graduated')
                                        <div style="font-size:11px;color:var(--muted);">→ {{ $glMap[$asPromo->to_grade] ?? ucfirst($asPromo->to_grade) }}</div>
                                    @endif
                                    <div style="font-size:10px;color:var(--muted);">{{ $asPromo->promoted_at?->format('M d, Y') ?? '' }}</div>
                                @else
                                    <span style="color:#ccc;font-size:12px;">—</span>
                                @endif
                            </td>
                            @php
                                $asTotalFee  = $asEnr ? ($asEnr->total_fee ?? 0) : 0;
                                $asAmtPaid   = $asEnr ? ($asEnr->payment_amount ?? 0) : 0;
                                $asBalance   = $asEnr ? ($asEnr->remaining_balance ?? max(0, $asTotalFee - $asAmtPaid)) : 0;
                                $asPayStatus = $asEnr ? ($asEnr->payment_status ?? 'pending') : 'pending';
                                $asFromSY    = $asEnr ? ($asEnr->school_year ?? '') : '';
                            @endphp
                            <td>
                                <button type="button" class="action-btn edit"
                                    title="{{ $isDone ? 'Re-assess' : 'Assess Student' }}"
                                    onclick="openSmAssessModal({{ $as->id }}, '{{ addslashes($as->name) }}', '{{ $asGl }}', '{{ $asSection }}', {{ $asTotalFee }}, {{ $asAmtPaid }}, {{ $asBalance }}, '{{ $asPayStatus }}', '{{ $asFromSY }}')"
                                    style="{{ $isDone ? 'background:#e8f5e9;color:#2e7d32;' : '' }}">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:60px;color:var(--muted);">
                                <i class="bi bi-mortarboard" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                                @if($assessTotal === 0)
                                    No eligible students for assessment.<br>
                                    <small>Nursery, Kinder, and Grade 1–6 students (excluding transferees) appear here.</small>
                                @else
                                    No students match the current filter.<br>
                                    <small><a href="{{ $assessUrl(['assess_grade' => 'all', 'assess_status' => 'all', 'assess_search' => '', 'assess_page' => 1]) }}">Clear filters</a></small>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                {{ $assessStudents->appends($assessBaseQuery)->links() }}
                <div class="pagination-info">
                    Showing {{ $assessStudents->firstItem() ?? 0 }} to {{ $assessStudents->lastItem() ?? 0 }} of {{ $assessStudents->total() }} student(s)
                </div>
            </div>
        </div>

        {{-- Assessment History --}}
        <div class="content-card mt-4">
            <div class="content-card-header">
                <h6><i class="bi bi-clock-history me-2" style="color:var(--gold);"></i>Assessment History</h6>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>From Grade</th>
                            <th>Result</th>
                            <th>To Grade</th>
                            <th>School Year</th>
                            <th>Remarks</th>
                            <th>Assessed By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allPromos = \App\Models\Promotion::with(['student','promotedBy'])
                                ->orderByDesc('promoted_at')
                                ->paginate(15, ['*'], 'promo_history_page');
                        @endphp
                        @forelse($allPromos as $promo)
                        @php
                            $pResult = $promo->to_grade === 'graduated' ? 'Graduated'
                                     : ($promo->from_grade === $promo->to_grade ? 'Retained' : 'Promoted');
                            $pColor  = match($pResult) { 'Promoted'=>'#28a745','Retained'=>'#e67e00','Graduated'=>'#1a3a6c',default=>'#666' };
                        @endphp
                        <tr>
                            <td style="font-weight:600;">{{ $promo->student->name ?? '—' }}</td>
                            <td><span class="grade-chip">{{ $glMap[$promo->from_grade] ?? ucfirst($promo->from_grade) }}</span></td>
                            <td>
                                <span style="font-weight:700;color:{{ $pColor }};">{{ $pResult }}</span>
                            </td>
                            <td><span class="grade-chip">{{ $glMap[$promo->to_grade] ?? ucfirst($promo->to_grade) }}</span></td>
                            <td style="font-size:12px;">{{ $promo->from_school_year }} → {{ $promo->to_school_year }}</td>
                            <td style="font-size:12px;color:var(--muted);max-width:200px;">{{ $promo->remarks ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $promo->promotedBy->name ?? 'System' }}</td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $promo->promoted_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:30px;color:var(--muted);">No assessment history yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                {{ $allPromos->appends(['section' => 'assessment'])->links() }}
                <div class="pagination-info">
                    Showing {{ $allPromos->firstItem() ?? 0 }} to {{ $allPromos->lastItem() ?? 0 }} of {{ $allPromos->total() }} record(s)
                </div>
            </div>
        </div>
    </div>
