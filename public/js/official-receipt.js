/*
 * The one Official Receipt for the whole system — the Cashier's slip layout
 * (logo, IEMELIF LEARNING CENTER, OR No / Date / Time, student, description,
 * AMOUNT PAID, Received by + signature). Used by Cashier, Admin, Finance and
 * the Student Portal so every printed receipt looks the same.
 *
 * printOfficialReceipt({
 *   or_no, date, time, student_name, grade_level, school_year,
 *   description, method, amount, received_by,
 *   type: 'walkin' | 'online',   // online → "PAID ONLINE" instead of the cashier signature
 *   txn_id,                       // optional, shown for online payments
 *   acknowledgment                // true → "Acknowledgment Receipt": the copy a student/parent
 *                                 // prints in the portal after paying online. Only school staff
 *                                 // issue the Official Receipt (same reference number).
 * })
 * Every value is HTML-escaped here, so callers pass plain text.
 * Returns false if the browser blocked the pop-up.
 */
function printOfficialReceipt(d) {
    d = d || {};
    var esc = function (v) {
        return String(v == null || v === '' ? '—' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    };
    // Amounts arrive as numbers or pre-formatted strings ("1,278.32", "₱1,278.32")
    var amt = parseFloat(String(d.amount == null ? 0 : d.amount).replace(/[^0-9.\-]/g, '')) || 0;
    var amount = amt.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    // grade1 / Grade1 → Grade 1, kindergarten → Kindergarten
    var g = String(d.grade_level || '').trim();
    var grade = /^grade\s*(\d+)$/i.test(g) ? g.replace(/^grade\s*(\d+)$/i, 'Grade $1') : (g ? g.charAt(0).toUpperCase() + g.slice(1) : '');
    var online = d.type === 'online';
    var ack = d.acknowledgment === true;
    var title = ack ? 'ACKNOWLEDGMENT RECEIPT' : 'OFFICIAL RECEIPT';
    var logo = new URL('/images/logo.png', window.location.origin).href;

    var w = window.open('', '_blank', 'width=420,height=640,scrollbars=yes');
    if (!w) {
        var msg = 'Pop-ups are blocked. Please allow pop-ups for this site to print receipts.';
        if (typeof window.showToast === 'function') window.showToast(msg, 'warning'); else alert(msg);
        return false;
    }
    var html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
        + '<title>' + (ack ? 'Acknowledgment Receipt' : 'Official Receipt') + ' — ' + esc(d.or_no) + '</title>'
        + '<style>'
        + 'body{font-family:"Courier New",monospace;font-size:12px;margin:0;padding:20px 24px;color:#000;}'
        + '.center{text-align:center;} .bold{font-weight:700;} .right{text-align:right;}'
        + 'h2{margin:0;font-size:15px;letter-spacing:.5px;} h3{margin:3px 0;font-size:11px;font-weight:400;}'
        + '.solid{border-top:2px solid #000;margin:10px 0;} .dash{border-top:1px dashed #000;margin:8px 0;}'
        + 'table{width:100%;border-collapse:collapse;}'
        + 'td{padding:3px 0;vertical-align:top;}'
        + 'td:last-child{text-align:right;word-break:break-word;padding-left:8px;}'
        + '.amt{font-size:16px;font-weight:900;}'
        + '.footer{margin-top:24px;font-size:10px;text-align:center;color:#444;}'
        + '.sig{margin-top:36px;border-top:1px solid #000;padding-top:4px;text-align:center;font-size:11px;}'
        + '.paid{margin-top:22px;border:1.5px solid #000;padding:6px;text-align:center;font-size:11px;font-weight:700;letter-spacing:1px;}'
        + '@media print{body{padding:10px;}}'
        + '</style></head>'
        + '<body onload="setTimeout(function(){window.print();},400);">'
        + '<div class="center">'
        + '<img src="' + logo + '" alt="ILC Logo" style="width:70px;height:70px;object-fit:contain;border-radius:50%;margin-bottom:8px;" onerror="this.style.display=\'none\'">'
        + '<h2>IEMELIF LEARNING CENTER</h2>'
        + '<h3>General Tinio, Nueva Ecija</h3>'
        + '<h3>Tel: 0951-989-9685</h3></div>'
        + '<div class="solid"></div>'
        + '<div class="center bold" style="font-size:13px;letter-spacing:1px;">' + title + '</div>'
        + '<div class="solid"></div>'
        + '<table>'
        + '<tr><td>' + (ack ? 'Reference No.:' : 'OR No.:') + '</td><td class="bold">' + esc(d.or_no) + '</td></tr>'
        + '<tr><td>Date:</td><td>' + esc(d.date) + '</td></tr>'
        + '<tr><td>Time:</td><td>' + esc(d.time) + '</td></tr>'
        + '</table>'
        + '<div class="dash"></div>'
        + '<table>'
        + '<tr><td>Student:</td><td class="bold">' + esc(d.student_name) + '</td></tr>'
        + '<tr><td>Grade Level:</td><td>' + esc(grade) + '</td></tr>'
        + '<tr><td>School Year:</td><td>' + esc(d.school_year) + '</td></tr>'
        + '</table>'
        + '<div class="dash"></div>'
        + '<table>'
        + '<tr><td>Description:</td><td>' + esc(d.description || 'Payment') + '</td></tr>'
        + '<tr><td>Method:</td><td>' + esc(d.method || 'Cash') + '</td></tr>'
        + (online && d.txn_id ? '<tr><td>Transaction ID:</td><td style="font-size:10px;">' + esc(d.txn_id) + '</td></tr>' : '')
        + '</table>'
        + '<div class="solid"></div>'
        + '<table><tr><td class="bold amt">AMOUNT PAID:</td><td class="bold amt right">&#x20B1;' + esc(amount) + '</td></tr></table>'
        + '<div class="solid"></div>'
        + (online
            ? '<div class="paid">PAID ONLINE &middot; VERIFIED BY PAYMENT GATEWAY</div>'
            : '<div style="margin-top:14px;font-size:11px;">Received by: <span class="bold">' + esc(d.received_by || 'Cashier') + '</span></div>'
              + '<div class="sig">Cashier Signature</div>')
        + '<div class="footer">'
        + '<div>Thank you for your payment!</div>'
        + '<div style="margin-top:3px;">This is a computer-generated receipt.' + (online ? ' No signature required.' : '') + '</div>'
        + (ack ? '<div style="margin-top:6px;">This is proof of your online payment. Your Official Receipt may be claimed at the Cashier / Finance Office.</div>' : '')
        + '</div>'
        + '</body></html>';
    w.document.open();
    w.document.write(html);
    w.document.close();
    return true;
}
