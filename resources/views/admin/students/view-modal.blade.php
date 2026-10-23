{{-- Shared by Admin and Super Admin Student Management (each page defines the functions its buttons call) --}}
<div class="modal fade" id="studentViewModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden;">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2a6dd6); color:#fff; border:0; padding:20px 24px;">

                <div>

                    <h5 class="modal-title" style="font-weight:700; margin:0;" id="sv-name">Student Name</h5>

                    <small id="sv-ref" style="opacity:0.8;"></small>

                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body" style="padding:24px;">

                <div class="row g-3">

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-person me-2"></i>Personal Info</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>LRN:</strong> <span id="sv-lrn"></span></div>

                                <div><strong>Email:</strong> <span id="sv-email"></span></div>

                                <div><strong>Gender:</strong> <span id="sv-gender"></span></div>

                                <div><strong>Birthdate:</strong> <span id="sv-birthdate"></span></div>

                                <div><strong>Age:</strong> <span id="sv-age"></span></div>

                                <div><strong>Place of Birth:</strong> <span id="sv-pob"></span></div>

                                <div><strong>Student Type:</strong> <span id="sv-type"></span></div>

                                <div><strong>Joined:</strong> <span id="sv-joined"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-mortarboard me-2"></i>Enrollment</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Grade:</strong> <span id="sv-grade"></span></div>

                                <div><strong>Section:</strong> <span id="sv-section"></span></div>

                                <div><strong>Status:</strong> <span id="sv-status"></span></div>

                                <div><strong>Payment:</strong> <span id="sv-payment"></span></div>

                                <div><strong>Amount Paid:</strong> <span id="sv-amount"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-people me-2"></i>Guardian</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Name:</strong> <span id="sv-guardian"></span></div>

                                <div><strong>Relationship:</strong> <span id="sv-relation"></span></div>

                                <div><strong>Phone:</strong> <span id="sv-gphone"></span></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-geo-alt me-2"></i>Address</h6>

                            <div style="font-size:13px; line-height:2;">

                                <div><strong>Street:</strong> <span id="sv-street"></span></div>

                                <div><strong>Barangay:</strong> <span id="sv-brgy"></span></div>

                                <div><strong>City:</strong> <span id="sv-city"></span></div>

                                <div><strong>Province:</strong> <span id="sv-province"></span></div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Documents Section --}}
                <div class="row g-3 mt-1">

                    <div class="col-md-12">

                        <div style="background:#f8f9fa; border-radius:10px; padding:16px;">

                            <h6 style="font-size:13px; font-weight:700; color:#1a3a6c; margin-bottom:12px;"><i class="bi bi-folder-check me-2"></i>Submitted Documents</h6>

                            <div id="sv-docs-loading" style="text-align:center;padding:20px;color:#999;">
                                <div class="spinner-border spinner-border-sm me-2"></div> Loading documents...
                            </div>

                            <div id="sv-docs-content" style="display:none;"></div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer" style="border:0; padding:16px 24px;">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <a id="sv-sf10-btn" href="#" target="_blank"
                   class="btn btn-success"
                   style="display:none; background:#1a6b2d; border-color:#1a6b2d;">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download SF10
                </a>

                <button type="button" class="btn btn-primary" id="sv-reenroll-btn" onclick="reEnrollStudent()" style="display:none;"><i class="bi bi-arrow-repeat me-1"></i> Re-Enroll</button>

            </div>

        </div>

    </div>

</div>
