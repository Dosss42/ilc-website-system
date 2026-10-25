/**
 * Contact Messages inbox — shared by Admin and Super Admin, so both work exactly the same.
 * Page: resources/views/admin/sections/messages.blade.php · pop-up: resources/views/partials/contact-messages-modal.blade.php
 * Server: PATCH /admin/messages/{id}/status, DELETE /admin/messages/{id} (Admin and Super Admin).
 *
 * Opening a message marks it Read. Counts, the sidebar badge (#nav-messages .sidebar-badge) and the
 * top-bar envelope badge (#topbarMsgBadge) follow every change. Chips + search filter the rows,
 * 10 per page. Toasts and the delete confirmation use whichever portal's own helpers are on the page.
 */
var _currentMsgId = null;

function msgCsrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : (typeof csrfToken !== 'undefined' ? csrfToken : '');
}
function msgToast(message, type) {
    if (typeof showAdminToast === 'function') showAdminToast(message, type);   // Admin
    else if (typeof saToast === 'function') saToast(type, message);            // Super Admin
}
function msgConfirmDelete(message, onConfirm) {
    if (typeof showDeleteConfirm === 'function') showDeleteConfirm(message, onConfirm);   // Admin
    else if (typeof saConfirm === 'function') saConfirm({                                 // Super Admin
        title: 'Delete Message', msg: message, icon: 'bi-trash3-fill',
        gradient: 'linear-gradient(135deg,#b91c1c,#dc2626)', btnLabel: 'Delete', btnColor: '#dc2626', onConfirm: onConfirm,
    });
    else if (window.confirm(message)) onConfirm();
}

function openMsgModal(id, name, email, phone, subject, message, date, status) {
    _currentMsgId = id;
    document.getElementById('msgModalSubject').textContent = subject;
    document.getElementById('msgModalMeta').textContent    = date;
    document.getElementById('msgModalName').textContent    = name;
    document.getElementById('msgModalEmail').textContent   = email;
    document.getElementById('msgModalPhone').textContent   = phone || '—';
    document.getElementById('msgModalDate').textContent    = date;
    document.getElementById('msgModalBody').textContent    = message;
    document.getElementById('msgModalReplyLink').href      = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);

    // Auto-mark as read when opened (so the modal offers only what is left to do)
    if (status === 'unread') { updateMsgStatus(id, 'read', false); status = 'read'; }

    // Status action buttons inside modal
    var btns = '';
    if (status !== 'read')    btns += '<button onclick="updateMsgStatus(' + id + ',\'read\',true)"    style="padding:8px 14px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-eye"></i> Mark as Read</button>';
    if (status !== 'replied') btns += '<button onclick="updateMsgStatus(' + id + ',\'replied\',true)" style="padding:8px 14px;background:#f0fdf4;color:#15803d;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-reply-fill"></i> Mark as Replied</button>';
    document.getElementById('msgModalStatusBtns').innerHTML = btns;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('msgViewModal')).show();
}

// Status badge + action buttons of one inbox row, from its data-status
var MSG_BADGE = { unread: ['pending', 'Unread'], read: ['approved', 'Read'], replied: ['enrolled', 'Replied'] };
function msgRenderRow(row) {
    var id = row.id.replace('msg-row-', ''), st = row.dataset.status, b = MSG_BADGE[st] || ['inactive', st];
    row.classList.toggle('msg-unread', st === 'unread');
    var dot = row.querySelector('.msg-dot');
    if (dot && st !== 'unread') dot.remove();
    row.querySelector('.msg-status-cell').innerHTML = '<span class="status-badge ' + b[0] + '">' + b[1] + '</span>';
    row.querySelector('.msg-actions-cell').innerHTML = '<div style="display:flex;gap:5px;">'
        + (st !== 'read' ? '<button type="button" class="action-btn edit" title="Mark as Read" onclick="updateMsgStatus(' + id + ', \'read\')"><i class="bi bi-envelope-open"></i></button>' : '')
        + (st !== 'replied' ? '<button type="button" class="action-btn view" title="Mark as Replied" onclick="updateMsgStatus(' + id + ', \'replied\')"><i class="bi bi-reply-fill"></i></button>' : '')
        + '<button type="button" class="action-btn delete" title="Delete" onclick="deleteMsg(' + id + ')"><i class="bi bi-trash3"></i></button></div>';
}

