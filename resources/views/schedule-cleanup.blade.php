<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Schedule Cleanup — ILC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background:#f5f7fa; padding:30px; }
        .cleanup-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:24px; }
        .schedule-table th { background:#f8fafc; font-size:12px; text-transform:uppercase; letter-spacing:.5px; }
        .schedule-table td { font-size:13px; vertical-align:middle; }
        .badge-active { background:#d1fae5; color:#065f46; }
        .badge-inactive { background:#fee2e2; color:#991b1b; }
        .search-box { max-width:400px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="bi bi-trash3 me-2"></i>Schedule Cleanup</h4>
                <p class="text-muted mb-0">Find and remove any schedule record directly from the database.</p>
            </div>
            <a href="/admin/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a>
        </div>

        <div class="cleanup-card mb-4">
            <div class="d-flex gap-3 align-items-center mb-3">
                <div class="search-box flex-grow-1">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search by subject, room, teacher, day...">
                    </div>
                </div>
                <div class="text-muted" style="white-space:nowrap;"><span id="visibleCount">{{ $schedules->count() }}</span> record(s)</div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover schedule-table mb-0" id="scheduleTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th style="width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $sched)
                            <tr data-search="{{ strtolower(($sched->subject->name ?? '') . ' ' . ($sched->room ?? '') . ' ' . ($sched->day_of_week ?? '') . ' ' . ($sched->teacher->name ?? '') . ' ' . ($sched->section->name ?? '')) }}">
                                <td><code>#{{ $sched->id }}</code></td>
                                <td>
                                    <div class="fw-semibold">{{ $sched->section->grade_level ?? '—' }}</div>
                                    <small class="text-muted">{{ $sched->section->name ?? '—' }}</small>
                                </td>
                                <td><span class="fw-semibold">{{ $sched->subject->name ?? '—' }}</span></td>
                                <td>{{ $sched->teacher->name ?? '—' }}</td>
                                <td>{{ $sched->day_of_week ?? '—' }}</td>
                                <td style="white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($sched->start_time)->format('g:i A') }}<br>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($sched->end_time)->format('g:i A') }}</span>
                                </td>
                                <td>{{ $sched->room ?? '—' }}</td>
                                <td>Term {{ $sched->term ?? '—' }}</td>
                                <td>
                                    @if($sched->is_active)
                                        <span class="badge badge-active">Active</span>
                                    @else
                                        <span class="badge badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="/admin/schedules/{{ $sched->id }}" method="POST" class="d-inline" onsubmit="return confirmDeleteSchedule(this, {{ $sched->id }});">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">No schedules found in the database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Live search
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#scheduleTable tbody tr[data-search]');
            let visible = 0;
            rows.forEach(row => {
                const match = !query || row.dataset.search.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('visibleCount').textContent = visible;
        });

        function showConfirm(message, onConfirm, opts) {
            opts = opts || {};
            var title = opts.title || 'Please Confirm';
            var confirmLabel = opts.confirmLabel || 'Yes, Continue';
            var danger = !!opts.danger;
            var iconBg = danger ? '#fdecea' : '#fff8ec';
            var iconColor = danger ? '#c0392b' : '#b45309';
            var okBg = danger ? '#dc2626' : '#0d6efd';

            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';

            var box = document.createElement('div');
            box.style.cssText = 'background:#fff;border-radius:16px;padding:28px 26px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);text-align:center;';
            // title/message/confirmLabel are set via textContent below, not
            // interpolated into this HTML string — all three can carry
            // server/user-controlled text.
            box.innerHTML =
                '<div style="width:52px;height:52px;border-radius:50%;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">' +
                    '<i class="bi ' + (danger ? 'bi-exclamation-triangle-fill' : 'bi-question-circle-fill') + '" style="font-size:24px;color:' + iconColor + ';"></i>' +
                '</div>' +
                '<div id="ilc-confirm-title" style="font-size:15px;font-weight:700;color:#1a3a6c;margin-bottom:6px;"></div>' +
                '<div id="ilc-confirm-message" style="font-size:13px;color:#64748b;line-height:1.5;margin-bottom:20px;"></div>' +
                '<div style="display:flex;gap:10px;">' +
                    '<button type="button" id="ilc-confirm-cancel" style="flex:1;padding:10px;border-radius:9px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-weight:600;font-size:13px;cursor:pointer;">Cancel</button>' +
                    '<button type="button" id="ilc-confirm-ok" style="flex:1;padding:10px;border-radius:9px;border:none;background:' + okBg + ';color:#fff;font-weight:600;font-size:13px;cursor:pointer;"></button>' +
                '</div>';
            box.querySelector('#ilc-confirm-title').textContent = title;
            box.querySelector('#ilc-confirm-message').textContent = message;
            box.querySelector('#ilc-confirm-ok').textContent = confirmLabel;

            overlay.appendChild(box);
            document.body.appendChild(overlay);

            function close() {
                overlay.remove();
                document.removeEventListener('keydown', onKey);
            }
            function onKey(e) { if (e.key === 'Escape') close(); }
            document.addEventListener('keydown', onKey);

            box.querySelector('#ilc-confirm-cancel').onclick = close;
            box.querySelector('#ilc-confirm-ok').onclick = function () { close(); onConfirm(); };
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
        }

        function confirmDeleteSchedule(form, id) {
            showConfirm('Delete schedule #' + id + ' permanently? This cannot be undone.', function () { form.submit(); },
                { title: 'Delete Schedule', confirmLabel: 'Delete', danger: true });
            return false;
        }
    </script>
</body>
</html>
