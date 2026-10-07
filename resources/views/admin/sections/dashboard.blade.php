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

        {{-- Stat Cards --}}

        {{-- CHANGE: Replace 0 with dynamic counts from controller --}}

        <div class="row g-3 mb-4">

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ ($students ?? collect())->count() }}</div>

                        <div class="stat-label">Total Students</div>

                        <div class="stat-change up"><i class="bi bi-arrow-up-short"></i> This year</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon gold"><i class="bi bi-person-badge-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ ($teachers ?? collect())->count() }}</div>

                        <div class="stat-label">Total Teachers</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-clipboard-check-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $paidCount ?? 0 }}</div>

                        <div class="stat-label">Paid</div>

                        <div class="stat-change up">S.Y. 2026“2027</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-hourglass-split"></i></div>

                    <div>

                        <div class="stat-value">{{ $unpaidCount ?? 0 }}</div>

                        <div class="stat-label">Unpaid</div>

                        <div class="stat-change down">Needs action</div>

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

            $chPaid    = \App\Models\Enrollment::where('payment_status','paid')->count();
            $chPartial = \App\Models\Enrollment::where('payment_status','partial')->count();
            $chUnpaid  = \App\Models\Enrollment::whereNotIn('payment_status',['paid','partial'])->count();
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-graph-up me-2" style="color:var(--blue);"></i>Monthly Enrollment Trend</h6>
                        <span style="font-size:11px;color:var(--muted);">Last 6 months</span>
                    </div>
                    <div class="p-3" style="height:200px;"><canvas id="adminEnrollTrend"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h6><i class="bi bi-pie-chart-fill me-2" style="color:var(--blue);"></i>Payment Status</h6>
                    </div>
                    <div class="p-3" style="height:200px;display:flex;justify-content:center;"><canvas id="adminPayDoughnut"></canvas></div>
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

                <div class="content-card">

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

                                    <td>{{ $s->grade ?? '-' }}</td>

                                    <td>{{ $s->section ?? '-' }}</td>

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

                <div class="content-card">

                    <div class="content-card-header">

                        <h6>Recent Announcements</h6>

                        <a href="#" onclick="showSection('announcements')">View All</a>

                    </div>

                    <div class="ann-dash-item">

                        <div class="ann-date-badge"><span class="ann-day">15</span><span class="ann-mon">Jun</span></div>

                        <div><div class="ann-body-title">School Opening Day</div><div class="ann-body-meta">Posted by Admin</div></div>

                    </div>

                    <div class="ann-dash-item">

                        <div class="ann-date-badge"><span class="ann-day">10</span><span class="ann-mon">Jun</span></div>

                        <div><div class="ann-body-title">Enrollment Period Now Open</div><div class="ann-body-meta">Posted by Registrar</div></div>

                    </div>

                    <div class="ann-dash-item">

                        <div class="ann-date-badge"><span class="ann-day">05</span><span class="ann-mon">Jun</span></div>

                        <div><div class="ann-body-title">Parent-Teacher Conference</div><div class="ann-body-meta">Posted by Admin</div></div>

                    </div>

                </div>

            </div>

        </div>

    </div>{{-- /section-dashboard --}}
