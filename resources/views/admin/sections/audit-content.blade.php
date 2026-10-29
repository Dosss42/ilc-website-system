        <div class="section-header">
            <div>
                <h1>Audit Trail</h1>
                <p>Who did what and when — every role except the Super Admin. Cashier and Finance actions are recorded here.</p>
            </div>
        </div>

        {{-- Click a card: set the filters below to match it and scroll to the entries — js/stat-jump.js --}}
        @php
            $aF = fn ($type, $from, $to) => ['#audit-filter-form [name=audit_search]' => '', '#audit-filter-form [name=audit_role]' => '',
                '#audit-filter-form [name=audit_from]' => $from, '#audit-filter-form [name=audit_to]' => $to, '#audit-filter-form [name=audit_type]' => $type];
            $aCards = [
                ['blue', 'bi-journal-check', $stats['total'], 'Total Entries', 'Show every entry below', $aF('', '', '')],
                ['green', 'bi-calendar-check-fill', $stats['today'], 'Today', "Show today's entries below", $aF('', $auditToday ?? '', $auditToday ?? '')],
                ['gold', 'bi-shield-exclamation', $stats['failed'], 'Failed / Denied (last 7 days)', 'Show failed logins and denied actions of the last 7 days below', $aF('failed_denied', $auditWeekAgo ?? '', '')],
            ];
        @endphp
        <div class="row g-3 mb-4">
            @foreach($aCards as [$color, $icon, $count, $label, $tip, $set])
            <div class="col-md-4 col-sm-6">
                <div class="stat-card stat-card-link" role="button" tabindex="0" title="{{ $tip }}" data-jump="{{ json_encode(["set" => $set, "to" => "#audit-list-card"]) }}">
                    <div class="stat-icon {{ $color }}"><i class="bi {{ $icon }}"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($count) }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Read-only here: only the Super Admin changes this (System Logs) --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding:10px 14px;background:var(--blue-pale);border-radius:10px;font-size:12.5px;color:var(--blue);">
            <i class="bi bi-clock-history"></i>
            <span>Automatic clearing: entries are kept for <strong>{{ $retention }}</strong>{{ $retention === 'Never (keep all)' ? '' : ', then removed' }}. Set by the Super Admin.</span>
        </div>

        <form method="GET" action="{{ url()->current() }}" id="audit-filter-form">
            <input type="hidden" name="section" value="audit">
            <div class="module-toolbar">
                <div class="toolbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="audit_search" placeholder="Search description or user..." value="{{ $auditSearch }}" oninput="debounceFormSubmit(this)">
                </div>
                <div class="toolbar-filter">
                    <select name="audit_type" onchange="this.form.requestSubmit()">
                        <option value="">All Events</option>
                        <option value="failed_denied" {{ $auditType === 'failed_denied' ? 'selected' : '' }}>Failed / Denied</option>
                        @foreach($eventTypes as $value => $label)
                            <option value="{{ $value }}" {{ $auditType === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="audit_role" onchange="this.form.requestSubmit()">
                        <option value="">All Roles</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ $auditRole === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="audit_from" value="{{ $auditFrom }}" title="From date" onchange="this.form.requestSubmit()"
                        style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                    <input type="date" name="audit_to" value="{{ $auditTo }}" title="To date" onchange="this.form.requestSubmit()"
                        style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                    @if($auditSearch || $auditType || $auditRole || $auditFrom || $auditTo)
                    <a href="{{ url()->current() }}?section=audit" class="btn-dash btn-secondary js-audit-clear" style="padding:8px 14px;">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                    @endif
                </div>
                <span class="toolbar-count">{{ number_format($logs->total()) }} entr{{ $logs->total() === 1 ? 'y' : 'ies' }}</span>
            </div>
        </form>

        <div class="content-card" id="audit-list-card">
            <div class="content-card-header">
                <h6><i class="bi bi-list-ul me-2" style="color:var(--blue);"></i>Activity</h6>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        @php
                            $badge = match(true) {
                                in_array($log->event_type, ['failed_login', 'denied', 'delete', 'error', 'payment_rejected']) => 'declined',
                                in_array($log->event_type, ['login', 'create'])                         => 'enrolled',
                                $log->event_type === 'logout'                                           => 'inactive',
                                $log->event_type === 'password_change'                                  => 'pending',
                                str_contains($log->event_type, 'payment')                               => 'completed',
                                default                                                                 => 'approved',
                            };
                        @endphp
                        <tr>
                            <td><span class="status-badge {{ $badge }}">{{ $eventTypes[$log->event_type] ?? ucwords(str_replace('_', ' ', $log->event_type)) }}</span></td>
                            <td style="max-width:420px;word-break:break-word;font-size:13px;">{{ $log->description }}</td>
                            <td>
                                @if($log->user_name)
                                    <div style="font-size:13px;font-weight:600;">{{ $log->user_name }}</div>
                                    <div style="font-size:11px;color:var(--muted);">{{ $roles[$log->user_role] ?? ucfirst($log->user_role ?? '') }}</div>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">System</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">{{ $log->ip_address ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                                <i class="bi bi-journal-x" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                                {{ ($auditSearch || $auditType || $auditRole || $auditFrom || $auditTo) ? 'No entries match the current filters.' : 'No activity recorded yet.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->total() > 0)
            <div class="p-3 border-top" style="border-color:var(--border);">
                {{ $logs->links() }}
                <div class="pagination-info">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} entries
                </div>
            </div>
            @endif
        </div>
