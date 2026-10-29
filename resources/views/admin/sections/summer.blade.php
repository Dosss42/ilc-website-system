    <div id="section-summer" class="dash-section" style="display:none;">

        <div class="section-header">

            <div>

                <h1>Summer Class Management</h1>

                <p>Manage summer classes for students who need remedial or make-up courses.</p>

            </div>

            <button class="btn-dash btn-primary" onclick="openCreateSummerModal()">

                <i class="bi bi-plus-lg"></i> Create Summer Class

            </button>

        </div>

        {{-- Click a card: list those classes below (the counts cover every school year) — js/stat-jump.js --}}
        <div class="row g-3 mb-4">
            @foreach([['blue', 'bi-sun-fill', $summerTotalCount ?? 0, 'Total Classes', ''], ['gold', 'bi-hourglass-split', $summerOngoingCount ?? 0, 'Ongoing', 'ongoing'], ['green', 'bi-check-circle-fill', $summerCompletedCount ?? 0, 'Completed', 'completed']] as [$color, $icon, $count, $label, $st])
            <div class="col-md-4 col-sm-6">
                <div class="stat-card stat-card-link" role="button" tabindex="0" title="Show {{ $st === '' ? 'all summer classes' : $label . ' classes' }} below"
                     data-jump="{{ json_encode(["set" => ["#summer-sy-filter" => "", "#summer-status-filter" => $st], "to" => "#summer-list-card"]) }}">
                    <div class="stat-icon {{ $color }}"><i class="bi {{ $icon }}"></i></div>
                    <div>
                        <div class="stat-value">{{ $count }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="module-toolbar">
            <div class="toolbar-filter">
                <select id="summer-sy-filter" onchange="loadSummerClasses()">
                    {{-- Opens on the Current School Year (Admin › Settings › Academic) --}}
                    @php $summerCurSy = \App\Models\Setting::getCurrentSchoolYear(); @endphp
                    <option value="">All School Years</option>
                    @foreach(\App\Models\Setting::schoolYearOptions() as $sy)
                        <option value="{{ $sy }}" {{ $sy === $summerCurSy ? 'selected' : '' }}>{{ $sy }}{{ $sy === $summerCurSy ? ' (current)' : '' }}</option>
                    @endforeach
                </select>
                <select id="summer-status-filter" onchange="loadSummerClasses()">
                    <option value="">All Status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="content-card" id="summer-list-card">

            <div class="content-card-header">

                <h6><i class="bi bi-list-ul me-2" style="color:var(--blue);"></i>Summer Classes</h6>

            </div>

            <div style="overflow-x:auto;">

                <table class="dash-table">

                    <thead>

                        <tr>

                            <th>Subject</th>

                            <th>Grade Level</th>

                            <th>Teacher</th>

                            <th>Schedule</th>

                            <th>Period</th>

                            <th>Students</th>

                            <th>Status</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody id="summer-classes-body">

                        <tr>

                            <td colspan="8" style="text-align:center; color:var(--text); padding:40px;">

                                <i class="bi bi-sun" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.3;"></i>

                                No summer classes created yet. Click "Create Summer Class" to get started.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <div id="summer-pagination" class="p-3 border-top" style="border-color:var(--border);">
                <!-- Pagination will be rendered here -->
            </div>

        </div>

    </div>{{-- /section-summer --}}
