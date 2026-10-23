/*
 * Print a School Form (SF10, SF9, SF5) instead of downloading it.
 *
 * Loads the form's PDF (?print=1 makes the server send it inline instead of as a download) in an
 * off-screen iframe and opens the browser's print window. That window can print on paper or
 * "Save as PDF", so downloading is still possible from there.
 *
 * Usage: <a href="/admin/students/5/sf10" onclick="return ilcPrintPdf(this.href)">Print SF10</a>
 *        ilcPrintPdf('/teacher/sf5?section_id=3')
 */
(function () {
    function note(text) {
        var el = document.getElementById('ilc-print-note');
        if (!text) { if (el) el.remove(); return; }
        if (!el) {
            el = document.createElement('div');
            el.id = 'ilc-print-note';
            el.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:100001;'
                + 'background:#1a3a6c;color:#fff;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;'
                + 'box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:8px;';
            document.body.appendChild(el);
        }
        el.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        el.appendChild(document.createTextNode(text));
    }

    window.ilcPrintPdf = function (url) {
        var printUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + 'print=1';

        var old = document.getElementById('ilc-print-frame');
        if (old) old.remove();

        // Off-screen rather than hidden: Chrome does not print a PDF from a display:none iframe
        var frame = document.createElement('iframe');
        frame.id = 'ilc-print-frame';
        frame.setAttribute('aria-hidden', 'true');
        frame.style.cssText = 'position:fixed;left:-10000px;top:0;width:800px;height:600px;border:0;';

        var fallback = setTimeout(function () { note(''); }, 30000);
        frame.onload = function () {
            clearTimeout(fallback);
            var doc = null;
            try { doc = frame.contentDocument; } catch (e) {}
            // Not a PDF (e.g. an error page): show it in a tab so the message can be read
            if (doc && doc.contentType && doc.contentType.indexOf('pdf') === -1) {
                note('');
                window.open(printUrl, '_blank');
                frame.remove();
                return;
            }
            // Give the PDF viewer a moment to render before printing
            setTimeout(function () {
                note('');
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                } catch (e) {
                    window.open(printUrl, '_blank'); // browser without iframe printing: its own viewer has Print
                }
            }, 600);
        };

        note('Preparing the form for printing…');
        frame.src = printUrl;
        document.body.appendChild(frame);
        return false;
    };
})();
