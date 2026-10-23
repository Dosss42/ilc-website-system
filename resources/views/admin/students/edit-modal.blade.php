{{-- Shared by Admin and Super Admin Student Management (each page defines the functions its buttons call) --}}
<div class="modal fade" id="studentEditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border:0;border-radius:20px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.18);">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,#1a3a6c 0%,#2563eb 100%);padding:22px 26px;position:relative;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:46px;height:46px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-fill-gear" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;color:#fff;line-height:1.1;">Edit Student</div>
                        <div id="se-header-name" style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">Loading student detailsâ€¦</div>
                    </div>
                </div>
                <button type="button" data-bs-dismiss="modal"
                    style="position:absolute;top:16px;right:18px;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
                <input type="hidden" id="se-id">
            </div>

            {{-- Tab Navigation --}}
            <div style="background:#f8faff;border-bottom:1.5px solid #e2e8f0;padding:0 24px;">
                <div style="display:flex;gap:2px;overflow-x:auto;" id="se-tab-nav">
                    <button type="button" onclick="seTab('personal')" id="se-tab-btn-personal"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid #2563eb;background:transparent;color:#2563eb;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-person-fill"></i>Personal Info
                    </button>
                    <button type="button" onclick="seTab('guardian')" id="se-tab-btn-guardian"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-people-fill"></i>Guardian
                    </button>
                    <button type="button" onclick="seTab('enrollment')" id="se-tab-btn-enrollment"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-journal-check"></i>Enrollment
                    </button>
                    <button type="button" onclick="seTab('address')" id="se-tab-btn-address"
                        style="display:flex;align-items:center;gap:7px;padding:14px 18px;border:none;border-bottom:3px solid transparent;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:all .15s;">
                        <i class="bi bi-geo-alt-fill"></i>Address
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:24px 26px;background:#fff;max-height:60vh;overflow-y:auto;">

                {{-- Personal Info --}}
                <div id="se-pane-personal">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-person-badge-fill me-1"></i> Basic Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Full Name</label>
                                <input type="text" id="se-name" class="form-fld" placeholder="Student full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Email Address</label>
                                <input type="email" id="se-email" class="form-fld" placeholder="Email address">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">LRN (Learner Reference No.)</label>
                                <input type="text" id="se-lrn" class="form-fld" placeholder="Enter LRN" style="font-family:monospace;letter-spacing:.5px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Gender</label>
                                <select id="se-gender" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Birthdate</label>
                                <input type="date" id="se-birthdate" class="form-fld">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Place of Birth</label>
                                <input type="text" id="se-pob" class="form-fld" placeholder="City/Municipality">
                            </div>
                        </div>
                    </div>
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-shield-check me-1"></i> Account Settings
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Student Type</label>
                                <select id="se-type" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="new">New Student</option>
                                    <option value="transferee">Transferee</option>
                                    <option value="returning">Returning Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Account Status</label>
                                <select id="se-active" class="form-fld">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guardian --}}
                <div id="se-pane-guardian" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-people-fill me-1"></i> Guardian / Parent Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Guardian Name</label>
                                <input type="text" id="se-guardian-name" class="form-fld" placeholder="Full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Relationship</label>
                                <select id="se-guardian-rel" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="guardian">Guardian</option>
                                    <option value="sibling">Sibling</option>
                                    <option value="grandparent">Grandparent</option>
                                    <option value="relative">Relative</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Contact Number</label>
                                <input type="text" id="se-guardian-phone" class="form-fld" placeholder="e.g. 09xxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Occupation</label>
                                <input type="text" id="se-guardian-occ" class="form-fld" placeholder="Occupation">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enrollment --}}
                <div id="se-pane-enrollment" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-journal-check me-1"></i> Academic Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Grade Level</label>
                                <select id="se-grade" class="form-fld">
                                    <option value="">-- Select Grade --</option>
                                    <option value="nursery">Nursery</option>
                                    <option value="kindergarten">Kindergarten</option>
                                    <option value="grade1">Grade 1</option>
                                    <option value="grade2">Grade 2</option>
                                    <option value="grade3">Grade 3</option>
                                    <option value="grade4">Grade 4</option>
                                    <option value="grade5">Grade 5</option>
                                    <option value="grade6">Grade 6</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Section</label>
                                <input type="text" id="se-section" class="form-fld" placeholder="Section name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Enrollment Status</label>
                                <select id="se-enroll-status" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-cash-stack me-1"></i> Payment Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">Payment Status</label>
                                <select id="se-pay-status" class="form-fld">
                                    <option value="">-- Select --</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Amount Paid (₱)</label>
                                <input type="number" id="se-pay-amount" class="form-fld" placeholder="0.00" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div id="se-pane-address" style="display:none;">
                    <div style="background:#f8faff;border:1.5px solid #e2e8f0;border-radius:14px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;">
                            <i class="bi bi-geo-alt-fill me-1"></i> Home Address
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-lbl">Street Address</label>
                                <input type="text" id="se-street" class="form-fld" placeholder="House/Block/Lot, Street">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Region</label>
                                <select id="se-region" class="form-fld">
                                    <option value="">Select Region</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Province</label>
                                <select id="se-province" class="form-fld" disabled>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">City/Municipality</label>
                                <select id="se-city" class="form-fld" disabled>
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Barangay</label>
                                <select id="se-brgy" class="form-fld" disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Zip Code</label>
                                <input type="text" id="se-zip" class="form-fld" placeholder="Zip code">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div style="padding:16px 26px;background:#f8faff;border-top:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <button type="button" data-bs-dismiss="modal"
                    style="display:flex;align-items:center;gap:6px;padding:11px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="bi bi-x-lg"></i>Cancel
                </button>
                <button type="button" onclick="saveStudent()"
                    style="display:flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#1a3a6c,#2563eb);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(37,99,235,.25);">
                    <i class="bi bi-floppy-fill"></i>Save Changes
                </button>
            </div>

        </div>
    </div>
</div>
