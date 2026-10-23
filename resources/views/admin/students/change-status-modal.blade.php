{{-- Shared by Admin and Super Admin Student Management (each page defines the functions its buttons call) --}}
    <div class="modal fade" id="changeStatusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border:0;border-radius:14px;overflow:hidden;">
                <div class="modal-header" style="background:var(--blue);color:#fff;border:0;padding:16px 20px;">
                    <h6 class="modal-title mb-0"><i class="bi bi-arrow-left-right me-2"></i>Change Enrollment Status</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <p style="font-size:13px;color:#555;margin-bottom:14px;">Student: <strong id="cs-student-name"></strong></p>
                    <label class="form-lbl">New Status</label>
                    <select id="cs-new-status" class="form-fld mb-3">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="enrolled">Enrolled</option>
                        <option value="declined">Declined</option>
                        <option value="completed">Completed</option>
                        <option value="dropped">Dropped</option>
                        <option value="ghost">Ghost</option>
                        <option value="transferred">Transferred</option>
                    </select>
                    <p style="font-size:11px;color:#999;margin-top:4px;">This updates the enrollment record immediately.</p>
                </div>
                <div class="modal-footer" style="border:0;padding:12px 20px;">
                    <button class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-dash btn-primary" onclick="confirmChangeStatus()">Save Status</button>
                </div>
            </div>
        </div>
    </div>
