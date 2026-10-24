    <div id="section-grade-oversight" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-patch-check-fill" style="color:var(--gold);"></i> Grade Oversight</h1>
                <p>Review the grades teachers submit each term. Approved grades become official and visible to students.</p>
            </div>
            <button class="btn-dash btn-secondary" onclick="loadGradeSubmissions()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>

        {{-- Stats (all pending submissions) --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;" class="go-stats">
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                <div><div class="stat-value" id="goStatBatches">0</div><div class="stat-label">Batches to Review</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-journal-check"></i></div>
                <div><div class="stat-value" id="goStatGrades">0</div><div class="stat-label">Grades Waiting</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div><div class="stat-value" id="goStatFailed">0</div><div class="stat-label">Grades Below 75</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-person-badge-fill"></i></div>
                <div><div class="stat-value" id="goStatTeachers">0</div><div class="stat-label">Teachers Waiting</div></div>
            </div>
        </div>

        {{-- How it works --}}
        <div style="background:#e8f4ff;border:1px solid #b8d4f0;border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:12px;color:#1a3a6c;display:flex;gap:10px;align-items:flex-start;">
            <i class="bi bi-info-circle-fill" style="font-size:16px;margin-top:1px;flex-shrink:0;"></i>
            <div>
                <strong>Flow:</strong> Teacher submits a subject's grades for a term → you review them here →
                if a grade is wrong, click <i class="bi bi-pencil-fill"></i> to correct it (recorded in the System Logs, and the teacher sees "Edited by registrar") →
                <strong>Approve</strong> (grades become official: students see them in their portal, and they count on the SF10).
                Each card is one teacher's subject, term and section.
            </div>
        </div>

        {{-- Filters --}}
        <div class="content-card mb-3">
            <div style="padding:14px 18px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">
                @php
                    $goCurrentSy = \App\Models\Setting::get('current_school_year');
                    $goGrades = ['nursery' => 'Nursery', 'kindergarten' => 'Kindergarten', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2',
                                 'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];
                @endphp
                <div>
                    <label class="form-lbl" style="font-size:11px;">School Year</label>
                    <select id="goFilterSy" class="form-fld" style="min-width:170px;padding:7px 10px;font-size:13px;" onchange="goFillSections(); renderGradeOversight(true)">
                        <option value="">All School Years</option>
                        @foreach(\App\Models\Setting::schoolYearOptions() as $sy)
                            <option value="{{ $sy }}" {{ $sy === $goCurrentSy ? 'selected' : '' }}>S.Y. {{ $sy }}{{ $sy === $goCurrentSy ? ' (current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-lbl" style="font-size:11px;">Term</label>
                    <select id="goFilterTerm" class="form-fld" style="min-width:110px;padding:7px 10px;font-size:13px;" onchange="renderGradeOversight(true)">
                        <option value="">All Terms</option>
                        <option value="1">Term 1</option>
                        <option value="2">Term 2</option>
                        <option value="3">Term 3</option>
                    </select>
                </div>
                <div>
                    <label class="form-lbl" style="font-size:11px;">Grade Level</label>
                    <select id="goFilterGrade" class="form-fld" style="min-width:150px;padding:7px 10px;font-size:13px;" onchange="goFillSections(); renderGradeOversight(true)">
                        <option value="">All Grade Levels</option>
                        @foreach($goGrades as $gk => $gl)
                            <option value="{{ $gk }}">{{ $gl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-lbl" style="font-size:11px;">Section</label>
                    <select id="goFilterSection" class="form-fld" style="min-width:150px;padding:7px 10px;font-size:13px;" onchange="renderGradeOversight(true)">
                        <option value="">All Sections</option>
                    </select>
                </div>
                <div style="flex:1;min-width:220px;">
                    <label class="form-lbl" style="font-size:11px;">Search</label>
                    <input type="text" id="goFilterSearch" class="form-fld" style="padding:7px 12px;font-size:13px;" placeholder="Teacher, subject or student name…" oninput="renderGradeOversight(true)">
                </div>
                <button type="button" class="btn-dash btn-secondary" style="padding:8px 14px;font-size:12.5px;" onclick="goClearFilters()">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </div>

        <div id="goResultLabel" style="font-size:12px;color:var(--muted);margin:0 2px 10px;"></div>

        {{-- Nothing pending at all --}}
        <div id="gradeOversightEmpty" class="content-card" style="display:none;text-align:center;padding:60px 20px;color:var(--muted);">
            <i class="bi bi-check2-all" style="font-size:48px;display:block;margin-bottom:12px;opacity:0.25;"></i>
            <div style="font-size:15px;font-weight:600;margin-bottom:4px;">No Pending Submissions</div>
            <div style="font-size:12px;">All submitted grades have been reviewed.</div>
        </div>

        {{-- Pending, but none match the filters --}}
        <div id="goNoMatch" class="content-card" style="display:none;text-align:center;padding:40px 20px;color:var(--muted);">
            <i class="bi bi-funnel" style="font-size:36px;display:block;margin-bottom:10px;opacity:0.3;"></i>
            <div style="font-size:14px;font-weight:600;">No submissions match these filters</div>
            <button type="button" class="btn-dash btn-secondary" style="margin-top:10px;padding:6px 14px;font-size:12px;" onclick="goClearFilters()">Clear filters</button>
        </div>

        <div id="gradeSubmissionsList"></div>
        <div id="goPagination" class="content-card" style="display:none;"></div>
    </div>{{-- /section-grade-oversight --}}

    {{-- Every section, so the Section filter can follow the chosen School Year and Grade Level --}}
    <script>
        window._goSections = @json(\App\Models\Section::orderBy('name')->get(['name', 'grade_level', 'school_year']));
    </script>

    <style>
        @media (max-width: 992px) { #section-grade-oversight .go-stats { grid-template-columns: repeat(2, 1fr) !important; } }
        .go-card { margin-bottom: 16px; overflow: hidden; transition: opacity .35s ease, transform .35s ease, max-height .45s ease, margin .45s ease; max-height: 4000px; }
        .go-card.go-leaving { opacity: 0; transform: translateX(24px); max-height: 0; margin-bottom: 0; }
        .go-card.has-failed { border-left: 4px solid var(--red); }
        .go-card-head { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; }
        .go-head-left { display: flex; gap: 14px; align-items: flex-start; min-width: 260px; flex: 1; }
        .go-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--blue-pale); color: var(--blue); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .go-title { font-size: 16px; font-weight: 700; color: var(--text); line-height: 1.2; }
        .go-chips { margin-top: 6px; display: flex; flex-wrap: wrap; gap: 5px; }
        .go-chip { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 20px; background: var(--blue-pale); color: var(--blue); }
        .go-chip.term { background: #fff8ec; color: #b45309; }
        .go-chip.sy { background: #f1f5f9; color: #475569; }
        .go-meta { font-size: 12px; color: var(--muted); margin-top: 7px; }
        .go-head-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .go-metrics { display: flex; gap: 8px; }
        .go-metric { min-width: 76px; padding: 7px 10px; border-radius: 10px; background: #f8f9fa; border: 1px solid var(--border); text-align: center; }
        .go-metric .v { font-size: 17px; font-weight: 700; color: var(--text); line-height: 1.1; }
        .go-metric .l { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }
        .go-metric.good .v { color: var(--green); }
        .go-metric.bad { background: #fff5f5; border-color: #fecaca; }
        .go-metric.bad .v { color: var(--red); }
        .go-actions { display: flex; flex-direction: column; gap: 6px; align-items: stretch; }
        .go-toggle { background: none; border: 1.5px solid var(--border); border-radius: 8px; color: var(--blue); font-size: 12px; font-weight: 600; cursor: pointer; padding: 6px 12px; white-space: nowrap; }
        .go-toggle:hover { background: var(--blue-pale); }
        .go-grades { border-top: 1px solid var(--border); }
        .go-row-failed td { background: #fff5f5; }
        @media (max-width: 768px) { .go-head-right { width: 100%; justify-content: space-between; } .go-metrics { flex-wrap: wrap; } }
    </style>
