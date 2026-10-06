    <div id="section-messages" class="dash-section" style="display:none;">
        <div class="section-header-bar">
            <h4><i class="bi bi-envelope-fill me-2" style="color:var(--ilc-gold);"></i>Contact Messages</h4>
            <small class="text-muted">Messages submitted through the website contact form</small>
        </div>

        {{-- Stats row --}}
        @php
            $msgUnread  = ($contactMessages ?? collect())->where('status','unread')->count();
            $msgRead    = ($contactMessages ?? collect())->where('status','read')->count();
            $msgReplied = ($contactMessages ?? collect())->where('status','replied')->count();
            $msgTotal   = ($contactMessages ?? collect())->count();
        @endphp
        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;">
            <div style="flex:1;min-width:120px;background:#fff;border:1px solid #e8edf5;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#1a3a6c;">{{ $msgTotal }}</div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-top:2px;">Total</div>
            </div>
            <div style="flex:1;min-width:120px;background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#92400e;">{{ $msgUnread }}</div>
                <div style="font-size:11px;color:#92400e;font-weight:600;text-transform:uppercase;margin-top:2px;">Unread</div>
            </div>
            <div style="flex:1;min-width:120px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#1d4ed8;">{{ $msgRead }}</div>
                <div style="font-size:11px;color:#1d4ed8;font-weight:600;text-transform:uppercase;margin-top:2px;">Read</div>
            </div>
            <div style="flex:1;min-width:120px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#15803d;">{{ $msgReplied }}</div>
                <div style="font-size:11px;color:#15803d;font-weight:600;text-transform:uppercase;margin-top:2px;">Replied</div>
            </div>
        </div>

        {{-- Messages Table --}}
        <div style="background:#fff;border:1px solid #e8edf5;border-radius:14px;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-weight:700;font-size:14px;color:#1a2a4a;">Inbox</span>
            </div>
            @if(($contactMessages ?? collect())->isEmpty())
                <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
                    <i class="bi bi-inbox" style="font-size:48px;display:block;margin-bottom:12px;opacity:.4;"></i>
                    <div style="font-size:14px;">No messages yet.</div>
                </div>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8faff;">
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:140px;">Sender</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;">Subject</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:110px;">Date</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:90px;">Status</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contactMessages ?? [] as $msg)
                        @php
                            $isUnread = $msg->status === 'unread';
                            $statusConfig = [
                                'unread'  => ['#fef3c7','#92400e','Unread'],
                                'read'    => ['#eff6ff','#1d4ed8','Read'],
                                'replied' => ['#f0fdf4','#15803d','Replied'],
                            ][$msg->status] ?? ['#f3f4f6','#6b7280','—'];
                        @endphp
                        <tr id="msg-row-{{ $msg->id }}"
                            style="border-bottom:1px solid #f0f4f8;background:{{ $isUnread ? '#fffbeb' : '#fff' }};cursor:pointer;"
                            onclick="openMsgModal({{ $msg->id }}, {{ json_encode($msg->name) }}, {{ json_encode($msg->email) }}, {{ json_encode($msg->phone ?? '—') }}, {{ json_encode($msg->subject) }}, {{ json_encode($msg->message) }}, {{ json_encode($msg->created_at->format('M d, Y h:i A')) }}, {{ json_encode($msg->status) }})">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:{{ $isUnread ? '700' : '500' }};color:#1e293b;">{{ $msg->name }}</div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:1px;">{{ $msg->email }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:{{ $isUnread ? '700' : '400' }};color:#1e293b;">{{ $msg->subject }}</div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;">{{ Str::limit($msg->message, 70) }}</div>
                            </td>
                            <td style="padding:12px 16px;font-size:12px;color:#64748b;white-space:nowrap;">{{ $msg->created_at->format('M d, Y') }}<br>{{ $msg->created_at->format('h:i A') }}</td>
                            <td style="padding:12px 16px;">
                                <span style="background:{{ $statusConfig[0] }};color:{{ $statusConfig[1] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $statusConfig[2] }}</span>
                            </td>
                            <td style="padding:12px 16px;" onclick="event.stopPropagation();">
                                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                    @if($msg->status !== 'read')
                                    <button onclick="updateMsgStatus({{ $msg->id }}, 'read')" title="Mark as Read"
                                        style="padding:4px 9px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @endif
                                    @if($msg->status !== 'replied')
                                    <button onclick="updateMsgStatus({{ $msg->id }}, 'replied')" title="Mark as Replied"
                                        style="padding:4px 9px;background:#f0fdf4;color:#15803d;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-reply-fill"></i>
                                    </button>
                                    @endif
                                    <button onclick="deleteMsg({{ $msg->id }})" title="Delete"
                                        style="padding:4px 9px;background:#fef2f2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
