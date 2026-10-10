{{-- Signature block shared by every school-report PDF (Finance, Cashier,
     and anything else built on pdf.shared.*). Flows in normal document
     order right after the report's last table/section, so it lands on the
     true last page, below that page's canvas-drawn footer band (see
     App\Support\SchoolPdfDecorator, which draws the Bible verse +
     Vision/Mission + print-meta footer on every page — moved out of this
     partial since it now needs to repeat per page, not just once here).
     Two fixed signatories, not a dynamic "prepared by" name: an
     Administrative Assistant / School Registrar (signs blank) and the
     school's Officer in Charge. --}}
<div class="fin-bottom">
    <div class="fin-sig-block">
        <div class="fin-sig-cell">
            <div class="fin-sig-line">RUTH F. BIANZON</div>
            <div class="fin-sig-label">Administrative Assistant / School Registrar</div>
        </div>
        <div class="fin-sig-cell">
            <div class="fin-sig-line">TEOFILA T. GUILLERMO</div>
            <div class="fin-sig-label">Officer in Charge</div>
        </div>
    </div>
</div>
