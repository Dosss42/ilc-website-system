<!-- Enrollment View Modal -->
<div class="modal fade" id="enrollmentViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,.22);">

            
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#1d5ebd 60%,#2471a3 100%);padding:22px 26px;position:relative;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-badge-fill" style="font-size:24px;color:#fff;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <h5 style="color:#fff;font-weight:800;font-size:18px;margin:0;line-height:1.2;">Enrollment Application</h5>
                            <span id="modal-type-badge" style="display:none;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);"></span>
                        </div>
                        <div style="margin-top:4px;font-size:12px;color:rgba(255,255,255,.7);">
                            Ref: <span id="modal-reference-number" style="font-weight:600;color:rgba(255,255,255,.9);"></span>
                            &nbsp;·&nbsp; S.Y. <span id="modal-school-year" style="font-weight:600;color:rgba(255,255,255,.9);"></span>
                        </div>
                    </div>
                    <div id="modal-status-pill" style="flex-shrink:0;"></div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:14px;right:16px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            
            <div style="background:#f8faff;border-bottom:1.5px solid #e2e8f0;padding:0 24px;">
                <div style="display:flex;gap:0;">
                    <button type="button" onclick="evmTab('details')" id="evm-tab-details"
                        style="display:flex;align-items:center;gap:7px;padding:13px 18px;border:none;border-bottom:3px solid #1a3a6c;background:transparent;color:#1a3a6c;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:inherit;">
                        <i class="bi bi-person-lines-fill"></i> Application Details
                    </button>
                    <button type="button" onclick="evmTab('documents')" id="evm-tab-documents"
                        style="display:flex;align-items:center;gap:7px;padding:13px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;">
                        <i class="bi bi-folder2-open"></i> Documents
                        <span id="evm-doc-count" style="display:none;background:#1a3a6c;color:#fff;font-size:10px;font-weight:700;border-radius:10px;padding:1px 7px;"></span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0" style="background:#f4f7fb;">

                
                <div id="evm-pane-details" style="padding:24px;">
                    <div class="row g-3">

                        
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d5ebd,#2471a3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-person-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Student Information</div>
                                        <div style="font-size:11px;color:#94a3b8;">Personal details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Full Name</label><div id="modal-student-name" class="evm-val"></div></div>
                                    <div class="evm-field"><label>Email Address</label><div id="modal-student-email" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Grade Level</label><div id="modal-grade-level" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Student Type</label><div id="modal-student-type" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Phone</label><div id="modal-student-phone" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Date of Birth</label><div id="modal-student-birthdate" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0891b2,#0e7490);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-shield-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Guardian Information</div>
                                        <div style="font-size:11px;color:#94a3b8;">Parent / Guardian details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Full Name</label><div id="modal-guardian-name" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Relationship</label><div id="modal-relationship" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Occupation</label><div id="modal-guardian-occupation" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Email</label><div id="modal-guardian-email" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Phone</label><div id="modal-guardian-phone" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#16a34a,#15803d);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-geo-alt-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Address</div>
                                        <div style="font-size:11px;color:#94a3b8;">Residential details</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:grid;gap:12px;">
                                    <div class="evm-field"><label>Street Address</label><div id="modal-street-address" class="evm-val"></div></div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Barangay</label><div id="modal-barangay" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>City / Municipality</label><div id="modal-city" class="evm-val"></div></div></div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="evm-field"><label>Province</label><div id="modal-province" class="evm-val"></div></div></div>
                                        <div class="col-6"><div class="evm-field"><label>Zip Code</label><div id="modal-zip-code" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-lg-6">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;height:100%;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#d97706,#b45309);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-clock-history" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Application Timeline</div>
                                        <div style="font-size:11px;color:#94a3b8;">Important dates &amp; status</div>
                                    </div>
                                </div>
                                <div style="padding:18px;display:flex;flex-direction:column;gap:10px;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #1a3a6c;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Application Date</span>
                                        <span id="modal-application-date" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #0891b2;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Last Updated</span>
                                        <span id="modal-updated-at" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8faff;border-radius:8px;border-left:4px solid #d97706;">
                                        <span style="font-size:12px;font-weight:600;color:#475569;">Processing Status</span>
                                        <span id="modal-processing-status" style="font-size:12px;font-weight:700;color:#1a2a4a;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-12" id="section-assignment-card" style="display:none;">
                            <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;">
                                <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="bi bi-people-fill" style="color:#fff;font-size:16px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Section Assignment</div>
                                            <div style="font-size:11px;color:#94a3b8;">Current assigned section</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-change-section" onclick="openChangeSectionModal()">
                                        <i class="bi bi-pencil-square"></i> Change Section
                                    </button>
                                </div>
                                <div style="padding:18px;">
                                    <div class="row g-3">
                                        <div class="col-md-6"><div class="evm-field"><label>Current Section</label><div id="modal-current-section" class="evm-val">Not assigned</div></div></div>
                                        <div class="col-md-6"><div class="evm-field"><label>Grade Level</label><div id="modal-section-grade-level" class="evm-val"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                
                <div id="evm-pane-documents" style="display:none;padding:24px;">
                    <div style="background:#fff;border-radius:14px;border:1px solid #e8edf5;overflow:hidden;">
                        <div style="padding:14px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-folder2-open" style="color:#fff;font-size:16px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13px;color:#1a2a4a;">Submitted Documents</div>
                                <div style="font-size:11px;color:#94a3b8;">Review and approve/reject documents uploaded by the student</div>
                            </div>
                        </div>
                        <div style="padding:18px;">
                            <div id="evm-docs-loading" style="text-align:center;padding:32px;color:#94a3b8;">
                                <div class="spinner-border spinner-border-sm me-2" style="color:#1a3a6c;"></div>
                                Loading documents…
                            </div>
                            <div id="evm-docs-content" style="display:none;"></div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div style="padding:16px 24px;border-top:1.5px solid #e8edf5;background:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <small style="color:#94a3b8;font-size:12px;"><i class="bi bi-info-circle me-1"></i>Review all information before taking action</small>
                <div id="modal-action-buttons" style="display:flex;gap:8px;flex-wrap:wrap;"></div>
            </div>

        </div>
    </div>
</div>

<style>
.evm-field { display:flex;flex-direction:column;gap:4px; }
.evm-field label { font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px; }
.evm-val { font-size:13px;font-weight:500;color:#1e293b;background:#f8faff;border:1px solid #e8edf5;border-radius:7px;padding:7px 11px;min-height:34px;display:flex;align-items:center; }
</style>

<!-- Change Section Modal -->
<div class="modal fade" id="changeSectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#0056b3);color:#fff;border:none;">
                <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Change Section Assignment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select New Section</label>
                    <select id="new-section-select" class="form-select">
                        <option value="">-- Select Section --</option>
                    </select>
                </div>
                <div class="alert alert-info" style="font-size:.875rem;">
                    <i class="bi bi-info-circle me-2"></i>
                    ILC has only one section per grade level. If you need multiple sections, create them first in the Section Management module.
                </div>
                <input type="hidden" id="change-section-enrollment-id">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSectionChange()"><i class="bi bi-check"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\admin\enrollments\view-modal.blade.php ENDPATH**/ ?>