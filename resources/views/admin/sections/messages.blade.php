    <div id="section-messages" class="dash-section" style="display:none;">
        <div class="section-header">
            <div>
                <h1><i class="bi bi-envelope-fill" style="color:var(--gold);"></i> Contact Messages</h1>
                <p>Messages sent from the website's Contact page. Opening a message marks it as read.</p>
            </div>
        </div>

        {{-- Counts — independent COUNT queries (EnrollmentController@adminIndex), not derived from
             $contactMessages, which is capped at 50 rows. They update live when a message is
             opened, marked or deleted (msgRecount()). --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;" class="msg-stats">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-envelope-fill"></i></div>
                <div><div class="stat-value" id="msgStatTotal">{{ $totalMessagesCount ?? 0 }}</div><div class="stat-label">Total Messages</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="bi bi-envelope-exclamation-fill"></i></div>
                <div><div class="stat-value" id="msgStatUnread">{{ $unreadMessagesCount ?? 0 }}</div><div class="stat-label">Unread</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="bi bi-envelope-open-fill"></i></div>
                <div><div class="stat-value" id="msgStatRead">{{ $readMessagesCount ?? 0 }}</div><div class="stat-label">Read</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-reply-fill"></i></div>
                <div><div class="stat-value" id="msgStatReplied">{{ $repliedMessagesCount ?? 0 }}</div><div class="stat-label">Replied</div></div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header" style="flex-wrap:wrap;gap:10px;">
                <h6><i class="bi bi-inbox-fill me-2" style="color:var(--gold);"></i>Inbox</h6>
                <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                    @foreach(['' => 'All', 'unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied'] as $mk => $ml)
                        <button type="button" class="msg-chip {{ $mk === '' ? 'active' : '' }}" data-status="{{ $mk }}" onclick="msgFilter(this.dataset.status)">{{ $ml }}</button>
                    @endforeach
                    <input type="text" id="msgSearch" class="form-fld" placeholder="Search name, email or subject…" oninput="msgFilter(null, true)" style="width:240px;padding:6px 12px;font-size:12.5px;">
                </div>
            </div>

            @if(($contactMessages ?? collect())->isEmpty())
                <div style="text-align:center;padding:60px 20px;color:var(--muted);">
                    <i class="bi bi-inbox" style="font-size:48px;display:block;margin-bottom:12px;opacity:.35;"></i>
                    <div style="font-size:15px;font-weight:600;margin-bottom:4px;">No messages yet</div>
                    <div style="font-size:12px;">Messages sent from the website's Contact page appear here.</div>
                </div>
            @else
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr><th>Sender</th><th>Subject</th><th style="width:130px;">Received</th><th style="width:100px;">Status</th><th style="width:130px;">Actions</th></tr>
                    </thead>
                    <tbody id="msgTableBody">
                        @foreach($contactMessages ?? [] as $msg)
                        @php $isUnread = $msg->status === 'unread'; @endphp
                        <tr id="msg-row-{{ $msg->id }}" class="msg-row {{ $isUnread ? 'msg-unread' : '' }}" data-status="{{ $msg->status }}"
                            data-search="{{ strtolower($msg->name . ' ' . $msg->email . ' ' . $msg->subject) }}" style="cursor:pointer;"
                            onclick="openMsgModal({{ $msg->id }}, {{ json_encode($msg->name) }}, {{ json_encode($msg->email) }}, {{ json_encode($msg->phone ?? '—') }}, {{ json_encode($msg->subject) }}, {{ json_encode($msg->message) }}, {{ json_encode($msg->created_at->format('M d, Y h:i A')) }}, this.dataset.status)">
                            <td>
                                <div class="msg-name" style="color:var(--text);">@if($isUnread)<span class="msg-dot" title="Unread"></span>@endif{{ $msg->name }}</div>
                                <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $msg->email }}</div>
                            </td>
                            <td>
                                <div class="msg-subject" style="color:var(--text);">{{ $msg->subject }}</div>
                                <div style="font-size:11.5px;color:var(--muted);margin-top:2px;max-width:420px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($msg->message, 90) }}</div>
                            </td>
                            <td style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $msg->created_at->format('M d, Y') }}<br>{{ $msg->created_at->format('h:i A') }}</td>
                            <td class="msg-status-cell"></td>
                            <td class="msg-actions-cell" onclick="event.stopPropagation();"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="msgNoMatch" style="display:none;text-align:center;padding:30px;color:var(--muted);font-size:13px;">No messages match.</div>
            <div id="msgPagination" style="display:none;"></div>
            @if(($totalMessagesCount ?? 0) > ($contactMessages ?? collect())->count())
                <div style="padding:10px 18px;font-size:11.5px;color:var(--muted);border-top:1px solid var(--border);">Showing the latest {{ ($contactMessages ?? collect())->count() }} of {{ $totalMessagesCount }} messages.</div>
            @endif
            @endif
        </div>
    </div>{{-- /section-messages --}}

    <style>
        @media (max-width: 992px) { #section-messages .msg-stats { grid-template-columns: repeat(2, 1fr) !important; } }
        .msg-chip { padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1.5px solid #ddd; background: #f8f9fa; color: #555; cursor: pointer; }
        .msg-chip.active { border-color: var(--blue); background: var(--blue); color: #fff; }
        .msg-row.msg-unread td { background: #fffbeb; }
        .msg-row.msg-unread .msg-name, .msg-row.msg-unread .msg-subject { font-weight: 700; }
        .msg-row .msg-name, .msg-row .msg-subject { font-weight: 500; }
        .msg-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--gold); margin-right: 6px; vertical-align: middle; }
    </style>
