        <div class="section-header">

            <div>

                <h1>Enrollment Management</h1>

                <p>Manage student enrollment applications and approvals.</p>

            </div>

            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

                {{-- Enrollment Window Toggle --}}
                @php $isEnrollOpen = $enrollmentOpen ?? true; @endphp
                <div id="enrollment-window-badge"
                     style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;
                            background:{{ $isEnrollOpen ? '#e8f8f0' : '#fdecea' }};
                            color:{{ $isEnrollOpen ? '#1a7a44' : '#c0392b' }};
                            border:1.5px solid {{ $isEnrollOpen ? '#27ae60' : '#e74c3c' }};">
                    <span style="width:8px;height:8px;border-radius:50%;display:inline-block;
                                 background:{{ $isEnrollOpen ? '#27ae60' : '#e74c3c' }};
                                 box-shadow:0 0 0 2px {{ $isEnrollOpen ? '#a8d5b5' : '#f5b7b1' }};"></span>
                    <span id="enrollment-window-label">Enrollment {{ $isEnrollOpen ? 'OPEN' : 'CLOSED' }}</span>
                </div>

                <button class="btn-dash {{ $isEnrollOpen ? 'btn-secondary' : 'btn-primary' }}"
                        id="enrollment-toggle-btn"
                        onclick="toggleEnrollmentWindow()">
                    <i class="bi bi-{{ $isEnrollOpen ? 'lock-fill' : 'unlock-fill' }} me-1"></i>
                    <span id="enrollment-toggle-label">{{ $isEnrollOpen ? 'Close Enrollment' : 'Open Enrollment' }}</span>
                </button>

                <a href="#" class="btn-dash btn-primary" onclick="openWalkInEnrollmentModal()">
                    <i class="bi bi-person-plus-fill"></i> New Enrollment
                </a>

            </div>

        </div>

        {{-- Enrollment closed notice --}}
        @if(!$isEnrollOpen)
        <div style="background:#fff8ec;border:1px solid #f5a623;border-radius:10px;padding:12px 18px;margin-bottom:16px;display:flex;align-items:center;gap:10px;font-size:13px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#d68910;font-size:16px;"></i>
            <span><strong>Online enrollment is currently closed.</strong> Students cannot submit new applications. Walk-in enrollment is still available through this panel.</span>
        </div>
        @endif

        {{-- Enrollment Stats --}}

        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-clipboard-check-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $enrolledThisYear ?? 0 }}</div>

                        <div class="stat-label">Enrolled This Year</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon gold"><i class="bi bi-cash-stack"></i></div>

                    <div>

                        <div class="stat-value">₱{{ number_format($totalFeesCollected ?? 0, 2) }}</div>

                        <div class="stat-label">Total Fees Collected</div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-exclamation-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $pendingPaymentsCount ?? 0 }}</div>

                        <div class="stat-label">Pending Payments</div>

                    </div>

                </div>

            </div>

        </div>

        {{-- React Component Island: Dashboard Charts --}}

        <div id="admin-dashboard-charts"></div>

        <div class="content-card mb-4">
            <div class="p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="enrollmentSearchInput" class="form-fld"
                                   placeholder="Search by name, email, or reference number..."
                                   value="{{ $enrollmentSearch ?? '' }}"
                                   oninput="debouncedEnrollmentSearch()"
                                   onkeydown="if(event.key==='Enter'){ event.preventDefault(); clearTimeout(_enrollmentSearchTimer); filterEnrollments(); }">
                        </div>
                    </div>
                    @if(($enrollmentSearch ?? '') !== '')
                    <div class="col-md-2">
                        <button type="button" class="btn-dash btn-secondary w-100" onclick="clearEnrollmentSearch()"><i class="bi bi-x-lg"></i> Clear</button>
                    </div>
                    @endif
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-fld" id="statusFilter" data-sort="{{ $sort ?? 'newest' }}" data-grade="{{ $gradeFilter ?? 'all' }}" onchange="filterEnrollments()">
                            <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ ($statusFilter ?? 'all') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($statusFilter ?? 'all') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="enrolled" {{ ($statusFilter ?? 'all') === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                            <option value="rejected" {{ ($statusFilter ?? 'all') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-fld" id="gradeFilter" data-sort="{{ $sort ?? 'newest' }}" data-status="{{ $statusFilter ?? 'all' }}" onchange="filterEnrollmentsGrade()">
                            <option value="all" {{ ($gradeFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Grades</option>
                            <option value="nursery" {{ ($gradeFilter ?? 'all') === 'nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="kindergarten" {{ ($gradeFilter ?? 'all') === 'kindergarten' ? 'selected' : '' }}>Kindergarten</option>
                            <option value="grade1" {{ ($gradeFilter ?? 'all') === 'grade1' ? 'selected' : '' }}>Grade 1</option>
                            <option value="grade2" {{ ($gradeFilter ?? 'all') === 'grade2' ? 'selected' : '' }}>Grade 2</option>
                            <option value="grade3" {{ ($gradeFilter ?? 'all') === 'grade3' ? 'selected' : '' }}>Grade 3</option>
                            <option value="grade4" {{ ($gradeFilter ?? 'all') === 'grade4' ? 'selected' : '' }}>Grade 4</option>
                            <option value="grade5" {{ ($gradeFilter ?? 'all') === 'grade5' ? 'selected' : '' }}>Grade 5</option>
                            <option value="grade6" {{ ($gradeFilter ?? 'all') === 'grade6' ? 'selected' : '' }}>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-fld" id="enrollmentSortFilter" data-status="{{ $statusFilter ?? 'all' }}" data-grade="{{ $gradeFilter ?? 'all' }}">
                            <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ ($sort ?? 'newest') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ ($sort ?? 'newest') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ ($sort ?? 'newest') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="text-muted" style="font-size:13px;">
                            @if(isset($enrollments))
                            Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() }} enrollments
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">

            <div class="content-card-header"><h6>Enrollment Records</h6></div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Student ID</th>

                            <th>Name</th>

                            <th>Grade</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @if(isset($enrollments) && $enrollments->count() > 0)

                            @foreach($enrollments as $e)

                            <tr>

                                <td>{{ $e->reference_number }}</td>

                                <td>
                                    {{ $e->student_data['first_name'] ?? '' }}
                                    {{ $e->student_data['middle_name'] ?? '' }}
                                    {{ $e->student_data['last_name'] ?? '' }}
                                    @php $eType = $e->student_data['student_type'] ?? ''; @endphp
                                    @if($eType === 'returning')
                                        <span style="display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px;vertical-align:middle;">
                                            <i class="bi bi-arrow-repeat"></i> Re-Enroll
                                        </span>
                                    @elseif($eType === 'transferee')
                                        <span style="display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;color:#15803d;border:1px solid #86efac;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px;vertical-align:middle;">
                                            <i class="bi bi-box-arrow-in-right"></i> Transferee
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    {{ $e->student_data['grade_level'] ?? '' }}

                                </td>

                                <td>

                                    @php
                                        $enrollIcon = match($e->status) {
                                            'enrolled'  => 'bi-check-circle-fill',
                                            'approved'  => 'bi-shield-check',
                                            'pending'   => 'bi-clock',
                                            'declined'  => 'bi-x-circle-fill',
                                            'completed' => 'bi-archive-fill',
                                            default     => 'bi-circle',
                                        };
                                    @endphp
                                    <span class="status-badge status-{{ $e->status }}">
                                        <i class="bi {{ $enrollIcon }}"></i> {{ ucfirst($e->status) }}
                                    </span>

                                </td>

                                <td>{{ $e->created_at }}</td>

                                <td>

                                    <a href="#" class="btn btn-sm js-enrollment-view" data-id="{{ $e->id }}" title="View Application Details"
                                        style="background:#eff6ff;color:#1d4ed8;border:none;">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($e->status === 'pending')
                                        <button class="btn btn-sm js-enrollment-approve" data-id="{{ $e->id }}" title="Approve Enrollment"
                                            style="background:#f0fdf4;color:#16a34a;border:none;">
                                            <i class="bi bi-check"></i>
                                        </button>

                                        <button class="btn btn-sm js-enrollment-decline" data-id="{{ $e->id }}" title="Decline Enrollment"
                                            style="background:#fef2f2;color:#dc2626;border:none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif

                                </td>

                            </tr>

                            @endforeach

                        @else

                            <tr>

                                <td colspan="6" style="text-align:center; color:var(--text); padding:40px;">

                                    <i class="bi bi-clipboard" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                    No enrollment records yet.

                                </td>

                            </tr>

                        @endif

                    </tbody>

                </table>

                {{-- Pagination Links --}}
                <div class="p-3 border-top" style="border-color:var(--border);">
                    @if(isset($enrollments))
                    {{ $enrollments->appends([
                        'sort' => $sort ?? 'newest',
                        'status' => $statusFilter ?? 'all',
                        'grade' => $gradeFilter ?? 'all',
                    ])->links() }}
                    @endif
                    @if(isset($enrollments) && $enrollments->count() > 0)
                    <div class="pagination-info">
                        Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }} of {{ $enrollments->total() }} enrollments
                    </div>
                    @endif
                </div>

            </div>

        </div>
