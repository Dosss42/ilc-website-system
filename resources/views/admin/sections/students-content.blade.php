        <div class="section-header">

            <div>

                <h1>Student Management</h1>

                <p>View, add, and manage all student records.</p>

            </div>

       {{--     <button class="btn-dash btn-primary" onclick="openWalkInEnrollmentModal()">

                <i class="bi bi-person-plus-fill"></i> Add Student

            </button> --}}

        </div>

        {{-- Analysis Summary Cards — $smTotal/$smEnrolled/etc. computed in
             EnrollmentController@adminIndex from the full filtered query,
             not from the paginated $students list (which is capped at 15). --}}
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-total-count">{{ $smTotal ?? 0 }}</div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-enrolled-count">{{ $smEnrolled ?? 0 }}</div>
                        <div class="stat-label">Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon teal"><i class="bi bi-check2-square"></i></div>
                    <div>
                        <div class="stat-value" id="sm-approved-count">{{ $smApproved ?? 0 }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-value" id="sm-pending-count">{{ $smPending ?? 0 }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-notenrolled-count">{{ $smNotEnrolled ?? 0 }}</div>
                        <div class="stat-label">Not Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="stat-value" id="sm-paid-count">{{ $smPaid ?? 0 }}</div>
                        <div class="stat-label">Fully Paid</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="stat-value" id="sm-balance-count">{{ $smBalance ?? 0 }}</div>
                        <div class="stat-label">With Balance</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Legend --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:16px;padding:12px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;font-size:12px;">
            <span style="font-weight:600;color:#555;margin-right:4px;">Enrollment:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Enrolled</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e3f2fd;border:1px solid #1565c0;border-radius:3px;display:inline-block;"></span> Approved</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff8ec;border:1px solid #f5a623;border-radius:3px;display:inline-block;"></span> Pending</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Declined</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#ede7f6;border:1px solid #512da8;border-radius:3px;display:inline-block;"></span> Completed</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #bf360c;border-radius:3px;display:inline-block;"></span> Dropped</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#f5f5f5;border:1px solid #546e7a;border-radius:3px;display:inline-block;"></span> Ghost</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e0f7fa;border:1px solid #006064;border-radius:3px;display:inline-block;"></span> Transferred</span>
            <span style="width:1px;height:16px;background:#ddd;margin:0 6px;"></span>
            <span style="font-weight:600;color:#555;margin-right:4px;">Payment:</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#e8f8f0;border:1px solid #27ae60;border-radius:3px;display:inline-block;"></span> Paid</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fff3e0;border:1px solid #e65100;border-radius:3px;display:inline-block;"></span> Partial</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;background:#fdecea;border:1px solid #e74c3c;border-radius:3px;display:inline-block;"></span> Unpaid</span>
        </div>


        {{-- Search & Filter --}}
        <div class="content-card mb-4">
            <div class="p-3">
                <form method="GET" action="{{ url()->current() }}" class="row g-3" id="student-filter-form">
                    <input type="hidden" name="section" value="students">
                    <div class="col-md-4">
                        <input type="text" name="student_search" class="form-fld" placeholder="Search student name, LRN, or email..." value="{{ $studentSearch ?? '' }}" oninput="debounceFormSubmit(this)">
                    </div>
                    <div class="col-md-2">
                        <select name="student_grade" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" {{ (!$studentGradeFilter || ($studentGradeFilter ?? 'all') === 'all') ? 'selected' : '' }}>All Grades</option>
                            <option value="nursery" {{ ($studentGradeFilter ?? '') === 'nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="kindergarten" {{ ($studentGradeFilter ?? '') === 'kindergarten' ? 'selected' : '' }}>Kindergarten</option>
                            <option value="grade1" {{ ($studentGradeFilter ?? '') === 'grade1' ? 'selected' : '' }}>Grade 1</option>
                            <option value="grade2" {{ ($studentGradeFilter ?? '') === 'grade2' ? 'selected' : '' }}>Grade 2</option>
                            <option value="grade3" {{ ($studentGradeFilter ?? '') === 'grade3' ? 'selected' : '' }}>Grade 3</option>
                            <option value="grade4" {{ ($studentGradeFilter ?? '') === 'grade4' ? 'selected' : '' }}>Grade 4</option>
                            <option value="grade5" {{ ($studentGradeFilter ?? '') === 'grade5' ? 'selected' : '' }}>Grade 5</option>
                            <option value="grade6" {{ ($studentGradeFilter ?? '') === 'grade6' ? 'selected' : '' }}>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="student_status" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" {{ (!$studentStatusFilter || ($studentStatusFilter ?? 'all') === 'all') ? 'selected' : '' }}>All Status</option>
                            <option value="enrolled"    {{ ($studentStatusFilter ?? '') === 'enrolled'    ? 'selected' : '' }}>Enrolled</option>
                            <option value="approved"    {{ ($studentStatusFilter ?? '') === 'approved'    ? 'selected' : '' }}>Approved</option>
                            <option value="pending"     {{ ($studentStatusFilter ?? '') === 'pending'     ? 'selected' : '' }}>Pending</option>
                            <option value="completed"   {{ ($studentStatusFilter ?? '') === 'completed'   ? 'selected' : '' }}>Completed</option>
                            <option value="dropped"     {{ ($studentStatusFilter ?? '') === 'dropped'     ? 'selected' : '' }}>Dropped</option>
                            <option value="ghost"       {{ ($studentStatusFilter ?? '') === 'ghost'       ? 'selected' : '' }}>Ghost</option>
                            <option value="transferred" {{ ($studentStatusFilter ?? '') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="declined"    {{ ($studentStatusFilter ?? '') === 'declined'    ? 'selected' : '' }}>Declined</option>
                            <option value="not_enrolled"{{ ($studentStatusFilter ?? '') === 'not_enrolled'? 'selected' : '' }}>Not Enrolled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="student_payment" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" {{ (!$studentPaymentFilter || ($studentPaymentFilter ?? 'all') === 'all') ? 'selected' : '' }}>All Payment</option>
                            <option value="paid" {{ ($studentPaymentFilter ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ ($studentPaymentFilter ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ ($studentPaymentFilter ?? '') === 'unpaid' ? 'selected' : '' }}>Not Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ ($sort ?? 'newest') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ ($sort ?? 'newest') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ ($sort ?? 'newest') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="student_schoolyear" class="form-fld" onchange="this.form.requestSubmit()">
                            <option value="all" {{ (!$studentSchoolYearFilter || $studentSchoolYearFilter === '' || $studentSchoolYearFilter === 'all') ? 'selected' : '' }}>All School Years</option>
                            @foreach(\App\Models\Setting::schoolYearOptions() as $sy)
                                <option value="{{ $sy }}" {{ ($studentSchoolYearFilter ?? '') === $sy ? 'selected' : '' }}>{{ $sy }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6>All Students</h6>

                <a href="#"><i class="bi bi-download me-1"></i>Export</a>

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Grade</th>

                            <th>Section</th>

                            <th>Contact</th>

                            <th>Status</th>

                            <th>Payment</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @if(isset($students) && $students->count() > 0)

                            @foreach($students as $s)

                            @php

                                $enr = $s->latestEnrollment;

                                $sd = $enr->student_data ?? [];

                                $gradeRaw = $sd['grade_level'] ?? ($enr->grade_level ?? null);

                                $gradeMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'Grade 1','grade2'=>'Grade 2','grade3'=>'Grade 3','grade4'=>'Grade 4','grade5'=>'Grade 5','grade6'=>'Grade 6'];

                                $gradeDisplay = $gradeMap[$gradeRaw] ?? ($gradeRaw ?: 'N/A');

                                $sectionDisplay = ($enr && $enr->section && $enr->section !== 'Unassigned') ? $enr->section : '—';

                                $enrollStatus = $enr ? $enr->status : 'inactive';

                                $statusClass = match($enrollStatus) {
                                    'enrolled'    => 'enrolled',
                                    'approved'    => 'approved',
                                    'pending'     => 'pending',
                                    'declined'    => 'declined',
                                    'completed'   => 'completed',
                                    'dropped'     => 'dropped',
                                    'ghost'       => 'ghost',
                                    'transferred' => 'transferred',
                                    default       => 'inactive',
                                };

                                $statusLabel = match($enrollStatus) {
                                    'enrolled'    => 'Enrolled',
                                    'approved'    => 'Approved',
                                    'pending'     => 'Pending',
                                    'declined'    => 'Declined',
                                    'completed'   => 'Completed',
                                    'dropped'     => 'Dropped',
                                    'ghost'       => 'Ghost',
                                    'transferred' => 'Transferred',
                                    default       => 'Not Enrolled',
                                };

                                $isAssessable = $enr && in_array($enrollStatus, ['enrolled','completed'])
                                    && in_array($gradeRaw, ['grade1','grade2','grade3','grade4','grade5','grade6']);

                                $payStatus = $enr->payment_status ?? 'unpaid';

                                $payIcon = $payStatus === 'paid' ? '✓' : '✗';
                                $payColor = $payStatus === 'paid' ? '#28a745' : ($payStatus === 'partial' ? '#e67e00' : '#dc3545');
                                $payBg = $payStatus === 'paid' ? '#e8f5e9' : ($payStatus === 'partial' ? '#fff3e0' : '#ffebee');

                            @endphp

                            <tr data-grade="{{ $gradeRaw ?? '' }}" data-status="{{ $enrollStatus }}" data-payment="{{ $payStatus }}" data-schoolyear="{{ $enr->school_year ?? '' }}" data-search="{{ strtolower($s->name . ' ' . ($s->lrn ?? '') . ' ' . $s->email) }}">

                                <td>

                                    <div class="user-row-name">

                                        <div class="user-row-avatar">{{ strtoupper(substr($s->name, 0, 2)) }}</div>

                                        <div>

                                            <div style="font-weight:600;">{{ $s->name }}</div>

                                            <div class="user-row-sub">{{ $enr ? $enr->reference_number : 'ID: '.$s->id }}</div>

                                        </div>

                                    </div>

                                </td>

                                <td>{{ $gradeDisplay }}</td>

                                <td><span class="{{ $sectionDisplay === '—' ? 'text-muted-alt' : '' }}">{{ $sectionDisplay }}</span></td>

                                <td class="fs-12">{{ $s->email }}</td>

                                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>

                                <td>
                                    @if($payStatus === 'paid')
                                        <span class="status-badge paid"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                    @elseif($payStatus === 'partial' || $payStatus === 'partially_paid')
                                        <span class="status-badge partial"><i class="bi bi-hourglass-split"></i> Partial</span>
                                    @elseif($payStatus === 'pending')
                                        <span class="status-badge pending"><i class="bi bi-clock"></i> Pending</span>
                                    @else
                                        <span class="status-badge unpaid"><i class="bi bi-exclamation-circle-fill"></i> Unpaid</span>
                                    @endif
                                </td>

                                <td style="white-space:nowrap;">

                                    <a href="#" class="action-btn view js-student-view" data-id="{{ $s->id }}" title="View"><i class="bi bi-eye-fill"></i></a>

                                    <a href="#" class="action-btn edit js-student-edit" data-id="{{ $s->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></a>

                                    <a href="{{ route('admin.students.sf10', $s->id) }}" target="_blank"
                                       class="action-btn" title="Download SF10"
                                       style="background:#e8f8f0;color:#1a6b2d;">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>

                                    <a href="#" class="action-btn" title="Change Status"
                                       style="background:#f0f4f8;color:#555;"
                                       onclick="openChangeStatusModal({{ $s->id }}, {{ $enr ? $enr->id : 'null' }}, '{{ $enrollStatus }}', '{{ addslashes($s->name) }}')">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </a>
                                    {{--  
                                    @if($isAssessable)
                                    <a href="#" class="action-btn" title="Assess Student"
                                       style="background:#e8f8f0;color:#1a7a44;"
                                       onclick="openSmAssessModal({{ $s->id }}, {{ $enr->id }}, '{{ addslashes($s->name) }}', '{{ $gradeRaw }}', '{{ $gradeDisplay }}')">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </a>
                                    @endif--}}

                                    <a href="#" class="action-btn js-student-archive"
                                       data-id="{{ $s->id }}" data-name="{{ addslashes($s->name) }}"
                                       title="Archive Student"
                                       style="background:#fff8e1;color:#b45309;">
                                        <i class="bi bi-archive-fill"></i>
                                    </a>

                                </td>

                            </tr>

                            @endforeach

                        @else

                            <tr>

                                <td colspan="7" style="text-align:center; color:var(--text); padding:40px;">

                                    <i class="bi bi-people" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                    No students enrolled yet.

                                </td>

                            </tr>

                        @endif

                    </tbody>

                </table>

                {{-- Pagination Links --}}
                <div class="p-3 border-top" style="border-color:var(--border);">
                    @if(isset($students))
                    {{ $students->appends([
                        'section' => 'students',
                        'sort' => $sort ?? 'newest',
                        'student_search' => $studentSearch ?? '',
                        'student_grade' => $studentGradeFilter ?? '',
                        'student_status' => $studentStatusFilter ?? '',
                        'student_payment' => $studentPaymentFilter ?? '',
                        'student_schoolyear' => $studentSchoolYearFilter ?? '',
                    ])->links() }}
                    <div class="pagination-info">
                        Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                    </div>
                    @endif
                </div>

            </div>

        </div>
