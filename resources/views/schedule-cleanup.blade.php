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
                                    <form action="/admin/schedules/{{ $sched->id }}" method="POST" class="d-inline" onsubmit="return confirm('Delete schedule #{{ $sched->id }} permanently?');">
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
    </script>
</body>
</html>
