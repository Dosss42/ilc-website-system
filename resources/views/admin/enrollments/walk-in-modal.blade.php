<!-- Walk-in Enrollment Modal -->
<div class="modal fade" id="walkInEnrollmentModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content modal-content-styled">

            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, var(--blue), #0056b3);">

                <h5 class="modal-title" style="color:#fff;"><i class="bi bi-person-plus-fill me-2"></i>Walk-in Enrollment</h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body modal-body-styled">

                <div style="background:#e8f4f8; border:1px solid #b8d4e8; border-radius:8px; padding:14px 16px; display:flex; align-items:center; gap:10px; margin-bottom:20px;">

                    <i class="bi bi-info-circle-fill" style="font-size:20px; color:#0056b3;"></i>

                    <div>

                        <div style="font-weight:700; font-size:13px; color:#004085;">Walk-in Enrollment</div>

                        <div style="font-size:12px; color:#004085;">Fill out this form for parents who enroll in person. Login credentials will be auto-generated and sent to the guardian's email after approval.</div>

                    </div>

                </div>

                <form id="walkInEnrollmentForm">

                    <input type="hidden" id="walkin-reenroll" name="reenroll_student_id" value="">

                    <!-- Personal Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-person me-2"></i>Personal Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="dash-form-label">First Name *</label>

                                <input type="text" id="walkin-first-name" class="dash-form-control" placeholder="Enter first name" required>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Middle Name</label>

                                <input type="text" id="walkin-middle-name" class="dash-form-control" placeholder="Enter middle name (optional)">

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Last Name *</label>

                                <input type="text" id="walkin-last-name" class="dash-form-control" placeholder="Enter last name" required>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Suffix</label>

                                <input type="text" id="walkin-suffix" class="dash-form-control" placeholder="Jr., Sr., III">

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Gender *</label>

                                <select id="walkin-gender" class="dash-form-control" required>

                                    <option value="">Select</option>

                                    <option value="male">Male</option>

                                    <option value="female">Female</option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Birthdate *</label>

                                <input type="date" id="walkin-birthdate" class="dash-form-control" placeholder="Select birthdate" required>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Place of Birth *</label>

                                <input type="text" id="walkin-place-of-birth" class="dash-form-control" required placeholder="e.g., Manila">

                            </div>

                            <div class="col-md-6">

                                <label class="dash-form-label">Nationality</label>

                                <input type="text" id="walkin-nationality" class="dash-form-control" placeholder="e.g., Filipino">

                            </div>

                        </div>

                    </div>

                    <!-- Parent Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-people me-2"></i>Parent Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="dash-form-label">Mother's Full Name *</label>

                                <input type="text" id="walkin-mother-name" class="dash-form-control" placeholder="Mother's full name" required>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Mother's Age *</label>

                                <input type="number" id="walkin-mother-age" class="dash-form-control" placeholder="Age" required min="1" max="120">

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Religious Affiliation *</label>

                                <input type="text" id="walkin-religious-affiliation" class="dash-form-control" placeholder="e.g., Roman Catholic" required>

                            </div>

                            <div class="col-md-6">

                                <label class="dash-form-label">Father's Full Name *</label>

                                <input type="text" id="walkin-father-name" class="dash-form-control" placeholder="Father's full name" required>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Father's Age *</label>

                                <input type="number" id="walkin-father-age" class="dash-form-control" placeholder="Age" required min="1" max="120">

                            </div>

                        </div>

                    </div>

                    <!-- Academic Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-book me-2"></i>Academic Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="dash-form-label">Grade Level *</label>

                                <select id="walkin-grade-level" class="dash-form-control" required onchange="updateWalkinPaymentOptions()">

                                    <option value="">Select</option>

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

                            <div class="col-md-4">

                                <label class="dash-form-label">Student Type *</label>

                                <select id="walkin-student-type" class="dash-form-control" required>

                                    <option value="">Select</option>

                                    <option value="new">New</option>

                                    <option value="transferee">Transferee</option>

                                    <option value="returning">Returning</option>

                                </select>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Last School Attended</label>

                                <input type="text" id="walkin-last-school" class="dash-form-control" placeholder="Enter previous school name">

                            </div>

                        </div>

                    </div>

                    <!-- Address Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-geo-alt me-2"></i>Address Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-3">

                                <label class="dash-form-label">Region *</label>

                                <select id="walkin-region" class="dash-form-control" required>
                                    <option value="">Loading regions...</option>
                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Province *</label>

                                <select id="walkin-province" class="dash-form-control" required disabled>
                                    <option value="">Select Province</option>
                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">City/Municipality *</label>

                                <select id="walkin-city" class="dash-form-control" required disabled>
                                    <option value="">Select City/Municipality</option>
                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="dash-form-label">Barangay *</label>

                                <select id="walkin-barangay" class="dash-form-control" required disabled>
                                    <option value="">Select Barangay</option>
                                </select>

                            </div>

                            <div class="col-md-8">

                                <label class="dash-form-label">Street / Purok / House No. *</label>

                                <input type="text" id="walkin-street-address" class="dash-form-control" placeholder="Enter Street / Purok / House No." required>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Zip Code</label>

                                <input type="text" id="walkin-zip-code" class="dash-form-control" placeholder="Enter zip code">

                            </div>

                        </div>

                    </div>

                    <!-- Guardian Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-shield-check me-2"></i>Guardian Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="dash-form-label">Full Name *</label>

                                <input type="text" id="walkin-guardian-name" class="dash-form-control" placeholder="Enter guardian name" required>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Relationship *</label>

                                <select id="walkin-relationship" class="dash-form-control" required>

                                    <option value="">Select</option>

                                    <option value="father">Father</option>

                                    <option value="mother">Mother</option>

                                    <option value="guardian">Guardian</option>

                                    <option value="sibling">Sibling</option>

                                    <option value="grandparent">Grandparent</option>

                                    <option value="relative">Relative</option>

                                </select>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Guardian Occupation</label>

                                <input type="text" id="walkin-guardian-occupation" class="dash-form-control" placeholder="Enter occupation (optional)">

                            </div>

                            <div class="col-md-6">

                                <label class="dash-form-label">Guardian Phone *</label>
                                <div style="display:flex;align-items:stretch;border:1.5px solid #c8d6f0;border-radius:6px;overflow:hidden;background:#fff;">
                                    <span style="padding:0 12px;background:#f0f4ff;color:#1d3557;font-weight:700;font-size:13px;display:flex;align-items:center;border-right:1.5px solid #c8d6f0;white-space:nowrap;">+63</span>
                                    <input type="tel" id="walkin-guardian-phone" class="dash-form-control"
                                        style="border:none;border-radius:0;flex:1;min-width:0;"
                                        placeholder="9XXXXXXXXX" maxlength="10"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                        required>
                                </div>
                                <small class="text-muted-alt">10 digits after +63 (e.g. 9123456789)</small>

                            </div>

                            <div class="col-md-6">

                                <label class="dash-form-label">Guardian Email (for credentials) *</label>

                                <input type="email" id="walkin-guardian-email" class="dash-form-control"
                                    placeholder="example@gmail.com" required
                                    oninput="validateWalkinGmail(this)">

                                <small class="text-muted-alt" id="walkin-email-hint">Must be a Gmail address — credentials will be sent here after approval.</small>

                            </div>

                        </div>

                    </div>

                    <!-- Health Information -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-heart-pulse me-2"></i>Health Information

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="dash-form-label">Blood Type</label>

                                <select id="walkin-blood-type" class="dash-form-control">

                                    <option value="">Select</option>

                                    <option value="A+">A+</option>

                                    <option value="A-">A-</option>

                                    <option value="B+">B+</option>

                                    <option value="B-">B-</option>

                                    <option value="AB+">AB+</option>

                                    <option value="AB-">AB-</option>

                                    <option value="O+">O+</option>

                                    <option value="O-">O-</option>

                                </select>

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Allergies</label>

                                <input type="text" id="walkin-allergies" class="dash-form-control" placeholder="List any known allergies">

                            </div>

                            <div class="col-md-4">

                                <label class="dash-form-label">Medical Conditions</label>

                                <input type="text" id="walkin-medical-conditions" class="dash-form-control" placeholder="Any medical conditions">

                            </div>

                        </div>

                    </div>

                    <!-- Payment Options -->

                    <div class="mb-4">

                        <h6 style="color:var(--blue); font-weight:700; margin-bottom:12px; border-bottom:2px solid #e8f4f8; padding-bottom:8px;">

                            <i class="bi bi-credit-card me-2"></i>Payment Option

                        </h6>

                        <div class="row g-3">

                            <div class="col-md-6 col-lg-3">

                                <div class="payment-option-card" id="walkin-card-opt-a" onclick="selectWalkinPaymentOption('A')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                    <div class="payment-option-header">

                                        <h6>Option A</h6>

                                    </div>

                                    <p class="text-muted mb-0" style="font-size:12px;">Cash Basis (20% discount)</p>

                                    <div class="payment-option-features mt-2">

                                        <span style="font-size:18px; font-weight:700; color:var(--gold);" id="walkin-opt-a-total">₱14,504</span>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 col-lg-3">

                                <div class="payment-option-card" id="walkin-card-opt-b" onclick="selectWalkinPaymentOption('B')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                    <div class="payment-option-header">

                                        <h6>Option B</h6>

                                    </div>

                                    <p class="text-muted mb-0" style="font-size:12px;">Monthly Payment (9 months)</p>

                                    <div class="payment-option-features mt-2">

                                        <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-b-monthly">₱1,056.10/mo</span>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 col-lg-3" id="walkin-opt-c-container">

                                <div class="payment-option-card" id="walkin-card-opt-c" onclick="selectWalkinPaymentOption('C')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                    <div class="payment-option-header">

                                        <h6>Option C</h6>

                                    </div>

                                    <p class="text-muted mb-0" style="font-size:12px;">Elementary Monthly (Grade 1-6)</p>

                                    <div class="payment-option-features mt-2">

                                        <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-c-monthly">₱1,278.32/mo</span>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 col-lg-3" id="walkin-opt-d-container">

                                <div class="payment-option-card" id="walkin-card-opt-d" onclick="selectWalkinPaymentOption('D')" style="border:2px solid #e0e0e0; border-radius:8px; padding:16px; cursor:pointer; transition:all 0.2s;">

                                    <div class="payment-option-header">

                                        <h6>Option D</h6>

                                    </div>

                                    <p class="text-muted mb-0" style="font-size:12px;">Nursery Monthly (Nursery/Kinder)</p>

                                    <div class="payment-option-features mt-2">

                                        <span style="font-size:16px; font-weight:700; color:var(--gold);" id="walkin-opt-d-monthly">₱1,278.32/mo</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div id="walkin-payment-breakdown" style="background:#f8f9fa; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-top:20px; display:none;">

                            <h6 style="color:var(--blue); font-weight:700; margin-bottom:16px;">

                                <i class="bi bi-receipt-cutoff me-2"></i>Payment Breakdown

                            </h6>

                            <div id="walkin-breakdown-content"></div>

                        </div>

                        <input type="hidden" id="walkin-payment-option">

                        <input type="hidden" id="walkin-downpayment-amount">

                        <input type="hidden" id="walkin-monthly-amount">

                        <input type="hidden" id="walkin-total-amount">

                    </div>

                </form>

            </div>

            <div class="modal-footer modal-footer-styled">

                <button type="button" class="btn-dash btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn-dash btn-primary" onclick="submitWalkInEnrollment()"><i class="bi bi-check-lg me-1"></i>Submit Enrollment</button>

            </div>

        </div>

    </div>

</div>
