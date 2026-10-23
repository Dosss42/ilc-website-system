{{-- Shared by Admin and Super Admin Student Management (each page defines the functions its buttons call) --}}
<div class="modal fade" id="studentTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-content-styled" style="border-radius:16px; overflow:hidden; border:none;">
            <div class="modal-header modal-header-styled" style="background:linear-gradient(135deg, #0056b3, #003d82); padding:18px 24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; background:rgba(255,255,255,0.15); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-receipt" style="font-size:18px; color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="color:#fff; font-weight:700; margin:0; font-size:16px;">Transaction History</h5>
                        <div id="stx-subtitle" style="color:rgba(255,255,255,.8); font-size:12px;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px; background:#fff;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                    <div style="font-size:12px; color:var(--muted);"><i class="bi bi-info-circle me-1"></i>Cashier (walk-in) and online payments, newest first.</div>
                    <select id="stx-sy" class="form-fld" style="width:auto; min-width:190px; padding:7px 12px; font-size:13px;" onchange="loadStudentTransactions()">
                        <option value="all">All School Years</option>
                    </select>
                </div>
                <div id="stx-summary" class="row g-3 mb-3"></div>
                <div style="overflow-x:auto;">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Date</th><th>Reference</th><th>Description</th><th>S.Y.</th>
                                <th>Method</th><th>Processed By</th><th style="text-align:right;">Amount</th><th>Status</th><th style="text-align:center;">Receipt</th>
                            </tr>
                        </thead>
                        <tbody id="stx-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
