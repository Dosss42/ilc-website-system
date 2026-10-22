        @php
            $totalSize = array_sum(array_column($backups, 'size_raw'));
            $totalSizeLabel = $totalSize >= 1048576 ? round($totalSize / 1048576, 2) . ' MB' : round($totalSize / 1024, 1) . ' KB';
        @endphp
        <div class="section-header">
            <div>
                <h1>Backup &amp; Restore</h1>
                <p>Create and download database backups, or restore the system from one.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-database-fill"></i></div>
                    <div>
                        <div class="stat-value">{{ count($backups) }}</div>
                        <div class="stat-label">Backups on the server ({{ $totalSizeLabel }})</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:{{ count($backups) ? '15px' : '' }};">{{ count($backups) ? $backups[0]['created'] : '—' }}</div>
                        <div class="stat-label">Latest Backup</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon {{ $driveConnected ? 'green' : 'gold' }}"><i class="bi bi-cloud-{{ $driveConnected ? 'check' : 'slash' }}-fill"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:15px;">{{ $driveConnected ? 'Connected' : 'Not connected' }}</div>
                        <div class="stat-label">Google Drive copy</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="content-card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                        <h6><i class="bi bi-archive me-2" style="color:var(--blue);"></i>Backup History</h6>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <button type="button" class="btn-dash btn-primary" style="padding:7px 16px;font-size:12px;" id="adminBtnCreateBackup" onclick="adminCreateBackup()">
                                <i class="bi bi-database-add"></i> Create Backup Now
                            </button>
                            <button type="button" class="btn-dash btn-secondary" style="padding:7px 16px;font-size:12px;" id="adminBtnRestoreFile" onclick="document.getElementById('adminRestoreFileInput').click()">
                                <i class="bi bi-upload"></i> Restore from File
                            </button>
                            <input type="file" id="adminRestoreFileInput" accept=".sql" style="display:none;" onchange="adminRestoreFromFile(this)">
                        </div>
                    </div>
                    <div style="padding:14px;">
                        @forelse($backups as $bk)
                        <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--border);border-radius:10px;margin-bottom:8px;">
                            <i class="bi bi-file-earmark-zip" style="font-size:20px;color:var(--blue);"></i>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:600;word-break:break-word;" title="{{ $bk['name'] }}">
                                    {{ preg_replace('/ \((before restore|uploaded)\)(?=\.sql$)/', '', $bk['label']) }}
                                    @if(str_ends_with($bk['name'], '_before-restore.sql'))
                                        <span class="status-badge pending" style="margin-left:4px;" title="Saved automatically just before a restore — restore this to undo it">Safety copy before restore</span>
                                    @elseif(str_ends_with($bk['name'], '_uploaded.sql'))
                                        <span class="status-badge approved" style="margin-left:4px;">Uploaded</span>
                                    @endif
                                </div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                                    <i class="bi bi-clock me-1"></i>{{ $bk['created'] }} &nbsp;·&nbsp; <i class="bi bi-hdd me-1"></i>{{ $bk['size'] }}
                                </div>
                            </div>
                            <a href="{{ route('admin.backup.download', $bk['name']) }}" class="action-btn view" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" class="action-btn edit js-admin-backup-restore" title="Restore Database" data-file="{{ $bk['name'] }}" data-label="{{ $bk['label'] }}">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                        @empty
                        <div style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="bi bi-database" style="font-size:36px;display:block;margin-bottom:10px;opacity:0.3;"></i>
                            <div style="font-weight:600;margin-bottom:4px;">No backups on the server yet</div>
                            <div style="font-size:12px;">Click <strong>Create Backup Now</strong>, or <strong>Restore from File</strong> with a backup you downloaded.</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card mb-3">
                    <div class="content-card-header"><h6><i class="bi bi-info-circle me-2" style="color:var(--blue);"></i>Before you restore</h6></div>
                    <div style="padding:14px;font-size:12.5px;line-height:1.6;">
                        <div style="background:#fff8ec;border:1px solid #fde68a;border-radius:8px;padding:10px;color:#92400e;margin-bottom:8px;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Restoring replaces <strong>all current data</strong> with the backup. The current data is saved first as a
                            <strong>safety copy</strong> — restore that copy to undo.
                        </div>
                        <div style="color:var(--muted);">
                            <i class="bi bi-cloud-arrow-down me-1"></i>
                            <strong>Download</strong> important backups to your computer: the online server's storage is cleared on every update.
                            Backups are also made automatically every day at 2:00 AM{{ $driveConnected ? ' and copied to Google Drive' : '' }}.
                            Deleting backups is done by the Super Admin.
                        </div>
                    </div>
                </div>
                <div class="content-card">
                    <div class="content-card-header"><h6><i class="bi bi-terminal me-2" style="color:var(--blue);"></i>Backup Log</h6></div>
                    <div id="adminBackupLog" style="padding:12px 14px;font-size:11px;font-family:monospace;max-height:170px;overflow-y:auto;color:var(--muted);">
                        Waiting for a backup operation…
                    </div>
                </div>
            </div>
        </div>
