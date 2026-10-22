    <div id="section-dashboard" class="dash-section">

        <div class="section-header">

            <div>

                <h1>Admin Dashboard</h1>

                <p>{{ now()->format('l, F d, Y') }} — Welcome back!</p>

            </div>

            <a href="#" onclick="openWalkInEnrollmentModal()" class="btn-dash btn-primary">

                <i class="bi bi-person-plus-fill"></i> Enroll Student

            </a>

        </div>

        {{-- Stat Cards — same height (h-100) and same three lines each: value, label, sub-line --}}

        <div class="row g-3 mb-4">

            <div class="col-md-3 col-sm-6">

                <div class="stat-card h-100">

                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $recentStudents->total() ?? 0 }}</div>

                        <div class="stat-label">Total Students</div>

                        <div class="stat-change up"><i class="bi bi-person-check"></i> Registered accounts</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card h-100">

                    <div class="stat-icon gold"><i class="bi bi-person-badge-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $teachersCount ?? ($teachers ?? collect())->count() }}</div>

                        <div class="stat-label">Total Teachers</div>

                        <div class="stat-change up"><i class="bi bi-book"></i> {{ $teachersWithSubjectsCount ?? 0 }} teaching this S.Y.</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card h-100">

                    <div class="stat-icon green"><i class="bi bi-clipboard-check-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $paidCount ?? 0 }}</div>

                        <div class="stat-label">Paid</div>

                        {{-- was hardcoded "S.Y. 2026“2027" (with a broken dash) --}}
                        <div class="stat-change up"><i class="bi bi-calendar3"></i> S.Y. {{ $currentSchoolYear ?? \App\Models\Setting::getCurrentSchoolYear() }}</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card h-100">

                    <div class="stat-icon red"><i class="bi bi-hourglass-split"></i></div>

                    <div>

                        <div class="stat-value">{{ $unpaidCount ?? 0 }}</div>

                        <div class="stat-label">Unpaid</div>

                        <div class="stat-change down"><i class="bi bi-exclamation-circle"></i> Needs action</div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ── Overview Charts ── --}}
        @php
            // Single aggregate query instead of one COUNT per month (was 6
            // queries — see docs/system-improvement-plan.md item #5).
            $chMonths = []; $chMonthKeys = [];
            for ($i=5;$i>=0;$i--) {
                $m = now()->subMonths($i);
                $chMonths[] = $m->format('M Y');
                $chMonthKeys[] = $m->format('Y-m');
            }
            $chEnrollByMonth = \App\Models\Enrollment::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt")
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('ym')
                ->pluck('cnt', 'ym');
            $chEnroll = array_map(fn($k) => (int) ($chEnrollByMonth[$k] ?? 0), $chMonthKeys);
        @endphp
        {{-- Chart data rides on the canvases as data-* attributes: variables set in this
             included partial never reach adminDashboard.blade.php's chart script, so both
             charts used to draw empty. The doughnut uses the same numbers as the Paid /
             Unpaid cards above (each student's latest enrollment). --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="content-card h-100">
                    <div class="content-card-header">
                        <h6><i class="bi bi-graph-up me-2" style="color:var(--blue);"></i>Monthly Enrollment Trend</h6>
                        <span style="font-size:11px;color:var(--muted);">Last 6 months</span>
                    </div>
                    <div class="p-3" style="height:220px;"><canvas id="adminEnrollTrend" data-labels='@json($chMonths)' data-values='@json($chEnroll)'></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="content-card h-100">
                    <div class="content-card-header">
                        <h6><i class="bi bi-pie-chart-fill me-2" style="color:var(--blue);"></i>Payment Status</h6>
                    </div>
                    <div class="p-3" style="height:220px;display:flex;justify-content:center;"><canvas id="adminPayDoughnut" data-values='@json([(int) ($paidCount ?? 0), (int) ($partialCount ?? 0), (int) ($unpaidCount ?? 0)])'></canvas></div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}

        <div class="row g-3 mb-4">

            <div class="col-12">

                <div class="content-card">

                    <div class="content-card-header"><h6>Quick Actions</h6></div>

                    <div class="p-3">

                        <div class="row g-2">

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('enrollment')" class="quick-action-card">

                                    <i class="bi bi-person-plus-fill"></i>

                                    <span>Enroll Student</span>

                                </a>

                            </div>

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('students')" class="quick-action-card">

                                    <i class="bi bi-file-earmark-text-fill"></i>

                                    <span>View Records</span>

                                </a>

                            </div>

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('reports')" class="quick-action-card">

                                    <i class="bi bi-printer-fill"></i>

                                    <span>Print Report</span>

                                </a>

                            </div>

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('announcements')" class="quick-action-card">

                                    <i class="bi bi-megaphone-fill"></i>

                                    <span>Post Announcement</span>

                                </a>

                            </div>

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('enrollment')" class="quick-action-card">

                                    <i class="bi bi-cash-stack"></i>

                                    <span>Finance</span>

                                </a>

                            </div>

                            <div class="col-6 col-md-2">

                                <a href="#" onclick="showSection('guidance')" class="quick-action-card">

                                    <i class="bi bi-journal-medical"></i>

                                    <span>Guidance</span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Students + Announcements --}}

        <div class="row g-4">

            <div class="col-lg-7">

                <div class="content-card h-100">

                    <div class="content-card-header">

                        <h6>Recent Students</h6>

                        <a href="#" onclick="showSection('students')">View All</a>

                    </div>

                    <div style="overflow-x:auto;">

                        <table class="dash-table">

                            <thead>

                                <tr>

                                    <th>Student</th>

                                    <th>Grade</th>

                                    <th>Section</th>

                                    <th>Status</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($recentStudents as $s)

                                @php
                                    // Grade/section live on the student's latest Enrollment,
                                    // not on the User row — same derivation
                                    // students-content.blade.php already uses.
                                    $recentEnr = $s->latestEnrollment;
                                    $recentSd = $recentEnr->student_data ?? [];
                                    $recentGradeRaw = $recentSd['grade_level'] ?? ($recentEnr->grade_level ?? null);
                                    $recentGradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];
                                    $recentGradeDisplay = $recentGradeMap[$recentGradeRaw] ?? ($recentGradeRaw ?: '-');
                                    $recentSectionDisplay = ($recentEnr && $recentEnr->section && $recentEnr->section !== 'Unassigned') ? $recentEnr->section : '-';
                                @endphp

                                <tr>

                                    <td>

                                        <div class="user-row-name">

                                            <div class="user-row-avatar">{{ strtoupper(substr($s->name, 0, 2)) }}</div>

                                            <div>

                                                <div style="font-weight:600;">{{ $s->name }}</div>

                                                <div class="user-row-sub">{{ $s->email }}</div>

                                            </div>

                                        </div>

                                    </td>

                                    <td>{{ $recentGradeDisplay }}</td>

                                    <td>{{ $recentSectionDisplay }}</td>

                                    <td><span class="status-badge {{ $s->is_active ? 'active' : 'inactive' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>

                                    <td>

                                        <a href="#" class="action-btn view js-student-view" data-id="{{ $s->id }}"><i class="bi bi-eye-fill"></i></a>

                                        <a href="#" class="action-btn edit js-student-edit" data-id="{{ $s->id }}"><i class="bi bi-pencil-fill"></i></a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        {{-- Recent Students Pagination --}}
                        <div class="p-3 border-top" style="border-color:var(--border);">
                            @if(isset($recentStudents))
                            {{ $recentStudents->links() }}
                            @endif
                            @if(isset($recentStudents) && $recentStudents->total() > 0)
                            <div class="pagination-info">
                                Showing {{ $recentStudents->firstItem() ?? 0 }} to {{ $recentStudents->lastItem() ?? 0 }} of {{ $recentStudents->total() }} students
                            </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="content-card h-100">

                    <div class="content-card-header">

                        <h6>Recent Announcements</h6>

                        <a href="#" onclick="showSection('announcements')">View All</a>

                    </div>

                    {{-- The newest real announcements (these three used to be hardcoded samples) --}}
                    @php
                        $dashAnnouncements = \App\Models\Announcement::with('teacher:id,name')
                            ->where('is_active', true)
                            ->latest()
                            ->take(6)
                            ->get(['id', 'title', 'category', 'teacher_id', 'created_at']);
                    @endphp

                    @forelse($dashAnnouncements as $ann)

                    <div class="ann-dash-item">

                        <div class="ann-date-badge"><span class="ann-day">{{ $ann->created_at->format('d') }}</span><span class="ann-mon">{{ $ann->created_at->format('M') }}</span></div>

                        <div><div class="ann-body-title">{{ $ann->title }}</div><div class="ann-body-meta">Posted by {{ $ann->teacher->name ?? 'Admin' }}{{ $ann->category ? ' · ' . ucfirst($ann->category) : '' }}</div></div>

                    </div>

                    @empty

                    <div style="padding:40px 20px;text-align:center;color:var(--muted);font-size:13px;">
                        <i class="bi bi-megaphone" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>No announcements yet.
                    </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>{{-- /section-dashboard --}}
