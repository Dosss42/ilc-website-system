        <div class="section-header">

            <div>

                <p>Create and manage all subjects offered per grade level.</p>

            </div>

            <button class="btn-dash btn-primary" onclick="openSubjectModal()">

                <i class="bi bi-plus-lg"></i> Add Subject

            </button>

        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon blue"><i class="bi bi-book-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ ($subjects ?? null)?->total() ?? 0 }}</div>

                        <div class="stat-label">Total Subjects</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $activeSubjectsCount ?? 0 }}</div>

                        <div class="stat-label">Active</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $inactiveSubjectsCount ?? 0 }}</div>

                        <div class="stat-label">Inactive</div>

                    </div>

                </div>

            </div>

        </div>

        <div class="module-toolbar">

            <div class="toolbar-search">

                <i class="bi bi-search"></i>

                <input type="text" id="subjectSearchInput" placeholder="Search subjects..." onkeyup="filterSubjectTable()">

            </div>

            <div class="toolbar-filter">

                <select id="subjectGradeFilter" onchange="filterSubjectTable()">

                    <option value="">All Grades</option>

                    <option value="nursery">Nursery</option>

                    <option value="kindergarten">Kindergarten</option>

                    <option value="grade1">Grade 1</option>

                    <option value="grade2">Grade 2</option>

                    <option value="grade3">Grade 3</option>

                    <option value="grade4">Grade 4</option>

                    <option value="grade5">Grade 5</option>

                    <option value="grade6">Grade 6</option>

                </select>

                <select id="subjectStatusFilter" onchange="filterSubjectTable()">

                    <option value="">All Status</option>

                    <option value="active">Active</option>

                    <option value="inactive">Inactive</option>

                </select>

            </div>

            <span class="toolbar-count">Showing <span id="subjectVisibleCount">0</span> of {{ isset($subjects) ? $subjects->total() : 0 }} subjects</span>

        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6><i class="bi bi-list-ul me-2" style="color:var(--blue);"></i>All Subjects</h6>

            </div>

            <div class="p-3">

            <div style="overflow-x:auto;">

                <table class="dash-table" id="subjectTable">

                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Grade Level</th>
                            <th>In Schedule</th>
                            <th>Status</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse(($subjects ?? collect()) as $subj)

                        @php
                            $subjSchedCount = ($allSchedules ?? collect())->where('subject_id', $subj->id)->count();
                            $subjSections   = ($allSchedules ?? collect())
                                ->where('subject_id', $subj->id)
                                ->filter(fn($s) => $s->section)
                                ->pluck('section.name')->unique()->values();
                        @endphp

                        <tr data-grade="{{ $subj->grade_level }}" data-status="{{ $subj->is_active ? 'active' : 'inactive' }}" data-search="{{ strtolower($subj->code . ' ' . $subj->name) }}">

                            <td><span class="code-chip">{{ $subj->code }}</span></td>

                            <td>
                                <div style="font-weight:600;">{{ $subj->name }}</div>
                                @if($subj->description)
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ Str::limit($subj->description, 50) }}</div>
                                @endif
                            </td>

                            <td><span class="grade-chip">{{ ucfirst(str_replace(['grade','_'],[' Grade ',''],$subj->grade_level ?? '')) }}</span></td>

                            <td>
                                @if($subjSchedCount > 0)
                                    <div style="font-size:12px;font-weight:600;color:#166534;">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $subjSchedCount }} schedule(s)
                                    </div>
                                    <div style="font-size:10px;color:var(--muted);">
                                        {{ $subjSections->take(2)->implode(', ') }}{{ $subjSections->count() > 2 ? ' +'.($subjSections->count()-2) : '' }}
                                    </div>
                                @else
                                    <span style="font-size:11px;color:#bbb;"><i class="bi bi-calendar-x me-1"></i>Not scheduled</span>
                                @endif
                            </td>

                            <td>
                                @if($subj->is_active)
                                    <span class="status-badge active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                @else
                                    <span class="status-badge inactive"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>
                                @endif
                            </td>

                            <td style="text-align:center;">
                                <button class="action-btn edit js-subject-edit" title="Edit Subject"
                                    data-id="{{ $subj->id }}" data-name="{{ $subj->name }}" data-code="{{ $subj->code }}"
                                    data-desc="{{ $subj->description }}" data-grade="{{ $subj->grade_level }}"
                                    data-active="{{ $subj->is_active ? '1' : '0' }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="action-btn" title="Add to Schedule"
                                    style="background:#e8f5e9;color:#166534;"
                                    onclick="showSection('schedules'); setTimeout(() => { document.getElementById('scheduleGradeFilter').value='{{ $subj->grade_level }}'; document.getElementById('scheduleGradeFilter').dispatchEvent(new Event('change')); }, 300);">
                                    <i class="bi bi-calendar-plus-fill"></i>
                                </button>
                                <button class="action-btn delete js-subject-delete" title="Delete Subject" data-id="{{ $subj->id }}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6" style="text-align:center; padding:60px 20px;">

                                <div style="display:inline-block; background:var(--blue-pale); width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">

                                    <i class="bi bi-book" style="font-size:32px; color:var(--blue);"></i>

                                </div>

                                <div style="font-weight:700; font-size:15px; color:var(--text); margin-bottom:4px;">No Subjects Yet</div>

                                <div style="font-size:13px; color:var(--muted); margin-bottom:16px;">Get started by adding your first subject.</div>

                                <button class="btn-dash btn-primary" onclick="openSubjectModal()">

                                    <i class="bi bi-plus-lg"></i> Add First Subject

                                </button>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="p-3 border-top" style="border-color:var(--border);">
                    @if(isset($subjects))
                    {{ $subjects->links() }}
                    @endif
                    @if(isset($subjects) && $subjects->count() > 0)
                    <div class="pagination-info">
                        Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} subjects
                    </div>
                    @endif
                </div>

            </div>

            </div>

        </div>
