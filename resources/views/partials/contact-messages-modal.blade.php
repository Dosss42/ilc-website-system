{{-- Contact message pop-up — shared by Admin and Super Admin (filled by openMsgModal() in public/js/contact-messages.js) --}}
<div class="modal fade" id="msgViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);border:none;padding:16px 22px;">
                <div style="flex:1;min-width:0;">
                    <h5 id="msgModalSubject" class="modal-title" style="color:#fff;font-weight:700;font-size:15px;margin:0;"></h5>
                    <div id="msgModalMeta" style="font-size:11px;color:rgba(255,255,255,.65);margin-top:3px;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                    <div style="background:#f8faff;border-radius:10px;padding:12px 14px;">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">From</div>
                        <div id="msgModalName" style="font-size:13px;font-weight:700;color:#1e293b;"></div>
                        <div id="msgModalEmail" style="font-size:12px;color:#64748b;margin-top:2px;"></div>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:12px 14px;">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Contact & Date</div>
                        <div id="msgModalPhone" style="font-size:13px;font-weight:600;color:#1e293b;"></div>
                        <div id="msgModalDate"  style="font-size:12px;color:#64748b;margin-top:2px;"></div>
                    </div>
                </div>
                <div style="background:#f8faff;border-radius:10px;padding:16px 18px;">
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Message</div>
                    <div id="msgModalBody" style="font-size:13px;color:#374151;line-height:1.7;white-space:pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e8edf5;padding:14px 22px;gap:8px;justify-content:space-between;">
                <div id="msgModalStatusBtns" style="display:flex;gap:8px;"></div>
                <div style="display:flex;gap:8px;">
                    <a id="msgModalReplyLink" href="#"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1a3a6c;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-envelope-arrow-up-fill"></i> Reply via Email
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
