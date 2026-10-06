    <div id="section-teachers" class="dash-section" style="display:none;">

        <div class="section-header">

            <div>

                <h1><i class="bi bi-person-badge-fill" style="color:var(--purple); margin-right:8px;"></i>Teacher Management</h1>

                <p>Manage faculty accounts and assignments.</p>

            </div>

            <button class="btn-dash btn-primary" onclick="openTeacherModal()">

                <i class="bi bi-plus-lg"></i> Add Teacher

            </button>

        </div>

        @php
            $totalTeachers    = ($teachers ?? collect())->count();
            $activeTeachers   = ($teachers ?? collect())->where('is_active', true)->count();
            $inactiveTeachers = $totalTeachers - $activeTeachers;
            // Computed in EnrollmentController@adminIndex as an independent
            // count query — not from $subjects, which moved to
            // sectionSubjects() and was also only ever one page (15) of
            // the full roster anyway. See docs/system-improvement-plan.md.
            $teachersWithSubjects = $teachersWithSubjectsCount ?? 0;

            // Build per-teacher maps from already-loaded data
            // Advisory: from TeacherAssignment (is_advisory=true)
            $teacherAdvisoryMap = collect();
            foreach ((\App\Models\TeacherAssignment::where('is_advisory', true)
                ->with('section:id,name,grade_level')
                ->get()) as $ta) {
                if (!$ta->section) continue;
                $teacherAdvisoryMap->push([
                    'teacher_id'   => $ta->teacher_id,
                    'section_name' => $ta->section->name,
                    'grade_level'  => $ta->section->grade_level,
                ]);
            }

            // Schedule-based: from Schedule (unique section+teacher pairs)
            $teacherScheduleMap = collect();
            foreach (($allSchedules ?? collect()) as $sched) {
                if (!$sched->teacher || !$sched->section) continue;
                $key = $sched->teacher_id . '-' . $sched->section_id;
                if (!$teacherScheduleMap->contains('key', $key)) {
                    $teacherScheduleMap->push([
                        'key'          => $key,
                        'teacher_id'   => $sched->teacher_id,
                        'section_name' => $sched->section->name,
                        'grade_level'  => $sched->section->grade_level,
                        'subject_name' => $sched->subject->name ?? '',
                    ]);
                }
            }
        @endphp

        <div class="row g-3 mb-4">

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon purple"><i class="bi bi-person-badge-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $totalTeachers }}</div>

                        <div class="stat-label">Total Teachers</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $activeTeachers }}</div>

                        <div class="stat-label">Active</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">

                <div class="stat-card">

                    <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>

                    <div>

                        <div class="stat-value">{{ $inactiveTeachers }}</div>

                        <div class="stat-label">Inactive</div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="stat-value">{{ $teacherScheduleMap->pluck('teacher_id')->unique()->count() }}</div>
                        <div class="stat-label">With Schedules</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="module-toolbar">

            <div class="toolbar-search">

                <i class="bi bi-search"></i>

                <input type="text" id="teacherSearchInput" placeholder="Search teachers..." onkeyup="filterTeacherTable()">

            </div>

            <div class="toolbar-filter">

                <select id="teacherStatusFilter" onchange="filterTeacherTable()">

                    <option value="">All Status</option>

                    <option value="active">Active</option>

                    <option value="inactive">Inactive</option>

                </select>

            </div>

            <span class="toolbar-count"><span id="teacherVisibleCount">{{ $totalTeachers }}</span> of {{ $totalTeachers }} teachers</span>

        </div>

        <div class="content-card">

            <div class="content-card-header">

                <h6>All Teachers</h6>

                <a href="#"><i class="bi bi-download me-1"></i>Export</a>

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table" id="teacherTable">

                    <thead>

                        <tr>

                            <th>Teacher</th>

                            <th>Email</th>

                            <th>Sections Handled</th>

                            <th>Status</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse(($teachers ?? collect()) as $t)

                        @php
                            $tStatus      = $t->is_active ? 'active' : 'inactive';
                            $tAdvisory    = $teacherAdvisoryMap->where('teacher_id', $t->id)->values();
                            $tScheduleSec = $teacherScheduleMap->where('teacher_id', $t->id)
                                ->unique('section_name')->values();
                            $glMap = ['nursery'=>'Nursery','kindergarten'=>'Kinder','grade1'=>'G1','grade2'=>'G2','grade3'=>'G3','grade4'=>'G4','grade5'=>'G5','grade6'=>'G6'];
                        @endphp

                        <tr data-search="{{ strtolower($t->name . ' ' . $t->email) }}" data-status="{{ $tStatus }}">

                            <td>
                                <div class="user-row-name">
                                    <div class="user-row-avatar" style="background:var(--purple);">{{ strtoupper(substr($t->name, 0, 2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;">{{ $t->name }}</div>
                                        <div class="user-row-sub">ID: {{ $t->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="fs-12">{{ $t->email }}</td>

                            <td style="max-width:260px;">
                                {{-- Advisory sections --}}
                                @foreach($tAdvisory as $adv)
                                    <span title="Advisory: {{ $adv['section_name'] }}"
                                        style="display:inline-flex;align-items:center;gap:3px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;border-radius:12px;padding:2px 8px;font-size:11px;font-weight:600;margin:2px 2px 2px 0;white-space:nowrap;">
                                        <i class="bi bi-star-fill" style="font-size:9px;"></i>
                                        {{ $adv['section_name'] }}
                                        <span style="font-size:10px;opacity:0.75;">({{ $glMap[$adv['grade_level']] ?? $adv['grade_level'] }})</span>
                                    </span>
                                @endforeach
                                {{-- Schedule-based teaching sections --}}
                                @foreach($tScheduleSec->take(4) as $sch)
                                    <span title="Teaches in: {{ $sch['section_name'] }}"
                                        style="display:inline-flex;align-items:center;gap:3px;background:#e8f5e9;color:#166534;border:1px solid #bbf7d0;border-radius:12px;padding:2px 8px;font-size:11px;font-weight:600;margin:2px 2px 2px 0;white-space:nowrap;">
                                        <i class="bi bi-calendar3" style="font-size:9px;"></i>
                                        {{ $sch['section_name'] }}
                                        <span style="font-size:10px;opacity:0.75;">({{ $glMap[$sch['grade_level']] ?? $sch['grade_level'] }})</span>
                                    </span>
                                @endforeach
                                @if($tScheduleSec->count() > 4)
                                    <span style="background:#eee;border-radius:12px;padding:2px 8px;font-size:11px;color:#555;">+{{ $tScheduleSec->count() - 4 }} more</span>
                                @endif
                                @if($tAdvisory->count() === 0 && $tScheduleSec->count() === 0)
                                    <span style="color:#bbb;font-size:12px;">— Not assigned</span>
                                @endif
                            </td>

                            <td>

                                <span class="status-badge {{ $tStatus === 'active' ? 'enrolled' : 'inactive' }}">{{ ucfirst($tStatus) }}</span>

                            </td>

                            <td style="white-space:nowrap;">
                                <button class="action-btn edit js-teacher-edit" title="Edit Teacher"
                                    data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-email="{{ $t->email }}" data-active="{{ $t->is_active ? '1' : '0' }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="action-btn" title="Assign Advisory"
                                    style="background:#e0f2fe;color:#0369a1;"
                                    onclick="openAddAssignmentModalForTeacher({{ $t->id }}, '{{ addslashes($t->name) }}')">
                                    <i class="bi bi-star-fill"></i>
                                </button>
                                <button class="action-btn" title="Add Schedule"
                                    style="background:#e8f5e9;color:#166534;"
                                    onclick="showSection('schedules'); setTimeout(() => openScheduleModal(), 200);">
                                    <i class="bi bi-calendar-plus-fill"></i>
                                </button>
                                <button class="action-btn delete js-teacher-delete" title="Delete Teacher" data-id="{{ $t->id }}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6" style="text-align:center; color:var(--muted); padding:40px;">

                                <i class="bi bi-person-badge" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                No teachers added yet. Click "Add Teacher" to get started.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Teacher Management Pagination --}}
            <div class="p-3 border-top" style="border-color:var(--border);">
                @if(isset($teachers))
                {{ $teachers->links() }}
                @endif
                @if(isset($teachers) && $teachers->count() > 0)
                <div class="pagination-info">
                    Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} teachers
                </div>
                @endif
            </div>

        </div>

        {{-- Teacher Add/Edit Modal --}}

        <div class="modal fade" id="teacherModal" tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content modal-content-styled">

                    <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--purple), #7b1fa2);">

                        <h5 class="modal-title" id="teacherModalTitle" style="color:#fff;"><i class="bi bi-person-badge-fill me-2"></i>Add Teacher</h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body modal-body-styled">

                        <input type="hidden" id="teacher-id">

                        <div class="mb-3">

                            <label class="dash-form-label"><i class="bi bi-person me-1"></i>Full Name</label>

                            <input type="text" id="teacher-name" class="dash-form-control" placeholder="e.g. Juan Dela Cruz">

                        </div>

                        <div class="mb-3">

                            <label class="dash-form-label"><i class="bi bi-envelope me-1"></i>Email Address</label>

                            <div style="position:relative;">
                                <input type="email" id="teacher-email" class="dash-form-control"
                                       placeholder="e.g. teacher@gmail.com"
                                       oninput="validateTeacherEmailField(this)"
                                       style="padding-right:38px;">
                                <span id="teacher-email-icon" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:16px;display:none;"></span>
                            </div>
                            <div id="teacher-email-msg" style="font-size:11px;margin-top:5px;display:none;"></div>

                        </div>

                        <div class="mb-3" id="teacher-autogen-info" style="display:none;">

                            <div style="background:#e8f8f0; border:1px solid #b8e6c8; border-radius:8px; padding:14px 16px; display:flex; align-items:center; gap:10px;">

                                <i class="bi bi-shield-lock-fill" style="font-size:20px; color:#28a745;"></i>

                                <div>

                                    <div style="font-weight:700; font-size:13px; color:#155724;">Password Auto-Generated</div>

                                    <div style="font-size:12px; color:#3c763d;">A secure PIN will be generated and sent to the teacher's email automatically.</div>

                                </div>

                            </div>

                        </div>

                        <div class="mb-3" id="teacher-password-group" style="display:none;">

                            <label class="dash-form-label"><i class="bi bi-key me-1"></i>New Password</label>

                            <input type="password" id="teacher-password" class="dash-form-control" placeholder="Leave blank to keep current">

                            <small class="text-muted-alt">Leave blank to keep current password.</small>

                        </div>

                        <div class="mb-3">

                            <label class="dash-form-label"><i class="bi bi-toggle-on me-1"></i>Status</label>

                            <select id="teacher-active" class="dash-form-control">

                                <option value="1">Active</option>

                                <option value="0">Inactive</option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer modal-footer-styled">

                        <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>

                        <button type="button" class="btn-dash btn-primary" onclick="saveTeacher()"><i class="bi bi-check-lg me-1"></i>Save Teacher</button>

                    </div>

                </div>

            </div>

        </div>

    </div>{{-- /section-teachers --}}