// Counts, sidebar badge and the top-bar envelope follow every change (no reload needed).
// Shown rows are the latest 200; anything older keeps its share of the server's counts.
var _msgBase = null;
function msgRecount() {
    var rows = document.querySelectorAll('#msgTableBody .msg-row');
    var c = { unread: 0, read: 0, replied: 0 };
    rows.forEach(function (r) { if (c[r.dataset.status] !== undefined) c[r.dataset.status]++; });
    if (!_msgBase) {
        var s = function (id) { var el = document.getElementById(id); return el ? (parseInt(el.textContent) || 0) : 0; };
        _msgBase = { unread: s('msgStatUnread') - c.unread, read: s('msgStatRead') - c.read, replied: s('msgStatReplied') - c.replied };
    }
    var unread = _msgBase.unread + c.unread, read = _msgBase.read + c.read, replied = _msgBase.replied + c.replied;
    var set = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
    set('msgStatUnread', unread); set('msgStatRead', read); set('msgStatReplied', replied); set('msgStatTotal', unread + read + replied);
    ['#nav-messages .sidebar-badge', '#topbarMsgBadge'].forEach(function (sel) {
        var el = document.querySelector(sel);
        if (el) { el.textContent = unread; el.style.display = unread ? '' : 'none'; }
    });
}

// Chips + search pick the matching rows, then they are shown 10 per page.
// A chip or a new search goes back to page 1; marking or deleting keeps the current page.
var _msgStatusFilter = '', _msgPage = 1, MSG_PER_PAGE = 10;
function msgFilter(status, resetPage) {
    if (status !== undefined && status !== null) {
        _msgStatusFilter = status;
        resetPage = true;
        document.querySelectorAll('.msg-chip').forEach(function (b) { b.classList.toggle('active', b.dataset.status === status); });
    }
    if (resetPage) _msgPage = 1;
    var q = ((document.getElementById('msgSearch') || {}).value || '').trim().toLowerCase();
    var rows = Array.prototype.slice.call(document.querySelectorAll('#msgTableBody .msg-row'));
    var matched = rows.filter(function (r) {
        return (!_msgStatusFilter || r.dataset.status === _msgStatusFilter) && (!q || r.dataset.search.indexOf(q) > -1);
    });
    var last = Math.max(1, Math.ceil(matched.length / MSG_PER_PAGE));
    if (_msgPage > last) _msgPage = last;
    var start = (_msgPage - 1) * MSG_PER_PAGE;
    rows.forEach(function (r) { r.style.display = 'none'; });
    matched.slice(start, start + MSG_PER_PAGE).forEach(function (r) { r.style.display = ''; });
    var none = document.getElementById('msgNoMatch');
    if (none) {
        none.style.display = matched.length ? 'none' : '';
        none.textContent = rows.length ? 'No messages match.' : 'No messages left in the inbox.';
    }
    msgRenderPagination(matched.length);
}

function msgGoToPage(p) {
    _msgPage = p;
    msgFilter();
    document.getElementById('section-messages').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Same pagination bar as the other lists (Previous / page numbers / Next + "Showing X to Y of Z")
function msgRenderPagination(total) {
    var box = document.getElementById('msgPagination');
    if (!box) return;
    if (!total) { box.style.display = 'none'; box.innerHTML = ''; return; }
    var last = Math.max(1, Math.ceil(total / MSG_PER_PAGE));
    var from = (_msgPage - 1) * MSG_PER_PAGE + 1, to = Math.min(total, _msgPage * MSG_PER_PAGE);
    var link = function (p, label) { return '<li class="page-item"><a href="#" class="page-link" onclick="msgGoToPage(' + p + '); return false;">' + label + '</a></li>'; };
    var html = '<div class="p-3"><nav><ul class="pagination">';
    html += _msgPage > 1 ? link(_msgPage - 1, 'Previous') : '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    (typeof pageWindow === 'function' ? pageWindow(_msgPage, last) : Array.from({ length: last }, function (_, k) { return k + 1; })).forEach(function (i) {
        if (i === '…') html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        else if (i === _msgPage) html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
        else html += link(i, i);
    });
    html += _msgPage < last ? link(_msgPage + 1, 'Next') : '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    html += '</ul></nav><div class="pagination-info">Showing ' + from + ' to ' + to + ' of ' + total + ' message' + (total === 1 ? '' : 's') + '</div></div>';
    box.innerHTML = html;
    box.style.display = '';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#msgTableBody .msg-row').forEach(msgRenderRow);
    msgRecount();
    msgFilter(null, true);
});

function updateMsgStatus(id, status, closeModal) {
    fetch('/admin/messages/' + id + '/status', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': msgCsrf(), 'Accept': 'application/json' },
        body: JSON.stringify({ status: status })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) return;
        var row = document.getElementById('msg-row-' + id);
        if (row) { row.dataset.status = status; msgRenderRow(row); }
        msgRecount();
        msgFilter();
        if (closeModal) {
            var m = bootstrap.Modal.getInstance(document.getElementById('msgViewModal'));
            if (m) m.hide();
        }
    })
    .catch(function () { msgToast('Could not update the message. Please try again.', 'error'); });
}

function deleteMsg(id) {
    msgConfirmDelete('Delete this message? This cannot be undone.', function () {
        fetch('/admin/messages/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': msgCsrf(), 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                var row = document.getElementById('msg-row-' + id);
                if (row) row.remove();
                msgRecount();
                msgFilter();
                msgToast('Message deleted.', 'success');
            }
        })
        .catch(function () { msgToast('Could not delete the message. Please try again.', 'error'); });
    });
}
