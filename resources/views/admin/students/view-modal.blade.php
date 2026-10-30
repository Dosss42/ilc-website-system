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

            {{-- Footer: Close on the left; the printable records grouped on the right as matching buttons --}}
            <style>
                #studentViewModal .sv-doc-btn { display:inline-flex; align-items:center; gap:8px; padding:7px 14px; border-radius:10px;
                    background:#fff; border:1.5px solid var(--sv-c); color:var(--sv-c); font-size:13px; font-weight:600; line-height:1.15;
                    text-decoration:none; text-align:left; transition:background .15s, color .15s; }
                #studentViewModal .sv-doc-btn:hover { background:var(--sv-c); color:#fff; }
                #studentViewModal .sv-doc-btn i { font-size:17px; }
                #studentViewModal .sv-doc-btn small { display:block; font-size:10.5px; font-weight:500; opacity:.8; }
            </style>
            <div class="modal-footer" style="border:0;border-top:1px solid #eef2f7;background:#f8fafc;padding:14px 24px;justify-content:space-between;gap:10px;">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-arrow-left me-1"></i> Close</button>

                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;margin-right:2px;">
                        <i class="bi bi-printer"></i> Print
                    </span>

                    <a id="sv-sf10-btn" href="#" onclick="return ilcPrintPdf(this.href)" class="sv-doc-btn" style="display:none;--sv-c:#1a6b2d;"
                       title="Learner's Permanent Academic Record">
                        <i class="bi bi-journal-text"></i><span>SF10<small>Permanent Record</small></span>
                    </a>

                    <button type="button" id="sv-goodmoral-btn" class="sv-doc-btn" style="display:none;--sv-c:#1a3a6c;"
                            onclick="openGoodMoral(this.dataset.student)" title="Certificate of Good Moral Character">
                        <i class="bi bi-award"></i><span>Good Moral<small>Certificate</small></span>
                    </button>

                    <button type="button" class="btn btn-primary" id="sv-reenroll-btn" onclick="reEnrollStudent()" style="display:none;"><i class="bi bi-arrow-repeat me-1"></i> Re-Enroll</button>
                </div>

            </div>

        </div>

    </div>

</div>

{{-- Certificate of Good Moral Character: purpose + what to know first (balance, guidance records — warned, not blocked) --}}
<div class="modal fade" id="goodMoralModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6c,#2471a3);color:#fff;border:0;">
                <h6 class="modal-title" style="font-weight:700;"><i class="bi bi-award me-1"></i> Certificate of Good Moral Character</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 22px;">
                <div id="gm-student" style="font-size:13px;margin-bottom:12px;"></div>
                <div id="gm-warnings"></div>
                <label class="form-lbl" for="gm-purpose-choice">Purpose</label>
                <select id="gm-purpose-choice" class="form-fld" onchange="document.getElementById('gm-purpose').style.display = this.value === 'other' ? '' : 'none';">
                    <option value="transfer to another school">Transfer to another school</option>
                    <option value="admission to Junior High School (Grade 7)">Admission to Junior High School (Grade 7)</option>
                    <option value="scholarship application">Scholarship application</option>
                    <option value="">Any legal purpose (no specific purpose)</option>
                    <option value="other">Other…</option>
                </select>
                <input type="text" id="gm-purpose" class="form-fld" maxlength="120" placeholder="Type the purpose" style="display:none;margin-top:8px;">
                <small class="text-muted" style="font-size:11px;display:block;margin-top:6px;">Printed as "… for the purpose of ___". Signed by the Guidance Counselor and noted by the School Head (Settings).</small>
            </div>
            <div class="modal-footer" style="border:0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="gm-print-btn" onclick="printGoodMoral()" disabled><i class="bi bi-printer-fill me-1"></i> Print Certificate</button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var studentId = null;
    var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };
    var peso = function (n) { return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };

    window.openGoodMoral = function (id) {
        studentId = id;
        var btn = document.getElementById('gm-print-btn');
        btn.disabled = true;
        document.getElementById('gm-student').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Checking the student\'s records…';
        document.getElementById('gm-warnings').innerHTML = '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('goodMoralModal')).show();

        fetch('/admin/students/' + id + '/good-moral/check', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) { document.getElementById('gm-student').innerHTML = '<span style="color:#b91c1c;">' + esc(d.message) + '</span>'; return; }
                document.getElementById('gm-student').innerHTML = '<strong>' + esc(d.student) + '</strong> · certifies ' + esc(d.covers);
                var w = '';
                if (d.balances.length) {
                    w += '<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:10px 12px;margin-bottom:10px;font-size:12px;color:#7f1d1d;">'
                        + '<strong><i class="bi bi-cash-coin"></i> Unpaid balance</strong>'
                        + d.balances.map(function (b) { return '<div>' + esc(b.grade) + ', S.Y. ' + esc(b.school_year) + ': <strong>' + peso(b.amount) + '</strong></div>'; }).join('')
                        + '<div style="margin-top:4px;">You can still print — the school decides whether to release it.</div></div>';
                }
                if (d.guidance.length) {
                    w += '<div style="background:#fff7ed;border:1px solid #fdba74;border-radius:10px;padding:10px 12px;margin-bottom:10px;font-size:12px;color:#7c2d12;">'
                        + '<strong><i class="bi bi-clipboard2-pulse"></i> ' + d.guidance.length + ' guidance record(s)</strong>'
                        + d.guidance.slice(0, 3).map(function (g) { return '<div>' + esc(g.date) + ' — ' + esc(g.concern) + (g.status ? ' (' + esc(g.status) + ')' : '') + '</div>'; }).join('')
                        + '<div style="margin-top:4px;">The certificate says the student "has not been subjected to any disciplinary action" — check with the Guidance Counselor first.</div></div>';
                }
                document.getElementById('gm-warnings').innerHTML = w;
                btn.disabled = false;
            })
            .catch(function () { document.getElementById('gm-student').innerHTML = '<span style="color:#b91c1c;">Could not check the student\'s records. Please try again.</span>'; });
    };

    window.printGoodMoral = function () {
        var choice = document.getElementById('gm-purpose-choice').value;
        var purpose = choice === 'other' ? document.getElementById('gm-purpose').value.trim() : choice;
        if (choice === 'other' && !purpose) { document.getElementById('gm-purpose').focus(); return; }
        ilcPrintPdf('/admin/students/' + studentId + '/good-moral' + (purpose ? '?purpose=' + encodeURIComponent(purpose) : ''));
    };
})();
</script>
